<?php
	require_once("../DBConnection.php");
	// evaluate offset and row count for query
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'Dorsal';
	$order = isset($_POST['order']) ? strval($_POST['order']) : 'ASC';
	$offset = ($page-1)*$rows;
	$result = array();
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute first query to know how many elements
	$rs=$conn->query("SELECT count(*) FROM Perros");
	$row=$rs->fetch_array();
	$result["total"] = $row[0];
	// second query to retrieve $rows starting at $offset
	$rs=$conn->query("SELECT * FROM Perros ORDER BY $sort $order LIMIT $offset,$rows");
	// retrieve result into an array
	$items = array();
	while($row = $rs->fetch_array()){
		// utf8 encode on needed fields
		$row["Nombre"]=utf8_encode($row["Nombre"]);
		$row["Raza"]=utf8_encode($row["Raza"]);
		$row["Guia"]=utf8_encode($row["Guia"]);
		// store data into result array
		array_push($items, $row);
	}
	$result["rows"] = $items;
	// disconnect from database
	$rs->free();
	DBConnection::closeConnection($conn);
	// and return json encoded $result variable
	echo json_encode($result);
?>