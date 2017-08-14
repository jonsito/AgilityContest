<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 2/04/16
 * Time: 16:20
partialscores_reader.php

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__."/../modules/Federations.php");
require_once(__DIR__."/../database/classes/DBObject.php");
require_once(__DIR__."/../database/classes/Entrenamientos.php");
require_once(__DIR__.'/Spout/Autoloader/autoload.php');
require_once(__DIR__.'/dog_reader.php');

class PartialScoresReader extends DogReader {

    protected $prueba;
    protected $jornada;
    protected $manga;

    public function __construct($name,$options) {
        $this->myDBObject = new DBObject($name);
        $this->prueba=$this->myDBObject->__selectAsArray("*","Pruebas","ID={$options['Prueba']}");
        $this->jornada=$this->myDBObject->__selectAsArray("*","Jornada","ID={$options['Jornada']}");
        $this->manga=$this->myDBObject->__selectAsArray("*","Mangas","ID={$options['Manga']}");
        if (!is_array($this->manga)) // realmente prueba y jornada no son necesarias, pero por consistencia se ponen
            throw new Exception("{$name}::construct(): invalid Manga ID: {$options['Manga']}");
        parent::__construct($name,$options);

        // extend default field list
        // name => index, required (1:true 0:false-to-evaluate -1:optional), default
        // add additional fields required to handle inscriptions
        $inscList= array(
            'Team' =>     array (  -17, 0, "i", "Equipo",   " `Equipo` int(4) NOT NULL DEFAULT 0, "), // equipo, a calcular
            'TeamName' => array (  -18,-1, "s", "NombreEquipo"," `NombreEquipo` varchar(255) NOT NULL DEFAULT '', "), // nombre equipo, opcional
            'Games'=>     array (  -19,-1, "i", "Games",    " `Games` int(4) NOT NULL DEFAULT 0, "), // required on games rounds
            'Faults' =>   array (  -20, 1, "i", "Faltas",   " `Faltas` int(4) NOT NULL DEFAULT 0, "), // faltas, requerido
            'Touchs' =>   array (  -21,-1, "i", "Tocados",  " `Tocados` int(4) NOT NULL DEFAULT 0, "), // tocados, opcional
            'Refusals' => array (  -22, 1, "i", "Rehuses",  " `Rehuses` int(4) NOT NULL DEFAULT 0, "), // rehuses, requerido
            'Eliminated'=>array (  -23, 1, "i", "Eliminado"," `Eliminado` int(4) NOT NULL DEFAULT 0, "), // eliminado, requerido
            'NotPresent'=>array (  -24, 1, "i", "NoPresentado", " `NoPresentado` int(4) NOT NULL DEFAULT 0, "), // nopresentado, requerido
            'Tiempo' =>   array (  -25, 1, "f", "Tiempo",   " `Tiempo` double NOT NULL DEFAULT 0, "), // tiempo, requerido
            // los campos penalizacion, calificacion, puntos, y estrellas se calculan en runtime, no se importan
        );
        foreach ($inscList as $key => $data) $this->fieldList[$key]=$data;
        // fix fields according contest type
        $fedobj=Federations::getFederation($this->federation);
        if ($fedobj->isInternational()) { $this->fieldList['Club'][1]=0; $this->fieldList['Country'][1]=1; } // country/club
        // on team rounds, make teamname required
        if (in_array($this->manga['Tipo'],array(8,9,13,14))) $this->fieldList['NombreEquipo'][1]=0;
        // on games rounds, make games required
        if (in_array($this->manga['Tipo'],array(29,30))) $this->fieldList['Games'][1]=0;
        $this->validPageNames=array("Results");
    }

