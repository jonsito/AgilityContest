<?php
require_once("DBObject.php");
require_once("Resultados.php");
require_once("Clasificaciones.php");
require_once("OrdenSalida.php");
require_once("Inscripciones.php");

class OrdenTandas extends DBObject {
	
	// tablas utilizadas para componer e insertar los idperroes en el string de orden de salida
	protected $default_orden = "BEGIN,END";
	
	// lista de tandas disponibles y rangos de busqueda en OrdenSalida
	static $lista_tandas = array (
		0	=> array('ID'=>0,	'TipoManga'=>0,		'From'=>'',			'To'=>'',			'Nombre'=>'-- Sin especificar --','Categoria'=>'-',	'Grado'=>'-'),
		// en pre-agility no hay categorias
		1	=> array('ID'=>1,	'TipoManga'=> 1,	'From'=>'BEGIN,',	'To'=>',END',		'Nombre'=>'Pre-Agility 1',			'Categoria'=>'-LMST','Grado'=>'P.A.'),
		2	=> array('ID'=>2,	'TipoManga'=> 2,	'From'=>'BEGIN,',	'To'=>',END',		'Nombre'=>'Pre-Agility 2',			'Categoria'=>'-LMST','Grado'=>'P.A.'),
		3	=> array('ID'=>3,	'TipoManga'=> 3,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Agility-1 GI Large',		'Categoria'=>'L',	'Grado'=>'GI'),
		4	=> array('ID'=>4,	'TipoManga'=> 3,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Agility-1 GI Medium',	'Categoria'=>'M',	'Grado'=>'GI'),
		5	=> array('ID'=>5,	'TipoManga'=> 3,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Agility-1 GI Small',		'Categoria'=>'S',	'Grado'=>'GI'),
		6	=> array('ID'=>6,	'TipoManga'=> 4,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Agility-2 GI Large',		'Categoria'=>'L',	'Grado'=>'GI'),
		7	=> array('ID'=>7,	'TipoManga'=> 4,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Agility-2 GI Medium',	'Categoria'=>'M',	'Grado'=>'GI'),
		8	=> array('ID'=>8,	'TipoManga'=> 4,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Agility-2 GI Small',		'Categoria'=>'S',	'Grado'=>'GI'),
		9	=> array('ID'=>9,	'TipoManga'=> 5,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Agility GII Large',		'Categoria'=>'L',	'Grado'=>'GII'),
		10	=> array('ID'=>10,	'TipoManga'=> 5,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Agility GII Medium',		'Categoria'=>'M',	'Grado'=>'GII'),
		11	=> array('ID'=>11,	'TipoManga'=> 5,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Agility GII Small',		'Categoria'=>'S',	'Grado'=>'GII'),
		12	=> array('ID'=>12,	'TipoManga'=> 6,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Agility GIII Large',		'Categoria'=>'L',	'Grado'=>'GIII'),
		13	=> array('ID'=>13,	'TipoManga'=> 6,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Agility GIII Medium',	'Categoria'=>'M',	'Grado'=>'GIII'),
		14	=> array('ID'=>14,	'TipoManga'=> 6,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Agility GIII Small',		'Categoria'=>'S',	'Grado'=>'GIII'),
		15	=> array('ID'=>15,	'TipoManga'=> 7,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Agility Open Large',		'Categoria'=>'L',	'Grado'=>'-'),
		16	=> array('ID'=>16,	'TipoManga'=> 7,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Agility Open Medium',	'Categoria'=>'M',	'Grado'=>'-'),
		17	=> array('ID'=>17,	'TipoManga'=> 7,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Agility Open Small',		'Categoria'=>'S',	'Grado'=>'-'),
		18	=> array('ID'=>18,	'TipoManga'=> 8,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Agility Eq. 3 Large',	'Categoria'=>'L',	'Grado'=>'-'),
		19	=> array('ID'=>19,	'TipoManga'=> 8,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Agility Eq. 3 Medium',	'Categoria'=>'M',	'Grado'=>'-'),
		20	=> array('ID'=>10,	'TipoManga'=> 8,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Agility Eq. 3 Small',	'Categoria'=>'S',	'Grado'=>'-'),
		21	=> array('ID'=>21,	'TipoManga'=> 9,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Ag. Equipos 4 Large',	'Categoria'=>'M',	'Grado'=>'-'),
		// en jornadas por equipos conjunta se mezclan categorias M y S
		22	=> array('ID'=>22,	'TipoManga'=> 9,	'From'=>'TAG_M0,',	'To'=>',TAG_T0',	'Nombre'=>'Ag. Equipos 4 Med/Small','Categoria'=>'MS',	'Grado'=>'-'),
		23	=> array('ID'=>23,	'TipoManga'=> 10,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Jumping GII Large',		'Categoria'=>'L',	'Grado'=>'GII'),
		24	=> array('ID'=>24,	'TipoManga'=> 10,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Jumping GII Medium',		'Categoria'=>'M',	'Grado'=>'GII'),
		25	=> array('ID'=>25,	'TipoManga'=> 10,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Jumping GII Small',		'Categoria'=>'S',	'Grado'=>'GII'),
		26	=> array('ID'=>26,	'TipoManga'=> 11,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Jumping GIII Large',		'Categoria'=>'L',	'Grado'=>'GIII'),
		27	=> array('ID'=>27,	'TipoManga'=> 11,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Jumping GIII Medium',	'Categoria'=>'M',	'Grado'=>'GIII'),
		28	=> array('ID'=>28,	'TipoManga'=> 11,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Jumping GIII Small',		'Categoria'=>'S',	'Grado'=>'GIII'),
		29	=> array('ID'=>29,	'TipoManga'=> 12,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Jumping Open Large',		'Categoria'=>'L',	'Grado'=>'-'),
		30	=> array('ID'=>30,	'TipoManga'=> 12,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Jumping Open Medium',	'Categoria'=>'M',	'Grado'=>'-'),
		31	=> array('ID'=>31,	'TipoManga'=> 12,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Jumping Open Small',		'Categoria'=>'S',	'Grado'=>'-'),
		32	=> array('ID'=>32,	'TipoManga'=> 13,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Jumping Eq. 3 Large',	'Categoria'=>'L',	'Grado'=>'-'),
		33	=> array('ID'=>33,	'TipoManga'=> 13,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Jumping Eq. 3 Medium',	'Categoria'=>'M',	'Grado'=>'-'),
		34	=> array('ID'=>34,	'TipoManga'=> 13,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Jumping Eq. 3 Small',	'Categoria'=>'S',	'Grado'=>'-'),
		// en jornadas por equipos conjunta se mezclan categorias M y S
		35	=> array('ID'=>35,	'TipoManga'=> 14,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Jp. Equipos 4 Large',	'Categoria'=>'M',	'Grado'=>'-'),
		36	=> array('ID'=>36,	'TipoManga'=> 14,	'From'=>'TAG_M0,',	'To'=>',TAG_T0',	'Nombre'=>'Jp. Equipos 4 Med/Small','Categoria'=>'MS',	'Grado'=>'-'),
		// en las rondas KO, los perros compiten todos contra todos
		37	=> array('ID'=>37,	'TipoManga'=> 15,	'From'=>'BEGIN,',	'To'=>',END',		'Nombre'=>'Manga K.O.',				'Categoria'=>'-LMST','Grado'=>'-'),
		38	=> array('ID'=>38,	'TipoManga'=> 16,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Manga Especial Large',	'Categoria'=>'L',	'Grado'=>'-'),
		39	=> array('ID'=>39,	'TipoManga'=> 16,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Manga Especial Medium',	'Categoria'=>'M',	'Grado'=>'-'),
		40	=> array('ID'=>40,	'TipoManga'=> 16,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Manga Especial Small',	'Categoria'=>'S',	'Grado'=>'-'),
	);

	/**
	 * return every array items that matches with provided key
	 * @param unknown $key
	 * @param unknown $value
	 * @return multitype:
	 */
	function getTandasBy($key,$value) {
		$res=array();
		foreach(OrdenTandas::$lista_tandas as $item) {
			if (!isset($item[$key])) return $res; // key not found: return empty array
			if ($item[$key]==$value) array_push($res,$item);
		}
		return $res;
	}
	
	/* use parent constructor and destructor */
	
	/**
	 * Retrieve Jornadas.Orden_Tandas
	 * @param {integer} $idjornada
	 * @return {string} orden de salida. "" si vacio; null on error
	 */
	function getOrden($idjornada) {
		$this->myLogger->enter();
		$res=$this->__selectObject("Orden_Tandas", "Jornadas", "( ID=$idjornada )");
		if (!is_object($res)) return $this->error($this->conn->error);
		$result = $res->Orden_Tandas;
		$this->myLogger->leave();
		return ($result==="")?$this->default_orden:$result;
	}
	
	/**
	 * Update Jornadas.Orden_Tandas with new value
	 * @param {integer} $idjornada Jornada ID
	 * @param {string} $orden new ordensalida
	 * @return {string} "" if success; null on error
	 */
	function setOrden($idjornada, $orden) {
		$this->myLogger->enter();
		$sql = "UPDATE Jornadas SET Orden_Tandas = '$orden' WHERE ( ID=$idjornada )";
		$rs = $this->query ($sql);
		// do not call $rs->free() as no resultset returned
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * coge el string con el orden de salida e inserta un elemento al final de su grupo
	 * Porsiaca lo intenta borrar previamente
	 * @param {string} $orden Orden de tandas actual
	 * @param {integer} $idperro
	 * @return {string} nuevo orden de salida
	 */
	function insertIntoList($orden, $idtanda) {
		// $this->myLogger->debug("inserting idperro:$idperro cat:$cat celo:$celo" );
		// lo borramos para evitar una posible doble insercion
		$str = "," . $idtanda . ",";
		$nuevoorden = str_replace ( $str, ",", $orden );
		// componemos el tag que hay que insertar
		$myTag = $idtanda . ",END";
		// y lo insertamos en lugar que corresponde
		$result = str_replace ( "END", $myTag, $nuevoorden );
		return $result;
	}
	
	/**
	 * Elimina una tanda del orden de tandas indicado
	 * @param {string} $orden orden de tandas actual
	 * @param {integer} $id de la tanda
	 * @return {string} nuevo orden de tandas
	 */
	function removeFromList($orden,$idtanda) {
		$str = "," . $idtanda . ",";
		$nuevoorden = str_replace ( $str, ",", $orden );
		return $nuevoorden;
	}


	/**
	 * Drag and drop a tanda into list
	 * @param {integer} $jornada ID de jornada
	 * @param {integer} $from ID de la tanda a mover
	 * @param {integer} $to ID de la tanda destino
	 * @param {integer} $where 0:encima 1:debajo
	 * @return string
	 */
	function dragAndDrop($prueba,$jornada,$from,$to,$where) {
		$this->myLogger->enter();
		if ( ($jornada<=0) || ($from<=0) || ($to<=0)) {
			return $this->error("dnd: either Jornada:$jornada or SrcTanda:$from or DestTanda:$to are invalid");
		}
		// recuperamos el orden de salida
		$orden = $this->getOrden ( $jornada );
		// extraemos "from" de donde este
		$str = ",$from,";
		$orden = str_replace ( $str , "," , $orden );
		$str1 = ",$to,";
		$str2 = ($where==0)? ",$from,$to," : ",$to,$from,";
		$orden = str_replace( $str1 , $str2 , $orden );
		$this->setOrden($jornada,$orden);
		$this->myLogger->leave();
		return "";
	}

	
	/**
	 * Ajusta el orden de tandas para que coincida con los datos de la jornada dada
	 * @param unknown $jornada
	 */
	function updateOrden($jornada) {
		$this->myLogger->enter();
		// obtenemos datos de la jornada
		$j=$this->__getObject("Jornadas",$jornada);
		if (!is_object($j)) return $this->error($this->conn->error);
		$orden=$j->Orden_Tandas;
		// obtenemos la lista de tandas de cada ronda
		if ($j->PreAgility	!=0) {
			foreach( $this->getTandasBy('TipoManga',1) as $item) { $orden = $this->insertIntoList($orden,$item['ID']);  }
		}
		if ($j->PreAgility2	!=0) {
			foreach( $this->getTandasBy('TipoManga',1) as $item) { $orden = $this->insertIntoList($orden,$item['ID']);  }
			foreach( $this->getTandasBy('TipoManga',2) as $item) { $orden = $this->insertIntoList($orden,$item['ID']);  }
		}
		if ($j->Grado1	!=0) {
			foreach( $this->getTandasBy('TipoManga',3) as $item) { $orden = $this->insertIntoList($orden,$item['ID']);  }
			foreach( $this->getTandasBy('TipoManga',4) as $item) { $orden = $this->insertIntoList($orden,$item['ID']);  }
		}
		if ($j->Grado2	!=0) {
			foreach( $this->getTandasBy('TipoManga',5) as $item) { $orden = $this->insertIntoList($orden,$item['ID']);  }
			foreach( $this->getTandasBy('TipoManga',10) as $item) { $orden = $this->insertIntoList($orden,$item['ID']);  }
		}
		if ($j->Grado3	!=0) {
			foreach( $this->getTandasBy('TipoManga',6) as $item) { $orden = $this->insertIntoList($orden,$item['ID']);  }
			foreach( $this->getTandasBy('TipoManga',11) as $item) { $orden = $this->insertIntoList($orden,$item['ID']);  }
		}
		if ($j->Open	!=0) {
			foreach( $this->getTandasBy('TipoManga',7) as $item) { $orden = $this->insertIntoList($orden,$item['ID']);  }
			foreach( $this->getTandasBy('TipoManga',12) as $item) { $orden = $this->insertIntoList($orden,$item['ID']);  }
		}
		if ($j->Equipos3	!=0) {
			foreach( $this->getTandasBy('TipoManga',8) as $item) { $orden = $this->insertIntoList($orden,$item['ID']);  }
			foreach( $this->getTandasBy('TipoManga',13) as $item) { $this->insertIntoList($orden,$item['ID']);  }
		}
		if ($j->Equipos4	!=0) {
			foreach( $this->getTandasBy('TipoManga',9) as $item) { $orden = $this->insertIntoList($orden,$item['ID']);  }
			foreach( $this->getTandasBy('TipoManga',14) as $item) { $orden = $this->insertIntoList($orden,$item['ID']);  }
		}
		if ($j->KO		!=0) {
			foreach( $this->getTandasBy('TipoManga',15) as $item) { $orden = $this->insertIntoList($orden,$item['ID']);  }
		}
		if ($j->Especial!=0) {
			foreach( $this->getTandasBy('TipoManga',16) as $item) { $orden = $this->insertIntoList($orden,$item['ID']);  }
		}
		$this->setOrden($jornada,$orden);
		$this->myLogger->leave();
	}

	/**
	 * Obtiene el programa de actividades de esta jornada
	 */
	function getTandas($prueba,$jornada) {
		$this->myLogger->enter();
		$orden=$this->getOrden($jornada);
		
		// prepared statement to retrieve mangas id
		$sql="SELECT ID FROM Mangas WHERE (Jornada=?) AND (Tipo=?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);	
		$res=$stmt->bind_param('ii',$idjornada,$tipo);
		if (!$res) return $this->error($stmt->error);

		$idjornada=$jornada;
		$result=array();
		$rows=array();
		$a=explode(",",$orden);
		foreach($a as $idx) {
			if($idx==='BEGIN') continue;
			if($idx==='END') continue;
			$tandas=$this->getTandasBy("ID",$idx);
			foreach($tandas as $tanda){
				$item=array('Prueba'=>$prueba, 'Jornada'=>$jornada);
				$item=array_merge($item,$tanda);
				$tipo=$item['TipoManga'];
				$rs=$stmt->execute();
				if (!$rs) return $this->error($stmt->error);
				$stmt->bind_result($mangaid);
				$stmt->fetch();
				$item['Manga']=$mangaid;
				array_push($rows,$item);	
			}
		}
		$stmt->close();
		$result['rows']=$rows;
		$result['total']=count($rows);
		$this->myLogger->leave();
		return $result;
	}
	
	/* 0,'','' */
	/* 1, 'Pre-Agility Manga 1', 'P.A.' */ // notice that in 1 manga mode there is no sister
	/* 2, 'Pre-Agility Manga 2', 'P.A.' */
	/* 3, 'Agility Grado I Manga 1', 'GI' */
	/* 4, 'Agility Grado I Manga 2', 'GI' */
	/* 5, 'Agility Grado II', 'GII' */
	/* 6, 'Agility Grado III', 'GIII' */
	/* 7, 'Agility Abierta (Open)', '-' */
	/* 8, 'Agility Equipos (3 mejores)', '-' */
	/* 9, 'Agility Equipos (Conjunta)', '-' */
	/* 10,'Jumping Grado II', 'GII' */
	/* 11,'Jumping Grado III', 'GIII' */
	/* 12,'Jumping Abierta (Open)', '-' */
	/* 13,'Jumping por Equipos (3 mejores)', '-' */
	/* 14,'Jumping por Equipos (Conjunta)', '-' */
	/* 15,'Ronda K.O.', '-' */
	/* 16,'Manga Especial', '-' */
	/**
	 * Obtiene la lista (actualizada) de perros de una jornada ordenada segun tandas/mangas
	 * En el proceso de inscripcion ya hemos creado la tabla de resultados, y el orden de salida
	 * con lo que la cosa es sencilla:
	 * Cogemos los perros inscritos en la jornada y los ordenamos segun el orden establecido, añadiendo
	 * el campo "Celo"
	 *
	 * @param {int} $prueba ID de prueba
	 * @param {int} $jornada ID de jornada
	 * @param {int} $pendientes 0: coge la lista completa; else coge los n primeros marcados como pendientes
	 * @return array[count,[data]] array ordenado segun tandas/ordensalida de datos de perros de una jornada 
	 */
	function getData($prueba,$jornada,$pendientes) {
		$this->myLogger->enter();
		$count=$pendientes;
		$rows=array();
		// fase 1 buscamos las tandas de cada jornada
		$lista_tandas=$this->getTandas($prueba,$jornada);
		foreach ($lista_tandas['rows'] as $tanda) {
			$manga=$tanda['Manga']; 
			// en cada manga cogemos el orden de salida asociado
			$os=new OrdenSalida("ordenTandas::getData()",$prueba,$jornada,$manga);
			$ordenmanga=$os->getOrden($manga);
			// extraemos el substring definido entre 'from' y 'to'
			$ordentanda=getInnerString($ordenmanga,$tanda['From'],$tanda['To']);
			// y recuperamos los perros inscritos
			if($ordentanda==="") continue; // skip empty tandas
			$orden=explode(',',$ordentanda);
			$celo=0;
			foreach($orden as $perro) {
				// from manual: don't compare strpos against 'true'
				if (strpos($perro,'TAG')!==false) { // separator. check for 'Celo' field
					if (strpos($perro,'1')===false) $celo=0;
					if (strpos($perro,'0')===false) $celo=1;
					continue; // next search
				}
				$res=$this->__selectAsArray("*","Resultados","(Prueba=$prueba) AND (Jornada=$jornada) AND (Manga=$manga) AND (Perro=$perro)");
				if (!is_array($res)) return $this->error($this->conn-error);
				$res['Celo']=$celo; // store celo info
				$res['Tanda']=$tanda['Nombre'];
				if ($pendientes==0) { array_push($rows,$res); continue; } // include all
				if ($res['Pendiente']==0) continue; // not pendiente: skip
				if ($count > 0) { $count--; array_push($rows,$res); continue; } // not yet at count: insert 
				// arriving here means that every requested dogs are filled
				$this->myLogger->debug("OrdenTandas::getData() Already have $pendientes dogs");
				// so return
				$result['rows']=$rows;
				$result['total']=count($rows);
				$this->myLogger->leave();
				return $result;
			}
		}
		$result['rows']=$rows;
		$result['total']=count($rows);
		$this->myLogger->leave();
		return $result;	
	}


} // class

?>