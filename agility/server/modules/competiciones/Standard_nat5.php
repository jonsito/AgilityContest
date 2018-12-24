<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Standard_nat5 extends Competitions {
    function __construct() {
        parent::__construct("National 5-height contest");
        $this->federationID=5;
        $this->competitionID=0;
    }

    function useLongNames() { return false; }
}