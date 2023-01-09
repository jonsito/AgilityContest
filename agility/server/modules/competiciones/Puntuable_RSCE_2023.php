<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
require_once(__DIR__ . "/Puntuable_RSCE_2022.php");

class Puntuable_RSCE_2023 extends Puntuable_RSCE_2022 {
    function __construct($name="Punt. Temporada 2023 (CE 2024)") {
        parent::__construct($name);
        $this->federationID=0;
        $this->competitionID=24;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20220109_2100";
        $this->puntos=array(
            // en la temporada 2018 desaparecen los puntos dobles
            // se anyade un campo extra para los puntos de ascenso a grado 3
            /* grado      puntos  AgL     AgM    AgS    JpL     JpM     JpS    pts  stars  extras(g3) */
            array("GII",    "Pv",  4.2,    4.0,   4.0,   4.4,    4.2,    4.2,   0,  1,      0 ),
            array("GII",    "Pa",  4.7,    4.5,   4.5,   4.9,    4.7,    4.7,   0,  1,      1 ), // same as g3
            array("GIII",   "Pm",  4.2,    4.0,   4.0,   4.4,    4.2,    4.2,   0,  1,      0 ), // same as pvG2
            array("GIII",   "Pv",  4.7,    4.5,   4.5,   4.9,    4.7,    4.7,   0,  1,      0 )
        );
        $this->trms=array( 2.5 /*agility*/, 3.0 /*jumping*/ );
    }

}

?>