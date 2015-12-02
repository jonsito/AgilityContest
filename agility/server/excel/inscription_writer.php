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

/**
 * genera fichero excel de perros seleccionada desde el menu de la base de datos en el orden especificado en la pantalla
*/

require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Inscripciones.php');
require_once(__DIR__."/common_writer.php");

class Excel_Inscripciones extends XLSX_Writer {

	protected $jornadas=array(); // lista de jornadas de la prueba

    protected $cols = array(
		'Name','Pedigree Name','Gender','Breed','License','KC id','Cat','Grad','Handler','Club','Country', // datos del perro
		'Dorsal','Team','Heat','Comments' // datos de la inscripcion en la jornada
	);
    protected $fields = array(
		'Nombre','NombreLargo','Genero','Raza','Licencia','LOE_RRC','Categoria','Grado','NombreGuia','NombreClub','Pais', // datos del perro
		'Dorsal','Equipo','Celo','Observaciones' // datos de la inscripcion en la jornada
	);

	/**
	 * Constructor
	 * @param {int} $prueba Prueba ID
	 * @throws Exception
	 */
	function __construct($prueba) {
		parent::__construct("inscriptionlist.xlsx");
        $p=new Pruebas("excel_Inscripciones");
        $res=$p->selectByID($prueba);
        if (!is_array($res)){
			$this->errormsg="excel_InscriptionList: getPruebaByID($prueba) failed";
			throw new Exception($this->errormsg);
		}
        $this->prueba=$res;
		$j=new Jornadas("excel_Inscripciones",$prueba);
		$res=$j->selectByPrueba();
		if (!is_array($res)){
			$this->errormsg="excel_InscriptionList: getJornadasByPrueba($prueba) failed";
			throw new Exception($this->errormsg);
		}
		$this->jornadas=$res['rows'];
	}

	private function writeTableHeader() {
		// internationalize header texts
		for($n=0;$n<count($this->cols);$n++) {
			$this->cols[$n]=_utf($this->cols[$n]);
		}
		// send to excel
		$this->myWriter->addRowWithStyle($this->cols,$this->rowHeaderStyle);
	}

	function createInfoPage(){
		parent::createInfoPage(_utf('Inscription List'),$this->prueba['RSCE']);
	}

	function createPruebaInfoPage() {
		// TODO: write
	}

	function composeTable() {
		$this->myLogger->enter();
		$insc=new Inscripciones("excel_printInscripciones",$this->prueba['ID']);
		// iterate on every valid journeys
		foreach ($this->jornadas as $jornada) {
			if ($jornada['Nombre']==='-- Sin asignar --') continue; // skip empty journeys
			// Create page
			$journeypage=$this->myWriter->addNewSheetAndMakeItCurrent();
			$journeypage->setName($jornada['Nombre']); // TODO: beware on forbidden characters
			// write header
			$this->writeTableHeader();
			$res=$insc->inscritosByJornada($jornada['ID']);
			if (!is_array($res)){
				$this->errormsg="excel_InscriptionList: inscritosByJornada({$jornada['ID']}) failed";
				throw new Exception($this->errormsg);
			}
			$lista=$res['rows'];
			foreach($lista as $perro) {
				// TODO: add team information
				$row=array();
				// extract relevant information from database received dog
				for($n=0;$n<count($this->fields);$n++) array_push($row,$perro[$this->fields[$n]]);
				$this->myWriter->addRow($row);
			}
		}
		$this->myLogger->leave();
	}
}

// Consultamos la base de datos
try {
	// 	Creamos generador de documento
	$prueba=http_request("Prueba","i",-1);
	$excel = new Excel_Inscripciones($prueba);
	$excel->open();
	$excel->createInfoPage();
	$excel->createPruebaInfoPage();
	$excel->composeTable();
	$excel->close();
    return 0;
} catch (Exception $e) {
	die ("Error accessing database: ".$e->getMessage());
}
?>