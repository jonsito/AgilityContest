<?php
/*
print_listaPerros.php

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

/**
 * genera fichero excel de perros seleccionada desde el menu de la base de datos en el orden especificado en la pantalla
*/

require_once(__DIR__."/../tools.php");
require_once(__DIR__ . "/classes/InscripcionesWriter.php");

// Consultamos la base de datos
try {
	// 	Creamos generador de documento
    $prueba=http_request("Prueba","i",-1);
    $club=http_request("Club","i",0); // -1:empty template 0:inscriptions x:club template
	$excel = new InscripcionesWriter($prueba,$club);
	$excel->open();
	$excel->composeTable();
	$excel->close();
    return 0;
} catch (Exception $e) {
	die ("Error accessing database: ".$e->getMessage());
}
?>