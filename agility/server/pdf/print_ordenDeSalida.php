<?php
/*
print_ordenDeSalida.php

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
 * genera un pdf ordenado por club, categoria y nombre con una pagina por cada jornada
*/

require_once(__DIR__."/fpdf.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/OrdenSalida.php');
require_once(__DIR__."/print_common.php");

class PDF extends PrintCommon {

	protected $manga; // datos de la manga
	protected $orden; // orden de salida
	protected $categoria; // categoria que estamos listando
	
	// geometria de las celdas
	protected $cellHeader;
					
	protected $pos	=array(  12,      30,     12,     15,    50,   30,     10,    26);
	protected $align=array(  'R',    'R',    'R',    'C',   'R',  'R',    'C',   'R');
	protected $cat  =array("-" => "","L"=>"Large","M"=>"Medium","S"=>"Small","T"=>"Tiny");
	
	/**
	 * Constructor
	 * @param {integer} $prueba Prueba ID
	 * @param {array} $inscritos Lista de inscritos en formato jquery array[count,rows[]]
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$manga) {
		parent::__construct('Portrait',$prueba,$jornada);
		if ( ($prueba<=0) || ($jornada<=0) || ($manga<=0) ) {
			$this->errormsg="printOrdenDeSalida: either prueba/jornada/ manga/orden data are invalid";
			throw new Exception($this->errormsg);
		}
		// Datos de la manga
		$m = new Mangas("printOrdenDeSalida",$jornada);
		$this->manga= $m->selectByID($manga);
		// Datos del orden de salida
		$o = new OrdenSalida("printOrdenDeSalida",$manga);
		$os= $o->getData();
		$this->orden=$os['rows'];
		$this->categoria="L";
		$this->cellHeader=
			array(_('Orden'),_('Nombre'),_('Dorsal'),_('Lic.'),_('Guía'),_('Club'),_('Celo'),_('Observaciones'));
	}
	
	// Cabecera de página
	function Header() {
		$this->print_commonHeader(_("Orden de Salida"));
		$this->print_identificacionManga($this->manga,$this->cat[$this->categoria]);
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}
	
	function writeTableHeader() {
		$this->myLogger->enter();
		// Colores, ancho de línea y fuente en negrita de la cabecera de tabla
		$this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg1')); // azul
		$this->ac_SetTextColor($this->config->getEnv('pdf_hdrfg1')); // blanco
		$this->ac_SetDrawColor(0,0,0); // line color
		$this->SetFont('Arial','B',9); // bold 9px
		for($i=0;$i<count($this->cellHeader);$i++) {
			// en la cabecera texto siempre centrado
			$this->Cell($this->pos[$i],7,$this->cellHeader[$i],1,0,'C',true);
		}
		// Restauración de colores y fuentes
		$this->ac_SetFillColor($this->config->getEnv('pdf_rowcolor2')); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont('Arial','',9); // remove bold
		$this->Ln();
		$this->myLogger->leave();
	}
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
		
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->SetLineWidth(.3);
		
		// Datos
		$fill = false;
		$rowcount=0;
		foreach($this->orden as $row) {
			// if change in categoria, reset orden counter and force page change
			if ($row['Categoria'] !== $this->categoria) {
				$this->categoria = $row['Categoria'];
				$this->Cell(array_sum($this->pos),0,'','T'); // forzamos linea de cierre
				$rowcount=0;
			}
			// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
			if( ($rowcount%37) == 0 ) { // assume 37 rows per page ( rowWidth = 6mmts )
				if ($rowcount>0) 
					$this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre en cambio de pagina
				$this->addPage();
				$this->writeTableHeader();
			}
			$this->SetFont('Arial','B',11); // bold 9px
			$this->Cell($this->pos[0],6,($rowcount+1)." - ",'LR',0,$this->align[0],$fill); // display order
			$this->SetFont('Arial','B',10); // remove bold 9px
			$this->Cell($this->pos[1],6,$row['Nombre'],		'LR',0,$this->align[2],$fill);
			$this->SetFont('Arial','',9); // remove bold 9px
			$this->Cell($this->pos[2],6,$row['Dorsal'],		'LR',0,$this->align[1],$fill);
			$this->Cell($this->pos[3],6,$row['Licencia'],	'LR',0,$this->align[3],$fill);
			$this->Cell($this->pos[4],6,$row['NombreGuia'],	'LR',0,$this->align[4],$fill);
			$this->Cell($this->pos[5],6,$row['NombreClub'],	'LR',0,$this->align[5],$fill);
			$this->Cell($this->pos[6],6,($row['Celo']==0)?"":"X",		'LR',0,$this->align[6],$fill);
			$this->Cell($this->pos[7],6,$row['Observaciones'],'LR',0,$this->align[7],$fill);
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
	$prueba=http_request("Prueba","i",0);
	$jornada=http_request("Jornada","i",0);
	$manga=http_request("Manga","i",0);
	// 	Creamos generador de documento
	$pdf = new PDF($prueba,$jornada,$manga);
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output("ordenDeSalida.pdf","D"); // "D" means open download dialog	
} catch (Exception $e) {
	die ("Error accessing database: ".$e.getMessage());
};
?>