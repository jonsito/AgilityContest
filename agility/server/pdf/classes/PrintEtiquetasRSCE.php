<?php
/*
print_etiquetas_pdf.php

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

class PrintEtiquetasRSCE extends PrintCommon {
	
	protected $mangasObj;
	protected $juecesObj;
	protected $serialno;
	
	 /** Constructor
	 * @param {obj} $manga datos de la manga
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$mangas) {
		parent::__construct('Portrait',"print_etiquetasPDF",$prueba,$jornada);
		$dbobj=new DBObject("print_etiquetas_pdf");
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
		$height=$this->config->getEnv('pdf_labelheight'); // 17 or 20 mmts ==> 16 or 13 labels/sheet
		$grado=$row['Grado'];
		if ( ($this->mangasObj[$mng] != null) && isMangaOpen($this->mangasObj[$mng]->Tipo)) $grado=_('Open');

		$mntop=($this->mangasObj[$mng] != null)?true:false;
		$mnbottom=($this->mangasObj[$mng+1] != null)?true:false;
		$this->ac_SetFillColor('0xffffff');
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		//dorsal (10,y,20,17)
		$y0=  $top + $height * $idx + 0;
		$y1=  $top + $height * $idx + 1;
		$y2=  $top + $height * $idx + 2;
		$y3=  $top + $height * $idx + 3;
		$y4=  $top + $height * $idx + 4;
		$y5=  $top + $height * $idx + 5;
		$y6=  $top + $height * $idx + 6;
		$y7=  $top + $height * $idx + 7;
		$y8=  $top + $height * $idx + 8;
        $y9=  $top + $height * $idx + 9;
		$y10= $top + $height * $idx + 10;
		$y11= $top + $height * $idx + 11;
        $y12=  $top+ $height * $idx + 12;
		$y13=  $top+ $height * $idx + 13;
		$y14=  $top+ $height * $idx + 14;
		$y15=  $top+ $height * $idx + 15;
		$y16=  $top+ $height * $idx + 16;
		$y17=  $top+ $height * $idx + 17;
		$ynext=$top+ $height * ($idx+1);
		
		$this->SetFont($this->getFontName(),'B',24); // bold 11px
		$this->setXY($left,$y1);
        $this->Cell(20,7,$row['Dorsal'],'L',0,'C',false);
        // en el margen izquierdo de las etiquetas
        // ponemos info de perro guia y club
        $this->SetFont($this->getFontName(),'BI',7); // font size for results data
        $this->SetXY($left,$y6+0.5);
        $this->Cell(23,4,$row['Nombre'],'L',0,'L',false);
        $this->SetXY($left,$y9);
        $this->Cell(23,4,$this->getHandlerName($row),'L',0,'L',false);
        $this->SetXY($left,$y12);
        $this->Cell(23,4,$row['NombreClub'],'L',0,'L',false);

        $this->SetFont($this->getFontName(),'I',8); // font for prueba,name
		// caja izquierda (35,y,35,15)
		$this->SetXY($left+22,$y1); // margins are 10mm each
		$this->Cell(55,15,'','L',0,'L',false);
		
		//logo   (30,y,15,15)
		// los logos tienen 150x150, que a 300 dpi salen aprox a 2.54 cmts
		$this->SetXY($left+22,$y2); // margins are 10mm each
		$this->Image($this->icon,$this->getX(),$this->getY(),15);
		
		//Nombre del club (45,y,38,5) left
		$this->SetXY($left+36,$y2);
		$this->Cell(38,5,$this->club->Nombre,0,0,'L',false);
		//Nombre de la prueba (45,y,38,5) left
		$this->SetXY($left+36,$y5);
		$this->Cell(38,5,$this->prueba->Nombre,0,0,'L',false);
		//Fecha (45,y+5,38,5) left
		$this->SetXY($left+36,$y8);
		$this->Cell(38,5,$this->jornada->Fecha,0,0,'L',false);
        // licencia
        $this->SetFont($this->getFontName(),'',5); // font for licencia
        $this->SetXY($left+36,$y11);
        $this->Cell(38,5,$this->serialno,0,0,'L',false);
        $this->SetFont($this->getFontName(),'I',8); // font for prueba,name

		//Perro (45,y+10,38,7) right
		$this->SetXY($left+36,$y9);
		$this->Cell(38,7,"{$row['Licencia']} - {$row['Nombre']}",0,0,'R',false);


		//Manga1Tipo(85,y,20,8) center
		$this->SetFont($this->getFontName(),'IB',8.5); // font for prueba,name
		if (!$mntop) {
			$tipo=" ------- ";
		} else if ( ($row['Grado']=='GI') && ($this->mangasObj[$mng]->Observaciones!=="") ) {
			$tipo=$this->mangasObj[$mng]->Observaciones;
		} else {
			$tipo = _(Mangas::getTipoManga($this->mangasObj[$mng]->Tipo, 3, $this->federation));
		}
		$this->SetXY($left+75,$y1);
		$this->Cell(20,8,$tipo,'LB',0,'L',false);
		//Manga2Tipo(85,y+8,20,9) center
		if (!$mnbottom) {
			$tipo=" ------- ";
		} else if ( ($row['Grado']=='GI') && ($this->mangasObj[$mng+1]->Observaciones!=="") ) {
			$tipo=$this->mangasObj[$mng+1]->Observaciones;
		} else {
			$tipo= _(Mangas::getTipoManga($this->mangasObj[$mng+1]->Tipo,3,$this->federation));
		}
		$this->SetXY($left+75,$y7);
		$this->Cell(20,9,$tipo,'L',0,'L',false);

		$this->SetFont($this->getFontName(),'',12); // font size for results data
		//Cat (105,y,12,8) center
		$this->SetXY($left+95,$y1);
		$this->Cell(12,8,$row['Categoria'],'L',0,'C',false);
		//Grado (105,y+8,12,9) center
		$this->SetXY($left+95,$y7);
		$this->Cell(12,9,$grado,'L',0,'C',false);
		//Penal1 (117,y,17,8) right
		$this->SetXY($left+107,$y1);
		$this->Cell(17,8,($mntop)?$row["P".($mng+1)]:"---",'LB',0,'C',false);
		//Penal2 (117,y+8,17,9) right
		$this->SetXY($left+107,$y7);
		$this->Cell(17,9,($mnbottom)?$row["P".($mng+2)]:"---",'L',0,'C',false);

        $this->SetFont($this->getFontName(),'',8.5); // font size for results data


		//Calif1 (134,y,25,8) right
		$this->SetXY($left+124,$y2);
		$v= "-"; $c="";
		if ($mntop) {
			$v = ($row["V".($mng+1)] == 0) ? "" : number_format2($row["V" . ($mng + 1)], 2) . "m/s - ";
			$c= $row["C".($mng+1)];
		}
		$this->Cell(24,7,$v.$c,'LB',0,'C',false);

		//Puesto1 (159,y,15,8) center
		$this->SetFont($this->getFontName(),'B',20); // font size for results data
		$this->ac_Cell($left+148.5,$y2,13,7,"/",'','C',false);
		$this->SetFont($this->getFontName(),'',10); // font size for results data
		$this->ac_Cell($left+148,$y2,13,7,"",'LBR','C',false);
		$this->ac_Cell($left+148,$y2,13,7,($mntop)?"{$row["Puesto".($mng+1)]}º":"-",'','L',false);
		$this->ac_Cell($left+148,$y3,13,7,($mntop)?"{$row['Participantes']}":"-",'','R',false);

		// en el margen izquierdo de las etiquetas
		// ponemos el juez de la manga
		$this->SetFont($this->getFontName(),'I',8); // font size for results data
		$this->ac_Cell($left+161,$y2,29,7,($mntop)?$this->juecesObj[$mng]['Nombre']:"",'B','L',false);
		$this->SetFont($this->getFontName(),'I',6); // font size for results data
		$juez=($mntop)?"Juez: ".$this->juecesObj[$mng]['Nombre']:"";
		$this->ac_Cell(0.5+ $left+75,0.5+$y6,2+strlen($juez),2.4,$juez,'','L',true);

		//Calif2 (134,y+8,25,9) right
		$this->SetFont($this->getFontName(),'',8.5); // font size for results data
		$this->SetXY($left+124,$y8);
		$v= "-"; $c="";
		if ($mnbottom) {
			$v = ($row["V".($mng + 2)] == 0) ? "" : number_format2($row["V".($mng+2)], 2) . "m/s - ";
			$c= $row["C".($mng+2)];
		}
		$this->Cell(24,8,$v.$c,'L',0,'C',false);

		//Puesto2 (159,y+8,15,9) center
		$this->SetFont($this->getFontName(),'B',20); // font size for results data
		$this->ac_Cell($left+148.5,$y8,13,8,"/",'','C',false);
		$this->SetFont($this->getFontName(),'',10); // font size for results data
		$this->ac_Cell($left+148,$y8,13,8,"",'LR','C',false);
		$this->ac_Cell($left+148,$y8,13,8,($mnbottom)?"{$row["Puesto".($mng+2)]}º":"",'','L',false);
		$this->ac_Cell($left+148,$y9,13,8,($mnbottom)?"{$row['Participantes']}":"",'','R',false);
		// en el margen izquierdo de las etiquetas
		// ponemos el juez de la manga
		$this->SetFont($this->getFontName(),'I',8); // font size for results data
		$this->ac_Cell($left+161,$y8,29,7,($mnbottom)?$this->juecesObj[1+$mng]['Nombre']:"",'','L',false);
		$this->SetFont($this->getFontName(),'I',6); // font size for results data
		$juez=($mnbottom)?"Juez: ".$this->juecesObj[1+$mng]['Nombre']:"";
		$this->ac_Cell(0.5+$left+75,$y13,2+strlen($juez),2.4,$juez,'','L',true);

		// si 13 etiquetas/pagina, linea al final de la celda
		if ($height==20) $this->Line($left,$y17,$left+190,$y17);
		// linea al principio de celda siguiente
		$this->Line($left,$ynext,$left+190,$ynext);
	}

	/**
	 * set up round data for CNEAC sheets. Has no use in RSCE labels
	 *@param {object} $r Clasification instance object
	*/
	function setRoundData($r) { /*empty, just for compatibility */ }

	function composeTable($resultados,$rowcount=0,$listadorsales="",$discriminate=1) {
		$this->myLogger->enter();
		$this->SetFillColor(224,235,255); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont($this->getFontName(),'',8); // default font
		$lc=$this->config->getEnv('pdf_linecolor');
		$labels=($this->config->getEnv('pdf_labelheight')==17)?16:13;
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
			if ($this->mangasObj[0]!==null) $nmangas++;
			if ($this->mangasObj[1]!==null) $nmangas++;
			if ($this->mangasObj[2]!==null) $nmangas++;

			if ($listadorsales!=="") {
				$aguja=",{$row['Dorsal']},";
				if (strpos($pajar,$aguja)===FALSE) continue; // Dorsal not in list
				// do not handle discrimination on user-defined list
			} else {
				// if country discrimination is active check country and reject on no match
				if ( ($discriminate==1) && $row['Pais']!=="ESP") continue;
			}

			// ver si la manga tiene 1, 2 o 3 rondas
			$skip=intval($this->config->getEnv('pdf_skipnpel'));
			switch ($nmangas) {
				case 1:
					// skip if not present
					if ($row['P1']>=200.0) continue 2;
					// skip on eliminated and set to skip by operator
					if ( ($skip===2) && ($row['P1']>=6.0)) continue 2;
					if ( ($skip===1) && ($row['P1']>=100.0)) continue 2;
					break;
				case 2:
					if ( ($row['P1']>=200.0) && ($row['P2']>=200.0) ) continue 2;
					if ( ($skip===2) && ($row['P1']>=6.0) && ($row['P2']>=6.0) ) continue 2;
					if ( ($skip===1) && ($row['P1']>=100.0) && ($row['P2']>=100.0) ) continue 2;
					break;
				case 3:
					if ( ($row['P1']>=200.0) && ($row['P2']>=200.0) && ($row['P3']>=200.0) ) continue 2;
					if ( ($skip===2) && ($row['P1']>=100.0) && ($row['P2']>=100.0) && ($row['P3']>=100.0) ) continue 2;
					if ( ($skip===1) && ($row['P1']>=100.0) && ($row['P2']>=100.0) && ($row['P3']>=100.0) ) continue 2;
					break;
				default: $this->myLogger->error( "cannot handle provided ({$nmangas}) number of rounds");
			}

			// control de salto de pagina
			if ( (($rowcount%$labels)==0) && ($rowcount!=0)) $this->AddPage(); // 16/13 etiquetas por pagina
			// ok. just print label for first 2 rounds
			$this->writeCell($rowcount%$labels,$row,0);
			$rowcount++;
			// and now print label for 3rd and (nonexistent ) 4th round
			if ($nmangas<=2) continue;
			if ( (($rowcount%$labels)==0) && ($rowcount!=0)) $this->AddPage(); // 16/13 etiquetas por pagina
			// ok. just print label for rounds 3 and (null) 4
			$this->writeCell($rowcount%$labels,$row,2);
			$rowcount++;
		}
		$this->myLogger->leave();
		return $rowcount;
	}
}
?>
