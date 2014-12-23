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
		$obj=$this->mySessionMgr->__selectObject("*","Sesiones","( SessionKey='$sk')");
		if (!$obj) throw new Exception ("Invalid session key: '$sk'");
		$userid=$obj->Operador;
		$lastModified=$obj->LastModified;
		/* if token expired throw exception */
		// TODO: write
		// else retrieve permission level
		$obj=$this->mySessionMgr->__getObject("Usuarios",$userid);
		if (!$obj) throw new Exception("Provided SessionKey:'$sk' provides invalid User ID: '$userid'");
		$this->myLogger->info("Username:{$obj->Login} Perms:{$obj->Perms}");
		$this->level=$obj->Perms;
		$this->mySessionKey=$sk;
		$this->operador=$obj->ID;
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
	function login($login,$password,$sid=0) {
		/* access database to check user credentials */
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
			'Prueba' 	=> 	http_request("Prueba","i",0),
			'Jornada'	=>	http_request("Jornada","i",0),
			'Manga'		=>	http_request("Manga","i",0),
			'Tanda'		=>	http_request("Tanda","i",0),
			// informacion de respuesta a la peticion
			'UserID'	=>	$obj->ID,
			'Login' 	=> 	$obj->Login,
			'Password'	=>	'', // don't send back password :-)
			'Gecos'		=>	$obj->Gecos,
			'Phone'		=>	$obj->Phone,
			'Email'		=>	$obj->Email,
			'Perms'		=>	$obj->Perms,
			// required for event manager
			'Type'		=>  'login', 
			'Source'	=>  'AuthManager'
		);
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
			$data['SessionID']=$sid;
			// TODO: check and alert on busy session ID
			// else join to declared session 
			$this->mySessionMgr->update($data);
			// and fire 'login' event
			$evtMgr=new Eventos("AuthManager",$sid);
			$evtMgr->putEvent($data);
		}
		// That's all. Return generated result data
		return $data;
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