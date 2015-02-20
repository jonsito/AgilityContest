<?php
/*
Equipos.php

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

class Equipos extends DBObject {

	protected $pruebaID;
	protected $jornadaID;
	protected $defaultTeam; //  {array} datos del equipo por defecto para esta prueba
	
	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {integer} $prueba Prueba ID
	 * @throws Exception if cannot contact database or invalid prueba/jornada ID
	 */
	function __construct($file,$prueba,$jornada) {
		parent::__construct($file);
		if ( $prueba<=0 ) {
			$this->errormsg="$file::construct() invalid prueba:$prueba ID";
			throw new Exception($this->errormsg);
		}
		$this->pruebaID=$prueba;
		$this->jornadaID=$jornada;
		if ( $jornada<=0 ) {
			$this->myLogger->info("Constructor with invalid jornada ID:0");
			$this->defaultTeam=0;
			return;
		}
		// obtenemos el equipo por defecto para esta prueba
		$res= $this->__selectAsArray(
				/* SELECT */ "ID",
				/* FROM */   "Equipos",
				/* WHERE */ "( Prueba = $prueba ) AND ( Jornada = $jornada ) AND ( Nombre = '-- Sin asignar --' )"
		);
		if (!is_array($res)) {
			$this->errormsg="$file::construct() cannot get default team data for prueba:$prueba jornada:$jornada" ;
			throw new Exception($this->errormsg);
		}
		$this->defaultTeam=$res;
	}
	
	function insert() {
		$this->myLogger->enter();
		
		// componemos un prepared statement
		$sql ="INSERT INTO Equipos (Prueba,Jornada,Categorias,Nombre,Observaciones) VALUES(?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('iisss',$prueba,$jornada,$categorias,$nombre,$observaciones);
		if (!$res) return $this->error($stmt->error);  
		
		// iniciamos los valores, chequeando su existencia
		$prueba		= $this->pruebaID; // not null
		$jornada	= $this->jornadaID; // not null
		$categorias = http_request("Categorias","s",null,false); // may be null
		$nombre 	= http_request("Nombre","s",null,false); // not null
		$observaciones= http_request('Observaciones',"s",null,false); // may be null
		$this->myLogger->info("Prueba ID:$prueba Nombre:'$nombre' Observaciones:'$observaciones'");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error); 
		$stmt->close();
		$this->myLogger->leave();
		return "";
	}
	
	function update($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Equipo ID provided");
		// componemos un prepared statement. Do not mofify any field that not matches current pruebaID
		$sql ="UPDATE Equipos SET Nombre=? , Observaciones=?, Categorias=? WHERE ( ID=$id )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('sss',$n,$o,$c);
		if (!$res) return $this->error($stmt->error); 
		
		// iniciamos los valores, chequeando su existencia
		$n = http_request("Nombre","s",null,false); 
		$o = http_request('Observaciones',"s",null,false);
		$c = http_request('Categorias',"s",null,false);
		
		$this->myLogger->info("Team:$id Prueba:{$this->pruebaID} Jornada:{$this->jornadaID} Nombre:'$n' Observ:'$o' Categ:'$c'");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error); 
		$stmt->close();
		$this->myLogger->leave();
		return "";
	}
	
	function delete($id) {
		$this->myLogger->enter();
		$def=$this->defaultTeam['ID'];
		if ($id<1) return $this->error("Invalid Equipo ID:$id provided"); // cannot delete ID=1
		if ($id==$def) return $this->error("Cannot delete default team for this Contest");

		// fase 1: desasignamos los perros de este equipo (los asignamos al equipo por defecto de la jornada)
		// TODO: write
		
		// fase 2: borramos el equipo de la base de datos
		$res= $this->query("DELETE FROM Equipos WHERE (ID=$id)");
		if (!$res) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}
	
	function select() {
		$this->myLogger->enter();
		//needed to properly handle multisort requests from datagrid
		$sort=getOrderString( 
			http_request("sort","s",""),
			http_request("order","s",""),
			"Nombre ASC"
		);
		// evaluate if any search criteria
		$search=http_Request("where","s","");
		// evaluate offset and row count for query
		$page=http_request("page","i",1);
		$rows=http_request("rows","i",50);
		$limit="";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit="".$offset.",".$rows;
		}
		$where = "(Equipos.Prueba={$this->pruebaID}) AND (Equipos.Jornada={$this->jornadaID})";
		if ($search!=='') $where=$where." AND ( (Equipos.Nombre LIKE '%$search%') OR ( Equipos.Observaciones LIKE '%$search%') ) ";
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "Equipos",
				/* WHERE */ $where,
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
		$where = "(Equipos.Prueba={$this->pruebaID}) AND (Equipos.Jornada={$this->jornadaID})";
		if ($q!=="") $where=$where." AND ( ( Equipos.Nombre LIKE '%$q%' ) OR ( Equipos.Observaciones LIKE '%$q%' ) )";
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "Equipos",
				/* WHERE */ $where,
				/* ORDER BY */ "Nombre ASC",
				/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Select a (single) entry that matches with provided Equipo ID
	 * @param {integer} $id Equipo ID (primary key)
	 * @return {array} result on success; null on error
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Provided Equipo ID");
		$obj=$this->__getObject("Equipos",$id);
		if (!is_object($data))	return $this->error("No Equipo found with ID=$id");
		$data= json_decode(json_encode($obj), true); // convert object to array
		$data['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		return $data;
	}
}
	
?>