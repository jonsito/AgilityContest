<?php	
	require_once("../DBConnection.php");
	$resul=array();
	// evaluate offset and row count for query
	$like = isset($_GET['q']) ? " WHERE Categoria LIKE '".$_GET['q']."%'" : "";
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute first query to know how many elements
	$rs=$conn->query("SELECT count(*) FROM Categorias_Perro $like");
	$row=$rs->fetch_row();
	$result["total"] = $row[0];
	// query to retrieve table data
	$rs=$conn->query("SELECT Categoria,Observaciones FROM Categorias_Perro ".$like." ORDER BY Categoria");
	// retrieve result into an array
	$items = array();
	while($row = $rs->fetch_array()){
		// utf8 encode data
		$row["Categoria"] = utf8_encode( $row["Categoria"] );
		$row["Observaciones"]   = utf8_encode( $row["Observaciones"]   );
		if ($row["Categoria"]==='-') { $row["selected"]=true; $row[2]=true;}
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