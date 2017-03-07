<?php
/*
print_etiquetas_csv.php

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
 * genera un CSV con los datos para las etiquetas
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

class PrintEtiquetasCSV  {
	
	public $myLogger;
	protected $prueba;
	protected $club;
	protected $jornada;
	protected $manga1;
	protected $manga2;
	protected $resultados;
	protected $icon;
	protected $config;
	protected $federation;
	
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
        $this->federation=Federations::getFederation( intval($this->prueba->RSCE) );
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
		$line .= _(Mangas::getTipoManga($this->manga1->Tipo,3,$this->federation)).":";
		$line .= $row['P1'].":";
		$line .= $row['C1'].":";
		$line .= $row['Puesto1'].":";
		$line .= _(Mangas::getTipoManga($this->manga2->Tipo,3,$this->federation)).":";
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
?>
