<?php
/*
print_clasificacion.php

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
 * genera un PDF con la clasificacion final individual
 */

require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/../../logging.php");
require_once(__DIR__."/../fpdf.php");
require_once(__DIR__.'/../../database/classes/DBObject.php');
require_once(__DIR__.'/../../database/classes/Clubes.php');
require_once(__DIR__.'/../../database/classes/Pruebas.php');
require_once(__DIR__.'/../../database/classes/Jueces.php');
require_once(__DIR__.'/../../database/classes/Jornadas.php');
require_once(__DIR__.'/../../database/classes/Mangas.php');
require_once(__DIR__.'/../../database/classes/Resultados.php');
require_once(__DIR__.'/../../database/classes/Clasificaciones.php');
require_once(__DIR__."/../print_common.php");

class PrintClasificacion extends PrintCommon {
	
	protected $manga1;
    protected $manga2;
    protected $manga3;
	protected $resultados;
	protected $trs1;
    protected $trs2;
    protected $trs3;
	protected $categoria;
	protected $hasGrades;

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
		$this->resultados=$results['rows'];
        $this->manga1=($mangas[0]!=0)?$dbobj->__getObject("Mangas",$mangas[0]):null;
        $this->manga2=($mangas[1]!=0)?$dbobj->__getObject("Mangas",$mangas[1]):null;
        $this->manga3=($mangas[2]!=0)?$dbobj->__getObject("Mangas",$mangas[2]):null;
        $this->trs1=($mangas[0]!=0)?$results['trs1']:null;
        $this->trs2=($mangas[1]!=0)?$results['trs2']:null;
        $this->trs3=($mangas[2]!=0)?$results['trs3']:null;
		$this->categoria=$this->getModeString(intval($mode));
		$this->hasGrades=Jornadas::hasGrades($this->jornada);
	}
	
	function print_datosMangas() {

	    // objeto para buscar jueces
		$jobj=new Jueces("print_Clasificaciones");

        // imprimimos informacion de la manga
        $this->setXY(10,40);
		$this->SetFont($this->getFontName(),'B',11); // bold 9px
		$this->Cell(80,6,_('Journey').": {$this->jornada->Nombre}",0,0,'',false);
		$this->Ln(6);
        $this->Cell(80,6,_('Date').": {$this->jornada->Fecha}",0,0,'',false);
        $this->Ln(6);
        $ronda=$this->getGradoString(intval($this->manga1->Tipo)); // todas las mangas comparten grado
        $this->Cell(80,6,_('Round').": $ronda - {$this->categoria}",0,0,'',false);

        // ahora los datos de cada manga individual
        // manga 1:
        if ($this->manga1!=null) {
            // pintamos los datos de TRS
            $trs=$this->trs1;
            $this->setXY(80,40);
            $this->SetFont($this->getFontName(),'B',10); // bold 9px
            $this->Cell(90,8,"","LTB",0,'L',false);// caja vacia
            $this->Cell(20,8,_('Dist').".: {$trs['dist']}m","LTB",0,'L',false);
            $this->Cell(20,8,_('Obst').".: {$trs['obst']}","LTB",0,'L',false);
            $this->Cell(25,8,_('SCT').": {$trs['trs']}s","LTB",0,'L',false);
            $this->Cell(25,8,_('MCT').": {$trs['trm']}s","LTB",0,'L',false);
            $this->Cell(25,8,_('Vel').".: {$trs['vel']}m/s","LTRB",0,'L',false);
            // ahora el nombre de la manga y los jueces
            $nmanga=_(Mangas::getTipoManga($this->manga1->Tipo,3,$this->federation)) . " - " . $this->categoria;
            $juez1=$jobj->selectByID($this->manga1->Juez1); $juez2=$jobj->selectByID($this->manga1->Juez2);
            $this->setXY(81,41);
            $this->SetFont($this->getFontName(),'B',10); // bold 9px
            $this->Cell( 88,4,$nmanga,"",0,'L',false);
            $this->setXY(81,44);
            $this->SetFont($this->getFontName(),'I',8); // bold 9px
            $jueces = _('Judge') .": ". $juez1['Nombre'];
            $jueces .= ($juez2['Nombre']==="-- Sin asignar --")? "" : " - {$juez2['Nombre']}";
            $this->Cell( 88,4,$jueces,"",0,'R',false);
        } else { $this->Ln(8); }
        // manga 2:
        if ($this->manga2!=null) {
            // pintamos los datos de TRS
            $trs = $this->trs2;
            $this->setXY(80, 48);
            $this->SetFont($this->getFontName(),'B',10); // bold 9px
            $this->Cell(90,8,"","LTB",0,'L',false);// caja vacia
            $this->Cell(20, 8, _('Dist') . ".: {$trs['dist']}m", "LTB", 0, 'L', false);
            $this->Cell(20, 8, _('Obst') . ".: {$trs['obst']}", "LTB", 0, 'L', false);
            $this->Cell(25, 8, _('SCT') . ": {$trs['trs']}s", "LTB", 0, 'L', false);
            $this->Cell(25, 8, _('MCT') . ": {$trs['trm']}s", "LTB", 0, 'L', false);
            $this->Cell(25, 8, _('Vel') . ".: {$trs['vel']}m/s", "LTBR", 0, 'L', false);
            // ahora el nombre de la manga y los jueces
            $nmanga=_(Mangas::getTipoManga($this->manga2->Tipo,3,$this->federation)) . " - " . $this->categoria;
            $juez1=$jobj->selectByID($this->manga2->Juez1); $juez2=$jobj->selectByID($this->manga2->Juez2);
            $this->setXY(81,49);
            $this->SetFont($this->getFontName(),'B',10); // bold 9px
            $this->Cell( 88,4,$nmanga,"",0,'L',false);
            $this->setXY(81,52);
            $this->SetFont($this->getFontName(),'I',8); // bold 9px
            $jueces = _('Judge') .": ". $juez1['Nombre'];
            $jueces .= ($juez2['Nombre']==="-- Sin asignar --")? "" : " - {$juez2['Nombre']}";
            $this->Cell( 88,4,$jueces,"",0,'R',false);
        } else { $this->Ln(8); }
        // manga 3:
        if ($this->manga3!=null) {
            // pintamos los datos de TRS
            $trs=$this->trs3;
            $this->setXY(80,56);
            $this->SetFont($this->getFontName(),'B',10); // bold 9px
            $this->Cell(90,8,"","LTB",0,'L',false); // caja vacia
            $this->Cell(20,8,_('Dist').".: {$trs['dist']}m","LTB",0,'L',false);
            $this->Cell(20,8,_('Obst').".: {$trs['obst']}","LTB",0,'L',false);
            $this->Cell(25,8,_('SCT').": {$trs['trs']}s","LTB",0,'L',false);
            $this->Cell(25,8,_('MCT').": {$trs['trm']}s","LTB",0,'L',false);
            $this->Cell(25,8,_('Vel').".: {$trs['vel']}m/s","LTBR",0,'L',false);
            // ahora el nombre de la manga y los jueces
            $nmanga=_(Mangas::getTipoManga($this->manga3->Tipo,3,$this->federation)) . " - " . $this->categoria;
            $juez1=$jobj->selectByID($this->manga3->Juez1); $juez2=$jobj->selectByID($this->manga3->Juez2);
            $this->setXY(81,57);
            $this->SetFont($this->getFontName(),'B',10); // bold 9px
            $this->Cell( 88,4,$nmanga,"",0,'L',false);
            $this->setXY(81,60);
            $this->SetFont($this->getFontName(),'I',8); // bold 9px
            $jueces = _('Judge') .": ". $juez1['Nombre'];
            $jueces .= ($juez2['Nombre']==="-- Sin asignar --")? "" : " - {$juez2['Nombre']}";
            $this->Cell( 88,4,$jueces,"",0,'R',false);
        } else { $this->Ln(8); }
        $this->Ln();
	}

	// on second and consecutive pages print a short description to avoid sheet missorder
	function print_datosMangas2() {
		$this->SetXY(35,20);
		$this->SetFont($this->getFontName(),'B',11); // bold 9px
		$this->Cell(80,7,"{$this->jornada->Nombre}",0,0,'',false);
		$this->SetXY(35,25);
		$this->Cell(80,7,"{$this->jornada->Fecha}",0,0,'',false);
		$ronda=_(Mangas::getTipoManga($this->manga1->Tipo,4,$this->federation)); // la misma que la manga 2
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
        $tm1=($this->manga1!==null)?_(Mangas::getTipoManga($this->manga1->Tipo,3,$this->federation)):"";
        $tm2=($this->manga2!==null)?_(Mangas::getTipoManga($this->manga2->Tipo,3,$this->federation)):"";
        $tm3=($this->manga3!==null)?_(Mangas::getTipoManga($this->manga3->Tipo,3,$this->federation)):"";
        $factor=($tm3==="")?1:0.75;
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
		$this->Cell(57*$factor,7,$tm1,0,0,'C',true);
        $this->Cell(57*$factor,7,$tm2,0,0,'C',true);
        if($tm3!=="") $this->Cell(57*$factor,7,$tm3,0,0,'C',true);
		$this->Cell(46*$factor,7,_('Scores'),0,0,'C',true);
		$this->ln();
		$this->SetFont($this->getFontName(),'',8); // default font

		// datos del participante
		$this->Cell(8,7,_('Dorsal'),0,0,'C',true); 	// dorsal
        if ($this->useLongNames) {
            if ($this->federation->isInternational()) {
                $this->Cell(($wide)?52:42,7,_('Name'),0,0,'C',true);	// nombre
            } else {
                $this->Cell(($wide)?37:32,7,_('Name'),0,0,'C',true);	// nombre
                $this->Cell(($wide)?15:10,7,_('Lic'),0,0,'C',true);	// licencia
            }
        } else {
            $this->Cell(($wide)?22:27,7,_('Name'),0,0,'C',true);	// nombre
            $this->Cell(($wide)?30:15,7,_('Lic'),0,0,'C',true);	// licencia
        }
		if ($this->hasGrades) {
			$this->Cell(10,7,_('Cat/Gr'),0,0,'C',true);	// categoria/grado
		} else {
			$this->Cell(10,7,_('Cat'),0,0,'C',true);	// categoria (jornadas Open / KO )
		}
		$this->Cell(($wide)?30:35,7,_('Handler'),0,0,'C',true);	// nombreGuia
		$this->Cell(($wide)?15:20,7,$this->strClub,0,0,'C',true);	// nombreClub

		// manga 1
        if ($this->manga1!==null) {
            $this->Cell(6*$factor,7,_('F/T'),0,0,'C',true);	// 1- Faltas+Tocados
            $this->Cell(6*$factor,7,_('Ref'),0,0,'C',true);	// 1- Rehuses
            $this->Cell(10*$factor,7,_('Time'),0,0,'C',true);	// 1- Tiempo
            $this->Cell(8*$factor,7,_('Vel'),0,0,'C',true);	// 1- Velocidad
            $this->Cell(10*$factor,7,_('Penal'),0,0,'C',true);	// 1- Penalizacion
            $this->Cell(10*$factor,7,_('Calif'),0,0,'C',true);	// 1- calificacion
            $this->Cell(7*$factor,7,_('Pos'),0,0,'C',true);	// 1- Posicion
        } else {
            $this->Cell(57*$factor,7,'',0,0,'C',true);	// espacio en blanco
        }

		// manga 2
		if ($this->manga2!==null) {
			$this->Cell(6*$factor,7,_('F/T'),0,0,'C',true);	// 2- Faltas+Tocados
			$this->Cell(6*$factor,7,_('Ref'),0,0,'C',true);	// 2- Rehuses
			$this->Cell(10*$factor,7,_('Time'),0,0,'C',true);	// 2- Tiempo
			$this->Cell(8*$factor,7,_('Vel'),0,0,'C',true);	// 2- Velocidad
			$this->Cell(10*$factor,7,_('Penal'),0,0,'C',true);	// 2- Penalizacion
			$this->Cell(10*$factor,7,_('Calif'),0,0,'C',true);	// 2- calificacion
            $this->Cell(7*$factor,7,_('Pos'),0,0,'C',true);	// 2- Posicion
		} else {
			$this->Cell(57*$factor,7,'',0,0,'C',true);	// espacio en blanco
		}

		// manga 3
        if ($this->manga3!==null) {
            $this->Cell(6*$factor,7,_('F/T'),0,0,'C',true);	// 3- Faltas+Tocados
            $this->Cell(6*$factor,7,_('Ref'),0,0,'C',true);	// 3- Rehuses
            $this->Cell(10*$factor,7,_('Time'),0,0,'C',true);	// 3- Tiempo
            $this->Cell(8*$factor,7,_('Vel'),0,0,'C',true);	// 3- Velocidad
            $this->Cell(10*$factor,7,_('Penal'),0,0,'C',true);	// 3- Penalizacion
            $this->Cell(10*$factor,7,_('Calif'),0,0,'C',true);	// 3- calificacion
            $this->Cell(7*$factor,7,_('Pos'),0,0,'C',true);	// 3- Posicion
        }
        // do not print empty data if no round 3

		// global
		$this->Cell(12*$factor,7,_('Time'),0,0,'C',true);	// Tiempo total
		$this->Cell(12*$factor,7,_('Penaliz'),0,0,'C',true);	// Penalizacion
		$this->Cell(14*$factor,7,_('Calific'),0,0,'C',true);	// Calificacion
		$this->Cell(8*$factor,7,_('Position'),0,0,'C',true);	// Puesto
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
		$penal=number_format2($row['Penalizacion'],$this->timeResolution);
		$tiempo=number_format2($row['Tiempo'],$this->timeResolution);
		$factor=1;
		if ($this->manga1!==null) {
            $v1= ($row['P1']>=200)?"-":number_format2($row['V1'],2);
            $t1= ($row['P1']>=200)?"-":number_format2($row['T1'],$this->timeResolution);
            $p1=number_format2($row['P1'],$this->timeResolution);
        } else { $v1="";$t1="";$p1=""; }
        if ($this->manga2!==null) {
            $v2= ($row['P2']>=200)?"-":number_format2($row['V2'],2);
            $t2= ($row['P2']>=200)?"-":number_format2($row['T2'],$this->timeResolution);
            $p2=number_format2($row['P2'],$this->timeResolution);
        } else { $v2="";$t2="";$p2=""; }
        if ($this->manga3!==null) {
		    $factor=0.75;
            $v3= ($row['P3']>=200)?"-":number_format2($row['V3'],2);
            $t3= ($row['P3']>=200)?"-":number_format2($row['T3'],$this->timeResolution);
            $p3=number_format2($row['P3'],$this->timeResolution);
        } else { $v3="";$t3="";$p3=""; }
		
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)

		$this->SetFont($this->getFontName(),'',8); // default font
		// datos del participante
		$this->Cell(8,6,$row['Dorsal'],0,0,'L',$fill); 	// dorsal
		$this->SetFont($this->getFontName(),'B',7); // Display Nombre in bold typeface
        if ($this->useLongNames) {
            $nombre=$row['Nombre']." - ".$row['NombreLargo'];
            if ($this->federation->isInternational()) {
                $this->Cell(($wide)?52:42,6,$nombre,0,0,'L',$fill);	// nombre
            } else {
                $this->Cell(($wide)?37:32,6,$nombre,0,0,'L',$fill);	// nombre
                $this->Cell(($wide)?15:10,6,$row['Licencia'],0,0,'L',$fill);	// licencia
            }
        } else {
            $this->Cell(($wide)?22:27,6,$row['Nombre'],0,0,'L',$fill);	// nombre
            $this->SetFont($this->getFontName(),'',($wide)?6:8); // default font
            $this->Cell(($wide)?30:15,6,$row['Licencia'],0,0,'C',$fill);	// licencia
        }
        $this->SetFont($this->getFontName(),'',7); // a bit little font to allow califications
		if ($this->hasGrades) {
            $cat=$this->federation->getCategoryShort($row['Categoria']);
            $grad=$this->federation->getGradeShort($row['Grado']);
			$this->Cell(10,6,"{$cat} {$grad}",0,0,'C',$fill);	// categoria/grado
		} else {
            $cat=$this->federation->getCategory($row['Categoria']);
			$this->Cell(10,6,"{$cat}",0,0,'C',$fill);	// categoria/grado
		}
		$this->Cell(($wide)?30:35,6,$row['NombreGuia'],0,0,'R',$fill);	// nombreGuia
		$this->Cell(($wide)?15:20,6,$row['NombreClub'],0,0,'R',$fill);	// nombreClub
		// manga 1
        if ($this->manga1!==null) {
            $pos= (intval($row['N1'])===0)?"{$row['Puesto1']}º":"-";
            $this->Cell(6*$factor,6,$row['F1'],0,0,'C',$fill);	// 1- Faltas+Tocados
            $this->Cell(6*$factor,6,$row['R1'],0,0,'C',$fill);	// 1- Rehuses
            $this->Cell(10*$factor,6,$t1,0,0,'C',$fill);	// 1- Tiempo
            $this->Cell(8*$factor,6,$v1,0,0,'C',$fill);	// 1- Velocidad
            $this->Cell(10*$factor,6,$p1,0,0,'C',$fill);	// 1- Penalizacion
            if ($row['P1']==0) $this->SetFont($this->getFontName(),'B',7); // put zero in bold
            $this->Cell(10*$factor,6,$row['C1'],0,0,'C',$fill);	// 1- calificacion
            $this->SetFont($this->getFontName(),'B',7);
            $this->Cell(7*$factor,6,$pos,'',0,'C',$fill);	// 1- puesto
            $this->SetFont($this->getFontName(),'',7);
        } else {
            $this->Cell(57*$factor,6,'',0,0,'C',$fill);	// espacio en blanco
        }
		// manga 2
		if ($this->manga2!==null) {
            $pos= (intval($row['N1'])===0)?"{$row['Puesto2']}º":"-";
			$this->Cell(6*$factor,6,$row['F2'],0,0,'C',$fill);	// 2- Faltas+Tocados
			$this->Cell(6*$factor,6,$row['R2'],0,0,'C',$fill);	// 2- Rehuses
			$this->Cell(10*$factor,6,$t2,0,0,'C',$fill);	// 2- Tiempo
			$this->Cell(8*$factor,6,$v2,0,0,'C',$fill);	// 2- Velocidad
			$this->Cell(10*$factor,6,$p2,0,0,'C',$fill);	// 2- Penalizacion
            if ($row['P2']==0) $this->SetFont($this->getFontName(),'B',7);
			$this->Cell(10*$factor,6,$row['C2'],0,0,'C',$fill);	// 2- calificacion
            $this->SetFont($this->getFontName(),'B',7);
            $this->Cell(7*$factor,6,$pos,'',0,'C',$fill);	// 2- puesto
            $this->SetFont($this->getFontName(),'',7);
		} else {
			$this->Cell(57*$factor,6,'',0,0,'C',$fill);	// espacio en blanco
		}
		// manga 3
        if ($this->manga3!==null) {
            $pos= (intval($row['N1'])===0)?"{$row['Puesto3']}º":"-";
            $this->Cell(6*$factor,6,$row['F3'],0,0,'C',$fill);	// 3- Faltas+Tocados
            $this->Cell(6*$factor,6,$row['R3'],0,0,'C',$fill);	// 3- Rehuses
            $this->Cell(10*$factor,6,$t3,0,0,'C',$fill);	// 3- Tiempo
            $this->Cell(8*$factor,6,$v3,0,0,'C',$fill);	// 3- Velocidad
            $this->Cell(10*$factor,6,$p3,0,0,'C',$fill);	// 3- Penalizacion
            if ($row['P3']==0) $this->SetFont($this->getFontName(),'B',7);
            $this->Cell(10*$factor,6,$row['C3'],0,0,'C',$fill);	// 3- calificacion
            $this->SetFont($this->getFontName(),'B',7);
            $this->Cell(7*$factor,6,$pos,'',0,'C',$fill);	// 3- puesto
            $this->SetFont($this->getFontName(),'',7);
        } // no else: no print
		// global
		$this->Cell(12*$factor,6,$tiempo,0,0,'C',$fill);	// Tiempo
		$this->Cell(12*$factor,6,$penal,0,0,'C',$fill);	// Penalizacion
        $this->SetFont($this->getFontName(),'B',7); // Put final calification in bold
		$this->Cell(14*$factor,6,$row['Calificacion'],0,0,'C',$fill);	// Calificacion
		$this->SetFont($this->getFontName(),'B',10); // default font
		$this->Cell(8*$factor,6,$puesto,0,0,'C',$fill);	// Puesto
		// lineas rojas
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->Line(10,$offset + 6*$idx,10,$offset + 6*($idx+1));
		$this->Line(10+115,$offset + 6*$idx,10+115,$offset + 6*($idx+1));
		$this->Line(10+115+57*$factor,$offset + 6*$idx,10+115+57*$factor,$offset + 6*($idx+1));
		$this->Line(10+115+57*2*$factor,$offset + 6*$idx,10+115+57*2*$factor,$offset + 6*($idx+1));
		if ($this->manga3!==null) {
            $this->Line(10+115+57*3*$factor,$offset + 6*$idx,10+115+57*3*$factor,$offset + 6*($idx+1));
            $this->Line(10+115+(57*3+46)*$factor,$offset + 6*$idx,10+115+(57*3+46)*$factor,$offset + 6*($idx+1));
        } else {
            $this->Line(10+115+(57*2+46)*$factor,$offset + 6*$idx,10+115+(57*2+46)*$factor,$offset + 6*($idx+1));
        }
		
		$this->Ln();
	}
	
	function composeTable() {
		$this->myLogger->enter();
        $len=(($this->manga3)!==null)?115+(57*3+46)*0.75:115+57*2+46; // lenght of closing line

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
				$this->cell($len,0,'','T'); // celda sin altura y con raya
				$this->AddPage();
				$rowcount=0;
			}
		}
		// pintamos linea de cierre final
		$this->setX(10);
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
		$this->cell($len,0,'','T'); // celda sin altura y con raya
		$this->myLogger->leave();
	}
}

?>