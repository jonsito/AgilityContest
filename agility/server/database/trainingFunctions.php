<?php

/*
userFunctions.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/classes/Entrenamientos.php");

$response="";
try {
	$result=null;
	$am= new AuthManager("trainingFunctions");
	$operation=http_request("Operation","s",null);
    $id=http_request("ID","i",0);
    $size=http_request("Size","i",10);
    $prueba=http_request("Prueba","i",0);
    $data=array(
        'Club'  =>    http_request("Club","i",0),
        'Fecha' =>    http_request("Fecha","s",date('Y-m-d')),
        'Firma' =>    http_request("Fecha","s",date('H:i:s')),
        'Veterinario' => http_request("Fecha","s",date('H:i:s')),
        'Entrada'=>   http_request("Entrada","s",date('H:i:s')),
        'Duracion'=>  http_request("Duracion","s","0"),
        'Ring1'  =>   http_request("Ring1","s",""),
        'Ring2'  =>   http_request("Ring2","s",""),
        'Ring3'  =>   http_request("Ring3","s",""),
        'Ring4'  =>   http_request("Rin4","s",""),
        'Observaciones' => http_request("Observaciones","s",""),
        'Estado' => http_request("Observaciones","i",-1), // -1:peonding 0:running 1:done
    );
	if ($operation===null) throw new Exception("Call to trainingFunctions without 'Operation' requested");
	$train= new Entrenamientos("trainingFunctions",$prueba);
	switch ($operation) {
		case "insert": $am->access(PERMS_OPERATOR); $result=$train->insert(); break;
		case "update": $am->access(PERMS_OPERATOR); $result=$train->update($id); break;
		case "delete": $am->access(PERMS_OPERATOR); $result=$train->delete($id); break;
		case "clear": $am->access(PERMS_OPERATOR); $result=$train->clear(); break;
		case "populate": $am->access(PERMS_OPERATOR); $result=$train->populate(); break;
		case "select": $result=$train->select(); break; // list with order, index, count and where
        case "enumerate": $result=$train->enumerate(); break; // list with where
        case "window": $result=$train->window($id,$size); break; // list next $size items starting at $id
        case "selectbyid": $result=$train->selectByID($id); break;
        case "dnd": $am->access(PERMS_OPERATOR); $result=$train->dragAndDrop(); break;
		default: throw new Exception("trainningFunctions:: invalid operation: '$operation' provided");
	}
	if ($result===null) 
		throw new Exception($train->errormsg);
	if ($result==="")
		$response= array('success'=>true,'insert_id'=>$train->conn->insert_id,'affected_rows'=>$train->conn->affected_rows);
	else $response=$result;
} catch (Exception $e) {
	do_log($e->getMessage());
	$response = array('errorMsg'=>$e->getMessage());
}
// take care on jsonp request to handle https cross domain for login and setPassword
if(isset($_GET['callback'])) echo $_GET['callback'].'('.json_encode($response).')'; // jsonp
else echo json_encode($response); // json

?>