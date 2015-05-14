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
require_once(__DIR__ . "/../database/classes/Inscripciones.php");

class PublicWeb {
	protected $myLogger;
	protected $myDBObject;
	protected $prueba;
	protected $jornada;
	protected $manga;
	protected $tanda;
	protected $config;
	protected $mangaid;
	protected $mode;
	protected $club;

	function __construct($pruebaid,$jornadaid,$mangaid,$mode) {
		$this->config=Config::getInstance();
		$this->myLogger=new Logger("PublicWeb.php",$this->config->getEnv("debug_level"));
		$this->myDBObject=new Clubes("PublicWeb"); // also is a dbobject. used to retrieve logos
           // obtenemos los datos desde las variables recibidas por http
		$this->session=null;
		$this->sessionid=0;
		$this->prueba=$this->myDBObject->__getArray("Pruebas",$pruebaid);
		$this->jornada=$this->myDBObject->__getArray("Jornadas",$jornadaid);
        $this->manga=null;
        $this->mangaid=$mangaid;
        if ($mangaid!=0) $this->manga=$this->myDBObject->__getArray("Mangas",$mangaid);
		$this->mode=$mode;
        $this->club= $this->myDBObject->__getArray("Clubes",$this->prueba['Club']);
		$this->myLogger->info("prueba:{$this->prueba['ID']} jornada:{$this->jornada['ID']} manga:{$this->mangaid} mode:$mode");
	}


	public static $cat=array('-'=>'','L'=>'Large','M'=>'Medium','S'=>'Small','T'=>'Tiny');
	public static $modestr  
		=array("Large","Medium","Small","Medium+Small","Conjunta L/M/S","Tiny","Large+Medium","Small+Tiny","Conjunta L/M/S/T");

    function isTeam() {
        if (intval($this->jornada['Equipos3'])==1) return true;
        if (intval($this->jornada['Equipos4'])==1) return true;
        return false;
    }

	function getModeString($mode) {	return PublicWeb::$modestr[$mode]; }
	
	function getBackground($row) {
		return (($row%2)!=0)?$this->config->getEnv("vw_rowcolor1"):$this->config->getEnv("vw_rowcolor2");
	}
	
	function generateHeaderInfo() {
        $mangastr=($this->mangaid==0)?"":Mangas::$tipo_manga[$this->manga['Tipo']][1];
        $this->myLogger->trace("tipo manga: " . $this->manga['Tipo']);
        echo '<div id="pb_HiddenHeader"><form id="pb_HiddenForm">';
        echo '<input type="hidden" id="pb_LogoClub" value="/agility/images/logos/' . $this->club['Logo'] . '"/>';
        echo '<input type="hidden" id="pb_NombrePrueba" value="' . $this->prueba['Nombre'] . '"/>';
        echo '<input type="hidden" id="pb_NombreJornada" value="' . $this->jornada['Nombre'] . '"/>';
        echo '<input type="hidden" id="pb_NombreManga" value="'.$mangastr.'"/>';
        echo '</form></div>';
	}

	function generateTandaInfo() {
        $str=$this->tanda['Nombre'];
        echo '<table class="vwc_callEntry"><tr><td colspan="5" class="vwc_callTanda">'.$str.'</td></tr></table>';
    }

	function publicweb_resultados() {
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
			';
			return 0;
		}
		$resmgr=new Resultados("publicweb_resultados",$this->prueba['ID'], $this->mangaid );
		$myManga=$this->myDBObject->__getObject("Mangas",$this->mangaid);
		$mode=$this->mode;
		$mangastr=Mangas::$tipo_manga[$myManga->Tipo][1]." - ".PublicWeb::$modestr[$mode];
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
				<th colspan="5">Datos de los Participantes</th>
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
            $logo=$this->myDBObject->getLogoName("NombreClub",$resultado['NombreClub']);
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
	
