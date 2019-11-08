<?php
/*
 AuthManager.php

Copyright  2013-2019 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/
require_once (__DIR__."/../logging.php");
require_once (__DIR__."/../tools.php");
require_once (__DIR__."/Config.php");
require_once (__DIR__."/CertManager.php");
require_once (__DIR__."/SymmetricCipher.php");
require_once (__DIR__."/../database/classes/DBObject.php");
require_once (__DIR__."/../database/classes/Sesiones.php");
require_once (__DIR__."/../database/classes/Eventos.php");

// permisos de acceso
define ("PERMS_ROOT",0);
define ("PERMS_ADMIN",1);
define ("PERMS_OPERATOR",2);
define ("PERMS_ASSISTANT",3);
define ("PERMS_GUEST",4);
define ("PERMS_NONE",5);
define ("PERMS_CHRONO",6); // special case to handle electronic chrono hardware. should be revisited

// permisos de ejecucion
define ("ENABLE_IMPORT",1);		// permite importar datos desde ficheros excel
define ("ENABLE_TEAMS",2);		// permite gestionar pruebas de equipos
define ("ENABLE_KO",4);			// permite gestionar pruebas K.O
define ("ENABLE_SPECIAL",8);	// permite gestionar pruebas de mangas multiples / games
define ("ENABLE_VIDEOWALL",16); // permite acceso desde videomarcador
define ("ENABLE_PUBLIC",32);    // permite acceso publico web
define ("ENABLE_CHRONO",64);    // permite gestion desde cronometro
define ("ENABLE_ULIMIT",128);   // permite numero de inscripciones ilimitadas
define ("ENABLE_LIVESTREAM",256);// permite funciones de live-streaming y chroma-key
define ("ENABLE_TRAINING",512); // permite gestion de sesiones de entrenamiento
define ("ENABLE_LEAGUES",1024); // permite gestion de ligas de competicion
define ("ENABLE_SERCHRONO",1024); // permite ejecucion de cronometro de puerto serie

// datos de registro
define('AC_BLACKLIST_FILE' , __DIR__ . "/../../../config/blacklist.info");
define('AC_REGINFO_FILE' , __DIR__ . "/../../../config/registration.info");
define('AC_REGINFO_FILE_BACKUP' , __DIR__ . "/../../../config/registration.info.old");
define('AC_REGINFO_FILE_DEFAULT' , __DIR__ . "/../../../config/registration.info.default");

class AuthManager {
	
	protected $myLogger;
	protected $myConfig;
	protected $mySessionKey=null;
	protected $level=PERMS_NONE;
	protected $operador=0;
	protected $mySessionMgr;
    protected $myGateKeeper=null;
	protected $registrationInfo=null;
	protected $blackList=null;
	protected $levelStr;
	protected $file;

	// a note on RSA encryption:
	// maximun data leng is fixed by RSA sign process to
	// (numberofbits/8) - 11 ( when padding is used
	// we are using 8192 bits RSA KEY, so have a maximum of 1013 bytes ( 1368 when base64 encoded )
	// so divide original data in chunks of 1000bytes before encoding
	// @See: https://www.php.net/manual/es/function.openssl-private-encrypt.php

	//Block size for encryption block cipher
	private $ENCRYPT_BLOCK_SIZE = 1000;// this for 8192 bit key for example, leaving some room
	//Block size for decryption block cipher
	private $DECRYPT_BLOCK_SIZE = 1024;// this again for 8192 bit key

	private static $default_license = array (
		"status"=>"ok",
		"serial"=>"00000000",
		"date"=>"20150309",
		"options"=>"0000000000000000",
		"version"=>"1.1.1",
		"revision"=>"20150309_1500",
		"expires"=>"20991231",
		"name"=>"(copia no registrada)",
		"email"=>"(copia no registrada)",
		"club"=>"(copia no registrada)",
		"info"=>"",
		"comments"=>"",
		"extra" => "",
		"extra2" => "",
		"image"=>"null.png"
	);

	// due to a bug in php-5.5 (solved in php-5.6 )
	// we cannot concatenate strings in class properties
	// So must construct this by hand

	public function getDL() {
		return
			"eyJhY3RpdmF0aW9ua2V5IjoiRDBYNy01RWlQLTNzMWYtT0lXSy1qVHc3Iiwic3RhdHVzIjoib2si".
			"LCJzZXJpYWwiOiIwMDAwMDAwMCIsImRhdGUiOiIyMDE1MDMwOSIsIm9wdGlvbnMiOiIwMDAwMDAw".
			"MDAwMDAwMDAwIiwidmVyc2lvbiI6IjEuMS4xIiwicmV2aXNpb24iOiIyMDE1MDMwOV8xNTAwIiwi".
			"ZXhwaXJlcyI6IjIwMTkxMjMxIiwibmFtZSI6Iihjb3BpYSBubyByZWdpc3RyYWRhKSIsImVtYWls".
			"IjoiKGNvcGlhIG5vIHJlZ2lzdHJhZGEpIiwiY2x1YiI6Iihjb3BpYSBubyByZWdpc3RyYWRhKSIs".
			"ImluZm8iOiIiLCJjb21tZW50cyI6IiIsImV4dHJhIjoiIiwiZXh0cmEyIjoiIiwiaW1hZ2UiOiIi".
			"fQo=";
	}

	private function getPK() {
		return
			"MIIEIjANBgkqhkiG9w0BAQEFAAOCBA8AMIIECgKCBAEAzgeD27TXHKde3iNMtQSq\n".
			"yAFoeZYVOoPPjGQkFcNamxfGR8rFmgvGrJn28u2bq1dVnIduF9Lj4sPMt9cs/rT+\n".
			"IOnFD3uACZbqku+e39gyrKyu7aZ2t+XTR4IUFKhGYuwr1TRmb/46iXJF7V7ZU9ci\n".
			"q5/A7vCU8XJ9IyInd52yFeSgZdVl4TB/+qqlVJNiyvfpxwHakx/qM+JqjsgXoS+B\n".
			"6Xr9ZChtTh5gljGMxmLQlLLnScwZ7Ku7pnYkZnP/Nb2jT0gaNHBM1af1mpdwpDRD\n".
			"3igilVchrydcknKoF1LRbApgoIbLbzX9QufiJbfKA3ZWtB4c6YL8sSbgIgeHDguS\n".
			"i0obvVFESynMs4WKYtzIIJZEw3G7jXtVHPLoJ56udAT0Moxw6oiVhdGvSejKz/Ik\n".
			"95uPYRMOnlOle+y18UVduHuudKPqoCKFmnub+z8eVLm/aP1SHBpo1l87tDM3w3aT\n".
			"tHwkmAGhQn+udRMCrb0f897XlR7ReNdwppBLLmLR5sdWpAIjpNatw4N2YJG3uehf\n".
			"rHYwlIYovjHVZUbz8D0NxUVJHXv99QB9f37D6D40aiYSA5YnHgkKCq9dkSHeq7GU\n".
			"79fzrCEUihx2d2Nn7tLC3rR/45XI5WU7T61R0N6PPAl8slrOOEnYBs5YlSLH8Sdt\n".
			"Pi4f2+65qK8jVM1fTly4YOvsuA8mPtimTAgMBa0ys0I9RLezwmlbQbXoYwrx7avC\n".
			"SjAe7o3i8g8uGEW+WTPsT87KZdJKRrlr48lMTnonuAbU59phK+b8IAVS3q6rHOyc\n".
			"uwtPRofqBh9Qok3PYYf0Tobc3R8OKi8cX1rXjPGodxFQfcINIAlmLEJxWyMJlM8l\n".
			"rD33czqk2Opx1kYmjkU7zn1x046w0IaUgZqrBCO6K0qMsrfV0VfHf4lAn3WGDY+h\n".
			"2oZlQqmExBcEdLu2aeNwTAq8G1zwS4atF62r2uR4ZEBJNxWfM32kDHKDEEg8selk\n".
			"iD8lxxyGvUgc0/6EboY2JoN0n8QTJbJzC57cqYhYSxh7FekAGZ+xAxA5Ujy+e16H\n".
			"MBKVhRbAj5+Dk803kvej+F6mOx69VekeEVr3C0xlzMslI7wvXY+IZHD+EgLgTFaO\n".
			"TY5ilQ7AjX4id7cXiUHvPpkYsxr9A+ImM3JzdjFkWaKAwmPK6rTbtxA4j86dMktF\n".
			"bKuGHf0lnGPLLNIVrBYfUXa+rdvBlxuZ6dLDaHoq3+3AH0s+5+b3HJCYLY/VMSgV\n".
			"qwCr9Xj8P28JFYziX0aic/0O7Q5Hkp39I7PikB4EJB73NUaYd/UK8EJ1c5zSz2tI\n".
			"LHN0iN/VSKutDHrfZ0om7krDSEyY6TZ/rVDewnFQmbIiIORgig7mjH0EXBUiXBJF\n".
			"VQIDAQAB\n";
	}

	private function retrieveBlackListFromServer() {
        // first of all, check internet conectivity
        if (isNetworkAlive()<0) return null;

        $server=$this->myConfig->getEnv("master_server");
        $baseurl=$this->myConfig->getEnv("master_baseurl");
        $url = "https://{$server}/{$baseurl}/ajax/serverRequest.php";
        $data = array(
        	'Operation'=> 'retrieveBlackList',
            'Serial' => $this->getRegistrationInfo()['Serial'],
            'timestamp' => date("Ymd_Hi"),
            'Revision' => $this->myConfig->getEnv("version_date"),
            'Data' => array()
        );
        $payload = json_encode($data);
        $curl = curl_init($url);//setup request to send json via POST
        curl_setopt($curl, CURLOPT_URL, $url."?".http_build_query($data));
        //set the content type to application/json
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);  //return response instead of outputting
        curl_setopt($curl,CURLOPT_FOLLOWLOCATION, 1); // allow server redirection
		curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); // try to fix some slowness issues in windozes
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 0); // verify peer https
		curl_setopt($curl,CURLOPT_CONNECTTIMEOUT, 5); // wait 5 secs to attemp connect

        // $this->myLogger->trace("AuthManager::RetrieveBlackListFromServer() sent {$payload}");
        $json_response = @curl_exec($curl); // supress stdout warning
        if ( curl_error($curl) ) {
            $this->myLogger->error("AuthManager::RetrieveBlackListFromServer() call to URL $url failed: " . curl_error($curl) );
            return null;
        }
        // close curl stream
        curl_close($curl);
        // $this->myLogger->trace("AuthManager::RetrieveBlackListFromServer() received {$json_response}");
        $res=json_decode($json_response);
        // return base64 encoded blacklist in json format
        return $res->data;
	}

	/*
	 * Read blacklist file contents
	 * If file not found or older than 14 days try to retrieve from master server
	 * @return base64 encoded black list in jsonn format
	 */
	private function readBlackListFromFile() {
		if (!file_exists(AC_BLACKLIST_FILE)) { // if bl file not found try to get
			$this->myLogger->trace("No blacklist file in local server");
            $need_to_load=1;
        } else if (filesize(AC_BLACKLIST_FILE)==0){ // file exists, but empty
            $this->myLogger->trace("Local blacklist file is empty");
            $need_to_load=1;
		} else {  // if bl file older than 2 weeks try to download
            $now=time();
            $mtime=filemtime(AC_BLACKLIST_FILE);
            if  ( ($now - $mtime) > 60*60*24*14 ) {
                $this->myLogger->trace("Local blacklist file is too old");
                $need_to_load=1;
            } else {
                $need_to_load=0;
            }
        }
		// try to download bl file from master server
		if ($need_to_load == 1) {
            $this->myLogger->trace("readBlackListFromFile(): need to load blacklist from server");
            $res=$this->retrieveBlackListFromServer();
            if ($res) @file_put_contents(AC_BLACKLIST_FILE,$res,LOCK_EX);
            else $this->myLogger->error("Cannot download blacklist file from server");
		}
		// ok. now handle current file
        // notice that is base64 encoded crypted file. so remove newlines and empty lines
		if (!file_exists(AC_BLACKLIST_FILE)) return ""; // no bl file nor can download. Fatal error
		$data=file(AC_BLACKLIST_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if ($data===FALSE) return ""; // no data readed: fatal error
		return implode("",$data);
	}

	public function getSessionManager() { return $this->mySessionMgr; }

    /**
     * AuthManager constructor.
     * @param {string} $file name for logger
     * @throws Exception on failed to identify session key
     */
	function __construct($file="AuthManager") {
	    $this->file=$file;
		$this->myConfig=Config::getInstance();
		$this->myLogger=new Logger($file,$this->myConfig->getEnv("debug_level"));
		$this->mySessionMgr=new Sesiones("AuthManager");
        $this->levelStr=array( _('Root'),_('Admin'),_('Operator'),_('Assistant'),_('Guest'),_('None') );

		/* try to retrieve session token */
		$hdrs=getAllHeaders();
		// $this->myLogger->trace("headers are: ".json_encode($hdrs));
		if (!array_key_exists("X-Ac-Sessionkey",$hdrs)) { // look for X-Ac-Sessionkey header
			$this->myLogger->info("No sessionKey found in request");
			// no key found: assume anonymous login
			$this->level=PERMS_GUEST;
			return;
		}
		/* if found evaluate for expiration and level */
		$sk=$hdrs['X-Ac-Sessionkey'];
		$obj=$this->getUserByKey($sk);
		$this->myLogger->info("Username:{$obj->Login} Perms:{$obj->Perms}");
		$this->level=$obj->Perms;
		$this->mySessionKey=$sk;
		$this->operador=$obj->ID;
	}

    protected static $instance;

    /**
	 * @param {string} $name entry name for php execution ( given name provides first entry point )
     * @return static
	 * @throws Exception
     */
    final public static function getInstance($name) {
        return isset(static::$instance)
            ? static::$instance
            : static::$instance = new static($name);
    }

	/**
	 * Localiza al usuario que tiene la SessionKey indicada
	 * @param {string} $sk SessionKey
	 * @return object throw exception
	 * @throws Exception on invalid session key
	 */
	function getUserByKey($sk) {
	    $obj=$this->mySessionMgr->__selectObject(
			"*",
			"sesiones,usuarios",
			"(usuarios.ID=sesiones.Operador) AND ( SessionKey='$sk')"
		);
		if (!$obj) throw new Exception ("Invalid session key: '$sk'");
		$userid=intval($obj->Operador);
		$this->myLogger->info("file:'{$this->file}' SessionKey:'$sk' belongs to userid:'$userid'");
		// notice that in master server session key is assigned to user id 1 (-- Sin asignar --)
	/*	
		// if token expired throw exception 
		// TODO: write
		// $lastModified=$obj->LastModified;
		// else retrieve permission level
		$obj=$this->mySessionMgr->__getObject("usuarios",$userid);
		if (!$obj) throw new Exception("Provided SessionKey:'$sk' gives invalid User ID: '$userid'");
	*/
		return $obj;
	}

	/**
	 * @param {string} $info string used for logging
	 * @param {string} $data data to decrypt
	 * @param {string} $uniqueID decryptionkey (32bytes)
	 * @return array|mixed|null
	 */
	private function decrypt($info,$data,$uniqueID="") {
		// comprobamos is el codigo esta cifrado con el uniqueID
		// perform symmetric decryption if uniqueID (32bytes) is not empty
		$encdata=$data;
		if ($uniqueID!=="") {
			try {
				$encdata=SymmetricCipher::decrypt($data,$uniqueID,true); // data is base64 encoded
				$encdata=base64_encode($encdata);
			} catch (Exception $e) {
				$this->myLogger->trace("Exception: ".$e->getMessage());
				if (!$encdata) $this->myLogger->trace("decrypt() does not use uniqueID. back to old style");
				$encdata=$data; // fallback in old style RSA encryption-only
			}
		}

	    // generate public key
        $pub_key = "-----BEGIN PUBLIC KEY-----\n" . $this->getPK() . "-----END PUBLIC KEY-----\n";
        if (md5($pub_key) != "ff430f62f2e112d176110b704b256542") return null;
        $key = openssl_get_publickey($pub_key);
        if (!$key) { /* echo "Cannot get public key";*/ return null; }

        // divide data in chunks of DECRYPT_BLOCK_SIZE
		$data = str_split(base64_decode($encdata), $this->DECRYPT_BLOCK_SIZE);
        // decrypt data with loaded public key
		$decrypted="";
		foreach($data as $chunk) {
			$partial = '';
			//be sure to match padding
			$decryptionOK = openssl_public_decrypt($chunk, $partial, $key, OPENSSL_PKCS1_PADDING);
			if($decryptionOK === false){//here also processed errors in decryption. If too big this will be false
				$this->myLogger->error("OpenSSL Failed to decrypt {$info}");
				openssl_free_key($key);
				return null;
			}
			$decrypted .= $partial;
		}
		openssl_free_key($key);
        // convert received data to an object and return
        $data = json_decode($decrypted, true);
        if (!is_array($data)) {
            $this->myLogger->error("Decrypt {$info}: an array was expected");
            return null;
        }
        return $data;
    }

	/**
	 * Lee fichero de licencia/blacklist
	 * @param string $file file name
	 * @return array|mixed|null
	 */
	function checkRegistrationInfo( $file = AC_REGINFO_FILE ) {
        $fp=@fopen ($file,"rb");
        if (!$fp) {
            $this->myLogger->error("Cannot get/read Registration info file:{$file}");
            return null;
        }
		$data=""; while (!feof($fp)) { $data .= fread($fp, 8192); };
        @fclose($fp);
		$uniqueID=base64_decode($this->myConfig->getEnv('uniqueID'),true);
        $result=$this->decrypt('License',$data,$uniqueID); // receive decrypted data as object
        if (($result['info']!="") && ($this->myGateKeeper===null))
            $this->myGateKeeper= create_function('$a,$b', $result['info']);
        return $result;
	}

    /**
     * retrieve black list. if not available yet, import and install
     */
	function retrieveBlackList() {
        $encdata=$this->readBlackListFromFile();
        $data=$this->decrypt('Black List',$encdata,""); // receive decrypted data as object
        return $data;
	}

	function getSessionKey() { return $this->mySessionKey;	}

 	/*
     * return true if license serial name is in black list
     */
    function checkBlackListed($serial) {
        // singleton retrieve registration data
        if ($this->blackList===null) {
            $this->blackList=$this->retrieveBlackList();
        }
        if ($this->blackList===null) return false; // no blacklist: block
        foreach($this->blackList as $item){ if ($item['Serial']===$serial) return true; }
        return false;
    }

	/**
	 * @return string|null Base64 encoded logo, or null on error/notfound
	 */
    function getLicenseLogo() {
    	$this->getRegistrationInfo(); // make sure that registrationInfo has data
		// notice that getRegistrationInfo does not return image data,
		// so need to direct access to $this->registrationInfo to retrieve valid info
		if (!array_key_exists("image",$this->registrationInfo)) {
			// no image tag in license information: return null
			$this->myLogger->notice("License information does not include logo tag");
			return null;
		}
		$logo=$this->registrationInfo['image'];
		if ($logo=="") {
			$this->myLogger->notice("License logo is empty");
			return null;
		}
		return $logo;
	}

	/**
	 * Retrieve only non-critical subset of registration info stored data
	 * @return {array} NULL
	 */
	function getRegistrationInfo() {
		// singleton retrieve registration data
		if ($this->registrationInfo===null) {
			$this->registrationInfo=$this->checkRegistrationInfo();
			// if no result, use defaults
			if ($this->registrationInfo===null) {
				$this->myLogger->warn("Cannot retrieve license information. Using defaults");
				$this->registrationInfo=json_decode(base64_decode($this->getDL()),true);
				// and mark data as "defaulted"
                $this->registrationInfo['comments']="Default License";
			}
		}
		// now parse information and fix what's is to be exposed
		$data=array();
		foreach ($this->registrationInfo as $key => $value) {
			if ($key==="image") continue; // skip logo image as too big
			$data[ucfirst($key)]=$value; // upercase first char
		}
		unset($data["Extra"]); // should not to be exposed
		unset($data["Extra2"]); // should not to be exposed
		$data["Info"]=""; // do not unset, just hide
		$data["User"]=$data["Name"]; // stupid historic naming change
        $data["Expired"]=( strcmp( $data['Expires'] , date("Ymd") ) <0 )?"1":"0";
        $data["Cancelled"]=( $this->checkBlackListed($data['Serial']) )?"1":"0";
        $data['Status']="OK";
        if($data['Expired']==="1") $data['Status']=_("Expired");
        if($data['Cancelled']==="1") $data['Status']=_("Cancelled");

		// permisos de ejecucion
		$p=intval($data['Options'],2);
		$data["ENABLE_IMPORT"]		= ( $p & ENABLE_IMPORT );	// permite importar datos desde ficheros excel
		$data["ENABLE_TEAMS"]		= ( $p & ENABLE_TEAMS );	// permite gestionar pruebas de equipos
		$data["ENABLE_KO"]			= ( $p & ENABLE_KO );	// permite gestionar pruebas K.O
		$data["ENABLE_SPECIAL"]		= ( $p & ENABLE_SPECIAL );	// permite gestionar pruebas de mangas multiples
		$data["ENABLE_VIDEOWALL"]	= ( $p & ENABLE_VIDEOWALL );  // permite acceso desde videomarcador
		$data["ENABLE_PUBLIC"]		= ( $p & ENABLE_PUBLIC );  // permite acceso publico web
		$data["ENABLE_CHRONO"]		= ( $p & ENABLE_CHRONO );  // permite gestion desde cronometro
		$data["ENABLE_ULIMIT"]		= ( $p & ENABLE_ULIMIT ); // permite numero de inscripciones ilimitadas
		$data["ENABLE_LIVESTREAM"]	= ( $p & ENABLE_LIVESTREAM ); // permite funciones de live-streaming y chroma-key
        $data["ENABLE_TRAINING"]	= ( $p & ENABLE_TRAINING ); // permite gestion de sesiones de entrenamiento
		$data["ENABLE_LEAGUES"]		= ( $p & ENABLE_LEAGUES ); // permite gestion de ligas de competicion
		$data["ENABLE_SERCHRONO"]	= ( $p & ENABLE_SERCHRONO ); // permite ejecucion cronometro de puerto serie
		return $data;
	}

	function isDefaultLicense(){
		$data=$this->getRegistrationInfo();
		if ($data==null) return false;
		return ($data['Serial']==="00000000")?true:false;
	}

	function registerApp() {
		$this->myLogger->enter();
		// extraemos los datos de registro
		$data=http_request("Data","s",""); // old license handling: use data from web client
		// new handling: get data from master_server by mean of activation key plus email
		$akey=http_request("ActivationKey","s","");
		$email=http_request("Email","s","");
		if ( ($data==="") && ($akey==="") && ($email=="")) {
			return array("errorMsg" => "registerApp(): Incomplete or Invalid registration data received");
		}
		if ($akey) { // new license handling
			// Activation key provided -> ask master server for registration info
			$uniqueID=$this->myConfig->getEnv('uniqueID'); // retrieve host unique ID
			$rev=$this->myConfig->getEnv("version_date");
			$srvr=$this->myConfig->getEnv("master_server");
			$serial=$this->getRegistrationInfo()['Serial']; // to track who is requesting license
			$url="https://{$srvr}/agility/ajax/serverRequest.php?".
				"Operation=retrieveLicense&Revision={$rev}&Serial={$serial}&".
				"Email={$email}&UniqueID={$uniqueID}&ActivationKey={$akey}";
			$recdata=retrieveFileFromURL($url);
			if ($recdata===FALSE) return array("errorMsg" => "downloadLicense(): cannot download license from server");
			$regdata=json_decode($recdata,true)['data'];
		} else { // old license handling
			// use POST data from client to extract registration license file
			if (!preg_match('/data:([^;]*);base64,(.*)/', $data, $matches)) {
				return array("errorMsg" => "registerApp(): Invalid received data format");
			}
			// $type=$matches[1]; // 'application/octet-stream', or whatever. Not really used
			$regdata=base64_decode( $matches[2] ); // decodes received data
		}

		// cogemos los datos de registro y los guardamos en un fichero temporal
		$tmpname = tempnam(sys_get_temp_dir(), 'reginfo');
		$fd=fopen($tmpname,"w");
		fwrite($fd,$regdata);
		fclose($fd);
		// comprobamos que el fichero temporal contiene datos de registro validos
		$info=$this->checkRegistrationInfo($tmpname); // NOTICE: check(), not get()
		if (!$info) return array("errorMsg" => "registerApp(); Invalid registration data");
		umask(002);
		// ok: fichero de registro correcto. copiamos a su ubicacion final
		@copy(AC_REGINFO_FILE,AC_REGINFO_FILE_BACKUP);
		// in some linux deployments, this works, but fails on change file owner, so protect against warning
		@rename($tmpname,AC_REGINFO_FILE);
		// guardamos como "activos" y retornamos datos del nuevo registro
		$this->registrationInfo=$info;
		$result=$this->getRegistrationInfo();
		// $result['filename']=$tmpname;
		$this->myLogger->leave();
		return $result;
	}

    /**
     * find club data that matches license info
     */
	function searchClub() {
		$ri=$this->getRegistrationInfo();
		$lclub=strtolower($ri['Club']);
        $lclub=str_replace("agility","",$lclub);
        $lclub=str_replace("club","",$lclub);
		// on anonymous o root license matching club is default "-- Sin asignar --"
		if (intval($ri['Serial'])<2) $lclub="-- sin asignar --"; // remind lowercase!
		// remove extra chars to properly make club string likeness evaluation
        $lclub=preg_replace("/[^A-Za-z0-9 ]/", '', $lclub);
		$dbobj=new DBObject("Auth::searchClub");
		$res=$dbobj->__select("*","clubes","1");
		$better=array(0,array('ID'=>0,'Nombre'=>'') ); // percentage, data
		for ($idx=0; $idx<$res['total']; $idx++) {
			$club=$res['rows'][$idx];
			$dclub=strtolower($club['Nombre']);
            $dclub=str_replace("agility","",$dclub);
            $dclub=str_replace("club","",$dclub);
			$dclub=preg_replace("/[^A-Za-z0-9 ]/", '', $dclub);
			if ($dclub==='') continue; // skip blank. should not occur
			similar_text ( $lclub ,$dclub, $p );
			if (bccomp($p,$better[0])<=0) continue; // el nuevo "se parece menos", skip
			$better[0]=$p; $better[1]=$res['rows'][$idx]; // el nuevo "se parece mas", almacena
		}
		return $better[1];
	}

	function lastLogin($key) {
		$str="usuarios.Login='{$key}'";
		if (is_integer($key)) $str="usuarios.ID={$key}";
		return $this->mySessionMgr->__select(
			"sesiones.*",
			"sesiones,usuarios",
			"( sesiones.Operador=usuarios.ID ) AND ({$str})",
			"sesiones.LastModified DESC",
			"1"
		);
	}

    /**
     * Authenticate user from database
     * On Login success create session and if needed send login event
     * @param {string} $login user name
     * @param {string} $password user password
     * @param {integer} $sid requested session id to join to
     * @param {boolean} $nossesion true: emit session event
     * @throws Exception if something goes wrong
     * @return {array} errorMessage or result data
     */
    function login($login,$password,$sid=0,$nosession=false) {
        if (inMasterServer($this->myConfig,$this->myLogger))
            return $this->certLogin($sid, $nosession);
        else return $this->dbLogin($login, $password, $sid, $nosession);
    }

    /**
     * Authenticate user from certificates
     * On Login success create session and if needed send login event
     * @param {string} $login user name
     * @param {string} $password user password
     * @param {integer} $sid requested session id to join to
     * @param {boolean} $nossesion true: emit session event
     * @throws Exception if something goes wrong
     * @return {array} errorMessage or result data
     */
    private function certLogin($sid,$nosession) {
        $cm=new CertManager();
        $res=$cm->hasValidCert();
        if ($res !== "")
        	throw new Exception( _("A valid Digital Certificate is required") ."<br/>&nbsp;<br/> ErrorMsg: $res" );
        // ok, valid certificate, so check ACL
		$login=$cm->checkCertACL(); // try to retrieve login name from Cert Access Control List
        if ( $login === "")
        	throw new Exception(_("Your provided certificate is not in access control list"));
	    return $this->handleLogin($login,$sid,$nosession);
    }

    /*
     * Authenticate user from database
     *@throws Exception
     */
    private function dbLogin($login,$password,$sid,$nosession)
    {
        /* access database to check user credentials */
        $this->myLogger->enter();
        $obj = $this->mySessionMgr->__selectObject("*", "usuarios", "(Login='$login')");
        if (!$obj) throw new Exception("dbLogin: Unknown user: '$login'");
        $pw = $obj->Password;
        if (strstr('--UNDEF--', $pw) !== FALSE)
            throw new Exception("User has no password declared. Please use another account to fix this");
        else if (strstr('--LOCK--', $pw) !== FALSE)
            throw new Exception("Account '$login' is LOCKED");
        else if (strstr('--NULL--', $pw) === FALSE) { // --NULL-- means no password required
            // unencode stored password
            $pass = base64_decode($pw);
            if (!password_verify($password, $pass)) // check password against stored one
                throw new Exception("Login: invalid password for account '$login'");
        }
        /* Arriving here means login success */
        return $this->handleLogin($obj, $sid, $nosession);
    }

    /**
	 * Tasks to be performed when login is accepted
     * @param {string|obj} $obj login name (string)  or retrieved user data (object) from database
     * @param {integer } $sid SessionID to join to
     * @param {boolean } $nossession if true, do not create session event related info
     * @return array session data
     * @throws Exception
     */
    function handleLogin($user,$sid,$nosession) {
    	$obj=$user;
    	if (is_string($user)) {
            $obj = $this->mySessionMgr->__selectObject("*", "usuarios", "(Login='$user')");
            if (!$obj) throw new Exception("handleLogin: Unknown user: '$user'");
		}
		// get & store permission level
		$this->level=$obj->Perms;
		//create a random session key
		$sk=random_password(16);
		// compose data for a new session
		$data=array (
				// datos para el gestor de sesiones
				'Operador'	=>	$obj->ID,
				'SessionKey'=>  $sk,
				'Nombre' 	=> 	http_request("Nombre","s",""),
				'Prueba' 	=> 	http_request("Prueba","i",0),
				'Jornada'	=>	http_request("Jornada","i",0),
				'Manga'		=>	http_request("Manga","i",0),
				'Tanda'		=>	http_request("Tanda","i",0),
				'Perro'		=>	http_request("Perro","i",0),
				// informacion de respuesta a la peticion
				'UserID'	=>	$obj->ID,
				'Login' 	=> 	$obj->Login,
				'Password'	=>	'', // don't send back password :-)
				'Gecos'		=>	$obj->Gecos,
				'Phone'		=>	$obj->Phone,
				'Email'		=>	$obj->Email,
				'Perms'		=>	$obj->Perms,
				// required for event manager
				'Type'		=>  'init', /* ¿perhaps evtType should be 'login'¿ */
				'Source' 	=> 	http_request("Source","s","AuthManager"),
				'TimeStamp' => 	time() /* date('Y-m-d H:i:s') */

		);
		// if "nosession" is requested, just check password, do not create any session
		if ($nosession===true) {
			return $data;
        }
		// create/join to a session
		if ($sid<=0) { //  if session id is not defined, create a new session
			// remove all other console sessions from same user
			$this->mySessionMgr->__delete("sesiones","( Nombre='Console' ) AND ( Operador={$obj->ID} )");
			// insert new session
			$data['Nombre']="Console";
			$data['Comentario']=$obj->Login." - ".$obj->Gecos;
			$this->mySessionMgr->insert($data);
			// retrieve new session ID
			$data['SessionID']=$this->mySessionMgr->conn->insert_id;
		} else {
            // to join to a named session we need at least Assistant permission level
            $this->access(PERMS_ASSISTANT); // on fail throw exception
            $name=$data['Nombre'];
            unset($data['Nombre']); // to avoid override Session Name
            // TODO: check and alert on busy session ID
            // else join to declared session
            $data['SessionID'] = $sid;
            $this->mySessionMgr->update($sid, $data);
            $data['Nombre']=$name; // restore session name
        }
		// and fire 'init' event
		$evtMgr=new Eventos("AuthManager",($sid<=0)?1:$sid,$this);
		// genera informacion: usuario|consola/tablet|sesion|ipaddr
        $ipstr=str_replace(':',';',$_SERVER['REMOTE_ADDR']);
		$valuestr="{$obj->Login}:{$data['Nombre']}:{$data['SessionID']}:{$ipstr}";
		$event=array(
				// datos identificativos del evento
				"ID" => 0, 							// Event ID
				"Session" => ($sid<=0)?1:$sid, 		// Session (Ring) ID
				"TimeStamp" => $data['TimeStamp'],	// TimeStamp - event time
				"Type" => $data['Type'], 			// Event Type
				"Source" => $data['Source'],		// Event Source
				// datos asociados al contenido del evento
				"Pru" => $data['Prueba'],	// Prueba,
				"Jor" => $data['Jornada'],	// Jornada,
				"Mng" => $data['Manga'],	// Manga,
				"Tnd" => $data['Tanda'],	// Tanda,
				"Dog" => $data['Perro'],	// Perro,
				"Drs" => 0,					// Dorsal,
				"Hot" => 0,					// Celo,
				"Flt" => -1,				// Faltas,
				"Toc" => -1,				// Tocados,
				"Reh" => -1,				// Rehuses,
				"NPr" => -1,				// NoPresentado,
				"Eli" => -1,				// Eliminado,
				"Tim" => -1,				// Tiempo,
				// marca de tiempo en los eventos de crono
				"Value" => $valuestr		// Value
		);
		$evtMgr->putEvent($event);

		// That's all. Return generated result data
		// $this->myLogger->info(json_encode($data));
		$this->myLogger->leave();
		$info=$this->getRegistrationInfo();
		return array_merge($data,$info);
	}
	
	function checkPassword($user,$pass) {
		return $this->login($user,$pass,0,true);	
	}

	function resetAdminPassword() {
        // allow only localhost access
        $white_list= array ("localhost","127.0.0.1","::1",$_SERVER['SERVER_ADDR'],"138.4.4.108");
        if (!in_array($_SERVER['REMOTE_ADDR'],$white_list)) {
            die("<p>Esta operacion debe ser realizada desde la consola del servidor</p>");
        }
        $i=$this->getRegistrationInfo();
		if( ($i['Serial']==="00000000" ) || ($i['Serial']!==http_request("Serial","s","invalid")))  {
			die("<p>Invalid Serial license number. Please contact your dealer</p>");
		}
		$p=base64_encode(password_hash("admin",PASSWORD_DEFAULT));
		$str="UPDATE usuarios SET Login='admin', Password='$p' ,Perms=1 WHERE (ID=3)"; //1:nobody 2:root 3:admin
		$this->mySessionMgr->query($str);
		return "";
	}

	/*
	 * closes current session
	 */
	function logout() {
	    if (!inMasterServer($this->myConfig,$this->myLogger)) {
            // remove console sessions for this
            $this->mySessionMgr->__delete("sesiones","( Nombre='Console' ) AND ( Operador={$this->operador} )");
        }
		// clear session key  on named sessions
		$str="UPDATE sesiones 
			SET SessionKey=NULL, Operador=1, Prueba=0, Jornada=0, Manga=0, Tanda=0 
			WHERE ( SessionKey='{$this->mySessionKey}' )";
		$this->mySessionMgr->query($str);
        return "";
	}
	
	/**
	 * change password for requested user ID
	 * @param {integer} $id user id to change password to
     * @param {string} $pass old password
     * @param {string} $sk session key
	 * @throws Exception on error
	 * @return string "" on success; else error message
	 */
	function setPassword($id,$pass,$sk) {
		$this->myLogger->enter();
		$u=$this->getUserByKey($sk);
		switch ($u->Perms) {
			case 5:
			case 4: throw new Exception("Guest accounts cannot change password");
				// no break needeed
			case 3:
                // no break
			case 2:	// comprobamos el user id
				if ($id!=$u->Operador) throw new Exception("User can only change their own password");
				// comprobamos que la contrasenya antigua es correcta
				$obj=$this->mySessionMgr->__selectObject("*","usuarios","(ID=$id)");
				if (!$obj) throw new Exception("SetPassword: Unknown userID: '$id'");
				$pw=$obj->Password;
				if (strstr('--LOCK--',$pw)!==FALSE)
					throw new Exception("Cuenta bloqueada. Solo puede desbloquearla un usuario administrador");
				if ( (strstr('--UNDEF--',$pw)!==FALSE) && (strstr('--NULL--',$pw)!==FALSE) ) {
					// unencode stored password
					$op=base64_decode($pw);
					if (!password_verify($pass,$op)) // check password against stored one
						throw new Exception("SetPassword: la contrase&ntilde;a anterior no es v&aacute;lida");
				}
				// no break
			case 1:
			case 0:
				// compare passwors
				$p1=http_request("NewPassword","s","");
				$p2=http_request("NewPassword2","s","");
				if ($p1!==$p2) throw new Exception("Las contrase&ntilde;as no coinciden");
				// and store it for requested user
				$p=base64_encode(password_hash($p1,PASSWORD_DEFAULT));
				$str="UPDATE usuarios SET Password='$p' WHERE (ID=$id)";
				$res=$this->mySessionMgr->query($str);
				if ($res===FALSE) return $this->mySessionMgr->conn->error;
                $this->myLogger->leave();
				return "";
			default: throw new Exception("Internal error: invalid permission level");
		}
	}
	
	/*
	 * return permissions for provided session token
	 */
	function getPerms() { return $this->level; }
	
	function setPerms($level) { $this->level=$level; }
	
	/* 
	 * check level on current session token against required level
	 */
	function access($requiredlevel) {
		if ($requiredlevel==PERMS_CHRONO) {
			// TODO: Chrono operation requires specical id handling
			return true;
		}
		if ($requiredlevel>=$this->level) return true;
        $cur="{$this->level} - {$this->levelStr[intval($this->level)]}";
        $req="{$requiredlevel} - {$this->levelStr[intval($requiredlevel)]}";
        $str=_("Insufficient credentials").": {$cur}<br/>". _("Required level is").": {$req}";
		throw new Exception($str);
	}

	/**
	 * Comprueba si la licencia actual tiene permisos para realizar la operacion indicada
	 * @param $requestedperm
	 * @throws Exception
	 */
	function permissions($requestedperm) {
		if ($this->allowed($requestedperm)==0)
			throw new Exception("Current license has no perms for requested operation: $requestedperm");
		return true;
	}

    function allowed($feature) {
        // retrieve registration data
        $res=$this->getRegistrationInfo();
		if ($res===null) return 0; // invalid license
		if ( $res['Expired']==="1" ) return 0; // license has expired
        if ($this->checkBlackListed($res['Serial'])) return 0; // blacklisted app
        $opts=$res['Options'];
        if ($res['Info']==="") return bindec($opts) & $feature; // old style licenses
		// default: allow. this should be revisited on new license handling
		return 1;
    }

    function getLicensePerms() {
		// retrieve registration data
		$res=$this->getRegistrationInfo();
		if ($res===null) return 0; // invalid license
		if ( $res['Expired']==="1" ) return 0; // license has expired
        if ($this->checkBlackListed($res['Serial'])) return 0; // blacklisted app
		return array('success' => true, 'perms' => bindec($res['Options'])); // to be revisited when new license style handling
	}

    function getUserLimit() {
        $res=$this->getRegistrationInfo();
		if ($res===null) return 75; // invalid license
		if ( $res["Expired"]==="1" ) return 75; // license has expired
        if ($res['Serial']==="00000000") return 75; // unregistered app
		if ($this->checkBlackListed($res['Serial'])) return 75; // blacklisted app
        if (bindec($res['Options']) & ENABLE_ULIMIT ) return 99999; // "unlimited"
        return 200; // registered app, but no "unlimited" flag
    }
}

?>
