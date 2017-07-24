<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Standard_intl3 extends Competitions {
    function __construct() {
        parent::__construct("International 3-height contest");
        $this->federationID=9;
        $this->competitionID=0;
    }

    function useLongNames() { return true; }
}