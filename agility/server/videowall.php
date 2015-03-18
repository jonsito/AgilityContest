<?php
/*
videowall.php

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__."/auth/Config.php");
require_once(__DIR__."/database/classes/DBObject.php");
require_once(__DIR__."/database/classes/Tandas.php");
require_once(__DIR__."/database/classes/Mangas.php");
require_once(__DIR__."/database/classes/Sesiones.php");
require_once(__DIR__."/database/classes/Inscripciones.php");

class VideoWall {
	protected $myLogger;
	protected $myDBObject;
	protected $sessionid;
	protected $session;
	protected $prueba;
	protected $jornada;
	protected $manga;
	protected $tanda;
	protected $config;
	protected $mangaid;
	protected $tandatype;
	protected $mode;
	
	function __construct($sessionid,$pruebaid,$jornadaid,$mangaid,$tandatype,$mode) {
		$this->config=Config::getInstance();
		$this->myLogger=new Logger("VideoWall.php",$this->config->getEnv("debug_level"));
		$this->myDBObject=new DBObject("Videowall");
		if ($sessionid!=0) {
			$this->session=$this->myDBObject->__getArray("Sesiones",$sessionid);
			$this->sessionid=$sessionid;
			$this->prueba=$this->myDBObject->__getArray("Pruebas",$this->session['Prueba']);
			$this->jornada=$this->myDBObject->__getArray("Jornadas",$this->session['Jornada']);
			$this->manga=$this->myDBObject->__getArray("Mangas",$this->session['Manga']);
			$this->mangaid=$this->manga['ID'];
			$this->tanda=$this->myDBObject->__getArray("Tandas",$this->session['Tanda']);
			$this->tandatype=$this->tanda['Tipo'];
			$this->mode=-1;
		} else {
			$this->session=null;
			$this->sessionid=0;
			$this->prueba=$this->myDBObject->__getArray("Pruebas",$pruebaid);
			$this->jornada=$this->myDBObject->__getArray("Jornadas",$jornadaid);
			$this->manga=$this->myDBObject->__getArray("Mangas",$mangaid);
			$this->mangaid=$this->manga['ID'];
			$this->tanda=null;
			$this->tandatype=$tandatype;
			$this->mode=$mode;	
		}
		$this->myLogger->info("sesion:$sessionid prueba:{$this->prueba['ID']} jornada:{$this->jornada['ID']} manga:{$this->mangaid} tanda:{$this->tandatype} mode:$mode");
	}

	public static $cat=array('-'=>'','L'=>'Large','M'=>'Medium','S'=>'Small','T'=>'Tiny');
	public static $modestr  
		=array("Large","Medium","Small","Medium+Small","Conjunta L/M/S","Tiny","Large+Medium","Small+Tiny","Conjunta L/M/S/T");
	
	function getModeString($mode) {	return VideoWall::$modestr[$mode]; }
	
	function getBackground($row) {
		return (($row%2)!=0)?$this->config->getEnv("vw_rowcolor1"):$this->config->getEnv("vw_rowcolor2");
	}
	
	
	function generateHeaderInfo() {
		$tandastr=Tandas::getTandaString($this->tandatype);
		$sesname=($this->sessionid!=0)?$this->session['Nombre']:'';
		echo '<input type="hidden" id="vw_NombreSesion" value="'.$sesname.'"/>';
		echo '<input type="hidden" id="vw_NombrePrueba" value="'.$this->prueba['Nombre'].'"/>';
		echo '<input type="hidden" id="vw_NombreJornada" value="'.$this->jornada['Nombre'].'"/>';
		echo '<input type="hidden" id="vw_NombreManga" value="'.$tandastr.'"/>';
		echo '<input type="hidden" id="vw_NombreTanda" value="'.$tandastr.'"/>';
	}
	
	function videowall_llamada($pendientes) {
		$lastTanda="";
		$otmgr=new Tandas("Llamada a pista",$this->prueba['ID'],$this->jornada['ID']);
		$result = $otmgr->getData($this->sessionid,$this->tanda['ID'],$pendientes)['rows']; // obtiene los 10 primeros perros pendientes
		$numero=0;
		$this->generateHeaderInfo();
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
			$bg=$this->getBackground($numero);
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
		return 0;
	}

	function videowall_resultados() {
		// anyade informacion extra en el resultado html
		$this->generateHeaderInfo();
		if ($this->mangaid==0) { // no manga defined yet
			echo '
			<!-- Datos de TRS y TRM -->
			<div id="vwc_tablaTRS">
				<table class="vwc_trs">
					<thead>
						<tr>
							<th colspan="2" style="align:left">Resultados Provisionales</th>
							<th colspan="3">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<tr><td colspan="5">&nbsp</td></tr>
						<tr style="align:right">
							<td>Distancia:</td>
							<td>Obst&aacute;culos:</td>
							<td>T.R.Standard:</td>
							<td>T.R.M&aacute;ximo:</td>
							<td>Velocidad:</td>
						</tr>
					</tbody>
				</table>
			</div>
			';
			return 0;
		}
		$resmgr=new Resultados("videowall_resultados",$this->prueba['ID'], $this->mangaid );
		$myManga=$this->myDBObject->__getObject("Mangas",$this->mangaid);
		
		// Si en lugar de TandaID tenemos definido el modo, obtenemos datos a partir de la manga
		$mode=-1;
		$mangastr="";
		if ($this->mode>=0){
			$mode=$this->mode;
			$mangastr=Mangas::$tipo_manga[$myManga->Tipo][1]." - ".VideoWall::$modestr[$mode];
		} else {
			// obtenemos modo de resultados asociado a la manga en base a TandaID
			$mode=Tandas::getModeByTanda($this->prueba['RSCE'],$myManga->Recorrido,$this->tandatype);
			$mangastr=Tandas::getMangaStringByTanda($this->tandatype)." - ".VideoWall::$modestr[$mode];
		}
		$this->myLogger->trace("**** Mode es $mode");
		$result = $resmgr->getResultados($mode);
		$numero=0;
		echo '
			<!-- Datos de TRS y TRM -->
			<div id="vwc_tablaTRS">
			<table class="vwc_trs">
				<thead>
					<tr>
						<th colspan="2" style="align:left">Resultados Provisionales</th>
						<th colspan="3">'.$mangastr.'</th>
					</tr>
				</thead>
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
			<thead>
				<th colspan="6">Participante</th>
				<th>Flt.</th>
				<th>Toc.</th>
				<th>Reh.</th>
				<th>Tiempo</th>
				<th>Vel.</th>
				<th>Penal.</th>
				<th colspan="2">Calificacion</th>
				<th>Puesto</th>
			</thead>
			<tbody>
			';
		foreach ($result['rows'] as $resultado) {
			error_log(json_encode($resultado));
			$numero++;
			$bg=$this->getBackground($numero);
			$logo=$resmgr->__selectAsArray("Logo","Clubes,PerroGuiaClub","(Clubes.ID=PerroGuiaClub.Club) AND (PerroGuiaClub.ID={$resultado['Perro']})")['Logo'];
			if ($logo==="") $logo='rsce.png';
			echo '
				<tr id="Resultado_'.$numero.'" style="background:'.$bg.'">
					<td><img src="/agility/images/logos/'.$logo.'" alt="'.$logo.'" width="50" height="50"/></td>
					<td colspan="2" class="vwc_nombre">'.$resultado['Nombre'].'</td>
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
					<td class="vwc_ftr">'.$resultado['Faltas'].'</td>
					<td class="vwc_ftr">'.$resultado['Tocados'].'</td>
					<td class="vwc_ftr">'.$resultado['Rehuses'].'</td>
					<td class="vwc_rlarge">'.number_format($resultado['Tiempo'],2).'</td>
					<td class="vwc_vel">'.number_format($resultado['Velocidad'],1).'</td>
					<td class="vwc_rlarge">'.number_format($resultado['Penalizacion'],2).'</td>
					<td colspan="2" class="vwc_calif">'.$resultado['Calificacion'].'</td>
					<td class="vwc_puesto">'.$resultado['Puesto'].'</td>
				</tr>
			';
		}
		echo '</tbody></table></div>';
		return 0;
	}
	
	function videowall_livestream() {
		/* recupera los datos de un perro y le añade informacion de celo */
		$celo = http_request("Celo","i",0);
		$id= http_request("Perro","i",0);
		$pmgr= new Dogs("VideoWall_LiveSTream");
		$data=$pmgr->selectByID($id);
		$data["Celo"]=$celo;
		return $data;
	}
	
	function videowall_inscripciones() {
		$imgr=new Inscripciones("videowall_inscripciones",$this->prueba['ID']);
		$result=$imgr->inscritosByJornada($this->jornada['ID']);
		$club=0;
		$fila=0; // used to set table background color
		$this->generateHeaderInfo();
		echo '<table style="width:100%"><tbody>';
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
			$bg=$this->getBackground($fila);
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
		echo '</tbody></table>';
		return 0;
	}
	
	function videowall_ordensalida() {
		$lastCategoria="";
		$osmgr=new OrdenSalida("Orden de salida",$this->mangaid);
		$result = $osmgr->getData()['rows']; // obtiene los 10 primeros perros pendientes
		$numero=0;
		$this->generateHeaderInfo();
		echo '<table class="vwc_callEntry">';
		foreach ($result as $participante) {
			if ($lastCategoria!==$participante['Categoria']){
				$lastCategoria=$participante['Categoria'];
				$categ=VideoWall::$cat[$lastCategoria];
				$mangastr=Tandas::getMangaStringByTanda($this->tandatype);
				echo '<tr><td colspan="5" class="vwc_callEntry vwc_callTanda">---- '.$mangastr.' - '.$categ.' ----</td></tr>';
				$numero=0;
			}
			$numero++;
			$logo=$osmgr->__selectAsArray("Logo","Clubes,PerroGuiaClub","(Clubes.ID=PerroGuiaClub.Club) AND (PerroGuiaClub.ID={$participante['Perro']})")['Logo'];
			if ($logo==="") $logo='rsce.png';
			$celo=($participante['Celo']==='1')?'Si':'No';
			$pcolor=($participante['Pendiente']==0)?"#000000":"#FF0000"; // foreground color=red if pendiente
			$bg=$this->getBackground($numero);
			echo '
				<tr id="participante_{$numero}" style="background:'.$bg.';">
					<td class="vwc_callEntry vwc_callNumero" style="color:'.$pcolor.';">'.$numero.'</td>
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
		return 0;
	}
} 

$sesion = http_request("Session","i",0);
$operacion = http_request("Operation","s",null);
$pendientes = http_request("Pendientes","i",10);
// on session==0, use this elements as IDentifiers
$prueba = http_request("Prueba","i",0);
$jornada = http_request("Jornada","i",0);
$manga = http_request("Manga","i",0);
$tanda = http_request("Tanda","i",0);
$mode = http_request("Mode","i",-1); // -1 means "do not use"

$vw=new VideoWall($sesion,$prueba,$jornada,$manga,$tanda,$mode);
try {
	if($operacion==="livestream") return $vw->videowall_livestream();
	if($operacion==="llamada") return $vw->videowall_llamada($pendientes);
	if($operacion==="resultados") return $vw->videowall_resultados();
	if($operacion==="inscripciones") return $vw->videowall_inscripciones();
	if($operacion==="ordensalida") return $vw->videowall_ordensalida();
} catch (Exception $e) {
	return "<p>Error:<br />".$e->getMessage()."</p>";
}