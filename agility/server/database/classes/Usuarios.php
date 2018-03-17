<?php
/*
Usuarios.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/



require_once("DBObject.php");

class Usuarios extends DBObject {

	function __construct($file="Usuarios") {
		parent::__construct($file);
	}

	/**
	 * Insert a new user into database
	 * @return {string} "" if ok; null on error
	 */
	function insert() {
		$this->myLogger->enter();
		// iniciamos los valores, chequeando su existencia
		$login =	http_request("Login","s",null,false); // pkey not null
		$gecos =	http_request("Gecos","s",null,false);
		$phone =	http_request("Phone","s",null,false);
		$email = 	http_request("Email","s",null,false);
        $club = 	http_request("Club","i",1);
        $perms= 	http_request("Perms","i",0);

		$this->myLogger->debug("Login: '$login' Gecos: '$gecos' Phone: '$phone' Email: '$email' Club: '$club' Perms: $perms");
		// componemos un prepared statement
		$sql ="INSERT INTO usuarios (Login,Password,Gecos,Phone,Email,Club,Perms) VALUES(?,'--UNDEF--',?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('ssssii',$login,$gecos,$phone,$email,$club,$perms);
		if (!$res) return $this->error($stmt->error);

		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error);
		$stmt->close();
		$this->myLogger->leave();
		return ""; 
	}
	
	/**
	 * Update user data
	 * @param {integer} $id Juez ID primary key
	 * @return {string} "" on success; null on error
	 */
	function update($id) {
		$this->myLogger->enter();
		// root may not be removed nor changed
		if ($id<=1) return $this->error("Invalid User ID");

		// iniciamos los valores, chequeando su existencia
		$login =	http_request("Login","s",null,false); // pkey not null
		$gecos =	http_request("Gecos","s",null,false);
		$phone =	http_request("Phone","s",null,false);
        $email = 	http_request("Email","s",null,false);
        $club = 	http_request("Club","i",1);
		$perms= 	http_request("Perms","i",0);
		$this->myLogger->debug("Login: '$login' Gecos: '$gecos' Phone: '$phone' Email: '$email' Club: '$club' Perms: $perms");

		// componemos un prepared statement
		$sql ="UPDATE usuarios SET Login=? , Gecos=? , Phone=? , Email=? , Club=?, Perms=? WHERE ( ID=$id )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ssssii',$login,$gecos,$phone,$email,$club,$perms);
		if (!$res) return $this->error($stmt->error);
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error);
		$stmt->close();
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Delete user with provided name
	 * @param {integer} $id ID primary key
	 * @return "" on success ; otherwise null
	 */
	function delete($id) {
		$this->myLogger->enter();
		if ($id<=1) return $this->error("Invalid User ID"); // cannot delete if user<=default
		$res= $this->__delete("usuarios","( ID={$id} )");
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}

	/**
	 * Select user with provided ID
	 * @param {string} $user name primary key
	 * @return "" on success ; otherwise null
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid User ID"); // User ID must be positive greater than 0 

		// make query
		$obj=$this->__getObject("usuarios",$id);
		if (!is_object($obj))	return $this->error("No user found with provided ID=$id");
		$data= json_decode(json_encode($obj), true); // convert object to array
		$data['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		return $data;
	} 
	
	function select() {
		$this->myLogger->enter();
		//needed to properly handle multisort requests from datagrid
		$sort=getOrderString(
				http_request("sort","s",""),
				http_request("order","s",""),
				"Login ASC"
		);
		// search string
		$search =  isset($_GET['where']) ? strval($_GET['where']) : '';
		// evaluate offset and row count for query
		$page=http_request("page","i",1);
		$rows=http_request("rows","i",50);
		$limit="";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit="".$offset.",".$rows;
		}
		$where = "(Login != 'root') AND (clubes.ID=usuarios.Club)"; // hide root user to app. Add club name
		if ($search!=='') $where=" AND ( (Login LIKE '%$search%') OR ( Gecos LIKE '%$search%' ) OR ( Email LIKE '%$search%') ) ";
		$result=$this->__select(
				/* SELECT */ "usuarios.*,clubes.Nombre AS NombreClub",
				/* FROM */ "usuarios,clubes",
				/* WHERE */ $where,
				/* ORDER BY */ $sort,
				/* LIMIT */ $limit
		);
		$this->myLogger->leave();
		return $result;
	}
	
	function enumerate() { // like select but with fixed order
		$this->myLogger->enter();
		// evaluate search criteria for query
		$q=http_request("q","s",null);
		$where="(clubes.ID=usuarios.Club)";
		if ($q!=="") $where="AND (Login LIKE '%".$q."%')";
		$result=$this->__select(
				/* SELECT */ "usuarios.*,clubes.Nombre as NombreClub",
				/* FROM */ "usuarios,clubes",
				/* WHERE */ $where,
				/* ORDER BY */ "Login ASC",
				/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}
}
?>