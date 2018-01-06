<?php
/**
 * Uploader.php
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

require_once (__DIR__."/../../logging.php");
require_once (__DIR__."/../../auth/Config.php");
require_once (__DIR__."/../classes/DBObject.php");
require_once (__DIR__."/Updater.php");

/**
 * Class Uploader
 *
 * Used to send to server any PerroGuiaClub database change since last update date
 */
class Uploader {

    protected $myDBObject;
    protected $myConfig;
    protected $myLogger;
    protected $progressFile;

    function __construct($name="DatabaseUploader",$suffix="") {
        $this->myDBObject=new DBObject($name);
        $this->myConfig=Config::getInstance();
        $this->myLogger=new Logger($name,$this->myConfig->getEnv("debug_level"));
        if (!defined("SYNCDIR") ) define("SYNCDIR",__DIR__."/../../../../logs/updateRequests");
        if (!is_dir(SYNCDIR)) @mkdir(SYNCDIR);
        $this->progressFile=SYNCDIR."/dbsync_{$suffix}.log";
    }

    protected function reportProgress($str) {
        $f=fopen($this->progressFile,"a"); // open for append-only
        if (!$f) { $this->myLogger->error("fopen() cannot open file: ".$this->progressFile); return;}
        fwrite($f,"$str\n");
        fclose($f);
        // sleep(3); /* unset to debug */
    }

    /**
     * retrieve from perroguiaclub every item newer than timestamp
     * @param $timestamp
     * @throws Exception
     * @return {array} requested data
     */
    function getUpdatedEntries($timestamp) {
        $result=array();
        // retrieve updated dogs from database
        $res=$this->myDBObject->__select(
          "Perros.*,Guias.ServerID as GuiasServerID",
          "Perros,Guias",
          "(Perros.Guia=Guias.ID) AND (Licencia != '') AND ( Perros.LastModified > '{$timestamp}')"
        );
        if (!$res) throw new Exception ("Updater::getUpdatedEntries(Perros): {$this->myDBObject->conn->error}");
        $result['Perros']=$res['rows'];

        // retrieve updated handlers from database
        $res=$this->myDBObject->__select(
            "Guias.*,Clubes.ServerID as ClubesServerID",
            "Guias,Clubes",
            "(Guias.Club=Clubes.ID) AND ( Guias.LastModified > '{$timestamp}')"
        );
        if (!$res) throw new Exception ("Updater::getUpdatedEntries(Guias): {$this->myDBObject->conn->error}");
        $result['Guias']=$res['rows'];

        // retrieve updated Clubs from database
        $res=$this->myDBObject->__select(
            "Clubes.*",
            "Clubes",
            "( LastModified > '{$timestamp}')"
        );
        if (!$res) throw new Exception ("Updater::getUpdatedEntries(Clubes): {$this->myDBObject->conn->error}");
        $result['Clubes']=$res['rows'];

        // retrieve updated Judges from database
        $res=$this->myDBObject->__select(
            "Jueces.*",
            "Jueces",
            "( LastModified > '{$timestamp}')"
        );
        if (!$res) throw new Exception ("Updater::getUpdatedEntries(Jueces): {$this->myDBObject->conn->error}");
        $result['Jueces']=$res['rows'];
        $result['total']=
            max(count($result['Perros']),count($result['Guias']),count($result['Clubes']),count($result['Jueces']));
        // add timestamp and "Operation" to request data
        $result['timestamp']=$timestamp;
        $result['Operation']="updateResponse";
        return $result;
    }

    /**
     * Send data to server as a json post request
     * and receive answer
     * @param {array} $data
     * @param {string} $serial
     * @throws Exception
     * @return {mixed} server response
     */
    function sendJSONRequest($data,$serial) {
        $server=$this->myConfig->getEnv("master_server");
        $checkcert= ($server==="localhost")?false:true; // do not verify cert on localhost
        $args=array(
            "Operation" => $data['Operation'],
            "Serial" => $serial,
            "timestamp" => $data['timestamp']
        );
        $url = "http://{$server}/agility/server/database/updater/updateRequest.php?". http_build_query($args);
        // PENDING: add license info and some sec/auth issues
        $postdata=array(
            'Data' => json_encode($data)
        );

        // prepare and execute json request
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $checkcert); // set to false when using "localhost" url

