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

require_once(__DIR__."/../tools.php");
require_once (__DIR__."/../logging.php");
require_once(__DIR__.'/../modules/Competitions.php');
require_once(__DIR__.'/classes/PrintEntradaDeDatos.php');
require_once(__DIR__.'/classes/PrintEntradaDeDatosGames.php');
require_once(__DIR__.'/classes/PrintEntradaDeDatosKO.php');

try {
	// extraemos los datos de la llamada
	$data=array(
        'prueba' 	=> http_request("Prueba","i",0),
    	'jornada' 	=> http_request("Jornada","i",0),
    	'manga' 	=> http_request("Manga","i",0),
    	'numrows'	=> http_request("Mode","i",0), // numero de perros por hoja 1 / 5 / 8(games) / 10 / 15 / 16(KO)
    	'cats' 		=> http_request("Categorias","s","-"),
    	'fill' 		=> http_request("FillData","i",0), // tell if print entered data in sheets
        'rango' 	=> http_request("Rango","s","1-99999"),
        'comentarios' => http_request("Comentarios","s","-"),
        'eqconjunta' => http_request("EqConjunta","i",0),
        'ko' 		=> http_request("JornadaKO","i",0),
        'games' 	=> http_request("JornadaGames","i",0),
        'title'     => http_request("Title","s",_("Data Entry"))
	);

	// Consultamos la base de datos
	// Datos de la manga y su manga hermana
	$m = new Mangas("printEntradaDeDatos",$data['jornada']);
	$data['mangas']= $m->getHermanas($data['manga']);
	// Datos del orden de salida
	$o = Competitions::getOrdenSalidaInstance("printEntradaDeDatos",$data['manga']);
	$data['orden']= $o->getData()['rows'];
	// Creamos generador de documento
	// para ello vemos el tipo de manga
	$mng=$m->selectByID($data['manga']);
	$data['datosmanga']=$mng;
	switch ( $mng->Tipo ) {
		case 15:case 18:case 19:case 20:case 21:case 22:case 23:case 24: // ko rounds
        	$pdf = new PrintEntradaDeDatosKO($data);
        	break;
		case 29:case 30: // snooker, gambler
        	$pdf = new PrintEntradaDeDatosGames($data);
        	break;
		default:
            $pdf = new PrintEntradaDeDatos($data);
            break;
	}
	$pdf->AliasNbPages();
	$pdf->composeTable();
    $pdf->Output($pdf->get_FileName(),"D"); // "D" web client (download) "F" file save
} catch (Exception $e) {
	die ("Error accessing database: ".$e->getMessage());
};
?>
