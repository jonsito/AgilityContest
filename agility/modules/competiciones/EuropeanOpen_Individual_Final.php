<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
/*
 * From: http://www.eo2017.it/files/eo2017_regulations_en.pdf

The EO Individual Final is an Agility round at A3 level.

The running order in the Individual Final is determined as follows:
   o First the qualified teams from the country qualifiers. They will start in
      reverse order of their time in the Individual Agility round. That is the fastest
      qualified team will run last, irrespective of the number of faults they had.
      Qualifiers to the Final without a valid time from the Individual Agility round
      will run first.
   o The 60 (L) / 36 (M) / 36 (S) qualified teams from both Jumping and Agility in
      reverse order and alternating (i.e. second to last will be the winner of the
      Jumping round, and last the winner of the Agility round)
   o The EO winner of the previous year will run last at the Final.

 */

/*
 * NOTA:
 * De momento no vamos a implementar el orden de salida para la seleccion de paises
 */

/**
 * Class EuropeanOpen_Individual_Final
 */
class EuropeanOpen_Individual_Final extends Competitions {
    function __construct() {
        parent::__construct("European Open - Final Round - Individual");
        $this->federationID=9;
        $this->competitionID=5;
        $this->moduleRevision="20170623_1151";
    }

    function useLongNames() { return true; }

    /**
     * Retrieve handler for manage ordensalida functions.
     * Default is use standard OrdenSalida, but may be overriden ( eg KO. Rounds )
     * @param {string} $file
     * @param {object} $prueba
     * @param {object} $jornada
     * @param {object} $manga
     * @return {OrdenSalida} instance of requested OrdenSalida object
     */
    public function getOrdenSalidaInstance($file,$prueba,$jornada,$manga) {
        return new OrdenSalida_EO_Final($file,$prueba,$jornada,$manga);
    }
}