<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
require_once(__DIR__ . "/Puntuable_RSCE_2018.php");

class Puntuable_RSCE_2019 extends Puntuable_RSCE_2018 {
    function __construct() {
        parent::__construct("Puntuable Temporada 2019");
        $this->competitionID=13;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="201901110_0852";
    }
}