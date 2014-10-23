<?php

require_once("logging.php");
require_once("tools.php");
require_once("classes/Eventos.php");

try {
	$result=null;
	$operation=http_request("Operation","s",null);
	$id=http_request("ID","i",0);
	$data=array (	
			'Session'	=> 	http_request("Session","i",0),
			'Type' 		=> 	http_request("Session","s",""),
			'Source'	=> 	http_request("Source","s",""),
			'ID' 		=> 	http_request("ID","i",0), // last session ID
			'Prueba' 	=> 	http_request("Prueba","i",0),
			'Jornada'	=>	http_request("Jornada","i",0),
			'Manga'		=>	http_request("Manga","i",0),
			'Tanda'		=>	http_request("Tanda","i",0),
			'Perro'		=>	http_request("Perro","i",0),
			'Value'		=>  http_request("Value","i",0)
	);
	if ($operation===null) throw new Exception("Call to eventFunctions without 'Operation' requested");
	$eventmgr= new Eventos("eventFunctions");
	switch ($operation) {
		case "getEvents": $result=$eventmgr->get($data); break;
		case "putEvent": $result=$eventmgr->update($data); break;
		case "listEvents": $result=$eventmgr->delete($data); break;
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