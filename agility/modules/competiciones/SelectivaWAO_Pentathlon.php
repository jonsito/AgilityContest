<?php

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
}