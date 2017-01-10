<?php
/*
getRandomImage.php

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
$config =Config::getInstance();
/*
 * Take chroma key color value and compose a 640x480 image
*/
$imagesDir=__DIR__."/../images/wallpapers/";
$images=$images = glob($imagesDir . '*.{jpg,jpeg}', GLOB_BRACE);
$image=$images[rand(0, count($images) - 1)];
header('Content-type: image/png');
$im = @imagecreate(1920, 1080) or die("Cannot Initialize new GD image stream");
$rgb=hex2rgb($config->getEnv('vw_chromakey'));
$color_fondo = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
imagepng($im);
imagedestroy($im);
?>
