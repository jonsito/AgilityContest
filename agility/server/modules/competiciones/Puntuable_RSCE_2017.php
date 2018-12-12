<?php

/**
 *
 * Sistema de calificacion para las pruebas puntuables C.E. RSCE Temporada 2017
 * En grado 1 se obtiene punto por cada excelente a cero
 *
 * En grado 2 y 3 Se obtienen puntos por cada excelente a cero con velocidad superior a:
 *
 * GII: Agility 3.6m/s Jumping 3.8m/s
 * GIII: Agility 4.1m/s Jumping 4.5m/s
 * Además los perros que hagan el recorrido a cero con una velocidad superior a 5.1(agility) / 5.5(Jumping)
 * obtendran un punto extra
 *
 * Para la clasificacion para el C.E. Se exigen seis excelentes a cero en cada manga,
 * en los que al menos 3 de ellos tienen que tener puntos
 */
class Puntuable_RSCE_2017 extends Competitions {

   protected $puntos;

    function __construct($name="Puntuable temporada 2017") {
        parent::__construct($name);
        $this->federationID=0;
        $this->competitionID=0;
        $this->federationLogoAllowed=true;
        $this->puntos=array(
            /* grado      puntos  AgL     AgM    AgS    JpL     JpM     JpS    pts  stars */
            array("GII",    "Pv",  3.8,    3.6,   3.6,   4.0,    3.8,    3.8,   0,  1 ),
            array("GII",    "2P",  5.3,    5.1,   5.1,   5.5,    5.3,    5.3,   0,  2 ), // need to confirm stars value
            array("GIII",   "Pv",  4.7,    4.5,   4.5,   4.9,    4.7,    4.7,   0,  1 ),
            array("GIII",   "2P",  5.3,    5.1,   5.1,   5.5,    5.3,    5.3,   0,  2 ),
        );
    }

    /**
     * Provide default TRS/TRM/Recorrido values for a given competitiona at
     * Round creation time
     * @param {integer} $tipo Round tipe as declared as Mangas::TipoManga
     * @return {array} trs array or null if no changes
     */
    public function presetTRSData($tipo) {
        if ( ($tipo!=6) && ($tipo!=11) ) return parent::presetTRSData($tipo); // Not grade 3,use parent default
        $manga=array();
        $manga['Recorrido']=0; // 0:separados 1:mixto 2:conjunto
        $manga['TRS_L_Tipo']=1;$manga['TRS_L_Factor']=15;$manga['TRS_L_Unit']='%'; // best dog + 15 %
        $manga['TRM_L_Tipo']=1;$manga['TRM_L_Factor']=50;$manga['TRM_L_Unit']='%'; // trs + 50 %
        $manga['TRS_M_Tipo']=1;$manga['TRS_M_Factor']=15;$manga['TRS_M_Unit']='%';
        $manga['TRM_M_Tipo']=1;$manga['TRM_M_Factor']=50;$manga['TRM_M_Unit']='%';
        $manga['TRS_S_Tipo']=1;$manga['TRS_S_Factor']=15;$manga['TRS_S_Unit']='%';
        $manga['TRM_S_Tipo']=1;$manga['TRM_S_Factor']=50;$manga['TRM_S_Unit']='%';
        $manga['TRS_T_Tipo']=1;$manga['TRS_T_Factor']=15;$manga['TRS_T_Unit']='%'; // not used but required
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
        // remember that prueba,jornada and manga are objects, so passed by reference
        $this->prueba->Selectiva = 0; // not really required, just to be sure
        // en grado 3 el trs lo marca el perro mas rapido + 15% sin redondeo
        if (($manga->Tipo==6) || ($manga->Tipo==11)) $roundUp=false;
        return $data;
    }

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($m,&$perro,$puestocat) {

        // comprueba que las mangas sean puntuables
        $flag=false;
        $tipo=$m->Tipo;
        if ($tipo==3) $flag=true; // agility G1 primera manga
        if ($tipo==4) $flag=true; // agility G1 segunda manga
        if ($tipo==5) $flag=true; // agility G2
        if ($tipo==6) $flag=true; // agility G3
        if ($tipo==10) $flag=true;// jumping G2
        if ($tipo==11) $flag=true;// jumping G3
        if (!$flag) return parent::evalPartialCalification($m,$perro,$puestocat);

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
            if ($perro['Categoria']==="M") $base=3;
            if ($perro['Categoria']==="S") $base=4;
            // si la velocidad es igual o superior se apunta tanto. notese que el array está ordenado por grad/velocidad
            if ($perro['Velocidad']>=$item[$base+$offset]) {
                $perro['Calificacion'] = _("Excellent")." ".$item[1];
                $perro['CShort'] = "Ex ".$item[1];
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
        // si las mangas no son puntuables utiliza los criterios de la clase padre
        $flag=false;
        $tipo=$mangas[0]->Tipo;
        if ($tipo==3) $flag=true; // agility G1 primera manga
        if ($tipo==4) $flag=true; // agility G1 segunda manga
        if ($tipo==5) $flag=true; // agility G2
        if ($tipo==6) $flag=true; // agility G3
        if ($tipo==10) $flag=true;// jumping G3
        if ($tipo==11) $flag=true;// jumping G3
        if (!$flag) {
            parent::evalFinalCalification($mangas,$resultados,$perro,$puestocat);
            return;
        }

        $grad=$perro['Grado']; // cogemos la categoria
        if ($grad==="P.A.") {
            parent::evalFinalCalification($mangas,$resultados,$perro,$puestocat);
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
        if ($perro['P1']==0) $p1=mb_substr($perro['C1'],-2,2);
        $p2=" ";
        if ($perro['P2']<6.0) $p2="-";
        if ($perro['P2']==0) $p2=mb_substr($perro['C2'],-2,2);
        $perro['Calificacion']="$p1 / $p2";
    }
}