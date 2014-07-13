<?php

require_once("DBObject.php");
require_once("Mangas.php");

class Jornadas extends DBObject {
	
	/* contenido de la tabla Tipo_Manga
	(1, 'Manga sin tipo definido', '-'),
	(2, 'Ronda de Pre-Agility', 'P.A.'),
	(3, 'Agility Grado I Manga 1', 'GI'),
	(4, 'Agility Grado I Manga 2', 'GI'),
	(5, 'Agility Grado II', 'GII'),
	(6, 'Agility Grado III', 'GIII'),
	(7, 'Agility Abierta (Open)', '-'),
	(8, 'Agility Equipos (3 mejores)', '-'),
	(9, 'Agility Equipos (Conjunta)', '-'),
	(10, 'Jumping Grado II', 'GII'),
	(11, 'Jumping Grado III', 'GIII'),
	(12, 'Jumping Abierta (Open)', '-'),
	(13, 'Jumping por Equipos (3 mejores)', '-'),
	(14, 'Jumping por Equipos (Conjunta)', '-'),
	(15, 'Ronda K.O.', '-'),
	(16, 'Ronda de Exhibición', '-');
	*/ 
	
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
	function declare_mangas($id,$grado1,$grado2,$grado3,$open,$equipos3,$equipos4,$preagility,$ko,$exhibicion,$otras) {
		$this->myLogger->enter();
		$mangas =new Mangas("jornadaFunctions",$id);
		
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
					Open=?, Equipos3=?, Equipos4=?, PreAgility=?, KO=?, Exhibicion=?, Otras=?, Cerrada=?
				WHERE ( ID=? );";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('isssiiiiiiiiiiii',
				$prueba,$nombre,$fecha,$hora,$grado1,$grado2,$grado3,$open,$equipos3,$equipos4,$preagility,$ko,$exhibicion,$otras,$cerrada,$id);
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
		$ko = http_request("KO","i",0);
		$exhibicion = http_request("Exhibicion","i",0);
		$otras = http_request("Otras","i",0);
		$cerrada = http_request("Cerrada","i",0);
		$id= $jornadaid;
		
