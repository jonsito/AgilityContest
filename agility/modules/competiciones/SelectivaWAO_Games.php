<?php

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

    function useLongNames() { return false; }
}