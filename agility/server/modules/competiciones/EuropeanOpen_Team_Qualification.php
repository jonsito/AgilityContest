<?php
require_once(__DIR__ . "/../competiciones/lib/resultados/Resultados_EO_Team_Qualifications.php");
require_once(__DIR__ . "/../competiciones/lib/clasificaciones/Clasificaciones_EO_Team_Qualifications.php");

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 *
 * - Para clasificarse para la Final por Equipos se realizará una pista de Agility y una pista de Jumping.
 * - Tras cada pista los participantes serán ordenados en función del número de faltas y tiempo total.
 *
 * - El primer clasificado de cada pista recibirá tantos puntos como Equipos participen en la competición
 *   (=número de Equipos x  4), el segundo clasificado lo mismo -1 punto,
 *   el tercero lo mismo -2  puntos y así sucesivamente. Los eliminados obtienen 0 puntos.
 *
 * - Para  obtener la puntuación del Equipo, en cada pista se sumarán los puntos
 *   de los 3 mejores componentes del Equipo.
 *
 * - Pasarán a la final los X primeros Equipos Estandar, Mini y Midi con mayor puntuación obtenida
 *   de la suma total de ambas pistas.
 *
 * - En caso de empate se tendrá en cuenta la puntuación del 4º componente del equipo.
 * - Si continuara el empate ( por ejemplo, cuando los cuartos estan eliminados)
 *   se tendrá en cuenta  la mayor puntuación individual que tenga el Equipo.
 *
 */
class EuropeanOpen_Team_Qualification extends Competitions {

    /*
    * Como el sistema de asignacion de puntos es iterativo ( puesto->punto ),
    * tenemos que inventar algun tipo de "memoria" donde guardar los perros que han puntuado
    * para detectar los empates y poder actuar en consecuencia
    *
    * Para ello vamos a crear un array de puestos, y asignar los perros por puestos,
    * de manera que si en algun puesto hay mas de un perro recalculamos los puntos para dicho
    * puesto en cada iteracion
    */
    private $parciales=array();
    private $finalp1=array();
    private $finalp2=array();
    private $finales=array();

    function __construct() {
        parent::__construct("European Open - Qualifications Series - Teams");
        $this->federationID=9;
        $this->competitionID=1;
        $this->moduleRevision="20170623_1151";
    }

    function useLongNames() { return true; }

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($m,&$perro,$puestocat) {

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
        }

        $perro['Estrellas']=0;
        $perro['Puntos']=0;
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
        if ($size>1) { // on first dog nothing to compare to :-)
            $lastperro=$this->parciales[$size-2];
            if ($perro['Penalizacion']==$lastperro['Penalizacion']) {
                if (($perro['Tiempo']==$lastperro['Tiempo'])) $perro['Puntos']=$lastperro['Puntos'];
            }
        }

        // y ahora dejamos "potito" el campo de calificacion
        $perro['CShort'] = "{$perro['Puntos']}";
        if ($perro['Penalizacion']>=26)	{
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