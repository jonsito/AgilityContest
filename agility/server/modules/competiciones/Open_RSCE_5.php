<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Open_RSCE_5 extends Competitions {
    function __construct() {
        parent::__construct("Prueba Open RSCE (5 alturas)");
        $this->federationID=0;
        $this->competitionID=16;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20191111_1212";
    }
    function getRoundHeights($manga) { return 5; }

    function useLongNames() { return true; }
}