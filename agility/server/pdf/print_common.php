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
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../auth/Config.php");
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__."/fpdf.php");
require_once(__DIR__.'/../../modules/Federations.php');
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
    protected $federation;
	protected $prueba; // datos de la prueba
	protected $club;   // club orcanizadod
	protected $icon;   // logo del club organizador
	protected $icon2;   // logo de la federacion
	protected $strClub; // _('Country') or _('Club') according federation
	protected $jornada; // datos de la jornada
	protected $myDBObject;
	protected $pageName; // name of file to be printed
    protected $authManager;
	protected $regInfo; // registration info from current license
	protected $timeResolution; // number of decimal numbers to show in time results
	protected $angle; // current text rotation angle ( for FPDF::Rotate() function )

	protected $centro;

	/* from http://www.fpdf.org/en/script/script2.php */
	function Rotate($angle,$x=-1,$y=-1)	{
		if($x==-1)	$x=$this->x;
		if($y==-1)	$y=$this->y;
		if($this->angle!=0)	$this->_out('Q');
		$this->angle=$angle;
		if($angle!=0) {
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;
			$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
		}
	}

	function AddPage($orientation='', $size='', $rotation=0) {
		parent::AddPage($orientation,$size,$rotation);
		if ( ($this->regInfo==null) || ($this->regInfo['Serial']==="00000000") ) {
			$img=getIconPath(0,'unregistered.png');
			$mx=190;$my=270;
			if($orientation=='') $orientation = $this->DefOrientation;
			else $orientation = strtoupper($orientation[0]);
			if ($orientation=='L') {$mx=270;$my=190;}
			for($x=10;$x<$mx;$x+=30) {
				for ($y=20;$y<$my;$y+=30) {
					$this->Rotate(60,$x,$y);
					$this->Image($img,$x,$y,32,20);
					$this->Rotate(0);
				}
			}
		}
	}

	function _endpage()	{
		if($this->angle!=0)	{
			$this->angle=0;
			$this->_out('Q');
		}
		parent::_endpage();
	}
	/* end text rotation patch */

	function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
		if (is_null($txt)) $txt="";
		if (is_numeric($txt)) $txt=strval($txt);
		if (is_string($txt)===FALSE) { return; } // Cell is only valid with strings
		// convert to iso-latin1 from html
		// special handling of &asymp; entity
		$txt=str_replace("&asymp;","± ",$txt);
		$txt=utf8_decode(html_entity_decode($txt));
		// let string fit into box
		for($n=strlen($txt);$n>0;$n--) {
			$str=substr($txt,0,$n);
			$sw=$this->GetStringWidth($str);
			if ($sw>=($w-(1.5))) continue;
			$txt=$str;
			break;
		}
		// and finally call real parent Cell function
		parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
	}

	/**
	 * Constructor de la superclase 
	 * @param {string} orientacion 'landscape' o 'portrait'
     * @param {string} file name of caller to be used in traces
	 * @param {int} $prueba ID de la jornada
	 * @param {int} jornada Jornada ID
	 */
	function __construct($orientacion,$file,$prueba,$jornada=0) {
		date_default_timezone_set('Europe/Madrid');
		parent::__construct($orientacion,'mm','A4'); // Portrait or Landscape
		$this->SetAutoPageBreak(true,1.7); // default margin is 2cm. so enlarge a bit 
		$this->centro=($orientacion==='Portrait')?107:145;
		$this->config=Config::getInstance();
		$this->myLogger= new Logger($file,$this->config->getEnv("debug_level"));
		$this->myDBObject=new DBObject($file);
		$this->prueba=null;
		$this->federation=Federations::getFederation(0); // defaults to RSCE
		if ($prueba!=0) {
			$this->prueba=$this->myDBObject->__getObject("Pruebas",$prueba);
			$this->federation=Federations::getFederation(intval($this->prueba->RSCE));
		}
		$this->strClub=($this->federation->isInternational())?_('Country'):_('Club');
        $fedName=$this->federation->get('Name');
		// $this->myLogger->trace("Federation is: ".json_decode($this->federation));
		$this->club=null;
		if ($prueba!=0){
			$this->club=$this->myDBObject->__getObject("Clubes",$this->prueba->Club); // club organizador
		}
		$this->jornada=null;
		if ($jornada!=0) {
			$this->jornada=$this->myDBObject->__getObject("Jornadas",$jornada);
		}
		// on international contests, use logos from federation
        $this->icon=getIconPath($fedName,$this->federation->get('OrganizerLogo'));
        $this->icon2=getIconPath($fedName,$this->federation->get('ParentLogo'));
		// on national events, use organizing club logo (if any )
		if ( (!$this->federation->isInternational())  && isset($this->club) ) {
            $this->icon=getIconPath($fedName,$this->club->Logo);
            $this->icon2=getIconPath($fedName,$this->federation->get('Logo'));
            if ($this->icon==$this->icon2) $this->icon2=getIconPath($fedName,$this->federation->get('ParentLogo'));
        }
		// handle registration info related to PDF generation
        $this->authManager=new AuthManager("print_common");
        $this->regInfo=$this->authManager->getRegistrationInfo();
        if ( ($this->regInfo==null) || ($this->regInfo['Serial']==="00000000") ) {
			$this->icon=getIconPath($fedName,"agilitycontest.png");;
		}
		// evaluate number of decimals to show when printing timestamps
		$this->timeResolution=($this->config->getEnv('crono_miliseconds')=="0")?2:3;
		// $this->myLogger->trace("Time resolution is ".$this->timeResolution);
	}


	function getCatString($cat) {
		$catstr=$this->federation->get('IndexedModeStrings');
		return $catstr[$cat];
	}

	function getModeString($mode) {
		$modestr=$this->federation->get('IndexedModes');
		return $modestr[$mode];
	}
	function getGradoString($tipo) {
		$r=Mangas::$tipo_manga[$tipo][2]; // obtenemos la abreviatura del grado
		$grstr=$this->federation->get('ListaGrados');
		return $grstr[$r];
	}

    /**
     * Gets the club logo based on Dog ID
     * @param {integer} $id Dog ID
     * @return {string} name of desired logo
     */
    function getLogoName($id) {
        $row=$this->myDBObject->__selectObject("Logo","Perros,Guias,Clubes","(Perros.Guia=Guias.ID ) AND (Guias.Club=Clubes.ID) AND (Perros.ID=$id)");
        if (!$row) {
			$this->myLogger->error("getLogoName(): no associated guia/club for Dog ID: $id");
			return getIconPath($this->federation->get('Name'),$this->icon);
		}
		return getIconPath($this->federation->get('Name'),$row->Logo);
    }

    /**
	 * Pinta la cabecera de pagina
	 * @param {string} $title Titulo a imprimir en el cajetin
	 */
	function print_commonHeader($title) {
		// $this->myLogger->enter();
		// pintamos Logo del club organizador a la izquierda y logo de la canina a la derecha
		// recordatorio
		// 		$this->Image(string file [, float x [, float y [, float w [, float h [, string type [, mixed link]]]]]])
		// 		$this->Cell( width, height, data, borders, where, align, fill)
		// 		los logos tienen 150x150, que a 300 dpi salen aprox a 2.54 cmts
		$this->SetXY(10,10); // margins are 10mm each
		$this->Image($this->icon,$this->GetX(),$this->GetY(),25.4);
		$this->SetXY($this->w - 35.4,10);
		$this->Image($this->icon2,$this->GetX(),$this->GetY(),25.4);
	
		// pintamos nombre de la prueba
		$this->SetXY($this->centro -50,10);
		$this->SetFont('Helvetica','BI',10); // Helvetica bold italic 10
        if (intval($this->prueba->ID)>1) { // solo apuntamos nombre de la prueba si no es la prueba por defecto
            $str=$this->prueba->Nombre." - ".$this->club->Nombre;
            $this->Cell(100,10,$str,0,0,'C',false);// Nombre de la prueba centrado
        }
		$this->Ln(); // Salto de línea
		
		// pintamos el titulo en un recuadro
		$this->SetFont('Helvetica','B',20); // Helvetica bold 20
		$this->SetXY($this->centro -60,20);
		$this->Cell(120,10,$title,1,0,'C',false);// Nombre de la prueba centrado
		$this->Ln(15); // Salto de línea
		// $this->myLogger->leave();
	}
	
	// Pie de página
	function print_commonFooter() {
		// Posición: a 1,5 cm del final
		$this->SetY(-15);
		// copyright
		$ver=$this->config->getEnv("version_name");
		$this->SetFont('Helvetica','I',6);
		$this->Cell(60,10,"AgilityContest-$ver Copyright 2013-2015 by J.A.M.C.",0,0,'L');
		// Número de página
		$this->Cell(50,10,_('Date').': '.date("Y/m/d H:i:s"),0,0,'C');
		$this->SetFont('Helvetica','IB',8);
		$this->Cell(20,10,_('Page').' '.$this->PageNo().'/{nb}',0,0,'C');
		// informacion de registro
		$ri=$this->authManager->getRegistrationInfo();
		$this->SetFont('Helvetica','I',6);
		$this->Cell(60,10,_("This copy is licensed to club").": {$ri['Club']}",0,0,'R');
	}

	// Identificacion de la Manga
	function print_identificacionManga($manga,$categoria) {
		// pintamos "identificacion de la manga"
		$this->SetFont('Helvetica','B',12); // Helvetica bold 15
		$str  = $this->jornada->Nombre . " - " . $this->jornada->Fecha;
		$tmanga= Mangas::$tipo_manga[$manga->Tipo][1];
		$str2 = "$tmanga - $categoria";
		$this->Cell(90,9,$str,0,0,'L',false); // a un lado nombre y fecha de la jornada
		$this->Cell(100,9,$str2,0,0,'R',false); // al otro lado tipo y categoria de la manga
		$this->Ln(9);
	}
	
	function ac_Cell($x,$y,$w,$h,$txt,$border,$align,$fill) {
		$this->setXY($x,$y);
		$this->Cell($w,$h,$txt,$border,0,$align,$fill);
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
		$this->SetFont('Helvetica','B',$size);
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
		$this->SetFont('Helvetica','',$size);
		$this->ac_SetFillColor($bg); // color de la fila
		$this->ac_SetTextColor('#000000'); // negro
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
		$this->SetLineWidth(.3); // ancho de linea
	}
}
?>