<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 15/03/18
 * Time: 11:05
 * file: AgilityContest_Master.php

Copyright  2013-2019 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
 * tools to be executed on AgilityContest Master Server
 */

// NOTICE: this macro only works on server as this scripts is intended to run on it
define("AC_BACKUP_FILE","/var/www/html/downloads/agility.sql");

class AgilityContest_Master {
    protected $license;
    protected $version;
    protected $client;

    function __construct($license,$version) {
        $this->license=$license;
        $this->version=$version;
        $this->client=$_SERVER['REMOTE_ADDR'];
    }

    public function sendBackup() {
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

    public function track($operation,$result="Success") {

    }

    public function licenseViolation() {

    }

    public function checkBlackList() {

    }
}


?>