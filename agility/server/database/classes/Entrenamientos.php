<?php
/*
Entrenamientos.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

class Entrenamientos extends DBObject {

    protected $pruebaID;
    protected $prueba;

	function __construct($name,$prueba) {
		parent::__construct($name);
        if ($prueba<=0) throw new Exception('$name: Invalid pruebaID:$prueba');
        $this->prueba=$this->__getObject("Pruebas",$prueba);
        if (!$this->prueba) throw new Exception('$name: Prueba with ID:$prueba not found in database');
        $this->pruebaID=$prueba;
	}

    /**
     * remove all trainning entries for provided contest id
     * @return {string} "" if ok; null on error
     */
    function clear() {
        $this->myLogger->enter();
        $this->myLogger->leave();
        return "";
    }

    /**
     * Fill trainning sesion with default data from database and configuration
     * @return {string} "" if ok; null on error
     */
    function populate() {
        $this->myLogger->enter();
        $this->myLogger->leave();
        return "";
    }

	/**
	 * Insert a new user into database
	 * @return {string} "" if ok; null on error
	 */
	function insert() {
		$this->myLogger->enter();
		$this->myLogger->leave();
		return ""; 
	}

	/**
	 * Update trainning entry data
	 * @param {integer} $id entry ID primary key
	 * @return {string} "" on success; null on error
	 */
	function update($id) {
		$this->myLogger->enter();
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Delete training entry with provided ID
	 * @param {integer} $id ID primary key
	 * @return "" on success ; otherwise null
	 */
	function delete($id) {
		$this->myLogger->enter();
		return "";
	}	
	
	/**
	 * Select user with provided ID
	 * @param {string} $user name primary key
	 * @return "" on success ; otherwise null
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Entry ID"); // Trainning entry ID must be positive greater than 0

		// make query
		$obj=$this->__getObject("Entrenamientos",$id);
		if (!is_object($obj))	return $this->error("No Training session found with provided ID=$id");
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
				"Orden ASC"
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
		$where = "(Entrenamientos.Club = Clubes.ID) ";
		if ($search!=='') $where= $where . " AND ( (Clubes.Nombre LIKE '%$search%') OR ( Clubes.Pais LIKE '%$search%' ) ) ";
		$result=$this->__select(
				/* SELECT */ "*, Clubes.Nombre as NombreClub, Clubes.Logo as LogoClub",
				/* FROM */ "Entrenamientos,Clubes",
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
		$where="(Entrenamientos.Club = Clubes.ID) ";
        if ($q!=="") $where=$where . " AND ( (Clubes.Nombre LIKE '%$q%') OR ( Clubes.Pais LIKE '%$q%' ) ) ";
		$result=$this->__select(
				/* SELECT */ "*, Clubes.Nombre as NombreClub, Clubes.Logo as LogoClub",
				/* FROM */ "Entrenamientos,Clubes",
				/* WHERE */ $where,
				/* ORDER BY */ "Orden ASC",
				/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}

	function dragAndDrop() {
        $f=http_request("From","i",-1);
        $t=http_request("To","i",-1);
        $w=http_request("Where","i",0);
        if (($f<0)|| ($t<0)) return $this->error("Invalid parameters From:$f or To:$t received");
        return "";
    }
}
?>