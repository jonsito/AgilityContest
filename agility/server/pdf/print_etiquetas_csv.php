<?php
/*
print_etiquetas_csv.php

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


header('Set-Cookie: fileDownload=true; path=/');
// mandatory 'header' to be the first element to be echoed to stdout

/**
 * genera un CSV con los datos para las etiquetas
 */

require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../auth/Config.php");
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Clubes.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jueces.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/Resultados.php');
require_once(__DIR__.'/../database/classes/Clasificaciones.php');

class CSV  {
	
	public $myLogger;
	protected $prueba;
	protected $club;
	protected $jornada;
	protected $manga1;
	protected $manga2;
	protected $resultados;
	protected $icon;
	protected $config;
	
	 /** Constructor
	 * @param {obj} $manga datos de la manga
	 * @param {obj} $resultados resultados asociados a la manga/categoria pedidas
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$mangas,$resultados) {
		$this->myLogger= new Logger("print_etiquetas_csv");
		$this->config=Config::getInstance();
		$dbobj=new DBObject("print_etiquetas_pdf");
		$this->prueba=$dbobj->__getObject("Pruebas",$prueba);
		$this->club=$dbobj->__getObject("Clubes",$this->prueba->Club);
		$this->jornada=$dbobj->__getObject("Jornadas",$jornada);
		$this->manga1=$dbobj->__getObject("Mangas",$mangas[0]);
		$this->manga2=$dbobj->__getObject("Mangas",$mangas[1]);
		$this->resultados=$resultados;
		// evaluage logo info
		$this->icon="rsce.png";
		if (isset($this->club)) $this->icon=$this->club->Logo;
	}
	
	// No tenemos cabecera: no cabe
	function writeHeader() {
		$str =_("Dorsal:Contest:Date:License:Name:Handler:Club:Category:Grade:");
		$str.=_("Round1:Penalization1:Calification1:Position1:");
		$str.=_("Round2:Penalization2:Calification2:Position2");
		$str.="\n";
		return $str;
	}
	
	function writeCell($row) {
		// $this->myLogger->trace(json_encode($row));
		$line  ="";
		$line .= $row['Dorsal'].":";
		$line .= $this->prueba->Nombre.":";
		$line .= $this->jornada->Fecha.":";
		$line .= $row['Licencia'].":";
		$line .= $row['Nombre'].":";
		$line .= $row['NombreGuia'].":";
		$line .= $row['NombreClub'].":";
		$line .= $row['Categoria'].":";
		$line .= $row['Grado'].":";
		$line .= Mangas::$tipo_manga[$this->manga1->Tipo][3].":";
		$line .= $row['P1'].":";
		$line .= $row['C1'].":";
		$line .= $row['Puesto1'].":";
		$line .= Mangas::$tipo_manga[$this->manga2->Tipo][3].":";
		$line .= $row['P2'].":";
		$line .= $row['C2'].":";
		$line .= $row['Puesto2'].":";
		$line .= "\n";
		return $line;
	}
	
	function composeTable($hdr) {
		$result="";
		if ($hdr) $result=$this->writeHeader();
		foreach($this->resultados as $row) {
			$result.=$this->writeCell($row);
		}
		return $result;
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

	header("Content-type: text/plain");
	header("Content-Disposition: attachment; filename=printEtiquetas.csv");	
	
	// buscamos los recorridos asociados a la mangas
	$dbobj=new DBObject("print_etiquetas_csv");
	$mng=$dbobj->__getObject("Mangas",$mangas[0]);
	$prb=$dbobj->__getObject("Pruebas",$prueba);
	$c= new Clasificaciones("print_etiquetas_csv",$prueba,$jornada);
	$result=array();
	$heights=intval(Federations::getFederation( intval($prb->RSCE) )->get('Heights'));
	switch($mng->Recorrido) {
		case 0: // recorridos separados large medium small
			$r=$c->clasificacionFinal($rondas,$mangas,0);
			$result[0]=$r['rows'];
			$r=$c->clasificacionFinal($rondas,$mangas,1);
			$result[1]=$r['rows'];
			$r=$c->clasificacionFinal($rondas,$mangas,2);
			$result[2]=$r['rows'];
			if ($heights!=3) {
				$r=$c->clasificacionFinal($rondas,$mangas,5);
				$result[5]=$r['rows'];
			}
			break;
		case 1: // large / medium+small
			if ($heights==3) {
				$r=$c->clasificacionFinal($rondas,$mangas,0);
				$result[0]=$r['rows'];
				$r=$c->clasificacionFinal($rondas,$mangas,3);
				$result[3]=$r['rows'];
			} else {
				$r=$c->clasificacionFinal($rondas,$mangas,6);
				$result[6]=$r['rows'];
				$r=$c->clasificacionFinal($rondas,$mangas,7);
				$result[7]=$r['rows'];
			}
			break;
		case 2: // recorrido conjunto large+medium+small
			if ($heights==3) {
				$r=$c->clasificacionFinal($rondas,$mangas,4);
				$result[4]=$r['rows'];
			} else {
				$r=$c->clasificacionFinal($rondas,$mangas,8);
				$result[8]=$r['rows'];
			}
			break;
	}
	$first=true;
	foreach ($result as $res) {
		$csv =new CSV($prueba,$jornada,$mangas,$res);
		echo $csv->composeTable($first);
		$first=false;
	}

} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}

?>
