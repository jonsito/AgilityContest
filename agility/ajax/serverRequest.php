<?php
/**
 * serverRequest.php
 * Created by PhpStorm.
 * User: jantonio
 * Date: 28/12/17
 * Time: 12:32

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

define("SYNCDIR",__DIR__."/../../logs/updateRequests");

require_once(__DIR__ . "/../server/logging.php");
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/AuthManager.php");
require_once(__DIR__ . "/../server/database/updater/Uploader.php");
require_once(__DIR__ . "/../server/database/updater/Downloader.php");

$ul=null;
try {
    $result=null;
    do_log("Post data is ".json_encode($_POST));
    $operation=http_request("Operation","s","");
    $suffix=http_request("Suffix","s","");
    $timestamp=http_request("timestamp","s",date('Y-m-d H:i:s'));
    // need to do a more elaborated way of hanlde this...
    $serial=http_request("Serial","s","");
    if (($serial==="") || (!is_numeric($serial))) throw new Exception("serverRequest.php: invalid serial number");
    switch($operation) {
        case "progress":
            if (!is_dir(SYNCDIR)) @mkdir(SYNCDIR);
            $progressfile=SYNCDIR."/dbsync_{$suffix}.log";
            $result= array( 'progress' => "Waiting for progress info..."); // default when file doesn't exist yet
            if (file_exists($progressfile)) {
                // retrieve last line of progress file
                $lines=file($progressfile,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $result=array( 'progress' => strval($lines[count($lines)-1]) );
            }
            break;
        case "checkForUpdates": // this is to be executed in client
            $am= AuthManager::getInstance("adminFunctions");
            if ($am->isDefaultLicense()) throw new Exception("updateRequest: Invalid License");
            $am->access(PERMS_ADMIN); // throw exception if not admin
            $ul=new Uploader("checkForUpdates",$suffix);
            // send / receive changes from server
            $res=$ul->doCheckForUpdates($serial);
            if (array_key_exists('errorMsg',$res)) {
                // if fail to contact server, do not abort, just set new entries to 0
                do_log($res['errorMsg']);
                // this is done to retain compatibility with 3.7.3 server structure
                $result=array("success"=>true,"NewEntries"=>0);
            } else {
                $result=array("success"=>true,"NewEntries"=>$res['rows'][0]['NewEntries']);
            }
            break;
        case "updateRequest": // this is to be executed on client app
            $am= AuthManager::getInstance("adminFunctions");
            if ($am->isDefaultLicense()) throw new Exception("updateRequest: Invalid License");
            $am->access(PERMS_ADMIN); // throw exception if not admin
            $ul=new Uploader("UserRequestedUpdateDB",$suffix);
            $res=$ul->doRequestForUpdates($serial);
            $result=""; // PENDING: handle received data from server
            break;
        case "updateResponse": // this is to be executed on server app
            // PENDING: check serial key and perms has no sense here. however some protection is required
            $data= http_request("Data","s","",false); // data is json encoded. do not "sqlfy"
            $dl=new Downloader($timestamp,$serial);
            $result=$dl->saveRetrievedData($data); // store new data from client to further revision
            $result=$dl->getUpdatedEntries(); // retrieve new data from server
            break;
        case "checkResponse": // this is to be executed on server app
            // PENDING: check serial key and perms has no sense here. however some protection is required
            $dl=new Downloader($timestamp,$serial);
            $result=$dl->checkForUpdatedEntries(); // return number of new available entries
            break;
        case "retrieveBlackList": // this is to be executed on master server
            // retrieve black list from server.
            // as config dir is restricted, cannot download by url. need an ajax call
            $dl=new Downloader($timestamp,$serial);
            $result=$dl->retrieveBlackList(); // read an return blacklist file
            break;
        default:
            throw new Exception("serverRequest.php: invalid operation '{$operation}' ");
    }
    // these two results should never happen, anyway for compatibility take care on them
    if ($result===null) throw new Exception("serverRequest: unspecified error");
    if ($result==="") echo json_encode(array('success'=>true));
    else echo json_encode($result); // json encode response and return it
} catch (Exception $e) {
    do_log($e->getMessage());
    if($ul!==null) $ul->reportProgress("Failed");
    echo json_encode(array('errorMsg'=>$e->getMessage()));
}