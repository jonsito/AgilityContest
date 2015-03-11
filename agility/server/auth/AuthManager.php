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

define ("PERMS_ROOT",0);
define ("PERMS_ADMIN",1);
define ("PERMS_OPERATOR",2);
define ("PERMS_ASSISTANT",3);
define ("PERMS_GUEST",4);
define ("PERMS_NONE",5);

class AuthManager {
	
	protected $myLogger;
	protected $mySessionKey=null;
	protected $level=PERMS_NONE;
	protected $operador=0;
	protected $mySessionMgr;
	
	function __construct($file) {
		$this->myLogger=new Logger($file);
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
		$obj=$this->mySessionMgr->__selectObject("*","Sesiones","( SessionKey='$sk')");
		if (!$obj) throw new Exception ("Invalid session key: '$sk'");
		$userid=$obj->Operador;
		$this->myLogger->info("SessionKey:'$sk' belongs to userid:'$userid'");
		/* if token expired throw exception */
		// TODO: write
		// $lastModified=$obj->LastModified;
		// else retrieve permission level
		$obj=$this->mySessionMgr->__getObject("Usuarios",$userid);
		if (!$obj) throw new Exception("Provided SessionKey:'$sk' gives invalid User ID: '$userid'");
		return $obj;
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
			'Source' 	=> 	http_request("Source","s","AuthManager")
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
			$this->mySessionMgr->update($sid,$data);
			// and fire 'login' event
			$evtMgr=new Eventos("AuthManager",$sid);
			$evtMgr->putEvent($data);
		}
		// That's all. Return generated result data
		// $this->myLogger->info(json_encode($data));
		$this->myLogger->leave();
		// add registration data
		$config=Config::getInstance();
		$ri=$config->getRegistrationInfo();
		$data["VersionName"]=$config->getEnv("version_name");
		$data["VersionDate"]=$config->getEnv("version_date");
		$data["RegisteredUser"]=$ri['name'];
		$data["RegisteredClub"]=$ri['club'];
		return $data;
	}
	
	function registrationInfo() {
		// add registration data
		$config=Config::getInstance();
		$ri=$config->getRegistrationInfo();
		$data["VersionName"]=$config->getEnv("version_name");
		$data["VersionDate"]=$config->getEnv("version_date");
		$data["User"]=$ri['name'];
		$data["Email"]=$ri['email'];
		$data["Club"]=$ri['club'];
		$data["Serial"]=$ri['serial'];
		return $data;
	}
	
	function registerApp() {
		// TODO: write
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