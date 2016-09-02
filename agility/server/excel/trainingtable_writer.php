<?php
/*
excel_listaPerros.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../../modules/Federations.php");
require_once(__DIR__.'/../database/classes/Entrenamientos.php');
require_once(__DIR__."/common_writer.php");

class excel_Entrenamientos extends XLSX_Writer {

	protected $lista; // tandas de entrenamientos

    protected $cols = array();
    protected $fields = array( 'NombreClub','Fecha','Firma','Veterinario','Comienzo','Duracion','Key1','Value1','Key2','Value2','Key3','Value3','Key4','Value4','Observaciones');

	/**
	 * Constructor
     * @param {interger} prueba PruebaID
	 * @throws Exception
	 */
	function __construct($prueba,$fed) {
		parent::__construct("trainingtable.xlsx");
		setcookie('fileDownload','true',time()+30,"/"); // tell browser to hide "downloading" message box
        $d=new Entrenamientos("excel_Entrenamientos",$prueba);
        $res=$d->select();
        if (!is_array($res)){
			$errormsg="print_listaPerros: select() failed";
			throw new Exception($errormsg);
		}
		$clb=Federations::getFederation(intval($fed))->getClubString(); // country or club
		$this->cols=
            array( $clb,_('Date'),_('Check-in'),_('Veterinary'),_('Start'),_('Duration'),'Key1','Value1','Key2','Value2','Key3','Value3','Key4','Value4',_('Comments'));
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
		$dogspage->setName(_("Trainings"));
		// write header
		$this->writeTableHeader();

		foreach($this->lista as $item) {
			$row=array();
			// extract relevant information from database received dog
			for($n=0;$n<count($this->fields);$n++) array_push($row,$item[$this->fields[$n]]);
			$this->myWriter->addRow($row);
		}
		$this->myLogger->leave();
	}
}

// Consultamos la base de datos
try {
	// 	Creamos generador de documento
	$prueba=http_request("Prueba","i",0);
    $fed=http_request("Federation","i",0);
	$excel = new Excel_Entrenamientos($prueba,$fed);
	$excel->open();
	$excel->createInfoPage(_utf("Training table"),intval($fed));
	$excel->composeTable();
	$excel->close();
    return 0;
} catch (Exception $e) {
	die ("Error accessing database: ".$e->getMessage());
}
?>