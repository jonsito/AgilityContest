<?php
	// retrieve the list of dogs owned by given guia
	require_once("../logging.php");
	require_once("../DBConnection.php");
	// evaluate offset and row count for query
	$result = array();
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute first query to know how many elements
	$guia=strval($_GET['Guia']);
	$str="SELECT count(*) FROM Perros WHERE ( Guia = '$guia' )";
	do_log("enumerate_PerrosByGuia::(count) $str");
	$rs=$conn->query($str);
	$row=$rs->fetch_row();
	$result["total"] = $row[0];
	// second query to retrieve $rows starting at $offset
	$str="SELECT * FROM Perros WHERE ( Guia ='$guia' ) ORDER BY Nombre ASC";
	do_log("enumerate_PerrosByGuia::(select) $str");
	$rs=$conn->query($str);
	// retrieve result into an array
	$items = array();
	while($row = $rs->fetch_array()){
		// utf8 encode data
		// $row["Dorsal"]    =utf8_encode( $row["Dorsal"] );
		// $row["Nombre"]    =utf8_encode( $row["Nombre"]   );
		// $row["Categoria"] =utf8_encode( $row["Categoria"] );
		// $row["Grado"]     =utf8_encode( $row["Grado"]   );
		// $row["Raza"]      =utf8_encode( $row["Raza"]   );
		// $row["LOE_RRC"]   =utf8_encode( $row["LOE_RRC"]   );
		// $row["Licencia"]  =utf8_encode( $row["Licencia"]   );
		// and store into result array
		array_push($items, $row);
	}
	$result["rows"] = $items;
	// disconnect from database
	$rs->free();
	DBConnection::closeConnection($conn);
	// and return json encoded $result variable
	echo json_encode($result);
?>