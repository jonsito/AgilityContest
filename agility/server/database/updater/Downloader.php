<?php

/**
 * Downloader.php
 * Created by PhpStorm.
 * User: jantonio
 * Date: 28/12/17
 * Time: 11:21

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

require_once (__DIR__."/../classes/DBObject.php");
require_once (__DIR__."/../../auth/Config.php");

class Downloader {
    protected $myDBObject;
    protected $myConfig;
    protected $myLogger;
    protected $timestamp;
    protected $serial;

    /**
     * Downloader constructor.
     * @param {string} $timestamp time stamp mark provided by requester in date("Ymd_Hi") format
     * @param {string} $serial license serial numbar
     * @throws Exception on database connection error
     */
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
            "perros.*,guias.Nombre AS NombreGuia,guias.ServerID AS GuiasServerID",
            "perros,guias",
            "(perros.Guia=guias.ID) AND ". // table join
                  "(Licencia != '') AND (perros.ServerID != 0) AND ( perros.LastModified > '{$this->timestamp}')" // changes
        );
        if (!$res) throw new Exception ("Updater::getUpdatedEntries(Perros): {$this->myDBObject->conn->error}");
        $result['Perros']=$res['rows'];

        // retrieve updated handlers from database
        // hay que recuperar:
        // los guias que hayan cambiado
        // los guias referenciados en la lista de perros
        $a=array();
        foreach ($res['rows'] as $perro) array_push($a,$perro['GuiasServerID']);
        $list=implode(",",$a);
        $qlist=($list==="")?"": " ( guias.ServerID IN ({$list}) ) OR "; // handler references in dog list
        $res=$this->myDBObject->__select(
            "guias.*,clubes.Nombre AS NombreClub,clubes.ServerID as ClubesServerID",
            "guias,clubes",
            "(guias.Club=clubes.ID) AND ( ". // table join
                    $qlist
                    ."( (guias.ServerID != 0) AND ( guias.LastModified > '{$this->timestamp}') )". // updated handlers
            ")"
        );
        if (!$res) throw new Exception ("Updater::getUpdatedEntries(Guias): {$this->myDBObject->conn->error}");
        $result['Guias']=$res['rows'];

        // retrieve updated Clubs from database
        // hay que recuperar:
        // los clubes que hayan cambiado
        // los clubes referenciados en la lista de guias
        $a=array();
        foreach ($res['rows'] as $guia) array_push($a,$guia['ClubesServerID']);
        $list=implode(",",$a);
        $qlist=($list==="")?"": "( clubes.ServerID IN ($list) ) OR "; // clubs references in handler list
        $res=$this->myDBObject->__select(
            "clubes.*",
            "clubes",
            "{$qlist} ( (clubes.ServerID != 0) AND ( LastModified > '{$this->timestamp}') )" // updated clubs
        );
        if (!$res) throw new Exception ("Updater::getUpdatedEntries(Clubes): {$this->myDBObject->conn->error}");
        $result['Clubes']=$res['rows'];

        // retrieve updated Judges from database
        $res=$this->myDBObject->__select(
            "jueces.*",
            "jueces",
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
     * @throws Exception on database operation failure
     */
    function checkForUpdatedEntries() {
        // retrieve updated elements from database
        $res=$this->myDBObject->__select(
            "count(*) AS NewEntries",
            "perroguiaclub",
            "(Licencia != '') AND ( LastModified > '{$this->timestamp}')"
        );
        if (!$res) throw new Exception ("Downloader::checkForUpdatedEntries(): {$this->myDBObject->errormsg}");
        return $res;
    }

    /**
     * Store retrieved data into temporary file to be parsed later
     * @param {array} $data
     * @throws Exception when no data received from client
     */
    function saveRetrievedData($data) {
        if ($data==="") throw new Exception ("Downloader::saveRetrievedData(): no data received from client");
        $obj=json_decode($data);

        // prepare store dir and timestamp format
        $dir=__DIR__."/../../../../logs/updateRequests";
        @mkdir($dir);
        $d=date("Ymd_Hi",strtotime($this->timestamp)); // convert "Y-m-d H:i:s" to "Ymd_Hi"
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

    /**
     * Retrieve black list file from configuration directory
     * This routine is necessary as .htaccess block any direct access to config directory
     * @return array
     * @throws Exception
     */
    function retrieveBlackList() {
        $blfile=__DIR__."/../../../../config/blacklist.info";
        if (!file_exists($blfile)) {
            $msg="Downloader: retrieveBlacList({$blfile}): file not found ";
            $this->myLogger->error($msg);
            throw new Exception ($msg);
        }
        // retrieve file contents, (base64 encoded) and return json message
        $data=file_get_contents($blfile);
        if (!$data) {
            $msg="Downloader: retrieveBlacList({$blfile}): file read error";
            $this->myLogger->error($msg);
            throw new Exception ($msg);
        }
        return array( "success"=>true, "data"=>$data );
    }
}