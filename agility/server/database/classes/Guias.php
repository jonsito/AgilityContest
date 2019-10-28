<?php
/*
Guias.php

Copyright  2013-2019 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
        $categoria= parseHandlerCat(http_request('Categoria',"s","A",false)); // default adult

		// componemos un prepared statement
		$sql ="INSERT INTO guias (Nombre,Telefono,Email,Club,Observaciones,Categoria,Federation)
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
        $this->setServerID("guias",$stmt->insert_id);
		$stmt->close();
		$this->myLogger->leave();
		return "";
	}

	function updateInscripciones($id) {
		// miramos las pruebas en las que el perro esta inscrito
		$res=$this->__select(
		/* SELECT */"inscripciones.*",
			/* FROM */	"inscripciones,pruebas,perroguiaclub",
			/* WHERE */	"(pruebas.ID=inscripciones.Prueba) AND (pruebas.Cerrada=0) AND (inscripciones.Perro=perroguiaclub.ID) AND (Guia=$id)",
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
        $categoria= parseHandlerCat(http_request('Categoria',"s","A",false)); // adult
        $guiaid 	= $id; // primary key
        $this->myLogger->info("ID: $id Nombre: $nombre Telefono: $telefono Email: $email Club: $club Observaciones: $observaciones");

		// componemos un prepared statement
		$sql ="UPDATE guias SET Nombre=? , Telefono=? , Email=? , Club=? , Observaciones=?, Categoria=? WHERE ( ID=? )";
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
		$res= $this->query("UPDATE perros SET Guia=1 WHERE ( Guia=$id )");
		if (!$res) return $this->error($this->conn->error); 
		// fase 2: borramos el guia de la base de datos
		$res= $this->__delete("guias","(ID={$id})");
		if (!$res) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}

    /**
     * Modify Database replaceing every instances of $fromID with $toID
     * That is: handler "from" becomes handler "to"
     * This code does not set up resulting handler properties, just move ID's
     * @param $fromIDs handler list of items to be replaced in form BEGIN,dog[,dog[...]],END
     * @param $toID handler to replace with
     */
    function joinTo($fromIDs,$toID) {
        $this->myLogger->enter();
        if ($toID<=0) return $this->error("joinTo() invalid to:$toID value");
        // simpler than join dogs, as handler id is only stored in dog table
        // results have hardcoded handler name and should not be changed
        $ids=getInnerString($fromIDs,"BEGIN,",",END");
        $this->query("START TRANSACTION");
        // phase 1: reassign handlers
        $res=$this->query("UPDATE perros SET Guia=$toID WHERE Guia IN ({$ids}) ");
        if (!$res) {
            $err=$this->conn->error;
            $this->query("ROLLBACK");
            return $this->error("Error (update perros) in Join handler: {$ids}: <br/>{$err}");
        }
        // phase 2: remove assigned handlers
        $ids=str_replace(",{$toID}","",$fromIDs);
        $ids=getInnerString($ids,"BEGIN,",",END");
        $res=$this->query("DELETE FROM guias WHERE ID IN ({$ids}) ");
        if (!$res) {
            $err=$this->conn->error;
            $this->query("ROLLBACK");
            return $this->error("Error (delete from guias) in Join handler: {$ids}: <br/>{$err}");
        }
        $this->query("COMMIT");
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
		$res= $this->query("UPDATE guias SET Club=1 WHERE ( ID=$id )");
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
		$where = "(guias.Club=clubes.ID)";
		if ($search!=='') $where="(guias.Club=clubes.ID) AND ( (guias.Nombre LIKE '%$search%') OR ( clubes.Nombre LIKE '%$search%') ) ";
		$result=$this->__select(
				/* SELECT */ "guias.ID, guias.Federation, guias.Nombre, Telefono, Categoria, guias.Email, Club, clubes.Nombre AS NombreClub, guias.Observaciones",
				/* FROM */ "guias,clubes",
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
		$where="(guias.Club=clubes.ID)";
		if ($q!=="") $where="(guias.Club=clubes.ID) AND ( ( guias.Nombre LIKE '%$q%' ) OR ( clubes.Nombre LIKE '%$q%' ) )";
		$result=$this->__select(
				/* SELECT */ "guias.*,clubes.Nombre AS NombreClub",
				/* FROM */ "guias,clubes",
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
				/* FROM */ "guias",
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
		$obj=$this->__getObject("guias",$id);
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