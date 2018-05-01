<?php
/*
getLogo.php

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
 * tool to retrieve an image from server by providing federation and canonical name
 */
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/modules/Federations.php");

$fed=http_request("Fed","i" ,http_request("Federation","i",0) );
$logo=http_request("Logo","s","null.png");
$fedname=Federations::getFederation(intval($fed))->get('Name');
$iconpath=getIconPath($fedname,$logo);
/*
$image = imagecreatefromstring(file_get_contents($iconpath));
imagealphablending($image, false); // preserve transparency
imagesavealpha($image, true);
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
*/
// from: http://stackoverflow.com/questions/2882472/php-send-file-to-user
$finfo = finfo_open(FILEINFO_MIME_TYPE);
header('Content-Type: ' . finfo_file($finfo, $iconpath));
finfo_close($finfo);
//Define file size
header('Content-Length: ' . filesize($iconpath));
ob_clean();
flush();
readfile($iconpath);
?>