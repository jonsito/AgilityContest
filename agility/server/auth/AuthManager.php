<?php
/*
 AccessControl.php

Copyright 2013-2014 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
	
	function __construct($file) {
		$this->myLogger=new Logger($file);
		/* try to retrieve session token */
		/* if found evaluate for expiration and level */
		/* if token expired throw exception */
		$this->level=PERMS_ROOT; // a temporary dirty trick :-)
	}
	
	/*
	 *  create session token or error
	 */
	function login() {
		/* access database to check user credentials */
		/* on login success create session token and store perm level */
		/* on login failed throw exception */
	}
	
	/*
	 * closes current session
	 */
	function logout() {
		/* retrieve session token and mark as expired */
	}
	
	/*
	 * return permissions for provided session token
	 */
	function getPerms() { return $this->level; }
	
	/* 
	 * check permissions on current session token against required level
	 */
	function access($requiredlevel) {
		if ($requiredlevel>=$this->level) return true;
		throw new Exception("Insufficient credentials:({$this->level}) required: $requiredlevel");
	}
}
?>