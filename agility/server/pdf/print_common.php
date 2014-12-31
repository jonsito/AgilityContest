<?php
/*
print_common.php

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


define('FPDF_FONTPATH', __DIR__."/font/");
require_once(__DIR__."/fpdf.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../auth/Config.php");
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Clubes.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jueces.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/Resultados.php');
require_once(__DIR__.'/../database/classes/Clasificaciones.php');
require_once(__DIR__."/print_common.php");

class PrintCommon extends FPDF {
	
	protected $myLogger;
	protected $config;
	protected $prueba; // datos de la prueba
	protected $club;   // club orcanizadod
	protected $icon;   // logo del club organizador
	protected $jornada; // datos de la jornada
	protected $myDBObject;
	protected $pageName; // name of file to be printed

	protected $centro;
	/**
	 * Constructor de la superclase 
	 * @param unknown $prueba ID de la prueba
	 * @param unknown $jornada ID de la jornada
	 * @param unknown $mangas array[{integer} con los IDs de las mangas
	 */
	function __construct($orientacion,$prueba,$jornada=0) {
		parent::__construct($orientacion,'mm','A4'); // Portrait or Landscape
		$this->centro=($orientacion==='Portrait')?107:145;
		$this->myLogger= new Logger("PrintCommon");
		$this->config=new Config();
		$this->myDBObject=new DBObject("print_common_pdf");
		$this->prueba=$this->myDBObject->__getObject("Pruebas",$prueba);
		$this->club=$this->myDBObject->__getObject("Clubes",$this->prueba->Club); // club organizador
		if ($jornada!=0) $this->jornada=$this->myDBObject->__getObject("Jornadas",$jornada);
		else $this->jornada=null;
		// evaluage logo info
		$this->icon="rsce.png";
		if (isset($this->club)) $this->icon=$this->club->Logo;
	}
	/**
	 * Pinta la cabecera de pagina
	 * @param {string} $title Titulo a imprimir en el cajetin
	 */
	function print_commonHeader($title) {
		$this->myLogger->enter();
		// pintamos Logo del club organizador a la izquierda y logo de la canina a la derecha
		// recordatorio
		// 		$this->Image(string file [, float x [, float y [, float w [, float h [, string type [, mixed link]]]]]])
		// 		$this->Cell( width, height, data, borders, where, align, fill)
		// 		los logos tienen 150x150, que a 300 dpi salen aprox a 2.54 cmts
		$icon2=($this->icon==="rsce.png")?"fci.png":"rsce.png"; // to avoid duplicate head logos
		$this->SetXY(10,10); // margins are 10mm each
		$this->Cell(25.4,25.4,$this->Image(__DIR__.'/../../images/logos/'.$this->icon,$this->getX(),$this->getY(),25.4),0,0,'L',false);
		$this->SetXY($this->w - 35.4,10);
		$this->Cell(25.4,25.4,$this->Image(__DIR__.'/../../images/logos/'.$icon2,$this->getX(),$this->getY(),25.4),0,0,'R',false);
	
		// pintamos nombre de la prueba
		$this->SetXY($this->centro -50,10);
		$this->SetFont('Arial','BI',10); // Arial bold italic 10
		$this->Cell(100,10,$this->prueba->Nombre,0,0,'C',false);// Nombre de la prueba centrado 
		$this->Ln(); // Salto de línea
		
		// pintamos el titulo en un recuadro
		$this->SetFont('Arial','B',20); // Arial bold 20
		$this->SetXY($this->centro -50,20);
		$this->Cell(100,10,$title,1,0,'C',false);// Nombre de la prueba centrado
		$this->Ln(15); // Salto de línea
		$this->myLogger->leave();
	}
		
	// Pie de página
	function print_commonFooter() {
		$this->myLogger->enter();
		// Posición: a 1,5 cm del final
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('Arial','I',8);
		// Número de página
		$this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
		$this->myLogger->leave();
	}

	// Identificacion de la Manga
	function print_identificacionManga($manga,$categoria) {
		// pintamos "identificacion de la manga"
		$this->SetFont('Arial','B',12); // Arial bold 15
		$str  = $this->jornada->Nombre . " - " . $this->jornada->Fecha;
		$tmanga= Mangas::$tipo_manga[$manga->Tipo][1];
		$str2="$tmanga - $categoria";
		$this->Cell(90,10,$str,0,0,'L',false); // a un lado nombre y fecha de la jornada
		$this->Cell(90,10,$str2,0,0,'R',false); // al otro lado tipo y categoria de la manga
		$this->Ln(10);
	}
	
	function setPageName($name) {$this->pageName=$name; }
	function getPageName(){ return $this->pageName; }
	
	function ac_SetFillColor($str) {
		$val=intval(str_replace('#','0x',$str),0);
		$r=(0x00FF0000&$val)>>16;
		$g=(0x0000FF00&$val)>>8;
		$b=(0x000000FF&$val);
		// $this->myLogger->info("fill color str:$str val:$val R:$r G:$g B:$b");
		$this->SetFillColor($r,$g,$b);
	}
	function ac_SetTextColor($str) {
		$val=intval(str_replace('#','0x',$str),0);
		$r=(0x00FF0000&$val)>>16;
		$g=(0x0000FF00&$val)>>8;
		$b=(0x000000FF&$val);
		// $this->myLogger->info("text color str:$str R:$r G:$g B:$b");
		$this->SetTextColor($r,$g,$b);
	}
	function ac_SetDrawColor($str) {
		$val=intval(str_replace('#','0x',$str),0);
		$r=(0x00FF0000&$val)>>16;
		$g=(0x0000FF00&$val)>>8;
		$b=(0x000000FF&$val);
		// $this->myLogger->info("draw color str:$str R:$r G:$g B:$b");
		$this->SetDrawColor($r,$g,$b);
	}
	
	function ac_header($idx,$size) {
		$this->SetFont('Arial','B',$size);
		if($idx==1) {
			$this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg1')); // naranja
			$this->ac_SetTextColor($this->config->getEnv('pdf_hdrfg1')); // negro
		} else {
			$this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg2')); // azul
			$this->ac_SetTextColor($this->config->getEnv('pdf_hdrfg2')); // blanco
		}
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
		$this->SetLineWidth(.3); // ancho de linea
	}
	
	function ac_row($idx,$size) {
		$bg=$this->config->getEnv('pdf_rowcolor1');
		if ( ($idx&0x01)==1)$bg=$this->config->getEnv('pdf_rowcolor2');
		$this->SetFont('Arial','B',$size);
		$this->ac_SetFillColor($bg); // color de la fila
		$this->ac_SetTextColor('#000000'); // negro
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
		$this->SetLineWidth(.3); // ancho de linea
	}
}
?>
