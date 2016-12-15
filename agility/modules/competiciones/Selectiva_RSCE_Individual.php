<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Selectiva_RSCE_Individual extends Puntuable_RSCE_2017 {

    private $poffset=array('L'=>0,'M'=>0,'S'=>0,'T'=>0); // to skip not-league competitors (partial scores)
    private $pfoffset=array('L'=>0,'M'=>0,'S'=>0,'T'=>0); // to skip not-league competitors (final scores)

    function __construct() {
        parent::__construct("Prueba selectiva AWC 2017");
        $this->federationID=0;
        $this->competitionID=1;
    }

    /**
     * Evaluate if a dog has a mixBreed License
     * @param $lic
     */
    function validLicense($lic){
        $lic=strval($lic);
        // remove dots, spaces and dashes
        $lic=str_replace(" ","",$lic);
        $lic=str_replace("-","",$lic);
        $lic=str_replace(".","",$lic);
        $lic=strtoupper($lic);
        if (strlen($lic)<4) {
            if (is_numeric($lic)) return true; // licenses from 0 to 999
            return false;
        }
        if (strlen($lic)>4) return false; // rsce licenses has up to 4 characters
        if (substr($lic,0,1)=='0') return true; // 0000 to 9999
        if (substr($lic,0,1)=='A') return true; // A000 to A999
        if (substr($lic,0,1)=='B') return true; // B000 to B999
        if (substr($lic,0,1)=='C') return true; // C000 to C999
        return false;
    }

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $p datos de la prueba
     * @param {object} $j datos de la jornada
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($p,$j,$m,&$perro,$puestocat) {
        if ($perro['Grado']!=="GIII") {
            parent::evalPartialCalification($p,$j,$m,$perro,$puestocat);
            return;
        }
        // arriving here means grado III
        if ($p->Selectiva==0) { // need to be marked as selectiva to properly evaluate TRS in GIII
            parent::evalPartialCalification($p,$j,$m,$perro,$puestocat);
            return;
        }
        // si no tiene excelente no puntua
        if ( ($perro['Penalizacion']>=6.0)) {
            parent::evalPartialCalification($p,$j,$m,$perro,$puestocat);
            return;
        }
        // comprobamos si el perro es mestizo
        if (! $this->validLicense($perro['Licencia']) ) { // perro mestizo o extranjero no puntua
            $this->poffset[$perro['Categoria']]++; // mark to skip point assignation
            parent::evalPartialCalification($p,$j,$m,$perro,$puestocat);
            return;
        }
        $pts=array("25","20","16","12","8","6","4","3","2","1"); // puntuacion manga de agility
        if (intval($m->Tipo)==11) $pts=array("20","16","12","8","6","5","4","3","2","1"); // puntuacion manga de jumping
        // solo puntuan los 10 primeros
        $puesto=$puestocat[$perro['Categoria']]-$this->pfoffset[$perro['Categoria']];
        if ( ($puesto>10) || ($puesto<=0) ) {
            parent::evalPartialCalification($p,$j,$m,$perro,$puestocat);
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
    }


    /**
     * Evalua la calificacion final del perro
     * @param {object} $p datos de la prueba
     * @param {object} $j datos de la jornada
     * @param {object} $m1 datos de la primera manga
     * @param {object} $m2 datos de la segunda manga
     * @param {array} $c1 resultados de la primera manga
     * @param {array} $c2 resultados de la segunda manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalFinalCalification($p,$j,$m1,$m2,$c1,$c2,&$perro,$puestocat){
        $grad=$perro['Grado']; // cogemos la categoria
        if ($grad==="GI") { // en grado uno puntua como prueba normal
            parent::evalFinalCalification($p,$j,$m1,$m2,$c1,$c2,$perro,$puestocat);
            return;
        }
        if ($grad==="GII") { // grado dos puntua como prueba normal
            parent::evalFinalCalification($p,$j,$m1,$m2,$c1,$c2,$perro,$puestocat);
            return;
        }
        if ($grad!=="GIII") { // ignore other extrange grades
            do_log("Invalid grade '$grad' found");
            return;
        }
        // arriving here means grado III
        if ($p->Selectiva==0){ // need to be marked as selectiva to properly evaluate TRS in GIII
            parent::evalFinalCalification($p,$j,$m1,$m2,$c1,$c2,$perro,$puestocat);
            return;
        }
        // arriving here means prueba selectiva and Grado III
        if ( ! $this->validLicense($perro['Licencia']) ) {  // comprobamos si el perro es mestizo o extranjero
            $this->pfoffset[$perro['Categoria']]++; // mark to skip point assignation
            parent::evalFinalCalification($p,$j,$m1,$m2,$c1,$c2,$perro,$puestocat);
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
        if ($c1 != null) { // extraemos los puntos de la primera manga
            $x=trim(substr($perro['C1'],-2));
            $pt1=(is_numeric($x))?$x:"0";
        }
        // manga 2
        $pt2="0";
        if ($c2!=null) { // extraemos los puntos de la segunda manga
            $x=trim(substr($perro['C2'],-2));
            $pt2=(is_numeric($x))?$x:"0";
        }
        // conjunta
        $pfin="0";
        $pi=intval($pt1)+intval($pt2);
        if ( ($c1==null) || ($c2==null)) { // si falta alguna manga no puntua en conjunta
            $perro['Calificacion']= "$pi / -";
            return;
        }
        // si no tiene doble excelente no puntua en conjunta
        if ( ($perro['P1']>=6.0) || ($perro['P2']>=6.0) ) {
            $perro['Calificacion']= "$pi / -";
            return;
        }
        // evaluamos puesto real una vez eliminados los "extranjeros"
        $puesto=$puestocat[$perro['Categoria']]-$this->pfoffset[$perro['Categoria']];
        // si esta entre los 10 primeros cogemos los puntos
        if ($puesto<11) $pfin=$ptsglobal[$puesto-1];
        // y asignamos la calificacion final
        $perro['Calificacion']="$pi / $pfin";

        return; // should be overriden
    }
}