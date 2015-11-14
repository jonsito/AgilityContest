<?php
/*
Dogs.php

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


require_once(__DIR__."/DBObject.php");
require_once(__DIR__."/../procesaInscripcion.php"); // to update inscription data

class Dogs extends DBObject {

	function __construct() {
		parent::__construct("Dogs");
	}

    /**
     * Evaluate if a dog has a mixBreed License
     * @param $lic
     */
    static function isMixBreed($lic){
        $lic=strval($lic);
        $lic=trim($lic);
        $lic=strtoupper($lic);
        if (strlen($lic)<4) return false;
        if (substr($lic,0,1)=='0') return false;
        if (substr($lic,0,1)=='A') return false;
        if (substr($lic,0,1)=='B') return false;
        return true;
    }

	/**
	 * Insert a new dog into database
	 * @return {string} "" if ok; null on error
	 */
	function insert($fed) {
		$this->myLogger->enter();
		if($fed<0) return $this->error("Dogs::insert() invalid federation value");
        // iniciamos los valores, chequeando su existencia
        $nombre =	http_request("Nombre","s",null,false);
        $raza =		http_request("Raza","s",null,false);
        $loe_rrc =	http_request("LOE_RRC","s",null,false);
        $licencia = http_request("Licencia","s",null,false);
        $categoria= http_request("Categoria","s",null,false);
        $grado =	http_request("Grado","s",null,false);
        $guia =		http_request("Guia","i",0);
		$nombrelargo= http_request("NombreLargo","s","",false);
		$genero= http_request("Genero","s","",false);
        $federation=$fed;
		// componemos un prepared statement (para evitar sql injection)
		$sql ="INSERT INTO Perros (Nombre,Raza,LOE_RRC,Licencia,Categoria,Grado,Guia,NombreLargo,Genero,Federation)
			   VALUES(?,?,?,?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ssssssissi',$nombre,$raza,$loe_rrc,$licencia,$categoria,$grado,$guia,$nombrelargo,$genero,$federation);
		if (!$res) return $this->error($this->conn->error);
		
		$this->myLogger->info("Nombre:$nombre Raza:$raza LOE:$loe_rrc Categoria:$categoria Grado:$grado Guia:$guia Federation:$federation");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error);
		$stmt->close();
		$this->myLogger->leave();
		return "";
		
	}
	
	function updateInscripciones($id) {
		// miramos las pruebas en las que el perro esta inscrito
		$res=$this->__select(
			/* SELECT */"Inscripciones.*",
			/* FROM */	"Inscripciones,Pruebas",
			/* WHERE */	"(Pruebas.ID=Inscripciones.Prueba) AND (Pruebas.Cerrada=0) AND (Perro=$id)",
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
	
	/**
	 * Update data for provided dog ID
	 * @param {integer} $id dog id primary key
	 * @return "" on success; null on error
	 */
	function update($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Dog ID:$id");
        // iniciamos los valores, chequeando su existencia
        $nombre =	http_request("Nombre","s",null,false);
        $raza =		http_request("Raza","s",null,false);
        $loe_rrc =	http_request("LOE_RRC","s",null,false);
        $licencia = http_request("Licencia","s",null,false);
        $categoria= http_request("Categoria","s",null,false);
        $grado =	http_request("Grado","s",null,false);
        $guia =		http_request("Guia","i",0);
		$nombrelargo= http_request("NombreLargo","s","",false);
		$genero= http_request("Genero","s","",false);
        $idperro =	$id;
		// componemos un prepared statement
		$sql ="UPDATE Perros SET Nombre=? , Raza=? , LOE_RRC=? , Licencia=? , Categoria=? , Grado=? , Guia=?, NombreLargo=?, Genero=?
		       WHERE ( ID=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ssssssissi',$nombre,$raza,$loe_rrc,$licencia,$categoria,$grado,$guia,$nombrelargo,$genero,$idperro);
		if (!$res) return $this->error($this->conn->error);

		$this->myLogger->info("\nUPDATE dogs: ID: $id Nombre: $nombre Raza: $raza Licencia: $licencia LOE: $loe_rrc Categoria: $categoria Grado: $grado Guia: $guia");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error); 
		$stmt->close();
		// update data on inscripciones
		$res=$this->updateInscripciones($id);
		$this->myLogger->leave();
		return $res;
	}
	
	/**
	 * Delete dog with provided idperro
	 * @param {integer} $idperro dog primary key
	 * @return "" on success ; otherwise null
	 */
	function delete($idperro) {
		$this->myLogger->enter();
		if ($idperro<=0) return $this->error("Invalid Dog ID:$idperro"); 
		$rs= $this->query("DELETE FROM Perros WHERE (ID=$idperro)");
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Desasigna el guia al perro indicado
	 * @param {integer} $idperro idperro id
	 * @return "" on success; otherwise null
	 */
	function orphan ($idperro) {
		$this->myLogger->enter();
		if ($idperro<=0) return $this->error("Invalid Dog ID:$idperro"); 
		// assign to default Guia ID=1
		$rs= $this->query("UPDATE Perros SET Guia=1 WHERE (ID=$idperro)");
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Enumerate all dogs that matches requested criteria and order
	 * @return null on error, else requested data
	 */
	 function select() {
		$this->myLogger->enter();
		// evaluate offset and row count for query
		$sort=getOrderString( //needed to properly handle multisort requests from datagrid
			http_request("sort","s",""),
			http_request("order","s",""),
			"Nombre ASC"
		);
		$this->myLogger->debug("Sort order is: $sort");
		$search=http_request("where","s","");
		$page=http_request("page","i",1);
		$rows=http_request("rows","i",50);
		$federation=http_request("Federation","i",-1);
		$fed="1";
		if ($federation >=0) $fed="( Federation = $federation )";
		$where = "1";
		$limit = "";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit="".$offset.",".$rows;
		}
		if ($search!=="") $where="( (Nombre LIKE '%$search%') OR ( NombreGuia LIKE '%$search%') OR ( Licencia LIKE '%$search%') OR ( NombreClub LIKE '%$search%') )";
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "PerroGuiaClub",
				/* WHERE */ "$fed AND $where",
				/* ORDER BY */ $sort,
				/* LIMIT */ $limit
		);
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Like select but not provide ordered search
	 * @return {array} NULL|multitype:multitype: unknown
	 */
	function enumerate() {
		$this->myLogger->enter();
		$federation=http_request("Federation","i",-1);
		$fed="1 ";
		if ($federation >=0) $fed="( Federation = $federation )";
		// evaluate search criteria for query
		$q=http_request("q","s","");
		$where =  ($q==="") ? "1" : " ( ( Nombre LIKE '%$q%' ) OR ( NombreGuia LIKE '%$q%' ) OR ( NombreClub LIKE '%$q%' ) )";
		// retrieve result from parent __select() call
		$result= $this->__select(
				/* SELECT */ "*",
				/* FROM */ "PerroGuiaClub",
				/* WHERE */ "$fed AND $where",
				/* ORDER BY */ "Club ASC, Guia ASC, Nombre ASC",
				/* LIMIT */ ""
		);
		// return composed array
		$this->myLogger->leave();
		return $result;
	}
	
	/** 
	 * enumera todos los perros asociados a un guia
	 * @param {integer} $guia ID del guia
	 */
	function selectByGuia($idguia) {
		$this->myLogger->enter();
		if ($idguia<=0) return $this->error("Invalid Guia ID:$idguia");
		$federation=http_request("Federation","i",-1);
		$fed="";
		if ($federation >=0) $fed="( Federation = $federation ) AND ";
		// retrieve result from parent __select() call
		$result= $this->__select(
				/* SELECT */ "*",
				/* FROM */ "PerroGuiaClub",
				/* WHERE */ "$fed ( Guia = $idguia )",
				/* ORDER BY */ "Nombre ASC",
				/* LIMIT */ ""
		);
		// return composed array
		$this->myLogger->leave();
		return $result;
	}
	
	/** 
	 * Obtiene los datos del perro con el idperro indicado
	 * Usado para rellenar formularios:  formid.form('load',url);
	 * @param {integer} $idperro dog primary key
	 * @return null on error; array() with data on success
	 */
	function selectByID($idperro){
		$this->myLogger->enter();
		if ($idperro<=0) return $this->error("Invalid Perro ID:$idperro");
		// make query
		$obj=$this->__getObject("PerroGuiaClub",$idperro);
		if (!is_object($obj))	return $this->error("No Dog found with ID=$idperro");
		$data= json_decode(json_encode($obj), true); // convert object to array
		$data['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		return $data;
	}
	
	/**
	 * Enumerate categorias ( std, small, medium, tiny 
	 * Notice that this is not a combogrid, just combobox, so dont result count
	 * @param {integer} federation module ID;  -1:any
	 * @return null on error; result on success
	 */
	function categoriasPerro($fed=-1) {
		$this->myLogger->enter();

		// evaluate category search argument
		$f="1";
		if ($fed>=0) {
			// ask how many heights has selected federation
			$fedinfo=Federations::getFederation($fed);
			if ($fedinfo) {
				if ($fedinfo->get('Heights')===3) $f="(Categoria <> 'T') ";
			} else {
				$this->myLogger->error("CategoriasPerro: invalid federation ID:$fed");
			}
		}

		// evaluate offset and row count for query
		$q=http_request("q","s","");
		$like =  ($q==="") ? "WHERE $f" : " WHERE $f AND Categoria LIKE '%".$q."%'";
	
		// query to retrieve table data
		$sql="SELECT Categoria,Observaciones FROM Categorias_Perro ".$like." ORDER BY Categoria";
		$this->myLogger->query($sql);
		$rs=$this->query($sql);
		if (!$rs) return $this->error($this->conn->error); 
		// retrieve result into an array
		$result = array();
		while($row = $rs->fetch_array(MYSQLI_ASSOC)){
			// add a default state for comobobox
			if ($row["Categoria"]==='-') 
				{ $row["selected"]=1; $row[2]=1;}
			else { $row["selected"]=0; $row[2]=0;}
			// and store into result array
			array_push($result, $row);
		}
		// clean and return
		$rs->free();
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Enumerate grados 
	 * @param {integer} federation ID; -1:any
	 * @return null on error; result on success
	 * Notice that this is not a combogrid, just combobox, so dont result count
	 */
	function gradosPerro($fed=-1) {
		$this->myLogger->enter();

		// evaluate federation search argument
		$f="1";
		if ($fed>=0) {
			// ask how many grades has selected federation
			$fedinfo=Federations::getFederation($fed);
			if ($fedinfo) {
				if ($fedinfo->get('Grades')===2) $f="(Grado <> 'GIII') ";
			} else {
				$this->myLogger->error("CategoriasPerro: invalid federation ID:$fed");
			}
		}
		// evaluate offset and row count for query
		$q=http_request("q","s","");
		$like =  ($q==="") ? "WHERE $f" : " WHERE $f AND ( Grado LIKE '%".$q."%' )";

		// query to retrieve table data
		$sql="SELECT Grado,Comentarios FROM Grados_Perro ".$like." ORDER BY Grado";
		$this->myLogger->query($sql);
		$rs=$this->query($sql);
		if (!$rs) return $this->error($this->conn->error);
		// retrieve result into an array
		$result = array();
		while($row = $rs->fetch_array(MYSQLI_ASSOC)){
			// add a default state for comobobox
			if ($row["Grado"]==='-') 
				{ $row["selected"]=1; $row[2]=1;}
			else { $row["selected"]=0; $row[2]=0;}
			// and store into result array
			array_push($result, $row);
		}
		// clean and return
		$rs->free();
		$this->myLogger->leave();
		return $result;
	}
}
	
?>