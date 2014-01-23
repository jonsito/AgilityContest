<?php
	require_once("DBConnection.php");

class Clubes {
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
	 * insert a new club into database
	 * @return empty string if ok; else null
	 */
	function insert() {
		do_log("insertClub:: enter");
		// componemos un prepared statement
		$sql ="INSERT INTO Clubes (Nombre,Direccion1,Direccion2,Provincia,Contacto1,Contacto2,Contacto3,GPS,
				Web,Email,Facebook,Google,Twitter,Observaciones,Baja)
			   VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) {
			$this->errormsg="insertClub::prepare() failed ".$this->conn->error;
			return null;
		}
		$res=$stmt->bind_param('sssssssssssssss',$nombre,$direccion1,$direccion2,$provincia,$contacto1,$contacto2,$contacto3,$gps,
				$web,$email,$facebook,$google,$twitter,$observaciones,$baja);
		if (!$res) {
			$this->errormsg="insertClub::prepare() failed ".$this->conn->error;
			return null;
		}
		
		// iniciamos los valores, chequeando su existencia
		$nombre 	= http_request("Nombre","s",null);
		$direccion1 = http_request('Direccion1',"s",null);
		$direccion2 = http_request('Direccion2',"s",null); 
		$provincia	= http_request('Provincia',"s",null);
		$contacto1	= http_request('Contacto1',"s",null);
		$contacto2	= http_request('Contacto2',"s",null);
		$contacto3	= http_request('Contacto3',"s",null);
		$gps		= http_request('GPS',"s",null);
		$web		= http_request('Web',"s",null);
		$email		= http_request('Email',"s",null);
		$facebook	= http_request('Facebook',"s",null);
		$google		= http_request('Google',"s",null);
		$twitter	= http_request('Twitter',"s",null);
		$observaciones = http_request('Observaciones',"s",null);
		$baja		= http_request('Baja',"i",0);
		do_log("insertClub:: retrieved data from client");
		do_log("Nombre: $nombre Direccion1: $direccion1 Contacto1: $contacto1 Observaciones: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		$stmt->close();
		if (!$res) {
			$this->errormsg="insertClub:: Error: ".$this->conn->error;
			return null;
		}
		// do_log("insertadas $stmt->affected_rows filas");
		do_log("insertClub:: exit ok");
		return ""; // return ok
	}
	
	/**
	 * Update entry in database table "Clubs"
	 * @return string "" empty if ok; null on error
	 */
	function update() {
		do_log("updateClub:: enter");
		
		// componemos un prepared statement
		$sql ="UPDATE Clubes
				SET Nombre=? , Direccion1=? , Direccion2=? , Provincia=? ,
				Contacto1=? , Contacto2=? , Contacto3=? , GPS=? , Web=? ,
				Email=? , Facebook=? , Google=? , Twitter=? , Observaciones=? , Baja=?
				WHERE ( Nombre=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) {
			$this->errormsg="updateClub::prepare() failed ".$this->conn->error;
			return null;
		}
		$res=$stmt->bind_param('ssssssssssssssis',$nombre,$direccion1,$direccion2,$provincia,$contacto1,$contacto2,$contacto3,$gps,
				$web,$email,$facebook,$google,$twitter,$observaciones,$baja,$viejo);
		if (!$res) {
			$this->errormsg="updateClub::bind() failed ".$this->conn->error;
			return null;
		}
		// iniciamos los valores, chequeando su existencia
		$nombre 	= http_request("Nombre","s",null);
		$viejo		= http_request("Viejo","s",null);
		$direccion1 = http_request('Direccion1',"s",null);
		$direccion2 = http_request('Direccion2',"s",null); 
		$provincia	= http_request('Provincia',"s",null);
		$contacto1	= http_request('Contacto1',"s",null);
		$contacto2	= http_request('Contacto2',"s",null);
		$contacto3	= http_request('Contacto3',"s",null);
		$gps		= http_request('GPS',"s",null);
		$web		= http_request('Web',"s",null);
		$email		= http_request('Email',"s",null);
		$facebook	= http_request('Facebook',"s",null);
		$google		= http_request('Google',"s",null);
		$twitter	= http_request('Twitter',"s",null);
		$observaciones = http_request('Observaciones',"s",null);
		$baja		= http_request('Baja',"i",0);
		
		do_log("updateClub:: retrieved data from client");
		do_log("Nombre: $nombre Direccion1: $direccion1 Contacto1: $contacto1 Observaciones: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) {
			$this->errormsg="updateClub:: Error: ".$this->conn->error;
			return null;
		}
		do_log("updateClub:: exit ok");
		$stmt->close();
		return "";
	}
	
	function delete($nombre) {
		if ($nombre===null) {
			$this->errormsg="deleteClub:: no club name provided";
			return null;
		}
		do_log("deleteClub($nombre):: enter");
		// fase 1: desasignar guias del club
		$res= $this->conn->query("UPDATE Guias SET Club='-- Sin asignar --'  WHERE (Club='$nombre')");
		if (!$res) {
			$this->errormsg="deleteClub::unassign handlers() Error: ".$this->conn->error;
			return null;
		} 
		// fase 2: borrar el club de la BBDD
		$res= $this->conn->query("DELETE FROM Clubes WHERE (Nombre='$nombre')");
		if (!$res) {
			$this->errormsg="deleteClub::query(delete) Error: ".$this->conn->error;
			return null;
		}
		do_log("deleteClub():: exit");
		return "";
	}
	
	/**
	 * retrieve all clubes from table, according sort, search and limit requested
	 */
	function select() {
		do_log("selectClubs() enter");
		// evaluate offset and row count for query
		$page= http_request("page","i",1);
		$rows= http_request("rows","i",20);
		$sort= http_request("sort","s","Nombre");
		$order=http_request("order","s","ASC");
		$search=http_Request("where","s","");
		$where = '';
		if ($search!=='') $where=" WHERE ( (Nombre LIKE '%$search%') OR ( Email LIKE '%$search%') OR ( Facebook LIKE '%$search%') ) ";
		$offset = ($page-1)*$rows;
		$result = array();
		
		// execute first query to know how many elements
		$rs=$this->conn->query("SELECT count(*) FROM Clubes $where");
		if ($rs===false) {
			$this->errormsg="select()::count error: ".$this->conn->error;
			return null;
		}
		$row=$rs->fetch_array();
		$rs->free();
		$result["total"] = $row[0];
		// second query to retrieve $rows starting at $offset
		$rs=$this->conn->query("SELECT * FROM Clubes $where ORDER BY $sort $order LIMIT $offset,$rows");
		if ($rs===false) {
			$this->errormsg="select() error: ".$this->conn->error;
			return null;
		}
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		$rs->free();
		// return composed array
		do_log("selectClubs():: exit");
		return $result;
	}
	
	/** 
	 * return a dupla Nombre,Provincia list according select criteria
	 * return data if success; null on error
	 */
	function enumerate() {
		do_log("enumerateClubs():: enter");
		// evaluate offset and row count for query
		$q=http_request("q","s",null);
		$like =  ($q===null) ? "" : " WHERE Nombre LIKE '%".$q."%'";
		
		// execute first query to know how many elements
		$result = array();
		$rs=$this->conn->query("SELECT count(*) FROM Clubes ".$like);
		if ($rs===false) {
			$this->errormsg="select( count* ) error: ".$this->conn->error;
			return null;
		}
		$row=$rs->fetch_row();
		$result["total"] = $row[0];
		$rs->free();
		// second query to retrieve $rows starting at $offset
		$rs=$this->conn->query("SELECT Nombre,Provincia FROM Clubes ".$like." ORDER BY Nombre ASC");
		if ($rs===false) {
			$this->errormsg="select() error: ".$this->conn->error;
			return null;
		}
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			// utf8 encode data
			// $row["Nombre"] =utf8_encode( $row["Nombre"] );
			// $row["Provincia"]   =utf8_encode( $row["Provincia"]   );
			// and store into result array
			array_push($items, $row);
		}
		$result["rows"] = $items;
		// return composed array
		do_log("enumerateClubs():: exit");
		$rs->free();
		return $result;
	}
	
} /* end of class "Clubes" */

?>