<?php
/*
excel_listaPerros.php

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

/**
 * genera fichero excel de perros seleccionada desde el menu de la base de datos en el orden especificado en la pantalla
 */

require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/logging.php");

require_once(__DIR__ . '/../../server/database/classes/Mangas.php');
require_once(__DIR__ . '/../../server/database/classes/OrdenSalida.php');
require_once(__DIR__ . '/../../server/database/classes/Resultados.php');

require_once(__DIR__ . '/../../server/excel/classes/OrdenSalidaWriter.php');
require_once(__DIR__ . "/../../server/excel/classes/EntrenamientosWriter.php");
require_once(__DIR__ . "/../../server/excel/classes/InscripcionesWriter.php");
require_once(__DIR__ . "/../../server/excel/classes/DogsWriter.php");
require_once(__DIR__ . "/../../server/excel/classes/ClubesWriter.php");
require_once(__DIR__ . "/../../server/excel/classes/ClasificacionesWriter.php");
require_once(__DIR__ . "/../../server/excel/classes/PartialScoresWriter.php");

// Obtenemos parametros de la peticion
$club=http_request("Club","i",0); // -1:empty template 0:inscriptions x:club template
$federation=http_request("Federation","i",-1);
$prueba=http_request("Prueba","i",-1);
$jornada=http_request("Jornada","i",0);
$manga=http_request("Manga","i",0);
$mode=http_request("Mode","i",0);
$categorias=http_request("Categorias","s","-");
$conjunta=http_request("EqConjunta","i",0);
$operation=http_request("Operation","s","");
try {
    switch($operation) {
        case "OrdenSalida":
            $excel = new OrdenSalidaWriter($prueba,$jornada,$manga,$categorias,$conjunta);
            break;
        case "Inscripciones":
            $excel = new InscripcionesWriter($prueba,$club);
            break;
        case "PartialScores":
            // get required objects
            // round
            $mngobj= new Mangas("excelResultadosByManga",$jornada);
            $mng=$mngobj->selectByID($manga);
            // results
            $resobj= Competitions::getResultadosInstance("excelResultadosByManga",$manga);
            $resultados=$resobj->getResultadosIndividual($mode); // throw exception if pending dogs
            // starting order
            $osobj= Competitions::getOrdenSalidaInstance("excelResultadosByManga",$manga);
            $res=$osobj->getData(false,$mode,$resultados); // reindex resultados in starting order
            do_log("results before:".count($resultados['rows'])." after:".count($res['rows']));
            $res['trs']=$resultados['trs']; // add trs/trm information
            // Creamos generador de documento
            $excel = new PartialScoresWriter($prueba,$jornada,$mng,$res,$mode);
            break;
        case "Dogs":
            $excel = new DogsWriter($federation);
            break;
        case "Clubes":
            $excel = new ClubesWriter($federation);
            break;
        case "TrainingTable":
            $excel = new EntrenamientosWriter($prueba,$federation);
            break;
        case "FinalScores":
            $excel = new ClasificacionesWriter($prueba);
            break;
        default: throw new Exception("Excel writer: unknown operation: ".$operation);
    }
    // 	Creamos generador de documento
    $excel->open();
    $excel->composeTable();
    $excel->close();
    return 0;
} catch (Exception $e) {
    die ("Error generating Excel file: ".$e->getMessage());
}
?>
