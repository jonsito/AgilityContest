<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Liga_RFEC extends Competitions {
    function __construct() {
        parent::__construct("Puntuable Liga RFEC");
        $this->federationID=1;
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
        $grad=$perro['Grado']; // cogemos el grado
        $cat=$perro['Categoria']; // cogemos la categoria
        if ($grad!=="GII") { // solo se puntua en grado II
            parent::evalPartialCalification($p,$j,$m,$perro,$puestocat);
            return;
        }
        $ptsmanga=array("5","4","3","2","1"); // puntos por manga y puesto
        $pt1=0;
        if ($perro['Penalizacion']<6.0) $pt1++; // 1 punto por excelente
        if ($perro['Penalizacion']==0.0) $pt1++; // 2 puntos por cero
        // puntos a los 5 primeros por manga/categoria si no estan eliminados
        if ( ($puestocat[$cat]>0) && ($perro['Penalizacion']<100) && ($puestocat[$cat]<=5) ) $pt1+= $ptsmanga[$puestocat[$cat]-1];
        if ($perro['Penalizacion']>=400)  {
            $perro['Penalizacion']=400.0;
            $perro['Calificacion'] = "-";
            $perro['CShort'] = "-";
        }
        else if ($perro['Penalizacion']>=200)  {
            $perro['Penalizacion']=200.0;
            $perro['Calificacion'] = _("Not Present");
            $perro['CShort'] = _("N.P.");
        }
        else if ($perro['Penalizacion']>=100) {
            $perro['Penalizacion']=100.0;
            $perro['Calificacion'] = _("Eliminated");
            $perro['CShort'] = _("Elim");
        }
        else if ($perro['Penalizacion']>=26)	{
            $perro['Calificacion'] = _("Not Clasified");
            $perro['CShort'] = _("N.C.");
        }
        else if ($perro['Penalizacion']>=16)	{
            $perro['Calificacion'] = _("Good")." - ".$pt1;
            $perro['CShort'] = _("Good");
        }
        else if ($perro['Penalizacion']>=6)	{
            $perro['Calificacion'] = _("V.G.")." - ".$pt1;
            $perro['CShort'] = _("V.G.");
        }
        else if ($perro['Penalizacion']>0)	{
            $perro['Calificacion'] = _("Exc")." - ".$pt1;
            $perro['CShort'] = _("Exc");
        }
        else if ($perro['Penalizacion']==0)	{
            $perro['Calificacion'] = _("Exc")." - ".$pt1;
            $perro['CShort'] = _("Exc");
        }
    }

    /**
     * Evalua la calificacion final del perro
     * @param {object} $p datos de la prueba
     * @param {object} $j datos de la jornada
     * @param {object} $m1 datos de la primera manga
     * @param {object} $m2 datos de la segunda manga
     * @param {array} $c1 datos de la primera manga
     * @param {array} $c2 datos de la segunda manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalFinalCalification($p,$j,$m1,$m2,$c1,$c2,&$perro,$puestocat){
        $grad=$perro['Grado']; // cogemos el grado
        $cat=$perro['Categoria']; // cogemos la categoria
        if ($grad!=="GII") { // solo se puntua en grado II
            $perro['Calificacion']=$perro['C1'];
            if ($perro['P1']<$perro['P2']) $perro['Calificacion']=$perro['C2'];
            return;
        }
        $ptsmanga=array("5","4","3","2","1"); // puntos por manga y puesto
        $ptsglobal=array("15","12","9","7","6","5","4","3","2","1"); //puestos por general (si no NC o Elim en alguna manga)
        // manga 1
        $pt1=0;
        if ($perro['P1']<6.0) $pt1++; // 1 punto por excelente
        if ($perro['P1']==0.0) $pt1++; // 2 puntos por cero
        // puntos a los 5 primeros por manga/categoria si no estan eliminados
        if ( ($perro['Pcat1']>0) && ($perro['P1']<100) && ($perro['Pcat1']<=5) ) $pt1+= $ptsmanga[$perro['Pcat1']-1];
        $perro['C1']=($pt1==0)?" ":strval($pt1);
        // manga 2
        $pt2=0;
        if ($c2!=null) {
            if ($perro['P2']<6.0) $pt2++; // 1 punto por excelente
            if ($perro['P2']==0.0) $pt2++; // 2 puntos por cero
            // puntos a los 5 primeros por manga/categoria si no estan eliminados
            if ( ($perro['Pcat2']>0) && ($perro['P2']<100) && ($perro['Pcat2']<=5) ) $pt2+= $ptsmanga[$perro['Pcat2']-1];
        }
        $perro['C2']=($pt2==0)?" ":strval($pt2);
        // conjunta
        $pfin=0;
        if ($puestocat[$cat]<11) {
            // puntuan los 10 primeros si no se han eliminado o no clasificado en ambas mangas
            if ( ($perro['P1']<=26.0) || ($perro['P2']<=26.0) ) {
                $pfin=$ptsglobal[$puestocat[$cat]-1];
            }
        }
        /** TODO: PENDIENTE DE VERIFICAR */
        // en las pruebas selectivas de caza (regional y nacional) se puntua doble
        // if ($p->Selectiva!=0) { $pt1*=2; $pt2*=2; $pfin*=2; }
        // finalmente componemos el string a presentar
        $perro['Calificacion']=strval($pfin);
    }
}