<?php
/*
print_resultadosByManga.php

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
 * genera un pdf con los participantes ordenados segun los resultados de la manga
 */

require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/logging.php");
require_once(__DIR__ . '/../../server/modules/Competitions.php');
require_once(__DIR__ . '/../../server/database/classes/Mangas.php');
require_once(__DIR__ . "/../../server/pdf/classes/PrintResultadosByManga.php");
require_once(__DIR__ . "/../../server/pdf/classes/PrintResultadosKO.php");
require_once(__DIR__ . "/../../server/pdf/classes/PrintResultadosGames.php");

// Consultamos la base de datos
try {
	$idprueba=http_request("Prueba","i",0);
	$idjornada=http_request("Jornada","i",0);
	$idmanga=http_request("Manga","i",0);
	$mode=http_request("Mode","i",0);
    $title=http_request("Title","s",_("Partial scores"));
	
	$mngobj= new Mangas("printResultadosByManga",$idjornada);
	$manga=$mngobj->selectByID($idmanga);
	$resobj= Competitions::getResultadosInstance("printResultadosByManga",$idmanga);
	$resultados=$resobj->getResultadosIndividual($mode); // throw exception if pending dogs

	// Creamos generador de documento
    if ( isMangaKO($manga->Tipo)) {
        $pdf = new PrintResultadosKO($idprueba,$idjornada,$manga,$resultados);
    } else if ( isMangaGames($manga->Tipo) ) { // snooker, gambler
        $pdf = new PrintResultadosGames($idprueba,$idjornada,$manga,$resultados,$mode);
    } else {
        $pdf = new PrintResultadosByManga($idprueba,$idjornada,$manga,$resultados,$mode,$title);
    }
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output($pdf->get_FileName(),"D"); // "D" means open download dialog
} catch (Exception $e) {
	die($e->getMessage());
}
?>