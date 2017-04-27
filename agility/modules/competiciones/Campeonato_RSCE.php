<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Campeonato_RSCE extends Competitions {
    function __construct() {
        parent::__construct("Prueba Open / Campeonato RSCE");
        $this->federationID=0;
        $this->competitionID=7;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20170427_1840";
    }
}