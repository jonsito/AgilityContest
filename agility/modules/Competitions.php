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

/*
 * This class handles every available kind of competitions on each federations
 * Starting on version 2.3.1_20161110 Table "Jornadas" includes a new Field "Tipo_Competicion"
 * That declares how to handle results in order to show points and clasifications
 *
 * This field eventually will replace and override functionality of "Selectiva" from Pruebas
 */
require_once(__DIR__."/Federations.php");
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
            "Email" => $contact,
            // Specific configuration for each module
            "Data" => array (
                "UseLongNames" => $this->useLongNames()
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
        return null;
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
        return new OrdenSalida($file,$prueba,$jornada,$manga);
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