<?php
	require_once("logging.php");
	require_once("DBConnection.php");
	
	/*
	 * Cada prueba lleva asociada 8 jornadas, que se crean automaticamente al crear la prueba
	 * Las funciones de inscripcion deben inscribir individualmente a cada dorsal en las jornadas
	 * solicitadas en el formulario
	 */
	
	/**
	 * Registra una nueva inscripcion
	 * @param MySQLConnection $conn
	 * @param array(jornadasID) $jornadas
	 * @param Dorsal del perro $dorsal
	 * 
	 * TODO: detectar si un perro esta ya inscrito antes de hacer nueva inscripcion
	 * mediante un "unique index (pruebaID,Numero,Dorsal)"
	 */
	function insertInscripcion($conn,$jornadas,$dorsal) {
		do_log("inscriptionFunctions::insert() enter");
		$msg="";
		// generamos un prepared statement
		$sql="INSERT INTO Inscripciones ( Jornada , Dorsal , Celo , Observaciones , Equipo , Pagado )
				VALUES (?,?,?,?,?,?)";
		$stmt=$conn->prepare($sql);
		if (!$stmt) {
			$msg="insertInscripcion::prepare() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		$res=$stmt->bind_param('iiisii',$jornada,$perro,$celo,$observaciones,$equipo,$pagado);
		if (!$res) {
			$msg="inscripcionFunctions::insert().bind() failed $conn->error";
			do_log($msg);
			return $msg;
		}
		// variables comunes a todas las jornadas
		$perro=$dorsal;
		$celo=(isset($_REQUEST['Celo']))?intval($_REQUEST['Celo']):0;
		$observaciones=(isset($_REQUEST['Observaciones']))?strval($_REQUEST['Observaciones']):"";
		$equipo=null; // TODO: write team handling
		$pagado=(isset($_REQUEST['Pagado']))?intval($_REQUEST['Pagado']):0;
		// inscribimos en cada una de las jornadas solicitadas
		for ($numero=1;$numero<9;$numero++) {
			// vemos si pide inscribirse
			$inscrito=(isset($_REQUEST["J$numero"]))?1:0;
			if (!$inscrito) continue;
			// obtenemos el JornadaID
			$jornada=$jornadas[$numero];
			// hacemos la inscripcion
			do_log("insertInscripcion::executeQuery() Jornada $numero: ID: $jornada Dorsal $dorsal");
			$res=$stmt->execute();
			if (!$res) {
				$msg="insertInscripcion::executeQuery() Error: $conn->error";
				do_log($msg);
				$stmt->close();
				return $msg;
			}
		}
		// all right: close prepared statement and return ok
		$stmt->close();
		return $msg;
	}
	
	/**
	 * Modifica una inscripcion ya existente
	 * @param MySQLConnection $conn
	 * @param array(jornadasID) $id
	 * @param Dorsal del perro $dorsal
	 */	
	function updateInscripcion ($conn,$jornadas,$dorsal) {
		do_log("inscriptionFunctions::update() enter");
		return "";
	}
	
	/**
	 * Borra una inscripcion existente EXCEPTO si la jornada esta cerrada
	 * 
	 * @param MySQLConnection $conn
	 * @param array(jornadasID) $id
	 * @param Dorsal del perro $dorsal
	 */	
	function deleteInscripcion($conn,$jornadas,$dorsal) {
		do_log("inscriptionFunctions::delete() enter");
		for ($n=1;$n<9;$n++) {
			$idjornada=$jornadas[$n];
			$cerrada=intval($_REQUEST["J$n"]);
			if ($cerrada!=0) {
				do_log("inscriptionFunctions::delete() skip delete Dorsal $dorsal on closed Jornada $idjornada");
				continue;
			}
			$sql="DELETE FROM Inscripciones where ( (Dorsal=$dorsal) AND (Jornada=$idjornada))";
			$res=$conn->query($sql);
			if (!$res) {
				$msg="inscriptionFunctions::delete() execute query failed :".$conn->error;
				do_log($msg);
				return $msg;
			}
		}
		return "";
	}
	
	/**
	 * genera un pdf ordenado por club, categoria y nombre con una pagina por cada jornada
	 * @param MySQLConnection $conn
	 * @param array(jornadasID) $id
	 */
	function printInscripciones($conn,$jornadas) {
		do_log("inscriptionFunctions::insert() enter");
		return "";
	}
	
	// connect database
	$conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
	if (!$conn) {
		$str='inscripcionFunctions() cannot contact database.';
		do_log($str);
		echo json_encode(array('errorMsg'=>$str));
		return;
	}
	
	if (! isset($_REQUEST['Operation'])) {
		$str='Call inscripcionFunctions() with no operation declared.';
		do_log($str);
		echo json_encode(array('errorMsg'=>$str));
		DBConnection::closeConnection($conn);
		return;
	}
	if (! isset($_REQUEST['ID'])) {
		$str='Call inscripcionFunctions() with no prueba ID declared.';
		do_log($str);
		echo json_encode(array('errorMsg'=>$str));
		DBConnection::closeConnection($conn);
		return;
	}
	$oper = $_REQUEST['Operation'];
	$id = $_REQUEST['ID'];
	$dorsal = (isset($_REQUEST['Dorsal']))?$_GET['Dorsal']:'';
	
	// obtenemos los id de las 8 jornadas asociadas a la prueba
	$jornadas=array(); 
	$rs=$conn->query("SELECT ID,Numero FROM Jornadas WHERE (Prueba=$id)");
	if (!$rs) {
		$str='inscripcionFunctions::getJornadasID() failed: $conn->error';
		do_log($str);
		echo json_encode(array('errorMsg'=>$str));
		DBConnection::closeConnection($conn);
		return;
	} else {
		while($row = $rs->fetch_array()) {
			$jornadas[$row['Numero']]=$row['ID'];
			// do_log("Jornada Numero ".$row['Numero']." ID: ".$jornadas[$row['Numero']]);
		}
		$rs->free();
	}
	if($oper==='insert') {
		$result=insertInscripcion($conn,$jornadas,$dorsal);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else 				echo json_encode(array('errorMsg'=>$result));
	}
	else if($oper==='update') {
		$result= updateInscripcion($conn,$jornadas,$dorsal);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else 				echo json_encode(array('errorMsg'=>$result));
	}
	else if($oper==='delete') {
		$result= deleteInscripcion($conn,$jornadas,$dorsal);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else				echo json_encode(array('msg'=>$result));
	}	
	else if($oper==='print') {
		$result= printInscripciones($conn,$jornadas);
		if ($result==="") 	echo json_encode(array('success'=>true));
		else				echo json_encode(array('msg'=>$result));
	}
	else {
		$result="inscripcionFunctions:: Invalid operation requested: $oper";
		do_log($result);
		json_encode(array('errorMsg'=>$result));
	}
	DBConnection::closeConnection($conn);
?>