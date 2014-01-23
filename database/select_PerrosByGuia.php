<?php
	// retrieve the list of dogs owned by given guia
	require_once("logging.php");
	require_once("classes/DBConnection.php");
	do_log("select_PerrosByGuia::enter");
	// evaluate offset and row count for query
	$result = array();
	$items = array();
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
	if($result["total"]>0) {
		$str="SELECT * FROM Perros WHERE ( Guia ='$guia' ) ORDER BY Nombre ASC";
		do_log("select_PerrosByGuia::(select) $str");
		$rs=$conn->query($str);
		// retrieve result into an array
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
	}
	// disconnect from database
	$rs->free();
	DBConnection::closeConnection($conn);
	// and return json encoded $result variable
	$result["rows"] = $items;
	echo json_encode($result);
?>