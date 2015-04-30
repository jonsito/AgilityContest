<?php
/*
Mangas.php

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



require_once("DBObject.php");

class Mangas extends DBObject {
	protected $jornada;
	protected $pruebaObj;
	
	/* copia de la estructura de la base de datos, para ahorrar consultas */
	public static $tipo_manga= array(
		0 =>	array ( 0,'Nombre largo','Grado corto','Nombre corto','Grado largo'),
		1 =>	array( 1, 'Pre-Agility Manga 1', 		'P.A.',	'PreAgility 1',	'Pre-Agility'),
		2 => 	array( 2, 'Pre-Agility Manga 2', 		'P.A.',	'PreAgility 2',	'Pre-Agility'),
		3 =>	array( 3, 'Agility Grado I Manga 1',	'GI',	'Agility-1 GI',	'Grado I'),
		4 => 	array( 4, 'Agility Grado I Manga 2',	'GI',	'Agility-2 GI',	'Grado I'),
		5 =>	array( 5, 'Agility Grado II', 			'GII',	'Agility GII',	'Grado II'),
		6 =>	array( 6, 'Agility Grado III', 			'GIII',	'Agility GIII',	'Grado III'),
		7 =>	array( 7, 'Agility Abierta (Open)', 	'-',	'Agility Open',	'Abierta'),
		8 =>	array( 8, 'Agility Equipos (3 mejores)','-',	'Agility Eq.',	'Abierta'),
		9 =>	array( 9, 'Agility Equipos (Conjunta)', '-',	'Agility Eq.',	'Abierta'),
		10 =>	array( 10,'Jumping Grado II',			'GII',	'Jumping GII',	'Grado II'),
		11 =>	array( 11,'Jumping Grado III',			'GIII',	'Jumping GIII',	'Grado III'),
		12 =>	array( 12,'Jumping Abierta (Open)',		'-',	'Jumping Open',	'Abierta'),
		13 =>	array( 13,'Jumping Equipos (3 mejores)','-','Jumping Eq.',	'Abierta'),
		14 =>	array( 14,'Jumping Equipos (Conjunta)', '-','Jumping Eq.',	'Abierta'),
		15 =>	array( 15,'Ronda K.O.', 				'-',	'Ronda K.O.',	'Abierta'),
		16 =>	array( 16,'Manga especial', 			'-',	'Manga Especial','Abierta')	
	);
	
	/* tabla para obtener facilmente la manga complementaria a una manga dada */
	public static $manga_hermana= array(
		0,	/* 0,'','' */
		2,	/* 1, 'Pre-Agility Manga 1', 'P.A.' */ // notice that in 1 manga mode there is no sister
		1,	/* 2, 'Pre-Agility Manga 2', 'P.A.' */
		4,	/* 3, 'Agility Grado I Manga 1', 'GI' */
		3,	/* 4, 'Agility Grado I Manga 2', 'GI' */
		10,	/* 5, 'Agility Grado II', 'GII' */
		11,	/* 6, 'Agility Grado III', 'GIII' */
		12,	/* 7, 'Agility Abierta (Open)', '-' */
		13,	/* 8, 'Agility Equipos (3 mejores)', '-' */
		14,	/* 9, 'Agility Equipos (Conjunta)', '-' */
		5,	/* 10,'Jumping Grado II', 'GII' */
		6,	/* 11,'Jumping Grado III', 'GIII' */
		7,	/* 12,'Jumping Abierta (Open)', '-' */
		8,	/* 13,'Jumping Equipos (3 mejores)', '-' */
		9,	/* 14,'Jumping Equipos (Conjunta)', '-' */
		0,	/* 15,'Ronda K.O.', '-' */
		0	/* 16,'Manga Especial', '-' */
	);
	
	public static $manga_recorridos= array(
		0 => array(/*RSCE*/ 'Large / Medium / Small'	,'L/M/S',/*RFEC*/	'Recorridos separados',	'L/M/S/T' ), // recorridos separados por categoria
		1 => array(/*RSCE*/ 'Large / Medium + Small'	,'L/M+S',/*RFEC*/	'Large+Medium / Small+Tiny','L+M/S+T'), // dos recorridos: large y M+S
		2 => array(/*RSCE*/'Large + Medium + Small'		,'L+M+S', /*RFEC*/	'Recorrido comun',		'L+M+S+T')  // recorrido unico conjunto
 	);
	
	public static $manga_modes= array (
		0 => array('Large','L'),
		1 => array('Medium','M'),
		2 => array('Small','S'),
		3 => array('Medium + Small','MS'),
		4 => array('Conjunta L+M+S','LMS'),
		5 => array('Tiny','T'),
		6 => array('Large + Medium','LM'),
		7 => array('Small + Tiny','ST'),
		8 => array('Conjunta L+M+S+T','LMST')
	);
	
	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {integer} $jornada jornada ID
	 * @throws Exception if cannot contact database or invalid jornada ID
	 */
	function __construct($file,$jornada) {
		parent::__construct($file);
		if ($jornada<=0) {
			$this->errormsg="Manga::Construct invalid jornada ID";
			throw new Exception($this->errormsg);
		}
		$this->jornada=$jornada;
		$this->pruebaObj=$this->__selectObject("Pruebas.ID,Pruebas.RSCE,Pruebas.Selectiva",
										"Pruebas,Jornadas",
										"(Pruebas.ID=Jornadas.Prueba) AND (Jornadas.ID=$jornada)");
	}
	
	/**
	 * inserta una manga en la jornada
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
		if(!is_object($res))
			return $this->error("Cannot get info on Mangas for Jornada:$j");
		
		if ($res->Result>0){
			$this->myLogger->info("Jornada:$j Manga:$tipo already exists");
		} else {
			$observaciones = http_request("Observaciones","s","");
			$str="INSERT INTO Mangas ( Jornada,Tipo,Grado,Observaciones,Orden_Salida ) VALUES ( $j,$tipo,'$grado','$observaciones','BEGIN,END' )";
			$rs=$this->query($str);
			if (!$rs) return $this->error($this->conn->error); 
		}

		/* si la prueba es selectiva ajustamos tipo de recorrido, y TRS */
		if ($this->pruebaObj->Selectiva!=0) {
			$doSelectiva=false;
			if ( ($this->pruebaObj->RSCE==0) && ($tipo==6) ) $doSelectiva=true; // RSCE Agility Grado III
			if ( ($this->pruebaObj->RSCE==0) && ($tipo==11) ) $doSelectiva=true; // RSCE Jumping Grado III
			if ( ($this->pruebaObj->RSCE!=0) && ($tipo==5) ) $doSelectiva=true; // RFEC Jumping Grado II
			if ( ($this->pruebaObj->RSCE!=0) && ($tipo==10) ) $doSelectiva=true; // RFEC Jumping Grado II
			if ($doSelectiva) {
				$str="UPDATE Mangas
				SET Recorrido=0,
				TRS_L_Tipo=1, TRS_M_Tipo=1, TRS_S_Tipo=1, TRS_T_Tipo=1,
				TRS_L_Factor=10, TRS_M_Factor=10, TRS_S_Factor=10, TRS_T_Factor=10,
				TRS_L_Unit='%', TRS_M_Unit='%', TRS_S_Unit='%', TRS_T_Unit='%'
				WHERE ( Jornada=$j ) AND  ( Tipo=$tipo ) AND ( Grado='$grado' )";
				$rs=$this->query($str);
				if (!$rs) return $this->error($this->conn->error);
			}
		}

		$this->myLogger->leave();
		return "";
	}
	
	function update($mangaid) {
		$this->myLogger->enter();
		if ($mangaid <=0) return $this->error("Invalid Manga ID"); 
		// preparamos la query SQL
		$sql= "UPDATE Mangas SET
 			Recorrido=? ,
			Dist_L=? , Obst_L=? , Dist_M=? , Obst_M=? , Dist_S=? , Obst_S=? , Dist_T=? , Obst_T=? ,
			TRS_L_Tipo=? , TRS_L_Factor=? , TRS_L_Unit=? , TRM_L_Tipo=? , TRM_L_Factor=? , TRM_L_Unit=? ,
			TRS_M_Tipo=? , TRS_M_Factor=? , TRS_M_Unit=? , TRM_M_Tipo=? , TRM_M_Factor=? , TRM_M_Unit=? ,
			TRS_S_Tipo=? , TRS_S_Factor=? , TRS_S_Unit=? , TRM_S_Tipo=? , TRM_S_Factor=? , TRM_S_Unit=? ,
			TRS_T_Tipo=? , TRS_T_Factor=? , TRS_T_Unit=? , TRM_T_Tipo=? , TRM_T_Factor=? , TRM_T_Unit=? ,
			Juez1=? , Juez2=? ,
			Observaciones=?
			WHERE (ID=?)";
		
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param(
			'iiiiiiiiiiisiisiisiisiisiisiisiisiisi',
			$recorrido,
			$dist_l,	$obst_l,	$dist_m,	$obst_m,	$dist_s,	$obst_s, 	$dist_t,	$obst_t,// distancias y obstaculos
			$trs_l_tipo,	$trs_l_factor,	$trs_l_unit,	$trm_l_tipo,	$trm_l_factor,	$trm_l_unit,// TRS y TRM Large
			$trs_m_tipo,	$trs_m_factor,	$trs_m_unit,	$trm_m_tipo,	$trm_m_factor,	$trm_m_unit,// TRS Y TRM Medium
			$trs_s_tipo,	$trs_s_factor,	$trs_s_unit,	$trm_s_tipo,	$trm_s_factor,	$trm_s_unit,// TRS y TRM Small
			$trs_t_tipo,	$trs_t_factor,	$trs_t_unit,	$trm_t_tipo,	$trm_t_factor,	$trm_t_unit,// TRS y TRM Small
			$juez1, 		$juez2, 		$observaciones,	$id		
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
		* Observaciones
		* Orden_Salida (se modifica en otro sitio)
		*/
		$id			= $mangaid;
		$recorrido	= http_request("Recorrido","i",0);
		$tipo	= http_request("Tipo","i",0);
		// distancias
		$dist_l = http_request("Dist_L","i",0);
		$dist_m = http_request("Dist_M","i",0);
		$dist_s = http_request("Dist_S","i",0);
		$dist_t = http_request("Dist_T","i",0);
		// obstaculos
		$obst_l = http_request("Obst_L","i",0); 
		$obst_m = http_request("Obst_M","i",0);
		$obst_s = http_request("Obst_S","i",0);
		$obst_t = http_request("Obst_T","i",0);
		// tipo TRS
		$trs_l_tipo = http_request("TRS_L_Tipo","i",0);
		$trs_m_tipo = http_request("TRS_M_Tipo","i",0);
		$trs_s_tipo = http_request("TRS_S_Tipo","i",0);
		$trs_t_tipo = http_request("TRS_T_Tipo","i",0);
		// tipo TRM
		$trm_l_tipo = http_request("TRM_L_Tipo","i",0);
		$trm_m_tipo = http_request("TRM_M_Tipo","i",0);
		$trm_s_tipo = http_request("TRM_S_Tipo","i",0);
		$trm_t_tipo = http_request("TRM_T_Tipo","i",0);
		// factor TRS
		$trs_l_factor = http_request("TRS_L_Factor","i",0);
		$trs_m_factor = http_request("TRS_M_Factor","i",0);
		$trs_s_factor = http_request("TRS_S_Factor","i",0);
		$trs_t_factor = http_request("TRS_T_Factor","i",0);
		// factor TRM
		$trm_l_factor = http_request("TRM_L_Factor","i",0);
		$trm_m_factor = http_request("TRM_M_Factor","i",0);
		$trm_s_factor = http_request("TRM_S_Factor","i",0);
		$trm_t_factor = http_request("TRM_T_Factor","i",0);
		// Unidad TRS
		$trs_l_unit = http_request("TRS_L_Unit","s","s",false);
		$trs_m_unit = http_request("TRS_M_Unit","s","s",false);
		$trs_s_unit = http_request("TRS_S_Unit","s","s",false);
		$trs_t_unit = http_request("TRS_T_Unit","s","s",false);
		// Unidad TRM
		$trm_l_unit = http_request("TRM_L_Unit","s","s",false);
		$trm_m_unit = http_request("TRM_M_Unit","s","s",false);
		$trm_s_unit = http_request("TRM_S_Unit","s","s",false);
		$trm_t_unit = http_request("TRM_T_Unit","s","s",false);
		// Jueces y observaciones
		$juez1 = http_request("Juez1","i",1);
		$juez2 = http_request("Juez2","i",1);
		$observaciones = http_request("Observaciones","s",null,false);
		
		// ejecutamos el query
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error); 
		$stmt->close();
		
		// actualizamos el campo "Recorrido" de las Mangas gemelas
		$tipogemelo=Mangas::$manga_hermana[$tipo];
		$sql="UPDATE Mangas SET Recorrido=$recorrido WHERE ( Jornada={$this->jornada} ) AND (Tipo=$tipogemelo)";
		$res=$this->query($sql);
		if (!$res) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}
	
	function shareJuez() {
		$juez1 = http_request("Juez1","i",1);
		$juez2 = http_request("Juez2","i",1);
		$sql="UPDATE Mangas SET Juez1=$juez1, Juez2=$juez2 WHERE ( Jornada={$this->jornada} )";		
		$res=$this->query($sql);
		if (!$res) return $this->error($this->conn->error); 
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
		$str="DELETE FROM Mangas WHERE ( Jornada = {$this->jornada} ) AND  ( Tipo = $tipo )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}
	
	function deleteByID($id) {
		$this->myLogger->enter();
		if ( ($id<=0) ) return $this->error("Invalid Manga ID: $id"); 
		// si la manga existe, borrarla; si no existe, no hacer nada
		$str="DELETE FROM Mangas WHERE ( Jornada = {$this->jornada} ) AND  ( ID = $id )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * recupera los datos de una manga determinada
	 * @param {int} $id id de la manga
	 * @return {array} null on error, data on success
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Manga ID"); 
		// second query to retrieve $rows starting at $offset
		$result=$this->__getObject("Mangas",$id);
		$result->Manga=$id;
		$result->Jornada=$this->jornada;
		$result->Operation="update";
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Enumera las mangas de una jornada
	 * @return null on error, result on success
	 */
	function selectByJornada() {
		$this->myLogger->enter();
		$result=$this->__select(
			/* SELECT */"Mangas.ID AS ID, Mangas.Tipo AS Tipo, Mangas.Recorrido AS Recorrido,Tipo_Manga.Grado AS Grado, Tipo_Manga.Descripcion AS Descripcion",
			/* FROM */ "Mangas,Tipo_Manga",
			/* WHERE */ "( ( Jornada = {$this->jornada} ) AND ( Mangas.Tipo = Tipo_Manga.ID ) )",
			/* ORDER */ "Descripcion ASC",
			/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}

	/**
	 * Obtiene la manga "hermana" de la que tiene el ID dado
	 * @param {integer} $id ID de la manga
	 * @return array[0:mangaid,1:mangahermanaid]
	 */
	function getHermanas($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Manga ID");
		// second query to retrieve $rows starting at $offset
		$result=$this->__getObject("Mangas",$id);
		if (!is_object($result)) return $this->error("Cannot locate Manga with ID=$id");
		$tipo=Mangas::$manga_hermana[$result->Tipo];
		if ($tipo==0) {
			$this->myLogger->info("La manga:$id de tipo:{$result->Tipo} no tiene hermana asociada");
			return array($result,null); 
		}
		// Obtenemos __Todas__ las mangas de esta jornada que tienen el tipo buscado ninguna, una o hasta 8(k.O.)
		$result2=$this->__select("*","Mangas","( Jornada={$this->jornada} ) AND ( Tipo=$tipo)","","");
		if (!is_array($result2)) {
			// inconsistency error muy serio 
			return $this->error("Falta la manga hermana de tipo:$tipo para manga:$id de tipo:{$result->Tipo}");
		}
		$hermanas=array();
		array_push($hermanas,$result); // manga original as index 0
		foreach ($result2['rows'] as $index => $item) {
			// iterate on every sisters found, converting it to Objects
			array_push($hermanas,json_decode(json_encode($item), FALSE));
		}
		$this->myLogger->leave();
		return $hermanas;
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
	 * @param {integer} $preagility la jornada tiene (1) o no (0) manga de preagility a una vuelta
	 * @param {integer} $preagility2 la jornada tiene (1) o no (0) mangas de preagility a dos vueltas
	 * @param {integer} $ko la jornada contiene (1) o no (0) una prueba k0
	 * @param {integer} $especial la jornada tiene (1) o no (0) mangas especial a una vuelta
	 * @param {integer} $observaciones nombre con el que se denominara la manga especial
	 * // TODO: handle ko, exhibicion and otras
	 */
	function prepareMangas($id,$grado1,$grado2,$grado3,$open,$equipos3,$equipos4,$preagility,$preagility2,$ko,$especial,$observaciones) {
		$this->myLogger->enter();

		/*  0,'','' */
		/* 1, 'Pre Agility (una manga)', 'P.A.' */
		/* 2, 'Pre Agility (dos mangas)', 'P.A.' */
		// truco para discernir si el pre agility tiene una manga o dos
		if ($preagility2) { $this->insert(1,'P.A.'); $this->insert(2,'P.A.');}
		else {
			$this->delete(2);
			if ($preagility) { $this->insert(1,'P.A.'); }
			else { $this->delete(1); }
		}
		
		/* 3, 'Agility Grado I Manga 1', 'GI' */
		/* 4, 'Agility Grado I Manga 2', 'GI' */
		if ($grado1) { 	$this->insert(3,'GI'); $this->insert(4,'GI');		}
		else { $this->delete(3);	$this->delete(4); }

		/* 5, 'Agility Grado II', 'GII' */
		/* 10,'Jumping Grado II', 'GII' */
		if ($grado2) { $this->insert(5,'GII'); $this->insert(10,'GII'); }
		else { $this->delete(5); $this->delete(10); }

		/* 6, 'Agility Grado III', 'GIII' */
		/* 11,'Jumping Grado III', 'GIII' */
		if ($grado3) { $this->insert(6,'GIII'); $this->insert(11,'GIII'); }
		else { $this->delete(6);	$this->delete(11); }

		/* 7, 'Agility Abierta (Open)', '-' */
		/* 12,'Jumping Abierta (Open)', '-' */
		if ($open) { $this->insert(7,'-'); $this->insert(12,'-'); }
		else { $this->delete(7);	$this->delete(12); }

		/* 8, 'Agility Equipos (3 mejores)', '-' */
		/* 13,'Jumping Equipos (3 mejores)', '-' */
		if ($equipos3) {	$this->insert(8,'-');	$this->insert(13,'-');	}
		else { $this->delete(8);	$this->delete(13);	}

		/* 9, 'Agility Equipos (Conjunta)', '-' */
		/* 14,'Jumping Equipos (Conjunta)', '-' */
		if ($equipos4) {	$this->insert(9,'-');	$this->insert(14,'-');	}
		else { $this->delete(9);	$this->delete(14);	}


		/* 16,'Ronda de Exhibición', '-' */
		if ($especial) { $this->insert(16,'-');}
		else { $this->delete(16); }

		/* 15,'Ronda K.O.', '-' */
		// TODO: las mangas KO hay que crearlas dinamicamente en funcion del numero de participantes
		
		$this->myLogger->leave();
	}
}

?>