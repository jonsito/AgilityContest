<?php

// El trofeo interclubes utiliza el mismo sistema que el european open para las clasificatorias

require_once(__DIR__ . "/../competiciones/lib/resultados/Resultados_EO_Team_Qualifications.php");
require_once(__DIR__ . "/../competiciones/lib/clasificaciones/Clasificaciones_EO_Team_Qualifications.php");

class Clasificatoria_Interclubes extends Competitions {    /*
    * Como el sistema de asignacion de puntos es iterativo ( puesto->punto ),
    * tenemos que inventar algun tipo de "memoria" donde guardar los perros que han puntuado
    * para detectar los empates y poder actuar en consecuencia
    *
    * Para ello vamos a crear un array de puestos, y asignar los perros por puestos,
    * de manera que si en algun puesto hay mas de un perro recalculamos los puntos para dicho
    * puesto en cada iteracion
    */
    private $parciales=array();

    function __construct() {
        parent::__construct("Trofeo Interclubes - Clasificatorias");
        $this->federationID=0;
        $this->competitionID=2;
        $this->moduleRevision="20171109_1145";
        $this->federationLogoAllowed=true;
    }

    function useLongNames() { return true; }
    function getRoundHeights($mangaid) { return 3; }

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($m,&$perro,$puestocat) {

        // con cada perro vamos componiendo un array de datos parciales
        // para poder evaluar los puntos de competicion
        $this->parciales[]=&$perro; // important: store by reference
        $size=count($this->parciales);

        // los puntos se asignan en funcion del puesto y el numero de perros
        // por consiguiente vamos a recorrer el array incrementando el contador de puntos
        // para saber los que hay que asignar
        for ($n=0;$n<$size;$n++) {
            // esto es un poco chapuza: en cada iteraccion hay que incrementar en uno el valor de
            // los puntos. Idealmente habria que hacerlo al final del bucle for en lugar de hacerlo
            // en cada perro, pero de momento es lo que hay
            if($this->parciales[$n]['Puntos']!==0){
                $this->parciales[$n]['Puntos']++;
                $this->parciales[$n]['CShort']=$this->parciales[$n]['Puntos'];
            }
            $calif=preg_replace('/ - .*/',' - ',$this->parciales[$n]['Calificacion']);
            $this->parciales[$n]['Calificacion']=$calif.$this->parciales[$n]['Puntos'];
        }

        $perro['Puntos']=0;
        $perro['Estrellas']=0;
        $perro['Extras']=0;
        /* los perros pendientes, no presentados o eliminados se contabilizan, pero tienen cero puntos */
        if ($perro['Penalizacion']>=400)  {
            $perro['Penalizacion']=400.0;
            $perro['Calificacion'] = "- 0";
            $perro['CShort'] = "0";
            return;
        }
        if ($perro['Penalizacion']>=200)  {
            $perro['Penalizacion']=200.0;
            $perro['Calificacion'] = _("Not Present - 0");
            $perro['CShort'] = "0";
            return;
        }
        if ($perro['Penalizacion']>=100) {
            $perro['Penalizacion']=100.0;
            $perro['Calificacion'] = _("Eliminated - 0");
            $perro['CShort'] = "0";
            return;
        }

        // si el perro esta en el mismo puesto que el anterior, se le dan los mismos puntos.

        $perro['Puntos']=1;
        // en caso contrario, se queda como esta ( un punto )
        if ($size>1) { // on first dog in array nothing to compare to :-)
            $lastperro=$this->parciales[$size-2];
            if ($perro['Penalizacion']==$lastperro['Penalizacion']) {
                if (($perro['Tiempo']==$lastperro['Tiempo'])) $perro['Puntos']=$lastperro['Puntos'];
            }
        }

        // y ahora dejamos "potito" el campo de calificacion
        $perro['CShort'] = "{$perro['Puntos']}";
        if ($perro['Penalizacion']>=100)	{
            $perro['Calificacion'] = _("Elim.")." - 0"; // should not arrive here. need to revise
        }
        else if ($perro['Penalizacion']>=26)	{
            $perro['Calificacion'] = _("N.C.")." - ".$perro['Puntos'];
        }
        else if ($perro['Penalizacion']>=16)	{
            $perro['Calificacion'] = _("Good")." - ".$perro['Puntos'];
        }
        else if ($perro['Penalizacion']>=6)	{
            $perro['Calificacion'] = _("V.G.")." - ".$perro['Puntos'];
        }
        else if ($perro['Penalizacion']>0)	{
            $perro['Calificacion'] = _("Exc")." - ".$perro['Puntos'];
        }
        else if ($perro['Penalizacion']==0)	{
            $perro['Calificacion'] = _("Exc")." - ".$perro['Puntos'];
        }
    }

    /**
     * Evalua la calificacion final del perro
     * @param {array} $mangas informacion {object} de las diversas mangas
     * @param {array} $resultados informacion {array} de los resultados de cada manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalFinalCalification($mangas,$resultados,&$perro,$puestocat) {
        $perro['Puntos']=$perro['Pt1']+$perro['Pt2'];
        $perro['Calificacion']=$perro['Puntos'];
    }

    /**
     * Retrieve handler for manage Resultados functions.
     * Default is use standard Resultados, but may be overriden ( eg wao. Rounds )
     * @param {string} $file
     * @param {object} $prueba
     * @param {object} $jornada
     * @param {object} $manga
     * @return {Resultados} instance of requested Resultados object
     */
    protected function getResultadosObject($file,$prueba,$jornada,$manga) {
        return new Resultados_EO_Team_Qualifications($file,$prueba,$jornada,$manga);
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
        return new Clasificaciones_EO_Team_Qualifications($file,$prueba,$jornada,$perro);
    }
}