<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Selectiva_PastorBelga extends Competitions {

    /*
     * En la selectiva del pastor belga, compiten pastores belgas de grado II y III
     * - puntuan tanto en individual como en conjunta los 10 primeros no eliminados.
     * - para puntuar en conjunta no puede estar eliminado en ninguna manga
     * - En caso de empate comparten la media de los puntos que les corresponderian
     *   si no hubieran empatado
     * - el baremo es 10,9,8,7,6,5,5,4,3,2,1
     */

    // solo puntuan los 10 primeros, pero anyadimos campos extras por si hay empate en el decimo
    protected $ptsmanga=array("10"," 9"," 8"," 7"," 6"," 5"," 4"," 3"," 2"," 1","0","0"); // puntos por manga y puesto
    protected $ptsglobal=array("10"," 9"," 8"," 7"," 6"," 5"," 4"," 3"," 2"," 1","0","0"); //puntos por general

    function __construct($name="Selectiva del Pastor Belga") {
        parent::__construct($name);
        $this->federationID=0;
        $this->competitionID=3;
        $this->federationLogoAllowed=true;
    }

    function getModuleInfo($contact = null)  {
        return parent::getModuleInfo("agility@ceppb.es");
    }

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

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($m,&$perro,$puestocat) {
        // cogemos la categoria, que en el pastor belga siempre deberia ser L
        $cat=$perro['Categoria'];
        $pt1=0;
        $puesto=$puestocat[$cat];
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
            $perro['Calificacion'] = _("Not Clasified")." - ".$pt1; // confirm that N.C Classified receives points
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
        $perro['Puntos']=$pt1;
        $perro['Estrellas']=0;
        $perro['Extras']=0;
    }

    /**
     * Evalua la calificacion final del perro
     * @param {array} $mangas informacion {object} de las diversas mangas
     * @param {array} $resultados informacion {array} de los resultados de cada manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     *
     * BUG: segun las normas del pastor belga, si dos o mas perros tienen misma penalizacion,
     * se les asigna a cada uno la media de la suma de sus puntuaciones.
     * Para poder implementar esto correctamente necesitaría analizar el array completo de
     * perros por manga y por conjunta, y eso obliga a re-escribir medio programa
     *
     * Una solucion intermedia sería guardar un puntero a los 10 primeros de cada manga y a
     * los de la conjunta, comprobando en cada iteracion si hay algún puesto que coincida...
     * De momento, lo dejamos estar
     */
    public function evalFinalCalification($mangas,$resultados,&$perro,$puestocat){
        $cat=$perro['Categoria']; // cogemos la categoria, que siempre deberia ser estandard (L)
        // manga 1
        // puntos a los 10 primeros por manga/categoria si no estan eliminados
        $perro['C1']="";
        if ($resultados[0]!==null) {
            if ( ($perro['Pcat1']>0) && ($perro['P1']<100) && ($perro['Pcat1']<=10) ) {
                $puesto=$perro['Pcat1'];
                $this->finalp1[$puesto][]=&$perro; // important: store by reference
                // evaluate points to assign according number of dogs with same puesto
                $nperros=count($this->finalp1[$puesto]);
                $pt1=0;
                for ($n=0;$n<$nperros;$n++) $pt1+=$this->ptsmanga[$puesto-1+$n];
                $pt1 =$pt1/$nperros;
                // assign evaluated points to every dogs with same puesto
                for ($n=0;$n<$nperros;$n++) $this->finalp1[$puesto][$n]['C1']=$pt1;
            }
        }
        // manga 2
        $perro['C2']="";
        if ($resultados[1]!==null) {
            // puntos a los 10 primeros por manga/categoria si no estan eliminados
            if ( ($perro['Pcat2']>0) && ($perro['P2']<100) && ($perro['Pcat2']<=10) ) {
                $puesto=$perro['Pcat2'];
                $this->finalp2[$puesto][]=&$perro; // important: store by reference
                // evaluate points to assign according number of dogs with same puesto
                $nperros=count($this->finalp2[$puesto]);
                $pt2=0;
                for ($n=0;$n<$nperros;$n++) $pt2+=$this->ptsmanga[$puesto-1+$n];
                $pt2 =$pt2/$nperros;
                // assign evaluated points to every dogs with same puesto
                for ($n=0;$n<$nperros;$n++) $this->finalp2[$puesto][$n]['C2']=$pt2;
            }
        }
        // conjunta
        $perro['Calificacion']="";
        // puntuan los 10 primeros si no se han eliminado o no clasificado en ninguna manga
        if ( ($perro['P1']<100.0) && ($perro['P2']<100.0) && ($puestocat[$cat]<=10)) {
            $puesto=$puestocat[$cat];
            $this->finales[$puesto][]=&$perro; // important: store by reference
            // evaluate points to assign according number of dogs with same puesto
            $nperros=count($this->finales[$puesto]);
            $ptf=0;
            for ($n=0;$n<$nperros;$n++) $ptf+=$this->ptsglobal[$puesto-1+$n];
            $ptf =$ptf/$nperros;
            // assign evaluated points to every dogs with same puesto
            for ($n=0;$n<$nperros;$n++) $this->finales[$puesto][$n]['Calificacion']=$ptf;
        }
    }

    /**
     * Provide default TRS/TRM/Recorrido values for a given competitiona at
     * Round creation time
     * @param {integer} $tipo Round tipe as declared as Mangas::TipoManga
     * @return {array} trs array or null if no changes
     */
    public function presetTRSData($tipo) {
        // if not grade 3 or 2, use parent as default
        if ( ($tipo!=6) && ($tipo!=11) && ($tipo!=5) && ($tipo!=10)) return parent::presetTRSData($tipo);
        $manga=array();
        $manga['Recorrido']=0; // 0:separados 1:mixto 2:conjunto
        $manga['TRS_X_Tipo']=2;$manga['TRS_X_Factor']=10;$manga['TRS_X_Unit']='%';
        $manga['TRM_X_Tipo']=1;$manga['TRM_X_Factor']=50;$manga['TRM_X_Unit']='%';
        $manga['TRS_L_Tipo']=2;$manga['TRS_L_Factor']=10;$manga['TRS_L_Unit']='%'; // media 3 mejores + 10% roundup
        $manga['TRM_L_Tipo']=1;$manga['TRM_L_Factor']=50;$manga['TRM_L_Unit']='%'; // trs + 50 %
        $manga['TRS_M_Tipo']=2;$manga['TRS_M_Factor']=10;$manga['TRS_M_Unit']='%';
        $manga['TRM_M_Tipo']=1;$manga['TRM_M_Factor']=50;$manga['TRM_M_Unit']='%';
        $manga['TRS_S_Tipo']=2;$manga['TRS_S_Factor']=10;$manga['TRS_S_Unit']='%';
        $manga['TRM_S_Tipo']=1;$manga['TRM_S_Factor']=50;$manga['TRM_S_Unit']='%';
        $manga['TRS_T_Tipo']=2;$manga['TRS_T_Factor']=10;$manga['TRS_T_Unit']='%'; // not used but required
        $manga['TRM_T_Tipo']=1;$manga['TRM_T_Factor']=50;$manga['TRM_T_Unit']='%'; // not used but required
        return $manga;
    }

    /**
     * Re-evaluate and fix -if required- results data used to evaluate TRS for
     * provided $prueba/$jornada/$manga
     * @param {object} $manga Round data and trs parameters. Passed by reference
     * @param {array} $data Original results provided for evaluation
     * @param {integer} $mode which categories must be selected
     * @param {boolean} $roundUp on true round UP SCT and MCT to nearest second. Passed by reference
     * @return {array} final data to be used to evaluate trs/trm
     */
    function checkAndFixTRSData(&$manga,$data,$mode,&$roundUp) {
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
        $parent=intval($this->jornada->SlaveOf);
        if ($parent==0) return $data;
        $myDBObject=new DBObject("checkAndFixTRSData");
        // fase 1: cogemos todos los resultados de standard grado II y III de la manga padre
        // como no hay pastores belga mini o midi, no hace falta checkar la categoria :-), so $mode is not used
        $res=$myDBObject->__select(
            /* SELECT */ "Perro, mangas.Tipo AS Tipo, GREATEST(200*NoPresentado,100*Eliminado,5*(Tocados+Faltas+Rehuses)) AS PRecorrido,Tiempo",
            /* FROM */   "resultados,mangas",
            /* WHERE */  "(resultados.Manga=mangas.ID) AND (Pendiente=0) AND (resultados.Jornada=$parent)".
                            "AND (Categoria='L') AND ( (resultados.Grado='GII') OR (resultados.Grado='GIII') )",
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