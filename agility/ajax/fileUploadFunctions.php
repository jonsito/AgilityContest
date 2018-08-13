<?php

/*
webhostingFunctions.php

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
require_once(__DIR__ . "/../server/logging.php");
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
require_once(__DIR__ . "/../server/auth/AuthManager.php");
require_once(__DIR__ . "/../server/FileUploader.php");

$response="";
try {
	if (file_exists(__DIR__ . "/../../config/system.ini"))
		throw new Exception( "AgilityContest is already installed");
    $operation = http_request("Operation","s","");
	$data=array(
        'data' 	=> http_request("Data","s",""),
        'file' 	=> http_request("File","s",""),
        'type' 	=> http_request("Type","s",""),
        'chunk' => http_request("Chunk","i",0)
	);
    $am= AuthManager::getInstance("adminFunctions");
	$result=null;
    $am->access(PERMS_ADMIN); // throw exception if not allowed
    $uld=new File_Uploader($data);
	switch ($operation) {
		case "upload":
			$result=$uld->fileUpload();
			break;
        case "abort":
            $result=$uld->abortUpload();
            break;
		default:
			throw new Exception("fileUploadFunctions:: invalid operation: '$operation' provided");
	}
	// on return string, convert into a json result
	if (is_string($result)) {
		if ($result==="") $result= array('success'=>true,'chunk'=>$data['chunk']); // success
		else $result=array('errorMsg'=>$result); // non empty string means error message
	}
	// send back result to client
	echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}
?>
