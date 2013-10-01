<?php
	// retrieve the list of dogs owned by given guia
	require_once("logging.php");
	require_once("DBConnection.php");
	do_log("selectJornadasByPrueba::enter()");
	// evaluate offset and row count for query
	$result = array();
	$items = array();
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute first query to know how many elements
	$prueba=strval($_GET['Prueba']);
	$str="SELECT count(*) FROM Jornadas WHERE ( Prueba = '$prueba' )";
	do_log("select_JornadasByPrueba::(count) $str");
	$rs=$conn->query($str);
	if (!$rs) {
		$err="select_jornadasByPruebas::select() error $conn->error";
		do_log($err);
		do_log("Query was:\n$str");
		DBConnection::closeConnection($conn);
		echo json_encode(array('errorMsg'=>$err));
		return;
	}
	$row=$rs->fetch_row();
	$result["total"] = $row[0];
	if ($result["total"]>0) {
		$str="SELECT * FROM Jornadas WHERE ( Prueba = '$prueba' ) ORDER BY ID ASC";
		do_log("select_JornadasByPrueba::(select) $str");
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