<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Liga_RFEC extends Competitions {
    function __construct() {
        parent::__construct("Puntuable Liga RFEC");
        $this->federationID=1;
        $this->competitionID=0;
    }
}