<?php
require_once(__DIR__.'/Puntuable_FMC_2018.php');

class Puntuable_FMC_2022 extends Puntuable_FMC_2018 {
    function __construct($name="Puntuable Liga FMC 2022") {
        parent::__construct($name);
        $this->federationID=1;
        $this->federationDefault=1;
        $this->competitionID=7;
        $this->moduleVersion="1.2.0";
        $this->moduleRevision="20180125_1113";
        $this->federationLogoAllowed=true;
        $this->ptsmanga=array(10,8,6,4,3,2,1); // puntos por manga y puesto
        $this->ptsglobal = array("10", "8", "6", "4", "3", "2", "1"); //puestos por general
    }
}