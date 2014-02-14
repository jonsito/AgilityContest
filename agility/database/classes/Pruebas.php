<?php

require_once("DBObject.php");

class Pruebas extends DBObject {
	
	function insert() {
		$this->myLogger->enter();
		// componemos un prepared statement
		$sql ="INSERT INTO Pruebas (Nombre,Club,Ubicacion,Triptico,Cartel,Observaciones,Cerrada)
			   VALUES(?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ssssssi',$nombre,$club,$ubicacion,$triptico,$cartel,$observaciones,$cerrada);
		if (!$res) return $this->error($this->conn->error);
		
		// iniciamos los valores, chequeando su existencia
		$nombre =	http_request("Nombre","s",null);
		$club =		http_request("Club","s",null);
		$ubicacion=	http_request("Ubicacion","s",null);
		$triptico =	http_request("Triptico","s",null);
		$cartel =	http_request("Cartel","s",null);
		$observaciones = http_request("Observaciones","s",null);
		$cerrada =	http_request("Cerrada","i",0);
		$this->myLogger->debug("Nombre: $nombre Club: $club Ubicacion: $ubicacion Observaciones: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error);
		
		// retrieve PruebaID on newly create prueba
		$pruebaid=$this->conn->insert_id;
		$stmt->close();
		
		// create default 'Equipos' entry for this contest
		$res=$this->query("INSERT INTO Equipos (Prueba,Nombre,Observaciones)
				VALUES ($pruebaid,'-- Sin asignar --','NO BORRAR: PRUEBA $pruebaid' - Equipo por defecto )");
		if (!$res) return $this->error($this->conn->error);
		
		// create eight journeys per contest
		for ($n=1;$n<9;$n++) {
			$sql ="INSERT INTO Jornadas (Prueba,Numero,Nombre,Fecha,Hora)
			VALUES ($pruebaid,$n,'-- Sin asignar --','2013-01-01','00:00:00')";
			$res=$this->query($sql);
			if (!$res) return $this->error($this->conn->error);
		}
		// arriving here means everything ok. notify success
		$this->myLogger->leave();
		return "";
	}
	
	function update() {
		$this->myLogger->enter();
		
		// componemos un prepared statement
		$sql ="UPDATE Pruebas
				SET Nombre=? , Club=? , Ubicacion=? , Triptico=? , Cartel=?, Observaciones=?, Cerrada=?
				WHERE ( ID=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ssssssii',$nombre,$club,$ubicacion,$triptico,$cartel,$observaciones,$cerrada,$id);
		if (!$res) return $this->error($this->conn->error);
		
		// iniciamos los valores, chequeando su existencia
		$nombre =	http_request("Nombre","s",null);
		$id =		http_request("ID","i",0);
		$club =		http_request("Club","s",null);
		$ubicacion=	http_request("Ubicacion","s",null);
		$triptico =	http_request("Triptico","s",null);
		$cartel =	http_request("Cartel","s",null);
		$observaciones = http_request("Observaciones","s",null);
		$cerrada =	http_request("Cerrada","i",0);
		$this->myLogger->debug("Nombre: $nombre Club: $club Ubicacion: $ubicacion Observaciones: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		$stmt->close();
		return "";
	}
	
	/**
	 * Borra una prueba
	 * @param {integer} $id ID de la prueba
	 * @return string
	 */
	function delete($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Prueba ID");
		$res= $this->query("DELETE FROM Pruebas WHERE (ID=$id) AND (Cerrada!=0) ");
		if (!$res) return $this->error($this->conn->error);
		// if affected rows == 0 implica prueba cerrada: notify error
		if ($this->conn->affected_rows==0) 
			return $this->error("Cannot delete prueba $id marked as 'closed'"); 
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
		$closed= http_request("closed","i",0); // si esta declarada, se incluyen las pruebas cerradas
		
		$where = '';
		if ($search!=='') {
			if ($closed==0) $where= " WHERE (
				( (Nombre LIKE '%$search%') OR ( Club LIKE '%$search%') OR ( Ubicacion LIKE '%$search%' ) )
				AND ( Cerrada = 0 )
				) ";
			else $where= " WHERE (
				(Nombre LIKE '%$search%') OR ( Club LIKE '%$search%') OR ( Ubicacion LIKE '%$search%' )
				) ";
		} else {
			if ($closed==0) $where = " WHERE ( Cerrada = 0 ) ";
			else $where="";
		}
		$offset = ($page-1)*$rows;
		$result = array();
		
		// execute first query to know how many elements
		$rs=$this->query("SELECT count(*) FROM Pruebas $where");
		if (!$rs) return $this->error($this->conn->error);
		$row=$rs->fetch_array();
		$result["total"] = $row[0];
		$rs->free();
		
		// second query to retrieve $rows starting at $offset
		$str="SELECT * FROM Pruebas $where ORDER BY $sort $order LIMIT $offset,$rows";
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
	
	/** 
	 * lista de pruebas abiertas
	 */
	function enumerate() {
		$this->myLogger->enter();
		$result = array();

		// evaluate search criteria for query
		$q=http_request("q","s",null);
		$like =  ($q===null) ? "" : " AND ( (Nombre LIKE '%$q%' ) OR (Club LIKE '%$q%') OR (Observaciones LIKE '%$q%') )";
		
		// execute first query to know how many elements
		$rs=$this->query("SELECT count(*) FROM Pruebas WHERE ( Cerrada=0 ) $like");
		if (!$rs) return $this->error($this->conn->error);
		$row=$rs->fetch_row();
		$result["total"] = $row[0];
		
		// second query to retrieve $rows starting at $offset
		$rs=$this->query("SELECT * FROM Pruebas WHERE (Cerrada=0) $like ORDER BY Nombre ASC");
		if (!$rs) return $this->error($this->conn->error);

		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()) {
			array_push($items, $row);
		}
		$result["rows"] = $items;
		// clean and return
		$rs->free();
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Retrieve data on requested prueba id
	 * @param {integer} $id prueba ID
	 * @return null on error, associative array on success
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id==0) return $this->error("Invalid Prueba ID");
		$str="SELECT * FROM Pruebas WHERE ( ID = '$id' )";
		// do_log("get_pruebaByID:: query string is $str");
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// retrieve result into an array
		$result = $rs->fetch_array();  // should only be one element
		$rs->free();
		// clean and return ok
		$this->myLogger->leave();
		return $result;
	}
	
	function selectEquiposByPrueba($id) {
		$this->myLogger->enter();
		if ($id==0) return $this->error("Invalid Prueba ID");
		$q=http_request("q","s","");
		$like="";
		if ($q!=="") $like=" AND ( ( Nombre LIKE '%$q%' ) OR ( Observaciones LIKE '%$q%' ) )";
		$result = array();
		
		// execute first query to know how many elements
		$rs=$this->query("SELECT count(*) FROM Equipos WHERE ( Prueba = $id )".$like);
		if (!$rs) return $this->error($this->conn->error);
		$row=$rs->fetch_row();
		$result["total"] = $row[0];
		$rs->free();
		
		// second query to retrieve $rows starting at $offset
		$str="SELECT * FROM Equipos WHERE ( Prueba = $id )".$like." ORDER BY Nombre ASC";
		do_log("pruebas::selectEquiposByPrueba() query string: $str");
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		// disconnect from database
		$rs->free();
		$this->myLogger->leave();
		return $result;
	}
}

?>