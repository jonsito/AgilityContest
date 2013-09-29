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
	private static $singleton;
	private $connections;
	
	private function __construct() {
		$connections=Array();
	}
	
	public static function getInstance() {
		if (self::$singleton == null) {
		    self::$singleton = new DBConnection();
		}
		return self::$singleton;
	}
	
	public function getConnectionList() { return $this->connections; }
	public function addConnection($user,$conn) {
		$this->connections[$user]=array($conn,1);
	}
	
	public function deleteConnection($conn) {
		foreach ($this->connections as $user => $data) {
			if ($data[0]==null) continue;
			if ($data[0]!==$conn) continue;
			if ($data[1]>0) $data[1]--;
			// arriving zero means close
			if ($data[1]==0) {
				$res = $conn->close();
				$data[0]=null;
				$this->connections[$user]=null;
				return $res;
			}
		}
		// arriving here means connection not found
		return FALSE;
	}
	
	public static function openConnection($user,$pass) {
		// obtiene una instancia del singleton
		$myDBConnection= self::getInstance();
		$instances= $myDBConnection->getConnectionList();
		// si no esta declarado el usuario lo creamos
		if ($instances[$user]==null) $instances[$user]= array(null,0);
		// si esta ya conectado, se retorna conexion
		if ($instances[$user][1]!=0) {
			$instances[$user][1]++;
			return $instances[$user][0];
		}
		// si no esta conectado, creamos conexion
		$instances[$user][0]=null;
		$res = new mysqli(DBHOST,$user,$pass,DBNAME);
		if (!$res->connect_error) {
			$myDBConnection->addConnection($user,$res);
		}
		$res->query("SET NAMES 'utf8'");
		return $res;
	}
	
	public static function closeConnection($conn) {
		// obtiene una instancia del singleton
		$myDBConnection= self::getInstance();
		return $myDBConnection->deleteConnection($conn);
	}
	

}

?>