<?php
/*
Resultados_KO.php

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

class Resultados_WAO_Pentathlon extends Resultados {

    /*
    CRITERIOS DE PUNTUACION EN PENTATHLON
    - Penalizacion = Tiempo + PTiempo + PRecorrido
    - Eliminado => PTiempo=50 + PRecorrido=50 => Penalizacion=100
    - Si PTiempo o PRecorrido son mayores que 50 se pone valor 50
    */

	/**
	 * Constructor
	 * @param {string} $file caller for this object
     * @param {object} $prueba Prueba
     * @param {object} $jornada Jornada
     * @param {object} $manga Manga
	 * @throws Exception when
	 * - cannot contact database
	 * - invalid manga ID
	 * - manga is closed
	 */
	function __construct($file,$prueba,$jornada,$manga) {
		parent::__construct($file,$prueba,$jornada,$manga);
	}

}
?>