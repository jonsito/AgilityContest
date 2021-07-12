<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
require_once(__DIR__ . "/Puntuable_RSCE_2018.php");

class Puntuable_RSCE_2021 extends Puntuable_RSCE_2020 {
    function __construct($name="Punt. Temporada 2021 (CE2022)") {
        parent::__construct($name);
        $this->federationID=0;
        $this->competitionID=20;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20212101_1100";
    }
}