<?php
/*
PrintResultadosKO.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

class PrintResultadosKO extends PrintCommon {
	
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
     * @param int|array $modes lista de mangas/alturas a imprimir. si es un entero es una simple manga/categoria
     * @param string $title
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$manga,$resultados,$modes,$title) {
		parent::__construct('Portrait',"print_resultadosKO",$prueba,$jornada);
		$this->manga=$manga;
		$this->resultados=$resultados;
        $this->hasGrades=Jornadas::hasGrades($this->jornada);
		$catgrad=($this->hasGrades)?_('Cat').'/'._('Grade'):_('Cat').".";
		$this->cellHeader=
			array(_('Pair'),_('Dorsal'),_('Name'),_('Lic'),_('Handler'),$this->strClub,$catgrad,_('Flt'),_('Tch'),_('Ref'),_('Time'),_('Vel'),_('Penal'),_('Pos'),_('Calification'));
        // set file name
        $this->set_FileName("ResultadosManga_KO.pdf");
        $this->icon2=getIconPath($this->federation->get('Name'),"null.png");
	}
	
	// Cabecera de página
	function Header() {
        $str = ($this->manga->Tipo == 16) ? _("Results") : _("Round scores");
        $this->print_commonHeader($str);
        $this->print_identificacionManga($this->manga, "");
    }

    function writeCourseData() {
	    $this->myLogger->enter();
		$this->SetFont($this->getFontName(),'B',9); // bold 9px
		$jobj=new Jueces("print_resultadosKO");
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
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
		$this->AddPage();
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->SetLineWidth(.3);
        //  old    0     1    2    3      4      5
        //  pair  dorsal name lic handler club  cat  fault touch refs time speed penal  pos calification
        //   0     1     2    3    4      5      6     7     8     9    10   11    12    13    14
        if ($this->federation->hasWideLicense()) { // on wide license, remove it enlarge name,handler, & club
            $this->pos[2]+=5;$this->pos[3]=0;$this->pos[4]+=5;$this->pos[5]+=5;
        } else if ($this->useLongNames) { // else if long name remove license and shorten club
            $this->pos[2]+=20;$this->pos[3]=0;$this->pos[5]-=5; // remove license shorten club.leave space for LongName
        }
		// Datos
		$rowcount=0;
		$rowsperpage=33; // let space for TRS/TRM data
		$firstOfpair=true;
		foreach($this->resultados['rows'] as $row) {
            if ($row['Dorsal']==='*') { // pair separator. add newline and draw pair name
                $firstOfpair=true;
                $this->Ln(2);
                continue; // skip row as senseless
            }
		    if ($rowcount==0) {
                if ($this->PageNo()==1) $this->writeCourseData();
		        $this->writeTableHeader();
            }
            if( $firstOfpair) { // on first colum of each pair, draw pair number
                $this->ac_row( intval($rowcount/2),9); // alternate pairs background color
                $this->SetFont($this->getFontName(),'B',12); // bold 12px
                $this->Cell($this->pos[0],10,(intval($rowcount/2)+1)." - ",'LRTB',0,$this->align[0],true); // display order
                $border='LRT';
            } else {
                $this->SetX($this->GetX()+$this->pos[0]);
                $border='LRB';
            }
			// properly format special fields
			$puesto= ($row['Penalizacion']>=200)? "-":"{$row['Puesto']}";
			$veloc= ($row['Penalizacion']>=200)?"-":number_format2($row['Velocidad'],2);
			$tiempo= ($row['Penalizacion']>=200)?"-":number_format2($row['Tiempo'],$this->timeResolution);
			$penal=number_format2($row['Penalizacion'],$this->timeResolution);

			$this->ac_row($rowcount,7);
			// print row data
			$this->SetFont($this->getFontName(),'',7); // set data font size
			$this->Cell($this->pos[1],5,$row['Dorsal'],			$border,	0,		$this->align[1],	true);
			$this->SetFont($this->getFontName(),'B',7); // mark Nombre as bold
            $nombre=$row['Nombre'];
            if ($this->useLongNames) $nombre .= " - " . $row['NombreLargo'];
			$this->Cell($this->pos[2],5,$nombre,			$border,	0,		$this->align[2],	true);
			$this->SetFont($this->getFontName(),'',7); // set data font size
			if ($this->pos[3]!=0) $this->Cell($this->pos[3],5,$row['Licencia'],$border,0,	$this->align[3],true);
			$this->Cell($this->pos[4],5,$this->getHandlerName($row),		$border,	0,		$this->align[4],	true);
			$this->Cell($this->pos[5],5,$row['NombreClub'],		$border,	0,		$this->align[5],	true);
			if ($this->hasGrades) {
                $cat=$this->federation->getCategoryShort($row['Categoria']);
                $grad=$this->federation->getGradeShort($row['Grado']);
				$this->Cell($this->pos[6],5,"{$cat} - {$grad}",	$border,	0,	$this->align[6],	true);
			} else {
				// $catstr=$row['Categoria'];
				$catstr=$this->federation->getCategory($row['Categoria']);
				$this->Cell($this->pos[6],5,$catstr,	$border,	0,		$this->align[6],	true);
			}
			$this->Cell($this->pos[7],5,$row['Faltas'],		$border,	0,		$this->align[7],	true);
			$this->Cell($this->pos[8],5,$row['Tocados'],		$border,	0,		$this->align[8],	true);
			$this->Cell($this->pos[9],5,$row['Rehuses'],		$border,	0,		$this->align[9],	true);
			$this->Cell($this->pos[10],5,$tiempo,				$border,	0,		$this->align[10],	true);
			$this->Cell($this->pos[11],5,$veloc,				$border,	0,		$this->align[11],	true);
			$this->Cell($this->pos[12],5,$penal,				$border,	0,		$this->align[12],	true);
            $this->SetFont($this->getFontName(),'B',8); // bold 11px
            $this->Cell($this->pos[13],5,$puesto,			$border,	0,		$this->align[13],	true);
            $this->SetFont($this->getFontName(),'I',7); // bold 11px
            if($row['CShort']!==_('Pass')) {
                $this->Cell($this->pos[14],5,$row['Calificacion'],	$border,	0,		$this->align[14],	true);
            } else {
                $this->Cell($this->pos[14],5,$row['Calificacion'],	$border,	0,		$this->align[14],	true);
                $this->Image(getIconPath($this->federation->get('Name'),"ok.png"),$this->GetX()-4.5,$this->GetY()+0.5,4,4);
            }
			$this->Ln();
			$rowcount++;
			$firstOfpair = !$firstOfpair;

            // check for end of page
            if ( ($rowcount>=$rowsperpage) ) { // assume 34/38 rows per page ( rowWidth = 6mmts )
                $rowsperpage=37; // next pages has no TRS/TRM data
                $rowcount=0;
                $this->AddPage();
            }
		}
		// Línea de cierre
		$this->myLogger->leave();
	}

    function composeMergedTable($mergecats) {
        return $this->composeTable(); // not used, but needed for compatibility
    }
}
?>