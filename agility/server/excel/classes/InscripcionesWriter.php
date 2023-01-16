<?php
/*
InscripcionesWriter.php

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
require_once(__DIR__ . '/../../database/classes/Pruebas.php');
require_once(__DIR__ . '/../../database/classes/Jornadas.php');
require_once(__DIR__ . '/../../database/classes/Inscripciones.php');
require_once(__DIR__ . "/XLSXWriter.php");

class InscripcionesWriter extends XLSX_Writer {

	protected $jornadas=array(); // lista de jornadas de la prueba
    protected $club=-1; // -1:inscriptiones 0:header x:template for club x

    protected $cols = array(
		'Dorsal',
		'Name','LongName','Gender','Breed','Chip','License','KC id','Category','Grade','Handler','CatGuia','Club','Country', // datos del perro
		'Heat','Comments' // Jornada1, Jornada2, // datos de la inscripcion en la jornada
	);
    protected $fields = array(
		'Dorsal',
		'Nombre','NombreLargo','Genero','Raza','Chip','Licencia','LOE_RRC','Categoria','Grado','NombreGuia','CatGuia','NombreClub','Pais', // datos del perro
		'Celo','Observaciones' //, Equipo1, Equipo2 .... // datos de la inscripcion en la jornada
	);

	/**
	 * Constructor
	 * @param {int} $prueba Prueba ID
	 * @throws Exception
	 */
	function __construct($prueba,$club) {
		parent::__construct("inscriptionlist.xlsx");
        $this->club=intval($club);
		setcookie('fileDownload','true',time()+30,"/"); // tell browser to hide "downloading" message box
        $p=new Pruebas("excel_Inscripciones");
        $res=$p->selectByID($prueba);
        if (!is_array($res)){
			$this->errormsg="excel_InscriptionList: getPruebaByID($prueba) failed";
			throw new Exception($this->errormsg);
		}
        $this->prueba=$res;
        $this->federation=Federations::getFederation(intval($this->prueba['RSCE']));
		$j=new Jornadas("excel_Inscripciones",$prueba);
		$res=$j->selectByPrueba();
		if (!is_array($res)){
			$this->errormsg="excel_InscriptionList: getJornadasByPrueba($prueba) failed";
			throw new Exception($this->errormsg);
		}
		$this->jornadas=$res['rows'];
		// fix excel file name to match club
        $suffix="";
        if ( $this->club > 0) {
            $club=$p->__selectObject("Nombre","clubes","ID={$this->club}");
            $name=$club->Nombre;
            $name = str_replace('\\', '', $name);
            $name = str_replace('/', '', $name);
            // Remove all characters that are not the separator, a-z, 0-9, or whitespace
            $name = preg_replace('![^'.preg_quote('-').'a-z0-_9\s]+!', '', strtolower($name));
            // Replace all separator characters and whitespace by a single separator
            $suffix = "_" . preg_replace('!['.preg_quote('-').'\s]+!u', '_', $name);
        }
        $this->myFile="inscriptionlist".$suffix.".xlsx";
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
		$this->createInfoPage(_utf('Inscription List'),$this->prueba['RSCE']);
		$this->createPruebaInfoPage($this->prueba,$this->jornadas);
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

        if ($this->club==-1) return; // just print header
        $insc=new Inscripciones("excel_printInscripciones",$this->prueba['ID']);
        if ($this->club==0) { // retrieve inscription list
            $lista=$insc->enumerate()['rows'];
        } else { // retrieve dog list from provided club
            $res=$insc->__select(
                "*",
                "perroguiaclub",
                "(Club={$this->club}) && (Federation={$this->prueba['RSCE']})",
                "Categoria ASC, Grado ASC, Nombre ASC",
                "");
            $lista=$res['rows'];
        }
		foreach ($lista as $perro) {
		    if ( ($this->club>0) && ($this->club!=$perro['Club']) ) continue;
            $strip=($this->club>0)?true:false;
			$row=array();
			$row[]=($this->club>0)? 0 : $perro['Dorsal'];
			$row[]=$perro['Nombre'];
			$row[]=$perro['NombreLargo'];
			$row[]=$perro['Genero'];
            $row[]=$perro['Raza'];
            $row[]=$perro['Chip'];
			$row[]=$perro['Licencia'];
			$row[]=$perro['LOE_RRC'];
            // $row[]=$this->federation->getCategory($perro['Categoria']);
            // $row[]=$this->federation->getGrade($perro['Grado']);
            $row[]=$this->federation->getCategoryShort($perro['Categoria']);
            $row[]=$perro['Grado'];
            $row[]=$perro['NombreGuia'];
            $row[]=$this->federation->getHandlerCategory($perro['CatGuia']);
			$row[]=$perro['NombreClub'];
			$row[]=$perro['Pais'];
			$row[]=($this->club>0)? "" : ( ((0x01 & $perro['Celo'])==1)?"X":"");
			$row[]=($this->club>0)? "" : $perro['Observaciones'];
			// aniadimos info de jornadas (inscrito/equipo
			foreach ($this->jornadas as $jornada) {
				if ($jornada['Nombre']==='-- Sin asignar --') continue; // skip empty journeys
                if ($this->club>0) { $row[]=""; continue; } // in club template mode just print empty field
				if ($perro['J'.$jornada['Numero']]==0) {
				    // el perro no esta inscrito en la jornada
					$row[]="";
					continue;
				}
				// si Estamos en una jornada por equipos, ponemos el nombre del equipo
                if( Jornadas::isJornadaEquipos($jornada) ){
                    $eqobj=new Equipos("excel_printInscripciones",$this->prueba['ID'],$jornada['ID']);
                    $equipo=$eqobj->getTeamByPerro($perro['Perro']);
                    $row[]=$equipo['Nombre'];
                    continue;
                }
				// llegando aqui, no es una jornada por equipos, pero el perro esta inscrito: ponemos "X"
                $row[]="X";
			}
			// por ultimo, informacion de pago
			$row[]=($this->club>0)? 0 : $perro['Pagado'];
			// !!finaly!! add perro to excel table
			$this->myWriter->addRow($row);
		}
		$this->myLogger->leave();
	}
}

?>