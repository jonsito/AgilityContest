<?php
/*
print_listaPerros.php

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
 * genera fichero excel con los datos, inscripciones y resultados de una prueba
*/

require_once(__DIR__ . "/../../tools.php");
require_once(__DIR__ . "/../../logging.php");
require_once(__DIR__ . '/../../database/classes/Dogs.php');
require_once(__DIR__ . '/../../database/classes/Equipos.php');
require_once(__DIR__ . '/../../database/classes/Pruebas.php');
require_once(__DIR__ . '/../../database/classes/Jornadas.php');
require_once(__DIR__ . '/../../database/classes/Inscripciones.php');
require_once(__DIR__ . '/../../database/classes/Mangas.php');
require_once(__DIR__ . '/../../database/classes/Jueces.php');
require_once(__DIR__ . '/../../database/classes/Resultados.php');
require_once(__DIR__ . "/../common_writer.php");

class Excel_Clasificaciones extends XLSX_Writer {

    protected $federation;
	protected $jornadas=array(); // lista de jornadas de la prueba
    protected $jdbObject; // gestion de jueces

    protected $cols = array(
		'Dorsal',
		'Name','LongName','Gender','Breed','Chip','License','KC id','Category','Grade','Handler','Club','Country', // datos del perro
		'Team','Heat','Comments', // datos de la inscripcion en la jornada
		'F1','R1','E1','N1','Tiempo1','Penal1', // datos de la manga 1
		'F2','R2','E2','N2','Tiempo2','Penal2', // datos de la manga 2
		// TODO: handle series with more than 2 rounds
		'Time','Penalizacion','Calification'
	);

