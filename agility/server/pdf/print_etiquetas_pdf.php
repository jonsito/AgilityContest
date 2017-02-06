<?php
/*
print_etiquetas_pdf.php

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


header('Set-Cookie: fileDownload=true; path=/');
// mandatory 'header' to be the first element to be echoed to stdout

/**
 * genera un CSV con los datos para las etiquetas
 */

require_once(__DIR__."/fpdf.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__.'/../auth/Config.php');
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Clubes.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jueces.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/Resultados.php');
require_once(__DIR__.'/../database/classes/Clasificaciones.php');
require_once(__DIR__."/print_common.php");

class Etiquetas_PDF extends PrintCommon {
	
	protected $manga1;
	protected $manga2;
	public $resultados;
	protected $serialno;
	
	 /** Constructor
	 * @param {obj} $manga datos de la manga
	 * @param {obj} $resultados resultados asociados a la manga/categoria pedidas
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$mangas) {
		parent::__construct('Portrait',"print_etiquetasPDF",$prueba,$jornada);
		$dbobj=new DBObject("print_etiquetas_pdf");
		$this->manga1=$dbobj->__getObject("Mangas",$mangas[0]);
		$this->manga2=$dbobj->__getObject("Mangas",$mangas[1]);
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
	
	function writeCell($idx,$row) {
		$top=$this->config->getEnv('pdf_topmargin');
		$left=$this->config->getEnv('pdf_leftmargin');
		$height=$this->config->getEnv('pdf_labelheight'); // 17 or 20 mmts ==> 16 or 13 labels/sheet
		
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		//dorsal (10,y,20,17)
		$y0=  $top + $height * $idx;
		$y1=  $top + $height * $idx + 1;
		$y2=  $top + $height * $idx + 2;
		// $y3=  $top + $height * $idx + 3;
		// $y4=  $top + $height * $idx + 4;
		$y5=  $top + $height * $idx + 5;
		// $y6=  $top + $height * $idx + 6;
		$y7=  $top + $height * $idx + 7;
		$y8=  $top + $height * $idx + 8;
        $y9=  $top + $height * $idx + 9;
		$y10= $top + $height * $idx + 10;
        $y12=  $top+ $height * $idx + 12;
		$y17=  $top+ $height * $idx + 17;
		$ynext=$top+ $height * ($idx+1);
		
		$this->SetFont($this->getFontName(),'B',24); // bold 11px
		$this->setXY($left,$y1-1);
		$this->Cell(20,17,$row['Dorsal'],'L',0,'C',false);
		$this->SetFont($this->getFontName(),'I',8); // font for prueba,name
		
		// caja izquierda (35,y,35,15)
		$this->SetXY($left+22,$y1); // margins are 10mm each
		$this->Cell(55,15,'','L',0,'L',false);
		
		//logo   (30,y,15,15)
		// los logos tienen 150x150, que a 300 dpi salen aprox a 2.54 cmts
		$this->SetXY($left+22,$y1); // margins are 10mm each
		$this->Image($this->icon,$this->getX(),$this->getY(),15);
		
		//Nombre del club (45,y,38,5) left
		$this->SetXY($left+36,$y1); 
		$this->Cell(38,5,$this->club->Nombre,0,0,'L',false);
		//Nombre de la prueba (45,y,38,5) left
		$this->SetXY($left+36,$y5);
		$this->Cell(38,5,$this->prueba->Nombre,0,0,'L',false);
		//Fecha (45,y+5,38,5) left
		$this->SetXY($left+36,$y9); 
		$this->Cell(38,5,$this->jornada->Fecha,0,0,'L',false);
        // licencia
        $this->SetFont($this->getFontName(),'',5); // font for licencia
        $this->SetXY($left+36,$y12);
        $this->Cell(38,5,$this->serialno,0,0,'L',false);
        $this->SetFont($this->getFontName(),'I',8); // font for prueba,name

		//Perro (45,y+10,38,7) right
		$this->SetXY($left+36,$y10); 
		$this->Cell(38,7,"{$row['Licencia']} - {$row['Nombre']}",0,0,'R',false);
		//Manga1Tipo(85,y,20,8) center
		$tipo=Mangas::$tipo_manga[$this->manga1->Tipo][3];
		$this->SetXY($left+75,$y1); 
		$this->Cell(20,7,$tipo,'LB',0,'L',false);
		//Manga2Tipo(85,y+8,20,9) center
		$tipo=Mangas::$tipo_manga[$this->manga2->Tipo][3];
		$this->SetXY($left+75,$y8); 
		$this->Cell(20,8,$tipo,'L',0,'L',false);

		$this->SetFont($this->getFontName(),'',12); // font size for results data
		//Cat (105,y,12,8) center
		$this->SetXY($left+95,$y1); 
		$this->Cell(12,7,$row['Categoria'],'L',0,'C',false);
		//Grado (105,y+8,12,9) center
		$this->SetXY($left+95,$y8); 
		$this->Cell(12,8,$row['Grado'],'L',0,'C',false);
		//Penal1 (117,y,17,8) right
		$this->SetXY($left+107,$y1); 
		$this->Cell(17,7,$row['P1'],'LB',0,'C',false);
		//Penal2 (117,y+8,17,9) right
		$this->SetXY($left+107,$y8); 
		$this->Cell(17,8,$row['P2'],'L',0,'C',false);

        $this->SetFont($this->getFontName(),'',9); // font size for results data
		//Calif1 (134,y,25,8) right
		$this->SetXY($left+124,$y1);
		$v=($row['V1']==0)?"":number_format($row['V1'],2)."m/s - ";
		$this->Cell(24,7,$v.$row['C1'],'LB',0,'C',false);
		//Calif2 (134,y+8,25,9) right
		$this->SetXY($left+124,$y8);
        $v=($row['V2']==0)?"":number_format($row['V2'],2)."m/s - ";
		$this->Cell(24,8,$v.$row['C2'],'L',0,'C',false);
		
		//Puesto1 (159,y,15,8) center
		$this->SetFont($this->getFontName(),'B',20); // font size for results data
		$this->ac_Cell($left+148.5,$y1,13,7,"/",'','C',false);
		$this->SetFont($this->getFontName(),'',10); // font size for results data
		$this->ac_Cell($left+148,$y1,13,7,"",'LBR','C',false);
		$this->ac_Cell($left+148,$y0,13,7,"{$row['Puesto1']}º",'','L',false);
		$this->ac_Cell($left+148,$y2,13,7,"${row['Participantes']}",'','R',false);

		//Puesto2 (159,y+8,15,9) center
		$this->SetFont($this->getFontName(),'B',20); // font size for results data
		$this->ac_Cell($left+148.5,$y8,13,8,"/",'','C',false);
		$this->SetFont($this->getFontName(),'',10); // font size for results data
		$this->ac_Cell($left+148,$y8,13,8,"",'LR','C',false);
		$this->ac_Cell($left+148,$y7,13,8,"{$row['Puesto2']}º",'','L',false);
		$this->ac_Cell($left+148,$y9,13,8,"${row['Participantes']}",'','R',false);
		// si 13 etiquetas/pagina, linea al final de la celda
		if ($height==20) $this->Line($left,$y17,$left+190,$y17);
		// linea al principio de celda siguiente
		$this->Line($left,$ynext,$left+190,$ynext);
		
		// en el margen izquierdo de las etiquetas
		// ponemos info de perro guia y club
		$this->SetFont($this->getFontName(),'B',10); // font size for results data
		$this->SetXY($left+170,$y1);
		$this->Cell(25,5,$row['Nombre'],'',0,'L',false);
		$this->SetXY($left+170,$y5);
		$this->Cell(25,5,$row['NombreGuia'],'',0,'L',false);
		$this->SetXY($left+170,$y9);
		$this->Cell(25,5,$row['NombreClub'],'',0,'L',false);
	}
	
	function composeTable($rowcount=0,$listadorsales="") {
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

		foreach($this->resultados as $row) {
			if ($listadorsales!=="") {
				$aguja=",{$row['Dorsal']},";
				$pajar=",$listadorsales,";
				if (strpos($pajar,$aguja)===FALSE) continue; // Dorsal not in list
			} else {
				// on double "not present" do not print label
                if ( ($row['P1']>=200.0) && ($row['P2']>=200.0) ) continue;
                // on double "eliminated", ( or eliminated+notpresent ) handle printing label accordind to configuration
				if ( ($this->config->getEnv('pdf_skipnpel')!=0) && ($row['P1']>=100.0) && ($row['P2']>=100.0) ) continue;
			}
			if ( (($rowcount%$labels)==0) && ($rowcount!=0)) $this->AddPage(); // 16/13 etiquetas por pagina
			$this->writeCell($rowcount%$labels,$row);
			$rowcount++;
		}
		$this->myLogger->leave();
		return $rowcount;
	}
}

try {
	$mangas=array();
	$result=array();
	for($n=0;$n<9;$n++) $result[$n]=array();
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
	$mode=http_request("Mode","i",0); // 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small
	$rowcount=http_request("Start","i",0); // offset to first label in page
	$listadorsales=http_request("List","s",""); // CSV Dorsal List
	
	// buscamos los recorridos asociados a la mangas
	$dbobj=new DBObject("print_etiquetas_csv");
	$mng=$dbobj->__getObject("Mangas",$mangas[0]);
	$prb=$dbobj->__getObject("Pruebas",$prueba);
	$c= new Clasificaciones("print_etiquetas_pdf",$prueba,$jornada);
	
	// obtenemos la clasificacion de la tanda seleccionada
	$r=$c->clasificacionFinal($rondas,$mangas,$mode);
	$result[0]=$r['rows'];
	/*
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
	*/
	// juntamos las categorias
	$res=array_merge($result[0],$result[1],$result[2],$result[3],$result[4],$result[5],$result[6],$result[7],$result[8]);
	// y ordenamos los resultados por dorsales
	usort($res,function($a,$b){return ($a['Dorsal']>$b['Dorsal'])?1:-1;});
	
	// Creamos generador de documento
	$pdf = new Etiquetas_PDF($prueba,$jornada,$mangas);
	$pdf->AddPage();
	// mandamos a imprimir
	$pdf->resultados=$res;
	$pdf->composeTable($rowcount,$listadorsales);
	// mandamos a la salida el documento
	$pdf->Output("print_etiquetas.pdf","D"); // "D" means open download dialog
} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}

?>
