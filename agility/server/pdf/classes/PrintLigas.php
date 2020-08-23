<?php
/*
PrintListaPerros.php

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
 * genera un pdf lista de perros seleccionada desde el menu de la base de datos en el orden especificado en la pantalla
*/

require_once(__DIR__."/../fpdf.php");
require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/../../logging.php");
require_once(__DIR__."/../print_common.php");

class PrintLigas extends PrintCommon {

    protected $lista=null; // {array} listado de resultados
    protected $header=null; // {array} campos de la tabla de resultados
    protected $perro=null; // {object} perro sobre el que se imprimen los resultados. may be null for global leagues
    protected $grado=null; // {string} grado de la liga. puede ser distinto del grado actual del perro

    // $results['header'] contains needed info to layout data
    protected $scale=0; // {float} scale factor to layout each rows

    /**
	 * Constructor
	 * @throws Exception
	 */
	function __construct($federation,$result) {
		date_default_timezone_set('Europe/Madrid');
		parent::__construct('Portrait',"print_liga",1,0); // use default prueba. not really needed
        if (array_key_exists("dog",$result)) $this->perro=$result['dog'];
        $this->lista=$result['rows'];
        $this->header=$result['header'];
        $this->perro= array_key_exists('dog',$result)?$result['dog']:null;
        // rework federation handling as parent got it from senseless prueba ID
        $this->federation=Federations::getFederation(intval($federation));
		$this->icon=getIconPath($this->federation->get('Name'),"agilitycontest.png");
        $this->icon2=getIconPath($this->federation->get('Name'),$this->federation->get('Logo'));
        $size=0;
        foreach($result['header'] as $item) {
            if(array_key_exists("hidden",$item)) continue; // skip hidden fields
            $size += intval($item['width']);
            $this->myLogger->trace("column:{$item['title']} width:{$item['width']} size:{$size}");
        }
        $this->scale=($size===0)?195:195/$size; // if no items avoid divide by zero
		$this->set_FileName(($this->perro==null)?"Resultados_Liga.pdf":"Resultados_Perro.pdf");
	}
	
	// Cabecera de página
	function Header() {
		// cabecera comun
		$this->print_commonHeader(_("League Results")." - ".$this->federation->get("Name"));
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}
	
	function writeTableHeader($rowcount) {
		$this->myLogger->enter();
		$this->setXY(10,40.0);
		if ( ($rowcount==0) && ($this->perro!==null) ) {
            $this->ac_header(2,9);
            $this->Cell("195","5", _("Dog Information"),'',0,"L",true);
            $this->Ln();
            $this->Cell("20","5",_("Name").": ",'',0,"R",true);
            $n="{$this->perro->Nombre} - {$this->perro->NombreLargo}";
            $this->Cell("175","5",$n,'',0,"L",false);
            $this->Ln();
            $g=$this->federation->getGrade($this->perro->Grado);
            $c=$this->federation->getCategory($this->perro->Categoria);
            $this->Cell("20","5", _("License").": ",'',0,"R",true);
            $n="{$this->perro->Licencia} - {$c} / {$g}";
            $this->Cell("175","5",$n,'',0,"L",false);
            $this->Ln();
            $this->Cell("20","5",_("Handler").": ",'',0,"R",true);
            $n="{$this->perro->NombreGuia} - {$this->perro->NombreClub}";
            $this->Cell("175","5",$n,'',0,"L",false);
            $this->Ln(6);
        }
        $this->ac_header(1,8);
		foreach($this->header as $field) {
            if(array_key_exists("hidden",$field)) continue; // skip hidden fields
            $size=$field['width']*$this->scale;
            $align="L";
            if ($field['align']==="center") $align="C";
            if ($field['align']==="right") $align="R";
            $this->Cell($size,6,$field['title'],'LTRB',0,$align,true);
        }
		$this->Ln();
		$this->myLogger->leave();
	}

    /**
     * print table data
     * @param $perro dog id
     * @return string
     */
	function composeTable() {
		$this->myLogger->enter();
		$rowcount=0;
		foreach($this->lista as $item) {
		    $nrows=47;
		    if($this->perro!==null) $nrows=($rowcount<=43)?43:47; // number of rows per page
			// $this->cell(width,height,text,border,start,align,fill)
			if (($rowcount%$nrows)==0) {
				$this->AddPage();
				$this->writeTableHeader( $rowcount );
			}
            $this->ac_row($rowcount,7.5);
            $this->setX(10);
            foreach($this->header as $column) {
                if(array_key_exists("hidden",$column)) continue; // skip hidden fields
                $size=$column['width']*$this->scale;
                $align="L";
                if ($column['align']==="center") $align="C";
                if ($column['align']==="right") $align="R";
                $this->Cell($size,5,$item[$column['field']],'LTRB',0,$align,true);
            }
            $this->Ln();
			$rowcount++;
		}
		$this->myLogger->leave();
        return "";
	}
}
?>