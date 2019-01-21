<?php
/**
 * Uploader.php
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

require_once (__DIR__."/../../logging.php");
require_once (__DIR__."/../../auth/Config.php");
require_once (__DIR__."/../classes/DBObject.php");
require_once (__DIR__."/Updater.php");

/**
 * Class Uploader
 *
 * Used to send to server any perroguiaclub database change since last update date
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

    public function reportProgress($str) {
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
        $data=array();
        // retrieve updated dogs from database
        $res=$this->myDBObject->__select(
          "perros.*,guias.Nombre as NombreGuia,guias.ServerID as GuiasServerID",
          "perros,guias",
          "(perros.Guia=guias.ID) AND (Licencia != '') AND ( perros.LastModified > '{$timestamp}')"
        );
        if (!$res) throw new Exception ("Updater::getUpdatedEntries(Perros): {$this->myDBObject->conn->error}");
        $data['Perros']=$res['rows'];

        // retrieve updated handlers from database
        $res=$this->myDBObject->__select(
            "guias.*,clubes.Nombre AS NombreClub, clubes.ServerID as ClubesServerID",
            "guias,clubes",
            "(guias.Club=clubes.ID) AND ( guias.LastModified > '{$timestamp}')"
        );
        if (!$res) throw new Exception ("Updater::getUpdatedEntries(Guias): {$this->myDBObject->conn->error}");
        $data['Guias']=$res['rows'];

        // retrieve updated Clubs from database
        $res=$this->myDBObject->__select(
            "clubes.*",
            "clubes",
            "( LastModified > '{$timestamp}')"
        );
        if (!$res) throw new Exception ("Updater::getUpdatedEntries(Clubes): {$this->myDBObject->conn->error}");
        $data['Clubes']=$res['rows'];

        // retrieve updated Judges from database
        $res=$this->myDBObject->__select(
            "jueces.*",
            "jueces",
            "( LastModified > '{$timestamp}')"
        );
        if (!$res) {
            throw new Exception ("Updater::getUpdatedEntries(Jueces): {$this->myDBObject->conn->error}");
        }
        $data['Jueces']=$res['rows'];
        $result['total']=
            max( count($data['Perros']), count($data['Guias']), count($data['Clubes']), count($data['Jueces']) );
        // add timestamp and "Operation" to request data
        $result['Data']=$data;
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
        $baseurl=$this->myConfig->getEnv("master_baseurl");
        $checkcert= ($server==="localhost")?false:true; // do not verify cert on localhost
        $url = "https://{$server}/{$baseurl}/ajax/serverRequest.php";
        // PENDING: add license info and some sec/auth issues
        $hdata=array(
            "Operation" => $data['Operation'],
            "Serial" => $serial,
            "timestamp" => $data['timestamp'],
            "Revision" => $this->myConfig->getEnv('version_date')
        );
        $pdata=array(
            'Data' => json_encode($data['Data'])
        );
        // prepare and execute json request
        $curl = curl_init($url."?".http_build_query($hdata) );
        //$curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // allow server redirection
        curl_setopt($curl, CURLOPT_POSTREDIR, 1); // do not change from post to get on "301 redirect"
        curl_setopt($curl, CURLOPT_POSTFIELDS, $pdata );
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $checkcert); // set to false when using "localhost" url
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT, 5); // wait 5 secs to attemp connect

        // retrieve response and check status
        // $this->myLogger->trace("Uploader::sendJSONRequest() sending ".json_encode($pdata));
        $json_response = @curl_exec($curl); // supress stdout warning
        if ( curl_error($curl) ) {
            throw new Exception("updater::SendJSONRequest() call to URL $url failed: " . curl_error($curl) );
        }
        // close curl stream
        curl_close($curl);
        // $this->myLogger->trace("Uploader::sendJSONRequest() returns {$json_response}");
        // and return retrieved data in object format
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
            "versionhistory",
            "Version='{$current_version}'"
        );
        if (!$res) {
            throw new Exception ("Updater::getTimeSTamp(): {$this->myDBObject->conn->error}");
        }
        if ($res['total']==0) {
            // no deberia ocurrir: significa que se ha cambiado a mano la base de datos
            // y por algun motivo upgradeVersion.php no ha actualizado el version history
            $this->myLogger->warn("updater::getTimeStamp(): VersionHistory not properly updated");
            $timestamp=date('Y-m-d H:i:s');
            $str="INSERT INTO versionhistory (Version,Updated) VALUES ('{$current_version}','{$timestamp}')";
            $res=$this->myDBObject->query($str);
            if (!$res) {
                throw new Exception("updater::getTimeStamp(): Cannot properly set VersionHistory");
            }
            return $timestamp; // no data should be returned as "updated just now"
        }
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
        $sql="UPDATE versionhistory SET Updated='{$timestamp}' WHERE  Version='{$current_version}'";
        $res=$this->myDBObject->query($sql);
        if (!$res) {
            throw new Exception ("Updater::updateTimeSTamp(): {$this->myDBObject->conn->error}");
        }
    }

    /**
     * Send new data to server and receive updates when available
     * @param {string} $serial license serial number
     * @return mixed
     * @throws Exception
     */
    function doRequestForUpdates($serial) {
        $timeout=ini_get('max_execution_time');
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
        set_time_limit($timeout);
        if (is_array($res)) {
            // notice that on error an array is returned, but not meaningfull data. Just log trace
            if (array_key_exists('errorMsg',$res)){
                $this->myLogger->error($res['errorMsg']);
                $this->reportProgress("Error");
                $this->reportProgress("Done.");
                return $res;
            }
            if (array_key_exists('Jueces',$res)){
                // update judges
                set_time_limit($timeout);
                foreach($res['Jueces'] as $juez) {
                    $this->reportProgress(_("Updating")." "._("Judge").": ".$juez['Nombre']);
                    $upd->handleJuez($juez);
                }
            }
            if (array_key_exists('Clubes',$res)){
                // actualizamos clubes
                set_time_limit($timeout);
                foreach($res['Clubes'] as $club) {
                    $this->reportProgress(_("Updating")." "._("Club").": ".$club['Nombre']);
                    $upd->handleClub($club);
                }
            }
            if (array_key_exists('Guias',$res)){
                // actualizamos guias
                set_time_limit($timeout);
                foreach($res['Guias'] as $guia) {
                    $this->reportProgress(_("Updating")." "._("Handler").": ".$guia['Nombre']);
                    $upd->handleGuia($guia);
                }
            }
            if (array_key_exists('Perros',$res)){
                // actualizamos perros
                set_time_limit($timeout);
                foreach($res['Perros'] as $perro) {
                    $this->reportProgress(_("Updating")." "._("Dog").": ".$perro['Nombre']);
                    $upd->handlePerro($perro);
                }
            }
            $this->reportProgress(_("Setting new update timestamp"));
            $this->updateTimeStamp();
        } else {
            $this->reportProgress(_("Could not receive updates from server. Abort"));
        }
        $this->reportProgress("Done.");
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
            "rows"      => array(),
            "Data"      => array()
        );
        $res=$this->sendJSONRequest($data,$serial);
        return $res;
    }
}