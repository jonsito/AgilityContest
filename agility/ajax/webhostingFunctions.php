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
require_once(__DIR__ . "/../server/database/classes/DBConnection.php");

$response="";
try {
	if (file_exists(__DIR__ . "/auth/system.ini"))
		throw new Exception( "AgilityContest is already installed");
    $operation = http_request("Operation","s","");
	$data=array(
        'server' 	=> http_request("Server","s",""),
        'dbname' 	=> http_request("Database","s",""),
        'dbuser' 	=> http_request("User","s",""),
        'dbpass' 	=> http_request("Password","s",""),
        'admin'		=> http_request("Admin","s",""),
        'operator'	=> http_request("Operator","s",""),
        'assistant'	=> http_request("Assistant","s",""),
        'license'	=> http_request("License","s",""),
	);
	$result=null;
	switch ($operation) {
		case "checkdbroot":
			$conn=DBConnection::getConnection($data['server'],$data['dbname'],$data['dbuser'],$data['dbpass']);
			if ($conn==null)throw new Exception("webhostingFunctions:: cannot contact database with provided data");
			$result="";
			break;
		case "install":
			$result="";
			break;
		default:
			throw new Exception("webhostingFunctions:: invalid operation: '$operation' provided");
	}
	if (is_string($result)) {
        if ($result==="ok") return; // don't generate any aditional response
		if ($result==="") $result= array('success'=>true); // success
		else $result=array('errorMsg'=>$result); // non empty string means error message
	}
	echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}
?>
