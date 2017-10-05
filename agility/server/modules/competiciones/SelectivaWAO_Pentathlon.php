<?php
require_once(__DIR__."/lib/resultados/Resultados_WAO_Penthatlon.php");
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class SelectivaWAO_Pentathlon extends Competitions {
    function __construct() {
        parent::__construct("Selectiva WAO - Pentathlon");
        $this->federationID=2;
        $this->competitionID=1;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20170506_1929";
    }

    function useLongNames() { return false; }

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
        return new Resultados_WAO_Pentathlon($file,$prueba,$jornada,$manga);
    }
}