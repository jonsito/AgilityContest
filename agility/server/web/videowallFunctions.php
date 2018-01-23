<?php
/*
 videowallFunctions.php

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

require_once(__DIR__ . "/../logging.php");
require_once(__DIR__ . "/../auth/Config.php");
require_once(__DIR__ . "/VideoWall.php");

$sesion = http_request("Session","i",0);
$operacion = http_request("Operation","s",null);
$pendientes = http_request("Pendientes","i",10);
// on session==0, use this elements as IDentifiers
$prueba = http_request("Prueba","i",0);
$jornada = http_request("Jornada","i",0);
$manga = http_request("Manga","i",0);
$tanda = http_request("Tanda","i",0); // used on access from videowall
$mode = http_request("Mode","i",0); // used on access from public
$perro = http_request("Perro","i",0); // used on access from public
$before = http_request("Before","i",3); // to compose starting order window
$after = http_request("After","i",12); //  to compose starting order window

$vw=new VideoWall($sesion,$prueba,$jornada,$manga,$tanda,$mode);
try {
    if($operacion==="infodata") return $vw->videowall_infodata();
	if($operacion==="livestream") return $vw->videowall_livestream();
    if($operacion==="llamada") return $vw->videowall_llamada($pendientes); // pendientes por salir
    if($operacion==="window") return $vw->videowall_windowCall(intval($perro),intval($before),intval($after)); // 15 por detras y 4 por delante
    if($operacion==="teamwindow") return $vw->videowall_teamWindowCall(intval($perro),intval($before),intval($after));
} catch (Exception $e) {
	echo "<p>Error:<br />".$e->getMessage()."</p>";
    return 0;
}
