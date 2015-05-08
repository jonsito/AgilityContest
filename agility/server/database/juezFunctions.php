<?php

/*
juezFunctions.php

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/classes/Jueces.php");

try {
	$result=null;
	$jueces= new Jueces("juezFunctions");
	$am= new AuthManager("juezFunctions");
	$operation=http_request("Operation","s",null);
    $federation=http_request("Federation","i",-1); // -1 defaults to all federations
	$idjuez=http_request("ID","i",0);
	if ($operation===null) throw new Exception("Call to juezFunctions without 'Operation' requested");
	switch ($operation) {
		case "insert": $am->access(PERMS_OPERATOR); $result=$jueces->insert(); break;
		case "update": $am->access(PERMS_OPERATOR); $result=$jueces->update($idjuez); break;
		case "delete": $am->access(PERMS_OPERATOR); $result=$jueces->delete($idjuez); break;
		case "selectbyid": $result=$jueces->selectByID($idjuez); break;
		case "select": $result=$jueces->select(); break; // list with order, index, count and where
		case "enumerate": $result=$jueces->enumerate($federation); break; // list with where
		default: throw new Exception("juezFunctions:: invalid operation: '$operation' provided");
	}
	if ($result===null) 
		throw new Exception($jueces->errormsg);
	if ($result==="")
		echo json_encode(array('success'=>true,'insert_id'=>$jueces->conn->insert_id,'affected_rows'=>$jueces->conn->affected_rows)); 
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>