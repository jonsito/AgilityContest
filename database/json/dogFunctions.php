<?php
	require_once("../DBConnection.php");
	ini_set("log_errors",1);
	ini_set("error_log","/tmp/json.log");
	
	function do_log($str) { error_log($str); }
	
	function insertDog ($conn) {
		do_log("insert:: enter");
		// componemos un prepared statement
		$sql ="INSERT INTO Perros (Nombre,Raza,LOE_RRC,Licencia,Categoria,Grado,Guia)
			   VALUES(?,?,?,?,?,?,?)";
		$stmt=$conn->prepare($sql);
		$res=$stmt->bind_param('sssssss',$nombre,$raza,$loe_rrc,$licencia,$categoria,$grado,$guia);
		if (!$res) {
			do_log("insert::prepare() failed $conn->error");
			return FALSE;
		}
		
		// iniciamos los valores, chequeando su existencia
		$nombre = $_REQUEST['Nombre'];
		$raza = (isset($_REQUEST['Raza']))?$_REQUEST['Raza']:null;
		$loe_rrc = (isset($_REQUEST['LOE_RRC']))?$_REQUEST['LOE_RRC']:null;
		$licencia = (isset($_REQUEST['Licencia']))?$_REQUEST['Licencia']:null;
		$categoria = (isset($_REQUEST['Categoria']))?$_REQUEST['Categoria']:null;
		$grado = (isset($_REQUEST['Grado']))?$_REQUEST['Grado']:null;
		$guia = (isset($_REQUEST['Guia']))?$_REQUEST['Guia']:null;
		do_log("insert:: retrieved data from client");
		do_log("Nombre: $nombre Raza: $raza LOE: $loe_rrc Categoria: $categoria Grado: $grado Guia: $guia");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		do_log("insertadas $stmt->affected_rows filas");
		do_log("Error: $conn->error");
		do_log("execute resulted: $res");
		
		$stmt->close();
		return $res;
	}
	
	function updateDog($conn,$id) {
		do_log("update:: enter");
		
		// componemos un prepared statement
		$sql ="UPDATE Perros SET Nombre=? , Raza=? , LOE_RRC=? , Licencia=? , Categoria=? , Grado=? , Guia=?
		       WHERE ( Dorsal=? )";
		$stmt=$conn->prepare($sql);
		if (!$stmt) {
			do_log("update::prepare() failed $conn->error");
			return FALSE;
		}
		$res=$stmt->bind_param('sssssssi',$nombre,$raza,$loe_rrc,$licencia,$categoria,$grado,$guia,$dorsal);
		if (!$res) {
			do_log("update::bind() failed $conn->error");
			return FALSE;
		}
		
		// iniciamos los valores, chequeando su existencia
		$nombre = $_REQUEST['Nombre'];
		$raza = (isset($_REQUEST['Raza']))?strval($_REQUEST['Raza']):null;
		$loe_rrc = (isset($_REQUEST['LOE_RRC']))?strval($_REQUEST['LOE_RRC']):null;
		$licencia = (isset($_REQUEST['Licencia']))?strval($_REQUEST['Licencia']):null;
		$categoria = (isset($_REQUEST['Categoria']))?strval($_REQUEST['Categoria']):null;
		$grado = (isset($_REQUEST['Grado']))?strval($_REQUEST['Grado']):null;
		$guia = (isset($_REQUEST['Guia']))?strval($_REQUEST['Guia']):null;
		$dorsal = $id;

		do_log("update:: retrieved data from client");
		do_log("Dorsal: $dorsal Nombre: $nombre Raza: $raza LOE: $loe_rrc Categoria: $categoria Grado: $grado Guia: $guia");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		do_log("update:: actualizadas $stmt->affected_rows filas");
		do_log("update:: Error: $conn->error");
		do_log("update::execute() resulted: $res");
		$stmt->close();
		return $res;
	}
	
	function deleteDog($conn,$dorsal) {
		do_log("delete:: enter");
		return $conn->query("DELETE FROM Perros WHERE (Dorsal=$dorsal)");
		do_log("delete:: actualizadas $stmt->affected_rows filas");
		do_log("delete:: Error: $conn->error");
		do_log("delete:: execute() resulted: $res");
	}

	// connect database
	$conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
	if (!$conn) {
		$str='dogFunctions() cannot contact database.';
		do_log($str);
		echo json_encode(array('msg'=>$str));
		return;
	}
	
	if (! isset($_GET['operation'])) {
		$str='Call dogFunctions() with no operation declared.';
		do_log($str);
		echo json_encode(array('msg'=>$str));
		DBConnection::closeConnection($conn);
		return;
	}
	$oper = $_GET['operation'];
	if($oper==='insert') {
		$result=insertDog($conn);
		if ($result) 	echo json_encode(array('success'=>true));
		else {
			$str='dogFunctions(insert) Some errors occured.';
			echo json_encode(array('msg'=>$str));
		}
		DBConnection::closeConnection($conn);
		return;	
	}
	if (! isset($_GET['Dorsal'])) {
		$str="Call dogFunctions(update/delete) without pkey declared.";
		do_log($str);
		echo json_encode(array('msg'=>$str));
		DBConnection::closeConnection($conn);
		return;
	}
	if($oper==='update') {
		$result= updateDog($conn,intval($_GET['Dorsal']));
		if ($result) 	echo json_encode(array('success'=>true));
		else {
			$str='dogFunctions(update) Some errors occured.';
			do_log($str);
			echo json_encode(array('msg'=>$str));
		}
		DBConnection::closeConnection($conn);
		return;
	}
	if($oper==='delete') {
		$result= deleteDog($conn,$_GET['Dorsal']);
		if ($result) 	echo json_encode(array('success'=>true));
		else {
			$str='dogFunctions(delete) Some errors occured.';
			echo json_encode(array('msg'=>$str));
		}
		DBConnection::closeConnection($conn);
		return;
	}
	do_log("Invalid operation requested: $oper");
	DBConnection::closeConnection($conn);
	echo json_encode(array('msg'=>'Invalid operation in dogFunctions() call'));
?>