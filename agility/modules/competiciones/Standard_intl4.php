<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Standard_int4 extends Competitions {
    function __construct($prueba,$jornada) {
        parent::__construct("Standard",$prueba,$jornada);
        $this->federationID=8;
        $this->competitionID=0;
    }
}