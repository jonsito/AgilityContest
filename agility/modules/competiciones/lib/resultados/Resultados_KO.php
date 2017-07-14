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
require_once(__DIR__."/../../../../server/database/classes/OrdenSalida.php");

class Resultados_KO extends Resultados {
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
     * Presenta una tabla ordenada segun los resultados de la manga
     *@param {integer} $mode 0:L 1:M 2:S 3:MS 4:LMS 5:T 6:L+M 7:S+T 8 L+M+S+T
     *@return {array} requested data or error
     */
    function getResultadosIndividual($mode) {
        // en una prueba KO los resultados individuales realmente no cuentan:
        // solo se tiene en cuenta cual de los perros que compiten por parejas pasa a la siguiente ronda.
        //
        // El metodo standard getResultadosIndividual() retorna los perros ordenados por resultados, con indicacion
        // del puesto que han obtenido por individual.
        // Lo que vamos a hacer es reordenar dicho resultado en función del orden de salida, combinando las funciones
        // Resultados::getResultadosIndividual() y Mangas::getData(), de manera que la salida va a estar ordenada
        // segun el orden de parejas

        // adicionalmente evaluaremos la calificacion: "Siguiente ronda" / "Eliminado"
        // obviando la llamada a evalPartialCalification, pues es dependiente de competición/federacion,
        // mientras que el KO va "por libre"

        $this->myLogger->enter();
        $res=parent::getResultadosIndividual(8); // ignore categories, just group all

        // le pasamos estos datos a OrdenSalida::getData()
        // $res contiene un entero 'total' y tres arrays: 'rows','trs','manga'
        $os=OrdenSalida::getInstance("getResuldatosIndividual",$res['manga']->ID);
        $osres=$os->getData(true,8,$res);

        // y ahora evaluamos las calificaciones dos a dos
        for ($n=1;$n<$osres['total'];$n+=3) {
            $p1=$osres['rows'][$n]['Puesto'];
            $p2=$osres['rows'][$n+1]['Puesto'];
            if ($p1<$p2) {
                $osres['rows'][$n]['Calificacion'] = _("Promote to next");
                $osres['rows'][$n]['CShort'] = _("Pass");
                $osres['rows'][$n+1]['Calificacion'] = _("Eliminated");
                $osres['rows'][$n+1]['CShort'] = _("Out");
            }
            if ($p1==$p2) {
                $osres['rows'][$n]['Calificacion'] = _("Need to run again");
                $osres['rows'][$n]['CShort'] = _("Again");
                $osres['rows'][$n+1]['Calificacion'] = _("Need to run again");
                $osres['rows'][$n+1]['CShort'] = _("Again");
            }
            if ($p1>$p2) {
                $osres['rows'][$n]['Calificacion'] = _("Eliminated");
                $osres['rows'][$n]['CShort'] = _("Out");
                $osres['rows'][$n+1]['Calificacion'] = _("Promote to next");
                $osres['rows'][$n+1]['CShort'] = _("Pass");
            }
        }
        $res['rows']=&$osres['rows'];
        $res['total']=count($res['rows']);

        $this->myLogger->leave();
        return $res;
    }

}
?>