<?php
/*
PrintEntradaDeDatosGames.php

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
 * genera un pdf con las hojas de asistente de pista en jornadas KO
*/

require_once(__DIR__."/../fpdf.php");
require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/../../logging.php");
require_once(__DIR__.'/../../database/classes/DBObject.php');
require_once(__DIR__.'/../../database/classes/Pruebas.php');
require_once(__DIR__.'/../../database/classes/Jornadas.php');
require_once(__DIR__."/../print_common.php");

class PrintEntradaDeDatosKO extends PrintCommon {
    protected $perros; // lista de participantes en esta jornada
    protected $manga; // datos de la manga
    protected $validcats; // categorias de las que se solicita impresion
    protected $fillData;
    protected $rango;
    protected $orden; // lista de perros segun el orden de salida
	
	/**
	 * Constructor
     * @param {array} $data constructor parameters: 'prueba','jornada','manga','cats','fill','rango','comentarios'
     * {integer} prueba Prueba ID
     * {integer} jornada Jornada ID
     * {integer} manga Manga ID
     * {string} cats categorias -XLMST
     * {string} rango [\d]-[\d]
     * {string} comentarios
	 * @throws Exception
	 */
    function __construct($data) {
    //    function __construct($prueba,$jornada,$manga,$cats,$fill=0) {
		parent::__construct('Portrait',"print_entradaDeDatosKO",$data['prueba'],$data['jornada'],$data['comentarios']);
		if ( ($data['prueba']<=0) || ($data['jornada']<=0) ) {
			$this->errormsg="print_datosKO: either prueba or jornada data are invalid";
			throw new Exception($this->errormsg);
		}
        // comprobamos que estamos en una jornada por equipos
        $flag=intval($this->jornada->KO);
        // if ($flag==0) {
        //    $this->errormsg="print_entradaDatos_KO: Jornada {$data['jornada']} has no KO competition declared";
        //    throw new Exception($this->errormsg);
        // }
        // guardamos info de la manga
        $this->manga=$this->myDBObject->__getObject("mangas",$data['manga']);
        $this->validcats=$data['cats'];
        $this->fillData=($data['fill']==0)?false:true;
        $this->rango= (preg_match('/^\d+-\d+$/',$data['rango']))? $data['rango'] : "1-99999";
        $this->orden=$data['orden'];

        // set pdf file name
        $grad=$this->federation->getTipoManga($this->manga->Tipo,3); // nombre de la manga
        $cat=$this->validcats; // categorias del listado
        $str=($cat=='-')?$grad:"{$grad}_{$cat}";
        $res=normalize_filename($str);
        $this->set_FileName("HojasAsistente_{$res}.pdf");
        $this->icon2=getIconPath($this->federation->get('Name'),"null.png");
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
        $categoria=$this->getCatString($this->validcats);
        $str2 = "$tmanga - $categoria";
        $this->Cell(100,9,$str2,0,0,'R',false); // al otro lado tipo y categoria de la manga
        $this->Ln(12);
        // indicamos nombre del operador que rellena la hoja
        $this->ac_header(2,12);
        $this->Cell(90,7,_("Record by").":",'LTBR',0,'L',true);
        $this->Cell(10,7,'',0,'L',false);
        $this->Cell(90,7,_("Review by").":",'LTBR',0,'L',true);
        $this->Ln(9);
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}

    private function palotes($count) { $str=""; for (;$count>0;$count--) $str.="| "; return $str; }

