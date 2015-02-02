<?php
/*
DBObject.php

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


/** mandatory requires for database and logging */
require_once (__DIR__."/../../auth/Config.php");
require_once (__DIR__."/../../logging.php");
require_once (__DIR__."/DBConnection.php");

class DBObject {
	public $conn; // TODO: should be protected
	protected $file;
	protected $cache; // ['table'][id->object]
	public $errormsg; // should be public to access to from caller
	protected $myLogger;
	
	private $fall; // boolean to notice use of fetch_all() or fetch_array loop
	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @throws Exception if cannot contact database
	 */
	function __construct($file) {
		// connect database
		$this->file=$file;
		$this->cache=array();
		$config=new Config();
		$h=$config->getEnv("database_host");
		$n=$config->getEnv("database_name");
		$u=$config->getEnv("database_user");
		$p=$config->getEnv("database_pass");
		$l=$config->getEnv("debug_level");
		$this->myLogger= new Logger($file,$l);
		$this->conn=DBConnection::openConnection($h,$n,$u,$p);
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
	 * Generic function for handle select() on child classes
	 * @param string $sel SELECT clause (required)
	 * @param string $from FROM clause (required)
	 * @param string $where WHERE clause (optional)
	 * @param string $order ORDER BY clause (optional)
	 * @param string $limit LIMIT offset,rows clause (optional
	 */
	function __select($select,$from,$where,$order,$limit) {
		// if $limit is not null, perform a first count query
		$result=array();
		if ($where!=="") $where=" WHERE ".$where;
		if ($order!=="") $order=" ORDER BY ".$order;
		if ($limit!=="") $limit=" LIMIT ".$limit;
		$result["total"]=0;
		if ($limit!=="") {
			$str= "SELECT count(*) FROM $from $where";
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
		$str="SELECT $select FROM $from $where $order $limit";
		// make query
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// generate result
		$result["rows"] = $this->fetch_all($rs);
		if ($result["total"]==0) $result["total"] = $rs->num_rows;
		$rs->free();
		return $result;
	}
	
	/**
	 * Perform a query that returns first (and unique) element
	 * as an Object
	 * @param unknown $select SELECT clause (required)
	 * @param unknown $from FROM clause (required)
	 * @param string $where WHERE clause
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
	 * @param unknown $select SELECT clause (required)
	 * @param unknown $from FROM clause (required)
	 * @param string $where WHERE clause
	 */
	function __selectAsArray($select,$from,$where="") {
		$obj=$this->__selectObject($select,$from,$where);
		if (!is_object($obj)) return $obj;
		return json_decode(json_encode($obj), true);
	}

	/**
	 * Retrieves and caches objects from database by given (table,id) pair
	 * @param {string} $table where to search object from
	 * @param {integer} $id primary key of requested object
	 * @return {object/string} obj if found, else error string
	 *
	 * TODO: properly handle cache
	 */
	function __getObject($table,$id) { return $this->__selectObject("*",$table,"(ID=$id)"); }
	function __getArray($table,$id) { return $this->__selectAsArray("*",$table,"(ID=$id)"); }
}