    protected function import_storeExcelRowIntoDB($index,$row) {
        $this->myLogger->enter();
        // compose insert sequence
        $str1= "INSERT INTO {$this->tablename} (";
        $str2= "ID, NombreClub ) VALUES (";
        $club="";
        $fecha=0;
        // for each row evaluate field name and get content from provided row
        // notice that
        foreach ($this->fieldList as $key => $val) {
            if ( ($val[0]<0) || ($val[1]==0)) continue; // field not provided or to be evaluated by importer
            if ($key!=='Club') $str1 .= "{$val[3]}, "; // add field name except when parsing club
            $item=$row[$val[0]];
            switch ($key){
                case 'Club':
                    $club=$this->myDBObject->conn->real_escape_string($item);
                    break;
                case 'Country':
                    $a=$this->myDBObject->conn->real_escape_string($item);
                    $str2.="'{$a}', ";
                    if ($club==="") $club=$a; // by default assume club= country until defined
                    break;
                case 'Date':
                    // excel provides dates as an integer indicating number of days after 1900
                    $fecha= excelTimeToSeconds($item);
                    $s=gmdate("Y-m-d",$fecha); // also, excel use CET, so use gmdate to evaluate properly
                    $str2.=" '{$s}', ";
                    break;
                case 'Check-in':
                    /* no break */
                case 'Veterinary':
                    /* no break */
                case 'Start':
                    // also excel declares times as a 24 hour decimal fraction
                    $seconds= excelTimeToSeconds($item);
                    if ($item<1) $seconds+=$fecha; // check for full datetime provided
                    $s=gmdate("Y-m-d H:i:s",$seconds);
                    $str2.=" '{$s}', ";
                    break;
                case 'Duration':
                    // if seconds provide store "as is", if provided min'secs" convert into seconds
                    $s=str_replace('"','',$item); // comilla doble "
                    $s=str_replace(' ','',$s); // espacios
                    $s=str_replace("'",":",$s); // comilla simple '
                    $a=explode(":",$s);
                    $s=(count($a)==1)?intval($a[0]):60*intval($a[0])+intval($a[1]);
                    $str2.=" {$s}, ";
                    break;
                default:
                    switch ($val[2]) {
                        case "s": // string
                            $a=$this->myDBObject->conn->real_escape_string($item);
                            $str2.="'{$a}', ";
                            break;
                        case "i":
                            $a=intval($item);
                            $str2 .= " {$a}, "; // integer
                            break;
                        case "b":
                            $a=(toBoolean($item))?1:0;
                            $str2 .= " {$a}, "; // boolean as 1/0
                            break;
                        case "f":
                            $a=floatval($item);
                            $str2 .= " {$a}, "; // float
                            break;
                        default:
                            // escape to avoid sql injection issues
                            $a=$this->myDBObject->conn->real_escape_string($item);
                            $str2 .= " {$a}, ";
                    }
            }
        }
        $str ="$str1 $str2 {$index} , '$club' );"; // compose insert string
        $res=$this->myDBObject->query($str);
        if (!$res) {
            $error=$this->myDBObject->conn->error;
            throw new Exception("{$this->name}::populateTable(): Error inserting row $index ".json_encode($row)."\n$error");
        }
        $this->myLogger->leave();
        return 0;
    }

    /**
     * @return {array} data to be evaluated
     */
    public function parse() {
        $this->myLogger->enter();
        $res=$this->myDBObject->__select(
        /* SELECT */ "*",
            /* FROM   */ TABLE_NAME,
            /* WHERE  */ "( ClubID = 0 )",
            /* ORDER BY */ "ClubID ASC",
            /* LIMIT */  ""
        );
        foreach ($res['rows'] as $item ) {
            $found=$this->findAndSetClub($item);
            if (is_string($found)) throw new Exception("import parse: $found");
            if (is_bool($found)) {
                if ($found===true) // item found and match: notify and return
                    return array('operation'=> 'parse', 'success'=> 'ok', 'search' => $item, 'found' => $found['rows']);
                else // item not found: create a default item
                    return array('operation'=> 'parse', 'success'=> 'fail', 'search' => $item, 'found' => array());
            }
            // nultiple matching items found: ask
            return array('operation'=> 'parse', 'success'=> 'fail', 'search' => $item, 'found' => $found['rows']);
        }
        // arriving here means no more items to analyze. So tell user to proccedd with import
        $this->myLogger->leave();
        return array('operation'=> 'parse', 'success'=> 'done');
    }

    // retorna array L,M,S,T,-
    private function howManyDogs($team){
        $list=$this->myDBObject->__select(
        /*select*/    "count(*) AS Numero,Categoria",
        /* from */    "Inscripciones,PerroGuiaClub",
        /* where */   "( Prueba={$this->prueba['ID']} ) AND (Inscripciones.Perro=PerroGuiaClub.ID) AND (Club=$team)",
        /* order by*/ "Categoria ASC",
        /* limit */   "",
        /* group by*/"Categoria"
        );
        $res= array( 'L' => 0, 'M' => 0, 'S'=>0, 'T' => 0, '-' => 0, 'Total' => 0);
        foreach($list['rows'] as $item) {
            $res[$item['Categoria']] += $item['Numero'];
            $res['Total']+=$item['Numero'];
        }
        return $res;
    }

