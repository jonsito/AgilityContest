<?php
require_once("logging.php");
require_once("tools.php");
require_once("classes/Resultados.php");

try {
	$result=null;
	$operation=http_request("Operation","s",null);
	$mangaID=http_request("ID","i",0);
	$dorsal=http_request("Dorsal","i",0);
	if ($operation===null) throw new Exception("Call to resultadosFunction without 'Operation' requested");
	$resultados= new Resultados("resultadosFunctions",$mangaID);
	switch ($operation) {
		case "insert": $result=$pruebas->insert($dorsal); break;
		case "update": $result=$pruebas->update($dorsal); break;
		case "delete": $result=$pruebas->delete($dorsal); break;
		case "select": $result=$pruebas->select($dorsal); break;
		case "enumerate": $result=$pruebas->enumerate(); break;
		case "resultados": $result=$pruebas->selectByID(http_request("Categorias","s","")); break;
		default: throw new Exception("resultadosFunctions:: invalid operation: $operation provided");
	}
	if ($result===null) throw new Exception($pruebas->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>