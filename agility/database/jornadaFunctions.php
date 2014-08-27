<?php
	require_once("logging.php");
	require_once("tools.php");
	require_once("classes/Jornadas.php");
	
	/***************** programa principal **************/

	try {
		$result=null;
		$jornadas= new Jornadas("jornadaFunctions",http_request("Prueba","i",0));
		$operation=http_request("Operation","s",null);
		$jornadaid=http_request("ID","i",0);
		$allowClosed=http_request("AllowClosed","i",0);
		if ($operation===null) throw new Exception("Call to jornadaFunctions without 'Operation' requested");
		switch ($operation) {
			// there is no need of "insert" method: every prueba has 8 "hard-linked" jornadas
			case "delete": $result=$jornadas->delete($jornadaid); break;
			case "close": $result=$jornadas->close($jornadaid); break;
			case "update": $result=$jornadas->update($jornadaid); break;
			case "select": $result=$jornadas->selectByPrueba(); break;
			case "enumerate": $result=$jornadas->searchByPrueba($allowClosed); break;
			case "rounds": $result=$jornadas->roundsByJornada($jornadaid); break;
			default: throw new Exception("jornadaFunctions:: invalid operation: $operation provided");
		}
		if ($result===null) throw new Exception($jornadas->errormsg);
		if ($result==="") echo json_encode(array('success'=>true));
		else echo json_encode($result);
	} catch (Exception $e) {
		do_log($e->getMessage());
		echo json_encode(array('errorMsg'=>$e->getMessage()));
	}
?>