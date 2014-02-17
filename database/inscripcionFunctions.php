<?php
	require_once("tools.php");
	require_once("logging.php");
	require_once("classes/Inscripciones.php");
	
	try {
		$result=null;
		$inscripciones= new Inscripciones("inscripcionFunctions",http_request("ID","i",0));
		$operation=http_request("Operation","s",null);
		if ($operation===null) throw new Exception("Call to inscripcionFunctions without 'Operation' requested");
		switch ($operation) {
			case "doit": $result=$inscripciones->doit(); break;
			case "remove": $result=$inscripciones->remove(); break;
			case "select": $result=$inscripciones->select(); break;
			default: throw new Exception("inscripcionFunctions:: invalid operation: $operation provided");
		}
		if ($result===null) throw new Exception($inscripciones->errormsg);
		if ($result==="") echo json_encode(array('success'=>true));
		else echo json_encode($result);
	} catch (Exception $e) {
		do_log($e->getMessage());
		echo json_encode(array('errorMsg'=>$e->getMessage()));
	}
?>