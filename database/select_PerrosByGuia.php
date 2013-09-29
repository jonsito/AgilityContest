<?php
	// retrieve the list of dogs owned by given guia
	require_once("logging.php");
	require_once("DBConnection.php");
	// evaluate offset and row count for query
	$result = array();
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute first query to know how many elements
	$guia=strval($_GET['Guia']);
	$str="SELECT count(*) FROM Perros WHERE ( Guia = '$guia' )";
	do_log("select_PerrosByGuia::(count) $str");
	$rs=$conn->query($str);
	$row=$rs->fetch_row();
	$result["total"] = $row[0];
	// second query to retrieve $rows starting at $offset
	$str="SELECT * FROM Perros WHERE ( Guia ='$guia' ) ORDER BY Nombre ASC";
	do_log("select_PerrosByGuia::(select) $str");
	$rs=$conn->query($str);
	// retrieve result into an array
	$items = array();
	while($row = $rs->fetch_array()){
		array_push($items, $row);
	}
	$result["rows"] = $items;
	// disconnect from database
	$rs->free();
	DBConnection::closeConnection($conn);
	// and return json encoded $result variable
	echo json_encode($result);
?>