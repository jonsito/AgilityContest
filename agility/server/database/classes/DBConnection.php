<?php
/*
DBConnection.php

Copyright  2013-2020 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

require_once (__DIR__."/../../auth/Config.php");
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
            self::$connections[$key]=array($conn,0);
        }
        self::$connections[$key][1]++; // increase link count
        return self::$connections[$key][0];
    }

    public static function getRootConnection() {
        $myConfig=Config::getInstance();
        $h=$myConfig->getEnv("database_host");
        $n=$myConfig->getEnv("database_name");
        $u=base64_decode($myConfig->getEnv("database_ruser"));
        $p=base64_decode($myConfig->getEnv("database_rpass"));
        return self::getConnection($h,$n,$u,$p);
    }
	
	public static function closeConnection($conn) {
        foreach(self::$connections as $key => $val) {
            if ($val[0]!==$conn) continue;
            $val[1]--;
            if ($val[1]>0) return;
            $conn->close();
            unset (self::$connections[$key]);
            return;
        }
	}

}


?>