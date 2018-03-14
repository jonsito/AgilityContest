<?php

/*
Admin.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

require_once(__DIR__."/../../logging.php");
require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/../../auth/Config.php");
require_once(__DIR__."/../../auth/AuthManager.php");
require_once(__DIR__."/DBObject.php");
require_once(__DIR__."/../../printer/RawPrinter.php");

class Admin extends DBObject {
	protected $restore_dir;
	protected $myConfig;
    protected $myAuth;
	protected $file;
	public $errormsg;
	private $dbname;
	private $dbhost;
	private $dbuser;
	private $dbpass;
	public $logfile;

	// used to store backup version info to handle remote updates
	protected $bckVersion="3.7.3";
	protected $bckRevision="20180212_1024";
	protected $bckLicense="00000000";
	protected $bckDate="20180215_0944";

    /**
     * Admin constructor.
     * @param {string} $file name of module to be registered in logs
     * @param {object} $am Auth Manager to be used
     * @param {string} $suffix suffix to be added to progress information file
     */
	function __construct($file,$am,$suffix="") {
        parent::__construct($file);
        $this->restore_dir=__DIR__."/../../../../logs/";
		// connect database
		$this->file=$file;
		$this->myConfig=Config::getInstance();
        $this->myAuth=$am;
        $this->logfile=$this->restore_dir."restore_{$suffix}.log";

		$this->dbname=$this->myConfig->getEnv('database_name');
		$this->dbhost=$this->myConfig->getEnv('database_host');
		$this->dbuser=base64_decode($this->myConfig->getEnv('database_user'));
		$this->dbpass=base64_decode($this->myConfig->getEnv('database_pass'));
	}

    /**
     * Parse mysql dump and pretty-print output
     * FROM: https://gist.github.com/lavoiesl/9a08e399fc9832d12794
     * @param {resource} $stream where to write to default backup ( file or php://output )
     * @param {string} $line parsed line
     * @param {string} base64 encoded encryption key
     * @throws Exception on mysqldump syntax error
     */
	private function process_line($stream,$line,$key) {
		$length = strlen($line);
		$pos = strpos($line, ' VALUES ') + 8;
		$str=substr($line, 0, $pos);
        @fwrite($stream, ($key==="")? $str : ac_encrypt($str,$key));
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
							@fwrite($stream, PHP_EOL);
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
            @fwrite($stream, ($key==="")? $line[$i] : ac_encrypt($line[$i],$key));
		}
	}

	public function dumpLog() {
        $fname="trace-".date("Ymd_Hi").".log";
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate');
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$fname.'"');
        $f=fopen(ini_get('error_log'),"r");
        if(!$f) throw new Exception("Error opening log file");
        while(!feof($f)) { $line = fgets($f); echo $line; }
        fclose($f);
        return "";
	}

	public function resetLog() {
        $f = @fopen(ini_get('error_log'), "r+");
        if ($f !== false) {
            ftruncate($f, 0);
            fputs($f,"Log registry started at ".date("Y-m-d H:i:s")."\n");
            fclose($f);
        }
        return "";
	}

	/**
	 * Automatic backup to log file
	 * and -if defined- to user specified file
	 * @param {integer} mode -1: no copy; 0: numerated backup copy to ${HOME}; 1: try to copy backup to specified file
     * @param {integer} directory to store user copy, or empty to use configuration value
	*/
    public function autobackup($mode=1,$directory="") {
        $this->myLogger->enter();
        $dbname=$this->dbname;
        $dbhost=$this->dbhost;
        $dbuser=$this->dbuser;
        $dbpass=$this->dbpass;
        $dnb=$this->restore_dir."/do_not_backup";
        if (file_exists($dnb)) { // if first_install mark exists do not autobackup at login
            @unlink($dnb);
            return array('do_not_backup'=>true);
        }
        set_time_limit(0); // some windozes are too slow dumping databases
		// for linux
        $cmd="mysqldump"; // unix
		// for windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $path=str_replace("\\apache\\bin\\httpd.exe","",PHP_BINARY);
            $cmd="start /B ".$path."\\mysql\\bin\\mysqldump.exe";
            // $drive=substr(__FILE__, 0, 1);
            // $cmd='start /B '.$drive.':\AgilityContest\xampp\mysql\bin\mysqldump.exe';
        }
        // for Mac-OSX
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'DAR') { // Darwin (MacOSX)
            $cmd='/Applications/XAMPP/xamppfiles/bin/mysqldump';
        }

        // prepare auto backup file
        // rename ( if any ) previous backup
        // notice name uses '_' (underscore) whilst named backup uses '-' (minus sign)
        // this is done to preserve autobackups on temporary directory clear
        $oldname="{$this->restore_dir}/{$dbname}_backup_old.sql";
        $fname="{$this->restore_dir}/{$dbname}_backup.sql";
        @rename($fname,$oldname); // @ to ignore errors in case of
        $resource=@fopen($fname,"w");
        @flock($resource,LOCK_EX);
        if (!$resource) {
            $this->myLogger->error("Cannot fopen system backup file {$fname}");
            return "adminFunctions::AutoBackup('fopen') failed";
        }

        // phase 1: dump structure
        $cmd1 = "$cmd --opt --no-data --single-transaction --routines --triggers -h $dbhost -u$dbuser -p$dbpass $dbname";
        $this->myLogger->info("Ejecutando comando: '$cmd1'");
        $input = popen($cmd1, 'r');
        if ($input===FALSE) return "adminFunctions::AutoBackup('popen 1') failed";

        // insert AgilityContest Info at begining of backup file
        $bckdate=date("Ymd_Hi");
        $ver=$this->myConfig->getEnv("version_name");
        $rev=$this->myConfig->getEnv("version_date");
        $lic=$this->myAuth->getRegistrationInfo()['Serial'];
        $key= ""; // base64_encode(openssl_random_pseudo_bytes(32)); // encryption key
        @fwrite($resource, "-- AgilityContest Version: {$ver} Revision: {$rev} License: {$lic}\n");
        @fwrite($resource, "-- AgilityContest Backup Date: {$bckdate} Key: {$key}\n");

        // now send to client database backup
        while(!feof($input)) {
            $line = fgets($input);
            if (substr($line, 0, 6) === 'INSERT') {
                $this->process_line($resource,$line,$key);
            } else {
                @fwrite($resource, ($key==="")? $line : ac_encrypt($line,$key));
            }
        }
        pclose($input);
        // phase 2: dump data. Exclude ImportData and (if configured to) Eventos table contents
        $noexport="--ignore-table=agility.ImportData";
        if (intval($this->myConfig->getEnv("full_backup"))==0) $noexport .= " --ignore-table=agility.Eventos";

        $cmd2 = "$cmd --opt --no-create-info --single-transaction --routines --triggers $noexport -h $dbhost -u$dbuser -p$dbpass $dbname";
        $this->myLogger->info("Ejecutando comando: '$cmd2'");
        $input = popen($cmd2, 'r');
        if ($input===FALSE) return "adminFunctions::AutoBackup('popen 2') failed";
        while(!feof($input)) {
            $line = fgets($input);
            if (substr($line, 0, 6) === 'INSERT') {
                $this->process_line($resource,$line,$key);
            } else {
                @fwrite($resource, ($key==="")? $line : ac_encrypt($line,$key));
            }
        }
        pclose($input);
        fclose($resource);

        // prepare user-requested backup file (if any)
        $tname="";
        $dt=date("Ymd_Hi");
        // on mode=0 copy to $HOME as numerated (date+hour) backup
        if ($mode==0) $tname="{$this->restore_dir}/{$dbname}-{$dt}.sql";
        // on mode=1 copy backup to file specified in configuration
        if ($mode>0) {
            $dirname=($directory=="")?$this->myConfig->getEnv("backup_dir"):$directory;
            if ($dirname=="") {
                $this->myLogger->trace( "adminFunctions::AutoBAckup) empty user directory");
                return ""; // not an error, just skip generating user defined backup
            }
            if (!is_dir($dirname)) return "adminFunctions::AutoBAckup) invalid user directory {$dirname}";
            if (!is_writeable($dirname)) return "adminFunctions::AutoBAckup) cannot write into user directory {$dirname}";
            $tname="{$dirname}/{$dbname}-userbackup.sql";
        }
        // notice that apache/php may restrict access permissions
        // and copy will silently fail
        if ($tname!=="") {
            $this->myLogger->trace("Copying backup to destination user file {$tname}");
            @copy($fname,$tname);
            if (!file_exists($tname)) return "adminFunctions::AutoBAckup) couldn't create user backup {$tname}";
        }
		// and finally return ok
        $this->myLogger->leave();
        return ""; // return empty string to let json response work
        // notice that exit procedure releases flock on agility_backup.sql
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
		if ($input===FALSE) { $this->errorMsg="adminFunctions::backup():popen() failed"; return null;}
        // open stdout as file handler
        $resource=fopen("php://output","w");
        if ($resource===FALSE) { $this->errorMsg="adminFunctions::backup():popen() failed"; return null;}
        // prepare html response header
        $bckdate=date("Ymd_Hi");
		$fname="$dbname-{$bckdate}.sql";
		header('Set-Cookie: fileDownload=true; path=/');
		header('Cache-Control: max-age=60, must-revalidate');
		header('Content-Type: text/plain; charset=utf-8');
		header('Content-Disposition: attachment; filename="'.$fname.'"');
		// insert AgilityContest Info at begining of backup file
        $ver=$this->myConfig->getEnv("version_name");
        $rev=$this->myConfig->getEnv("version_date");
        $lic=$this->myAuth->getRegistrationInfo()['Serial'];
        $key= ""; // base64_encode(substr("{$lic}{$rev}{$bckdate}",-32)); // encryption key
        $keystr= hash("md5",$key,false);
        @fwrite($resource, "-- AgilityContest Version: {$ver} Revision: {$rev} License: {$lic}\n");
        @fwrite($resource, "-- AgilityContest Backup Date: {$bckdate} Key: {$keystr}\n");
        // now send to client database backup
		while(!feof($input)) {
			$line = fgets($input);
			if (substr($line, 0, 6) === 'INSERT') {
				$this->process_line($resource,$line,$key);
			} else {
                @fwrite($resource, ($key==="")? $line : ac_encrypt($line,$key));
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
                $this->process_line($resource,$line,$key);
            } else {
                @fwrite($resource, ($key==="")? $line : ac_encrypt($line,$key));
            }
        }
        pclose($input);
        fclose($resource);
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
		if ($data==="remoteDownload") {
		    $res=retrieveFileFromURL("http://www.agilitycontest.es/downloads/agility.sql");
		    if ($res===FALSE) return array("errorMsg" => "downloadDatabase(): cannot download file from server");
		    return $res;
        }
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
        // first line is copyright info
        $num=sscanf($lines[0],
            "-- AgilityContest Version: %s Revision: %s License: %s",
            $this->bckVersion, $this->bckRevision, $this->bckLicense);
        if ($num!==3) $this->bckLicense="00000000"; //older db backups lacks on third field
        // second line is backup file creation date
        $num=sscanf("$lines[1]","-- AgilityContest Backup Date: %s",$this->bckDate);
        if ($num===0) $this->bckDate=date("Ymd_Hi"); // older db backups lacks on backup creation date
        // prepare restore process
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

	public function restore() {
        // we need root database access to re-create tables
        $rconn=DBConnection::getRootConnection();
        if ($rconn->connect_error)
			throw new Exception("Cannot perform upgrade process: database::dbConnect()");
		// phase 1: retrieve file from http request
        $data=$this->retrieveDBFile();
        if (is_array($data))
            throw new Exception($data['errorMsg']);
        // phase 2: verify received file
		if (strpos(substr($data,0,25),"-- AgilityContest")===FALSE)
			throw new Exception("Install file is not an AgilityContest database file");
        // phase 3: delete all tables and structures from database
        $this->dropAllTables($rconn);
        // phase 4: parse sql file and populate tables into database
        $this->readIntoDB($rconn,$data);
        // phase 5 update VersionHistory: set current sw version entry with restored backup creation date
        $bckd=toLongDateString($this->bckDate);
        $swver=$this->myConfig->getEnv("version_date");
        $str="INSERT INTO VersionHistory (Version,Updated) VALUES ('{$swver}','{$bckd}') ".
            "ON DUPLICATE KEY UPDATE UPDATE Updated='{$bckd}'";
        $rconn->query($str);
        // finally close db connection and return
        DBConnection::closeConnection($rconn);
        // mark system to do not auto-backup on next login
        @touch($this->restore_dir."/do_not_backup");
		return "";
	}

    public function clearContests($fed=-1) {
        $fed=intval($fed); // for yes the flies
        $f=($fed===-1)?"":" AND (RSCE={$fed})";
        // this will recursively delete teams, journeys, rounds, results, and so
        return $this->query("DELETE FROM Pruebas WHERE (ID>1) {$f}");
    }

	public function clearDatabase($fed=-1) {
        $fed=intval($fed); // for yes the flies...
        $f=($fed===-1)?"":" AND (Federation={$fed})";
		// drop pruebas
        $this->clearContests($fed);
        // do not delete users nor sessions

        // delete dogs and handlers on selected federation
        $this->query("DELETE FROM Perros WHERE (ID>1) {$f}");
        $this->query("DELETE FROM Guias WHERE (ID>1) {$f}");

        // judges and clubes need to evaluate federation mask;
        // do not delete clubes for international contests, !cause they are just countries!
        if ($fed===-1) { // delete all data regardless federation
            $this->query("DELETE FROM Clubes WHERE (ID>1) AND (Federations < 512)"); // do not delete countries!!
            $this->query("DELETE FROM Jueces WHERE (ID>1)");
            $this->query("DELETE FROM Eventos");
        } else {
            $fmask=1<<$fed; // get federation mask
            // remove every judges related only to provided federation
            $this->query("DELETE FROM Jueces WHERE (ID>1) AND (Federations={$fmask})");
            // delete all clubes registered _only_ on requested federation
            if ($f<512) $this->query("DELETE FROM Clubes WHERE (ID>1) AND (Federations={$fmask})");
            // pending: also remove related logos (when no shared)
        }
		return "";
	}

	public function clearTemporaryDirectory() {
		// borramos ficheros relacionados con actualizaciones
		$this->myLogger->trace("Clearing update related tmp files");
		array_map('unlink',glob("{$this->restore_dir}AgilityContest*.zip"));
        array_map('unlink',glob("{$this->restore_dir}update.log"));

		// ficheros excel importados
        $this->myLogger->trace("Clearing excel import related tmp files");
        array_map('unlink',glob("{$this->restore_dir}import*.xlsx"));
        array_map('unlink',glob("{$this->restore_dir}import*.log"));
        array_map('unlink',glob("{$this->restore_dir}import*.json"));

        // ficheros excel importados
        $this->myLogger->trace("Clearing database sync related tmp files");
        if (!is_dir("{$this->restore_dir}updateRequest")) @mkdir("{$this->restore_dir}updateRequest");
        array_map('unlink',glob("{$this->restore_dir}updateRequest/dbsync*.log"));
        array_map('unlink',glob("{$this->restore_dir}updateRequest/*.json"));

        // restore operations log
        $this->myLogger->trace("Clearing restore related tmp files");
        array_map('unlink',glob("{$this->restore_dir}restor*.log"));

        // remove results mail directories
        $this->myLogger->trace("Clearing mail results related tmp files");
        array_map('unlink',glob("{$this->restore_dir}results_*/*.*"));
        array_map('rmdir',glob("{$this->restore_dir}results_*"));

        // remove inscriptions mail directories
        $this->myLogger->trace("Clearing mail inscriptions related tmp files");
        array_map('unlink',glob("{$this->restore_dir}mail_*/*.*"));
        array_map('rmdir',glob("{$this->restore_dir}mail_*"));

        // finally clear also named backup files, but preserve autobackups
        array_map('unlink',glob("{$this->restore_dir}agility-*.sql"));
	}

	public function checkForUpgrades($fireException=true) {
        $info=retrieveFileFromURL(UPDATE_INFO);
        if ( ($info===null) || ($info===FALSE) || (!is_string($info)) ) {
            if ($fireException)  throw new Exception("checkForUpgrade(): cannot retrieve version info from internet");
            $info="version_name = \"0.0.0\"\nversion_date = \"19700101_0000\"\n"; // escape quotes to get newlines into string
        }

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
		$f=fopen($this->restore_dir."/do_upgrade","w");
		fwrite($f,$this->myAuth->getSessionKey());
		fclose($f);
		return $res;
	}

	public function downloadUpgrades($version) {
	    $this->myLogger->enter();
        $source='https://codeload.github.com/jonsito/AgilityContest/zip/master';
        $dest=__DIR__."/../../../../logs/AgilityContest-{$version}.zip";
		// file_get_contents() and copy() suffers from allow_url_fopen and max_mem problem, so just use curl
		// to download about 300Mb
		$res="";
		@unlink($dest); // use @ to prevent warns to console
        set_time_limit(0);
        $fp = fopen ($dest, 'w+');  //This is the file where we save the information
		if(!$fp) {
        	$errors= error_get_last();
        	$res="Create upgrade file error:{$errors['type']} {$errors['message']}";
            $this->handleSession("Done");
        	return $res;
    	}
        $ch = curl_init(str_replace(" ","%20",$source)); //Here is the file we are downloading, replace spaces with %20
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // not really needed but...
        curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 minutes should be enougth for wellknownforslowness github
        curl_setopt($ch, CURLOPT_CAINFO, __DIR__."/../../auth/cacert.pem");
        curl_setopt($ch, CURLOPT_FILE, $fp); // write curl response to file
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // to allow redirect
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); // try to fix some slowness issues in windozes
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, array($this,'downloadProgress'));
        curl_setopt($ch, CURLOPT_NOPROGRESS, false); // needed to make progress function work
        curl_setopt($ch, CURLOPT_BUFFERSIZE, (1024*1024*4)); // set buffer to 4Mb
        if ( curl_exec($ch) === false ) { // get curl response
            $res="Upgrade download error: ".curl_error($ch);
            $this->handleSession("Done");
            return $res;
		}
        curl_close($ch);
        fclose($fp);
        $this->handleSession("Verifying download...");
        // now verify downloaded file
        $zip = new ZipArchive();
        $chk = $zip->open($dest, ZipArchive::CHECKCONS);
        if ($chk !== TRUE) {
            switch($chk) {
                case ZipArchive::ER_NOZIP: $res='Downloaded file is not a zip archive'; break;
                case ZipArchive::ER_INCONS: $res='Upgrade zipfile consistency check failed'; break;
                case ZipArchive::ER_CRC : $res='Upgrade zipfile checksum failed'; break;
                default: $res='Upgrade zipfile check error ' . $chk; break;
            }
        }
        $zip->close();
        $this->handleSession($res);
        $this->handleSession("Done");
        $this->myLogger->leave();
        return $res;
	}

	// notice that this function is called as callback from curl
	// so cannot use any resource of current class because no scope set
	// also github does not provide file size to curl, so cannot evaluate percentage
    function downloadProgress($resource,$download_size, $downloaded, $upload_size, $uploaded)  {
		$dl=intval($downloaded/(1024*1024));
		$msg="$dl Mbytes";
		$this->handleSession($msg);
    }
}

?>
