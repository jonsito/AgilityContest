<?php
/*
sessionFunctions.php

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

require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/classes/Sesiones.php");

try {
	$result=null;
	$operation=http_request("Operation","s",null);
    $id=http_request("ID","i",0);
    $sname=http_request("SessionName","s","");
    $stype=http_request("SessionType","s","");
	$data=array ();
	// parse only provided variables
	$data=testAndSet($data,"Nombre","s","-- Sin asignar --",false);
	$data=testAndSet($data,"Comentario","s","",false);
	$data["Prueba"]=http_request("Prueba","i",1); // cannot be null
	$data=testAndSet($data,"Jornada","i",0);
	$data=testAndSet($data,"Manga","i",0);
	$data=testAndSet($data,"Tanda","i",0);
	$data=testAndSet($data,"Operador","i",1);
	$data=testAndSet($data,"Background","s","",false);
	$data=testAndSet($data,"LiveStream","s","",false);
	$data=testAndSet($data,"LiveStream2","s","",false);
	$data=testAndSet($data,"LiveStream3","s","",false);
    $data["Hidden"]=http_request("Hidden","i",0);
	
	if ($operation===null) throw new Exception("Call to sessionFunctions without 'Operation' requested");
	$sesion= new Sesiones("sessionFunctions");
	$am= new AuthManager("sessionFunctions");
	switch ($operation) {
        case "select": $result=$sesion->select($data,false); break;
        case "selectring": $result=$sesion->select($data,true); break;
		case "insert": $am->access(PERMS_OPERATOR); $result=$sesion->insert($data); break;
		case "update": $am->access(PERMS_ASSISTANT); $result=$sesion->update($id,$data); break;
		case "delete": $am->access(PERMS_OPERATOR); $result=$sesion->delete($id); break;
		case "reset": $am->access(PERMS_OPERATOR); $result=$sesion->reset($id); break;
		case "enumerate": $result=$sesion->enumerate(); break; // no select (yet)
		case "getByNombre":	$result=$sesion->selectByNombre($data['Nombre']); break;
        case "getByID":	$result=$sesion->selectByID($id); break;
        case "getClients":$result=$sesion->getClients($stype); break;
        case "testAndSet":$result=$sesion->testAndSet($sname); break;
        case "playlist":$result=$sesion->playlist(); break;
		default: throw new Exception("sessionFunctions:: invalid operation: $operation provided");
	}
	if ($result===null) 
		throw new Exception($sesion->errormsg);
	if ($result==="") 
		echo json_encode(array('success'=>true,'insert_id'=>$sesion->conn->insert_id,'affected_rows'=>$sesion->conn->affected_rows));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>