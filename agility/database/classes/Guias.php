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
		if (!$res) return $this->error($stmt->error);  
		
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
		$res=$stmt->bind_param('sssisi',$nombre,$telefono,$email,$club,$observaciones,$guiaid);
		if (!$res) return $this->error($stmt->error); 
		
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
		if (!$res) return $this->error($stmt->error); 
		$stmt->close();
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
	function orphan($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Guia ID"); 
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
		$page=http_request("page","i",1);
		$rows=http_request("rows","i",50);
		$limit="";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit="".$offset.",".$rows;
		}	
		$where = "(Guias.Club=Clubes.ID)";
		if ($search!=='') $where="(Guias.Club=Clubes.ID) AND ( (Guias.Nombre LIKE '%$search%') OR ( Clubes.Nombre LIKE '%$search%') ) ";
		$result=$this->__select(
				/* SELECT */ "Guias.ID, Guias.Nombre, Telefono, Guias.Email, Club, Clubes.Nombre AS NombreClub, Guias.Observaciones",
				/* FROM */ "Guias,Clubes",
				/* WHERE */ $where,
				/* ORDER BY */ $sort." ".$order,
				/* LIMIT */ $limit
		);
		$this->myLogger->leave();
		return $result;
	}
	
	function enumerate() { // like select but do not provide order query. Used in comboboxes
		$this->myLogger->enter();
		// evaluate search string
		$q=http_request("q","s","");
		$where="(Guias.Club=Clubes.ID)";
		if ($q!=="") $where="(Guias.Club=Clubes.ID AND ( ( Guias.Nombre LIKE '%$q%' ) OR ( Clubes.Nombre LIKE '%$q%' ) )";
		$result=$this->__select(
				/* SELECT */ "Guias.ID AS ID ,Guias.Nombre AS Nombre, Guias.Club AS Club,Clubes.Nombre AS NombreClub",
				/* FROM */ "Guias,Clubes",
				/* WHERE */ $where,
				/* ORDER BY */ "NombreClub ASC, Nombre ASC",
				/* LIMIT */ ""
		);
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
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "Guias",
				/* WHERE */ "( Club=$club )",
				/* ORDER BY */ "Nombre ASC",
				/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Select a (single) entry that matches with provided handler ID
	 * @param {integer} $id Handler ID primary key
	 * @return result on success; null on error
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Provided Handler ID");
		$data= $this->__singleSelect(
				/* SELECT */ "*",
				/* FROM */ "Guias",
				/* WHERE */ "( ID=$id )"
		); 
		if (!$data)	return $this->error("No handler found with ID=$id");
		$data['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		return $data;
	}
}
	
?>