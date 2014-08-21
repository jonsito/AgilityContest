<?php
require_once("logging.php");
require_once("tools.php");
require_once("classes/Resultados.php");

try {
	$result=null;
	$operation=http_request("Operation","s",null);
	$mangaID=http_request("Manga","i",0);
	$idperro=http_request("Perro","i",0);
	if ($operation===null) throw new Exception("Call to resultadosFunction without 'Operation' requested");
	$resultados= new Resultados("resultadosFunctions",$mangaID);
	switch ($operation) {
		case "insert": $result=$resultados->insert($idperro); break;
		case "update": $result=$resultados->update($idperro); break;
		case "delete": $result=$resultados->delete($idperro); break;
		case "select": $result=$resultados->select($idperro); break;
		case "getResultados": $result=$resultados->getResultados(); break;
		default: throw new Exception("resultadosFunctions:: invalid operation: $operation provided");
	}
	if ($result===null) throw new Exception($resultados->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>