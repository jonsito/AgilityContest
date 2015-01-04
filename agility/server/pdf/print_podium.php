<?php
/*
print_podium.php

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

class Print_Podium extends PrintCommon {
	
	protected $manga1;
	protected $manga2;
	protected $resultados;

	 /** Constructor
	 * @param {array} $manga lista de mangaid's
	 * @param {array} $resultados resultados asociados a la manga pedidas
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$mangas,$resultados) {
		parent::__construct('Landscape',$prueba,$jornada);
		$dbobj=new DBObject("print_clasificacion");
		$this->manga1=$dbobj->__getObject("Mangas",$mangas[0]);
		$this->manga2=null;
		if ($mangas[1]!=0) $this->manga2=$dbobj->__getObject("Mangas",$mangas[1]);
		$this->resultados=$resultados;
	}

	function Header() {
		$grado=Mangas::$tipo_manga[$this->manga1->Tipo][4];
		$this->print_commonHeader("Pódium $grado");
	}
	
	// Pie de página: tampoco cabe
	function Footer() {
		$this->print_commonFooter();
	}
	
	function print_InfoJornada() {
		$this->setXY(10,40);
		$this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg2')); // gris
		$this->ac_SetTextColor($this->config->getEnv('pdf_hdrfg2')); // negro
		$this->ac_SetDrawColor(0,0,0); // line color
		$this->SetFont('Arial','B',11); // bold 11px
		$this->Cell(140,6,"Jornada: {$this->jornada->Nombre}",0,0,'L',true);
		$this->Cell(135,6,"Fecha: {$this->jornada->Fecha}",0,0,'R',true);
		$this->ln(10); // TODO: write jornada / fecha / grado
	}
	
	function writeTableHeader($mode) {
		$tm1=Mangas::$tipo_manga[$this->manga1->Tipo][3];
		$tm2=null;
		if ($this->manga2!=null) $tm2=Mangas::$tipo_manga[$this->manga2->Tipo][3];
		
		$this->ac_header(1,12);
		
		$this->SetX(10); // first page has 3 extra header lines
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		// first row of table header
		$this->SetFont('Arial','BI',12); // default font
		$this->Cell(115,6,Mangas::$manga_modes[$mode][0],0,0,'L',true);
		$this->Cell(59,6,$tm1,0,0,'C',true);
		$this->Cell(59,6,$tm2,0,0,'C',true);
		$this->Cell(42,6,'Clasificación',0,0,'C',true);
		$this->ln();
		$this->SetFont('Arial','',8); // default font
		// datos del participante
		$this->Cell(10,6,'Dorsal',0,0,'C',true); 	// dorsal
		$this->Cell(25,6,'Nombre',0,0,'C',true);	// nombre (20,y
		$this->Cell(15,6,'Lic.',0,0,'C',true);	// licencia
		$this->Cell(10,6,'Cat./Gr.',0,0,'C',true);	// categoria/grado
		$this->Cell(35,6,'Guía',0,0,'C',true);	// nombreGuia
		$this->Cell(20,6,'Club',0,0,'C',true);	// nombreClub
		// manga 1
		$this->Cell(7,6,'F/T',0,0,'C',true);	// 1- Faltas+Tocados
		$this->Cell(7,6,'Reh',0,0,'C',true);	// 1- Rehuses
		$this->Cell(12,6,'Tiempo',0,0,'C',true);	// 1- Tiempo
		$this->Cell(9,6,'Vel.',0,0,'C',true);	// 1- Velocidad
		$this->Cell(12,6,'Penal',0,0,'C',true);	// 1- Penalizacion
		$this->Cell(12,6,'Calif',0,0,'C',true);	// 1- calificacion
		// manga 2
		if ($this->manga2!=null) {
			$this->Cell(7,6,'F/T',0,0,'C',true);	// 2- Faltas+Tocados
			$this->Cell(7,6,'Reh',0,0,'C',true);	// 2- Rehuses
			$this->Cell(12,6,'Tiempo',0,0,'C',true);	// 2- Tiempo
			$this->Cell(9,6,'Vel.',0,0,'C',true);	// 2- Velocidad
			$this->Cell(12,6,'Penal',0,0,'C',true);	// 2- Penalizacion
			$this->Cell(12,6,'Calif',0,0,'C',true);	// 2- calificacion
		} else {
			$this->Cell(59,6,'',0,0,'C',true);	// espacio en blanco
		}
		// global
		$this->Cell(12,6,'Tiempo.',0,0,'C',true);	// Tiempo total
		$this->Cell(12,6,'Penaliz.',0,0,'C',true);	// Penalizacion
		$this->Cell(9,6,'Calific.',0,0,'C',true);	// Calificacion
		$this->Cell(9,6,'Puesto',0,0,'C',true);	// Puesto	
		$this->Ln();	
		// restore colors
		$this->ac_SetFillColor($this->config->getEnv('pdf_rowcolor2')); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
	}
	
	function writeCell($idx,$row) {
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		$y=$this->getY();
		$this->SetX(10 ); // first page has 3 extra header lines
		$this->ac_row($idx,10);
		
		// fomateamos datos
		$puesto= ($row['Penalizacion']>=200)? "-":"{$row['Puesto']}º";
		$penal=number_format($row['Penalizacion'],2);
		$v1= ($row['P1']>=200)?"-":number_format($row['V1'],1);
		$t1= ($row['P1']>=200)?"-":number_format($row['T1'],2);
		$p1=number_format($row['P1'],2);
		$v2= ($row['P2']>=200)?"-":number_format($row['V2'],1);
		$t2= ($row['P2']>=200)?"-":number_format($row['T2'],2);
		$p2=number_format($row['P2'],2);
		
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		// datos del participante
		$this->Cell(10,6,$row['Dorsal'],0,0,'R',true); 	// dorsal
		$this->Cell(25,6,$row['Nombre'],0,0,'L',true);	// nombre (20,y
		$this->Cell(15,6,$row['Licencia'],0,0,'C',true);	// licencia
		$this->Cell(10,6,"{$row['Categoria']} {$row['Grado']}",0,0,'C',true);	// categoria/grado
		$this->Cell(35,6,$row['NombreGuia'],0,0,'R',true);	// nombreGuia
		$this->Cell(20,6,$row['NombreClub'],0,0,'R',true);	// nombreClub
		// manga 1
		$this->Cell(7,6,$row['F1'],0,0,'C',true);	// 1- Faltas+Tocados
		$this->Cell(7,6,$row['R1'],0,0,'C',true);	// 1- Rehuses
		$this->Cell(12,6,$t1,0,0,'C',true);	// 1- Tiempo
		$this->Cell(9,6,$v1,0,0,'C',true);	// 1- Velocidad
		$this->Cell(12,6,$p1,0,0,'C',true);	// 1- Penalizacion
		$this->Cell(12,6,$row['C1'],0,0,'C',true);	// 1- calificacion
		// manga 2
		if ($this->manga2!=null) {
			$this->Cell(7,6,$row['F2'],0,0,'C',true);	// 2- Faltas+Tocados
			$this->Cell(7,6,$row['R2'],0,0,'C',true);	// 2- Rehuses
			$this->Cell(12,6,$t2,0,0,'C',true);	// 2- Tiempo
			$this->Cell(9,6,$v2,0,0,'C',true);	// 2- Velocidad
			$this->Cell(12,6,$p2,0,0,'C',true);	// 2- Penalizacion
			$this->Cell(12,6,$row['C2'],0,0,'C',true);	// 2- calificacion
		} else {
			$this->Cell(59,6,'',0,0,'C',true);	// espacio en blanco
		}
		// global
		$this->Cell(12,6,$t1+$t2,0,0,'C',true);	// Tiempo
		$this->Cell(12,6,$penal,0,0,'C',true);	// Penalizacion
		$this->Cell(9,6,$row['Calificacion'],0,0,'C',true);	// Calificacion
		$this->SetFont('Arial','B',9); // default font
		$this->Cell(9,6,$puesto,0,0,'R',true);	// Puesto
		$this->SetFont('Arial','',8); // default font
		// lineas rojas
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->Line(10    ,$y,10,    $y+6);
		$this->Line(10+115,$y,10+115,$y+6);
		$this->Line(10+174,$y,10+174,$y+6);
		$this->Line(10+233,$y,10+233,$y+6);
		$this->Line(10+275,$y,10+275,$y+6);
		$this->Ln(6);
	}
	
	function composeTable() {
		$this->myLogger->enter();

		$this->addPage();
		$this->print_InfoJornada();
		foreach($this->resultados as $mode => $data) {
			$rowcount=0;
			foreach($data as $row) {
				if($rowcount==0) $this->writeTableHeader($mode);
				if($rowcount>2) break; // only print 3 first results
				$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
				$this->writeCell( $rowcount,$row);
				$rowcount++;
			}
			// pintamos linea de cierre final
			$this->setX(10);
			$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
			$this->cell(275,0,'','T'); // celda sin altura y con raya
			$this->Ln(3); // 3 mmts to next box
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
	
	// buscamos los recorridos asociados a la manga
	$dbobj=new DBObject("print_clasificacion");
	$mng=$dbobj->__getObject("Mangas",$mangas[0]);
	$prb=$dbobj->__getObject("Pruebas",$prueba);
	$c= new Clasificaciones("print_podium_pdf",$prueba,$jornada);
	$result=array();
	$rsce=($prb->RSCE==0)?true:false;
	switch($mng->Recorrido) {
		case 0: // recorridos separados large medium small
			$r=$c->clasificacionFinal($rondas,$mangas,0);
			$result[0]=$r['rows'];
			$r=$c->clasificacionFinal($rondas,$mangas,1);
			$result[1]=$r['rows'];
			$r=$c->clasificacionFinal($rondas,$mangas,2);
			$result[2]=$r['rows'];
			if (!$rsce) {
				$r=$c->clasificacionFinal($rondas,$mangas,5);
				$result[5]=$r['rows'];
			}
			break;
		case 1: // large / medium+small
			if ($rsce) {
				$r=$c->clasificacionFinal($rondas,$mangas,0);
				$result[0]=$r['rows'];
				$r=$c->clasificacionFinal($rondas,$mangas,3);
				$result[3]=$r['rows'];
			} else {
				$r=$c->clasificacionFinal($rondas,$mangas,6);
				$result[6]=$r['rows'];
				$r=$c->clasificacionFinal($rondas,$mangas,7);
				$result[7]=$r['rows'];
			}
			break;
		case 2: // recorrido conjunto large+medium+small
			if ($rsce) {
				$r=$c->clasificacionFinal($rondas,$mangas,4);
				$result[4]=$r['rows'];
			} else {
				$r=$c->clasificacionFinal($rondas,$mangas,8);
				$result[8]=$r['rows'];
			}
			break;
	}
	
	// Creamos generador de documento
	$pdf = new Print_Podium($prueba,$jornada,$mangas,$result);
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output("print_podium.pdf","D"); // "D" means open download dialog
} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}
?>