<?php
	// retrieve the list of dogs owned by given guia
	require_once("logging.php");
	require_once("classes/DBConnection.php");
	do_log("enumerate_JornadasAbiertasByPrueba::enter()");
	$result = array();
	$items = array();
	// evaluate search terms
	$q=isset($_REQUEST['q'])?strval($_REQUEST['q']):"";
	$like=")";
	if ($q!=="") $like = " AND ( (Nombre LIKE '%$q%') OR (Numero LIKE '%$q%') ) )";
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute first query to know how many elements
	$prueba=intval($_REQUEST['Prueba']);
	$str="SELECT count(*) FROM Jornadas WHERE ( ( Prueba = $prueba ) AND ( Cerrada=0) $like";
	// do_log("enumerate_jornadasAbiertasByPrueba::(count) $str");
	$rs=$conn->query($str);
	if (!$rs) {
		$err="enumerate_jornadasAbiertasByPruebas::select(count) error ".$conn->error;
		do_log($err);
		do_log("Query was:\n$str");
		DBConnection::closeConnection($conn);
		echo json_encode(array('errorMsg'=>$err));
		return;
	}
	$row=$rs->fetch_row();
	$result["total"] = $row[0];
	if ($result["total"]>0) {
		$str="SELECT * FROM Jornadas WHERE ( ( Prueba = $prueba ) AND ( Cerrada=0 ) $like ORDER BY Numero ASC";
		// do_log("enumerate_jornadasAbiertasByPrueba::(select) $str");
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