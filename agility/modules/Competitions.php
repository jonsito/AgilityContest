<?php
/**
 * Competitions.php
 *
Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

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
     */
    public function getModuleInfo() {
        return array(
            // for compatibility with getModuleList($fed)
            "ID" => $this->competitionID,
            "Nombre" => $this->competitionName,
            // module information name
            "ModuleName" => $this->competitionName,
            "FederationName" => Federations::getFederation($this->federationID)->get("Name"),
            "ModuleID" => $this->competitionID,
            "FederationID" => $this->federationID,
            "ModuleVersion" => $this->moduleVersion,
            "ModuleRevision" => $this->moduleRevision
        );
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
            $perro['CShort'] = _("Ex 0");
        }
    }

    /**
     * Evalua la calificacion final del perro
     * @param {object} $p datos de la prueba
     * @param {object} $j datos de la jornada
     * @param {object} $m1 datos de la primera manga
     * @param {object} $m2 datos de la segunda manga
     * @param {array} $c1 resultados de la primera manga
     * @param {array} $c2 resultados de la segunda manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalFinalCalification($p,$j,$m1,$m2,$c1,$c2,&$perro,$puestocat){
        return; // normally is overriden by child classes
    }

    /**
     * Re-evaluate and fix -if required- results data used to evaluate TRS for
     * provided $prueba/$jornada/$manga
     * @param {object} $prueba Contest data
     * @param {object} $jornada Journey data
     * @param {object} $manga Round data
     * @param {array} $data Original results provided for evaluation
     * @return {array} final data to be used to evaluate trs/trm
     */
    public function checkAndFixTRSData($prueba,$jornada,$manga,$data) {
        // en el caso de pruebas subordinadas ( por ejemplo, selectiva del pastor belga),
        // puede ocurrir que los datos ( mejor o tres mejores ) no haya que tomarlos de la
        // manga actual, sino de la manga padre.
        // para contemplarlo, hacemos un bypass, que nos devolvera los datos correctos
        return $data;
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
     * @param $prueba
     * @param $jornada
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

}
?>