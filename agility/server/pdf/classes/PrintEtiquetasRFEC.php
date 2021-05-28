<?php
/*
PrintEtiquetasRFEC.php

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
 * genera un CSV con los datos para las etiquetas
 */

require_once(__DIR__."/../fpdf.php");
require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/../../logging.php");
require_once(__DIR__.'/../../auth/Config.php');
require_once(__DIR__.'/../../database/classes/DBObject.php');
require_once(__DIR__.'/../../database/classes/Clubes.php');
require_once(__DIR__.'/../../database/classes/Pruebas.php');
require_once(__DIR__.'/../../database/classes/Jueces.php');
require_once(__DIR__.'/../../database/classes/Jornadas.php');
require_once(__DIR__.'/../../database/classes/Mangas.php');
require_once(__DIR__.'/../../database/classes/Resultados.php');
require_once(__DIR__.'/../../database/classes/Clasificaciones.php');
require_once(__DIR__."/../print_common.php");

class PrintEtiquetasRFEC extends PrintCommon {
	
	protected $mangasObj;
	protected $juecesObj;
	protected $serialno;
	
	 /** Constructor
	 * @param {obj} $manga datos de la manga
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$mangas) {
		parent::__construct('Landscape',"print_etiquetas_rfec_PDF",$prueba,$jornada);
		$dbobj=new DBObject("print_etiquetas_rfec_pdf");
		$this->mangasObj= array();
		$this->juecesObj= array();
		for ($n=0;$n<8;$n++) {
			$this->mangasObj[$n]= null;
			$this->juecesObj[$n]= null;
			if ($mangas[$n]!=0) {
				$this->mangasObj[$n]= $dbobj->__getObject("mangas",$mangas[$n]);
				$this->juecesObj[$n]= $dbobj->__selectAsArray("*",'jueces',"ID={$this->mangasObj[$n]->Juez1}");
			}
		}
        // add version date and license serial to every label
        $ser= substr( $this->regInfo['Serial'],4,4);
        $ver= substr( $this->config->getEnv("version_date"),2,6) ;
        $this->serialno="{$ver}-${ser}";
	}
	
	// No tenemos cabecera: no cabe
	function Header() {// pintamos una linea	
		$top=$this->config->getEnv('pdf_topmargin');
		$left=$this->config->getEnv('pdf_leftmargin');
		$this->Line($left,$top,$left+190,$top);
	}
	
	// Pie de página: tampoco cabe
	function Footer() {
	}

	/**
	 * @param {integer} $idx numero de pegatina
	 * @param {array} $row datos a imprimir
	 * @param {integer} $mng numero de manga dentro de los datos
	 */
	function writeCell($idx,$row,$mng=0) {
		$top=$this->config->getEnv('pdf_topmargin');
		$left=$this->config->getEnv('pdf_leftmargin');
		$height=$this->config->getEnv('pdf_labelheight');
		$grado=Mangas::getTipoManga($this->mangasObj[$mng]->Tipo,2,$this->federation);

		$basey=10+10*($idx%19);
		$basex=10+140*(($idx%38)>=19); // 10:col1 150:col2
		$this->ac_SetFillColor('0xffffff');

		$this->SetFont($this->getFontName(),'B',8.5); // font size for results data

		// caja auxiliar: Dorsal/Perro/Club (15mmts)
		$this->SetXY($basex,$basey);
		$this->Cell(15,3,$row['Dorsal'],'',0,'C',false);
		$this->SetXY($basex,$basey+5);
		$this->Cell(15,3,$row['Nombre'],'',0,'C',false);
		$this->SetXY($basex,$basey+5);
		$this->Cell(15,3,$row['NombreClub'],'',0,'C',false);

		// Primera caja: Fecha (20mmts)
		$this->SetXY($basex+15,$basey);
		$this->Cell(20,9,$this->jornada->Fecha,'LTB',0,'C',false);

		// segunda caja superior: Nombre y firma del juez (80mmts)
		$this->SetXY($basex+15+20,$basey);
		$this->Cell(80,4.5,"Juez: ".$this->juecesObj[$mng]['Nombre'],'LT',0,'C',false);
		// segunda caja inferior(1): Nombre del club,
		$this->SetXY($basex+15+20,$basey+4.5); // (40mmts+40mmts)
		$this->Cell(40,4.5,"Club: ".$this->club->Nombre,'LT',0,'C',false);

		// segunda caja inferior(2) manga grado categoria
		// ¿Agility o Jumping?
		if ( ($row['Grado']=='GI') && ($this->mangasObj[$mng]->Observaciones!=="") ) {
			$tipo=$this->mangasObj[$mng]->Observaciones;
		} else {
			$tipo = _(Mangas::getTipoManga($this->mangasObj[$mng]->Tipo, 3, $this->federation));
		}
		$str="{$tipo} {$grado} {$row['Categoria']}";
		$this->Cell(40,4.5,$str,'LBT',0,'C',false);

		// tercera caja: Calificacion/Velocidad (20mmts)
		$v = (is_numeric($row["V".($mng+1)])) ? number_format2($row["V".($mng+1)], 2) . "m/s": "-";
		$c= $row["C".($mng+1)];
		$this->SetXY($basex+15+20,$basey);
		$this->Cell(20,4.5,$c,'LTR',0,'C',false);
		$this->SetXY($basex+15+20,$basey+4.5);
		$this->Cell(20,4.5,$v,'LTBR',0,'C',false);
	}

