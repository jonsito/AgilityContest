<?php
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

class PrintCommon extends FPDF {
	
	protected $myLogger;
	protected $prueba; // datos de la prueba
	protected $club;   // club orcanizadod
	protected $icon;   // logo del club organizadod
	protected $jornada; // datos de la jornada

	/**
	 * Constructor de la superclase 
	 * @param unknown $prueba ID de la prueba
	 * @param unknown $jornada ID de la jornada
	 * @param unknown $mangas array[{integer} con los IDs de las mangas
	 */
	function __construct($orientacion,$prueba,$jornada) {
		parent::__construct($orientacion,'mm','A4'); // Portrait or Landscape
		$this->myLogger= new Logger("PrintCommon");
		$dbobj=new DBObject("print_etiquetas_pdf");
		$this->prueba=$dbobj->__getObject("Pruebas",$prueba);
		$this->club=$dbobj->__getObject("Clubes",$this->prueba->Club);
		$this->jornada=$dbobj->__getObject("Jornadas",$jornada);
		// evaluage logo info
		$this->icon="welpe.png";
		if (isset($this->club)) $this->icon=$this->club->Logo;
	}
	/**
	 * Pinta la cabecera de pagina
	 * @param {string} $title Titulo a imprimir en el cajetin
	 */
	function print_commonHeader($title) {
		$this->myLogger->enter();
		// pintamos Logo del club organizador a la izquierda y logo de la canina a la derecha
		// recordatorio
		// 		$this->Image(string file [, float x [, float y [, float w [, float h [, string type [, mixed link]]]]]])
		// 		$this->Cell( width, height, data, borders, where, align, fill)
		// 		los logos tienen 150x150, que a 300 dpi salen aprox a 2.54 cmts
		$icon2=($this->icon==="rsce.png")?"fci.png":"rsce.png"; // to avoid duplicate head logos
		$this->SetXY(10,10); // margins are 10mm each
		$this->Cell(25.4,25.4,$this->Image(__DIR__.'/../images/logos/'.$this->icon,$this->getX(),$this->getY(),25.4),0,0,'L',false);
		$this->SetXY($this->w - 35.4,10);
		$this->Cell(25.4,25.4,$this->Image(__DIR__.'/../images/logos/'.$icon2,$this->getX(),$this->getY(),25.4),0,0,'R',false);
	
		// pintamos nombre de la prueba
		$this->SetXY(10,10);
		$this->SetFont('Arial','BI',10); // Arial bold italic 10
		$this->Cell(50); // primer cuarto de la linea
		$this->Cell(100,10,$this->prueba->Nombre,0,0,'C',false);// Nombre de la prueba centrado 
		$this->Ln(); // Salto de línea
		
		// pintamos el titulo en un recuadro
		$this->SetFont('Arial','B',20); // Arial bold 20
		$this->Cell(50); // primer cuarto de la linea
		$this->Cell(100,10,$title,1,0,'C',false);// Nombre de la prueba centrado
		$this->Ln(15); // Salto de línea
		$this->myLogger->leave();
	}
		
	// Pie de página
	function print_commonFooter() {
		$this->myLogger->enter();
		// Posición: a 1,5 cm del final
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('Arial','I',8);
		// Número de página
		$this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
		$this->myLogger->leave();
	}

	// Identificacion de la Manga
	function print_identificacionManga($manga,$categoria) {
		// pintamos "identificacion de la manga"
		$this->SetFont('Arial','B',12); // Arial bold 15
		$str  = $this->jornada->Nombre . " - " . $this->jornada->Fecha;
		$tmanga= Mangas::$tipo_manga[$manga->Tipo][1];
		$str2="$tmanga - $categoria";
		$this->Cell(90,10,$str,0,0,'L',false); // a un lado nombre y fecha de la jornada
		$this->Cell(90,10,$str2,0,0,'R',false); // al otro lado tipo y categoria de la manga
		$this->Ln(10);
	}
}
?>