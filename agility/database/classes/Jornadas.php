<?php

require_once("DBObject.php");
require_once("Mangas.php");

class Jornadas extends DBObject {
	
	// bitfield of 512:Esp 256:KO 128:Eq4 64:Eq3 32:Opn 16:G3 8:G2 4:G1 2:Pre2 1:Pre1
	public static $tipo_ronda= array(
		array(0,	''),
		array(1,	'Pre-Agility (1 Manga)'),
		array(2,	'Pre-Agility (2 Mangas)'),
		array(4,	'Ronda de Grado I'),
		array(8,	'Ronda de Grado II'),
		array(16,	'Ronda de Grado III'),
		array(32,	'Ronda Abierta (Open)'),
		array(64,	'Equipos ( 3 mejores )'),
		array(128,	'Equipos ( 4 conjunta )'),
		array(256,	'Ronda K.O'),
		array(512,	'Manga especial')
	);
	
	protected $prueba; // id de prueba
	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {integer} $prueba Prueba ID for these jornadas
	 * @throws Exception if cannot contact database
	 */
	function __construct($file,$prueba) {
		parent::__construct($file);
		if ($prueba<0){
			$this->errormsg="$file::construct() invalid prueba ID";
			throw new Exception($this->errormsg);
		}
		$this->prueba=$prueba;
	}
	
	/*****
	 * No insert required: a set of 8 journeys are created on each every new prueba 
	 * update, delete, select (by) functions
	 */
	
	/**
	 * Update journey data
	 * @param {integer} $jornadaid
	 * @return string
	 */
	function update($jornadaid) {
		$this->myLogger->enter();
		// if prueba or jornada are closed refuse to upate
		if ($jornadaid<=0) return $this->error("Invalid jornada ID"); 
		// componemos un prepared statement
		$sql ="UPDATE Jornadas
				SET Prueba=?, Nombre=?, Fecha=?, Hora=?, Grado1=?, Grado2=?, Grado3=?,
					Open=?, Equipos3=?, Equipos4=?, PreAgility=?, PreAgility2=?, KO=?, Especial=?, Observaciones=?, Cerrada=?
				WHERE ( ID=? );";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('isssiiiiiiiiiisii',
				$prueba,$nombre,$fecha,$hora,$grado1,$grado2,$grado3,$open,$equipos3,$equipos4,$preagility,$preagility2,$ko,$especial,$observaciones,$cerrada,$id);
		if (!$res) return $this->error($this->conn->error); 
		
		// iniciamos los valores, chequeando su existencia
		$prueba = $this->prueba;
		$nombre = http_request("Nombre","s",null,false); // Name or comment for jornada
		$fecha = str_replace("/","-",http_request("Fecha","s","",false)); // mysql requires format YYYY-MM-DD
		$hora = http_request("Hora","s","",false);
		$grado1 = http_request("Grado1","i",0);
		$grado2 = http_request("Grado2","i",0);
		$grado3 = http_request("Grado3","i",0);
		$open = http_request("Open","i",0);
		$equipos3 = http_request("Equipos3","i",0);
		$equipos4 = http_request("Equipos4","i",0);
		$preagility = http_request("PreAgility","i",0);
		$preagility2 = http_request("PreAgility2","i",0);
		$ko = http_request("KO","i",0);
		$especial = http_request("Especial","i",0);
		$observaciones = http_request("Observaciones","s","(sin especificar)");
		$cerrada = http_request("Cerrada","i",0);
		$id= $jornadaid;
		
