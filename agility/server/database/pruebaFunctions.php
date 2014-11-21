<?php
/*
pruebaFunctions.php

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
require_once(__DIR__."/classes/Pruebas.php");

try {
	$result=null;
	$pruebas= new Pruebas("pruebaFunctions");
	$operation=http_request("Operation","s",null);
	if ($operation===null) throw new Exception("Call to pruebaFunctions without 'Operation' requested");
	$pruebaID=http_request("ID","i",0);
	switch ($operation) {
		case "insert": $result=$pruebas->insert(); break;
		case "update": $result=$pruebas->update($pruebaID); break;
		case "delete": $result=$pruebas->delete($pruebaID); break;
		case "select": $result=$pruebas->select(); break;
		case "enumerate": $result=$pruebas->enumerate(); break;
		case "getbyid": $result=$pruebas->selectByID($pruebaID); break;
		default: throw new Exception("pruebaFunctions:: invalid operation: $operation provided");
	}
	if ($result===null) 
		throw new Exception($pruebas->errormsg);
	if ($result==="") 
		echo json_encode(array('success'=>true,'insert_id'=>$pruebas->conn->insert_id,'affected_rows'=>$pruebas->conn->affected_rows));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>