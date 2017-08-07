<?php
/*
PrintResultadosGames.php

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

/**
 * genera un pdf con los participantes ordenados segun los resultados de la manga
 */

require_once(__DIR__."/../fpdf.php");
require_once(__DIR__."/../../tools.php");
require_once (__DIR__."/../../logging.php");
require_once(__DIR__.'/../../database/classes/DBObject.php');
require_once(__DIR__.'/../../database/classes/Pruebas.php');
require_once(__DIR__.'/../../database/classes/Jueces.php');
require_once(__DIR__.'/../../database/classes/Jornadas.php');
require_once(__DIR__.'/../../database/classes/Mangas.php');
require_once(__DIR__.'/../../database/classes/Resultados.php');
require_once(__DIR__."/../print_common.php");

class PrintResultadosGames extends PrintCommon {
	
	protected $manga;
	protected $resultados;
	protected $mode;
	protected $hasGrades;
	
	// geometria de las celdas
	protected $cellHeader;
    //                     pair  dorsal name lic handler club  cat  fault touch refs time speed penal  pos calification
    //                       0     1     2    3    4     5      6     7     8     9    10   11    12    13    14
	protected $pos	=array(  10,   8,	17,	 15,  30,	 20,	12,	  6,    6,    6,   10,   7,   12,   7,    23 );
	protected $align=array(  'C', 'L',  'L', 'C', 'R',   'R',   'C',  'C',  'C',  'C', 'R', 'R',  'R',  'C',  'L');

	
	/**
	 * Constructor
     * @param integer $prueba prueba ID
     * @param integer $jornada Jornada ID
     * @param array $manga datos tecnicos de la manga
	 * @param array $resultados resultados asociados a la manga/categoria pedidas
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$manga,$resultados) {
		parent::__construct('Portrait',"print_resultadosGames",$prueba,$jornada);
		$this->manga=$manga;
		$this->resultados=$resultados;
        $this->hasGrades=Jornadas::hasGrades($this->jornada);
		$catgrad=($this->hasGrades)?_('Cat').'/'._('Grade'):_('Cat').".";
		$this->cellHeader=
			array(_('Pair'),_('Dorsal'),_('Name'),_('Lic'),_('Handler'),$this->strClub,$catgrad,_('Flt'),_('Tch'),_('Ref'),_('Time'),_('Vel'),_('Penal'),_('Pos'),_('Calification'));
        // set file name
        $this->set_FileName("ResultadosManga_Games.pdf");
	}
	
	// Cabecera de página
	function Header() {
        $str = ($this->manga->Tipo == 16) ? _("Resultados") : _("Round scores");
        $this->print_commonHeader($str);
        $this->print_identificacionManga($this->manga, "");
    }

    function writeCourseData() {
	    $this->myLogger->enter();
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
		$this->Ln(10); // en total tres lineas extras en la primera hoja
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

    function composeTableSnooker() {
        // PENDING: write
    }
    function composeTableGambler() {
        // PENDING: write
    }

    function composeTable() {
        if ($this->manga->Tipo==29) return $this->composeTableSnooker();
        if ($this->manga->Tipo==30) return $this->composeTableGambler();
        $this->myLogger->error("Round type:{$this->manga->Tipo} is not Snooker/Gamblers");
    }
}
?>