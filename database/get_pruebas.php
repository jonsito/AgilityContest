<?php
	require_once("DBConnection.php");
	require_once("logging.php");
	do_log("get_pruebas():: enter");
	// evaluate offset and row count for query
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$rows = isset($_GET['rows']) ? intval($_GET['rows']) : 20;
	$sort = isset($_GET['sort']) ? strval($_GET['sort']) : 'Nombre';
	$order = isset($_GET['order']) ? strval($_GET['order']) : 'ASC';
	$search =  isset($_GET['where']) ? strval($_GET['where']) : '';
	do_log("closed is: ".$_GET['closed']);
	$closed = isset($_GET['closed'])? intval($_GET['closed']) : 0;
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
	if (!$rs) {
		$err="get_pruebas::select( count * ) error $conn->error";
		do_log($err);
		echo json_encode(array('errorMsg'=>$err));
		DBConnection::closeConnection($conn);
		return;
	}
	$row=$rs->fetch_array();
	$result["total"] = $row[0];
	// second query to retrieve $rows starting at $offset
	$str="SELECT * FROM Pruebas $where ORDER BY $sort $order LIMIT $offset,$rows";
	do_log("get_pruebas() query is: \n$str");
	$rs=$conn->query($str);
	if (!$rs) {
		$err="get_pruebas::select query() error $conn->error";
		do_log($err);
		do_log("Select query was:\n$str");
		echo json_encode(array('errorMsg'=>$err));
		DBConnection::closeConnection($conn);
		return;
	}
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