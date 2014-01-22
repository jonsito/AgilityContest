<?php
/** mandatory requires for database and logging */
require_once("tools.php");
require_once("logging.php");
require_once("DBConnection.php");
require_once("OrdenSalida.php");

$file="ordenSalidaFunctions";

// connect database
$conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
if (!$conn) {
	$str='ordenSalidaFunctions() cannot contact database.';
	do_log($str);
	echo json_encode(array('errorMsg'=>$str));
	return;
}
if (! isset($_REQUEST['Operacion'])) {
	$str='Call ordenSalidaFunctions() with no operation declared.';
	do_log($str);
	echo json_encode(array('errorMsg'=>$str));
	DBConnection::closeConnection($conn);
	return;
}

// retrieve variables
$j = (isset($_REQUEST['Jornada']))?intval($_REQUEST['Jornada']):-1;
$m = (isset($_REQUEST['Manga']))?intval($_REQUEST['Manga']):-1;
$d = (isset($_REQUEST['Dorsal']))?intval($_REQUEST['Dorsal']):-1;
$d2 = (isset($_REQUEST['Dorsal2']))?intval($_REQUEST['Dorsal2']):-1;
if (($j<0)||($m<0)) {
	$str="ordenSalidaFunctions() invalid jornada:$j or manga:$m ID.";
	do_log($str);
	echo json_encode(array('errorMsg'=>$str));
	return;
}

$os=new OrdenSalida($conn,"ordenSalidaFunctions");
$oper=strval($_REQUEST['Operacion']);
$result=null;
if      ($oper==='random')	$result = $os->random($j,$m,true);
else if ($oper==='remove')	$result = $os->remove($j,$m,$d);
else if ($oper==='insert')	$result = $os->insert($j,$m,$d);
else if ($oper==='swap')	$result = $os->swap($j,$m,$d,$d2);
else if ($oper==='getData')	$result = $os->getData($j,$m);
else {
	// arriving here means invalid operation
	$result="ordenSalidaFunctions() Invalid operation requested: $oper";
	do_log($result);
	echo json_encode(array('errorMsg'=>$result));
}
DBConnection::closeConnection($conn);
?>