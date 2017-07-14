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

class Resultados_EO_Team_Qualifications extends Resultados {
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

    /**
     * Gestion de resultados en Equipos3/Equipos4
     * Agrupa los resultados por equipos y genera una lista de equipos ordenados por resultados
     * @param {array} $results resultados de invocar getResultadosIndividual($mode)
     * @return {array} datos de equipos de la manga ordenados por resultados de equipo
     */
    function getResultadosEquipos($results) {
        $resultados=$results['rows'];
        // evaluamos mindogs
        $mindogs=Jornadas::getTeamDogs($this->getDatosJornada())[0]; // get mindogs
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
            $equipo['Puntos']=0;
            $equipo['Extra']=0;
            $equipo['Best']=0;
            $equipos[$equipo['ID']]=$equipo;
        }
        // now fill team members array.
        // notice that $resultados is already sorted by results
        foreach($resultados as &$result) {
            $teamid=$result['Equipo'];
            $equipo=&$equipos[$teamid];
            array_push($equipo['Resultados'],$result);
            // suma el tiempo y penalizaciones de los tres/cuatro primeros
            // almacena los puntos del mejor y del cuarto
            if (count($equipo['Resultados'])<=$mindogs) {
                $equipo['Tiempo']+=floatval($result['Tiempo']);
                $equipo['Penalizacion']+=floatval($result['Penalizacion']);
                $equipo['Puntos']+=$result['Puntos'];
                if (count($equipo['Resultados'])==1) $equipo['Best']=$result['Puntos'];
            } else {
                $equipo['Extra']=+$result['Puntos'];
            }
        }

        // rastrea los equipos con menos de $mindogs participantes y marca los que faltan
        // no presentados
        $teams=array();
        foreach($equipos as &$equipo) {
            switch(count($equipo['Resultados'])){
                case 0: continue; // ignore team (category doesnt match with results )
					break;
                case 1: $equipo['Penalizacion']+=400.0; // required team member undeclared
                    // no break
                case 2: if ($mindogs==3) $equipo['Penalizacion']+=400.0; // required team member undeclared
                    // no break;
                case 3: if ($mindogs==4) $equipo['Penalizacion']+=400.0; // required team member undeclared
                    // no break;
                case 4:
                    array_push($teams,$equipo); // add team to result to remove unused/empty teams
                    break;
                default:
                    $myLogger=new Logger("Resultados::getTreamResults()");
                    $myLogger->error("Equipo {$equipo['ID']} : '{$equipo['Nombre']}' con exceso de participantes:".count($equipo['Resultados']));
                    break;
            }
        }
        // re-ordenamos los datos en base a la puntuacion
        usort($teams, function($a, $b) {
            if ( $a['Puntos'] == $b['Puntos'] )	{
                if ($a['Extra']==$b['Extra']) {
                    return ($a['Best'] < $b['Best'])? 1:-1;
                }
                return ($a['Extra'] < $b['Extra'])? 1:-1;
            }
            return ( $a['Puntos'] < $b['Puntos'])?1:-1;
        });
        return $teams;
    }

}
?>