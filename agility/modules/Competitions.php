<?php
/**
 * Competitions.php
 *
Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

// to make sure that poedit gets data to be translated
$dummy= _('Points');
$dummy= _('Stars');
$dummy= _('KC_ID'); // LOE_RRC ( also exists 'KC id' that goes to 'LOE/RRC' )

require_once(__DIR__."/Federations.php");
require_once(__DIR__."/../server/database/classes/Clasificaciones.php");
require_once(__DIR__."/../server/database/classes/Resultados.php");
require_once(__DIR__."/../server/database/classes/OrdenSalida.php");
require_once(__DIR__."/competiciones/lib/resultados/Resultados_KO.php");
require_once(__DIR__."/competiciones/lib/ordensalida/OrdenSalida_KO.php");

/*
 * This class handles every available kind of competitions on each federations
 * Starting on version 2.3.1_20161110 Table "Jornadas" includes a new Field "Tipo_Competicion"
 * That declares how to handle results in order to show points and clasifications
 *
 * This field eventually will replace and override functionality of "Selectiva" from Pruebas
 */
class Competitions {

        // in order to update modules, we assume that moduleRevision must be greater or equal
        // to running software revision
        protected $moduleVersion="1.0.0";
        protected $moduleRevision="20161121_0900";

        // each pair $federationID:$competitionID must be unique
        protected $federationID=0;
        protected $competitionID=0;
        protected $competitionName="Standard";
        protected $selectiva=0; // historic flag from Prueba table
        protected $prueba=null;
        protected $jornada=null;

        /* protected */ function __construct($name) {
            $this->competitionName=$name;
        }

    /**
     * Retrieve module information
     * @param $contact email address for contac
     */
    public function getModuleInfo($contact=null) {
        $fed=Federations::getFederation($this->federationID);
        if (!$contact) $contact=$fed->get("Email");
        return array(
            // for compatibility with getModuleList($fed)
            "ID" => $this->competitionID,
            "Nombre" => $this->competitionName,
            // module information name
            "ModuleName" => $this->competitionName,
            "FederationName" => $fed->get("Name"),
            "ModuleID" => $this->competitionID,
            "FederationID" => $this->federationID,
            "ModuleVersion" => $this->moduleVersion,
            "ModuleRevision" => $this->moduleRevision,
            // Specific configuration for each module
            "Data" => array (
                "UseLongNames" => $this->useLongNames(),
                "Email" => $contact,
                /*    1 0000 0000 0000 0001 -> Pre-Agility 1 */
                /*    2 0000 0000 0000 0010 -> Pre-Agility 2 */ // TO BE REMOVED
                /*    4 0000 0000 0000 0100 -> Grade A1      */ // 1:2rounds 2:1round 3:3rounds
                /*    8 0000 0000 0000 1000 -> Grade A2      */
                /*   16 0000 0000 0001 0000 -> Grade A3      */
                /*   32 0000 0000 0010 0000 -> Open          */
                /*   64 0000 0000 0100 0000 -> Teams 3/4     */
                /*  128 0000 0000 1000 0000 -> Teams 4       */
                /*  256 0000 0001 0000 0000 -> KO Series     */
                /*  512 0000 0010 0000 0000 -> Special       */
                /* 1024 0000 0100 0000 0000 -> Teams 2/3     */ /*need to revise. may be false */
                /* 2048 0000 1000 0000 0000 -> Teams 2       */ /*need to revise. may be false */
                /* 4096 0001 0000 0000 0000 -> Teams 3       */ /*need to revise. may be false */
                /* 8192 0010 0000 0000 0000 -> Games / WAO   */
                /*16384 0100 0000 0000 0000 -> Junior */
                /*32768 1000 0000 0000 0000 -> undefined     */
                "ValidRounds" => bindec('1111111111111111')
            )
        );
    }

