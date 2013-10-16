<?php
	require_once("logging.php");
	require_once("DBConnection.php");
	
	/*
	 * Cada prueba lleva asociada 8 jornadas, que se crean automaticamente al crear la prueba
	 */
	function insertJornadas($conn,$nombre) {
		// fase 1: averiguar el ID de la prueba que tiene el nombre dado
		$sql="SELECT ID FROM Pruebas WHERE (Nombre='$nombre')";
		$rs=$conn->query($sql);
		$row=$rs->fetch_array();
		$id = $row[0];
		do_log("El id de la prueba $nombre es $id");
		// fase 2: insertar 8 jornadas numeradas de 1 a 8
		for ($n=1;$n<9;$n++) { 
			$sql ="INSERT INTO Jornadas (Prueba,Numero,Nombre,Fecha,Hora) VALUES ($id,$n,'-- Sin asignar --','2013-01-01','00:00:00')";
			$res=$conn->query($sql);
			if (!$res) {
				$msg="insertPrueba::insertJornada($n) failed $conn->error";
				do_log($msg);
				return $msg;
			}
		}
		return "";
	}
	
	function insertPrueba ($conn) {
		$msg=""; // default: no errors
		do_log("insertPrueba:: enter");
		// componemos un prepared statement
		$sql ="INSERT INTO Pruebas (Nombre,Club,Ubicacion,Triptico,Cartel,Observaciones,Cerrada)
			   VALUES(?,?,?,?,?,?,?)";
		$stmt=$conn->prepare($sql);		
		if (!$stmt) {
			$msg="insertPrueba::prepare() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		$res=$stmt->bind_param('ssssssi',$nombre,$club,$ubicacion,$triptico,$cartel,$observaciones,$cerrada);
		if (!$res) {
			$msg="insertPrueba::bind() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		
		// iniciamos los valores, chequeando su existencia
		$nombre = strval($_REQUEST['Nombre']); // unique not null pkey
		$club = strval($_REQUEST['Club']); // not null
		$ubicacion = (isset($_REQUEST['Ubicacion']))?strval($_REQUEST['Ubicacion']):null;
		$triptico = (isset($_REQUEST['Triptico']))?strval($_REQUEST['Triptico']):null;
		$cartel = (isset($_REQUEST['Cartel']))?strval($_REQUEST['Cartel']):null;
		$observaciones = (isset($_REQUEST['Observaciones']))?strval($_REQUEST['Observaciones']):null;
		$cerrada = (isset($_REQUEST['Cerrada']))?intval($_REQUEST['Cerrada']):0;
		do_log("insertPrueba:: retrieved data from web client");
		do_log("Nombre: $nombre Club: $club Ubicacion: $ubicacion Observaciones: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		do_log("insertadas $stmt->affected_rows filas");
		if (!$res) {
			$msg="insertPrueba:: Error: $conn->error";
			do_log($msg);
		}
		else  do_log("execute resulted: $res");
		$stmt->close();
		$msg=insertJornadas($conn,$nombre);
		return $msg;
	}
	
	function updatePrueba($conn) {
		$msg="";
		do_log("updatePrueba:: enter");
		
		// componemos un prepared statement
		$sql ="UPDATE Pruebas 
				SET Nombre=? , Club=? , Ubicacion=? , Triptico=? , Cartel=?, Observaciones=?, Cerrada=? 
				WHERE ( ID=? )";
		$stmt=$conn->prepare($sql);
		if (!$stmt) {
			$msg="updatePrueba::prepare() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		$res=$stmt->bind_param('ssssssii',$nombre,$club,$ubicacion,$triptico,$cartel,$observaciones,$cerrada,$id);
		if (!$res) {
			$msg="updatePrueba::bind() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		
		// iniciamos los valores, chequeando su existencia
		$nombre = strval($_REQUEST['Nombre']); // not null
		$id = strval($_REQUEST['ID']); // primary key
		$club = strval($_REQUEST['Club']); // not null
		$ubicacion = (isset($_REQUEST['Ubicacion']))?strval($_REQUEST['Ubicacion']):null;
		$triptico = (isset($_REQUEST['Triptico']))?strval($_REQUEST['Triptico']):null;
		$cartel = (isset($_REQUEST['Cartel']))?strval($_REQUEST['Cartel']):null;
		$observaciones = (isset($_REQUEST['Observaciones']))?strval($_REQUEST['Observaciones']):null;
		$cerrada = (isset($_REQUEST['Cerrada']))?intval($_REQUEST['Cerrada']):0;
		do_log("updatePrueba:: retrieved data from client");
		do_log("Nombre: $nombre Club: $club Ubicacion: $ubicacion Observaciones: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		do_log("updatePrueba:: actualizadas $stmt->affected_rows filas");
		if (!$res) {
			$msg="updatePrueba:: Error: $conn->error";
			do_log($msg);
		} else do_log("updatePrueba::execute() resulted: $res");
		$stmt->close();
		return $msg;
	}
	
	function deletePrueba($conn,$id) {
		$msg="";
		do_log("deletePrueba:: enter");
		$res= $conn->query("DELETE FROM Pruebas WHERE (ID=$id)");
		if (!$res) {
			$msg="deletePrueba::query(delete) Error: $conn->error";
			do_log($msg);
		} else do_log("deletePrueba:: execute() resulted: $res");
		return $msg;
	}
	
	// connect database
	$conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
	if (!$conn) {
		$str='pruebaFunctions() cannot contact database.';
		do_log($str);
		echo json_encode(array('errorMsg'=>$str));
		return;
	}
	
	if (! isset($_GET['Operation'])) {
		$str='Call pruebaFunctions() with no operation declared.';
		do_log($str);
		echo json_encode(array('errorMsg'=>$str));
		DBConnection::closeConnection($conn);
		return;
	}
	$oper = $_GET['Operation'];
	if($oper==='insert') {
		$result=insertPrueba($conn);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else 				echo json_encode(array('errorMsg'=>$result));
	}
	else if($oper==='update') {
		$result= updatePrueba($conn);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else 				echo json_encode(array('errorMsg'=>$result));
	}
	else if($oper==='delete') {
		$result= deletePrueba($conn,intval($_GET['ID']));
		if ($result==="") 	echo json_encode(array('success'=>true));
		else				echo json_encode(array('msg'=>$result));
	}
	else {
		$result="PruebaFunctions:: Invalid operation requested: $oper";
		do_log($result);
		json_encode(array('errorMsg'=>$result));
	}
	DBConnection::closeConnection($conn);
?>