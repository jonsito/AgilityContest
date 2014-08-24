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
	protected $manga2; // datos de la manga 2
	protected $numrows; // formato del pdf 0:1 1:5 2:15 perros/pagina
	protected $categoria; // categoria que estamos listando
	protected $myLogger;

	// geometria de las celdas
	protected $cellHeader
					=array('Dorsal','Nombre','Lic.','Guía','Club','Celo', 'Observaciones');
	protected $pos	=array(  15,       25,     15,    50,   45,     10,    30);
	protected $align=array(  'C',      'R',    'C',   'L',  'R',    'C',   'R');
	protected $fmt	=array(  'i',      's',    's',   's',  's',    'b',   's');
	protected $cat  =array("-" => "Sin categoria","L"=>"Large","M"=>"Medium","S"=>"Small","T"=>"Tiny");
	
	/**
	 * Constructor
	 * @param {object} $prueba 
	 * @param {object} $jornada 
	 * @param {array[object]} datos de la manga y (si existe) manga hermana
	 * @param {array} $orden Lista de inscritos en formato jquery array[count,rows[]]
	 * @param {integer} $numrows numero de perros a imprimir por cada hoja
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$mangas,$orden,$numrows) {
		parent::__construct('Portrait','mm');
		if ( ($prueba===null) || ($jornada===null) || ($mangas===null) || ($orden===null) ) {
			$this->errormsg="printEntradaDeDatos: either prueba/jornada/ manga/orden data are invalid";
			throw new Exception($this->errormsg);
		}
		$this->prueba=$prueba;
		$this->jornada=$jornada;
		$this->manga=$mangas[0];
		$this->manga2=$mangas[1];
		$this->orden=$orden;
		$this->numrows=$numrows;
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
		
		if($this->numrows==1) $this->Ln(20);
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
	
	function writeTableCell_compacto($rowcount,$row) {
		$this->myLogger->trace("imprimiendo datos del idperro: ".$row['Perro']);
		// cada celda tiene una cabecera con los datos del participante
		$this->SetFillColor(0,0,255); // azul
		$this->SetDrawColor(0,0,0); // negro para los recuadros
		// save cursor position
		$x=$this->getX();
		$y=$this->GetY();

		// fase 1: contenido de cada celda de la cabecera
		// Cell( width,height,message,border,cursor,align,fill)
		// border 1:0:'[LTRB]'
		$this->SetTextColor(255,255,255); // blanco
		$this->SetFont('Arial','B',20); // bold 20px
		$this->Cell(15,14,$row['Dorsal'],		'LTBR',0,'C',true); // display order
		$this->SetFont('Arial','B',10); // bold 10px
		$this->Cell(10,7,($row['Celo']!=0)?"Celo":"",'TR',0,'C',true);
		$this->Cell(15,7,$row['Licencia'],		'TR',0,'C',true);
		$this->Cell(25,7,$row['Nombre'],		'TR',0,'R',true);
		$this->Cell(50,7,$row['NombreGuia'],	'TR',0,'R',true);
		$this->Cell(35,7,$row['NombreClub'],	'TR',0,'R',true);
		$this->Cell(7, 14,'',	'TRB',0,'L',false);
		$this->Cell(7, 14,'',	'TRB',0,'L',false);
		$this->Cell(7, 14,'',	'TRB',0,'L',false);
		$this->Cell(19, 14,'',  'TRB',0,'L',false);

		// ahora pintamos los nombres de los campos en negro fondo transparente sin borde
		$this->SetXY($x,$y); // restore cursor position
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont('Arial','I',8); // italic 8px
		$this->Cell(15,4,'Dorsal',	'',0,'L',false); // display order
		$this->Cell(10,4,'Celo',	'',0,'L',false);
		$this->Cell(15,4,'Licencia','',0,'L',false);
		$this->Cell(25,4,'Nombre',	'',0,'L',false);
		$this->Cell(50,4,'Guia',	'',0,'L',false);
		$this->Cell(35,4,'Club',	'',0,'L',false);
		$this->Cell(7, 4,'Flt.',	'',0,'C',false);
		$this->Cell(7, 4,'Toc.',	'',0,'C',false);
		$this->Cell(7, 4,'Reh.',	'',0,'C',false);
		$this->Cell(19,4,'Tiempo',  '',0,'C',false);
		
		// ahora pintamos recuadritos
		$this->SetFillColor(224,235,255); // azul merle
		$this->SetXY($x+15,$y+7); 
		$this->Cell(15,7,'Faltas','RB',0,'L',false);
		for ($i=1;$i<=10;$i++) $this->Cell(5,7,$i,'RB',0,'C',(($i&0x01)==0)?false:true);
		$this->Cell(15,7,'Tocados','RB',0,'L',false);
		for ($i=1;$i<=5;$i++) $this->Cell(5,7,$i,'RB',0,'C',(($i&0x01)==0)?false:true);
		$this->Cell(15,7,'Rehúses','RB',0,'L',false);
		for ($i=1;$i<=3;$i++) $this->Cell(5,7,$i,'RB',0,'C',(($i&0x01)==0)?false:true);
		$this->Ln(8);
	}
	
	/**
	 * 
	 * @param unknown $rowcount Row index
	 * @param unknown $row Row data
	 * @param number $f width factor (to be reused on extended print)
	 */
	function writeTableCell_normal($rowcount,$row,$f=1) {
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
		$this->Cell($this->pos[0],10*$f,$row['Dorsal'],		'LTR',0,$this->align[0],true); // display order
		$this->SetFont('Arial','B',12); // bold 9px
		$this->Cell($this->pos[1],10*$f,$row['Nombre'],		'LTR',0,$this->align[1],true);
		$this->Cell($this->pos[2],10*$f,$row['Licencia'],		'LTR',0,$this->align[2],true);
		$this->Cell($this->pos[3],10*$f,$row['NombreGuia'],	'LTR',0,$this->align[3],true);
		$this->Cell($this->pos[4],10*$f,$row['NombreClub'],	'LTR',0,$this->align[4],true);
		$this->Cell($this->pos[5],10*$f,($row['Celo']!=0)?"Celo":"",'LTR',0,$this->align[5],true);
		$this->Cell($this->pos[6],10*$f,$row['Observaciones'],	'LTR',0,$this->align[6],true);

		// fase 2: nombre de cada celda de la cabecera
		$this->SetXY($x,$y); // restore cursor position
		$this->SetTextColor(0,0,0); // negro
		$this->SetFont('Arial','I',8); // italic 8px
		$this->Cell($this->pos[0],5,($f!=1)?'Dorsal':'','',	0,'L',false); // Dorsal
		$this->Cell($this->pos[1],5,'Nombre:',	'',	0,'L',false);
		$this->Cell($this->pos[2],5,'Licencia:','',	0,'L',false);
		$this->Cell($this->pos[3],5,'Guia:',	'',	0,'L',false);
		$this->Cell($this->pos[4],5,'Club:',	'',	0,'L',false);
		$this->Cell($this->pos[5],5,'Celo:',	'',	0,'L',false);
		$this->Cell($this->pos[6],5,'Observaciones:','',0,'L',false);
		$this->Cell(0,10*$f); // increase height before newline
		
		// Restauración de colores y fuentes
		$this->SetFillColor(224,235,255); // azul merle
		$this->SetTextColor(0,0,0); // negro
		$this->Ln();
		// datos de Faltas, Tocados y Rehuses
		$this->Cell(20,10*$f,"Faltas",1,0,'L',false);
		for ($i=1;$i<=10;$i++) $this->Cell(10,10*$f,$i,1,0,'C',(($i&0x01)==0)?false:true);
		$this->Cell(10); $this->Cell(20,10*$f,"F: ",1,0,'L',false);
		$this->Cell(40,10*$f,"Tiempo: ",'LTR',0,'C',true);
		$this->Ln();
		$this->Cell(20,10*$f,"Tocados",1,0,'L',false);
		for ($i=1;$i<=10;$i++) $this->Cell(10,10*$f,$i,1,0,'C',(($i&0x01)==0)?false:true);
		$this->Cell(10); $this->Cell(20,10*$f,"T: ",1,0,'L',false);
		$this->Cell(40,10*$f,"",'LR',0,'C',true);
		$this->Ln();
		$this->Cell(20,10*$f,"Rehúses",1,0,'L',false);
		for ($i=1;$i<=3;$i++) $this->Cell(10,10*$f,$i,1,0,'C',(($i&0x01)==0)?false:true);
		$this->Cell(10); $this->Cell(30,10*$f,"Elim. ",1,0,'L',false); $this->Cell(30,10,"N.P. ",1,0,'L',false);
		$this->Cell(10); $this->Cell(20,10*$f,"R: ",1,0,'L',false);
		$this->Cell(40,10*$f,"",'LBR',0,'C',true);
		$this->Ln(17);
	}

	function writeTableCell_extendido($rowcount,$row) {
		// imprimimos informacion de la primera manga
		$this->writeTableCell_normal($rowcount,$row,2);
		// si existe imprimimos informacion de la segunda manga
		if ($this->manga2==null) return;
		$this->Ln(20);
		// pintamos "identificacion" de la segunda manga
		$this->SetFont('Arial','B',12); // Arial bold 15
		$str  = $this->jornada['Nombre'] . " - " . $this->jornada['Fecha'];
		$tmanga= Mangas::$tipo_manga[$this->manga2->Tipo][1];
		$str2 = $tmanga . " - " . $this->cat[$this->categoria];
		$this->Cell(90,10,$str,0,0,'L',false); // a un lado nombre y fecha de la jornada
		$this->Cell(90,10,$str2,0,0,'R',false); // al otro lado tipo y categoria de la manga
		$this->Ln(10);
		// y volvemos a pintar el recuadro para la segunda manga
		$this->writeTableCell_normal($rowcount,$row,2);
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
			if( ($rowcount % $this->numrows) == 0 ) { // assume $numrows entries per page 
				$this->addPage();
			}
			switch($this->numrows) {
				case 1: $this->writeTableCell_extendido($rowcount,$row);break;
				case 5: $this->writeTableCell_normal($rowcount,$row);break;
				case 15: $this->writeTableCell_compacto($rowcount,$row);break;
			}
			$rowcount++;
		}
		// Línea de cierre
		$this->Cell(array_sum($this->pos),0,'','T');
		$this->myLogger->leave();
	}
}

