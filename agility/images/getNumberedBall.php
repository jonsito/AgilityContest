<?php
require_once(__DIR__."/../server/tools.php");
/*
getNumberedBall.php

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
 * tool to create a png with a numbered ball.
 * Usefull for WAO Games
 */

$color=http_request("Color","s","FFF");
$bgcolor=http_request("Background","s","000");
$number=http_request("Number","s"," ");
$logo=http_request("Logo","s","null.png");
// crear una imagen "vacia"
$imagen = imagecreate(51, 51);
// color de fondo
$c=hex2rgb($bgcolor); // primer colorallocate sets background
$fondo=imagecolorallocate($imagen, $c[0], $c[1], $c[2]);
//color para la bola
$c=hex2rgb($color);
$bola = imagecolorallocate($imagen, $c[0], $c[1], $c[2]);
// colores blanco y negro
$black=imagecolorallocate($imagen,0,0,0);
$white=imagecolorallocate($imagen, 255,255, 255);
// pintamos bola coloreada
imagefilledellipse($imagen, 25, 25, 49, 49, $bola);
// pintamos centro de la bola y el texto
imagefilledellipse($imagen, 25, 25, 30, 30, $white);
$font = "./arial.ttf";
imagettftext($imagen, 20, 0, (strlen($number)==1)?17:11, 35, $black, $font, $number);
// imprimir la imagen
header("Content-type: image/png");
imagepng($imagen);
imagedestroy($imagen);
?>