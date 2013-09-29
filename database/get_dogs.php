<?php
	require_once("logging.php");
	require_once("DBConnection.php");
	// evaluate offset and row count for query
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
	$sort = isset($_GET['sort']) ? strval($_GET['sort']) : 'Dorsal';
	$order = isset($_GET['order']) ? strval($_GET['order']) : 'ASC';
	$search =  isset($_GET['where']) ? strval($_GET['where']) : '';
	$where = ' ';
	if ($search!=='') $where="AND ( (Perros.Nombre LIKE '%$search%') OR ( Guia LIKE '%$search%') OR (Guias.Club LIKE '%$search%') )";  
	$offset = ($page-1)*$rows;
	$result = array();
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute first query to know how many elements
	$rs=$conn->query("SELECT count(*) FROM Perros,Guias WHERE ( Perros.Guia = Guias.Nombre) $where");
	$row=$rs->fetch_array();
	$result["total"] = $row[0];
	// second query to retrieve $rows starting at $offset
	$str="SELECT Dorsal,Perros.Nombre,Raza,LOE_RRC,Licencia,Categoria,Guia,Grado,Club
			FROM Perros,Guias
			WHERE ( Perros.Guia = Guias.Nombre) $where 
			ORDER BY $sort $order LIMIT $offset,$rows";
	do_log("get_dogs:: query string is $str");
	$rs=$conn->query($str);
	if (!$rs) {
		$err="get_dogs::query() error $conn->error";
		do_log($err);
		echo json_encode(array('errorMsg'=>$err));
		DBConnection::closeConnection($conn);
		return;
	} 
	// retrieve result into an array
	$items = array();
	while($row = $rs->fetch_array()){
		// utf8 encode on needed fields
		// $row["Nombre"]=utf8_encode($row["Nombre"]);
		// $row["Raza"]=utf8_encode($row["Raza"]);
		// $row["Guia"]=utf8_encode($row["Guia"]);
		// store data into result array
		array_push($items, $row);
	}
	$result["rows"] = $items;
	// disconnect from database
	$rs->free();
	DBConnection::closeConnection($conn);
	// and return json encoded $result variable
	echo json_encode($result);
?>