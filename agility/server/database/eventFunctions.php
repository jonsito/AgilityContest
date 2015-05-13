<?php

/*
eventFunctions.php

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
require_once(__DIR__."/classes/Eventos.php");

try {
	$result=null;
	$am= new AuthManager("eventsFunctions");
	$operation=http_request("Operation","s",null);
	$data=array (
			// common data for senders and receivers
			'ID'		=>	http_request("ID","i",0),
			'Session'	=> 	http_request("Session","i",0),
			'TimeStamp'	=> 	http_request("TimeStamp","i",0), // last timestamp parsed
			'Type' 		=> 	http_request("Type","s",""),
			'Source'	=> 	http_request("Source","s",""),
			// datos identificativos del evento que se envia
			'Pru' 	=> 	http_request("Prueba","i",0),
			'Jor'	=>	http_request("Jornada","i",0),
			'Mng'	=>	http_request("Manga","i",0),
			'Tnd'	=>	http_request("Tanda","i",0),
			'Dog'	=>	http_request("Perro","i",0),
			'Drs'	=>	http_request("Dorsal","i",0),
            'Hot'	=>	http_request("Celo","i",0),
            'Eqp'	=>	http_request("Equipo","i",0),
			// el valor por defecto "-1" indica que no se debe utilizar dicho campo
			'Flt'	=>	http_request("Faltas","i",-1),
			'Toc'	=>	http_request("Tocados","i",-1),
			'Reh'	=>	http_request("Rehuses","i",-1),
			'NPr'	=>	http_request("NoPresentado","i",-1),
			'Eli'	=>	http_request("Eliminado","i",-1),
			'Tim'	=>	http_request("Tiempo","d",-1),
			'Value'	=>	http_request("Value","i",-1)
	);
	if ($operation===null) throw new Exception("Call to eventFunctions without 'Operation' requested");
	$eventmgr= new Eventos("eventFunctions",$data['Session']);
	switch ($operation) {
		case "getEvents": $result=$eventmgr->getEvents($data); break;
		case "putEvent": $am->access(PERMS_ASSISTANT); $result=$eventmgr->putEvent($data); break;
		case "chronoEvent": $am->access(PERMS_CHRONO); $result=$eventmgr->putEvent($data); break;
		case "listEvents": $result=$eventmgr->listEvents($data); break;
		case "connect": $result=$eventmgr->connect($data); break;
		default: throw new Exception("eventFunctions:: invalid operation: $operation provided");
	}
	if ($result===null) 
		throw new Exception($eventmgr->errormsg);
	if ($result==="")
		echo json_encode(array('success'=>true,'insert_id'=>$eventmgr->conn->insert_id,'affected_rows'=>$eventmgr->conn->affected_rows));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>