<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
require_once(__DIR__ . "/Puntuable_RSCE_2020.php");

class Puntuable_RSCE_2022 extends Puntuable_RSCE_2020 {
    function __construct($name="Punt. Temporada 2022 (CE 2023)") {
        parent::__construct($name);
        $this->federationID=0;
        $this->competitionID=23;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20212101_1100";
        $this->trms=array( 2.5 /*agility*/, 3.0 /*jumping*/ );
    }

    public function checkAndFixTRSData(&$manga,$data,$mode,&$roundUp) {
        // in 2022, senior dogs do not contribute to TRS/TRM evaluation
        // so remove from data used to eval it
        $res=array();
        for($n=0;$n<count($data);$n++) {
            if ($data[$n]['Grado']==="Sr") continue;
            array_push($res,$data[$n]);
        }
        return $data;
    }
}

?>