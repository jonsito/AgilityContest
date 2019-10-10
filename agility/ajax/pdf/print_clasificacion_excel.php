<?php
/*
print_clasificacion_excel.php

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
 * genera un fichero excel con los datos de la clasificacion de la ronda
 */

require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/logging.php");
require_once(__DIR__ . '/../../server/modules/Competitions.php');
require_once(__DIR__ . '/../../server/pdf/classes/PrintClasificacionExcel.php');

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
	
	$mode=http_request("Mode","i","0"); // 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small
	$c= Competitions::getClasificacionesInstance("print_etiquetas_pdf",$jornada);
	$result=$c->clasificacionFinal($rondas,$mangas,$mode);

	// Creamos generador de documento
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=clasificacion.xls");
	header("Content-Transfer-Encoding: binary ");
	
	$excel=new PrintClasificacionExcel($prueba,$jornada,$mangas);
	$excel->xlsBOF();
	$base=$excel->write_PageHeader($prueba,$jornada,$mangas);
	
	// buscamos los recorridos asociados a la mangas
	$c= Competitions::getClasificacionesInstance("print_clasificacion_excel",$jornada);
	// buscamos las alturas referidas segun la manga y el tipo de competicion
    $dbobj=new DBObject("print_podium_individual");
    $mng=$dbobj->__getObject("mangas",$mangas[0]);
    $prb=$dbobj->__getObject("pruebas",$prueba);
    $jrd=$dbobj->__getObject($prueba,$jornada);
    $mangasInfo=Mangas::getMangaInfo($mng->ID);
    $heights=Competitions::getCompetition($prb,$jrd)->getHeights($mangasInfo);

    $result=array();
	switch($excel->manga1->Recorrido) {
		case 0: // recorridos separados xlarge large medium small tiny
            if ($heights==3) {
                $l=$c->clasificacionFinal($rondas,$mangas,0);
                $base = $excel->composeTable($mangas,$l,0,$base+1);
                $m=$c->clasificacionFinal($rondas,$mangas,1);
                $base = $excel->composeTable($mangas,$m,1,$base+1);
                $s=$c->clasificacionFinal($rondas,$mangas,2);
                $base = $excel->composeTable($mangas,$s,2,$base+1);
            }
            if ($heights==4) {
                $l=$c->clasificacionFinal($rondas,$mangas,0);
                $base = $excel->composeTable($mangas,$l,0,$base+1);
                $m=$c->clasificacionFinal($rondas,$mangas,1);
                $base = $excel->composeTable($mangas,$m,1,$base+1);
                $s=$c->clasificacionFinal($rondas,$mangas,2);
                $base = $excel->composeTable($mangas,$s,2,$base+1);
                $t=$c->clasificacionFinal($rondas,$mangas,5);
                $base = $excel->composeTable($mangas,$t,5,$base+1);
            }
            if ($heights==5) {
                $x=$c->clasificacionFinal($rondas,$mangas,9);
                $base = $excel->composeTable($mangas,$x,9,$base+1);
                $l=$c->clasificacionFinal($rondas,$mangas,0);
                $base = $excel->composeTable($mangas,$l,0,$base+1);
                $m=$c->clasificacionFinal($rondas,$mangas,1);
                $base = $excel->composeTable($mangas,$m,1,$base+1);
                $s=$c->clasificacionFinal($rondas,$mangas,2);
                $base = $excel->composeTable($mangas,$s,2,$base+1);
                $t=$c->clasificacionFinal($rondas,$mangas,5);
                $base = $excel->composeTable($mangas,$t,5,$base+1);
            }
			break;
		case 1: // dos grupos (l+ms) (lm+st) (xl+mst)
			if ($heights==3) {
				$l=$c->clasificacionFinal($rondas,$mangas,0);
				$base = $excel->composeTable($mangas,$l,0,$base+1);
				$ms=$c->clasificacionFinal($rondas,$mangas,3);
				$base = $excel->composeTable($mangas,$ms,3,$base+1);
			}
			if ($heights==4) {
				$lm=$c->clasificacionFinal($rondas,$mangas,6);
				$base = $excel->composeTable($mangas,$lm,6,$base+1);
				$st=$c->clasificacionFinal($rondas,$mangas,7);
				$base = $excel->composeTable($mangas,$st,7,$base+1);
			}
			if ($heights==5) {
                $xl=$c->clasificacionFinal($rondas,$mangas,10);
                $base = $excel->composeTable($mangas,$xl,10,$base+1);
                $mst=$c->clasificacionFinal($rondas,$mangas,11);
                $base = $excel->composeTable($mangas,$mst,11,$base+1);
            }
			break;
		case 2: // recorrido conjunto large+medium+small+tiny
			if ($heights==3) {
				$lms=$c->clasificacionFinal($rondas,$mangas,4);
				$base = $excel->composeTable($mangas,$lms,4,$base+1);
			}
			if ($heights==4) {
				$lmst=$c->clasificacionFinal($rondas,$mangas,8);
				$base = $excel->composeTable($mangas,$lmst,8,$base+1);
			}
			if ($heights==5) {
                $xlmst=$c->clasificacionFinal($rondas,$mangas,12);
                $base = $excel->composeTable($mangas,$xlmst,12,$base+1);
            }
			break;
        case 3: // tres grupos. Implica $heights==5
            $xl=$c->clasificacionFinal($rondas,$mangas,10);
            $base = $excel->composeTable($mangas,$xl,10,$base+1);
            $m=$c->clasificacionFinal($rondas,$mangas,1);
            $base = $excel->composeTable($mangas,$m,1,$base+1);
            $st=$c->clasificacionFinal($rondas,$mangas,7);
            $base = $excel->composeTable($mangas,$st,7,$base+1);
            break;
	}
	$excel->xlsEOF();
} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}
?>