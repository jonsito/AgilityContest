<?php
require_once(__DIR__ . "/../competiciones/lib/resultados/Resultados_EO_Team_Final.php");

/**
 *
 * La Final por Equipos del European Open es una carrera de relevos donde se montan 4 pistas
 * independientes en una misma pista de Agility ampliada, donde algunos obstáculos serán compartidos.
 * El tiempo comienza a contar cuando el primer perro del Equipo sobrepasa su linea de salida,
 * y se para cuando el cuarto y último perro sobrepasa la línea de llegada.
 *
 * A los eliminados del Equipo se les contabilizará 100 de falta y automáticamente el tiempo total del
 * equipo será el máximo estipulado. Los 15 equipos comenzarán en orden inverso a su clasificación,
 * de tal manera que el mejor equipo en la clasificación saltará en el último lugar en la Final.
 *
 * Todos los Equipos de la Final deben competir con los mismos componentes que participaron
 * en la clasificación. Si un Equipo está compuesto sólo por 3 guías en la clasificación
 * y llega a la final, un miembro del equipo tendrá que correr 2 veces.
 *
 */

class EuropeanOpen_Team_Final extends Competitions {
    function __construct() {
        parent::__construct("European Open - Final Round - Teams");
        $this->federationID=9;
        $this->competitionID=3;
        $this->moduleRevision="20170623_1151";
    }

    /**
     * Retrieve handler for manage Resultados functions.
     * Default is use standard Resultados, but may be overriden ( eg wao. Rounds )
     * @param {string} $file
     * @param {object} $prueba
     * @param {object} $jornada
     * @param {object} $manga
     * @return {Resultados} instance of requested Resultados object
     * @throws Exception on invalid prueba/jornada/manga
     */
    protected function getResultadosObject($file,$prueba,$jornada,$manga) {
        return new Resultados_EO_Team_Final($file,$prueba,$jornada,$manga);
    }

    function useLongNames() { return true; }
}