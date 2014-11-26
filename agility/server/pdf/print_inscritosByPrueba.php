<?php
/*
print_inscritosByPrueba.php

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
 * genera un pdf ordenado por club, categoria y nombre con una pagina por cada jornada
*/

require_once(__DIR__."/fpdf.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Clubes.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Inscripciones.php');
require_once(__DIR__."/print_common.php");

class PrintCatalogo extends PrintCommon {
	protected $inscritos;
	protected $cat=array('-'=>'','L'=>'Large','M'=>'Medium','S'=>'Small','T'=>'Tiny');
	
	/**
	 * Constructor
	 * @param {integer} $prueba Prueba ID
	 * @param {array} $inscritos Lista de inscritos en formato jquery array[count,rows[]]
	 * @throws Exception
	*/
	function __construct($prueba,$inscritos) {
		parent::__construct('Portrait',$prueba,0);
		if ( ($prueba==0) || ($inscritos===null) ) {
			$this->errormsg="printInscritosByPrueba: either prueba or inscription data are invalid";
			throw new Exception($this->errormsg);
		}
		$this->inscritos=$inscritos['rows'];
		$this->setPageName("catalogoInscripciones.pdf");
	}
	
	// Cabecera de página
	function Header() {
		$this->myLogger->enter();
		$this->print_commonHeader("Catálogo de Participantes");
		$this->Ln(5);
		$this->myLogger->leave();
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}

	function printClub($pos,$id) {
		// retrieve club data
		$cmgr=new Clubes('printCatalogo');
		$club=$cmgr->selectByID($id);
		$icon=($club['Logo']==="")?'rsce.png':$club['Logo'];
		$this->myLogger->trace("Position: ".$pos." ID:".$id." Club: ".$club['Nombre']);
		
		$this->SetFillColor(0,0,255); // fondo azul
		$this->SetTextColor(255,255,255); // texto blanco
		$this->SetDrawColor(128,0,0); // lineas rojo palido
		$this->SetLineWidth(.3); // ancho de linea
		
		// pintamos logo
		$this->SetXY(10,5+10*$pos);
		$this->Cell(25,25,$this->Image(__DIR__.'/../../images/logos/'.$icon,$this->getX(),$this->getY(),22),0,0,'LTB',false);

		// pintamos info del club
		$this->SetFont('Arial','B',9);
		$this->SetXY(35,5+10*$pos);
		$this->Cell( 50, 6, $club['Direccion1'],	'LT', 0, 'L', true); // pintamos direccion1
		$this->SetXY(35,6+5+10*$pos);
		$this->Cell( 50, 6, $club['Direccion2'],	'L', 0, 'L',	true);	// pintamos direccion2
		$this->SetXY(35,12+5+10*$pos);
		$this->Cell( 50, 6, $club['Provincia'],	'L', 0, 'L',	true);	// pintamos provincia
		$this->SetFont('Arial','IB',24);
		$this->SetXY(85,5+10*$pos);
		$this->Cell( 110, 18, $club['Nombre'],	'T', 0, 'R',	true);	// pintamos Nombre
		$this->Cell( 5, 18, '',	'TR', 0, 'R',	true);	// caja vacia de relleno
		
		// pintamos cabeceras de la tabla
		$this->SetFillColor(192,192,192); // fondo gris
		$this->SetTextColor(0,0,0); // texto blanco
		$this->SetFont('Arial','B',9);
		$this->SetXY(35,18+5+10*$pos);
		$this->Cell( 40, 7, 'Nombre','LTB', 0, 'C',true);
		$this->Cell( 35, 7, 'Raza','LTB', 0, 'C',true);
		$this->Cell( 15, 7, 'Licencia','LTB', 0, 'C',true);
		$this->Cell( 25, 7, 'Cat/Grado','LTB', 0, 'C',true);
		$this->Cell( 50, 7, 'Guía','LTBR', 0, 'C',true);
		$this->Ln();
	}
	
	function printParticipante($pos,$row) {
		$this->myLogger->trace("Position: ".$pos." Dorsal: ".$row['Dorsal']);
		$fill = (($pos&0x01)==0)?true:false;
		$this->SetFillColor(224,235,255); // fondo azul merle
		$this->SetTextColor(0,0,0); // texto negro
		$this->SetDrawColor(128,0,0); // lineas rojo palido
		$this->SetLineWidth(.3); // ancho de linea
		$this->setXY(20,10*$pos); // posicion inicial
		// REMINDER: 
		// $this->cell( width, height, data, borders, where, align, fill)
		$this->SetFont('Arial','B',18); //
		$this->Cell( 15, 10, $row['Dorsal'],	'LB', 0, 'C',	$fill);
		$this->SetFont('Arial','BI',15); // bold 9px
		$this->Cell( 40, 10, $row['Nombre'],	'LB', 0, 'C',	$fill);
		$this->SetFont('Arial','',10); // bold 9px
		$this->Cell( 35, 10, substr($row['Raza'],0,20),		'LB', 0, 'R',	$fill);
		$this->Cell( 15, 10, $row['Licencia'],	'LB', 0, 'C',	$fill);
		$this->Cell( 25, 10, $this->cat[$row['Categoria']]." - ".$row['Grado'],	'LB', 0, 'C',	$fill);
		$this->SetFont('Arial','B',11); // bold 9px
		$this->Cell( 50, 10, substr($row['NombreGuia'],0,30),'LBR', 0, 'R',	$fill);
		$this->Ln(10);
	}
	
	function composeTable() {
		$this->myLogger->enter();
		$club=0;
		$pos=5; // header takes 5 cmts
		foreach($this->inscritos as $row) {
			// at end of page newPage
			if ($pos>=27) { $this->addPage(); $pos=5; }
			// at start of page print club header
			if ($pos==5) { $club=$row['Club']; $this->printClub($pos,$club); $pos+=3; }
			// on club change, if at end of page, do newline
			if ( $club!=$row['Club'] ) {
				$club=$row['Club'];
				if ($pos>23) { $this->addPage(); $pos=5; }
				$this->printClub($pos,$club);
				$pos+=3;
			}
			$this->printParticipante($pos,$row);
			$pos+=1;	
		}
		$this->myLogger->leave();		
	}
}

class PrintEstadisticas extends PrintCommon {
	protected $inscritos;
	
	/**
	 * Constructor
	 * @param {integer} $prueba Prueba ID
	 * @param {array} $inscritos Lista de inscritos en formato jquery array[count,rows[]]
	 * @throws Exception
	 */
	function __construct($prueba,$inscritos) {
		parent::__construct('Portrait',$prueba,0);
		if ( ($prueba==0) || ($inscritos===null) ) {
			$this->errormsg="printInscritosByPrueba: either prueba or inscription data are invalid";
			throw new Exception($this->errormsg);
		}
		$this->inscritos=$inscritos['rows'];
		$this->setPageName("estadisticasInscripciones.pdf");
	}
	
	// Cabecera de página
	function Header() {
		$this->myLogger->enter();
		$this->print_commonHeader("Estadísticas");
		$this->Ln(5);
		$this->myLogger->leave();
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}
	
	function composeTable() {
	
	}
}

class PrintInscritos extends PrintCommon {
	
	protected $inscritos;

	// geometria de las celdas
	protected $cellHeader
					=array('Dorsal','Nombre','Lic.','Guía','Club','Cat.','Grado','Celo','Observaciones','Sab.','Dom.');
	protected $pos	=array(  10,       20,     10,    40,   30,    10,     10,     10,    30,    10,    10 );
	protected $align=array(  'R',      'L',    'C',   'R',  'R',   'C',    'L',    'C',   'R',   'C',   'C');
	protected $fmt	=array(  'i',      's',    's',   's',  's',   's',    's',    'b',   's',   'b',   'b');
	
	/**
	 * Constructor
	 * @param {integer} $prueba Prueba ID
	 * @param {array} $inscritos Lista de inscritos en formato jquery array[count,rows[]]
	 * @throws Exception
	 */
	function __construct($prueba,$inscritos) {
		parent::__construct('Portrait',$prueba,0);
		if ( ($prueba==0) || ($inscritos===null) ) {
			$this->errormsg="printInscritosByPrueba: either prueba or inscription data are invalid";
			throw new Exception($this->errormsg);
		}
		$this->inscritos=$inscritos['rows'];
		$this->setPageName("inscritosByPrueba.pdf");
	}
	
	// Cabecera de página
	function Header() {
		$this->myLogger->enter();
		$this->print_commonHeader("Listado de Participantes");
		$this->Ln(5);
		$this->myLogger->leave();
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
		foreach($this->inscritos as $row) {
			// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
			if( ($rowcount%32) == 0 ) { // assume 32 rows per page ( rowWidth = 7mmts )
				if ($rowcount>0) 
					$this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre
				$this->addPage();
				$this->writeTableHeader();
			} 
			// $this->Cell($this->pos[0],7,$row['IDPerro'],	'LR',0,$this->align[0],$fill);
			// $this->Cell($this->pos[0],7,$rowcount+1,		'LR',	0,		$this->align[0],$fill); // display order instead of idperro

			$this->Cell($this->pos[0],7,$row['Dorsal'],		'LR',	0,		$this->align[1],	$fill);
			$this->Cell($this->pos[1],7,$row['Nombre'],		'LR',	0,		$this->align[1],	$fill);
			$this->Cell($this->pos[2],7,$row['Licencia'],	'LR',	0,		$this->align[2],	$fill);
			$this->Cell($this->pos[3],7,$row['NombreGuia'],	'LR',	0,		$this->align[3],	$fill);
			$this->Cell($this->pos[4],7,$row['NombreClub'],	'LR',	0,		$this->align[4],	$fill);
			$this->Cell($this->pos[5],7,$row['Categoria'],	'LR',	0,		$this->align[5],	$fill);
			$this->Cell($this->pos[6],7,$row['Grado'],		'LR',	0,		$this->align[6],	$fill);
			$this->Cell($this->pos[7],7,($row['Celo']==0)?"":"X",'LR',0,	$this->align[7],	$fill);
			$this->Cell($this->pos[8],7,$row['Observaciones'],'LR',	0,		$this->align[9],	$fill);
			$this->Cell($this->pos[9],7,($row['J1']==0)?"":"X",	'LR',0,		$this->align[9],	$fill);
			$this->Cell($this->pos[10],7,($row['J2']==0)?"":"X",'LR',0,		$this->align[10],	$fill);
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
	$pruebaid=http_request("Prueba","i",0);
	$mode=http_request("Mode","i",0);
	$pdf=null;
	$name="";
	// Datos de inscripciones
	$inscripciones = new Inscripciones("printInscritosByPrueba",$pruebaid);
	$inscritos= $inscripciones->enumerate();
	// Creamos generador de documento
	switch ($mode) {
		case 0: $pdf=new PrintInscritos($pruebaid,$inscritos); break;
		case 1: $pdf=new PrintCatalogo($pruebaid,$inscritos); break;
		case 2: $pdf=new PrintEstadisticas($pruebaid,$inscritos); break;
		default: throw new Exception ("Inscripciones::print() Invalid print mode selected $mode");
	}
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output($pdf->getPageName(),"D"); // "D" means open download dialog
} catch (Exception $e) {
	die ("Error accessing database: ".$e.getMessage());
}
echo json_encode(array('success'=>true));
?>