<?php
	require_once("DBConnection.php");
	require_once("logging.php");
	do_log("select_InscritosByPrueba():: enter");
	// evaluate offset and row count for query
	$id = intval($_GET['ID']); // pruebaID: not null
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$rows = isset($_GET['rows']) ? intval($_GET['rows']) : 20;
	$sort = isset($_GET['sort']) ? strval($_GET['sort']) : 'Club';
	$order = isset($_GET['order']) ? strval($_GET['order']) : 'ASC';
	$search =  isset($_GET['where']) ? strval($_GET['where']) : '';
	$extra = ')';
	if ($search!=='') $extra=" AND ( (PerroGuiaClub.Nombre LIKE '%$search%') OR ( Club LIKE '%$search%') OR ( Guia LIKE '%$search%' ) ) )";
	$offset = ($page-1)*$rows;
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// FASE 1: obtener lista de perros inscritos con sus datos
	$str="SELECT Numero , Inscripciones.Dorsal AS Dorsal , PerroGuiaClub.Nombre AS Nombre, 
			Categoria , Grado , Celo , Guia , Club , Equipo , Observaciones , Pagado
		  FROM Inscripciones,PerroGuiaClub,Jornadas
		  WHERE ( ( Inscripciones.Dorsal = PerroGuiaClub.Dorsal) 
		  			AND ( Inscripciones.Jornada = Jornadas.ID ) 
		  			AND ( Prueba= $id ) 
				$extra "; // a single ')' or name search criterion
	do_log("select_InscritosByPrueba() query string is \n$str");
	$rs=$conn->query($str);
	if (!$rs) {
		$err="select_InscritosByPrueba::select( ) error $conn->error";
		do_log($err);
		echo json_encode(array('errorMsg'=>$err));
		DBConnection::closeConnection($conn);
		return;
	}
	
	// Fase 2: la tabla de resultados a devolver
	$result = array(); // result { total(numberofrows), data(arrayofrows)
	$count = 0;
	$dorsales = array();
	while($row = $rs->fetch_array()){
		do_log("select_InscritosByPrueba::select() examine dorsal ".$row['Dorsal']);
		if (!isset($dorsales[$row['Dorsal']])) {
			$count++;
			$dorsales[$row['Dorsal']]= array(
					'Dorsal' => $row['Dorsal'],
					'Nombre' => $row['Nombre'],
					'Categoria' => $row['Categoria'],
					'Grado' => $row['Grado'],
					'Celo' => $row['Celo'],
					'Guia' => $row['Guia'],
					'Club' => $row['Club'],
					'Equipo' => $row['Equipo'],
					'Observaciones' => $row['Observaciones'],
					'Pagado' => $row['Pagado'],
					'J1' => 0,
					'J2' => 0,
					'J3' => 0,
					'J4' => 0,
					'J5' => 0,
					'J6' => 0,
					'J7' => 0,
					'J8' => 0
				);
		} // create row if not exists
		// store wich jornada is subscribed into array
		$jornada=$row['Numero'];
		$dorsales[$row['Dorsal']]["J$jornada"]=1;
	}
	$rs->free(); 
	DBConnection::closeConnection($conn);
	// OK: compose result to be returned
	$items=array();
	foreach($dorsales as $item) array_push($items,$item);
	$result['total']=$count; // number of rows retrieved
	$result['rows']=$items;
	// and return json encoded $result variable
	echo json_encode($result);
?>