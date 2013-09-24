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
	
	public static function getConnection($user,$pass) {
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
			$instances[$user][1]++;
			$instances[$user][0]=$res;
		}
		return $instances[$user][0];
	}
	
	public static function removeConnection($conn) {
		// obtiene una instancia del singleton
		$myDBConnection= self::getInstance();
		$instances= $myDBConnection->getConnectionList();
		foreach ($instances as $item) {
			if ($item[0]==null) continue;
			if ($item[0]!==$conn) continue;
			if ($item[1]>0) $item[1]--;
			// arriving zero means close
			if ($item[1]==0) {
				$res = $conn->close();
				$item[0]=null;
				return $res;	
			}
		}
		// arriving here means connection not found
		return FALSE;
	}
}

?>