		$this->myLogger->info("ID: $id Prueba: $prueba Nombre: $nombre Fecha: $fecha Hora: $hora");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error); 
		$stmt->close();
		if (!$cerrada) {
			$this->declare_mangas($id,$grado1,$grado2,$grado3,$open,$equipos3,$equipos4,$preagility,$ko,$exhibicion,$otras);
		}
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Delete jornada with provided name
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
		if (!$data)	return $this->error("No Jornada found with ID=$id");
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
	 * @return unknown
	 */
	function searchByPrueba() {
		$this->myLogger->enter();
		// evaluate search terms
		$q=http_request("q","s","");
		$where= "( Prueba = ".$this->prueba." ) AND ( Cerrada=0)";
		if ($q!=="") $where= "( Prueba = ".$this->prueba." ) AND ( Cerrada=0) AND ( (Nombre LIKE '%$q%') OR (Numero LIKE '%$q%') ) ";
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
	 * @param {integer} $jornada
	 * @param {integer} $tipo (ID de la tabla Tipo_Manga
	 * @return manga id ; 0 if not found; null on error
	 */
	function fetchManga($jornada,$tipo) {
		$this->myLogger->enter();
		$str="SELECT * FROM Mangas WHERE (Jornada=$jornada) AND (Tipo=$tipo)";
		$rs= $this->query($str);
		if (!$rs) return $this->conn->error;
		$row=$rs->fetch_object();
		$rs->free();
		if (!$row) {
			$this->myLogger->error("No result found for Manga $tipo in Jornada $jornada");
			return 0;
		}
		$this->myLogger->debug("$str retorna ".$row->ID);
		$this->myLogger->leave();
		return $row;
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
		$str="SELECT * FROM Jornadas WHERE (ID=$jornadaid)";
		$rs=$this->query($str);
		if (!$rs)  return $this->error($this->conn->error);
		// retrieve result into an array
		$data=array();
		$row = $rs->fetch_object();
		if (!$row) return $this->error("No Jornadas with ID=$jornadaid");
		if ($row->Grado1!=0) {
			$manga1= $this->fetchManga($jornadaid,3); // 'Agility-1 GI'
			$manga2= $this->fetchManga($jornadaid,4); // 'Agility-2 GI'
			array_push($data,array("Nombre" => "Ronda de Grado I", 
					"Manga1" => $manga1->ID, "Manga2" => $manga2->ID, 
					"Nombre1" => "Agility", "Nombre2" => "Agility",
					"Juez11" => $manga1->Juez1, "Juez12" => $manga1->Juez2,
					"Juez21" => $manga2->Juez1, "Juez22" => $manga2->Juez2,
					"Observaciones1" => $manga1->Observaciones,
					"Observaciones2" => $manga2->Observaciones) );
		}
		if ($row->Grado2!=0) {
			$manga1= $this->fetchManga($jornadaid,5); // 'Agility GII'
			$manga2= $this->fetchManga($jornadaid,10); // 'Jumping GII'
			array_push($data,array("Nombre" => "Ronda de Grado II", 
					"Manga1" => $manga1->ID, "Manga2" => $manga2->ID, 
					"Nombre1" => "Agility", "Nombre2" => "Jumping",
					"Juez11" => $manga1->Juez1, "Juez12" => $manga1->Juez2,
					"Juez21" => $manga2->Juez1, "Juez22" => $manga2->Juez2,
					"Observaciones1" => $manga1->Observaciones,
					"Observaciones2" => $manga2->Observaciones) );
		}
		if ($row->Grado3!=0) {
			$manga1= $this->fetchManga($jornadaid,6); // 'Agility GIII'
			$manga2= $this->fetchManga($jornadaid,11); // 'Jumping GIII'
			array_push($data,array("Nombre" => "Ronda de Grado III", 
					"Manga1" => $manga1->ID, "Manga2" => $manga2->ID, 
					"Nombre1" => "Agility", "Nombre2" => "Jumping",
					"Juez11" => $manga1->Juez1, "Juez12" => $manga1->Juez2,
					"Juez21" => $manga2->Juez1, "Juez22" => $manga2->Juez2,
					"Observaciones1" => $manga1->Observaciones,
					"Observaciones2" => $manga2->Observaciones) );
		}
		if ($row->Open!=0) {
			$manga1= $this->fetchManga($jornadaid,7); // 'Agility Open'
			$manga2= $this->fetchManga($jornadaid,12); // 'Jumping Open'
			array_push($data,array("Nombre" => "Prueba Abierta (Open)", 
					"Manga1" => $manga1->ID, "Manga2" => $manga2->ID, 
					"Nombre1" => "Agility", "Nombre2" => "Jumping",
					"Juez11" => $manga1->Juez1, "Juez12" => $manga1->Juez2,
					"Juez21" => $manga2->Juez1, "Juez22" => $manga2->Juez2,
					"Observaciones1" => $manga1->Observaciones,
					"Observaciones2" => $manga2->Observaciones) );
		}
		if ($row->PreAgility!=0) {
			$manga1= $this->fetchManga($jornadaid,2); // 'Pre-Agility'
			$manga2= 0;
			array_push($data,array("Nombre" => "Manga de Pre-Agility", 
					"Manga1" => $manga1->ID, "Manga2" => 0, 
					"Nombre1" => "Pre-Agility", "Nombre2" => "",
					"Juez11" => $manga1->Juez1, "Juez12" => $manga1->Juez2,
					"Juez21" => "", "Juez22" => "",
					"Observaciones1" => $manga1->Observaciones,
					"Observaciones2" => "") );
		}
		if ($row->Equipos3!=0) {
			$manga1= $this->fetchManga($jornadaid,8); // 'Agility Equipos (3 mejores)'
			$manga2= $this->fetchManga($jornadaid,13); // 'Jumping Equipos (3 mejores)'
			array_push($data,array("Nombre" => "Competicion por equipos", 
					"Manga1" => $manga1->ID, "Manga2" => $manga2->ID, 
					"Nombre1" => "Agility", "Nombre2" => "Jumping",
					"Juez11" => $manga1->Juez1, "Juez12" => $manga1->Juez2,
					"Juez21" => $manga2->Juez1, "Juez22" => $manga2->Juez2,
					"Observaciones1" => $manga1->Observaciones,
					"Observaciones2" => $manga2->Observaciones) );
		}
		if ($row->Equipos4!=0) {
			$manga1= $this->fetchManga($jornadaid,9); // 'Agility Equipos (conjunta)'
			$manga2= $this->fetchManga($jornadaid,14); // 'Jumping Equipos (conjunta)'
			array_push($data,array("Nombre" => "Competicion por equipos", 
					"Manga1" => $manga1->ID, "Manga2" => $manga2->ID, 
					"Nombre1" => "Agility", "Nombre2" => "Jumping",
					"Juez11" => $manga1->Juez1, "Juez12" => $manga1->Juez2,
					"Juez21" => $manga2->Juez1, "Juez22" => $manga2->Juez2,
					"Observaciones1" => $manga1->Observaciones,
					"Observaciones2" => $manga2->Observaciones) );
		}
		if ($row->KO!=0) {
			$manga1= $this->fetchManga($jornadaid,15); // Ronda K.O.
			$manga2= 0;
			array_push($data,array("Nombre" => "Ronda K.O.", 
					"Manga1" => $manga1->ID, "Manga2" => 0, 
					"Nombre1" => "Ronda K.O.", "Nombre2" => "",
					"Juez11" => $manga1->Juez1, "Juez12" => $manga1->Juez2,
					"Juez21" => "", "Juez22" => "",
					"Observaciones1" => $manga1->Observaciones,
					"Observaciones2" => "") );
		}
		if ($row->Exhibicion!=0) {
			$manga1= $this->fetchManga($jornadaid,16); // 'Exhibición'
			$manga2= 0;
			array_push($data,array("Nombre" => "Manga de Exhibicion", 
					"Manga1" => $manga1->ID, "Manga2" => 0, 
					"Nombre1" => "Exhibicion", "Nombre2" => "",
					"Juez11" => $manga1->Juez1, "Juez12" => $manga1->Juez2,
					"Juez21" => "", "Juez22" => "",
					"Observaciones1" => $manga1->Observaciones,
					"Observaciones2" => "") );
		}			
		if ($row->Otras!=0) {
			$manga1= $this->fetchManga($jornadaid,1); // 'Otras' ( sin tipo definido )
			$manga2= 0;
			array_push($data,array("Nombre" => "Otras (sin definir)", 
					"Manga1" => $manga1->ID, "Manga2" =>  0, 
					"Nombre1" => "", "Nombre2" => "",
					"Juez11" => $manga1->Juez1, "Juez12" => $manga1->Juez2,
					"Juez21" => "", "Juez22" => "",
					"Observaciones1" => $manga1->Observaciones,
					"Observaciones2" => "") );
		}
		$rs->free();
		$result=array();
		$result['total']=count($data);
		$result['rows']=$data;
		$this->myLogger->leave();
		return $result;
	}
}
?>