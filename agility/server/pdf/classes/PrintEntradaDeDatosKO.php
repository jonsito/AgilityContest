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
		parent::__construct('Portrait',"print_entradaDeDatosEquipos4",$data['prueba'],$data['jornada'],$data['comentarios']);
		if ( ($data['prueba']<=0) || ($data['jornada']<=0) ) {
			$this->errormsg="print_datosEquipos4: either prueba or jornada data are invalid";
			throw new Exception($this->errormsg);
		}
        // comprobamos que estamos en una jornada por equipos
        $flag=intval($this->jornada->Equipos3)+intval($this->jornada->Equipos4);
        if ($flag==0) {
            $this->errormsg="print_datosEquipos4: Jornada {$data['jornada']} has no Team competition declared";
            throw new Exception($this->errormsg);
        }
        // guardamos info de la manga
        $this->manga=$this->myDBObject->__getObject("Mangas",$data['manga']);
        $this->validcats=$data['cats'];
        $this->fillData=($data['fill']==0)?false:true;
        $this->rango= (preg_match('/^\d+-\d+$/',$data['rango']))? $data['rango'] : "1-99999";

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
        // indicamos nombre del operador que rellena la hoja
        $this->ac_header(2,12);
        $this->Cell(90,7,_("Record by").":",'LTBR',0,'L',true);
        $this->Cell(10,7,'',0,'L',false);
        $this->Cell(90,7,_("Review by").":",'LTBR',0,'L',true);
        $this->Ln();
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}

	function writeTableCell_1($row,$orden) {
        // cada 2 entradas (orden impar) ponemos numero de pareja
        // pintamos dato del perro
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
            if( ($rowcount % 16) == 0 ) { // assume $numrows entries per page
                $this->AddPage();
                // indicamos nombre del operador que rellena la hoja
                $this->ac_header(2,12);
                $this->Cell(90,7,_('Record by').':','LTBR',0,'L',true);
                $this->Cell(10,7,'',0,'L',false);
                $this->Cell(90,7,_('Review by').':','LTBR',0,'L',true);
                $this->Ln(15);
            }
            $this->writeTableCell_1($row,$orden);
            $rowcount++;
            $orden++;
        }
        // Línea de cierre
        $this->Cell(array_sum($this->pos),0,'','T');
        $this->myLogger->leave();
	}
}

?>

