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
	function update($id) {
		$this->myLogger->enter();
		
		// componemos un prepared statement
		$sql ="UPDATE Clubes
				SET Nombre=? , Direccion1=? , Direccion2=? , Provincia=? ,
				Contacto1=? , Contacto2=? , Contacto3=? , GPS=? , Web=? ,
				Email=? , Facebook=? , Google=? , Twitter=? , Observaciones=? , Baja=?
				WHERE ( ID=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ssssssssssssssis',$nombre,$direccion1,$direccion2,$provincia,$contacto1,$contacto2,$contacto3,$gps,
				$web,$email,$facebook,$google,$twitter,$observaciones,$baja,$idclub);
		if (!$res) return $this->error($this->conn->error);
		// iniciamos los valores, chequeando su existencia
		$nombre 	= http_request("Nombre","s",null,false);
		$idclub		= $id;
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
		
		$this->myLogger->debug("Nombre: $nombre ID: $idclub Provincia: $provincia Direccion1: $direccion1 Contacto1: $contacto1 ");
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		$stmt->close();
		return "";
	}
	
	function delete($id) {
		$this->myLogger->enter();
		// cannot delete default club id or null club id
		if ($id<=1)  return $this->error("No club or invalid Club ID '$id' provided");
		// fase 1: desasignar guias del club (assign to default club with ID=1)
		$res= $this->query("UPDATE Guias SET Club=1  WHERE (Club=$id)");
		if (!$res) return $this->error($this->conn->error);
		// fase 2: borrar el club de la BBDD
		$res= $this->query("DELETE FROM Clubes WHERE (ID=$id)");
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
		$sort= http_request("sort","s","Nombre");
		$order=http_request("order","s","ASC");
		$search=http_Request("where","s","");
		$where = '';
		if ($search!=='') $where=" WHERE ( (Nombre LIKE '%$search%') OR ( Email LIKE '%$search%') OR ( Facebook LIKE '%$search%') ) ";
		$result = array();
		// query to retrieve $rows 
		$rs=$this->query("SELECT * FROM Clubes $where ORDER BY $sort $order");
		if (!$rs) return $this->error($this->conn->error);
		// retrieve result into an array
		$result["rows"] = $this->fetch_all($rs);
		$result["total"] = $rs->num_rows;
		// disconnect from database and return composed array
		$rs->free();
		// return composed array
		$this->myLogger->leave();
		return $result;
	}
	
	/** 
	 * return a dupla ID Nombre,Provincia list according select criteria
	 * return data if success; null on error
	 */
	function enumerate() {
		$this->myLogger->enter();
		// evaluate offset and row count for query
		$q=http_request("q","s","");
		$like =  ($q==="") ? "" : " WHERE Nombre LIKE '%".$q."%'";
		
		$result = array();
		// query to retrieve data
		$rs=$this->query("SELECT ID,Nombre,Provincia FROM Clubes ".$like." ORDER BY Nombre ASC");
		if (!$rs) return $this->error($this->conn->error);
		// retrieve result into an array
		$result["rows"] = $this->fetch_all($rs);
		$result["total"] = $rs->num_rows;
		// disconnect from database and return composed array
		$rs->free();
		// return composed array
		$this->myLogger->leave();
		return $result;
	}
	
	/** 
	 * Retorna el logo asociado al club de nombre indicado
	 * NOTA: esto no retorna una respuesta json, sino una imagen
	 * @param {integer} $id club id
	 */
	function getLogo($id) {
		$this->myLogger->enter();
		if ($id==0) $id=1; // on insert, select default logo
		$str="SELECT Logo FROM Clubes WHERE ID=$id";
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