<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
require_once(__DIR__ . "/Puntuable_RSCE_2020.php");

class Puntuable_RSCE_2022 extends Puntuable_RSCE_2020 {
    function __construct($name="Punt. Temporada 2022 (CE 2023)") {
        parent::__construct($name);
        $this->federationID=0;
        $this->competitionID=23;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20212101_1100";
        $this->trms=array( 2.5 /*agility*/, 3.0 /*jumping*/ );
    }

    public function checkAndFixTRSData(&$manga,$data,$mode,&$roundUp) {
        // in 2022, you can declare a dog "out of competition
        // internally they are stored in "Celo" field with mask 0x02
        $res=array();
        for($n=0;$n<count($data);$n++) {
            if ( (0x02 & intval($data[$n]['Celo']))!==0 ) continue;
            array_push($res,$data[$n]);
        }
        return $res;
    }

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($m,&$perro,$puestocat) {

        // comprueba que las mangas sean puntuables
        if (! in_array($m->Tipo, array(3 /*GI-1*/, 4/*GI*/, 5/*A2*/,6/*A3*/,10/*J2*/,11/*J3*/,17/*GI-3*/))) {
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }

        // si estamos en preagility, grado 1 o no tiene cero puntos de penalizacion, utiliza la puntuacion estandard
        if ($perro['Grado']==="P.A.") {
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        if ($perro['Grado']==="GI") {
            parent::evalPartialCalification($m,$perro,$puestocat);
            $perro['Estrellas']=0;
            $perro['Extras']=0;
            if($perro['Penalizacion']==0) $perro['Puntos']=1;
            return;
        }
        if ($perro['Penalizacion']>0) {
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        $perro['Calificacion'] = _("Excellent")." P.";
        $perro['CShort'] = "Ex P.";
        $perro['Puntos'] = 1;
        $perro['Estrellas'] = 0;
        $perro['Extras'] = 0;
        foreach ( $this->puntos as $item) {
            if ($perro['Grado']!==$item[0]) continue;
            // comprobamos si estamos en agility o en jumping (1:agility,2:jumping,3:third round and so )
            $offset=( (Mangas::$tipo_manga[$m->Tipo][5]) == 1)?0/*agility*/:3/*jumping*/;
            $base=2;
            switch($perro['Categoria']) {
                case "X": case "L": $base=2; break;
                case "M": $base=3; break;
                case "S":case "T": $base=4; break;
            }
            // si la velocidad es igual o superior se apunta tanto. notese que el array está ordenado por grad/velocidad
            if ($perro['Velocidad']>=$item[$base+$offset]) {
                // en la temporada 2022 los perros que compiten fuera de altura no puntuan
                if ((0x02 & intval($perro['Celo'])) !== 0) {
                    $perro['Calificacion'] = _("Excellent");
                    $perro['CShort'] = _("Exc");
                    $perro['Puntos'] = 0;
                    $perro['Estrellas'] = 0;
                    $perro['Extras'] = 0;
                } else {
                    $perro['Calificacion'] = _("Excellent")." ".$item[1];
                    $perro['CShort'] = "Ex ".$item[1];
                    $perro['Puntos'] = $item[8];
                    $perro['Estrellas'] = $item[9];
                    $perro['Extras'] = $item[10];
                }
            }
        }
    }

}

?>