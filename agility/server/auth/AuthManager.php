<?php
/*
 AccessControl.php

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
require_once (__DIR__."/../logging.php");
require_once (__DIR__."/../tools.php");
require_once (__DIR__."/../database/classes/Sesiones.php");
require_once (__DIR__."/../database/classes/Eventos.php");
require_once (__DIR__."/Config.php");

// permisos de acceso
define ("PERMS_ROOT",0);
define ("PERMS_ADMIN",1);
define ("PERMS_OPERATOR",2);
define ("PERMS_ASSISTANT",3);
define ("PERMS_GUEST",4);
define ("PERMS_NONE",5);

// datos de registro
define('AC_PUBKEY_FILE' , __DIR__."/AgilityContest_puk.pem");
define('AC_REGINFO_FILE' , __DIR__."/registration.info");
define('AC_REGINFO_FILE_BACKUP' , __DIR__."/registration.info.old");
define('AC_REGINFO_FILE_DEFAULT' , __DIR__."/registration.info.default");

class AuthManager {
	
	protected $myLogger;
	protected $mySessionKey=null;
	protected $level=PERMS_NONE;
	protected $operador=0;
	protected $mySessionMgr;
	
	function __construct($file) {
		$config=Config::getInstance();
		$this->myLogger=new Logger($file,$config->getEnv("debug_level"));
		$this->mySessionMgr=new Sesiones("AuthManager");
		/* try to retrieve session token */
		$hdrs= getAllHeaders();
		if (!array_key_exists("X-AC-SessionKey",$hdrs)) {
			$this->myLogger->info("No sessionKey found in request");
			// no key found: assume anonymous login
			$this->level=PERMS_GUEST;
			return;
		} 
		/* if found evaluate for expiration and level */
		$sk=$hdrs['X-AC-SessionKey'];
		$obj=$this->getUserByKey($sk);
		$this->myLogger->info("Username:{$obj->Login} Perms:{$obj->Perms}");
		$this->level=$obj->Perms;
		$this->mySessionKey=$sk;
		$this->operador=$obj->ID;
	}
	
	/**
	 * Localiza al usuario que tiene la SessionKey indicada
	 * @param {string} $sk SessionKey
	 * @return object throw exception
	 */
	function getUserByKey($sk) {
		$obj=$this->mySessionMgr->__selectObject("*","Sesiones,Usuarios","(Usuarios.ID=Sesiones.Operador) AND ( SessionKey='$sk')");
		if (!$obj) throw new Exception ("Invalid session key: '$sk'");
		$userid=$obj->Operador;
		$this->myLogger->info("SessionKey:'$sk' belongs to userid:'$userid'");
	/*	
		// if token expired throw exception 
		// TODO: write
		// $lastModified=$obj->LastModified;
		// else retrieve permission level
		$obj=$this->mySessionMgr->__getObject("Usuarios",$userid);
		if (!$obj) throw new Exception("Provided SessionKey:'$sk' gives invalid User ID: '$userid'");
	*/
		return $obj;
	}
	
	function checkRegistrationInfo( $file = AC_REGINFO_FILE ) {
		$fp=fopen (AC_PUBKEY_FILE,"rb"); $pub_key=fread ($fp,8192); fclose($fp);
		$fp=fopen ($file,"rb"); $data=fread ($fp,8192); fclose($fp);
		$key=openssl_get_publickey($pub_key);
		if (!$key) { /* echo "Cannot get public key";*/	return null; }
		$res=openssl_public_decrypt(base64_decode($data),$decrypted,$key);
		openssl_free_key($key);
		if ($res) return json_decode($decrypted,true);
		return null;
	}
	
	/**
	 * Retrieve only non-critical subset of registration info stored data
	 * @return NULL
	 */
	function getRegistrationInfo() {
		// retrieve registration data
		$ri=$this->checkRegistrationInfo();
		$data["Version"]=$ri["version"];
		$data["Revision"]=$ri["revision"];
		$data["Date"]=$ri["date"];
		$data["User"]=$ri['name'];
		$data["Email"]=$ri['email'];
		$data["Club"]=$ri['club'];
		$data["Serial"]=$ri['serial'];
		return $data;
	}
	
	function registerApp() {
		$this->myLogger->enter();
		// extraemos los datos de registro
		$data=http_request("Data","s",null);
		if (!$data) return array("errorMsg" => "registerApp(): No registration data received");
		if (!preg_match('/data:([^;]*);base64,(.*)/', $data, $matches)) {
			return array("errorMsg" => "registerApp(): Invalid received data format");
		}
		$type=$matches[1]; // 'application/octet-stream', or whatever. Not really used
		$regdata=base64_decode( $matches[2] ); // decodes received data
		// cogemos los datos de registro y los guardamos en un fichero temporal
		$tmpname = tempnam(sys_get_temp_dir(), 'reginfo');
		$fd=fopen($tmpname,"w");
		fwrite($fd,$regdata);
		fclose($fd);
		// comprobamos que el fichero temporal contiene datos de registro validos
		$info=$this->checkRegistrationInfo($tmpname);
		if (!$info) return array("errorMsg" => "registerApp(); Invalid registration data");
		umask(002);
		// ok: fichero de registro correcto. copiamos a su ubicacion final
		copy(AC_REGINFO_FILE,AC_REGINFO_FILE_BACKUP);
		rename($tmpname,AC_REGINFO_FILE);
		// retornamos datos del nuevo registro
		$result=array();
		$result["Version"]=$info['version'];
		$result["Revision"]=$info['revision'];
		$result["Date"]=$info['date'];
		$result["User"]=$info['name'];
		$result["Email"]=$info['email'];
		$result["Club"]=$info['club'];
		$result["Serial"]=$info['serial'];
		// $result['filename']=$tmpname;
		$this->myLogger->leave();
		return $result;
	}

	/**
	 * Authenticate user.
	 * On Login success create session and if needed send login event
	 * @param {string} $login user name
	 * @param {string} $password user password
	 * @param {integer} $sid requested session id to join to
	 * @throws Exception if something goes wrong
	 * @return {array} errorMessage or result data
	 */
	function login($login,$password,$sid=0,$nosession=false) {
		/* access database to check user credentials */
		$this->myLogger->enter();
		$obj=$this->mySessionMgr->__selectObject("*","Usuarios","(Login='$login')");
		if (!$obj) throw new Exception("Login: Unknown user: '$login'");
		$pw=$obj->Password;
		if (strstr('--UNDEF--',$pw)!==FALSE)
			throw new Exception("Seems that AgilityContest has not been properly configured. Please reinstall");
		else if (strstr('--LOCK--',$pw)!==FALSE)
			throw new Exception("Account '$login' is LOCKED");
		else if (strstr('--NULL--',$pw)===FALSE) { // --NULL-- means no password required
			// unencode stored password
			$pass=base64_decode($pw);
			if (!password_verify($password,$pass)) // check password against stored one
				throw new Exception("Login: invalid password for account '$login'");
		}
		/* Arriving here means login success */
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
				'TimeStamp' => 	date('Y-m-d G:i:s')
		);
		// if "nosession" is requested, just check password, do not create any session
		if ($nosession==true) return $data;
		// create/join to a session
		if ($sid<=0) { //  if session id is not defined, create a new session
			// remove all other console sessions from same user
			$str="DELETE FROM Sesiones WHERE ( Nombre='Console' ) AND ( Operador={$obj->ID} )";
			$this->mySessionMgr->query($str);
			// insert new session
			$data['Nombre']="Console";
			$data['Comentario']=$obj->Login." - ".$obj->Gecos;
			$this->mySessionMgr->insert($data);
			// and retrieve new session ID
			$data['SessionID']=$this->mySessionMgr->conn->insert_id;
		} else {
			// to join to a named session we need at least Assistant permission level
			$this->access(PERMS_ASSISTANT); // on fail throw exception
			unset($data['Nombre']); // to avoid override Session Name
			// TODO: check and alert on busy session ID
			// else join to declared session
			$data['SessionID']=$sid;
			$this->mySessionMgr->update($sid,$data);
			// and fire 'login' event
			$evtMgr=new Eventos("AuthManager",$sid);
			$event=array(
					// datos identificativos del evento
					"ID" => 0, 							// Event ID
					"Session" => $sid,  				// Session (Ring) ID
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
					"Value" => 0				// Value
			);
			$evtMgr->putEvent($event);
		}
		// That's all. Return generated result data
		// $this->myLogger->info(json_encode($data));
		$this->myLogger->leave();
		$info=$this->getRegistrationInfo();
		return array_merge($data,$info);
	}
	
	function checkPassword($user,$pass) {
		return $this->login($user,$pass,0,true);	
	}
	
	/*
	 * closes current session
	 */
	function logout() {
		// remove console sessions for this user
		$str="DELETE FROM Sesiones WHERE ( Nombre='Console' ) AND ( Operador={$this->operador} )";
		$this->mySessionMgr->query($str);
		// clear session key  on named sessions
		$str="UPDATE Sesiones 
			SET SessionKey=NULL, Operador=1, Prueba=0, Jornada=0, Manga=0, Tanda=0 
			WHERE ( SessionKey='{$this->mySessionKey}' )";
		$this->mySessionMgr->query($str);
	}
	
	/**
	 * change password for requested user ID
	 * @param {integer} $id user id to change password to
	 * @param {string} $pass old password
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
			case 2:	// comprobamos el user id
				if ($id!=$this->operador) throw new Exception("User can only change their own password");
				// comprobamos que la contrasenya antigua es correcta
				$obj=$this->mySessionMgr->__selectObject("*","Usuarios","(ID=$id)");
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
				$str="UPDATE Usuarios SET Password='$p' WHERE (ID=$id)";
				$res=$this->mySessionMgr->query($str);
				if ($res===FALSE) return $this->mySessionMgr->conn->error;
				return "";
			default: throw new Exception("Internal error: invalid permission level");
		}
		$this->myLogger->leave();
	}
	
	/*
	 * return permissions for provided session token
	 */
	function getPerms() { return $this->level; }
	
	function setPerms($level) { $this->level=$level; }
	
	/* 
	 * check permissions on current session token against required level
	 */
	function access($requiredlevel) {
		if ($requiredlevel>=$this->level) return true;
		throw new Exception("Insufficient credentials:({$this->level}) required: $requiredlevel");
	}
}

?>
