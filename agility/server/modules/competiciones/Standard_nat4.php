<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Standard_nat4 extends Competitions {
    function __construct() {
        parent::__construct("National 4-height contest");
        $this->federationID=2;
        $this->competitionID=4;
    }

    function useLongNames() { return false; }
}