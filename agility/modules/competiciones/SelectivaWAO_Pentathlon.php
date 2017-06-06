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
    }

    function useLongNames() { return false; }
}