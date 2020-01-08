<?php
/*
resultadosFunctions.php

Copyright  2013-2020 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


require_once(__DIR__ . "/../../server/logging.php");
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/AuthManager.php");
require_once(__DIR__ . "/../../server/database/classes/Resultados.php");

try {
	$result=null;
	$resultados=null;
	$operation=http_request("Operation","s",null);
	$pruebaID=http_request("Prueba","i",0);
	$jornadaID=http_request("Jornada","i",0);
	$mangaID=http_request("Manga","i",0);
	$idperro=http_request("Perro","i",0);
	$mode=http_request("Mode","i",12); // XLMST includes 4 (LMS) and 8 (LMST)
	$cats= http_request("Categorias","s","-"); // sort everything LMST by default

    // preliminary checks
	if ($operation===null) throw new Exception("Call to resultadosFunction without 'Operation' requested");
	if ($mangaID==0) throw new Exception("Call to resultadosFunction without 'Manga' provided");

	// get aut manager and resultados instance
	$resultados= Competitions::getResultadosInstance("resultadosFunctions",$mangaID);
	$am= AuthManager::getInstance("resultadosFunctions");

	// invoke proper operation
	switch ($operation) {
		// no insert as done by mean of procesa_inscripcion
		case "update": $am->access(PERMS_ASSISTANT); $result=$resultados->update($idperro); break;
		case "delete": $am->access(PERMS_OPERATOR); $result=$resultados->delete($idperro); break;
		case "select": $result=$resultados->select($idperro); break;
		case "reset":
            $catmode=12;
            switch ($cats) {
                case "-": $catmode=12; break; // use 12 cause includes 4 (LMS) and 8 (LMST)
                case "X": $catmode=9; break;
                case "L": $catmode=0; break;
                case "M": $catmode=1; break;
                case "S": $catmode=2; break;
                case "T": $catmode=5; break;
            }
		    $am->access(PERMS_OPERATOR);
		    $result=$resultados->reset($catmode);
		    break;
        case "swap": $am->access(PERMS_OPERATOR); $result=$resultados->swapMangas($cats); break;
        case "enumerate": $result=$resultados->enumerate($mode); break;
		case "getPendientes": $result=$resultados->getPendientes($mode); break;
		case "getResultadosIndividual":$result=$resultados->getResultadosIndividual($mode); break;
		case "getResultadosIndividualyEquipos":$result=$resultados->getResultadosIndividualyEquipos($mode); break;
		case "getPuesto":
			$data=array(
				'Perro' => $idperro,
				'Faltas'=> http_request("Faltas","i",0),
				'Tocados'=> http_request("Tocados","i",0),
				'Rehuses'=> http_request("Rehuses","i",0),
				'Eliminado'=> http_request("Eliminado","i",0),
				'NoPresentado'=> http_request("NoPresentado","i",1),
				'Tiempo'=> http_request("Tiempo","f",0)
			);
			$result=$resultados->getPuesto($mode,$data);
			break;
		case "getTRS": $result=$resultados->getTRS($mode); break;
		case "bestTimes": $result=$resultados->bestTimes($mode); break;
		default: throw new Exception("resultadosFunctions:: invalid operation: $operation provided");
	}
	if ($result===null) 
		throw new Exception($resultados->errormsg);
	if ($result==="") 
		echo json_encode(array('success'=>true,'insert_id'=>$resultados->conn->insert_id,'affected_rows'=>$resultados->conn->affected_rows));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>