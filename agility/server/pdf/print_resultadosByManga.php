<?php
/*
print_resultadosByManga.php

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

class ResultadosByManga extends PrintCommon {
	
	protected $manga;
	protected $resultados;
	protected $mode;
	
	// geometria de las celdas
	protected $cellHeader;
	protected $pos	=array(  8,		17,		15,		30,		20,		15,		   6,      6,    6,       12,     7,    12,      24,			10 );
	protected $align=array(  'L',    'L',    'C',    'R',   'R',    'C',       'C',   'C',   'C',     'R',    'R',  'R',     'L',			'C');

	
	/**
	 * Constructor
	 * @param {obj} $manga datos de la manga
	 * @param {obj} $resultados resultados asociados a la manga/categoria pedidas
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$manga,$resultados,$mode) {
		parent::__construct('Portrait',"print_resultadosByManga",$prueba,$jornada);
		$this->manga=$manga;
		$this->resultados=$resultados;
		$this->mode=$mode;
		$catgrad=(Jornadas::hasGrades($this->jornada))?_('Cat').'/'._('Grade'):_('Cat').".";
		$this->cellHeader=
			array(_('Dorsal'),_('Name'),_('Lic'),_('Handler'),$this->strClub,$catgrad,_('Flt'),_('Tch'),_('Ref'),_('Time'),_('Vel'),_('Penal'),_('Calification'),_('Pos'));
	}
	
	// Cabecera de página
	function Header() {
        $str=($this->manga->Tipo==16)?_("Resultados"):_("Round scores");
		$this->print_commonHeader($str);
		$this->print_identificacionManga($this->manga,$this->getModeString(intval($this->mode)));
		
		// Si es la primera hoja pintamos datos tecnicos de la manga
		if ($this->PageNo()!=1) return;

		$this->SetFont($this->getFontName(),'B',9); // bold 9px
		$jobj=new Jueces("print_resultadosByManga");
		$juez1=$jobj->selectByID($this->manga->Juez1);
		$juez2=$jobj->selectByID($this->manga->Juez2);
		$this->Cell(20,7,_('Judge')." 1:","LT",0,'L',false);
		$str=($juez1['Nombre']==="-- Sin asignar --")?"":$juez1['Nombre'];
		$this->Cell(70,7,$str,"T",0,'L',false);
		$this->Cell(20,7,_('Judge')." 2:","T",0,'L',false);
		$str=($juez2['Nombre']==="-- Sin asignar --")?"":$juez2['Nombre'];
		$this->Cell(78,7,$str,"TR",0,'L',false);
		$this->Ln(7);
		$this->Cell(20,7,_('Distance').":","LB",0,'L',false);
		$this->Cell(25,7,"{$this->resultados['trs']['dist']} mts","B",0,'L',false);
		$this->Cell(20,7,_('Obstacles').":","B",0,'L',false);
		$this->Cell(25,7,$this->resultados['trs']['obst'],"B",0,'L',false);
		$this->Cell(10,7,_('SCT').":","B",0,'L',false);
		$this->Cell(20,7,"{$this->resultados['trs']['trs']} "._('Secs'),"B",0,'L',false);
		$this->Cell(10,7,_('MCT').":","B",0,'L',false);
		$this->Cell(20,7,"{$this->resultados['trs']['trm']} "._('Secs'),"B",0,'L',false);
		$this->Cell(20,7,_('Speed').":","B",0,'L',false);
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
		$this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg1')); // azul
		$this->ac_SetTextColor($this->config->getEnv('pdf_hdrfg1')); // blanco
		$this->ac_SetDrawColor("0x000000"); // line color
		$this->SetFont($this->getFontName(),'B',8); // bold 9px
		for($i=0;$i<count($this->cellHeader);$i++) {
			// en la cabecera texto siempre centrado
			if ($this->pos[$i]!=0) $this->Cell($this->pos[$i],7,$this->cellHeader[$i],1,0,'C',true);
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
		
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->SetLineWidth(.3);
		if ($this->federation->get('WideLicense')) {
            $this->pos[1]+=5;$this->pos[2]=0;$this->pos[3]+=5;$this->pos[4]+=5;
        } else if ($this->federation->isInternational()) {
            $this->pos[1]+=20;$this->pos[2]=0;$this->pos[4]-=5; // remove license. leave space for LongName
        }
		// Datos

		$rowcount=0;
		$numrows=($this->PageNo()==1)?30:33;
		$this->myLogger->trace("before foreach");
		foreach($this->resultados['rows'] as $row) {
			// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
			if( ($rowcount%$numrows) == 0 ) { // assume $numrows rows per page ( rowWidth = 7mmts )
				if ($rowcount>0) 
					$this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre
				$this->AddPage();
				$this->writeTableHeader();
			}
			// properly format special fields

			$puesto= ($row['Penalizacion']>=200)? "-":"{$row['Puesto']}";
			$veloc= ($row['Penalizacion']>=200)?"-":number_format($row['Velocidad'],2);
			$tiempo= ($row['Penalizacion']>=200)?"-":number_format($row['Tiempo'],$this->timeResolution);
			$penal=number_format($row['Penalizacion'],$this->timeResolution);
			$this->ac_row($rowcount,8);
			// print row data
			$this->SetFont($this->getFontName(),'',8); // set data font size
			$this->Cell($this->pos[0],6,$row['Dorsal'],			'LR',	0,		$this->align[0],	true);
			$this->SetFont($this->getFontName(),'B',8); // mark Nombre as bold
            $nombre=$row['Nombre'];
            if ($this->federation->isInternational()) $nombre .= " - " . $row['NombreLargo'];
			$this->Cell($this->pos[1],6,$nombre,			'LR',	0,		$this->align[1],	true);
			$this->SetFont($this->getFontName(),'',8); // set data font size
			if ($this->pos[2]!=0) $this->Cell($this->pos[2],6,$row['Licencia'],		'LR',	0,		$this->align[2],	true);
			$this->Cell($this->pos[3],6,$row['NombreGuia'],		'LR',	0,		$this->align[3],	true);
			$this->Cell($this->pos[4],6,$row['NombreClub'],		'LR',	0,		$this->align[4],	true);
			if (Jornadas::hasGrades($this->jornada->ID)) {
				$this->Cell($this->pos[5],6,$row['Categoria'].' - '.$row['Grado'],	'LR',	0,		$this->align[5],	true);
			} else {
				// $catstr=$row['Categoria'];
				$catstr=$this->federation->getCategory($row['Categoria']);
				$this->Cell($this->pos[5],6,$catstr,	'LR',	0,		$this->align[5],	true);
			}
			$this->Cell($this->pos[6],6,$row['Faltas'],			'LR',	0,		$this->align[6],	true);
			$this->Cell($this->pos[7],6,$row['Tocados'],		'LR',	0,		$this->align[7],	true);
			$this->Cell($this->pos[8],6,$row['Rehuses'],		'LR',	0,		$this->align[8],	true);
			$this->Cell($this->pos[9],6,$tiempo,				'LR',	0,		$this->align[9],	true);
			$this->Cell($this->pos[10],6,$veloc,				'LR',	0,		$this->align[10],	true);
			$this->Cell($this->pos[11],6,$penal,				'LR',	0,		$this->align[11],	true);
			$this->Cell($this->pos[12],6,$row['Calificacion'],	'LR',	0,		$this->align[12],	true);
			$this->SetFont($this->getFontName(),'B',11); // bold 11px
			$this->Cell($this->pos[13],6,$puesto,			'LR',	0,		$this->align[13],	true);
			$this->Ln();
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
	$resultados=$resobj->getResultados($mode); // throw exception if pending dogs

	// Creamos generador de documento
	$pdf = new ResultadosByManga($idprueba,$idjornada,$manga,$resultados,$mode);
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output("resultadosByManga.pdf","D"); // "D" means open download dialog
} catch (Exception $e) {
	die($e->getMessage());
}
?>