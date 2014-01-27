<?php

require_once("DBConnection.php");

class Mangas {
	protected $conn;
	protected $file;
	protected $jornada;
	public $errormsg; // should be public to access to from caller

	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {string} $tipo jornada type
	 * @throws Exception if cannot contact database or invalid jornada ID
	 */
	function __construct($file,$jornada) {
		// connect database
		$this->file=$file;		
		if ($jornada<=0) {
			$this->errormsg="Manga::Construct invalid jornada ID";
			throw new Exception($this->errormsg);
		}
		$this->jornada=$jornada;
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
	
	function insert($tipo,$grado) {
		do_log("insertMangas:: enter");
		// si la manga existe no hacer nada; si no existe crear manga
		$str="SELECT count(*) AS 'result' FROM Mangas WHERE ( Jornada = $jornada ) AND  ( Tipo = '".$tipo."' )";
		$rs=$this->conn->query($str);
		if (!$rs) {
			$this->errormsg="insertManga( select_count(*) , ".$this->jornada." , ".$tipo." ) failed: ".$this->conn->error;
			return null;
		}
		if ($rs->num_rows > 0) {
			do_log("insertMangas:: Manga already exists. return");
			return "";
		}
		$rs->free();
		$str="INSERT INTO Mangas ( Jornada , Tipo, Grado ) VALUES (".$this->$jornada.",'".$tipo."','".$grado."')";
		$rs=$this->conn->query($str);
		if (!$rs) {
			$str="inscripcionFunctions::create_manga( insert , ".$jornada." , '".$tipo.", ".$grado." ) failed: ".$this->conn->error;
			return null;
		}
		return "";
	}
	
	function update() {
	}
	
	/**
	 * Delete a Manga from jornada $jornada when tipo is $tipo
	 * @return "" on success; null on error
	 */
	function delete($tipo) {
		do_log("deleteManga:: enter");
		if ($tipo===null) {
			$this->errormsg="deleteManga:: invalid parameter 'tipo'";
			return null;
		}
		// si la manga existe, borrarla; si no existe, no hacer nada
		$str="DELETE FROM Mangas WHERE ( Jornada = ".$this->jornada." ) AND  ( Tipo = '".$tipo."' )";
		$rs=$this->conn->query($str);
		if (!$rs) {
			$this->errormsg="inscripcionFunctions::delete_manga( ".$this->jornada." , ".$tipo." ) failed: ".$this->conn->error;
			return null;
		}
		do_log("deleteManga:: exit success");
		return "";
	}
	
	function selectByID($id) {
		
	}
	
	function selectByJornada() {
		
	}
}

?>