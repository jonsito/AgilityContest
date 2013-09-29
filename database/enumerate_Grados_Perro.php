<?php	
	require_once("DBConnection.php");
	$resul=array();
	// evaluate offset and row count for query
	$like = isset($_GET['q']) ? " WHERE Grado LIKE '".$_GET['q']."%'" : "";
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute first query to know how many elements
	$rs=$conn->query("SELECT count(*) FROM Grados_Perro $like");
	$row=$rs->fetch_row();
	$result["total"] = $row[0];
	// query to retrieve table data
	$rs=$conn->query("SELECT Grado,Comentarios FROM Grados_Perro ".$like." ORDER BY Grado");
	// retrieve result into an array
	$items = array();
	while($row = $rs->fetch_array()){
		// utf8 encode data
		// $row["Grado"] = utf8_encode( $row["Grado"] );
		// $row["Comentarios"]   = utf8_encode( $row["Comentarios"]   );
		if ($row["Grado"]==='-') $row["selected"]=true;
		// and store into result array
		array_push($items, $row);
	}
	// disconnect from database
	$rs->free();
	DBConnection::closeConnection($conn);
	// and return json encoded $items variable
	$result["rows"]=$items;
	echo json_encode($items); // ALERT: in comboboxes DO NOT RETURN size+Table, just table elements
?>