<?php

require_once("DBObject.php");

class Jueces extends DBObject {
	
	/**
	 * Insert a new juez into database
	 * @return {string} "" if ok; null on error
	 */
	function insert() {
		$this->myLogger->enter();
		// componemos un prepared statement
		$sql ="INSERT INTO Jueces (Nombre,Direccion1,Direccion2,Telefono,Internacional,Practicas,Email,Observaciones)
			   VALUES(?,?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('ssssiiss',$nombre,$direccion1,$direccion2,$telefono,$internacional,$practicas,$email,$observaciones);
		if (!$res) return $this->error($this->conn->error);
		
		// iniciamos los valores, chequeando su existencia
		$nombre =		http_request("Nombre","s",null); // pkey not null
		$direccion1 =	http_request("Direccion1","s",null);
		$direccion2 =	http_request("Direccion2","s",null);
		$telefono = 	http_request("Telefono","s",null);
		$internacional= http_request("Internacional","i",0); // not null
		$practicas =	http_request("Practicas","i",0);
		$email =		http_request("Email","s",null); // not null
		$observaciones=	http_request("Observaciones","s",null);
		
		$this->myLogger->debug("Nombre: $nombre Dir1: $direccion1 Dir2: $Direccion2 Tel: $telefono I: $internacional P: $practicas Email: $email Obs: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error);
		$stmt->close();
		$this->myLogger->leave();
		return ""; 
	}
	
	function update() {
		$this->myLogger->enter();
		// componemos un prepared statement
		$sql ="UPDATE Jueces SET Nombre=? , Direccion1=? , Direccion2=? , Telefono=? , Internacional=? , Practicas=? , Email=? , Observaciones=?
		       WHERE ( Nombre=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ssssiisss',$nombre,$direccion1,$direccion2,$telefono,$internacional,$practicas,$email,$observaciones,$viejo);
		if (!$res) return $this->error($this->conn->error);
		
		// iniciamos los valores, chequeando su existencia
		$nombre =		http_request("Nombre","s",null); // pkey not null
		$viejo =		http_request("Viejo","s",null); // to allow change name
		$direccion1 =	http_request("Direccion1","s",null);
		$direccion2 =	http_request("Direccion2","s",null);
		$telefono = 	http_request("Telefono","s",null);
		$internacional= http_request("Internacional","i",0); // not null
		$practicas =	http_request("Practicas","i",0);
		$email =		http_request("Email","s",null); // not null
		$observaciones=	http_request("Observaciones","s",null);
		$this->myLogger->debug("N.Viejo: $viejo N.nuevo: $nombre Dir1: $direccion1 Dir2: $direccion2 Tel: $telefono I: $internacional P: $practicas Email: $email Obs: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error);
		$stmt->close();
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Delete juez with provided name
	 * @param {string} $juez name primary key
	 * @return "" on success ; otherwise null
	 */
	function delete($juez) {
		$this->myLogger->enter();
		if ($juez==='-- Sin asignar --') return $this->error("Cannot delete default juez"); 
		$str="DELETE FROM Jueces WHERE ( Nombre='$juez' )";
		$res= $this->query($str);
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	} 
	
	function select() {
		$this->myLogger->enter();
		// evaluate offset and row count for query
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$rows = isset($_GET['rows']) ? intval($_GET['rows']) : 20;
		$sort = isset($_GET['sort']) ? strval($_GET['sort']) : 'Nombre';
		$order = isset($_GET['order']) ? strval($_GET['order']) : 'ASC';
		$search =  isset($_GET['where']) ? strval($_GET['where']) : '';
		$where = '';
		if ($search!=='') $where=" WHERE ( (Nombre LIKE '%$search%') OR ( Email LIKE '%$search%') ) ";
		$offset = ($page-1)*$rows;
		$result = array();
		
		// execute first query to know how many elements
		$rs=$this->query("SELECT count(*) FROM Jueces $where");
		if (!$rs) return $this->error($this->conn->error);
		$row=$rs->fetch_array();
		$result["total"] = $row[0];
		
		// second query to retrieve $rows starting at $offset
		$str="SELECT * FROM Jueces $where ORDER BY $sort $order LIMIT $offset,$rows";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		// clean and return
		$rs->free();
		$this->myLogger->leave();
		return $result;
	}
	
	function enumerate() { // like select but do not perform offset/rows operation
		$this->myLogger->enter();
		// evaluate search criteria for query
		$q=http_request("q","s",null);
		$like =  ($q===null) ? "" : " WHERE Nombre LIKE '%".$q."%'";
		
		// execute first query to know how many elements
		$rs=$this->query("SELECT count(*) FROM Jueces ".$like);
		if (!$rs) return $this->error($this->conn->error);
		$row=$rs->fetch_row();
		$result["total"] = $row[0];
		
		// second query to retrieve $rows starting at $offset
		$str="SELECT * FROM Jueces ".$like." ORDER BY Nombre ASC";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		// clean and return
		$rs->free();
		$this->myLogger->leave();
		return $result;
	}
}
?>