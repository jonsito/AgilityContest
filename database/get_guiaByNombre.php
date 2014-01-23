<?php
	require_once("logging.php");
	require_once("classes/DBConnection.php");
	// evaluate offset and row count for query
	$nombre = isset($_GET['Nombre']) ? strval($_GET['Nombre']) : '';
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// second query to retrieve $rows starting at $offset
	$str="SELECT * FROM Guias WHERE ( Nombre = '$nombre' )";
	do_log("get_guiaByNombre:: query string is $str");
	$rs=$conn->query($str);
	if (!$rs) {
		$err="get_guiaByNombre::query() error ".$conn->error;
		do_log($err);
		echo json_encode(array('errorMsg'=>$err));
		DBConnection::closeConnection($conn);
		return;
	} 
	// retrieve result into an array
	$result = array();
	while($row = $rs->fetch_array()){
		$row['Operation']='update'; // dirty trick to ensure that form operation is fixed
		array_push($result, $row);
	}
	// disconnect from database
	$rs->free();
	DBConnection::closeConnection($conn);
	// and return json encoded first (and only) $result
	echo json_encode($result[0]);
?>