<?php
/*
excel_DogsWriter.php

Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__ . "/XLSXWriter.php");

class DogsWriter extends XLSX_Writer {

	protected $lista; // listado de perros
    protected $fedID;

    protected $cols = array( 'Name','LongName','Gender','Breed','Chip','License','KC id','Category','Grade','Handler','CatGuia','Club','Province','Country');
    protected $fields = array( 'Nombre','NombreLargo','Genero','Raza','Chip','Licencia','LOE_RRC','Categoria','Grado','NombreGuia','CatGuia','NombreClub','Provincia','Pais');

	/**
	 * Constructor
     * @param {integer} $fed Federation ID
	 * @throws Exception
	 */
	function __construct($fed) {
		parent::__construct("doglist.xlsx");
		setcookie('fileDownload','true',time()+30,"/"); // tell browser to hide "downloading" message box
        $this->fedID=$fed;
        $d=new Dogs("excel_listaPerros",$this->fedID);
        $res=$d->select();
        if (!is_array($res)){
			$this->errormsg="excel_listaPerros: select() failed";
			throw new Exception($this->errormsg);
		}
        $this->lista=$res['rows'];
        $this->federation=Federations::getFederation($fed);
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
        // create inrormational page
        $this->createInfoPage(_utf("Dog Listing"),intval($this->fedID));
		// Create data page
		$dogspage=$this->myWriter->addNewSheetAndMakeItCurrent();
		$dogspage->setName(_("Dogs"));
		// write header
		$this->writeTableHeader();
        // populate table
		foreach($this->lista as $perro) {
			$row=array();
			// extract relevant information from database received dog
            foreach ($this->fields as $item) {
                if ($item=== 'Categoria' ) {
                    array_push($row,$this->federation->getCategoryShort($perro[$item]));
                } else {
                    array_push($row,$perro[$item]);
                }
            }
			$this->myWriter->addRow($row);
		}
		$this->myLogger->leave();
	}
}

?>