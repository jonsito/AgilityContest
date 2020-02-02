<?php
/*
print_resultadosByManga.php

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
 * genera un pdf con los participantes ordenados segun los resultados de la manga
 */

require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/logging.php");
require_once(__DIR__ . '/../../server/modules/Competitions.php');
require_once(__DIR__ . '/../../server/database/classes/Mangas.php');
require_once(__DIR__ . "/../../server/pdf/classes/PrintResultadosByManga.php");
require_once(__DIR__ . "/../../server/pdf/classes/PrintResultadosKO.php");
require_once(__DIR__ . "/../../server/pdf/classes/PrintResultadosGames.php");
require_once(__DIR__ . "/../../server/pdf/classes/PrintParcialGeneral.php");

// Consultamos la base de datos
try {
	$idprueba = http_request("Prueba","i",0);
	$idjornada = http_request("Jornada","i",0);
	$idmanga = http_request("Manga","i",0);
    $modes = http_request("Mode","i",0);
	$global = http_request("Global","i",0);
    $title = http_request("Title","s",_("Partial scores"));
    // usado en cinco alturas cuando se quiere sacar listados mezclando alturas pero conservando el TRS de cada una
    $merge=http_request("Merge","i",0); // 0:separado 1:dos_grupos 3:tres_grupos 2:conjunto ( Recorrido )
	
	$mngobj= new Mangas("printResultadosByManga",$idjornada);
	$manga=$mngobj->selectByID($idmanga);
	$resobj= Competitions::getResultadosInstance("printResultadosByManga",$idmanga);
    $results=array();
    $mergecats=null;
    if ($global==0) { // single height
        $results=$resobj->getResultadosIndividual($modes); // throw exception if pending dogs
        $pdf = new PrintResultadosByManga($idprueba,$idjornada,$manga,$results,$modes,$title);
    } else { // print all results on provided round
        $heights=Competitions::getHeights($idprueba,$idjornada,$idmanga);
        // modes 0:L 1:M 2:S 3:MS 4:LMS 5:T 6:LM 7:ST 8:LMST 9:X 10:XL 11:MST 12:XLMST
        // retrieve all results for this round according recorrido/heights
        switch ($manga->Recorrido) {
            case 0: // recorridos separados xlarge large medium small toy
                if ($heights==3) {
                    $results[]=$resobj->getResultadosIndividual(0); // L
                    $results[]=$resobj->getResultadosIndividual(1); // M
                    $results[]=$resobj->getResultadosIndividual(2); // S
                    $modes = array(0,1,2);
                }
                if ($heights==4) {
                    $results[]=$resobj->getResultadosIndividual(0); // L
                    $results[]=$resobj->getResultadosIndividual(1); // M
                    $results[]=$resobj->getResultadosIndividual(2); // S
                    $results[]=$resobj->getResultadosIndividual(5); // T
                    $modes = array(0,1,2,5);
                }
                if ($heights==5) {
                    $results[]=$resobj->getResultadosIndividual(9); // X
                    $results[]=$resobj->getResultadosIndividual(0); // L
                    $results[]=$resobj->getResultadosIndividual(1); // M
                    $results[]=$resobj->getResultadosIndividual(2); // S
                    $results[]=$resobj->getResultadosIndividual(5); // T
                    $modes = array(9,0,1,2,5);
                    // si merge es distinto de cero, tenemos que mezclar los resultados.
                    switch($merge) { //  Calculamos las matrices de mezclado
                        case 1: $mergecats=[ [0,1] /* X+L */ ,[2,3,4] /* M+S+T */ ]; break;
                        case 2: $mergecats=[ [0,1,2,3,4,5] /* X+L+M+S+T */ ]; break;
                        case 3: $mergecats=[ [0,1] /* X+L */,[2] /* M */ ,[3,4] /* S+T */ ]; break;
                        default: $mergecats=null; break;
                    }
                }
                break;
            case 1: // dos grupos: (l+ms) (lm+st) (xl+mst)
                if ($heights==3) {
                    $results[]= $resobj->getResultadosIndividual(0); // L
                    $results[]= $resobj->getResultadosIndividual(3); // MS
                    $modes = array(0,3);
                }
                if ($heights==4) {
                    $results[]= $resobj->getResultadosIndividual(6); // LM
                    $results[]= $resobj->getResultadosIndividual(7); // ST
                    $modes = array(6,7);
                }
                if ($heights==5) {
                    $results[]= $resobj->getResultadosIndividual(10); // XL
                    $results[]= $resobj->getResultadosIndividual(11); // MST
                    $modes = array(10,11);
                }
                break;
            case 2: // recorrido conjunto xlarge-large+medium+small+toy
                if ($heights==3) $results[]= $resobj->getResultadosIndividual(4); // LMS
                if ($heights==4) $results[]= $resobj->getResultadosIndividual(8); // LMST
                if ($heights==5) $results[]= $resobj->getResultadosIndividual(12); // XLMS
                $modes = array(4,8,12);
                break;
            case 3: // tres grupos. Xlarge-Large Medium Small-Toy implica $heights==5
                $results[]=$resobj->getResultadosIndividual(10); // XL
                $results[]=$resobj->getResultadosIndividual(1); // M
                $results[]=$resobj->getResultadosIndividual(7); // ST
                $modes = array(10,1,7);
                break;
        }
        $pdf = new PrintParcialGeneral($idprueba,$idjornada,$manga,$results,$modes,$title);
    }
	// Creamos generador de documento
    if ( isMangaKO($manga->Tipo)) {
        $pdf = new PrintResultadosKO($idprueba,$idjornada,$manga,$results,$modes,$title);
        $mergecats=null;
    }
    if ( isMangaGames($manga->Tipo) ) { // snooker, gambler
        $pdf = new PrintResultadosGames($idprueba,$idjornada,$manga,$results,$modes,$title);
        $mergecats=null;
    }
	$pdf->AliasNbPages();
    if ($mergecats==null) $pdf->composeTable();
    else  $pdf->composeMergedTable($mergecats);
	$pdf->Output($pdf->get_FileName(),"D"); // "D" means open download dialog
} catch (Exception $e) {
	die($e->getMessage());
}
?>