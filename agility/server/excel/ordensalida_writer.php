<?php
/*
excel_listaPerros.php

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
require_once(__DIR__."/../logging.php");
require_once(__DIR__.'/../i18n/Country.php');
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Dogs.php');
require_once(__DIR__.'/classes/OrdenSalidaWriter.php');

// Consultamos la base de datos
try {
    $prueba=http_request("Prueba","i",0);
    $jornada=http_request("Jornada","i",0);
    $manga=http_request("Manga","i",0);
    $categorias=http_request("Categorias","s","-");
    $conjunta=http_request("EqConjunta","i",0);
    // 	Creamos generador de documento
    $excel = new OrdenSalidaWriter($prueba,$jornada,$manga,$categorias,$conjunta);
    $excel->open();
    if (! $conjunta) $excel->composeTable();
    else $excel->composeTableConjunta();
    $excel->close();
    return 0;
} catch (Exception $e) {
    die ("Error accessing database: ".$e->getMessage());
}
?>
