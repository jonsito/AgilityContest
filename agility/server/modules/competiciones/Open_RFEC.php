<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Open_RFEC extends Competitions {
    function __construct() {
        parent::__construct("Prueba Open RFEC");
        $this->federationID=1;
        $this->competitionID=1;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20181018_1114";
    }

    function useLongNames() { return false; }
}