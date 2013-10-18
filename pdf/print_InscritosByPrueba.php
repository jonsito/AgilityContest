<?php

/**
 * genera un pdf ordenado por club, categoria y nombre con una pagina por cada jornada
 */

define('FPDF_FONTPATH', '../pdf/font/');
require_once("../database/DBConnection.php");
require_once("../database/logging.php");
require_once("../pdf/fpdf.php");

// return array of data or null on error
function queryDB($prueba) {
	do_log("print_InscritosByPrueba():: enter");
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) return null;
	// FASE 1: obtener lista de perros inscritos con sus datos
	$str="SELECT Numero , Inscripciones.Dorsal AS Dorsal , PerroGuiaClub.Nombre AS Nombre, 
			Categoria , Grado , Celo , Guia , Club , Licencia
		  FROM Inscripciones,PerroGuiaClub,Jornadas
		  WHERE ( Inscripciones.Dorsal = PerroGuiaClub.Dorsal) 
		  		AND ( Inscripciones.Jornada = Jornadas.ID ) 
		  		AND ( Prueba= $prueba ) 
				ORDER BY Club, Categoria, Grado";
	$rs=$conn->query($str);
	if (!$rs) {
		$err="select_InscritosByPrueba::select( ) error ".$conn->error;
		do_log($err);
		DBConnection::closeConnection($conn);
		return null;
	}
	
	// Fase 2: la tabla de resultados a devolver
	$dorsales = array();
	$count=0;
	while($row = $rs->fetch_array()){
		do_log("select_InscritosByPrueba::select() examine dorsal ".$row['Dorsal']);
		if (!isset($dorsales[$row['Dorsal']])) {
			$count++;
			$dorsales[$row['Dorsal']]= array(
					'Dorsal' 	=> $count,
					'Nombre' 	=> $row['Nombre'],
					'Categoria' => $row['Categoria'],
					'Grado'		=> $row['Grado'],
					'Celo'		=> $row['Celo'],
					'Guia'		=> $row['Guia'],
					'Club'		=> $row['Club'],
					'Licencia'	=> $row['Licencia'],
					'J1' => 0,
					'J2' => 0,
					'J3' => 0,
					'J4' => 0,
					'J5' => 0,
					'J6' => 0,
					'J7' => 0,
					'J8' => 0
				);
		} // create row if not exists
		// store wich jornada is subscribed into array
		$jornada=$row['Numero'];
		$dorsales[$row['Dorsal']]["J$jornada"]=1;
	}
	$rs->free(); 
	DBConnection::closeConnection($conn);
	// OK: compose result to be returned
	$items=array();
	foreach($dorsales as $key => $item) array_push($items,$item);
	return $items;
}


//Create new pdf file
$pdf=new FPDF();
//Open file
$pdf->Open();
//Disable automatic page break
$pdf->SetAutoPageBreak(false);
//Add first page
$pdf->AddPage();

//set initial y axis position per page
$y_axis_initial = 25;
//Set Row Height
$row_height = 6;

//print column titles for the actual page
$pdf->SetFillColor(232, 232, 232);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetY($y_axis_initial);
$pdf->SetX(20);

$pdf->Cell(10, 6, 'Num.', 1, 0, 'L', 1);
$pdf->Cell(35, 6, 'Nombre', 1, 0, 'L', 1);
$pdf->Cell(20, 6, 'Lic.', 1, 0, 'L', 1);
$pdf->Cell(15, 6, 'Cat.',	1, 0, 'R', 1);
$pdf->Cell(50, 6, 'Guia',	1, 0, 'R', 1);
$pdf->Cell(25, 6, 'Club',	1, 0, 'R', 1);
$pdf->Cell(10, 6, 'Celo',	1, 0, 'C', 1);
$pdf->Cell(5, 6, 'S',	1, 0, 'C', 1);
$pdf->Cell(5, 6, 'D',	1, 0, 'C', 1);

// set table data values
$y_axis = $y_axis_initial + $row_height;
//Set maximum rows per page
$max = 25;

// Select data you want to show in your PDF file
$result=queryDB($_REQUEST['Prueba']);
if (!$result) die ("Error accessing database");

//initialize counter
$i = 0;

foreach($result as $row ) {
	
	//If the current row is the last one, create new page and print column title
	if ($i == $max) {
		$pdf->SetFillColor(232, 232, 232);
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->AddPage(); // mark new page
		//print column titles for the current page
		$pdf->SetY($y_axis_initial);
		$pdf->SetX(20);
		$pdf->Cell(10, 6, 'Num', 1, 0, 'L', 1);
		$pdf->Cell(35, 6, 'Nombre', 1, 0, 'L', 1);
		$pdf->Cell(20, 6, 'Lic.', 1, 0, 'L', 1);
		$pdf->Cell(15, 6, 'Cat.',	1, 0, 'R', 1);
		$pdf->Cell(50, 6, 'Guia',	1, 0, 'R', 1);
		$pdf->Cell(25, 6, 'Club',	1, 0, 'R', 1);
		$pdf->Cell(10, 6, 'Celo',	1, 0, 'C', 1);
		$pdf->Cell(5, 6, 'S',	1, 0, 'C', 1);
		$pdf->Cell(5, 6, 'D',	1, 0, 'C', 1);
		//Go to next row
		$y_axis = $y_axis_initial + $row_height;
		//Set $i variable to 0 (first row)
		$i = 0;
	}
	
	// set normal bold and text-background
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);

	$pdf->SetY($y_axis);
	$pdf->SetX(20);
		$pdf->Cell(10, 6, $row['Dorsal'], 	1, 0, 'L', 1);
		$pdf->Cell(35, 6, utf8_decode($row['Nombre']), 	1, 0, 'L', 1);
		$pdf->Cell(20, 6, $row['Licencia'],	1, 0, 'L', 1);
		$pdf->Cell(15, 6, $row['Categoria'].'-'.$row['Grado'],1, 0, 'R', 1);
		$pdf->Cell(50, 6, utf8_decode($row['Guia']),		1, 0, 'R', 1);
		$pdf->Cell(25, 6, utf8_decode($row['Club']),		1, 0, 'R', 1);
		$pdf->Cell(10, 6, ($row['Celo']==0)?' ':'X',		1, 0, 'C', 1);
		$pdf->Cell(5, 6, ($row['J1']==0)?' ':'X',		1, 0, 'C', 1);
		$pdf->Cell(5, 6, ($row['J2']==0)?' ':'X',		1, 0, 'C', 1);
	//Go to next row
	$y_axis = $y_axis + $row_height;
	$i = $i + 1;
}

//Create file
$pdf->Output();
?>