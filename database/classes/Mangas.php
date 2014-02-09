<?php

require_once("DBObject.php");

class Mangas extends DBObject {
	protected $jornada;
	
	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {string} $jornada jornada ID
	 * @throws Exception if cannot contact database or invalid jornada ID
	 */
	function __construct($file,$jornada) {
		parent::__construct($file);
		if ($jornada<=0) {
			$this->errormsg="Manga::Construct invalid jornada ID";
			throw new Exception($this->errormsg);
		}
		$this->jornada=$jornada;
	}
	
	function insert($tipo,$grado) {
		$this->myLogger->enter();
		// si la manga existe no hacer nada; si no existe crear manga
		$str="SELECT count(*) AS 'result' FROM Mangas WHERE ( Jornada = ".$this->jornada." ) AND  ( Tipo = '".$tipo."' )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		if ($rs->num_rows > 0) {
			$this->myLogger->info("Jornada:".$this->jornada." Manga: $tipo already exists. exit OK");
			return "";
		}
		$rs->free();
		$str="INSERT INTO Mangas ( Jornada , Tipo, Grado ) VALUES (".$this->jornada.",'".$tipo."','".$grado."')";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}
	
	function update($mangaid) {
		$this->myLogger->enter();
		if ($mangaid <=0) return $this->error("Invalid Manga ID"); 
		// preparamos la query SQL
		$sql= "UPDATE Mangas SET
 			Recorrido=? ,
			Dist_L=? , Obst_L=? , Dist_M=? , Obst_M=? , Dist_S=? , Obst_S=? ,
			TRS_L_Tipo=? , TRS_L_Factor=? , TRS_L_Unit=? , TRM_L_Tipo=? , TRM_L_Factor=? , TRM_L_Unit=? ,
			TRS_M_Tipo=? , TRS_M_Factor=? , TRS_M_Unit=? , TRM_M_Tipo=? , TRM_M_Factor=? , TRM_M_Unit=? ,
			TRS_S_Tipo=? , TRS_S_Factor=? , TRS_S_Unit=? , TRM_S_Tipo=? , TRM_S_Factor=? , TRM_S_Unit=? ,
			Juez1=? , Juez2=? ,
			Observaciones=? , Cerrada=?
			WHERE (ID=?)";
		
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param(
				'iiiiiiiiisiisiisiisiisiissssii',
				$recorrido,
				$dist_l,		$obst_l,		$dist_m,		$obst_m,		$dist_s,		$obst_s, 	// distancias y obstaculos
				$trs_l_tipo,	$trs_l_factor,	$trs_l_unit,	$trm_l_tipo,	$trm_l_factor,	$trm_l_unit,// TRS y TRM Large
				$trs_m_tipo,	$trs_m_factor,	$trs_m_unit,	$trm_m_tipo,	$trm_m_factor,	$trm_m_unit,// TRS Y TRM Medium
				$trs_s_tipo,	$trs_s_factor,	$trs_s_unit,	$trm_s_tipo,	$trm_s_factor,	$trm_s_unit,// TRS y TRM Small
				$juez1, 		$juez2, 		$observaciones,	$cerrada,		$id		
		);
		if (!$res) return $this->error($this->conn->error); 
		
		// retrieve http request variables
		/*
		 * ID		(PRIMARY KEY)
		* Jornada	(no debe ser modificada)
		* Tipo 	(no debe ser modificada)
		* Recorrido
		* Dist_L Obst_L Dist_M Obst_M Dist_S Obst_S
		* TRS_L_Tipo TRS_L_Factor TRS_L_Unit TRM_L_Tipo TRM_L_Factor TRM_L_Unit
		* TRS_M_Tipo TRS_M_Factor TRS_M_Unit TRM_M_Tipo TRM_M_Factor TRM_M_Unit
		* TRS_S_Tipo TRS_S_Factor TRS_S_Unit TRM_S_Tipo TRM_S_Factor TRM_S_Unit
		* Juez1 Juez2
		* Observaciones Cerrada
		* Orden_Salida (se modifica en otro sitio)
		*/
		$id			= $mangaid;
		$recorrido	= http_request("Recorrido","i",0);(isset($_REQUEST['Recorrido']))?intval($_REQUEST['Recorrido']):0;
		// distancias
		$dist_l = http_request("Dist_L","i",0);
		$dist_m = http_request("Dist_M","i",0);
		$dist_s = http_request("Dist_S","i",0);
		// obstaculos
		$obst_l = http_request("Obst_L","i",0); 
		$obst_m = http_request("Obst_M","i",0);
		$obst_s = http_request("Obst_S","i",0);
		// tipo TRS
		$trs_l_tipo = http_request("TRS_L_Tipo","i",0);
		$trs_m_tipo = http_request("TRS_M_Tipo","i",0);
		$trs_s_tipo = http_request("TRS_S_Tipo","i",0);
		// tipo TRM
		$trm_l_tipo = http_request("TRM_L_Tipo","i",0);
		$trm_m_tipo = http_request("TRM_M_Tipo","i",0);
		$trm_s_tipo = http_request("TRM_S_Tipo","i",0);
		// factor TRS
		$trs_l_factor = http_request("TRS_L_Factor","i",0);
		$trs_m_factor = http_request("TRS_M_Factor","i",0);
		$trs_s_factor = http_request("TRS_S_Factor","i",0);
		// factor TRM
		$trm_l_factor = http_request("TRM_L_Factor","i",0);
		$trm_m_factor = http_request("TRM_M_Factor","i",0);
		$trm_s_factor = http_request("TRM_S_Factor","i",0);
		// Unidad TRS
		$trs_l_unit = http_request("TRS_L_Unit","s","s");
		$trs_m_unit = http_request("TRS_M_Unit","s","s");
		$trs_s_unit = http_request("TRS_S_Unit","s","s");
		// Unidad TRM
		$trm_l_unit = http_request("TRM_L_Unit","s","s");
		$trm_m_unit = http_request("TRM_M_Unit","s","s");
		$trm_s_unit = http_request("TRM_S_Unit","s","s");
		// Jueces y observaciones
		$juez1 = http_request("Juez1","s",null);
		$juez2 = http_request("Juez2","s",null);
		$observaciones = http_request("Observaciones","s",null);
		// cerrada
		$cerrada = http_request("Cerrada","i",0);
		
		// ejecutamos el query
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error); 
		$stmt->close();
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Delete a Manga from jornada $this->jornada when tipo is $tipo
	 * @return "" on success; null on error
	 */
	function delete($tipo) {
		$this->myLogger->enter();
		if ($tipo===null) return $this->error("Invalid value for 'Tipo'"); 
		// si la manga existe, borrarla; si no existe, no hacer nada
		$str="DELETE FROM Mangas WHERE ( Jornada = ".$this->jornada." ) AND  ( Tipo = '".$tipo."' )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * recupera los datos de una manga determinada
	 * @param unknown $id id de la manga
	 * @return null on error, data on success
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Manga ID"); 
		// second query to retrieve $rows starting at $offset
		$str="SELECT * FROM Mangas WHERE ( ID = $id )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		// retrieve result into an array
		if ($rs->num_rows==0) return $this->error("No manga(s) found"); 
		$result = $rs->fetch_object();  // should only be one element
		// disconnect from database
		$rs->free();
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Enumera las mangas de una jornada
	 * @return null on error, result on success
	 */
	function selectByJornada() {
		$this->myLogger->enter();
		$result = array();
		$items = array();
		
		$str="SELECT count(*) FROM Mangas WHERE ( Jornada = ".$this->jornada." )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		$row=$rs->fetch_row();
		$rs->free();
		$result["total"] = $row[0];
		
		if($result["total"]>0) {
			$str="SELECT ID, Mangas.Tipo AS Tipo, Tipo_Manga.Descripcion AS Descripcion
			FROM Mangas,Tipo_Manga
			WHERE ( ( Jornada = ".$this->jornada." ) AND ( Mangas.Tipo = Tipo_Manga.Tipo) )
			ORDER BY Descripcion ASC";
			$rs=$this->query($str);
			if (!$rs) return $this->error($this->conn->error); 
			// retrieve result into an array
			while($row = $rs->fetch_array()) {
				array_push($items, $row); 
			}
			$rs->free();
		}
		$result["rows"] = $items;
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Evalua el TRS y TRM para cada categoria asociados a la manga indicada
	 * @param {integer} $manga ID de la manga
	 * @param {array} $tiempos Tres mejores tiempos de cada categoria array[$categoria][0,1,2]
	 * @return {array} datos[$categoria]['Dist','TRS','TRM']; null on error
	 */
	function datosTRS($manga,$tiempos) {
		$this->myLogger->enter();

		// preparamos el array con el resultado
		$result=array();
		$result["-"]=array();$result["L"]=array();$result["M"]=array();$result["S"]=array();$result["T"]=array();
		foreach ( array("-","L","M","S","T") as $c ) { 
			$result[$c]['Distancia']=0; $result[$c]['Obstaculos']=0 ;$result[$c]['TRS']=0;$result[$c]['TRM']=0; 
		}
		// obtenemos los datos de la manga
		$manga=$this->selectByID($manga);
		if (!$manga) return $this->error($this->errormsg);
		// distancias y obstáculos (necesaria para el calculo de la velocidad
		$result['L']['Dist']=$manga->Dist_L; $result['L']['Obstaculos']=$manga->Obst_L;
		$result['M']['Dist']=$manga->Dist_M; $result['M']['Obstaculos']=$manga->Obst_M;
		$result['S']['Dist']=$manga->Dist_S; $result['S']['Obstaculos']=$manga->Obst_S;
		
		// Calculo del TRS para standard
		switch ($manga->TRS_L_Tipo) {
			case 0: // tiempo fijo
				$result['L']['TRS']=$manga->TRS_L_Factor;
				break;
			case 1: // mejor tiempo 
				if ($manga->$TRS_L_Unit==="s") $result['L']['TRS']=$tiempos['L'][0] + $manga->TRS_L_Factor; // ( + X segundos )
				else $result['L']['TRS']=$tiempos['L'][0] * ( (100+$manga->TRS_L_Factor) / 100) ; // (+ X por ciento)
				break;
			case 2: // media de los tres mejores tiempos
				$media=($tiempos['L'][0]+$tiempos['L'][1]+$tiempos['L'][2]) / 3;
				if ($manga->$TRS_L_Unit==="s") $result['L']['TRS']=$media + $manga->TRS_L_Factor; // ( + X segundos )
				else $result['L']['TRS'] = $media * ( (100+$manga->TRS_L_Factor) / 100) ; // (+ X por ciento)
				break;
		}
		$result['L']['TRS'] = ceil( $result['L']['TRS'] ); // redondeamos hacia arriba el TRS de Standard
		
		// Calculo del TRS para Medium
		switch ($manga->TRS_M_Tipo) {
			case 0: // tiempo fijo
				$result['M']['TRS']=$manga->TRS_M_Factor;
				break;
			case 1: // mejor tiempo
				if ($manga->$TRS_M_Unit==="s") $result['M']['TRS']=$tiempos['M'][0] + $manga->TRS_M_Factor; // ( + X segundos )
				else $result['M']['TRS']=$tiempos['M'][0] * ( (100+$manga->TRS_M_Factor) / 100) ; // (+ X por ciento)
				break;
			case 2: // media de los tres mejores tiempos
				$media=($tiempos['M'][0]+$tiempos['M'][1]+$tiempos['M'][2]) / 3;
				if ($manga->$TRS_M_Unit==="s") $result['M']['TRS']=$media + $manga->TRS_M_Factor; // ( + X segundos )
				else $result['M']['TRS'] = $media * ( (100+$manga->TRS_M_Factor) / 100) ; // (+ X por ciento)
				break;
			case 3: // Referencia de tiempo el TRS de Standard
				if ($manga->$TRS_M_Unit==="s") $result['M']['TRS']=$result['L']['TRS'] + $manga->TRS_M_Factor; // ( + X segundos )
				else $result['M']['TRS'] = $result['L']['TRS'] * ( (100+$manga->TRS_M_Factor) / 100) ; // (+ X por ciento)
				break;
		}
		$result['M']['TRS'] = ceil( $result['M']['TRS'] ); // redondeamos hacia arriba el TRS de Midi

		// Calculo del TRS para Small
		switch ($manga->TRS_S_Tipo) {
			case 0: // tiempo fijo
				$result['S']['TRS']=$manga->TRS_S_Factor;
				break;
			case 1: // mejor tiempo
				if ($manga->$TRS_S_Unit==="s") $result['S']['TRS']=$tiempos['S'][0] + $manga->TRS_S_Factor; // ( + X segundos )
				else $result['S']['TRS']=$tiempos['S'][0] * ( (100+$manga->TRS_S_Factor) / 100) ; // (+ X por ciento)
				break;
			case 2: // media de los tres mejores tiempos
				$media=($tiempos['S'][0]+$tiempos['S'][1]+$tiempos['S'][2]) / 3;
				if ($manga->$TRS_S_Unit==="s") $result['S']['TRS']=$media + $manga->TRS_S_Factor; // ( + X segundos )
				else $result['S']['TRS'] = $media * ( (100+$manga->TRS_S_Factor) / 100) ; // (+ X por ciento)
				break;
			case 3: // Referencia de tiempo el TRS de Standard
				if ($manga->$TRS_S_Unit==="s") $result['S']['TRS']=$result['L']['TRS'] + $manga->TRS_S_Factor; // ( + X segundos )
				else $result['S']['TRS'] = $result['L']['TRS'] * ( (100+$manga->TRS_S_Factor) / 100) ; // (+ X por ciento)
				break;
			case 4: // Referencia de tiempo el TRS de Midi
				if ($manga->$TRS_S_Unit==="s") $result['S']['TRS']=$result['M']['TRS'] + $manga->TRS_S_Factor; // ( + X segundos )
				else $result['S']['TRS'] = $result['M']['TRS'] * ( (100+$manga->TRS_S_Factor) / 100) ; // (+ X por ciento)
				break;
		}
		$result['S']['TRS'] = ceil( $result['S']['TRS'] ); // redondeamos hacia arriba el TRS de Midi

		// Calculo del TRM para Standard
		switch($manga->TRM_L_Tipo) {
			case 0: // TRM Fijo
				$result['L']['TRM']=$manga->TRM_L_Factor;
				break;
			case 1: // TRS + (segs o porcentaje)
				if ($manga->$TRM_L_Unit==="s") $result['L']['TRM']=$result['L']['TRS'] + $manga->TRM_L_Factor; // ( + X segundos )
				else $result['L']['TRM'] = $result['L']['TRS'] * ( (100+$manga->TRM_L_Factor) / 100) ; // (+ X por ciento)
				break;
		}
		$result['L']['TRM'] = ceil( $result['L']['TRM'] ); // redondeamos hacia arriba el TRM de Standard

		// Calculo del TRM para Midi
		switch($manga->TRM_M_Tipo) {
			case 0: // TRM Fijo
				$result['M']['TRM']=$manga->TRM_M_Factor;
				break;
			case 1: // TRS + (segs o porcentaje)
				if ($manga->$TRM_M_Unit==="s") $result['M']['TRM']=$result['M']['TRS'] + $manga->TRM_M_Factor; // ( + X segundos )
				else $result['M']['TRM'] = $result['M']['TRS'] * ( (100+$manga->TRM_M_Factor) / 100) ; // (+ X por ciento)
				break;
		}
		$result['M']['TRM'] = ceil( $result['M']['TRM'] ); // redondeamos hacia arriba el TRM de Standard

		// Calculo del TRM para Small
		switch($manga->TRM_S_Tipo) {
			case 0: // TRM Fijo
				$result['S']['TRM']=$manga->TRM_S_Factor;
				break;
			case 1: // TRS + (segs o porcentaje)
				if ($manga->$TRM_S_Unit==="s") $result['S']['TRM']=$result['S']['TRS'] + $manga->TRM_S_Factor; // ( + X segundos )
				else $result['S']['TRM'] = $result['S']['TRS'] * ( (100+$manga->TRM_S_Factor) / 100) ; // (+ X por ciento)
				break;
		}
		$result['S']['TRM'] = ceil( $result['S']['TRM'] ); // redondeamos hacia arriba el TRM de Standard
		
		$this->myLogger->leave();
		
		return $result; // NOTICE: this IS NOT datagrid expected return format
	}
}

?>