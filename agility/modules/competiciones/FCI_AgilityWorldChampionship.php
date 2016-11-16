<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class FCI_AgilityWoldChampionship extends Competitions {
    function __construct($prueba,$jornada) {
        parent::__construct("FCI Agility World Championship",$prueba,$jornada);
        $this->federationID=9;
        $this->competitionID=2;
    }
}