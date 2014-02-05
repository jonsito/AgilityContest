<?php
require_once("DBConnection.php");

class Guias {
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
		log_enter($this->file);
		
		// componemos un prepared statement
		$sql ="INSERT INTO Guias (Nombre,Telefono,Email,Club,Observaciones)
			   VALUES(?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) {
			$this->errormsg="insertGuia::prepare() failed ".$this->conn->error;
			return null;
		}
		$res=$stmt->bind_param('sssss',$nombre,$telefono,$email,$club,$observaciones);
		if (!$res) {
			$msg="insertGuia::prepare() failed ".$this->conn->error;
			return null;
		}
		
		// iniciamos los valores, chequeando su existencia
		$nombre 	= http_request("Nombre","s",null); // primary key
		$telefono = http_request('Telefono',"s",null);
		$email = http_request('Email',"s",null);
		$club	= http_request('Club',"s",null); // not null
		$observaciones= http_request('Observaciones',"s",null);
		do_log("insertGuia:: retrieved data from client");
		do_log("Nombre: $nombre Telefono: $telefono Email: $email Club: $club Observaciones: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		$stmt->close();
		if (!$res) {
			$this->errormsg="insertGuia:: Error: ".$this->conn->error;
			return null;
		}
		log_exit($this->file);
		return "";
	}
	
	function update() {
		log_enter($this->file);
		
		// componemos un prepared statement
		$sql ="UPDATE Guias SET Nombre=? , Telefono=? , Email=? , Club=? , Observaciones=? WHERE ( Nombre=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) {
			$this->errormsg="updateGuia::prepare() failed ".$this->conn->error;
			return null;
		}
		$res=$stmt->bind_param('ssssss',$nombre,$telefono,$email,$club,$observaciones,$viejo);
		if (!$res) {
			$this->errormsg="updateGuia::bind() failed ".$this->conn->error;
			return null;
		}
		
		// iniciamos los valores, chequeando su existencia
		$nombre 	= http_request("Nombre","s",null); // primary key
		$viejo 	= http_request("Viejo","s",null); 
		$telefono = http_request('Telefono',"s",null);
		$email = http_request('Email',"s",null);
		$club	= http_request('Club',"s",null); // not null
		$observaciones= http_request('Observaciones',"s",null);
		
		do_log("updateGuia:: retrieved data from client");
		do_log("Viejo: $viejo Nombre: $nombre Telefono: $telefono Email: $email Club: $club Observaciones: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		$stmt->close();
		if (!$res) {
			$this->errormsg="updateGuia:: Error: ".$this->conn->error;
			return null;
		}
		log_exit($this->file);
		return "";
	}
	
	function delete($nombre) {
		log_enter($this->file);
		if ($nombre===null) {
			$this->errormsg="deleteGuia:: no guia name provided";
			return null;
		}
		// fase 1: desasignamos los perros de este guia
		$res= $this->conn->query("UPDATE Perros SET GUIA='-- Sin asignar --' WHERE ( Guia='$nombre')");
		if (!$res) {
			$this->errormsg="deleteGuia::unassign dogs() Error: ".$this->conn->error;
			return null;
		}
		// fase 2: borramos el guia de la base de datos
		$res= $this->conn->query("DELETE FROM Guias WHERE (Nombre='$nombre')");
		if (!$res) {
			$this->errormsg="deleteGuia::query(delete) Error: ".$this->conn->error;
			do_log($msg);
		} 
		log_exit($this->file);
		return "";
	}
	
	/**
	 * remove a handler from provided club
	 * @param unknown $guia
	 * @return "" on success ; null on error
	 */
	function orphan($guia) {
		if ($guia===null) {
			$this->errormsg="orphanClub:: no handler name provided";
			return null;
		}
		log_enter($this->file);
		$res= $this->conn->query("UPDATE Guias SET Club='-- Sin asignar --' WHERE ( Nombre='$guia' )");
		if (!$res) {
			$this->errormsg="orphanGuiaFromClub::query(delete) Error: ".$this->conn->error;
			do_log($msg);
			return null;
		}
		log_exit($this->file);
		return "";
	}
	
