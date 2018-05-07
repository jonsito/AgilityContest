<?php
/*
clubFunctions.php

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


require_once(__DIR__ . "/../../server/logging.php");
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/AuthManager.php");
require_once(__DIR__ . "/../../server/i18n/Country.php");
require_once(__DIR__ . "/../../server/database/classes/Clubes.php");

	try {
		$result=null;
		$operation=http_request("Operation","s",null);
		$federation=http_request("Federation","i",-1); // force exception if undefined
		$idclub=http_request("ID","i",0);
		if ($operation===null)
			throw new Exception("Call to clubFunctions without 'Operation' requested");
		$clubes= new Clubes("clubFunctions",$federation);
		$am= AuthManager::getInstance("clubFunctions");
		switch ($operation) {
			case "insert": $am->access(PERMS_OPERATOR); $result=$clubes->insert(); break;
			case "update": $am->access(PERMS_OPERATOR); $result=$clubes->update($idclub); break;
			case "delete": $am->access(PERMS_OPERATOR); $result=$clubes->delete($idclub); break;
			case "select": $result=$clubes->select(); break;
			case "selectbyid": $result=$clubes->selectByID($idclub); break;
			case "enumerate": $result=$clubes->enumerate(); break;
            case "countries": $c=new Country(); $result=$c->enumerate(); break;
			case "getlogo": // not a json function; just return an image 
				$result=$clubes->getLogo($idclub);
				return;
			case "setlogo":
				$am->access(PERMS_OPERATOR);
				// this call provides an image in base64 encoded format. Needs special handling
				$result=$clubes->setLogo($idclub);
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