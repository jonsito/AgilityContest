<?php
/*
print_listaPerros.php

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
require_once(__DIR__ . "/../../server/auth/AuthManager.php");
require_once(__DIR__ . "/../../server/modules/Competitions.php");

require_once(__DIR__ . "/../../server/pdf/classes/PrintLigas.php");

// obtenemos datos de la peticion
$result=null;
$operation=http_request("Operation","s","");
$federation=http_request("Federation","i",0);
$grado=http_request("Grado","s","GI");
$perro=http_request("Perro","i",0);
// Consultamos la base de datos
try {
    $am= new AuthManager("ligaFunctions");
    if ($operation==="") throw new Exception("Call to printLigas without 'Operation' requested");
    // verificamos permisos de acceso
    $am->access(PERMS_GUEST);
    $am->permissions(ENABLE_LEAGUES);
    // obtenemos instancia del gestor de ligas adecuado a la federacion
    $l=Competitions::getLigasInstance("ligaFunctions",$federation);
    switch ($operation) {
        case "shortData":
            $result=$l->getShortData($federation,$grado);
            break;
        case "longData":
            // need grado cause dog may change
            $result=$l->getLongData($perro,$federation,$grado);
            break;
        default: throw new Exception("ligaFunctions:: invalid operation: $operation provided");
    }
    if ($result===null) throw new Exception($result->errorMsg);
    if ($result==="") throw new Exception("PrintLigas: empty result received");
	$pdf = new PrintLigas($federation,$result);
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output($pdf->get_FileName(),"D"); // "D" means open download dialog
    return 0;
} catch (Exception $e) {
	die ("Error accessing database: ".$e->getMessage());
}
?>