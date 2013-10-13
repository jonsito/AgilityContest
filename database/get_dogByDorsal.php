<?php
	require_once("logging.php");
	require_once("DBConnection.php");
	// evaluate offset and row count for query
	$dorsal = isset($_GET['Dorsal']) ? intval($_GET['Dorsal']) : 0;
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// second query to retrieve $rows starting at $offset
	$str="SELECT * FROM Perros WHERE ( Dorsal = $dorsal )";
	do_log("get_dogsByDorsal:: query string is $str");
	$rs=$conn->query($str);
	if (!$rs) {
		$err="get_dogsByDorsal::query() error $conn->error";
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