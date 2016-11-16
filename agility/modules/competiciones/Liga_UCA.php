<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Liga_UCA extends Competitions {
    function __construct($prueba,$jornada) {
        parent::__construct("Puntuable Liga UCA",$prueba,$jornada);
        $this->federationID=2;
        $this->competitionID=0;
    }
}