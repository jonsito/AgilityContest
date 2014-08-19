<?php
header('Set-Cookie: fileDownload=true; path=/');
// mandatory 'header' to be the first element to be echoed to stdout

/**
 * genera un pdf ordenado por club, categoria y nombre con una pagina por cada jornada
*/

define('FPDF_FONTPATH', __DIR__."/font/");
require_once(__DIR__."/fpdf.php");
require_once(__DIR__."/../database/tools.php");
require_once (__DIR__."/../database/logging.php");
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/OrdenSalida.php');

class PDF extends FPDF {

	protected $prueba; // datos de la prueba
	protected $jornada; // datos de la jornada
	protected $manga; // datos de la manga
	protected $orden; // orden de salida
	protected $categoria; // categoria que estamos listando
	protected $myLogger;

	// geometria de las celdas
	protected $cellHeader
					=array('Dorsal','Nombre','Lic.','Guía','Club','Celo', 'Observaciones');
	protected $pos	=array(  15,       35,     20,    50,   30,     10,    30);
	protected $align=array(  'C',      'R',    'C',   'R',  'R',    'C',   'R');
	protected $fmt	=array(  'i',      's',    's',   's',  's',    'b',   's');
	protected $cat  =array("-" => "Sin categoria","L"=>"Large","M"=>"Medium","S"=>"Small","T"=>"Tiny");
	
