<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Puntuable_RSCE extends Competitions {
    function __construct() {
        parent::__construct("Puntuable C.E. RSCE");
        $this->federationID=0;
        $this->competitionID=0;
    }
}