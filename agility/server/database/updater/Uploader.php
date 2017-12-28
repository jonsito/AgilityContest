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
require_once (__DIR__."/../classes/DBObject.php");
require_once (__DIR__."/../../auth/Config.php");

/**
 * Class Uploader
 *
 * Used to send to server any PerroGuiaClub database change since last update date
 */
class Uploader {

    protected $myDBObject;
    protected $myConfig;

    function __construct() {
        $this->myDBObject=new DBObject("Uploader");
        $this->myConfig=Config::getInstance();
    }

    /**
     * retrieve from perroguiaclub every item newer than timestamp
     * @param $timestamp
     */
    function getUpdatedEntries($timestamp) {
        // retrieve updated elements from database
        $res=$this->myDBObject->__select(
          "*",
          "PerroGuiaClub",
          "(Licencia != '') AND ( LastModified > '{$timestamp}')"
        );
        if (!$res) return null; // on error, return null
        $res['timestamp']=$timestamp; // add timestamp to response
        return $res;
    }

    /**
     * Send data to server as a json post request
     * and receive answer
     * @param $data
     */
    function sendJSONRequest($data) {
        // $url = "https://www.agilitycontest.es/agility/server/updater/updateRequest.php";
        $url = "https://localhost/agility/server/updater/updateRequest.php";

        // PENDING: add license info and some sec/auth issues
        $data['operation']="updateRequest"; // add 'operation' to request
        $content = json_encode($data);

        // prepare and execute json request
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

        // retrieve response and check status
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ( $status != 201 ) { // notice 201, not 200
            die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        }
        // close curl stream
        curl_close($curl);

        // and return retrieved data
        return json_decode($json_response, true);
    }

    /**
     * retrieve date of last update
     */
    function getTimeStamp () {
        $current_version=$this->myConfig->getEnv("version_date");
        $res=$this->myDBObject->__select(
            "*",
            "VersionHistory",
            "Version='{$current_version}'"
        );
        if (!$res) throw new Exception ("Updater::getTimeSTamp(): {$this->myDBObject->conn->error}");
    }

    /**
     * Update timestamp mark to notice date of last database update
     * We will use "updateversion" table to store time of last update
     */
    function updateTimeStamp() {
        // get version
        $current_version=$this->myConfig->getEnv("version_date");
        $timestamp=date('Y-m-d H:i:s');
        $sql="UPDATE VersionHistory SET Updated='{$timestamp}' WHERE  Version='{$current_version}'";

        $res=$this->myDBObject->query($sql);
        if (!$res) throw new Exception ("Updater::updateTimeSTamp(): {$this->myDBObject->conn->error}");
    }
}