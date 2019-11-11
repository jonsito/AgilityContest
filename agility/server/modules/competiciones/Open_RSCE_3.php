<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Open_RSCE_3 extends Competitions {
    function __construct() {
        parent::__construct("Prueba Open RSCE (3 alturas)");
        $this->federationID=0;
        $this->competitionID=8;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20170523_2045";
    }

    function getRoundHeights($manga) { return 3; }

    function useLongNames() { return true; }
}