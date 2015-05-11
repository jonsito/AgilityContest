<?php
/*
Jueces.php

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

class Jueces extends DBObject {
	
	/**
	 * Insert a new juez into database
	 * @return {string} "" if ok; null on error
	 */
	function insert() {
		$this->myLogger->enter();
		// componemos un prepared statement
		$sql ="INSERT INTO Jueces (Nombre,Direccion1,Direccion2,Telefono,Internacional,Practicas,Email,Observaciones,Federations)
			   VALUES(?,?,?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('ssssiissi',$nombre,$direccion1,$direccion2,$telefono,$internacional,$practicas,$email,$observaciones,$feds);
		if (!$res) return $this->error($this->conn->error);
		
		// iniciamos los valores, chequeando su existencia
		$nombre =		http_request("Nombre","s",null,false); // pkey not null
		$direccion1 =	http_request("Direccion1","s",null,false);
		$direccion2 =	http_request("Direccion2","s",null,false);
		$telefono = 	http_request("Telefono","s",null,false);
		$internacional= http_request("Internacional","i",0); // not null
		$practicas =	http_request("Practicas","i",0);
		$email =		http_request("Email","s",null,false); // not null
        $observaciones=	http_request("Observaciones","s",null,false);
        $feds=	        http_request("Federations","i",1);
		
		$this->myLogger->debug("Nombre: $nombre Dir1: $direccion1 Dir2: $direccion2 Tel: $telefono I: $internacional P: $practicas Email: $email Obs: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error);
		$stmt->close();
		$this->myLogger->leave();
		return ""; 
	}
	
	/**
	 * Update juez data
	 * @param {integer} $id Juez ID primary key
	 * @return {string} "" on success; null on error
	 */
	function update($id) {
		$this->myLogger->enter();
		if ($id<=1) return $this->error("No Juez or Invalid Juez ID: $id provided");
		// componemos un prepared statement
		$sql ="UPDATE Jueces SET Nombre=? , Direccion1=? , Direccion2=? , Telefono=? , Internacional=? , Practicas=? , Email=? , Observaciones=?, Federations=?
		       WHERE ( ID=$id )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ssssiissi',$nombre,$direccion1,$direccion2,$telefono,$internacional,$practicas,$email,$observaciones,$federations);
		if (!$res) return $this->error($this->conn->error);
		
		// iniciamos los valores, chequeando su existencia
		$nombre =		http_request("Nombre","s",null); // pkey not null
		$direccion1 =	http_request("Direccion1","s",null);
		$direccion2 =	http_request("Direccion2","s",null);
		$telefono = 	http_request("Telefono","s",null);
		$internacional= http_request("Internacional","i",0); // not null
		$practicas =	http_request("Practicas","i",0);
		$email =		http_request("Email","s",null); // not null
		$observaciones=	http_request("Observaciones","s",null);
        $federations =	http_request("Federations","i",1);
		$this->myLogger->debug("ID: $id Nombre: $nombre Dir1: $direccion1 Dir2: $direccion2 Tel: $telefono I: $internacional P: $practicas Email: $email Obs: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error);
		$stmt->close();
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Delete juez with provided name
	 * @param {integer} $id ID primary key
	 * @return "" on success ; otherwise null
	 */
	function delete($id) {
		$this->myLogger->enter();
		if ($id<=1) return $this->error("Invalid Juez ID"); // cannot delete if juez<=default 
		$str="DELETE FROM Jueces WHERE ( ID=$id )";
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
		if ($id<=0) return $this->error("Invalid Juez ID"); // Juez ID must be positive greater than 0 

		// make query
		$obj=$this->__getObject("Jueces",$id);
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
				"Nombre ASC"
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
		$where = "";
		if ($search!=='') $where="( (Nombre LIKE '%$search%') OR ( Email LIKE '%$search%') ) ";
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "Jueces",
				/* WHERE */ $where,
				/* ORDER BY */ $sort,
				/* LIMIT */ $limit
		);
		$this->myLogger->leave();
		return $result;
	}
	
	function enumerate($federation) { // like select but with fixed order
		$this->myLogger->enter();
		// evaluate search criteria for query
		$q=http_request("q","s",null);
		$where="1";
		$fed="1";
		if ( $federation>=0) {
			$mask=1<<$federation;
			$fed=" ( (Federations & $mask ) != 0 )";
		};
		if ($q!=="") $where="( Nombre LIKE '%".$q."%' )";
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "Jueces",
				/* WHERE */ "$where AND $fed",
				/* ORDER BY */ "Nombre ASC",
				/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}
}
?>