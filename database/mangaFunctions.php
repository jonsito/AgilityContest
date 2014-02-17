<?php

require_once("logging.php");
require_once("tools.php");
require_once("classes/Mangas.php");

try {
	$result=null;
	$jornada=http_request("Jornada","i",0);
	$mangas= new Mangas("mangaFunctions",$jornada);
	$operation=http_request("Operation","s",null);
	if ($operation===null) throw new Exception("Call to mangaFunctions without 'Operation' requested");
	switch ($operation) {
		// no direct "insert", as created/destroyed from jornadaFunctions
		case "update": $result=$mangas->update(http_request("Manga","i",0)); break;
		// no direct delete as created/destroyed from jornadaFunctions
		case "enumerate": $result=$mangas->selectByJornada(http_request("Jornada","i",0)); break; 
		case "getbyid":	$result=$mangas->selectByID(http_request("Manga","i",0)); break; 
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