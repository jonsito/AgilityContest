<?php

/*
eventFunctions.php

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
require_once(__DIR__."/classes/Eventos.php");

try {
	$result=null;
	$operation=http_request("Operation","s",null);
	$data=array (
			// common data for senders and receivers
			'ID'		=>	http_request("ID","i",0),
			'Session'	=> 	http_request("Session","i",0),
			'TimeStamp'	=> 	http_request("TimeStamp","i",0), // last timestamp parsed
			// datos identificativos del evento que se envia
			'Type' 		=> 	http_request("Type","s",""),
			'Source'	=> 	http_request("Source","s",""),
			'Prueba' 	=> 	http_request("Prueba","i",0),
			'Jornada'	=>	http_request("Jornada","i",0),
			'Manga'		=>	http_request("Manga","i",0),
			'Tanda'		=>	http_request("Tanda","i",0),
			'Perro'		=>	http_request("Perro","i",0),
			'Dorsal'	=>	http_request("Dorsal","i",0),
			'Celo'		=>	http_request("Celo","i",0),
			// el valor por defecto "-1" indica que no se debe utilizar dicho campo
			'Faltas'	=>	http_request("Faltas","i",-1),
			'Tocados'	=>	http_request("Tocados","i",-1),
			'Rehuses'	=>	http_request("Rehuses","i",-1),
			'NoPresentado'	=>	http_request("NoPresentado","i",-1),
			'Eliminado'	=>	http_request("Eliminado","i",-1),
			'Tiempo'	=>	http_request("Tiempo","d",-1),
			'Value'		=>	http_request("Value","i",-1)
	);
	if ($operation===null) throw new Exception("Call to eventFunctions without 'Operation' requested");
	$eventmgr= new Eventos("eventFunctions",$data['Session']);
	switch ($operation) {
		case "getEvents": $result=$eventmgr->getEvents($data); break;
		case "putEvent": $result=$eventmgr->putEvent($data); break;
		case "listEvents": $result=$eventmgr->listEvents($data); break;
		case "connect": $result=$eventmgr->connect($data); break;
		default: throw new Exception("eventFunctions:: invalid operation: $operation provided");
	}
	if ($result===null) throw new Exception($eventmgr->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>