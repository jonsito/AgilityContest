<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class EuropeanOpen extends Competitions {
    function __construct($prueba,$jornada) {
        parent::__construct("European Open",$prueba,$jornada);
        $this->federationID=9;
        $this->competitionID=1;
    }
}