<?php
	require_once("logging.php");
	require_once("DBConnection.php");
	
	function insertGuia ($conn) {
		$msg=""; // default: no errors
		do_log("insertGuia:: enter");
		// componemos un prepared statement
		$sql ="INSERT INTO Guias (Nombre,Telefono,Email,Club,Observaciones)
			   VALUES(?,?,?,?,?)";
		$stmt=$conn->prepare($sql);
		$res=$stmt->bind_param('sssss',$nombre,$telefono,$email,$club,$observaciones);
		if (!$res) {
			$msg="insertGuia::prepare() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		
		// iniciamos los valores, chequeando su existencia
		$nombre = $_REQUEST['Nombre'];
		$telefono = (isset($_REQUEST['Telefono']))?$_REQUEST['Telefono']:null;
		$email = (isset($_REQUEST['Email']))?$_REQUEST['Email']:null;
		$club = (isset($_REQUEST['Club']))?$_REQUEST['Club']:null;
		$observaciones = (isset($_REQUEST['Observaciones']))?$_REQUEST['Observaciones']:null;
		do_log("insertGuia:: retrieved data from client");
		do_log("Nombre: $nombre Telefono: $telefono Club: $club Observaciones: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		do_log("insertadas $stmt->affected_rows filas");
		if (!$res) {
			$msg="insertGuia:: Error: $conn->error";
			do_log($msg);
		}
		else  do_log("execute resulted: $res");
		
		$stmt->close();
		return $msg;
	}
	
	function updateGuia($conn) {
		$msg="";
		do_log("updateGuia:: enter");
		
		// componemos un prepared statement
		$sql ="UPDATE Guias SET Nombre=? , Telefono=? , Email=? , Club=? , Observaciones=? WHERE ( Nombre=? )";
		$stmt=$conn->prepare($sql);
		if (!$stmt) {
			$msg="updateGuia::prepare() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		$res=$stmt->bind_param('ssssss',$nombre,$telefono,$email,$club,$observaciones,$viejo);
		if (!$res) {
			$msg="updateGuia::bind() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		
		// iniciamos los valores, chequeando su existencia
		$nombre = $_REQUEST['Nombre']; // pkey cannot be null
		$viejo = strval($_REQUEST['Viejo']);
		$telefono = (isset($_REQUEST['Telefono']))?$_REQUEST['Telefono']:null;
		$email = (isset($_REQUEST['Email']))?$_REQUEST['Email']:null;
		$club = (isset($_REQUEST['Club']))?$_REQUEST['Club']:null;
		$observaciones = (isset($_REQUEST['Observaciones']))?$_REQUEST['Observaciones']:null;

		do_log("updateGuia:: retrieved data from client");
		do_log("Nombre: $nombre Telefono: $telefono Club: $club Observaciones: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		do_log("updateGuia:: actualizadas $stmt->affected_rows filas");
		if (!$res) {
			$msg="updateGuia:: Error: $conn->error";
			do_log($msg);
		} else do_log("updateGuia::execute() resulted: $res");
		$stmt->close();
		return $msg;
	}
	
	function deleteGuia($conn,$nombre) {
		$msg="";
		do_log("deleteGuia:: enter");
		$res= $conn->query("DELETE FROM Guias WHERE (Nombre='$nombre')");
		if (!$res) {
			$msg="deleteGuia::query(delete) Error: $conn->error";
			do_log($msg);
		} else do_log("deleteGuia:: execute() resulted: $res");
		return $msg;
	}

	function orphanPerroFromGuia($conn,$dorsal) {
		$msg="";
		do_log("orphanPerroFromGuia:: enter");
		$res= $conn->query("UPDATE Perros SET Guia=' Sin Asignar' WHERE (Dorsal='$dorsal')");
		if (!$res) {
			$msg="orphanPerroFromGuia::query(delete) Error: $conn->error";
			do_log($msg);
		} else do_log("deleteGuia:: execute() resulted: $res");
		return $msg;
	}
	
	// connect database
	$conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
	if (!$conn) {
		$str='guiaFunctions() cannot contact database.';
		do_log($str);
		echo json_encode(array('errorMsg'=>$str));
		return;
	}
	
	if (! isset($_GET['operation'])) {
		$str='Call guiaFunctions() with no operation declared.';
		do_log($str);
		echo json_encode(array('errorMsg'=>$str));
		DBConnection::closeConnection($conn);
		return;
	}
	$oper = $_GET['operation'];
	if($oper==='insert') {
		$result=insertGuia($conn);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else 				echo json_encode(array('errorMsg'=>$result));
	}
	else if($oper==='update') {
		$result= updateGuia($conn);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else 				echo json_encode(array('errorMsg'=>$result));
	}
	else if($oper==='delete') {
		$result= deleteGuia($conn,strval($_GET['Nombre']));
		if ($result==="") 	echo json_encode(array('success'=>true));
		else				echo json_encode(array('msg'=>$result));
	}
	else if($oper==='orphan') {
		$result= orphanPerroFromGuia($conn,intval($_GET['Dorsal']));
		if ($result==="") 	echo json_encode(array('success'=>true));
		else				echo json_encode(array('msg'=>$result));
	}
	else {
		$result="guiaFunctions:: Invalid operation requested: $oper";
		do_log($result);
		json_encode(array('errorMsg'=>$result));
	}
	DBConnection::closeConnection($conn);
?>