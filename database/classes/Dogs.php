<?php
require_once("DBConnection.php");

class Dogs {
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
	 * Insert a new dog into database
	 * @return {string} "" if ok; null on error
	 */
	function insert() {
		do_log("insertDog:: enter");
		// componemos un prepared statement
		$sql ="INSERT INTO Perros (Nombre,Raza,LOE_RRC,Licencia,Categoria,Grado,Guia)
			   VALUES(?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) {
			$this->errormsg="insertDog::prepare() failed ".$this->conn->error;
			return null;
		}
		$res=$stmt->bind_param('sssssss',$nombre,$raza,$loe_rrc,$licencia,$categoria,$grado,$guia);
		if (!$res) {
			$this->errormsg="insertDog::prepare() failed ".$this->conn->error;
			return null;
		}
		// iniciamos los valores, chequeando su existencia
		$nombre =	http_request("Nombre","s",null); 
		$raza =		http_request("Raza","s",null); 
		$loe_rrc =	http_request("LOE_RRC","s",null); 
		$licencia = http_request("Licencia","s",null); 
		$categoria= http_request("Categoria","s",null); 
		$grado =	http_request("Grado","s",null); 
		$guia =		http_request("Guia","s",null); 
		
		do_log("Nombre: $nombre Raza: $raza LOE: $loe_rrc Categoria: $categoria Grado: $grado Guia: $guia");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		$stmt->close();
		if (!$res) {
			$this->errormsg="insertDog:: Error: ".$this->conn->error;
			return null;
		}
		//do_log("insertadas $stmt->affected_rows filas");
		do_log("insertDog:: exit OK");
		return "";
		
	}
	
	/**
	 * Update data for provided dog ID
	 * @param {integer} $dorsal dog id primary key
	 * @return "" on success; null on error
	 */
	function update($dorsal) {
		do_log("updateDog:: enter");
		if ($dorsal==0) {
			$stmt->close();
			$this->errormsg="updateDog:: no dorsal provided for update";
			return null;
		}
		// componemos un prepared statement
		$sql ="UPDATE Perros SET Nombre=? , Raza=? , LOE_RRC=? , Licencia=? , Categoria=? , Grado=? , Guia=?
		       WHERE ( Dorsal=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) {
			$this->errormsg="updateDog::prepare() failed ".$this->conn->error;
			return null;
		}
		$res=$stmt->bind_param('sssssssi',$nombre,$raza,$loe_rrc,$licencia,$categoria,$grado,$guia,$dorsal);
		if (!$res) {
			$this->errormsg="updateDog::bind() failed ".$this->conn->error;
			return null;
		}

		// iniciamos los valores, chequeando su existencia
		$nombre =	http_request("Nombre","s",null);
		$raza =		http_request("Raza","s",null);
		$loe_rrc =	http_request("LOE_RRC","s",null);
		$licencia = http_request("Licencia","s",null);
		$categoria= http_request("Categoria","s",null);
		$grado =	http_request("Grado","s",null);
		$guia =		http_request("Guia","s",null);

		do_log("Dorsal: $dorsal Nombre: $nombre Raza: $raza LOE: $loe_rrc Categoria: $categoria Grado: $grado Guia: $guia");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		$stmt->close();
		if (!$res) {
			$this->errormsg="updateDog:: Error: ".$this->conn->error;
			return null;
		} 
		do_log("updateDog:: exit OK");
		return "";
	}
	
	/**
	 * Delete dog with provided dorsal
	 * @param {integer} $dorsal dog primary key
	 * @return "" on success ; otherwise null
	 */
	function delete($dorsal) {
		do_log("deleteDog:: enter");
		if ($dorsal==0) {
			$stmt->close();
			$this->errormsg="deleteDog:: no dorsal provided for deletion";
			return null;
		}
		$res= $this->conn->query("DELETE FROM Perros WHERE (Dorsal=$dorsal)");
		if (!$res) {
			$this->errormsg="deleteDog:: Error: ".$this->conn->error;
			return null;
		} 
		do_log("deleteDog:: exit OK");
		return "";
	}
	
