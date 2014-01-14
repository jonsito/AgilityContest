<?php
	require_once("logging.php");
	require_once("DBConnection.php");
	
	/*********** creacion / borrado de mangas asociadas a una jornada *************/
	function create_manga($conn,$jornada,$tipo) {
		// si la manga existe no hacer nada; si no existe crear manga
		$str="SELECT count(*) AS 'result' FROM Mangas WHERE ( Jornada = $jornada ) AND  ( Tipo = '".$tipo."' )";
		$rs=$conn->query($str);
		if (!$rs) {
			$str="inscripcionFunctions::create_manga( select_count(*) , $jornada , $tipo ) failed: ".$conn->error;
			do_log($str);
			return;
		}
		$row=$rs->fetch_row();
		if ($row[0]!=0) return; // manga already created
		$rs->free();
		$str="INSERT INTO Mangas ( Jornada , Tipo ) VALUES ($jornada,'".$tipo."')";
		$rs=$conn->query($str);
		if (!$rs) {
			$str="inscripcionFunctions::create_manga( insert , $jornada , $tipo ) failed: ".$conn->error;
			do_log($str);
		}
		// crear entrada de TRS/TRM asociada a la manga
		$id=$rs->insert_id; // retrieve jornada id
		$str="INSERT INTO Tiempos_Manga ( Manga ) VALUES ( $id )";
		if (!$rs) {
			$str="inscripcionFunctions::create_manga( insert , $jornada , $tipo ) failed: ".$conn->error;
			do_log($str);
		}
		return;
	}
	
	function delete_manga($conn,$jornada,$tipo) {
		// si la manga existe, borrarla; si no existe, no hacer nada
		$str="DELETE FROM Mangas WHERE ( Jornada = $jornada ) AND  ( Tipo = '".$tipo."' )";
		$rs=$conn->query($str);
		if (!$rs) {
			$str="inscripcionFunctions::delete_manga( $jornada , $tipo ) failed: ".$conn->error;
			do_log($str);
		}
		return;
	}
	
	function declare_mangas($conn,$id,$grado1,$grado2,$grado3,$equipos,$preagility,$ko,$exhibicion,$otras) {
		if ($grado1) {
			create_manga($conn,$id,'Agility-1 GI');
			create_manga($conn,$id,'Agility-2 GI');
		} else {
			create_manga($conn,$id,'Agility-1 GI');
			create_manga($conn,$id,'Agility-2 GI');
		}
		if ($grado2) {
			create_manga($conn,$id,'Agility GII');
			create_manga($conn,$id,'Jumping GII');
		} else {
			delete_manga($conn,$id,'Agility GII');
			delete_manga($conn,$id,'Jumping GII');
		}
		if ($grado3) {
			create_manga($conn,$id,'Agility GIII');
			create_manga($conn,$id,'Jumping GIII');
		} else {
			delete_manga($conn,$id,'Agility GIII');
			delete_manga($conn,$id,'Jumping GIII');
		}
		if ($equipos) {
			create_manga($conn,$id,'Agility Equipos');
			create_manga($conn,$id,'Jumping Equipos');
		} else {
			delete_manga($conn,$id,'Agility Equipos');
			delete_manga($conn,$id,'Jumping Equipos');
		}
		if ($preagility) { create_manga($conn,$id,'Pre-Agility'); } else { delete_manga($conn,$id,'Pre-Agility'); }
		if ($exhibicion) { create_manga($conn,$id,'Exhibicion'); } else { delete_manga($conn,$id,'Exhibicion'); }
		// TODO: Decidir que se hace con las mangas 'otras'
		// TODO: las mangas KO hay que crearlas dinamicamente en funcion del numero de participantes
	}
	
	/******************* Creacion de jornadas ****************/
	
	function insertJornada ($conn) {
		$msg=""; // default: no errors
		do_log("insertJornada:: enter");
		// componemos un prepared statement
		$sql ="INSERT INTO Jornadas (Prueba,Nombre,Fecha,Hora,Grado1,Grado2,Grado3,Equipos,PreAgility,KO,Exhibicion,Otras,Cerrada)
			   VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?);";
		$stmt=$conn->prepare($sql);		
		if (!$stmt) {
			$msg="insertJornada::prepare() failed ".$conn->error;
			do_log($msg);
			return $msg;
		}
		$res=$stmt->bind_param('isssiiiiiiiii',
				$prueba,$nombre,$fecha,$hora,$grado1,$grado2,$grado3,$equipos,$preagility,$ko,$exhibicion,$otras,$cerrada);
		if (!$res) {
			$msg="insertJornada::bind() failed ".$conn->error;
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
		$stmt->close();
		if (!$res) {
			$msg="insertJornada:: Error: ".$conn->error;
			do_log($msg);
		}
		else  do_log("execute resulted: $res");
		// retrieve ID on last created jornada
		$jornadaid=$conn->insert_id;
		// if not closed ( an stupid thing create a closed jornada, but.... ) create mangas and default team
		if (!$cerrada) {
			// creamos las mangas asociadas a esta jornada
			declare_mangas($conn,$jornadaid,$grado1,$grado2,$grado3,$equipos,$preagility,$ko,$exhibicion,$otras);
			// create a default team for this jornada
			$conn->query("INSERT INTO Equipos (Jornada,Nombre,Observaciones) 
				VALUES ($jornadaid,'-- Sin asignar --','NO BORRAR: USADO COMO GRUPO POR DEFECTO PARA LA JORNADA $jornadaid')");
		};
		return $msg;
	}
	
	/************************ modificacion de jornadas ***********************/
	
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
			$msg="updateJornada::prepare() failed ".$conn->error;
			do_log($msg);
			return $msg;
		}
		$res=$stmt->bind_param('isssiiiiiiiiii',
				$prueba,$nombre,$fecha,$hora,$grado1,$grado2,$grado3,$equipos,$preagility,$ko,$exhibicion,$otras,$cerrada,$id);
		if (!$res) {
			$msg="updateJornada::bind() failed ".$conn->error;
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
			$msg="updateJornada:: Error: ".$conn->error;
			do_log($msg);
		} else do_log("updateJornada::execute() resulted: $res");
		$stmt->close();
		if (!$cerrada) {
			declare_mangas($conn,$id,$grado1,$grado2,$grado3,$equipos,$preagility,$ko,$exhibicion,$otras);
		}
		return $msg;
	}
	
	/************************* borrado de jornadas ************************/
	
	function deleteJornada($conn) {
		$msg="";
		do_log("deleteJornada:: enter");
		$id = (isset($_REQUEST['ID']))?intval($_REQUEST['ID']):0; // primary key not null
		// si la jornada esta cerrada en lugar de borrarla la movemos a "-- Sin asignar --"
		// con esto evitamos borrar mangas y resultados ya fijos
		$res= $conn->query("UPDATE Jornadas SET Prueba='-- Sin asignar --' WHERE ( (ID=$id) AND (Cerrada=1) );");
		if (!$res) {
			$msg="deleteJornada::query(update) Error: ".$conn->error;
			do_log($msg);
		} else do_log("deleteJornada:: query(update) resulted: $res");
		// si la jornada no está cerrada, directamente la borramos
		// recuerda que las mangas y resultados asociados se borran por la "foreign key"
		$res= $conn->query("DELETE FROM Jornadas WHERE ( (ID=$id) AND (Cerrada=0) );");
		if (!$res) {
			$msg="deleteJornada::query(delete) Error: ".$conn->error;
			do_log($msg);
		} else do_log("deleteJornada:: query(delete) resulted: $res");
		return $msg;
	}
	
	/***************** programa principal **************/
	
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