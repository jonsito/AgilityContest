<?php
	require_once("logging.php");
	require_once("DBConnection.php");
	// evaluate offset and row count for query
	$id = isset($_GET['ID']) ? intval($_GET['ID']) : 0;
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// second query to retrieve $rows starting at $offset
	$str="SELECT * FROM Pruebas WHERE ( ID = '$id' )";
	do_log("get_pruebaByID:: query string is $str");
	$rs=$conn->query($str);
	if (!$rs) {
		$err="get_pruebaByID::query() error ".$conn->error;
		do_log($err);
		echo json_encode(array('errorMsg'=>$err));
		DBConnection::closeConnection($conn);
		return;
	} 
	// retrieve result into an array
	$result = array();
	while($row = $rs->fetch_array()){ // should only be one element
		array_push($result, $row);
	}
	// disconnect from database
	$rs->free();
	DBConnection::closeConnection($conn);
	// and return json encoded first (and only) $result
	echo json_encode($result[0]);
?>