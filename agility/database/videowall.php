<?php

require_once("logging.php");
require_once(__DIR__."/classes/DBObject.php");
require_once(__DIR__."/classes/OrdenTandas.php");
require_once(__DIR__."/classes/Sesiones.php");

function videowall_llamada($idsesion) {
	$sesmgr=new Sesiones("VideoWall_Llamada");
	$otmgr=new OrdenTandas("Llamada a pista");
	$mySession=$sesmgr->__getObject("Sesiones",$idsesion);
	$result = $otmgr->getData($mySession->Prueba,$mySession->Jornada,10)['rows']; // obtiene los 10 primeros perros pendientes
	$numero=0;
	foreach ($result as $participante) {
		error_log("Prueba:{$mySession->Prueba} Jornada {$mySession->Jornada} Participante".json_encode($participante) );
		$numero++;
		$logo=$otmgr->__selectAsArray("Logo","Clubes,PerroGuiaClub","(Clubes.ID=PerroGuiaClub.Club) AND (PerroGuiaClub.ID={$participante['Perro']})")['Logo'];
		if ($logo==="") $logo='rsce.png';
		echo '
	<div id="participante_'.$numero.'">
		<table class="llamada">
		<tr>
			<th rowspan="2">'.$numero.'</th>
			<td rowspan="2"><img src="/agility/images/logos/'.$logo.'" alt="'.$logo.'" width="50" height="50"/></td>
			<td rowspan="2">'.$participante['Grado'].' - '.$participante['Categoria'].'</td>
			<td>Dorsal: '.$participante['Dorsal'].'</td>
		</tr>
		<tr>
			<td colspan="3" style="text-align:left">Gu&iacute;a: '.$participante['NombreGuia'].'</td>
			<td style="text-align:center">-</td>
			<td colspan="2" style="text-align:left">Club: '.$participante['NombreClub'].'</td>
		</tr>
		</table>
		<hr />
	</div>
	';
	}
}

function videowall_resultados($sesion) {
}

function videowall_livestream($sesion) {

}

$sesion = http_request("Session","i",0);
$operacion = http_request("Operation","s",null);

if($operacion==="Livestream") return videowall_livestream($sesion);
if($operacion==="Llamada") return videowall_llamada($sesion);
if($operacion==="Resultados") return videowall_resultados($sesion);
