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

require_once(__DIR__ . "/../logging.php");
require_once(__DIR__ . "/../auth/Config.php");
require_once(__DIR__ . "/../database/classes/DBObject.php");
require_once(__DIR__ . "/../database/classes/Clubes.php");
require_once(__DIR__ . "/../database/classes/Tandas.php");
require_once(__DIR__ . "/../database/classes/Mangas.php");
require_once(__DIR__ . "/../database/classes/Sesiones.php");
require_once(__DIR__ . "/../database/classes/Inscripciones.php");

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
	protected $club;

	function __construct($sessionid,$pruebaid,$jornadaid,$mangaid,$tandatype,$mode) {
		$this->config=Config::getInstance();
		$this->myLogger=new Logger("VideoWall.php",$this->config->getEnv("debug_level"));
		$this->myDBObject=new DBObject("Videowall");
		if ($sessionid!=0) {
            // obtenemos los datos desde la sesion abierta en el tablet
			$this->session=$this->myDBObject->__getArray("Sesiones",$sessionid);
			$this->sessionid=$sessionid;
			$this->prueba=$this->myDBObject->__getArray("Pruebas",$this->session['Prueba']);
			$this->jornada=$this->myDBObject->__getArray("Jornadas",$this->session['Jornada']);
            $this->tanda=$this->myDBObject->__getArray("Tandas",$this->session['Tanda']);
            $this->tandatype=$this->tanda['Tipo'];
            if ($this->session['Manga']==0) {
                // take care on User-defined Tandas (Manga=0)
                $this->manga=null;
                $this->mangaid=0;
            } else {
                // normal Tandas
                $this->manga=$this->myDBObject->__getArray("Mangas",$this->session['Manga']);
                $this->mangaid=$this->manga['ID'];
            }
			$this->mode=-1;
		} else {
            // obtenemos los datos desde las variables recibidas por http
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
        $this->club= $this->myDBObject->__getArray("Clubes",$this->prueba['Club']);
		$this->myLogger->info("sesion:$sessionid prueba:{$this->prueba['ID']} jornada:{$this->jornada['ID']} manga:{$this->mangaid} tanda:{$this->tandatype} mode:$mode");
	}


	public static $cat=array('-'=>'','L'=>'Large','M'=>'Medium','S'=>'Small','T'=>'Tiny');
	public static $modestr  
		=array("Large","Medium","Small","Medium+Small","Conjunta L/M/S","Tiny","Large+Medium","Small+Tiny","Conjunta L/M/S/T");

    function isTeam() {
        if (intval($this->jornada['Equipos3'])==1) return true;
        if (intval($this->jornada['Equipos4'])==1) return true;
        return false;
    }

	function getModeString($mode) {	return VideoWall::$modestr[$mode]; }
	
	function getBackground($row) {
		return (($row%2)!=0)?$this->config->getEnv("vw_rowcolor1"):$this->config->getEnv("vw_rowcolor2");
	}
	
	function generateHeaderInfo() {
		$tandastr=Tandas::getTandaString($this->tandatype);
        $mangastr=($this->mangaid==0)?"":Mangas::$tipo_manga[$this->manga['Tipo']][1];
		$sesname=($this->sessionid!=0)?$this->session['Nombre']:'';
        echo '<div id="vw_HiddenHeader"><form id="vw_HiddenForm">';
		echo '<input type="hidden" id="vw_NombreSesion" value="'.$sesname.'"/>';
		echo '<input type="hidden" id="vw_NombreSesion" value="'.$this->session['Nombre'].'"/>';
		echo '<input type="hidden" id="vw_NombrePrueba" value="'.$this->prueba['Nombre'].'"/>';
		echo '<input type="hidden" id="vw_NombreJornada" value="'.$this->jornada['Nombre'].'"/>';
        echo '<input type="hidden" id="vw_NombreManga" value="'. $mangastr .'"/>';
		echo '<input type="hidden" id="vw_NombreTanda" value="'. $tandastr . '"/>';
        echo '</form></div';
	}

	function generateTandaInfo() {
        $str=$this->tanda['Nombre'];
        echo '<table class="vwc_callEntry"><tr><td colspan="5" class="vwc_callTanda">'.$str.'</td></tr></table>';
    }
	function videowall_llamada($pendientes) {
        $lastTanda="";
        $lastTeam=0;
        $this->generateHeaderInfo();
        if ($this->manga==null) $this->generateTandaInfo();
		$otmgr=new Tandas("Llamada a pista",$this->prueba['ID'],$this->jornada['ID']);
		$result = $otmgr->getData($this->sessionid,$this->tanda['ID'],$pendientes)['rows']; // obtiene los $pendientes primeros perros
		$numero=0;
		echo '<table class="vwc_callEntry">';
		foreach ($result as $participante) {
			if ($lastTanda!==$participante['Tanda']){
				$lastTanda=$participante['Tanda'];
                $lastTeam=0; // make sure team's name is shown
				echo '<tr><td colspan="5" class="vwc_callTanda">---- '.$lastTanda.' ----</td></tr>';
			}
            if ( $this->isTeam() && ($lastTeam!==$participante['Equipo']) ){
                $lastTeam=$participante['Equipo'];
                $team=$this->myDBObject->__getObject("Equipos",$lastTeam);
                echo '<tr style="height:3%;"><td colspan="5" class="vwc_callTeam">Equipo: ' . $team->Nombre . '</td></tr>';
            }

			$numero++;
            $logo=$this->club->getLogoName('NombreClub',$participante['NombreClub']);
			$celo=($participante['Celo']==1)?'Si':'No';
			$bg=$this->getBackground($numero);
			echo '
				<tr id="participante_'.$numero.'" style="background:'.$bg.';">
					<td class="vwc_callNumero">'.$numero.'</td>
					<td class="vwc_callLogo">
						<!-- trick to insert a resizeable image: use div+bgimage instead of img tag -->
						<div style="height=100%;
									position:relative;
									background:url(\'/agility/images/logos/'.$logo.'\')no-repeat;
									background-size:contain;
									background-position:center;
									font-size:400%">&nbsp;</div>
					</td>
					<td class="vwc_callDatos">
						Dorsal: '.$participante['Dorsal'].'<br />
						Lic. : '.$participante['Licencia'].'<br />
						Grado: '.$participante['Grado'].'<br />	
						Cat. : '.$participante['Categoria'].'		
					</td>
					<td class="vwc_callGuiaClub">
						Gu&iacute;a: '.$participante['NombreGuia'].'<br />
						Club: '.$participante['NombreClub'].'<br />
						Celo: '.$celo.'	
					</td>				
					<td class="vwc_callNombre">'.$participante['Nombre'].'</td>
				</tr>
			';
		}
		echo '</table>';
		return 0;
	}
			
	function videowall_resultados() {
		// anyade informacion extra en el resultado html
        $this->generateHeaderInfo();
		if ($this->manga==null) { // no manga defined yet
			echo '
			<!-- Datos de TRS y TRM -->
			<div id="vwc_tablaTRS">
				<table class="vwc_trs">
					<thead>
						<tr>
							<th colspan="2" style="text-align:left">Resultados Provisionales</th>
							<th colspan="3">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<tr><td colspan="5">&nbsp</td></tr>
						<tr style="text-align:right">
							<td>Distancia:</td>
							<td>Obst&aacute;culos:</td>
							<td>T.R.Standard:</td>
							<td>T.R.M&aacute;ximo:</td>
							<td>Velocidad:</td>
						</tr>
					</tbody>
				</table>
			</div>
            $this->generateTandaInfo();
			';
			return 0;
		}
		$resmgr=new Resultados("videowall_resultados",$this->prueba['ID'], $this->mangaid );
		$myManga=$this->myDBObject->__getObject("Mangas",$this->mangaid);
		
		// Si en lugar de TandaID tenemos definido el modo, obtenemos datos a partir de la manga
		if ($this->mode>=0){
			$mode=$this->mode;
			$mangastr=Mangas::$tipo_manga[$myManga->Tipo][1]." - ".VideoWall::$modestr[$mode];
		} else {
			// obtenemos modo de resultados asociado a la manga en base a TandaID
			$mode=Tandas::getModeByTanda($this->prueba['RSCE'],$myManga->Recorrido,$this->tandatype);
			$mangastr=Tandas::getMangaStringByTanda($this->tandatype)." - ".VideoWall::$modestr[$mode];
		}
		$this->myLogger->trace("tanda:{$this->session['Tanda']} recorrido:{$myManga->Recorrido} **** Mode es $mode");
		$result = $resmgr->getResultados($mode);
		$numero=0;
		echo '
			<!-- Datos de TRS y TRM -->
			<div id="vwc_tablaTRS">
			<table class="vwc_trs">
				<thead>
					<tr>
						<th colspan="2" style="text-align:left">Resultados Provisionales</th>
						<th colspan="3">'.$mangastr.'</th>
					</tr>
				</thead>
				<tbody>
					<tr><td colspan="5">&nbsp</td></tr>
					<tr style="text-align:right">
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
			    <tr>
				<th colspan="5">Participante</th>
				<th>F</th>
				<th>T</th>
				<th>R</th>
				<th>Tiempo</th>
				<th>Vel.</th>
				<th>Penal.</th>
				<th colspan="2">Calificacion</th>
				<th>Puesto</th>
			    </tr>
			</thead>
			<tbody>
			';
		foreach ($result['rows'] as $resultado) {
			error_log(json_encode($resultado));
			$numero++;
			$bg=$this->getBackground($numero);
            $logo=$this->club->getLogoName("NombreClub",$resultado['NombreClub']);
			echo '
				<tr id="Resultado_'.$numero.'" style="background:'.$bg.'">
					<td class="vwc_Entry vwc_logo">
						<!-- trick to insert a resizeable image: use div+bgimage instead of img tag -->
						<div style="height=100%;
									position:relative;
									background:url(\'/agility/images/logos/'.$logo.'\')no-repeat;
									background-size:contain;
									background-position:center;
									font-size:400%">&nbsp;</div>
					</td>
					<td colspan="2" class="vwc_Entry vwc_nombre">'.$resultado['Nombre'].'</td>
					<td class="vwc_Entry vwc_Datos">
						Dorsal: '.$resultado['Dorsal'].'<br />
						Lic. : '.$resultado['Licencia'].'<br />
						Grado: '.$resultado['Grado'].'<br />	
						Cat. : '.$resultado['Categoria'].'		
					</td>
					<td class="vwc_Entry vwc_GuiaClub">
						Gu&iacute;a: '.$resultado['NombreGuia'].'<br />
						Club: '.$resultado['NombreClub'].'<br />
					</td>
					<td class="vwc_Entry vwc_ftr">'.$resultado['Faltas'].'</td>
					<td class="vwc_Entry vwc_ftr">'.$resultado['Tocados'].'</td>
					<td class="vwc_Entry vwc_ftr">'.$resultado['Rehuses'].'</td>
					<td class="vwc_Entry vwc_rlarge">'.number_format($resultado['Tiempo'],2).'</td>
					<td class="vwc_Entry vwc_vel">'.number_format($resultado['Velocidad'],1).'</td>
					<td class="vwc_Entry vwc_rlarge">'.number_format($resultado['Penalizacion'],2).'</td>
					<td colspan="2" class="vwc_Entry vwc_calif">'.$resultado['Calificacion'].'</td>
					<td class="vwc_Entry vwc_puesto">'.$resultado['Puesto'].'</td>
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
                $logo=$this->club->getLogoName("Clubes",$i['Club']);
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
        $this->generateHeaderInfo();
        if ($this->manga==null) {
            $this->generateTandaInfo();
            return 0; // orden de salida has no sense when no manga active
        }
		$osmgr=new OrdenSalida("Orden de salida",$this->mangaid);
		$result = $osmgr->getData()['rows']; // obtiene los primeros perros pendientes
		$numero=0;
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
            $logo=$this->club->getLogoName("NombreClub",$participante['NombreClub']);
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

    function videowall_infodata() {
        $res= array(
            'Prueba' => $this->prueba,
            'Jornada' => $this->jornada,
            'Manga' => ($this->manga==null)? array() : $this->manga,
            'Tanda' => ($this->tanda==null)? array() : $this->tanda,
            'Club' => $this->club // club organizador
        );
        echo json_encode($res);
    }

} 

$sesion = http_request("Session","i",0);
$operacion = http_request("Operation","s",null);
$pendientes = http_request("Pendientes","i",10);
// on session==0, use this elements as IDentifiers
$prueba = http_request("Prueba","i",0);
$jornada = http_request("Jornada","i",0);
$manga = http_request("Manga","i",0);
$tanda = http_request("Tanda","i",0); // used on access from videowall
$mode = http_request("Mode","i",0); // used on access from public

$vw=new VideoWall($sesion,$prueba,$jornada,$manga,$tanda,$mode);
try {
    if($operacion==="infodata") return $vw->videowall_infodata();
	if($operacion==="livestream") return $vw->videowall_livestream();
	if($operacion==="llamada") return $vw->videowall_llamada($pendientes);
	if($operacion==="resultados") return $vw->videowall_resultados();
	if($operacion==="inscripciones") return $vw->videowall_inscripciones();
	if($operacion==="ordensalida") return $vw->videowall_ordensalida();
} catch (Exception $e) {
	echo "<p>Error:<br />".$e->getMessage()."</p>";
    return 0;
}
