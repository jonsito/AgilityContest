<?php
require_once("DBObject.php");

class Dogs extends DBObject {
	
	/**
	 * Insert a new dog into database
	 * @return {string} "" if ok; null on error
	 */
	function insert() {
		$this->myLogger->enter();
		// componemos un prepared statement
		$sql ="INSERT INTO Perros (Nombre,Raza,LOE_RRC,Licencia,Categoria,Grado,Guia)
			   VALUES(?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('sssssss',$nombre,$raza,$loe_rrc,$licencia,$categoria,$grado,$guia);
		if (!$res) return $this->error($this->conn->error);
		// iniciamos los valores, chequeando su existencia
		$nombre =	http_request("Nombre","s",null,false); 
		$raza =		http_request("Raza","s",null,false); 
		$loe_rrc =	http_request("LOE_RRC","s",null,false); 
		$licencia = http_request("Licencia","s",null,false); 
		$categoria= http_request("Categoria","s",null,false); 
		$grado =	http_request("Grado","s",null,false); 
		$guia =		http_request("Guia","s",null,false); 
		
		$this->myLogger->info("Nombre: $nombre Raza: $raza LOE: $loe_rrc Categoria: $categoria Grado: $grado Guia: $guia");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error);
		$stmt->close();
		$this->myLogger->leave();
		return "";
		
	}
	
	/**
	 * Update data for provided dog ID
	 * @param {integer} $id dog id primary key
	 * @return "" on success; null on error
	 */
	function update($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("No Dog ID provided"); 
		// componemos un prepared statement
		$sql ="UPDATE Perros SET Nombre=? , Raza=? , LOE_RRC=? , Licencia=? , Categoria=? , Grado=? , Guia=?
		       WHERE ( ID=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ssssssii',$nombre,$raza,$loe_rrc,$licencia,$categoria,$grado,$guia,$idperro);
		if (!$res) return $this->error($this->conn->error);

		// iniciamos los valores, chequeando su existencia
		$nombre =	http_request("Nombre","s",null,false);
		$raza =		http_request("Raza","s",null,false);
		$loe_rrc =	http_request("LOE_RRC","s",null,false);
		$licencia = http_request("Licencia","s",null,false);
		$categoria= http_request("Categoria","s",null,false);
		$grado =	http_request("Grado","s",null,false);
		$guia =		http_request("Guia","i",0,false);
		$idperro =	$id;

		$this->myLogger->info("ID: $id Nombre: $nombre Raza: $raza LOE: $loe_rrc Categoria: $categoria Grado: $grado Guia: $guia");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error); 
		$stmt->close();
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Delete dog with provided idperro
	 * @param {integer} $idperro dog primary key
	 * @return "" on success ; otherwise null
	 */
	function delete($idperro) {
		$this->myLogger->enter();
		if ($idperro<=0) return $this->error("No Dog ID"); 
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
		if ($idperro<=0) return $this->error("No Dog ID provided"); 
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
		$sort= http_request("sort","s","Nombre");
		$order=http_request("order","s","ASC");
		$search=http_request("where","s","");
		$page=http_request("page","i",0);
		$rows=http_request("rows","i",0);
		$limit="";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit="".$offset.",".$rows;
		}
		$where = "";
		if ($search!=="") $where="(Nombre LIKE '%$search%') OR ( NombreGuia LIKE '%$search%') OR ( NombreClub LIKE '%$search%')";
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "PerroGuiaClub",
				/* WHERE */ $where,
				/* ORDER BY */ $sort." ".$order,
				/* LIMIT */ $limit
		);
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Like select but not provide ordered search
	 * @return NULL|multitype:multitype: unknown
	 */
	function enumerate() {
		$this->myLogger->enter();	
		// evaluate search criteria for query
		$q=http_request("q","s","");
		$where =  ($q==="") ? "" : " ( ( Nombre LIKE '%$q%' ) OR ( NombreGuia LIKE '%$q%' ) OR ( NombreClub LIKE '%$q%' ) )";
		// retrieve result from parent __select() call
		$result= $this->__select(
				/* SELECT */ "*",
				/* FROM */ "PerroGuiaClub",
				/* WHERE */ $where,
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
		if ($idguia<=0) return $this->error("Invalid Guia ID");
		// retrieve result from parent __select() call
		$result= $this->__select(
				/* SELECT */ "*",
				/* FROM */ "PerroGuiaClub",
				/* WHERE */ "( Guia = $idguia )",
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
	function selectByIDPerro($idperro){
		$this->myLogger->enter();
		if ($idperro<=0) return $this->error("No Perro ID specified");
		// make query
		$data= $this->__singleSelect(
				/* SELECT */ "*",
				/* FROM */ "PerroGuiaClub",
				/* WHERE */ "( ID=$idperro )"
		);
		if (!$data)	return $this->error("No Dog found with ID=$idperro");
		$data['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		return $data;
	}
	
	/**
	 * Enumerate categorias ( std, small, medium, tiny 
	 * Notice that this is not a combogrid, just combobox, so dont result count
	 * @return null on error; result on success
	 */
	function categoriasPerro() {
		$this->myLogger->enter();
		// evaluate offset and row count for query
		$q=http_request("q","s","");
		$like =  ($q==="") ? "" : " WHERE Categoria LIKE '%".$q."%'";
	
		// query to retrieve table data
		$sql="SELECT Categoria,Observaciones FROM Categorias_Perro ".$like." ORDER BY Categoria";
		$this->myLogger->query($sql);
		$rs=$this->query($sql);
		if (!$rs) return $this->error($this->conn->error); 
		// retrieve result into an array
		$result = array();
		while($row = $rs->fetch_array()){
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
	 * @return null on error; result on success
	 * Notice that this is not a combogrid, just combobox, so dont result count
	 */
	function gradosPerro() {
		$this->myLogger->enter();
		// evaluate offset and row count for query
		$q=http_request("q","s","");
		$like =  ($q==="") ? "" : " WHERE Grado LIKE '%".$q."%'";

		// query to retrieve table data
		$sql="SELECT Grado,Comentarios FROM Grados_Perro ".$like." ORDER BY Grado";
		$this->myLogger->query($sql);
		$rs=$this->query($sql);
		if (!$rs) return $this->error($this->conn->error);
		// retrieve result into an array
		$result = array();
		while($row = $rs->fetch_array()){
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