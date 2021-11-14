<?php

require_once (__DIR__."/SelectivaWAO_Games.php");
class SelectivaWAO_Games_5 extends SelectivaWAO_Games {
    function __construct() {
        parent::__construct("Selectiva WAO - Games");
        $this->federationID=5;
        $this->competitionID=3;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20211114_2137";
        $this->federationLogoAllowed=true;
    }
}