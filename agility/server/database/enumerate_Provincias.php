<?php

/*
enumerateProvincias.php

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


require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/classes/DBConnection.php");

class Provincias {
	protected $conn;
	protected $file;
	public $errormsg; // should be public to access to from caller
	
	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @throws Exception if cannot contact database
	 */
	function __construct($file) {
		// connect database
		$this->file=$file;
		$this->conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
		if (!$this->conn) {
			$this->errormsg="$file::construct() cannot contact database";
			throw new Exception($this->errormsg);
		}
	}
	
	/**
	 * Destructor
	 * Just disconnect from database
	 */
	function  __destruct() {
		DBConnection::closeConnection($this->conn);
	}
	
	function select() {
		// evaluate offset and row count for query
		$q=http_request("q","s","");
		$like = ($q!=="") ? " WHERE Provincia LIKE '%".$q."%'" : "";
		$result = array();
		
		// execute first query to know how many elements
		$rs=$this->conn->query("SELECT count(*) FROM Provincias ".$like);
		if (!$rs) {
			$this->errormsg="enumerate_provincias::select( count(*)) Error: ".$this->conn->error;
			return null;
		}
		$row=$rs->fetch_row();
		$rs->free();
		$result["total"] = $row[0];
		
		// second query to retrieve $rows starting at $offset
		$str="SELECT * FROM Provincias ".$like." ORDER BY Provincia ASC";
		do_log("enumerate_provincias::query() $str");
		$rs=$this->conn->query($str);
		if (!$rs) {
			$this->errormsg="enumerate_provincias::query() Error: ".$this->conn->error;
			return null;
		}
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$rs->free();
		$result["rows"] = $items;
		return $result;
	}
}
	
try {
	$result=null;
	$provincias= new Provincias("provincias");
	$operation=http_request("Operation","s",null);
	if ($operation===null) throw new Exception("Call to provincias without 'Operation' requested");
	switch ($operation) {
		case "select": $result=$provincias->select(); break;
		default: throw new Exception("provincias:: invalid operation: $operation provided");
	}
	if ($result===null) throw new Exception($provincias->errormsg);
	if ($result==="") echo json_encode(array('success'=>true));
	else echo json_encode($result);
} catch (Exception $e) {
	do_log($e->getMessage());
	echo json_encode(array('errorMsg'=>$e->getMessage()));
}

	
?>