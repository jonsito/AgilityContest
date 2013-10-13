<?php
	require_once("DBConnection.php");
	// evaluate offset and row count for query
	$like = isset($_GET['q']) ? " WHERE ( (Nombre LIKE '".$_GET['q']."%') OR (Club LIKE '".$_GET['q']."%'))" : "";
	$result = array();
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute first query to know how many elements
	$rs=$conn->query("SELECT count(*) FROM Guias ".$like);
	$row=$rs->fetch_row();
	$result["total"] = $row[0];
	// second query to retrieve $rows starting at $offset
	$rs=$conn->query("SELECT Nombre,Club FROM Guias ".$like." ORDER BY Club,Nombre");
	// retrieve result into an array
	$items = array();
	while($row = $rs->fetch_array()){
		// utf8 encode data
		// $row["Nombre"] =utf8_encode( $row["Nombre"] );
		// $row["Club"]   =utf8_encode( $row["Club"]   );
		// and store into result array
		array_push($items, $row);
	}
	$result["rows"] = $items;
	// disconnect from database
	$rs->free();
	DBConnection::closeConnection($conn);
	// and return json encoded $result variable
	echo json_encode($result);
?>