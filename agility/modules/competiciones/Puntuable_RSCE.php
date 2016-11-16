<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Puntuable_RSCE extends Competitions {
    function __construct($prueba,$jornada) {
        parent::__construct("Puntuable C.E. RSCE",$prueba,$jornada);
        $this->federationID=0;
        $this->competitionID=0;
    }
}