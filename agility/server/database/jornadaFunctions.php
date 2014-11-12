<?php
/*
jornadaFunctions.php

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
require_once(__DIR__."/classes/Jornadas.php");
	
	/***************** programa principal **************/

	try {
		$result=null;
		$jornadas= new Jornadas("jornadaFunctions",http_request("Prueba","i",0));
		$operation=http_request("Operation","s",null);
		$jornadaid=http_request("ID","i",0);
		$allowClosed=http_request("AllowClosed","i",0);
		if ($operation===null) throw new Exception("Call to jornadaFunctions without 'Operation' requested");
		switch ($operation) {
			// there is no need of "insert" method: every prueba has 8 "hard-linked" jornadas
			case "delete": $result=$jornadas->delete($jornadaid); break;
			case "close": $result=$jornadas->close($jornadaid); break;
			case "update": $result=$jornadas->update($jornadaid); break;
			case "select": $result=$jornadas->selectByPrueba(); break;
			case "enumerate": $result=$jornadas->searchByPrueba($allowClosed); break;
			case "rounds": $result=$jornadas->roundsByJornada($jornadaid); break;
			default: throw new Exception("jornadaFunctions:: invalid operation: $operation provided");
		}
		if ($result===null) throw new Exception($jornadas->errormsg);
		if ($result==="") echo json_encode(array('success'=>true));
		else echo json_encode($result);
	} catch (Exception $e) {
		do_log($e->getMessage());
		echo json_encode(array('errorMsg'=>$e->getMessage()));
	}
?>