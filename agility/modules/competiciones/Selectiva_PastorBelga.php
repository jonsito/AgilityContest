<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Selectiva_PastorBelga extends Competitions {

    protected $ptsmanga=array("10"," 9"," 8"," 7"," 6"," 5"," 4"," 3"," 2"," 1"); // puntos por manga y puesto
    protected $ptsglobal=array("10"," 9"," 8"," 7"," 6"," 5"," 4"," 3"," 2"," 1"); //puntos por general

    function __construct($name="Selectiva Pastor Belga") {
        parent::__construct($name);
        $this->federationID=0;
        $this->competitionID=3;
    }

    /*
     * En la selectiva del pastor belga, compiten pastores belgas de grado II y III
     * - puntuan tanto en individual como en conjunta los 10 primeros no eliminados.
     * - para puntuar en conjunta no puede estar eliminado en ninguna manga
     * - En caso de empate comparten los mismos puntos
     * - el baremo es 10,9,8,7,6,5,5,4,3,2,1
     */

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $p datos de la prueba
     * @param {object} $j datos de la jornada
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($p,$j,$m,&$perro,$puestocat) {
        // cogemos la categoria, que en el pastor belga siempre deberia ser L
        $cat=$perro['Categoria'];
        $pt1="";
        // puntos a los 10 primeros por manga/categoria si no estan eliminados
        if ( ($puestocat[$cat]>0) && ($perro['Penalizacion']<100) && ($puestocat[$cat]<=5) ) {
            $pt1 = $this->ptsmanga[$puestocat[$cat]-1];
        }
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
            $perro['CShort'] = _("N.C.")." - ".$pt1; // confirm that N.C Classified receives points
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
        $cat=$perro['Categoria']; // cogemos la categoria, que siempre deberia ser estandard (L)
        // manga 1
        // puntos a los 10 primeros por manga/categoria si no estan eliminados
        $perro['C1']="";
        if ($c1!=null) {
            if ( ($perro['Pcat1']>0) && ($perro['P1']<100) && ($perro['Pcat1']<=10) ) {
                $perro['C1'] = $this->ptsmanga[$perro['Pcat1']-1];
            }
        }
        // manga 2
        $perro['C2']="";
        if ($c2!=null) {
            // puntos a los 10 primeros por manga/categoria si no estan eliminados
            if ( ($perro['Pcat2']>0) && ($perro['P2']<100) && ($perro['Pcat2']<=10) ) {
                $perro['C2'] = $this->ptsmanga[$perro['Pcat2']-1];
            }
        }
        // conjunta
        $perro['Calificacion']="";
        if ($puestocat[$cat]<11) {
            // puntuan los 10 primeros si no se han eliminado o no clasificado en ninguna manga
            if ( ($perro['P1']<100.0) && ($perro['P2']<100.0) ) { // verificar si es NC o eliminado
                $perro['Calificacion']=$this->ptsglobal[$puestocat[$cat]-1];
            }
        }
    }

    function checkAndFixTRSData($prueba,$jornada,$manga,$data) {
        do_log("checkAndFixTRSData: enter()");
        /*
         * El TRS de una selectiva de PB es el la media de los tres mejores perros
         * de grado II y III de _cualquier_raza_ de la prueba RSCE asociada
         * Esto se declara en la definicion de la manga de la selectiva, pero hay
         * que hacer un bypass especial para cambiar los datos de la manga actual (selectiva)
         * por la manga padre ( puntuable rsce )
         *
         * Es por ello que se inserta esta rutina en medio de la funcion Resultados::evalTRS
         */
        // fase 0: buscamos la jornada padre
        $parent=intval($jornada->SlaveOf);
        if ($parent==0) return $data;
        $myDBObject=new DBObject("checkAndFixTRSData");
        // fase 1: cogemos todos los resultados de standard grado II y III de la manga padre
        $res=$myDBObject->__select(
            /* SELECT */ "Perro, Mangas.Tipo AS Tipo, GREATEST(200*NoPresentado,100*Eliminado,5*(Tocados+Faltas+Rehuses)) AS PRecorrido,Tiempo",
            /* FROM */   "Resultados,Mangas",
            /* WHERE */  "(Resultados.Manga=Mangas.ID) AND (Pendiente=0) AND (Resultados.Jornada=$parent)".
                            "AND (Categoria='L') AND ( (Resultados.Grado='GII') OR (Resultados.Grado='GIII') )",
            /* ORDER BY */" PRecorrido ASC, Tiempo ASC",
            /* LIMIT */  ""
        );
        // fase 2: eliminamos aquellos que no coincidan con el tipo de manga (agility/jumping)
        $tipo=Mangas::$tipo_manga[$manga->Tipo][5]; // vemos si estamos en agility o jumping
        $result=array();
        foreach ($res['rows'] as $row ) {
            if (Mangas::$tipo_manga[$row['Tipo']][5] != $tipo) continue;
            $result[]=$row; // tipo coincide. anyadimos al resultado. Recuerda que ya estan ordenados
        }
        // finalmente retornamos el resultado
        return $result;
    }
}