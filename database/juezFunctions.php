<?php

require_once("logging.php");
require_once("tools.php");
require_once("classes/Jueces.php");

try {
	$result=null;
	$jueces= new Jueces("juezFunctions");
	$operation=http_request("Operation","s",null);
	if ($operation===null) throw new Exception("Call to juezFunctions without 'Operation' requested");
	switch ($operation) {
		case "insert": $result=$jueces->insert(); break;
		case "update": $result=$jueces->update(); break;
		case "delete": $result=$jueces->delete(http_request("Nombre","s",0)); break;
		case "select": $result=$jueces->select(); break; // list with order, index, count and where
		case "enumerate": $result=$jueces->enumerate(); break; // list with where
		default: throw new Exception("juezFunctions:: invalid operation: $operation provided");
	}
	if ($result===null) throw new Exception($perros->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>