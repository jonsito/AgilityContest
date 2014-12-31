<?php
/*
print_inscritosByPrueba.php

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
require_once(__DIR__.'/../database/classes/Clubes.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
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
		$y=10*$pos;
		// retrieve club data
		$cmgr=new Clubes('printCatalogo');
		$club=$cmgr->selectByID($id);
		$icon=($club['Logo']==="")?'rsce.png':$club['Logo'];
		$this->myLogger->trace("Position: ".$pos." ID:".$id." Club: ".$club['Nombre']);

		$this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg1')); // azul
		$this->ac_SetTextColor($this->config->getEnv('pdf_hdrfg1')); // blanco
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
		$this->SetLineWidth(.3); // ancho de linea
		
		// pintamos logo
		$this->SetXY(10,$y);
		$this->Cell(25,25,'','LTB',0,'C',false);
		$this->Image(__DIR__.'/../../images/logos/'.$icon,12.5,2.5+$y,20,20);

		// pintamos info del club
		$this->SetFont('Arial','B',9);
		$this->SetXY(35,$y);
		$this->Cell( 50, 6, $club['Direccion1'],	'LT', 0, 'L', true); // pintamos direccion1
		$this->SetXY(35,6+$y);
		$this->Cell( 50, 6, $club['Direccion2'],	'L', 0, 'L',	true);	// pintamos direccion2
		$this->SetXY(35,12+$y);
		$this->Cell( 50, 6, $club['Provincia'],	'L', 0, 'L',	true);	// pintamos provincia
		$this->SetFont('Arial','IB',24);
		$this->SetXY(85,$y);
		$this->Cell( 110, 18, $club['Nombre'],	'T', 0, 'R',	true);	// pintamos Nombre
		$this->Cell( 5, 18, '',	'TR', 0, 'R',	true);	// caja vacia de relleno
		
		// pintamos cabeceras de la tabla		
		$this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg2')); // gris
		$this->ac_SetTextColor($this->config->getEnv('pdf_hdrfg2')); // negro
		$this->SetFont('Arial','B',9);
		$this->SetXY(35,18+$y);
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

		$this->ac_SetFillColor($this->config->getEnv('pdf_rowcolor2')); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
		
		$this->SetLineWidth(.3); // ancho de linea
		$this->setXY(20,10*$pos-5); // posicion inicial
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
		$this->addPage(); // start page
		$club=0;
		$pos=4; // header takes 4 cmts
		foreach($this->inscritos as $row) {
			switch($pos) {
				case 25: // check for new club
				case 26:
				case 27:
					if ($club==$row['Club']) break;
					// else cannot insert new club header
					// no break
				case 28: // force new page
					$this->addPage(); 
					$pos=4;
					// no break
				case 4: // always insert club data at top
					$club=$row['Club'];
					$this->printClub($pos,$club);
					$pos+=3;
					break;
				default:
					if ($club==$row['Club']) break;
					$club=$row['Club'];
					$this->printClub($pos,$club);
					$pos+=3;
					break;				
			}
			$this->printParticipante($pos,$row);
			$pos+=1;	
		}
		$this->myLogger->leave();		
	}
}

class PrintEstadisticas extends PrintCommon {
	protected $inscritos;
	protected $jornadas;
	
	/**
	 * Constructor
	 * @param {integer} $prueba Prueba ID
	 * @param {array} $inscritos Lista de inscritos en formato jquery array[count,rows[]]
	 * @throws Exception
	 */
	function __construct($prueba,$inscritos,$jornadas) {
		parent::__construct('Portrait',$prueba,0);
		if ( ($prueba==0) || ($inscritos===null) ) {
			$this->errormsg="printInscritosByPrueba: either prueba or inscription data are invalid";
			throw new Exception($this->errormsg);
		}
		$this->inscritos=$inscritos['rows'];
		$this->jornadas=$jornadas['rows'];
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
	
	function evalItem($jornada,&$data,$item) {
		$data[$jornada]['G']['C']++;
		$data[$jornada]['G'][$item['Categoria']]++;
		$data[$jornada][$item['Grado']]['C']++;
		$data[$jornada][$item['Grado']][$item['Categoria']]++;
	}
	
	function evalData() {
		$est=array();
		// datos globales
		$est['Prueba']=array();
		$est['Prueba']['G']=array();
		$est['Prueba']['G']['C']=0;$est['Prueba']['G']['L']=0;$est['Prueba']['G']['M']=0;$est['Prueba']['G']['S']=0;$est['Prueba']['G']['T']=0;		
		$est['Prueba']['P.A.']=array();
		$est['Prueba']['P.A.']['C']=0;$est['Prueba']['P.A.']['L']=0;$est['Prueba']['P.A.']['M']=0;$est['Prueba']['P.A.']['S']=0;$est['Prueba']['P.A.']['T']=0;	
		$est['Prueba']['GI']=array();
		$est['Prueba']['GI']['C']=0;$est['Prueba']['GI']['L']=0;$est['Prueba']['GI']['M']=0;$est['Prueba']['GI']['S']=0;$est['Prueba']['GI']['T']=0;	
		$est['Prueba']['GII']=array();
		$est['Prueba']['GII']['C']=0;$est['Prueba']['GII']['L']=0;$est['Prueba']['GII']['M']=0;$est['Prueba']['GII']['S']=0;$est['Prueba']['GII']['T']=0;	
		$est['Prueba']['GIII']=array();
		$est['Prueba']['GIII']['C']=0;$est['Prueba']['GIII']['L']=0;$est['Prueba']['GIII']['M']=0;$est['Prueba']['GIII']['S']=0;$est['Prueba']['GIII']['T']=0;
		// Jornada 1		
		$est['J1']=array();
		$est['J1']['G']=array();
		$est['J1']['G']['C']=0;$est['J1']['G']['L']=0;$est['J1']['G']['M']=0;$est['J1']['G']['S']=0;$est['J1']['G']['T']=0;		
		$est['J1']['P.A.']=array();
		$est['J1']['P.A.']['C']=0;$est['J1']['P.A.']['L']=0;$est['J1']['P.A.']['M']=0;$est['J1']['P.A.']['S']=0;$est['J1']['P.A.']['T']=0;	
		$est['J1']['GI']=array();
		$est['J1']['GI']['C']=0;$est['J1']['GI']['L']=0;$est['J1']['GI']['M']=0;$est['J1']['GI']['S']=0;$est['J1']['GI']['T']=0;	
		$est['J1']['GII']=array();
		$est['J1']['GII']['C']=0;$est['J1']['GII']['L']=0;$est['J1']['GII']['M']=0;$est['J1']['GII']['S']=0;$est['J1']['GII']['T']=0;	
		$est['J1']['GIII']=array();
		$est['J1']['GIII']['C']=0;$est['J1']['GIII']['L']=0;$est['J1']['GIII']['M']=0;$est['J1']['GIII']['S']=0;$est['J1']['GIII']['T']=0;
		// Jornada 2
		$est['J2']=array();
		$est['J2']['G']=array();
		$est['J2']['G']['C']=0;$est['J2']['G']['L']=0;$est['J2']['G']['M']=0;$est['J2']['G']['S']=0;$est['J2']['G']['T']=0;
		$est['J2']['P.A.']=array();
		$est['J2']['P.A.']['C']=0;$est['J2']['P.A.']['L']=0;$est['J2']['P.A.']['M']=0;$est['J2']['P.A.']['S']=0;$est['J2']['P.A.']['T']=0;
		$est['J2']['GI']=array();
		$est['J2']['GI']['C']=0;$est['J2']['GI']['L']=0;$est['J2']['GI']['M']=0;$est['J2']['GI']['S']=0;$est['J2']['GI']['T']=0;
		$est['J2']['GII']=array();
		$est['J2']['GII']['C']=0;$est['J2']['GII']['L']=0;$est['J2']['GII']['M']=0;$est['J2']['GII']['S']=0;$est['J2']['GII']['T']=0;
		$est['J2']['GIII']=array();
		$est['J2']['GIII']['C']=0;$est['J2']['GIII']['L']=0;$est['J2']['GIII']['M']=0;$est['J2']['GIII']['S']=0;$est['J2']['GIII']['T']=0;
		// Jornada 3
		$est['J3']=array();
		$est['J3']['G']=array();
		$est['J3']['G']['C']=0;$est['J3']['G']['L']=0;$est['J3']['G']['M']=0;$est['J3']['G']['S']=0;$est['J3']['G']['T']=0;
		$est['J3']['P.A.']=array();
		$est['J3']['P.A.']['C']=0;$est['J3']['P.A.']['L']=0;$est['J3']['P.A.']['M']=0;$est['J3']['P.A.']['S']=0;$est['J3']['P.A.']['T']=0;
		$est['J3']['GI']=array();
		$est['J3']['GI']['C']=0;$est['J3']['GI']['L']=0;$est['J3']['GI']['M']=0;$est['J3']['GI']['S']=0;$est['J3']['GI']['T']=0;
		$est['J3']['GII']=array();
		$est['J3']['GII']['C']=0;$est['J3']['GII']['L']=0;$est['J3']['GII']['M']=0;$est['J3']['GII']['S']=0;$est['J3']['GII']['T']=0;
		$est['J3']['GIII']=array();
		$est['J3']['GIII']['C']=0;$est['J3']['GIII']['L']=0;$est['J3']['GIII']['M']=0;$est['J3']['GIII']['S']=0;$est['J3']['GIII']['T']=0;
		// Jornada 4
		$est['J4']=array();
		$est['J4']['G']=array();
		$est['J4']['G']['C']=0;$est['J4']['G']['L']=0;$est['J4']['G']['M']=0;$est['J4']['G']['S']=0;$est['J4']['G']['T']=0;
		$est['J4']['P.A.']=array();
		$est['J4']['P.A.']['C']=0;$est['J4']['P.A.']['L']=0;$est['J4']['P.A.']['M']=0;$est['J4']['P.A.']['S']=0;$est['J4']['P.A.']['T']=0;
		$est['J4']['GI']=array();
		$est['J4']['GI']['C']=0;$est['J4']['GI']['L']=0;$est['J4']['GI']['M']=0;$est['J4']['GI']['S']=0;$est['J4']['GI']['T']=0;
		$est['J4']['GII']=array();
		$est['J4']['GII']['C']=0;$est['J4']['GII']['L']=0;$est['J4']['GII']['M']=0;$est['J4']['GII']['S']=0;$est['J4']['GII']['T']=0;
		$est['J4']['GIII']=array();
		$est['J4']['GIII']['C']=0;$est['J4']['GIII']['L']=0;$est['J4']['GIII']['M']=0;$est['J4']['GIII']['S']=0;$est['J4']['GIII']['T']=0;
		// Jornada 5
		$est['J5']=array();
		$est['J5']['G']=array();
		$est['J5']['G']['C']=0;$est['J5']['G']['L']=0;$est['J5']['G']['M']=0;$est['J5']['G']['S']=0;$est['J5']['G']['T']=0;
		$est['J5']['P.A.']=array();
		$est['J5']['P.A.']['C']=0;$est['J5']['P.A.']['L']=0;$est['J5']['P.A.']['M']=0;$est['J5']['P.A.']['S']=0;$est['J5']['P.A.']['T']=0;
		$est['J5']['GI']=array();
		$est['J5']['GI']['C']=0;$est['J5']['GI']['L']=0;$est['J5']['GI']['M']=0;$est['J5']['GI']['S']=0;$est['J5']['GI']['T']=0;
		$est['J5']['GII']=array();
		$est['J5']['GII']['C']=0;$est['J5']['GII']['L']=0;$est['J5']['GII']['M']=0;$est['J5']['GII']['S']=0;$est['J5']['GII']['T']=0;
		$est['J5']['GIII']=array();
		$est['J5']['GIII']['C']=0;$est['J5']['GIII']['L']=0;$est['J5']['GIII']['M']=0;$est['J5']['GIII']['S']=0;$est['J5']['GIII']['T']=0;
		// Jornada 6
		$est['J6']=array();
		$est['J6']['G']=array();
		$est['J6']['G']['C']=0;$est['J6']['G']['L']=0;$est['J6']['G']['M']=0;$est['J6']['G']['S']=0;$est['J6']['G']['T']=0;
		$est['J6']['P.A.']=array();
		$est['J6']['P.A.']['C']=0;$est['J6']['P.A.']['L']=0;$est['J6']['P.A.']['M']=0;$est['J6']['P.A.']['S']=0;$est['J6']['P.A.']['T']=0;
		$est['J6']['GI']=array();
		$est['J6']['GI']['C']=0;$est['J6']['GI']['L']=0;$est['J6']['GI']['M']=0;$est['J6']['GI']['S']=0;$est['J6']['GI']['T']=0;
		$est['J6']['GII']=array();
		$est['J6']['GII']['C']=0;$est['J6']['GII']['L']=0;$est['J6']['GII']['M']=0;$est['J6']['GII']['S']=0;$est['J6']['GII']['T']=0;
		$est['J6']['GIII']=array();
		$est['J6']['GIII']['C']=0;$est['J6']['GIII']['L']=0;$est['J6']['GIII']['M']=0;$est['J6']['GIII']['S']=0;$est['J6']['GIII']['T']=0;
		// Jornada 7
		$est['J7']=array();
		$est['J7']['G']=array();
		$est['J7']['G']['C']=0;$est['J7']['G']['L']=0;$est['J7']['G']['M']=0;$est['J7']['G']['S']=0;$est['J7']['G']['T']=0;
		$est['J7']['P.A.']=array();
		$est['J7']['P.A.']['C']=0;$est['J7']['P.A.']['L']=0;$est['J7']['P.A.']['M']=0;$est['J7']['P.A.']['S']=0;$est['J7']['P.A.']['T']=0;
		$est['J7']['GI']=array();
		$est['J7']['GI']['C']=0;$est['J7']['GI']['L']=0;$est['J7']['GI']['M']=0;$est['J7']['GI']['S']=0;$est['J7']['GI']['T']=0;
		$est['J7']['GII']=array();
		$est['J7']['GII']['C']=0;$est['J7']['GII']['L']=0;$est['J7']['GII']['M']=0;$est['J7']['GII']['S']=0;$est['J7']['GII']['T']=0;
		$est['J7']['GIII']=array();
		$est['J7']['GIII']['C']=0;$est['J7']['GIII']['L']=0;$est['J7']['GIII']['M']=0;$est['J7']['GIII']['S']=0;$est['J7']['GIII']['T']=0;
		// Jornada 8
		$est['J8']=array();
		$est['J8']['G']=array();
		$est['J8']['G']['C']=0;$est['J8']['G']['L']=0;$est['J8']['G']['M']=0;$est['J8']['G']['S']=0;$est['J8']['G']['T']=0;
		$est['J8']['P.A.']=array();
		$est['J8']['P.A.']['C']=0;$est['J8']['P.A.']['L']=0;$est['J8']['P.A.']['M']=0;$est['J8']['P.A.']['S']=0;$est['J8']['P.A.']['T']=0;
		$est['J8']['GI']=array();
		$est['J8']['GI']['C']=0;$est['J8']['GI']['L']=0;$est['J8']['GI']['M']=0;$est['J8']['GI']['S']=0;$est['J8']['GI']['T']=0;
		$est['J8']['GII']=array();
		$est['J8']['GII']['C']=0;$est['J8']['GII']['L']=0;$est['J8']['GII']['M']=0;$est['J8']['GII']['S']=0;$est['J8']['GII']['T']=0;
		$est['J8']['GIII']=array();
		$est['J8']['GIII']['C']=0;$est['J8']['GIII']['L']=0;$est['J8']['GIII']['M']=0;$est['J8']['GIII']['S']=0;$est['J8']['GIII']['T']=0;
		
		foreach($this->inscritos as $item){
			$j=$item['Jornadas'];
			$this->evalItem('Prueba',$est,$item);
			if ($j&0x01) $this->evalItem('J1',$est,$item);
			if ($j&0x02) $this->evalItem('J2',$est,$item);
			if ($j&0x04) $this->evalItem('J3',$est,$item);
			if ($j&0x08) $this->evalItem('J4',$est,$item);
			if ($j&0x10) $this->evalItem('J5',$est,$item);
			if ($j&0x20) $this->evalItem('J6',$est,$item);
			if ($j&0x40) $this->evalItem('J7',$est,$item);
			if ($j&0x80) $this->evalItem('J8',$est,$item);
		}
		return $est;
	}
	
	function printTableHeader($data,$name,$text) {
		$this->SetX(10);		
		$this->ac_header(1,15);
		// $this->cell( width, height, data, borders, where, align, fill)
		$this->Cell(190,10,$text,'LRTB',0,'L',true);
		$this->Ln();	
	}
	
	function printTableData($data,$name,$rsce) {
		$this->ac_header(2,9);
		$this->SetX(10);
		if ($rsce==0) {
			$this->SetFont('Arial','B',9);
			// $this->cell( width, height, data, borders, where, align, fill)
			$this->cell(30,7,'','RB',0,'L',true);
			$this->cell(30,7,'Large','TRB',0,'C',true);
			$this->cell(30,7,'Medium','TRB',0,'C',true);
			$this->cell(30,7,'Small','TRB',0,'C',true);
			$this->cell(30,7,'Total','TRB',0,'C',true);
			$this->Ln(7);

			$this->ac_header(2,9); // pre-agility
			$this->cell(30,7,'Pre-Agility','RB',0,'L',true);
			$this->ac_row(0,9);
			$this->cell(30,7,$data[$name]['P.A.']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['P.A.']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['P.A.']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['P.A.']['C'],'RB',0,'C',true);
			$this->Ln(7);

			$this->ac_header(2,9); // grado I
			$this->cell(30,7,'Grado I','RB',0,'L',true);
			$this->ac_row(0,9);
			$this->cell(30,7,$data[$name]['GI']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GI']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GI']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GI']['C'],'RB',0,'C',true);
			$this->Ln(7);
			
			$this->ac_header(2,9); // grado II
			$this->cell(30,7,'Grado II','RB',0,'L',true);
			$this->ac_row(0,9);
			$this->cell(30,7,$data[$name]['GII']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GII']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GII']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GII']['C'],'RB',0,'C',true);
			$this->Ln(7);
			
			$this->ac_header(2,9); // grado III
			$this->cell(30,7,'Grado III','RB',0,'L',true);
			$this->ac_row(0,9);
			$this->cell(30,7,$data[$name]['GIII']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GIII']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GIII']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GIII']['C'],'RB',0,'C',true);
			$this->Ln(7);

			$this->ac_header(2,9); // Total
			$this->cell(30,7,'Total','RB',0,'L',true);
			$this->ac_row(0,9);
			$this->cell(30,7,$data[$name]['G']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['G']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['G']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['G']['C'],'RB',0,'C',true);
			$this->Ln(10);
		} else {
			$this->SetFont('Arial','B',9);
			// $this->cell( width, height, data, borders, where, align, fill)
			$this->cell(30,7,'','RB',0,'L',true);
			$this->cell(30,7,'Large','TRB',0,'C',true);
			$this->cell(30,7,'Medium','TRB',0,'C',true);
			$this->cell(30,7,'Small','TRB',0,'C',true);
			$this->cell(30,7,'Tiny','TRB',0,'C',true);
			$this->cell(30,7,'Total','TRB',0,'C',true);
			$this->Ln(7);

			$this->ac_header(2,9); // pre-agility
			$this->cell(30,7,'Grado 0','RB',0,'L',true);
			$this->ac_row(0,9);
			$this->cell(30,7,$data[$name]['P.A.']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['P.A.']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['P.A.']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['P.A.']['T'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['P.A.']['C'],'RB',0,'C',true);
			$this->Ln(7);

			$this->ac_header(2,9); // grado I
			$this->cell(30,7,'Grado I','RB',0,'L',true);
			$this->ac_row(0,9);
			$this->cell(30,7,$data[$name]['GI']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GI']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GI']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GI']['T'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GI']['C'],'RB',0,'C',true);
			$this->Ln(7);
			
			$this->ac_header(2,9); // grado II
			$this->cell(30,7,'Grado II','RB',0,'L',true);
			$this->ac_row(0,9);
			$this->cell(30,7,$data[$name]['GII']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GII']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GII']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GII']['T'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GII']['C'],'RB',0,'C',true);
			$this->Ln(7);

			$this->ac_header(2,9); // Total
			$this->cell(30,7,'Total','RB',0,'L',true);
			$this->ac_row(0,9);
			$this->cell(30,7,$data[$name]['G']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['G']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['G']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['G']['T'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['G']['C'],'RB',0,'C',true);
			$this->Ln(10);
		}
	}
	
	function composeTable() {
		$est=$this->evalData();
		$this->addPage();
		$count=0;
		$this->printTableHeader($est,'Prueba','Datos globales de participación');
		$this->printTableData($est,'Prueba',$this->prueba->RSCE);
		$count++;
		foreach($this->jornadas as $jornada) {
			if ($jornada['Nombre']==='-- Sin asignar --') continue;
			$name="J{$jornada['Numero']}";
			$this->printTableHeader($est,$name,$jornada['Nombre']);
			$this->printTableData($est,$name,$this->prueba->RSCE);
			$count++;
			if ($count%4==0) $this->addPage();
		}
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
	function __construct($prueba,$inscritos,$jornadas) {
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
		$this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg1')); // azul
		$this->ac_SetTextColor($this->config->getEnv('pdf_hdrfg1')); // blanco
		$this->ac_SetDrawColor(0,0,0); // line color
		$this->SetFont('Arial','B',8); // bold 9px
		for($i=0;$i<count($this->cellHeader);$i++) {
			// en la cabecera texto siempre centrado
			$this->Cell($this->pos[$i],7,$this->cellHeader[$i],1,0,'C',true);
		}
		// Restauración de colores y fuentes
		$this->ac_SetFillColor($this->config->getEnv('pdf_rowcolor2')); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont(''); // remove bold
		$this->Ln();
		$this->myLogger->leave();
	}
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
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
	$jmgr= new Jornadas("printInscritosByPrueba",$pruebaid);
	$jornadas=$jmgr->selectByPrueba();
	$inscripciones = new Inscripciones("printInscritosByPrueba",$pruebaid);
	$inscritos= $inscripciones->enumerate();
	// Creamos generador de documento
	switch ($mode) {
		case 0: $pdf=new PrintInscritos($pruebaid,$inscritos,$jornadas); break;
		case 1: $pdf=new PrintCatalogo($pruebaid,$inscritos); break;
		case 2: $pdf=new PrintEstadisticas($pruebaid,$inscritos,$jornadas); break;
		default: throw new Exception ("Inscripciones::print() Invalid print mode selected $mode");
	}
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output($pdf->getPageName(),"D"); // "D" means open download dialog
} catch (Exception $e) {
	die ("Error accessing database: ".$e.getMessage());
}
?>