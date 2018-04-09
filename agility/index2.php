<?php
/*
index2.php

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

// just a simple redirector to public/index2.php
// from: https://stackoverflow.com/questions/15110355/how-to-safely-get-full-url-of-parent-directory-of-current-php-page
$url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$url .= $_SERVER['SERVER_NAME'];
$url .= htmlspecialchars($_SERVER['REQUEST_URI']);
$url= str_replace("index2.php","public/index2.php",$url);
header("Location: {$url}",false);
die();
?>
