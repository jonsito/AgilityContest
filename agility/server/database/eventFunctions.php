<?php

require_once("logging.php");
require_once("tools.php");
require_once("classes/Eventos.php");

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
			// el valor por defecto "-1" indica que no se debe utilizar dicho campo
			'Perro'		=>	http_request("Perro","i",-1),
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