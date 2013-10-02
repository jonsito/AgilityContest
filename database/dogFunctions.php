<?php
	require_once("logging.php");
	require_once("DBConnection.php");
	
	function insertDog ($conn) {
		$msg=""; // default: no errors
		do_log("insertDog:: enter");
		// componemos un prepared statement
		$sql ="INSERT INTO Perros (Nombre,Raza,LOE_RRC,Licencia,Categoria,Grado,Guia)
			   VALUES(?,?,?,?,?,?,?)";
		$stmt=$conn->prepare($sql);
		$res=$stmt->bind_param('sssssss',$nombre,$raza,$loe_rrc,$licencia,$categoria,$grado,$guia);
		if (!$res) {
			$msg="insertDog::prepare() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		
		// iniciamos los valores, chequeando su existencia
		$nombre = $_REQUEST['Nombre'];
		$raza = (isset($_REQUEST['Raza']))?$_REQUEST['Raza']:null;
		$loe_rrc = (isset($_REQUEST['LOE_RRC']))?$_REQUEST['LOE_RRC']:null;
		$licencia = (isset($_REQUEST['Licencia']))?$_REQUEST['Licencia']:null;
		$categoria = (isset($_REQUEST['Categoria']))?$_REQUEST['Categoria']:null;
		$grado = (isset($_REQUEST['Grado']))?$_REQUEST['Grado']:null;
		$guia = (isset($_REQUEST['Guia']))?$_REQUEST['Guia']:null;
		do_log("insertDog:: retrieved data from client");
		do_log("Nombre: $nombre Raza: $raza LOE: $loe_rrc Categoria: $categoria Grado: $grado Guia: $guia");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		do_log("insertadas $stmt->affected_rows filas");
		if (!$res) {
			$msg="insertDog:: Error: $conn->error";
			do_log($msg);
		}
		else  do_log("execute resulted: $res");
		
		$stmt->close();
		return $msg;
	}
	
	function updateDog($conn,$id) {
		$msg="";
		do_log("updateDog:: enter");
		
		// componemos un prepared statement
		$sql ="UPDATE Perros SET Nombre=? , Raza=? , LOE_RRC=? , Licencia=? , Categoria=? , Grado=? , Guia=?
		       WHERE ( Dorsal=? )";
		$stmt=$conn->prepare($sql);
		if (!$stmt) {
			$msg="updateDog::prepare() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		$res=$stmt->bind_param('sssssssi',$nombre,$raza,$loe_rrc,$licencia,$categoria,$grado,$guia,$dorsal);
		if (!$res) {
			$msg="updateDog::bind() failed $conn->error";
			do_log($msg);
			return $msg;
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

		do_log("updateDog:: retrieved data from client");
		do_log("Dorsal: $dorsal Nombre: $nombre Raza: $raza LOE: $loe_rrc Categoria: $categoria Grado: $grado Guia: $guia");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		do_log("updateDog:: actualizadas $stmt->affected_rows filas");
		if (!$res) {
			$msg="updateDog:: Error: $conn->error";
			do_log($msg);
		} else do_log("updateDog::execute() resulted: $res");
		$stmt->close();
		return $msg;
	}
	
	function deleteDog($conn,$dorsal) {
		$msg="";
		do_log("deleteDog:: enter");
		$res= $conn->query("DELETE FROM Perros WHERE (Dorsal=$dorsal)");
		if (!$res) {
			$msg="deleteDog:: Error: $conn->error";
			do_log($msg);
		} else do_log("deleteDog:: execute() resulted: $res");
		return $msg;
	}

	// connect database
	$conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
	if (!$conn) {
		$str='dogFunctions() cannot contact database.';
		do_log($str);
		echo json_encode(array('errorMsg'=>$str));
		return;
	}
	
	if (! isset($_GET['Operation'])) {
		$str='Call dogFunctions() with no operation declared.';
		do_log($str);
		echo json_encode(array('errorMsg'=>$str));
		DBConnection::closeConnection($conn);
		return;
	}
	$oper = $_GET['Operation'];
	if ( ($oper!=='insert') && (! isset($_GET['Dorsal'])) ) {
		$str="Call dogFunctions(update/delete) without pkey declared.";
		do_log($str);
		echo json_encode(array('errorMsg'=>$str));
		DBConnection::closeConnection($conn);
		return;
	}
	if($oper==='insert') {
		$result=insertDog($conn);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else 				echo json_encode(array('errorMsg'=>$result));
	}
	else if($oper==='update') {
		$result= updateDog($conn,intval($_GET['Dorsal']));
		if ($result==="") 	echo json_encode(array('success'=>true));
		else 				echo json_encode(array('errorMsg'=>$result));
	}
	else if($oper==='delete') {
		$result= deleteDog($conn,$_GET['Dorsal']);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else				echo json_encode(array('msg'=>$result));
	}
	else {
		$result="dogFunctions:: Invalid operation requested: $oper";
		do_log($result);
		json_encode(array('errorMsg'=>$result));
	}
	DBConnection::closeConnection($conn);
?>