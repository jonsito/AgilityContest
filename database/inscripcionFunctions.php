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
			$msg="insertInscripcion::prepare() failed ".$conn->error;
			do_log($msg);
			return $msg;
		}
		$res=$stmt->bind_param('iiisii',$jornada,$perro,$celo,$observaciones,$equipo,$pagado);
		if (!$res) {
			$msg="inscripcionFunctions::insert().bind() failed ".$conn->error;
			do_log($msg);
			return $msg;
		}
		// variables comunes a todas las jornadas
		$perro=$dorsal;
		$celo=(isset($_REQUEST['Celo']))?intval($_REQUEST['Celo']):0;
		$observaciones=(isset($_REQUEST['Observaciones']))?strval($_REQUEST['Observaciones']):"";
		$equipo=(isset($_REQUEST['Equipo']))?intval($_REQUEST['Equipo']):'NULL'; // if not set defaults to null
		$pagado=(isset($_REQUEST['Pagado']))?intval($_REQUEST['Pagado']):0;
		// inscribimos en cada una de las jornadas solicitadas
		for ($numero=1;$numero<9;$numero++) {
			// vemos si pide inscribirse
			$solicita=(isset($_REQUEST["J$numero"]))?1:0;
			if (!$solicita) continue;
			
			// obtenemos el JornadaID
			$jornada=$jornadas[$numero];
			
			// vamos a ver si esta ya inscrito. Para ello lo que haremos sera intentar un update,
			// y ver si se ha tocado alguna fila
			$sql2="UPDATE Inscripciones
				SET Celo=$celo , Observaciones='$observaciones' , Equipo=$equipo , Pagado=$pagado
				WHERE ( (Dorsal=$dorsal) AND (Jornada=$jornada))";
			$rs=$conn->query($sql2);
			if ($rs===false) { // error en query
				echo json_encode(array('errorMsg'=>$conn->error));
				DBConnection::closeConnection($conn);
				exit(0);
			}
			if ($conn->affected_rows != 0) { // ya estaba inscrito
				do_log("El dorsal $dorsal ya esta inscrito en la jornada $jornada. Realizando update");
				return "";
			}
			
			// si no esta inscrito, vamos a hacer la inscripcion
			do_log("insertInscripcion::executeQuery() Jornada $numero: ID: $jornada Dorsal $dorsal");
			$res=$stmt->execute();
			if (!$res) {
				$msg="insertInscripcion::executeQuery() Error: ".$conn->error;
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
		$sql="";
		do_log("inscriptionFunctions::update() enter");
		// iterate on every jornadas
		for ($n=1;$n<9;$n++) {
			$idjornada=$jornadas[$n];
			$cerrada=intval($_REQUEST["c$n"]);
			if ($cerrada!=0) {
				do_log("inscriptionFunctions::updateInscription() skip update Dorsal $dorsal on closed Jornada $idjornada");
				continue;
			}
			$celo=(isset($_REQUEST['Celo']))?intval($_REQUEST['Celo']):0;
			$observaciones=(isset($_REQUEST['Observaciones']))?strval($_REQUEST['Observaciones']):"";
			$pagado=(isset($_REQUEST['Pagado']))?intval($_REQUEST['Pagado']):0;
			$equipo=(isset($_REQUEST['Equipo']))?intval($_REQUEST['Equipo']):'NULL';
			// jornada is not cerrada check for inscription changes
			$old=(isset($_REQUEST["oldJ$n"]))?intval($_REQUEST["oldJ$n"]):0;
			$new=(isset($_REQUEST["J$n"]))?intval($_REQUEST["J$n"]):0;
			do_log("inscriptionFunctions::updateInscription() old:$old new:$new");
			if ( ($old==0) && ($new==0) ) {
				do_log("inscriptionFunctions::updateInscription() Dorsal $dorsal has no inscription in Jornada $idjornada");
				continue; // no inscription
			}
			if ( ($old==0) && ($new!=0) ) { // new inscription
				$sql="INSERT INTO Inscripciones ( Jornada , Dorsal , Celo , Observaciones , Equipo , Pagado )
				VALUES ($idjornada,$dorsal,$celo,'$observaciones',$equipo,$pagado)";
			}
			if ( ($old!=0) && ($new==0) ) { // remove inscription
				$sql="DELETE FROM Inscripciones where ( (Dorsal=$dorsal) AND (Jornada=$idjornada))";
			}
			if ( ($old!=0) && ($new!=0) ) { // already subscribed: just update data
				$sql="UPDATE Inscripciones 
						SET Celo=$celo , Observaciones='$observaciones' , Equipo=$equipo , Pagado=$pagado
						WHERE ( (Dorsal=$dorsal) AND (Jornada=$idjornada))";
			}
			do_log("inscriptionFunctions::updateInscription() executing query: \n$sql");
			$res=$conn->query($sql);
			if (!$res) {
				$msg="inscriptionFunctions::update::executeQuery() Error: ".$conn->error;
				do_log($msg);
				return $msg;
			}
		}
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
		$str="inscripcionFunctions::getJornadasID() failed: ".$conn->error;
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
	else {
		$result="inscripcionFunctions:: Invalid operation requested: $oper";
		do_log($result);
		json_encode(array('errorMsg'=>$result));
	}
	DBConnection::closeConnection($conn);
?>