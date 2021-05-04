<?php
/*
print_clasificacion.php

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
 * genera un CSV con los datos para las etiquetas
 */

require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/logging.php");
require_once(__DIR__ . '/../../server/pdf/classes/PrintHallOfFame.php');

try {
	$result=null;
	$mangas=array();
    $prueba=http_request("Prueba","i",0);
    $jornadas=http_request("Jornadas","s","");
	$pdf=new PrintHallOfFame($prueba,$jornadas);
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output("HallOfFame_{$prueba}.pdf","D"); // "D" means output to web client (download)
} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}
?>