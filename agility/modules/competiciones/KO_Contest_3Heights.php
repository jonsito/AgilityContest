<?php
require_once(__DIR__ . "/lib/ordensalida/OrdenSalida_KO.php");

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class KO_Contest_3Heights extends Competitions {
    function __construct() {
        parent::__construct("Prueba K.O. ( 3-alturas )");
        $this->federationID=3;
        $this->competitionID=0;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20170506_1404";
    }

    /**
     * This kind of contests, are not really official competitions, so let's us be informal :-)
     * @return false;
     */
    function useLongNames() { return false; }

    /**
     * Retrieve handler for manage ordensalida functions.
     * Default is use standard OrdenSalida, but may be overriden ( eg KO. Rounds )
     * @param {string} $file
     * @param {object} $prueba
     * @param {object} $jornada
     * @param {object} $manga
     * @return {OrdenSalida} instance of requested OrdenSalida object
     */
    function getOrdenSalidaInstance($file,$prueba,$jornada,$manga) {
        return new OrdenSalida_KO($file,$prueba,$jornada,$manga);
    }

}