<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class FCI_AgilityWorldChampionship extends Competitions {
    function __construct() {
        parent::__construct("FCI Agility World Championship");
        $this->federationID=9;
        $this->competitionID=2;
    }

    function useLongNames() { return true; }
}