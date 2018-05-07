<?php
/*
guiaFunctions.php

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
require_once(__DIR__ . "/../../server/database/classes/Guias.php");
	
	try {
		$result=null;
		$guias= new Guias("guiaFunctions");
		$am= AuthManager::getInstance("guiaFunctions");
		$operation=http_request("Operation","s",null);
		$guiaid=http_request("ID","i",0);
		$clubid=http_request("Club","i",0);
        $federation=http_request("Federation","i",-1);
        $guias= new Guias("guiaFunctions",$federation);
		if ($operation===null) throw new Exception("Call to guiaFunctions without 'Operation' requested");
		switch ($operation) {
			case "insert": $am->access(PERMS_OPERATOR); $result=$guias->insert(); break;
			case "update": $am->access(PERMS_OPERATOR); $result=$guias->update($guiaid); break;
			case "delete": $am->access(PERMS_OPERATOR); $result=$guias->delete($guiaid); break;
			case "select": $result=$guias->select(); break; // select *
			case "orphan": $am->access(PERMS_OPERATOR); $result=$guias->orphan($guiaid); break; // unassign from club
			case "enumerate":   $result=$guias->enumerate(); break; // block select
			case "getbyclub":   $result=$guias->selectByClub($clubid); break;
			case "getbyid":     $result=$guias->selectByID($guiaid); break;
            case "categorias":	$result=$guias->categoriasGuia(); break;
			default: throw new Exception("guiaFunctions:: invalid operation: $operation provided");
		}
		if ($result===null) 
			throw new Exception($guias->errormsg);
		if ($result==="")
			echo json_encode(array('success'=>true,'insert_id'=>$guias->conn->insert_id,'affected_rows'=>$guias->conn->affected_rows));
		else echo json_encode($result);
	} catch (Exception $e) {
		do_log($e->getMessage());
		echo json_encode(array('errorMsg'=>$e->getMessage()));
	}

?>