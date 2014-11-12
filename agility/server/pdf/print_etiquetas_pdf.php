<?php
/*
print_etiquetas_pdf.php

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


header('Set-Cookie: fileDownload=true; path=/');
// mandatory 'header' to be the first element to be echoed to stdout

/**
 * genera un CSV con los datos para las etiquetas
 */

require_once(__DIR__."/fpdf.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Clubes.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jueces.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/Resultados.php');
require_once(__DIR__.'/../database/classes/Clasificaciones.php');
require_once(__DIR__."/print_common.php");

class PDF extends FPDF {
	
	public $myLogger;
	protected $prueba;
	protected $club;
	protected $jornada;
	protected $manga1;
	protected $manga2;
	protected $resultados;
	protected $icon;
	
	 /** Constructor
	 * @param {obj} $manga datos de la manga
	 * @param {obj} $resultados resultados asociados a la manga/categoria pedidas
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$mangas,$resultados) {
		parent::__construct('Portrait','mm','A4');
		$this->myLogger= new Logger("print_etiquetas_pdf");
		$dbobj=new DBObject("print_etiquetas_pdf");
		$this->prueba=$dbobj->__getObject("Pruebas",$prueba);
		$this->club=$dbobj->__getObject("Clubes",$this->prueba->Club);
		$this->jornada=$dbobj->__getObject("Jornadas",$jornada);
		$this->manga1=$dbobj->__getObject("Mangas",$mangas[0]);
		$this->manga2=$dbobj->__getObject("Mangas",$mangas[1]);
		$this->resultados=$resultados;
		// evaluage logo info
		$this->icon="welpe.png";
		if (isset($this->club)) $this->icon=$this->club->Logo;
	}
	
	// No tenemos cabecera: no cabe
	function Header() {// pintamos una linea	
		$this->SetDrawColor(128,0,0); // line color
		$this->Line(10,10,175,10);	
		$this->SetDrawColor(128,128,128); // restore line color
	}
	
	// Pie de página: tampoco cabe
	function Footer() {
	}
	
	function writeCell($idx,$row) {
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		//dorsal (10,y,20,17)
		$y1=  10+17*$idx;
		$y5=  10+17*$idx+5;
		$y10= 10+17*$idx+10;
		$y8=  10+17*$idx+8;
		$ynext=10+17*($idx+1);
		
		$this->SetFont('Arial','B',24); // bold 11px
		$this->setXY(10,$y1);
		$this->Cell(20,17,$row['Dorsal'],0,0,'C',false);
		$this->SetFont('Arial','',12); // restore font size
		
		//logo   (30,y,15,15)
		// los logos tienen 150x150, que a 300 dpi salen aprox a 2.54 cmts
		$this->SetXY(30,$y1); // margins are 10mm each
		$this->Cell(17,17,$this->Image(__DIR__.'/../../images/logos/'.$this->icon,$this->getX(),$this->getY(),17),0,0,'L',false);
		
		//Nombre de la prueba (47,y,38,5) left
		$this->SetXY(47,$y1); 
		$this->Cell(38,5,$this->prueba->Nombre,0,0,'L',false);
		//Fecha (47,y+5,38,5) left
		$this->SetXY(47,$y5); 
		$this->Cell(38,5,$this->jornada->Fecha,0,0,'L',false);
		//Perro (47,y+10,38,7) right
		$this->SetXY(47,$y10); 
		$this->Cell(38,7,"{$row['Licencia']} - {$row['Nombre']}",0,0,'R',false);
		//Manga1Tipo(85,y,20,8) center
		$tipo=Mangas::$tipo_manga[$this->manga1->Tipo][3];
		$this->SetXY(85,$y1); 
		$this->Cell(20,8,$tipo,'LB',0,'L',false);
		//Manga2Tipo(85,y+8,20,9) center
		$tipo=Mangas::$tipo_manga[$this->manga2->Tipo][3];
		$this->SetXY(85,$y8); 
		$this->Cell(20,9,$tipo,'L',0,'L',false);
		//Cat (105,y,15,8) center
		$this->SetXY(105,$y1); 
		$this->Cell(15,8,$row['Categoria'],'L',0,'C',false);
		//Grado (105,y+8,15,9) center
		$this->SetXY(105,$y8); 
		$this->Cell(15,9,$row['Grado'],'L',0,'C',false);
		//Penal1 (120,y,15,8) right
		$this->SetXY(120,$y1); 
		$this->Cell(15,8,$row['P1'],'LB',0,'C',false);
		//Penal2 (120,y+8,15,9) right
		$this->SetXY(120,$y8); 
		$this->Cell(15,9,$row['P2'],'L',0,'C',false);
		//Calif1 (135,y,25,8) right
		$this->SetXY(135,$y1); 
		$this->Cell(25,8,$row['C1'],'LB',0,'C',false);
		//Calif2 (135,y+8,25,9) right
		$this->SetXY(135,$y8); 
		$this->Cell(25,9,$row['C2'],'L',0,'C',false);
		//Puesto1 (160,y,15,8) center
		$this->SetXY(160,$y1); 
		$this->Cell(15,8,"{$row['Puesto1']}º",'LB',0,'C',false);
		//Puesto2 (160,y+8,15,9) center
		$this->SetXY(160,$y8); 
		$this->Cell(15,9,"{$row['Puesto2']}º",'L',0,'C',false);
		
		// pintamos una linea	
		$this->SetDrawColor(128,0,0); // line color
		$this->Line(10,$ynext,175,$ynext);	
		$this->SetDrawColor(128,128,128); // line color
	}
	
	function composeTable() {
		$this->myLogger->enter();

		$this->SetFillColor(224,235,255); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont('Arial','',8); // default font		
		$this->SetDrawColor(128,128,128); // line color
		$this->SetLineWidth(.3);
		
		$this->SetMargins(10,10,10); // left top right
		$this->SetAutoPageBreak(true,10);
		
		$rowcount=0;
		$numrows=16; // 16 etiquetas/pagina
		$this->addPage();
		foreach($this->resultados as $row) {
			$this->writeCell($rowcount,$row);
			$rowcount++;
			if ($rowcount>=$numrows) {
				$this->addPage();
				$rowcount=0;
			}
		}
		$this->myLogger->leave();
	}
}

try {
	$result=null;
	$mangas=array();
	$prueba=http_request("Prueba","i",0);
	$jornada=http_request("Jornada","i",0);
	$rondas=http_request("Rondas","i","0"); // bitfield of 512:Esp 256:KO 128:Eq4 64:Eq3 32:Opn 16:G3 8:G2 4:G1 2:Pre2 1:Pre1
	$mangas[0]=http_request("Manga1","i",0); // single manga
	$mangas[1]=http_request("Manga2","i",0); // mangas a dos vueltas
	$mangas[2]=http_request("Manga3","i",0);
	$mangas[3]=http_request("Manga4","i",0); // 1,2:GII 3,4:GIII
	$mangas[4]=http_request("Manga5","i",0);
	$mangas[5]=http_request("Manga6","i",0);
	$mangas[6]=http_request("Manga7","i",0);
	$mangas[7]=http_request("Manga8","i",0);
	$mangas[8]=http_request("Manga9","i",0); // mangas 3..9 are used in KO rondas
	$mode=http_request("Mode","i","0"); // 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small
	$c= new Clasificaciones("print_etiquetas_pdf",$prueba,$jornada);
	$result=$c->clasificacionFinal($rondas,$mangas,$mode);
} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}

// Creamos generador de documento
$pdf = new PDF($prueba,$jornada,$mangas,$result['rows']);
$pdf->AliasNbPages();
$pdf->composeTable();
$pdf->Output("print_etiquetas.pdf","D"); // "D" means open download dialog
?>