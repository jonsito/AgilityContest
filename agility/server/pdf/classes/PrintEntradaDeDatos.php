<?php
/*
print_entradaDeDatos.php

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
 * genera un pdf con las hojas del asistente de pista
*/

require_once(__DIR__."/../fpdf.php");
require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/../../logging.php");
require_once(__DIR__.'/../../database/classes/DBObject.php');
require_once(__DIR__.'/../../database/classes/Pruebas.php');
require_once(__DIR__.'/../../database/classes/Jornadas.php');
require_once(__DIR__.'/../../database/classes/Mangas.php');
require_once(__DIR__.'/../../database/classes/OrdenSalida.php');
require_once(__DIR__."/../print_common.php");

class PrintEntradaDeDatos extends PrintCommon {

	protected $manga=null; // datos de la manga
	protected $manga2=null; // datos de la manga 2
    protected $orden=null; // orden de salida de la manga
	protected $numrows; // formato del pdf 0:1 1:5 2:15 perros/pagina
	protected $categoria; // categoria que estamos listando
	protected $validcats; // lista de categorias solicitadas
	protected $fillData=false; // populate sheets with result data
	protected $rango; // data item to be printed

	// geometria de las celdas
	protected $cellHeader
					=array('Dorsal','Nombre','Lic.','Guía','Club','Celo', 'Observaciones');
	protected $pos	=array(  15,       25,     18,    50,   42,     10,    30);
	protected $align=array(  'C',      'R',    'C',   'L',  'R',    'C',   'R');
	protected $fmt	=array(  'i',      's',    's',   's',  's',    'b',   's');
	
	/**
	 * Constructor
     * @param {array} data (prueba,jornada, mangas,ordensalida,numperros,categorias, filldata,rango,comentarios)
	 * param {integer} $prueba
	 * param {integer} $jornada
	 * param {array[object]} mangas datos de la manga y (si existe) manga hermana
	 * param {array} $ordens Lista de inscritos en formato jquery array[count,rows[]]
	 * param {integer} $numrows numero de perros a imprimir por cada hoja 1/5/15
     * param {string} rango [\d]-[\d]
     * param {string} comentarios
	 * @throws Exception on invalid data
	 */
    function __construct($data) {
		parent::__construct('Portrait',"print_entradaDeDatos",$data['prueba'],$data['jornada'],$data['comentarios']);
		if ( ($data['prueba']<=0) || ($data['jornada']<=0) || ($data['mangas']===null) || ($data['orden']===null) ) {
			$this->errormsg="printEntradaDeDatos: either prueba/jornada/ manga/orden data are invalid";
			throw new Exception($this->errormsg);
		}
		$this->manga=$data['mangas'][0];
		if(array_key_exists(1,$data['mangas'])) $this->manga2=$data['mangas'][1];
		$this->orden=$data['orden'];
		$this->numrows=$data['numrows'];
		$this->categoria="L";
		$this->cellHeader[4]=$this->strClub; // fix country/club text
		$this->validcats=$data['cats'];
		$this->fillData=($data['fill']!=0)?true:false;
        $this->rango= (preg_match('/^\d+-\d+$/',$data['rango']))? $data['rango'] : "1-99999";
        // set file name
        $grad=$this->federation->getTipoManga($this->manga->Tipo,3); // nombre de la manga
        $cat=$this->validcats; // categorias del listado
        $str=($cat=='-')?$grad:"{$grad}_{$cat}";
        $res=normalize_filename($str);
        $this->set_FileName("HojasAsistente_{$res}.pdf");
	}

