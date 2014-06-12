<?php
require_once("DBObject.php");
require_once("Jornadas.php");
require_once("OrdenSalida.php"); // to insert/remove inscriptions from mangas

class Inscripciones extends DBObject {
	
	protected $prueba;
	protected $jornadas; // array of jornadas for this prueba
	protected $declaradas; // declare if a jornada has flag "-- Sin asignar --"
	
	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {integer} $prueba Prueba ID
	 * @throws Exception if cannot contact database or invalid prueba/jornada ID
	 */
	function __construct($file,$prueba) {
		parent::__construct($file);
		if ( $prueba<=0 ) {
			$this->errormsg="$file::construct() invalid prueba:$prueba ID";
			throw new Exception($this->errormsg);
		}
		$this->prueba=$prueba;
		// obtenemos la lista de jornadas asociadas a esta prueba
		$j=new Jornadas("inscripciones",$prueba);
		$res=$j->selectByPrueba();
		if ( ($res===null) || ($res==="") ) {
			$this->errormsg="$file::construct() cannot get list of Jornadas for this prueba";
			throw new Exception($this->errormsg);
		}
		$this->jornadas=array();
		$this->declaradas=array();
		foreach($res["rows"] as $item) { 
			$this->jornadas[$item["Numero"]]=$item;
			$this->declaradas[$item["Numero"]]=($item["Nombre"]!=="-- Sin asignar --")?1:0;
		} 
	}

