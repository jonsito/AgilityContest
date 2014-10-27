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
		$numero++;
		$logo=$otmgr->__selectAsArray("Logo","Clubes,PerroGuiaClub","(Clubes.ID=PerroGuiaClub.Club) AND (PerroGuiaClub.ID={$participante['Perro']})")['Logo'];
		if ($logo==="") $logo='rsce.png';
		$celo=($participante['Celo']==='1')?'Celo':'';
		$bg=(($numero%2)!=0)?"#ffffff":"#d0d0d0";
		echo '
	<div id="participante_'.$numero.'" style="background:'.$bg.'">
		<table class="llamada">
		<tr>
			<th rowspan="2">'.$numero.'</th>
			<td rowspan="2"><img src="/agility/images/logos/'.$logo.'" alt="'.$logo.'" width="50" height="50"/></td>
			<td rowspan="2">'.$participante['Grado'].' - '.$participante['Categoria'].'</td>
			<td>Dorsal: '.$participante['Dorsal'].'</td>
			<td>Lic.  : '.$participante['Licencia'].'</td>
			<td colspan="2" style="text-align:center; font-style:italic; font-size:25px;">'.$participante['Nombre'].'</td>
			<td style="text-align:right;">'.$celo.'</td>		
		</tr>
		<tr>
			<td colspan="3" style="text-align:left">Gu&iacute;a: '.$participante['NombreGuia'].'</td>
			<td style="text-align:center">-</td>
			<td colspan="2" style="text-align:left">Club: '.$participante['NombreClub'].'</td>
		</tr>
		</table>
	</div>
	';
	}
}

function videowall_resultados($idsesion) {
	$sesmgr=new Sesiones("VideoWall_Resultados");
	$mySession=$sesmgr->__getObject("Sesiones",$idsesion);
	$resmgr=new Resultados("videowall_resultados",$mySession->Prueba, 2 /* TODO: FIX: $mySession->Manga*/ );
	$result = $resmgr->getResultados(0 /* TODO: EVALUATE MODE */);
	$numero=0;
	foreach ($result['rows'] as $resultado) {
		error_log(json_encode($resultado));
		$numero++;
		$logo=$resmgr->__selectAsArray("Logo","Clubes,PerroGuiaClub","(Clubes.ID=PerroGuiaClub.Club) AND (PerroGuiaClub.ID={$resultado['Perro']})")['Logo'];
		if ($logo==="") $logo='rsce.png';
		echo '
			<div id="Resultado_'.$numero.'>
				<table class="llamada">
					<tr>
						<th rowspan="2">'.$resultado['Puesto'].'</th>
						<td rowspan="2"><img src="/agility/images/logos/'.$logo.'" alt="'.$logo.'" width="50" height="50"/></td>
						<td>Dorsal: '.$resultado['Dorsal'].'</td>
						<td>Lic.  : '.$resultado['Licencia'].'</td>
						<td>Cat  : '.$resultado['Categoria'].'</td>
						<td>Grado  : '.$resultado['Grado'].'</td>
						<td>Nombre.  : '.$resultado['Nombre'].'</td>
						<td>NombreGuia.  : '.$resultado['NombreGuia'].'</td>
						<td>NombreClub.  : '.$resultado['NombreClub'].'</td>
					</tr>
				</table>
				<hr />
			</div>
		';
	}
}

function videowall_livestream($sesion) {

}

$sesion = http_request("Session","i",0);
$operacion = http_request("Operation","s",null);

if($operacion==="LiveStream") return videowall_livestream($sesion);
if($operacion==="Llamada") return videowall_llamada($sesion);
if($operacion==="Resultados") return videowall_resultados($sesion);
