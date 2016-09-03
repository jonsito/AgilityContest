<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 2/04/16
 * Time: 16:20
trainingtable_reader.php

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
require_once(__DIR__."/../database/classes/Entrenamientos.php");
require_once(__DIR__.'/Spout/Autoloader/autoload.php');
require_once(__DIR__.'/dog_reader.php');

class EntrenamientosReader extends DogReader {

    protected $prueba;

    public function __construct($name,$pruebaID,$options) {
        $this->myDBObject = new DBObject($name);
        $this->prueba=$this->myDBObject->__selectAsArray("*","Pruebas","ID=$pruebaID");
        if (!is_array($this->prueba))
            throw new Exception("EntrenamientosReader::construct(): invalid Prueba ID: $pruebaID");
        parent::__construct("ImportExcel(training session)",$this->prueba['RSCE'],$options);

        // instead of using parent field list, use our own one
        $this->fieldList= array(
            // name => index, required (1:true 0:false-to-evaluate -1:optional), default
            // 'ID' =>      array (  -1,  0,  "i", "ID",        " `ID` int(4) UNIQUE NOT NULL, "), // automatically added
            // Prueba: fixed
            // Orden: to be evaluated
            // club related data
            // in international contests user can provide ISO country name either in "Club" or in "Country" field
            'ClubID' =>     array (  -2,    0, "i", "ClubID",    " `ClubID` int(4) NOT NULL DEFAULT 0, "),  // to be evaluated by importer
            'Club' =>       array (  -3,    1, "s", "NombreClub"," `NombreClub` varchar(255) NOT NULL,"),  // Club's Name. required
            'Country' =>    array (  -4,   -1, "s", "Pais",      " `Pais` varchar(255) NOT NULL,"),  // Country. optional
            // datos de horarios y duracion del entrenamiento
            'Date' =>       array (  -5,    1, "s", "Fecha",     " `Fecha` date DEFAULT '2016-01-01', "), // required
            'Check-in' =>    array (  -6,   1, "s", "Firma",     " `Firma` timestamp  DEFAULT 0 , "), // required
            'Veterinary' => array (  -7,    1, "s", "Veterinario"," `Veterinario` timestamp DEFAULT  0 , "), // required
            'Start' =>      array (  -8,    1, "s", "Comienzo",  " `Comienzo` timestamp DEFAULT  0 , "), // required
            'Duration' =>   array (  -9,    1, "i", "Duracion",  " `Duracion` int(4) NOT NULL DEFAULT 0, "), // required segundos
            // datos de los cuatro rings
            'Key1' =>       array (  -10,   1, "s", "Key1",      " `Key1` varchar(32) DEFAULT 'L', "), // required
            'Value1' =>     array (  -11,  -1, "i", "Value1",    " `Value1` int(4) NOT NULL DEFAULT 0, "), // optional
            'Key2' =>       array (  -12,   1, "s", "Key2",      " `Key2` varchar(32) DEFAULT 'M', "), // required
            'Value2' =>     array (  -13,  -1, "i", "Value2",    " `Value2` int(4) NOT NULL DEFAULT 0, "), // optional
            'Key3' =>       array (  -14,   1, "s", "Key3",      " `Key3` varchar(32) DEFAULT 'S', "), // required
            'Value3' =>     array (  -15,  -1, "i", "Value3",    " `Value3` int(4) NOT NULL DEFAULT 0, "), // optional
            'Key4' =>       array (  -16,  -1, "s", "Key4",      " `Key4` varchar(32) DEFAULT 'T', "), // 4th ring is optional in 3 height
            'Value4' =>     array (  -17,  -1, "i", "Value4",    " `Value4` int(4) NOT NULL DEFAULT 0, "), // 4th ring is optional in 3 height
            // comentarios
            'Comments' =>   array (  -18,  -1, "s", "Observaciones", " `Observaciones` varchar(255) DEFAULT '', "),  // optional
            // Estado: default -1
        );
        // fix fields according contest type
        $fedobj=Federations::getFederation($this->federation);
        if ($fedobj->isInternational()) { $this->fieldList['Club'][1]=-1; $this->fieldList['Country'][1]=1; } // country/club
        if ($fedobj->get('Heights')==4) { $this->fieldList['Key4'][1]=1; $this->fieldList['Value4'][1]=1; } // required on 4 heights
        $this->validPageNames=array("Trainings");
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
        if ($cur===FALSE) return mktime($def['hour'], $def['minute'], $def['second'], $def['month'], $def['day'], $def['year']);
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

    // convert m's" into seconds
    private function parseMinSecs($str) {
        $str=str_replace('"','',$str); // comilla doble "
        $str=str_replace(' ','',$str); // espacios
        $str=str_replace("'",":",$str); // comilla simple '
        $a=explode(":",$str);
        return 60*$a[0]+$a[1];
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
            if ($row['Duracion']==0)
                $row['Duracion']=($mode==0)? $items['Total']*$dtime : max($items['L'],$items['M'],$items['S'],$items['T'])*$dtime;
            $data['Duracion']=$this->parseMinSecs($row['Duracion']);

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