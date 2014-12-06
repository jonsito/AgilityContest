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
	protected $token=null;
	protected $level=PERMS_NONE;
	protected $mySessionMgr;
	
	function __construct($file) {
		$this->myLogger=new Logger($file);
		/* try to retrieve session token */
		$hdrs= getAllHeaders();
		if (!array_key_exists("X-AC-SessionKey",$hdrs)) {
			// no key found: assume anonymous login
			// $this->level=PERMS_GUEST;
			$this->level=PERMS_ROOT; // a temporary dirty trick :-)
			return;
		} 
		/* if found evaluate for expiration and level */
		$sk=$hdrs['X-AC-SessionKey'];
		$this->mySessionMgr=new Sesiones("AuthManager");
		$obj=$this->mySessionMgr->__selectObject("*","Sesiones","( SessionKey='$sk')");
		if (!$obj) throw new Exception ("Invalid session key: '$sk'");
		$userid=$obj->Operador;
		$lastModified=$obj->LastModified;
		/* if token expired throw exception */
		// TODO: write
		// else retrieve permission level
		$obj=$this->mySessionMgr->getObject("Usuarios","(ID=$userid)");
		if (!$obj) throw new Exception("Provided SessionKey:'$sk' provides invalid User ID: '$userid'");
		// $this->level=$obj->Perms;
		$this->level=PERMS_ROOT; // TODO: remove temporary dirty trick :-)
	}
	
	/*
	 *  create session token or error
	 */
	function login($login,$password) {
		/* access database to check user credentials */
		$obj=$this->mySessionMgr->getObject("*","Usuarios","(Login='$login')");
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
		//create a random session key and store into a new session
		$sk=random_password(16);
		$data=array (
			'Nombre' 	=> 	$obj->Login,
			'Comentario'=> 	$obj->Gecos,
			'Prueba' 	=> 	0,
			'Jornada'	=>	0,
			'Manga'		=>	0,
			'Tanda'		=>	0,
			'Operador'	=>	$obj->ID,
			'SessionKey'=>  $sk,
			// requiredn event manager
			'Type'		=>  'login', 
			'Source'	=>  'AuthManager'
		);
		$this->mySessionMgr->insert(data);
		// retrieve session_id and fire 'login' event
		$sid=$this->mySesionMgr->conn->insert_id;
		$evtMgr=new Eventos("AuthManager",$sid);
		$evtMgr->insert($data);
		// That's all. Return generated session key
		return $sk;
	}
	
	/*
	 * closes current session
	 */
	function logout() {
		/* retrieve session token and mark as expired */
		// TODO: write
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