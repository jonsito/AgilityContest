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
		$nombre 	= http_request("Nombre","s",null); // primary key
		$telefono = http_request('Telefono',"s",null);
		$email = http_request('Email',"s",null);
		$club	= http_request('Club',"s",null); // not null
		$observaciones= http_request('Observaciones',"s",null);
		$this->myLogger->info("Nombre: $nombre Telefono: $telefono Email: $email Club: $club Observaciones: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		$stmt->close();
		if (!$res) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}
	
	function update() {
		$this->myLogger->enter();
		
		// componemos un prepared statement
		$sql ="UPDATE Guias SET Nombre=? , Telefono=? , Email=? , Club=? , Observaciones=? WHERE ( Nombre=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('ssssss',$nombre,$telefono,$email,$club,$observaciones,$viejo);
		if (!$res) return $this->error($this->conn->error); 
		
		// iniciamos los valores, chequeando su existencia
		$nombre 	= http_request("Nombre","s",null); // primary key
		$viejo 	= http_request("Viejo","s",null); 
		$telefono = http_request('Telefono',"s",null);
		$email = http_request('Email',"s",null);
		$club	= http_request('Club',"s",null); // not null
		$observaciones= http_request('Observaciones',"s",null);
		
		$this->myLogger->info("Viejo: $viejo Nombre: $nombre Telefono: $telefono Email: $email Club: $club Observaciones: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		$stmt->close();
		if (!$res) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}
	
	function delete($nombre) {
		$this->myLogger->enter();
		if ($nombre===null) return $this->error("No guia name provided");
		// fase 1: desasignamos los perros de este guia
		$res= $this->query("UPDATE Perros SET GUIA='-- Sin asignar --' WHERE ( Guia='$nombre')");
		if (!$res) return $this->error($this->conn->error); 
		// fase 2: borramos el guia de la base de datos
		$res= $this->query("DELETE FROM Guias WHERE (Nombre='$nombre')");
		if (!$res) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * remove a handler from provided club
	 * @param unknown $guia
	 * @return "" on success ; null on error
	 */
	function orphan($guia) {
		if ($guia===null) return $this->error("No handler name provided"); 
		$this->myLogger->enter();
		$res= $this->query("UPDATE Guias SET Club='-- Sin asignar --' WHERE ( Nombre='$guia' )");
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	function select() {
		$this->myLogger->enter();
		// evaluate offset and row count for query
		$page= http_request("page","i",1);
		$rows= http_request("rows","i",20);
		$sort= http_request("sort","s","Nombre");
		$order=http_request("order","s","ASC");
		$search=http_Request("where","s","");
		$where = '';
		if ($search!=='') $where=" WHERE ( (Nombre LIKE '%$search%') OR ( Club LIKE '%$search%') ) ";
		$offset = ($page-1)*$rows;
		$result = array();
		
		// execute first query to know how many elements
		$rs=$this->query("SELECT count(*) FROM Guias $where");
		if (!$rs) return $this->error($this->conn->error);
		$row=$rs->fetch_array();
		$rs->free();
		$result["total"] = $row[0];
		
		// second query to retrieve $rows starting at $offset
		$rs=$this->query("SELECT * FROM Guias $where ORDER BY $sort $order LIMIT $offset,$rows");
		if (!$rs) return $this->error($this->conn->error); 
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		// disconnect from database and return composed array
		$rs->free();
		$this->myLogger->leave();
		return $result;
	}
	
	function enumerate() { // like select but do not provide indexed block query
		$this->myLogger->enter();
		// evaluate search string
		$q=http_request("q","s",null);
		$like =  ($q===null) ? "" : " WHERE ( ( Nombre LIKE '%$q%' ) OR ( Club LIKE '%$q%' ) )";
		
		$result = array();
		// execute first query to know how many elements
		$rs=$this->query("SELECT count(*) FROM Guias ".$like);
		if (!$rs) return $this->error($this->conn->error);
		$row=$rs->fetch_row();
		$rs->free();
		$result["total"] = $row[0];
		
		// second query to retrieve $rows starting at $offset
		$rs=$this->query("SELECT Nombre,Club FROM Guias ".$like." ORDER BY Club,Nombre");
		if (!$rs) return $this->error($this->conn->error);
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		// disconnect from database and return
		$rs->free();
		$this->myLogger->leave();
		return $result;
	}
	
	/** 
	 * Enumerate by club (exact match)
	 * @param {string} $club Club name (key search) 
	 * @return result on success; null on error
	 */
	function selectByClub($club) {
		$this->myLogger->enter();
		if ($club===null) return $this->error("No club name provided");
		$result = array();
		$items = array();
		
		// execute first query to know how many elements
		$club=strval($_GET['Club']);
		$str="SELECT count(*) FROM Guias WHERE ( Club = '$club' )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		$row=$rs->fetch_row();
		$result["total"] = $row[0];
		$rs->free();
		// second query to retrieve elements
		$str="SELECT * FROM Guias WHERE ( Club ='$club' ) ORDER BY Nombre ASC";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		// retrieve result into an array
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		// clean environment and return
		$rs->free();
		$result["rows"] = $items;
		return result;
	}
	
	/**
	 * Select a (single) entry that matches with provided handler name
	 * @param {string} $nombre handler's name
	 * @return result on success; null on error
	 */
	function selectByNombre($nombre) {
		$this->myLogger->enter();
		if ($nombre===null) return $this->error("No handler name provided"); 
		// query to retrieve $rows starting at $offset
		$str="SELECT * FROM Guias WHERE ( Nombre = '$nombre' )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// retrieve result into an array
		$result = array();
		while($row = $rs->fetch_array()){ // should only be one item
			$row['Operation']='update'; // dirty trick to ensure that form operation is fixed
			array_push($result, $row);
		}
		// disconnect from database
		$rs->free();
		$this->myLogger->leave();
		return $result;
	}
}
	
?>