	/**
	 * Enumerate all dogs that matches requested criteria and order
	 * @return null on error, else requested data
	 */
	function select() {
		// evaluate offset and row count for query
		$page= http_request("page","i",1);
		$rows= http_request("rows","i",20);
		$sort= http_request("sort","s","Nombre");
		$order=http_request("order","s","ASC");
		$search=http_Request("where","s","");
		$where = ' ';
		if ($search!=='') $where="WHERE (Nombre LIKE '%$search%') OR ( Guia LIKE '%$search%') OR ( Club LIKE '%$search%')";
		$offset = ($page-1)*$rows;
		$result = array();

		// execute first query to know how many elements
		$rs=$this->conn->query("SELECT count(*) FROM PerroGuiaClub $where");
		if (!$rs) {
			$this->errormsg="selectDog (count *) error ".$this->conn->error;
			return null;
		}
		$row=$rs->fetch_array();
		$result["total"] = $row[0];
		$rs->free();
		// second query to retrieve $rows starting at $offset
		$str="SELECT * FROM PerroGuiaClub $where ORDER BY $sort $order LIMIT $offset,$rows";
		do_log("select_dogs:: query string is $str");
		$rs=$this->conn->query($str);
		if (!$rs) {
			$this->errormsg="selectDog() error ".$this->conn->error;
			return null;
		}
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		// disconnect from database and return result
		$rs->free();
		return $result;
	}
	
	/**
	 * Like select but not provide indexed search
	 * @return NULL|multitype:multitype: unknown
	 */
	function enumerate() {
		do_log("enumerateDogs():: enter");
		
		// evaluate offset and row count for query
		$q=http_request("q","s",null);
		$like =  ($q===null) ? "" : " WHERE ( ( Nombre LIKE '%$q%' ) OR ( Guia LIKE '%$q%' ) OR ( Club LIKE '%$q%' ) )";

		// execute first query to know how many elements
		$result = array();
		$rs=$this->conn->query("SELECT count(*) FROM PerroGuiaClub ".$like);
		if ($rs===false) {
			$this->errormsg="select( count* ) error: ".$this->conn->error;
			return null;
		}
		$row=$rs->fetch_row();
		$result["total"] = $row[0];
		$rs->free();
		// second query to retrieve $rows starting at $offset
		$rs=$this->conn->query("SELECT * FROM PerroGuiaClub $like ORDER BY Club,Guia,Nombre");
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
		// return composed array
		do_log("enumerateDogs():: exit");
		$rs->free();
		return $result;
	}
	
	/** 
	 * enumera todos los perros asociados a un guia
	 * @param {string} $guia Nombre del guia
	 */
	function selectByGuia($guia) {
		do_log("selectDogsByGuia::enter");
		if ($guia===null) {
			$this->errormsg="selectDogsByGuia: No guia specified";
			return null;
		}
		// evaluate offset and row count for query
		$result = array();
		$items = array();
		// execute first query to know how many elements
		$str="SELECT count(*) FROM Perros WHERE ( Guia = '".$guia."' )";
		do_log("select_PerrosByGuia::(count) $str");
		$rs=$this->conn->query($str);
		if ($rs===false) {
			$this->errormsg="selectDogsByGuia( count* ) error: ".$this->conn->error;
			return null;
		}
		$row=$rs->fetch_row();
		$result["total"] = $row[0];
		$rs->free();
		$str="SELECT * FROM Perros WHERE ( Guia ='$guia' ) ORDER BY Nombre ASC";
		do_log("select_PerrosByGuia::(select) $str");
		$rs=$this->conn->query($str);
		if ($rs===false) {
			$this->errormsg="selectDogByGuia( ) error: ".$this->conn->error;
			return null;
		}
		// retrieve result into an array
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		// free resources and return
		$rs->free();
		$result["rows"] = $items;
		do_log("selectDogsByGuia::exit");
		return $result;
	}
	
	/** 
	 * Obtiene los datos del perro con el dorsal indicado
	 * @param {integer} $dorsal dog primary key
	 * @return "" on success; otherwise null
	 */
	function selectByDorsal($dorsal){
		do_log("selectByDorsal:: enter");
		if ($dorsal==0) {
			$this->errormsg="selectDogsByDorsal: No dorsal specified";
			return null;
		}
		// second query to retrieve $rows starting at $offset
		$str="SELECT * FROM PerroGuiaClub WHERE ( Dorsal = $dorsal )";
		do_log("get_dogsByDorsal:: query string is $str");
		$rs=$this->conn->query($str);
		if (!$rs) {
			$this->errormsg="get_dogsByDorsal::query() error ".$this->conn->error;
			return null;
		}
		// retrieve result into an array
		$result = array();
		while($row = $rs->fetch_array()){
			$row['Operation']='update'; // dirty trick to ensure that form operation is rewritten on loadform
			array_push($result, $row);
		}
		// free resources and return result
		$rs->free();
		do_log("selectByDorsal:: exit");
		return $result;
	}
}
	
?>