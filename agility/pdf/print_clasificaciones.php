<?php
header('Set-Cookie: fileDownload=true; path=/');
// mandatory 'header' to be the first element to be echoed to stdout

/**
 * genera un pdf ordenado por club, categoria y nombre con una pagina por cada jornada
*/

define('FPDF_FONTPATH', __DIR__."/font/");
require_once(__DIR__.'/fpdf.php');
require_once(__DIR__.'/../database/tools.php');
require_once(__DIR__.'/../database/logging.php');
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/Clasificaciones.php');

class parcialPDF extends FPDF {

	protected $prueba; // datos de la prueba
	protected $jornada; // datos de la jornada
	protected $manga; // datos de la manga
	protected $categorias; // categorias del listado
	protected $clasificacion; // clasificacion de la manga
	protected $myLogger;
	
	// geometria de las celdas
	protected $cellHeader
					=array('Orden','Nombre','Lic.','Guía','Club','Cat.','Flt','Rhs','Toc','Tiempo','Vel.','Penal.','Calific.');
	protected $pos	=array(  10,       20,    12,    37,   25,     7,    7,     7,    7,   12,      8,    12,      22);
	protected $align=array(  'C',      'L',   'L',   'R',  'R',   'C',  'C',   'C',  'C',  'R',     'R',   'R',     'C');
	protected $fmt	=array(  'i',      's',   's',   's',  's',   's',  's');
	protected $cat  =array(	"-" => "Sin categoria", "0" => "Sin categoria",
							"L"=>"Large",	"1"=>"Large",
							"M"=>"Medium",	"2"=>"Medium",
							"S"=>"Small",	"3"=>"Small",
							"T"=>"Tiny",
							"MS" => "Medium-Small",	"4"=>"Medium-Small",
							"LMS" => "Conjunta",	"5"=>"Conjunta");
	
