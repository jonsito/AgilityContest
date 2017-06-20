<?php

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
 * - Si continuara el empate se tendrá en cuenta  la mayor puntuación individual que tenga el Equipo.
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
        parent::__construct("European Open - Team Qualifications");
        $this->federationID=9;
        $this->competitionID=1;
    }

    function useLongNames() { return true; }

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($m,&$perro,$puestocat) {

        $cat=$perro['Categoria'];
        $pt1="";
        $puesto=$puestocat[$cat];
        $this->parciales[$puesto][]=&$perro; // important: store by reference

        $perro['Puntos']=0;
        $perro['Estrellas']=0;
        /* los perros pendientes, no presentados o eliminados se contabilizan, pero tienen cero puntos */
        if ($perro['Penalizacion']>=400)  {
            $perro['Penalizacion']=400.0;
            $perro['Calificacion'] = "- 0";
            $perro['CShort'] = "- 0";
            return;
        }
        if ($perro['Penalizacion']>=200)  {
            $perro['Penalizacion']=200.0;
            $perro['Calificacion'] = _("Not Present - 0");
            $perro['CShort'] = _("N.P. - 0");
            return;
        }
        if ($perro['Penalizacion']>=100) {
            $perro['Penalizacion']=100.0;
            $perro['Calificacion'] = _("Eliminated - 0");
            $perro['CShort'] = _("Elim - 0");
            return;
        }

        // los puntos se asignan en funcion del puesto y el numero de perros
        // por consiguiente vamos a recorrer el array incrementando el contador de puntos
        // para saber los que hay que asignar
        // puntos a los 10 primeros por manga/categoria si no estan eliminados
        if ( ($puesto>0) && ($perro['Penalizacion']<100) && ($puesto<=10) ) {
            $this->parciales[$puesto][]=&$perro; // important: store by reference
            // evaluate points to assign according number of dogs with same puesto
            $nperros=count($this->parciales[$puesto]);
            $pt1=0;
            for ($n=0;$n<$nperros;$n++) $pt1+=$this->ptsmanga[$puesto-1+$n];
            $pt1 =$pt1/$nperros;
            // assign evaluated points to every dogs with same puesto
            for ($n=0;$n<$nperros;$n++) $this->parciales[$puesto][$n]['Calificacion']=
                preg_replace('/(\w+) - (\d+)/i','${1} - '.$pt1,$this->parciales[$puesto][$n]['Calificacion']);
        }

        if ($perro['Penalizacion']>=26)	{
            $perro['Calificacion'] = _("Not Clasified")." - ".$pt1;
            $perro['CShort'] = _("N.C.")." - ".$pt1;
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
        $perro['Puntos']=$pt1;
        $perro['Estrellas']=0;
    }
}