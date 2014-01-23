<?php
	require_once("logging.php");
	require_once("tools.php");
	require_once("classes/Guias.php");
	
	try {
		$result=null;
		$guias= new Clubes("guiaFunctions");
		$operation=http_request("Operation","s",null);
		if ($operation===null) throw new Exception("Call to guiaFunctions without 'Operation' requested");
		switch ($operation) {
			case "insert": $result=$guias->insert(); break;
			case "update": $result=$guias->update(); break;
			case "delete": $result=$guias->delete(http_request("Nombre","s",null)); break;
			case "orphan": $result=$guias->orphan(http_request("Nombre","s",0)); break; // unassing from club
			case "select": $result=$guias->select(); break; // select *
			case "enumerate": $result=$guias->enumerate(); break; // block select
			case "getbyclub": $result=$guias->selectByClub(http_request("Nombre","s",null)); break;
			case "getbyname": $result=$guias->selectByNombre(http_request("Nombre","s",null)); break;
			default: throw new Exception("guiaFunctions:: invalid operation: $operation provided");
		}
		if ($result===null) throw new Exception($guias->errormsg);
		if ($result==="") echo json_encode(array('success'=>true));
		else echo json_encode($result);
	} catch (Exception $e) {
		do_log($e->getMessage());
		echo json_encode(array('errorMsg'=>$e->getMessage()));
	}

?>