<?php
require_once(__DIR__ . "/../competiciones/lib/resultados/Resultados_Games.php");
require_once(__DIR__ . "/../competiciones/lib/clasificaciones/Clasificaciones_SelWAO.php");

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class SelectivaWAO_Games extends Competitions {
    function __construct() {
        parent::__construct("Selectiva WAO - Games");
        $this->federationID=2;
        $this->competitionID=3;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20170506_1929";
    }

    function useLongNames() { return true; }

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
        return new Resultados_Games($file,$prueba,$jornada,$manga);
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
    /**
     * Gets Course penalization, Time, and SCT data and compose penalization
     *
     * En Snooker y Gambler no hay faltas ni rehuses,
     * solo puntuaciones en secuencia de apertura y cierre
     * junto con el tiempo
     *
     * AgilityContest usa los campos Faltas y Tocados para almacenar los puntos de cada secuencia
     * En PRecorrido guardamos los puntos de la secuencia de apertura
     * En PTiempo guardamos los puntos de la secuencia de cierre/Gambler
     * En Penalizacion guardamos la suma de PTiempo y PRecorrido
     * los campos puntos y calificacion no se usan
     *
     * @param {array} $perro dog data . Passed by reference
     * @param {array} $tdata sct data
     */
    public function evalPartialPenalization(&$perro,$tdata) {
        $ptiempo=intval($perro['Faltas']);
        $precorrido=intval($perro['Tocados']);
        $eliminado=intval($perro['Eliminado']);
        $nopresentado=intval($perro['NoPresentado']);
        $perro['Estrellas'] = 0; // not used in games
        if (($eliminado!=00) || ($nopresentado!==0)) {
            $perro['PTiempo']=0;
            $perro['PRecorrido']=0;
            $perro['Penalizacion']=0;
            $perro['Puntos']=0;
        } else {
            $perro['PRecorrido']= $perro['Faltas'];
            $perro['PTiempo']=$perro['Tocados'];
            $perro['Penalizacion']=$ptiempo+$precorrido;
            $perro['Puntos']=$ptiempo+$precorrido;
        }
    }

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($m,&$perro,$puestocat) {
        // en games es sencillo: se ponen los puntos, o "eliminado", o "No presentado"
        if (intval($perro['Eliminado'])!==0) {
            $perro['Calificacion'] = _("Eliminated");
            $perro['CShort'] = _("Elim");
        } else if (intval($perro['NoPresentado'])!==0) {
            $perro['Calificacion'] = _("Not Present");
            $perro['CShort'] = _("N.P.");
        } else {
            $perro['Calificacion'] = $perro['Puntos'];
            $perro['CShort'] = $perro['Puntos'];
        }
    }
}