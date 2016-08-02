<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 2/04/16
 * Time: 16:20
dog_reader.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../auth/Config.php");
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/../../modules/Federations.php");
require_once(__DIR__."/../database/classes/DBObject.php");
require_once(__DIR__.'/Spout/Autoloader/autoload.php');
require_once(__DIR__.'/dog_reader.php');
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

class InscriptionReader extends DogReader{

    protected $prueba;
    protected $jornadas;

    public function __construct($name,$pruebaID,$options) {
        $this->myDBObject = new DBObject($name);
        $this->prueba=$this->myDBObject->__selectAsArray("*","Pruebas","ID=$pruebaID");
        if (!is_array($this->prueba))
            throw new Exception("InscriptionReader::construct(): invalid Prueba ID: $pruebaID");
        parent::__construct("ImportExcel(inscriptions)",$this->prueba['RSCE'],$options);

        // add additional fields required to handle inscriptions
        $inscList= array(
            'Dorsal' =>  array (  -15,  -1, "i", "Dorsal", " `Dorsal` int(4) NOT NULL DEFAULT 0, "), // dorsal, opcional
            'Heat' =>  array (  -16,  -1, "b", "Celo", " `Dorsal` tinyint(1) NOT NULL DEFAULT 0, "), // celo, opcional
            'Comments' =>  array (  -17,  -1, "s", "Observaciones", " `Observaciones` varchar(255) NOT NULL DEFAULT '', "), // comentarios, opcional
            'Pay' =>  array (  -18,  -1, "i", "Pagado", " `Pagado` int(4) NOT NULL DEFAULT 0, "), // pagadol, opcional
            'Journeys' =>  array (  -19,  0, "i", "Jornadas", " `Jornadas` int(4) NOT NULL DEFAULT 0, "), // jornadas. to evaluate
            'Orden' =>  array (  -20,  0, "i", "Orden", " `Order` int(4) NOT NULL DEFAULT 0, "), // orden, to evaluate
        );
        foreach ($inscList as $key => $data) $this->fieldList[$key]=$data;

        // add as columns for contest journeys
        $res=$this->myDBObject->__select("*","Jornadas","(Prueba=$pruebaID","","");
        if (!$res) throw new Exception("InscriptionReader::construct(): cannot retrieve list of journeys for prueba: $pruebaID");
        $index=-21;
        foreach ($res['rows'] as $jornada) {
            $name=$jornada['Nombre'];
            if ($name==="-- Sin asignar --") continue;
            else $name=preg_replace('/\s+/', '', $name); // remove spaces to get friendly with database field naming
            $name=mysqli_real_escape_string($this->myDBObject->conn,$name); // escape to avoid SQL injection
            $key="Jornada:".$jornada['Numero'];
            $this->fieldList[$key]= array( $index,1,"s",$name," `$name` varchar(255) NOT NULL DEFAULT '', ");
            $index--;
        }
    }

    /**
     * When parse, analyze and mix is done, time to update database with final results
     * @return array|string
     */
    public function beginImport() {
        $res=parent::beginImport();
        if (!is_array($res)) return $res;
        // override parent return data to mark start inscriptions parsing
        return array( 'operation'=>'import','success'=>'teams');
    }

    // create teams for every journey on this contest
    public function createTeams() {
        $this->saveStatus("Looking for teams");
        foreach ($this->jornadas as $jornada) {
            // check for team journey
            $team = intval($jornada['Equipos3']) + intval($jornada['Equipos4']);
            if($team==0) continue; // not a team journey: skip

            // if team journey retrieve all team names and create them
            $jname=$jornada['Nombre'];
            $t=TABLE_NAME;
            $this->saveStatus("Creating teams for Journey: $jname");
            // select distinct jornadaname from temporary tabla where jornadaname!="" group by jornadaname
            $res=$this->myDBObject->__select("DISTINCT Categoria , $jname AS Equipo",TABLE_NAME,"($jname<>'')","","",$jname);
            // parse result to join categories on same team
            $teams=array();
            foreach ($res['rows']as $team) {
                $eq=$team['Equipo'];
                if (!array_key_exists($eq)) $teams[$eq]=''; // team not yet declared.
                if (strpos($teams[$eq],$team['Categoria'])===FALSE) $teams[$eq] .= $team['Categoria'];
            }
            // and now iterate result and store teams into database
            foreach ($teams as $team => $cat) {
                // create teams for this journey
                $this->saveStatus("Creating team: $jname -> $team");
                // evaluate categories
                $eq=new Equipos("importInscriptions",$this->prueba['ID'],$jornada['ID']);
                $eq->realInsert($cat,$team,/* "Excel imported"*/ "");
            }
        }
        return array( 'operation'=>'import','success'=>'inscribe');
    }