    /**
     * Gets Course penalization, Time, and SCT data and compose penalization
     *
     * Normal mode is that Penalization= CoursePenalization + TimeOverTRS
     * But some competitions resolves Penalization = CoursePenalization+Time
     * So this module is required to be overriden in case of
     *
     * @param {array} $perro dog data . Passed by reference
     * @param {array} $tdata sct data
     */
    public function evalPartialPenalization(&$perro,$tdata) {
        $trs=$tdata['trs'];
        $trm=$tdata['trm'];
        if ($trs==0) {
            // si TRS==0 no hay penalizacion por tiempo
            $perro['PTiempo']		= 	0.0;
            $perro['Penalizacion']=	$perro['PRecorrido'];
        } else {
            // evaluamos penalizacion por tiempo y penalizacion final
            if ($perro['Tiempo']<$trs) { // Por debajo del TRS
                $perro['PTiempo']		= 	0.0;
                $perro['Penalizacion']=	$perro['PRecorrido'];
            }
            if ($perro['Tiempo']>=$trs) { // Superado TRS
                $perro['PTiempo']		=	$perro['Tiempo'] 		-	$trs;
                $perro['Penalizacion']=	floatval($perro['PRecorrido'])	+	$perro['PTiempo'];
            }
            if ($perro['Tiempo']>$trm) { // Superado TRM: eliminado
                $perro['Penalizacion']=	100.0;
            }
        }
    }

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($m,&$perro,$puestocat) {
        // datos para la exportacion de parciales en excel
        $perro['Puntos'] = 0;
        $perro['Estrellas']= 0;
        if ($perro['Penalizacion']>=400)  { // pending
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
            $perro['Calificacion'] = _("Very good");
            $perro['CShort'] = _("V.G.");
        }
        else if ($perro['Penalizacion']>0)	{
            $perro['Calificacion'] = _("Excellent");
            $perro['CShort'] = _("Exc");
        }
        else if ($perro['Penalizacion']==0)	{
            $perro['Calificacion'] = _("Excellent (0)");
            $perro['CShort'] = _("Exc");
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
        return; // normally is overriden by child classes
    }

    /**
     * Provide default TRS/TRM/Recorrido values for a given competitiona at
     * Round creation time
     * @param {integer} $tipo Round tipe as declared as Mangas::TipoManga
     * @return {array} trs array or null if no changes
     */
    public function presetTRSData($tipo) {
        // on KO rounds preset to TRS=0:TRM=100 mode=8
        if ( ! in_array ($tipo, array(15,18,19,20,21,22,23,24) ) ) return null; // no KO->no preset
        $manga=array();
        $manga['Recorrido']=2; // 0:separados 1:mixto 2:conjunto
        $manga['TRS_L_Tipo']=0;$manga['TRS_L_Factor']=0;$manga['TRS_L_Unit']='s'; // no TRS
        $manga['TRM_L_Tipo']=0;$manga['TRM_L_Factor']=100;$manga['TRM_L_Unit']='s'; // TRM=100 segs
        $manga['TRS_M_Tipo']=0;$manga['TRS_M_Factor']=0;$manga['TRS_M_Unit']='s';
        $manga['TRM_M_Tipo']=0;$manga['TRM_M_Factor']=100;$manga['TRM_M_Unit']='s';
        $manga['TRS_S_Tipo']=0;$manga['TRS_S_Factor']=0;$manga['TRS_S_Unit']='s';
        $manga['TRM_S_Tipo']=0;$manga['TRM_S_Factor']=100;$manga['TRM_S_Unit']='s';
        $manga['TRS_T_Tipo']=0;$manga['TRS_T_Factor']=0;$manga['TRS_T_Unit']='s';
        $manga['TRM_T_Tipo']=0;$manga['TRM_T_Factor']=100;$manga['TRM_T_Unit']='s';
        return $manga;
    }

    /**
     * Re-evaluate and fix -if required- results data used to evaluate TRS for
     * provided $prueba/$jornada/$manga
     * @param {object} $manga Round data and trs parameters
     * @param {array} $data Original results provided for evaluation
     * @param {integer} $mode to evaluate which categories are to be used
     * @return {array} final data to be used to evaluate trs/trm
     */
    public function checkAndFixTRSData($manga,$data,$mode=0) {
        // en el caso de pruebas subordinadas ( por ejemplo, selectiva del pastor belga),
        // puede ocurrir que los datos ( mejor o tres mejores ) no haya que tomarlos de la
        // manga actual, sino de la manga padre.
        // para contemplarlo, hacemos un bypass, que nos devolvera los datos correctos
        // en otros casos PE grado 3 en puntuables rsce y selectivas rsce hay que
        // fijar los parametros del trs y el flag de selectiva
        return $data;
    }

    /**
     * Request if current competition module requires show short-long dog names
     * @return {boolean} true or false
     */
    public function useLongNames() {
        return false; // default use short name
    }

    /**
     * Retrieve handler for manage ordensalida functions.
     * Default is use standard OrdenSalida, but may be overriden ( eg KO. Rounds )
     * @param {string} $file
     * @param {object} $prueba
     * @param {object} $jornada
     * @param {object} $manga
     * @return {OrdenSalida} instance of requested OrdenSalida object
     */
    public function getOrdenSalidaInstance($file,$prueba,$jornada,$manga) {
        // la gestion del orden de salida en una manga KO es comun a todas las competiciones
        if ( in_array ($manga->Tipo, array(15,18,19,20,21,22,23,24) ) ) {
            return new OrdenSalida_KO($file,$prueba,$jornada,$manga);
        }
        return new OrdenSalida($file,$prueba,$jornada,$manga);
    }

    /**
     * Retrieve handler for manage Resultados functions.
     * Default is use standard Resultados, but may be overriden ( eg wao. Rounds )
     * @param {string} $file
     * @param {object} $prueba
     * @param {object} $jornada
     * @param {object} $manga
     * @param {object} $manga
     * @return {Resultados} instance of requested Resultados object
     */
    public function getResultadosInstance($file,$prueba,$jornada,$manga) {
        // la gestion del orden de salida en una manga KO es comun a todas las competiciones
        if ( in_array ($manga->Tipo, array(15,18,19,20,21,22,23,24) ) ) {
            $os=$this->getOrdenSalidaInstance($file,$prueba,$jornada,$manga);
            return new Resultados_KO($file,$prueba,$jornada,$manga,$os);
        }
        return new Resultados($file,$prueba,$jornada,$manga);
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
    public function getClasificacionesInstance($file,$prueba,$jornada,$perro) {
        return new Clasificaciones($file,$prueba,$jornada,$perro);
    }
    /**************************************** static functions comes here *************************************/

    /**
     * Create json array of available competitions for provided federation (or enumerate all if fed<0)
     * When $fed <0 return array in json datagrid format
     * if $fed >=0 return array in json combobox format
     * @param {integer} $fed Federation id
     * @return {array} ({int}ID,{string}name)
     */
    static function getAvailableCompetitions($fed=-1) {
        $competitionList=array();
        // analize sub-directories looking for classes matching federation id
        // Notice that module class name must match file name
        foreach( glob(__DIR__.'/competiciones/*.php') as $filename) {
            $name=str_replace(".php","",basename($filename));
            require_once($filename);
            $comp=new $name;
            if (!$comp) continue; // cannot instantiate class. should report error
            if (($fed >= 0) && ($comp->federationID != $fed) ) continue;
            $competitionList[]=$comp->getModuleInfo();
        }
        // arriving here means requested federation not found
        if ($fed>=0) return $competitionList; // combobox getCompetitionList($fed)
        return array("total"=> count($competitionList), "rows"=>$competitionList);
    }

    /**
     * Retrieve a competition object based in prueba/jornada information
     * @param {object} $prueba
     * @param {object} $jornada
     * @return {Competitions} requested competition module, or default if not found
     */
    static function getCompetition($prueba,$jornada) {
        $fed=intval($prueba->RSCE);
        $type=intval($jornada->Tipo_Competicion);
        $sel=intval($prueba->Selectiva);
        // analize sub-directories looking for classes matching federation and journey ID
        // Notice that module class name must match file name
        foreach( glob(__DIR__.'/competiciones/*.php') as $filename) {
            $name=str_replace(".php","",basename($filename));
            require_once($filename);
            $comp=new $name;
            if (!$comp) continue; // cannot instantiate class. should report error
            if ($comp->federationID!=$fed) continue;
            if ($comp->competitionID!=$type) continue;
            // competition found: assign selective flag and return
            $comp->selectiva=$sel;
            $comp->prueba=$prueba;
            $comp->jornada=$jornada;
            return $comp;
        }
        // arriving here means requested federation not found: warn and return default
        do_log("Cannot find valid competition module for federation:$fed type:$type . Using defaults");
        return new Competitions("Default for Fed:$fed Type:$type");
    }

    /**
     * @param {integer} $fed federation id
     * @param {integer} $type competition type
     * @return {array|string} requested data or error string
     * @throws Exception
     */
    static function moduleInfo($fed,$type) {
        foreach( glob(__DIR__.'/competiciones/*.php') as $filename) {
            $name=str_replace(".php","",basename($filename));
            require_once($filename);
            $comp=new $name;
            if (!$comp) continue; // cannot instantiate class. should report error
            if ($comp->federationID!=$fed) continue;
            if ($comp->competitionID!=$type) continue;
            // competition found: assign selective flag and return
            return $comp->getModuleInfo();
        }
        // arriving here means competition module not found
        throw new Exception ("Modules::moduleInfo() module information for fed:$fed type:$type not found");
    }
}
?>