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
        $result=array();

        // retrieve updated dogs from database
        $res=$this->myDBObject->__select(
            "Perros.*,Guias.ServerID AS GuiasServerID",
            "Perros,Guias",
            "(Perros.Guia=Guias.ID) AND (Licencia!='') AND (Perros.ServerID != 0) AND ( Perros.LastModified > '{$this->timestamp}')"
        );
        if (!$res) throw new Exception ("Updater::getUpdatedEntries(Perros): {$this->myDBObject->conn->error}");
        $result['Perros']=$res['rows'];

        // retrieve updated handlers from database
        $res=$this->myDBObject->__select(
            "Clubes.*,Clubes.ServerID as ClubesServerID",
            "Guias,Clubes",
            "(Guias.Club=Clubes.ID) AND (Guias.ServerID != 0) AND ( Guias.LastModified > '{$this->timestamp}')"
        );
        if (!$res) throw new Exception ("Updater::getUpdatedEntries(Guias): {$this->myDBObject->conn->error}");
        $result['Guias']=$res['rows'];

        // retrieve updated Clubs from database
        $res=$this->myDBObject->__select(
            "Clubes.*",
            "Clubes",
            "(ServerID != 0) AND ( LastModified > '{$this->timestamp}')"
        );
        if (!$res) throw new Exception ("Updater::getUpdatedEntries(Clubes): {$this->myDBObject->conn->error}");
        $result['Clubes']=$res['rows'];

        // retrieve updated Judges from database
        $res=$this->myDBObject->__select(
            "Jueces.*",
            "Jueces",
            "(ServerID != 0) AND ( LastModified > '{$this->timestamp}')"
        );
        if (!$res) throw new Exception ("Updater::getUpdatedEntries(Jueces): {$this->myDBObject->conn->error}");
        $result['Jueces']=$res['rows'];

        // add timestamp and "Operation" to request data
        $result['timestamp']=$this->timestamp;
        $result['Operation']="updateResponse";
        $result['total']=max(count($result['Perros']),count($result['Guias']),count($result['Clubes']),count($result['Jueces']));
        return $result;
    }

    /**
     * retrieve number of new entries in master server database
     * notice that this returns an orientative value, as changes may come from 4 different tables
     * @return { array } (total, rows) with select() response
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

        // prepare store dir and timestamp format
        $dir=__DIR__."/../../../../logs/updateRequests";
        @mkdir($dir);
        $d=date("Ymd_gi",strtotime($this->timestamp)); // convert "Y-m-d G:i:s" to "Ymd_Gi"
        foreach (array('Perros','Guias','Clubes','Jueces') as $item) {
            if (count($obj->$item)==0) continue;
            // los ficheros se guardan en la carpeta logs/updateRequests/serial-timestamp
            // si el fichero ya existe se ignora la peticion pues ya tiene los datos salvados
            //   esto en principio no deberia dar problemas cuando se usa la misma licencia desde dos
            //   ordenadores... pues no coincidira el timestamp, o bien uno sera el backup del otro
            $fname="{$item}-{$this->serial}-{$d}.json";
            if (!file_exists("{$dir}/{$fname}")) {
                $this->myLogger->trace("Downloader: storing data into file: {$dir}/{$fname}");
                file_put_contents("{$dir}/{$fname}",json_encode($obj->$item));
            }
        }
        return "";
    }
}