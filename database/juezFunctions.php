<?php
	require_once("logging.php");
	require_once("classes/DBConnection.php");
	
	function insertJuez ($conn) {
		$msg=""; // default: no errors
		do_log("insertJuez:: enter");
		// componemos un prepared statement
		$sql ="INSERT INTO Jueces (Nombre,Direccion1,Direccion2,Telefono,Internacional,Practicas,Email,Observaciones)
			   VALUES(?,?,?,?,?,?,?,?)";
		$stmt=$conn->prepare($sql);
		$res=$stmt->bind_param('ssssiiss',$nombre,$direccion1,$direccion2,$telefono,$internacional,$practicas,$email,$observaciones);
		if (!$res) {
			$msg="insertJuez::prepare() failed ".$conn->error;
			do_log($msg);
			return $msg;
		}
		
		// iniciamos los valores, chequeando su existencia
		$nombre = strval($_REQUEST['Nombre']);
		$Direccion1 = (isset($_REQUEST['Direccion1']))?strval($_REQUEST['Direccion1']):null;
		$Direccion2 = (isset($_REQUEST['Direccion2']))?strval($_REQUEST['Direccion2']):null;
		$Telefono = (isset($_REQUEST['Telefono']))?strval($_REQUEST['Telefono']):null;
		$internacional = (isset($_REQUEST['Internacional']))?intval($_REQUEST['Internacional']):0; // intl. and pract. cannot be null
		$practicas = (isset($_REQUEST['Practicas']))?intval($_REQUEST['Practicas']):0;
		$email = (isset($_REQUEST['Email']))?strval($_REQUEST['Email']):null;
		$observaciones = (isset($_REQUEST['Observaciones']))?strval($_REQUEST['Observaciones']):null;
		do_log("insertJuez:: retrieved data from client");
		do_log("Nombre: $nombre Dir1: $direccion1 Dir2: $Direccion2 Tel: $telefono");
		do_log("I: $internacional P: $practicas Email: $email Obs: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		do_log("insertJuez:: insertadas $stmt->affected_rows filas");
		if (!$res) {
			$msg="insertJuez:: Error: ".$conn->error;
			do_log($msg);
		}
		do_log("inzertJuez:: execute resulted: $res");
		$stmt->close();
		return $msg; // return error message (in case of)
	}
	
	function updateJuez($conn) {
		$msg="";
		do_log("juezFunctions::updateJuez() enter");
		
		// componemos un prepared statement
		$sql ="UPDATE Jueces SET Nombre=? , Direccion1=? , Direccion2=? , Telefono=? , Internacional=? , Practicas=? , Email=? , Observaciones=?
		       WHERE ( Nombre=? )";
		$stmt=$conn->prepare($sql);
		if (!$stmt) {
			$msg="juezFunctions::updateJuez() prepare() failed ".$conn->error;
			do_log($msg);
			return $msg;
		}
		$res=$stmt->bind_param('ssssiisss',$nombre,$direccion1,$direccion2,$telefono,$internacional,$practicas,$email,$observaciones,$viejo);
		if (!$res) {
			$msg="juezFunctions::updateJuez() bind() failed ".$conn->error;
			do_log($msg);
			return $msg;
		}
		
		// iniciamos los valores, chequeando su existencia
		$nombre = strval($_REQUEST['Nombre']);
		$viejo = strval($_REQUEST['Viejo']);
		$direccion1 = (isset($_REQUEST['Direccion1']))?strval($_REQUEST['Direccion1']):null;
		$direccion2 = (isset($_REQUEST['Direccion2']))?strval($_REQUEST['Direccion2']):null;
		$telefono = (isset($_REQUEST['Telefono']))?strval($_REQUEST['Telefono']):null;
		$internacional = (isset($_REQUEST['Internacional']))?intval($_REQUEST['Internacional']):0; // pract and intl cannot be null
		$practicas = (isset($_REQUEST['Practicas']))?intval($_REQUEST['Practicas']):0;
		$email = (isset($_REQUEST['Email']))?strval($_REQUEST['Email']):null;
		$observaciones = (isset($_REQUEST['Observaciones']))?strval($_REQUEST['Observaciones']):null;

		do_log("juezFunctions::updateJuez() retrieved data from client");
		do_log("N.Viejo: $viejo N.nuevo: $nombre Dir1: $direccion1 Dir2: $direccion2 Tel: $telefono");
		do_log("I: $internacional P: $practicas Email: $email Obs: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		do_log("juezFunctions::updateJuez() actualizadas $stmt->affected_rows filas");
		if (!$res) {
			$msg="juezFunctions::updateJuez() Error: ".$conn->error;
			do_log($msg);
		}
		do_log("juezFunctions::updateJuez() execute() resulted: $res");
		$stmt->close();
		return $msg;
	}
	
	function deleteJuez($conn,$ID) {
		$msg="";
		do_log("juezFunctions::deleteJuez() enter");
		if ($ID==='-- Sin asignar --') {
			$msg="juezFunctions::deleteJuez() Ignore deletion of default value";
			return $msg;
		}
		$str="DELETE FROM Jueces WHERE ( Nombre='$ID' )";
		$res= $conn->query($str);
		if (!$res) {
			$msg="juezFunctions::deleteJuez() Error: ".$conn->error;
			do_log($msg);
		}
		else do_log("juezFunctions::deleteJuez() execute() resulted: $res");
		return $msg;
	}

	// connect database
	$conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
	if (!$conn) {
		$msg='juezFunctions() cannot contact database.';
		do_log($msg);
		echo json_encode(array('errorMsg'=>$msg));
		return;
	}
	
	if (! isset($_GET['Operation'])) {
		$str='Call juezFunctions() with no operation declared.';
		do_log($str);
		echo json_encode(array('errorMsg'=>$str));
		DBConnection::closeConnection($conn);
		return;
	}
	$oper = $_GET['Operation'];
	if($oper==='insert') {
		$result=insertJuez($conn);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else				echo json_encode(array('errorMsg'=>$result));
	}
	else if($oper==='update') {
		$result= updateJuez($conn);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else				echo json_encode(array('errorMsg'=>$result));
	}
	else if($oper==='delete') {
		$result= deleteJuez($conn,strval($_GET['Nombre']));
		if ($result==="") 	echo json_encode(array('success'=>true));
		else 				echo json_encode(array('errorMsg'=>$result));
	} else {
		$result="Call to juezFunctions() Invalid operation requested: $oper";
		do_log($result);
		echo json_encode(array('errorMsg'=>$result));
	}
	DBConnection::closeConnection($conn);
?>