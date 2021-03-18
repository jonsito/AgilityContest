<?php
/*
tandasFunctions.php

Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


/** mandatory requires for database and logging */
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/logging.php");
require_once(__DIR__ . "/../../server/auth/AuthManager.php");
require_once(__DIR__ . "/../../server/database/classes/Tandas.php");

$file="tandasFunctions";

try {
	$result=null;
	$am= AuthManager::getInstance($file);
	// retrieve variables
	$operation=http_request("Operation","s",null);
	if ($operation===null) 
		throw new Exception("Call to tandasFunctions without 'Operation' requested");
	// request prueba and jornada. invoke constructor
	$p = http_request("Prueba","i",0);
	$j = http_request("Jornada","i",0);
	$ot = new Tandas($file,$p,$j);
	
	// datos para listados, altas y bajas
	$id = http_request("ID","i",0); // tanda ID
	$d = http_request("Dorsal","i",0);
	$s = http_request("Sesion","i",0); // default is no session
	$a = http_request("Pendientes","i",0);
	
	// los siguiente campos se usan para drag and drop
	$f = http_request("From","i",0);
    $l = http_request("List","s","");
	$t = http_request("To","i",0);
	$w = http_request("Where","s","top"); // top:false  botton:true
	if ( ($p<=0) || ($j<=0) ) 
		throw new Exception("Call to tandasFunctions with Invalid Prueba:$p or Jornada:$j ID");
	switch ($operation) {
		case "insert":	$am->access(PERMS_OPERATOR); $result = $ot->insert($ot->getHttpData()); break;
		case "update":	$am->access(PERMS_ASSISTANT); $result = $ot->update($id,$ot->getHttpData()); break;
		case "delete":	$am->access(PERMS_OPERATOR); $result = $ot->delete($id); break;
		/* DO NOT CALL These functions from client side
		case "populateJornada":
		case "deleteJornada":
		*/
		case "getTandas":$result = $ot->getTandas($s); break;
		case "getData":	$result = $ot->getData($s,$id,$p); break;
		case "getDataByTanda": $result = $ot->getDataByTanda($s,$id); break;
		case "getDataByDorsal": $result = $ot->getDataByDorsal($s,$id,$d); break;
		case "swap": $result = $ot->swap($f,$t); break;
        case "dnd":	$am->access(PERMS_ASSISTANT); $result = $ot->dragAndDrop($f,$t,($w=="bottom")?true:false); break;
        case "dndList":	$am->access(PERMS_ASSISTANT); $result = $ot->dragAndDropList($l,$t,($w=="bottom")?true:false); break;
	}
	// result may contain null (error),  "" success, or (any) data
	if ($result===null) 
		throw new Exception($ot->errormsg);
	if ($result==="") 
		echo json_encode(array('success'=>true,'insert_id'=>0,'affected_rows'=>0));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>