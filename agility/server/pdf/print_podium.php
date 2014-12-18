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
	
	function print_datosMangas() {
		$this->ln(14); // TODO: write jornada / fecha / grado
	}
	
	function Header() {
		$this->print_commonHeader("Podiums");
	}
	
	// Pie de página: tampoco cabe
	function Footer() {
		$this->print_commonFooter();
	}
	
	function writeTableHeader() {
		$tm1=Mangas::$tipo_manga[$this->manga1->Tipo][3];
		$tm2=null;
		if ($this->manga2!=null) $tm2=Mangas::$tipo_manga[$this->manga2->Tipo][3];
		
		$this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg1')); // azul
		$this->ac_SetTextColor($this->config->getEnv('pdf_hdrfg1')); // blanco
		$this->ac_SetDrawColor(0,0,0); // line color
		
		$this->SetX(10); // first page has 3 extra header lines
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		// first row of table header
		$this->SetFont('Arial','BI',12); // default font
		$this->Cell(115,7,'Datos del participante',0,0,'L',true);
		$this->Cell(59,7,$tm1,0,0,'C',true);
		$this->Cell(59,7,$tm2,0,0,'C',true);
		$this->Cell(42,7,'Clasificación',0,0,'C',true);
		$this->ln();
		$this->SetFont('Arial','',8); // default font
		// datos del participante
		$this->Cell(10,7,'Dorsal',0,0,'C',true); 	// dorsal
		$this->Cell(25,7,'Nombre',0,0,'C',true);	// nombre (20,y
		$this->Cell(15,7,'Lic.',0,0,'C',true);	// licencia
		$this->Cell(10,7,'Cat./Gr.',0,0,'C',true);	// categoria/grado
		$this->Cell(35,7,'Guía',0,0,'C',true);	// nombreGuia
		$this->Cell(20,7,'Club',0,0,'C',true);	// nombreClub
		// manga 1
		$this->Cell(7,7,'F/T',0,0,'C',true);	// 1- Faltas+Tocados
		$this->Cell(7,7,'Reh',0,0,'C',true);	// 1- Rehuses
		$this->Cell(12,7,'Tiempo',0,0,'C',true);	// 1- Tiempo
		$this->Cell(9,7,'Vel.',0,0,'C',true);	// 1- Velocidad
		$this->Cell(12,7,'Penal',0,0,'C',true);	// 1- Penalizacion
		$this->Cell(12,7,'Calif',0,0,'C',true);	// 1- calificacion
		// manga 2
		if ($this->manga2!=null) {
			$this->Cell(7,7,'F/T',0,0,'C',true);	// 2- Faltas+Tocados
			$this->Cell(7,7,'Reh',0,0,'C',true);	// 2- Rehuses
			$this->Cell(12,7,'Tiempo',0,0,'C',true);	// 2- Tiempo
			$this->Cell(9,7,'Vel.',0,0,'C',true);	// 2- Velocidad
			$this->Cell(12,7,'Penal',0,0,'C',true);	// 2- Penalizacion
			$this->Cell(12,7,'Calif',0,0,'C',true);	// 2- calificacion
		} else {
			$this->Cell(59,7,'',0,0,'C',true);	// espacio en blanco
		}
		// global
		$this->Cell(12,7,'Tiempo.',0,0,'C',true);	// Tiempo total
		$this->Cell(12,7,'Penaliz.',0,0,'C',true);	// Penalizacion
		$this->Cell(9,7,'Calific.',0,0,'C',true);	// Calificacion
		$this->Cell(9,7,'Puesto',0,0,'C',true);	// Puesto	
		$this->Ln();	
		// restore colors
		$this->ac_SetFillColor($this->config->getEnv('pdf_rowcolor2')); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
	}
	
	function writeCell($idx,$row) {
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		$offset=($this->PageNo()==1)?80:55;
		$this->SetXY(10, $offset + 7*$idx ); // first page has 3 extra header lines
		$fill=(($idx%2)!=0)?true:false;
		
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
		$this->Cell(10,7,$row['Dorsal'],0,0,'R',$fill); 	// dorsal
		$this->Cell(25,7,$row['Nombre'],0,0,'L',$fill);	// nombre (20,y
		$this->Cell(15,7,$row['Licencia'],0,0,'C',$fill);	// licencia
		$this->Cell(10,7,"{$row['Categoria']} {$row['Grado']}",0,0,'C',$fill);	// categoria/grado
		$this->Cell(35,7,$row['NombreGuia'],0,0,'R',$fill);	// nombreGuia
		$this->Cell(20,7,$row['NombreClub'],0,0,'R',$fill);	// nombreClub
		// manga 1
		$this->Cell(7,7,$row['F1'],0,0,'C',$fill);	// 1- Faltas+Tocados
		$this->Cell(7,7,$row['R1'],0,0,'C',$fill);	// 1- Rehuses
		$this->Cell(12,7,$t1,0,0,'C',$fill);	// 1- Tiempo
		$this->Cell(9,7,$v1,0,0,'C',$fill);	// 1- Velocidad
		$this->Cell(12,7,$p1,0,0,'C',$fill);	// 1- Penalizacion
		$this->Cell(12,7,$row['C1'],0,0,'C',$fill);	// 1- calificacion
		// manga 2
		if ($this->manga2!=null) {
			$this->Cell(7,7,$row['F2'],0,0,'C',$fill);	// 2- Faltas+Tocados
			$this->Cell(7,7,$row['R2'],0,0,'C',$fill);	// 2- Rehuses
			$this->Cell(12,7,$t2,0,0,'C',$fill);	// 2- Tiempo
			$this->Cell(9,7,$v2,0,0,'C',$fill);	// 2- Velocidad
			$this->Cell(12,7,$p2,0,0,'C',$fill);	// 2- Penalizacion
			$this->Cell(12,7,$row['C2'],0,0,'C',$fill);	// 2- calificacion
		} else {
			$this->Cell(59,7,'',0,0,'C',$fill);	// espacio en blanco
		}
		// global
		$this->Cell(12,7,$t1+$t2,0,0,'C',$fill);	// Tiempo
		$this->Cell(12,7,$penal,0,0,'C',$fill);	// Penalizacion
		$this->Cell(9,7,$row['Calificacion'],0,0,'C',$fill);	// Calificacion
		$this->Cell(9,7,$puesto,0,0,'R',$fill);	// Puesto
		// lineas rojas
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->Line(10,$offset + 7*$idx,10,$offset + 7*($idx+1));
		$this->Line(10+115,$offset + 7*$idx,10+115,$offset + 7*($idx+1));
		$this->Line(10+174,$offset + 7*$idx,10+174,$offset + 7*($idx+1));
		$this->Line(10+233,$offset + 7*$idx,10+233,$offset + 7*($idx+1));
		$this->Line(10+275,$offset + 7*$idx,10+275,$offset + 7*($idx+1));
		
		$this->Ln();
	}
	
	function composeTable() {
		$this->myLogger->enter();

		$this->ac_SetFillColor($this->config->getEnv('pdf_rowcolor2')); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont('Arial','',8); // default font		
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
		$this->SetLineWidth(.3);

		$line=0;
		foreach($this->resultados as $mode => $data) {
			$rowcount=0;
			$this->print_datosMangas();
			$line+=2;
			foreach($data as $row) {
				if($rowcount==0) { $this->writeTableHeader(); $line +=2; }
				if($rowcount>2) break; // only print 3 first results
				$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
				$this->writeCell( $line,$row);
				$rowcount++;
				$line++;
			}
			// pintamos linea de cierre final
			$this->setX(10);
			$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
			$this->cell(275,0,'','T'); // celda sin altura y con raya
			$this->Ln(7);
			$line++;	
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
	$c= new Clasificaciones("print_podium_pdf",$prueba,$jornada);
	$result=array();
	switch($mng->Recorrido) {
		case 0: // recorridos separados large medium small
			$r=$c->clasificacionFinal($rondas,$mangas,0);
			$result[0]=$r['rows'];
			$r=$c->clasificacionFinal($rondas,$mangas,1);
			$result[1]=$r['rows'];
			$r=$c->clasificacionFinal($rondas,$mangas,2);
			$result[2]=$r['rows'];
			break;
		case 1: // large / medium+small
			$r=$c->clasificacionFinal($rondas,$mangas,0);
			$result[0]=$r['rows'];
			$r=$c->clasificacionFinal($rondas,$mangas,3);
			$result[3]=$r['rows'];
			break;
		case 2: // recorrido conjunto large+medium+small
			$r=$c->clasificacionFinal($rondas,$mangas,4);
			$result[4]=$r['rows'];
			break;
	}
	
	// Creamos generador de documento
	$pdf = new Print_Podium($prueba,$jornada,$mangas,$result);
	$pdf->AliasNbPages();
	$pdf->addPage();
	$pdf->composeTable();
	$pdf->Output("print_clasificacion.pdf","D"); // "D" means open download dialog
} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}
?>