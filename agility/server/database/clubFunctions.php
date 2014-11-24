<?php
/*
clubFunctions.php

Copyright 2013-2014 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
	require_once(__DIR__."/classes/Clubes.php");

	try {
		$result=null;
		$clubes= new Clubes("clubFunctions");
		$operation=http_request("Operation","s",null);
		$idclub=http_request("ID","i",0);
		if ($operation===null) throw new Exception("Call to clubFunctions without 'Operation' requested");
		switch ($operation) {
			case "insert": $result=$clubes->insert(); break;
			case "update": $result=$clubes->update($idclub); break;
			case "delete": $result=$clubes->delete($idclub); break;
			case "select": $result=$clubes->select(); break;
			case "enumerate": $result=$clubes->enumerate(); break;
			case "getlogo": // not a json function; just return an image 
				$result=$clubes->getLogo($idclub);
				return;
			case "setlogo":
				// this call provides an image in base64 encoded format. Needs special handling
				$result=$clubes->setLogo($idclub);
				return;
			case "testlogo": // resize and resend received image. just for testing
				$result=$clubes->testLogo();
				return;
			default: throw new Exception("clubFunctions:: invalid operation: '$operation' provided");
		}
		if ($result===null) 
			throw new Exception($clubes->errormsg);
		if ($result==="") 
			echo json_encode(array('success'=>true,'insert_id'=>$clubes->conn->insert_id,'affected_rows'=>$clubes->conn->affected_rows));
		else echo json_encode($result);
	} catch (Exception $e) {
		do_log($e->getMessage());
		echo json_encode(array('errorMsg'=>$e->getMessage()));
	}
?>