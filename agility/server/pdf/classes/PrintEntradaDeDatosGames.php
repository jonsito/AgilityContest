<?php
/*
PrintEntradaDeDatosGames.php

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
 * genera un pdf con las hojas de asistente de pista en jornadas Snooker/Gambler
*/

require_once(__DIR__."/../fpdf.php");
require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/../../logging.php");
require_once(__DIR__.'/../../database/classes/DBObject.php');
require_once(__DIR__.'/../../database/classes/Pruebas.php');
require_once(__DIR__.'/../../database/classes/Jornadas.php');
require_once(__DIR__."/../print_common.php");

class PrintEntradaDeDatosGames extends PrintCommon {
    protected $perros; // lista de participantes en esta jornada
    protected $manga; // datos de la manga
    protected $orden=null; // orden de salida de la manga
    protected $categoria;
    protected $validcats; // categorias de las que se solicita impresion
    protected $fillData;
    protected $rango;
	
	// geometria de las celdas
	protected $cellHeader;
    //                      Dorsal  nombre raza licencia Categoria guia club  celo  observaciones
	protected $pos	=array( 10,     25,     27,    10,    18,      40,   25,  10,    25);
	protected $align=array( 'R',    'C',    'R',    'C',  'C',     'R',  'R', 'C',   'R');
	
	/**
	 * Constructor
     * @param {array} $data constructor parameters: 'prueba','jornada','manga','cats','fill','rango','comentarios'
     * {integer} prueba Prueba ID
     * {integer} jornada Jornada ID
     * {integer} manga Manga ID
     * {string} cats categorias -LMST
     * {string} rango [\d]-[\d]
     * {string} comentarios
	 * @throws Exception
	 */
    function __construct($data) {
    //    function __construct($prueba,$jornada,$manga,$cats,$fill=0) {
		parent::__construct('Portrait',"print_entradaDeDatosGames",$data['prueba'],$data['jornada'],$data['comentarios']);
		if ( ($data['prueba']<=0) || ($data['jornada']<=0) ) {
			$this->errormsg="print_entradaDeDatosGames: either prueba or jornada data are invalid";
			throw new Exception($this->errormsg);
		}
        // guardamos info de la manga
        $this->orden=$data['orden'];
        $this->manga=$data['datosmanga'];
        $this->numrows=$data['numrows']; // should be 8
        $this->validcats=$data['cats'];
        $this->fillData=($data['fill']==0)?false:true;
        $this->rango= (preg_match('/^\d+-\d+$/',$data['rango']))? $data['rango'] : "1-99999";

        // comprobamos que estamos en una jornada de tipo Games
        if (! in_array($this->manga->Tipo,array(29,30))) {
            $this->errormsg="print_entradaDeDatosGames: Jornada {$data['jornada']} has no Games competition declared";
            throw new Exception($this->errormsg);
        }
        // set pdf file name
        $grad=$this->federation->getTipoManga($this->manga->Tipo,3); // nombre de la manga
        $cat=$this->validcats; // categorias del listado
        $str=($cat=='-')?$grad:"{$grad}_{$cat}";
        $res=normalize_filename($str);
        $this->set_FileName("HojasAsistente_{$res}.pdf");
	}

	// Cabecera de página
	function Header() {
        $str=_("Data entry"). " (".$this->federation->getTipoManga($this->manga->Tipo,3).")";
		$this->print_commonHeader($str);

        // pintamos datos de la jornada
        $this->SetFont($this->getFontName(),'B',12); // bold 15
        $str  = $this->jornada->Nombre . " - " . $this->jornada->Fecha;
        $this->Cell(90,9,$str,0,0,'L',false);

        // pintamos tipo y categoria de la manga
        $tmanga= _(Mangas::getTipoManga($this->manga->Tipo,1,$this->federation));
        $categoria=$this->getCatString($this->categoria);
        $str2 = "$tmanga - $categoria";
        $this->Cell(100,9,$str2,0,0,'R',false); // al otro lado tipo y categoria de la manga
        $this->Ln(12);
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}

	private function writeTableCell_common($row,$orden) {
        $wide=$this->federation->get('WideLicense'); // if required use long cell for license
        $logo=$this->getLogoName($row['Perro']);
        $this->ac_header(1,20);
        // save cursor position
        $x=$this->getX();
        $y=$this->GetY();

        // fase 1: contenido de cada celda de la cabecera
        // Cell( width,height,message,border,cursor,align,fill)
        // pintamos logo
        $this->Cell(15,19,'','LTBR',0,'L',false);
        $this->SetXY($x+1,$y+2); // restore cursor position
        $this->Image($logo,$this->getX()+0.5,$this->getY(),12);
        // pintamos numero de orden
        $this->ac_header(2,12);
        // $this->SetXY($x+16,$y+7);
        $this->SetXY($x+1.5,$y+14);
        $this->Cell(12,4,$orden,'',0,'R',true);

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
        // datos cabecera de celda
        $this->SetXY($x+15,$y+2); // restore cursor position
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
    }

	private function writeTableCell_snooker($row,$orden) {
        $this->writeTableCell_common($row,$orden);
        // PENDING WRITE
        $this->Ln(25);
    }

    private function writeTableCell_gambler($row,$orden) {
        $this->writeTableCell_common($row,$orden);
        // PENDING WRITE
        $this->Ln(25);
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
            if ($this->manga->Tipo==29) $this->writeTableCell_snooker($row,$orden);
            if ($this->manga->Tipo==30) $this->writeTableCell_gambler($row,$orden);
            $rowcount++;
            $orden++;
        }
        // Línea de cierre
        $this->Cell(array_sum($this->pos),0,'','T');
        $this->myLogger->leave();
	}
}

?>

