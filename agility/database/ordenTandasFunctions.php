<?php
/** mandatory requires for database and logging */
require_once("tools.php");
require_once("logging.php");
require_once("classes/DBConnection.php");
require_once("classes/OrdenTandas.php");

$file="ordenTandasFunctions";

try {
	$result=null;
	$os=new OrdenTandas($file);
	// retrieve variables
	$operation=http_request("Operation","s",null);
	if ($operation===null) 
		throw new Exception("Call to ordenTandasFunctions without 'Operation' requested");
	$p = http_request("Prueba","i",0);
	$j = http_request("Jornada","i",0);
	// los siguiente campos se usan para drag and drop
	$f = http_request("From","i",0);
	$t = http_request("To","i",0);
	$w = http_request("Where","i",0); // 0:up 1:down
	$a = http_request("Pendientes",i,0); // 0: listado completo; else retorna hasta "n" perros pendientes
	if ( ($p<=0) || ($j<=0) ) 
		throw new Exception("Call to ordenTandasFunctions with Invalid Prueba:$p or Jornada:$j ID");
	switch ($operation) {
		case "getTandas":$result = $os->getTandas($p,$j); break;
		case "getData":	$result = $os->getData($p,$j,$a); break;
		case "dnd":	$result = $os->dragAndDrop($p,$j,$f,$t,$w); break;
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