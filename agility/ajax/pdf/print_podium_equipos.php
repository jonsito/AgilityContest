<?php
/*
print_podium.php

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

require_once(__DIR__ . "/../../server/logging.php");
require_once(__DIR__ . "/../../server/modules/Federations.php");
require_once(__DIR__ . '/../../server/modules/Competitions.php');
require_once(__DIR__ . '/../../server/database/classes/Mangas.php');
require_once(__DIR__ . '/../../server/pdf/classes/PrintClasificacionTeam.php');

try {
	$result=null;
	$mangas=array();
	$prueba=http_request("Prueba","i",0);
	$jornada=http_request("Jornada","i",0);
	$rondas=http_request("Rondas","i","0"); // bitfield of 512:Esp 256:KO 128:Eq4 64:Eq3 32:Opn 16:G3 8:G2 4:G1 2:Pre2 1:Pre1
	$mangas[0]=http_request("Manga1","i",0); // single manga
	$mangas[1]=http_request("Manga2","i",0); // mangas a dos vueltas
	$mangas[2]=http_request("Manga3","i",0);
	$mangas[3]=http_request("Manga4","i",0); // 1,2:GII 3,4:GIII
	$mangas[4]=http_request("Manga5","i",0);
	$mangas[5]=http_request("Manga6","i",0);
	$mangas[6]=http_request("Manga7","i",0);
	$mangas[7]=http_request("Manga8","i",0);// mangas 3..8 are used in KO rondas
	
	// buscamos los recorridos asociados a la manga
    $mangasInfo=Mangas::getMangaInfo($mangas[0]);
	$c= Competitions::getClasificacionesInstance("print_podium_pdf",$jornada);
    $heights=Competitions::getCompetition($mangasInfo->Prueba,$mangasInfo->Jornada)->getHeights($mangasInfo);
	$result=array();
	switch($mangasInfo->Manga->Recorrido) {
		case 0: // recorridos separados large medium small
            $result[0]=$c->clasificacionFinalEquipos($rondas,$mangas,0);
            $result[1]=$c->clasificacionFinalEquipos($rondas,$mangas,1);
            $result[2]=$c->clasificacionFinalEquipos($rondas,$mangas,2);
			if ($heights!=3) {
                $result[5]=$c->clasificacionFinalEquipos($rondas,$mangas,5);
			}
            if ($heights==5) {
                $result[9]=$c->clasificacionFinalEquipos($rondas,$mangas,9);
            }
			break;
		case 1: // dos grupos (l+ms) (lm+st) (xl+mst)
			if ($heights==3) {
				$result[0]=$c->clasificacionFinalEquipos($rondas,$mangas,0);
				$result[3]=$c->clasificacionFinalEquipos($rondas,$mangas,3);
			}
			if ($heights==4) {
                $result[6]=$c->clasificacionFinalEquipos($rondas,$mangas,6);
                $result[7]=$c->clasificacionFinalEquipos($rondas,$mangas,7);
			}
			if ($heights==5) {
                $result[10]=$c->clasificacionFinalEquipos($rondas,$mangas,10);
                $result[11]=$c->clasificacionFinalEquipos($rondas,$mangas,11);
            }
			break;
		case 2: // recorrido conjunto large+medium+small
			if ($heights==3) {
                $result[4]=$c->clasificacionFinalEquipos($rondas,$mangas,4);
			}
			if ($heights==4) {
                $result[8]=$c->clasificacionFinalEquipos($rondas,$mangas,8);
			}
			if ($heights==5) {
                $result[12]=$c->clasificacionFinalEquipos($rondas,$mangas,12);
            }
			break;
        case 3: // tres grupos (xl+m+st). Implica $heights==5
            $result[10]=$c->clasificacionFinalEquipos($rondas,$mangas,10);
            $result[1]=$c->clasificacionFinalEquipos($rondas,$mangas,1);
            $result[7]=$c->clasificacionFinalEquipos($rondas,$mangas,7);
            break;
	}
	
	// Creamos generador de documento
	$pdf = new PrintClasificacionTeam($mangasInfo->Prueba,$mangasInfo->Jornada);
	$pdf->set_FileName("Podium_Teams.pdf");
	$pdf->AliasNbPages();
	foreach($result as $mode =>$clasif) {
	    if(count($clasif['individual'])==0) continue; // skip categories with no teams
	    $pdf->pct_setParameters($mangas,$clasif,$mode,_("Podium")." "._("Teams"));
        $pdf->composeTable(3);
    }
	$pdf->Output($pdf->get_FileName(),"D"); // "D" means open download dialog
} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}
?>