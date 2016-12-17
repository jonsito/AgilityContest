<?php

/**
 *
 * Sistema de calificacion para las pruebas puntuables C.E. RSCE Temporada 2017
 * En grado 1 se obtiene punto por cada excelente a cero
 *
 * En grado 2 y 3 Se obtienen puntos por cada excelente a cero con velocidad superior a:
 *
 * GII: Agility 3.5m/s Jumping 3.8m/s
 * GIII: Agility 4.1m/s Jumping 4.5m/s
 * Además los perros que hagan el recorrido a cero con una velocidad superior a 5.1(agility) / 5.5(Jumping)
 * obtendran un punto extra
 *
 * Para la clasificacion para el C.E. Se exigen seis excelentes a cero en cada manga,
 * en los que al menos 3 de ellos tienen que tener puntos
 */
class Puntuable_RSCE_2017 extends Competitions {

   protected $puntos;

    function __construct($name="Prueba puntuable C.E. RSCE 2017") {
        parent::__construct($name);
        $this->federationID=0;
        $this->competitionID=5;
        $this->puntos=array(
            /* grado      puntos  AgL     AgM    AgS    JpL     JpM     JpS */
            array("GII",    "1",  3.8,    3.6,   3.6,   4.0,    3.8,    3.8 ),
            array("GII",    "2",  5.3,    5.1,   5.1,   5.5,    5.3,    5.3 ),
            array("GIII",   "1",  4.7,    4.5,   4.5,   4.9,    4.7,    4.7 ),
            array("GIII",   "2",  5.3,    5.1,   5.1,   5.5,    5.3,    5.3 ),
        );
    }

    /**
     * Re-evaluate and fix -if required- results data used to evaluate TRS for
     * provided $prueba/$jornada/$manga
     * @param {object} $manga Round data and trs parameters
     * @param {array} $data Original results provided for evaluation
     * @return {array} final data to be used to evaluate trs/trm
     */
    public function checkAndFixTRSData($manga,$data) {

        // en pruebas puntuables RSCE de la temporada 2017
        // el trs para grado 3 es el mejor tiempo * 1.15 y con redondeo hacia arriba

        // remember that prueba,jornada and manga are objects, so passed by reference
        $this->prueba->Selectiva=0; // to allow round up
        if ($manga->Grado==="GIII") {
            $manga->TRS_L_Tipo=1;$manga->TRS_L_Factor=15;$manga->TRS_L_Unit='%';
            $manga->TRM_L_Tipo=1;$manga->TRM_L_Factor=50;$manga->TRM_L_Unit='%';
            $manga->TRS_M_Tipo=1;$manga->TRS_M_Factor=15;$manga->TRS_M_Unit='%';
            $manga->TRM_M_Tipo=1;$manga->TRM_M_Factor=50;$manga->TRM_M_Unit='%';
            $manga->TRS_S_Tipo=1;$manga->TRS_S_Factor=15;$manga->TRS_S_Unit='%';
            $manga->TRM_S_Tipo=1;$manga->TRM_S_Factor=50;$manga->TRM_S_Unit='%';
        }

        return $data;
    }

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($m,&$perro,$puestocat) {
        // si estamos en preagility, grado 1 o tiene 6 o mas puntos de penalizacion, utiliza la puntuacion estandard
        if ($perro['Grado']==="P.A.") {
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        if ($perro['Grado']==="GI") {
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        if ($perro['Penalizacion']>0) {
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        $perro['Calificacion'] = _("Excellent")." 0";
        $perro['CShort'] = "Ex 0";
        foreach ( $this->puntos as $item) {
            if ($perro['Grado']!==$item[0]) continue;
            // comprobamos si estamos en agility o en jumping
            $offset=(Mangas::$tipo_manga[$m->Tipo][5])?0/*agility*/:3/*jumping*/;
            $base=2;
            if ($perro['Categoria']==="M") $base=3;
            if ($perro['Categoria']==="S") $base=4;
            // si la velocidad es igual o superior se apunta tanto. notese que el array está ordenado por grad/velocidad
            if ($perro['Velocidad']>=$item[$base+$offset]) {
                $perro['Calificacion'] = _("Excellent")." ".$item[1];
                $perro['CShort'] = "Exc ".$item[1];
            }
        }
    }

    /**
     * Evalua la calificacion final del perro
     * @param {object} $m1 datos de la primera manga
     * @param {object} $m2 datos de la segunda manga
     * @param {array} $c1 resultados de la primera manga
     * @param {array} $c2 resultados de la segunda manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalFinalCalification($m1,$m2,$c1,$c2,&$perro,$puestocat){
        $grad=$perro['Grado']; // cogemos la categoria
        if ($grad==="P.A.") {
            parent::evalFinalCalification($m1,$m2,$c1,$c2,$perro,$puestocat);
            return;
        }
        if ($grad==="GI") { // en grado uno se puntua por cada manga
            $pts=0;
            if ($perro['P1']==0.0) $pts++;
            if ($perro['P2']==0.0) $pts++;
            $perro['Calificacion'] = "";
            if ($pts==1) $perro['Calificacion'] = "1 Punto";
            if ($pts==2) $perro['Calificacion'] = "2 Puntos";
            return;
        }
        // componemos string de calificacion final
        $p1=" ";
        if ($perro['P1']<6.0) $p1="-";
        if ($perro['P1']==0) $p1=substr($perro['C1'],-1,1);
        $p2=" ";
        if ($perro['P2']<6.0) $p2="-";
        if ($perro['P2']==0) $p2=substr($perro['C2'],-1,1);
        $perro['Calificacion']="$p1 / $p2";
    }
}