<?php
/*
print_clasificacion.php

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
 * genera un CSV con los datos para las etiquetas
 */

require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__.'/../modules/Competitions.php');
require_once(__DIR__.'/classes/PrintClasificacion.php');
require_once(__DIR__.'/classes/PrintClasificacionGames.php');

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
	$mode=http_request("Mode","i","0"); // 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small 5:tiny 6:L+M 7:S+T 8:L+M+S+T
	$c= Competitions::getClasificacionesInstance("print_clasificacion_pdf",$jornada);
	$result=$c->clasificacionFinal($rondas,$mangas,$mode);
	// Creamos generador de documento
    if (intval($c->getJornada()->Games)!==0)
        $pdf=new PrintClasificacionGames($prueba,$jornada,$mangas,$result,$mode);
	else $pdf = new PrintClasificacion($prueba,$jornada,$mangas,$result,$mode);
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$suffix=$c->getName($mangas,$mode);
	$pdf->Output("FinalScores_{$suffix}.pdf","D"); // "D" means output to web client (download)
} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}
?>