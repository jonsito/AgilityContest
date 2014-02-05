<?php

require_once("DBConnection.php");
require_once("Mangas.php");

class Jornadas {
	protected $conn;
	protected $file;
	public $errormsg; // should be public to access to from caller
	public $prueba; // id de prueba

	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {integer} $prueba Prueba ID for these jornadas
	 * @throws Exception if cannot contact database
	 */
	function __construct($file,$prueba) {
		if ($prueba<=0){
			$this->errormsg="$file::construct() invalid prueba ID";
			throw new Exception($this->errormsg);
		}
		// initialize
		$this->file=$file;
		$this->prueba=$prueba;
		// connect database
		$this->conn=DBConnection::openConnection("agility_operator","operator@cachorrera");
		if (!$this->conn) {
			$this->errormsg="$file::construct() cannot contact database";
			throw new Exception($this->errormsg);
		}
	}

	/**
	 * Destructor
	 * Just disconnect from database
	 */
	function  __destruct() {
		DBConnection::closeConnection($this->conn);
	}
	
	/**
	 * creacion / borrado de mangas asociadas a una jornada 
	 * @param {integer} $id ID de jornada
	 * @param {integer} $grado1 la jornada tiene(1) o no (0) mangas de grado 1
	 * @param {integer} $grado2 la jornada tiene (1) o no (0) mangas de grado 2
	 * @param {integer} $grado3 la jornada tiene (1) o no (0) mangas de grado 3
	 * @param {integer} $equipos la jornada tiene (1) o no (0) una manga por equipos
	 * @param {integer} $preagility la jornada tiene (1) o no (0) manga de preagility
	 * @param {integer} $ko la jornada contiene (1) o no (0) una prueba k0
	 * @param {integer} $exhibicion la jornada tiene (1) o no (0) mangas de exhibicion
	 * @param {integer} $otras la jornada contiene (1) o no (0) mangas no definidas
	 * // TODO: handle ko, exhibicion and otras
	 */
	function declare_mangas($id,$grado1,$grado2,$grado3,$equipos,$preagility,$ko,$exhibicion,$otras) {
		$mangas =new Mangas("jornadaFunctions",$id);
		if ($grado1) { 	$mangas->insert('Agility-1 GI','GI'); $mangas->insert('Agility-2 GI','GI');		}
		else { $mangas->delete('Agility-1 GI');	$mangas->delete('Agility-2 GI'); }
		if ($grado2) { $mangas->insert('Agility GII','GII'); $mangas->insert('Jumping GII','GII'); }
		else { $mangas->delete('Agility GII'); $mangas->delete('Jumping GII'); }
		if ($grado3) { $mangas->insert('Agility GIII','GIII'); $mangas->insert('Jumping GIII','GIII'); }
		else { $mangas->delete('Agility GIII');	$mangas->delete('Jumping GIII'); }
		if ($equipos) {	$mangas->insert('Agility Equipos','-');	$mangas->insert('Jumping Equipos','-');	}
		else { $mangas->delete('Agility Equipos');	$mangas->delete('Jumping Equipos');	}
		if ($preagility) { $mangas->insert('Pre-Agility','P.A.'); }
		else { $mangas->delete('Pre-Agility'); }
		if ($exhibicion) { $mangas->insert('Exhibicion','-');}
		else { $mangas->delete('Exhibicion'); }
		// TODO: Decidir que se hace con las mangas 'otras'
		// TODO: las mangas KO hay que crearlas dinamicamente en funcion del numero de participantes
	}
	
	/***** insert, update, delete, select (by) functions*/
	
