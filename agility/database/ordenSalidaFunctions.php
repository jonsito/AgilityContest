<?php
/** mandatory requires for database and logging */
require_once("tools.php");
require_once("logging.php");
require_once("classes/DBConnection.php");
require_once("classes/OrdenSalida.php");

$file="ordenSalidaFunctions";

try {
	$result=null;
	$os=new OrdenSalida($file);
	// retrieve variables
	$operation=http_request("Operation","s",null);
	if ($operation===null) 
		throw new Exception("Call to ordenSalidaFunctions without 'Operation' requested");
	$p = http_request("Prueba","i",0);
	$j = http_request("Jornada","i",0);
	$m = http_request("Manga","i",0);
	$d = http_request("Perro","i",0);
	// los siguiente campos se usan para drag and drop
	$f = http_request("From","i",0);
	$t = http_request("To","i",0);
	$w = http_request("Where","i",0);
	if (($p<=0) || ($j<=0) || ($m<=0)) 
		throw new Exception("Call to ordenSalidaFunctions with Invalid Prueba:$p Jornada:$j or manga:$m ID");
	switch ($operation) {
		case "random":	$result = $os->random($j,$m); break;
		case "reverse":	$result = $os->reverse($j,$m); break;
		case "handle":	$result = $os->handle($j,$m,$d); break;
		case "getData":	$result = $os->getData($p,$j,$m); break;
		case "dnd":	$result = $os->dragAndDrop($j,$m,$f,$t,$w); break;
	}
	// result may contain null (error),  "" success, or (any) data
	if ($result===null) throw new Exception($os->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>