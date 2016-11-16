<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class WorldAgilityOpen extends Competitions {
    function __construct($prueba,$jornada) {
        parent::__construct("World Agility Open",$prueba,$jornada);
        $this->federationID=8;
        $this->competitionID=1;
    }
}