<?php
/*
excel_listaPerros.php

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
 * genera fichero excel de perros seleccionada desde el menu de la base de datos en el orden especificado en la pantalla
*/

require_once(__DIR__ . "/../../tools.php");
require_once(__DIR__ . "/../../logging.php");
require_once(__DIR__ . '/../../database/classes/Dogs.php');
require_once(__DIR__ . "/../common_writer.php");

class Excel_ListaPerros extends XLSX_Writer {

	protected $lista; // listado de perros

    protected $cols = array( 'Name','LongName','Gender','Breed','Chip','License','KC id','Category','Grade','Handler','Club','Province','Country');
    protected $fields = array( 'Nombre','NombreLargo','Genero','Raza','Chip','Licencia','LOE_RRC','Categoria','Grado','NombreGuia','NombreClub','Provincia','Pais');

	/**
	 * Constructor
	 * @throws Exception
	 */
	function __construct() {
		parent::__construct("doglist.xlsx");
		setcookie('fileDownload','true',time()+30,"/"); // tell browser to hide "downloading" message box
        $d=new Dogs("excel_listaPerros",$this->federation);
        $res=$d->select();
        if (!is_array($res)){
			$this->errormsg="print_listaPerros: select() failed";
			throw new Exception($this->errormsg);
		}
        $this->lista=$res['rows'];
	}

	private function writeTableHeader() {
		// internationalize header texts
		for($n=0;$n<count($this->cols);$n++) {
			$this->cols[$n]=_utf($this->cols[$n]);
		}
		// send to excel
		$this->myWriter->addRowWithStyle($this->cols,$this->rowHeaderStyle);
	}

	function composeTable() {
		$this->myLogger->enter();

		// Create page
		$dogspage=$this->myWriter->addNewSheetAndMakeItCurrent();
		$dogspage->setName(_("Dogs"));
		// write header
		$this->writeTableHeader();

		foreach($this->lista as $perro) {
			$row=array();
			// extract relevant information from database received dog
			for($n=0;$n<count($this->fields);$n++) array_push($row,$perro[$this->fields[$n]]);
			$this->myWriter->addRow($row);
		}
		$this->myLogger->leave();
	}
}

?>