	/**
	 * Actualiza el orden de salida si es necesario
	 * @param {integer} $jornada
	 * @param {integer} $idperro
	 * @param {integer} $celo
	 * @return "" on success; null on error
	 */
	function updateOrdenSalida($jornada,$idperro) {
		$this->myLogger->enter();
		// obtenemos un manejador de ordenes de salida
		$os=new OrdenSalida("inscripciones::ordensalida");
	
		// buscamos la lista de mangas que tiene la jornada
		$str="SELECT ID FROM Mangas	WHERE ( Jornada = $jornada ) ORDER BY Tipo ASC";
		$rs=$this->query($str);
		if(!$rs) return $this->error($this->conn->error); 
		// retrieve result into an array
		while($row = $rs->fetch_object()){
			$manga=$row->ID;
			$this->myLogger->debug("Ajustando el orden de salida jornada:$jornada manga:$manga idperro:$idperro");
			$os->handle($jornada,$manga,$idperro);
		}
		$rs->free();
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * insert/update/delete a new inscripcion into database
	 * @return empty string if ok; else null
	 */
	function doit() {
		$this->myLogger->enter();

		// variables comunes a todas las jornadas
		$idperro=http_request("Perro","i",0);
		if ($idperro==0) return $this->error("Invalid IDPerro ID"); 
		$celo=http_request("Celo","i",0);
		$observaciones=http_request("Observaciones","s","");
		$equipo=http_request("Equipo","i",0);
		$pagado=http_request("Pagado","i",0);
		
		// si el ID de equipo es cero, buscamos el equipo por defecto para la prueba solicitada
		if ($equipo==0) {
			$this->myLogger->info("No equipo selected on prueba ".$this->prueba."; get default");
			$sql="SELECT ID FROM Equipos WHERE ( Prueba = ".$this->prueba." ) AND ( Nombre = '-- Sin asignar --' )";
			$rs=$this->query($sql);		
			if (!$rs) return $this->error($this->conn->error);
			$row=$rs->fetch_row();
			$equipo = $row[0];
			$rs->free();
		}
		// inscribimos en cada una de las jornadas solicitadas
		for ($numero=1;$numero<9;$numero++) {
			// si la jornada esta cerrada no se hace nada
			if ($this->jornadas[$numero]["Cerrada"]!=0) {
				$this->myLogger->info("La jornada $numero esta cerrada");
				continue;
			}
			// obtenemos el JornadaID
			$jornada=$this->jornadas[$numero]["ID"];
			// vemos si pide inscribirse en esta jornada. Si no lo pide, se mira si inscribir por defecto
			$solicita=http_request("J$numero","i",$this->declaradas[$numero]);
			if ($solicita) {
				// vamos a ver si esta ya inscrito. 
				$this->myLogger->debug("Insert/Update inscripcion Jornada:$numero ID:$jornada IDPerro:$idperro");
				// usamos una sentencia "replace" que equivala a "insert of update if exists"
				$sql="REPLACE INTO Inscripciones ( Jornada , IDPerro , Celo , Observaciones , Equipo , Pagado )
					VALUES ( $jornada , $idperro, $celo , '$observaciones' , $equipo , $pagado )";
				$rs=$this->query($sql);
				if (!$rs) return $this->error($this->conn->error);
			} else {
				// no solicita inscripcion: borrar datos
				$this->myLogger->debug("Borrar inscripcion Jornada:$numero ID:$jornada IDPerro:$idperro");
				$sql="DELETE FROM Inscripciones where ( (IDPerro=$idperro) AND (Jornada=$jornada))";
				$rs=$this->query($sql);
				if (!$rs) return $this->error($this->conn->error);
			}
			// actualizamos el orden de salida
			$res=$this->updateOrdenSalida($jornada,$idperro);
			if ($res===null) return $this->error($this->errormsg);
		}
		// all right return ok
		$this->myLogger->leave();
		return ""; // return ok
	}
	
	/**
	 * Remove all inscriptions of IDPerro in non-closed jornadas from provided prueba 
	 * @return {string} "" on success; null on error
	 */
	function remove() {
		$this->myLogger->enter();
		$idperro=http_request("IDPerro","i",0);
		if ($idperro==0) return $this->error("Invalid IDPerro ID"); 
		for ($n=1;$n<9;$n++) {
			$jornada=$this->jornadas[$n]["ID"];
			if ($this->jornadas[$n]["Cerrada"]!=0) {
				$this->myLogger->info("Skip delete IDPerro $idperro on closed Jornada $jornada");
				continue;
			}
			$sql="DELETE FROM Inscripciones where ( (IDPerro=$idperro) AND (Jornada=$jornada))";
			$res=$this->query($sql);
			if (!$res) return $this->error($this->conn->error); 
			$res=$this->updateOrdenSalida($jornada,$idperro);
			if ($res===null) $this->conn->error($this->errormsg);
		} // for every jornada on provided prueba
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * retrieve all dogs that has no inscitpion on this prueba
	 */
	function noinscritos() {
		$this->myLogger->enter();
		
		$search =  http_request("q","s","");
		$extra = '';
		if ($search!=='') $extra=" AND ( (PerroGuiaClub.Nombre LIKE '%$search%')
		OR ( NombreClub LIKE '%$search%') OR ( NombreGuia LIKE '%$search%' ) )";
		
		// !toma ya con la query :-) 
		$str="SELECT * FROM PerroGuiaClub
				WHERE 
					ID NOT IN ( SELECT Perro FROM Inscripciones WHERE (Prueba=".$this->prueba.") )
					$extra
				ORDER BY Club ASC, Categoria ASC, Grado ASC, Nombre ASC";

		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		
		// Fase 2: la tabla de resultados a devolver
		$data = array(); // result { total(numberofrows), data(arrayofrows)
		while($row = $rs->fetch_array()) array_push($data,$row);
		$rs->free();
		$result=array('total'=>count($data), 'rows'=>$data);
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * retrieve all inscriptions of stored prueba (no page/order selection)
	 */
	function inscritos() {
		$this->myLogger->enter();
	
		// evaluate offset and row count for query
		$id = $this->prueba;
		$search =  http_request("where","s","");
		// $extra= a single ')' or name search criterion
		$extra = '';
		if ($search!=='') $extra=" AND ( (PerroGuiaClub.Nombre LIKE '%$search%') 
				OR ( NombreClub LIKE '%$search%') OR ( NombreGuia LIKE '%$search%' ) ) ";

		// FASE 1: obtener lista de perros inscritos con sus datos
		$str="SELECT Inscripciones.ID AS ID, Dorsal , Inscripciones.Perro AS Perro , PerroGuiaClub.Nombre AS Nombre,
		Categoria , Grado , Celo , Guia , Club , NombreGuia, NombreClub, Equipo , Observaciones , J1,J2,J3,J4,J5,J6,J7,J8,Pagado
		FROM Inscripciones,PerroGuiaClub
		WHERE ( Inscripciones.Perro = PerroGuiaClub.ID) AND ( Prueba= $id )	$extra 
		ORDER BY NombreClub ASC, Categoria ASC, Grado ASC, Nombre ASC"; 
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
	
		// Fase 2: la tabla de resultados a devolver
		$data = array(); // result { total(numberofrows), data(arrayofrows)
		while($row = $rs->fetch_array()) array_push($data,$row);
		$rs->free();
		$result=array('total'=>count($data), 'rows'=>$data);
		$this->myLogger->leave();
		return $result;
	
	}
	
} /* end of class "Inscripciones" */

?>