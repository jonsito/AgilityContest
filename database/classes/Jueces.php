<?php

require_once("DBConnection.php");

class Jueces {
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
	/**
	 * Insert a new juez into database
	 * @return {string} "" if ok; null on error
	 */
	function insert() {
		log_enter($this->file);
		// componemos un prepared statement
		$sql ="INSERT INTO Jueces (Nombre,Direccion1,Direccion2,Telefono,Internacional,Practicas,Email,Observaciones)
			   VALUES(?,?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) {
			$this->errormsg="insertDog::prepare() failed ".$this->conn->error;
			return null;
		}
		$res=$stmt->bind_param('ssssiiss',$nombre,$direccion1,$direccion2,$telefono,$internacional,$practicas,$email,$observaciones);
		if (!$res) {
			$this->errormsg="insertJuez::prepare() failed ".$this->conn->error;
			return null;
		}
		
		// iniciamos los valores, chequeando su existencia
		$nombre =		http_request("Nombre","s",null); // pkey not null
		$direccion1 =	http_request("Direccion1","s",null);
		$direccion2 =	http_request("Direccion2","s",null);
		$telefono = 	http_request("Telefono","s",null);
		$internacional= http_request("Internacional","i",0); // not null
		$practicas =	http_request("Practicas","i",0);
		$email =		http_request("Email","s",null); // not null
		$observaciones=	http_request("Observaciones","s",null);
		
		do_log("insertJuez:: retrieved data from client");
		do_log("Nombre: $nombre Dir1: $direccion1 Dir2: $Direccion2 Tel: $telefono");
		do_log("I: $internacional P: $practicas Email: $email Obs: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) {
			$this->errormsg="insertJuez:: Error: ".$this->conn->error;
			return null;
		}
		$stmt->close();
		log_exit($this->file);
		return ""; 
	}
	
	function update() {
		log_enter($this->file);
		// componemos un prepared statement
		$sql ="UPDATE Jueces SET Nombre=? , Direccion1=? , Direccion2=? , Telefono=? , Internacional=? , Practicas=? , Email=? , Observaciones=?
		       WHERE ( Nombre=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) {
			$this->errormsg="juezFunctions::updateJuez() prepare() failed ".$this->conn->error;
			return null;
		}
		$res=$stmt->bind_param('ssssiisss',$nombre,$direccion1,$direccion2,$telefono,$internacional,$practicas,$email,$observaciones,$viejo);
		if (!$res) {
			$this->errormsg="juezFunctions::updateJuez() bind() failed ".$this->conn->error;
			return null;
		}
		
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
		
		do_log("juezFunctions::updateJuez() retrieved data from client");
		do_log("N.Viejo: $viejo N.nuevo: $nombre Dir1: $direccion1 Dir2: $direccion2 Tel: $telefono");
		do_log("I: $internacional P: $practicas Email: $email Obs: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) {
			$this->errormsg="juezFunctions::updateJuez() Error: ".$this->conn->error;
			return null;
		}
		$stmt->close();
		log_exit($this->file);
		return "";
	}
	
	/**
	 * Delete juez with provided name
	 * @param {string} $juez name primary key
	 * @return "" on success ; otherwise null
	 */
	function delete($juez) {
		log_enter($this->file);
		if ($juez==='-- Sin asignar --') {
			$this->errormsg="juezFunctions::deleteJuez() Ignore deletion of default value";
			return null;
		}
		$str="DELETE FROM Jueces WHERE ( Nombre='$juez' )";
		$res= $this->conn->query($str);
		if (!$res) {
			$this->errormsg="juezFunctions::deleteJuez() Error: ".$this->conn->error;
			return null;
		}
		log_exit($this->file);
		return "";
	} 
	
	function select() {
		log_enter($this->file);
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
		$rs=$this->conn->query("SELECT count(*) FROM Jueces $where");
		if (!$rs){
			$this->errormsg="select ( count(*) ) failed: ".$this->conn->error;
			return null;
		}
		$row=$rs->fetch_array();
		$result["total"] = $row[0];
		
		// second query to retrieve $rows starting at $offset
		$str="SELECT * FROM Jueces $where ORDER BY $sort $order LIMIT $offset,$rows";
		do_log("select_jueces:: query string is $str");
		$rs=$this->conn->query($str);
		if (!$rs){
			$this->errormsg="select ( * ) failed: ".$this->conn->error;
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
		log_exit($this->file);
		return $result;
	}
	
	function enumerate() { // like select but do not perform offset/rows operation
		log_enter($this->file);
		// evaluate search criteria for query
		$q=http_request("q","s",null);
		$like =  ($q===null) ? "" : " WHERE Nombre LIKE '%".$q."%'";
		
		// execute first query to know how many elements
		$rs=$this->conn->query("SELECT count(*) FROM Jueces ".$like);
		if (!$rs){
			$this->errormsg="select ( count(*) ) failed: ".$this->conn->error;
			return null;
		}
		$row=$rs->fetch_row();
		$result["total"] = $row[0];
		
		// second query to retrieve $rows starting at $offset
		$str="SELECT * FROM Jueces ".$like." ORDER BY Nombre ASC";
		do_log("enumerate_jueces::query() $str");
		$rs=$this->conn->query($str);
		if (!$rs) {
			$this->errormsg="enumerate_jueces::query() failed: ".$this->conn->error;
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
		log_exit($this->file);
		return $result;
	}
}
?>