<?php

/*
dogFunctions.php

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


require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/classes/Dogs.php");

try {
	$result=null;
	$am= new AuthManager("dogFunctions");
	$operation=http_request("Operation","s",null);
	$idperro=http_request("ID","i",0);
	$idguia=http_request("Guia","i",0);
	$federation=http_request("Federation","i",-1);
    $idfrom=http_request("From","i",0);
    $idto=http_request("To","i",0);
    $perros= new Dogs("dogFunctions",$federation);
	if ($operation===null) throw new Exception("Call to dogFunctions without 'Operation' requested");
	switch ($operation) {
		case "insert": $am->access(PERMS_OPERATOR); $result=$perros->insert(); break;
		case "update": $am->access(PERMS_OPERATOR); $result=$perros->update($idperro); break;
		case "delete": $am->access(PERMS_OPERATOR); $result=$perros->delete($idperro); break;
        case "orphan": $am->access(PERMS_OPERATOR); $result=$perros->orphan($idperro); break; // unassign from handler
        case "join":   $am->access(PERMS_OPERATOR); $result=$perros->joinTo($idfrom,$idto); break; // join two dogs
		case "select": $result=$perros->select(); break; // list with order, index, count and where
        case "enumerate":	$result=$perros->enumerate(); break; // list with where
        case "duplicates":	$result=$perros->duplicates(); break; // with same license number
		case "getbyguia":	$result=$perros->selectByGuia($idguia); break;
		case "getbyidperro":	$result=$perros->selectByID($idperro); break;
		case "categorias":	$result=$perros->categoriasPerro(); break;
		case "grados":		$result=$perros->gradosPerro(); break;
		default: throw new Exception("dogFunctions:: invalid operation: $operation provided");
	}
	if ($result===null) 
		throw new Exception($perros->errormsg);
	if ($result==="") 
		echo json_encode(array('success'=>true,'insert_id'=>$perros->conn->insert_id,'affected_rows'=>$perros->conn->affected_rows));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>