<?php
/*
videowall.php

Copyright 2013-2014 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

require_once(__DIR__."/logging.php");
require_once(__DIR__."/database/classes/DBObject.php");
require_once(__DIR__."/database/classes/OrdenTandas.php");
require_once(__DIR__."/database/classes/Sesiones.php");
require_once(__DIR__."/database/classes/Inscripciones.php");

class VideoWall {
	protected $myLogger;
	
	function __construct() {
		$this->myLogger=new Logger("VideoWall.php");
	}

	public static $cat=array('-'=>'','L'=>'Large','M'=>'Medium','S'=>'Small','T'=>'Tiny');
	public static $modestr  =array("Large","Medium","Small","Medium+Small","Conjunta L/M/S");
	
	// matrid de modos a evaluar en funcion del tipo de recorrido y de la tanda
	// recorridos:
	// 0: l/m/s separados 1: l/m+s 2: l+m+s conjunto
	// modos:
	// 0:large 1:medium 2:small 3:m+s 4:l+m+s -1:no valido
	public static $modes=array(
			0	=> array(-1,-1,-1, '-- Sin especificar --'),
			// en pre-agility no hay categorias
			1	=> array(-1, -1,  4, 'Pre-Agility 1'), // en pre agility-compiten todos juntos
			2	=> array(-1, -1,  4, 'Pre-Agility 2'),
			3	=> array( 0,  0,  4, 'Agility Grado I Manga 1'/* Large */),
			4	=> array( 1,  3,  4, 'Agility Grado I Manga 1'/* Medium */),
			5	=> array( 2,  3,  4, 'Agility Grado I Manga 1'/* Small */),
			6	=> array( 0,  0,  4, 'Agility Grado I Manga 2'/* Large */),
			7	=> array( 1,  3,  4, 'Agility Grado I Manga 2'/* Medium */),
			8	=> array( 2,  3,  4, 'Agility Grado I Manga 2'/* Small */),
			9	=> array( 0,  0,  4, 'Agility Grado II'/* Large */),
			10	=> array( 1,  3,  4, 'Agility Grado II'/* Medium */),
			11	=> array( 2,  3,  4, 'Agility Grado II'/* Small */),
			12	=> array( 0,  0,  4, 'Agility Grado III'/* Large */),
			13	=> array( 1,  3,  4, 'Agility Grado III'/* Medium */),
			14	=> array( 2,  3,  4, 'Agility Grado III'/* Small */),
			15	=> array( 0,  0,  4, 'Agility Abierta (Open)'/* Large */),
			16	=> array( 1,  3,  4, 'Agility Abierta (Open)'/* Medium */),
			17	=> array( 2,  3,  4, 'Agility Abierta (Open)'/* Small */),
			18	=> array( 0,  0,  4, 'Agility Eq. (3 mejores)'/* Large */),
			19	=> array(-1,  3,  4, 'Agility Eq. (3 mejores)'/* Medium */), // en equipos compiten m y s juntos
			20	=> array(-1,  3,  4, 'Agility Eq. (3 mejores)'/* Small */),
			21	=> array( 0,  0,  4, 'Agility. Eq. (4 conjunta)'/* Large */),
			// en jornadas por equipos conjunta se mezclan categorias M y S
			22	=> array(-1,  3,  4, 'Agility Eq. (4 conjunta)'/* Med/Small */),
			23	=> array( 0,  0,  4, 'Jumping Grado II'/* Large */),
			24	=> array( 1,  3,  4, 'Jumping Grado II'/* Medium */),
			25	=> array( 2,  3,  4, 'Jumping Grado II'/* Small */),
			26	=> array( 0,  0,  4, 'Jumping Grado III'/* Large */),
			27	=> array( 1,  3,  4, 'Jumping Grado III'/* Medium */),
			28	=> array( 2,  3,  4, 'Jumping Grado III'/* Small */),
			29	=> array( 0,  0,  4, 'Jumping Abierta (Open)'/* Large */),
			30	=> array( 1,  3,  4, 'Jumping Abierta (Open)'/* Medium */),
			31	=> array( 2,  3,  4, 'Jumping Abierta (Open)'/* Small */),
			32	=> array( 0,  0,  4, 'Jumping Eq. (3 mejores)'/* Large */),
			33	=> array(-1,  3,  4, 'Jumping Eq. (3 mejores)'/* Medium */),
			34	=> array(-1,  3,  4, 'Jumping Eq. (3 mejores)'/* Small */),
			// en jornadas por equipos conjunta se mezclan categorias M y S
			35	=> array( 0,  0,  4, 'Jumping. Eq. (4 conjunta)'/* Large */),
			36	=> array(-1,  3,  4, 'Jumping. Eq. (4 conjunta)'/* Med/Small */),
			// en las rondas KO, los perros compiten todos contra todos
			37	=> array(-1, -1,  4, 'Manga K.O.'),
			38	=> array( 0,  0,  4, 'Manga Especial'/* Large */),
			39	=> array( 1,  3,  4, 'Manga Especial'/* Medium */),
			40	=> array( 2,  3,  4, 'Manga Especial'/* Small */),
	);

	function videowall_llamada($idsesion,$pendientes) {
		$lastTanda="";
		$sesmgr=new Sesiones("VideoWall_Llamada");
		$otmgr=new OrdenTandas("Llamada a pista");
		$mySession=$sesmgr->__getObject("Sesiones",$idsesion);
		$result = $otmgr->getData($mySession->Prueba,$mySession->Jornada,$pendientes,$mySession->Tanda)['rows']; // obtiene los 10 primeros perros pendientes
		$numero=0;
		echo '<table class="vwc_callEntry">';
		foreach ($result as $participante) {
			if ($lastTanda!==$participante['Tanda']){
				$lastTanda=$participante['Tanda'];
				echo '<tr><td colspan="5" class="vwc_callEntry vwc_callTanda">---- '.$lastTanda.' ----</td></tr>';
			}
			$numero++;
			$logo=$otmgr->__selectAsArray("Logo","Clubes,PerroGuiaClub","(Clubes.ID=PerroGuiaClub.Club) AND (PerroGuiaClub.ID={$participante['Perro']})")['Logo'];
			if ($logo==="") $logo='rsce.png';
			$celo=($participante['Celo']==='1')?'Si':'No';
			$bg=(($numero%2)!=0)?"#ffffff":"#d0d0d0";
			echo '
				<tr id="participante_'.$numero.'" style="background:'.$bg.';">
					<td class="vwc_callEntry vwc_callNumero">'.$numero.'</td>
					<td class="vwc_callEntry vwc_callLogo">
						<!-- trick to insert a resizeable image: use div+bgimage instead of img tag -->
						<div style="height=100%;
									position:relative;
									background:url(\'/agility/images/logos/'.$logo.'\')no-repeat;
									background-size:contain;
									background-position:center;
									font-size:600%">&nbsp;</div>
					</td>
					<td class="vwc_callEntry vwc_callDatos">
						Dorsal: '.$participante['Dorsal'].'<br />
						Lic. : '.$participante['Licencia'].'<br />
						Grado: '.$participante['Grado'].'<br />	
						Cat. : '.$participante['Categoria'].'		
					</td>
					<td class="vwc_callEntry vwc_callGuiaClub">
						Gu&iacute;a: '.$participante['NombreGuia'].'<br />
						Club: '.$participante['NombreClub'].'<br />
						Celo: '.$celo.'	
					</td>				
					<td class="vwc_callEntry vwc_callNombre">'.$participante['Nombre'].'</td>
				</tr>
			';
		}
		echo '</table>';
	}

	function videowall_resultados($idsesion) {
		$sesmgr=new Sesiones("VideoWall_Resultados");
		$mySession=$sesmgr->__getObject("Sesiones",$idsesion);
		$resmgr=new Resultados("videowall_resultados",$mySession->Prueba, $mySession->Manga );
		// obtenemos modo de resultados asociado a la manga
		$myManga=$sesmgr->__getObject("Mangas",$mySession->Manga);
		$mode=VideoWall::$modes[$mySession->Tanda][$myManga->Recorrido];
		$this->myLogger->trace("**** Mode es $mode");
		$result = $resmgr->getResultados($mode);
		$numero=0;
		$mangastr=VideoWall::$modes[$mySession->Tanda][3]." - ".VideoWall::$modestr[$mode];
		// cabecera de la tabla
		echo '
			<!-- Datos de TRS y TRM -->
			<div id="vwc_tablaTRS">
			<table class="vwc_trs">
				<theader>
					<tr>
						<th colspan="2" style="align:leftt">Resultados Provisionales</th>
						<th colspan="3">'.$mangastr.'</th>
					</tr>
				</theader
				<tbody>
					<tr><td colspan="5">&nbsp</td></tr>
					<tr style="align:right">
						<td>Distancia: '.$result['trs']['dist'].'mts.</td>
						<td>Obst&aacute;culos: '.$result['trs']['obst'].'</td>
						<td>T.R.Standard: '.$result['trs']['trs'].'secs.</td>
						<td>T.R.M&aacute;ximo: '.$result['trs']['trm'].'secs.</td>
						<td>Velocidad: '.$result['trs']['vel'].'m/s</td>
					</tr>
				</tbody>
			</table>
			<hr />
			</div>
			<!-- Resultados -->
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
	
	function videowall_inscripciones($prueba,$jornada) {
		$imgr=new Inscripciones("videowall_inscripciones",$prueba);
		$result=$imgr->inscritosByJornada($jornada);
		$club=0;
		$fila=0; // used to set table background color
		foreach ($result['rows'] as $i) {
			if ($club!=$i['Club']) {
				$club=$i['Club'];
				$fila=0;
				// evaluamos logo
				$logo=$imgr->__selectAsArray("Logo","Clubes,PerroGuiaClub","(Clubes.ID=PerroGuiaClub.Club) AND (PerroGuiaClub.ID={$i['Perro']})")['Logo'];
				if ($logo==="") $logo='rsce.png';
				// pintamos cabecera
				echo '<tr><td colspan="6"><hr /></td></tr>';
				echo "<tr id=\"Club_$club\">";
				echo "<td colspan=\"1\" style=\"width:10%\" rowspan=\"2\">";
				echo '	<img src="/agility/images/logos/'.$logo.'" alt="'.$logo.'" width="75" height="75"/>';
				echo "</td>";
				echo '<td colspan="5" class="vwi_club">'.$i['NombreClub'].'</td>';
				echo "</tr>";
				echo "<tr>";
				echo "<th style=\"width:20%;padding-left:25px\">Nombre</th>";
				echo "<th style=\"width:15%\">Raza</th>";
				echo "<th style=\"width:5%\" align=\"center\">Licencia</th>";
				echo "<th style=\"width:15%\" align=\"center\">Categ. - Grado</th>";
				echo "<th style=\"width:30%;text-align:right;padding-right:25px\">Gu&iacute;a</th>";
				echo "</tr>";
			}
			$bg=(($fila%2)!=0)?"#ffffff":"#d0d0d0";
			$c=VideoWall::$cat[$i['Categoria']];
			echo "<tr id=\"Inscripcion_{$i['Dorsal']}\" style=\"background:$bg;font-size:1.4em\">";
			echo "<td style=\"width:10%;padding-left:25px\">{$i['Dorsal']}</td>";
			echo "<td style=\"width:20%;font-weight:bold;font-style:italic;padding-left:25px\">{$i['Nombre']}</td>";
			echo "<td style=\"width:15%\">{$i['Raza']}</td>";
			echo "<td style=\"width:5%;text-align:center\">{$i['Licencia']}</td>";
			echo "<td style=\"width:15%;text-align:center\">{$c} - {$i['Grado']}</td>";
			echo "<td style=\"width:30%;font-style:italic;text-align:right;padding-right:25px;\">{$i['NombreGuia']}</td>";
			echo "</tr>";
			$fila++;
		}
	}
} 

$sesion = http_request("Session","i",0);
$prueba = http_request("Prueba","i",0);
$jornada = http_request("Jornada","i",0);
$operacion = http_request("Operation","s",null);
$pendientes = http_request("Pendientes","i",10);
$vw=new VideoWall();
try {
	if($operacion==="livestream") return $vw->videowall_livestream($sesion);
	if($operacion==="llamada") return $vw->videowall_llamada($sesion,$pendientes);
	if($operacion==="resultados") return $vw->videowall_resultados($sesion);
	if($operacion==="inscripciones") return $vw->videowall_inscripciones($prueba,$jornada);
} catch (Exception $e) {
	return "<p>Error:<br />".$e->getMessage()."</p>";
}