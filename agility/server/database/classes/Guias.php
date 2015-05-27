<?php
/*
Guias.php

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

class Guias extends DBObject {
	
	function insert() {
		$this->myLogger->enter();
		
		// componemos un prepared statement
		$sql ="INSERT INTO Guias (Nombre,Telefono,Email,Club,Observaciones,Federation)
			   VALUES(?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('sssisi',$nombre,$telefono,$email,$club,$observaciones,$federation);
		if (!$res) return $this->error($stmt->error);  
		
		// iniciamos los valores, chequeando su existencia
        // don't escape http data cause we're using prepared statements
		$nombre 	= http_request("Nombre","s",null,false); // primary key
		$telefono = http_request('Telefono',"s",null,false);
		$email = http_request('Email',"s",null,false);
		$club	= http_request('Club',"i",0); // not null
		$observaciones= http_request('Observaciones',"s",null,false);
		$federation= http_request('Federation',"i",0);
		$this->myLogger->info("Nombre: $nombre Telefono: $telefono Email: $email Club: $club Observaciones: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error); 
		$stmt->close();
		$this->myLogger->leave();
		return "";
	}
	
	function update($id) {
		$this->myLogger->enter();
		if ($id<=1) return $this->error("No guia or Invalid Guia ID:$id provided");

        // iniciamos los valores, chequeando su existencia
        $nombre 	= http_request("Nombre","s",null,false);
        $telefono = http_request('Telefono',"s",null,false);
        $email = http_request('Email',"s",null,false);
        $club	= http_request('Club',"i",0); // not null
        $observaciones= http_request('Observaciones',"s",null,false);
        $guiaid 	= $id; // primary key
        $this->myLogger->info("ID: $id Nombre: $nombre Telefono: $telefono Email: $email Club: $club Observaciones: $observaciones");

		// componemos un prepared statement
		$sql ="UPDATE Guias SET Nombre=? , Telefono=? , Email=? , Club=? , Observaciones=? WHERE ( ID=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('sssisi',$nombre,$telefono,$email,$club,$observaciones,$guiaid);
		if (!$res) return $this->error($stmt->error); 

		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error); 
		$stmt->close();
		$this->myLogger->leave();
		// TODO: study an easy way to propagate NombreGuia and NombreClub changes to Resultados table
		return "";
	}
	
	function delete($id) {
		$this->myLogger->enter();
		if ($id<=1) return $this->error("Invalid Guia ID:$id provided"); // cannot delete ID=1
		// fase 1: desasignamos los perros de este guia (los asignamos al guia id=1)
		$res= $this->query("UPDATE Perros SET GUIA=1 WHERE ( Guia=$id )");
		if (!$res) return $this->error($this->conn->error); 
		// fase 2: borramos el guia de la base de datos
		$res= $this->query("DELETE FROM Guias WHERE (ID=$id)");
		if (!$res) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * remove a handler from provided club
	 * @param {integer} $id Guia ID primary key
	 * @return "" on success ; null on error
	 */
	function orphan($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Guia ID"); 
		$res= $this->query("UPDATE Guias SET Club=1 WHERE ( ID=$id )");
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	function select() {
		$this->myLogger->enter();
		$sort=getOrderString( //needed to properly handle multisort requests from datagrid
			http_request("sort","s",""),
			http_request("order","s",""),
			"Nombre ASC"
		);
		$search=http_Request("where","s","");
		$page=http_request("page","i",1);
		$rows=http_request("rows","i",50);
		$limit="";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit="".$offset.",".$rows;
		}
		$federation=http_request("Federation","i",-1);
		$fed="1";
		if ($federation >=0) $fed="( Federation = $federation )";
		$where = "(Guias.Club=Clubes.ID)";
		if ($search!=='') $where="(Guias.Club=Clubes.ID) AND ( (Guias.Nombre LIKE '%$search%') OR ( Clubes.Nombre LIKE '%$search%') ) ";
		$result=$this->__select(
				/* SELECT */ "Guias.ID, Guias.Federation, Guias.Nombre, Telefono, Guias.Email, Club, Clubes.Nombre AS NombreClub, Guias.Observaciones",
				/* FROM */ "Guias,Clubes",
				/* WHERE */ "$fed AND $where",
				/* ORDER BY */ $sort,
				/* LIMIT */ $limit
		);
		$this->myLogger->leave();
		return $result;
	}
	
	function enumerate() { // like select but do not provide order query. Used in comboboxes
		$this->myLogger->enter();
		// evaluate search string
		$q=http_request("q","s","");
		$federation=http_request("Federation","i",-1);
		$fed="1";
		if ($federation >=0) $fed="( Federation = $federation )";
		$where="(Guias.Club=Clubes.ID)";
		if ($q!=="") $where="(Guias.Club=Clubes.ID) AND ( ( Guias.Nombre LIKE '%$q%' ) OR ( Clubes.Nombre LIKE '%$q%' ) )";
		$result=$this->__select(
				/* SELECT */ "Guias.ID AS ID, Guias.Federation AS Federation, Guias.Nombre AS Nombre, Guias.Club AS Club,Clubes.Nombre AS NombreClub",
				/* FROM */ "Guias,Clubes",
				/* WHERE */ "$fed AND $where",
				/* ORDER BY */ "Nombre ASC, NombreClub ASC",
				/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}
	
	/** 
	 * Enumerate by club (exact match)
	 * @param {integer} $club Club ID primary key
	 * @return {array} result on success; null on error
	 */
	function selectByClub($club) {
		$this->myLogger->enter();
		$federation=http_request("Federation","i",-1);
		$fed="1";
		if ($federation >=0) $fed="( Federation = $federation )";
		if ($club<=0) return $this->error("Invalid Club ID provided");
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "Guias",
				/* WHERE */ "$fed AND ( Club=$club )",
				/* ORDER BY */ "Nombre ASC",
				/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Select a (single) entry that matches with provided handler ID
	 * @param {integer} $id Handler ID primary key
	 * @return {array} result on success; null on error
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Provided Handler ID");
		$obj=$this->__getObject("Guias",$id);
		if (!is_object($obj))	return $this->error("No handler found with ID=$id");
		$data= json_decode(json_encode($obj), true); // convert object to array
		$data['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		return $data;
	}
}
	
?>