<?php
/*
PrintOrdenSalidaWAO.php

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
 * genera un pdf ordenado por club, categoria y nombre con una pagina por cada jornada
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

class PrintOrdenSalidaWAO extends PrintCommon {

	protected $manga; // datos de la manga
	protected $orden; // orden de salida
	protected $categoria; // categoria que estamos listando
	protected $validcats; // categorias que nos han pedido listar
    protected $rango; // ordenes a imprimir
	protected $teams; // lista de equipos de esta jornada
	
	// geometria de las celdas
	protected $cellHeader;
    protected $pos;
    protected $align;
	
	/**
	 * Constructor
     * @param array $data (prueba,jornada, manga, categorias, rango, comentarios)
     *      {integer} Prueba ID
     *      {integer} jornada Jornada ID
     *      {integer} manga Manga ID
     *      {string} categorias -XLMST
     *      {string} rango [\d]-[\d]
     *      {string} comentarios
	 * @throws Exception
	 */
    function __construct($data) {
		parent::__construct('Portrait',"print_ordenDeSalida",$data['prueba'],$data['jornada'],$data['comentarios']);
		if ( ($data['prueba']<=0) || ($data['jornada']<=0) || ($data['manga']<=0) ) {
			$this->errormsg="printOrdenDeSalida: either prueba/jornada/ manga/orden data are invalid";
			throw new Exception($this->errormsg);
		}
		// Datos de la manga
		$m = new Mangas("printOrdenDeSalida",$data['jornada']);
		$this->manga= $m->selectByID($data['manga']);
		// Datos del orden de salida
		$o = Competitions::getOrdenSalidaInstance("printOrdenDeSalida",$data['manga']);
        $os= $o->getData(/*false,8,null*/); // no team view, no categories, no previous resultset
		$this->orden=$os['rows'];
		$this->categoria="X";
        $this->cellHeader =
         //                0            1       2         3        4           5           6           7              8          9
                array(_('Order'),_('Dorsal'),_('Name'),_('Breed'),_('Gender'),_('Handler'),_('Cat'),$this->strClub,_('Heat'),_('Comments'));
        //                  orden    dorsal  nombre   raza Genero               guia  catGuia club   celo   observaciones
        $this->pos	=array(  12,      10,     30,    22,    10,                38,   15,    24,     9,    20);
        $this->align=array(  'R',    'R',    'L',    'R',   'C',               'R',  'C',   'R',    'C',   'R');
        // obtenemos los datos de equipos de la jornada indexados por el ID del equipo
		$eq=new Equipos("print_ordenDeSalida",$data['prueba'],$data['jornada']);
        $this->teams=array();
        foreach($eq->getTeamsByJornada() as $team) $this->teams[$team['ID']]=$team;
        $this->rango= (preg_match('/^\d+-\d+$/',$data['rango']))? $data['rango'] : "1-99999";
        // set file name
        $grad=$this->federation->getTipoManga($this->manga->Tipo,3); // nombre de la manga
        $str=($data['categorias']=='-')?$grad:"{$grad}_{$data['categorias']}";  // categorias del listado
        $res=normalize_filename($str);
        $this->set_FileName("OrdenDeSalidaWAO_{$res}.pdf");
        // set categories to compare against
        $this->heights=Competitions::getHeights($this->prueba->ID,$this->jornada->ID,$this->manga->ID);
        $this->validcats=compatible_categories($this->heights,$data['categorias']);
        // do not show fed icon in pre-agility, special, or ko
        if (in_array($this->manga->Tipo,array(0,1,2,15,16,18,19,20,21,22,23,24,))) {
            $this->icon2=getIconPath($this->federation->get('Name'),"null.png");
        }
	}

	private function isTeam() {
		switch ($this->manga->Tipo) {
			case 8: case 9: case 13: case 14: return true;
			default: return false;
		}
	}
	
	// Cabecera de página
	function Header() {
		$this->print_commonHeader(_("Starting order"));
		$this->print_identificacionManga($this->manga,$this->getCatString($this->categoria,$this->heights));
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}
	
	function writeTableHeader() {
		// $this->myLogger->enter();

		// Colores, ancho de línea y fuente en negrita de la cabecera de tabla
		$this->ac_header(1,9);

        $this->Cell($this->pos[0],7,$this->cellHeader[0],1,0,'C',true); // orden
        if ($this->federation->isInternational())
            $this->Cell($this->pos[7],7,$this->cellHeader[7],1,0,'C',true); // pais
        $this->Cell($this->pos[1],7,$this->cellHeader[1],1,0,'C',true); // dorsal
        $this->Cell($this->pos[2],7,$this->cellHeader[2],1,0,'C',true); // nombre
        $this->Cell($this->pos[3],7,$this->cellHeader[3],1,0,'C',true); // raza
        $this->Cell($this->pos[4],7,$this->cellHeader[4],1,0,'C',true); // genero
        $this->Cell($this->pos[5],7,$this->cellHeader[5],1,0,'C',true); // nombreguia
        $this->Cell($this->pos[6],7,$this->cellHeader[6],1,0,'C',true); // catguia
        if (! $this->federation->isInternational())
            $this->Cell($this->pos[7],7,$this->cellHeader[7],1,0,'C',true); // nombreclub
        $this->Cell($this->pos[8],7,$this->cellHeader[8],1,0,'C',true); // celo
        $this->Cell($this->pos[9],7,$this->cellHeader[9],1,0,'C',true); // observaciones

		// Restauración de colores y fuentes
		$this->ac_row(2,9);
		$this->Ln();
		// $this->myLogger->leave();
	}
	
	function printTeamInformation($team) {
		$this->ac_header(2,9);
		$nombre=$this->teams[$team]['Nombre'];
		$this->Cell(190,6,$nombre,'LTBR',0,'R',true);
		$this->ac_row(2,9);
		$this->Ln();
	}
    /*
     * Insert a json hidden text into pdf to allow easy data recovery from PDF
    * this is a little help to let "pdf2txt --layout" to generate something available to be parsed and
    * compose a csv table to be imported: just write a hidden text key for each value in row
     *
     * usage ( suggestion. need to be revised after command execution, as some data may be missing ) :
     * pdftotext --layout Catalogo_inscripciones.pdf - | awk '/NombreLargo/ {print;}'
    */
    private function printHiddenRowData($count,$row) {
        $data = new stdClass();
        $data->Dorsal=$row['Dorsal'];
        $data->Nombre=$row['Nombre'];
        $data->NombreLargo=$row['NombreLargo'];
        $data->Raza=$row['Raza'];
        $data->Licencia=$row['Licencia'];
        $data->Categoria=$row['Categoria'];
        $data->Grado=$row['Grado'];
        $data->NombreGuia=$row['NombreGuia'];
        $data->CatGuia=$row['CatGuia'];
        $data->NombreGuia=$this->getHandlerName($row);
        $data->Club=$row['NombreClub'];
        $str=json_encode($data);
        // preserve current X coordinate and evaluate where to put hidden data
        $x=$this->GetX(); $y=$this->GetY();
        $this->SetX($x+10);
        // set foregroud and background to white to let text trasparent
        // notice that hidden data should be printed _before_ real data, otherwise real data printout will be overriden
        $this->SetTextColor(255,255,255);
        $this->SetFillColor( 255,255,255);
        $this->SetFont($this->getFontName(),'',1); // tiny size, wont be visible
        $this->myLogger->trace("hidden line:\n{$str}");
        $this->Cell(140,7,$str,'',0,'L',true);
        $this->ac_row($count,10); // set proper row background
        $this->SetTextColor(0,0,0); // negro
        $this->SetXY($x,$y); // restore cursor position
    }
	// Tabla coloreada
	function composeTable() {
        $rowsperpage=38;
		$this->myLogger->enter();
        $this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor'));
		$this->SetLineWidth(.3);


        // Rango
        $fromItem=1;
        $toItem=99999;
        if (preg_match('/^\d+-\d+$/',$this->rango)!==FALSE) {
            $a=explode("-",$this->rango);
            $fromItem=( intval($a[0]) <= 0 )? 1 : intval($a[0]);
            $toItem=( intval($a[1]) > count($this->orden) )? count($this->orden) : intval($a[1]);
            $this->myLogger->trace("from:$fromItem to:$toItem");
        }
		// Datos
		$rowcount=0; // contador de filas en la hoja
		$order=0; // contador de perros existentes en orden de salida
		$lastTeam=0;
        $printed=1; // contador de perros realmente impresos
		foreach($this->orden as $row) {
		    // elimina todos los perros que no entran en las categorias a imprimir
			if (!category_match($row['Categoria'],$this->heights,$this->validcats)) continue;

			$newTeam=intval($row['Equipo']);
			// REMINDER: $this->cell( width, height, data, borders, where, align, fill)

			// if change in categoria, reset orden counter.
            $ccats=compatible_categories($this->heights,$this->categoria);
            if (!category_match($row['Categoria'],$this->heights,$ccats)) {
                $this->categoria = $row['Categoria'];
                $this->Cell(array_sum($this->pos),0,'','T'); // forzamos linea de cierre

			    // if new category header fits in page show it; else force new page
                if($rowcount > 32) {
                    $rowcount=$rowsperpage;
                } else if ($rowcount!=0) {
                    $this->Ln(10);
                    $this->print_identificacionManga($this->manga,$this->getCatString($this->categoria,$this->heights));
                    $this->writeTableHeader();
                    $rowcount+=4;
                }
				$order=0;
				$lastTeam=0;
			}
            // dog not in range to print: count and  skip
            if ( (($order+1)<$fromItem) || (($order+1)>$toItem) ) { $order++; continue; }
            // in team best min/max, there can be more dogs than max, so if dog is marked as "Not Presented" skip
            if(intval($row['NoPresentado'])===1) { $order++; continue; } ;
			// on team, if team change, make sure that new team fits in page. Else force new page
			if ( $this->isTeam() && ($newTeam!=$lastTeam) && ($rowcount>=32) ) $rowcount=$rowsperpage;

			if ( ($rowcount==0) || ($rowcount>=$rowsperpage) ) { // assume 38 rows per page ( rowWidth = 6mmts )
                $this->Cell(array_sum($this->pos),0,'','T');// linea de cierre en cambio de pagina$this->AddPage();
				$rowcount=0;
				$this->AddPage();
                $this->writeTableHeader();
				$lastTeam=0; // force writting of team header information
			}
			// on team Events and team change add Team header information
			if ( $this->isTeam() && ($newTeam!=$lastTeam) ) {
				$lastTeam=$newTeam;
				$this->printTeamInformation($lastTeam);
				$rowcount++;
			}

			// "print" hidden data to allow importing as Excel-like file
            $this->printHiddenRowData($order,$row);
			// now start real printing :-)
            $this->ac_row($order,9);
			$this->SetFont($this->getFontName(),'B',11); // bold 11px
            // if dog has already run, use "*" instead of "-"
            $ordersep=($row['Pendiente']==0)?" * ":" - ";
			$this->Cell($this->pos[0],6,($printed).$ordersep,'LR',0,$this->align[0],true); // display order
			$this->SetFont($this->getFontName(),'',9); // remove bold 11px
            if ($this->federation->isInternational()) {
                $this->Cell($this->pos[7],6,$row['NombreClub'],	'LR',0,$this->align[7],true);
            }
			$this->Cell($this->pos[1],6,$row['Dorsal'],		'LR',0,$this->align[1],true);
            $n=($this->useLongNames)? $row['Nombre']." - ".$row['NombreLargo']:$row['Nombre'];
            $this->Cell($this->pos[2],6,$n,		'LR',0,$this->align[2],true);
            $this->Cell($this->pos[3],6,$row['Raza'],		'LR',0,$this->align[3],true);
            $this->Cell($this->pos[4],6,$row['Genero'],		'LR',0,$this->align[4],true);
            $this->Cell($this->pos[5],6,$row['NombreGuia'],	'LR',0,$this->align[5],true);
            $this->Cell($this->pos[6],6,$this->getHandlerCategory($row),	'LR',0,$this->align[6],true);
            if (! $this->federation->isInternational()) {
                $this->Cell($this->pos[7],6,$row['NombreClub'],	'LR',0,$this->align[7],true);
            }
			$this->Cell($this->pos[8],6,((0x01&intval($row['Celo']))===0)?"":_("Heat"),	'LR',0,$this->align[8],true);
			$this->Cell($this->pos[9],6,$row['Observaciones'],'LR',0,$this->align[9],true);
			$this->Ln();
			$rowcount++;
			$order++;
            $printed++;
		}
		// Línea de cierre
		$this->Cell(array_sum($this->pos),0,'','T');
		$this->myLogger->leave();
	}
}
?>