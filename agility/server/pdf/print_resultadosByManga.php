<?php
/*
print_resultadosByManga.php

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
 * genera un pdf con los participantes ordenados segun los resultados de la manga
 */

require_once(__DIR__."/fpdf.php");
require_once(__DIR__."/../tools.php");
require_once (__DIR__."/../logging.php");
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jueces.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/Resultados.php');
require_once(__DIR__."/print_common.php");

class PDF extends PrintCommon {
	
	protected $manga;
	protected $resultados;
	protected $mode;
	
	// geometria de las celdas
	protected $cellHeader
					=array('Puesto','Dorsal','Nombre','Lic.','Guía','Club','Cat/Grad','Falt.','Toc.','Reh.','Tiempo','Vel.','Penal','Calificacion');
	protected $pos	=array(  10,      10,     12,     10,     29,    31,     12,        7,      7,    7,       10,     7,    12,      24 );
	protected $align=array(  'L',     'R',    'L',    'C',    'R',   'R',    'C',       'C',   'C',   'C',     'R',    'R',  'R',     'L');
	protected $fmt	=array(  'i',     'i',    's',    's',    's',   's',    's',       'i',   'i',   'i',     'f',    'f',  'f',     's');

	protected $modestr  =array("Large","Medium","Small","Medium+Small","Conjunta L/M/S");
	/**
	 * Constructor
	 * @param {obj} $manga datos de la manga
	 * @param {obj} $resultados resultados asociados a la manga/categoria pedidas
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$manga,$resultados,$mode) {
		parent::__construct('Portrait',$prueba,$jornada);
		$this->manga=$manga;
		$this->resultados=$resultados;
		$this->mode=$mode;
		$this->myLogger= new Logger("printResultadosByManga");
	}
	
	// Cabecera de página
	function Header() {
		$this->print_commonHeader("Resultados Parciales");
		$this->print_identificacionManga($this->manga,$this->modestr[intval($this->mode)]);
		
		// Si es la primera hoja pintamos datos tecnicos de la manga
		if ($this->PageNo()!=1) return;

		$this->SetFont('Arial','B',9); // bold 9px
		$jobj=new Jueces("print_resultadosByManga");
		$juez1=$jobj->selectByID($this->manga->Juez1);
		$juez2=$jobj->selectByID($this->manga->Juez2);
		$this->Cell(20,7,"Juez 1:","LT",0,'L',false);
		$this->Cell(70,7,$juez1['Nombre'],"T",0,'L',false);
		$this->Cell(20,7,"Juez 2:","T",0,'L',false);
		$this->Cell(78,7,$juez2['Nombre'],"TR",0,'L',false);
		$this->Ln(7);
		$this->Cell(20,7,"Distancia:","LB",0,'L',false);
		$this->Cell(25,7,"{$this->resultados['trs']['dist']} mts","B",0,'L',false);
		$this->Cell(20,7,"Obstáculos:","B",0,'L',false);
		$this->Cell(25,7,$this->resultados['trs']['obst'],"B",0,'L',false);
		$this->Cell(10,7,"TRS:","B",0,'L',false);
		$this->Cell(20,7,"{$this->resultados['trs']['trs']} seg.","B",0,'L',false);
		$this->Cell(10,7,"TRM:","B",0,'L',false);
		$this->Cell(20,7,"{$this->resultados['trs']['trm']} seg.","B",0,'L',false);
		$this->Cell(20,7,"Velocidad:","B",0,'L',false);
		$this->Cell(18,7,"{$this->resultados['trs']['vel']} m/s","BR",0,'L',false);
		$this->Ln(14); // en total tres lineas extras en la primera hoja
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}
	
	function writeTableHeader() {
		$this->myLogger->enter();
		// Colores, ancho de línea y fuente en negrita de la cabecera de tabla
		$this->SetFillColor(0,0,255); // azul
		$this->SetTextColor(255,255,255); // blanco
		$this->SetFont('Arial','B',8); // bold 9px
		for($i=0;$i<count($this->cellHeader);$i++) {
			// en la cabecera texto siempre centrado
			$this->Cell($this->pos[$i],7,$this->cellHeader[$i],1,0,'C',true);
		}
		// Restauración de colores y fuentes
		$this->SetFillColor(224,235,255); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont(''); // remove bold
		$this->Ln();
		$this->myLogger->leave();
	}
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
		
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		
		// Datos
		$fill = false;
		$rowcount=0;
		$numrows=($this->PageNo()==1)?26:29;
		foreach($this->resultados['rows'] as $row) {
			// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
			if( ($rowcount%$numrows) == 0 ) { // assume $numrows rows per page ( rowWidth = 7mmts )
				if ($rowcount>0) 
					$this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre
				$this->addPage();
				$this->writeTableHeader();
			}
			// properly format special fields

			$puesto= ($row['Penalizacion']>=200)? "-":$row['Puesto'];
			$veloc= ($row['Penalizacion']>=200)?"-":number_format($row['Velocidad'],1);
			$tiempo= ($row['Penalizacion']>=200)?"-":number_format($row['Tiempo'],2);
			$penal=number_format($row['Penalizacion'],2);
			
			// print row data
			$this->SetFont('Arial','B',11); // bold 11px
			$this->Cell($this->pos[0],7," $puesto -",			'LR',	0,		$this->align[0],	$fill);
			$this->SetFont('Arial','',8); // set data font size
			$this->Cell($this->pos[1],7,$row['Dorsal'],			'LR',	0,		$this->align[1],	$fill);
			$this->Cell($this->pos[2],7,$row['Nombre'],			'LR',	0,		$this->align[2],	$fill);
			$this->Cell($this->pos[3],7,$row['Licencia'],		'LR',	0,		$this->align[3],	$fill);
			$this->Cell($this->pos[4],7,$row['NombreGuia'],		'LR',	0,		$this->align[4],	$fill);
			$this->Cell($this->pos[5],7,$row['NombreClub'],		'LR',	0,		$this->align[5],	$fill);
			$this->Cell($this->pos[6],7,$row['Categoria'].' - '.$row['Grado'],	'LR',	0,		$this->align[6],	$fill);
			$this->Cell($this->pos[7],7,$row['Faltas'],			'LR',	0,		$this->align[7],	$fill);
			$this->Cell($this->pos[8],7,$row['Tocados'],		'LR',	0,		$this->align[8],	$fill);
			$this->Cell($this->pos[9],7,$row['Rehuses'],		'LR',	0,		$this->align[9],	$fill);
			$this->Cell($this->pos[10],7,$tiempo,				'LR',	0,		$this->align[10],	$fill);
			$this->Cell($this->pos[11],7,$veloc,				'LR',	0,		$this->align[11],	$fill);
			$this->Cell($this->pos[12],7,$penal,				'LR',	0,		$this->align[12],	$fill);
			$this->Cell($this->pos[13],7,$row['Calificacion'],	'LR',	0,		$this->align[13],	$fill);
			$this->Ln();
			$fill = ! $fill;
			$rowcount++;
		}
		// Línea de cierre
		$this->Cell(array_sum($this->pos),0,'','T');
		$this->myLogger->leave();
	}
}

// Consultamos la base de datos
try {
	$idprueba=http_request("Prueba","i",0);
	$idjornada=http_request("Jornada","i",0);
	$idmanga=http_request("Manga","i",0);
	$mode=http_request("Mode","i",0);
	
	$mngobj= new Mangas("printResultadosByManga",$idjornada);
	$manga=$mngobj->selectByID($idmanga);
	$resobj= new Resultados("printResultadosByManga",$idprueba,$idmanga);
	$resultados=$resobj->getResultados($mode);

	// Creamos generador de documento
	$pdf = new PDF($idprueba,$idjornada,$manga,$resultados,$mode);
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output("resultadosByManga.pdf","D"); // "D" means open download dialog
} catch (Exception $e) {
	die ("Error accessing database: ".$e.getMessage());
}
?>