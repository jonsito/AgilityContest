<?php
/*
excel_listaPerros.php

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

class PartialScoresWriter extends XLSX_Writer {

    protected $myDBObject;
	protected $manga;
	protected $resultados;
	protected $mode;
	protected $timeResolution;
    // initialize in constructor to avoid stupid windows php
    protected $cols = null;
    protected $fields = null;

    /**
     * PartialScoresWriter constructor.
     * @param {integer} $idprueba
     * @param {integer} $idjornada
     * @param {object} $manga
     * @param {array} $resultados
     * @param {integer} $mode
     */
	function __construct($idprueba,$idjornada,$manga,$resultados,$mode) {
        parent::__construct("partial_scores.xlsx");
        setcookie('fileDownload', 'true', time() + 30, "/"); // tell browser to hide "downloading" message box
        $this->myDBObject = new DBObject("partial_scores.xlsx");
        $this->prueba = $this->myDBObject->__getObject("pruebas", $idprueba);
        $this->federation=Federations::getFederation(intval($this->prueba->RSCE));
        $this->jornada = $this->myDBObject->__getObject("jornadas", $idjornada);
        $this->manga = $manga;
        $this->resultados = $resultados;
        $this->mode = $mode;
        $this->timeResolution = ($this->myConfig->getEnv('crono_milliseconds') == "0") ? 2 : 3;
        // populate cols and fields
        if (isMangaGames($manga->Tipo)) { // Snooker,Gambler
            $this->fields = array('Dorsal','Licencia', 'Categoria', 'Grado', 'Nombre', 'NombreLargo', 'Raza', 'NombreGuia', 'NombreClub', 'Faltas', 'Rehuses', 'Games', 'Eliminado', 'NoPresentado', 'Velocidad', 'Tiempo', 'Penalizacion', 'Calificacion', 'Puntos');
            $this->cols = array('Dorsal','License', 'Category', 'Grade', 'Name', 'LongName', 'Breed', 'Handler', 'Club', 'Faults', 'Refusals', 'Games', 'Eliminated', 'NotPresent','Speed', 'Time', 'Penalization', 'Calification', 'Points');
        } else if (isMangaEquipos($manga->Tipo)) { // teams
            $this->fields = array('Dorsal','Licencia', 'Categoria', 'Grado', 'Nombre', 'NombreLargo', 'Raza', 'NombreGuia', 'NombreEquipo','NombreClub', 'Faltas', 'Rehuses', 'Eliminado', 'NoPresentado', 'Velocidad', 'Tiempo', 'Penalizacion', 'Calificacion', 'Puntos', 'Estrellas');
            $this->cols = array('Dorsal','License', 'Category', 'Grade', 'Name', 'LongName', 'Breed', 'Handler', 'Team','Club', 'Faults', 'Refusals',  'Eliminated', 'NotPresent', 'Speed', 'Time', 'Penalization', 'Calification', 'Points', 'Stars');
        } else {
            $this->fields = array('Dorsal','Licencia', 'Categoria', 'Grado', 'Nombre', 'NombreLargo', 'Raza', 'NombreGuia', 'NombreClub', 'Faltas', 'Rehuses', 'Eliminado', 'NoPresentado', 'Velocidad', 'Tiempo', 'Penalizacion', 'Calificacion', 'Puntos', 'Estrellas','Extras');
            $this->cols = array('Dorsal','License', 'Category', 'Grade', 'Name', 'LongName', 'Breed', 'Handler', 'Club', 'Faults', 'Refusals',  'Eliminated', 'NotPresent', 'Speed', 'Time', 'Penalization', 'Calification', 'Points', 'Stars','Extras');
        }
    }

    // Cabecera de página
    function writeCourseData() {
        // evaluate needed data from parameters
        $modestr=$this->federation->get('IndexedModes')[intval($this->mode)];
        $juez1=$this->myDBObject->__getObject("jueces",$this->manga->Juez1);
        $juez2=$this->myDBObject->__getObject("jueces",$this->manga->Juez2);
        $j1=($juez1->Nombre==="-- Sin asignar --")?"":$juez1->Nombre;
        $j2=($juez2->Nombre==="-- Sin asignar --")?"":$juez2->Nombre;
        // dump excel rows
	    $row=array(_("Contest"),$this->prueba->Nombre);  $this->myWriter->addRow($row);
	    $row=array(_("Journey"),$this->jornada->Nombre,$this->jornada->Fecha); $this->myWriter->addRow($row);
	    $row=array(_("Round"),_(Mangas::getTipoManga($this->manga->Tipo,1,$this->federation)),$modestr); $this->myWriter->addRow($row);
	    $row=array(_("Judges"),$j1,$j2); $this->myWriter->addRow($row);
        $row=array(_('Dist'),$this->resultados['trs']['dist']." mts"); $this->myWriter->addRow($row);
        $row=array(_('Obst'),$this->resultados['trs']['obst']." obs"); $this->myWriter->addRow($row);
        $row=array(_("SCT"),$this->resultados['trs']['trs']." seg"); $this->myWriter->addRow($row);
        $row=array(_("MCT"),$this->resultados['trs']['trm']." seg"); $this->myWriter->addRow($row);
        $row=array(_("Spd"),$this->resultados['trs']['vel']." m/s"); $this->myWriter->addRow($row);
        $this->myWriter->addRow(array());
    }

	private function writeTableHeader() {
	    // translate header to i18n
	    for($n=0;$n<count($this->cols);$n++) $this->cols[$n]=_utf($this->cols[$n]);
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
            $row['Velocidad']= ($row['Penalizacion']>=200)?"-":number_format2($row['Velocidad'],2);
            $row['Tiempo']= ($row['Penalizacion']>=200)?"0":number_format2($row['Tiempo'],$this->timeResolution);
            $row['Penalizacion']=number_format2($row['Penalizacion'],$this->timeResolution);
            $row['Faltas']=$row['Faltas']+$row['Tocados'];
            // extract relevant information from database received dog
			$line=array();
            foreach ($this->fields as $item) {
                if ($item==='Categoria' ) {
                    array_push($line,$this->federation->getCategoryShort($row[$item]));
                } else {
                    array_push($line,$row[$item]);
                }
            }
			$this->myWriter->addRow($line); // add row to excel table
		}
		$this->myLogger->leave();
	}
}
?>