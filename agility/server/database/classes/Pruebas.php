<?php
/*
Pruebas.php

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
require_once("Tandas.php");
require_once("Jornadas.php");

class Pruebas extends DBObject {
	
	function insert() {
		$this->myLogger->enter();
		// componemos un prepared statement
		$sql ="INSERT INTO Pruebas (Nombre,Club,Ubicacion,Triptico,Cartel,Observaciones,RSCE,Selectiva,Cerrada)
			   VALUES(?,?,?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('sissssiii',$nombre,$club,$ubicacion,$triptico,$cartel,$observaciones,$rsce,$selectiva,$cerrada);
		if (!$res) return $this->error($this->conn->error);
		
		// iniciamos los valores, chequeando su existencia
		$nombre =	http_request("Nombre","s",null,false);
		$club =		http_request("Club","i",0);
		$ubicacion=	http_request("Ubicacion","s",null,false);
		$triptico =	http_request("Triptico","s",null,false);
		$cartel =	http_request("Cartel","s",null,false);
		$observaciones = http_request("Observaciones","s",null,false);
		$rsce =	http_request("RSCE","i",0);
		$selectiva =	http_request("Selectiva","i",0);
		$cerrada =	http_request("Cerrada","i",0);
		$this->myLogger->debug("Nombre: $nombre Club: $club Ubicacion: $ubicacion Observaciones: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error);
		
		// retrieve PruebaID on newly create prueba
		$pruebaid=$this->conn->insert_id;
		$stmt->close();
		
		// create eight journeys per contest
		for ($n=1;$n<9;$n++) {
			$sql ="INSERT INTO Jornadas (Prueba,Numero,Nombre,Fecha,Hora)
			VALUES ($pruebaid,$n,'-- Sin asignar --','2013-01-01','00:00:00')";
			$res=$this->query($sql);
			if (!$res) return $this->error($this->conn->error);
			// retrieve ID of inserted jornada
			$jornadaid=$this->conn->insert_id;
			
			// create default team for each journey
			$str="INSERT INTO Equipos (Prueba,Jornada,Orden,Nombre,Observaciones,Miembros,DefaultTeam)
			VALUES ($pruebaid,$jornadaid,1,'-- Sin asignar --','NO BORRAR: PRUEBA $pruebaid JORNADA $jornadaid - Equipo por defecto','BEGIN,END',1 )";
			$res=$this->query($str);
			if (!$res) return $this->error($this->conn->error);
			
			// regenerate Orden_Tandas field
			$ot=new Tandas("Pruebas::Insert()",$pruebaid,$jornadaid);
			$ot->populateJornada();
		}
		// arriving here means everything ok. notify success
		$this->myLogger->leave();
		return "";
	}
	
	function update($pruebaid) {
		$this->myLogger->enter();
		if ($pruebaid<=0) return $this->error("pruebas::update() Invalid Prueba ID:$pruebaid");
		// componemos un prepared statement
		$sql ="UPDATE Pruebas
				SET Nombre=? , Club=? , Ubicacion=? , Triptico=? , Cartel=?, Observaciones=?, RSCE=?, Selectiva=?, Cerrada=?
				WHERE ( ID=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('sissssiiii',$nombre,$club,$ubicacion,$triptico,$cartel,$observaciones,$rsce,$selectiva,$cerrada,$id);
		if (!$res) return $this->error($this->conn->error);
		
		// iniciamos los valores, chequeando su existencia
		$nombre =	http_request("Nombre","s",null,false);
		$id =		$pruebaid;
		$club =		http_request("Club","i",0);
		$ubicacion=	http_request("Ubicacion","s",null,false);
		$triptico =	http_request("Triptico","s",null,false);
		$cartel =	http_request("Cartel","s",null,false);
		$observaciones = http_request("Observaciones","s",null,false);
		$rsce =	http_request("RSCE","i",0);
		$selectiva =	http_request("Selectiva","i",0);
		$cerrada =	http_request("Cerrada","i",0);
		$this->myLogger->debug("Nombre: $nombre Club: $club Ubicacion: $ubicacion Observaciones: $observaciones");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		$stmt->close();
		return "";
	}
	
	/**
	 * Borra una prueba
	 * @param {integer} $id ID de la prueba
	 * @return string
	 */
	function delete($id) {
		$this->myLogger->enter();
		// pruebaID==1 is default prueba, so avoid deletion
		if ($id<=1) return $this->error("pruebas::delete() Invalid Prueba ID:$id");
		// Borramos resultados asociados a esta prueba
		$res=$this->query("DELETE FROM Resultados WHERE ( Prueba=$id)");
		// Borramos inscripciones de esta prueba
		$res=$this->query("DELETE FROM Inscripciones WHERE ( Prueba=$id)");
		// Borramos las jornadas (y mangas) de esta prueba
		$j=new Jornadas("Pruebas.php",$id);
		$j->deleteByPrueba();
		// Borramos tambien las tandas de las jornadas de esta prueba
		$res=$this->query("DELETE FROM Tandas WHERE ( Prueba=$id)");
		// finalmente intentamos eliminar la prueba
		$res= $this->query("DELETE FROM Pruebas WHERE (ID=$id)");
		if (!$res) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Lista pruebas ordenando por los parametros especificados y con criterios de busqueda
	 * @return null on error, else array in jquery expected format
	 */
	function select() {
		$this->myLogger->enter();
		$sort=getOrderString( //needed to properly handle multisort requests from datagrid
			http_request("sort","s",""),
			http_request("order","s",""),
			"Nombre ASC"
		);
		$search=http_Request("where","s","");
		$closed= http_request("closed","i",0); // si esta declarada, se incluyen las pruebas cerradas
		$page=http_request("page","i",1);
		$rows=http_request("rows","i",50);
		$limit="";
		$where="";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit="".$offset.",".$rows;
		}
		if ( ($search!=="") && ($closed==0) )
			$where="( (Pruebas.Club=Clubes.ID) && ( Pruebas.Cerrada=0 ) && 	( (Pruebas.Nombre LIKE '%$search%') OR ( Clubes.Nombre LIKE '%$search%') OR ( Ubicacion LIKE '%$search%' ) ) ) ";
		if ( ($search!=="") && ($closed!=0) )
			$where="( (Pruebas.Club=Clubes.ID) && ( (Pruebas.Nombre LIKE '%$search%') OR ( Clubes.Nombre LIKE '%$search%') OR ( Ubicacion LIKE '%$search%' ) ) )";
		if ( ($search==="") && ($closed==0) )
			$where="( (Pruebas.Club=Clubes.ID) && ( Pruebas.Cerrada=0 ) )";
		if ( ($search==="") && ($closed!=0) )
			$where="(Pruebas.Club=Clubes.ID)";

		// execute query to retrieve $rows starting at $offset
		$result=$this->__select(
				/* SELECT */ "Pruebas.ID AS ID, Pruebas.Nombre AS Nombre, Pruebas.Club AS Club,Clubes.Nombre AS NombreClub,
							Pruebas.Ubicacion AS Ubicacion,Pruebas.Triptico AS Triptico, Pruebas.Cartel AS Cartel,
							Pruebas.RSCE AS RSCE, Pruebas.Selectiva AS Selectiva,
							Pruebas.Cerrada AS Cerrada, Pruebas.Observaciones AS Observaciones",
				/* FROM */ "Pruebas,Clubes",
				/* WHERE */ $where,
				/* ORDER BY */ $sort,
				/* LIMIT */ $limit
		);
		$this->myLogger->leave();
		return $result;
	}
	
	/** 
	 * lista de pruebas abiertas.
	 * As select but not sort criteria and show only open contests. Used in combogrids
	 */
	function enumerate() {
		$this->myLogger->enter();

		// evaluate search criteria for query
		$q=http_request("q","s",null);
		$where= "(Pruebas.Club=Clubes.ID) && ( Pruebas.Cerrada=0 )";
		if($q!=="") $where="(Pruebas.Club=Clubes.ID) && ( Cerrada=0 ) AND ( (Pruebas.Nombre LIKE '%$q%' ) OR (Clubes.Nombre LIKE '%$q%') OR (Pruebas.Observaciones LIKE '%$q%') )";
		// retrieve result from parent __select() call
		$result= $this->__select(
				/* SELECT */ "Pruebas.ID AS ID, Pruebas.Nombre AS Nombre, Pruebas.Club AS Club,Clubes.Nombre AS NombreClub,
							Pruebas.Ubicacion AS Ubicacion, Pruebas.Triptico AS Triptico, Pruebas.Cartel AS Cartel, 
							Pruebas.RSCE AS RSCE, Pruebas.Selectiva AS Selectiva, Pruebas.Cerrada AS Cerrada, Pruebas.Observaciones AS Observaciones",
				/* FROM */ "Pruebas,Clubes",
				/* WHERE */ $where,
				/* ORDER BY */ "Nombre ASC",
				/* LIMIT */ ""
		);
		// return composed array
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Retrieve data on requested prueba id
	 * @param {integer} $id prueba ID
	 * @return null on error, associative array on success
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("prueba:::selectByID() Invalid Prueba ID:$id");

		// make query
		$data= $this->__selectAsArray(
				/* SELECT */ "Pruebas.ID AS ID, Pruebas.Nombre AS Nombre, Pruebas.Club AS Club,Clubes.Nombre AS NombreClub,
					Pruebas.Ubicacion AS Ubicacion,Pruebas.Triptico AS Triptico, Pruebas.Cartel AS Cartel,
					Pruebas.RSCE AS RSCE, Pruebas.Selectiva AS Selectiva,
					Pruebas.Cerrada AS Cerrada, Pruebas.Observaciones AS Observaciones",
				/* FROM */ "Pruebas,Clubes",
				/* WHERE */ "( Clubes.ID=Pruebas.Club) && ( Pruebas.ID=$id )"
		);
		if (!is_array($data))	return $this->error("No Prueba found with ID=$id");
		$data['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		return $data;
	}
}

?>