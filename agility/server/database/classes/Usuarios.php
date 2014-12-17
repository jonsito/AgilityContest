<?php
/*
Usuarios.php

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



require_once("DBObject.php");

class Usuarios extends DBObject {
	
	/**
	 * Insert a new user into database
	 * @return {string} "" if ok; null on error
	 */
	function insert() {
		$this->myLogger->enter();
		// componemos un prepared statement
		$sql ="INSERT INTO Usuarios (Login,Password,Gecos,Phone,Email,Perms) VALUES(?,'--UNDEF--',?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('ssssi',$login,$gecos,$phone,$email,$perms);
		if (!$res) return $this->error($this->conn->error);
		
		// iniciamos los valores, chequeando su existencia
		$login =	http_request("Login","s",null,false); // pkey not null
		$gecos =	http_request("Gecos","s",null,false);
		$phone =	http_request("Phone","s",null,false);
		$email = 	http_request("Email","s",null,false);
		$perms= 	http_request("Perms","i",0); 
		$this->myLogger->debug("Login: '$login' Gecos: '$gecos' Phone: '$phone' Email: '$email' Perms: $perms");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error);
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
		// componemos un prepared statement
		$sql ="UPDATE Usuarios SET Login=? , Gecos=? , Phone=? , Email=? , Perms=? WHERE ( ID=$id )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ssssi',$login,$gecos,$phone,$email,$perms);
		if (!$res) return $this->error($this->conn->error);
		
		// iniciamos los valores, chequeando su existencia
		$login =	http_request("Login","s",null,false); // pkey not null
		$gecos =	http_request("Gecos","s",null,false);
		$phone =	http_request("Phone","s",null,false);
		$email = 	http_request("Email","s",null,false);
		$perms= 	http_request("Perms","i",0);
		$this->myLogger->debug("Login: '$login' Gecos: '$gecos' Phone: '$phone' Email: '$email' Perms: $perms");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error);
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
		$str="DELETE FROM Usuarios WHERE ( ID=$id )";
		$res= $this->query($str);
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}	
	
	/**
	 * Select juez with provided ID
	 * @param {string} $juez name primary key
	 * @return "" on success ; otherwise null
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid User ID"); // Juez ID must be positive greater than 0 

		// make query
		$obj=$this->__getObject("Usuarios",$id);
		if (!is_object($obj))	return $this->error("No Juez found with provided ID=$id");
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
		$where = "(Login != 'root') "; // hide root user to app
		if ($search!=='') $where=" AND ( (Login LIKE '%$search%') OR ( Gecos LIKE '%$search%' ) OR ( Email LIKE '%$search%') ) ";
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "Usuarios",
				/* WHERE */ $where,
				/* ORDER BY */ $sort,
				/* LIMIT */ $limit
		);
		$this->myLogger->leave();
		return $result;
	}
	
	function setPassword() {
			// TODO: write
	}
	
	function enumerate() { // like select but with fixed order
		$this->myLogger->enter();
		// evaluate search criteria for query
		$q=http_request("q","s",null);
		$where="";
		if ($q!=="") $where="Login LIKE '%".$q."%'";
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "Usuarios",
				/* WHERE */ $where,
				/* ORDER BY */ "Login ASC",
				/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}
}
?>