<?php
/*
print_ordenTandas.php

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
 * genera un pdf con diversas plantillas
*/

require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__.'/classes/PrintTRSTemplates.php');

// Consultamos la base de datos
try {
	$prueba=http_request("Prueba","i",0);
    $jornada=http_request("Jornada","i",0);
    $mode=http_request("Mode","i",0);
	// 	Creamos generador de documento
	$pdf = new PrintTRSTemplates($prueba,$jornada,$mode);
	$pdf->AliasNbPages();
    switch ($mode) {
        case 0:
            $pdf->set_FileName("TablaTRS_DistanciaVelocidad.pdf");
            $pdf->composeTable(); // tabla de trs versus distancia/velocidad
            break;
        case 1:
            $pdf->set_FileName("Formulario_MangasTRS.pdf");
            $pdf->printFormulario(); // hoja para apuntar trs de cada manga
            break;
        case 2:
            $pdf->set_FileName("Hoja_Asistente_vacia.pdf");
            $pdf->printDataForm(); // plantilla de asistente de pista vacia
            break;
    }
	$pdf->Output($pdf->get_FileName(),"D"); // "D" means open download dialog
} catch (Exception $e) {
	die ("Error accessing database: ".$e->getMessage());
};
?>