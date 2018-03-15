<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 15/03/18
 * Time: 11:05
 * file: getBackup.php

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

/*
 * a little tool to verify client credentials and on success download latest backup
 */

// NOTICE: this macro only works on server as this scripts is intended to run on it
define("AC_BACKUP_FILE","/var/www/html/downloads/agility.sql");

function sendBackup() {
    // $f=date("Ymd_Hi");
    $fd=fopen(AC_BACKUP_FILE,"r");
    if (!$fd) {
        setcookie('fileDownload','false',time()+30,"/");
        header("Cache-Control", "no-cache, no-store, must-revalidate");
    } else {
        $fsize = filesize(AC_BACKUP_FILE);
        // notice false: do not show any dialog, just download
        setcookie('fileDownload','false',time()+30,"/");
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=agility.sql");
        header("Content-length: $fsize");
        header("Cache-control: private"); //use this to open files directly
        while(!feof($fd)) {
            $buffer = fread($fd, 2048);
            echo $buffer;
        }
        fclose ($fd);
    }
}

ini_set('zlib.output_compression', 0);
header("Access-Control-Allow-Origin: https//{$_SERVER['SERVER_ADDR']}/agility",false);
header("Access-Control-Allow-Origin: https://{$_SERVER['SERVER_NAME']}/agility",false);
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
if (!isset($config) ) $config=Config::getInstance();

/* check for properly installed xampp */
if( ! function_exists('openssl_get_publickey')) {
    die("Invalid configuration: please uncomment line 'module=php_openssl.dll' in file '\\xampp\\php\\php.ini'");
}
/* Check operating system against requested protocol */
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'LIN') {
    // en windows/android hay que usar https para que las cosas funcionen
    if (!is_https()) die("You MUST use https protocol to access this file");
}
/* check for properly installed xampp */
if( ! function_exists('password_verify')) {
    die("Invalid environment: php-5.5.X or higher version installed is required");
}

$ver=http_request("Revision","s","");
$lic=http_request("License","s","");

// if no license nor version is provided, abort
if ($ver==="") die("AgilityContest client revision info is not provided");
if ($lic==="") die("AgilityContest client license number is not provided");
if (intval($lic)===0) die("AgilityContest client has no registered license");
// PENDING: check for blacklisted licenses :-)

// PENDING: generate log
sendBackup();
?>