	function publicweb_inscripciones() {
        $cobj= new Clubes("publicweb_inscripciones");
		$imgr=new Inscripciones("publicweb_inscripciones",$this->prueba['ID']);
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
                $logo=$cobj->getLogoName("Clubes",$i['Club']);
				// pintamos cabecera	
				echo '<tr><td colspan="6"><hr /></td></tr>';
				echo "<tr id=\"Club_$club\">";
				echo "<td colspan=\"1\" style=\"width:10%\" rowspan=\"2\">";
				echo '	<img src="/agility/images/logos/'.$logo.'" alt="'.$logo.'" width="75" height="75"/>';
				echo "</td>";
				echo '<td colspan="5" class="pb_club">'.$i['NombreClub'].'</td>';
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
			$c=PublicWeb::$cat[$i['Categoria']];
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
	
	function publicweb_ordensalida() {
		$lastCategoria="";
        $this->generateHeaderInfo();
        if ($this->mangaid==0) return ""; // no manga info yet, so don't return anything
		$osmgr=new OrdenSalida("public_ordensalida",$this->mangaid);
		$result = $osmgr->getData()['rows']; // obtiene los primeros perros pendientes
		$numero=0;
		echo '<table class="vwc_callEntry">';
		foreach ($result as $participante) {
			if ($lastCategoria!==$participante['Categoria']){
				$lastCategoria=$participante['Categoria'];
				$categ=PublicWeb::$cat[$lastCategoria];
                $mangastr=($this->mangaid==0)?"":Mangas::$tipo_manga[$this->manga['Tipo']][1];
				echo '<tr><td colspan="5" class="vwc_callEntry vwc_callTanda">---- '.$mangastr.' - '.$categ.' ----</td></tr>';
				$numero=0;
			}
			$numero++;
            $logo=$this->myDBObject->getLogoName("NombreClub",$participante['NombreClub']);
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

    function publicweb_programa() {
        $this->generateHeaderInfo();
        // obtenemos el programa de la jornada
        $o=new Tandas("publicweb_programa",$this->prueba['ID'],$this->jornada['ID']);
        $ot= $o->getTandas();
        $orden=$ot['rows'];
        // cabcecera de la tabla
        echo '<table width="100%" class="vwc_tresultados" ><thead style="font-size:1.4em;"><tr></tr>';
        echo "<th>&nbsp</th>"; // numero de orden
        echo "<th>Horario</th>";
        echo "<th>Actividad</th>";
        echo "<th>Ring</th>";
        echo "<th># participantes</th>";
        echo "<th>Comentarios</th>";
        echo "</tr></thead>";
        $rowcount=1;
        echo '<tbody>';
        foreach($orden as $row) {
            $bg=$this->getBackground($rowcount);
            echo '<tr id="actividad_'.$rowcount.'" style="background:'.$bg.';font-size:1.4em;height:30px">';
            echo '<td style="font-weight:bold;font-size:1.4em;">'.$rowcount.'</td>';          // imprimimos numero de orden
            echo '<td style="text-align:center">'.$row["Horario"].'</td>'; // comentarios de la tanda
            echo '<td style="text-align:right">'.$row["Nombre"].'</td>';   // imprimimos nombre de la tanda
            $ring=$row["NombreSesion"];
            if ($ring==='-- Sin asignar --') $ring="&nbsp;";
            echo '<td style="text-align:center;">'.$ring.'</td>'; // datos del ring
            // evaluamos (si es necesario) el numero de participantes
            if ($row['Tipo']!=0) {
                $str="( Prueba={$row['Prueba']} ) AND ( Jornada={$row['Jornada']} ) AND (Manga={$row['Manga']})";
                $result=$this->myDBObject->__select("*","Resultados",$str,"","");
                if (!is_array($result)) {
                    $this->myLogger->error($result); return $result; }
                // comparamos categoria y grado
                $count=0;
                foreach($result['rows'] as $item) {
                    // si el grado es '-' se contabiliza. else si coincide grado se contabiliza
                    if (($row['Grado']!=='-') && ($item['Grado']!==$row['Grado']) ) continue;
                    // comparamos categorias
                    if ( strstr($row['Categoria'],$item['Categoria'])===false ) continue;
                    $count++;
                }
                echo '<td style="text-align:center">'.$count.'</td>'; // datos del participacion
            } else {
                echo '<td style="text-align:center"> ---- </td>'; // no hay participantes
            }
            echo '<td style="text-align:left">'.$row["Comentario"].'</td>'; // comentarios de la tanda
            $rowcount++;
            echo '</tr>';
        }
        echo '</tbody></table>';
    }
} 

$operacion = http_request("Operation","s",null);
$pendientes = http_request("Pendientes","i",10);
// on session==0, use this elements as IDentifiers
$prueba = http_request("Prueba","i",0);
$jornada = http_request("Jornada","i",0);
$manga = http_request("Manga","i",0);
$mode = http_request("Mode","i",0); // used on access from public

$vw=new PublicWeb($prueba,$jornada,$manga,$mode);
try {
	if($operacion==="resultados") return $vw->publicweb_resultados();
	if($operacion==="inscripciones") return $vw->publicweb_inscripciones();
	if($operacion==="ordensalida") return $vw->publicweb_ordensalida();
    if($operacion==="clasificaciones") return $vw->publicweb_clasificaciones();
    if($operacion==="programa") return $vw->publicweb_programa();
} catch (Exception $e) {
	echo "<p>Error:<br />".$e->getMessage()."</p>";
    return 0;
}
