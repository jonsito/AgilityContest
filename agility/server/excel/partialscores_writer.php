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
require_once(__DIR__.'/../database/classes/Dogs.php');
require_once(__DIR__."/common_writer.php");

class Excel_PartialScores extends XLSX_Writer {

    protected $myDBObject;
    protected $prueba;
    protected $jornada;
	protected $manga;
	protected $resultados;
	protected $mode;
	protected $timeResolution;

    protected $cols = array( 'License','Category','Grade','Name','LongName','Breed','Handler','Club','Faults','Refusals','Speed','Time','Penalization','Calification','Points','Stars');
    protected $fields = array( 'Licencia','Categoria','Grado','Nombre','NombreLargo','Raza','NombreGuia','NombreClub','Faltas','Rehuses','Velocidad','Tiempo','Penalizacion','Calificacion','Puntos','Estrellas');

	/**
	 * Constructor
	 * @throws Exception
	 */
	function __construct($idprueba,$idjornada,$manga,$resultados,$mode) {
		parent::__construct("partial_scores.xlsx");
		setcookie('fileDownload','true',time()+30,"/"); // tell browser to hide "downloading" message box
        $this->myDBObject=new DBObject("partial_scores.xlsx");
        $this->prueba=$this->myDBObject->__getObject("Pruebas",$idprueba);
        $this->jornada=$this->myDBObject->__getObject("Jornadas",$idjornada);
        $this->manga=$manga;
        $this->resultados=$resultados;
        $this->mode=$mode;
        $this->timeResolution=($this->myConfig->getEnv('crono_miliseconds')=="0")?2:3;
	}

    // Cabecera de página
    function writeCourseData() {
        // evaluate needed data from parameters
        $federation=Federations::getFederation(intval($this->prueba->RSCE));
        $modestr=$federation->get('IndexedModes')[intval($this->mode)];
        $juez1=$this->myDBObject->__getObject("Jueces",$this->manga->Juez1);
        $juez2=$this->myDBObject->__getObject("Jueces",$this->manga->Juez2);
        $j1=($juez1->Nombre==="-- Sin asignar --")?"":$juez1->Nombre;
        $j2=($juez2->Nombre==="-- Sin asignar --")?"":$juez2->Nombre;
        // dump excel rows
	    $row=array(_("Contest"),$this->prueba->Nombre);  $this->myWriter->addRow($row);
	    $row=array(_("Journey"),$this->jornada->Nombre,$this->jornada->Fecha); $this->myWriter->addRow($row);
	    $row=array(_("Round"),Mangas::$tipo_manga[$this->manga->Tipo][1],$modestr); $this->myWriter->addRow($row);
	    $row=array(_("Judges"),$j1,$j2); $this->myWriter->addRow($row);
        $row=array(_('Dist'),$this->resultados['trs']['dist']." mts"); $this->myWriter->addRow($row);
        $row=array(_('Obst'),$this->resultados['trs']['obst']." mts"); $this->myWriter->addRow($row);
        $row=array(_("SCT"),$this->resultados['trs']['trs']." mts"); $this->myWriter->addRow($row);
        $row=array(_("MCT"),$this->resultados['trs']['trm']." mts"); $this->myWriter->addRow($row);
        $row=array(_("Spd"),$this->resultados['trs']['vel']." m/s"); $this->myWriter->addRow($row);
        $this->myWriter->addRow(array());
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
		// Set page name
        $dogspage=$this->myWriter->getCurrentSheet();
        $name=_utf("Results");
        $dogspage->setName($this->normalizeSheetName($name));

		// write Round Information
        $this->writeCourseData();

		// write column names
		$this->writeTableHeader();
		// and dump results
		foreach($this->resultados['rows'] as $row) {
		    // preformat specific fields
            $row['Puesto']= ($row['Penalizacion']>=200)? "-":"{$row['Puesto']}";
            $row['Velocidad']= ($row['Penalizacion']>=200)?"-":number_format($row['Velocidad'],2);
            $row['Tiempo']= ($row['Penalizacion']>=200)?"-":number_format($row['Tiempo'],$this->timeResolution);
            $row['Penalizacion']=number_format($row['Penalizacion'],$this->timeResolution);
            $row['Faltas']=$row['Faltas']+$row['Tocados'];
            // extract relevant information from database received dog
			$line=array();
			for($n=0;$n<count($this->fields);$n++) array_push($line,$row[$this->fields[$n]]);
			$this->myWriter->addRow($line); // add row to excel table
		}
		$this->myLogger->leave();
	}
}

// Consultamos la base de datos
try {
    $idprueba=http_request("Prueba","i",0);
    $idjornada=http_request("Jornada","i",0);
    $idmanga=http_request("Manga","i",0);
    $mode=http_request("Mode","i",0);

    $mngobj= new Mangas("printResultadosByManga",$idjornada);
    $manga=$mngobj->selectByID($idmanga);
    $resobj= new Resultados("printResultadosByManga",$idprueba,$idmanga);
    $resultados=$resobj->getResultados($mode); // throw exception if pending dogs

    // Creamos generador de documento
    $excel = new Excel_PartialScores($idprueba,$idjornada,$manga,$resultados,$mode);
    $excel->open();
    $excel->composeTable();
    $excel->close();
    return 0;
} catch (Exception $e) {
    die($e->getMessage());
}
?>