		$this->myLogger->info("ID: $id Prueba: $prueba Nombre: $nombre Fecha: $fecha Hora: $hora");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error); 
		$stmt->close();
		if (!$cerrada) {
			$mangas =new Mangas("jornadaFunctions",$id);
			$mangas->prepareMangas($id,$grado1,$grado2,$grado3,$open,$equipos3,$equipos4,$preagility,$preagility2,$ko,$especial,$observaciones);
		}
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Delete jornada with provided ID
	 * @param {integer} jornada name primary key
	 * @return "" on success ; otherwise null
	 */
	function delete($jornadaid) {
		$this->myLogger->enter();
		if ($jornadaid<=0) return $this->error("Invalid Jornada ID"); 
		
		// si la jornada esta cerrada en lugar de borrarla la movemos a la prueba por defecto (ID=1)
		// con esto evitamos borrar mangas y resultados ya fijos
		$res= $this->query("UPDATE Jornadas SET Prueba=1 WHERE ( (ID=$jornadaid) AND (Cerrada=1) );");
		if (!$res) return $this->error($this->conn->error); 
		
		// si la jornada no está cerrada, directamente la borramos
		// recuerda que las mangas y resultados asociados se borran por la "foreign key"
		$res= $this->query("DELETE FROM Jornadas WHERE ( (ID=$jornadaid) AND (Cerrada=0) );");
		if (!$res)  return $this->error($this->conn->error); 

		$this->myLogger->leave();
		return "";
	} 
	
	/**
	 * Close jornada with provided ID
	 * @param {integer} jornada name primary key
	 * @return "" on success ; otherwise null
	 */
	function close($jornadaid) {
		$this->myLogger->enter();
		if ($jornadaid<=0) return $this->error("Invalid Jornada ID");
		// marcamos la jornada con ID=$jornadaid como cerrada
		$res= $this->query("UPDATE Jornadas SET Cerrada=1 WHERE ( ID=$jornadaid ) ;");
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * delete all journeys that belongs to current pruebaID
	 */
	function deleteByPrueba() {
		$this->myLogger->enter();
		$p=$this->prueba;
		if ($p <= 0 ) return $this->error("Invalid Prueba ID"); 
		if ($p == 1 ) return $this->error("Cannot delete Journeys linked to default Contest");
		// first pass: closed journeys now belongs to default Contest
		$res=  $this->query("UPDATE Jornadas SET Prueba=1 WHERE ( (Prueba=$p) AND (Cerrada=1) );");
		if (!$res) return $this->error($this->conn->error);
		// second pass: remove non closed journeys related with current PruebaID
		$res=  $this->query("DELETE FROM Jornadas WHERE ( (Prueba=$p) AND (Cerrada=0) );");
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
	}
	
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Jornada ID");
		
		// make query
		$data= $this->__singleSelect(
				/* SELECT */ "*",
				/* FROM */ "Jornadas",
				/* WHERE */ "( ID=$id )"
		);
		if (!is_array($data))	return $this->error("No Jornada found with ID=$id");
		$this->myLogger->leave();
		return $data;
	}
	
	/**
	 * select all jornadas related to provided prueba 
	 * @return unknown
	 */
	function selectByPrueba() {
		$this->myLogger->enter();
		$result = array();
		$items = array();
		
		// retrieve result from parent __select() call
		$result= $this->__select(
				/* SELECT */ "*",
				/* FROM */ "Jornadas",
				/* WHERE */ "( Prueba = ".$this->prueba." )",
				/* ORDER BY */ "Numero ASC",
				/* LIMIT */ ""
		);
		// return composed array
		$this->myLogger->leave();
		return $result;
	}	
	
	/**
	 * search all jornadas related to provided prueba that matches provided criteria
	 * @param {integer} $allowClosed 1:allow listing closed jornadas; 0:don't
	 * @return unknown
	 */
	function searchByPrueba($allowClosed=0) {
		$this->myLogger->enter();
		// evaluate search terms
		$q=http_request("q","s","");
		$cerrada=($allowClosed==0)?"AND ( Cerrada=0 )":"";
		$where= "( Prueba = {$this->prueba} ) ";
		if ($q!=="") $where= "( Prueba = {$this->prueba} ) $cerrada AND ( (Nombre LIKE '%$q%') OR (Numero LIKE '%$q%') ) ";
		// retrieve result from parent __select() call
		$result= $this->__select(
				/* SELECT */ "*",
				/* FROM */ "Jornadas",
				/* WHERE */ $where,
				/* ORDER BY */ "Numero ASC",
				/* LIMIT */ ""
		);
		// return composed array
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Obtiene el ID de la manga de tipo $tipo asociada a la jornada $jornada
	 * @param {object} $mangas lista de las mangas de estajornada
	 * @param {integer} $tipo campo Tipo a buscar en la tabla de mangas
	 * @param {integer} $round En rondas K.O. indica el numero de la ronda deseada 0..7,8
	 * @return datos de la manga pedida ; null if not found
	 */
	private function fetchManga($mangas,$jornadaid,$tipo,$round=0) {
		foreach ($mangas as $manga) {
			if ($manga["Tipo"]==$tipo) return $manga;
		}
		$this->myLogger->error("Cannot locate Mangas of Tipo:$tipo in Jornada:$jornadaid");
		return null;
	}
	
	/**
	 * Devuelve una lista de las rondas de que consta esta jornada (GI,GII,GIII, PreAgility..)
	 * @param unknown $jornadaid ID de jornada
	 * @return null on error, result in combogrid format "info,idManga1,idmanga2"
	 */
	function roundsByJornada($jornadaid) {
		$this->myLogger->enter();
		// on start, no jornada id is provided, so don't throw error
		if ($jornadaid<=0) {
			$result=array();
			$result['total']=0;
			$result['rows']=array();
			$this->myLogger->notice("jornada ID is 0: return");
			return $result;
		}
		// obtenemos informacion de la jornada y de las mangas de esta jornada
		$row=$this->__selectObject("*","Jornadas","(ID=$jornadaid)");
		if (!is_object($row)) return $this->error("No Jornadas with ID=$jornadaid");
		$mangas=$this->__select("*","Mangas","Jornada=$jornadaid","","");
		if (!is_array($mangas)) return $this->error("No Mangas with Jornada ID=$jornadaid");
		// retrieve result into an array
		$data=array();
		if ($row->Grado1!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,3); // 'Agility-1 GI'
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,4); // 'Agility-2 GI'
			array_push($data,array( 
									"Ronda" => Jornadas::$tipo_ronda[2][0],
									"Nombre" => Jornadas::$tipo_ronda[2][1],
									"Manga1" => $manga1['ID'],"Manga2" => $manga2['ID'],
									"Recorrido1" => $manga1['Recorrido'],"Recorrido2" => $manga2['Recorrido']
									) );
		}
		if ($row->Grado2!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,5); // 'Agility GII'
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,10); // 'Jumping GII'
			array_push($data,array( 
									"Ronda" => Jornadas::$tipo_ronda[4][0],
									"Nombre" => Jornadas::$tipo_ronda[4][1], 
									"Manga1" => $manga1['ID'],"Manga2" => $manga2['ID'],
									"Recorrido1" => $manga1['Recorrido'],"Recorrido2" => $manga2['Recorrido']
									) );
		}
		if ($row->Grado3!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,6); // 'Agility GIII'
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,11); // 'Jumping GIII'
			array_push($data,array( 
									"Ronda" => Jornadas::$tipo_ronda[5][0],
									"Nombre" => Jornadas::$tipo_ronda[5][1],
									"Manga1" => $manga1['ID'],"Manga2" => $manga2['ID'],
									"Recorrido1" => $manga1['Recorrido'],"Recorrido2" => $manga2['Recorrido']
									) );
		}
		if ($row->Open!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,7); // 'Agility Open'
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,12); // 'Jumping Open'
			array_push($data,array( 
									"Ronda" => Jornadas::$tipo_ronda[6][0],
									"Nombre" => Jornadas::$tipo_ronda[6][1], 
									"Manga1" => $manga1['ID'],"Manga2" => $manga2['ID'],
									"Recorrido1" => $manga1['Recorrido'],"Recorrido2" => $manga2['Recorrido']
									) );
		}
		if ($row->PreAgility!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,1); // 'Pre-Agility (1 manga)'
			$manga2= null;
			array_push($data,array( 
									"Ronda" => Jornadas::$tipo_ronda[1][0],
									"Nombre" => Jornadas::$tipo_ronda[1][1],
									"Manga1" => $manga1['ID'], "Manga2" => 0,
									"Recorrido1" => $manga1['Recorrido'],"Recorrido2" => -1
									 ) );
		}			
		if ($row->PreAgility2!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,1); // 'Pre-Agility (2 mangas)
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,2); // 'Pre-Agility (2 mangas)
			array_push($data,array( 
									"Ronda" => Jornadas::$tipo_ronda[2][0],
									"Nombre" => Jornadas::$tipo_ronda[2][1], 
									"Manga1" => $manga1['ID'],"Manga2" => $manga2['ID'],
									"Recorrido1" => $manga1['Recorrido'],"Recorrido2" => $manga2['Recorrido']
									) );
		}
		if ($row->Equipos3!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,8); // 'Agility Equipos (3 mejores)'
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,13); // 'Jumping Equipos (3 mejores)'
			array_push($data,array( 
									"Ronda" => Jornadas::$tipo_ronda[7][0],
									"Nombre" => Jornadas::$tipo_ronda[7][1], 
									"Manga1" => $manga1['ID'],"Manga2" => $manga2['ID'],
									"Recorrido1" => $manga1['Recorrido'],"Recorrido2" => $manga2['Recorrido']
									) );
		}
		if ($row->Equipos4!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,9); // 'Agility Equipos (conjunta)'
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,14); // 'Jumping Equipos (conjunta)'
			array_push($data,array( 
									"Ronda" => Jornadas::$tipo_ronda[8][0],
									"Nombre" => Jornadas::$tipo_ronda[8][1],
									"Manga1" => $manga1['ID'],"Manga2" => $manga2['ID'],
									"Recorrido1" => $manga1['Recorrido'],"Recorrido2" => $manga2['Recorrido']
									) );
		}
		if ($row->KO!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,15); // Ronda K.O.
			$manga2= null;
			array_push($data,array( 
									"Ronda" => Jornadas::$tipo_ronda[9][0],
									"Nombre" => Jornadas::$tipo_ronda[9][1], 
									"Manga1" => $manga1['ID'],"Manga2" => 0,
									"Recorrido1" => $manga1['Recorrido'],"Recorrido2" => -1
									) );
		}
		if ($row->Especial!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,16); // 'Manga especial'
			$manga2= null;
			array_push($data,array( 
									"Ronda" => Jornadas::$tipo_ronda[10][0],
									"Nombre" => Jornadas::$tipo_ronda[10][1], 
									"Manga1" => $manga1['ID'],"Manga2" => 0,
									"Recorrido1" => $manga1['Recorrido'],"Recorrido2" => -1
									) );
		}
		$result=array();
		$result['total']=count($data);
		$result['rows']=$data;
		$this->myLogger->leave();
		return $result;
	}
}
?>