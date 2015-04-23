<?php
/*
Inscripciones.php

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
require_once("Jornadas.php");
require_once(__DIR__."/../procesaInscripcion.php"); // to insert/remove inscriptions from mangas

class Inscripciones extends DBObject {
	
	protected $pruebaID;
	protected $insertid;
	
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
		$this->insertid=0; // initial value
	}
	
	/**
	 * Create a new inscripcion
	 * @param {integer} perro ID del perro
	 * @return empty string if ok; else null
	 */
	function insert($idperro) {
		$this->myLogger->enter();
		if ($idperro<=0) return $this->error("Invalid IDPerro ID");
		$res= $this->__SelectObject(
			/* SELECT */ "count(*) AS count",
			/* FROM */ "Inscripciones",
			/* WHERE */ "( Prueba=".$this->pruebaID.") AND ( Perro=$idperro )"
		);
		if (!is_object($res))
			return $this->error("No puedo obtener datos del perro con ID:$idperro para la prueba:{$this->pruebaID}");
		if($res->count>0)
			return $this->error("El perro con ID:$idperro ya esta inscrito en la prueba:{$this->pruebaID}");
		
		// obtenemos los restantes valores de la inscripcion
		$prueba=$this->pruebaID;
		$jornadas=http_request("Jornadas","i",0);
		$pagado=http_request("Pagado","i",0);
		$celo=http_request("Celo","i",0);
		$observaciones="";
		
		// ok, ya tenemos todo. Vamos a inscribirle... pero solo en las jornadas abiertas
		$str= "INSERT INTO Inscripciones (Prueba,Perro,Celo,Observaciones,Jornadas,Pagado)
			VALUES ($prueba,$idperro,$celo,'$observaciones',$jornadas,$pagado)";
		$res=$this->query($str);
		$this->insertid=$this->conn->insert_id;
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
			/* SELECT */	"ID, Celo, Observaciones, Jornadas, Pagado", 
			/* FROM */		"Inscripciones",
			/* WHERE */		"(Perro=$idperro) AND (Prueba=$p)"
		);
		if (!is_object($res))
			return $this->error("El perro cond ID:$idperro no figura inscrito en la prueba:$p");

		// buscamos datos nuevos y mezclamos con los actuales
		$id=$res->ID;
		$celo=http_request("Celo","i",$res->Celo);
		$observaciones=http_request("Observaciones","s",$res->Observaciones);
		$pagado=http_request("Pagado","i",$res->Pagado);
		$jornadas=http_request("Jornadas","i",$res->Jornadas);

		// actualizamos bbdd
		$str="UPDATE Inscripciones 
			SET Celo=$celo, Observaciones='$observaciones', Jornadas=$jornadas, Pagado=$pagado
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
		$res=$this->__selectAsArray("ID", "Inscripciones", "(Perro=$idperro) AND (Prueba=$p)");
		if (!is_array($res)) return $this->error("El perro con id:$idperro no esta inscrito en la prueba:$p");
		$i=$res['ID'];
		// fase 1: actualizamos la DB para indicar que el perro no esta inscrito en ninguna jornada
		$sql="UPDATE Inscripciones SET Jornadas = 0  WHERE (ID=$i)";
		$res=$this->query($sql);
		if (!$res) return $this->error($this->conn->error);
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
		$res=$this->__selectAsArray(
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
		$obj=$this->__getObject("Inscripciones",$id);
		if (!is_object($obj))	return $this->error("No Inscripcion found with ID=$id");
		$data= json_decode(json_encode($obj), true); // convert object to array
		$this->myLogger->leave();
		return $data;
	}
	
	/**
	 * retrieve all dogs that has no inscitpion on this prueba
	 */
	function noinscritos() {
		$this->myLogger->enter();
		
		$id = $this->pruebaID;
		$fed =  http_request("Federation","i",0);
		$search =  http_request("where","s","");
		$extra = '';
		if ($search!=='') $extra=" AND ( (PerroGuiaClub.Nombre LIKE '%$search%')
		OR ( NombreClub LIKE '%$search%') OR ( NombreGuia LIKE '%$search%' ) ) ";

		$page=http_request("page","i",1);
		$rows=http_request("rows","i",50);
		$limit="";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit=" ".$offset.",".$rows;
		};
		$order=getOrderString( 
			http_request("sort","s",""),
			http_request("order","s",""),
			"NombreClub ASC, Categoria ASC, Grado ASC, Nombre ASC"
		);
		$result= $this->__select(
			/* SELECT */	"*",
			/* FROM */		"PerroGuiaClub",
			/* WHERE */		"( Federation = $fed ) AND ID NOT IN ( SELECT Perro FROM Inscripciones WHERE (Prueba=$id) ) $extra ",
			/* ORDER BY */	$order,
			/* LIMIT */		$limit
		);

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
				Raza, Licencia, LOE_RRC, Categoria , Grado , Celo , Guia , Club ,
				NombreGuia, NombreClub, Inscripciones.Observaciones AS Observaciones, Jornadas, Pagado
			FROM Inscripciones,PerroGuiaClub
			WHERE ( Inscripciones.Perro = PerroGuiaClub.ID) 
				AND ( Inscripciones.Prueba=$id )
			ORDER BY NombreClub ASC,Grado ASC, Categoria ASC, Nombre ASC";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
	
		// Fase 2: la tabla de resultados a devolver
		$data = array(); // result { total(numberofrows), data(arrayofrows)
		while($row = $rs->fetch_array(MYSQLI_ASSOC)) {
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
	 * Tell how many dogs have inscription in this contest
	 */
	function howMany() {
		return $this->__selectObject("count(*) AS Inscritos","Inscripciones","(Prueba={$this->pruebaID})");
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
		FROM Inscripciones,PerroGuiaClub
		WHERE ( Inscripciones.Perro = PerroGuiaClub.ID) 
			AND ( Inscripciones.Prueba=$id ) $extra";
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
				Raza, Licencia, LOE_RRC, Categoria , Grado , Celo , Guia , Club , 
				NombreGuia, NombreClub, Inscripciones.Observaciones AS Observaciones, Jornadas, Pagado
			FROM Inscripciones,PerroGuiaClub
			WHERE ( Inscripciones.Perro = PerroGuiaClub.ID) AND ( Inscripciones.Prueba=$id ) $extra 
		ORDER BY $order $limit"; 
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
	
		// Fase 2: la tabla de resultados a devolver
		$data = array(); // result { total(numberofrows), data(arrayofrows)
		while($row = $rs->fetch_array(MYSQLI_ASSOC)) {
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
	
	/*
	 * As inscritos, but dont use page nor search and list only those inscritos that belongs to provided team
	 */
	function inscritosByTeam($team) {
		$this->myLogger->enter();
		// obtenemos los datos del equipo
		$teamobj=$this->__getObject("Equipos",$team);
		if (!is_object($teamobj))
			return $this->error("No puedo obtener datos del equipo con ID: $team");
		// vemos el numero de la jornada asociada
		$jornadaobj=$this->__getObject("Jornadas",$teamobj->Jornada);
		if (!is_object($jornadaobj))
			return $this->error("No puedo obtener datos de la jornada: {$teamobj->Jornada} asociada al equipo: $team");
		$mask= 1 << ($jornadaobj->Numero -1 );
		$prueba= $this->pruebaID;
		$rows=array();
	
		// extraemos la lista de inscritos
		$lista=$this->__select(
				/*select*/	"Inscripciones.ID AS ID, Inscripciones.Prueba AS Prueba, {$teamobj->Jornada} AS Jornada,
				Dorsal, Inscripciones.Perro AS Perro , PerroGuiaClub.Nombre AS Nombre,
				Raza, Licencia, LOE_RRC, Categoria , Grado , Celo , Guia , Club ,
				NombreGuia, NombreClub, $team AS Equipo, '{$teamobj->Nombre}' AS NombreEquipo ,
				Inscripciones.Observaciones AS Observaciones, Jornadas, Pagado",
				/* from */	"Inscripciones,PerroGuiaClub",
				/* where */ "( Inscripciones.Perro = PerroGuiaClub.ID)	AND ( Inscripciones.Prueba=$prueba ) AND ( ((Inscripciones.Jornadas & $mask))<>0 )",
				/* order */ "",
				/* limit */ ""
			);
		// reindex using perro as index
		$inscripciones=array();
		foreach($lista['rows'] as $inscripcion) { 
			$inscripciones[$inscripcion['Perro']]=$inscripcion;
		}
		// ahora comprobamos consistencia de los listados de equipos
		// y generamos el array de resultados
		$rows=array();
		$list=explode(',',$teamobj->Miembros);
		foreach ( $list as $perro) {
			if (strpos($perro,"BEGIN")!==false) continue;
			if (strpos($perro,"END")!==false) continue;
			if (!array_key_exists($perro,$inscripciones)) {
				$this->error("Inscripciones::inscritosByTeam():El perro $perro del equipo $team no esta inscrito en la jornada {$teamobj->Jornada} de la prueba $prueba");
				$this->removeFromList($perro); // cleanup. should not be needed, but....
			}
			// todo correcto: anyadimos el perro a la lista
			array_push($rows,$inscripciones[$perro]);
		}
		// ok: componemos resultado y retornamos
		$result= array( 'total' => count($rows), 'rows' => $rows);
		$this->myLogger->leave();
		return $result;
	}
			
	/*
	 * Reorder dorsales by mean of club,categoria,grado,nombre
	 */
	function reorder() {
		$this->myLogger->enter();
		// ordenamos los perros por club, categoria grado
		$inscritos=$this->__select(
				"Perro,Nombre,NombreClub,Categoria,Grado",
				"Inscripciones,PerroGuiaClub",
				"(Inscripciones.Prueba={$this->pruebaID}) AND (Inscripciones.Perro=PerroGuiaClub.ID)", 
				"NombreClub ASC,Grado ASC, Categoria ASC, Nombre ASC", 
				"");
		if (!is_array($inscritos))
			return $this->error("reorder(): Canot retrieve list of inscritos");

		//usaremos prepared statements para acelerar
		$str1="UPDATE Inscripciones SET Dorsal=? WHERE (Prueba={$this->pruebaID}) AND (Perro=?)";
		$str2="UPDATE Resultados SET DORSAL=? WHERE (Prueba={$this->pruebaID}) AND (Perro=?)";
			
		$stmt1=$this->conn->prepare($str1);
		if (!$stmt1) return $this->error($this->conn->error);
		$stmt2=$this->conn->prepare($str2);
		if (!$stmt2) return $this->error($this->conn->error);
			
		$res1=$stmt1->bind_param('ii',$dorsal1,$perro1);
		if (!$res1) return $this->error($stmt1->error);
		$res2=$stmt2->bind_param('ii',$dorsal2,$perro2);
		if (!$res2) return $this->error($stmt2->error);

		$dorsal=1;
		$len=count($inscritos['rows']);
		
		for($n=0;$n<$len;$n++,$dorsal++) { 
			// actualizamos las tabla de inscripciones y resultados
			$dorsal1=$dorsal; $dorsal2=$dorsal;
			$perro1=$inscritos['rows'][$n]['Perro']; $perro2=$perro1;
			$res=$stmt1->execute();
			if (!$res) return $this->error($stmt1->error);
			$res=$stmt2->execute();
			if (!$res) return $this->error($stmt2->error);
		}
		$stmt1->close();
		$stmt2->close();
		$this->myLogger->leave();
		return "";
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
			/* SELECT */"Inscripciones.ID AS ID, Inscripciones.Prueba AS Prueba, Inscripciones.Perro AS Perro, Raza,
				Dorsal, PerroGuiaClub.Nombre AS Nombre, Licencia, LOE_RRC, Categoria , Grado , Celo , Guia , Club ,
				NombreGuia, NombreClub,	Inscripciones.Observaciones AS Observaciones, Jornadas, Pagado",
			/* FROM */	"Inscripciones,PerroGuiaClub",
			/* WHERE */ "( Inscripciones.Perro = PerroGuiaClub.ID) AND 
				( Inscripciones.Prueba=$pruebaid ) AND ( ( Inscripciones.Jornadas&$mask ) != 0 ) ",
			/* ORDER BY */ "NombreClub ASC, Grado ASC , Categoria ASC, Celo ASC",
			/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}
} /* end of class "Inscripciones" */

?>