    /* return an integer (number of seconds since 1-Enero-1970 */
    private function getTime($str,$deftime) {
        $def=date_parse(date('Y-m-d H:i:s',$deftime));
        if($def===FALSE) {
            $this->myLogger->warn("Cannot parse current provided time: '$str'");
            $def=time();
        }
        $cur=date_parse($str);
        if ($cur===FALSE) {
            return mktime($def['hour'], $def['minute'], $def['second'], $def['month'], $def['day'], $def['year']);
        }
        // combine def and cur
        return mktime(
            empty($cur['hour'])?    $def['hour']:$cur['hour'],
            empty($cur['minute'])?  $def['minute']:$cur['minute'],
            empty($cur['second'])?  $def['second']:$cur['second'],
            empty($cur['month'])?   $def['month']:$cur['month'],
            empty($cur['day'])?     $def['day']:$cur['day'],
            empty($cur['year'])?    $def['year']:$cur['year']
        );
    }

    function beginImport() {
        $this->myLogger->enter();
        // borramos datos de la tabla de entrenamientos de la prueba
        $str="DELETE FROM Entrenamientos WHERE ( Prueba={$this->prueba['ID']})";
        $this->myDBObject->query($str);
        $entries=$this->myDBObject->__select("*",TABLE_NAME,"","")['rows'];
        $orden=1;

        // usaremos estas variables para controlar asignar los tiempos de los campos que esten vacios
        $defTime=time(mktime(0,0,0,date("m"),date("d"),date("Y"))); // default time

        $mode=intval($this->myConfig->getEnv("training_type"));
        $dtime=intval($this->myConfig->getEnv("training_time"));
        $gtime=intval($this->myConfig->getEnv("training_grace"));

        $trobj=new Entrenamientos("ExcelImportEntrenamientos",$this->prueba['ID']);
        foreach ($entries as $row) {
            $data=array();
            $items=$this->howManyDogs($row['ClubID']);
            $data['Club']=$row['ClubID'];
            $data['Orden']=$orden;
            // obtenemos fecha
            $defTime=$this->getTime($row['Fecha'],$defTime);
            $data['Fecha'] = date('Y-m-d H:i',$defTime);
            //hora de la firma del equipo
            $firma=$this->getTime($row['Firma'],$defTime+300); //
            $data['Firma'] = date('Y-m-d H:i',$firma);
            // hora de la revision veterinaria
            $veterinario=$this->getTime($row['Veterinario'],$defTime+300); // 5 minutes after check-in
            $data['Veterinario'] = date('Y-m-d H:i',$veterinario);
            // hora de entrada a pista
            $comienzo=$this->getTime($row['Comienzo'],$defTime+3600); // 1 hour after check-in
            $data['Comienzo']= date('Y-m-d H:i',$comienzo);
            // si no nos dan duracion la evaluamos
            $data['Duracion']=$row['Duracion'];
            if ($row['Duracion']==0)
                $data['Duracion']=($mode==0)? $items['Total']*$dtime : max($items['L'],$items['M'],$items['S'],$items['T'])*$dtime;

            $data['Key1']=$row['Key1'];
            $data['Key2']=$row['Key2'];
            $data['Key3']=$row['Key3'];
            $data['Key4']=$row['Key4'];
            $data['Value1']=($row['Value1']==0)?$items['L']:$row['Value1'];
            $data['Value2']=($row['Value2']==0)?$items['M']:$row['Value2'];
            $data['Value3']=($row['Value3']==0)?$items['S']:$row['Value3'];
            $data['Value4']=($row['Value4']==0)?$items['T']:$row['Value4'];
            $data['Observaciones']=$row['Observaciones'];
            $this->saveStatus("Importing training data session for entry: '{$row['NombreClub']}'");
            $res=$trobj->insert($data);
            if ($res!=="") return $res; // will throw exception and mark error in client
            $orden++;
            $defTime=$defTime+3600+$data['Duracion']+$gtime;
        }
        $this->myLogger->leave();
        return array( 'operation'=>'import','success'=>'close');
    }
}
?>