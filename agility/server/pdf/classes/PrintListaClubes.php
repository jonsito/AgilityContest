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
require_once(__DIR__.'/../../database/classes/DBObject.php');
require_once(__DIR__.'/../../database/classes/Clubes.php');
require_once(__DIR__."/../print_common.php");

class PrintListaClubes extends PrintCommon {

	protected $lista; // listado de clubes

    protected $cols    = array( 'Name',  'Address',   'Country','Contact',  'Internet', 'RSCE','RFEC','CPC','Intl 4','Intl 3','Out');
    protected $fields1 = array( 'Nombre','Direccion1','Pais',   'Contacto1','Email',    'RSCE','RFEC','CPC','Intl4', 'Intl3', 'Out');
    protected $fields2 = array( '',      'Direccion2','',       'Contacto2','Web','',   '',    '',    '',   '',      '',      ''   );
    protected $fields3 = array( '',      'Provincia', '',       'Contacto3','Facebook', '',    '',    '',   '',      '',      ''   );
    protected $pos     = array( 25,      35,          10,        25,        45,         8,     8,     8,    8,       8,       10    );
    protected $align   = array( 'R',     'R',         'L',       'L',       'R',        'C',   'C',   'C',  'C',     'C',     'C'  );

	/**
	 * Constructor
	 * @throws Exception
	 */
	function __construct() {
		date_default_timezone_set('Europe/Madrid');
		parent::__construct('Portrait',"print_listaPerros",1,0); // use default prueba. not really needed
        $d=new Clubes("print_listaClubes");
        $res=$d->select();
        if (!is_array($res)){
			$this->errormsg="print_listaClubes: select() failed";
			throw new Exception($this->errormsg);
		}
        $this->lista=$res['rows'];

        // rework federation handling no sense in clubes, to properly generate page logos
        $this->federation=Federations::getFederation(0); // just to retrieve logos, not used
		$this->icon=getIconPath($this->federation->get('Name'),"agilitycontest.png");
        $this->icon2=getIconPath($this->federation->get('Name'),"null.png");

		$this->set_FileName("Lista_Clubes.pdf");
	}
	
	// Cabecera de página
	function Header() {
		// cabecera comun
		$this->print_commonHeader(_("List of registered clubs"));
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}
	
	function writeTableHeader() {
		$this->myLogger->enter();
		$this->ac_header(1,7);
		$this->setXY(10,37.5);
        for ($n=0;$n<count($this->cols);$n++){
			if ($this->pos[$n]==0) continue;
            // REMINDER: $this->cell( width, height, data, borders, where, align, fill)
            $this->Cell($this->pos[$n],6,_($this->cols[$n]),'LTRB',0,'C',true);
        }
		$this->Ln();
		$this->myLogger->leave();
	}
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();

		$rowcount=0;
		foreach($this->lista as $club) {
		    if ($club['ID']==1) continue;
		    // we print three (3) lines for club, to provide enought space for full info
			// $this->myLogger->trace("club: ".$club['Nombre']);
            // extract federation info
            $club['RSCE']= (( $club['Federations']& 0x0001) == 0 )? "":"RSCE";
            $club['RFEC']= (( $club['Federations']& 0x0002) == 0 )? "":"RFEC";
            $club['CPC']=  (( $club['Federations']& 0x0010) == 0 )? "":"CPC";
            $club['Intl4']=(( $club['Federations']& 0x0100) == 0 )? "":"Intl-4";
            $club['Intl3']=(( $club['Federations']& 0x0200) == 0 )? "":"Intl-3";
            $club['Out']=( $club['Baja'] == 0 )? "":_('Out');
			// $this->cell(width,height,text,border,start,align,fill)
			if (($rowcount%60)==0) {
				$this->AddPage();
				$this->writeTableHeader();
			}
            $this->ac_row($rowcount/3,7);
            $this->setX(10);
            for ($n=0;$n<count($this->cols);$n++){ // primera fila
                // first item on first line is club name. paint bold
                $this->SetFont($this->getFontName(),($n==0)?'B':'',7); // bold 7px
                $data=($this->fields1[$n]==='')?"":$club[$this->fields1[$n]];
                $this->Cell($this->pos[$n],4,$data,'LTR',0,$this->align[$n],true);
            }
            $this->Ln(4);
            for ($n=0;$n<count($this->cols);$n++){ // segunda fila
                $data=($this->fields2[$n]==='')?"":$club[$this->fields2[$n]];
                $this->Cell($this->pos[$n],4,$data,'LR',0,$this->align[$n],true);
            }
            $this->Ln(4);
            for ($n=0;$n<count($this->cols);$n++){ // tercera fila
                $data=($this->fields3[$n]==='')?"":$club[$this->fields3[$n]];
                $this->Cell($this->pos[$n],4,$data,'LBR',0,$this->align[$n],true);
            }
            $this->Ln(4);
			$rowcount+=3;
		}
		$this->myLogger->leave();
        return "";
	}
}
?>