	/**
	 * set up round data for CNEAC sheets. Has no use in RSCE nor RFEC labels
	 *@param {object} $r Clasification instance object
	*/
	function setRoundData($r) { /*empty, just for compatibility */ }

	/*
	 las pegatinas de RFEC no tienen "estandard" de etiqueta: son tiras
	de 120x9 mmts que contienen un único resultado (agility o jumping )
	Tenemos que usar una hoja de pegatinas sin troquelar, y para ahorrar
	pegatina, usaremos formato apaisado con dos columnas de 19 pegatinas
	en cada columna

	en RFEC solo se tienen en cuenta los excelentes, por consiguiente,
	a partir de las clasificaciones, el procedimiento es:
	- coger todas las mangas de cada competidor
	- Si la manga no es excelente, se ignora
	- Se imprime de arriba a abajo, pasando a la siguiente columna cada
	19 resultados
	 */


	/**
	 * @param $resultados clasificaciones de la manga (o globales)
	 * @param int $rowcount etiqueta inicial
	 * @param string $listadorsales lista de dorsales a imprimir
	 * @param int $discriminate discriminar por país o no
	 * @return int|mixed
	 */
	function composeTable($resultados,$rowcount=0,$listadorsales="",$discriminate=1) {
		$this->myLogger->enter();
		$this->SetFillColor(224,235,255); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont($this->getFontName(),'',8); // default font
		$lc=$this->config->getEnv('pdf_linecolor');
		$labels=38; // 19 + 19 labels per sheet
		$this->ac_SetDrawColor($lc);
		$this->SetLineWidth(.3);
		
		$this->SetMargins(10,$this->config->getEnv('pdf_topmargin'),10); // left top right
		$this->SetAutoPageBreak(true,10);

		if ($listadorsales!=="") {
			$granero=join(",",expand_range($listadorsales));
			$pajar=",{$granero},";
		}

		foreach($resultados as $row) {
			$nmangas=0;

			if ($listadorsales!=="") {
				$aguja=",{$row['Dorsal']},";
				if (strpos($pajar,$aguja)===FALSE) continue; // Dorsal not in list
				// do not handle discrimination on user-defined list
			} else {
				// if country discrimination is active check country and reject on no match
				if ( ($discriminate==1) && $row['Pais']!=="ESP") continue;
			}

			// iteramos sobre las tres mangas
			for ($n=1;$n<4;$n++) {
				if (!array_key_exists('P'.$n,$row)) continue;
				if ( $row['P'.$n]>=6.0 ) continue; // print only excelents
				if ( (($rowcount%38)==0) && ($rowcount!=0)) $this->AddPage(); // 19+19 etiquetas por pagina
				$this->writeCell($rowcount,$row,$n-1);
				$rowcount++;
			}
		}
		$this->myLogger->leave();
		return $rowcount;
	}
}
?>
