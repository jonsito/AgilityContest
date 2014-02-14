<?php
require_once("logging.php");
require_once("tools.php");
require_once("classes/Clasificaciones.php");

try {
	$result=null;
	$clasificaciones= new Clasificaciones("clasificacionesFunctions");
	$operation=http_request("Operation","s",null);
	if ($operation===null) throw new Exception("Call to clasificacionesFunctions without 'Operation' requested");
	$manga1=http_request("Manga","i",0);
	$manga2=http_request("Manga2","i",0);
	switch ($operation) {
		case "parcial": $result=$clasificaciones->parcial($manga1); break;
		case "final": $result=$clasificaciones->final($manga1,$manga2); break;
		default: throw new Exception("clasificacionesFunctions:: invalid operation: $operation provided");
	}
	if ($result===null) throw new Exception($clasificaciones->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>