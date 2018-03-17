<?php
/*
Guias.php

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
require_once(__DIR__."/../procesaInscripcion.php");// to update inscription data

class Guias extends DBObject {
    protected $federation;

	function __construct($name="Guias",$fed=-1) {
		parent::__construct($name);
		$this->federation=intval($fed);
	}

	function insert() {
		$this->myLogger->enter();
		// iniciamos los valores, chequeando su existencia
		// don't escape http data cause we're using prepared statements
		$nombre 	= http_request("Nombre","s",null,false); // primary key
		$telefono = http_request('Telefono',"s",null,false);
		$email = http_request('Email',"s",null,false);
		$club	= http_request('Club',"i",0); // not null
        $observaciones= http_request('Observaciones',"s",null,false);
        $categoria= http_request('Categoria',"s","A",false); // default adult

		// componemos un prepared statement
		$sql ="INSERT INTO Guias (Nombre,Telefono,Email,Club,Observaciones,Categoria,Federation)
			   VALUES(?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('sssissi',$nombre,$telefono,$email,$club,$observaciones,$categoria,$this->federation);
		if (!$res) return $this->error($stmt->error);  

		$this->myLogger->info("Nombre: $nombre Telefono: $telefono Email: $email Club: $club Observaciones: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error);
        // if running on master server set ServerID as insert_id
        $this->setServerID("Guias",$stmt->insert_id);
		$stmt->close();
		$this->myLogger->leave();
		return "";
	}

	function updateInscripciones($id) {
		// miramos las pruebas en las que el perro esta inscrito
		$res=$this->__select(
		/* SELECT */"Inscripciones.*",
			/* FROM */	"Inscripciones,Pruebas,PerroGuiaClub",
			/* WHERE */	"(Pruebas.ID=Inscripciones.Prueba) AND (Pruebas.Cerrada=0) AND (Inscripciones.Perro=PerroGuiaClub.ID) AND (Guia=$id)",
			/* ORDER BY */	"",
			/* LIMIT*/	""
		);
		if (!is_array($res)) return $this->conn->error;
		// actualizamos los datos de inscripcion de la prueba
		foreach($res['rows'] as $inscripcion) {
			procesaInscripcion($inscripcion['Prueba'],$inscripcion['ID']);
		}
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
        $categoria= http_request('Categoria',"s","A",false); // adult
        $guiaid 	= $id; // primary key
        $this->myLogger->info("ID: $id Nombre: $nombre Telefono: $telefono Email: $email Club: $club Observaciones: $observaciones");

		// componemos un prepared statement
		$sql ="UPDATE Guias SET Nombre=? , Telefono=? , Email=? , Club=? , Observaciones=?, Categoria=? WHERE ( ID=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('sssissi',$nombre,$telefono,$email,$club,$observaciones,$categoria,$guiaid);
		if (!$res) return $this->error($stmt->error); 

		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error); 
		$stmt->close();
		// update data on inscripciones
		$res=$this->updateInscripciones($id);
		$this->myLogger->leave();
		return $res;
	}
	
	function delete($id) {
		$this->myLogger->enter();
		if ($id<=1) return $this->error("Invalid Guia ID:$id provided"); // cannot delete ID=1
		// fase 1: desasignamos los perros de este guia (los asignamos al guia id=1)
		$res= $this->query("UPDATE Perros SET GUIA=1 WHERE ( Guia=$id )");
		if (!$res) return $this->error($this->conn->error); 
		// fase 2: borramos el guia de la base de datos
		$res= $this->__delete("Guias","(ID={$id})");
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
		$fed="1";
		if ($this->federation >=0) $fed="( Federation = {$this->federation} )";
		$where = "(Guias.Club=Clubes.ID)";
		if ($search!=='') $where="(Guias.Club=Clubes.ID) AND ( (Guias.Nombre LIKE '%$search%') OR ( Clubes.Nombre LIKE '%$search%') ) ";
		$result=$this->__select(
				/* SELECT */ "Guias.ID, Guias.Federation, Guias.Nombre, Telefono, Categoria, Guias.Email, Club, Clubes.Nombre AS NombreClub, Guias.Observaciones",
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
		$fed="1";
		if ($this->federation >=0) $fed="( Federation = {$this->federation} )";
		$where="(Guias.Club=Clubes.ID)";
		if ($q!=="") $where="(Guias.Club=Clubes.ID) AND ( ( Guias.Nombre LIKE '%$q%' ) OR ( Clubes.Nombre LIKE '%$q%' ) )";
		$result=$this->__select(
				/* SELECT */ "Guias.*,Clubes.Nombre AS NombreClub",
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
		$fed="1";
		if ($this->federation >=0) $fed="( Federation = {$this->federation} )";
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

    /**
     * Enumerate categorias ( children, junior,adults, senior, veterans, para-agility )
     * Notice that this is not a combogrid, just combobox, so don't result count
     * @return null on error; result on success
     */
    function categoriasGuia() {
        $this->myLogger->enter();

        // evaluate category search argument
        $fedinfo=new Federations();
        if ($this->federation>=0) {
            $f=Federations::getFederation(intval($this->federation));
            if ($f) $fedinfo=$f;
            else $this->myLogger->error("CategoriasGuia: invalid federation ID:{$this->federation}");
        }
        $result =array();
        foreach ($fedinfo->get('ListaCatGuias') as $cat => $name) {
            if ($cat==="-")
                array_push($result,array("Categoria"=>$cat,"Observaciones"=>$name,"selected"=>1));
            else array_push($result,array("Categoria"=>$cat,"Observaciones"=>$name,"selected"=>0));
        }
        $this->myLogger->leave();
        return $result;
    }
}
	
?>