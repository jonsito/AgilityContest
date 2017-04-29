<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Liga_UCA extends Competitions {
    function __construct() {
        parent::__construct("Prueba puntuable Liga UCA");
        $this->federationID=2;
        $this->competitionID=0;
    }

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $p datos de la prueba
     * @param {object} $j datos de la jornada
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($m,&$perro,$puestocat) {
        if ($perro['Grado']!=="GII") { // solo se puntua en grado II
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        if ($perro['Penalizacion']>=400)  { // tiene manga pendiente de salir
            $perro['Penalizacion']=400.0;
            $perro['Calificacion'] = "";
            $perro['CShort'] = "";
        }
        if ($perro['Penalizacion']>=200)  { // no presentado: no puntua
            $perro['Penalizacion']=200.0;
            $perro['Calificacion'] = _("Not Present");
            $perro['CShort'] = _("N.P.");
        }
        else if ($perro['Penalizacion']>=100) { // eliminado: no puntua
            $perro['Penalizacion']=100.0;
            $perro['Calificacion'] = _("Eliminated");
            $perro['CShort'] = _("Elim");
        }
        else if ($perro['Penalizacion']>=26) { // No clasificado: no puntua
            $perro['Calificacion'] = _("Not Clasified");
            $perro['CShort'] = _("N.C.");
        }
        else if ($perro['Penalizacion']>=16)	{ // Bien: 2 puntos
            $perro['Calificacion'] = _("Good")." - 2";
            $perro['CShort'] = _("Good");
            $perro['Puntos'] = 2;
        }
        else if ($perro['Penalizacion']>=6)	{ // Muy bien: 3 puntos
            $perro['Calificacion'] = _("Very good")." - 3";
            $perro['CShort'] = _("V.G.");
            $perro['Puntos'] = 3;
        }
        else if ($perro['Penalizacion']>0)	{ // Excelente: 4 puntos
            $perro['Calificacion'] = _("Excellent")." - 4";
            $perro['CShort'] = _("Exc");
            $perro['Puntos'] = 4;
        }
        else if ($perro['Penalizacion']==0)	{ // Cero: 5 puntos
            $perro['Calificacion'] = _("Excellent")." - 5";
            $perro['CShort'] = _("Exc");
            $perro['Puntos'] = 5;
        }
        $perro['Estrellas']= 0;
    }


    /**
     * Evalua la calificacion final del perro
     * @param {array} $mangas informacion {object} de las diversas mangas
     * @param {array} $resultados informacion {array} de los resultados de cada manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalFinalCalification($mangas,$resultados,&$perro,$puestocat){
        $grad=$perro['Grado']; // cogemos el grado
        $cat=$perro['Categoria']; // cogemos la categoria
        if ($grad!=="GII") { // solo se puntua en grado II
            $perro['Calificacion']=$perro['C1'];
            if ($perro['P1']<$perro['P2']) $perro['Calificacion']=$perro['C2'];
            return;
        }
        $pts=array("10","8","6","4","3","2","1");
        // manga 1
        $pt1=0;
        if($resultados[0]!==null) {
            if ($perro['P1']>=26) $pt1=0; // NC o eliminado: no puntua
            if ($perro['P1']<26) $pt1=2;
            if ($perro['P1']<16) $pt1=3;
            if ($perro['P1']<6) $pt1=4;
            if ($perro['P1']==0) $pt1=5;
        }
        $perro['C1']=($pt1==0)?" ":strval($pt1);
        // manga 2
        $pt2=0;
        if ($resultados[1]!==null) {
            if ($perro['P2']>=26) $pt2=0; // NC o eliminado: no puntua
            if ($perro['P2']<26) $pt2=2;
            if ($perro['P2']<16) $pt2=3;
            if ($perro['P2']<6) $pt2=4;
            if ($perro['P2']==0) $pt2=5;
        }
        $perro['C2']=($pt2==0)?" ":strval($pt2);
        // final
        // solo puntuan en la global los siete primeros con dobles excelentes
        if (($pt1<4) || ($pt2<4) || ($puestocat[$cat]>7) || ($puestocat[$cat]<=0) ) {
            $perro['Calificacion']="";
        } else {
            $perro['Calificacion']= $pts[ $puestocat[$cat]-1 ];
        }
    }
}