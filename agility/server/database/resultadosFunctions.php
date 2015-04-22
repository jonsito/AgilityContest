<?php
/*
resultadosFunctions.php

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


require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/classes/Resultados.php");

try {
	$result=null;
	$operation=http_request("Operation","s",null);
	$pruebaID=http_request("Prueba","i",0);
	$JornadaID=http_request("Jornada","i",0);
	$mangaID=http_request("Manga","i",0);
	$idperro=http_request("Perro","i",0);
	$dorsal=http_request("Dorsal","i",0);
	$mode=http_request("Mode","i",0);
	if ($operation===null) throw new Exception("Call to resultadosFunction without 'Operation' requested");
	$resultados= new Resultados("resultadosFunctions",$pruebaID,$mangaID);
	$am= new AuthManager("resultadosFunctions");
	switch ($operation) {
		case "update": $am->access(PERMS_ASSISTANT); $result=$resultados->update($idperro); break;
		case "delete": $am->access(PERMS_OPERATOR); $result=$resultados->delete($idperro); break;
		case "select": $result=$resultados->select($idperro); break;
		case "getPendientes": $result=$resultados->getPendientes($mode); break;
		case "getResultados": $result=$resultados->getResultados($mode); break;
		case "enumerateResultados": $result=$resultados->enumerateResultados($JornadaID); break;
		case "getTRS": $result=$resultados->getTRS($mode); break;
		default: throw new Exception("resultadosFunctions:: invalid operation: $operation provided");
	}
	if ($result===null) 
		throw new Exception($resultados->errormsg);
	if ($result==="") 
		echo json_encode(array('success'=>true,'insert_id'=>$resultados->conn->insert_id,'affected_rows'=>$resultados->conn->affected_rows));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>