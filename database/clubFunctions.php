<?php
	require_once("logging.php");
	require_once("DBConnection.php");
	
	function insertClub ($conn) {
		$msg=""; // default: no errors
		do_log("insertClub:: enter");
		// componemos un prepared statement
		$sql ="INSERT INTO Clubes (Nombre,Direccion1,Direccion2,Provincia,Contacto1,Contacto2,Contacto3,GPS,
				Web,Email,Facebook,Google,Twitter,Observaciones,Baja)
			   VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$stmt=$conn->prepare($sql);
		$res=$stmt->bind_param('sssssssssssssss',$nombre,$direccion1,$direccion2,$provincia,$contacto1,$contacto2,$contacto3,$gps,
				$web,$email,$facebook,$google,$twitter,$observaciones,$baja);
		if (!$res) {
			$msg="insertClub::prepare() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		
		// iniciamos los valores, chequeando su existencia
		$nombre = $_REQUEST['Nombre'];
		$direccion1 = (isset($_REQUEST['Direccion1']))?strval($_REQUEST['Direccion1']):null;
		$direccion2 = (isset($_REQUEST['Direccion2']))?strval($_REQUEST['Direccion2']):null;
		$provincia	= (isset($_REQUEST['Provincia']))?strval($_REQUEST['Provincia']):null;
		$contacto1	= (isset($_REQUEST['Contacto1']))?strval($_REQUEST['Contacto1']):null;
		$contacto2	= (isset($_REQUEST['Contacto2']))?strval($_REQUEST['Contacto2']):null;
		$contacto3	= (isset($_REQUEST['Contacto3']))?strval($_REQUEST['Contacto3']):null;
		$gps		= (isset($_REQUEST['GPS']))?strval($_REQUEST['GPS']):null;
		$web		= (isset($_REQUEST['Web']))?strval($_REQUEST['Web']):null;
		$email		= (isset($_REQUEST['Email']))?strval($_REQUEST['Email']):null;
		$facebook	= (isset($_REQUEST['Facebook']))?strval($_REQUEST['Facebook']):null;
		$google		= (isset($_REQUEST['Google']))?strval($_REQUEST['Google']):null;
		$twitter	= (isset($_REQUEST['Twitter']))?strval($_REQUEST['Twitter']):null;
		$observaciones = (isset($_REQUEST['Observaciones']))?strval($_REQUEST['Observaciones']):null;
		$baja		= (isset($_REQUEST['Baja']))?intval($_REQUEST['Baja']):0;
		do_log("insertClub:: retrieved data from client");
		do_log("Nombre: $nombre Direccion1: $direccion1 Contacto1: $contacto1 Observaciones: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		do_log("insertadas $stmt->affected_rows filas");
		if (!$res) {
			$msg="insertClub:: Error: $conn->error";
			do_log($msg);
		}
		else  do_log("execute resulted: $res");
		
		$stmt->close();
		return $msg;
	}
	
	function updateClub($conn) {
		$msg="";
		do_log("updateClub:: enter");

		// componemos un prepared statement
		$sql ="UPDATE Clubes 
				SET Nombre=? , Direccion1=? , Direccion2=? , Provincia=? , 
				Contacto1=? , Contacto2=? , Contacto3=? , GPS=? , Web=? , 
				Email=? , Facebook=? , Google=? , Twitter=? , Observaciones=? , Baja=?
				WHERE ( Nombre=? )";
		$stmt=$conn->prepare($sql);
		if (!$stmt) {
			$msg="updateClub::prepare() failed $conn->error";
			do_log($msg);
			return $msg;	
		}
		$res=$stmt->bind_param('ssssssssssssssis',$nombre,$direccion1,$direccion2,$provincia,$contacto1,$contacto2,$contacto3,$gps,
				$web,$email,$facebook,$google,$twitter,$observaciones,$baja,$viejo);
		if (!$res) {
			$msg="updateClub::bind() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		// iniciamos los valores, chequeando su existencia
		$nombre = $_REQUEST['Nombre']; // pkey cannot be null
		$viejo = strval($_REQUEST['Viejo']);
		$direccion1 = (isset($_REQUEST['Direccion1']))?strval($_REQUEST['Direccion1']):null;
		$direccion2 = (isset($_REQUEST['Direccion2']))?strval($_REQUEST['Direccion2']):null;
		$provincia	= (isset($_REQUEST['Provincia']))?strval($_REQUEST['Provincia']):null;
		$contacto1	= (isset($_REQUEST['Contacto1']))?strval($_REQUEST['Contacto1']):null;
		$contacto2	= (isset($_REQUEST['Contacto2']))?strval($_REQUEST['Contacto2']):null;
		$contacto3	= (isset($_REQUEST['Contacto3']))?strval($_REQUEST['Contacto3']):null;
		$gps		= (isset($_REQUEST['GPS']))?strval($_REQUEST['GPS']):null;
		$web		= (isset($_REQUEST['Web']))?strval($_REQUEST['Web']):null;
		$email		= (isset($_REQUEST['Email']))?strval($_REQUEST['Email']):null;
		$facebook	= (isset($_REQUEST['Facebook']))?strval($_REQUEST['Facebook']):null;
		$google		= (isset($_REQUEST['Google']))?strval($_REQUEST['Google']):null;
		$twitter	= (isset($_REQUEST['Twitter']))?strval($_REQUEST['Twitter']):null;
		$observaciones = (isset($_REQUEST['Observaciones']))?strval($_REQUEST['Observaciones']):null;
		$baja		= (isset($_REQUEST['Baja']))?intval($_REQUEST['Baja']):0;

		do_log("updateClub:: retrieved data from client");
		do_log("Nombre: $nombre Direccion1: $direccion1 Contacto1: $contacto1 Observaciones: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		do_log("updateClub:: actualizadas $stmt->affected_rows filas");
		if (!$res) {
			$msg="updateClub:: Error: $conn->error";
			do_log($msg);
		} else do_log("updateClub::execute() resulted: $res");
		$stmt->close();
		return $msg;
	}
	
	function deleteClub($conn,$nombre) {
		$msg="";
		do_log("deleteClub:: enter");
		// fase 1: desasignar guias del club
		$res= $conn->query("UPDATE Guias SET Club='-- Sin asignar --'  WHERE (Club='$nombre')");
		if (!$res) {
			$msg="deleteClub::unassign handlers() Error: $conn->error";
			do_log($msg);
			return $msg;
		} else do_log("deleteClub:: unassing handlers() resulted: $res");
		// fase 2: borrar el club de la BBDD
		$res= $conn->query("DELETE FROM Clubes WHERE (Nombre='$nombre')");
		if (!$res) {
			$msg="deleteClub::query(delete) Error: $conn->error";
			do_log($msg);
		} else do_log("deleteClub:: remove club() resulted: $res");
		return $msg;
	}

	function orphanGuiaFromClub($conn,$guia) {
		$msg="";
		do_log("orphanGuiaFromClub::($guia) enter");
		$res= $conn->query("UPDATE Guias SET Club='-- Sin asignar --' WHERE ( Nombre='$guia' )");
		if (!$res) {
			$msg="orphanGuiaFromClub::query(delete) Error: $conn->error";
			do_log($msg);
		} else do_log("orphanGuiaFromClub:: execute() resulted: $res");
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
	
	if (! isset($_GET['Operation'])) {
		$str='Call guiaFunctions() with no operation declared.';
		do_log($str);
		echo json_encode(array('errorMsg'=>$str));
		DBConnection::closeConnection($conn);
		return;
	}
	$oper = $_GET['Operation'];
	if($oper==='insert') {
		$result=insertClub($conn);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else 				echo json_encode(array('errorMsg'=>$result));
	}
	else if($oper==='update') {
		$result= updateClub($conn);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else 				echo json_encode(array('errorMsg'=>$result));
	}
	else if($oper==='delete') {
		$result= deleteClub($conn,strval($_GET['Nombre']));
		if ($result==="") 	echo json_encode(array('success'=>true));
		else				echo json_encode(array('msg'=>$result));
	}
	else if($oper==='orphan') {
		$result= orphanGuiaFromClub($conn,strval($_GET['Nombre']));
		if ($result==="") 	echo json_encode(array('success'=>true));
		else				echo json_encode(array('msg'=>$result));
	}
	else {
		$result="clubFunctions:: Invalid operation requested: $oper";
		do_log($result);
		json_encode(array('errorMsg'=>$result));
	}
	DBConnection::closeConnection($conn);
?>