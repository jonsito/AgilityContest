<?php
/*
print_clasificacion.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__."/fpdf.php");
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Clubes.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jueces.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/Resultados.php');
require_once(__DIR__.'/../database/classes/Clasificaciones.php');
require_once(__DIR__."/print_common.php");

class PrintClasificacion extends PrintCommon {
	
	protected $manga1;
	protected $manga2;
	protected $resultados;
	protected $trs1;
	protected $trs2;
	protected $categoria;

	 /** Constructor
      * @param {int} $prueba prueba id
      * @param {int} $jornada jornada id
	 * @param {array} $mangas datos de la manga
	 * @param {array} $results resultados asociados a la manga/categoria pedidas
      * @param {int} $mode manga mode
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$mangas,$results,$mode) {
		parent::__construct('Landscape',"print_clasificacion",$prueba,$jornada);
		$dbobj=new DBObject("print_clasificacion");
		$this->manga1=$dbobj->__getObject("Mangas",$mangas[0]);
		$this->manga2=null;
		if ($mangas[1]!=0) $this->manga2=$dbobj->__getObject("Mangas",$mangas[1]);
		$this->resultados=$results['rows'];
		$this->trs1=$results['trs1'];
		$this->trs2=null;
		if ($mangas[1]!=0) $this->trs2=$results['trs2'];
		$this->categoria=$this->getModeString(intval($mode));
	}
	
	function print_datosMangas() {
		$this->setXY(10,40);
		$this->SetFont($this->getFontName(),'B',9); // bold 9px
		
		$jobj=new Jueces("print_Clasificaciones");
		$juez1=$jobj->selectByID($this->manga1->Juez1);
		$juez2=$jobj->selectByID($this->manga1->Juez2); // asume mismos jueces en dos mangas
		$tm1=Mangas::$tipo_manga[$this->manga1->Tipo][3] . " - " . $this->categoria;
		$tm2=null;
		if ($this->manga2!=null)
			$tm2=Mangas::$tipo_manga[$this->manga2->Tipo][3] . " - " . $this->categoria;

		$this->SetFont($this->getFontName(),'B',11); // bold 9px
		$this->Cell(80,7,_('Journey').": {$this->jornada->Nombre}",0,0,'',false);
		$this->SetFont($this->getFontName(),'B',9); // bold 9px
		$this->Cell(20,7,_('Judge')." 1:","LT",0,'L',false);
		$n=$juez1['Nombre'];
		$this->Cell(75,7,($n==="-- Sin asignar --")?"":$n,"T",0,'L',false);
		$this->Cell(20,7,_('Judge')." 2:","T",0,'L',false);
		$n=$juez2['Nombre'];
		$this->Cell(80,7,($n==="-- Sin asignar --")?"":$n,"TR",0,'L',false);
		$this->Ln();
		$trs=$this->trs1;
		$this->SetFont($this->getFontName(),'B',11); // bold 9px
		$this->Cell(80,7,_('Date').": {$this->jornada->Fecha}",0,0,'',false);
		$this->SetFont($this->getFontName(),'B',9); // bold 9px
		$this->Cell(70,7,$tm1,"LTB",0,'L',false);
		$this->Cell(25,7,_('Dist').".: {$trs['dist']}m","LTB",0,'L',false);
		$this->Cell(25,7,_('Obst').".: {$trs['obst']}","LTB",0,'L',false);
		$this->Cell(25,7,_('SCT').": {$trs['trs']}s","LTB",0,'L',false);
		$this->Cell(25,7,_('MCT').": {$trs['trm']}s","LTB",0,'L',false);
		$this->Cell(25,7,_('Vel').".: {$trs['vel']}m/s","LTRB",0,'L',false);
		$this->Ln();
		if ($this->trs2==null) { $this->Ln(); return; }
		$trs=$this->trs2;
		//$ronda=Mangas::$tipo_manga[$this->manga1->Tipo][4]; // la misma que la manga 2
		$ronda=$this->getGradoString(intval($this->manga1->Tipo));
		$this->SetFont($this->getFontName(),'B',11); // bold 9px
		$this->Cell(80,7,_('Round').": $ronda - {$this->categoria}",0,0,'',false);
		$this->SetFont($this->getFontName(),'B',9); // bold 9px
		$this->Cell(70,7,$tm2,"LTB",0,'L',false);
		$this->Cell(25,7,_('Dist').".: {$trs['dist']}m","LTB",0,'L',false);
		$this->Cell(25,7,_('Obst').".: {$trs['obst']}","LTB",0,'L',false);
		$this->Cell(25,7,_('SCT').": {$trs['trs']}s","LTB",0,'L',false);
		$this->Cell(25,7,_('MCT').": {$trs['trm']}s","LTB",0,'L',false);
		$this->Cell(25,7,_('Vel').".: {$trs['vel']}m/s","LTBR",0,'L',false);
		$this->Ln();
	}

	// on second and consecutive pages print a short description to avoid sheet missorder
	function print_datosMangas2() {
		$this->SetXY(35,20);
		$this->SetFont($this->getFontName(),'B',11); // bold 9px
		$this->Cell(80,7,"{$this->jornada->Nombre}",0,0,'',false);
		$this->SetXY(35,25);
		$this->Cell(80,7,"{$this->jornada->Fecha}",0,0,'',false);
		$ronda=Mangas::$tipo_manga[$this->manga1->Tipo][4]; // la misma que la manga 2
		$this->SetXY(35,30);
		$this->Cell(80,7,"$ronda - {$this->categoria}",0,0,'',false);
	}

	function Header() {
		$this->print_commonHeader(_("Final scores"));
	}
	
	// Pie de página: tampoco cabe
	function Footer() {
		$this->print_commonFooter();
	}
	
	function writeTableHeader() {
		$wide=$this->federation->get('WideLicense'); // some federations need extra space to show license id
		$tm1=Mangas::$tipo_manga[$this->manga1->Tipo][3];
		$tm2=null;
		if ($this->manga2!=null) $tm2=Mangas::$tipo_manga[$this->manga2->Tipo][3];
		
		$this->ac_header(1,12);
		$this->SetXY(10,65);// first page has 3 extra header lines
		if ($this->PageNo()!=1) {
			$this->print_datosMangas2();
			$this->SetXY(10,40);
		}
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		// first row of table header
		$this->SetFont($this->getFontName(),'BI',12); // default font
		$this->Cell(115,7,_('Competitor data'),0,0,'L',true);
		$this->Cell(57,7,$tm1,0,0,'C',true);
		$this->Cell(57,7,$tm2,0,0,'C',true);
		$this->Cell(46,7,_('Scores'),0,0,'C',true);
		$this->ln();
		$this->SetFont($this->getFontName(),'',8); // default font
		// datos del participante
		$this->Cell(10,7,_('Dorsal'),0,0,'C',true); 	// dorsal
        if ($this->federation->isInternational()) {
            $this->Cell(($wide)?50:40,7,_('Name'),0,0,'C',true);	// nombre
        } else {
            $this->Cell(($wide)?20:25,7,_('Name'),0,0,'C',true);	// nombre
            $this->Cell(($wide)?30:15,7,_('Lic'),0,0,'C',true);	// licencia
        }
		if (Jornadas::hasGrades($this->jornada)) {
			$this->Cell(10,7,_('Cat/Gr'),0,0,'C',true);	// categoria/grado
		} else {
			$this->Cell(10,7,_('Cat'),0,0,'C',true);	// categoria (jornadas Open / KO )
		}
		$this->Cell(($wide)?30:35,7,_('Handler'),0,0,'C',true);	// nombreGuia
		$this->Cell(($wide)?15:20,7,$this->strClub,0,0,'C',true);	// nombreClub
		// manga 1
		$this->Cell(7,7,_('F/T'),0,0,'C',true);	// 1- Faltas+Tocados
		$this->Cell(7,7,_('Ref'),0,0,'C',true);	// 1- Rehuses
		$this->Cell(12,7,_('Time'),0,0,'C',true);	// 1- Tiempo
		$this->Cell(9,7,_('Vel'),0,0,'C',true);	// 1- Velocidad
		$this->Cell(12,7,_('Penal'),0,0,'C',true);	// 1- Penalizacion
		$this->Cell(10,7,_('Calif'),0,0,'C',true);	// 1- calificacion
		// manga 2
		if ($this->manga2!=null) {
			$this->Cell(7,7,_('F/T'),0,0,'C',true);	// 2- Faltas+Tocados
			$this->Cell(7,7,_('Ref'),0,0,'C',true);	// 2- Rehuses
			$this->Cell(12,7,_('Time'),0,0,'C',true);	// 2- Tiempo
			$this->Cell(9,7,_('Vel'),0,0,'C',true);	// 2- Velocidad
			$this->Cell(12,7,_('Penal'),0,0,'C',true);	// 2- Penalizacion
			$this->Cell(10,7,_('Calif'),0,0,'C',true);	// 2- calificacion
		} else {
			$this->Cell(57,7,'',0,0,'C',true);	// espacio en blanco
		}
		// global
		$this->Cell(12,7,_('Time'),0,0,'C',true);	// Tiempo total
		$this->Cell(12,7,_('Penaliz'),0,0,'C',true);	// Penalizacion
		$this->Cell(14,7,_('Calific'),0,0,'C',true);	// Calificacion
		$this->Cell(8,7,_('Position'),0,0,'C',true);	// Puesto
		$this->Ln();	
		// restore colors
		$this->ac_SetFillColor($this->config->getEnv('pdf_rowcolor2')); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
	}
	
	function writeCell($idx,$row) {
		$wide=$this->federation->get('WideLicense'); // use extra space for wide license id
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		$offset=($this->PageNo()==1)?80:55;
		$this->SetXY(10, $offset + 6*$idx ); // first page has 3 extra header lines
		$fill=(($idx%2)!=0)?true:false;
		
		// fomateamos datos
		$puesto= ($row['Penalizacion']>=200)? "-":"{$row['Puesto']}º";
		$penal=number_format($row['Penalizacion'],$this->timeResolution);
		$tiempo=number_format($row['Tiempo'],$this->timeResolution);
		$v1= ($row['P1']>=200)?"-":number_format($row['V1'],1);
		$t1= ($row['P1']>=200)?"-":number_format($row['T1'],$this->timeResolution);
		$p1=number_format($row['P1'],$this->timeResolution);
		$v2= ($row['P2']>=200)?"-":number_format($row['V2'],1);
		$t2= ($row['P2']>=200)?"-":number_format($row['T2'],$this->timeResolution);
		$p2=number_format($row['P2'],$this->timeResolution);
		
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)

		$this->SetFont($this->getFontName(),'',8); // default font
		// datos del participante
		$this->Cell(10,6,$row['Dorsal'],0,0,'L',$fill); 	// dorsal
		$this->SetFont($this->getFontName(),'B',8); // Display Nombre in bold typeface
        if ($this->federation->isInternational()) {
            $nombre=$row['Nombre']." - ".$row['NombreLargo'];
            $this->Cell(($wide)?50:40,6,$nombre,0,0,'L',$fill);	// nombre
        } else {
            $this->Cell(($wide)?20:25,6,$row['Nombre'],0,0,'L',$fill);	// nombre
            $this->SetFont($this->getFontName(),'',($wide)?6:8); // default font
            $this->Cell(($wide)?30:15,6,$row['Licencia'],0,0,'C',$fill);	// licencia
        }
        $this->SetFont($this->getFontName(),'',8); // default font
		if (Jornadas::hasGrades($this->jornada)) {
			$this->Cell(10,6,"{$row['Categoria']} {$row['Grado']}",0,0,'C',$fill);	// categoria/grado
		} else {
			$this->Cell(10,6,"{$row['Categoria']}",0,0,'C',$fill);	// categoria/grado
		}
		$this->Cell(($wide)?30:35,6,$row['NombreGuia'],0,0,'R',$fill);	// nombreGuia
		$this->Cell(($wide)?15:20,6,$row['NombreClub'],0,0,'R',$fill);	// nombreClub
		// manga 1
		$this->Cell(7,6,$row['F1'],0,0,'C',$fill);	// 1- Faltas+Tocados
		$this->Cell(7,6,$row['R1'],0,0,'C',$fill);	// 1- Rehuses
		$this->Cell(12,6,$t1,0,0,'C',$fill);	// 1- Tiempo
		$this->Cell(9,6,$v1,0,0,'C',$fill);	// 1- Velocidad
		$this->Cell(12,6,$p1,0,0,'C',$fill);	// 1- Penalizacion
		$this->Cell(10,6,$row['C1'],0,0,'C',$fill);	// 1- calificacion
		// manga 2
		if ($this->manga2!=null) {
			$this->Cell(7,6,$row['F2'],0,0,'C',$fill);	// 2- Faltas+Tocados
			$this->Cell(7,6,$row['R2'],0,0,'C',$fill);	// 2- Rehuses
			$this->Cell(12,6,$t2,0,0,'C',$fill);	// 2- Tiempo
			$this->Cell(9,6,$v2,0,0,'C',$fill);	// 2- Velocidad
			$this->Cell(12,6,$p2,0,0,'C',$fill);	// 2- Penalizacion
			$this->Cell(10,6,$row['C2'],0,0,'C',$fill);	// 2- calificacion
		} else {
			$this->Cell(57,6,'',0,0,'C',$fill);	// espacio en blanco
		}
		// global
		$this->Cell(12,6,$tiempo,0,0,'C',$fill);	// Tiempo
		$this->Cell(12,6,$penal,0,0,'C',$fill);	// Penalizacion
		$this->Cell(14,6,$row['Calificacion'],0,0,'C',$fill);	// Calificacion
		$this->SetFont($this->getFontName(),'B',10); // default font
		$this->Cell(8,6,$puesto,0,0,'C',$fill);	// Puesto
		// lineas rojas
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->Line(10,$offset + 6*$idx,10,$offset + 6*($idx+1));
		$this->Line(10+115,$offset + 6*$idx,10+115,$offset + 6*($idx+1));
		$this->Line(10+172,$offset + 6*$idx,10+172,$offset + 6*($idx+1));
		$this->Line(10+229,$offset + 6*$idx,10+229,$offset + 6*($idx+1));
		$this->Line(10+275,$offset + 6*$idx,10+275,$offset + 6*($idx+1));
		
		$this->Ln();
	}
	
	function composeTable() {
		$this->myLogger->enter();

		$this->ac_SetFillColor($this->config->getEnv('pdf_rowcolor2')); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont($this->getFontName(),'',8); // default font
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
		$this->SetLineWidth(.3);
		
		$rowcount=0;
		$this->AddPage();
		$this->print_datosMangas();
		foreach($this->resultados as $row) {
			$numrows=($this->PageNo()==1)?18:22;
			if($rowcount==0) $this->writeTableHeader();	
			$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
			$this->writeCell( $rowcount % $numrows,$row);
			$rowcount++;
			if ($rowcount>=$numrows) {
				// pintamos linea de cierre 	
				$this->setX(10);
				$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
				$this->cell(275,0,'','T'); // celda sin altura y con raya
				$this->AddPage();
				$rowcount=0;
			}
		}
		// pintamos linea de cierre final
		$this->setX(10);
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
		$this->cell(275,0,'','T'); // celda sin altura y con raya
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
	$mode=http_request("Mode","i","0"); // 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small 5:tiny 6:L+M 7:S+T 8:L+M+S+T
	$c= new Clasificaciones("print_clasificacion_pdf",$prueba,$jornada);
	$result=$c->clasificacionFinal($rondas,$mangas,$mode);
	// Creamos generador de documento
	$pdf = new PrintClasificacion($prueba,$jornada,$mangas,$result,$mode);
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output("print_clasificacion.pdf","D"); // "D" means open download dialog
} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}
?>