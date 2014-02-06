<?php
require_once("DBObject.php");

class OrdenSalida extends DBObject {
	
	// tablas utilizadas para componer e insertar los dorsales en el string de orden de salida
	protected $default_orden = "BEGIN,TAG_-0,TAG_-1,TAG_L0,TAG_L1,TAG_M0,TAG_M1,TAG_S0,TAG_S1,TAG_T0,TAG_T1,END";
	protected $tags_orden = array ( // orden LargeMediumSmall/Tiny
			'-0' => 'TAG_-1',
			'-1' => 'TAG_L0',
			'L0' => 'TAG_L1',
			'L1' => 'TAG_M0',
			'M0' => 'TAG_M1',
			'M1' => 'TAG_S0',
			'S0' => 'TAG_S1',
			'S1' => 'TAG_T0',
			'T0' => 'TAG_T1',
			'T1' => 'END' 
	);

	/* use parent constructor and destructor */
	
	/**
	 * Retrieve Mangas.Orden_Salida
	 * @param unknown $manga
	 * @return {string} orden de salida. o "" si vacio
	 */
	function getOrden($manga) {
		$this->myLogger->enter();
		$sql = "SELECT Orden_Salida FROM Mangas WHERE ( ID=$manga )";
		$rs = $this->query ( $sql );
		if (!$rs) return $this->error($this->conn->error);
		$row = $rs->fetch_object ();
		$result = $row->Orden_Salida;
		$rs->free ();
		$this->myLogger->leave();
		return ($result===null)?"":$result;
	}
	