	/**
	 * Constructor
	 * @param {object} $prueba datos de la prueba
	 * @param {object} $jornada datos de la jornada
	 * @param {object} $manga datos de la manga
	 * @param {String} $categorias Lista de categorias a enumerar
	 * @param {object} $datos de clasificacion en formato jquery
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$manga,$categorias,$clasificacion) {
		parent::__construct('Portrait','mm');
		if ( ($prueba===null) || ($jornada===null) || ($manga===null) || ($clasificacion===null) ) {
			$this->errormsg="clasificacionManga: either prueba/jornada/ manga/clasif data are invalid";
			throw new Exception($this->errormsg);
		}
		$this->prueba=$prueba;
		$this->jornada=$jornada;
		$this->manga=$manga;
		$this->categorias=$categorias;
		$this->clasificacion=$clasificacion;
		$this->myLogger= new Logger("clasificacionManga");
		$this->myLogger->info("Categorias is:: $categorias");
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
		$this->Cell(100,10,"Clasificacion Parcial",1,0,'C',false);// Nombre de la prueba centrado
		$this->Ln(); // Salto de línea
		
		// pintamos "identificacion de la manga"
		$this->SetFont('Arial','B',12); // Arial bold 15
		$str  = $this->jornada->Nombre . " - " . $this->jornada->Fecha;
		$str2 .= $this->manga->Tipo . " - " . $this->cat[$this->categorias];
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
		foreach($this->clasificacion as $row) {
			// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
			if( ($rowcount%30) == 0 ) { // assume 35 rows per page ( rowWidth = 7mmts )
				if ($rowcount>0) 
					$this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre en cambio de pagina
				$this->addPage();
				$this->writeTableHeader();
			}

			$this->SetFont('Arial','B',10); // remove bold
			$this->Cell($this->pos[0],7,	$rowcount+1,			'LR',0,$this->align[0],$fill); // display order
			$this->SetFont('Arial','',10); // remove bold
			$this->Cell($this->pos[1],7,	$row['Nombre'],			'LR',0,$this->align[1],$fill);
			$this->Cell($this->pos[2],7,	$row['Licencia'],		'LR',0,$this->align[2],$fill);
			$this->Cell($this->pos[3],7,	$row['NombreGuia'],		'LR',0,$this->align[3],$fill);
			$this->Cell($this->pos[4],7,	$row['NombreClub'],		'LR',0,$this->align[4],$fill);
			$this->Cell($this->pos[5],7,	$row['Categoria'],		'LR',0,$this->align[5],$fill);
			$this->SetFont('Arial','B',9); // remove bold
			$this->Cell($this->pos[6],7,	$row['Faltas'],			'LR',0,$this->align[6],$fill);
			$this->Cell($this->pos[7],7,	$row['Rehuses'],		'LR',0,$this->align[7],$fill);
			$this->Cell($this->pos[8],7,	$row['Tocados'],		'LR',0,$this->align[8],$fill);
			$t=number_format((float)$row['Tiempo'], 2, '.', '');
			$this->Cell($this->pos[9],7,	$t,						'LR',0,$this->align[9],$fill);
			$this->SetFont('Arial','',9); // remove bold
			$v=number_format((float)$row['Velocidad'], 1, '.', '');
			$p=number_format((float)$row['Penalizacion'], 2, '.', '');
			$this->Cell($this->pos[10],7,	$v,						'LR',0,$this->align[10],$fill);
			$this->Cell($this->pos[11],7,	$p,						'LR',0,$this->align[11],$fill);
			$this->Cell($this->pos[12],7,	$row['Calificacion'],	'LR',0,$this->align[12],$fill);
			$this->Ln();
			$fill = ! $fill;
			$rowcount++;
		}
		// Línea de cierre
		$this->Cell(array_sum($this->pos),0,'','T');
		$this->myLogger->leave();
	}
}

class finalPDF extends FPDF {

	protected $prueba; // datos de la prueba
	protected $jornada; // datos de la jornada
	protected $manga; // datos de la manga
	protected $categorias; // categorias del listado
	protected $clasificacion; // clasificacion de la manga
	protected $myLogger;

	// geometria de las celdas
	protected $cellHeader
		=array('Orden','Nombre','Guía','Club','Cat.'
				,'F','R','T','Tmp','Vel.','Penal.','Calif.'
				,'F','R','T','Tmp','Vel.','Penal.','Calif.'
				,'Tiempo','Penal.','Punto');
	protected $pos	
		=array(  10,     20,     35,    30,   7,
				  7,  7,  7,  12,   10,      12,    10,
				  7,  7,  7,  12,   10,      12,    10,
				  12,         15,     10);
	protected $align
		=array(  'C',      'L',   'L',   'R',  'C',   
				  'C','C','C','R',  'R',     'R',   'C',
				  'C','C','C','R',  'R',     'R',   'C',
			      'R',      'R',	'C');
	protected $cat  
		=array(	"-" => "Sin categoria", "0" => "Sin categoria",
			"L"=>"Large",	"1"=>"Large",
			"M"=>"Medium",	"2"=>"Medium",
			"S"=>"Small",	"3"=>"Small",
			"T"=>"Tiny",
			"MS" => "Medium-Small",	"4"=>"Medium-Small",
			"LMS" => "Conjunta",	"5"=>"Conjunta");

	/**
	 * Constructor
	 * @param {object} $prueba datos de la prueba
	 * @param {object} $jornada datos de la jornada
	 * @param {object} $manga1 datos de la manga 1
	 * @param {object} $manga2 datos de la manga 2
	 * @param {String} $categorias Lista de categorias a enumerar
	 * @param {object} $datos de clasificacion en formato jquery
	 * @throws Exception
	*/
	function __construct($prueba,$jornada,$manga1,$manga2,$categorias,$clasificacion) {
		parent::__construct('Landscape','mm');
		if ( ($prueba===null) || ($jornada===null) || ($manga1===null) ||($manga2===null) || ($clasificacion===null) ) {
			$this->errormsg="clasificacionManga: either prueba/jornada/ manga/clasif data are invalid";
			throw new Exception($this->errormsg);
		}
		$this->prueba=$prueba;
		$this->jornada=$jornada;
		$this->manga1=$manga1;
		$this->manga2=$manga2;
		$this->categorias=$categorias;
		$this->clasificacion=$clasificacion;
		$this->myLogger= new Logger("clasificacionManga");
		$this->myLogger->info("Categorias is:: $categorias");
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
		$this->Cell(55); // primer cuarto de la linea
		$this->Cell(150,10,$this->prueba['Nombre'],0,0,'C',false);// Nombre de la prueba centrado
		$this->Ln(); // Salto de línea

		// pintamos "listado de participantes en un recuadro"
		$this->SetFont('Arial','B',20); // Arial bold 20
		$this->Cell(55); // primer cuarto de la linea
		$this->Cell(150,10,"Clasificacion Final",1,0,'C',false);// Nombre de la prueba centrado
		$this->Ln(); // Salto de línea

		// pintamos "identificacion de la manga"
		$this->SetFont('Arial','B',12); // Arial bold 15
		$str  = $this->jornada->Nombre . " - " . $this->jornada->Fecha;
		$str2 = $this->manga1->Grado . " - " . $this->cat[$this->categorias];
		$this->Cell(130,10,$str,0,0,'L',false); // a un lado nombre y fecha de la jornada
		$this->Cell(130,10,$str2,0,0,'R',false); // al otro lado tipo y categoria de la manga
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
		$this->SetFillColor(224,235,255); // azul merle
		$this->SetFont('Arial','B',9); // bold 9px

		// indicadores de participante, manga1, manga2 y resultados finales
		$this->Cell(102,7,"Participantes",1,0,'C',true);
		$this->Cell(65,7,$this->manga1->Tipo,1,0,'C',true); 
		$this->Cell(65,7,$this->manga2->Tipo,1,0,'C',true); 
		$this->Cell(37,7,"Clasificación",1,0,'C',true);
		$this->Ln();
		// cabecera principal
		$this->SetTextColor(255,255,255); // blanco
		$this->SetFillColor(0,0,255); // azul
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
		foreach($this->clasificacion as $row) {
			// REMINDER: $this->cell( width, height, data, borders, where, align, fill)
			if( ($rowcount%18) == 0 ) { // assume 18 rows per page ( rowWidth = 7mmts )
				if ($rowcount>0)
					$this->Cell(array_sum($this->pos),0,'','T'); // linea de cierre en cambio de pagina
				$this->addPage();
				$this->writeTableHeader();
			}
			// datos personales
			$this->SetFont('Arial','B',10); // remove bold
			$this->Cell($this->pos[0],7,	$rowcount+1,			'LR',0,$this->align[0],$fill); // display order
			$this->SetFont('Arial','',10); // remove bold
			$this->Cell($this->pos[1],7,	$row['Nombre'],			'LR',0,$this->align[1],$fill);
			$this->Cell($this->pos[2],7,	$row['Guia'],			'LR',0,$this->align[2],$fill);
			$this->Cell($this->pos[3],7,	$row['Club'],			'LR',0,$this->align[3],$fill);
			$this->Cell($this->pos[4],7,	$row['Categoria'],		'LR',0,$this->align[4],$fill);
			// datos de manga 1
			$this->SetFont('Arial','B',9); // remove bold
			$this->Cell($this->pos[5],7,	$row['Faltas'],			'LR',0,$this->align[5],$fill);
			$this->Cell($this->pos[6],7,	$row['Rehuses'],		'LR',0,$this->align[6],$fill);
			$this->Cell($this->pos[7],7,	$row['Tocados'],		'LR',0,$this->align[7],$fill);
			$t=number_format((float)$row['Tiempo'], 2, '.', '');
			$this->Cell($this->pos[8],7,	$t,			'LR',0,$this->align[8],$fill);
			$this->SetFont('Arial','',9); // remove bold
			$v=number_format((float)$row['Velocidad'], 1, '.', '');
			$p=number_format((float)$row['Penalizacion'], 2, '.', '');
			$this->Cell($this->pos[9],7,	$v,			'LR',0,$this->align[9],$fill);
			$this->Cell($this->pos[10],7,	$p,			'LR',0,$this->align[10],$fill);
			$this->Cell($this->pos[11],7,	$row['Calificacion'],	'LR',0,$this->align[11],$fill);
			// datos de manga 2
			$this->SetFont('Arial','B',9); // remove bold
			$this->Cell($this->pos[12],7,	$row['Faltas2'],		'LR',0,$this->align[12],$fill);
			$this->Cell($this->pos[13],7,	$row['Rehuses2'],		'LR',0,$this->align[13],$fill);
			$this->Cell($this->pos[14],7,	$row['Tocados2'],		'LR',0,$this->align[14],$fill);
			$t=number_format((float)$row['Tiempo2'], 2, '.', '');
			$this->Cell($this->pos[15],7,	$t,		'LR',0,$this->align[15],$fill);
			$this->SetFont('Arial','',9); // remove bold
			$v=number_format((float)$row['Velocidad2'], 1, '.', '');
			$p=number_format((float)$row['Penalizacion2'], 2, '.', '');
			$this->Cell($this->pos[16],7,	$v,						'LR',0,$this->align[16],$fill);
			$this->Cell($this->pos[17],7,	$p,						'LR',0,$this->align[17],$fill);
			$this->Cell($this->pos[18],7,	$row['Calificacion2'],	'LR',0,$this->align[18],$fill);
			// datos de clasificacion conjunta
			$t=number_format((float)$row['TFinal'], 2, '.', '');
			$p=number_format((float)$row['PFinal'], 2, '.', '');
			$this->Cell($this->pos[19],7,	$t,			'LR',0,$this->align[19],$fill);
			$this->Cell($this->pos[20],7,	$p,			'LR',0,$this->align[20],$fill);
			$this->Cell($this->pos[21],7,	$row['Puntos'],			'LR',0,$this->align[21],$fill);
			
			$this->Ln();
			$fill = ! $fill;
			$rowcount++;
		}
		// Línea de cierre
		$this->Cell(array_sum($this->pos),0,'','T');
		$this->myLogger->leave();
	}
}

