<?php
/*
print_ordenTandas.php

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
 * genera un pdf con la secuencia ordenada de tandas de la jornada y los participantes de cada tanda
*/

require_once(__DIR__."/../fpdf.php");
require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/../../logging.php");
require_once(__DIR__.'/../../database/classes/DBObject.php');
require_once(__DIR__.'/../../database/classes/Pruebas.php');
require_once(__DIR__.'/../../database/classes/Jornadas.php');
require_once(__DIR__.'/../../database/classes/Mangas.php');
require_once(__DIR__.'/../../database/classes/Tandas.php');
require_once(__DIR__."/../print_common.php");

class PrintTRSTemplates extends PrintCommon {

	protected $mode; // orden de tandas
	
	/**
	 * Constructor
	 * @param {integer} $prueba Prueba ID
	 * @param {integer} $jornada Jornada ID
     * @param {integer} $m Print mode. 0:Trs/Trm evaluation calc sheet 1:Trsdata template to enter data
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$m) {
		date_default_timezone_set('Europe/Madrid');
		parent::__construct(($m==0)?'Landscape':'Portrait',"print_Templates",$prueba,$jornada);
		if ( ($prueba<=0) || ($jornada<=0) ) {
			$this->errormsg="printTemplates: either prueba or jornada data are invalid";
			throw new Exception($this->errormsg);
		}
		$this->mode=intval($m);
		if ( in_array($this->mode,array(0,2) ) ) {
            $this->icon2=getIconPath($this->federation->get('Name'),"null.png");
        }
	}
	
	// Cabecera de página
	function Header() {
        switch ($this->mode){
            case 0: // tabla distancia-velocidad para calculo del trs
                $this->ac_header(1,12);
                $this->SetXY(10,10);
                $this->Cell(100,7,_('SheetCalc to evaluate SCT and MCT'),'LTBR',0,'C',true); // cabecera muy simple :-)
                break;
            case 1: // hoja para apuntar datos de trs/trm
                $this->print_commonHeader(_("SCT / MCT data form"));
                $this->ac_header(1,12);
                // pintamos identificacion de la jornada
                $str  = _("Journey").": {$this->jornada->Nombre} - {$this->jornada->Fecha}";
                $this->Cell(100,7,$str,0,0,'L',false); // a un lado nombre y fecha de la jornada
                $this->Ln(5);
                $str  = _("Start time").": {$this->jornada->Hora}";
                $this->Cell(90,7,$str,0,0,'L',false); // a un lado nombre y fecha de la jornada
                break;
            case 2: // formulario vacio de entrada de datos
                $this->print_commonHeader(_("Data entry"));
                $this->ac_header(1,12);
                // pintamos "identificacion de la manga"
                $this->Cell(100,9,_("Journey").":                   "._("Date").":",0,0,'L',false); // a un lado nombre y fecha de la jornada
                $this->Cell(90,9,_("Category").":                   "._("Grade").":",0,0,'L',false); // al otro lado tipo y categoria de la manga
                $this->Ln(9);
                break;
        }
	}

    function Footer() {
        $this->print_commonFooter();
    }

	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
        $this->AddPage();
        // header
        $count=0;
        $this->SetXY(10,20);
        $this->cell(7,5,"",0,0,'C',false);
        for ($n=141;$n<=240;$n+=3) {
            $this->ac_header(2,10);
            $this->cell(8,5,strval($n),'RB',0,'C',true);
        }
        $this->Ln();
        // rows
        for($vel=26;$vel<60;$vel++) {
            $this->ac_header(2,10);
            $this->cell(7,5,strval($vel/10.0),'RB',0,'C',true);
            for ($n=131;$n<=230;$n+=3) {
                // trace reference lines if needed
                $this->ac_row($count,9);
                $this->cell(8,5,strval(ceil((10*$n)/$vel)),'RB',0,'C',true);
            }
            if ($vel%5==0) {
                $this->SetLineWidth(0.6);
                $y=$this->GetY();
                $this->Rect(10,$y+0.2,279,4.4);
                $this->SetLineWidth(0.2);
            }
            $this->Ln();
            $count++;
        }
		$this->myLogger->leave();
        return "";
	}

    function printFormulario(){
        $cols=array(_("Category"),_("Dist").".",_("Obst").".",_("Spd").".",_("SCT"),_("MCT"));
        $size=array(20,15,12.5,12.5,15,15);
        $mng=new Mangas("printFormularioTRS",$this->jornada);
        // obtenemos la lista de mangas de la jornada
        $mangas=$mng->selectByJornada()['rows'];
        for($count=0;$count<count($mangas);$count++) {
            $heights=Competitions::getHeights($this->prueba->ID,$this->jornada->ID,$mangas[$count]['ID']);
            if ($count%8==0){
                $this->AddPage();
                $this->Ln(10);
            }
            $manga1=$mangas[$count];
            $manga2=null;
            if (array_key_exists($count+1,$mangas)) $manga2=$mangas[$count+1];
            // cabecera
            $this->ac_header(2,12);
            $this->Cell(90,8,$manga1['Descripcion'],'LTBR',0,'L',true);
            if ($manga2!==null) {
                $this->Cell(10,8,"",0,0,0,false);
                $this->Cell(90,8,$manga2['Descripcion'],'LTBR',0,'L',true);
            }
            $this->Ln();
            // columnas
            $this->ac_row(1,10);
            $this->Cell(20,8,$cols[0],'LRB',0,'C',true);
            for ($n=1;$n<count($size);$n++) $this->Cell($size[$n],8,$cols[$n],"RB",0,"C",true);
            if ($manga2!==null) {
                $this->Cell(10,8,"",0,0,0,false);
                $this->Cell(20,8,$cols[0],'LRB',0,'C',true);
                for ($n=1;$n<count($size);$n++) $this->Cell($size[$n],8,$cols[$n],"RB",0,"C",true);
            }
            $this->Ln();
            // datos
            $this->ac_row(0,10);
            if($heights==5) {
                $this->Cell(20,8,$this->federation->getCategory('X'),'LRB',0,'C',true);
                for ($n=1;$n<count($size);$n++) $this->Cell($size[$n],8,"","RB",0,"C",true);
                if ($manga2!==null) {
                    $this->Cell(10,8,"",0,0,0,false);
                    $this->Cell(20,8,$this->federation->getCategory('X'),'LRB',0,'C',true);
                    for ($n=1;$n<count($size);$n++) $this->Cell($size[$n],8,"","RB",0,"C",true);
                }
                $this->Ln();
            }
            $this->Cell(20,8,$this->federation->getCategory('L'),'LRB',0,'C',true);
            for ($n=1;$n<count($size);$n++) $this->Cell($size[$n],8,"","RB",0,"C",true);
            if ($manga2!==null) {
                $this->Cell(10,8,"",0,0,0,false);
                $this->Cell(20,8,$this->federation->getCategory('L'),'LRB',0,'C',true);
                for ($n=1;$n<count($size);$n++) $this->Cell($size[$n],8,"","RB",0,"C",true);
            }
            $this->Ln();
            $this->Cell(20,8,$this->federation->getCategory('M'),'LRB',0,'C',true);
            for ($n=1;$n<count($size);$n++) $this->Cell($size[$n],8,"","RB",0,"C",true);
            if ($manga2!==null) {
                $this->Cell(10,8,"",0,0,0,false);
                $this->Cell(20,8,$this->federation->getCategory('M'),'LRB',0,'C',true);
                for ($n=1;$n<count($size);$n++) $this->Cell($size[$n],8,"","RB",0,"C",true);
            }
            $this->Ln();
            $this->Cell(20,8,$this->federation->getCategory('S'),'LRB',0,'C',true);
            for ($n=1;$n<count($size);$n++) $this->Cell($size[$n],8,"","RB",0,"C",true);
            if ($manga2!==null) {
                $this->Cell(10,8,"",0,0,0,false);
                $this->Cell(20,8,$this->federation->getCategory('S'),'LRB',0,'C',true);
                for ($n=1;$n<count($size);$n++) $this->Cell($size[$n],8,"","RB",0,"C",true);
            }
            $this->Ln();
            if($heights!=3) {
                $this->Cell(20,8,$this->federation->getCategory('T'),'LRB',0,'C',true);
                for ($n=1;$n<count($size);$n++) $this->Cell($size[$n],8,"","RB",0,"C",true);
                if ($manga2!==null) {
                    $this->Cell(10,8,"",0,0,0,false);
                    $this->Cell(20,8,$this->federation->getCategory('T'),'LRB',0,'C',true);
                    for ($n=1;$n<count($size);$n++) $this->Cell($size[$n],8,"","RB",0,"C",true);
                }
                $this->Ln();
            }
            $this->Ln(2);
            $count++;
        }
    }

    /*
     * Formulario de entrada de datos, sin datos de participantes
     */
    function printDataForm() {
        $this->myLogger->enter();

        // tocamos la cabecera, eliminando datos de prueba y club
        $this->prueba->Nombre="";
        $this->club->Nombre="";
        $this->icon=getIconPath($this->federation->get('Name'),"agilitycontest.png");

        // ahora empezamos a pintar
        $this->AddPage();
        $this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
        $this->SetLineWidth(.3);

        // indicamos nombre del operador que rellena la hoja
        $this->ac_header(2,12);
        $this->Cell(90,7,_('Record by').':','LTBR',0,'L',true);
        $this->Cell(10,7,'','',0,'L',false);
        $this->Cell(90,7,_('Review by').':','LTBR',0,'L',true);
        $this->Ln(15);
        $wide=$this->federation->hasWideLicense(); // use long cell for license when required
        for ($rowcount=0; $rowcount<10;$rowcount++) {

            $this->ac_header(1,20);
            // save cursor position
            $x=$this->getX();
            $y=$this->GetY();

            // fase 1: contenido de cada celda de la cabecera
            // Cell( width,height,message,border,cursor,align,fill)
            // pintamos logo
            $this->Cell(15,19,'','LTBR',0,'L',false);
            $this->SetXY($x+1,$y+2); // restore cursor position
            $this->Image($this->icon,$this->getX()+0.5,$this->getY(),12);
            $this->SetX($this->GetX()+12);

            // bordes cabecera de celda
            $this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg1')); // color de fondo 2
            $this->SetXY($x+15,$y); // restore cursor position
            $this->SetFont($this->getFontName(),'B',10); // bold 10px
            $this->Cell(15,6,'',	'LTR',0,'L',true); // dorsal
            $this->Cell(10,6,'',	'TR',0,'L',true); // celo
            if ($wide) {
                $this->Cell(50,6,'',	'TR',0,'L',true); // perro
            } else {
                $this->Cell(20, 6, '', 'TR', 0, 'L', true); // licencia
                $this->Cell(30,6,'',	'TR',0,'L',true); // perro
            }
            $this->Cell(60,6,'',	'TR',0,'L',true); // guia
            $this->Cell(40,6,'',	'TR',0,'L',true); // club

            // titulos cabecera de celda ( no hay datos para pintar )
            $this->SetXY($x+15,$y); // restore cursor position
            $this->SetTextColor(0,0,0); // negro
            $this->SetFont($this->getFontName(),'I',8); // italic 8px
            $this->Cell(15,4,_('Dorsal'),	'',0,'L',false); // display order
            $this->Cell(10,4,_('Heat'),	'',0,'L',false);
            if ($wide) {
                $this->Cell(50,4,_('Name'),	'',0,'L',false);
            } else {
                $this->Cell(20,4,_('Lic'),'',0,'L',false);
                $this->Cell(30,4,_('Name'),	'',0,'L',false);
            }
            $this->Cell(60,4,_('Handler'),	'',0,'L',false);
            $this->Cell(40,4,$this->strClub,	'',0,'L',false);

            // ahora pintamos zona de escritura de palotes
            $this->SetXY($x+15,$y+6);
            $this->Cell(60,13,'','TRB',0,'',false); // palotes faltas
            $this->Cell(40,13,'','TRB',0,'',false); // palotes rehuses
            $this->Cell(25,13,'','TRB',0,'',false); // palotes tocados
            $this->Cell(7, 13,'','TRB',0,'',false); // total faltas
            $this->Cell(7, 13,'','TRB',0,'',false); // total rehuses
            $this->Cell(7, 13,'','TRB',0,'',false); // total tocados
            $this->Cell(29,13,'','TRB',0,'',false); // tiempo
            $this->SetXY($x+15,$y+6);
            $this->Cell(60,5,_('Faults'),	'',0,'L',false);
            $this->Cell(40,5,_('Refusals'),	'',0,'L',false);
            $this->Cell(25,5,_('Touchs'),	'',0,'L',false);
            $this->Cell(7, 5,_('Flt'),	'',0,'C',false);
            $this->Cell(7, 5,_('Ref'),	'',0,'C',false);
            $this->Cell(7, 5,_('Tch'),	'',0,'C',false);
            $this->Cell(29,5,_('Time'),  '',0,'L',false);
            $this->Ln(15);
        }
        // Línea de cierre
        // $this->Cell(array_sum($this->pos),0,'','T');
        $this->myLogger->leave();
    }
}
?>