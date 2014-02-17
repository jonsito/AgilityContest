<?php
require_once("logging.php");
require_once("tools.php");
require_once("classes/Pruebas.php");

try {
	$result=null;
	$pruebas= new Pruebas("pruebaFunctions");
	$operation=http_request("Operation","s",null);
	if ($operation===null) throw new Exception("Call to pruebaFunctions without 'Operation' requested");
	$pruebaID=http_request("ID","i",0);
	switch ($operation) {
		case "insert": $result=$pruebas->insert(); break;
		case "update": $result=$pruebas->update(); break;
		case "delete": $result=$pruebas->delete($pruebaID); break;
		case "select": $result=$pruebas->select(); break;
		case "enumerate": $result=$pruebas->enumerate(); break;
		case "getbyid": $result=$pruebas->selectByID($pruebaID); break;
		case "equipos": $result=$pruebas->selectEquiposByPrueba($pruebaID); break;
		default: throw new Exception("pruebaFunctions:: invalid operation: $operation provided");
	}
	if ($result===null) throw new Exception($pruebas->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>