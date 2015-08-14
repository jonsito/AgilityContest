<?php

/*
userFunctions.php

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


require_once(__DIR__."/logging.php");
require_once(__DIR__."/tools.php");
require_once(__DIR__."/auth/Config.php");
require_once(__DIR__."/auth/AuthManager.php");
require_once(__DIR__."/database/classes/DBObject.php");

class Admin extends DBObject {
	protected $myConfig;
    protected $myAuth;
	protected $file;
	public $errormsg;
	private $dbname;
	private $dbhost;
	private $dbuser;
	private $dbpass;

	function __construct($file,$am) {
        parent::__construct("adminFunctions");
		// connect database
		$this->file=$file;
		$this->myConfig=Config::getInstance();
        $this->myAuth=$am;

		$this->dbname=$this->myConfig->getEnv('database_name');
		$this->dbhost=$this->myConfig->getEnv('database_host');
		$this->dbuser=$this->myConfig->getEnv('database_user');
		$this->dbpass=$this->myConfig->getEnv('database_pass');
	}
	
	// FROM: https://gist.github.com/lavoiesl/9a08e399fc9832d12794
	private function process_line($line) {
		$length = strlen($line);
		$pos = strpos($line, ' VALUES ') + 8;
		echo substr($line, 0, $pos);
		$parenthesis = false;
		$quote = false;
		$escape = false;
		for ($i = $pos; $i < $length; $i++) {
			switch($line[$i]) {
				case '(':
					if (!$quote) {
						if ($parenthesis) {
							throw new Exception('double open parenthesis');
						} else {
							echo PHP_EOL;
							$parenthesis = true;
						}
					}
					$escape = false;
					break;
				case ')':
					if (!$quote) {
						if ($parenthesis) {
							$parenthesis = false;
						} else {
							throw new Exception('closing parenthesis without open');
						}
					}
					$escape = false;
					break;
				case '\\':
					$escape = !$escape;
					break;
				case "'":
					if ($escape) {
						$escape = false;
					} else {
						$quote = !$quote;
					}
					break;
				default:
					$escape = false;
					break;
			}
			echo $line[$i];
		}
	}
	
	public function backup() {
		
		$dbname=$this->dbname;
		$dbhost=$this->dbhost;
		$dbuser=$this->dbuser;
		$dbpass=$this->dbpass;
		
		$cmd="mysqldump"; // unix
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$drive=substr(__FILE__, 0, 1);
			$cmd='start /B '.$drive.':\xampp\mysql\bin\mysqldump.exe';
		}
		$cmd = "$cmd --opt --single-transaction --routines --triggers -h $dbhost -u$dbuser -p$dbpass $dbname";
		$this->myLogger->info("Ejecutando comando: '$cmd'");
		$input = popen($cmd, 'r');
		if ($input===FALSE) { $this->errorMsg="adminFunctions::popen() failed"; return null;}
		
		$fname="$dbname-".date("Ymd_Hi").".sql";
		header('Set-Cookie: fileDownload=true; path=/');
		header('Cache-Control: max-age=60, must-revalidate');
		header('Content-Type: text/plain; charset=utf-8');
		header('Content-Disposition: attachment; filename="'.$fname.'"');
		// insert AgilityContest Info at begining of backup file
        $ver=$this->myConfig->getEnv("version_name");
        $rev=$this->myConfig->getEnv("version_date");
        echo "-- AgilityContest Version: $ver Revision: $rev\n";
        // now send to client database backup
		while(!feof($input)) {
			$line = fgets($input);
			if (substr($line, 0, 6) === 'INSERT') {
				$this->process_line($line);
			} else {
				echo $line;
			}
		}
		pclose($input);
		return "ok";
	}	

	private function handleSession($str) {
		session_start();
		$_SESSION["progress"]=$str;
		session_write_close();
	}

	private function retrieveDBFile() {
		$this->myLogger->enter();
		$this->handleSession("download");
		// extraemos los datos de registro
		$data=http_request("Data","s",null);
		if (!$data) return array("errorMsg" => "restoreDB(): No restoration data received");
		if (!preg_match('/data:([^;]*);base64,(.*)/', $data, $matches)) {
			return array("errorMsg" => "restoreDatabase(): Invalid received data format");
		}
		$type=$matches[1]; // 'application/octet-stream', or whatever. Not really used
		$this->myLogger->leave();
		return base64_decode( $matches[2] ); // decodes received data
	}

	private function dropAllTables($conn) {
        $conn->query('SET foreign_key_checks = 0');
        if ($result = $conn->query("SHOW TABLES")) {
            while($row = $result->fetch_array(MYSQLI_NUM)) {
				$this->handleSession("Drop table ".$row[0]);
                $conn->query('DROP TABLE IF EXISTS '.$row[0]);
            }
        }
        $conn->query('SET foreign_key_checks = 1');
    }

    private function readIntoDB($conn,$data) {
        // Temporary variable, used to store current query
        $templine = '';
        $trigger=false;
        // Read entire file into an array
        $lines = explode("\n",$data); // remember use double quote
		$numlines=count($lines);
		$timeout=ini_get('max_execution_time');
        // Loop through each line
        foreach ($lines as $idx => $line) {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || trim($line) == '') continue;
            // properly handle "DELIMITER ;;" command
            if (trim($line)=="DELIMITER ;;") { $trigger=true; continue; }
            else if (trim($line)=="DELIMITER ;") { $trigger=false; }
            else $templine .= $line;    // Add this line to the current segment
            if ($trigger) continue;
            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {
				$this->handleSession("".intval((100*$idx)/$numlines) );
				// avoid php to be killed on very slow systems
				set_time_limit($timeout);
                // Perform the query
                $conn->query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . $conn->error() . '<br /><br />');
                // Reset temp variable to empty
                $templine = '';
            }
        }
		$this->handleSession("Done");
        $this->myLogger->info("database restore success");
        return "";
    }

	public function restore() {
        // we need root database access to re-create tables
        $rconn=DBConnection::getRootConnection();
        if ($rconn->connect_error) throw new Exception("Cannot perform upgrade process: database::dbConnect()");
		// phase 1: retrieve file from http request
        $data=$this->retrieveDBFile();
        // phase 2: verify received file
		// TODO: make sure that this is a correct AgilityContest Database file
        // phase 3: delete all tables and structures from database
        $this->dropAllTables($rconn);
        // phase 4: parse sql file and populate tables into database
        $this->readIntoDB($rconn,$data);
        // phase 5 final tests
        DBConnection::closeConnection($rconn);
		return "";
	}
	
	public function clearDatabase() {
		// TODO: reset configuration
		// drop pruebas
        $this->clearContests();
        // delete data
        $this->query("DELETE FROM Jueces WHERE ID>1");
        $this->query("DELETE FROM Perros WHERE ID>1");
        $this->query("DELETE FROM Guias WHERE ID>1");
        $this->query("DELETE FROM Clubes WHERE ID>1");
        // do not delete users nor sessions
        $this->query("DELETE FROM Eventos");
		return "";
	}

	public function clearContests() {
        return $this->query("DELETE FROM Pruebas WHERE ID>1");
	}
}

$response="";
try {
	$result=null;
	$operation=http_request("Operation","s","");
	if ($operation===null) throw new Exception("Call to adminFunctions without 'Operation' requested");
	if ($operation==="progress") {
		// special case: just retrieve session status and return
		session_start();
		echo json_encode( array( 'progress' => $_SESSION["progress"] ));
		return;
	}
	$am= new AuthManager("adminFunctions");
    $adm= new Admin("adminFunctions",$am);
	switch ($operation) {
		case "backup":
			/* $am->access(PERMS_ADMIN); */
			$result=$adm->backup();	break;
		case "restore":
			$am->access(PERMS_ADMIN); $result=$adm->restore(); break;
		case "reset":
			$am->access(PERMS_ADMIN); $result=$adm->clearDatabase(); break;
		case "clear":
			$am->access(PERMS_ADMIN); $result=$adm->clearContests(); break;
		case "reginfo": 
			$result=$am->getRegistrationInfo(); if ($result==null) $adm->errormsg=$am->errormsg; break;
		case "register":
			$am->access(PERMS_ADMIN); $result=$am->registerApp(); if ($result==null) $adm->errormsg=$am->errormsg; break;
		case "loadConfig": 
			$conf=Config::getInstance(); $result=$conf->loadConfig(); break;
		case "saveConfig": 
			$am->access(PERMS_ADMIN); $conf=Config::getInstance(); $result=$conf->saveConfig(); break;
		case "defaultConfig": 
			$am->access(PERMS_ADMIN); $conf=Config::getInstance(); $result=$conf->defaultConfig(); break;
		default: 
			throw new Exception("adminFunctions:: invalid operation: '$operation' provided");
	}
	if ($result===null)	throw new Exception($adm->errormsg); // error
	if ($result==="ok") return; // don't generate any aditional response 
	if ($result==="") $result= array('success'=>true); // success
	echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}
?>
