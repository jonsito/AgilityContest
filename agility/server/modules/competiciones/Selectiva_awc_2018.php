<?php

/*
Selectiva_awc_2018.php

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
class Selectiva_awc_2018 extends Puntuable_RSCE_2018 {

    protected $poffset=array('L'=>0,'M'=>0,'S'=>0,'T'=>0); // to skip not-league competitors (partial scores)
    protected $pfoffset=array('L'=>0,'M'=>0,'S'=>0,'T'=>0); // to skip not-league competitors (final scores)

    function __construct($name="Selectiva AWC 2018") {
        parent::__construct($name);
        $this->federationID=0;
        $this->competitionID=11;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20180125_0931";
        $this->selectiva=1;
    }

    /**
     * Provide default TRS/TRM/Recorrido values for a given competitiona at
     * Round creation time
     * @param {integer} $tipo Round tipe as declared as Mangas::TipoManga
     * @return {array} trs array or null if no changes
     */
    public function presetTRSData($tipo) {
        if ( ($tipo!=6) && ($tipo!=11) ) return parent::presetTRSData($tipo); // Not grade 3, use parent
        $manga=array();
        $manga['Recorrido']=0; // 0:separados 1:mixto 2:conjunto
        $manga['TRS_L_Tipo']=1;$manga['TRS_L_Factor']=0;$manga['TRS_L_Unit']='s'; // best dog + 0s no roundup
        $manga['TRM_L_Tipo']=1;$manga['TRM_L_Factor']=50;$manga['TRM_L_Unit']='%'; // trs + 50 %
        $manga['TRS_M_Tipo']=1;$manga['TRS_M_Factor']=0;$manga['TRS_M_Unit']='s';
        $manga['TRM_M_Tipo']=1;$manga['TRM_M_Factor']=50;$manga['TRM_M_Unit']='%';
        $manga['TRS_S_Tipo']=1;$manga['TRS_S_Factor']=0;$manga['TRS_S_Unit']='s';
        $manga['TRM_S_Tipo']=1;$manga['TRM_S_Factor']=50;$manga['TRM_S_Unit']='%';
        $manga['TRS_T_Tipo']=1;$manga['TRS_T_Factor']=0;$manga['TRS_T_Unit']='s'; // not used but required
        $manga['TRM_T_Tipo']=1;$manga['TRM_T_Factor']=50;$manga['TRM_T_Unit']='%';
        return $manga;
    }

    /**
     * Re-evaluate and fix -if required- results data used to evaluate TRS for
     * provided $prueba/$jornada/$manga
     * @param {object} $manga Round data and trs parameters
     * @param {array} $data Original results provided for evaluation
     * @param {integer} $mode which categories have to be selected
     * @param {boolean} $roundUp on true round UP SCT and MCT to nearest second
     * @return {array} final data to be used to evaluate trs/trm
     */
    public function checkAndFixTRSData($manga,$data,$mode,&$roundUp) {
        // just mark contest as selective.
        // it's overriden by European open declaration
        // remember that prueba,jornada and manga are objects, so passed by reference
        $this->prueba->Selectiva = 1; // not really required, just to be sure
        if (($manga->Tipo==6) || ($manga->Tipo==11)) $roundUp=false;
        return $data;
    }

    /**
     * Starting at 2017 season license naming convention changed. Now there is no way to detect
     * if a dog is registered in LOE/RRC by just looking at license number
     *
     * So we use a different approach
     * - On startup modify database to make sure that every dog with old license style has LOE/RRC
     * - if not, create a dummy LOE/RRC entry
     * - Change this code to check LOE/RRC instead of license number
     * @param $loe Inscription number for LOE/RRC. In old style licences may have fake values
     */
    function canReceivePoints($loe){
        $loe=strval($loe);
        // remove dots, spaces and dashes
        $loe=str_replace(" ","",$loe);
        $loe=str_replace("-","",$loe);
        $loe=str_replace(".","",$loe);
        // algunos cachondos ponen "si/no" para indicar que si/no tiene loe/rrc
        $yn=parseYesNo($loe);
        if ($yn!==null) return $yn;
        return ($loe!=="")?true:false;
        /*
        $loe=strtoupper($loe);
        if (strlen($loe)<4) {
            if (is_numeric($loe)) return true; // licenses from 0 to 999
            return false;
        }
        if (substr($loe,0,1)=='0') return true; // 0000 to 9999
        if (substr($loe,0,1)=='A') return true; // A000 to A999
        if (substr($loe,0,1)=='B') return true; // B000 to B999
        if (substr($loe,0,1)=='C') return true; // C000 to C999
        return false;
        */
    }

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($m,&$perro,$puestocat) {
        if ($perro['Grado']!=="GIII") {
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        // arriving here means grado III
        if ($this->selectiva==0) {
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        // si no tiene excelente no puntua
        if ( ($perro['Penalizacion']>=6.0)) {
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        // comprobamos si el perro es mestizo
        if (! $this->canReceivePoints($perro['LOE_RRC']) ) { // perro mestizo o extranjero no puntua
            $this->poffset[$perro['Categoria']]++; // mark to skip point assignation
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        $pts=array("25","20","16","12","8","6","4","3","2","1"); // puntuacion manga de agility
        if (intval($m->Tipo)==11) $pts=array("20","16","12","8","6","5","4","3","2","1"); // puntuacion manga de jumping
        // solo puntuan los 10 primeros
        $puesto=$puestocat[$perro['Categoria']]-$this->poffset[$perro['Categoria']];
        if ( ($puesto>10) || ($puesto<=0) ) {
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        // si llegamos aqui tenemos los 10 primeros perros una prueba selectiva en grado 3 con un perro no mestizo que ha sacado excelente :-)
        $pt1=$pts[$puesto-1];
        if ($perro['Penalizacion']>0)	{
            $perro['Calificacion'] = _("Excellent")." $pt1";
            $perro['CShort'] = _("Exc")." $pt1";
        }
        if ($perro['Penalizacion']==0)	{
            $perro['Calificacion'] = _("Excellent")." (p) $pt1";
            $perro['CShort'] = _("ExP")." $pt1";
        }
        // en una selectiva solo puntua el primer perro, por lo que no tiene demasiado sentido la evaluacion de puntos y estrellas
        // de momento ponemos lo ponemos... luego ya veremos
        $perro['Puntos']=($puesto>1)?0:1;
        $perro['Estrellas']=0;
        $perro['Extras']=0;
        if ($puesto>1) return;
        foreach ( $this->puntos as $item) {
            if ($perro['Grado']!==$item[0]) continue;
            // comprobamos si estamos en agility o en jumping (1:agility,2:jumping,3:third round and so )
            $offset=( (Mangas::$tipo_manga[$m->Tipo][5]) == 1)?0/*agility*/:3/*jumping*/;
            $base=2;
            if ($perro['Categoria']==="M") $base=3;
            if ($perro['Categoria']==="S") $base=4;
            // si la velocidad es igual o superior se apunta tanto. notese que el array está ordenado por grad/velocidad
            if ($perro['Velocidad']>=$item[$base+$offset]) {
                $perro['Puntos'] = $item[8];
                $perro['Estrellas'] = $item[9];
            }
        }
    }


    /**
     * Evalua la calificacion final del perro
     * @param {array} $mangas informacion {object} de las diversas mangas
     * @param {array} $resultados informacion {array} de los resultados de cada manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalFinalCalification($mangas,$resultados,&$perro,$puestocat){
        $grad=$perro['Grado']; // cogemos la categoria
        if ($grad==="GI") { // en grado uno puntua como prueba normal
            parent::evalFinalCalification($mangas,$resultados,$perro,$puestocat);
            return;
        }
        if ($grad==="GII") { // grado dos puntua como prueba normal
            parent::evalFinalCalification($mangas,$resultados,$perro,$puestocat);
            return;
        }
        if ($grad!=="GIII") { // ignore other extrange grades
            do_log("Invalid grade '$grad' found");
            return;
        }
        // arriving here means grado III
        if ($this->selectiva==0){
            parent::evalFinalCalification($mangas,$resultados,$perro,$puestocat);
            return;
        }
        // arriving here means prueba selectiva and Grado III
        if ( ! $this->canReceivePoints($perro['LOE_RRC']) ) {  // comprobamos si el perro es mestizo o extranjero
            $this->pfoffset[$perro['Categoria']]++; // mark to skip point assignation
            // parent::evalFinalCalification($mangas,$resultados,$perro,$puestocat);
            $perro['Calificacion']= "No puntua";
            return;
        }

        // en la temporada 2017 el trs para individual y equipos es el mismo
        // la calificacion conjunta no puntua por individual, solo por equipos
        // lo que se pondrá como calificacion es X / Y
        // donde X es la suma de las calificaciones individuales
        //       Y es la clasificacion por equipos
        // solo puntuan por conjunta los 10 primeros perros no mestizos/extranjeros que tengan doble excelente

        $ptsglobal = array("20", "16", "12", "8", "7", "6", "4", "3", "2", "1"); //puestos por general (si excelentes en ambas mangas)

        // manga 1
        $pt1 = "0";
        if ($resultados[0] !== null) { // extraemos los puntos de la primera manga
            $x=trim(substr($perro['C1'],-2));
            $pt1=(is_numeric($x))?$x:"0";
        }
        // manga 2
        $pt2="0";
        if ($resultados[1] !== null) { // extraemos los puntos de la segunda manga
            $x=trim(substr($perro['C2'],-2));
            $pt2=(is_numeric($x))?$x:"0";
        }
        // conjunta
        $pfin="0";
        if ( (trim($pt1)=="") || ($pt1==0) ) $pt1="-";
        if ( (trim($pt2)=="") || ($pt2==0) ) $pt2="-";
        if ( ($resultados[0]===null) || ($resultados[1]===null)) { // si falta alguna manga no puntua en conjunta
            $perro['Calificacion']= "$pt1 / $pt2 / -";
            return;
        }
        // si no tiene doble excelente no puntua en conjunta
        if ( ($perro['P1']>=6.0) || ($perro['P2']>=6.0) ) {
            $perro['Calificacion']= "$pt1 / $pt2 / -";
            return;
        }
        // evaluamos puesto real una vez eliminados los "extranjeros"
        $puesto=$puestocat[$perro['Categoria']]-$this->pfoffset[$perro['Categoria']];
        // si esta entre los 10 primeros cogemos los puntos
        if ($puesto<11) $pfin=$ptsglobal[$puesto-1];
        // y asignamos la calificacion final
        $perro['Calificacion']="$pt1 / $pt2 / $pfin";
        return; // should be overriden
    }
}