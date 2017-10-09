<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class FCI_AgilityWorldChampionship_2016 extends Competitions {
    function __construct() {
        parent::__construct("FCI Agility World Championship 2016");
        $this->federationID=9;
        $this->competitionID=3;
    }

    function useLongNames() { return true; }
}