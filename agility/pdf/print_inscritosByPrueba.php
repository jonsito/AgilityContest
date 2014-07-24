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
require_once(__DIR__.'/../database/classes/Inscripciones.php');

class PDF extends FPDF {
	
	protected $prueba;
	protected $inscritos;
	protected $myLogger;

	// geometria de las celdas
	protected $cellHeader
					=array('Dorsal','Nombre','Lic.','Guía','Club','Cat.','Grado','Celo','Observaciones','Sab.','Dom.');
	protected $pos	=array(  10,       20,     10,    40,   30,    10,     10,     10,    30,    10,    10 );
	protected $align=array(  'R',      'L',    'C',   'R',  'R',   'C',    'L',    'C',   'R',   'C',   'C');
	protected $fmt	=array(  'i',      's',    's',   's',  's',   's',    's',    'b',   's',   'b',   'b');
	
	/**
	 * Constructor
	 * @param {integer} $prueba Prueba ID
	 * @param {array} $inscritos Lista de inscritos en formato jquery array[count,rows[]]
	 * @throws Exception
	 */
	function __construct($prueba,$inscritos) {
		parent::__construct('Portrait','mm');
		if ( ($prueba===null) || ($inscritos===null) ) {
			$this->errormsg="printInscritosByPrueba: either prueba or inscription data are invalid";
			throw new Exception($this->errormsg);
		}
		$this->prueba=$prueba;
		$this->inscritos=$inscritos['rows'];
		$this->myLogger= new Logger("printInscritosByPrueba");
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
		
		// pintamos "listado de participantes en un recuadro
		$this->SetFont('Arial','BI',20); // Arial bold italic 10
		$this->Cell(50); // primer cuarto de la linea
		$this->Cell(100,10,"Listado de participantes",1,0,'C',false);// Nombre de la prueba centrado
		$this->Ln(15);
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
		$this->SetFont('Arial','B',8); // bold 9px
		for($i=0;$i<count($this->cellHeader);$i++) {
			// en la cabecera texto siempre centrado
			$this->Cell($this->pos[$i],7,$this->cellHeader[$i],1,0,'C',true);
		}
		// Restauración de colores y fuentes
		$this->SetFillColor(224,235,255); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont(''); // remove bold
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
		foreach($this->inscritos as $row) {
			// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
			if( ($rowcount%35) == 0 ) { // assume 35 rows per page ( rowWidth = 7mmts )
				if ($rowcount>0) 
					$this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre
				$this->addPage();
				$this->writeTableHeader();
			} 
			// $this->Cell($this->pos[0],7,$row['IDPerro'],	'LR',0,$this->align[0],$fill);
			$this->Cell($this->pos[0],7,$rowcount+1,	'LR',0,		$this->align[0],$fill); // display order instead of idperro
			$this->Cell($this->pos[1],7,$row['Nombre'],	'LR',0,		$this->align[1],$fill);
			$this->Cell($this->pos[2],7,$row['Licencia'],'LR',0,	$this->align[2],$fill);
			$this->Cell($this->pos[3],7,$row['Guia'],	'LR',0,		$this->align[3],$fill);
			$this->Cell($this->pos[4],7,$row['Club'],	'LR',0,		$this->align[4],$fill);
			$this->Cell($this->pos[5],7,$row['Categoria'],'LR',0,	$this->align[5],$fill);
			$this->Cell($this->pos[6],7,$row['Grado'],	'LR',0,		$this->align[6],$fill);
			$this->Cell($this->pos[7],7,$row['Celo'],	'LR',0,		$this->align[7],$fill);
			$this->Cell($this->pos[8],7,$row['Observaciones'],'LR',0,$this->align[9],$fill);
			$this->Cell($this->pos[9],7,$row['J1'],		'LR',0,		$this->align[9],$fill);
			$this->Cell($this->pos[10],7,$row['J2'],		'LR',0,	$this->align[10],$fill);
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
	$pruebaid=http_request("Prueba","i",0);
	// Datos de la prueba
	$prueba =new Pruebas("printInscritosByPrueba");
	$datosPrueba=$prueba->selectByID($pruebaid);
	// Datos de inscripciones
	$inscripciones = new Inscripciones("printInscritosByPrueba",$pruebaid);
	$inscritos= $inscripciones->inscritos();
} catch (Exception $e) {
	die ("Error accessing database: ".$e.getMessage());
}
// Creamos generador de documento
$pdf = new PDF($datosPrueba,$inscritos);
$pdf->AliasNbPages();
$pdf->composeTable();
$pdf->Output("inscritosByPrueba.pdf","D"); // "D" means open download dialog
?>