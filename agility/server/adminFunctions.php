<?php

/*
userFunctions.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

// Github redirects links, and make curl fail.. so use real ones
// define ('UPDATE_INFO','https://github.com/jonsito/AgilityContest/raw/master/agility/server/auth/system.ini');
define ('UPDATE_INFO','https://raw.githubusercontent.com/jonsito/AgilityContest/master/agility/server/auth/system.ini');
define ('RESTORE_DIR',__DIR__."/../../logs/");

require_once(__DIR__."/logging.php");
require_once(__DIR__."/tools.php");
require_once(__DIR__."/auth/Config.php");
require_once(__DIR__."/auth/AuthManager.php");
require_once(__DIR__."/database/classes/DBObject.php");
require_once(__DIR__."/printer/RawPrinter.php");

class Admin extends DBObject {
	protected $myConfig;
    protected $myAuth;
	protected $file;
	public $errormsg;
	private $dbname;
	private $dbhost;
	private $dbuser;
	private $dbpass;
	public $logfile;

	function __construct($file,$am,$suffix="") {
        parent::__construct("adminFunctions");
		// connect database
		$this->file=$file;
		$this->myConfig=Config::getInstance();
        $this->myAuth=$am;
        $this->logfile=RESTORE_DIR."restore_{$suffix}.log";

		$this->dbname=$this->myConfig->getEnv('database_name');
		$this->dbhost=$this->myConfig->getEnv('database_host');
		$this->dbuser=base64_decode($this->myConfig->getEnv('database_user'));
		$this->dbpass=base64_decode($this->myConfig->getEnv('database_pass'));
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
		set_time_limit(0); // some windozes are too slow dumping databases
		$cmd="mysqldump"; // unix
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$path=str_replace("\\apache\\bin\\httpd.exe","",PHP_BINARY);
			$cmd="start /B ".$path."\\mysql\\bin\\mysqldump.exe";
			// $drive=substr(__FILE__, 0, 1);
			// $cmd='start /B '.$drive.':\AgilityContest\xampp\mysql\bin\mysqldump.exe';
		}
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'DAR') { // Darwin (MacOSX)
			$cmd='/Applications/XAMPP/xamppfiles/bin/mysqldump';
		}
		// phase 1: dump structure
		$cmd1 = "$cmd --opt --no-data --single-transaction --routines --triggers -h $dbhost -u$dbuser -p$dbpass $dbname";
		$this->myLogger->info("Ejecutando comando: '$cmd1'");
		$input = popen($cmd1, 'r');
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
		// phase 2: dump data. Exclude ImportData and (if configured to) Eventos table contents
        $noexport="--ignore-table=agility.ImportData";
		if (intval($this->myConfig->getEnv("full_backup"))==0) $noexport .= " --ignore-table=agility.Eventos";

        $cmd2 = "$cmd --opt --no-create-info --single-transaction --routines --triggers $noexport -h $dbhost -u$dbuser -p$dbpass $dbname";
        $this->myLogger->info("Ejecutando comando: '$cmd2'");
        $input = popen($cmd2, 'r');
        if ($input===FALSE) { $this->errorMsg="adminFunctions::popen() failed"; return null;}
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
        $f=fopen($this->logfile,"a"); // open for append-only
        if (!$f) { $this->myLogger->error("fopen() cannot create file: ".$this->logfile); return;}
		fwrite($f,"$str\n");
        fclose($f);
	}

	private function retrieveDBFile() {
		$this->myLogger->enter();
		$this->handleSession("Download");
		// extraemos los datos de registro
		$data=http_request("Data","s",null);
		if (!$data) return array("errorMsg" => "restoreDB(): No restoration data received");
		if (!preg_match('/data:([^;]*);base64,(.*)/', $data, $matches)) {
			return array("errorMsg" => "restoreDatabase(): Invalid received data format");
		}
		// $type=$matches[1]; // 'application/octet-stream', or whatever. Not really used
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
				$this->handleSession(intval((100*$idx)/$numlines) );
				// avoid php to be killed on very slow systems
				set_time_limit($timeout);
                // Perform the query
                if (! $conn->query($templine) ){
					$this->myLogger->error('Error performing query \'<strong>' . $templine . '\': ' . $conn->error . '<br />');
				}
                // Reset temp variable to empty
                $templine = '';
            }
        }
		$this->handleSession("Done");
        $this->myLogger->info("database restore success");
        return "";
    }

	// returns a file retrieved from an URL as a variable
	private function file_get($url) {
		$res=retrieveFileFromURL($url);
		if ($res!==FALSE) return $res;
		// arriving here means no way to load file from remote site
		$this->myLogger->error('Cannot retrieve update information. Check your internet connection');
		return null;
	}

	public function restore() {
        // we need root database access to re-create tables
        $rconn=DBConnection::getRootConnection();
        if ($rconn->connect_error)
			throw new Exception("Cannot perform upgrade process: database::dbConnect()");
		session_start();
		unset($_SESSION['progress']);
		session_write_close();
		// phase 1: retrieve file from http request
        $data=$this->retrieveDBFile();
        // phase 2: verify received file
		if (strpos(substr($data,0,25),"-- AgilityContest")===FALSE)
			throw new Exception("Install file is not an AgilityContest database file");
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
        $this->query("DELETE FROM Clubes WHERE ID>1 AND Federations < 512"); // do not delete countries!!
        // do not delete users nor sessions
        $this->query("DELETE FROM Eventos");
		return "";
	}

	public function clearContests() {
        return $this->query("DELETE FROM Pruebas WHERE ID>1");
	}

	public function checkForUpgrades() {
        $info = $this->file_get(UPDATE_INFO);
        if ( ($info==null) || (!is_string($info)) )
            throw new Exception("checkForUpgrade(): cannot retrieve version info from internet");
        $info = str_replace("\r\n", "\n", $info);
        $info = str_replace(" ", "", $info);
        $data = explode("\n",$info);
        foreach ($data as $line) {
            if (strpos($line,"version_name=")===0) $version_name = trim(substr($line,13),'"');
            if (strpos($line,"version_date=")===0) $version_date = trim(substr($line,13),'"');
        }
        $res=array(
            'version_name' => $version_name,
            'version_date' => $version_date
        );
		// mark filesystem to allow upgrade
		$f=fopen(__DIR__."/../../logs/do_upgrade","w");
		fwrite($f,$this->myAuth->getSessionKey());
		fclose($f);
		return $res;
	}
}

$response="";
try {
	$result=null;
	$operation=http_request("Operation","s","");
	$perms=http_request("Perms","i",PERMS_NONE);
	$suffix=http_request("Suffix","s","");
	if ($operation===null) throw new Exception("Call to adminFunctions without 'Operation' requested");
	if ($operation==="progress") {
		$logfile=RESTORE_DIR."restore_{$suffix}.log";
		// no progressfile yet. return a dummy message to avoid warn to console in windows xampp
		if (!file_exists($logfile)) {
            echo json_encode( array( 'progress' => "Waiting for progress info...") );
            return;
		}
        // retrieve last line of progress file
        $lines=file($logfile,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		echo json_encode( array( 'progress' => strval($lines[count($lines)-1]) ) );
		return;
	}
	$am= new AuthManager("adminFunctions");
    $adm= new Admin("adminFunctions",$am,$suffix);
	switch ($operation) {
		case "userlevel":
			$am->access($perms); $result=array('success'=>true); break;
		case "permissions":
			$am->permissions($perms); $result=array('success'=>true); break;
		case "capabilities":
			$am->access(PERMS_NONE); $result=$am->getLicensePerms(); break;
		case "backup":
			/* $am->access(PERMS_ADMIN); */
			$result=$adm->backup();	break;
		case "restore":
			$am->access(PERMS_ADMIN); $result=$adm->restore(); break;
		case "reset":
			$am->access(PERMS_ADMIN); $result=$adm->clearDatabase(); break;
		case "clear":
			$am->access(PERMS_ADMIN); $result=$adm->clearContests(); break;
		case "upgrade":
			$am->access(PERMS_ADMIN); $result=$adm->checkForUpgrades(); break;
		case "reginfo": 
			$result=$am->getRegistrationInfo(); if ($result==null) $adm->errormsg="Cannot retrieve license information"; break;
		case "register":
			$am->access(PERMS_ADMIN); $result=$am->registerApp(); if ($result==null) $adm->errormsg="Cannot import license data"; break;
		case "loadConfig": // send configuration to browser
			$config=Config::getInstance();
			$result=$config->loadConfig();
			break;
		case "backupConfig": // generate and download a "config.ini" file
			$config=Config::getInstance();
			$result=$config->backupConfig();
			break;
		case "restoreConfig": // receive, analyze and save configuration from file
			$am->access(PERMS_ADMIN);
			$config=Config::getInstance();
			$result=$config->restoreConfig();
			$ev=new Eventos("RestoreConfig",1,$am);
			$ev->reconfigure();
			break;
		case "saveConfig": 
			$am->access(PERMS_ADMIN);
			$config=Config::getInstance();
			$result=$config->saveConfig();
			$ev=new Eventos("SaveConfig",1,$am);
			$ev->reconfigure();
			break;
		case "defaultConfig": 
			$am->access(PERMS_ADMIN);
			$config=Config::getInstance();
			$result=$config->defaultConfig();
			$ev=new Eventos("DefaultConfig",1,$am);
			$ev->reconfigure();
			break;
        case "getAvailableLanguages":
            $result= Config::getAvailableLanguages();
            break;
		case "printerCheck":
			$am->access(PERMS_OPERATOR);
			$config=Config::getInstance();
			$pname=http_request("event_printer","s","");
			$pwide=http_request("wide_printer","i",-1);
			$printer=new RawPrinter($pname,$pwide);
			$printer->rawprinter_Check();
			break;
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
