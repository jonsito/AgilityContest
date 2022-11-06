<?php
/*
Dogs.php

Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
    protected $federation;
	function __construct($name="Dogs",$fed=-1) {
		parent::__construct($name);
		$this->federation=intval($fed);
	}

	/**
	 * Insert a new dog into database
	 * @return {string} "" if ok; null on error
	 */
	function insert() {
		$this->myLogger->enter();
		if($this->federation<0) return $this->error("Dogs::insert() invalid federation value");
        // iniciamos los valores, chequeando su existencia
        // no hace falta escapar http_request porque usamos prepared statement para el insert
        $nombre =	http_request("Nombre","s",null,false);
        $raza =		http_request("Raza","s",null,false);
        $chip =	http_request("Chip","s","",false);
        $loe_rrc =	http_request("LOE_RRC","s",null,false);
        $licencia = http_request("Licencia","s",null,false);
        $categoria= parseCategory(http_request("Categoria","s",null,false),$this->federation);
        $grado =	parseGrade(http_request("Grado","s",null,false));
        $baja =	    http_request("Baja","i",0);
        $guia =		http_request("Guia","i",0);
		$nombrelargo= http_request("NombreLargo","s","",false);
		$genero= http_request("Genero","s","",false);
        $federation=$this->federation;
        $licencia=normalize_license($licencia);
		// componemos un prepared statement (para evitar sql injection)
		$sql ="INSERT INTO perros (Nombre,Raza,Chip,LOE_RRC,Licencia,Categoria,Grado,Baja,Guia,NombreLargo,Genero,Federation)
			   VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('sssssssiissi',$nombre,$raza,$chip,$loe_rrc,$licencia,$categoria,$grado,$baja,$guia,$nombrelargo,$genero,$federation);
		if (!$res) return $this->error($stmt->error);
		
		$this->myLogger->info("Nombre:$nombre Raza:$raza LOE:$loe_rrc Categoria:$categoria Grado:$grado Guia:$guia Federation:$federation");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error);
        // if running on master server set ServerID as insert_id
        if ($licencia!=="") $this->setServerID("perros",$stmt->insert_id);
		$stmt->close();
		$this->myLogger->leave();
		return "";
		
	}
	
	function updateInscripciones($id) {
		// miramos las pruebas en las que el perro esta inscrito
		$res=$this->__select(
			/* SELECT */"inscripciones.*",
			/* FROM */	"inscripciones,pruebas",
			/* WHERE */	"(pruebas.ID=inscripciones.Prueba) AND (pruebas.Cerrada=0) AND (Perro=$id)",
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
        $chip =	    http_request("Chip","s","",false);
        $loe_rrc =	http_request("LOE_RRC","s",null,false);
        $licencia = http_request("Licencia","s",null,false);
        $categoria= parseCategory(http_request("Categoria","s",null,false),$this->federation);
        $grado =	parseGrade(http_request("Grado","s",null,false));
        $baja =		http_request("Baja","i",0);
        $guia =		http_request("Guia","i",0);
		$nombrelargo= http_request("NombreLargo","s","",false);
		$genero= http_request("Genero","s","",false);
        $idperro =	$id;
        $licencia=normalize_license($licencia);
		// componemos un prepared statement
		$sql ="UPDATE perros SET Nombre=? , Raza=? , Chip=?, LOE_RRC=? , Licencia=? , Categoria=? , Grado=? , Baja=?, Guia=?, NombreLargo=?, Genero=?
		       WHERE ( ID=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('sssssssiissi',$nombre,$raza,$chip,$loe_rrc,$licencia,$categoria,$grado,$baja,$guia,$nombrelargo,$genero,$idperro);
		if (!$res) return $this->error($stmt->error);

		$this->myLogger->info("\nUPDATE dogs: ID: $id Nombre: $nombre Raza: $raza Licencia: $licencia LOE: $loe_rrc Categoria: $categoria Grado: $grado Guia: $guia");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error); 
		$stmt->close();
		// PENDING: study if this is really neccesary:
        // no sense in client, and in server is done "by hand"
		if (!is_null($licencia)) $this->setServerID("perros",$id);
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
		$rs= $this->__delete("perros","(ID=$idperro)");
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
		$rs= $this->query("UPDATE perros SET Guia=1 WHERE (ID=$idperro)");
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}

    /**
     * Modify Database replaceing every instances of $fromID with $toID
     * That is: dog "from" becomes dog "to"
     * This code does not set up resulting dog properties, just move ID's
     * @param $fromIDs dog list of items to be replaced in form BEGIN,dog[,dog[...]],END
     * @param $toID dog to replace with
     */
	function joinTo($fromIDs,$toID) {
        $this->myLogger->enter();
        if ($toID<=0) return $this->error("joinTo() invalid to:$toID value");
        // en teoria, inscripciones y resultados se actualizan mediante foreign key (Perros.ID), pero
        // por siacaso lo hacemos a mano
	    // update inscripciones
        $this->query("START TRANSACTION");
        $ids = explode(',', $fromIDs); //split string into array seperated by ', '
        foreach($ids as $fromID)  { //loop over values
            if ( $fromID=="BEGIN" ) continue;
            if ( $fromID=="END" ) continue;
            if ( $fromID=="" ) continue;
            if ( $fromID==$toID ) continue; // do not replace myself
            $res=$this->query("UPDATE inscripciones SET Perro=$toID WHERE ( Perro=$fromID )");
            if (!$res) {
                $err=$this->conn->error;
                $this->query("ROLLBACK");
                return $this->error("Error (update inscripciones) in Join Dog: {$fromID}: <br/>{$err}");
            }
            // update resultados
            $res=$this->query("UPDATE resultados SET Perro=$toID WHERE ( Perro=$fromID )");
            if (!$res) {
                $err=$this->conn->error;
                $this->query("ROLLBACK");
                return $this->error("Error (update resultados) in Join Dog: {$fromID}: <br/>{$err}");
            }
            // en estas tablas, hay que actualizar las listas del tipo ',FromID,' a ',ToID,'
            // update starting orders
            $res=$this->query("UPDATE mangas SET Orden_Salida=REPLACE(Orden_Salida,',$fromID,',',$toID,') where (Orden_Salida like '%,$fromID,%')");
            if (!$res) {
                $err=$this->conn->error;
                $this->query("ROLLBACK");
                return $this->error("Error (update mangas) in Join Dog: {$fromID}: <br/>{$err}");
            }
            // update team member lists
            $res=$this->query("UPDATE equipos SET Miembros=REPLACE(Miembros,',$fromID,',',$toID,') WHERE (Miembros LIKE '%,$fromID,%')");
            if (!$res)  {
                $err=$this->conn->error;
                $this->query("ROLLBACK");
                return $this->error("Error (update equipos) in Join Dog: {$fromID}: <br/>{$err}");
            }

            // and finally remove "from" dog
            $rs= $this->__delete("perros","(ID=$fromID)");
            if (!$rs)  {
                $err=$this->conn->error;
                $this->query("ROLLBACK");
                return $this->error("Error (delete dog) in Join Dog: {$fromID}: <br/>{$err}");
            }
        }
        $this->query("COMMIT");
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
		// $this->myLogger->debug("Sort order is: $sort");
		$search=http_request("where","s","");
		$page=http_request("page","i",1);
		$rows=http_request("rows","i",50);
		$fed="1";
		if ($this->federation >=0) $fed="( Federation = {$this->federation} )";
		$where = "1";
		$limit = "";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit="".$offset.",".$rows;
		}
		if ($search !=="" ) {
		    $where="( 
		        (Nombre LIKE '%$search%') OR ( NombreLargo LIKE '%$search%') 
		        OR ( NombreGuia LIKE '%$search%') OR ( Licencia LIKE '%$search%') 
		        OR ( NombreClub LIKE '%$search%') )";
            $g=parseGrade($search); $grad=($g==="-")? "":" ( Grado='{$g}' ) ";
            $c=parseCategory($search,$this->federation); $cat=($c==="-")? "":" ( Categoria='{$c}' ) ";
            if ($g!=="-" ) $where=$grad;
            if ($c!=="-" ) $where=$cat;
        }
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "perroguiaclub",
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
		$fed="1 ";
		if ($this->federation >=0) $fed="( Federation = {$this->federation} )";
		// evaluate search criteria for query
		$q=http_request("q","s","");
		$where =  ($q==="") ? "1" : " ( ( Nombre LIKE '%$q%' ) OR ( NombreLargo LIKE '%$q%' ) OR ( NombreGuia LIKE '%$q%' ) OR ( NombreClub LIKE '%$q%' ) )";
		// retrieve result from parent __select() call
		$result= $this->__select(
				/* SELECT */ "*",
				/* FROM */ "perroguiaclub",
				/* WHERE */ "$fed AND $where",
				/* ORDER BY */ "Club ASC, Guia ASC, Nombre ASC",
				/* LIMIT */ ""
		);
		// return composed array
		$this->myLogger->leave();
		return $result;
	}


    /**
     * Like select(), but only with dogs with duplicate licenses
     * Enumerate all dogs that matches requested criteria and order
     * @return null on error, else requested data
     */
    function duplicates() {
        $this->myLogger->enter();
        // evaluate offset and row count for query
        $sort=getOrderString( //needed to properly handle multisort requests from datagrid
            http_request("sort","s",""),
            http_request("order","s",""),
            "Licencia ASC,ID ASC"
        );
        $this->myLogger->debug("Sort order is: $sort");
        $search=http_request("where","s","");
        $page=http_request("page","i",1);
        $rows=http_request("rows","i",50);
        $fed="1";
        if ($this->federation >=0) $fed="( Federation = {$this->federation} )";
        // where field in select is too slow. use this trick to speedup
        // https://stackoverflow.com/questions/6135376/mysql-select-where-field-in-subquery-extremely-slow-why
        $dups= " Licencia IN (".
                    "SELECT * FROM (".
                        " SELECT Licencia FROM perroguiaclub GROUP BY Licencia HAVING COUNT(*) > 1".
                    ") AS Subquery ".
                ") ";
        $where = "1";
        $limit = "";
        if ($page!=0 && $rows!=0 ) {
            $offset=($page-1)*$rows;
            $limit="".$offset.",".$rows;
        }
        if ($search!=="") $where="( (Nombre LIKE '%$search%') OR ( NombreLargo LIKE '%$search%' ) OR ( NombreGuia LIKE '%$search%') OR ( Licencia LIKE '%$search%') OR ( NombreClub LIKE '%$search%') )";
        $result=$this->__select(
        /* SELECT */ "*",
            /* FROM */ "perroguiaclub",
            /* WHERE */ "$fed AND $where AND $dups",
            /* ORDER BY */ $sort,
            /* LIMIT */ $limit
        );
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
		$fed="";
		if ($this->federation >=0) $fed="( Federation = {$this->federation} ) AND ";
		// retrieve result from parent __select() call
		$result= $this->__select(
				/* SELECT */ "*",
				/* FROM */ "perroguiaclub",
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
		$obj=$this->__getObject("perroguiaclub",$idperro);
		if (!is_object($obj))	return $this->error("No Dog found with ID=$idperro");
		$data= json_decode(json_encode($obj), true); // convert object to array
		$data['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		// in videowall/livestream, add results data for journey
		$jornada=http_request("Jornada","i",0);
		if ($jornada==0) return $data; // no need to add competition data
		$res=$this->__select("*","resultados","(Perro=$idperro) AND (Jornada=$jornada)");
		$data["results"]=$res['rows'];
		return $data;
	}
	
	/**
	 * Enumerate categorias ( std, small, medium, tiny 
	 * Notice that this is not a combogrid, just combobox, so dont result count
	 * @return null on error; result on success
	 */
	function categoriasPerro() {
		$this->myLogger->enter();
        // default federation info
        $fedinfo=new Federations();
        // search requested federation
        if ($this->federation>=0) {
            $f=Federations::getFederation(intval($this->federation));
            if ($f) $fedinfo=$f;
            else $this->myLogger->error("CategoriasPerro: invalid federation ID:{$this->federation}");
        }
        // compose result
        $result =array();
        foreach ($fedinfo->get('ListaCategorias') as $cat => $name) {
            if ($cat==="-")
                array_push($result,array("Categoria"=>$cat,"Observaciones"=>$name,"selected"=>1));
            else array_push($result,array("Categoria"=>$cat,"Observaciones"=>$name,"selected"=>0));
        }
        $this->myLogger->leave();
        return $result;

	}
	
	/**
	 * Enumerate grados
	 * @return null on error; result on success
	 * Notice that this is not a combogrid, just combobox, so dont result count
	 */
	function gradosPerro() {
        $this->myLogger->enter();
        // default federation info
        $fedinfo=new Federations();
        // search requested federation
        if ($this->federation>=0) {
            $f=Federations::getFederation(intval($this->federation));
            if ($f) $fedinfo=$f;
            else $this->myLogger->error("GradosPerro: invalid federation ID:{$this->federation}");
        }
        // compose result
        $result =array();
        foreach ($fedinfo->get('ListaGrados') as $cat => $name) {
            if ($cat==="-")
                array_push($result,array("Grado"=>$cat,"Comentarios"=>$name,"selected"=>1));
            else array_push($result,array("Grado"=>$cat,"Comentarios"=>$name,"selected"=>0));
        }
        $this->myLogger->leave();
        return $result;
	}
}
	
?>