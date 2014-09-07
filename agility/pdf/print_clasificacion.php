<?php
header('Set-Cookie: fileDownload=true; path=/');
// mandatory 'header' to be the first element to be echoed to stdout

/**
 * genera un CSV con los datos para las etiquetas
 */

define('FPDF_FONTPATH', __DIR__."/font/");
require_once(__DIR__."/fpdf.php");
require_once(__DIR__."/../database/tools.php");
require_once(__DIR__."/../database/logging.php");
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Clubes.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jueces.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/Resultados.php');
require_once(__DIR__.'/../database/classes/Clasificaciones.php');
require_once(__DIR__."/print_common.php");

class PDF extends PrintCommon {
	
	protected $manga1;
	protected $manga2;
	protected $resultados;

	 /** Constructor
	 * @param {obj} $manga datos de la manga
	 * @param {obj} $resultados resultados asociados a la manga/categoria pedidas
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$mangas,$resultados) {
		parent::__construct('Landscape',$prueba,$jornada);
		$dbobj=new DBObject("print_clasificacion");
		$this->manga1=$dbobj->__getObject("Mangas",$mangas[0]);
		$this->manga2=$dbobj->__getObject("Mangas",$mangas[1]);
		$this->resultados=$resultados;
	}
	
	function print_datosMangas() {
		$this->setXY(10,40);
		$this->SetFont('Arial','B',9); // bold 9px
		$jobj=new Jueces("print_Clasificaciones");
		$juez1=$jobj->selectByID($this->manga1->Juez1);
		$juez2=$jobj->selectByID($this->manga1->Juez2);
		$this->Cell(20,7,"Juez 1:","LT",0,'L',false);
		$this->Cell(70,7,$juez1['Nombre'],"T",0,'L',false);
		$this->Cell(20,7,"Juez 2:","T",0,'L',false);
		$this->Cell(70,7,$juez2['Nombre'],"TR",0,'L',false);
		$this->Ln();
		$this->Cell(180,7,'Datos Manga 1',"LTR",0,'L',false);
		$this->Ln();
		$this->Cell(180,7,'Datos Manga 2',"LRTB",0,'L',false);
	}
	
	function Header() {
		$this->print_commonHeader("Clasificación Final");
	}
	
	// Pie de página: tampoco cabe
	function Footer() {
		$this->print_commonFooter();
	}
	
	function writeTableHeader() {
		
		// colores para la cabecera de la tabla
		$this->SetFillColor(0,0,255); // azul
		$this->SetTextColor(255,255,255); // blanco
		$this->SetDrawColor(0,0,0); // line color
		
		$this->SetXY(10,($this->PageNo()==1)?65:40); // first page has 3 extra header lines
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		// first row of table header
		$this->SetFont('Arial','BI',12); // default font
		$this->Cell(105,7,'Datos del participante',0,0,'L',true);
		$this->Cell(65,7,'Manga 1',0,0,'C',true);
		$this->Cell(65,7,'Manga 2',0,0,'C',true);
		$this->Cell(40,7,'Clasificacion',0,0,'R',true);
		$this->ln();
		$this->SetFont('Arial','',8); // default font
		// datos del participante
		$this->Cell(10,7,'Dorsal',0,0,'C',true); 	// dorsal
		$this->Cell(20,7,'Nombre',0,0,'C',true);	// nombre (20,y
		$this->Cell(15,7,'Lic.',0,0,'C',true);	// licencia
		$this->Cell(10,7,'Cat./Gr.',0,0,'C',true);	// categoria/grado
		$this->Cell(30,7,'Guía',0,0,'C',true);	// nombreGuia
		$this->Cell(20,7,'Club',0,0,'C',true);	// nombreClub
		// manga 1
		$this->Cell(8,7,'F/T',0,0,'C',true);	// 1- Faltas+Tocados
		$this->Cell(8,7,'Reh',0,0,'C',true);	// 1- Rehuses
		$this->Cell(13,7,'Tiempo',0,0,'C',true);	// 1- Tiempo
		$this->Cell(10,7,'Vel.',0,0,'C',true);	// 1- Velocidad
		$this->Cell(13,7,'Penal',0,0,'C',true);	// 1- Penalizacion
		$this->Cell(13,7,'Calif',0,0,'C',true);	// 1- calificacion
		// manga 2
		$this->Cell(8,7,'F/T',0,0,'C',true);	// 2- Faltas+Tocados
		$this->Cell(8,7,'Reh',0,0,'C',true);	// 2- Rehuses
		$this->Cell(13,7,'Tiempo',0,0,'C',true);	// 2- Tiempo
		$this->Cell(10,7,'Vel.',0,0,'C',true);	// 2- Velocidad
		$this->Cell(13,7,'Penal',0,0,'C',true);	// 2- Penalizacion
		$this->Cell(13,7,'Calif',0,0,'C',true);	// 2- calificacion
		// global
		$this->Cell(20,7,'Penaliz.',0,0,'C',true);	// Penalizacion
		$this->Cell(10,7,'Calific.',0,0,'C',true);	// Calificacion
		$this->Cell(10,7,'Puesto',0,0,'C',true);	// Puesto	
		$this->Ln();	
		// restore colors
		$this->SetFillColor(224,235,255); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->SetDrawColor(128,128,128); // line color
	}
	
	function writeCell($idx,$row) {
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		$offset=($this->PageNo()==1)?80:55;
		$this->SetXY(10, $offset + 7*$idx ); // first page has 3 extra header lines
		$fill=(($idx%2)==0)?true:false;
		
		// fomateamos datos
		$puesto= ($row['Penalizacion']>=200)? "-":"{$row['Puesto']}º";
		$penal=number_format($row['Penalizacion'],2);
		$v1= ($row['P1']>=200)?"-":number_format($row['V1'],1);
		$t1= ($row['P1']>=200)?"-":number_format($row['T1'],2);
		$p1=number_format($row['P1'],2);
		$v2= ($row['P2']>=200)?"-":number_format($row['V2'],1);
		$t2= ($row['P2']>=200)?"-":number_format($row['T2'],2);
		$p2=number_format($row['P2'],2);
		
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		// datos del participante
		$this->Cell(10,7,$row['Dorsal'],0,0,'L',$fill); 	// dorsal
		$this->Cell(20,7,$row['Nombre'],0,0,'R',$fill);	// nombre (20,y
		$this->Cell(15,7,$row['Licencia'],0,0,'C',$fill);	// licencia
		$this->Cell(10,7,"{$row['Categoria']} {$row['Grado']}",0,0,'C',$fill);	// categoria/grado
		$this->Cell(30,7,$row['NombreGuia'],0,0,'R',$fill);	// nombreGuia
		$this->Cell(20,7,$row['NombreClub'],0,0,'R',$fill);	// nombreClub
		// manga 1
		$this->Cell(8,7,$row['F1'],0,0,'C',$fill);	// 1- Faltas+Tocados
		$this->Cell(8,7,$row['R1'],0,0,'C',$fill);	// 1- Rehuses
		$this->Cell(13,7,$t1,0,0,'C',$fill);	// 1- Tiempo
		$this->Cell(10,7,$v1,0,0,'C',$fill);	// 1- Velocidad
		$this->Cell(13,7,$p1,0,0,'C',$fill);	// 1- Penalizacion
		$this->Cell(13,7,$row['C1'],0,0,'C',$fill);	// 1- calificacion
		// manga 2
		$this->Cell(8,7,$row['F2'],0,0,'C',$fill);	// 2- Faltas+Tocados
		$this->Cell(8,7,$row['R2'],0,0,'C',$fill);	// 2- Rehuses
		$this->Cell(13,7,$t2,0,0,'C',$fill);	// 2- Tiempo
		$this->Cell(10,7,$v2,0,0,'C',$fill);	// 2- Velocidad
		$this->Cell(13,7,$p2,0,0,'C',$fill);	// 2- Penalizacion
		$this->Cell(13,7,$row['C2'],0,0,'C',$fill);	// 2- calificacion
		// global
		$this->Cell(20,7,$penal,0,0,'C',$fill);	// Penalizacion
		$this->Cell(10,7,$row['Calificacion'],0,0,'C',$fill);	// Calificacion
		$this->Cell(10,7,$puesto,0,0,'R',$fill);	// Puesto
		// lineas rojas
		$this->SetDrawColor(128,0,0);
		$this->Line(10+105,$offset + 7*$idx,10+105,$offset + 7*($idx+1));
		$this->Line(10+170,$offset + 7*$idx,10+170,$offset + 7*($idx+1));
		$this->Line(10+235,$offset + 7*$idx,10+235,$offset + 7*($idx+1));	
		$this->SetDrawColor(128,128,128); // line color
		
		$this->Ln();
	}
	
	function composeTable() {
		$this->myLogger->enter();

		$this->SetFillColor(224,235,255); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont('Arial','',8); // default font		
		$this->SetDrawColor(128,128,128); // line color
		$this->SetLineWidth(.3);
		
		$rowcount=0;
		$this->addPage();
		$this->print_datosMangas();
		foreach($this->resultados as $row) {
			$numrows=($this->PageNo()==1)?15:19;
			if($rowcount==0) $this->writeTableHeader();
			$this->writeCell( $rowcount % $numrows,$row);
			$rowcount++;
			if ($rowcount>=$numrows) {
				$this->addPage();
				$rowcount=0;
			}
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
} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}

// Creamos generador de documento
$pdf = new PDF($prueba,$jornada,$mangas,$result['rows']);
$pdf->AliasNbPages();
$pdf->composeTable();
$pdf->Output("print_etiquetas.pdf","D"); // "D" means open download dialog
?>