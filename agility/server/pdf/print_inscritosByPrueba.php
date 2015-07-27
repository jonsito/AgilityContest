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
	protected $jornadas;
	protected $cat=array('-'=>'','L'=>'Large','M'=>'Medium','S'=>'Small','T'=>'Tiny');
	
	protected $width = array( 30,25,15,25,35,5,5,5,5,5,5,5,5); // anchos predefinidos de las celdas
	protected $cellHeader = array( 'J1','J2','J3','J4','J5','J6','J7','J8');
	
	/**
	 * Constructor
	 * @param {integer} $prueba Prueba ID
	 * @param {array} $inscritos Lista de inscritos en formato jquery array[count,rows[]]
	 * @throws Exception
	*/
	function __construct($prueba,$inscritos,$jornadas) {
		parent::__construct('Portrait',"print_catalogo",$prueba,0);
		if ( ($prueba==0) || ($inscritos===null) ) {
			$this->errormsg="printInscritosByPrueba: either prueba or inscription data are invalid";
			throw new Exception($this->errormsg);
		}
		$this->inscritos=$inscritos['rows'];
		$this->jornadas=$jornadas['rows'];
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

	function printClub($id) {
        $x=$this->GetX();
        $y=$this->GetY();
		// retrieve club data
		$cmgr=new Clubes('printCatalogo');
		$club=$cmgr->selectByID($id);

        // evaluate logo
        $icon=$this->federation->getLogo();
        if ( $club['Logo']==="") {
            $this->myLogger->error("inscritosByPrueba::printClub() club:$id {$club['Nombre']} no logo declared");
        } else if ( !file_exists(__DIR__.'/../../images/logos/'.$icon) ) {
            $this->myLogger->error("inscritosByPrueba::printClub() club:$id {$club['Nombre']} logo '$icon' not found");
        } else $icon=$club['Logo'];
		$this->myLogger->trace("ID:".$id." Club: ".$club['Nombre']);

		$this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg1')); // azul
		$this->ac_SetTextColor($this->config->getEnv('pdf_hdrfg1')); // blanco
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
		$this->SetLineWidth(.3); // ancho de linea
		
		// pintamos logo
		$this->SetXY(10,$y);
		$this->Cell(22,22,'','LTB',0,'C',false);
		$this->Image(__DIR__.'/../../images/logos/'.$icon,12,2+$y,18,18);

		// pintamos info del club
		$this->SetFont('Arial','B',9);
		$this->SetXY(32,$y);
		$this->Cell( 50, 5, $club['Direccion1'],	'LT', 0, 'L', true); // pintamos direccion1
		$this->SetXY(32,5+$y);
		$this->Cell( 50, 5, $club['Direccion2'],	'L', 0, 'L',	true);	// pintamos direccion2
		$this->SetXY(32,10+$y);
		$this->Cell( 50, 5, $club['Provincia'],	'L', 0, 'L',	true);	// pintamos provincia
		$this->SetFont('Arial','IB',24);
		$this->SetXY(82,$y);
		$this->Cell( 110, 15, $club['Nombre'],	'T', 0, 'R',	true);	// pintamos Nombre
		$this->Cell( 10, 15, '',	'TR', 0, 'R',	true);	// caja vacia de relleno
		
		// pintamos cabeceras de la tabla		
		$this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg2')); // gris
		$this->ac_SetTextColor($this->config->getEnv('pdf_hdrfg2')); // negro
		$this->SetFont('Arial','B',9);
		$this->SetXY(32,15+$y);
		$this->Cell( $this->width[0], 7, 'Nombre','LTB', 0, 'C',true);
		$this->Cell( $this->width[1], 7, 'Raza','LTB', 0, 'C',true);
		$this->Cell( $this->width[2], 7, 'Licencia','LTB', 0, 'C',true);
		$this->Cell( $this->width[3], 7, 'Cat/Grado','LTB', 0, 'C',true);
		$this->Cell( $this->width[4], 7, 'Guía','LTBR', 0, 'C',true);
		// print names of each declared journeys
		for($i=5;$i<count($this->width);$i++) {
			// en la cabecera texto siempre centrado
			if ($this->width[$i]==0) continue;
			$this->Cell($this->width[$i],7,$this->cellHeader[$i-5],1,0,'C',true);
		}
		$this->Ln();
	}
	
	function printParticipante($count,$row) {
		// $this->myLogger->trace("Position: ".$pos." Dorsal: ".$row['Dorsal']);
        $this->ac_row($count,10); // set proper row background
		$this->SetTextColor(0,0,0); // negro
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
        $this->SetLineWidth(.3); // ancho de linea

        $this->SetX(17);
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		$this->SetFont('Arial','B',15); //
		$this->Cell( 15, 7, $row['Dorsal'],	'TLB', 0, 'C',	true);
		$this->SetFont('Arial','BI',12); // bold 9px
		$this->Cell( $this->width[0], 7, $row['Nombre'],	'LB', 0, 'C',	true);
		$this->SetFont('Arial','',8); // bold 8px
		$this->Cell( $this->width[1], 7, substr($row['Raza'],0,20),		'LB', 0, 'R',	true);
        if ($this->federation->getFederation()==1) $this->SetFont('Arial','',6); // bold 6px
        $this->Cell( $this->width[2], 7, $row['Licencia'],	'LB', 0, 'C',	true);
        $this->SetFont('Arial','',8); // bold 8px
		$this->Cell( $this->width[3], 7, $this->cat[$row['Categoria']]." - ".$row['Grado'],	'LB', 0, 'C',	true);
		$this->SetFont('Arial','B',10); // bold 9px
		$this->Cell( $this->width[4], 7, substr($row['NombreGuia'],0,30),'LBR', 0, 'R',	true);
		
		$this->SetFont('Arial','',8); // bold 8px
		
		// print inscrption data on each declared journeys
		for($i=5;$i<count($this->width);$i++) {
			// en la cabecera texto siempre centrado
			if ($this->width[$i]==0) continue;
			$j=$i-4;
			$this->Cell($this->width[$i],7,($row["J$j"]==0)?"":"X",'LBR',0,'C',true);
		}
		$this->Ln(7);
	}
	
	function composeTable() {
		$this->myLogger->enter();
		
		// check for show journeys in configuration options
		$skip=intval($this->config->getEnv('pdf_journeys')); 
		foreach($this->jornadas as $row => $jornada) {
			// contamos las jornadas sin asignar
			if (($skip==0) || ($jornada['Nombre']==='-- Sin asignar --')) {
				$this->cellHeader[$row]='';
				$this->width[0]+=2;$this->width[1]+=1;$this->width[4]+=2;
				$this->width[5+$row]=0;
			} else {
				$this->cellHeader[$row]=$jornada['Nombre'];
			}
		}
        // si la prueba es de caza ajustamos para que quepa la licencia
        if ($this->federation->getFederation()==1) {
            $this->width[0] -= 7;  $this->width[1] -= 7; $this->width[2] +=14;
        }
		$this->addPage(); // start page
		$club=0;
        $count=0;
		foreach($this->inscritos as $row) {
            $pos = $this->GetY();
            if (($club == $row['Club'])) {
                // no hay cambio de club
                if ($pos > 270) {
                    $this->addPage();
                    $this->printClub($club);
                    $count = 0;
                }
            } else {
                $club = $row['Club'];
                // cambio de club
                $this->ln(7); // extra newline
                if ($pos > 250) $this->addPage();
                $this->printClub($club);
                $count = 0;
            }
            $this->printParticipante($count, $row);
            $count++;
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
		parent::__construct('Portrait','print_Estadisticas',$prueba,0);
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
		// do not account when undefined catetory or grade
		if ($item['Categoria']==='-') return;
		if ($item['Grado']==='-') return;
		if ($item['Grado']==='P.B.') return; // perro en blanco no se toma en cuenta
		if ($item['Grado']==='Baja') return; // TODO: no se deberia admitir la inscripcion
		if ($item['Grado']==='Ret.') return; // TODO: no se deberia admitir la inscripcion
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
			$this->cell(30,7,'','LRB',0,'L',true);
			$this->cell(30,7,'Large','TRB',0,'C',true);
			$this->cell(30,7,'Medium','TRB',0,'C',true);
			$this->cell(30,7,'Small','TRB',0,'C',true);
			$this->cell(30,7,'Total','TRB',0,'C',true);
			$this->Ln(7);

			$this->ac_header(2,9); // pre-agility
			$this->cell(30,7,'Pre-Agility','LRB',0,'L',true);
			$this->ac_row(0,9);
			$this->cell(30,7,$data[$name]['P.A.']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['P.A.']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['P.A.']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['P.A.']['C'],'RB',0,'C',true);
			$this->Ln(7);

			$this->ac_header(2,9); // grado I
			$this->cell(30,7,'Grado I','LRB',0,'L',true);
			$this->ac_row(1,9);
			$this->cell(30,7,$data[$name]['GI']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GI']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GI']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GI']['C'],'RB',0,'C',true);
			$this->Ln(7);
			
			$this->ac_header(2,9); // grado II
			$this->cell(30,7,'Grado II','LRB',0,'L',true);
			$this->ac_row(2,9);
			$this->cell(30,7,$data[$name]['GII']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GII']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GII']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GII']['C'],'RB',0,'C',true);
			$this->Ln(7);
			
			$this->ac_header(2,9); // grado III
			$this->cell(30,7,'Grado III','LRB',0,'L',true);
			$this->ac_row(3,9);
			$this->cell(30,7,$data[$name]['GIII']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GIII']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GIII']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GIII']['C'],'RB',0,'C',true);
			$this->Ln(7);

			$this->ac_header(2,9); // Total
			$this->cell(30,7,'Total','LRB',0,'L',true);
			$this->ac_row(4,9);
			$this->cell(30,7,$data[$name]['G']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['G']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['G']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['G']['C'],'RB',0,'C',true);
			$this->Ln(10);
		} else {
			$this->SetFont('Arial','B',9);
			// $this->cell( width, height, data, borders, where, align, fill)
			$this->cell(30,7,'','LRB',0,'L',true);
			$this->cell(30,7,'Large','TRB',0,'C',true);
			$this->cell(30,7,'Medium','TRB',0,'C',true);
			$this->cell(30,7,'Small','TRB',0,'C',true);
			$this->cell(30,7,'Tiny','TRB',0,'C',true);
			$this->cell(30,7,'Total','TRB',0,'C',true);
			$this->Ln(7);

			$this->ac_header(2,9); // pre-agility
			$this->cell(30,7,'Grado 0','LRB',0,'L',true);
			$this->ac_row(0,9);
			$this->cell(30,7,$data[$name]['P.A.']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['P.A.']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['P.A.']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['P.A.']['T'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['P.A.']['C'],'RB',0,'C',true);
			$this->Ln(7);

			$this->ac_header(2,9); // grado I
			$this->cell(30,7,'Grado I','LRB',0,'L',true);
			$this->ac_row(1,9);
			$this->cell(30,7,$data[$name]['GI']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GI']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GI']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GI']['T'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GI']['C'],'RB',0,'C',true);
			$this->Ln(7);
			
			$this->ac_header(2,9); // grado II
			$this->cell(30,7,'Grado II','LRB',0,'L',true);
			$this->ac_row(2,9);
			$this->cell(30,7,$data[$name]['GII']['L'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GII']['M'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GII']['S'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GII']['T'],'RB',0,'C',true);
			$this->cell(30,7,$data[$name]['GII']['C'],'RB',0,'C',true);
			$this->Ln(7);

			$this->ac_header(2,9); // Total
			$this->cell(30,7,'Total','LRB',0,'L',true);
			$this->ac_row(3,9);
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
	protected $jornadas;

	// geometria de las celdas
	protected $cellHeader;
	protected $pos =	array(  11,       21,     16,    38,   29,     8,     8,     9,       11,     5,  5,  5,  5,  5,  5,  5,  5 );
	protected $align=	array(  'R',      'L',    'C',   'R',  'R',   'C',    'L',   'C',    'L',    'C','C','C','C','C','C','C','C');
	
	/**
	 * Constructor
	 * @param {integer} $prueba Prueba ID
	 * @param {array} $inscritos Lista de inscritos en formato jquery array[count,rows[]]
	 * @throws Exception
	 */
	function __construct($prueba,$inscritos,$jornadas) {
		parent::__construct('Portrait','print_inscritosByPrueba',$prueba,0);
		if ( ($prueba==0) || ($inscritos===null) ) {
			$this->errormsg="printInscritosByPrueba: either prueba or inscription data are invalid";
			throw new Exception($this->errormsg);
		}
		$this->inscritos=$inscritos['rows'];
		$this->jornadas=$jornadas['rows'];
		$this->setPageName("inscritosByPrueba.pdf");
		$this->cellHeader=
			array(_('Dorsal'),_('Nombre'),_('Lic.'),_('Guía'),_('Club'),_('Cat.'),_('Grado'),_('Celo'),_('Observaciones'),_('Sab.'),_('Dom.'));	
	}
	
	// Cabecera de página
	function Header() {
		$this->myLogger->enter();
		$this->print_commonHeader(_("Listado de Participantes"));
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
		$this->ac_SetDrawColor("0x000000"); // line color
		$this->SetFont('Arial','B',9); // bold 9px
		for($i=0;$i<count($this->cellHeader);$i++) {
			// en la cabecera texto siempre centrado
			if ($this->pos[$i]==0) continue;
			$this->Cell($this->pos[$i],6,$this->cellHeader[$i],1,0,'C',true);
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
		
		// contamos las jornadas sin asignar	
		foreach($this->jornadas as $row => $jornada) {
			if ($jornada['Nombre']==='-- Sin asignar --') {
				$this->pos[8]+=5;
				$this->pos[9+$row]=0;
				continue;
			} else {
				$this->cellHeader[9+$row]=$jornada['Nombre'];
			}
		}
        // si estamos en caza ajustamos para que quepa la licencia
        if ($this->federation->getFederation()==1) {
            $this->pos[0]-=1; $this->pos[1]-=2; $this->pos[2]+=20; $this->pos[3]-=5; $this->pos[4]-=5; $this->pos[8]-=7;
        }
		// Datos
		$fill = false;
		$rowcount=0;
		foreach($this->inscritos as $row) {
			// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
			if( ($rowcount%39) == 0 ) { // assume 39 rows per page ( rowWidth = 7mmts )
				if ($rowcount>0) 
					$this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre
				$this->addPage();
				$this->writeTableHeader();
			} 
			// $this->Cell($this->pos[0],7,$row['IDPerro'],	'LR',0,$this->align[0],$fill);
			// $this->Cell($this->pos[0],7,$rowcount+1,		'LR',	0,		$this->align[0],$fill); // display order instead of idperro

			$this->Cell($this->pos[0],6,$row['Dorsal'],		'LR',	0,		$this->align[1],	$fill);
			$this->SetFont('Arial','B',8); // bold 8px
			$this->Cell($this->pos[1],6,$row['Nombre'],		'LR',	0,		$this->align[1],	$fill);
            if ($this->federation->getFederation()==1) $this->SetFont('Arial','',7); // normal 7px
            else $this->SetFont('Arial','',8); // normal 8px
			$this->Cell($this->pos[2],6,$row['Licencia'],	'LR',	0,		$this->align[2],	$fill);
            $this->SetFont('Arial','',8); // normal 8px
			$this->Cell($this->pos[3],6,$row['NombreGuia'],	'LR',	0,		$this->align[3],	$fill);
			$this->Cell($this->pos[4],6,$row['NombreClub'],	'LR',	0,		$this->align[4],	$fill);
			$this->Cell($this->pos[5],6,$row['Categoria'],	'LR',	0,		$this->align[5],	$fill);
			$this->Cell($this->pos[6],6,$row['Grado'],		'LR',	0,		$this->align[6],	$fill);
			$this->Cell($this->pos[7],6,($row['Celo']==0)?"":"X",'LR',0,	$this->align[7],	$fill);
			$this->Cell($this->pos[8],6,$row['Observaciones'],'LR',	0,		$this->align[8],	$fill);
			if ($this->pos[9]!=0)
				$this->Cell($this->pos[9],6,($row['J1']==0)?"":"X",	'LR',0,		$this->align[9],	$fill);
			if ($this->pos[10]!=0)
				$this->Cell($this->pos[10],6,($row['J2']==0)?"":"X",'LR',0,		$this->align[10],	$fill);
			if ($this->pos[11]!=0)
				$this->Cell($this->pos[11],6,($row['J3']==0)?"":"X",'LR',0,		$this->align[11],	$fill);
			if ($this->pos[12]!=0)
				$this->Cell($this->pos[12],6,($row['J4']==0)?"":"X",'LR',0,		$this->align[12],	$fill);
			if ($this->pos[13]!=0)
				$this->Cell($this->pos[13],6,($row['J5']==0)?"":"X",'LR',0,		$this->align[13],	$fill);
			if ($this->pos[14]!=0)
				$this->Cell($this->pos[14],6,($row['J6']==0)?"":"X",'LR',0,		$this->align[14],	$fill);
			if ($this->pos[15]!=0)
				$this->Cell($this->pos[15],6,($row['J7']==0)?"":"X",'LR',0,		$this->align[15],	$fill);
			if ($this->pos[16]!=0)
				$this->Cell($this->pos[16],6,($row['J8']==0)?"":"X",'LR',0,		$this->align[16],	$fill);
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
		case 1: $pdf=new PrintCatalogo($pruebaid,$inscritos,$jornadas); break;
		case 2: $pdf=new PrintEstadisticas($pruebaid,$inscritos,$jornadas); break;
		default: throw new Exception ("Inscripciones::print() Invalid print mode selected $mode");
	}
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output($pdf->getPageName(),"D"); // "D" means open download dialog
} catch (Exception $e) {
	die ("Error accessing database: ".$e->getMessage());
}
?>
