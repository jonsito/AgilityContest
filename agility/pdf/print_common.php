<?php
require_once(__DIR__.'/../database/classes/Clubes.php');

/**
 * Funciones comunes a todos los modulos de impresion.
 */
	
/**
 * Pinta la cabecera de pagina
 * @param {object} $pdf objeto pdf para impresion
 * @param {object} $prueba Datos de la prueba
 * @param {object} $jornada Datos de la jornada
 * @param {object} $manga Datos de la manga afectada
 * @param {string} $title Titulo a imprimir en el cajetin
 */
function print_commonHeader($pdf,$prueba,$jornada,$manga,$title) {
	$pdf->myLogger->enter();
	
	// pintamos Logo del club organizador a la izquierda y logo de la canina a la derecha

	// recordatorio
	// $pdf->Image(string file [, float x [, float y [, float w [, float h [, string type [, mixed link]]]]]])
	// $pdf->Cell( width, height, data, borders, where, align, fill)
	
	if (!isset($pdf->club)) { // to avoid duplicated database queries
		$clubobj=new Clubes("print_commonHeader");
		$club=$clubobj->__selectObject("*","Clubes","(ID={$prueba['Club']})");
		if (is_object($club)) $pdf->club=$club;
	}
	$icon="welpe.png";
	if (isset($pdf->club)) $icon=$pdf->club->Logo;
	// los logos tienen 150x150, que a 300 dpi salen aprox a 2.54 cmts
	$pdf->SetXY(10,10); // margins are 10mm each
	$pdf->Cell(25.4,25.4,$pdf->Image(__DIR__.'/../images/logos/'.$icon,$pdf->getX(),$pdf->getY(),25.4),0,0,'L',false);
	$pdf->SetXY($pdf->w - 35.4,10);
	$pdf->Cell(25.4,25.4,$pdf->Image(__DIR__.'/../images/logos/rsce.png',$pdf->getX(),$pdf->getY(),25.4),0,0,'R',false);

	// pintamos nombre de la prueba
	$pdf->SetXY(10,10);
	$pdf->SetFont('Arial','BI',10); // Arial bold italic 10
	$pdf->Cell(50); // primer cuarto de la linea
	$pdf->Cell(100,10,$prueba['Nombre'],0,0,'C',false);// Nombre de la prueba centrado 
	$pdf->Ln(); // Salto de línea
	
	// pintamos "listado de participantes en un recuadro"
	$pdf->SetFont('Arial','B',20); // Arial bold 20
	$pdf->Cell(50); // primer cuarto de la linea
	$pdf->Cell(100,10,$title,1,0,'C',false);// Nombre de la prueba centrado
	$pdf->Ln(15); // Salto de línea
	$pdf->myLogger->leave();
}
		
// Pie de página
function print_commonFooter($pdf,$prueba,$jornada,$manga) {
	$pdf->myLogger->enter();
	// Posición: a 1,5 cm del final
	$pdf->SetY(-15);
	// Arial italic 8
	$pdf->SetFont('Arial','I',8);
	// Número de página
	$pdf->Cell(0,10,'Página '.$pdf->PageNo().'/{nb}',0,0,'C');
	$pdf->myLogger->leave();
}

// Identificacion de la Manga
function print_identificacionManga($pdf,$prueba,$jornada,$manga,$categoria) {
		// pintamos "identificacion de la manga"
		$pdf->SetFont('Arial','B',12); // Arial bold 15
		$str  = $jornada['Nombre'] . " - " . $jornada['Fecha'];
		$tmanga= Mangas::$tipo_manga[$manga->Tipo][1];
		$str2="$tmanga - $categoria";
		$pdf->Cell(90,10,$str,0,0,'L',false); // a un lado nombre y fecha de la jornada
		$pdf->Cell(90,10,$str2,0,0,'R',false); // al otro lado tipo y categoria de la manga
		$pdf->Ln(10);
}

?>