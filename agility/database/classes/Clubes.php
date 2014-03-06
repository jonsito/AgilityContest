<?php
	require_once("DBObject.php");

class Clubes extends DBObject {

	/* use parent constructor and destructors */
	
	/**
	 * insert a new club into database
	 * @return empty string if ok; else null
	 */
	function insert() {
		$this->myLogger->enter();
		// componemos un prepared statement
		$sql ="INSERT INTO Clubes (Nombre,Direccion1,Direccion2,Provincia,Contacto1,Contacto2,Contacto3,GPS,
				Web,Email,Facebook,Google,Twitter,Observaciones,Baja)
			   VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('sssssssssssssss',$nombre,$direccion1,$direccion2,$provincia,$contacto1,$contacto2,$contacto3,$gps,
				$web,$email,$facebook,$google,$twitter,$observaciones,$baja);
		if (!$res)  return $this->error($this->conn->error);
		
		// iniciamos los valores, chequeando su existencia
		$nombre 	= http_request("Nombre","s",null,false);
		$direccion1 = http_request('Direccion1',"s",null,false);
		$direccion2 = http_request('Direccion2',"s",null,false); 
		$provincia	= http_request('Provincia',"s",null,false);
		$contacto1	= http_request('Contacto1',"s",null,false);
		$contacto2	= http_request('Contacto2',"s",null,false);
		$contacto3	= http_request('Contacto3',"s",null,false);
		$gps		= http_request('GPS',"s",null,false);
		$web		= http_request('Web',"s",null,false);
		$email		= http_request('Email',"s",null,false);
		$facebook	= http_request('Facebook',"s",null,false);
		$google		= http_request('Google',"s",null,false);
		$twitter	= http_request('Twitter',"s",null,false);
		$observaciones = http_request('Observaciones',"s",null,false);
		$baja		= http_request('Baja',"i",0);
		$this->myLogger->debug("Nombre: $nombre Direccion1: $direccion1 Contacto1: $contacto1 Observaciones: $observaciones");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		$stmt->close();
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return ""; // return ok
	}
	
	/**
	 * Update entry in database table "Clubs"
	 * @return string "" empty if ok; null on error
	 */
	function update() {
		$this->myLogger->enter();
		
		// componemos un prepared statement
		$sql ="UPDATE Clubes
				SET Nombre=? , Direccion1=? , Direccion2=? , Provincia=? ,
				Contacto1=? , Contacto2=? , Contacto3=? , GPS=? , Web=? ,
				Email=? , Facebook=? , Google=? , Twitter=? , Observaciones=? , Baja=?
				WHERE ( Nombre=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ssssssssssssssis',$nombre,$direccion1,$direccion2,$provincia,$contacto1,$contacto2,$contacto3,$gps,
				$web,$email,$facebook,$google,$twitter,$observaciones,$baja,$viejo);
		if (!$res) return $this->error($this->conn->error);
		// iniciamos los valores, chequeando su existencia
		$nombre 	= http_request("Nombre","s",null,false);
		$viejo		= http_request("Viejo","s",null,false);
		$direccion1 = http_request('Direccion1',"s",null,false);
		$direccion2 = http_request('Direccion2',"s",null,false); 
		$provincia	= http_request('Provincia',"s",null,false);
		$contacto1	= http_request('Contacto1',"s",null,false);
		$contacto2	= http_request('Contacto2',"s",null,false);
		$contacto3	= http_request('Contacto3',"s",null,false);
		$gps		= http_request('GPS',"s",null,false);
		$web		= http_request('Web',"s",null,false);
		$email		= http_request('Email',"s",null,false);
		$facebook	= http_request('Facebook',"s",null,false);
		$google		= http_request('Google',"s",null,false);
		$twitter	= http_request('Twitter',"s",null,false);
		$observaciones = http_request('Observaciones',"s",null,false);
		$baja		= http_request('Baja',"i",0);
		
		$this->myLogger->debug("Nombre: $nombre Viejo: $viejo Provincia: $provincia Direccion1: $direccion1 Contacto1: $contacto1 ");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		$stmt->close();
		return "";
	}
	
	function delete($nombre) {
		$this->myLogger->enter();
		if ($nombre===null)  return $this->error("No club name provided");
		// fase 1: desasignar guias del club
		$res= $this->query("UPDATE Guias SET Club='-- Sin asignar --'  WHERE (Club='$nombre')");
		if (!$res) return $this->error($this->conn->error);
		// fase 2: borrar el club de la BBDD
		$res= $this->query("DELETE FROM Clubes WHERE (Nombre='$nombre')");
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * retrieve all clubes from table, according sort, search and limit requested
	 */
	function select() {
		$this->myLogger->enter();
		// evaluate offset and row count for query
		$page= http_request("page","i",1);
		$rows= http_request("rows","i",20);
		$sort= http_request("sort","s","Nombre");
		$order=http_request("order","s","ASC");
		$search=http_Request("where","s","");
		$where = '';
		if ($search!=='') $where=" WHERE ( (Nombre LIKE '%$search%') OR ( Email LIKE '%$search%') OR ( Facebook LIKE '%$search%') ) ";
		$offset = ($page-1)*$rows;
		$result = array();
		
		// execute first query to know how many elements
		$rs=$this->query("SELECT count(*) FROM Clubes $where");
		if (!$rs)  return $this->error($this->conn->error);
		$row=$rs->fetch_array();
		$rs->free();
		$result["total"] = $row[0];
		
		// second query to retrieve $rows starting at $offset
		$rs=$this->query("SELECT * FROM Clubes $where ORDER BY $sort $order LIMIT $offset,$rows");
		if (!$rs) return $this->error($this->conn->error);
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			array_push($items, $row);
		}
		$result["rows"] = $items;
		$rs->free();
		// return composed array
		$this->myLogger->leave();
		return $result;
	}
	
	/** 
	 * return a dupla Nombre,Provincia list according select criteria
	 * return data if success; null on error
	 */
	function enumerate() {
		$this->myLogger->enter();
		// evaluate offset and row count for query
		$q=http_request("q","s",null);
		$like =  ($q===null) ? "" : " WHERE Nombre LIKE '%".$q."%'";
		
		// execute first query to know how many elements
		$result = array();
		$rs=$this->query("SELECT count(*) FROM Clubes ".$like);
		if (!$rs)  return $this->error($this->conn->error);
		$row=$rs->fetch_row();
		$result["total"] = $row[0];
		$rs->free();
		
		// second query to retrieve $rows starting at $offset
		$rs=$this->query("SELECT Nombre,Provincia FROM Clubes ".$like." ORDER BY Nombre ASC");
		if (!$rs) return $this->error($this->conn->error);
		
		// retrieve result into an array
		$items = array();
		while($row = $rs->fetch_array()){
			// utf8 encode data
			// $row["Nombre"] =utf8_encode( $row["Nombre"] );
			// $row["Provincia"]   =utf8_encode( $row["Provincia"]   );
			// and store into result array
			array_push($items, $row);
		}
		$result["rows"] = $items;
		// return composed array
		$this->myLogger->leave();
		$rs->free();
		return $result;
	}
	
	/** 
	 * Retorna el logo asociado al club de nombre indicado
	 * NOTA: esto no retorna una respuesta json, sino una imagen
	 * @param unknown $nombre
	 */
	function getLogo($nombre) {
		$this->myLogger->enter();
		$str="SELECT Logo FROM Clubes WHERE Nombre='$nombre'";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$row=$rs->fetch_object();
		$rs->free();
		$name="rsce.png";
		if ($row) $name=$row->Logo;
		$fname=__DIR__."/../../images/logos/$name";
		if (!file_exists($fname)) {
			$this->myLogger->notice("Logo file $fname does not exists");
			$fname=__DIR__."/../../images/logos/rsce.png"; // use default name
		}
		$size = getimagesize($fname);
		header('Content-type: '.$size['mime']);
		readfile($fname);
	}
} /* end of class "Clubes" */

?>