<?php
/*
print_clasificacion_excel.php

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



header('Set-Cookie: fileDownload=true; path=/');
// mandatory 'header' to be the first element to be echoed to stdout

/**
 * genera un fichero excel con los datos de la clasificacion de la ronda
 */

require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Clubes.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jueces.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/Resultados.php');
require_once(__DIR__.'/../database/classes/Clasificaciones.php');

class Excel {
	protected $myLogger;
	protected $club; // club organizador
	protected $prueba; // datos de la prueba
	protected $jornada; // datos de la jornada
	protected $manga1; // datos de la manga
	protected $manga2;
	protected $resultados;
	protected $trs1;
	protected $trs2;
	protected $categoria;

	 /** Constructor
	 * @param {obj} $manga datos de la manga
	 * @param {obj} $resultados resultados asociados a la manga/categoria pedidas
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$mangas,$resultados,$mode) {
		$this->myLogger= new Logger("PrintCommon");
		$dbobj=new DBObject("print_clasificacion_excel");
		$this->prueba=$dbobj->__getObject("Pruebas",$prueba);
		$this->club=$dbobj->__getObject("Clubes",$this->prueba->Club); // club organizador
		if ($jornada!=0) $this->jornada=$dbobj->__getObject("Jornadas",$jornada);
		else $this->jornada=null;
		$this->manga1=$dbobj->__getObject("Mangas",$mangas[0]);
		$this->manga2=$dbobj->__getObject("Mangas",$mangas[1]);
		$this->resultados=$resultados['rows'];
		$this->trs1=$resultados['trs1'];
		$this->trs2=$resultados['trs2'];
		$this->categoria = Mangas::$manga_modes[$mode][0];
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
	
	function write_datosMangas() {
		$jobj=new Jueces("print_Clasificaciones");
		$juez1=$jobj->selectByID($this->manga1->Juez1);
		$juez2=$jobj->selectByID($this->manga1->Juez2);
		$j1=$juez1['Nombre'];
		$j2=$juez2['Nombre'];
		$tm1=Mangas::$tipo_manga[$this->manga1->Tipo][3] . " - " . $this->categoria;
		$tm2=Mangas::$tipo_manga[$this->manga2->Tipo][3] . " - " . $this->categoria;
		$ronda=Mangas::$tipo_manga[$this->manga1->Tipo][4]; // la misma que la manga 2
		
		$this->xlsLabel(0,0,"Prueba");
			$this->xlsLabel(0,1,iconv( "UTF-8", "ISO-8859-1",$this->prueba->Nombre));
		$this->xlsLabel(1,0,"Jornada");
			$this->xlsLabel(1,1,iconv( "UTF-8", "ISO-8859-1",$this->jornada->Nombre));
		$this->xlsLabel(2,0,"Fecha");
			$this->xlsLabel(2,1,$this->jornada->Fecha);
		$this->xlsLabel(3,0,"Ronda");
			$this->xlsLabel(3,1, "$ronda - {$this->categoria}");
		$this->xlsLabel(4,0,"Juez 1");
			$this->xlsLabel(4,1,iconv( "UTF-8", "ISO-8859-1",($j1==="-- Sin asignar --")?"":$j1));
		$this->xlsLabel(5,0,"Juez 2");
			$this->xlsLabel(5,1,iconv( "UTF-8", "ISO-8859-1",($j2==="-- Sin asignar --")?"":$j2));
		$this->xlsLabel(6,0,"Manga 1");
			$trs=$this->trs1;
			$this->xlsLabel(6,1,$tm1);
			$this->xlsLabel(6,2,"Dist.: {$trs['dist']}m");
			$this->xlsLabel(6,3,"Obst.: {$trs['obst']}");
			$this->xlsLabel(6,4,"TRS: {$trs['trs']}s");
			$this->xlsLabel(6,5,"TRM: {$trs['trm']}s");
			$this->xlsLabel(6,6,"Vel.: {$trs['vel']}m/s");
		$this->xlsLabel(7,0,"Manga 2");
			$trs=$this->trs2;
			$this->xlsLabel(7,1,$tm2);
			$this->xlsLabel(7,2,"Dist.: {$trs['dist']}m");
			$this->xlsLabel(7,3,"Obst.: {$trs['obst']}");
			$this->xlsLabel(7,4,"TRS: {$trs['trs']}s");
			$this->xlsLabel(7,5,"TRM: {$trs['trm']}s");
			$this->xlsLabel(7,6,"Vel.: {$trs['vel']}m/s");
	}
	
	function write_TableHeader() {
		
		$base=10; // primera cabecera
		$tm1=Mangas::$tipo_manga[$this->manga1->Tipo][3];
		$tm2=Mangas::$tipo_manga[$this->manga2->Tipo][3];
		$this->xlsLabel($base,0,"Datos del participante");
		$this->xlsLabel($base,7,$tm1);
		$this->xlsLabel($base,13,$tm2);
		$this->xlsLabel($base,19,"Clasificacion");
		
		$base=11; // segunda cabecera
		$this->xlsLabel($base,0,"Dorsal");
		$this->xlsLabel($base,1,"Nombre");
		$this->xlsLabel($base,2,"Licencia");
		$this->xlsLabel($base,3,"Categoria");
		$this->xlsLabel($base,4,"Grado");
		$this->xlsLabel($base,5,"Guia");
		$this->xlsLabel($base,6,"Club");
		$this->xlsLabel($base,7,"Faltas");
		$this->xlsLabel($base,8,"Rehuses");
		$this->xlsLabel($base,9,"Tiempo");
		$this->xlsLabel($base,10,"Velocidad");
		$this->xlsLabel($base,11,"Penalizacion");
		$this->xlsLabel($base,12,"Calificacion");
		$this->xlsLabel($base,13,"Faltas");
		$this->xlsLabel($base,14,"Rehuses");
		$this->xlsLabel($base,15,"Tiempo");
		$this->xlsLabel($base,16,"Velocidad");
		$this->xlsLabel($base,17,"Penalizacion");
		$this->xlsLabel($base,18,"Calificacion");
		$this->xlsLabel($base,19,"Tiempo");
		$this->xlsLabel($base,20,"Penalizacion");
		$this->xlsLabel($base,21,"Calificacion");
		$this->xlsLabel($base,22,"Puesto");
	}
	
	function write_TableCell($idx,$row) {
		
		// fomateamos datos
		$puesto= ($row['Penalizacion']>=200)? "-":"{$row['Puesto']}";
		$penal=number_format($row['Penalizacion'],2);
		$v1= ($row['P1']>=200)?"-":number_format($row['V1'],1);
		$t1= ($row['P1']>=200)?"-":number_format($row['T1'],2);
		$p1=number_format($row['P1'],2);
		$v2= ($row['P2']>=200)?"-":number_format($row['V2'],1);
		$t2= ($row['P2']>=200)?"-":number_format($row['T2'],2);
		$p2=number_format($row['P2'],2);
		
		$base=$idx+12;

		$this->xlsNumber($base,0,$row['Dorsal']);
		$this->xlsLabel($base,1,iconv( "UTF-8", "ISO-8859-1",$row['Nombre']));
		$this->xlsLabel($base,2,$row['Licencia']);
		$this->xlsLabel($base,3,$row['Categoria']);
		$this->xlsLabel($base,4,$row['Grado']);
		$this->xlsLabel($base,5,iconv( "UTF-8", "ISO-8859-1",$row['NombreGuia']));
		$this->xlsLabel($base,6,iconv( "UTF-8", "ISO-8859-1",$row['NombreClub']));
		$this->xlsNumber($base,7,$row['F1']);
		$this->xlsNumber($base,8,$row['R1']);
		$this->xlsNumber($base,9,$t1);
		$this->xlsNumber($base,10,$v1);
		$this->xlsNumber($base,11,$p1);
		$this->xlsLabel($base,12,$row['C1']);
		$this->xlsNumber($base,13,$row['F2']);
		$this->xlsNumber($base,14,$row['R2']);
		$this->xlsNumber($base,15,$t2);
		$this->xlsNumber($base,16,$v2);
		$this->xlsNumber($base,17,$p2);
		$this->xlsLabel($base,18,$row['C2']);
		$this->xlsNumber($base,19,$t1+$t2);
		$this->xlsNumber($base,20,$penal);
		$this->xlsLabel($base,21,$row['Calificacion']);
		$this->xlsLabel($base,22,$puesto);
	}
	
	function composeTable() {
		$this->myLogger->enter();
		$rowcount=0;
		$this->write_datosMangas();
		$this->write_TableHeader();
		foreach($this->resultados as $row) {
			$this->write_TableCell($rowcount,$row);
			$rowcount++;
		}
		$this->myLogger->leave();
	}
}

try {
	$result=null;
	$mangas=array();
	$prueba=http_request("Prueba","i",0);
	$jornada=http_request("Jornada","i",0);
	$rondas=http_request("Rondas","i","0"); // bitfield of 512:Esp 256:KO 128:Eq4 64:Eq3 32:Opn 16:G3 8:G2 4:G1 2:Pre2 1:Pre1
	$mangas[0]=http_request("Manga1","i",0); // single manga
	$mangas[1]=http_request("Manga2","i",0); // mangas a dos vueltas
	$mangas[2]=http_request("Manga3","i",0);
	$mangas[3]=http_request("Manga4","i",0); // 1,2:GII 3,4:GIII
	$mangas[4]=http_request("Manga5","i",0);
	$mangas[5]=http_request("Manga6","i",0);
	$mangas[6]=http_request("Manga7","i",0);
	$mangas[7]=http_request("Manga8","i",0);
	$mangas[8]=http_request("Manga9","i",0); // mangas 3..9 are used in KO rondas
	$mode=http_request("Mode","i","0"); // 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small
	$c= new Clasificaciones("print_etiquetas_pdf",$prueba,$jornada);
	$result=$c->clasificacionFinal($rondas,$mangas,$mode);

	// Creamos generador de documento
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=clasificacion.xls");
	header("Content-Transfer-Encoding: binary ");
	$excel = new Excel($prueba,$jornada,$mangas,$result,$mode);
	$excel->xlsBOF();
	$excel->composeTable();
	$excel->xlsEOF();
} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}
?>