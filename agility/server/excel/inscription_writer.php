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
		'Dorsal',
		'Name','Pedigree Name','Gender','Breed','License','KC id','Cat','Grad','Handler','Club','Country', // datos del perro
		'Heat','Comments' // Jornada1, Jornada2, // datos de la inscripcion en la jornada
	);
    protected $fields = array(
		'Dorsal',
		'Nombre','NombreLargo','Genero','Raza','Licencia','LOE_RRC','Categoria','Grado','NombreGuia','NombreClub','Pais', // datos del perro
		'Celo','Observaciones' //, Equipo1, Equipo2 .... // datos de la inscripcion en la jornada
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

	function composeTable() {
		$this->myLogger->enter();
		$this->createInfoPage();
		$this->createPruebaInfoPage($this->prueba,$this->jornadas);
		$insc=new Inscripciones("excel_printInscripciones",$this->prueba['ID']);
		// evaluate journeys to be added as excel column
		foreach ($this->jornadas as $jornada) {
			if ($jornada['Nombre']==='-- Sin asignar --') continue; // skip empty journeys
			array_push($this->cols,$jornada['Nombre']);
			array_push($this->fields,"J".$jornada['Numero']);
		}
		// add "pagado" at the end
		array_push($this->cols,_utf('Paid'));
		array_push($this->fields,'Pagado');

		// Create page
		$journeypage=$this->myWriter->addNewSheetAndMakeItCurrent();
		$name=$this->normalizeSheetName(_utf('Inscriptions'));
		$journeypage->setName($name);

		// write header
		$this->writeTableHeader();

		// retrieve inscription list
		$lista=$insc->enumerate()['rows'];
		foreach ($lista as $perro) {
			$row=array();
			$row[]=$perro['Dorsal'];
			$row[]=$perro['Nombre'];
			$row[]=$perro['NombreLargo'];
			$row[]=$perro['Genero'];
			$row[]=$perro['Raza'];
			$row[]=$perro['Licencia'];
			$row[]=$perro['LOE_RRC'];
			$row[]=$perro['Categoria'];
			$row[]=$perro['Grado'];
			$row[]=$perro['NombreGuia'];
			$row[]=$perro['NombreClub'];
			$row[]=$perro['Pais'];
			$row[]=($perro['Celo']==1)?"X":"";
			$row[]=$perro['Observaciones'];
			// aniadimos info de jornadas (inscrito/equipo
			foreach ($this->jornadas as $jornada) {
				if ($jornada['Nombre']==='-- Sin asignar --') continue; // skip empty journeys
				if ($perro['J'.$jornada['Numero']]==0) { // el perro no esta inscrito en la jornada
					$row[]="";
				} else {	// perro inscrito en la jornada. buscamos equipo. Si no default se pone nombre, else "X"
					$eqobj=new Equipos("excel_printInscripciones",$this->prueba['ID'],$jornada['ID']);
					$equipo=$eqobj->getTeamByPerro($perro['Perro']);
					$row[]=($equipo['DefaultTeam']==1)?"X":$equipo['Nombre'];
				}
			}
			// finalmente informacion de pago
			$row[]=$perro['Pagado'];
			// !!finaly!! add perro to excel table
			$this->myWriter->addRow($row);
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
	$excel->composeTable();
	$excel->close();
    return 0;
} catch (Exception $e) {
	die ("Error accessing database: ".$e->getMessage());
}
?>