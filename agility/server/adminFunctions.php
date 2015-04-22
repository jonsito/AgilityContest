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

class Admin {
	protected $myLogger;
	protected $myConfig;
	protected $file;
	public $errormsg;
	
	function __construct($file) {
		// connect database
		$this->file=$file;
		$this->myConfig=Config::getInstance();
		$this->myLogger= new Logger($file,$this->myConfig->getEnv("debug_level"));
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
		
		$dbname=$this->myConfig->getEnv('database_name');
		$dbhost=$this->myConfig->getEnv('database_host');
		$dbuser=$this->myConfig->getEnv('database_user');
		$dbpass=$this->myConfig->getEnv('database_pass');
		
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
	
	public function restore() {
		// TODO: write
		return "";
	}
	
	public function factoryReset() {
		// TODO: write
		
		// reset configuration
		// drop database
		// re-create database
		return "";
	}
	
}

$response="";
try {
	$result=null;
	$adm= new Admin("adminFunctions");
	$am= new AuthManager("adminFunctions");
	$sk=http_request("SessionKey","s","");
	$operation=http_request("Operation","s","");
	if ($operation===null) throw new Exception("Call to adminFunctions without 'Operation' requested");
	switch ($operation) {
		case "backup": 
			/* $am->access(PERMS_ADMIN); */
			$result=$adm->backup();	break;
		case "restore":
			$am->access(PERMS_ADMIN); $result=$adm->restore(); break;
		case "reset":
			$am->access(PERMS_ADMIN); $result=$adm->factoryReset();	break;
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
