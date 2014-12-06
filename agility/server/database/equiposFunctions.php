<?php

/*
equiposFunctions.php

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

require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/classes/Equipos.php");
	
	try {
		$result=null;
		$equipos= new Equipos("equiposFunctions",http_request("Prueba","i",0));
		$am= new AuthManager("equiposFunctions");
		$operation=http_request("Operation","s",null);
		$equipo=http_request("ID","i",0); // used on update/delete
		if ($operation===null) throw new Exception("Call to inscripcionFunctions without 'Operation' requested");
		switch ($operation) {
			case "insert": $am->access("PERMS_OPERATOR"); $result=$equipos->insert(); break; // nuevo equipo
			case "update": $am->access("PERMS_OPERATOR"); $result=$equipos->update($equipo); break; // editar equipo
			case "delete": $am->access("PERMS_OPERATOR"); $result=$equipos->delete($equipo); break; // borrar equipo
			case "select": $result=$equipos->select(); break; // listado ordenado/bloques/busqueda
			case "enumerate": $result=$equipos->enumerate(); break; // listado solo busqueda
			case "selectbyid": $result=$equipos->enumerate(); break; // recupera entrada unica
			default: throw new Exception("equiposFunctions:: invalid operation: $operation provided");
		}
		if ($result===null) 
			throw new Exception($equipos->errormsg);
		if ($result==="")
			echo json_encode(array('success'=>true,'insert_id'=>$equipos->conn->insert_id,'affected_rows'=>$equipos->conn->affected_rows));
		else echo json_encode($result);
	} catch (Exception $e) {
		do_log($e->getMessage());
		echo json_encode(array('errorMsg'=>$e->getMessage()));
	}
?>