	/**
	 * Constructor
	 * @param {int} $prueba Prueba ID
	 * @throws Exception
	 */
	function __construct($prueba) {
		parent::__construct("clasifications.xlsx");
		setcookie('fileDownload','true',time()+30,"/"); // tell browser to hide "downloading" message box
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

    /**
     * Pagina de datos de las mangas de cada jornada
     */
    function createJornadaInfoPage($jornada) {
        $jdatapage=$this->myWriter->addNewSheetAndMakeItCurrent();
        $name=$this->normalizeSheetName("Info ".$jornada['Nombre']);
        $jdatapage->setName($name);
        $hdr=array(_('Type'),_('Round'),_('Dist'),_('Obst'),_('SCT'),_('MCT'),_('Vel'),_('Judge')." 1",_('Judge')." 2");
        $this->myWriter->addRowWithStyle($hdr,$this->rowHeaderStyle);
        // por cada manga buscamos el nombre, tipo y datos de trs/trm
        // obtenemos la lista de mangas de la jornada
        $res=Jornadas::enumerateMangasByJornada($jornada['ID'])['rows'];
        foreach($res as $manga) {
            $results=Competitions::getResultadosInstance("excel_Clasificicaciones",$manga['Manga']);
            $row=array();
			array_push($row,$manga['TipoManga']); // tipo de manga
			array_push($row,$manga['Nombre']); // nombre de la manga
            $resultados=$results->getResultadosIndividual($manga['Mode']);
            $trs=$resultados['trs'];
            array_push($row,$trs['dist']);
            array_push($row,$trs['obst']);
            array_push($row,$trs['trs']);
            array_push($row,$trs['trm']);
			$vel=str_replace("&asymp;",'± ',$trs['vel']);
            array_push($row,$vel);
            array_push($row,$this->jdbObject->selectByID($manga['Juez1'])['Nombre']);
            array_push($row,$this->jdbObject->selectByID($manga['Juez2'])['Nombre']);
            $this->myWriter->addRow($row);
        }
    }

    /**
     * Pagina de resultados de la jornada ordenados por grado/categoría/puesto
	 * @param {array} $jornada datos de la jornada
	 * @param {object} $insc objeto de tipo Inscripcion con las inscripciones de la prueba
     */
    function createJornadaDataPage($jornada,$insc) {
		// create Excel sheet
        $jdatapage=$this->myWriter->addNewSheetAndMakeItCurrent();
        $name=$this->normalizeSheetName("Data ".$jornada['Nombre']);
        $jdatapage->setName($name);
        // write table header
        $this->writeTableHeader();
		$rondas=Jornadas::enumerateRondasByJornada($jornada['ID'])['rows'];

		// obtenemos los datos "personales" de los perros de la jornada
		$lista=$insc->inscritosByJornada($jornada['ID'],false)['rows'];
		$eq=new Equipos("excel_printInscripciones",$this->prueba['ID'],$jornada['ID']);
		$inscritos=array();
		foreach($lista as $perro) {
			// add team information
			$perro['Equipo']=$eq->getTeamByPerro($perro['Perro'])['Nombre'];
			// reindexamos las inscripciones por el PerroID
			$inscritos[$perro['Perro']]=$perro;
		}

		// obtenemos todas las clasificaciones de la jornada
		$clas=Competitions::getClasificacionesInstance("excel_Clasificaciones",$jornada['ID']);
		$results=array();
		foreach ($rondas as $ronda) {
			$mangas=array($ronda['Manga1'],$ronda['Manga2'],$ronda['Manga3'],$ronda['Manga4'],$ronda['Manga5'],$ronda['Manga6'],$ronda['Manga7'],$ronda['Manga8']);
			$clasifRonda=$clas->clasificacionFinal($ronda['Rondas'],$mangas,$ronda['Mode']);
			$results=array_merge($results,$clasifRonda['rows']);
		}
		// OK ya tenemos los datos de toda la jornada; ahora a ordenar por grado,categoría y puesto
		usort($results,function($a,$b){
			$res=strcmp($a['Grado'],$b['Grado']); if ($res!=0) return $res;
			$res=strcmp($a['Categoria'],$b['Categoria']); if($res!=0) return $res;
			return ($a['Puesto']>$b['Puesto'])?1:-1;
		});

		// componemos la fila Excel anyiadiendo datos personales
		//
		// 'Dorsal',
		// 'Nombre','NombreLargo','Genero','Raza','Chip','Licencia','LOE_RRC','Categoria','Grado','NombreGuia','NombreClub','Pais', // datos del perro
		// 'Equipo','Celo','Observaciones', // datos de la inscripcion en la jornada
		// 'F1','R1','E1','N1','Tiempo1','Penal1', // datos de la manga 1
		// 'F2','R2','E2','N2','Tiempo2','Penal2', // datos de la manga 2
		//  // TODO: handle series with more than 2 rounds
		// 'Tiempo','Penalizacion','Calificacion'
		foreach($results as $perro) {
			$row=array();
			// si el perro no esta en la lista de inscritos, marca error e ignora entrada
			if (!array_key_exists($perro['Perro'],$inscritos)) {
				$this->myLogger->error("Encontrada Clasificacion para perro no inscrito:".$perro['Perro']);
				continue;
			}
			$pdata=&$inscritos[$perro['Perro']];
			$pdata['Done']=1; // mark perro inscrito _and_ with clasification
			// datos personales
			$row[]=$perro['Dorsal'];
			$row[]=$pdata['Nombre'];
			$row[]=$pdata['NombreLargo'];
			$row[]=$pdata['Genero'];
            $row[]=$pdata['Raza'];
            $row[]=$pdata['Chip'];
			$row[]=$pdata['Licencia'];
			$row[]=$pdata['LOE_RRC'];
			$row[]=$pdata['Categoria'];
			$row[]=$pdata['Grado'];
			$row[]=$pdata['NombreGuia'];
			$row[]=$pdata['NombreClub'];
			$row[]=$pdata['Pais'];
			// Datos de la inscripcion
			$row[]=$pdata['Equipo'];
			$row[]=$pdata['Celo'];
			$row[]=$pdata['Observaciones'];
			// resultados manga 1
			$row[]=$perro['F1']; // Manga 1: faltas + tocados
			$row[]=$perro['R1']; // Manga 1: rehuses
			$row[]=$perro['E1']; // Manga 1: eliminado
			$row[]=$perro['N1']; // manga 1: no presentado
			$row[]=$perro['T1']; // manga 1: tiempo
			$row[]=$perro['P1']; // manga 1: penalizacion
			// resultados manga 2
            $flag=false;
            if (array_key_exists('F2',$perro)) $flag=true;
			$row[]=($flag)? $perro['F2']:'-'; // Manga 2: faltas + tocados
			$row[]=($flag)? $perro['R2']:'-'; // Manga 2: rehuses
			$row[]=($flag)? $perro['E2']:'-'; // Manga 2: eliminado
			$row[]=($flag)? $perro['N2']:'-'; // manga 2: no presentado
			$row[]=($flag)? $perro['T2']:'-'; // manga 2: tiempo
			$row[]=($flag)? $perro['P2']:'-'; // manga 2: penalizacion
			// datos globales de clasificacion
			$row[]=$perro['Tiempo'];
			$row[]=$perro['Penalizacion'];
			$row[]=$perro['Calificacion'];

			// !!finaly!! add perro to excel table
			$this->myWriter->addRow($row);
		}
        // por ultimo metemos las inscripciones que no tienen resultado asociado
        foreach($inscritos as $pdata) {
            if(array_key_exists('Done',$pdata)) continue; // already done
            $row=array();
            // datos personales
            $row[]=$pdata['Dorsal'];
            $row[]=$pdata['Nombre'];
            $row[]=$pdata['NombreLargo'];
            $row[]=$pdata['Genero'];
            $row[]=$pdata['Raza'];
            $row[]=$pdata['Chip'];
            $row[]=$pdata['Licencia'];
            $row[]=$pdata['LOE_RRC'];
            $row[]=$pdata['Categoria'];
            $row[]=$pdata['Grado'];
            $row[]=$pdata['NombreGuia'];
            $row[]=$pdata['NombreClub'];
            $row[]=$pdata['Pais'];
            // add perro without results to excel table
            $this->myWriter->addRow($row);
        }
    }

	function composeTable() {
		$this->myLogger->enter();
        $this->createInfoPage(_utf('Scores'),$this->prueba['RSCE']);
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

?>