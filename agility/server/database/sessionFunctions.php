<?php
/*
sessionFunctions.php

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
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/classes/Sesiones.php");

try {
	$result=null;
	$operation=http_request("Operation","s",null);
	$id=http_request("ID","i",0);
	$data=array (
			'Nombre' 	=> 	http_request("Nombre","s","-- Sin asignar --"),
			'Comentario'=> 	http_request("Comentario","s",""),
			'Prueba' 	=> 	http_request("Prueba","i",0),
			'Jornada'	=>	http_request("Jornada","i",0),
			'Manga'		=>	http_request("Manga","i",0),
			'Tanda'		=>	http_request("Tanda","i",0),
			'Operador'	=>	http_request("Operador","i",0),
	);
	if ($operation===null) throw new Exception("Call to sessionFunctions without 'Operation' requested");
	$sesion= new Sesiones("sessionFunctions");
	$am= new AuthManager("sessionFunctions");
	switch ($operation) {
		case "insert": $am->access(PERMS_OPERATOR); $result=$sesion->insert($data); break;
		case "update": $am->access(PERMS_OPERATOR); $result=$sesion->update($id,$data); break;
		case "delete": $am->access(PERMS_OPERATOR); $result=$sesion->delete($id); break;
		case "enumerate": $result=$sesion->enumerate(); break; // no select (yet)
		case "getByNombre":	$result=$sesion->selectByNombre($data['Nombre']); break;
		case "getByID":	$result=$sesion->selectByID($id); break;
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