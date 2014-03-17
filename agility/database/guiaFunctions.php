<?php
	require_once("logging.php");
	require_once("tools.php");
	require_once("classes/Guias.php");
	
	try {
		$result=null;
		$guias= new Guias("guiaFunctions");
		$operation=http_request("Operation","s",null);
		$guiaid=http_request("ID","i",0);
		$clubid=http_request("Club","i",0);
		if ($operation===null) throw new Exception("Call to guiaFunctions without 'Operation' requested");
		switch ($operation) {
			case "insert": $result=$guias->insert(); break;
			case "update": $result=$guias->update($guiaid); break;
			case "delete": $result=$guias->delete($guiaid); break;
			case "select": $result=$guias->select(); break; // select *
			case "orphan": $result=$guias->orphan($guiaid); break; // unassing from club
			case "enumerate": $result=$guias->enumerate(); break; // block select
			case "getbyclub": $result=$guias->selectByClub($clubid); break; 
			case "getbyid": $result=$guias->selectByID($guiaid); break;
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