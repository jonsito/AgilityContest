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
		$nombre =	http_request("Nombre","s",null); 
		$raza =		http_request("Raza","s",null); 
		$loe_rrc =	http_request("LOE_RRC","s",null); 
		$licencia = http_request("Licencia","s",null); 
		$categoria= http_request("Categoria","s",null); 
		$grado =	http_request("Grado","s",null); 
		$guia =		http_request("Guia","s",null); 
		
		$this->myLogger->info("Nombre: $nombre Raza: $raza LOE: $loe_rrc Categoria: $categoria Grado: $grado Guia: $guia");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		$stmt->close();
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
		
	}
	
	/**
	 * Update data for provided dog ID
	 * @param {integer} $dorsal dog id primary key
	 * @return "" on success; null on error
	 */
	function update($dorsal) {
		$this->myLogger->enter();
		if ($dorsal===null) return $this->error("No dorsal provided"); 
		// componemos un prepared statement
		$sql ="UPDATE Perros SET Nombre=? , Raza=? , LOE_RRC=? , Licencia=? , Categoria=? , Grado=? , Guia=?
		       WHERE ( Dorsal=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('sssssssi',$nombre,$raza,$loe_rrc,$licencia,$categoria,$grado,$guia,$dorsal);
		if (!$res) return $this->error($this->conn->error);

		// iniciamos los valores, chequeando su existencia
		$nombre =	http_request("Nombre","s",null);
		$raza =		http_request("Raza","s",null);
		$loe_rrc =	http_request("LOE_RRC","s",null);
		$licencia = http_request("Licencia","s",null);
		$categoria= http_request("Categoria","s",null);
		$grado =	http_request("Grado","s",null);
		$guia =		http_request("Guia","s",null);

		$this->myLogger->info("Dorsal: $dorsal Nombre: $nombre Raza: $raza LOE: $loe_rrc Categoria: $categoria Grado: $grado Guia: $guia");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		$stmt->close();
		if (!$res) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Delete dog with provided dorsal
	 * @param {integer} $dorsal dog primary key
	 * @return "" on success ; otherwise null
	 */
	function delete($dorsal) {
		$this->myLogger->enter();
		if ($dorsal===null) return $this->error("No dorsal provided"); 
		$rs= $this->query("DELETE FROM Perros WHERE (Dorsal=$dorsal)");
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Desasigna el guia al perro indicado
	 * @param {integer} $dorsal dorsal id
	 * @return "" on success; otherwise null
	 */
	function orphan ($dorsal) {
		$this->myLogger->enter();
		if ($dorsal===null) return $this->error("No dorsal provided"); 
		$rs= $this->query("UPDATE Perros SET Guia='-- Sin asignar --' WHERE (Dorsal='$dorsal')");
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Enumerate all dogs that matches requested criteria and order
	 * @return null on error, else requested data
	 */
	function select() {
		// evaluate offset and row count for query
		$page= http_request("page","i",1);
		$rows= http_request("rows","i",20);
		$sort= http_request("sort","s","Nombre");
		$order=http_request("order","s","ASC");
		$search=http_Request("where","s","");
		$where = ' ';
		if ($search!=='') $where="WHERE (Nombre LIKE '%$search%') OR ( Guia LIKE '%$search%') OR ( Club LIKE '%$search%')";
		$offset = ($page-1)*$rows;
		$result = array();

		// execute first query to know how many elements
		$rs=$this->query("SELECT count(*) FROM PerroGuiaClub $where");
		if (!$rs) return $this->error($this->conn->error);
		$row=$rs->fetch_array();
		$result["total"] = $row[0];
		$rs->free();
		// second query to retrieve $rows starting at $offset
		$str="SELECT * FROM PerroGuiaClub $where ORDER BY $sort $order LIMIT $offset,$rows";
		$this->myLogger->query($str);
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		// disconnect from database and return result
		$rs->free();
		return $result;
	}
	
	/**
	 * Like select but not provide indexed search
	 * @return NULL|multitype:multitype: unknown
	 */
	function enumerate() {
		$this->myLogger->enter();
		
		// evaluate search criteria for query
		$q=http_request("q","s",null);
		$like =  ($q===null) ? "" : " WHERE ( ( Nombre LIKE '%$q%' ) OR ( Guia LIKE '%$q%' ) OR ( Club LIKE '%$q%' ) )";

		// execute first query to know how many elements
		$result = array();
		$rs=$this->query("SELECT count(*) FROM PerroGuiaClub ".$like);
		if (!$rs) return $this->error($this->conn->error);
		$row=$rs->fetch_row();
		$result["total"] = $row[0];
		$rs->free();
		// second query to retrieve $rows starting at $offset
		$rs=$this->query("SELECT * FROM PerroGuiaClub $like ORDER BY Club,Guia,Nombre");
		if (!$rs) return $this->error($this->conn->error);
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		// return composed array
		$this->myLogger->leave();
		$rs->free();
		return $result;
	}
	
	/** 
	 * enumera todos los perros asociados a un guia
	 * @param {string} $guia Nombre del guia
	 */
	function selectByGuia($guia) {
		$this->myLogger->enter();
		if ($guia===null) return $this->error("No guia specified");
		// evaluate offset and row count for query
		$result = array();
		$items = array();
		// execute first query to know how many elements
		$str="SELECT count(*) FROM Perros WHERE ( Guia = '".$guia."' )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$row=$rs->fetch_row();
		$result["total"] = $row[0];
		$rs->free();
		$str="SELECT * FROM Perros WHERE ( Guia ='$guia' ) ORDER BY Nombre ASC";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// retrieve result into an array
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		// free resources and return
		$rs->free();
		$result["rows"] = $items;
		$this->myLogger->leave();
		return $result;
	}
	
	/** 
	 * Obtiene los datos del perro con el dorsal indicado
	 * Usado para rellenar formularios:  formid.form('load',url);
	 * @param {integer} $dorsal dog primary key
	 * @return null on error; array() with data on success
	 */
	function selectByDorsal($dorsal){
		$this->myLogger->enter();
		if ($dorsal==0) {
			$this->errormsg="selectByDorsal: No dorsal specified";
			return null;
		}
		// second query to retrieve $rows starting at $offset
		$str="SELECT * FROM PerroGuiaClub WHERE ( Dorsal = $dorsal )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		// retrieve result into an array
		$result =$rs->fetch_array(); // should be only one item
		$result['Operation']='update'; // dirty trick to ensure that form operation is rewritten on loadform

		// free resources and return result
		$rs->free();
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Enumerate categorias ( std, small, medium, tiny 
	 * Notice that this is not a combogrid, just combobox, so dont result count
	 * @return null on error; result on success
	 */
	function categoriasPerro() {
		$this->myLogger->enter();
		// evaluate offset and row count for query
		$q=http_request("q","s",null);
		$like =  ($q===null) ? "" : " WHERE Categoria LIKE '%".$q."%'";
	
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
		$q=http_request("q","s",null);
		$like =  ($q===null) ? "" : " WHERE Grado LIKE '%".$q."%'";

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