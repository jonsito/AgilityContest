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
		$nombre =		http_request("Nombre","s",null,false); // pkey not null
		$direccion1 =	http_request("Direccion1","s",null,false);
		$direccion2 =	http_request("Direccion2","s",null,false);
		$telefono = 	http_request("Telefono","s",null,false);
		$internacional= http_request("Internacional","i",0); // not null
		$practicas =	http_request("Practicas","i",0);
		$email =		http_request("Email","s",null,false); // not null
		$observaciones=	http_request("Observaciones","s",null,false);
		
		$this->myLogger->debug("Nombre: $nombre Dir1: $direccion1 Dir2: $Direccion2 Tel: $telefono I: $internacional P: $practicas Email: $email Obs: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error);
		$stmt->close();
		$this->myLogger->leave();
		return ""; 
	}
	
	/**
	 * Update juez data
	 * @param {integer} $id Juez ID primary key
	 * @return {string} "" on success; null on error
	 */
	function update($id) {
		$this->myLogger->enter();
		if ($id==0) return $this->error("Invalid Juez ID");
		// componemos un prepared statement
		$sql ="UPDATE Jueces SET Nombre=? , Direccion1=? , Direccion2=? , Telefono=? , Internacional=? , Practicas=? , Email=? , Observaciones=?
		       WHERE ( ID=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ssssiisss',$nombre,$direccion1,$direccion2,$telefono,$internacional,$practicas,$email,$observaciones,$idjuez);
		if (!$res) return $this->error($this->conn->error);
		
		// iniciamos los valores, chequeando su existencia
		$nombre =		http_request("Nombre","s",null); // pkey not null
		$idjuez =		$id;
		$direccion1 =	http_request("Direccion1","s",null);
		$direccion2 =	http_request("Direccion2","s",null);
		$telefono = 	http_request("Telefono","s",null);
		$internacional= http_request("Internacional","i",0); // not null
		$practicas =	http_request("Practicas","i",0);
		$email =		http_request("Email","s",null); // not null
		$observaciones=	http_request("Observaciones","s",null);
		$this->myLogger->debug("ID: $id Nombre: $nombre Dir1: $direccion1 Dir2: $direccion2 Tel: $telefono I: $internacional P: $practicas Email: $email Obs: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error);
		$stmt->close();
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Delete juez with provided name
	 * @param {integer} $id ID primary key
	 * @return "" on success ; otherwise null
	 */
	function delete($id) {
		$this->myLogger->enter();
		if ($id<=1) return $this->error("Invalid Juez ID"); // cannot delete if juez<=default 
		$str="DELETE FROM Jueces WHERE ( ID=$id )";
		$res= $this->query($str);
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}	
	
	/**
	 * Select juez with provided ID
	 * @param {string} $juez name primary key
	 * @return "" on success ; otherwise null
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Juez ID"); // Juez ID must be positive greater than 0 
		$str="SELECT * FROM Jueces WHERE ( ID=$id )";
		$rs= $this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$row=$rs->fetch_array();
		$rs->free();
		if(!$row) return $this->error("No Juez found with provided ID $id");
		$this->myLogger->leave();
		return $row;
	} 
	
	function select() {
		$this->myLogger->enter();
		// evaluate offset and row count for query
		$sort = isset($_GET['sort']) ? strval($_GET['sort']) : 'Nombre';
		$order = isset($_GET['order']) ? strval($_GET['order']) : 'ASC';
		$search =  isset($_GET['where']) ? strval($_GET['where']) : '';
		$where = '';
		if ($search!=='') $where=" WHERE ( (Nombre LIKE '%$search%') OR ( Email LIKE '%$search%') ) ";
		$result = array();
		
		// query to retrieve data
		$str="SELECT * FROM Jueces $where ORDER BY $sort $order";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		$result["total"] = $rs->num_rows;
		// clean and return
		$rs->free();
		$this->myLogger->leave();
		return $result;
	}
	
	function enumerate() { // like select but do not perform search operation
		$this->myLogger->enter();
		// evaluate search criteria for query
		$q=http_request("q","s",null);
		$like =  ($q===null) ? "" : " WHERE Nombre LIKE '%".$q."%'";
		
		//  query to retrieve data
		$str="SELECT * FROM Jueces ".$like." ORDER BY Nombre ASC";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		$result["total"] = $rs->num_rows;
		// clean and return
		$rs->free();
		$this->myLogger->leave();
		return $result;
	}
}
?>