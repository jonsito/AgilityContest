<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Selectiva_RSCE_Individual extends Competitions {
    function __construct() {
        parent::__construct("Selectiva RSCE Individual");
        $this->federationID=0;
        $this->competitionID=1;
    }
}