    // iterate over inscriptions and perform it
    public function doInscription() {
        $t=TABLE_NAME;
        // retrieve list of pending inscriptions
        $res=$this->myDBObject->__select("*",TABLE_NAME,"(Orden=0)","ID ASC","");
        if (!$res) return "doInscription::getParticipantes() error:".$this->myDBObject->conn->error;
        // inscribe first item
        if ($res['total']>0) {
            $item=$res['rows'][0];
            $id=$item['ID'];
            $this->saveStatus("Registering inscription: {$item['Nombre']} - {$item['NombreGuia']}");
            // evaluate journeys to inscribe into
            $jornadas=0;
            foreach ($this->fieldList as $key => $val) {
                if (strpos($key,'Jornada')===FALSE) continue; // not a journey field
                $numero=intval(explode(':',$key)[2]) - 1;
                $nombre=$val[3];
                if (trim($nombre)!=="") $jornadas |= (1<<$numero); // field not empty means need to inscribe
            }

            // make inscription
            $idperro=$item['DogID'];
            $pagado=$item['Pagado'];
            $celo=(trim($item['Celo'])==="")?0:1;
            $obs=mysqli_real_escape_string($this->myDBObject->conn,$item['Comentarios']);
            $newdorsal=intval($item['Dorsal']);
            $insc=new Inscripciones("excelImport",$this->prueba['ID']);
            $dorsal=$insc->realInsert($idperro,$this->prueba['ID'],$jornadas,$pagado,$celo,$obs);
            if (is_string($dorsal)) return $dorsal; // error
            // set dorsal if provided
            if ($dorsal!=0) $insc->setDorsal($idperro,$dorsal,$newdorsal);

            // add to proper team when required
            foreach ($this->jornadas as $jornada) {

                // check if a given journey has teams and if dog is inscribed
                if ( (intval($jornada['Equipos3']) + intval($jornada['Equipos4']))==0) continue; // not team journey
                $name=preg_replace('/\s+/', '', $jornada['Nombre']); // remove spaces to get friendly with database field naming
                if (trim($item[$name])==="") continue; // not inscribed in this team journey

                // need to inscribe: locate team id and perform inscription into requested team
                $eq= new Equipos("excelImport",$this->prueba['ID'],$jornada['ID']);
                $tbj=$eq->getTeamsByJornada();
                foreach($tbj as $team) {
                    if ($team['Nombre']!==$item[$name]) continue;
                    $eq->updateTeam($idperro,$team['ID']);
                    break;
                }
                // go to next journey
            }
            // mark entry as already inscribed
            $str ="UPDATE $t SET ORDEN=$id WHERE ID=$id";
            $res=$this->myDBObject->query($str);
            if (!$res) return "doInscription::markAsInscribed($id) error:".$this->myDBObject->conn->error;
            // ask for next result
            return array( 'operation'=> 'import','success'=> 'inscribe');
        }
        // no inscriptions pending. tell client to end import
        return array( 'operation'=>'import','success'=>'close');
    }
}

// skip web server parsing when running in local (CommandLineInterpreter - cli ) mode
if (php_sapi_name() != "cli" ) {
    $op=http_request("Operation","s","");
    if ($op==='progress') {
        // retrieve last line of progress file
        $lines=file(IMPORT_LOG,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) {
            echo json_encode( array( 'operation'=>'progress','success'=>'fail', 'status' => "Error reading progress file" ) );
        } else {
            echo json_encode( array( 'operation'=>'progress','success'=>'ok', 'status' => strval($lines[count($lines)-1]) ) );
        }
        return;
    }
    // Consultamos la base de datos
    try {
        // 	Creamos generador de documento
        $options=array();
        $prueba=http_request("Prueba","i",0);
        $options['Blind']=http_request("Blind","i",0);
        $options['DBPriority']=http_request("DBPriority","i",1);
        $options['WordUpperCase']=http_request("WordUpperCase","i",1);
        $options['IgnoreWhiteSpaces']=http_request("IgnoreWhitespaces","i",1);

        if ($pruebaa=0) throw new Exception("inscription_reader::ImportExcel(): invalid Prueba ID: $prueba");

        $dr=new InscriptionReader("ExcelImport(inscriptions",$prueba,$options);
        $result="";
        switch ($op) {
            case "upload":
                // download excel file from browser
                $result = $dr->retrieveExcelFile();
                break;
            case "check":
                // check that received file matches PerroGuiaClub format
                // and store in temporary database table
                $file=http_request("Filename","s",null);
                $result = $dr->validateFile($file);
                break;
            case "parse":
                // start analysis
                $result = $dr->parse();
                break;
            case "create":
                // a new line has been accepted from user: insert and update temporary excel file
                $result = $dr->createEntry();
                break;            
            case "update":
                // a new line has been accepted from user: insert and update temporary excel file
                $result = $dr->updateEntry();
                break;
            case "ignore":
                // received entry has been refused by user: remove and update temporary excel file
                $result = $dr->ignoreEntry();
                break;
            case "abort":
                // user has cancelled import file: clear and return temporary data
                $result = $dr->cancelImport();
                break;
            case "import":
                // every entries have been corrected and have proper entry ID's: start importing
                $result = $dr->beginImport();
                break;
            case "teams":
                // every entries have been corrected and have proper entry ID's: start importing
                $result = $dr->createTeams();
                break;
            case "inscribe":
                // every entries have been corrected and have proper entry ID's: start importing
                $result = $dr->doInscription();
                break;
            case "close":
                // end of import. clear and return;
                $result = $dr->endImport();
                break;
            default: throw new Exception("excel_import(dogs): invalid operation '$op' requested");
        }
        if ($result===null) throw new Exception($dr->errormsg);// null on error
        if (is_string($result)) throw new Exception($result);
        if ( ($result==="") || ($result===0) )  // empty or zero on success
            echo json_encode(array('operation'=> $op, 'success'=>'ok'));
        else echo json_encode($result);         // else return data already has been set
    } catch (Exception $e) {
        $dr->myLogger->error($e->getMessage());
        echo json_encode(array("operation"=>$op, 'success'=>'fail', 'errorMsg'=>$e->getMessage()));
    }
}
