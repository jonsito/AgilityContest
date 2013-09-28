<?php
	require_once("../logging.php");
	require_once("../DBConnection.php");
	
	function insertJuez ($conn) {
		do_log("insertJuez:: enter");
		// componemos un prepared statement
		$sql ="INSERT INTO Jueces (Nombre,Direccion1,Direccion2,Telefono,Internacional,Practicas,Email,Observaciones)
			   VALUES(?,?,?,?,?,?,?,?)";
		$stmt=$conn->prepare($sql);
		$res=$stmt->bind_param('ssssiiss',$nombre,$direccion1,$direccion2,$telefono,$internacional,$practicas,$email,$observaciones);
		if (!$res) {
			do_log("insertJuez::prepare() failed $conn->error");
			return FALSE;
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
		if (!$res) do_log("inzertJuez:: Error: $conn->error");
		do_log("inzertJuez:: execute resulted: $res");
		
		$stmt->close();
		return $res;
	}
	
	function updateJuez($conn) {
		do_log("updateJuez:: enter");
		
		// componemos un prepared statement
		$sql ="UPDATE Jueces SET Nombre=? , Direccion1=? , Direccion2=? , Telefono=? , Internacional=? , Practicas=? , Email=? , Observaciones=?
		       WHERE ( Nombre=? )";
		$stmt=$conn->prepare($sql);
		if (!$stmt) {
			do_log("updateJuez::prepare() failed $conn->error");
			return FALSE;
		}
		$res=$stmt->bind_param('ssssiisss',$nombre,$direccion1,$direccion2,$telefono,$internacional,$practicas,$email,$observaciones,$viejo);
		if (!$res) {
			do_log("update::bind() failed $conn->error");
			return FALSE;
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

		do_log("updateJuez:: retrieved data from client");
		do_log("N.Viejo: $viejo N.nuevo: $nombre Dir1: $direccion1 Dir2: $direccion2 Tel: $telefono");
		do_log("I: $internacional P: $practicas Email: $email Obs: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		do_log("updateJuez:: actualizadas $stmt->affected_rows filas");
		if (!$res) do_log("updateJuez:: Error: $conn->error");
		do_log("updateJuez::execute() resulted: $res");
		$stmt->close();
		return $res;
	}
	
	function deleteJuez($conn,$ID) {
		do_log("deleteJuez:: enter");
		$str="DELETE FROM Jueces WHERE ( Nombre='$ID' )";
		$res= $conn->query($str);
		if (!$res) do_log("deleteJuez:: Error: $conn->error");
		else do_log("deleteJuez:: execute() resulted: $res");
		return $res;
	}

	// connect database
	$conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
	if (!$conn) {
		$str='juezFunctions() cannot contact database.';
		do_log($str);
		echo json_encode(array('msg'=>$str));
		return;
	}
	
	if (! isset($_GET['operation'])) {
		$str='Call juezFunctions() with no operation declared.';
		do_log($str);
		echo json_encode(array('msg'=>$str));
		DBConnection::closeConnection($conn);
		return;
	}
	$oper = $_GET['operation'];
	if($oper==='insert') {
		$result=insertJuez($conn);
		if ($result) 	echo json_encode(array('success'=>true));
		else {
			$str='juezFunctions(insert) Some errors occured.';
			echo json_encode(array('msg'=>$str));
		}
		DBConnection::closeConnection($conn);
		return;	
	}
	if($oper==='update') {
		$result= updateJuez($conn);
		if ($result) 	echo json_encode(array('success'=>true));
		else {
			$str='juezFunctions(update) Some errors occured.';
			do_log($str);
			echo json_encode(array('msg'=>$str));
		}
		DBConnection::closeConnection($conn);
		return;
	}
	if($oper==='delete') {
		$result= deleteJuez($conn,strval($_GET['Nombre']));
		if ($result) 	echo json_encode(array('success'=>true));
		else {
			$str='juezFunctions(delete) Some errors occured.';
			echo json_encode(array('msg'=>$str));
		}
		DBConnection::closeConnection($conn);
		return;
	}
	do_log("Invalid operation requested: $oper");
	DBConnection::closeConnection($conn);
	echo json_encode(array('msg'=>'Invalid operation in juezFunctions() call'));
?>