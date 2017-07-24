<?php
/*
excel_listaPerros.php

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

/**
 * genera fichero excel de perros seleccionada desde el menu de la base de datos en el orden especificado en la pantalla
*/

require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/OrdenSalida.php');
require_once(__DIR__.'/../database/classes/Resultados.php');
require_once(__DIR__."/classes/Excel_PartialScores.php");

// Consultamos la base de datos
try {
    $idprueba=http_request("Prueba","i",0);
    $idjornada=http_request("Jornada","i",0);
    $idmanga=http_request("Manga","i",0);
    $mode=http_request("Mode","i",0);

    $mngobj= new Mangas("excelResultadosByManga",$idjornada);
    $manga=$mngobj->selectByID($idmanga);
    $resobj= Competitions::getResultadosInstance("excelResultadosByManga",$idmanga);

    // retrieve results
    $resultados=$resobj->getResultadosIndividual($mode); // throw exception if pending dogs
    $osobj= Competitions::getOrdenSalidaInstance("excelResultadosByManga",$idmanga);
    // reindex resultados in starting order
    $res=$osobj->getData(false,$mode,$resultados);
    // add trs/trm information
    $res['trs']=$resultados['trs'];

    // Creamos generador de documento
    $excel = new Excel_PartialScores($idprueba,$idjornada,$manga,$res,$mode);
    $excel->open();
    $excel->composeTable();
    $excel->close();
    return 0;
} catch (Exception $e) {
    die($e->getMessage());
}
?>