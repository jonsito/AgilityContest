<?php
/*
print_etiquetas_pdf.php

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
require_once(__DIR__ . '/../../server/database/classes/DBObject.php');
require_once(__DIR__ . '/../../server/pdf/classes/PrintEtiquetasRSCE.php');
require_once(__DIR__ . '/../../server/pdf/classes/PrintEtiquetasCNEAC.php');

try {
	$mangas=array();
	$result=array();
	for($n=0;$n<9;$n++) $result[$n]=array();
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
	$mode=http_request("Mode","i",0); // 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small
	$rowcount=http_request("Start","i",0); // offset to first label in page
	$listadorsales=http_request("List","s",""); // CSV Dorsal List
	$prmode=http_request("PrintMode","i","1"); // 1:RSCE 2:CNEAC
	$discriminate=http_request("Discriminate","i","1"); // 0:any 1:only handlers belonging RSCE or CNEAC
	$global=http_request("Global","i",0); // 0: just selected categoriesByMode; else all categories
	
	// buscamos los recorridos asociados a la mangas
	$dbobj=new DBObject("print_etiquetas_csv");
	$mng=$dbobj->__getObject("mangas",$mangas[0]);
	$prb=$dbobj->__getObject("pruebas",$prueba);
	$c= Competitions::getClasificacionesInstance("print_etiquetas_pdf",$jornada);

	// Creamos generador de documento
	if ($prmode===1) { // RSCE
		$pdf = new PrintEtiquetasRSCE($prueba,$jornada,$mangas);
		$pdf->AddPage();
	} else { // CNEAC
		$pdf = new PrintEtiquetasCNEAC($prueba,$jornada,$mangas);
		$rowcount=0;
	}
	// indicamos que se deben numerar las paginas
	$pdf->AliasNbPages();

	// obtenemos la clasificacion de la tanda seleccionada
	$clasificaciones=array();
	if ($global==0) {
		$suffix=$c->getName($mangas,$mode);
		$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,$mode);
	} else {
		$suffix="Global";
		// buscamos todas las clasificaciones de esta manga y componemos el array
		$heights=Competitions::getHeights($prueba,$jornada,$mangas[0]); // same heights for every round
		switch($mng->Recorrido) {
			case 0: // recorridos separados xlarge large medium small toy
				if ($heights==3) {
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,0); // L
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,1); // M
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,2); // S
				}
				if ($heights==4) {
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,0); // L
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,1); // M
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,2); // S
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,5); // T
				}
				if ($heights==5) {
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,9); // X
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,0); // L
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,1); // M
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,2); // S
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,5); // T
				}
				break;
			case 1: // dos grupos: (l+ms) (lm+st) (xl+mst)
				if ($heights==3) {
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,0); // L
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,3); // MS
				}
				if ($heights==4) {
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,6); // LM
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,7); // ST
				}
				if ($heights==5) {
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,10); // XL
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,11); // MST
				}
				break;
			case 2: // recorrido conjunto xlarge-large+medium+small+toy
				if ($heights==3) {
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,4); // LMS
				}
				if ($heights==4){
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,8); // LMST
				}
				if ($heights==5) {
					$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,12); // XLMST
				}
				break;
			case 3: // tres grupos. Xlarge-Large Medium Small-Toy implica $heights==5
				$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,10); // XL
				$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,1); // M
				$clasificaciones[]=$c->clasificacionFinal($rondas,$mangas,7); // ST
				break;
		}
	}
	// iterate every evaluated clasification series
	foreach($clasificaciones as $cl) {
		$pdf->setRoundData($cl);
		$res=$cl['rows'];
		// ordenamos los resultados por dorsales
		usort($res,function($a,$b){return ($a['Dorsal']>$b['Dorsal'])?1:-1;});
		// imprimimos las etiquetas de esta categoria
		$rowcount=$pdf->composeTable($res,$rowcount,$listadorsales,$discriminate);
	}
	// indicamos nombre del fichero de salida
	$pdf->Output("Etiquetas_{$suffix}.pdf","D"); // "D" means output to web client (download)

} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}

?>
