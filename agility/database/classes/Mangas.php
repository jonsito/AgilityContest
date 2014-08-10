<?php

require_once("DBObject.php");

class Mangas extends DBObject {
	protected $jornada;
	
	/* copia de la estructura de la base de datos, para ahorrar consultas */
	public $tipo_manga= array(
			array ( 0,'',''),
			array( 1, 'Manga sin tipo definido', '-'),
			array( 2, 'Ronda de Pre-Agility', 'P.A.'),
			array( 3, 'Agility Grado I Manga 1', 'GI'),
		 	array( 4, 'Agility Grado I Manga 2', 'GI'),
			array( 5, 'Agility Grado II', 'GII'),
			array( 6, 'Agility Grado III', 'GIII'),
			array( 7, 'Agility Abierta (Open)', '-'),
			array( 8, 'Agility Equipos (3 mejores)', '-'),
			array( 9, 'Agility Equipos (Conjunta)', '-'),
			array( 10,'Jumping Grado II', 'GII'),
			array( 11,'Jumping Grado III', 'GIII'),
			array( 12,'Jumping Abierta (Open)', '-'),
			array( 13,'Jumping por Equipos (3 mejores)', '-'),
			array( 14,'Jumping por Equipos (Conjunta)', '-'),
			array( 15,'Ronda K.O.', '-'),
			array( 16,'Ronda de Exhibición', '-')	
	);
	
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
	
