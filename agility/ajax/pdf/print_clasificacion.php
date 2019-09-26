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
require_once(__DIR__ . '/../../server/modules/Competitions.php');
require_once(__DIR__ . '/../../server/pdf/classes/PrintClasificacion.php');
require_once(__DIR__ . '/../../server/pdf/classes/PrintClasificacionGames.php');

try {
	$result=null;
	$mangas=array();
	$prueba=http_request("Prueba","i",0);
	$jornada=http_request("Jornada","i",0);
	$rondas=http_request("Rondas","i","0"); // bitfield of 512:Esp 256:KO 128:Eq4 64:Eq3 32:Opn 16:G3 8:G2 4:G1 2:Pre2 1:Pre1
	$mangas[0]=http_request("Manga1","i",0); // single manga
	$mangas[1]=http_request("Manga2","i",0); // mangas a dos vueltas
	$mangas[2]=http_request("Manga3","i",0); // mangas a tres vueltas
	$mangas[3]=http_request("Manga4","i",0); // 1,2:GII 3,4:GIII
	$mangas[4]=http_request("Manga5","i",0);
	$mangas[5]=http_request("Manga6","i",0);
	$mangas[6]=http_request("Manga7","i",0);
	$mangas[7]=http_request("Manga8","i",0);
	$mangas[8]=http_request("Manga9","i",0); // mangas 3..9 are used in KO rondas
    // 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small 5:tiny 6:L+M 7:S+T 8:L+M+S+T 9:X 10:X+L 11:M+S+T 12:X+L+M+S+T
	$mode=http_request("Mode","i","0");
    $stats=http_request("Stats","i","0");
    $children=http_request("Children","i","0");
	$c= Competitions::getClasificacionesInstance("print_clasificacion_pdf",$jornada);
	$result=$c->clasificacionFinal($rondas,$mangas,$mode);
	// en caso de manga junior y children true separamos infantil de juvenil
    $split= ( ($children!==0) && ($rondas&16384)!==0 )?1:0;
	// Creamos generador de documento
    if (intval($c->getJornada()->Games)!==0)
        $pdf=new PrintClasificacionGames($prueba,$jornada,$mangas,$result,$mode);
	else $pdf = new PrintClasificacion($prueba,$jornada,$mangas,$result,$mode);
	$pdf->AliasNbPages();
	$pdf->composeTable($split);
    if($stats==1) $pdf->print_stats();
	$suffix=$c->getName($mangas,$mode);
	$pdf->Output("FinalScores_{$suffix}.pdf","D"); // "D" means output to web client (download)
} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}
?>