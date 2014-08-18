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
					=array('Orden','Nombre','Lic.','Guía','Club','Celo','Observaciones');
	protected $pos	=array(  10,       30,     15,    50,   30,     10,    40);
	protected $align=array(  'R',      'L',    'C',   'R',  'R',    'C',   'R');
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
			$this->errormsg="printOrdenDeSalida: either prueba/jornada/ manga/orden data are invalid";
			throw new Exception($this->errormsg);
		}
		$this->prueba=$prueba;
		$this->jornada=$jornada;
		$this->manga=$manga;
		$this->orden=$orden;
		$this->categoria="L";
		$this->myLogger= new Logger("printOrdenDeSalida");
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
		$this->Cell(100,10,"Orden de Salida",1,0,'C',false);// Nombre de la prueba centrado
		$this->Ln(); // Salto de línea
		
		// pintamos "identificacion de la manga"
		$this->SetFont('Arial','B',12); // Arial bold 15
		$str  = $this->jornada->Nombre . " - " . $this->jornada->Fecha;
		$str2 .= $this->manga->Tipo . " - " . $this->cat[$this->categoria];
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
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
		
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		
		// Datos
		$fill = false;
		$rowcount=0;
		foreach($this->orden as $row) {
			// if change in categoria, reset orden counter and force page change
			if ($row->Categoria !== $this->categoria) {
				$this->categoria = $row->Categoria;
				$this->Cell(array_sum($this->pos),0,'','T'); // forzamos linea de cierre
				$rowcount=0;
			}
			// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
			if( ($rowcount%35) == 0 ) { // assume 35 rows per page ( rowWidth = 7mmts )
				if ($rowcount>0) 
					$this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre en cambio de pagina
				$this->addPage();
				$this->writeTableHeader();
			}
			$this->Cell($this->pos[0],7,$rowcount+1,	'LR',0,$this->align[0],$fill); // display order
			$this->Cell($this->pos[1],7,$row->Nombre,	'LR',0,$this->align[1],$fill);
			$this->Cell($this->pos[2],7,$row->Licencia,	'LR',0,$this->align[2],$fill);
			$this->Cell($this->pos[3],7,$row->Guia,		'LR',0,$this->align[3],$fill);
			$this->Cell($this->pos[4],7,$row->Club,		'LR',0,$this->align[4],$fill);
			$this->Cell($this->pos[5],7,$row->Celo,		'LR',0,$this->align[5],$fill);
			$this->Cell($this->pos[6],7,$row->Observaciones,'LR',0,$this->align[6],$fill);
			$this->Ln();
			$fill = ! $fill;
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
	$p=new Pruebas("printOrdenDeSalida");
	$prueba=$p->selectByID($pruebaid);
	// Datos de la jornada
	$j=new Jornadas("printOrdenDeSalida",$pruebaid);
	$jornada=$j->selectByID($jornadaid);
	// Datos de la manga
	$m = new Mangas("printOrdenDeSalida",$jornadaid);
	$manga= $m->selectByID($mangaid);
	// Datos del orden de salida
	$o = new OrdenSalida("printOrdenDeSalida");
	$orden= $o->getData($pruebaid,$jornadaid,$mangaid);
} catch (Exception $e) {
	die ("Error accessing database: ".$e.getMessage());
};
// Creamos generador de documento
$pdf = new PDF($prueba,$jornada,$manga,$orden['rows']);
$pdf->AliasNbPages();
$pdf->composeTable();
$pdf->Output("printOrdenDeSalida.pdf","D"); // "D" means open download dialog
echo json_encode(array('success'=>true));
?>