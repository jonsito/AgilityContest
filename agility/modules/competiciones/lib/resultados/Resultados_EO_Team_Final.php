<?php
/*
Resultados.php

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

require_once(__DIR__."/../../../../server/database/classes/Resultados.php");

class Resultados_EO_Team_Final extends Resultados {
	/**
	 * Constructor
	 * @param {string} $file caller for this object
     * @param {object} $prueba Prueba ID
     * @param {object} $jornada Jornada ID
     * @param {object} $manga Manga ID
	 * @throws Exception when
	 * - cannot contact database
	 * - invalid manga ID
	 * - manga is closed
	 */
	function __construct($file,$prueba,$jornada,$manga) {
		parent::__construct($file,$prueba,$jornada,$manga);
	}

	function getResultadosIndividualyEquipos($mode) {
        // obtenemos resultados individuales y TRS
        $resultados=$this->getResultadosIndividual($mode);
        // buscamos los resultados por equipos
        $resultados['individual']=$resultados['rows'];
        $resultados['equipos']=$this->getResultadosEquipos($resultados);
        return $resultados;
    }

    /**
     * Gestion de resultados en EO Final team
     *
     * La final del TEAM EO es una manga única a cuatro conjunta sobre un recorrido compartido
     * - Los perros corren uno detras de otro
     * - El crono arranca con el primer perro y para con el ultimo
     * - Perro eliminado implica 100 de penalizacion y TRM para el equipo
     * - Si menos de cuatro perros, un perro debe correr dos veces
     *
     * Internamente Resultados::update() tiene en cuenta si la prueba es equipos4
     * - Tiempo cero no implica no presentado
     * - Se pueden poner mas de tres rehuses sin eliminar
     *
     * Agrupa los resultados por equipos y genera una lista de equipos ordenados por resultados
     * @param {array} $results resultado de invocar getResultadosIndividual(mode)
     * @return {array} datos de equipos de la manga ordenados por resultados de equipo
     */
    function getResultadosEquipos($results) {
        $resultados=$results['rows'];
        // evaluamos mindogs
        $maxdogs=Jornadas::getTeamDogs($this->getDatosJornada())[1]; // get maxdogs
        // Datos de equipos de la jornada. obtenemos prueba y jornada del primer elemento del array
        $m=new Equipos("getResultadosEquipos",$this->IDPrueba,$this->IDJornada);
        $teams=$m->getTeamsByJornada();

        // reindexamos por ID y anyadimos campos extra:
        // Tiempo, penalizacion,Puntos, mejor punto del equipo y el array de resultados del equipo
        $equipos=array();
        foreach ($teams as &$equipo) {
            $equipo['Resultados']=array();
            $equipo['Tiempo']=0.0;
            $equipo['Penalizacion']=0.0;
            $equipo['Puntos']=0; // points are not used here
            $equipo['Eliminados']=0;
            $equipos[$equipo['ID']]=$equipo;
        }
        // now fill team members array.
        // notice that $resultados is already sorted by individual results
        foreach($resultados as &$result) {
            $teamid=$result['Equipo'];
            $equipo=&$equipos[$teamid];
            array_push($equipo['Resultados'],$result);
            // suma el tiempo y penalizaciones de los tres/cuatro primeros
            // almacena los puntos del mejor y del cuarto
            if (count($equipo['Resultados'])>$maxdogs) { // hey! more dogs in team than required
                $this->myLogger->notice("Team {$equipo['ID']} has more than {$maxdogs} dogs");
                continue;
            }
            if ($result['Penalizacion']>=200) continue; // not present or not yet run
            if ($result['Penalizacion']>=100) {
                $equipo['Eliminados']++;
                $equipo['Penalizacion']+=100.0; // question to ask: eliminated clears other penalizations ???
                continue;
            }
            $equipo['Tiempo']+=floatval($result['Tiempo']);
            $equipo['Penalizacion']+=floatval($result['Penalizacion']);
        }
        // iterate teams to check/parse eliminated
        foreach($teams as &$team) { // pass by refence as need to modify inner data
            // on one or more eliminated, set tiempo as TRM
            if ($team['Eliminados']>0) $team['Tiempo']=floatval($results['trs']['trm']);
        }
        // re-ordenamos los datos en base a penalizacion/tiempo
        usort($teams, function($a, $b) {
            if ( $a['Penalizacion'] == $b['Penalizacion'] )	{
                return ($a['Tiempo'] > $b['Tiempo'])? 1:-1;
            }
            return ( $a['Penaliacion'] > $b['Penalizacion'])?1:-1;
        });
        // retornamos el resultado final
        return $teams;
    }

}
?>