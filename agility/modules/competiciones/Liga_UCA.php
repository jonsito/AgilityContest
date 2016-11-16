<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Liga_UCA extends Competitions {
    function __construct() {
        parent::__construct("Puntuable Liga UCA");
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
    public function evalPartialCalification($p,$j,$m,&$perro,$puestocat) {
        if ($perro['Grado']!=="GII") { // solo se puntua en grado II
            parent::evalPartialCalification($p,$j,$m,$perro,$puestocat);
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
        }
        else if ($perro['Penalizacion']>=6)	{ // Muy bien: 3 puntos
            $perro['Calificacion'] = _("Very good")." - 3";
            $perro['CShort'] = _("V.G.");
        }
        else if ($perro['Penalizacion']>0)	{ // Excelente: 4 puntos
            $perro['Calificacion'] = _("Excellent")." - 4";
            $perro['CShort'] = _("Exc");
        }
        else if ($perro['Penalizacion']==0)	{ // Cero: 5 puntos
            $perro['Calificacion'] = _("Excellent")." - 5";
            $perro['CShort'] = _("Exc");
        }
    }
}