<?php
/*
publicFunctions.php

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

require_once(__DIR__ . "/../tools.php");
require_once(__DIR__ . "/public.php");

$operacion = http_request("Operation","s",null);
$pendientes = http_request("Pendientes","i",10);
// on session==0, use this elements as IDentifiers
$prueba = http_request("Prueba","i",0);
$jornada = http_request("Jornada","i",0);
$manga = http_request("Manga","i",0);
$mode = http_request("Mode","i",0); // used on access from public

$pb=new PublicWeb($prueba,$jornada,$manga,$mode);
try {
    switch ($operacion) {
        case "infodata": 
            $res=$pb->publicweb_infodata();
            echo json_encode($res);
            break;
        case "deploy":   
            $res=$pb->publicweb_deploy(); 
            echo json_encode($res);
            break;
        default:throw new Exception("publicFunctions.php: operacion invalida:'$operacion'"); break;
    }
} catch (Exception $e) {
	echo "<p>Error:<br />".$e->getMessage()."</p>";
}
return 0;
