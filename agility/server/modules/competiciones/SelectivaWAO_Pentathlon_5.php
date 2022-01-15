<?php

require_once (__DIR__."/SelectivaWAO_Pentathlon.php");
class SelectivaWAO_Pentathlon_5 extends SelectivaWAO_Pentathlon {
    function __construct() {
        parent::__construct("Selectiva WAO - Pentathlon");
        $this->federationID=5;
        $this->competitionID=1;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20170506_1929";
        $this->federationLogoAllowed=true;
        // licencias que pueden utilizar este modulo
        $this->accessControlList=array("0dcca3fa1b2fc28fc4b95fa41f258378");
    }
}