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

class PDF extends FPDF {
	
	public $myLogger;
	protected $prueba;
	protected $jornada;
	protected $manga;
	protected $resultados;
	protected $mode;
	
	// geometria de las celdas
	protected $cellHeader
					=array('Puesto','Dorsal','Nombre','Lic.','Guía','Club','Cat/Grad','Falt.','Reh.','Toc.','Tiempo','Vel.','Penal','Calificacion');
	protected $pos	=array(  10,      10,     12,     10,     29,    31,     12,        7,      7,    7,       10,     7,    12,      24 );
	protected $align=array(  'L',     'R',    'L',    'C',    'R',   'R',    'C',       'C',   'C',   'C',     'R',    'R',  'R',     'L');
	protected $fmt	=array(  'i',     'i',    's',    's',    's',   's',    's',       'i',   'i',   'i',     'f',    'f',  'f',     's');

	protected $modestr  =array("Large","Medium","Small","Medium+Small","Conjunta L/M/S");
	/**
	 * Constructor
	 * @param {obj} $manga datos de la manga
	 * @param {obj} $resultados resultados asociados a la manga/categoria pedidas
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$manga,$resultados,$mode) {
		parent::__construct('Portrait','mm');
		$this->prueba=$prueba;
		$this->jornada=$jornada;
		$this->manga=$manga;
		$this->resultados=$resultados;
		$this->mode=$mode;
		$this->myLogger= new Logger("printResultadosByManga");
		
		// evaluage logo info
		if (!isset($this->club)) { // to avoid duplicated database queries
			$clubobj=new Clubes("print_commonHeader");
			$club=$clubobj->__getObject("Clubes",$prueba['Club']);
			if (is_object($club)) $this->club=$club;
		}
		$icon="welpe.png";
		if (isset($this->club)) $icon=$this->club->Logo;
	}
	
	// No tenemos cabecera: no cabe
	function Header() {
	}
	
	// Pie de página: tampoco cabe
	function Footer() {
	}
	
	// las etiquetas tampoco no tienen cabecera
	function writeTableHeader() {
	}
	
	function writeCell($idx,$row) {
		// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
		//dorsal (10,y,20,17)
		$this->SetFont('Arial','B',12); // bold 11px
		$this->setXY(10,10+17*$idx);
		$this->Cell(20,17,$row['Dorsal'],0,0,'C',false);
		$this->SetFont('Arial','',8); // restore font size
		
		//logo   (30,y,15,15)
		// los logos tienen 150x150, que a 300 dpi salen aprox a 2.54 cmts
		$pdf->SetXY(30,10+17*idx); // margins are 10mm each
		$pdf->Cell(17,17,$pdf->Image(__DIR__.'/../images/logos/'.$icon,$pdf->getX(),$pdf->getY(),17),0,0,'L',false);
		
		//NombreClub (47,y,38,5) left
		//Fecha (47,y+5,38,5) left
		//Perro (47,y+10,38,7) right
		//Manga1Tipo(85,y,20,8) center
		//Manga2Tipo(85,y+8,20,9) center
		//Cat (105,y,15,8) center
		//Grado (105,y+8,15,9) center
		//Penal1 (120,y,15,8) right
		//Penal2 (120,y+8,15,9) right
		//Calif1 (135,y,25,8) right
		//Calif2 (135,y+8,25,9) right
		//Puesto1 (160,y,15,8) center
		//Puesto2 (160,y+8,15,9) center
	}
	
	function composeTable() {
		$this->myLogger->enter();
		$rowcount=0;
		$numrows=16; // 16 etiquetas/pagina
		foreach($resultados as $row) {
			if(($rowcount>0) && ($rowcount%$numrows==0)) $this->addPage();
			$this->writeCell($rowcount%numrows,$row);
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
	$c= new Clasificaciones("clasificacionesFunctions",$prueba,$jornada);
	$result=$c->clasificacionFinal($rondas,$mangas,$mode);
	if ($result===null) throw new Exception($clasificaciones->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}

// Creamos generador de documento
$pdf = new PDF($prueba,$jornada,$manga,$resultados,$mode);
$pdf->AliasNbPages();
$pdf->composeTable();
$pdf->Output("resultadosByManga.pdf","D"); // "D" means open download dialog
?>