<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Copa_PastorBelga extends Competitions {
    function __construct($prueba,$jornada) {
        parent::__construct("Copa del Pastor Belga",$prueba,$jornada);
        $this->federationID=0;
        $this->competitionID=4;
    }
}