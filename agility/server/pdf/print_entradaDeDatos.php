<?php
/*
print_entradaDeDatos.php

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

header('Set-Cookie: fileDownload=true; path=/');
// mandatory 'header' to be the first element to be echoed to stdout

/**
 * genera un pdf con las hojas del asistente de pista
*/

require_once(__DIR__."/fpdf.php");
require_once(__DIR__."/../tools.php");
require_once (__DIR__."/../logging.php");
require_once(__DIR__.'/../database/classes/OrdenSalida.php');;
require_once(__DIR__.'/classes/PrintEntradaDeDatos.php');

// Consultamos la base de datos
try {
	$prueba=http_request("Prueba","i",0);
	$jornada=http_request("Jornada","i",0);
	$manga=http_request("Manga","i",0);
	$mode=http_request("Mode","i",0);
	$cats=http_request("Categorias","s","-");
	$fill=http_request("FillData","i",0); // tell if print data in sheets

	// Datos de la manga y su manga hermana
	$m = new Mangas("printEntradaDeDatos",$jornada);
	$mangas= $m->getHermanas($manga);
	// Datos del orden de salida
	$o = new OrdenSalida("printEntradaDeDatos",$manga);
	$orden= $o->getData();
	// Creamos generador de documento
	$pdf = new PrintEntradaDeDatos($prueba,$jornada,$mangas,$orden['rows'],$mode,$cats,$fill);
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output("entradaDeDatos.pdf","D"); // "D" means open download dialog
} catch (Exception $e) {
	die ("Error accessing database: ".$e->getMessage());
};
?>