	/**
	 * 
	 * @param {integer} $tipo ID del tipo manga (tabla 'Tipo_Manga')
	 * @param {string} $grado valor asociado al grado de la manga de la ID dada
	 * @return {string} empty on success, else error 
	 */
	function insert($tipo,$grado) {
		$this->myLogger->enter();
		$j=$this->jornada;
		// si la manga existe no hacer nada; si no existe crear manga
		$res=$this->__selectObject(
				"count(*) AS Result", 
				"Mangas", 
				"( Jornada=$j ) AND  ( Tipo=$tipo ) AND ( Grado='$grado' )"
		);
		if ($res->Result>0){
			$this->myLogger->info("Jornada:".$this->jornada." Manga: $tipo already exists. exit OK");
			return "";
		}
		$str="INSERT INTO Mangas ( Jornada , Tipo, Grado ) VALUES ($j,$tipo,'$grado')";
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
		$trs_l_unit = http_request("TRS_L_Unit","s","s",false);
		$trs_m_unit = http_request("TRS_M_Unit","s","s",false);
		$trs_s_unit = http_request("TRS_S_Unit","s","s",false);
		// Unidad TRM
		$trm_l_unit = http_request("TRM_L_Unit","s","s",false);
		$trm_m_unit = http_request("TRM_M_Unit","s","s",false);
		$trm_s_unit = http_request("TRM_S_Unit","s","s",false);
		// Jueces y observaciones
		$juez1 = http_request("Juez1","s",null,false);
		$juez2 = http_request("Juez2","s",null,false);
		$observaciones = http_request("Observaciones","s",null,false);
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
	 * @param {integer} tipo ID a sociado a tipo manga
	 * @return "" on success; null on error
	 */
	function delete($tipo) {
		$this->myLogger->enter();
		if ( ($tipo<=0) || ($tipo>16) ) return $this->error("Invalid value for 'Tipo'"); 
		// si la manga existe, borrarla; si no existe, no hacer nada
		$str="DELETE FROM Mangas WHERE ( Jornada = ".$this->jornada." ) AND  ( Tipo = ".$tipo." )";
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
			$str="SELECT Mangas.ID AS ID, Mangas.Tipo AS Tipo, Tipo_Manga.Descripcion AS Descripcion
			FROM Mangas,Tipo_Manga
			WHERE ( ( Jornada = ".$this->jornada." ) AND ( Mangas.Tipo = Tipo_Manga.ID ) )
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
	 * creacion / borrado de mangas asociadas a una jornada
	 * @param {integer} $id ID de jornada
	 * @param {integer} $grado1 la jornada tiene(1) o no (0) mangas de grado 1
	 * @param {integer} $grado2 la jornada tiene (1) o no (0) mangas de grado 2
	 * @param {integer} $grado3 la jornada tiene (1) o no (0) mangas de grado 3
	 * @param {integer} $open la jornada tiene (1) o no (0) una prueba abierta (Open)
	 * @param {integer} $equipos3 la jornada tiene (1) o no (0) una manga por equipos (3 de 4)
	 * @param {integer} $equipos4 la jornada tiene (1) o no (0) una manga por equipos (conjunta)
	 * @param {integer} $preagility la jornada tiene (1) o no (0) manga de preagility
	 * @param {integer} $ko la jornada contiene (1) o no (0) una prueba k0
	 * @param {integer} $exhibicion la jornada tiene (1) o no (0) mangas de exhibicion
	 * @param {integer} $otras la jornada contiene (1) o no (0) mangas no definidas
	 * // TODO: handle ko, exhibicion and otras
	 */
	function prepareMangas($id,$grado1,$grado2,$grado3,$open,$equipos3,$equipos4,$preagility,$ko,$exhibicion,$otras) {
		$this->myLogger->enter();
	
		if ($grado1) { 	$mangas->insert(3,'GI'); $mangas->insert(4,'GI');		}
		else { $mangas->delete(3);	$mangas->delete(4); }
	
		if ($grado2) { $mangas->insert(5,'GII'); $mangas->insert(10,'GII'); }
		else { $mangas->delete(5); $mangas->delete(10); }
	
		if ($grado3) { $mangas->insert(6,'GIII'); $mangas->insert(11,'GIII'); }
		else { $mangas->delete(6);	$mangas->delete(11); }
	
		if ($open) { $mangas->insert(7,'-'); $mangas->insert(12,'-'); }
		else { $mangas->delete(7);	$mangas->delete(12); }
	
		if ($equipos3) {	$mangas->insert(8,'-');	$mangas->insert(13,'-');	}
		else { $mangas->delete(8);	$mangas->delete(13);	}
	
		if ($equipos4) {	$mangas->insert(9,'-');	$mangas->insert(14,'-');	}
		else { $mangas->delete(9);	$mangas->delete(14);	}
	
		if ($preagility) { $mangas->insert(2,'P.A.'); }
		else { $mangas->delete(2); }
	
		if ($exhibicion) { $mangas->insert(16,'-');}
		else { $mangas->delete(16); }
		// TODO: Decidir que se hace con las mangas 'otras'
		// TODO: las mangas KO hay que crearlas dinamicamente en funcion del numero de participantes
		$this->myLogger->leave();
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
			$result[$c]['Dist']=0; $result[$c]['Obst']=0 ;$result[$c]['TRS']=0;$result[$c]['TRM']=0; 
		}
		// obtenemos los datos de la manga
		$manga=$this->selectByID($manga);
		if (!$manga) return $this->error($this->errormsg);
		
		// distancias y obstáculos (necesaria para el calculo de la velocidad
		// $this->myLogger->trace("Calculando distancias y obstaculos");
		$result['L']['Dist']=$manga->Dist_L; $result['L']['Obst']=$manga->Obst_L;
		$result['M']['Dist']=$manga->Dist_M; $result['M']['Obst']=$manga->Obst_M;
		$result['S']['Dist']=$manga->Dist_S; $result['S']['Obst']=$manga->Obst_S;

		// Calculo del TRS para standard
		// $this->myLogger->trace("Calculando TRS Standard");
		switch ($manga->TRS_L_Tipo) {
			case 0: // tiempo fijo
				$result['L']['TRS']=$manga->TRS_L_Factor;
				break;
			case 1: // mejor tiempo 
				if ($manga->TRS_L_Unit==="s") $result['L']['TRS']=$tiempos['L'][0] + $manga->TRS_L_Factor; // ( + X segundos )
				else $result['L']['TRS']=$tiempos['L'][0] * ( (100+$manga->TRS_L_Factor) / 100) ; // (+ X por ciento)
				break;
			case 2: // media de los tres mejores tiempos
				$media=($tiempos['L'][0]+$tiempos['L'][1]+$tiempos['L'][2]) / 3;
				if ($manga->TRS_L_Unit==="s") $result['L']['TRS']=$media + $manga->TRS_L_Factor; // ( + X segundos )
				else $result['L']['TRS'] = $media * ( (100+$manga->TRS_L_Factor) / 100) ; // (+ X por ciento)
				break;
		}
		$result['L']['TRS'] = ceil( $result['L']['TRS'] ); // redondeamos hacia arriba el TRS de Standard
		
		// Calculo del TRS para Medium
		// $this->myLogger->trace("Calculando TRS Midi");
		switch ($manga->TRS_M_Tipo) {
			case 0: // tiempo fijo
				$result['M']['TRS']=$manga->TRS_M_Factor;
				break;
			case 1: // mejor tiempo
				if ($manga->TRS_M_Unit==="s") $result['M']['TRS']=$tiempos['M'][0] + $manga->TRS_M_Factor; // ( + X segundos )
				else $result['M']['TRS']=$tiempos['M'][0] * ( (100+$manga->TRS_M_Factor) / 100) ; // (+ X por ciento)
				break;
			case 2: // media de los tres mejores tiempos
				$media=($tiempos['M'][0]+$tiempos['M'][1]+$tiempos['M'][2]) / 3;
				if ($manga->TRS_M_Unit==="s") $result['M']['TRS']=$media + $manga->TRS_M_Factor; // ( + X segundos )
				else $result['M']['TRS'] = $media * ( (100+$manga->TRS_M_Factor) / 100) ; // (+ X por ciento)
				break;
			case 3: // Referencia de tiempo el TRS de Standard
				if ($manga->TRS_M_Unit==="s") $result['M']['TRS']=$result['L']['TRS'] + $manga->TRS_M_Factor; // ( + X segundos )
				else $result['M']['TRS'] = $result['L']['TRS'] * ( (100+$manga->TRS_M_Factor) / 100) ; // (+ X por ciento)
				break;
		}
		$result['M']['TRS'] = ceil( $result['M']['TRS'] ); // redondeamos hacia arriba el TRS de Midi

		// Calculo del TRS para Small
		// $this->myLogger->trace("Calculando TRS Mini");
		switch ($manga->TRS_S_Tipo) {
			case 0: // tiempo fijo
				$result['S']['TRS']=$manga->TRS_S_Factor;
				break;
			case 1: // mejor tiempo
				if ($manga->TRS_S_Unit==="s") $result['S']['TRS']=$tiempos['S'][0] + $manga->TRS_S_Factor; // ( + X segundos )
				else $result['S']['TRS']=$tiempos['S'][0] * ( (100+$manga->TRS_S_Factor) / 100) ; // (+ X por ciento)
				break;
			case 2: // media de los tres mejores tiempos
				$media=($tiempos['S'][0]+$tiempos['S'][1]+$tiempos['S'][2]) / 3;
				if ($manga->TRS_S_Unit==="s") $result['S']['TRS']=$media + $manga->TRS_S_Factor; // ( + X segundos )
				else $result['S']['TRS'] = $media * ( (100+$manga->TRS_S_Factor) / 100) ; // (+ X por ciento)
				break;
			case 3: // Referencia de tiempo el TRS de Standard
				if ($manga->TRS_S_Unit==="s") $result['S']['TRS']=$result['L']['TRS'] + $manga->TRS_S_Factor; // ( + X segundos )
				else $result['S']['TRS'] = $result['L']['TRS'] * ( (100+$manga->TRS_S_Factor) / 100) ; // (+ X por ciento)
				break;
			case 4: // Referencia de tiempo el TRS de Midi
				if ($manga->TRS_S_Unit==="s") $result['S']['TRS']=$result['M']['TRS'] + $manga->TRS_S_Factor; // ( + X segundos )
				else $result['S']['TRS'] = $result['M']['TRS'] * ( (100+$manga->TRS_S_Factor) / 100) ; // (+ X por ciento)
				break;
		}
		$result['S']['TRS'] = ceil( $result['S']['TRS'] ); // redondeamos hacia arriba el TRS de Midi

		// Calculo del TRM para Standard
		// $this->myLogger->trace("Calculando TRM Standard");
		switch($manga->TRM_L_Tipo) {
			case 0: // TRM Fijo
				$result['L']['TRM']=$manga->TRM_L_Factor;
				break;
			case 1: // TRS + (segs o porcentaje)
				if ($manga->TRM_L_Unit==="s") $result['L']['TRM']=$result['L']['TRS'] + $manga->TRM_L_Factor; // ( + X segundos )
				else $result['L']['TRM'] = $result['L']['TRS'] * ( (100+$manga->TRM_L_Factor) / 100) ; // (+ X por ciento)
				break;
		}
		$result['L']['TRM'] = ceil( $result['L']['TRM'] ); // redondeamos hacia arriba el TRM de Standard

		// Calculo del TRM para Midi
		// $this->myLogger->trace("Calculando TRM Midi");
		switch($manga->TRM_M_Tipo) {
			case 0: // TRM Fijo
				$result['M']['TRM']=$manga->TRM_M_Factor;
				break;
			case 1: // TRS + (segs o porcentaje)
				if ($manga->TRM_M_Unit==="s") $result['M']['TRM']=$result['M']['TRS'] + $manga->TRM_M_Factor; // ( + X segundos )
				else $result['M']['TRM'] = $result['M']['TRS'] * ( (100+$manga->TRM_M_Factor) / 100) ; // (+ X por ciento)
				break;
		}
		$result['M']['TRM'] = ceil( $result['M']['TRM'] ); // redondeamos hacia arriba el TRM de Standard

		// Calculo del TRM para Small
		// $this->myLogger->trace("Calculando TRM Mini");
		switch($manga->TRM_S_Tipo) {
			case 0: // TRM Fijo
				$result['S']['TRM']=$manga->TRM_S_Factor;
				break;
			case 1: // TRS + (segs o porcentaje)
				if ($manga->TRM_S_Unit==="s") $result['S']['TRM']=$result['S']['TRS'] + $manga->TRM_S_Factor; // ( + X segundos )
				else $result['S']['TRM'] = $result['S']['TRS'] * ( (100+$manga->TRM_S_Factor) / 100) ; // (+ X por ciento)
				break;
		}
		$result['S']['TRM'] = ceil( $result['S']['TRM'] ); // redondeamos hacia arriba el TRM de Standard
		
		// Calculo de la velocidad 
		if ($result["L"]["TRS"]==0) $result["L"]["Vel"]= number_format(0,2); // fix divide-by-zero if no data
		else $result["L"]["Vel"] = number_format( $result["L"]["Dist"] / $result["L"]["TRS"], 2);
		if ($result["M"]["TRS"]==0) $result["M"]["Vel"]= number_format(0,2); // fix divide-by-zero if no data
		else $result["M"]["Vel"] = number_format( $result["M"]["Dist"] / $result["M"]["TRS"], 2);
		if ($result["S"]["TRS"]==0) $result["S"]["Vel"]= number_format(0,2); // fix divide-by-zero if no data
		else $result["S"]["Vel"] = number_format( $result["S"]["Dist"] / $result["S"]["TRS"], 2);
				
		$this->myLogger->leave();
		// $this->myLogger->trace(print_r($result,true));
		return $result; // NOTICE: this IS NOT datagrid expected return format
	}
	
	function getTRS($manga) {
		$this->myLogger->enter();
		// extraemos los tres mejores tiempos de cada categoria
		$str="SELECT Categoria, Tiempo, ( 5*Faltas + 5*Rehuses + 5*Tocados + 100*Eliminado + 200*NoPresentado ) AS PRecorrido 
		FROM Resultados
		WHERE ( Manga = $manga ) 
		ORDER BY Categoria ASC , PRecorrido ASC , Tiempo ASC ";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		
		$count=0;		// orden de clasificacion en funcion de la categoria
		// preparamos el almacen de los tres mejores tiempos de cada categoria
		$tiempos=array();
		$tiempos["-"]=array();
		$tiempos["L"]=array();
		$tiempos["M"]=array();
		$tiempos["S"]=array();
		$tiempos["T"]=array();
		foreach ( array("-","L","M","S","T") as $c ) { $tiempos[$c][0]=0;$tiempos[$c][1]=0;$tiempos[$c][2]=0; }
		
		// analizamos el resultado de la bbdd y guardamos los tres mejores tiempos de cada categoria en $tiempos
		$lastCategoria="*";
		$data=array();
		while($row = $rs->fetch_object()) {
			if ( $row->Categoria !== $lastCategoria ) {
				$count=0;
				$lastCategoria=$row->Categoria;
			}
			if ($count<3) $tiempos[$lastCategoria][$count++]=$row->Tiempo;
			array_push($data,$row);
		}
		$rs->free();
		$d= $this->datosTRS($manga,$tiempos);
		$this->myLogger->enter();
		$res=array();
		// reformulamos el resultado para que tenga forma de un form de jquery
		// TODO: handle "Tiny" and "Any" Categories
		$res["DIST_L"]=	strval($d["L"]["Dist"]); $res["DIST_M"]=strval($d["M"]["Dist"]);$res["DIST_S"]=strval($d["S"]["Dist"]);
		$res["OBST_L"]=strval($d["L"]["Obst"]); $res["OBST_M"]=strval($d["M"]["Obst"]);	$res["OBST_S"]=strval($d["S"]["Obst"]);
		$res["TRS_L"]=strval($d["L"]["TRS"]);	$res["TRS_M"]=strval($d["M"]["TRS"]);	$res["TRS_S"]=strval($d["S"]["TRS"]);
		$res["TRM_L"]=strval($d["L"]["TRM"]);	$res["TRM_M"]=strval($d["M"]["TRM"]);	$res["TRM_S"]=strval($d["S"]["TRM"]);
		$res["VEL_L"]=strval($d["L"]["Vel"]);	$res["VEL_M"]=strval($d["M"]["Vel"]);	$res["VEL_S"]=strval($d["S"]["Vel"]);
		return $res;
	}
}

?>