    /**
     * Prints 15 dogs / page
     * @param {array} $row
     * @param {integer} $orden . Starting order in their category
     */
    function writeTableCell_16($row,$orden) {
        $wide=$this->federation->hasWideLicense();; // if required use long cell for license
        // save cursor position
        $x=$this->getX();
        $y=$this->GetY();

        // fase 1: contenido de cada celda de la cabecera
        // Cell( width,height,message,border,cursor,align,fill)

        // pintamos Numero de orden de la pareja del KO si estamos en orden impar
        if ($orden%2!=0) {
            $this->ac_header(1,20);
            $this->Cell(15,13,'','LTBR',0,'L',false);
            $this->SetXY($x+1,$y+1); // restore cursor position
            $this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg2')); // color de fondo 1
            $this->Cell(13,11,1+intval($orden/2),'',0,'R',true);
        } else {
            // en pareja par no se pone numero de orden
            $this->Cell(15,13,'','R',0,'L',false);
        }

        // pintamos logo
        $this->ac_header(2,12);
        $this->SetXY($x+15,$y+6);
        $logo=$this->getLogoName($row['Perro']);
        $this->Image($logo,$this->getX()+0.5,$this->getY(),6.5);

        // bordes cabecera de celda
        $this->ac_SetFillColor($this->config->getEnv('pdf_hdrbg1')); // color de fondo 2
        $this->SetXY($x+15,$y); // restore cursor position
        $this->SetFont($this->getFontName(),'B',10); // bold 10px
        $this->Cell(13,5,'',	'LTR',0,'L',true); // dorsal
        $this->Cell(12,5,'',	'LTR',0,'L',true); // dorsal
        $this->Cell(10,5,'',	'TR',0,'L',true); // celo
        if ($wide) {
            $this->Cell(50,5,'',	'TR',0,'L',true); // perro
        } else {
            $this->Cell(20, 5, '', 'TR', 0, 'L', true); // licencia
            $this->Cell(30,5,'',	'TR',0,'L',true); // perro
        }
        $this->Cell(50,5,'',	'TR',0,'L',true); // guia
        $this->Cell(40,5,'',	'TR',0,'L',true); // club
        // datos cabecera de celda
        $this->SetXY($x+15,$y+1); // restore cursor position
        $this->Cell(13,4,$row['Dorsal'],		'',0,'R',false); // display order
        $cat=$this->federation->getCategoryShort($row['Categoria']);
        $this->Cell(12,4,$cat,	'',0,'R',false); // display order
        $this->Cell(10,4,($row['Celo']!=0)?"Celo":"",'',0,'R',false);
        if ($wide) {
            $this->Cell(50,4,$row['Nombre'],		'',0,'R',false);
        } else {
            $this->Cell(20,4,$row['Licencia'],		'',0,'R',false);
            $this->Cell(30,4,$row['Nombre'],		'',0,'R',false);
        }
        $this->Cell(50,4,$this->getHandlerName($row),	'',0,'R',false);
        $this->Cell(40,4,$row['NombreClub'],	'',0,'R',false);

        // titulos cabecera de celda
        $this->SetXY($x+14,$y); // restore cursor position
        $this->SetTextColor(0,0,0); // negro
        $this->SetFont($this->getFontName(),'I',7); // italic 8px
        $this->Cell(13,3,_('Dorsal'),	'',0,'L',false); // display order
        $this->Cell(12,3,_('Cat'),	'',0,'L',false); // display order
        $this->Cell(10,3,_('Heat'),	'',0,'L',false);
        if ($wide) {
            $this->Cell(50,3,_('Name'),	'',0,'L',false);
        } else {
            $this->Cell(20,3,_('Lic'),'',0,'L',false);
            $this->Cell(30,3,_('Name'),	'',0,'L',false);
        }
        $this->Cell(50,3,_('Handler'),	'',0,'L',false);
        $this->Cell(40,3,$this->strClub,	'',0,'L',false);

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
        if (! $this->fillData) {
            $this->Ln(($orden%2!=0)?8:10.5);
            return;
        }
        // arriving here means populate results
        $this->SetFont($this->getFontName(),'B',9); //
        $this->SetXY($x+40,$y+8);
        $this->Cell(45,5,$this->palotes($row['Faltas']),	'',0,'L',false);
        $this->Cell(40,5,$this->palotes($row['Rehuses']),	'',0,'L',false);
        $this->Cell(15,5,$this->palotes($row['Tocados']),	'',0,'L',false);
        $this->Cell(7, 5,$row['Faltas'],	'',0,'C',false);
        $this->Cell(7, 5,$row['Rehuses'],	'',0,'C',false);
        $this->Cell(7, 5,$row['Tocados'],	'',0,'C',false);
        $this->Cell(9,5,$row['Tiempo'],  '',0,'L',false);
        if($row['Pendiente']!=0)  $this->Cell(20,5,_('Pending'),  '',0,'L',false);
        else if($row['NoPresentado']!=0)  $this->Cell(19,5,_('Not Present'),  '',0,'L',false);
        else if($row['Eliminado']!=0)  $this->Cell(19,5,_('Eliminated'),  '',0,'L',false);
        $this->Ln( ($orden%2!=0)?4:7);
    }

	// La hoja de asistente de pista es parecida a la hoja normal, solo que con 16 perros, agrupados de 2 en dos
	function composeTable() {
        $this->myLogger->enter();

        $this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
        $this->SetLineWidth(.3);

        // Rango.
        $fromItem=1;
        $toItem=99999;
        if (($this->rango!=="") && preg_match('/^\d+-\d+$/',$this->rango)!==FALSE) {
            $a=explode("-",$this->rango);
            $fromItem=intval($a[0]);
            $toItem=intval($a[1]);
            // Debemos asegurarnos que origen y final son multiplos de 16
            $fromItem=( ($fromItem-1)%16==0) ? $fromItem : $fromItem - ($fromItem-1)%16;
            $toItem=($toItem%16==0)?$toItem:$toItem-$toItem%16;
            if ($fromItem==$toItem) $toItem+=16;
        }
        // Datos
        $orden=1;
        $rowcount=0;
        $heights=Competitions::getHeights($this->prueba->ID,$this->jornada->ID,0); // same height for all rounds
        foreach($this->orden as $row) {
            // remember: in ko rounds heigths are grouped. so just skip if not selected, but no add new page
            if (!category_match($row['Categoria'],$heights,$this->validcats)) continue;

            if (($orden<$fromItem) || ($orden>$toItem) ) { $orden++; continue; } // not in range; skip
            // REMINDER: $this->cell( width, height, data, borders, where, align, fill)
            if( ($rowcount % 16) == 0 ) { // assume $numrows entries per page
                $this->AddPage();
            }
            $this->writeTableCell_16($row,$orden);
            $rowcount++;
            $orden++;
        }
        $this->myLogger->leave();
	}
}

?>

