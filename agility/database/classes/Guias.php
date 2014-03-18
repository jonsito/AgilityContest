<?php
require_once("DBObject.php");

class Guias extends DBObject {
	
	function insert() {
		$this->myLogger->enter();
		
		// componemos un prepared statement
		$sql ="INSERT INTO Guias (Nombre,Telefono,Email,Club,Observaciones)
			   VALUES(?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('sssss',$nombre,$telefono,$email,$club,$observaciones);
		if (!$res) return $this->error($this->conn->error);  
		
		// iniciamos los valores, chequeando su existencia
		$nombre 	= http_request("Nombre","s",null,false); // primary key
		$telefono = http_request('Telefono',"s",null,false);
		$email = http_request('Email',"s",null,false);
		$club	= http_request('Club',"s",null,false); // not null
		$observaciones= http_request('Observaciones',"s",null,false);
		$this->myLogger->info("Nombre: $nombre Telefono: $telefono Email: $email Club: $club Observaciones: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error); 
		$stmt->close();
		$this->myLogger->leave();
		return "";
	}
	
	function update($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Guia ID provided");
		// componemos un prepared statement
		$sql ="UPDATE Guias SET Nombre=? , Telefono=? , Email=? , Club=? , Observaciones=? WHERE ( ID=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('ssssss',$nombre,$telefono,$email,$club,$observaciones,$guiaid);
		if (!$res) return $this->error($this->conn->error); 
		
		// iniciamos los valores, chequeando su existencia
		$nombre 	= http_request("Nombre","s",null,false); 
		$telefono = http_request('Telefono',"s",null,false);
		$email = http_request('Email',"s",null,false);
		$club	= http_request('Club',"s",null,false); // not null
		$observaciones= http_request('Observaciones',"s",null,false);
		$guiaid 	= $id; // primary key
		
		$this->myLogger->info("ID: $id Nombre: $nombre Telefono: $telefono Email: $email Club: $club Observaciones: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		$stmt->close();
		if (!$res) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}
	
	function delete($id) {
		$this->myLogger->enter();
		if ($id<=1) return $this->error("Invalid Guia ID provided"); // cannot delete ID=1
		// fase 1: desasignamos los perros de este guia (los asignamos al guia id=1)
		$res= $this->query("UPDATE Perros SET GUIA=1 WHERE ( Guia=$id )");
		if (!$res) return $this->error($this->conn->error); 
		// fase 2: borramos el guia de la base de datos
		$res= $this->query("DELETE FROM Guias WHERE (ID=$id)");
		if (!$res) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * remove a handler from provided club
	 * @param {integer} $id Guia ID primary key
	 * @return "" on success ; null on error
	 */
	function orphan($i) {
		$this->myLogger->enter();
		if ($i<=0) return $this->error("Invalid Guia ID"); 
		$res= $this->query("UPDATE Guias SET Club=1 WHERE ( ID=$id )");
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	function select() {
		$this->myLogger->enter();
		// evaluate offset and row count for query
		$sort= http_request("sort","s","Nombre");
		$order=http_request("order","s","ASC");
		$search=http_Request("where","s","");
		$where = '';
		if ($search!=='') $where=" AND ( (Guias.Nombre LIKE '%$search%') OR ( Clubes.Nombre LIKE '%$search%') ) ";
		$result = array();
		
		// query to retrieve data
		$rs=$this->query(
				"SELECT Guias.ID,Guias.Nombre,Telefono,Guias.Email,Club, Clubes.Nombre AS NombreClub,Guias.Observaciones
				FROM Guias,Clubes 
				WHERE (Guias.Club=Clubes.ID) $where 
				ORDER BY $sort $order");
		if (!$rs) return $this->error($this->conn->error); 
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		$result["total"] = $rs->num_rows;
		// disconnect from database and return composed array
		$rs->free();
		$this->myLogger->leave();
		return $result;
	}
	
	function enumerate() { // like select but do not provide order query
		$this->myLogger->enter();
		// evaluate search string
		$q=http_request("q","s",null);
		$like =  ($q===null) ? "" : " WHERE ( ( Nombre LIKE '%$q%' ) OR ( Club LIKE '%$q%' ) )";
		
		$result = array();
		// query to retrieve data
		$rs=$this->query("SELECT Nombre,Club FROM Guias ".$like." ORDER BY Club,Nombre");
		if (!$rs) return $this->error($this->conn->error);
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		$result["total"] = $rs->num_rows;
		// disconnect from database and return
		$rs->free();
		$this->myLogger->leave();
		return $result;
	}
	
	/** 
	 * Enumerate by club (exact match)
	 * @param {integer} $club Club ID primary key
	 * @return result on success; null on error
	 */
	function selectByClub($club) {
		$this->myLogger->enter();
		if ($club<=0) return $this->error("Invalid Club ID provided");
		$result = array();
		$items = array();
		
		// execute first query to know how many elements
		$str="SELECT count(*) FROM Guias WHERE ( Club=$club )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		$row=$rs->fetch_row();
		$result["total"] = $row[0];
		$rs->free();
		// second query to retrieve elements
		$str="SELECT * FROM Guias WHERE ( Club=$club ) ORDER BY Nombre ASC";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		// retrieve result into an array
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		// clean environment and return
		$rs->free();
		$result["rows"] = $items;
		return $result;
	}
	
	/**
	 * Select a (single) entry that matches with provided handler ID
	 * @param {integer} $id Handler ID primary key
	 * @return result on success; null on error
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		if ($nombre<=0) return $this->error("Invalid Provided Handler ID"); 
		// query to retrieve $rows starting at $offset
		$str="SELECT * FROM Guias WHERE ( ID = $id )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// retrieve result into an array
		$row = $rs->fetch_array();
		$rs->free();
		if (!$row)	return $this->error("No handler found with ID=$id");
		$row['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		return $row;
	}
}
	
?>