<?php
	require_once("tools.php");
	require_once("logging.php");
	require_once("classes/Equipos.php");
	
	try {
		$result=null;
		$equipos= new Equipos("equiposFunctions",http_request("IDPrueba","i",0));
		$operation=http_request("Operation","s",null);
		$equipo=http_request("ID","i",0); // used on update/delete
		if ($operation===null) throw new Exception("Call to inscripcionFunctions without 'Operation' requested");
		switch ($operation) {
			case "insert": $result=$equipos->insert(); break; // nuevo equipo
			case "update": $result=$equipos->update($equipo); break; // editar equipo
			case "delete": $result=$equipos->delete($equipo); break; // borrar equipo
			case "select": $result=$equipos->select(); break; // listado ordenado/bloques/busqueda
			case "enumerate": $result=$equipos->enumerate(); break; // listado solo busqueda
			case "selectbyid": $result=$equipos->enumerate(); break; // recupera entrada unica
			default: throw new Exception("equiposFunctions:: invalid operation: $operation provided");
		}
		if ($result===null) throw new Exception($equipos->errormsg);
		if ($result==="") echo json_encode(array('success'=>true));
		else echo json_encode($result);
	} catch (Exception $e) {
		do_log($e->getMessage());
		echo json_encode(array('errorMsg'=>$e->getMessage()));
	}
?>