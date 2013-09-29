<?php
	require_once("../DBConnection.php");
	// evaluate offset and row count for query
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
	$sort = isset($_GET['sort']) ? strval($_GET['sort']) : 'Nombre';
	$order = isset($_GET['order']) ? strval($_GET['order']) : 'ASC';
	$offset = ($page-1)*$rows;
	$result = array();
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute first query to know how many elements
	$rs=$conn->query("SELECT count(*) FROM Guias");
	$row=$rs->fetch_array();
	$result["total"] = $row[0];
	// second query to retrieve $rows starting at $offset
	$rs=$conn->query("SELECT * FROM Guias ORDER BY $sort $order LIMIT $offset,$rows");
	// retrieve result into an array
	$items = array();
	while($row = $rs->fetch_array()){
		// utf8 encode on needed fields
		$row["Nombre"]=utf8_encode($row["Nombre"]);
		$row["Telefono"]=utf8_encode($row["Telefono"]);
		$row["Email"]=utf8_encode($row["Email"]);
		$row["Club"]=utf8_encode($row["Club"]);
		$row["Observaciones"]=utf8_encode($row["Observaciones"]);
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