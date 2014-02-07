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
	 * @return {string} orden de salida. "" si vacio; null on error
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
	 * @param {string} $ordensalida Orden de salida actual
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
	 * Elimina un dorsal del orden de salida indicao
	 * @param {string} $ordensalida orden de salida actual
	 * @param {integer} $dorsal
	 * @return {string} nuevo orden de salida
	 */
	function removeFromList($ordensalida,$dorsal) {
		$this->myLogger->enter();
		$str = "," . $dorsal . ",";
		$nuevoorden = str_replace ( $str, ",", $ordensalida );
		$this->myLogger->leave();
		return $nuevoorden;
	}
	
	/**
	 * Comprueba si el perro esta bien insertado
	 * @param unknown $ordensalida
	 * @param unknown $dorsal
	 * @param unknown $cat
	 * @param unknown $celo
	 * @return true o false
	 */
	function verify($ordensalida, $dorsal, $cat, $celo) {
		$this->myLogger->enter();
		// si no esta insertado indica error
		if (strpos($ordensalida,',$dorsal,')===false) return false;
		$tag="$cat$celo";
		$from="";$to="";
		switch($tag) {
			case "-0": $from="TAG_-0"; $to="TAG_-1"; break;
			case "-1": $from="TAG_-1"; $to="TAG_L0"; break;
			case "L0": $from="TAG_L0"; $to="TAG_L1"; break;
			case "L1": $from="TAG_L1"; $to="TAG_M0"; break;
			case "M0": $from="TAG_M0"; $to="TAG_M1"; break;
			case "M1": $from="TAG_M1"; $to="TAG_S0"; break;
			case "S0": $from="TAG_S0"; $to="TAG_S1"; break;
			case "S1": $from="TAG_S1"; $to="TAG_T0"; break;
			case "T0": $from="TAG_T0"; $to="TAG_T1"; break;
			case "T1": $from="TAG_T1"; $to="END"; break;
			default:
				$this->myLogger->error("Invalid Categoria/Celo values ($cat,$celo) $for dorsal $dorsal");
				return false;
		}
		$f=strpos($ordensalida,$from);
		$l=strpos($ordensalida,$to)-f;
		$str=substr($ordensalida,f,l);
		$this->myLogger->leave();
		return (strpos($str,',$dorsal,')===false)?false:true;
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
		$sql2 = "SELECT Grado FROM Mangas WHERE ( ID=$manga )";
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
		$sql2 = "SELECT Grado FROM Mangas WHERE ( ID=$manga )";
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
	 * Inserta/actualiza/elimina un perro del orden de salida
	 * @param {integer} $idjornada ID de jornada
	 * @param {integer} $idmanga ID de manga
	 * @param {integer} $dorsal Dorsal
	 * @return {string} null on error, "" on success
	 */
	function handle($idjornada,$idmanga,$dorsal) {
		$this->myLogger->enter();
		$this->myLogger->trace("--------------- before datos jornada ---------------- ");
		// obtenemos datos de jornada, manga y perro
		$sql = "SELECT * FROM Jornadas WHERE ( ID=$idjornada )";
		$rs = $this->query( $sql );
		if (!$rs) return $this->error($this->conn->error);
		$jornada = $rs->fetch_object();
		$rs->free ();
		if (!$jornada) return $this->error("No hay datos registrados de la jornada $idjornada");
		if ($jornada->Cerrada==1) return $this->error("No se puede modificar una jornada cerrada");

		$this->myLogger->trace("--------------- before datos manga ---------------- ");
		$sql = "SELECT * FROM Mangas WHERE ( ID=$idmanga )";
		$rs = $this->query( $sql );
		if (!$rs) return $this->error($this->conn->error);
		$manga = $rs->fetch_object();
		$rs->free ();
		if (!$manga) return $this->error("No hay datos registrados de la manga $idmanga");
		if ($manga->Cerrada==1) return $this->error("No se puede modificar una manga cerrada");

		$this->myLogger->trace("--------------- before datos perro ---------------- ");
		// si el perro no esta inscrito en esta jornada retornamos error
		$sql = "SELECT * FROM InscritosJornada WHERE ( Jornada=$idjornada ) AND ( Dorsal=$dorsal)";
		$rs = $this->query ($sql );
		if (!$rs) return $this->error($this->conn->error);
		$perro = $rs->fetch_object();
		$rs->free ();
		if (!$perro) return $this->error("El perro $dorsal no figura inscrito en la jornada $idjornada");

		$this->myLogger->trace("--------------- before check empty order ---------------- ");
		// si el orden de salida esta vacio, generamos uno aleatorio y retornamos
		$ordensalida= $manga->Orden_Salida;
		if (!$ordensalida) return $this->error("Cannot retrieve ordensalida for manga $idmanga");
		if ($ordensalida==="") {
			$this->myLogger->info("La manga $idmanga no tiene predefinido orden de salida. Generando orden aleatorio");
			return $this->random($idjornada,$idmanga);
		}

		$this->myLogger->trace("--------------- before check grado ---------------- ");
		// si el perro esta inscrito en la jornada, pero la manga no es compatible, lo borramos de la manga
		if ($perro->Grado != $manga->Grado) {
			if ($manga->Grado!=="-") {
				$this->myLogger->info("El perro con dorsal $dorsal no puede competir en la manga $idmanga");
				$ordensalida=$this->removeFromList($ordensalida,$dorsal);
				$this->myLogger->leave();
				return $this->setOrden($idmanga,$ordensalida);
			}
		}
		// si llegamos hasta aqui hay que inscribir al perro en la manga

		$this->myLogger->trace("--------------- before if not in list ---------------- ");
		// si no esta inscrito en la manga, lo inscribimos
		if ( strpos($ordensalida,',$dorsal,')===false) {
			$nuevoorden=$this->insertIntoList($ordensalida, $dorsal, $perro->Categoria, $perro->Celo);
			$this->myLogger->leave();
			return $this->setOrden($idmanga,$nuevoorden);
		}

		$this->myLogger->trace("--------------- before check correct in list ---------------- ");
		// si esta inscrito en la manga, vemos si esta en el sitio correcto (cat,celo)
		$bien=$this->verify($ordensalida,dorsal, $perro->Categoria, $perro->Celo);
		if ($bien) {
			// si esta bien inscrito, no hacemos nada
			$this->myLogger->info("El perro $dorsal ya esta BIEN inscrito en la manga $idmanga");
		} else {
			// si esta mal inscrito lo borramos y reinsertamos en el sitio correcto
			$this->myLogger->info("El perro $dorsal esta MAL inscrito en la manga $idmanga . Corregimos");
			$nuevoorden=$this->removeFromList($ordensalida,$dorsal);
			$ordensalida=$this->insertIntoList($ordensalida, $dorsal, $perro->Categoria, $perro->Celo);
		}
		$this->myLogger->leave();
		return $this->setOrden($idmanga,$ordensalida);
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