	/**
	 * Constructor
	 * @param {integer} $prueba Prueba ID
	 * @param {array} $inscritos Lista de inscritos en formato jquery array[count,rows[]]
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$manga,$orden) {
		parent::__construct('Portrait','mm');
		if ( ($prueba===null) || ($jornada===null) || ($manga===null) || ($orden===null) ) {
			$this->errormsg="printEntradaDeDatos: either prueba/jornada/ manga/orden data are invalid";
			throw new Exception($this->errormsg);
		}
		$this->prueba=$prueba;
		$this->jornada=$jornada;
		$this->manga=$manga;
		$this->orden=$orden;
		$this->categoria="L";
		$this->myLogger= new Logger("printEntradaDeDatos");
	}
	
	// Cabecera de página
	function Header() {
		$this->myLogger->enter();
		// pintamos Logo
		// TODO: escoger logo en funcion del club
		// $this->image(file,startx,starty,width)
		$this->Image(__DIR__.'/../images/logos/welpe.png',15,10,20);
		
		// recordatorio
		// $this->cell( width, height, data, borders, where, align, fill)
		
		// pintamos nombre de la prueba
		$this->SetFont('Arial','BI',10); // Arial bold italic 10
		$this->Cell(50); // primer cuarto de la linea
		$this->Cell(100,10,$this->prueba['Nombre'],0,0,'C',false);// Nombre de la prueba centrado 
		$this->Ln(); // Salto de línea
		
		// pintamos "listado de participantes en un recuadro"
		$this->SetFont('Arial','B',20); // Arial bold 20
		$this->Cell(50); // primer cuarto de la linea
		$this->Cell(100,10,"Introducción de Datos",1,0,'C',false);// Nombre de la prueba centrado
		$this->Ln(); // Salto de línea
		
		// pintamos "identificacion de la manga"
		$this->SetFont('Arial','B',12); // Arial bold 15
		$str  = $this->jornada['Nombre'] . " - " . $this->jornada['Fecha'];
		$tmanga= Mangas::$tipo_manga[$this->manga->Tipo][1];
		$str2 = $tmanga . " - " . $this->cat[$this->categoria];
		$this->Cell(90,10,$str,0,0,'L',false); // a un lado nombre y fecha de la jornada
		$this->Cell(90,10,$str2,0,0,'R',false); // al otro lado tipo y categoria de la manga
		$this->Ln(10);
		
		$this->myLogger->leave();
	}
		
	// Pie de página
	function Footer() {
		$this->myLogger->enter();
		// Posición: a 1,5 cm del final
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('Arial','I',8);
		// Número de página
		$this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
		$this->myLogger->leave();
	}
	
	function writeTableHeader() {
		$this->myLogger->enter();
		// Colores, ancho de línea y fuente en negrita de la cabecera de tabla
		$this->SetFillColor(0,0,255); // azul
		$this->SetTextColor(255,255,255); // blanco
		$this->SetFont('Arial','B',9); // bold 9px
		for($i=0;$i<count($this->cellHeader);$i++) {
			// en la cabecera texto siempre centrado
			$this->Cell($this->pos[$i],7,$this->cellHeader[$i],1,0,'C',true);
		}
		// Restauración de colores y fuentes
		$this->SetFillColor(224,235,255); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont('Arial','',9); // remove bold
		$this->Ln();
		$this->myLogger->leave();
	}
	
	function writeTableCell($rowcount,$row) {
		$this->myLogger->trace("imprimiendo datos del idperro: ".$row['Perro']);
		// cada celda tiene una cabecera con los datos del participante
		$this->SetFillColor(0,0,255); // azul
		$this->SetDrawColor(0,0,0); // negro para los recuadros
		// save cursor position 
		$x=$this->getX();
		$y=$this->GetY();
		// fase 1: contenido de cada celda de la cabecera
		$this->SetTextColor(255,255,255); // blanco
		$this->SetFont('Arial','B',20); // bold 9px
		$this->Cell($this->pos[0],10,$row['Dorsal'],		'LTR',0,$this->align[0],true); // display order
		$this->SetFont('Arial','B',12); // bold 9px
		$this->Cell($this->pos[1],10,$row['Nombre'],		'LTR',0,$this->align[1],true);
		$this->Cell($this->pos[2],10,$row['Licencia'],		'LTR',0,$this->align[2],true);
		$this->Cell($this->pos[3],10,$row['NombreGuia'],	'LTR',0,$this->align[3],true);
		$this->Cell($this->pos[4],10,$row['NombreClub'],	'LTR',0,$this->align[4],true);
		$this->Cell($this->pos[5],10,($row['Celo']!=0)?"Celo":"",'LTR',0,$this->align[5],true);
		$this->Cell($this->pos[6],10,$row['Observaciones'],	'LTR',0,$this->align[6],true);

		// fase 2: nombre de cada celda de la cabecera
		$this->SetXY($x,$y); // restore cursor position
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont('Arial','I',8); // italic 8px
		$this->Cell($this->pos[0],5,'',	'',	0,'L',false); // Dorsal
		$this->Cell($this->pos[1],5,'Nombre:',	'',	0,'L',false);
		$this->Cell($this->pos[2],5,'Licencia:','',	0,'L',false);
		$this->Cell($this->pos[3],5,'Guia:',	'',	0,'L',false);
		$this->Cell($this->pos[4],5,'Club:',	'',	0,'L',false);
		$this->Cell($this->pos[5],5,'Celo:',	'',	0,'L',false);
		$this->Cell($this->pos[6],5,'Observaciones:','',0,'L',false);
		$this->Cell(0,10); // increase height before newline
		
		// Restauración de colores y fuentes
		$this->SetFillColor(224,235,255); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->Ln();
		// datos de Faltas, Tocados y Rehuses
		$this->Cell(20,10,"Faltas",1,0,'L',false);
		for ($i=1;$i<=10;$i++) $this->Cell(10,10,$i,1,0,'C',(($i&0x01)==0)?false:true);
		$this->Cell(10); $this->Cell(20,10,"F: ",1,0,'L',false);
		$this->Cell(40,10,"Tiempo: ",'LTR',0,'C',true);
		$this->Ln();
		$this->Cell(20,10,"Tocados",1,0,'L',false);
		for ($i=1;$i<=10;$i++) $this->Cell(10,10,$i,1,0,'C',(($i&0x01)==0)?false:true);
		$this->Cell(10); $this->Cell(20,10,"T: ",1,0,'L',false);
		$this->Cell(40,10,"",'LR',0,'C',true);
		$this->Ln();
		$this->Cell(20,10,"Rehúses",1,0,'L',false);
		for ($i=1;$i<=3;$i++) $this->Cell(10,10,$i,1,0,'C',(($i&0x01)==0)?false:true);
		$this->Cell(10); $this->Cell(30,10,"Elim. ",1,0,'L',false); $this->Cell(30,10,"N.P. ",1,0,'L',false);
		$this->Cell(10); $this->Cell(20,10,"R: ",1,0,'L',false);
		$this->Cell(40,10,"",'LBR',0,'C',true);
		$this->Ln(17);
	}
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
		
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		
		// Datos
		$rowcount=0;
		foreach($this->orden as $row) {
			// if change in categoria, reset orden counter and force page change
			if ($row['Categoria'] !== $this->categoria) {
				$this->myLogger->trace("Nueva categoria es: ".$row['Categoria']);
				$this->categoria = $row['Categoria'];
				$this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre de categoria
				$rowcount=0;
			}
			// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
			if( ($rowcount%5) == 0 ) { // assume 5 entries per page 
				$this->addPage();
			}
			$this->writeTableCell($rowcount,$row);
			$rowcount++;
		}
		// Línea de cierre
		$this->Cell(array_sum($this->pos),0,'','T');
		$this->myLogger->leave();
	}
}

// Consultamos la base de datos
try {
	$myLogger=new Logger("base");
	$pruebaid=http_request("Prueba","i",0);
	$jornadaid=http_request("Jornada","i",0);
	$mangaid=http_request("Manga","i",0);
	$myLogger->info("Prueba:$pruebaid Jornada:$jornadaid Manga:$mangaid");
	
	// Datos de la prueba
	$p=new Pruebas("printEntradaDeDatos");
	$prueba=$p->selectByID($pruebaid);
	// Datos de la jornada
	$j=new Jornadas("printEntradaDeDatos",$pruebaid);
	$jornada=$j->selectByID($jornadaid);
	// Datos de la manga
	$m = new Mangas("printEntradaDeDatos",$jornadaid);
	$manga= $m->selectByID($mangaid);
	// Datos del orden de salida
	$o = new OrdenSalida("printEntradaDeDatos");
	$orden= $o->getData($pruebaid,$jornadaid,$mangaid);
} catch (Exception $e) {
	die ("Error accessing database: ".$e.getMessage());
};
// Creamos generador de documento
$pdf = new PDF($prueba,$jornada,$manga,$orden['rows']);
$pdf->AliasNbPages();
$pdf->composeTable();
$pdf->Output("printEntradaDeDatos.pdf","D"); // "D" means open download dialog
echo json_encode(array('success'=>true));
?>