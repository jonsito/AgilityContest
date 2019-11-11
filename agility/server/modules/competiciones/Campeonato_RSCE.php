<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Campeonato_RSCE extends Competitions {

    // en el campeonato de España 2017 aparte de la clasificacion absoluta
    // hay trofeos al primer equipo Infantil/Junior y Senior
    // por ello es preciso llevar un contador para cada categoria
    protected $pJunior=1;
    protected $pSenior=1;

    function __construct() {
        parent::__construct("Campeonato RSCE 2017");
        $this->federationID=0;
        $this->competitionID=7;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20170427_1840";
        $this->federationLogoAllowed=true;
    }

    function getRoundHeights($manga){ return 3; }

    function useLongNames() { return true; }

    /**
     * Evalua la calificacion final del perro
     * @param {array} $mangas informacion {object} de las diversas mangas
     * @param {array} $resultados informacion {array} de los resultados de cada manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalFinalCalification($mangas,$resultados,&$perro,$puestocat){
        // no hay categorias ni grados: la final individual es una prueba open
        // simplemente hay que llevar la cuenta de infantil/junior y senior
        // nota: en RSCE junior e infantil califican y suben a podium juntos
        if ( $perro['CatGuia']==="I" || $perro['CatGuia']==='J' ) { // infantil - junior
            $perro['Calificacion']= "{$this->pJunior}º - Junior";
            $this->pJunior++;
        }
        if ($perro['CatGuia']==="S") { // senior
            $perro['Calificacion']= "{$this->pSenior}º - Senior";
            $this->pSenior++;
        }
    }
}