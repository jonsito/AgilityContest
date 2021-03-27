<?php
/*
print_common.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__."/../auth/AuthManager.php");
require_once(__DIR__.'/../modules/Federations.php');
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/classes/PrintEstadisticas.php');

class PrintCommon extends FPDF {

	protected $myLogger;
	protected $config;
    protected $federation;
    protected $competition;
	protected $prueba; // datos de la prueba
	protected $club;   // club orcanizadod
	protected $icon;   // logo del club organizador
	protected $icon2;   // logo de la federacion
	protected $strClub; // _('Country') or _('Club') according federation
	protected $jornada; // datos de la jornada
	protected $myDBObject;
	protected $comments; // comentario adicional en la cabecera de pagina
	protected $fileName; // name of file to be printed
    protected $authManager;
	protected $regInfo; // registration info from current license
	protected $timeResolution; // number of decimal numbers to show in time results
	protected $angle; // current text rotation angle ( for FPDF::Rotate() function )
	protected $myStats; // pointer to statistics composer class

	protected $centro;
	protected $useUTF8=false;
	protected $useLongNames=false;
	protected $myFontName="Helvetica";
	protected $errormsg;

	function set_FileName($fname="output.pdf") { $this->fileName=$fname;}
	function get_FileName(){return $this->fileName;}

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
	}

	function _endpage()	{
		if ( ($this->regInfo===null) || ($this->regInfo['Serial']==="00000000") ) {
			$img=getIconPath(0,'unregistered.png');
			$mx=190;$my=270;
			if ($this->DefOrientation=='L') {$mx=270;$my=190;}
			for($x=10;$x<$mx;$x+=30) {
				for ($y=20;$y<$my;$y+=30) {
					$this->Rotate(60,$x,$y);
					$this->Image($img,$x,$y,32,20);
					$this->Rotate(0);
				}
			}
		}
		if($this->angle!=0)	{
			$this->angle=0;
			$this->_out('Q');
		}
		parent::_endpage();
	}
	/* end text rotation patch */

	protected function getFontName() { return $this->myFontName; }

	function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
		if (is_null($txt)) $txt="";
		if (is_numeric($txt)) $txt=strval($txt);
		if (is_string($txt)===FALSE) { return; } // Cell is only valid with strings
		// convert to iso-latin1 from html
		// special handling of some entities
        $txt=str_replace("&asymp;","± ",$txt);
		$txt=html_entity_decode($txt);
		if (!$this->useUTF8) $txt=utf8_decode($txt);
		// let string fit into box
		for($n=strlen($txt);$n>0;$n--) {
			$str=mb_substr($txt,0,$n);
			$sw=$this->GetStringWidth($str);
			if ($sw>=($w-(1.5))) continue;
			$txt=$str;
			break;
		}
		// and finally call real parent Cell function
		parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
	}

	function SetFont($family,$style='',$size=0) {
		// not sure why, but seems that UTF fonts are bigger than latin1 fonts
		// so analyze and reduce size when required
		switch (strtolower($family)) { // alllow any combination of upper/lower case
			case "dejavu": if ($size>1) $size-=0.5; // no break;
			case "free": if ($size>0) $size-=0.5; break;
		}
		parent::SetFont($family,$style,$size);
	}

	function SetFontSize($size) {
		// not sure why, but seems that UTF fonts are bigger than latin1 fonts
		// so analyze and reduce size when required
		switch (strtolower($this->FontFamily)) { // fpdf stores font family in lowercase
			case "dejavu": if ($size>1) $size-=0.5; // no break;
			case "free": if ($size>0) $size-=0.5; break;
		}
		parent::SetFontSize($size);
	}

	function installFonts($font) {
		$this->useUTF8=true;
		switch($font) {
			case 'Courier':
			case 'Helvetica':
			case 'Times': $this->useUTF8=false;
				break;
			case 'DejaVu':
			case 'Free':
				$this->AddFont($font,'',$font.'Sans.ttf',true);
				$this->AddFont($font,'B',$font.'Sans-Bold.ttf',true);
				$this->AddFont($font,'I',$font.'Sans-Oblique.ttf',true);
				$this->AddFont($font,'BI',$font.'Sans-BoldOblique.ttf',true);
				break;
			default:
				$this->myLogger->error("Invalid font name: $font. Using Helvetica");
				$font="Helvetica";
				break;
		}
		$this->myFontName=$font;
	}

	// function to handle in-memory images ( raw )
	// not really used, just for reference
	// http://www.fpdf.org/en/script/script45.php
	function MemImage($data, $x=null, $y=null, $w=0, $h=0, $link='') {
		// Display the image contained in $data
		$v = 'img'.md5($data);
		$GLOBALS[$v] = $data;
		$a = getimagesize('var://'.$v);
		if(!$a)
			$this->Error('Invalid image data');
		$type = substr(strstr($a['mime'],'/'),1);
		$this->Image('var://'.$v, $x, $y, $w, $h, $type, $link);
		unset($GLOBALS[$v]);
	}

	// function to handle in-memory images (GD library)
	// not really used, just for reference
	// http://www.fpdf.org/en/script/script45.php
	function GDImage($im, $x=null, $y=null, $w=0, $h=0, $link='') {
		// Display the GD image associated with $im
		ob_start();
		imagepng($im);
		$data = ob_get_clean();
		$this->MemImage($data, $x, $y, $w, $h, $link);
	}

	/**
	 * Add ( or not ) handler category to handler's name according configuration
	 * probably need some improvements (i18n and so), but ok for now
	 * @param $row
	 */
	function getHandlerName($row) {
		$ch=$this->config->getEnv('pdf_cathandlers');
		if ($ch==0)  return $row['NombreGuia'];
		if (!array_key_exists('CatGuia',$row)) return $row['NombreGuia'];
		if ($row['CatGuia']=='A') return $row['NombreGuia'];
		if ($row['CatGuia']=='-') return $row['NombreGuia']; // categoria sin asignar
		return "{$row['NombreGuia']} ({$row['CatGuia']})";
	}

	/**
	 * Set icons properly according competition
	 * @param $fedobj Federation object
	 * @param $jobj Jornada Object
	 */
	function handleLogos($fedobj,$jobj) {
		$fedName=$fedobj->get('Name');
		$serial=$this->regInfo['Serial'];
		// no valid or null license: use defaults
		if ( ($this->regInfo===null) || ($serial==="00000000") ) {
			$fedName=$this->federation->get('Name');
			$this->icon=getIconPath($fedName,"agilitycontest.png");
			$this->icon2=getIconPath($fedName,"null.png");
			return;
		}
		// try to extract logo from license
		$data=$this->authManager->getLicenseLogo();
		if ($data!=null) {
			// base 64 decode file
			$logo=base64_decode($data);
			// use memory stream to store_it
			$v = "img_{$serial}-".md5($logo).".png"; // add md5 sum to name to handle caching
			$GLOBALS[$v] = $logo;
			// and setup logo name
			$this->icon="var://{$v}";
		}
		// on no image from license or privileged licenses select logo in "old style"
		if ( ($data==null) || ($serial==="00000001") ) {
			$this->icon=getIconPath($fedName,$fedobj->get('OrganizerLogo')); // default: organizer logo
			// si la prueba no es internacional se usa el logo del club
			if ( (!$fedobj->isInternational()) && isset($this->club) ) {
				$this->icon=getIconPath($fedName,$this->club->Logo);
			}
		}

		// phase 3: federation logo

		// default: use defined federation logo
		$this->icon2=getIconPath($fedName,$fedobj->get('Logo'));
		if ($this->icon==$this->icon2) $this->icon2=getIconPath($fedName,$fedobj->get('ParentLogo'));
		// on international contest, use parent logo
		if ( $fedobj->isInternational())   {
			$this->icon2=getIconPath($fedName,$this->federation->get('ParentLogo'));
		}
		// no journey -> no fedlogo
		if (!$jobj) {
			$this->icon2=getIconPath($fedName,"null.png");
			return;
		}
		if (!$this->competition) { // should be set, but check anyway
			$this->competition=Competitions::getCompetition($this->prueba,$this->jornada);
		}
		// on KO events use AgilityContest Logo instead of federation logo
		if ($jobj && $jobj->KO!=0) {
			$this->icon2=getIconPath($fedName,"agilitycontest.png");
		}
		// if competition mode states no logo, obbey them
		if ( ! $this->competition->isFederationLogoAllowed()) {
			$this->icon2=getIconPath($fedName,"null.png");
		}
	}

	/**
	 * Constructor de la superclase 
	 * @param {string} orientacion 'landscape' o 'portrait'
     * @param {string} file name of caller to be used in traces
	 * @param {int|object} $prueba ID de la jornada
	 * @param {int|object} $jornada Jornada ID
	 * @param {string} comentarios String to be added at header
	 */
	function __construct($orientacion,$file,$prueba,$jornada=0,$comentarios="") {
		date_default_timezone_set('Europe/Madrid');
        parent::__construct($orientacion,'mm','A4'); // Portrait or Landscape
		// register in-memory images streams
		if (!in_array('var',stream_get_wrappers())) {
			stream_wrapper_register('var', 'VariableStream');
		}
		$this->config=Config::getInstance();
		$this->myLogger= new Logger($file,$this->config->getEnv("debug_level"));
		$this->SetAutoPageBreak(true,1.7); // default margin is 2cm. so enlarge a bit
		$this->installFonts($this->config->getEnv("pdf_fontfamily"));
		$this->centro=($orientacion==='Portrait')?107:145;
		$this->myDBObject=new DBObject($file);
		$this->prueba=null;
		$this->club=null;
		$this->federation=Federations::getFederation(0); // defaults to RSCE
		if (is_numeric($prueba) && ($prueba!=0)) {
			$this->prueba=$this->myDBObject->__getObject("pruebas",$prueba);
		}
		if (is_object($prueba)) $this->prueba=$prueba;
		$this->club=$this->myDBObject->__getObject("clubes",$this->prueba->Club); // club organizador
		$this->federation=Federations::getFederation(intval($this->prueba->RSCE));
		$this->strClub=($this->federation->isInternational())?_('Country'):_('Club');
		$this->useLongNames=false;
		$this->jornada=null;
		if (is_numeric($jornada) && ($jornada!=0) ) {
			$this->jornada=$this->myDBObject->__getObject("jornadas",$jornada);
		}
		if (is_object($jornada)) {
			$this->jornada=$jornada;
		}
		if ($this->jornada!=null) {
			$this->competition=Competitions::getCompetition($this->prueba,$this->jornada);
			$this->useLongNames=$this->competition->useLongNames();
		}
		$this->authManager=AuthManager::getInstance("print_common");
		$this->regInfo=$this->authManager->getRegistrationInfo();
		$this->handleLogos($this->federation,$this->jornada);
		// evaluate number of decimals to show when printing timestamps
		$this->timeResolution=($this->config->getEnv('crono_milliseconds')=="0")?2:3;
		// $this->myLogger->trace("Time resolution is ".$this->timeResolution);
		$this->comments=$comentarios;
		$this->myStats=new PrintEstadisticas($this);
	}

	// return the minimum and maximum nomber of dogs for team on this journey
	function getMinDogs() {
        return Jornadas::getTeamDogs($this->jornada)[0]; // get mindogs
	}
    function getMaxDogs() {
        return Jornadas::getTeamDogs($this->jornada)[1]; // get maxdogs
    }

    // several functions to stringify modes cat and grades
	function getCatString($cat,$heights=0) {
		if ($heights==3 && $cat=='X') $cat='L';
		if ($heights==3 && $cat=='T') $cat='S'; // join XL-L and S-XS in 3heihgts
		if ($heights==4 && $cat=='X') $cat='L'; // join XL-L in 4 heights
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
        $row=$this->myDBObject->__selectObject("Logo","perros,guias,clubes","(perros.Guia=guias.ID ) AND (guias.Club=clubes.ID) AND (perros.ID=$id)");
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
		$this->myLogger->enter();
		// pintamos Logo del club organizador a la izquierda y logo de la canina a la derecha
		// recordatorio
		// 		$this->Image(string file [, float x [, float y [, float w [, float h [, string type [, mixed link]]]]]])
		// 		$this->Cell( width, height, data, borders, where, align, fill)
		// 		los logos tienen 150x150, que a 300 dpi salen aprox a 2.54 cmts
		$this->SetXY(10,10); // margins are 10mm each
		$this->Image($this->icon,$this->GetX(),$this->GetY(),25.4,25.4);
		$this->SetXY($this->w - 35.4,10);
		$this->Image($this->icon2,$this->GetX(),$this->GetY(),25.4,25.4);

		// pintamos nombre de la prueba
		$this->SetXY($this->centro -65,10);
		$this->SetFont($this->getFontName(),'BI',10); // bold italic 10
        if (intval($this->prueba->ID)>1) { // solo apuntamos nombre de la prueba si no es la prueba por defecto
            $str=$this->prueba->Nombre." - ".$this->club->Nombre;
            $this->Cell(130,10,$str,0,0,'C',false);// Nombre de la prueba centrado
        }
		$this->Ln(); // Salto de línea

		// pintamos el titulo en un recuadro
		$this->SetFont($this->getFontName(),'B',20); // bold 20
		$this->SetXY($this->centro -65,20);
		$this->Cell(130,10,$title,1,0,'C',false);// Titulo del listado en el centro
        // si la jornada esta definida, debajo del recuadro imprimimos modalidad de competicion
		$this->Ln(10);
		if ($this->jornada!==null) {
            $this->SetFont($this->getFontName(),'I',8); // bold 20
            $this->SetXY($this->centro -65,30);
            $cname=Competitions::getCompetition($this->prueba,$this->jornada)->getPDFCompetitionName();
            $this->Cell(130,5,$cname,0,0,'C',false);// Titulo del listado en el centro
		}
		$this->Ln(5);

		//$this->myLogger->leave();
	}
	
	// Pie de página
	function print_commonFooter() {
		// Posición: a 1,5 cm del final
		$this->SetY(-15);
		// copyright
		$ver=$this->config->getEnv("version_name");
		$this->SetFont($this->getFontName(),'I',6);
		$this->Cell(60,10,"AgilityContest-$ver Copyright 2013-2015 by J.A.M.C.",0,0,'L');
		// Número de página
		$this->Cell(40,10,_('Date').': '.date("Y/m/d H:i:s"),0,0,'C');
		$this->SetFont($this->getFontName(),'IB',8);
		$this->Cell(30,10,_('Page').' '.$this->PageNo().' / {nb}',0,0,'C');
		// informacion de registro
		$ri=$this->authManager->getRegistrationInfo();
		$this->SetFont($this->getFontName(),'I',6);
		$this->Cell(60,10,_("This copy is licensed to club").": {$ri['Club']}",0,0,'R');
	}

	// Identificacion de la Manga
	function print_identificacionManga($manga,$categoria) {
		// pintamos "identificacion de la manga"
		$this->SetFont($this->getFontName(),'B',12); // bold 15
		$str  = $this->jornada->Nombre . " - " . $this->jornada->Fecha;
		$tmanga= _(Mangas::getTipoManga($manga->Tipo,1,$this->federation));
		// JAMC agosto 2020: on Grade 1 RSCE, add agility or jumping according "Observaciones"
		if ( ($manga->Grado=="GI") && ($this->federation->get('Name')=='RSCE') ) {
			if ($manga->Observaciones!=="")	$tmanga .= " ({$manga->Observaciones})";
			else if (isMangaAgility($manga->Tipo)) $tmanga .= " (Agility)";
			else if (isMangaJumping($manga->Tipo)) $tmanga .= " (Jumping)";
		}
        $str2=($categoria==="")? "$tmanga":"$tmanga - $categoria";

		if ($this->comments==="") {
            $this->Cell(90,9,$str,0,0,'L',false); // a un lado nombre y fecha de la jornada
            $this->Cell(100,9,$str2,0,0,'R',false); // al otro lado tipo y categoria de la manga
		} else {
            $this->Cell(60,9,$str,0,0,'L',false); // a un lado nombre y fecha de la jornada
            $this->Cell(60,9,$this->comments,0,0,'C',false); // en el centro texto auxiliar
            $this->Cell(70,9,$str2,0,0,'R',false); // al otro lado tipo y categoria de la manga
		}
		$this->Ln(9);
	}
	
	function ac_Cell($x,$y,$w,$h,$txt,$border,$align,$fill) {
		$this->setXY($x,$y);
		$this->Cell($w,$h,$txt,$border,0,$align,$fill);
	}
	
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
		$this->SetFont($this->getFontName(),'B',$size);
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
	
	function ac_row($idx,$size,$style='') {
		$bg=$this->config->getEnv('pdf_rowcolor1');
		if ( ($idx&0x01)==1)$bg=$this->config->getEnv('pdf_rowcolor2');
		$this->SetFont($this->getFontName(),$style,$size);
		$this->ac_SetFillColor($bg); // color de la fila
		$this->ac_SetTextColor('#000000'); // negro
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
		$this->SetLineWidth(.3); // ancho de linea
	}
}
?>
