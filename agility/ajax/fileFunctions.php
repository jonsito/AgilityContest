<?php

/*
webhostingFunctions.php

Copyright  2013-2020 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__ . "/../server/File_Loader.php");

$response="";
try {
    $operation = http_request("Operation","s","");
	$data=array(
        'data' 	=> http_request("Data","s",""),
        'file' 	=> http_request("File","s",""),
		'type' 	=> http_request("Type","s",""),
		'suffix'=> http_request("Suffix","s",""),
        'chunk' => http_request("Chunk","i",0)
	);
    $uld=new File_Loader($data);
	switch ($operation) {
		case "progress":
			if (!is_dir(DOWNLOAD_DIR)) @mkdir(DOWNLOAD_DIR);
			$progressfile=DOWNLOAD_DIR."/docsync_{$data['suffix']}.log";
			$result= array( 'progress' => "Waiting for progress info..."); // default when file doesn't exist yet
			if (file_exists($progressfile)) {
				// retrieve last line of progress file
				$lines=file($progressfile,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
				$result=array( 'progress' => strval($lines[count($lines)-1]) );
			}
			break;
		// get file from web browser in chunks of data and store into cache
		case "upload":
			$am= AuthManager::getInstance("adminFunctions");
			$result=null;
			$am->access(PERMS_ADMIN); // throw exception if not allowed
			$result=$uld->fileUpload();
			break;
		// check for file in cache, else download, store and transfer to browser
		case "download":
			$result=$uld->fileDownload();
			return; // do not send any extra response, just requested file
		// check for documentation file(s) in cache, else download and store
		case "documentation":
			$result=$uld->downloadDocumentation();
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
