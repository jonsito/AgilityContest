<?php
require_once("DBObject.php");
require_once("Jornadas.php");
require_once("OrdenSalida.php"); // to insert/remove inscriptions from mangas

class Inscripciones extends DBObject {
	
	protected $pruebaID;
	protected $defaultTeam; //  {array} datos del equipo por defecto para esta prueba
	
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
		$this->pruebaID=$prueba;
		
		// obtenemos el equipo por defecto para esta prueba
		$res= $this->__singleSelect(
			/* SELECT */ "ID",
			/* FROM */   "Equipos",
			/* WHERE */ "( Prueba = $prueba ) AND ( Nombre = '-- Sin asignar --' )"
		);
		if (($res===null) || ($res==="")) {
			$this->errormsg="$file::construct() cannot get default team data for this prueba";
			throw new Exception($this->errormsg);
		}
		$this->defaultTeam=$res;
	}
	
	function updateOrdenSalida($operation,$idperro) {
		// obtenemos un manejador de ordenes de salida
		$os=new OrdenSalida("inscripciones::ordensalida");
		
		// Cogemos la lista de jornadas abiertas de esta prueba
		$j=new Jornadas($operation,$this->pruebaID);
		$jornadas=$j->searchByPrueba();
		if ( ($jornadas===null) || ($jornadas==="") ) {
			return $this->error("$file::updateOrdenSalida() cannot get list of open Jornadas for prueba:".$this->pruebaID);
		}
		// por cada jornada abierta, cogemos la lista de mangas
		foreach($jornadas["rows"] as $jornada) {
			$idjornada=$jornada["ID"];
			$mangas= $this->__select("ID","Mangas","( Jornada=$idjornada )","Tipo ASC","");
			if ( ($mangas===null) || ($mangas==="") ) {
				return $this->error("$file::updateOrdenSalida() cannot get list of mangas for jornada:$idjornada on prueba:".$this->pruebaID);
			}
			// Por cada manga de cada jornada, actualizamos -si es necesario- el orden de salida del perro
			foreach($mangas["rows"] as $manga) {
				$os->handle($idjornada,$manga["ID"],$idperro);
			}
		}
		return ""; // no errors
	}
	
	/**
	 * Create a new inscripcion
	 * @param {integer} perro ID del perro
	 * @return empty string if ok; else null
	 */
	function insert($perro) {
		$this->myLogger->enter();
		if ($perro<=0) return $this->error("Invalid IDPerro ID");
		$res= $this->__SelectObject(
			/* SELECT */ "count(*) AS count",
			/* FROM */ "Inscripciones",
			/* WHERE */ "( Prueba=".$this->pruebaID.") AND ( Perro=$perro )"
		);
		if($res->count>0)
			return $this->error("El perro con ID:".$perro." ya esta inscrito en la prueba:".$this->pruebaID);
		
		// Generamos los valores por defecto de "celo" "observaciones" "jornadas" y "pagado" para la inscripcion.
		// Para ello, buscamos las jornadas que no están ni cerradas ni declaradas como "-- Sin asignar --" y componemos
		// el valor por defecto de jornadas y el dinero "pagado" por la inscripcion
		$res=$this->__selectObject(
			/* SELECT */ "12*count(*) AS Pagado, IFNULL(SUM(1<<NUMERO),0) AS Jornadas",
			/* FROM */   "Jornadas",
			/* WHERE */  "(Jornadas.Prueba=".$this->pruebaID.") AND (Jornadas.Cerrada=0) AND (Jornadas.Nombre!='-- Sin asignar --')"
		);
		$prueba=$this->pruebaID;
		$jornadas=$res->Jornadas;
		$pagado=$res->Pagado;
		$equipo=$this->defaultTeam["ID"];
		$celo=0;
		$observaciones="";
		
		// ok, ya tenemos todo. Vamos a inscribirle... pero solo en las jornadas abiertas
		$str= "INSERT INTO Inscripciones (Prueba,Perro,Celo,Observaciones,Equipo,Jornadas,Pagado)
			VALUES ($prueba,$perro,$celo,'$observaciones',$equipo,$jornadas,$pagado)";
		$res=$this->query($str);
		if (!$res) return $this->error($this->conn->error);
		
		// una vez inscrito, vamos a repasar la lista de jornadas y actualizar en caso necesario
		// los datos de las mangas (en concreto el orden de salida)
		$res=$this->updateOrdenSalida("Insert inscripcion of perro:$perro",$perro);
		if ($res===null) return $this->error($this->errormsg);
		// all right return ok
		$this->myLogger->leave();
		return ""; // return ok
	}
	
	/**
	 * Update an inscripcion
	 * @param {integer} perro ID del perro
	 * @return empty string if ok; else null
	 */
	function update($idperro) {
		$this->myLogger->enter();
		if ($idperro<=0) return $this->error("Invalid IDPerro ID");

		// cogemos los datos actuales
		$res=$this->__selectObject(
			/* SELECT */ "Celo, Observaciones, Equipo, Jornadas, Pagado", // idinscripcion, idprueba, idperro y dorsal no cambian
			/* FROM */ "Inscripciones",
			/* WHERE */ "(Perro=$idperro) AND (Prueba=".$this->pruebaID.")"
		);
		if (($res==null) || ($res===""))
			return $this->error("El perro cond ID:$idperro no esta inscrito en la prueba: ".$this->pruebaID.")");

		// buscamos datos nuevos y mezclamos con los actuales
		$celo=http_request("Celo","i",$res->Celo);
		$observaciones=http_request("Observaciones","s",$res->Observaciones);
		$equipo=http_request("Equipo","i",$res->Equipo);
		$pagado=http_request("Pagado","i",$res->Pagado);

		// TODO: Make sure that form leaves unchanged Closed jornada's inscription state
		$jornadas=http_request("Jornadas","s",$res->Jornadas);
		// actualizamos bbdd
		$str="UPDATE Inscripciones 
			SET Celo=$celo , Observaciones='$observaciones' , Equipo=$equipo , Jornadas=$jornadas , Pagado=$pagado
			WHERE ( Perro=$idperro ) AND ( Prueba=".$this->pruebaID." )";
		
		// actualizamos datos de inscripcion
		$res=$this->query($str);
		if (!$res) return $this->error($this->conn->error);
		
		// recalculamos orden de salida en cada jornada
		$res=$this->updateOrdenSalida("Update inscripcion of perro:$idperro",$idperro);
		if ($res===null) return $this->error($this->errormsg);
		
		// everything ok. return
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Remove all inscriptions of IDPerro in non-closed jornadas from provided prueba 
	 * @return {string} "" on success; null on error
	 */
	function delete($idperro) {
		$this->myLogger->enter();
		if ($idperro<=0) return $this->error("Invalid IDPerro ID");
		
		// Eliminamos el perro de la tabla de inscripciones
		$sql="DELETE FROM Inscripciones WHERE (Perro=$idperro) AND (Prueba=".$this->pruebaID.")";
		$res=$this->query($sql);
		if (!$res) return $this->error($this->conn->error);
		
		// recalculamos orden de salida en cada jornada abierta
		$res=$this->updateOrdenSalida("Delete inscripcion of perro:$idperro",$idperro);
		if ($res===null) return $this->error($this->errormsg);
		
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * retrieve all dogs that has no inscitpion on this prueba
	 */
	function noinscritos() {
		$this->myLogger->enter();
		
		$id = $this->pruebaID;
		$search =  http_request("where","s","");
		$extra = '';
		if ($search!=='') $extra=" AND ( (PerroGuiaClub.Nombre LIKE '%$search%')
		OR ( NombreClub LIKE '%$search%') OR ( NombreGuia LIKE '%$search%' ) ) ";

		$page=http_request("page","i",1);
		$rows=http_request("rows","i",50);
		$limit="";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit=" LIMIT ".$offset.",".$rows;
		};
		$order=getOrderString( 
			http_request("sort","s",""),
			http_request("order","s",""),
			"NombreClub ASC, Categoria ASC, Grado ASC, Nombre ASC"
		);
		$str="SELECT * FROM PerroGuiaClub
				WHERE 
					ID NOT IN ( SELECT Perro FROM Inscripciones WHERE (Prueba=$id) )
					$extra
				ORDER BY $order $limit";

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
	 * retrieve all inscriptions in stored prueba
	 * no search, no order, no limit, just retrieve all in 'Dorsal ASC' order
	 */
	function enumerate() {
		$this->myLogger->enter();
		// evaluate offset and row count for query
		$id = $this->pruebaID;
		// FASE 1: obtener lista de perros inscritos con sus datos
		$str="SELECT Inscripciones.ID AS ID, Inscripciones.Prueba AS Prueba, Dorsal , 
				Inscripciones.Perro AS Perro , PerroGuiaClub.Nombre AS Nombre,
				Licencia, LOE_RRC, Categoria , Grado , Celo , Guia , Club ,
				NombreGuia, NombreClub, Equipos.ID AS Equipo,Equipos.Nombre AS NombreEquipo ,
				Inscripciones.Observaciones AS Observaciones, Jornadas, Pagado
			FROM Inscripciones,PerroGuiaClub,Equipos
			WHERE ( Inscripciones.Perro = PerroGuiaClub.ID) 
				AND ( Inscripciones.Prueba=$id ) 
				AND (Equipos.ID=Inscripciones.Equipo)
				ORDER BY Dorsal ASC";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
	
		// Fase 2: la tabla de resultados a devolver
		$data = array(); // result { total(numberofrows), data(arrayofrows)
		while($row = $rs->fetch_array()) {
			$row['J1']=($row['Jornadas']&0x0001)?1:0;
			$row['J2']=($row['Jornadas']&0x0002)?1:0;
			$row['J3']=($row['Jornadas']&0x0004)?1:0;
			$row['J4']=($row['Jornadas']&0x0008)?1:0;
			$row['J5']=($row['Jornadas']&0x0010)?1:0;
			$row['J6']=($row['Jornadas']&0x0020)?1:0;
			$row['J7']=($row['Jornadas']&0x0040)?1:0;
			$row['J8']=($row['Jornadas']&0x0080)?1:0;
			array_push($data,$row);
		}
		$rs->free();
		$result=array('total'=>count($data), 'rows'=>$data);
		$this->myLogger->leave();
		return $result;
	
	}
	
	/**
	 * retrieve all inscriptions of stored prueba
	 */
	function inscritos() {
		$this->myLogger->enter();
	
		// evaluate offset and row count for query
		$id = $this->pruebaID;
		$search =  http_request("where","s","");
		// $extra= a single ')' or name search criterion
		$extra = '';
		if ($search!=='') $extra=" AND ( (PerroGuiaClub.Nombre LIKE '%$search%') 
				OR ( NombreClub LIKE '%$search%') OR ( NombreGuia LIKE '%$search%' ) ) ";
		$page=http_request("page","i",1);
		$rows=http_request("rows","i",50);
		$limit="";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit=" LIMIT ".$offset.",".$rows;
		};
		$order=getOrderString( 
			http_request("sort","s",""),
			http_request("order","s",""),
			"NombreClub ASC, Categoria ASC, Grado ASC, Nombre ASC"
		);
		// FASE 1: obtener lista de perros inscritos con sus datos
		$str="SELECT Inscripciones.ID AS ID, Inscripciones.Prueba AS Prueba, Dorsal, Inscripciones.Perro AS Perro , PerroGuiaClub.Nombre AS Nombre,
				Licencia, LOE_RRC, Categoria , Grado , Celo , Guia , Club , 
				NombreGuia, NombreClub, Equipos.ID AS Equipo,Equipos.Nombre AS NombreEquipo , 
				Inscripciones.Observaciones AS Observaciones, Jornadas, Pagado
			FROM Inscripciones,PerroGuiaClub,Equipos
			WHERE ( Inscripciones.Perro = PerroGuiaClub.ID) AND ( Inscripciones.Prueba=$id ) AND (Equipos.ID=Inscripciones.Equipo) $extra 
			ORDER BY $order $limit"; 
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
	
		// Fase 2: la tabla de resultados a devolver
		$data = array(); // result { total(numberofrows), data(arrayofrows)
		while($row = $rs->fetch_array()) {
			$row['J1']=($row['Jornadas']&0x0001)?1:0;
			$row['J2']=($row['Jornadas']&0x0002)?1:0;
			$row['J3']=($row['Jornadas']&0x0004)?1:0;
			$row['J4']=($row['Jornadas']&0x0008)?1:0;
			$row['J5']=($row['Jornadas']&0x0010)?1:0;
			$row['J6']=($row['Jornadas']&0x0020)?1:0;
			$row['J7']=($row['Jornadas']&0x0040)?1:0;
			$row['J8']=($row['Jornadas']&0x0080)?1:0;
			array_push($data,$row);
		}
		$rs->free();
		$result=array('total'=>count($data), 'rows'=>$data);
		$this->myLogger->leave();
		return $result;
	}
	
} /* end of class "Inscripciones" */

?>