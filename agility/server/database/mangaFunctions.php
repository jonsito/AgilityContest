<?php

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