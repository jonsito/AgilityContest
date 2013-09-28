<?php
	require_once("../DBConnection.php");
	
	function insert ($conn) {
		
		// componemos un prepared statement
		$sql ="INSERT INTO Perros (Nombre,Raza,LOE_RRC,Licencia,Categoria,Grado,Guia)
			   VALUES(?,?,?,?,?,?,?)";
		$stmt=$conn->prepare($sql);
		$res=$stmt->bind_param('sssssss',$nombre,$raza,$loe_rrc,$licencia,$categoria,$grado,$guia);
		if (!$res) return FALSE;
		
		// iniciamos los valores, chequeando su existencia
		$nombre = $_REQUEST['Nombre'];
		$raza = (isset($_REQUEST['Raza']))?$_REQUEST['Raza']:null;
		$loe_rrc = (isset($_REQUEST['LOE_RRC']))?$_REQUEST['LOE_RRC']:null;
		$licencia = (isset($_REQUEST['Licencia']))?$_REQUEST['Licencia']:null;
		$categoria = (isset($_REQUEST['Categoria']))?$_REQUEST['Categoria']:null;
		$grado = (isset($_REQUEST['Grado']))?$_REQUEST['Grado']:null;
		$guia = (isset($_REQUEST['Guia']))?$_REQUEST['Guia']:null;
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		$stmt->close();
		return $res;
	}
	
	function update ($conn,$dorsal) {
		
		// componemos un prepared statement
		$sql ="UPDATE Perros SET Nombre=? , Raza=? , LOE_RRC=? , Licencia=? , Categoria=? , Grado=? , Guia=? )
		       WHERE ( Dorsal = ? )";
		$stmt=$conn->prepare($sql);
		$res=$stmt->bind_param('ssssssi',$nombre,$raza,$loe_rrc,$licencia,$categoria,$grado,$guia,$dorsal);
		if (!res) return FALSE;
		
		// iniciamos los valores, chequeando su existencia
		$nombre = $_REQUEST['Nombre'];
		$raza = (isset($_REQUEST['Raza']))?stringval($_REQUEST['Raza']):null;
		$loe_rrc = (isset($_REQUEST['LOE_RRC']))?stringval($_REQUEST['LOE_RRC']):null;
		$licencia = (isset($_REQUEST['Licencia']))?stringval($_REQUEST['Licencia']):null;
		$categoria = (isset($_REQUEST['Categoria']))?stringval($_REQUEST['Categoria']):null;
		$grado = (isset($_REQUEST['Grado']))?stringval($_REQUEST['Grado']):null;
		$guia = (isset($_REQUEST['Guia']))?stringval($_REQUEST['Guia']):null;
		$dorsal = intval($_REQUEST['Dorsal']);
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		$stmt->close();
		return $res;
	}
	
	function delete ($conn,$dorsal) {
		return $conn->query("DELETE FROM Perros WHERE (Dorsal=$dorsal)");
	}

	// connect database
	$conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
	if (!$conn) {
		echo json_encode(array('msg'=>'dogFunctions() cannot contact database.'));
		return;
	}
	
	if (! isset($_GET['operation'])) {
		echo json_encode(array('msg'=>'Call dogFunctions() with no operation declared.'));
		DBConnection::closeConnection($conn);
		return;
	}
	$oper = $_GET['operation'];
	if($oper==='insert') {
		$result=insert($conn);
		if ($result) 	echo json_encode(array('success'=>true));
		else 			echo json_encode(array('msg'=>'dogFunctions(insert) Some errors occured.'));
		DBConnection::closeConnection($conn);
		return;	
	}
	if (! isset($_GET['id'])) {
		echo json_encode(array('msg'=>'Call dogFunctions(update/delete) without pkey declared.'));
		DBConnection::closeConnection($conn);
		return;
	}
	if($oper==='update') {
		$result= update($conn,$_GET['id']);
		if ($result) 	echo json_encode(array('success'=>true));
		else 			echo json_encode(array('msg'=>'dogFunctions(update) Some errors occured.'));
		DBConnection::closeConnection($conn);
		return;
	}
	if($oper==='delete') {
		$result= delete($conn,$_GET['id']);
		if ($result) 	echo json_encode(array('success'=>true));
		else 			echo json_encode(array('msg'=>'dogFunctions(delete) Some errors occured.'));
		DBConnection::closeConnection($conn);
		return;
	}
	DBConnection::closeConnection($conn);
	echo json_encode(array('msg'=>'Invalid operation in dogFunctions() call'));
?>