<?php
/*
print_listados_db.php

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
 * genera un pdf lista de perros seleccionada desde el menu de la base de datos en el orden especificado en la pantalla
*/

require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/logging.php");
require_once(__DIR__ . "/../../server/pdf/classes/PrintListaPerros.php");
require_once(__DIR__ . "/../../server/pdf/classes/PrintListaClubes.php");

// Consultamos la base de datos
try {
    $pdf=null;
	// 	Creamos generador de documento
    $fed=http_request("Federation","i",0);
    $oper=http_request("Operation","s",""); // Dogs, Handlers, Clubes, Judges
    if ($oper==='Dogs') $pdf = new PrintListaPerros($fed);
    if ($oper==='Clubes') $pdf = new PrintListaClubes();
    if ($pdf==null) throw new Exception("print_listados_db: unsupported (yet) listing");
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output($pdf->get_FileName(),"D"); // "D" means open download dialog
    return 0;
} catch (Exception $e) {
	die ("Error printing dog list: ".$e->getMessage());
}
?>