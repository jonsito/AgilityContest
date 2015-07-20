<?php
/*
DBConnection.php

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

/**
 * DB connection handler
 * This class should only be used from DBConnection objects
 * @author jantonio
 *
 */
class DBConnection {

	private static $connections=array();

    /**
     * Singleton static method to create database connection
     * @param $host
     * @param $name
     * @param $user
     * @param $pass
     * @return null
     */
    public static function getConnection($host,$name,$user,$pass) {
        $key="$host:$name:$user";
        if (!array_key_exists($key,self::$connections)) {
            $conn = new mysqli($host,$user,$pass,$name);
            if ($conn->connect_error) return null;
            $conn->query("SET NAMES 'utf8'");
            self::$connections[$key]=$conn;
        }
        return self::$connections[$key];
    }

	// singleton pattern
	private static $instance=null;
	public static function getInstance() {
		if (  !self::$instance instanceof self) self::$instance = new self;
		return self::$instance;
	}
	
	public static function closeConnection($conn) {
        foreach(self::$connections as $c) {
            // if ($c===$conn) $c->close(); //TODO: what to do in close ?
        }
	}

}


?>