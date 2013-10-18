<?php
	require_once("DBConnection.php");
	require_once("logging.php");
	// evaluate offset and row count for query
	$like = isset($_GET['q']) ? " WHERE Provincia LIKE '".$_GET['q']."%'" : "";
	$result = array();
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute first query to know how many elements
	$rs=$conn->query("SELECT count(*) FROM Provincias ".$like);
	$row=$rs->fetch_row();
	$result["total"] = $row[0];
	// second query to retrieve $rows starting at $offset
	$str="SELECT * FROM Provincias ".$like." ORDER BY Provincia ASC";
	do_log("enumerate_provincias::query() $str");
	$rs=$conn->query($str);
	if (!$rs) {
		$str="enumerate_provincias::query() failed: ".$conn->error;
		do_log($str);
		DBConnection::closeConnection($conn);
		return json_encode(array('errorMsg'=>$str));
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