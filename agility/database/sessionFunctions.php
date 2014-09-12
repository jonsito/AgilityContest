<?php

require_once("logging.php");
require_once("tools.php");
require_once("classes/Sesiones.php");

try {
	$result=null;
	$operation=http_request("Operation","s",null);
	$id=http_request("ID","i",0);
	$data=array (
			'Nombre' 	=> 	http_request("Nombre","s","-- Sin asignar --"),
			'Prueba' 	=> 	http_request("Prueba","i",0),
			'Jornada'	=>	http_request("Jornada","i",0),
			'Manga'		=>	http_request("Manga","i",0),
			'Tanda'		=>	http_request("Tanda","i",0),
			'Perro'		=>	http_request("Perro","i",0),
			'Resultado'	=>	http_request("Resultado","i",0),
			'Usuario'	=>	http_request("Usuario","s",'-- Anonimo --'),
	);
	if ($operation===null) throw new Exception("Call to sessionFunctions without 'Operation' requested");
	$sesion= new Sesiones("sessionFunctions");
	switch ($operation) {
		case "insert": $result=$sesion->insert($data); break;
		case "update": $result=$sesion->update($id,$data); break;
		case "delete": $result=$sesion->delete($id); break;
		case "enumerate": $result=$sesion->enumerate(); break; // no select (yet)
		case "getByNombre":	$result=$sesion->selectByNombre($data['Nombre']); break;
		case "getByID":	$result=$sesion->selectByID($id); break;
		default: throw new Exception("sessionFunctions:: invalid operation: $operation provided");
	}
	if ($result===null) throw new Exception($sesion->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>