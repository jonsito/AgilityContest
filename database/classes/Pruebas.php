<?php

require_once("DBConnection.php");

class Pruebas {
	protected $conn;
	protected $file;
	public $errormsg; // should be public to access to from caller

	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @throws Exception if cannot contact database
	 */
	function __construct($file) {
		// connect database
		$this->file=$file;
		$this->conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
		if (!$this->conn) {
			$this->errormsg="$file::construct() cannot contact database";
			throw new Exception($this->errormsg);
		}
	}

	/**
	 * Destructor
	 * Just disconnect from database
	 */
	function  __destruct() {
		DBConnection::closeConnection($this->conn);
	}
	
	function insert() {
		do_log("insertPrueba:: enter");
		// componemos un prepared statement
		$sql ="INSERT INTO Pruebas (Nombre,Club,Ubicacion,Triptico,Cartel,Observaciones,Cerrada)
			   VALUES(?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) {
			$this->errormsg="insertPrueba::prepare() failed ".$this->conn->error;
			return null;
		}
		$res=$stmt->bind_param('ssssssi',$nombre,$club,$ubicacion,$triptico,$cartel,$observaciones,$cerrada);
		if (!$res) {
			$this->errormsg="insertPrueba::bind() failed ".$this->conn->error;
			return null;
		}
		
		// iniciamos los valores, chequeando su existencia
		$nombre =	http_request("Nombre","s",null);
		$club =		http_request("Club","s",null);
		$ubicacion=	http_request("Ubicacion","s",null);
		$triptico =	http_request("Triptico","s",null);
		$cartel =	http_request("Cartel","s",null);
		$observaciones = http_request("Observaciones","s",null);
		$cerrada =	http_request("Cerrada","i",0);
		do_log("insertPrueba:: retrieved data from web client");
		do_log("Nombre: $nombre Club: $club Ubicacion: $ubicacion Observaciones: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) {
			$this->errormsg="insertPrueba:: Error: ".$this->conn->error;
			return null;
		}
		
		// retrieve PruebaID on newly create prueba
		$pruebaid=$this->conn->insert_id;
		$stmt->close();
		
		// create default 'Equipos' entry for this contest
		$res=$conn->query("INSERT INTO Equipos (Prueba,Nombre,Observaciones)
				VALUES ($pruebaid,'-- Sin asignar --','NO BORRAR: PRUEBA $pruebaid' - Equipo por defecto )");
		if (!$res) {
			$this->errormsg="insertPrueba::insertEquipo() failed: ".$this->conn->error;
			return null;
		}
		
		// create eight journeys per contest
		for ($n=1;$n<9;$n++) {
			$sql ="INSERT INTO Jornadas (Prueba,Numero,Nombre,Fecha,Hora)
			VALUES ($pruebaid,$n,'-- Sin asignar --','2013-01-01','00:00:00')";
			$res=$this->conn->query($sql);
			if (!$res) {
				$this->errormsg="insertPrueba::insertJornada($n) failed ".$this->conn->error;
				return null;
			}
		}
		// arriving here means everything ok. notify success
		do_log("insertPrueba:: exit OK");
		return "";
	}
	
	function update() {
		do_log("updatePrueba:: enter");
		
		// componemos un prepared statement
		$sql ="UPDATE Pruebas
				SET Nombre=? , Club=? , Ubicacion=? , Triptico=? , Cartel=?, Observaciones=?, Cerrada=?
				WHERE ( ID=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) {
			$this->errormsg="updatePrueba::prepare() failed ".$this->conn->error;
			return null;
		}
		$res=$stmt->bind_param('ssssssii',$nombre,$club,$ubicacion,$triptico,$cartel,$observaciones,$cerrada,$id);
		if (!$res) {
			$this->errormsg="updatePrueba::bind() failed ".$this->conn->error;
			return null;
		}
		
		// iniciamos los valores, chequeando su existencia
		$nombre =	http_request("Nombre","s",null);
		$id =		http_request("ID","i",0);
		$club =		http_request("Club","s",null);
		$ubicacion=	http_request("Ubicacion","s",null);
		$triptico =	http_request("Triptico","s",null);
		$cartel =	http_request("Cartel","s",null);
		$observaciones = http_request("Observaciones","s",null);
		$cerrada =	http_request("Cerrada","i",0);
		
		do_log("updatePrueba:: retrieved data from client");
		do_log("Nombre: $nombre Club: $club Ubicacion: $ubicacion Observaciones: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) {
			$this->errormsg="updatePrueba:: Error: ".$this->conn->error;
			return null;
		} 
		do_log("updatePrueba:: exit OK");
		$stmt->close();
		return "";
	}
	
	/**
	 * Borra una prueba
	 * @param {integer} $id ID de la prueba
	 * @return string
	 */
	function delete($id) {
		do_log("deletePrueba:: enter");
		if ($id==0) {
			$this->errormsg="deletePrueba:: no valid prueba ID provided for deletion";
			return null;
		}
		$res= $this->conn->query("DELETE FROM Pruebas WHERE (ID=$id) AND (Cerrada!=0) ");
		if (!$res) {
			$this->errormsg="deletePrueba::query(delete) Error: ".$this->conn->error;
			return null;
		}
		// if affected rows == 0 implica prueba cerrada: notify error
		if ($this->conn->affected_rows==0) {
			$this->errormsg="deletePrueba:: cannot delete prueba $id marked as 'closed'";
			return null;
		}
		do_log("deletePrueba:: exit OK");
		return "";
	}
	
	function select() {
		do_log("selectPuebas():: enter");
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
		$rs=$this->conn->query("SELECT count(*) FROM Pruebas $where");
		if (!$rs) {
			$this->errormsg="selectPruebas::select( count (*) ) error ".$this->conn->error;
			return null;
		}
		$row=$rs->fetch_array();
		$result["total"] = $row[0];
		$rs->free();
		// second query to retrieve $rows starting at $offset
		$str="SELECT * FROM Pruebas $where ORDER BY $sort $order LIMIT $offset,$rows";
		do_log("selectPruebas() query is: \n$str");
		$rs=$this->conn->query($str);
		if (!$rs) {
			$this->errormsg="selectPruebas::select( * ) error ".$this->conn->error;
			return null;
		}
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		// clean and return
		$rs->free();
		do_log("selectPruebas:: exit OK");
		return $result;
	}
	
	/** 
	 * lista de pruebas abiertas
	 */
	function enumerate() {
		do_log("enumeratePruebas:: enter");

		// evaluate search criteria for query
		$q=http_request("q","s",null);
		$like =  ($q===null) ? "" : " AND ( (Nombre LIKE '%$q%' ) OR (Club LIKE '%$q%') OR (Observaciones LIKE '%$q%') )";

		$result = array();
		// execute first query to know how many elements
		$rs=$this->conn->query("SELECT count(*) FROM Pruebas WHERE ( Cerrada=0 ) $like");
		if (!$rs){
			$this->errormsg="select ( count(*) ) failed: ".$this->conn->error;
			return null;
		}
		$row=$rs->fetch_row();
		$result["total"] = $row[0];
		
		// second query to retrieve $rows starting at $offset
		$rs=$this->conn->query("SELECT * FROM Pruebas WHERE (Cerrada=0) $like ORDER BY Nombre ASC");
		if (!$rs){
			$this->errormsg="select ( * ) failed: ".$this->conn->error;
			return null;
		}
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()) {
			array_push($items, $row);
		}
		$result["rows"] = $items;
		// clean and return
		$rs->free();
		do_log("enumeratePruebas:: exit OK");
		return $result;
	}
	
	function selectByID($id) {
		do_log("selectPruebaByID:: enter");
		if ($id==0) {
			$this->errormsg="selectPruebaByID: invalid prueba ID";
			return null;
		}
		$str="SELECT * FROM Pruebas WHERE ( ID = '$id' )";
		do_log("get_pruebaByID:: query string is $str");
		$rs=$this->conn->query($str);
		if (!$rs) {
			$this->errormsg="selectPruebaByID::query() error ".$this->conn->error;
			return null;
		}
		// retrieve result into an array
		$result = array();
		while($row = $rs->fetch_array()){ // should only be one element
			array_push($result, $row);
		}
		// clean and return ok
		do_log("selectPruebaByID:: exit OK");
		$rs->free();
		return $result;
	}
	
}

?>