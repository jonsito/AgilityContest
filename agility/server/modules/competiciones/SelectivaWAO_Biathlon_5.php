<?php
require_once (__DIR__."/SelectivaWAO_Biathlon.php");
class SelectivaWAO_Biathlon_5 extends SelectivaWAO_Biathlon {
    function __construct() {
        parent::__construct("Selectiva WAO - Biathlon");
        $this->federationID=5;
        $this->competitionID=2;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20211114_2140";
        $this->federationLogoAllowed=true;
    }
}