	// Cabecera de página
	function Header() {
		$this->print_commonHeader(_("Data entry"));
		if($this->numrows!=1) { 
			// normal/compacto: pinta id de la jornada y de la manga
			$this->print_identificacionManga($this->manga,$this->getCatString($this->categoria));
		} else {
			// modo extendido: pinta solo identificacion de la jornada
			$this->SetFont($this->getFontName(),'B',12); // bold 15
			$str  = $this->jornada->Nombre . " - " . $this->jornada->Fecha;
			$this->Cell(90,9,$str,0,0,'L',false); // a un lado nombre y fecha de la jornada
			$this->Ln(9);
		}
		
	}

	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}

	private function palotes($count) {
	    $str="";
	    for (;$count>0;$count--) $str.="| ";
	    return $str;
    }

    /**
	 * Prints 15 dogs / page
     * @param {number} $rowcount
     * @param {array} $row
	 * @param {integer} $orden . Starting order in their category
     */
	function writeTableCell_compacto($row,$orden) {
		$wide=$this->federation->get('WideLicense'); // if required use long cell for license
		$logo=$this->getLogoName($row['Perro']);
		$this->ac_header(1,20);
		// save cursor position
		$x=$this->getX();
		$y=$this->GetY();

		// fase 1: contenido de cada celda de la cabecera
		// Cell( width,height,message,border,cursor,align,fill)
		// pintamos logo
		$this->Cell(15,13,'','LTBR',0,'L',false);
		$this->SetXY($x+1,$y+1); // restore cursor position
		$this->Image($logo,$this->getX()+0.5,$this->getY(),12);
		// pintamos numero de orden
		$this->ac_header(2,12);
		$this->SetXY($x+16,$y+7);
		$this->Cell(14,5,$orden,'',0,'L',true);
		
		// bordes cabecera de celda
		$this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg1')); // color de fondo 2
		$this->SetXY($x+15,$y); // restore cursor position
		$this->SetFont($this->getFontName(),'B',10); // bold 10px
		$this->Cell(15,5,'',	'LTR',0,'L',true); // dorsal
		$this->Cell(10,5,'',	'TR',0,'L',true); // celo
        if ($wide) {
            $this->Cell(50,5,'',	'TR',0,'L',true); // perro
        } else {
            $this->Cell(20, 5, '', 'TR', 0, 'L', true); // licencia
            $this->Cell(30,5,'',	'TR',0,'L',true); // perro
        }
		$this->Cell(60,5,'',	'TR',0,'L',true); // guia
		$this->Cell(40,5,'',	'TR',0,'L',true); // club
		// datos cabecera de celda
		$this->SetXY($x+15,$y+1); // restore cursor position
		$this->Cell(15,4,$row['Dorsal'],		'',0,'R',false); // display order
		$this->Cell(10,4,($row['Celo']!=0)?"Celo":"",'',0,'R',false);
        if ($wide) {
            $this->Cell(50,4,$row['Nombre'],		'',0,'R',false);
        } else {
            $this->Cell(20,4,$row['Licencia'],		'',0,'R',false);
            $this->Cell(30,4,$row['Nombre'],		'',0,'R',false);
        }
		$this->Cell(60,4,$row['NombreGuia'],	'',0,'R',false);
		$this->Cell(40,4,$row['NombreClub'],	'',0,'R',false);

		// titulos cabecera de celda
		$this->SetXY($x+15,$y); // restore cursor position
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont($this->getFontName(),'I',7); // italic 8px
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
		$this->SetXY($x+15,$y+5);
		$this->Cell(60,8,'','TRB',0,'',false); // palotes faltas
		$this->Cell(40,8,'','TRB',0,'',false); // palotes rehuses
		$this->Cell(25,8,'','TRB',0,'',false); // palotes tocados
		$this->Cell(7, 8,'','TRB',0,'',false); // total faltas
		$this->Cell(7, 8,'','TRB',0,'',false); // total rehuses
		$this->Cell(7, 8,'','TRB',0,'',false); // total tocados
		$this->Cell(29,8,'','TRB',0,'',false); // tiempo
		$this->SetXY($x+30,$y+5);
		$this->Cell(45,5,_('Faults'),	'',0,'L',false);
		$this->Cell(40,5,_('Refusals'),	'',0,'L',false);
		$this->Cell(25,5,_('Touchs'),	'',0,'L',false);
		$this->Cell(7, 5,_('Flt'),	'',0,'C',false);
		$this->Cell(7, 5,_('Ref'),	'',0,'C',false);
		$this->Cell(7, 5,_('Tch'),	'',0,'C',false);
		$this->Cell(29,5,_('Time'),  '',0,'L',false);
		if (! $this->fillData) { $this->Ln(9); return; }
		// arriving here means populate results
        $this->SetFont($this->getFontName(),'B',9); //
        $this->SetXY($x+40,$y+8);
        $this->Cell(45,5,$this->palotes($row['Faltas']),	'',0,'L',false);
        $this->Cell(40,5,$this->palotes($row['Tocados']),	'',0,'L',false);
        $this->Cell(15,5,$this->palotes($row['Rehuses']),	'',0,'L',false);
        $this->Cell(7, 5,$row['Faltas'],	'',0,'C',false);
        $this->Cell(7, 5,$row['Tocados'],	'',0,'C',false);
        $this->Cell(7, 5,$row['Rehuses'],	'',0,'C',false);
        $this->Cell(9,5,$row['Tiempo'],  '',0,'L',false);
        if($row['Pendiente']!=0)  $this->Cell(20,5,_('Pending'),  '',0,'L',false);
        else if($row['NoPresentado']!=0)  $this->Cell(19,5,_('Not Present'),  '',0,'L',false);
        else if($row['Eliminado']!=0)  $this->Cell(19,5,_('Eliminated'),  '',0,'L',false);
        $this->Ln(6);
	}
	
	/**
	 * Prints 5 dogs / page
	 * @param {number} $rowcount Row index
	 * @param {number} $row Row data
	 * @param {number} $f width factor (to be reused on extended print)
	 * @param {integer} $orden . Starting order in their category
	 */
	function writeTableCell_normal($row,$orden) {
        // remember that this method is called iteratively ... so make sure first time license goes to zero
        if ($this->federation->get('WideLicense')) {
            $this->pos[1]+=$this->pos[2]; $this->pos[2]=0; // on wide license ID skip license info
        }
		// cada celda tiene una cabecera con los datos del participante
		$this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg1')); // azul
		$this->ac_SetTextColor($this->config->getEnv('pdf_hdrfg1')); // blanco
		$this->ac_SetDrawColor("0x000000"); // line color
		// save cursor position 
		$x=$this->getX();
		$y=$this->GetY();
		// fase 1: contenido de cada celda de la cabecera
		$this->SetFont($this->getFontName(),'B',20); // bold 9px
		$this->Cell($this->pos[0],10,$row['Dorsal'],		'LTR',0,$this->align[0],true); // dorsal
		// pintamos cajas con fondo
		$this->Cell($this->pos[1],10,'',		'LTR',0,$this->align[1],true); // nombre
		if ($this->pos[2]!=0) $this->Cell($this->pos[2],10,'',		'LTR',0,$this->align[2],true); // licencia
		$this->Cell($this->pos[3],10,'',	'LTR',0,$this->align[3],true); // guia
		$this->Cell($this->pos[4],10,'',	'LTR',0,$this->align[4],true);
		$this->Cell($this->pos[5],10,'','LTR',0,$this->align[5],true);
		$this->Cell($this->pos[6],10,'',	'LTR',0,$this->align[6],true);
		
		// pintamos textos un poco desplazados hacia abajo y sin borde ni fondo
		$this->SetXY($x+$this->pos[0],$y+3); // restore cursor position
		$this->SetFont($this->getFontName(),'B',12); // bold 9px
		$this->Cell($this->pos[1],10-3,$row['Nombre'],		'',0,$this->align[1],false);
        if ($this->pos[2]!=0) $this->Cell($this->pos[2],10-3,$row['Licencia'],		'',0,$this->align[2],false);
		$this->Cell($this->pos[3],10-3,$row['NombreGuia'],	'',0,$this->align[3],false);
		$this->Cell($this->pos[4],10-3,$row['NombreClub'],	'',0,$this->align[4],false);
		$this->Cell($this->pos[5],10-3,($row['Celo']!=0)?"Celo":"",'',0,$this->align[5],false);
		$this->Cell($this->pos[6],10-3,$row['Observaciones'],	'',0,$this->align[6],false);
		
		// nombre nombre de cada celda de la cabecera
		$this->SetXY($x,$y); // restore cursor position
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont($this->getFontName(),'I',8); // italic 8px
		$this->Cell($this->pos[0],5,'',			'',	0,'L',false); // Dorsal
		$this->Cell($this->pos[1],5,_('Name').':',	'',	0,'L',false);
        if ($this->pos[2]!=0) $this->Cell($this->pos[2],5,_('License').':','',	0,'L',false);
		$this->Cell($this->pos[3],5,_('Handler').':',	'',	0,'L',false);
		$this->Cell($this->pos[4],5,$this->strClub.':',	'',	0,'L',false);
		$this->Cell($this->pos[5],5,_('Heat').':',	'',	0,'L',false);
		$this->Cell($this->pos[6],5,_('Comments').':','',0,'L',false);
		$this->Cell(0,10); // increase height before newline
		
		// Restauración de colores y fuentes
		$this->ac_SetFillColor($this->config->getEnv('pdf_rowcolor2')); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->Ln();
		// datos de Faltas, Tocados y Rehuses
		$this->Cell(20,10,_("Faults"),1,0,'L',false);
		for ($i=1;$i<=10;$i++) $this->Cell(10,10,$i,1,0,'C',(($i&0x01)==0)?false:true);
		$this->Cell(10); $this->Cell(20,10,"F: ",1,0,'L',false);
		$this->Cell(40,10,_("Time"),'LTR',0,'C',true);
		$this->Ln();
		$this->Cell(20,10,_("Touchs"),1,0,'L',false);
		for ($i=1;$i<=10;$i++) $this->Cell(10,10,$i,1,0,'C',(($i&0x01)==0)?false:true);
		$this->Cell(10); $this->Cell(20,10,"T: ",1,0,'L',false);
		$this->Cell(40,10,"",'LR',0,'C',true);
		$this->Ln();
		$this->Cell(20,10,_("Refusals"),1,0,'L',false);
		for ($i=1;$i<=3;$i++) $this->Cell(10,10,$i,1,0,'C',(($i&0x01)==0)?false:true);
		$this->Cell(10); $this->Cell(30,10,_("Elim"),1,0,'L',false);
		$this->Cell(30,10,_("N.P."),1,0,'L',false);
		$this->Cell(10); $this->Cell(20,10,"R: ",1,0,'L',false);
		$this->Cell(40,10,"",'LBR',0,'C',true);

        if (! $this->fillData) { $this->Ln(14); return; }
        // arriving here means populate results
		$this->Ln(14);
	}

    /**
	 * Prints 1 dog / page
     * @param {number} $rowcount
     * @param {array} $row
	 * @param {integer} $orden . Startin order in their category
     */
	function writeTableCell_extendido($row,$orden) {
		$logo=$this->getLogoName($row['Perro']);
		// cada celda tiene una cabecera con los datos del participante
		$this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg1')); // azul
		$this->ac_SetTextColor($this->config->getEnv('pdf_hdrfg1')); // blanco
		$this->ac_SetDrawColor("0x000000"); // line color
		// save cursor position
		$x=$this->getX();
		$y=$this->GetY();
		
		// pintamos celda de dorsal y logo del club
		$this->SetFont($this->getFontName(),'B',22); // bold 9px
		$this->Cell($this->pos[0],15,$row['Dorsal'],		'LTRB',0,$this->align[0],true); // display order
		$this->SetXY($x,$y+15); // logo border
		$this->Cell($this->pos[0],15,'','LB',0,false);
		$this->SetXY($x+1.5,$y+16); // logo position
		$this->Image($logo,$this->getX(),$this->getY(),12);

		// fase 1: contenido de cada celda de la cabecera
		$this->SetXY($x+$this->pos[0],$y); // next cell position
		// pintamos cajas con fondo
		$this->Cell($this->pos[1],30,'',	'LTRB',0,$this->align[1],true);
        $this->Cell($this->pos[2]+$this->pos[3],30,'',	'TRB',0,$this->align[3],true); // unify license and guia
		$this->Cell($this->pos[4],30,'',	'TRB',0,$this->align[4],true);
		$this->Cell($this->pos[5],30,'',	'TRB',0,$this->align[5],true);
		$this->Cell($this->pos[6],30,'',	'TRB',0,$this->align[6],true);
		
		// pintamos textos un poco desplazados hacia abajo y sin borde ni fondo
		$this->SetXY($x+$this->pos[0],$y+3); // restore cursor position
		$this->SetFont($this->getFontName(),'B',12); // bold 9px
        $this->Cell($this->pos[1],30-3,$row['Nombre'],		'',0,$this->align[1],false);
        $this->SetXY($x+$this->pos[0]+$this->pos[1],$y+3); // restore cursor position
        $this->Cell($this->pos[2]+$this->pos[3],30-3,$row['NombreGuia'],	'',0,$this->align[3],false);
        $this->SetXY($x+$this->pos[0]+$this->pos[1],$y+15); // restore cursor position
        $this->Cell($this->pos[2]+$this->pos[3],30-15,$row['Licencia'],	'',0,$this->align[3],false);
        $this->SetXY($x+$this->pos[0]+$this->pos[1]+$this->pos[2]+$this->pos[3],$y+3); // restore cursor position
 		$this->Cell($this->pos[4],30-3,$row['NombreClub'],	'',0,$this->align[4],false);
		$this->Cell($this->pos[5],30-3,($row['Celo']!=0)?"Celo":"",'',0,$this->align[5],false);
		$this->Cell($this->pos[6],30-3,$row['Observaciones'],	'',0,$this->align[6],false);
		
		// nombre nombre de cada celda de la cabecera
		$this->SetXY($x,$y); // restore cursor position
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont($this->getFontName(),'I',8); // italic 8px
		$this->Cell($this->pos[0],5,_('Dorsal'),	'',	0,'L',false); // Dorsal
		$this->Cell($this->pos[1],5,_('Name'),	'',	0,'L',false);
        $this->Cell($this->pos[2]+$this->pos[3],5,_('Handler').' - '._('License'),	'',	0,'L',false); // unify license and guia
		$this->Cell($this->pos[4],5,$this->strClub,	'',	0,'L',false);
		$this->Cell($this->pos[5],5,('Heat'),	'',	0,'L',false);
		$this->Cell($this->pos[6],5,_('Comments'),'',0,'L',false);
		$this->Cell(0,30); // increase height before newline
		$this->Ln(30);		


		// Datos de manga
		$this->ac_header(1,15);
		$strcat=$this->getCatString($this->categoria);
		if($this->manga!=null) {
			$str=_(Mangas::getTipoManga($this->manga->Tipo,1,$this->federation));
			$str="$str - $strcat";
			$this->ac_Cell(10,85,90,10,$str,"LTBR","C",false);
		}
		if($this->manga2!=null) {
			$str=_(Mangas::getTipoManga($this->manga2->Tipo,1,$this->federation));
			$str="$str - $strcat";
			$this->ac_Cell(110,85,90,10,$str,"LTBR","C",false);
		}
		
		// Restauración de colores y fuentes

		$this->ac_header(2,10);
		$this->ac_SetFillColor($this->config->getEnv('pdf_rowcolor2')); // azul merle
		$this->SetTextColor(0,0,0); // negro
		
		// datos manga 1
		if ($this->manga !=null) {
			$this->ac_Cell(10,100,72,10,_("Faults").":","LTBR","L",false);
			$this->ac_Cell(82,100,18,20,"F:","LTBR","L",true);
			for ($n=0;$n<9;$n++)$this->ac_Cell(10+(8*$n),110,8,10,$n+1,"LB","C",(($n%2)!=0)?true:false);

			$this->ac_Cell(10,125,72,10,_("Touchs").":","LTBR","L",false);
			$this->ac_Cell(82,125,18,20,"T:","LTBR","L",true);
			for ($n=0;$n<9;$n++)$this->ac_Cell(10+(8*$n),135,8,10,$n+1,"LB","C",(($n%2)!=0)?true:false);
			
			$this->ac_Cell(10,150,72,10,_("Refusals").":","LTBR","L",false);
			$this->ac_Cell(82,150,18,20,"R:","LTBR","L",true);
			for ($n=0;$n<3;$n++)$this->ac_Cell(10+(8*$n),160,8,10,$n+1,"LBR","C",(($n%2)!=0)?true:false);

			$this->ac_Cell(10,175,28,20,"","LTB","L",false);
			$this->ac_Cell(38,175,28,20,"","LTBR","L",false);
			$this->ac_Cell(70,175,30,20,"","LTBR","L",true);
			$this->ac_Cell(10,175,28,10,_("Not Present"),"","L",false);
			$this->ac_Cell(38,175,28,10,_("Eliminated"),"","L",false);
			$this->ac_Cell(70,175,30,10,_("Time").":","","L",false);
		}
		// datos manga 2
		if ($this->manga2 !=null) {
			$this->ac_Cell(110,100,72,10,_("Faults").":","LTBR","L",false);
			$this->ac_Cell(182,100,18,20,"F:","LTBR","L",true);
			for ($n=0;$n<9;$n++)$this->ac_Cell(110+(8*$n),110,8,10,$n+1,"LB","C",(($n%2)!=0)?true:false);

			$this->ac_Cell(110,125,72,10,_("Touchs").":","LTBR","L",false);
			$this->ac_Cell(182,125,18,20,"T:","LTBR","L",true);
			for ($n=0;$n<9;$n++)$this->ac_Cell(110+(8*$n),135,8,10,$n+1,"LB","C",(($n%2)!=0)?true:false);
			
			$this->ac_Cell(110,150,72,10,_("Refusals").":","LTBR","L",false);
			$this->ac_Cell(182,150,18,20,"R:","LTBR","L",true);
			for ($n=0;$n<3;$n++)$this->ac_Cell(110+(8*$n),160,8,10,$n+1,"LBR","C",(($n%2)!=0)?true:false);

			$this->ac_Cell(110,175,28,20,"","LTB","L",false);
			$this->ac_Cell(138,175,28,20,"","LTBR","L",false);
			$this->ac_Cell(170,175,30,20,"","LTBR","L",true);
			$this->ac_Cell(110,175,28,10,_("Not Present"),"","L",false);
			$this->ac_Cell(138,175,28,10,_("Eliminated"),"","L",false);
			$this->ac_Cell(170,175,30,10,_("Time").":","","L",false);
				
		}

		// Asistentes de pista
		$this->ac_header(2,12);
		if($this->manga!=null) 	$this->ac_Cell(10,200,90,10,_("Record by").":","LTBR","L",true);
		if($this->manga2!=null)	$this->ac_Cell(110,200,90,10,_("Record by").":","LTBR","L",true);
		if($this->manga!=null) 	$this->ac_Cell(10,215,90,10,_("Review by").":","LTBR","L",true);
		if($this->manga2!=null)	$this->ac_Cell(110,215,90,10,_("Review by").":","LTBR","L",true);
		
		$this->SetTextColor(0,0,0); // restore color on footer
	}
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
		
		$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->SetLineWidth(.3);

        // Rango
        $fromItem=1;
        $toItem=99999;
        if (($this->rango!=="") && preg_match('/^\d+-\d+$/',$this->rango)!==FALSE) {
            $a=explode("-",$this->rango);
            $fromItem=intval($a[0]);
            $toItem=intval($a[1]);
        }
		// Datos
		$orden=1;
		$rowcount=0;
		foreach($this->orden as $row) {
			if (!category_match($row['Categoria'],$this->validcats)) continue;
			// if change in categoria, reset orden counter and force page change
			if ($row['Categoria'] !== $this->categoria) {
				// $this->myLogger->trace("Nueva categoria es: ".$row['Categoria']);
				$this->categoria = $row['Categoria'];
				// $this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre de categoria
				$rowcount=0;
				$orden=1;
			}
            if (($orden<$fromItem) || ($orden>$toItem) ) { $orden++; continue; } // not in range; skip
			// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
			if( ($rowcount % $this->numrows) == 0 ) { // assume $numrows entries per page 
				$this->AddPage();
				if($this->numrows!=1) {
					// indicamos nombre del operador que rellena la hoja
					$this->ac_header(2,12);
					$this->Cell(90,7,_('Record by').':','LTBR',0,'L',true);
					$this->Cell(10,7,'',0,'L',false);
					$this->Cell(90,7,_('Review by').':','LTBR',0,'L',true);
					$this->Ln(15);
				}
			}
			switch($this->numrows) {
				case 1: $this->writeTableCell_extendido($row,$orden);break;
				case 5: $this->writeTableCell_normal($row,$orden);break;
				case 15: $this->writeTableCell_compacto($row,$orden);break;
			}
			$rowcount++;
			$orden++;
		}
		// Línea de cierre
		$this->Cell(array_sum($this->pos),0,'','T');
		$this->myLogger->leave();
	}
}

?>
