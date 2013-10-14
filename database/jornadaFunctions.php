<?php
	require_once("logging.php");
	require_once("DBConnection.php");
	
	function insertJornada ($conn) {
		$msg=""; // default: no errors
		do_log("insertJornada:: enter");
		// componemos un prepared statement
		$sql ="INSERT INTO Jornadas (Prueba,Nombre,Fecha,Hora,Grado1,Grado2,Grado3,Equipos,PreAgility,KO,Exhibicion,Otras,Cerrada)
			   VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?);";
		$stmt=$conn->prepare($sql);		
		if (!$stmt) {
			$msg="insertJornada::prepare() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		$res=$stmt->bind_param('isssiiiiiiiii',
				$prueba,$nombre,$fecha,$hora,$grado1,$grado2,$grado3,$equipos,$preagility,$ko,$exhibicion,$otras,$cerrada);
		if (!$res) {
			$msg="insertJornada::bind() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		
		// iniciamos los valores, chequeando su existencia
		$id = (isset($_REQUEST['ID']))?intval($_REQUEST['ID']):0; // primary key not null
		$prueba = intval($_REQUEST['Prueba']); // foreign key not null
		$nombre = (isset($_REQUEST['Nombre']))?strval($_REQUEST['Nombre']):null;  // Name or comment for jornada
		$fecha = str_replace("/","-",strval($_REQUEST['Fecha'])); // mysql requires format YYYY-MM-DD
		$hora = strval($_REQUEST['Hora']); //not null
		$grado1 = (isset($_REQUEST['Grado1']))?intval($_REQUEST['Grado1']):0;
		$grado2 = (isset($_REQUEST['Grado2']))?intval($_REQUEST['Grado2']):0;
		$grado3 = (isset($_REQUEST['Grado3']))?intval($_REQUEST['Grado3']):0;
		$equipos = (isset($_REQUEST['Equipos']))?intval($_REQUEST['Equipos']):0;
		$preagility = (isset($_REQUEST['PreAgility']))?intval($_REQUEST['PreAgility']):0;
		$ko = (isset($_REQUEST['KO']))?intval($_REQUEST['KO']):0;
		$exhibicion = (isset($_REQUEST['Exhibicion']))?intval($_REQUEST['Exhibicion']):0;
		$otras = (isset($_REQUEST['Otras']))?intval($_REQUEST['Otras']):0;
		$cerrada = (isset($_REQUEST['Cerrada']))?intval($_REQUEST['Cerrada']):0;
		
		do_log("insertJornada:: retrieved data from web client");
		do_log("insertJornada: ID: $id Prueba: $prueba Nombre: $nombre Fecha: $fecha Hora: $hora");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		do_log("insertadas $stmt->affected_rows filas");
		if (!$res) {
			$msg="insertJornada:: Error: $conn->error";
			do_log($msg);
		}
		else  do_log("execute resulted: $res");
		
		$stmt->close();
		return $msg;
	}
	
	function updateJornada($conn) {
		$msg="";
		do_log("updateJornada:: enter");
		
		// componemos un prepared statement
		$sql ="UPDATE Jornadas 
				SET Prueba=?, Nombre=?, Fecha=?, Hora=?, Grado1=?, Grado2=?, Grado3=?, 
					Equipos=?, PreAgility=?, KO=?, Exhibicion=?, Otras=?, Cerrada=?
				WHERE ( ID=? );";
		$stmt=$conn->prepare($sql);
		if (!$stmt) {
			$msg="updateJornada::prepare() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		$res=$stmt->bind_param('isssiiiiiiiiii',
				$prueba,$nombre,$fecha,$hora,$grado1,$grado2,$grado3,$equipos,$preagility,$ko,$exhibicion,$otras,$cerrada,$id);
		if (!$res) {
			$msg="updateJornada::bind() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		
		// iniciamos los valores, chequeando su existencia
		$id = (isset($_REQUEST['ID']))?intval($_REQUEST['ID']):0; // primary key not null
		$prueba = intval($_REQUEST['Prueba']); // foreign key not null 
		$nombre = (isset($_REQUEST['Nombre']))?strval($_REQUEST['Nombre']):null;  // Name or comment for jornada
		$fecha = str_replace("/","-",strval($_REQUEST['Fecha'])); // mysql requires format YYYY-MM-DD
		$hora = strval($_REQUEST['Hora']); //not null
		$grado1 = (isset($_REQUEST['Grado1']))?intval($_REQUEST['Grado1']):0;
		$grado2 = (isset($_REQUEST['Grado2']))?intval($_REQUEST['Grado2']):0;
		$grado3 = (isset($_REQUEST['Grado3']))?intval($_REQUEST['Grado3']):0;
		$equipos = (isset($_REQUEST['Equipos']))?intval($_REQUEST['Equipos']):0;
		$preagility = (isset($_REQUEST['PreAgility']))?intval($_REQUEST['PreAgility']):0;
		$ko = (isset($_REQUEST['KO']))?intval($_REQUEST['KO']):0;
		$exhibicion = (isset($_REQUEST['Exhibicion']))?intval($_REQUEST['Exhibicion']):0;
		$otras = (isset($_REQUEST['Otras']))?intval($_REQUEST['Otras']):0;
		$cerrada = (isset($_REQUEST['Cerrada']))?intval($_REQUEST['Cerrada']):0;
		
		do_log("updateJornada:: retrieved data from web client");
		do_log("updateJornada: ID: $id Prueba: $prueba Nombre: $nombre Fecha: $fecha Hora: $hora");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		do_log("updateJornada:: actualizadas $stmt->affected_rows filas");
		if (!$res) {
			$msg="updateJornada:: Error: $conn->error";
			do_log($msg);
		} else do_log("updateJornada::execute() resulted: $res");
		$stmt->close();
		return $msg;
	}
	
	function deleteJornada($conn) {
		$msg="";
		do_log("deleteJornada:: enter");
		$id = (isset($_REQUEST['ID']))?intval($_REQUEST['ID']):0; // primary key not null
		// si la jornada esta cerrada en lugar de borrarla la movemos a "-- Sin asignar --"
		$res= $conn->query("UPDATE Jornadas SET Prueba='-- Sin asignar --' WHERE ( (ID=$id) AND (Cerrada=1) );");
		if (!$res) {
			$msg="deleteJornada::query(update) Error: $conn->error";
			do_log($msg);
		} else do_log("deleteJornada:: query(update) resulted: $res");
		// si la jornada no está cerrada, directamente la borramos
		$res= $conn->query("DELETE FROM Jornadas WHERE ( (ID=$id) AND (Cerrada=0) );");
		if (!$res) {
			$msg="deleteJornada::query(delete) Error: $conn->error";
			do_log($msg);
		} else do_log("deleteJornada:: query(delete) resulted: $res");
		return $msg;
	}
	
	// connect database
	$conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
	if (!$conn) {
		$str='JornadaFunctions() cannot contact database.';
		do_log($str);
		echo json_encode(array('errorMsg'=>$str));
		return;
	}
	
	if (! isset($_GET['Operation'])) {
		$str='Call JornadaFunctions() with no operation declared.';
		do_log($str);
		echo json_encode(array('errorMsg'=>$str));
		DBConnection::closeConnection($conn);
		return;
	}
	$oper = $_GET['Operation'];
	if($oper==='insert') {
		$result=insertJornada($conn);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else 				echo json_encode(array('errorMsg'=>$result));
	}
	else if($oper==='update') {
		$result= updateJornada($conn);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else 				echo json_encode(array('errorMsg'=>$result));
	}
	else if($oper==='delete') {
		$result= deleteJornada($conn);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else				echo json_encode(array('msg'=>$result));
	}
	// no puede existir una jornada sin una prueba asociada: no hay funcion "orphan"
	else {
		$result="JornadaFunctions:: Invalid operation requested: $oper";
		do_log($result);
		json_encode(array('errorMsg'=>$result));
	}
	DBConnection::closeConnection($conn);
?>