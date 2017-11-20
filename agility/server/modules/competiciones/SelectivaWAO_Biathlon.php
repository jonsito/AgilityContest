<?php

require_once(__DIR__ . "/../competiciones/lib/clasificaciones/Clasificaciones_SelWAO.php");
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class SelectivaWAO_Biathlon extends Competitions {
    function __construct() {
        parent::__construct("Selectiva WAO - Biathlon");
        $this->federationID=2;
        $this->competitionID=2;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20170506_1929";
    }

    function useLongNames() { return true; }

    /**
     * Se sigue el criterio PFinal= PTiempo + PRecorrido
     * Eliminado tiene PFinal=100 == Ptiempo=50 + PRecorrido=50
     *
     * Para la asignacion de puntos de biathlon, en funcion del resultado obtenido
     * en la manga, y para los 10 primeros perros ( que tengan menos de 16 puntos de penalizacion )
     * se asignan respectivamente 15,12,10,8,7,6,5,4,3, y 2 puntos
     *
     * La calificacion final es la suma de los puntos de las dos mangas de agility y de jumping
     * En caso de empata gana la calificacion por agilitys
     * Si sigue el empate en puntos de agility gana el que menos penalizacion/tiempo total tiene
     */

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($m,&$perro,$puestocat) {
        $cat=$perro['Categoria']; // cogemos la categoria
        // puntos por manga y puesto a los 10 mejores de cada categoria si tienen excelente o muy bien
        $ptsmanga=array("15","12","10","8","7","6","5","4","3","2");
        $pt1=0;
        $puesto=$puestocat[$cat];
        if ( ($puesto>0) && ($perro['Penalizacion']<16) ) {
            // puntos a los 10 primeros manga/categoria si tienen excelente o muy bien
            if ($puesto<=count($ptsmanga)) $pt1= $ptsmanga[$puesto-1];
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
            $perro['CShort'] = _("N.C.");
        }
        else if ($perro['Penalizacion']>=16)	{
            $perro['Calificacion'] = _("Good");
            $perro['CShort'] = _("Good");
        }
        else if ($perro['Penalizacion']>=6)	{
            $perro['Calificacion'] = $pt1." - "._("Very good");
            $perro['CShort'] = $pt1." - "._("V.G.");
        }
        else if ($perro['Penalizacion']>0)	{
            $perro['Calificacion'] = $pt1." - "._("Excellent");
            $perro['CShort'] = $pt1." - "._("Exc");
        }
        else if ($perro['Penalizacion']==0)	{
            $perro['Calificacion'] = $pt1." - "._("Excellent");
            $perro['CShort'] = $pt1." - "._("Exc");
        }
        // datos para la exportacion de parciales en excel
        $perro['Puntos'] = $pt1;
        $perro['Estrellas']= 0;
    }

    /**
     * Evalua la calificacion final del perro
     * Se tienen en cuenta los puntos de las dos mangas de agility y las dos de jumping
     *
     * @param {array} $mangas informacion {object} de las diversas mangas
     * @param {array} $resultados informacion {array} de los resultados de cada manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalFinalCalification($mangas,$resultados,&$perro,$puestocat){

        // la calificacion final es la suma de los puntos
        // de las dos mangas de agility y las dos mangas del jumping
        // si en alguna manga el perro es no presentado, no clasifica en la final
        $hasPoints=intval($perro['N1'])+intval($perro['N2'])+intval($perro['N3'])+intval($perro['N4']);
        $puntos=intval($perro['Pt1'])+intval($perro['Pt2'])+intval($perro['Pt3'])+intval($perro['Pt4']);
        do_log("HasPoints:{$hasPoints} PERRO: ".json_encode($perro));
        // conjunta
        $perro['CShort']= ($hasPoints===0)? $puntos: "-";
        $perro['Calificacion']= ($hasPoints===0)? $puntos: "-";
        return;
    }

    /**
     * Retrieve handler for manage Clasificaciones functions.
     * Default is use standard Clasificaciones, but may be overriden ( eg wao and eo )
     * @param {string} $file
     * @param {object} $prueba
     * @param {object} $jornada
     * @param {integer} $perro Dog ID to evaluate position ( if any )
     * @return {Resultados} instance of requested Resultados object
     */
    protected function getClasificacionesObject($file,$prueba,$jornada,$perro) {
        return new Clasificaciones_SelWAO($file,$prueba,$jornada,$perro);
    }
}