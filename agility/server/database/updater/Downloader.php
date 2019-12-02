<?php

/**
 * Downloader.php
 * Created by PhpStorm.
 * User: jantonio
 * Date: 28/12/17
 * Time: 11:21

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

require_once (__DIR__."/../classes/DBObject.php");
require_once (__DIR__."/../../auth/Config.php");

// NOTICE: this macro only works on server as this scripts is intended to run on it
define("AC_BACKUP_FILE","/var/www/html/downloads/agility.sql");

/*
 * Esta clase se ejecuta siempre y solo en el master_server
 * Sirve para obtener/descargar ("Downloader") diversos elementos del servidor:
 * - Backup de la base de datos
 * - Blacklist de licencias
 * - Actualizaciones de la base de datos
 * - Licencias ( indicando el activation key )
 * 31-Jul-2019
 * Todas las funciones de AgilityContest_Master se han movido aqui. No tiene sentido duplicar ficheros
 */
class Downloader {
    protected $myDBObject;
    protected $myConfig;
    protected $myLogger;
    protected $timestamp;
    protected $serial;
    protected $revision;

    /**
     * Downloader constructor.
     * @param {string} $timestamp time stamp mark provided by requester in date("Ymd_Hi") format
     * @param {string} $serial license serial numbar
     * @param {string} $revision software revision number ( to disable upgrades in older versions )
     * @throws Exception on database connection error
     */
    function __construct($timestamp,$serial,$revision) {
        $this->myDBObject=new DBObject("Downloader");
        $this->myConfig=Config::getInstance();
        $this->myLogger=new Logger("DatabaseDownloader",$this->myConfig->getEnv("debug_level"));
        $this->timestamp=$timestamp;
        $this->serial=$serial; // store license serial number
        $this->revision=$revision; // store client running sw revision
    }

    /**
     * retrieve from perroguiaclub every item newer than timestamp
     * @throws Exception to generate response error in caller
     */
    function getUpdatedEntries() {
        $canUpgrade=strcmp($this->revision, "20180830_1200"); // 1 if newer version
        if ( $canUpgrade <=0) {
            $this->myLogger->notice("Client has older sw version: {$this->revision}. Do not update DB");
        }
        $result=array();

        // retrieve updated dogs from database
        $res=$this->myDBObject->__select(
            "perros.*,guias.Nombre AS NombreGuia,guias.ServerID AS GuiasServerID",
            "perros,guias",
            "(perros.Guia=guias.ID) AND ". // table join
                  "(Licencia != '') AND (perros.ServerID != 0) AND ( perros.LastModified > '{$this->timestamp}')" // changes
        );
        if (!$res) throw new Exception ("Updater::getUpdatedEntries(Perros): {$this->myDBObject->conn->error}");
        $result['Perros']=($canUpgrade>0)?$res['rows']:array();

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
        $result['Guias']=($canUpgrade>0)?$res['rows']:array();

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
        $result['Clubes']=($canUpgrade>0)?$res['rows']:array();

        // retrieve updated Judges from database
        $res=$this->myDBObject->__select(
            "jueces.*",
            "jueces",
            "(ServerID != 0) AND ( LastModified > '{$this->timestamp}')"
        );
        if (!$res) throw new Exception ("Updater::getUpdatedEntries(Jueces): {$this->myDBObject->conn->error}");
        $result['Jueces']=($canUpgrade>0)?$res['rows']:array();

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

        // when client is an old sw version, do not inform on upgrades

        $canUpgrade=strcmp($this->revision, "20180830_1200"); // 1 if newer version
        if ( $canUpgrade <=0) {
            $this->myLogger->notice("Client has older sw version: {$this->revision}. Do not update DB");
            return array( 'total' => 0,'rows' => array(array('NewEntries' =>0 )));
        }

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
        // code to generate license is -of course- outside github and is not covered by GPL
        // we just call it via shell_exec()
        $data=shell_exec("/usr/local/bin/getLicense.php"); // no parameters: just compile black list
        return array('success'=>true,'data'=>$data);
    }

    function retrieveLicense($email,$uniqueID,$activationKey,$serial) {
        // code to generate license is -of course- outside github and is not covered by GPL
        // we just call it via shell_exec()
        $data=shell_exec("/usr/local/bin/getLicense.php {$serial} {$email} {$uniqueID} {$activationKey}");
        return array('success'=>true,'data'=>$data);
    }

    public function retrieveBackup() {
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
}