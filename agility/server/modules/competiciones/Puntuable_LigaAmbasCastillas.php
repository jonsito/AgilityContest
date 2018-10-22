<?php

require_once(__DIR__."/Puntuable_LigaNorte_2019.php");
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Puntuable_LigaAmbasCastillas extends Puntuable_LigaNorte_2019 {
    function __construct($name="Puntuable Liga Dos Castillas") {
        parent::__construct($name);
        $this->federationID=1;
        $this->competitionID=4;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20181022_0905";
    }

}