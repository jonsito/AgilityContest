<?php
/**
 * updateRequest.php
 * Created by PhpStorm.
 * User: jantonio
 * Date: 28/12/17
 * Time: 12:32

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

require_once(__DIR__."/../../logging.php");
require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/Uploader.php");
require_once(__DIR__."/Downloader.php");

try {
    $result=null;
    $operation=http_request("Operation","s","");
    $timestamp=http_request("timestamp","s",date('Y-m-d H:i:s'));
    // need to do a more elaborated way of hanlde this...
    $serial=http_request("Serial","s","");
    if ($serial==="") throw new Exception("updateRequest.php: invalid serial number");

    switch($operation) {
        case "updateRequest": // this is to be executed on client app
            $ul=new Uploader("UserRequestedUpdateDB");
            // send / receive changes from server
            $res=$ul->doRequestForUpdates($serial);
            // PENDING: import changes from server into local database
            $result="";
            break;
        case "updateResponse": // this is to be executed on server app
            $data= http_request("Data","s","",false); // data is json encoded. do not "sqlfy"
            $dl=new Downloader($timestamp,$serial);
            $result=$dl->saveRetrievedData($data); // store new data from client to further revision
            $result=$dl->getUpdatedEntries(); // retrieve new data from server
            break;
        case "checkResponse": // this is to be executed on server app
            $dl=new Downloader($timestamp,$serial);
            $result=$dl->checkForUpdatedEntries(); // return number of new available entries
            break;
        default:
            throw new Exception("updateRequest.php: invalid operation '{$operation}' ");
    }
    // these two results should never happen, anyway for compatibility take care on them
    if ($result===null) throw new Exception("updateRequest: unspecified error");
    if ($result==="") echo json_encode(array('success'=>true));
    else echo json_encode($result); // json encode response and return it
} catch (Exception $e) {
    do_log($e->getMessage());
    echo json_encode(array('errorMsg'=>$e->getMessage()));
}