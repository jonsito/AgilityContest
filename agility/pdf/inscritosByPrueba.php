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
	
	// Tabla coloreada
	function composeTable() {
		$this->myLogger->enter();
		// geometria de las celdas
		$cellHeader  
		    =array('Dorsal','Nombre','Guía','Club','Cat.','Grado','Celo','Sab.', 'Dom.', 'Observaciones');
		$pos=array(  10,       25,     40,   30,    10,     10,     10,  10,  10,  30 );
		$align=array('R',      'R',    'R',  'R',   'C',    'L',    'C', 'C', 'C', 'L');
		$fmt=array(  'i',      's',    's',  's',   's',    's',    'b', 'b', 'b', 's');
		
		// Colores, ancho de línea y fuente en negrita
		$this->SetFillColor(0,0,255);
		$this->SetTextColor(255);
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		$this->SetFont('','B',9);
		
		for($i=0;$i<count($cellHeader);$i++) {
			// en la cabecera texto siempre centrado
			$this->Cell($pos[$i],7,$cellHeader[$i],1,0,'C',true);
		}
		$this->Ln();
		// Restauración de colores y fuentes
		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);
		$this->SetFont('');
		// Datos
		$fill = false;
		foreach($this->inscritos as $row) {
			$this->myLogger->info("Datosd del perro: ".$row['Nombre']);
			// $this->cell( width, height, data, borders, where, align, fill)
			$this->Cell($pos[0],7,$row['Dorsal'],	'LR',0,$align[0],$fill);
			$this->Cell($pos[1],7,$row['Nombre'],	'LR',0,$align[1],$fill);
			$this->Cell($pos[2],7,$row['Guia'],		'LR',0,$align[2],$fill);
			$this->Cell($pos[3],7,$row['Club'],		'LR',0,$align[3],$fill);
			$this->Cell($pos[4],7,$row['Categoria'],'LR',0,$align[4],$fill);
			$this->Cell($pos[5],7,$row['Grado'],	'LR',0,$align[5],$fill);
			$this->Cell($pos[6],7,$row['Celo'],		'LR',0,$align[6],$fill);
			$this->Cell($pos[7],7,$row['J1'],		'LR',0,$align[7],$fill);
			$this->Cell($pos[8],7,$row['J2'],		'LR',0,$align[8],$fill);
			$this->Cell($pos[9],7,$row['Observaciones'],'LR',0,$align[9],$fill);
			$this->Ln();
			$fill = !$fill;
		}
		// Línea de cierre
		$this->Cell(array_sum($pos),0,'','T');
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
$pdf->AddPage();
$pdf->composeTable();
$pdf->Output("inscritosByPrueba.pdf","D"); // "D" means open download dialog
?>