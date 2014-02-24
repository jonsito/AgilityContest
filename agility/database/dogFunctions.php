<?php

require_once("logging.php");
require_once("tools.php");
require_once("classes/Dogs.php");

try {
	$result=null;
	$perros= new Dogs("dogFunctions");
	$operation=http_request("Operation","s",null);
	if ($operation===null) throw new Exception("Call to dogFunctions without 'Operation' requested");
	switch ($operation) {
		case "insert": $result=$perros->insert(); break;
		case "update": $result=$perros->update(http_request("IDPerro","i",0)); break;
		case "delete": $result=$perros->delete(http_request("IDPerro","i",0)); break;
		case "orphan": $result=$perros->orphan(http_request("IDPerro","i",0)); break; // unassign from handler
		case "select": $result=$perros->select(); break; // list with order, index, count and where
		case "enumerate":	$result=$perros->enumerate(); break; // list with where
		case "getbyguia":	$result=$perros->selectByGuia(http_request("Guia","s",null)); break;
		case "getbyidperro":	$result=$perros->selectByIDPerro(http_request("IDPerro","i",0)); break;
		case "categorias":	$result=$perros->categoriasPerro(); break;
		case "grados":		$result=$perros->gradosPerro(); break;
		default: throw new Exception("dogFunctions:: invalid operation: $operation provided");
	}
	if ($result===null) throw new Exception($perros->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>