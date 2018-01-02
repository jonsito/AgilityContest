<?php

/**
 * Downloader.php
 * Created by PhpStorm.
 * User: jantonio
 * Date: 28/12/17
 * Time: 11:21

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

require_once (__DIR__."/../classes/DBObject.php");
require_once (__DIR__."/../../auth/Config.php");

class Downloader {
    protected $myDBObject;
    protected $myConfig;
    protected $myLogger;
    protected $timestamp;
    protected $serial;

    function __construct($timestamp,$serial) {
        $this->myDBObject=new DBObject("Downloader");
        $this->myConfig=Config::getInstance();
        $this->myLogger=new Logger("DatabaseDownloader",$this->myConfig->getEnv("debug_level"));
        $this->timestamp=$timestamp;
        $this->serial=$serial;
    }

    /**
     * retrieve from perroguiaclub every item newer than timestamp
     */
    function getUpdatedEntries() {
        // retrieve updated elements from database
        $res=$this->myDBObject->__select(
            "*",
            "PerroGuiaClub",
            "(Licencia != '') AND ( LastModified > '{$this->timestamp}')"
        );
        if (!$res) throw new Exception ("Downloader::getUpdatedEntries(): {$this->myDBObject->errormsg}");
        return $res;
    }

    /**
     * retrieve number of new entries in master server database
     */
    function checkForUpdatedEntries() {
        // retrieve updated elements from database
        $res=$this->myDBObject->__select(
            "count(*) AS NewEntries",
            "PerroGuiaClub",
            "(Licencia != '') AND ( LastModified > '{$this->timestamp}')"
        );
        if (!$res) throw new Exception ("Downloader::checkForUpdatedEntries(): {$this->myDBObject->errormsg}");
        return $res;
    }

    /**
     * Store retrieved data into temporary file to be parsed later
     * @param {array} $data
     */
    function saveRetrievedData($data) {
        if ($data==="") throw new Exception ("Downloader::saveRetrievedData(): no data received from client");
        $obj=json_decode($data);
        if($obj->total==0) return ""; // no data, nothing to save

        // los ficheros se guardan en la carpeta logs/updateRequests/serial-timestamp
        // si el fichero ya existe se ignora la peticion pues ya tiene los datos salvados
        //   esto en principio no deberia dar problemas cuando se usa la misma licencia desde dos
        //   ordenadores... pues no coincidira el timestamp, o bien uno sera el backup del otro
        $dir=__DIR__."/../../../../logs/updateRequests";
        @mkdir($dir);
        // convert "Y-m-d G:i:s" to "Ymd_Gi"
        $d=date("Ymd_gi",strtotime($this->timestamp));
        $fname="req-{$this->serial}-{$d}.json";
        if (!file_exists("{$dir}/{$fname}")) {
            $this->myLogger->trace("Downloader: storing data into file: {$dir}/{$fname}");
            file_put_contents("{$dir}/{$fname}",json_encode($obj->rows));
        }
        return "";
    }
}