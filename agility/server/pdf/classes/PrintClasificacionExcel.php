<?php
/*
PrintClasificacionExcel.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
 * genera un fichero excel con los datos de la clasificacion de la ronda
 */

require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/../../logging.php");
require_once(__DIR__."/../../auth/Config.php");
require_once(__DIR__.'/../../database/classes/DBObject.php');
require_once(__DIR__.'/../../database/classes/Clubes.php');
require_once(__DIR__.'/../../database/classes/Pruebas.php');
require_once(__DIR__.'/../../database/classes/Jueces.php');
require_once(__DIR__.'/../../database/classes/Jornadas.php');
require_once(__DIR__.'/../../database/classes/Mangas.php');
require_once(__DIR__.'/../../database/classes/Resultados.php');
require_once(__DIR__.'/../../database/classes/Clasificaciones.php');

class PrintClasificacionExcel {
	protected $myLogger;
	protected $dbobj;
	public $prueba;
	protected $club;
	protected $jornada;
	public $manga1;
	public $manga2; // in RSCE excel must allways exists (no single round)
	protected $myConfig;
	protected $timeResolution; // number of decimal digits in chrono
    protected $federation;

	 /** Constructor
	 * @param {obj} $manga datos de la manga
	 * @param {obj} $resultados resultados asociados a la manga/categoria pedidas
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$mangas) {
		$this->myLogger	= new Logger("PrintClasificacionExcel");
		$this->dbobj	= new DBObject("print_clasificacion_excel");
		$this->prueba	= $this->dbobj->__getObject("pruebas",$prueba);
		$this->club		= $this->dbobj->__getObject("clubes",$this->prueba->Club); // club organizador
		$this->jornada	= $this->dbobj->__getObject("jornadas",$jornada);
        $this->manga1=($mangas[0]!=0)?$this->dbobj->__getObject("mangas",$mangas[0]):null;
        $this->manga2=($mangas[1]!=0)?$this->dbobj->__getObject("mangas",$mangas[1]):null;
        $this->manga3=($mangas[2]!=0)?$this->dbobj->__getObject("mangas",$mangas[2]):null;
		$this->myConfig = Config::getInstance();
		// evaluate number of decimals to show when printing timestamps
		$this->timeResolution=($this->myConfig->getEnv('crono_milliseconds')=="0")?2:3;
		$this->federation=Federations::getFederation( intval($this->prueba->RSCE) );
	}
	
	// This one makes the beginning of the xls file
	function xlsBOF() {
		echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
		return;
	}
	
	// This one makes the end of the xls file
	function xlsEOF() {
		echo pack("ss", 0x0A, 0x00);
		return;
	}

	// Function to write a Number (double) into Row, Col
	function xlsNumber($Row, $Col, $Value) {
		echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
		echo pack("d", $Value);
		return;
	}
	
	// this will write text in the cell you specify
	function xlsLabel($Row, $Col, $Value ) {
		$L = strlen($Value);
		echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
		echo $Value;
		return;
	}
	
	function write_pageHeader($prueba,$jornada,$mangas) {
		$ronda=_(Mangas::getTipoManga($this->manga1->Tipo,4,$this->federation)); // la misma que la manga 2
		// starts at 0
		$this->xlsLabel(0,0,_("Contest"));
		$this->xlsLabel(0,1,iconv( "UTF-8", "ISO-8859-1",$this->prueba->Nombre));
		$this->xlsLabel(1,0,_("Club"));
		$this->xlsLabel(1,1,iconv( "UTF-8", "ISO-8859-1",$this->club->Nombre));
		$this->xlsLabel(2,0,_("Journey"));
		$this->xlsLabel(2,1,iconv( "UTF-8", "ISO-8859-1",$this->jornada->Nombre));
		$this->xlsLabel(3,0,_("Date"));
		$this->xlsLabel(3,1,$this->jornada->Fecha);
		$this->xlsLabel(4,0,("Round"));
		$this->xlsLabel(4,1, iconv("UTF-8", "ISO-8859-1",$ronda));
		return 5;
	}
	
	function write_datosMangas($result,$row, $mode) {
		$jobj=new Jueces("print_Clasificaciones");
		$juez1=$jobj->selectByID($this->manga1->Juez1);
		if ($this->manga2!==null) $juez2=$jobj->selectByID($this->manga2->Juez1);
        else $juez2=$jobj->selectByID($this->manga1->Juez2);
		$j1=$juez1['Nombre'];
		$j2=$juez2['Nombre'];
		$categoria = Mangas::getMangaMode($mode,0,$this->federation);
		$tm1=_(Mangas::getTipoManga($this->manga1->Tipo,3,$this->federation)) . " - " . $categoria;
		$tm2=_(Mangas::getTipoManga($this->manga2->Tipo,3,$this->federation)) . " - " . $categoria;
		
		$this->xlsLabel($row,0,_("Judge")." 1");
			$this->xlsLabel($row,1,iconv( "UTF-8", "ISO-8859-1",($j1==="-- Sin asignar --")?"":$j1));
		$this->xlsLabel($row+1,0,_("Judge")." 2");
			$this->xlsLabel($row+1,1,iconv( "UTF-8", "ISO-8859-1",($j2==="-- Sin asignar --")?"":$j2));
		$this->xlsLabel($row+2,0,_("Round")." 1");
			$trs=$result['trs1'];
			$this->xlsLabel($row+2,1,$tm1);
			$this->xlsLabel($row+2,2,_("Dist").".: {$trs['dist']}m");
			$this->xlsLabel($row+2,3,_("Obst").".: {$trs['obst']}");
			$this->xlsLabel($row+2,4,_("SCT").": {$trs['trs']}s");
			$this->xlsLabel($row+2,5,_("MCT").": {$trs['trm']}s");
			$vel=str_replace("&asymp;",utf8_decode('± '),$trs['vel']);
			$this->xlsLabel($row+2,6,_("Vel").".: {$vel}m/s");
		$this->xlsLabel($row+3,0,_("Round")." 2");
			$trs=$result['trs2'];
			$this->xlsLabel($row+3,1,$tm2);
			$this->xlsLabel($row+3,2,_("Dist").".: {$trs['dist']}m");
			$this->xlsLabel($row+3,3,_("Obst").".: {$trs['obst']}");
			$this->xlsLabel($row+3,4,_("SCT").": {$trs['trs']}s");
			$this->xlsLabel($row+3,5,_("MCT").": {$trs['trm']}s");
			$vel=str_replace("&asymp;",utf8_decode('± '),$trs['vel']);
			$this->xlsLabel($row+3,6,_("Vel").".: {$vel}m/s");
		return $row+4;
	}
	
	function write_TableHeader($base) {
		
		// primera cabecera
		$tm1=_(Mangas::getTipoManga($this->manga1->Tipo,3,$this->federation));
		$tm2=_(Mangas::getTipoManga($this->manga2->Tipo,3,$this->federation));
		$this->xlsLabel($base,0,_("Competitor data"));
		$this->xlsLabel($base,7,$tm1);
		$this->xlsLabel($base,15,$tm2);
		$this->xlsLabel($base,23,_("Scores"));
		
		$base++; // segunda cabecera
		$this->xlsLabel($base,0,_("Dorsal"));
		$this->xlsLabel($base,1,_("Name"));
		$this->xlsLabel($base,2,_("License"));
		$this->xlsLabel($base,3,_("Category"));
		$this->xlsLabel($base,4,_("Grade"));
		$this->xlsLabel($base,5,_("Handler"));
		$this->xlsLabel($base,6,_("Club"));
		$this->xlsLabel($base,7,_("Faults"));
		$this->xlsLabel($base,8,_("Refusals"));
		$this->xlsLabel($base,9,_("Time"));
		$this->xlsLabel($base,10,_("Speed"));
		$this->xlsLabel($base,11,_("Penaliz"));
        $this->xlsLabel($base,12,_("Score"));
        $this->xlsLabel($base,13,_("Points")."_A");
        $this->xlsLabel($base,14,_("Stars")."_A");
		$this->xlsLabel($base,15,_("Faults"));
		$this->xlsLabel($base,16,_("Refusals"));
		$this->xlsLabel($base,17,_("Time"));
		$this->xlsLabel($base,18,_("Speed"));
		$this->xlsLabel($base,19,_("Penaliz"));
		$this->xlsLabel($base,20,_("Score"));
        $this->xlsLabel($base,21,_("Points")."_J");
        $this->xlsLabel($base,22,_("Stars")."_J");
		$this->xlsLabel($base,23,_("Time"));
		$this->xlsLabel($base,24,_("Penaliz"));
        $this->xlsLabel($base,25,_("Score"));
        $this->xlsLabel($base,26,_("Points"));
		$this->xlsLabel($base,27,_("Position"));
		return $base+1;
	}
	
	function write_TableCell($base,$row) {
		
		// fomateamos datos
		$puesto= ($row['Penalizacion']>=200)? "-":"{$row['Puesto']}";
		$penal=number_format2($row['Penalizacion'],$this->timeResolution);
		$v1= ($row['P1']>=200)?"-":number_format2($row['V1'],2);
		$t1= ($row['P1']>=200)?"-":number_format2($row['T1'],$this->timeResolution);
		$p1=number_format2($row['P1'],$this->timeResolution);
		$v2= ($row['P2']>=200)?"-":number_format2($row['V2'],2);
		$t2= ($row['P2']>=200)?"-":number_format2($row['T2'],$this->timeResolution);
		$p2=number_format2($row['P2'],$this->timeResolution);

		$this->xlsNumber($base,0,$row['Dorsal']);
		$this->xlsLabel($base,1,iconv( "UTF-8", "ISO-8859-1",$row['Nombre']));
		$this->xlsLabel($base,2,$row['Licencia']);
		$this->xlsLabel($base,3,$this->federation->getCategoryShort($row['Categoria']));
		$this->xlsLabel($base,4,$row['Grado']);
		$this->xlsLabel($base,5,iconv( "UTF-8", "ISO-8859-1",$row['NombreGuia']));
		$this->xlsLabel($base,6,iconv( "UTF-8", "ISO-8859-1",$row['NombreClub']));
		$this->xlsNumber($base,7,$row['F1']);
		$this->xlsNumber($base,8,$row['R1']);
		$this->xlsNumber($base,9,$t1);
		$this->xlsNumber($base,10,$v1);
		$this->xlsNumber($base,11,$p1);
        $this->xlsLabel($base,12,$row['C1']);
        $this->xlsLabel($base,13,$row['Pt1']);
        $this->xlsLabel($base,14,$row['St1']);
		$this->xlsNumber($base,15,$row['F2']);
		$this->xlsNumber($base,16,$row['R2']);
		$this->xlsNumber($base,17,$t2);
		$this->xlsNumber($base,18,$v2);
		$this->xlsNumber($base,19,$p2);
		$this->xlsLabel($base,20,$row['C2']);
        $this->xlsLabel($base,21,$row['Pt2']);
        $this->xlsLabel($base,22,$row['St2']);
		$this->xlsNumber($base,23,number_format2($row['Tiempo'],$this->timeResolution));
		$this->xlsNumber($base,24,$penal);
		$this->xlsLabel($base,25,$row['Calificacion']);
        $this->xlsLabel($base,26,$row['Puntos']);
        $this->xlsLabel($base,27,$puesto);
		return $base+1;
	}
	
	function composeTable($mangas,$result,$mode,$base) {
		$base= $this->write_datosMangas($result,$base,$mode);
		$base=$this->write_TableHeader( $base);
		foreach($result['rows'] as $item) {
			$base=$this->write_TableCell($base,$item);
		}
		return $base;
	}
}

?>