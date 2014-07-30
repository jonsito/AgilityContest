<?php
	require_once("tools.php");
	require_once("logging.php");
	require_once("classes/Inscripciones.php");
	
	try {
		$result=null;
		$inscripciones= new Inscripciones("inscripcionFunctions",http_request("Prueba","i",0));
		$operation=http_request("Operation","s",null);
		$perro=http_request("IDPerro","i",0);
		if ($operation===null) throw new Exception("Call to inscripcionFunctions without 'Operation' requested");
		switch ($operation) {
			case "insert": $result=$inscripciones->insert($perro); break; // nueva inscripcion
			case "update": $result=$inscripciones->update($perro); break; // editar inscripcion ya existente
			case "delete": $result=$inscripciones->delete($perro); break; // borrar inscripcion
			case "noinscritos": $result=$inscripciones->noinscritos(); break;
			case "inscritos": $result=$inscripciones->inscritos(); break;
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