<?php
/*
DBObject.php

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


/** mandatory requires for database and logging */
require_once (__DIR__."/../../auth/Config.php");
require_once (__DIR__."/../../logging.php");
require_once (__DIR__."/DBConnection.php");

class DBObject {
	public $conn; // TODO: should be protected
	protected $file;
	public $errormsg; // should be public to access to from caller
	protected $myLogger;
	protected $myConfig;
	
	private $fall; // boolean to notice use of fetch_all() or fetch_array loop
	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @throws Exception if cannot contact database
	 */
	function __construct($file) {
		// connect database
		$this->file=$file;
		$this->myConfig=Config::getInstance();
		$h=$this->myConfig->getEnv("database_host");
		$n=$this->myConfig->getEnv("database_name");
		$u=base64_decode($this->myConfig->getEnv("database_user"));
		$p=base64_decode($this->myConfig->getEnv("database_pass"));
		$l=$this->myConfig->getEnv("debug_level");
		$this->myLogger= new Logger($file,$l);
		$this->conn=DBConnection::getConnection($h,$n,$u,$p);
		if (!$this->conn) {
			$this->errormsg="$file::construct() cannot contact database";
			throw new Exception($this->errormsg);
		}
		// check if exists resultset::fetch_all() method 
		$this->fall= (method_exists('mysqli_result', 'fetch_all'))?true:false; 
	}
	
	/**
	 * Destructor
	 * Just disconnect from database
	 */
	function  __destruct() {
		// DBConnection::closeConnection($this->conn);
	}

	function error($msg) {
		$trace=debug_backtrace();
		$this->errormsg=$this->file."::".$trace[1]['function']."() Error at ".$trace[1]['file'].":".$trace[1]['line'].":\n".$msg;
		return null;
	}
	
	function query($sql) {
		$this->myLogger->query($sql);
		return $this->conn->query($sql);
	}
	
	function fetch_all($rs) {
		// estilo mysqlnd
		if ($this->fall) return $rs->fetch_all(MYSQLI_ASSOC);
		// estilo mysqli
		$res= array();
		while ($row= $rs->fetch_array(MYSQLI_ASSOC)) array_push($res,$row);
		return $res;
	}

    /**
     * if running in master server, set ServerID as ID on last insert
     * @param {string} $table to insert into
     * @param {integer} $id ID of affected row
     */
	function setServerID($table,$id) {
	    // if not in master server do nothing
        $server=$this->myConfig->getEnv('master_server');
        $myself=gethostbyaddr($_SERVER['SERVER_ADDR']);
        if ($server!==$myself) return;
        // on server, every insert in Jueces,Clubes, Perros and Guias
        // should set their server id to be same as their ID
        $sql="UPDATE {$table} SET ServerID={$id} WHERE (ID={$id})";
        $rs=$this->query($sql);
        if (!$rs) return $this->error($this->conn->error);
        $rs->free();
        return "";
    }

	/**
	 * Generic function for handle select() on child classes
	 * @param string $sel SELECT clause (required)
	 * @param string $from FROM clause (required)
	 * @param string $where WHERE clause (optional)
	 * @param string $order ORDER BY clause (optional)
     * @param string $limit LIMIT offset,rows clause (optional)
     * @param string $group GROUP BY clause (optional)
     * @return {array} result (total,rows)
	 */
	function __select($select,$from,$where,$order="",$limit="",$group="") {
		// if $limit is not null, perform a first count query
		$result=array();
		if ($where!=="") $where=" WHERE ".$where;
        if ($group!=="") $group=" GROUP BY ".$group;
        if ($order!=="") $order=" ORDER BY ".$order;
		if ($limit!=="") $limit=" LIMIT ".$limit;
		$result["total"]=0;
		if ($limit!=="") {
			$str= "SELECT count(*) FROM $from $where $group";
			$this->myLogger->query($str);
			$rs=$this->query($str);
			if (!$rs) return $this->error($this->conn->error);
			$row=$rs->fetch_array();
			$result["total"] = $row[0];
			$rs->free();
			// if (rowcount==0) no need to perform a second query
			if ($result["total"]==0) {
				$result["rows"]=array();
				return $result;
			}
		}
		// compose real request
		$str="SELECT $select FROM $from $where $group $order $limit";
		// make query
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// generate result
		$result["rows"] = $this->fetch_all($rs);
		if ($result["total"]==0) $result["total"] = $rs->num_rows;
		$rs->free();
		return $result;
	}

	// mysql has "affected_rows" variable, but sometimes need "matched_rows"
    // to take care on update success, but no real change in row
    // from: https://stackoverflow.com/questions/5289475/get-number-of-rows-matched-by-update-query-with-php-mysqli
	function matched_rows() {
        preg_match_all('!\d+!', $this->conn->info, $m);
        return $m[0][0];
    }

	/**
	 * Perform a query that returns first (and unique) element
	 * as an Object
	 * @param {string} $select SELECT clause (required)
	 * @param {string} $from FROM clause (required)
	 * @param {string} $where WHERE clause
     * @return {object|string} object on success; else error string
	 */
	function __selectObject($select,$from,$where) {
		// compose SQL query
		$str="SELECT $select FROM $from";
		if ($where!=="") $str= $str." WHERE ".$where;
		// make query
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// generate result
		$result=$rs->fetch_object();
		$rs->free();
		return $result;
	}
	
	/**
	 * Perform a query that returns first (and unique) element
	 * as an associative array
	 * @param {string} $select SELECT clause (required)
	 * @param {string} $from FROM clause (required)
	 * @param {string} $where WHERE clause
     * @return {array|string} array on success; else error string
	 */
	function __selectAsArray($select,$from,$where="") {
		$obj=$this->__selectObject($select,$from,$where);
		if (!is_object($obj)) return $obj;
		return json_decode(json_encode($obj), true);
	}

	/**
	 * Retrieves objects from database by given (table,id) pair
	 * @param {string} $table where to search object from
	 * @param {integer} $id primary key of requested object
	 * @return {object|string} obj if found, else error string
	 */
	function __getObject($table,$id) { return $this->__selectObject("*",$table,"(ID=$id)"); }
	function __getArray($table,$id) { return $this->__selectAsArray("*",$table,"(ID=$id)"); }
}