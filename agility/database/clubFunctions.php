<?php
	require_once("logging.php");
	require_once("tools.php");
	require_once("classes/Clubes.php");

	try {
		$result=null;
		$clubes= new Clubes("clubFunctions");
		$operation=http_request("Operation","s",null);
		$nombre=http_request("Nombre","s",null);
		if ($operation===null) throw new Exception("Call to clubFunctions without 'Operation' requested");
		switch ($operation) {
			case "insert": $result=$clubes->insert(); break;
			case "update": $result=$clubes->update(); break;
			case "delete": $result=$clubes->delete($nombre); break;
			case "select": $result=$clubes->select(); break;
			case "enumerate": $result=$clubes->enumerate(); break;
			case "logo": $result=$clubes->getLogo($nombre);
				// not a json function; just return an image 
				return;
			default: throw new Exception("clubFunctions:: invalid operation: $operation provided");
		}
		if ($result===null) throw new Exception($clubes->errormsg);
		if ($result==="") echo json_encode(array('success'=>true));
		else echo json_encode($result);
	} catch (Exception $e) {
		do_log($e->getMessage());
		echo json_encode(array('errorMsg'=>$e->getMessage()));
	}
?>