// Consultamos la base de datos
try {
	$myLogger=new Logger("print_entradaDeDatos");
	$pruebaid=http_request("Prueba","i",0);
	$jornadaid=http_request("Jornada","i",0);
	$mangaid=http_request("Manga","i",0);
	$mode=http_request("Mode","i",0);
	$myLogger->info("Prueba:$pruebaid Jornada:$jornadaid Manga:$mangaid Mode:$mode");
	
	// Datos de la prueba
	$p=new Pruebas("printEntradaDeDatos");
	$prueba=$p->selectByID($pruebaid);
	// Datos de la jornada
	$j=new Jornadas("printEntradaDeDatos",$pruebaid);
	$jornada=$j->selectByID($jornadaid);
	// Datos de la manga y su manga hermana
	$m = new Mangas("printEntradaDeDatos",$jornadaid);
	$mangas= $m->getHermanas($mangaid);
	// Datos del orden de salida
	$o = new OrdenSalida("printEntradaDeDatos");
	$orden= $o->getData($pruebaid,$jornadaid,$mangaid);
} catch (Exception $e) {
	die ("Error accessing database: ".$e.getMessage());
};
// Creamos generador de documento
$pdf = new PDF($prueba,$jornada,$mangas,$orden['rows'],$mode);
$pdf->AliasNbPages();
$pdf->composeTable();
$pdf->Output("printEntradaDeDatos.pdf","D"); // "D" means open download dialog
echo json_encode(array('success'=>true));
?>