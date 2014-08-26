<?php
require_once("DBObject.php");
require_once("Jornadas.php");
require_once(__DIR__."/../procesaInscripcion.php"); // to insert/remove inscriptions from mangas

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
		
		// obtenemos los restantes valores de la inscripcion
		$prueba=$this->pruebaID;
		$jornadas=http_request("Jornadas","i",0);
		$pagado=http_request("Pagado","i",0);
		$equipo=http_request("Equipo","i",$this->defaultTeam["ID"]);
		$celo=http_request("Celo","i",0);
		$observaciones="";
		
		// ok, ya tenemos todo. Vamos a inscribirle... pero solo en las jornadas abiertas
		$str= "INSERT INTO Inscripciones (Prueba,Perro,Celo,Observaciones,Equipo,Jornadas,Pagado)
			VALUES ($prueba,$perro,$celo,'$observaciones',$equipo,$jornadas,$pagado)";
		$res=$this->query($str);
		if (!$res) return $this->error($this->conn->error);
		// una vez inscrito, vamos a repasar la lista de jornadas y actualizar en caso necesario
		$inscripcionid=$this->conn->insert_id;
		// los datos de las mangas y resultados
		procesaInscripcion($prueba,$inscripcionid);
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
		$p=$this->pruebaID;
		if ($idperro<=0) return $this->error("Invalid IDPerro ID");

		// cogemos los datos actuales
		$res=$this->__selectObject(
						// idinscripcion, idprueba, idperro y dorsal no cambian
			/* SELECT */	"ID, Celo, Observaciones, Equipo, Jornadas, Pagado", 
			/* FROM */		"Inscripciones",
			/* WHERE */		"(Perro=$idperro) AND (Prueba=$p)"
		);
		if (($res==null) || ($res===""))
			return $this->error("El perro cond ID:$idperro no figura inscrito en la prueba:$p");

		// buscamos datos nuevos y mezclamos con los actuales
		$id=$res->ID;
		$celo=http_request("Celo","i",$res->Celo);
		$observaciones=http_request("Observaciones","s",$res->Observaciones);
		$equipo=http_request("Equipo","i",$res->Equipo);
		$pagado=http_request("Pagado","i",$res->Pagado);
		$jornadas=http_request("Jornadas","i",$res->Jornadas);

		// TODO: Make sure that form leaves unchanged Closed jornada's inscription state
		$jornadas=http_request("Jornadas","s",$res->Jornadas);
		// actualizamos bbdd
		$str="UPDATE Inscripciones 
			SET Celo=$celo, Observaciones='$observaciones', Equipo=$equipo, Jornadas=$jornadas, Pagado=$pagado
			WHERE ( ID=$id)";
		
		// actualizamos datos de inscripcion
		$res=$this->query($str);
		if (!$res) return $this->error($this->conn->error);
		
		// recalculamos la inscripcion, orden de salida y tabla de resultados
		procesaInscripcion($this->pruebaID,$id);
		
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
		$p=$this->pruebaID;
		if ($idperro<=0) return $this->error("Invalid Perro ID");
		// fase 0: obtenemos el ID de la inscripcion
		$res=$this->__singleSelect("ID", "Inscripciones", "(Perro=$idperro) AND (Prueba=$p");
		if (!$res) return $this->error("El perro con id:$idperro esta inscrito en la prueba:$p");
		$i=$res['ID'];
		// fase 1: actualizamos la DB para indicar que el perro no esta inscrito en ninguna jornada
		$sql="Update Inscripciones SET Jornadas = 0  WHERE (ID=$i)";
		// fase 2: eliminamos informacion del perro en los ordenes de salida y tabla de resultados
		procesaInscripcion($p, $i);
		// fase 3: finalmente eliminamos el perro de la tabla de inscripciones
		$sql="DELETE FROM Inscripciones WHERE (ID=$i)";
		$res=$this->query($sql);
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Recupera los datos de la inscripcion de un perro y una prueba dadas
	 * @param {integer} $id ID del perro
	 */
	function selectByPerro($idperro) {
		$this->myLogger->enter();
		$prueba=$this->pruebaID;
		$res=$this->__singleSelect(
				/* SELECT */ "*", 
				/* FROM */   "Inscripciones",
				/* WHERE */  "( Prueba=$prueba ) AND ( Perro=$idperro )");
		$this->myLogger->leave();
		return $res;
	}
	
	/**
	 * Recupera los datos de una inscripcion definida por su ID
	 * @param {integer} $id ID de la inscripcion
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		$prueba=$this->pruebaID;
		$res=$this->__singleSelect(
				/* SELECT */ "*",
				/* FROM */   "Inscripciones",
				/* WHERE */  "( ID=$id )");
		$this->myLogger->leave();
		return $res;
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
				ORDER BY Club ASC,Categoria ASC,Grado ASC";
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
		$result=array();
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
		// FASE 0: cuenta el numero total de inscritos
		$str="SELECT count(*)
		FROM Inscripciones,PerroGuiaClub,Equipos
		WHERE ( Inscripciones.Perro = PerroGuiaClub.ID) 
			AND ( Inscripciones.Prueba=$id ) 
			AND (Equipos.ID=Inscripciones.Equipo) $extra";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$row=$rs->fetch_array();
		$result["total"] = $row[0];
		$rs->free();
		// if (rowcount==0) no need to perform a second query
		if ($result["total"]==0) {
			$result["rows"]=array();
			return $result;
		}
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
		$result['rows']=$data;
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * retrieve all inscriptions of stored prueba and jornada
	 * @param {int} $jornadaID ID de jornada
	 */
	function inscritosByJornada($jornadaID) {
		$this->myLogger->enter();
		$pruebaid=$this->pruebaID;
		// Cogemos la lista de jornadas abiertas de esta prueba
		$j=new Jornadas("inscripciones::inscritosByJornada()",$this->pruebaID);
		$jornadas=$j->searchByPrueba();
		if ( ($jornadas===null) || ($jornadas==="") ) {
			return $this->error("$file::updateOrdenSalida() cannot get list of open Jornadas for prueba:".$this->pruebaID);
		}
		// por cada jornada abierta, miramos a ver si la ID coincide con la que buscamos
		$mask=0;
		foreach($jornadas["rows"] as $jornada) {
			if ($jornada['ID']==$jornadaID) $mask=1<<($jornada['Numero']-1); // 1..8
		}
		if ($mask==0) {
			return $this->error("$file::inscritosByJornada() cannot find open Jornada ID: $jornadaID in prueba:".$this->pruebaID);
		}
		// obtenemos la lista de perros inscritos con sus datos
		$result=$this->__select(
			/* SELECT */"Inscripciones.ID AS ID, Inscripciones.Prueba AS Prueba, Inscripciones.Perro AS Perro, 
				Dorsal, PerroGuiaClub.Nombre AS Nombre, Licencia, LOE_RRC, Categoria , Grado , Celo , Guia , Club ,
				NombreGuia, NombreClub, Equipos.ID AS Equipo,Equipos.Nombre AS NombreEquipo ,
				Inscripciones.Observaciones AS Observaciones, Jornadas, Pagado",
			/* FROM */	"Inscripciones,PerroGuiaClub,Equipos",
			/* WHERE */ "( Inscripciones.Perro = PerroGuiaClub.ID) AND 
				( Inscripciones.Prueba=$pruebaid ) AND ( ( Inscripciones.Jornadas&$mask ) != 0 ) AND
				(Equipos.ID=Inscripciones.Equipo)",
			/* ORDER BY */ "Categoria ASC , Celo ASC, Equipo",
			/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}
} /* end of class "Inscripciones" */

?>