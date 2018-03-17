<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 2/04/16
 * Time: 16:20
InscriptionReader.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

require_once(__DIR__ . "/../../logging.php");
require_once(__DIR__ . "/../../tools.php");
require_once(__DIR__ . "/../../auth/Config.php");
require_once(__DIR__ . "/../../auth/AuthManager.php");
require_once(__DIR__ . "/../../modules/Federations.php");
require_once(__DIR__ . "/../../database/classes/DBObject.php");
require_once(__DIR__ . '/../Spout/Autoloader/autoload.php');
require_once(__DIR__ . '/DogReader.php');

class InscriptionReader extends DogReader {

    protected $prueba;
    protected $jornadas;

    public function __construct($name,$options) {
        $pay=_('Pay'); // stupid poedit
        $this->myDBObject = new DBObject($name);
        $this->prueba=$this->myDBObject->__selectAsArray("*","pruebas","ID={$options['Prueba']}");
        if (!is_array($this->prueba))
            throw new Exception("{$name}::construct(): invalid Prueba ID: {$options['Prueba']}");
        parent::__construct($name,$options);

        // add additional fields required to handle inscriptions
        $inscList= array(
            'Heat' =>    array (  -17,  -1, "b", "Celo", " `Celo` tinyint(1) NOT NULL DEFAULT 0, "), // celo, opcional
            'Comments' =>array (  -18,  -1, "s", "Observaciones", " `Observaciones` varchar(255) NOT NULL DEFAULT '', "), // comentarios, opcional
            'Pay' =>     array (  -19,  -1, "i", "Pagado", " `Pagado` int(4) NOT NULL DEFAULT 0, "), // pagadol, opcional
            'Journeys' =>array (  -20,   0, "i", "Jornadas", " `Jornadas` int(4) NOT NULL DEFAULT 0, "), // jornadas. to evaluate
            'Order' =>   array (  -21,   0, "i", "Orden", " `Orden` int(4) NOT NULL DEFAULT 0, "), // orden, to evaluate
            'Dorsal' =>  array (  -22,  -1, "i", "Dorsal", " `Dorsal` int(4) NOT NULL DEFAULT 0, ") // dorsal, opcional
        );
        foreach ($inscList as $key => $data) $this->fieldList[$key]=$data;

        // add as columns for contest journeys
        $res=$this->myDBObject->__select("*","jornadas","(Prueba={$options['Prueba']})","","");
        if (!$res) throw new Exception("InscriptionReader::construct(): cannot retrieve list of journeys for prueba: {$options['Prueba']}");
        $this->jornadas=$res['rows'];
        $index=-23; // notice negative index, as stored in fieldList ( to be _decremented_ )
        foreach ($this->jornadas as $jornada) {
            $name=$jornada['Nombre'];
            if ($name==="-- Sin asignar --") continue;
            else $name=preg_replace('/\s+/', '', $name); // remove spaces to get friendly with database field naming
            $name=$this->myDBObject->conn->real_escape_string($name); // escape to avoid SQL injection
            $key="Jornada:".$jornada['Numero'];
            $this->fieldList[$key]= array( $index,1,"s",$name," `$name` varchar(255) NOT NULL DEFAULT '', ");
            $this->myLogger->trace("Adding journey {$key} => ".json_encode($this->fieldList[$key]));
            $index--;
        }
        $this->validPageNames=array("Inscriptions");
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
            $jname=preg_replace('/\s+/', '', $jname); // remove spaces to get friendly with database field naming
            $jname=$this->myDBObject->conn->real_escape_string($jname); // escape to avoid SQL injection

            $this->saveStatus("Creating teams for Journey: $jname");
            // select distinct jornadaname from temporary tabla where jornadaname!="" group by jornadaname
            $res=$this->myDBObject->__select("DISTINCT Categoria , $jname AS NombreEquipo",TABLE_NAME,"($jname<>'')","NombreEquipo ASC, Categoria ASC","");
            // parse result to join categories on same team
            $teams=array();
            foreach ($res['rows']as $team) {
                // recuerda que se han eliminado los que tienen la inscripcion en esta jornada en blanco
                $nequipo=trim($team['NombreEquipo']);
                // si la respuesta es si/no/X no hay que declarar equipo: se asigna al equipo por defecto
                if (! is_null( parseYesNo( $nequipo ) ) ) continue;
                // else, miramos si el equipo esta ya declarado
                if (!array_key_exists($nequipo,$teams)) $teams[$nequipo]=''; // team not yet declared. So create
                if (strpos($teams[$nequipo],$team['Categoria'])===FALSE) $teams[$nequipo] .= $team['Categoria'];
            }
            // and now iterate result and store teams into database
            $eq=new Equipos("importInscriptions",$this->prueba['ID'],$jornada['ID']);
            foreach ($teams as $team => $cat) {
                // create teams for this journey
                $this->saveStatus("Creating team: $jname -> $team - $cat");
                // evaluate categories
                $eq->realInsert($cat,$team,/* "Excel imported"*/ "");
            }
        }
        return array( 'operation'=>'import','success'=>'inscribe');
    }

    // iterate over inscriptions and perform it
    public function doInscription() {
        $t=TABLE_NAME;
        // retrieve list of pending inscriptions
        $lista=$this->myDBObject->__select("*",TABLE_NAME,"(Orden=0)","ID ASC","");
        if (!$lista) return "doInscription::getParticipantes() error:".$this->myDBObject->conn->error;
        for ($index=0;$index<$lista['total'];$index++) {
            $item=$lista['rows'][$index];
            $id=$item['ID'];
            $this->saveStatus("Check and Register inscription: {$item['Nombre']} - {$item['NombreGuia']}");
            // evaluate journeys to inscribe into
            $jornadas=0;
            foreach ($this->fieldList as $key => $val) {
                if (strpos($key,'Jornada')===FALSE) continue; // not a journey field
                $numero=intval(explode(':',$key)[1]) - 1;
                $nombre=trim($item[$val[3]]);
                if ($nombre==="") continue; // not inscribed
                $yn=parseYesNo($nombre);
                if (is_null($yn) || ($yn==true)) $jornadas |= (1<<$numero); // field not empty means need to inscribe
            }

            if ($jornadas!=0) { // if zero no journeys to inscribe into, so skip and try next item in list
                $this->myLogger->trace("Inscribiendo perro ID:$id Nombre:{$item['Nombre']} Jornadas:$jornadas");

                // make inscription
                $idperro=$item['DogID'];
                $pagado=intval($item['Pagado']);
                $celo = intval(trim($item['Celo']));
                $obs=$this->myDBObject->conn->real_escape_string($item['Observaciones']);
                $newdorsal=intval($item['Dorsal']);
                $insc=new Inscripciones("excelImport",$this->prueba['ID']);
                $dorsal=$insc->realInsert($idperro,$this->prueba['ID'],$jornadas,$pagado,$celo,$obs);
                if (!is_numeric($dorsal)) return $dorsal; // error
                $dorsal=intval($dorsal);
                // set dorsal if provided
                if ($dorsal!=0) $insc->setDorsal($idperro,$dorsal,$newdorsal);
                // add to proper team when required
                foreach ($this->jornadas as $jornada) {

                    // check if a given journey has teams and if dog is inscribed
                    if ( (intval($jornada['Equipos3']) + intval($jornada['Equipos4']))==0) continue; // not team journey
                    $name=preg_replace('/\s+/', '', $jornada['Nombre']); // remove spaces to get friendly with database field naming
                    $itemTeam=trim($item[$name]);
                    if (!is_null(parseYesNo($itemTeam))) continue; // not inscribed or inscribed into default team
                    // need to inscribe in non-default team: locate team id and perform inscription into requested team
                    $eq= new Equipos("excelImport",$this->prueba['ID'],$jornada['ID']);
                    $tbj=$eq->getTeamsByJornada();
                    foreach($tbj as $team) {
                        if ($team['Nombre']!==$itemTeam) continue;
                        $eq->updateTeam($idperro,$team['ID']);
                        break;
                    }
                    // go to next journey to look for team ones
                }
            }
            // mark entry as already inscribed
            $str ="UPDATE $t SET ORDEN=$id WHERE ID=$id";
            $res=$this->myDBObject->query($str);
            if (!$res) return "doInscription::markAsProcessed($id) error:".$this->myDBObject->conn->error;
            // if inscription done return to client to ask for next iteration
            if ($jornadas!=0) return array( 'operation'=> 'import','success'=> 'inscribe');
            // else continue loop looking for any item with jornadas!=0
        }
        // no inscriptions pending. tell client to end import
        return array( 'operation'=>'import','success'=>'close');
    }
}
?>