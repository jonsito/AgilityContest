<?php

require_once("DBObject.php");

class Sesiones extends DBObject {
	
	/**
	 * Insert a new session into database
	 * @return {string} "" if ok; null on error
	 */
	function insert($data) {
		$this->myLogger->enter();
		// componemos un prepared statement
		$sql ="INSERT INTO Sesiones (Nombre,Prueba,Jornada,Manga,Tanda,Perro,Resultado,Usuario)
			   VALUES(?,?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('siiiiiis',$nombre,$prueba,$jornada,$manga,$tanda,$perro,$resultado,$usuario);
		if (!$res) return $this->error($this->conn->error);	
		
		// extraemos los valores del parametro
		$nombre =	$data['Nombre'];
		$prueba =	$data['Prueba'];
		$jornada =	$data['Jornada'];
		$manga =	$data['Manga'];
		$tanda =	$data['Tanda'];
		$perro =	$data['Perro'];
		$resultado =$data['Resultado'];
		$usuario =	$data['Usuario'];
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		$stmt->close();
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return ""; 
	}
	
	/**
	 * Update session data
	 * @param {integer} $id session ID primary key
	 * @param {integer} $data datos a actualizar. si cero o null no se tocan
	 * @return {string} "" on success; null on error
	 */
	function update($id,$data) {
		$this->myLogger->enter();
		if ($id==0) return $this->error("Invalid Session ID:$id");
		$now=date('Y-m-d G:i:s');
		$sql="UPDATE Sesiones SET LastModified='$now'";
		if ($data['Nombre']!==null)	$sql .=", Nombre='{$data['Nombre']}' ";
		if ($data['Prueba']!=0)		$sql .=", Prueba={$data['Prueba']} ";
		if ($data['Jornada']!=0)	$sql .=", Jornada={$data['Jornada']} ";
		if ($data['Manga']!=0)		$sql .=", Manga={$data['Manga']} ";
		if ($data['Tanda']!=0)		$sql .=", Tanda={$data['Tanda']} ";
		if ($data['Perro']!=0)		$sql .=", Perro={$data['Perro']} ";
		if ($data['Resultado']!=0)	$sql .=", Resultado={$data['Resultado']} ";
		if ($data['Usuario']!==null)$sql .=", Usuario='{$data['Usuario']}' ";
		$sql .= "WHERE (ID=$id);";
		$this->myLogger->trace("Sesiones::update() query string:\n$sql");
		$res= $this->query($sql);
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Delete session with provided name
	 * @param {integer} $id ID primary key
	 * @return "" on success ; otherwise null
	 */
	function delete($id) {
		$this->myLogger->enter();
		if ($id<=1) return $this->error("Invalid Session ID"); // cannot delete if juez<=default 
		$str="DELETE FROM Sesiones WHERE ( ID=$id )";
		$res= $this->query($str);
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}	
	
	/**
	 * Select sesion with provided ID
	 * @param {string} $juez name primary key
	 * @return {array} data on success ; otherwise error string
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Session ID:$id"); // session ID must be positive greater than 0 

		// make query
		$obj=$this->__getObject("Sesiones",$id);
		if (!is_object($obj))	return $this->error("No Session found with provided ID=$id");
		$data= json_decode(json_encode($obj), true); // convert object to array
		$data['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		return $data;
	}
	
	/**
	 * Select sesion with provided ID
	 * @param {string} $juez name primary key
	 * @return {array} data on success ; otherwise error string
	 */
	function selectByNombre($nombre) {
		$this->myLogger->enter();
		if ($nombre==="") return $this->error("Invalid Session Name"); // session name should not be empty
		// make query
		$obj=$this->__selectObject("*","Sesiones","Nombre=$nombre");
		if (!is_object($obj))	return $this->error("No Session found with provided ID=$id");
		$data= json_decode(json_encode($obj), true); // convert object to array
		$data['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		return $data;
	}
	
	/* no select() function */
	
	function enumerate($q="") { // like select but with fixed order
		$this->myLogger->enter();
		// evaluate search criteria for query
		$where="";
		if ($q!=="") $where="Nombre LIKE '%".$q."%'";
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "Sesiones",
				/* WHERE */ $where,
				/* ORDER BY */ "Nombre ASC",
				/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}
}
?>