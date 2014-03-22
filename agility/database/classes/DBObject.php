<?php
/** mandatory requires for database and logging */
require_once (__DIR__."/../logging.php");
require_once (__DIR__."/DBConnection.php");

class DBObject {
	protected $conn;
	protected $file;
	public $errormsg; // should be public to access to from caller
	protected $myLogger;
	
	private $fall; // boolean to notice use of fetch_all() or fetch_array loop
	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @throws Exception if cannot contact database
	 */
	function __construct($file) {
		// connect database
		$this->file=$file;
		$this->myLogger= new Logger($file);
		
		$this->conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
		if (!$this->conn) {
			$this->errormsg="$file::construct() cannot contact database";
			throw new Exception($this->errormsg);
		}
		// check if exists resultset::fetch_all() method 
		$this->fall= (method_exists('mysqli_result', 'fetch_all'))?true:false; 
	}
	
	/**
	 * Destructor
	 * Just disconnect from database
	 */
	function  __destruct() {
		// DBConnection::closeConnection($this->conn);
	}
	
	function error($msg) {
		$trace=debug_backtrace();
		$this->errormsg=$this->file."::".$trace[1]['function']."() Error at ".$trace[1]['file'].":".$trace[1]['line'].":\n".$msg;
		return null;
	}
	
	function query($sql) {
		$this->myLogger->query($sql);
		return $this->conn->query($sql);
	}
	
	function fetch_all($rs) {
		// estilo mysqlnd
		if ($this->fall) return $rs->fetch_all(MYSQLI_ASSOC);
		// estilo mysqli
		$res= array();
		while ($row= $rs->fetch_array(MYSQLI_ASSOC)) array_push($res,$row);
		return $res;
	}
}