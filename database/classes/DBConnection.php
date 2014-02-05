<?php
define("DBHOST","localhost");
define("DBNAME","agility");

/**
 * DB connection handler
 * This class should only be used from DBConnection objects
 * @author jantonio
 *
 */
class DBConnection {
	
	public static function openConnection($user,$pass) {
		$conn = new mysqli(DBHOST,$user,$pass,DBNAME);
		if ($conn->connect_error) return null; 
		$conn->query("SET NAMES 'utf8'");
		return $conn;
	}
	
	public static function closeConnection($conn) {
		return $conn->close();
	}

}


?>