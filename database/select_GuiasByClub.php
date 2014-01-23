<?php
	// retrieve the list of dogs owned by given guia
	require_once("logging.php");
	require_once("classes/DBConnection.php");
	// evaluate offset and row count for query
	$result = array();
	$items = array();
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute first query to know how many elements
	$club=strval($_GET['Club']);
	$str="SELECT count(*) FROM Guias WHERE ( Club = '$club' )";
	do_log("select_GuiasByClub::(count) $str");
	$rs=$conn->query($str);
	$row=$rs->fetch_row();
	$result["total"] = $row[0];
	if ($result["total"]>0) {
		$str="SELECT * FROM Guias WHERE ( Club ='$club' ) ORDER BY Nombre ASC";
		do_log("select_GuiasByClub::(select) $str");
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