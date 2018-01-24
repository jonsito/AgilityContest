<?php
/*
jornadaFunctions.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__."/classes/Jornadas.php");
require_once(__DIR__."/classes/Ligas.php");
require_once(__DIR__."/classes/Admin.php");
	
	/***************** programa principal **************/

	try {
		$result=null;
		$jornadas= new Jornadas("jornadaFunctions",http_request("Prueba","i",0));
		$am= new AuthManager("jornadaFunctions");
		$operation=http_request("Operation","s",null);
        $jornadaid=http_request("ID","i",0);
        $perms=http_request("Perms","i",0);
        $mode=http_request("Mode","i",1); // on close operation, 0:open 1:close
		$allowClosed=http_request("AllowClosed","i",0);
		$hideUnassigned=http_request("HideUnassigned","i",0);
		if ($operation===null) throw new Exception("Call to jornadaFunctions without 'Operation' requested");
		switch ($operation) {
			// there is no need of "insert" method: every prueba has 8 "hard-linked" jornadas
			case "delete": $am->access(PERMS_OPERATOR); $result=$jornadas->delete($jornadaid); break;
			case "close":
			    $am->access(PERMS_OPERATOR); // check permission. fire exception on denied
                $adm= new Admin("jornadaFunctions",$am,"");
                $adm->autobackup(0,""); // make a backup before open/close
			    $result=$jornadas->close($jornadaid,$mode); // open/close journey according mode
                // handle leagues
                $l=new Ligas("CloseJourney");
                $l->update($jornadaid,$mode); // add or delete points to league according open/close journey
			    break;
			case "update": $am->access(PERMS_OPERATOR); $result=$jornadas->update($jornadaid,$am); break;
            case "select": $result=$jornadas->selectByPrueba(); break;
            case "getbyid": $result=$jornadas->selectByID($jornadaid); break;
			case "enumerate": $result=$jornadas->searchByPrueba($allowClosed,$hideUnassigned); break;
			case "rounds": $result=$jornadas->roundsByJornada($jornadaid); break;
            case "getAvailableParents": $result=$jornadas->getAvailableParents($jornadaid); break;
			case "enumerateMangasByJornada": $result=Jornadas::enumerateMangasByJornada($jornadaid); break;
            case "enumerateRondasByJornada": $result=Jornadas::enumerateRondasByJornada($jornadaid); break;
            case "access": $result=$jornadas->checkAccess($am,$jornadaid,$perms); break;
			default: throw new Exception("jornadaFunctions:: invalid operation: $operation provided");
		}
		if ($result===null) 
			throw new Exception($jornadas->errormsg);
		if ($result==="")
			echo json_encode(array('success'=>true,'insert_id'=>0,'affected_rows'=>0));
		else echo json_encode($result);
	} catch (Exception $e) {
		do_log($e->getMessage());
		echo json_encode(array('errorMsg'=>$e->getMessage()));
	}
?>