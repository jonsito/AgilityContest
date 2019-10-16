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
require_once(__DIR__ . '/../../server/database/classes/DBObject.php');
require_once(__DIR__ . '/../../server/pdf/classes/PrintClasificacionGeneral.php');
require_once(__DIR__ . '/../../server/pdf/classes/PrintPodium.php');

function pp_getArray($mode,$data) {
    $result = array(
        'Mode' => $mode,
        'Data' => $data['rows'],
        'TRS'  => array($data['trs1'],$data['trs2'],$data['trs3'],$data['trs4'],$data['trs5'],$data['trs6'],$data['trs7'],$data['trs8'] ),
        'Jueces' => $data['jueces']
    );
    return $result;
}

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
	$mangas[7]=http_request("Manga8","i",0);
	$mangas[8]=http_request("Manga9","i",0); // mangas 3..9 are used in KO rondas
    $podium=http_request("Podium","i",0); // 0:general-completa 1:Podium-3primeros
	
	// buscamos los recorridos asociados a la manga
    $mangasInfo=Mangas::getMangaInfo($mangas[0]);
	$c= Competitions::getClasificacionesInstance("print_podium_pdf",$jornada);
	$result=array();
	$heights=Competitions::getCompetition($mangasInfo->Prueba,$mangasInfo->Jornada)->getHeights($mangasInfo);
	switch($mangasInfo->Manga->Recorrido) {
		case 0: // recorridos separados xlarge large medium small toy
			$l=$c->clasificacionFinal($rondas,$mangas,0);
			$m=$c->clasificacionFinal($rondas,$mangas,1);
			$s=$c->clasificacionFinal($rondas,$mangas,2);
            if ($heights==3) {
                $result[] = pp_getArray(0,$l);
                $result[] = pp_getArray(1,$m);
                $result[] = pp_getArray(2,$s);
            }
            if ($heights==4) {
                $t=$c->clasificacionFinal($rondas,$mangas,5);
                $result[] = pp_getArray(0,$l);
                $result[] = pp_getArray(1,$m);
                $result[] = pp_getArray(2,$s);
                $result[] = pp_getArray(5,$t);
            }
            if ($heights==5) {
                $t=$c->clasificacionFinal($rondas,$mangas,5);
                $x = $c->clasificacionFinal($rondas, $mangas, 9);
                $result[] = pp_getArray(9,$x);
                $result[] = pp_getArray(0,$l);
                $result[] = pp_getArray(1,$m);
                $result[] = pp_getArray(2,$s);
                $result[] = pp_getArray(5,$t);
            }
			break;
        case 1: // dos grupos: (l+ms) (lm+st) (xl+mst)
			if ($heights==3) {
				$l=$c->clasificacionFinal($rondas,$mangas,0);
				$ms=$c->clasificacionFinal($rondas,$mangas,3);
                $result[] = pp_getArray(0,$l);
                $result[] = pp_getArray(3,$ms);
			}
			if ($heights==4) {
				$lm=$c->clasificacionFinal($rondas,$mangas,6);
				$st=$c->clasificacionFinal($rondas,$mangas,7);
                $result[] = pp_getArray(6,$lm);
                $result[] = pp_getArray(7,$st);
			}
			if ($heights==5) {
                $xl=$c->clasificacionFinal($rondas,$mangas,10);
                $mst=$c->clasificacionFinal($rondas,$mangas,11);
                $result[] = pp_getArray(10,$xl);
                $result[] = pp_getArray(11,$mst);
            }
			break;
		case 2: // recorrido conjunto xlarge-large+medium+small+toy
			if ($heights==3) {
				$lms=$c->clasificacionFinal($rondas,$mangas,4);
                $result[] = pp_getArray(4,$lms);
			}
			if ($heights==4){
				$lmst=$c->clasificacionFinal($rondas,$mangas,8);
                $result[] = pp_getArray(8,$lmst);
			}
			if ($heights==5) {
                $xlmst=$c->clasificacionFinal($rondas,$mangas,12);
                $result[] = pp_getArray(12,$xlmst);
            }
			break;
        case 3: // tres grupos. Xlarge-Large Medium Small-Toy implica $heights==5
            $xl=$c->clasificacionFinal($rondas,$mangas,10);
            $m=$c->clasificacionFinal($rondas,$mangas,1);
            $st=$c->clasificacionFinal($rondas,$mangas,7);
            $result[] = pp_getArray(10,$xl);
            $result[] = pp_getArray(1,$m);
            $result[] = pp_getArray(7,$st);
            break;
	}
	
	// Creamos generador de documento
    if ($podium==1) $pdf = new PrintPodium($prueba,$jornada,$mangas,$result);
    else $pdf = new PrintClasificacionGeneral($prueba,$jornada,$mangas,$result);
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output($pdf->get_FileName(),"D"); // "D" means open download dialog
} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}
?>