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
	protected $pos;
    protected $align;
    protected $bold;

    /**
	 * Constructor
     * @param integer $prueba prueba ID
     * @param integer $jornada Jornada ID
     * @param object $manga datos tecnicos de la manga
	 * @param array $resultados resultados asociados a la manga/categoria pedidas
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$manga,$resultados,$mode) {
		parent::__construct('Portrait',"print_resultadosGames",$prueba,$jornada);
		$name=(intval($manga->Tipo)==29)?'Snooker':'Gambler';
		$this->manga=$manga;
        $this->mode=$mode;
		$this->resultados=$resultados;
        $this->hasGrades=Jornadas::hasGrades($this->jornada);
		$seqname=(intval($manga->Tipo)==29)?_('Closing Seq'):'Gambler';
		$this->cellHeader=
			array(_('Dorsal'),_('Name'),_('Cat'),_('Handler'),$this->strClub,_('Opening Seq'),$seqname,_('Total'),_('Time'),_('Calification'),_('Pos'),);
		$this->pos=
            array(    10,         24,      10,      38,          25,                12,             12,     12,         15,     20,                 10);
		$this->align=
            array(  'C',         'L',      'C',     'R',         'R',             'R',            'R',    'R',        'R',    'R',    'C');
        $this->bold=
            array(  true,         true,      false,     false,    false,             false,        false,    false,        false,    false,    true);
        // set file name
        $this->set_FileName("ResultadosManga_{$name}.pdf");
	}
	
	// Cabecera de página
	function Header() {
        $this->print_commonHeader(_("Round Scores"));
        $this->print_identificacionManga($this->manga,$this->getModeString(intval($this->mode)));

        // Si es la primera hoja pintamos datos tecnicos de la manga
        if ($this->PageNo()!==1) return;

	    $this->myLogger->enter();
		$this->SetFont($this->getFontName(),'B',9); // bold 9px
		$jobj=new Jueces("print_resultadosByManga");
		$juez1=$jobj->selectByID($this->manga->Juez1);
		$juez2=$jobj->selectByID($this->manga->Juez2);
		$this->Cell(20,7,_('Judge')." 1:","LTB",0,'L',false);
		$str=($juez1['Nombre']==="-- Sin asignar --")?"":$juez1['Nombre'];
		$this->Cell(70,7,$str,"TB",0,'L',false);
		$this->Cell(20,7,_('Judge')." 2:","TB",0,'L',false);
		$str=($juez2['Nombre']==="-- Sin asignar --")?"":$juez2['Nombre'];
		$this->Cell(78,7,$str,"TRB",0,'L',false);
		$this->Ln(12);
		// en snooker-gambler no hay datos de TRS
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

	function writeCell($row,$count) {
        $data=array();
        array_push($data,$row['Dorsal']);
        array_push($data,($this->useLongNames)? "{$row['Nombre']} - {$row['NombreLargo']}":$row['Nombre']);
        array_push($data,$this->federation->getCategoryShort($row['Categoria'])); // no grades in games
        array_push($data,$row['NombreGuia']);
        array_push($data,($this->federation->isInternational())?$row['Pais']:$row['NombreClub']);
        array_push($data,$row['PRecorrido']);
        array_push($data,$row['PTiempo']);
        array_push($data,$row['Penalizacion']);
        array_push($data,number_format($row['Tiempo'],$this->timeResolution));
        array_push($data,$row['Calificacion']);
        array_push($data,"".$row['Puesto']."º");
        $this->ac_row($count,8);
        $this->SetFont($this->getFontName(),'',8); // set data font size
        $cnt=count($data)-1;
        for($n=0; $n<=$cnt;$n++) {
            if ($this->pos[$n]!=0) {
                $this->SetFont($this->getFontName(),($this->bold[$n])?'B':'',($n==$cnt)?10:8); // set data font size
                $this->Cell($this->pos[$n],6,$data[$n],'LTR',0,$this->align[$n],true);
            }
        }
        $this->Ln();
    }

    function composeTable() {
        $this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
        $this->SetLineWidth(.3);
	    $page=0;
	    $rowcount=36;
        $this->AddPage();
	    $this->writeTableHeader();
	    foreach($this->resultados['rows'] as $row) {
	        if($rowcount===0) {
	            $this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre
	            $this->AddPage();
                $this->writeTableHeader();
	            $page++;
                $rowcount=39;
	        }
	        $this->writeCell($row,$rowcount);
	        $rowcount --;
        }
        // linea final de cierre
        $this->Cell(array_sum($this->pos),0,'','T');
    }
}
?>