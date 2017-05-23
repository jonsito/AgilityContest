<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class WorldAgilityOpen extends Competitions {
    function __construct() {
        parent::__construct("World Agility Open");
        $this->federationID=8;
        $this->competitionID=1;
    }

    function useLongNames() { return true; }
}