	/**
	 * Update Mangas.Orden_Salida with new value
	 * @param {integer} $manga manga ID
	 * @param {string} $orden new ordensalida
	 * @return {string} "" if success; null on error
	 */
	function setOrden($manga, $orden) {
		$this->myLogger->enter();
		$sql = "UPDATE Mangas SET Orden_Salida = '" . $orden . "' WHERE ( ID=$manga )";
		$rs = $this->query ($sql);
		// do not call $rs->free() as no resultset returned
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * coge el string con el orden de salida e inserta un elemento al final de su grupo
	 * Porsiaca lo intenta borrar previamente
	 * @param {string} $ordensalida 
	 * @param {integer} $dorsal
	 * @param {string[1]} $cat
	 * @param {integer[1]} $celo
	 * @return {string} nuevo orden de salida
	 */
	function insertIntoList($ordensalida, $dorsal, $cat, $celo) {
		$this->myLogger->enter();
		$this->myLogger->debug("inserting dorsal:$dorsal cat:$cat celo:$celo" );
		// lo borramos para evitar una posible doble insercion
		$str = "," . $dorsal . ",";
		$nuevoorden = str_replace ( $str, ",", $ordensalida );
		// componemos el tag que hay que insertar
		$myTag = $dorsal . "," . $this->tags_orden [$cat . $celo];
		// y lo insertamos en lugar que corresponde
		$result = str_replace ( $this->tags_orden [$cat . $celo], $myTag, $nuevoorden );
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Obtiene la lista (actualizada) de perros de una manga
	 *
	 * @param {int} $jornada ID de jornada
	 * @param {int} $manga ID de manga
	 * @return array[count,[data]] array ordenado segun ordensalida de datos de perros de una manga 
	 */
	function getData($jornada, $manga) {
		$this->myLogger->enter();
		// fase 0: vemos si ya hay una lista definida
		$ordensalida = $this->getOrden ( $manga );
		if ($ordensalida === "") { // no hay orden predefinido
		    // TODO: comprobamos si estamos en la segunda manga y usamos resultados como orden de salida
			$ordensalida = $this-> random ( $jornada, $manga, false );
		}
		$this->myLogger->debug("El orden de salida actual es $ordensalida" );
		// ok tenemos orden de salida. vamos a convertirla en un array asociativo
		$registrados = explode ( ",", $ordensalida );
		
		// fase 1: obtener los perros inscritos en la jornada
		$sql1 = "SELECT * FROM InscritosJornada WHERE ( Jornada=$jornada ) ORDER BY Categoria ASC , Celo ASC, Equipo, Orden";
		$rs1 = $this->query ( $sql1 );
		if (!$rs1) return $this->error($this->conn->error);
		
		// fase 2: obtener las categorias de perros que debemos aceptar
		$sql2 = "SELECT Tipo_Manga.Grado FROM Mangas,Tipo_Manga WHERE (Mangas.Tipo=Tipo_Manga.Tipo) AND ( ID=$manga )";
		$rs2 = $this->query( $sql2 );
		if (!$rs2) return $this->error($this->conn->error);
		$obj2 = $rs2->fetch_object();
		$rs2->free ();
		$grado = $obj2->Grado;
		
		// fase 3: crear el array y la lista de perros y contrastarlo con la tabla y orden registrado
		$data = array ();
		while ( $row = $rs1->fetch_object () ) {
			// only add to list when grado is '-' (Any) or grado matches requested
			if (($grado !== "-") && ($grado !== $row->Grado))
				continue;
			$idx = array_search ( $row->Dorsal, $registrados );
			// si dorsal en lista se inserta; si no esta en lista implica error de consistencia
			if ($idx === false)
				$this->myLogger->notice("El dorsal " . $row->Dorsal . " esta inscrito pero no aparece en el orden de salida" );
			else
				$registrados [$idx] = $row;
		}
		$rs1->free();
		
		// fase 4: construimos la tabla de resultados
		$count = 0;
		$data = array ();
		foreach ( $registrados as $item ) {
			// si es un objeto anyadimos el dorsal
			if (is_object ( $item )) {
				array_push ( $data, $item );
				$this->myLogger->debug("push:" . $item->Dorsal . " count:$count" );
				$count ++;
				continue;
			}
			// si es un string vemos si es un tag o un "hueco"
			if (is_string ( $item )) {
				if (strpos ( $item, "BEGIN" ) !== false) continue;
				if (strpos ( $item, "END" ) !== false) continue;
				if (strpos ( $item, "TAG_" ) !== false)	continue;
					// dorsal no registrado: error
				$this->myLogger->notice("El dorsal $item esta en el orden de salida, pero no esta inscrito" );
			}
		}
		// finally encode result and send to client
		$result = array ();
		$result ["total"] = $count;
		$result ["rows"] = $data;
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Reordena el orden de salida de una manga al azar
	 * @param  	{int} $jornada ID de jornada
	 * @param	{int} $manga ID de manga
	 * @return {string} nuevo orden de salida
	 */
	function random($jornada, $manga) {
		$this->myLogger->enter();
		// fase 0: establecemos los string iniciales en base al orden especificado
		$ordensalida = $this->default_orden;
		// fase 1: obtener los perros inscritos en la jornada
		$sql1 = "SELECT * FROM InscritosJornada WHERE ( Jornada=$jornada ) ORDER BY Categoria ASC , Celo ASC, Equipo, Orden";
		$rs1 = $this->query ($sql1 );
		if (!$rs1) return $this->error($this->conn->error);
		
		// fase 2: obtener las categorias de perros que debemos aceptar
		$sql2 = "SELECT Grado FROM Mangas,Tipo_Manga WHERE (Mangas.Tipo=Tipo_Manga.Tipo) AND ( ID=$manga )";
		$rs2 = $this->query ($sql2 );
		if (!$rs2) return $this->error($this->conn->error);
		$obj2 = $rs2->fetch_object ();
		$rs2->free ();
		$grado = $obj2->Grado;
		
		// fase 3: generar la lista de perros "ordenada" al azar
		while ( $row = $rs1->fetch_object () ) {
			// only add to list when grado is '-' (Any) or grado matches requested
			if (($grado !== "-") && ($grado !== $row->Grado))
				continue;
				// elaborate ordensalida
			$ordensalida = $this->insertIntoList ( $ordensalida, $row->Dorsal, $row->Categoria, $row->Celo );
		}
		$rs1->free ();
		
		// fase 4: almacenar el orden de salida en los datos de la manga
		$this->setOrden ( $manga, $ordensalida );
		
		// fase 5: limpieza y retorno de resultados
		$this->myLogger->leave();
		return $ordensalida;
	}
	
	/**
	 * Elimina un dorsal del orden de salida
	 * si esta inscrito indica error y devuelve lista actual
	 * @param {integer} $jornada ID de jornada
	 * @param {integer} $manga ID de manga
	 * @param {integer} $dorsal ID de dorsal
	 * @return {string} nuevo orden de salida
	 */
	function remove($jornada, $manga, $dorsal) {
		$this->myLogger->enter();
		// TODO: si el dorsal esta inscrito y la manga es compatible damos error ( no se deberia borrar )
		/*
		// fase: vemos si el perro esta inscrito
		$sql = "SELECT count (*) FROM InscritosJornada WHERE ( Jornada=$jornada ) AND ( Dorsal=$dorsal)";
		$rs = $this->query ( $sql );
		$row = $rs->fetch_row ();
		$inscrito = $row [0];
		$rs->free ();
		*/

		// recuperamos el orden de salida y borramos el perro indicado
		$ordensalida = $this->getOrden ( $manga );
		$str = "," . $dorsal . ",";
		$nuevoorden = str_replace ( $str, ",", $ordensalida );
		// guardamos nuevo orden de salida y retornamos
		$this->setOrden ( $manga, $nuevoorden );
		$this->myLogger->leave();
		return $nuevoorden;
	}
	
	/**
	 * Inserta un perro en la lista al final de su categoria
	 *
	 * Comprueba que el dorsal esta inscrito; si no, devuelve lista actual
	 * Si esta ya en la lista lo saca de donde esta
	 * Inserta el dorsal en el ultimo puesto de los perros de su misma categoria/celo	 
	 * @param {integer} $jornada ID de jornada
	 * @param {integer} $manga ID de manga
	 * @param {integer} $dorsal ID de dorsal
	 * @return {string} nuevo orden de salida
	 */
	function insert($jornada, $manga, $dorsal) {
		$this->myLogger->enter();
		// si el dorsal no esta inscrito en la jornada da error
		$sql = "SELECT * FROM InscritosJornada WHERE ( Jornada=$jornada ) AND ( Dorsal=$dorsal)";
		$rs = $this->query($sql );
		if (!$rs) return $this->error($this->conn->error);
		$perro = $rs->fetch_object ();
		$rs->free ();
		if ($perro === null) {
			return $this->error("El perro con dorsal $dorsal no figura inscrito en la jornada $jornada" );
		}
		
		// si la categoria del perro no es la correcta, indicamos error
		$sql2 = "SELECT Grado FROM Mangas,Tipo_Manga WHERE (Mangas.Tipo=Tipo_Manga.Tipo) AND ( ID=$manga )";
		$rs2 = $this->query( $sql2 );
		if (!$rs2) return $this->error($this->conn->error);
		$obj2 = $rs2->fetch_object ();
		$rs2->free ();
		$grado = $obj2->Grado;
		if (($grado !== '-') && ($grado !== $perro->grado)) {
			return $this->error("El grado del dorsal $dorsal ($perro->grado) no es compatible con el grado de la manga ($grado) " );
		}
		// recuperamos orden de salida
		$ordensalida = getOrden ( $manga );
		// obtener datos de categoria y celo para obtener el tag a buscar
		$ordensalida = insertIntoList ( $ordensalida, $perro->Dorsal, $perro->Categoria, $perro->Celo );
		// actualizamos orden de salida
		setOrden ( $manga, $ordensalida );
		// cerramos y salimos
		$this->myLogger->leave();
		return $ordensalida;
	}
	
	/**
	 * Intercambia el orden de dos dorsales siempre que esten consecutivos
	 * @param {integer} $jornada ID de jornada
	 * @param {integer} $manga ID de manga
	 * @param {integer} $dorsal1 Dorsal del primer perro
	 * @param {integer} $dorsal2 Dorsal del segundo perro
	 * @return {string} nuevo orden de salida
	 */
	function swap($jornada, $manga, $dorsal1, $dorsal2) {
		$this->myLogger->enter();
		// componemos strings
		$str1 = "," . $dorsal1 . "," . $dorsal2 . ",";
		$str2 = "," . $dorsal2 . "," . $dorsal1 . ",";
		// recuperamos el orden de salida
		$ordensalida = getOrden ( $manga );
		if ($ordensalida === "") $nuevoorden=""; // no change
			// si encontramos str1 lo substituimos por str2
		else if (strpos ( $ordensalida, $str1 ) !== false)
			$nuevoorden = str_replace ( $str1, $str2, $ordensalida );
			// si encontramos str2 lo substituimos por str1
		else if (strpos ( $ordensalida, $str2 ) !== false)
			$nuevoorden = str_replace ( $str2, $str1, $ordensalida );
			// si no encontramos ninguno de los dos lo dejamos como estaba
		else {
			$this->error("Los dorsales $dorsal1 y $dorsal2 no estan consecutivos" );
			$nuevoorden=$ordensalida;
		}
		// actualizamos orden de salida
		setOrden ( $manga, $nuevoorden );
		$this->myLogger->leave();
		return $nuevoorden;
	}
} // class

?>