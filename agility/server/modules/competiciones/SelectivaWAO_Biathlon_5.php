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
        // a partir de 2022 se aplica puntuacion wao internacional
        // puntos por manga y puesto a los 10 mejores de cada categoria si tienen excelente o muy bien
        $this->ptsmanga=array("15","12","10","9","8","7","6","5","4","3");
        // licencias que pueden utilizar este modulo
        $this->accessControlList=array("00000021");
    }
}
?>