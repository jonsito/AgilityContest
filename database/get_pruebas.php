<?php
	require_once("DBConnection.php");
	// evaluate offset and row count for query
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$rows = isset($_GET['rows']) ? intval($_GET['rows']) : 20;
	$sort = isset($_GET['sort']) ? strval($_GET['sort']) : 'Nombre';
	$order = isset($_GET['order']) ? strval($_GET['order']) : 'ASC';
	$search =  isset($_GET['where']) ? strval($_GET['where']) : '';
	$closed = isset($_GET['cerrada'])? intval($_GET['where']) : 0;
	$where = '';
	if ($search!=='') {
		if ($closed==0)" WHERE (
			( (Nombre LIKE '%$search%') OR ( Club LIKE '%$search%') OR ( Ubicacion LIKE '%$search%' ) ) 
			AND ( Cerrada = 0 )
			) ";
		else $where= " WHERE ( 
			(Nombre LIKE '%$search%') OR ( Club LIKE '%$search%') OR ( Ubicacion LIKE '%$search%' ) 
			) ";
	} else {
		if ($closed==0) $where = " WHERE ( Cerrada = 0 ) ";
		else $where="";
	}
	$offset = ($page-1)*$rows;
	$result = array();
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute first query to know how many elements
	$rs=$conn->query("SELECT count(*) FROM Pruebas $where");
	$row=$rs->fetch_array();
	$result["total"] = $row[0];
	// second query to retrieve $rows starting at $offset
	$rs=$conn->query("SELECT * FROM Pruebas $where ORDER BY $sort $order LIMIT $offset,$rows");
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