	/**
	 * Insert a new jornada into database
	 * @return {string} "" if ok; null on error
	 */
	function insert() {
		do_log("jornadas::insert():: enter");
		
		// componemos un prepared statement
		$sql ="INSERT INTO Jornadas (Prueba,Nombre,Fecha,Hora,Grado1,Grado2,Grado3,Equipos,PreAgility,KO,Exhibicion,Otras,Cerrada)
			   VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?);";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) {
			$this->errormsg="jornadas::insert() Error: prepare() failed ".$this->conn->error;
			return null;
		}
		$res=$stmt->bind_param('isssiiiiiiiii',
				$prueba,$nombre,$fecha,$hora,$grado1,$grado2,$grado3,$equipos,$preagility,$ko,$exhibicion,$otras,$cerrada);
		if (!$res) {
			$this->errormsg="jornadas::insert() Error: bind() failed ".$this->conn->error;
			return null;
		}
		
		// iniciamos los valores, chequeando su existencia
		$prueba = $this->prueba;
		$nombre = http_request("Nombre","s",null); // Name or comment for jornada
		$fecha = str_replace("/","-",http_request("Fecha","s","")); // mysql requires format YYYY-MM-DD
		$hora = http_request("Hora","s","");
		$grado1 = http_request("Grado1","i",0);
		$grado2 = http_request("Grado2","i",0);
		$grado3 = http_request("Grado3","i",0);
		$equipos = http_request("Equipos","i",0);
		$preagility = http_request("PreAgility","i",0);
		$ko = http_request("KO","i",0);
		$exhibicion = http_request("Exhibicion","i",0);
		$otras = http_request("Otras","i",0);
		$cerrada = http_request("Cerrada","i",0);
		// do_log("jornadas::insert() retrieved data from web client");
		do_log("jornadas::insert()  Prueba: $prueba Nombre: $nombre Fecha: $fecha Hora: $hora");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) {
			$this->errormsg="jornadas::insert() Error: ".$this->conn->error;
			return null;
		}
		else  do_log("jornadas::insert() insertadas $stmt->affected_rows filas");
		$stmt->close();
		// retrieve ID on last created jornada
		$jornadaid=$this->conn->insert_id;
		// if not closed ( an stupid thing create a closed jornada, but.... ) create mangas and default team
		if (!$cerrada) {
			// creamos las mangas asociadas a esta jornada
			declare_mangas($jornadaid,$grado1,$grado2,$grado3,$equipos,$preagility,$ko,$exhibicion,$otras);
			do_log("jornadas::insert() declare mangas for jornadaid $jornadaid");
			// create a default team for this jornada
			$this->conn->query("INSERT INTO Equipos (Jornada,Nombre,Observaciones)
					VALUES ($jornadaid,'-- Sin asignar --','NO BORRAR: USADO COMO GRUPO POR DEFECTO PARA LA JORNADA $jornadaid')");
			do_log("jornadas::insert() declare default team for jornadaid $jornadaid");
		};
		do_log("jornadas::insert() exit OK");
		return ""; 
	}
	
	function update($jornadaid) {
		do_log("jornadas::update() enter");
		if ($jornadaid<=0) {
			$this->errormsg="jornada::update() Error: invalid jornada ID";
			return null;
		}
		// componemos un prepared statement
		$sql ="UPDATE Jornadas
				SET Prueba=?, Nombre=?, Fecha=?, Hora=?, Grado1=?, Grado2=?, Grado3=?,
					Equipos=?, PreAgility=?, KO=?, Exhibicion=?, Otras=?, Cerrada=?
				WHERE ( ID=? );";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) {
			$this->errormsg="jornadas:update() Error: prepare() failed ".$this->conn->error;
			return null;
		}
		$res=$stmt->bind_param('isssiiiiiiiiii',
				$prueba,$nombre,$fecha,$hora,$grado1,$grado2,$grado3,$equipos,$preagility,$ko,$exhibicion,$otras,$cerrada,$id);
		if (!$res) {
			$this->errormsg="jornadas::update() Error: bind() failed ".$this->conn->error;
			return null;
		}
		
		// iniciamos los valores, chequeando su existencia
		$prueba = $this->prueba;
		$nombre = http_request("Nombre","s",null); // Name or comment for jornada
		$fecha = str_replace("/","-",http_request("Fecha","s","")); // mysql requires format YYYY-MM-DD
		$hora = http_request("Hora","s","");
		$grado1 = http_request("Grado1","i",0);
		$grado2 = http_request("Grado2","i",0);
		$grado3 = http_request("Grado3","i",0);
		$equipos = http_request("Equipos","i",0);
		$preagility = http_request("PreAgility","i",0);
		$ko = http_request("KO","i",0);
		$exhibicion = http_request("Exhibicion","i",0);
		$otras = http_request("Otras","i",0);
		$cerrada = http_request("Cerrada","i",0);
		$id= $jornadaid;
		//do_log("jornadas::update() retrieved data from web client");
		do_log("jornadas::update() ID: $id Prueba: $prueba Nombre: $nombre Fecha: $fecha Hora: $hora");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) {
			$this->errormsg="jornadas::update() Error: ".$this->conn->error;
			return null;
		} 
		do_log("jornadas::update() actualizadas $stmt->affected_rows filas");
		$stmt->close();
		if (!$cerrada) {
			$this->declare_mangas($id,$grado1,$grado2,$grado3,$equipos,$preagility,$ko,$exhibicion,$otras);
		}
		do_log("jornadas::update() exit OK");
		return "";
	}
	
	/**
	 * Delete jornada with provided name
	 * @param {integer} jornada name primary key
	 * @return "" on success ; otherwise null
	 */
	function delete($jornadaid) {
		do_log("jornada::delete() enter");
		if ($jornadaid<=0) {
			$this->errormsg="jornada::delete() Error: invalid jornada ID";
			return null;
		}
		
		// si la jornada esta cerrada en lugar de borrarla la movemos a "-- Sin asignar --"
		// con esto evitamos borrar mangas y resultados ya fijos
		$res= $this->conn->query("UPDATE Jornadas SET Prueba='-- Sin asignar --' WHERE ( (ID=$jornadaid) AND (Cerrada=1) );");
		if (!$res) {
			$this->erormsg="jornada::delete() query(update) Error: ".$this->conn->error;
			return null;
		} 
		// do_log("jornada::delete() query(update) resulted: $res");
		
		// si la jornada no está cerrada, directamente la borramos
		// recuerda que las mangas y resultados asociados se borran por la "foreign key"
		$res= $this->conn->query("DELETE FROM Jornadas WHERE ( (ID=$jornadaid) AND (Cerrada=0) );");
		if (!$res) {
			$this->errormsg="jornadas::delete() query(delete) Error: ".$this->conn->error;
			return null;
		} 
		// do_log("deleteJornada:: query(delete) resulted: $res");
		
		do_log("jornada::delete() exit OK");
	} 
	
	/**
	 * select all jornadas related to provided prueba 
	 * @return unknown
	 */
	function selectByPrueba() {
		do_log("jornada::selectByPrueba() enter");
		$result = array();
		$items = array();
		
		$str="SELECT count(*) FROM Jornadas WHERE ( Prueba = ".$this->prueba." )";
		$rs=$this->conn->query($str);
		if (!$rs) {
			$this->errormsg="jornadas::selectByPrueba() select( count(*) ) Error: ".$this->conn->error;
			return null;
		}
		$row=$rs->fetch_row();
		$result["total"] = $row[0];
		$rs->free();
		if ($result["total"]>0) {
			$str="SELECT * FROM Jornadas WHERE ( Prueba = ".$this->prueba." ) ORDER BY Numero ASC";
			do_log("select_JornadasByPrueba::(select) $str");
			$rs=$this->conn->query($str);
			if (!$rs){
				$this->errormsg="jornadas::selectByPrueba() select(*) error ".$this->conn->error;
				return null;
			}
			// retrieve result into an array
			while($row = $rs->fetch_array()){
				array_push($items, $row);
			}
			$rs->free();
		}
		$result["rows"] = $items;
		do_log("jornada::selectByPrueba() exit OK");
		return $result;
	}	
	
	/**
	 * search all jornadas related to provided prueba that matches provided criteria 
	 * @return unknown
	 */
	function searchByPrueba() {
		do_log("jornada::searchByPrueba() enter");
		
		$result = array();
		$items = array();
		// evaluate search terms
		$q=http_request("q","s","");
		$like=")";
		if ($q!=="") $like = " AND ( (Nombre LIKE '%$q%') OR (Numero LIKE '%$q%') ) )";
		
		$str="SELECT count(*) FROM Jornadas WHERE ( ( Prueba = ".$this->prueba." ) AND ( Cerrada=0) $like";
		// do_log("enumerate_jornadasAbiertasByPrueba::(count) $str");
		$rs=$this->conn->query($str);
		if (!$rs) {
			$this->errormsg="jornadas::searchByPrueba() select(count(*)) error ".$this->conn->error;
			return null;
		}
		$row=$rs->fetch_row();
		$result["total"] = $row[0];
		$rs->free();
		if ($result["total"]>0) {
			$str="SELECT * FROM Jornadas WHERE ( ( Prueba = ".$this->prueba." ) AND ( Cerrada=0 ) $like ORDER BY Numero ASC";
			// do_log("enumerate_jornadasAbiertasByPrueba::(select) $str");
			$rs=$this->conn->query($str);
			if (!$rs) {
				$this->errormsg="jornadas::searchByPrueba() select() error ".$this->conn->error;
				return null;
			}
			// retrieve result into an array
			while($row = $rs->fetch_array()){
				array_push($items, $row);
			}
			$rs->free();
		}
		$result["rows"] = $items;
		do_log("jornada::searchByPrueba() exit OK");
		return $result;
	}
}
?>