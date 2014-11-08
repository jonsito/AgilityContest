<?php

/*
mangaFunctions.php

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


require_once("logging.php");
require_once("tools.php");
require_once("classes/Mangas.php");

try {
	$result=null;
	$prueba=http_request("Prueba","i",0);
	$jornada=http_request("Jornada","i",0);
	$operation=http_request("Operation","s",null);
	$manga=http_request("Manga","i",0);
	if ($operation===null) throw new Exception("Call to mangaFunctions without 'Operation' requested");
	$mangas= new Mangas("mangaFunctions",$jornada);
	switch ($operation) {
		// no direct "insert", as created/destroyed from jornadaFunctions
		case "update": 		$result=$mangas->update($manga); break;
		// no direct delete as created/destroyed from jornadaFunctions
		case "enumerate": 	$result=$mangas->selectByJornada($jornada); break; 
		case "getbyid":		$result=$mangas->selectByID($manga); break;
		case "getTandas":	$result=$mangas->getTandasByJornada($jornada); break; 
		default: throw new Exception("mangaFunctions:: invalid operation: $operation provided");
	}
	if ($result===null) throw new Exception($mangas->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>