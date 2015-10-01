<?php
/*
print_listaPerros.php

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
 * genera un pdf lista de perros seleccionada desde el menu de la base de datos en el orden especificado en la pantalla
*/

require_once(__DIR__."/fpdf.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Dogs.php');
require_once(__DIR__."/print_common.php");

class Print_ListaPerros extends PrintCommon {

	protected $lista; // listado de perros

    // $cols=array( `ID`,`Federation`,`Nombre`,`Raza`,`Licencia`,`LOE_RRC`,`Categoria`,`NombreCategoria`,`Grado`,`NombreGrado`,`Guia`,`NombreGuia`,`Club`,`NombreClub`);
    public static $cols = array( 'ID','Name','Breed','License','KC id','Cat','Grad','Handler','Club');
    public static $fields = array( 'ID','Nombre','Raza','Licencia','LOE_RRC','Categoria','Grado','NombreGuia','NombreClub');
    public static $pos = array(   10,  25,      20,    20,        15,       10,          10,    45,    35  );
    public static $align = array( 'C',  'L',    'L',   'R',       'R',       'C',          'C',    'R',    'R'  );

	/**
	 * Constructor
	 * @throws Exception
	 */
	function __construct() {
		date_default_timezone_set('Europe/Madrid');
		parent::__construct('Portrait',"print_ordenTandas",1,0); // use default prueba. not really needed
        $d=new Dogs("print_listaPerros");
        $res=$d->select();
        if (!is_array($res)){
			$this->errormsg="print_listaPerros: select() failed";
			throw new Exception($this->errormsg);
		}
        $this->lista=$res['rows'];
        $this->icon2=$this->icon;
        $this->icon="agilitycontest.png";
	}
	
	// Cabecera de página
	function Header() {
		// cabecera comun
		$this->print_commonHeader(_("List of registered dogs"));
	}
	
	// Pie de página
	function Footer() {
		$this->print_commonFooter();
	}
	
	function writeTableHeader() {
		$this->myLogger->enter();
		$this->ac_header(1,10);
		$this->setXY(10,37.5);
        for ($n=0;$n<count(Print_ListaPerros::$cols);$n++){
            // REMINDER: $this->cell( width, height, data, borders, where, align, fill)
            $this->Cell(Print_ListaPerros::$pos[$n],8,_(Print_ListaPerros::$cols[$n]),'LTRB',0,'C',true);
        }
		$this->Ln();
		$this->myLogger->leave();
	}
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();

		$rowcount=0;
		foreach($this->lista as $perro) {
			// $this->cell(width,height,text,border,start,align,fill)
			if (($rowcount%47)==0) {
				$this->addPage();
				$this->writeTableHeader();
			}
            $this->ac_row($rowcount,8.5);
            $this->setX(10);
            for ($n=0;$n<count(Print_ListaPerros::$cols);$n++){
                $this->Cell(Print_ListaPerros::$pos[$n],5,$perro[Print_ListaPerros::$fields[$n]],'LBR',0,Print_ListaPerros::$align[$n],true);
            }
            $this->Ln();
			$rowcount++;
		}
		$this->myLogger->leave();
        return "";
	}
}

// Consultamos la base de datos
try {
	// 	Creamos generador de documento
	$pdf = new Print_ListaPerros();
	$pdf->AliasNbPages();
	$pdf->composeTable();
	$pdf->Output("print_listaPerros.pdf","D"); // "D" means open download dialog
    return 0;
} catch (Exception $e) {
	die ("Error accessing database: ".$e->getMessage());
}
?>