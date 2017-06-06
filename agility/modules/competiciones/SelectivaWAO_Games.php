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
        $this->competitionID=2;
    }

    function useLongNames() { return false; }
}