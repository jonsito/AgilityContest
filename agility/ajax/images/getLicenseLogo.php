<?php
/*
getLicenseLogo.php

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
require_once(__DIR__ . "/../../server/auth/AuthManager.php");

$myAuth=AuthManager::getInstance("getLicenseLogo");
$data=$myAuth->getLicenseLogo();
if ($data==null) {
    $path=getIconPath('rsce',"null.png");
    $logo=file_get_contents($path);
} else {
    $logo=base64_decode($data);
}
do_log(json_encode($logo));
header('Content-Type: image/png');
header('Content-Length: ' . strlen($logo));
ob_clean();
flush();
echo $logo;
?>