function composeCSV($prueba,$jornada,$manga1,$manga2,$categorias,$result) {
	header("Content-type: text/plain");
	header("Content-Disposition: attachment; filename=printEtiquetas.txt");
	$count=1;
	$numrows=count($result);
	foreach ($result as $row){
		// fecha : nombre : categoria : grado : 
		// tipomanga1 : penalizacion : calificacion
		// tipomanga2 : penalizacion : calificacion
		$str  = $jornada->Fecha . ":";
		$str .= $row['Nombre'] . " - " . $row['Guia'] . ":";
		$str .= $row['Categoria']  . ":";
		$str .= $manga1->Grado 	. ":";
		$str .= $manga1->Tipo  	. ":";
		$str .= number_format((float)$row['Penalizacion'], 2, '.', '') . ":";
		$str .= $row['Calificacion']  . ":";
		$str .= $manga2->Tipo  	. ":";
		$str .= number_format((float)$row['Penalizacion2'], 2, '.', '') . ":";
		$str .= $row['Calificacion2']  . "\n";
		echo $str;		
	}
}

// Consultamos la base de datos
try {
	$myLogger=new Logger("base");
	
	// obtenemos parametros  de la peticion
	$pruebaid=http_request("Prueba","i",0);
	$jornadaid=http_request("Jornada","i",0);
	$manga1id=http_request("Manga","i",0);
	$manga2id=http_request("Manga2","i",0);
	$categorias=http_request("Categorias","s","0");
	$operation=http_request("Operation","i",0);
	
	// Datos de la prueba
	$p=new Pruebas("printClasificaciones");
	$prueba=$p->selectByID($pruebaid);
	// Datos de la jornada
	$j=new Jornadas("printClasificaciones",$pruebaid);
	$jornada=$j->selectByID($jornadaid);
	// Datos de la primera y segunda mangas
	$m = new Mangas("printClasificaciones",$jornadaid);
	$manga1= $m->selectByID($manga1id);
	$manga2= $m->selectByID($manga2id);
	// datos de las clasificaciones
	$clasificaciones= new Clasificaciones("printClasificaciones");
	$pdf=null;
	$result=null;
	switch($operation) {
		case 0: /* manga 1 */ 
			$result=$clasificaciones->clasificacionParcial($manga1id,$categorias);
			$pdf = new parcialPDF($prueba,$jornada,$manga1,$categorias,$result['rows']);
			break;
		case 1: /* manga 2 */ 
			$result=$clasificaciones->clasificacionParcial($manga2id,$categorias); 
			$pdf = new parcialPDF($prueba,$jornada,$manga2,$categorias,$result['rows']);
			break;
		case 2: /* final */
			$result=$clasificaciones->clasificacionFinal($manga1id,$manga2id,$categorias);  
			$pdf = new finalPDF($prueba,$jornada,$manga1,$manga2,$categorias,$result['rows']);
			break;
		case 3: $result=$clasificaciones->clasificacionFinal($manga1id,$manga2id,$categorias);
			$csv= composeCSV($prueba,$jornada,$manga1,$manga2,$categorias,$result['rows']);
			return;
	}
	if ($pdf!=null) {
		// Creamos generador de documento
		$pdf->AliasNbPages();
		$pdf->composeTable();
		$pdf->Output("printClasificaciones.pdf","D"); // "D" means open download dialog
	}
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
};
echo json_encode(array('success'=>true));
?>