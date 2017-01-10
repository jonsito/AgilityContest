<?php
/*
clasificacionessFunctions.php

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__."/classes/Clasificaciones.php");

try {
	$result=null;
	$prueba=http_request("Prueba","i",0);
	$jornada=http_request("Jornada","i",0);
	$mode=http_request("Mode","i","0"); // 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small
    $perro= http_request("Perro","i",0); // used to evaluate puesto or time to get first
    $op=http_request("Operation","s","clasificacionIndividual");
	$c= new Clasificaciones("clasificacionesFunctions",$prueba,$jornada,$perro);
	switch($op) {
		case "clasificacionIndividual":
			$mangas=array();
			$rondas=http_request("Rondas","i","0"); // bitfield of 512:Esp 256:KO 128:Eq4 64:Eq3 32:Opn 16:G3 8:G2 4:G1 2:Pre2 1:Pre1
			$mangas[0]=http_request("Manga1","i",0); // single manga
			$mangas[1]=http_request("Manga2","i",0); // mangas a dos vueltas
			$mangas[2]=http_request("Manga3","i",0);
			$mangas[3]=http_request("Manga4","i",0); // 1,2:GII 3,4:GIII
			$mangas[4]=http_request("Manga5","i",0);
			$mangas[5]=http_request("Manga6","i",0);
			$mangas[6]=http_request("Manga7","i",0);
			$mangas[7]=http_request("Manga8","i",0);// mangas 3..8 are used in KO rondas
			$result=$c->clasificacionFinal($rondas,$mangas,$mode);
			break;
		case "clasificacionEquipos":
			$mangas=array();
			$rondas=http_request("Rondas","i","0"); // bitfield of 512:Esp 256:KO 128:Eq4 64:Eq3 32:Opn 16:G3 8:G2 4:G1 2:Pre2 1:Pre1
			$mangas[0]=http_request("Manga1","i",0); // single manga
			$mangas[1]=http_request("Manga2","i",0); // mangas a dos vueltas
			$mangas[2]=http_request("Manga3","i",0);
			$mangas[3]=http_request("Manga4","i",0); // 1,2:GII 3,4:GIII
			$mangas[4]=http_request("Manga5","i",0);
			$mangas[5]=http_request("Manga6","i",0);
			$mangas[6]=http_request("Manga7","i",0);
			$mangas[7]=http_request("Manga8","i",0);// mangas 3..8 are used in KO rondas
			$result=$c->clasificacionFinalEquipos($rondas,$mangas,$mode);
			break;
		case "getPuesto":
			$data=array(
			    'Perro' => $perro,
				'Manga' => http_request("Manga","i",0),
				'Faltas'=> http_request("Faltas","i",0),
				'Tocados'=> http_request("Tocados","i",0),
				'Rehuses'=> http_request("Rehuses","i",0),
				'Eliminado'=> http_request("Eliminado","i",0),
				'NoPresentado'=> http_request("NoPresentado","i",1),
				'Tiempo'=> http_request("Tiempo","f",0)
			);
			$result=$c->getPuestoFinal($mode,$data);
			break;
	}
	if ($result===null) throw new Exception($c->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

?>