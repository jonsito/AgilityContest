<?php
require_once("logging.php");
require_once("tools.php");
require_once("classes/Clasificaciones.php");

try {
	$result=null;
	$clasificaciones= new Clasificaciones("clasificacionesFunctions");
	$operation=http_request("Operation","s",null);
	if ($operation===null) throw new Exception("Call to clasificacionesFunctions without 'Operation' requested");
	$prueba=http_request("Prueba","i",0);
	$jornada=http_request("Jornada","i",0);
	$rondas=http_request("Rondas","i","0"); // bitfield of 512:Esp 256:KO 128:Eq4 64:Eq3 32:Opn 16:G3 8:G2 4:G1 2:Pre2 1:Pre1
	$manga1=http_request("Jornada","i",0);
	$manga2=http_request("Jornada","i",0);
	$mode=http_request("Modo","i","0"); // 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small
	$result=$c->clasificacionFinal($prueba,$jornada,$rondas,$manga1,$manga2,$mode);
	if ($result===null) throw new Exception($clasificaciones->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>