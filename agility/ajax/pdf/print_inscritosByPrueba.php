<?php
/*
print_inscritosByPrueba.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


header('Set-Cookie: fileDownload=true; path=/');
// mandatory 'header' to be the first element to be echoed to stdout

/**
 * genera un pdf ordenado por club, categoria y nombre con una pagina por cada jornada
*/

require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/logging.php");
require_once(__DIR__ . '/../../server/database/classes/Jornadas.php');
require_once(__DIR__ . '/../../server/database/classes/Inscripciones.php');
require_once(__DIR__ . "/../../server/pdf/classes/PrintInscripciones.php");

// Consultamos la base de datos
try {
	$pruebaid=http_request("Prueba","i",0);
	$jornadaid=http_request("Jornada","i",0);
	$mode=http_request("Mode","i",0);
	$pdf=null;
	$name="";
	// Datos de inscripciones
	$jmgr= new Jornadas("printInscritosByPrueba",$pruebaid);
	$jornadas=$jmgr->selectByPrueba();
	$inscripciones = new Inscripciones("printInscritosByPrueba",$pruebaid);
	$inscritos= $inscripciones->enumerate();
	// Creamos generador de documento
	switch ($mode) {
		case 0: // imprimir inscripciones
			$pdf=new PrintInscritos($pruebaid,$inscritos,$jornadas);
			break;
		case 1: // imprimir catalogo
			$pdf=new PrintCatalogo($pruebaid,$inscritos,$jornadas);
			break;
		case 2: // imprimir estadisticas
			$pdf=new PrintEstadisticas($pruebaid,$inscritos,$jornadas);
			break;
		case 3: // inscripciones de una jornada
			$pdf=new PrintInscritosByJornada($pruebaid,$inscritos,$jornadas,$jornadaid);
			break;
        case 4: // imprimir segun el listado que aparece en pantalla
			$inscritos=$inscripciones->inscritos(true);
        	$pdf=new PrintInscritos($pruebaid,$inscritos,$jornadas,true);
        	break;
        case 5: // imprimir seleccion de pantalla en modo tarjeta de visita
            $inscritos=$inscripciones->inscritos(true);
            $pdf=new PrintTarjetasDeVisita($pruebaid,$inscritos,$jornadas);
        	break;
		default: throw new Exception ("Inscripciones::print() Invalid print mode selected $mode");
	}
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output($pdf->get_FileName(),"D"); // "D" means open download dialog
} catch (Exception $e) {
	die ("Error accessing database: ".$e->getMessage());
}
?>
