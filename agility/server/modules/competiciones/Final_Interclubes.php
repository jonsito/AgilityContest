<?php
require_once(__DIR__ . "/../competiciones/lib/resultados/Resultados_EO_Team_Final.php");

/**
 *
 * La Final por Equipos del trofeo interclubes en es una carrera de relevos donde se montan 4 pistas
 * independientes en una misma pista de Agility ampliada, donde algunos obstáculos serán compartidos.
 * El tiempo comienza a contar cuando el primer perro del Equipo sobrepasa su linea de salida,
 * y se para cuando el cuarto y último perro sobrepasa la línea de llegada.
 *
 * A los eliminados del Equipo se les contabilizará 100 de falta pero tienen la obligacion
 * de terminar el recorrido. Al contrario que en la final del European Open el tiempo sigue contando
 * hasta que el eliminado termina. Si no termina el equipo entero se descalifica
 * Los 15 equipos comenzarán en orden inverso a su clasificación,
 * de tal manera que el mejor equipo en la clasificación saltará en el último lugar en la Final.
 *
 * Todos los Equipos de la Final deben competir con los mismos componentes que participaron
 * en la clasificación. Si un Equipo está compuesto sólo por 3 guías en la clasificación
 * y llega a la final, un miembro del equipo tendrá que correr 2 veces.
 *
 */

class Final_Interclubes extends Competitions {
    function __construct() {
        parent::__construct("Trofeo Interclubes - Finales");
        $this->federationID=0;
        $this->competitionID=9;
        $this->moduleRevision="20171109_1153";
        $this->federationLogoAllowed=true;
    }

    function useLongNames() { return true; }
    function getRoundHeights($mangaid) { return 5; }
}