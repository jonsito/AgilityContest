<?php

/*
dogFunctions.php

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
require_once(__DIR__."/classes/Dogs.php");

try {
	$result=null;
	$perros= new Dogs("dogFunctions");
	$operation=http_request("Operation","s",null);
	$idperro=http_request("ID","i",0);
	$idguia=http_request("Guia","i",0);
	if ($operation===null) throw new Exception("Call to dogFunctions without 'Operation' requested");
	switch ($operation) {
		case "insert": $result=$perros->insert(); break;
		case "update": $result=$perros->update($idperro); break;
		case "delete": $result=$perros->delete($idperro); break;
		case "orphan": $result=$perros->orphan($idperro); break; // unassign from handler
		case "select": $result=$perros->select(); break; // list with order, index, count and where
		case "enumerate":	$result=$perros->enumerate(); break; // list with where
		case "getbyguia":	$result=$perros->selectByGuia($idguia); break;
		case "getbyidperro":	$result=$perros->selectByID($idperro); break;
		case "categorias":	$result=$perros->categoriasPerro(); break;
		case "grados":		$result=$perros->gradosPerro(); break;
		default: throw new Exception("dogFunctions:: invalid operation: $operation provided");
	}
	if ($result===null) throw new Exception($perros->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>