        // retrieve response and check status
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ( $status != 200 ) { // notice 201, not 200
            throw new Exception("updater::SendJSONRequest() call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        }
        // close curl stream
        curl_close($curl);

        // and return retrieved data
        return json_decode($json_response, true);
    }

    /**
     * retrieve date of last update
     * @throws Exception
     * @return {string} timestamp of last update
     */
    function getTimeStamp () {
        $current_version=$this->myConfig->getEnv("version_date");
        $res=$this->myDBObject->__select(
            "*",
            "VersionHistory",
            "Version='{$current_version}'"
        );
        if (!$res) throw new Exception ("Updater::getTimeSTamp(): {$this->myDBObject->conn->error}");
        return $res['rows'][0]['Updated'];
    }

    /**
     * Update timestamp mark to notice date of last database update
     * We will use "updateversion" table to store time of last update
     * @throws Exception
     */
    function updateTimeStamp() {
        // get version
        $current_version=$this->myConfig->getEnv("version_date");
        $timestamp=date('Y-m-d H:i:s');
        $sql="UPDATE VersionHistory SET Updated='{$timestamp}' WHERE  Version='{$current_version}'";
        $res=$this->myDBObject->query($sql);
        if (!$res) throw new Exception ("Updater::updateTimeSTamp(): {$this->myDBObject->conn->error}");
    }

    /**
     * Send new data to server and receive updates when available
     * @param {string} $serial license serial number
     * @return mixed
     * @throws Exception
     */
    function doRequestForUpdates($serial) {
        $this->reportProgress(_("Get timestamp of last update"));
        // notice that on fail Exception will be thrown in inner routines
        $ts=$this->getTimeStamp();
        $this->reportProgress(_("Reading local database changes"));
        $data=$this->getUpdatedEntries($ts);
        // $this->myLogger->trace("Data sent to send to server: ".json_encode($data));
        $this->reportProgress(_("Looking for remote database changes"));
        $res=$this->sendJSONRequest($data,$serial);
        // $this->myLogger->trace("Data received from server: ".json_encode($res));
        $upd=new Updater("Updater_$serial");
        // actualizamos jueces
        foreach($res['Jueces'] as $juez) {
            $this->reportProgress(_("Updating")." "._("Judge").": ".$juez['Nombre']);
            $upd->handleJuez($juez);
        }
        // actualizamos clubes
        foreach($res['Clubes'] as $club) {
            $this->reportProgress(_("Updating")." "._("Club").": ".$club['Nombre']);
            $upd->handleClub($club);
        }
        // actualizamos guias
        foreach($res['Guias'] as $guia) {
            $this->reportProgress(_("Updating")." "._("Handler").": ".$guia['Nombre']);
            $upd->handleGuia($guia);
        }
        // actualizamos perros
        foreach($res['Perros'] as $perro) {
            $this->reportProgress(_("Updating")." "._("Dog").": ".$perro['Nombre']);
            $upd->handlePerro($perro);
        }
        $this->reportProgress(_("Setting new update timestamp"));
        $this->updateTimeStamp();
        $this->reportProgress("Done");
        return $res;
    }

    /**
     * @param {string} $serial license serial number
     * @return mixed
     * @throws Exception
     */
    function doCheckForUpdates($serial) {
        $ts=$this->getTimeStamp();
        $data=array(
            "Operation" => 'checkResponse',
            "Serial"    => $serial,
            "timestamp" => $ts,
            "total"     =>  0,
            "rows"      => array()
        );
        $res=$this->sendJSONRequest($data,$serial);
        return $res;
    }
}