	function select() {
		log_enter($this->file);
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
		$rs=$this->conn->query("SELECT count(*) FROM Guias $where");
		if ($rs===false) {
			$this->errormsg="select( count(*) ) error: ".$this->conn->error;
			return null;
		}
		$row=$rs->fetch_array();
		$rs->free();
		$result["total"] = $row[0];
		
		// second query to retrieve $rows starting at $offset
		$rs=$this->conn->query("SELECT * FROM Guias $where ORDER BY $sort $order LIMIT $offset,$rows");
		if ($rs===false) {
			$this->errormsg="select( ) error: ".$this->conn->error;
			return null;
		}
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		// disconnect from database and return composed array
		$rs->free();
		log_exit($this->file);
		return $result;
	}
	
	function enumerate() { // like select but do not provide indexed block query

		log_enter($this->file);
		
		// evaluate search string
		$q=http_request("q","s",null);
		$like =  ($q===null) ? "" : " WHERE ( ( Nombre LIKE '%$q%' ) OR ( Club LIKE '%$q%' ) )";
		
		$result = array();
		// execute first query to know how many elements
		$rs=$this->conn->query("SELECT count(*) FROM Guias ".$like);
		if ($rs===false) {
			$this->errormsg="select( count (*) ) error: ".$this->conn->error;
			return null;
		}
		$row=$rs->fetch_row();
		$rs->free();
		$result["total"] = $row[0];
		
		// second query to retrieve $rows starting at $offset
		$rs=$this->conn->query("SELECT Nombre,Club FROM Guias ".$like." ORDER BY Club,Nombre");
		if ($rs===false) {
			$this->errormsg="select( * ) error: ".$this->conn->error;
			return null;
		}
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		// disconnect from database and return
		$rs->free();
		log_exit($this->file);
		return $result;
	}
	
	/** 
	 * Enumerate by club (exact match)
	 * @param {string} $club Club name (key search) 
	 * @return result on success; null on error
	 */
	function selectByClub($club) {
		log_enter($this->file);
		if ($club===null){
			$this->errormsg="selectGuiasByClub() Error: no club provided";
			return null;
		}
		$result = array();
		$items = array();
		
		// execute first query to know how many elements
		$club=strval($_GET['Club']);
		$str="SELECT count(*) FROM Guias WHERE ( Club = '$club' )";
		do_log("select_GuiasByClub::(count) $str");
		$rs=$this->conn->query($str);
		if ($rs===false) {
			$this->errormsg="selectGuiasByClub( count(*) ) error: ".$this->conn->error;
			return null;
		}
		$row=$rs->fetch_row();
		$result["total"] = $row[0];
		$rs->free();
		$str="SELECT * FROM Guias WHERE ( Club ='$club' ) ORDER BY Nombre ASC";
		do_log("select_GuiasByClub::(select) $str");
		$rs=$this->conn->query($str);
		if ($rs===false) {
			$this->errormsg="selectGuiasByClub( ) error: ".$this->conn->error;
			return null;
		}
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
		log_enter($this->file);
		if ($nombre===null) {
			$this->errormsg="selectGuiaByNombre: No name specified";
			return null;
		}
		// second query to retrieve $rows starting at $offset
		$str="SELECT * FROM Guias WHERE ( Nombre = '$nombre' )";
		do_log("selectGuiaByNombre:: query string is $str");
		$rs=$this->conn->query($str);
		if (!$rs) {
			$this->errormsg="get_guiaByNombre::query() error ".$this->conn->error;
			return null;
		}
		// retrieve result into an array
		$result = array();
		while($row = $rs->fetch_array()){ // should only be one item
			$row['Operation']='update'; // dirty trick to ensure that form operation is fixed
			array_push($result, $row);
		}
		// disconnect from database
		$rs->free();
		log_exit($this->file);
		return $result;
	}
}
	
?>