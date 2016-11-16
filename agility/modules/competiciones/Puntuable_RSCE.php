<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Puntuable_RSCE extends Competitions {
    function __construct() {
        parent::__construct("Puntuable C.E. RSCE");
        $this->federationID=0;
        $this->competitionID=0;
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
        if (intval($p->Selectiva)==0) {
            parent::evalPartialCalification($p,$j,$m,$perro,$puestocat);
            return;
        }
        // arriving here means prueba selectiva and Grado III
        // comprobamos si el perro es mestizo
        if (! $this->validLicense($perro['Licencia']) ) { // perro mestizo o extranjero no puntua
            parent::evalPartialCalification($p,$j,$m,$perro,$puestocat);
            return;
        }
        // si no tiene excelente no puntua
        if ( ($perro['Penalizacion']>=6.0)) {
            parent::evalPartialCalification($p,$j,$m,$perro,$puestocat);
            return;
        }
        $pts=array("25","20","16","12","8","6","4","3","2","1"); // puntuacion manga de agility
        if (intval($m->Tipo)==11) $pts=array("18","14","11","8","6","5","4","3","2","1"); // puntuacion manga de jumping
        // solo puntuan los 10 primeros
        if ( ($puestocat[$perro['Categoria']]>10) || ($puestocat[$perro['Categoria']]<=0) ) {
            parent::evalPartialCalification($p,$j,$m,$perro,$puestocat);
            return;
        }
        // si llegamos aqui tenemos los 10 primeros perros una prueba selectiva en grado 3 con un perro no mestizo que ha sacado excelente :-)
        $pt1=$pts[$puestocat[$perro['Categoria']]-1];
        if ($perro['Penalizacion']>0)	{
            $perro['Calificacion'] = _("Exc")." - $pt1";
            $perro['CShort'] = _("Exc");
        }
        if ($perro['Penalizacion']==0)	{
            $perro['Calificacion'] = _("Exc")." (p) - $pt1";
            $perro['CShort'] = _("Ex P");
        }
    }

}