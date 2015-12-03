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
 * genera fichero excel con los datos, inscripciones y resultados de una prueba
*/

require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__.'/../database/classes/Dogs.php');
require_once(__DIR__.'/../database/classes/Equipos.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Inscripciones.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/Jueces.php');
require_once(__DIR__.'/../database/classes/Resultados.php');
require_once(__DIR__."/common_writer.php");

class Excel_Clasificaciones extends XLSX_Writer {

    protected $federation;
	protected $jornadas=array(); // lista de jornadas de la prueba
    protected $jdbObject; // gestion de jueces

    protected $cols = array(
		'Dorsal',
		'Name','Pedigree Name','Gender','Breed','License','KC id','Cat','Grad','Handler','Club','Country', // datos del perro
		'Team','Heat','Comments', // datos de la inscripcion en la jornada
		'F1','R1','T1','E1','N1','Tiempo1','Penal1', // datos de la manga 1
		'F2','R2','T2','E2','N2','Tiempo2','Penal2', // datos de la manga 2
		'F3','R3','T3','E3','N3','Tiempo3','Penal3', // datos de la manga 3
		'F4','R4','T4','E4','N4','Tiempo4','Penal4', // datos de la manga 4
		'F5','R5','T5','E5','N5','Tiempo5','Penal5', // datos de la manga 5
		'F6','R6','T6','E6','N6','Tiempo6','Penal6', // datos de la manga 6
		'F7','R7','T7','E7','N7','Tiempo7','Penal7', // datos de la manga 7
		'F8','R8','T8','E8','N8','Tiempo8','Penal8', // datos de la manga 8
		'Tiempo','Penalizacion'
	);
    protected $fields = array(
		'Dorsal',
		'Nombre','NombreLargo','Genero','Raza','Licencia','LOE_RRC','Categoria','Grado','NombreGuia','NombreClub','Pais', // datos del perro
		'Equipo','Celo','Observaciones', // datos de la inscripcion en la jornada
		'F1','R1','T1','E1','N1','Tiempo1','Penal1', // datos de la manga 1
		'F2','R2','T2','E2','N2','Tiempo2','Penal2', // datos de la manga 2
		'F3','R3','T3','E3','N3','Tiempo3','Penal3', // datos de la manga 3
		'F4','R4','T4','E4','N4','Tiempo4','Penal4', // datos de la manga 4
		'F5','R5','T5','E5','N5','Tiempo5','Penal5', // datos de la manga 5
		'F6','R6','T6','E6','N6','Tiempo6','Penal6', // datos de la manga 6
		'F7','R7','T7','E7','N7','Tiempo7','Penal7', // datos de la manga 7
		'F8','R8','T8','E8','N8','Tiempo8','Penal8', // datos de la manga 8
		'Tiempo','Penalizacion'
	);

	/**
	 * Constructor
	 * @param {int} $prueba Prueba ID
	 * @throws Exception
	 */
	function __construct($prueba) {
		parent::__construct("clasifications.xlsx");
        $p=new Pruebas("excel_Clasificaciones");
        $res=$p->selectByID($prueba);
        if (!is_array($res)){
			$this->errormsg="excel_Clasificaciones: getPruebaByID($prueba) failed";
			throw new Exception($this->errormsg);
		}
        $this->prueba=$res;
		$j=new Jornadas("excel_Clasificaciones",$prueba);
		$res=$j->selectByPrueba();
		if (!is_array($res)){
			$this->errormsg="excel_Clasificaciones: getJornadasByPrueba($prueba) failed";
			throw new Exception($this->errormsg);
		}
		$this->jornadas=$res['rows'];
        // to get mode in trs evaluation
        $this->federation=Federations::getFederation($this->prueba['RSCE']);
        $this->jdbObject=new Jueces("excelClasification");
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

    /**
     * Pagina de datos de las mangas de cada jornada
     */
    function createJornadaInfoPage($jornada) {
        $jdatapage=$this->myWriter->addNewSheetAndMakeItCurrent();
        $name=$this->normalizeSheetName("Info ".$jornada['Nombre']);
        $jdatapage->setName($name);
        $hdr=array(_('Round'),_('Dist'),_('Obst'),_('SCT'),_('MCT'),_('Vel'),_('Judge')." 1",_('Judge')." 2");
        $this->myWriter->addRowWithStyle($hdr,$this->rowHeaderStyle);
        // por cada manga buscamos el nombre, tipo y datos de trs/trm
        // obtenemos la lista de mangas de la jornada
        $res=Resultados::enumerateMangasByJornada($jornada['ID'])['rows'];
        foreach($res as $manga) {
            $res=new Resultados("excel_Clasificicaciones",$this->prueba['ID'],$manga['Manga']);
            $row=array();
            array_push($row,$manga['Nombre']); // nombre de la manga
            $resultados=$res->getResultados($manga['Mode']);
            $trs=$resultados['trs'];
            array_push($row,$trs['dist']);
            array_push($row,$trs['obst']);
            array_push($row,$trs['trs']);
            array_push($row,$trs['trm']);
            array_push($row,$trs['vel']);
            array_push($row,$this->jdbObject->selectByID($manga['Juez1'])['Nombre']);
            array_push($row,$this->jdbObject->selectByID($manga['Juez2'])['Nombre']);
            $this->myWriter->addRow($row);
        }
    }

    /**
     * Pagina de resultados de la jornada ordenados por grado/categoría/puesto
     */
    function createJornadaDataPage($jornada,$insc) {
        $jdatapage=$this->myWriter->addNewSheetAndMakeItCurrent();
        $name=$this->normalizeSheetName("Data ".$jornada['Nombre']);
        $jdatapage->setName($name);
        // write header
        $this->writeTableHeader();
        $res=$insc->inscritosByJornada($jornada['ID'],false);
        $lista=$res['rows'];
        $eq=new Equipos("excel_Clasificaciones",$this->prueba['ID'],$jornada['ID']);
        foreach($lista as $perro) {
            // add team information
            $perro['Equipo']=$eq->getTeamByPerro($perro['Perro'])['Nombre'];
            $row=array();
            // extract relevant information from database received dog
            for($n=0;$n<count($this->fields);$n++) {
                if (array_key_exists($this->fields[$n],$perro)) array_push($row,$perro[$this->fields[$n]]);
                else array_push($row,"");
            }
            $this->myWriter->addRow($row);
        }
    }

	function composeTable() {
		$this->myLogger->enter();
        $this->createInfoPage();
        $this->createPruebaInfoPage($this->prueba,$this->jornadas);
		$insc=new Inscripciones("excel_Clasificaciones",$this->prueba['ID']);
		// iterate on every valid journeys
		foreach ($this->jornadas as $jornada) {
			if ($jornada['Nombre']==='-- Sin asignar --') continue; // skip empty journeys
            // create info page for this journey
            $this->createJornadaInfoPage($jornada,$insc);
			// Create data page for this journey
            $this->createJornadaDataPage($jornada,$insc);
		}
		$this->myLogger->leave();
	}
}

// Consultamos la base de datos
try {
	// 	Creamos generador de documento
	$prueba=http_request("Prueba","i",-1);
	$excel = new Excel_Clasificaciones($prueba);
	$excel->open();
	$excel->composeTable();
	$excel->close();
    return 0;
} catch (Exception $e) {
	die ("Error accessing database: ".$e->getMessage());
}
?>