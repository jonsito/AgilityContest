<?php

require_once("logging.php");
require_once(__DIR__."/classes/DBObject.php");
require_once(__DIR__."/classes/OrdenTandas.php");
require_once(__DIR__."/classes/Sesiones.php");

function videowall_llamada($idsesion) {
	$sesmgr=new Sesiones("VideoWall_Llamada");
	$otmgr=new OrdenTandas("Llamada a pista");
	$mySession=$sesmgr->__getObject("Sesiones",$idsesion);
	$result = $otmgr->getData($mySession->Prueba,$mySession->Jornada,10,$mySession->Tanda)['rows']; // obtiene los 10 primeros perros pendientes
	$numero=0;
	foreach ($result as $participante) {
		$numero++;
		$logo=$otmgr->__selectAsArray("Logo","Clubes,PerroGuiaClub","(Clubes.ID=PerroGuiaClub.Club) AND (PerroGuiaClub.ID={$participante['Perro']})")['Logo'];
		if ($logo==="") $logo='rsce.png';
		$celo=($participante['Celo']==='1')?'Celo':'';
		$bg=(($numero%2)!=0)?"#ffffff":"#d0d0d0";
		echo '
	<div id="participante_'.$numero.'" style="background:'.$bg.'">
		<table class="vwc_llamada">
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
			<td colspan="2" style="text-align:right">Club: '.$participante['NombreClub'].'</td>
		</tr>
		</table>
	</div>
	';
	}
}

function videowall_resultados($idsesion) {
	$sesmgr=new Sesiones("VideoWall_Resultados");
	$mySession=$sesmgr->__getObject("Sesiones",$idsesion);
	$resmgr=new Resultados("videowall_resultados",$mySession->Prueba, $mySession->Manga );
	$result = $resmgr->getResultados(0 /* TODO: EVALUATE MODE */);
	$numero=0;
	// cabecera de la tabla
	echo '
			<div id="vwc_tablaResultados">
			<table class="vwc_tresultados">
			<theader>
				<th>Puesto</th>
				<th>&nbsp</th>
				<th colspan="5">Participante</th>
				<th>Flt.</th>
				<th>Toc.</th>
				<th>Reh.</th>
				<th>Tiempo</th>
				<th>Vel.</th>
				<th>Penal.</th>
				<th colspan="2">Calificacion</th>
			</theader>
			<tbody>
			';
	foreach ($result['rows'] as $resultado) {
		error_log(json_encode($resultado));
		$numero++;
		$bg=(($numero%2)!=0)?"#ffffff":"#d0d0d0";
		$logo=$resmgr->__selectAsArray("Logo","Clubes,PerroGuiaClub","(Clubes.ID=PerroGuiaClub.Club) AND (PerroGuiaClub.ID={$resultado['Perro']})")['Logo'];
		if ($logo==="") $logo='rsce.png';
		echo '
			<tr id="Resultado_'.$numero.'" style="background:'.$bg.'">
				<td class="vwc_puesto">'.$resultado['Puesto'].'</td>
				<td><img src="/agility/images/logos/'.$logo.'" alt="'.$logo.'" width="50" height="50"/></td>
				<td colspan="3">
					<table class="vwc_trparticipantes">
						<tr>
							<td>Dorsal:</td><td>'.$resultado['Dorsal'].'</td>
							<td>Lic.:</td><td>'.$resultado['Licencia'].'</td>
							<td>Grado:</td><td>'.$resultado['Grado'].'</td>
							<td>Cat:</td><td>'.$resultado['Categoria'].'</td>
						</tr>
						<tr>
							<td>Guia:</td><td colspan="4">'.$resultado['NombreGuia'].'</td>
							<td>Club:</td><td colspan="2">'.$resultado['NombreClub'].'</td>
						</tr>
					</table>
				</td>
				<td colspan="2" class="vwc_nombre">'.$resultado['Nombre'].'</td>
				<td class="vwc_ftr">'.$resultado['Faltas'].'</td>
				<td class="vwc_ftr">'.$resultado['Tocados'].'</td>
				<td class="vwc_ftr">'.$resultado['Rehuses'].'</td>
				<td class="vwc_rlarge">'.number_format($resultado['Tiempo'],2).'</td>
				<td class="vwc_vel">'.number_format($resultado['Velocidad'],1).'</td>
				<td class="vwc_rlarge">'.number_format($resultado['Penalizacion'],2).'</td>
				<td colspan="2" class="vwc_calif">'.$resultado['Calificacion'].'</td>
			</tr>
		';
	}
	echo '</tbody></table></div>';
}

function videowall_livestream($sesion) {
	/* recupera los datos de un perro y le añade informacion de celo */
	$celo = http_request("Celo","i",0);
	$id= http_request("Perro","i",0);
	$pmgr= new Dogs("VideoWall_LiveSTream");
	$data=$pmgr->selectByID($id);
	$data["Celo"]=$celo;
	return $data;
}

$sesion = http_request("Session","i",0);
$operacion = http_request("Operation","s",null);

if($operacion==="livestream") return videowall_livestream($sesion);
if($operacion==="llamada") return videowall_llamada($sesion);
if($operacion==="resultados") return videowall_resultados($sesion);
