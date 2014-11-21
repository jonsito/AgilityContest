<?php
/*
inscripcionFunctions.php

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


require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__."/classes/Inscripciones.php");
	
	try {
		$result=null;
		$inscripciones= new Inscripciones("inscripcionFunctions",http_request("Prueba","i",0));
		$operation=http_request("Operation","s",null);
		$perro=http_request("Perro","i",0);
		if ($operation===null) throw new Exception("Call to inscripcionFunctions without 'Operation' requested");
		switch ($operation) {
			case "insert": $result=$inscripciones->insert($perro); break; // nueva inscripcion
			case "update": $result=$inscripciones->update($perro); break; // editar inscripcion ya existente
			case "delete": $result=$inscripciones->delete($perro); break; // borrar inscripcion
			case "noinscritos": $result=$inscripciones->noinscritos(); break;
			case "inscritos": $result=$inscripciones->inscritos(); break;
			case "reorder": $result=$inscripciones->reorder(); break;
			default: throw new Exception("inscripcionFunctions:: invalid operation: $operation provided");
		}
		if ($result===null) 
			throw new Exception($inscripciones->errormsg);
		if ($result==="")
			echo json_encode(array('success'=>true,'insert_id'=>$inscripciones->insert_id,'affected_rows'=>$inscripciones->conn->affected_rows));
		else echo json_encode($result);
	} catch (Exception $e) {
		do_log($e->getMessage());
		echo json_encode(array('errorMsg'=>$e->getMessage()));
	}
?>