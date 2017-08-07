<?php
/*
Inscripciones.php

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
	public $insertid;
	
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
     * @param {int} perro ID del perro
     * @return {string} empty string if ok; else null
     */
	function insert($idperro) {
        // obtenemos los restantes valores de la inscripcion
        $prueba=$this->pruebaID;
        $jornadas=http_request("Jornadas","i",0);
        $pagado=http_request("Pagado","i",0);
        $celo=http_request("Celo","i",0);
        $observaciones="";
        $res=$this->realInsert($idperro,$prueba,$jornadas,$pagado,$celo,$observaciones);
        if (is_string($res)) return $res; // error
        return ""; // return ok
    }

	function realInsert($idperro,$prueba,$jornadas,$pagado,$celo,$observaciones) {
		$this->myLogger->enter();
		if ($idperro<=0) return $this->error("Invalid IDPerro ID");
		$res= $this->__selectObject(
			/* SELECT */ "*",
			/* FROM */ "Inscripciones",
			/* WHERE */ "( Prueba=".$this->pruebaID.") AND ( Perro=$idperro )"
		);
		if($res!==null){ // already inscribed, try to update
            if (!is_object($res)) return $res;
            $this->myLogger->notice("El perro con ID:$idperro ya esta inscrito en la prueba:{$this->pruebaID}");
            $this->real_update($jornadas,$celo,$observaciones,$pagado,$res->ID);
            return $res->Dorsal;
        }

		// ok, ya tenemos lo necesario. Vamos a inscribirle... pero solo en las jornadas abiertas
		$str= "INSERT INTO Inscripciones (Prueba,Perro,Celo,Observaciones,Jornadas,Pagado)
			VALUES ($prueba,$idperro,$celo,'$observaciones',$jornadas,$pagado)";
		$res=$this->query($str);
		$this->insertid=$this->conn->insert_id;
		if (!$res) return $this->error($this->conn->error);
		// vamos a evaluar el Dorsal. Se supone que hay un trigger que ya lo hace,
		// pero se ha debido perder por el camino en alguna actualizacion
        // so get last dorsal, increase and update.... but only if trigger does not work
		$obj=$this->__selectObject("1 + Max(Dorsal) AS LastDorsal","Inscripciones","(Prueba=$prueba)");
		$str="UPDATE Inscripciones SET Dorsal={$obj->LastDorsal} WHERE (Prueba=$prueba) AND (Perro=$idperro) AND (Dorsal=0)";
		$res=$this->query($str);
		if (!$res) return $this->error($this->conn->error);

		// una vez inscrito y ajustado el dorsal vamos a repasar la lista de jornadas/resultados y actualizar en caso necesario
		$inscripcionid=$this->insertid;
		// los datos de las mangas y resultados
		procesaInscripcion($prueba,$inscripcionid);
		// all right return ok
		$this->myLogger->leave();
        return $obj->LastDorsal;
	}

    /**
     * Update an inscripcion
     * @param {int} perro ID del perro
     * @return {string} empty string if ok; else null
     */
	function update($idperro) {
        $p=$this->pruebaID;
        if ($idperro<=0) return $this->error("Invalid IDPerro ID");
        // cogemos los datos actuales
        $res=$this->__selectObject("*","Inscripciones","(Perro=$idperro) AND (Prueba=$p)");
        if (!is_object($res))
            return $this->error("El perro cond ID:$idperro no figura inscrito en la prueba:$p");
        $celo=http_request("Celo","i",$res->Celo);
        $observaciones=http_request("Observaciones","s",$res->Observaciones);
        $pagado=http_request("Pagado","i",$res->Pagado);
        $jornadas=http_request("Jornadas","i",$res->Jornadas);
        return $this->real_update($jornadas,$celo,$observaciones,$pagado,$res->ID);
    }

	function real_update($jornadas,$celo,$observaciones,$pagado,$inscriptionID) {
		$this->myLogger->enter();
		// actualizamos bbdd
		$str="UPDATE Inscripciones 
			SET Celo=$celo, Observaciones='$observaciones', Jornadas=$jornadas, Pagado=$pagado
			WHERE ( ID=$inscriptionID)";
		
		// actualizamos datos de inscripcion
		$res=$this->query($str);
		if (!$res) return $this->error($this->conn->error);
		
		// recalculamos la inscripcion, orden de salida y tabla de resultados
		procesaInscripcion($this->pruebaID,$inscriptionID);
		
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
		if (!is_array($res)) return $this->error("Inscripciones::delete(): El perro con id:$idperro no esta inscrito en la prueba:$p");
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
		$extra = "AND (PerroGuiaClub.Grado<>'Baja') AND (PerroGuiaClub.Grado<>'Ret.') " ;
		if ($search!=='') {
		    $extra .= " AND ( (PerroGuiaClub.Nombre LIKE '%$search%') ";
		    $extra .= " OR ( NombreClub LIKE '%$search%') OR ( NombreGuia LIKE '%$search%' ) ";
		    $extra .= " OR ( PerroGuiaClub.NombreLargo LIKE '%$search%') OR ( PerroGuiaClub.Licencia LIKE '%$search%') ) ";
        }

		$page=http_request("page","i",0);
		$rows=http_request("rows","i",0);
		$limit="";
		if ($page>0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit=" ".$offset.",".$rows;
		};
		if ($page<0) {
			$this->myLogger->error("noinscritos::select(): Requested negative page: $page");
			return array("total"=>0,"rows"=>array());
		}
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
				Inscripciones.Perro AS Perro , PerroGuiaClub.Nombre AS Nombre, NombreLargo,
				Genero, Raza,Chip, Licencia, LOE_RRC, Categoria , Grado , Celo , Guia , Club ,
				NombreGuia, NombreClub, Pais,Inscripciones.Observaciones AS Observaciones, Jornadas, Pagado
			FROM Inscripciones,PerroGuiaClub
			WHERE ( Inscripciones.Perro = PerroGuiaClub.ID) 
				AND ( Inscripciones.Prueba=$id )
			ORDER BY Dorsal ASC";
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
	 * @param {boolean} useHttp true to retrieve extra data from http request; else false
	 */
	function inscritos($useHttp=true) {
		$this->myLogger->enter();
		$result=array();
		// evaluate offset and row count for query
		$id = $this->pruebaID;
		$search =  ($useHttp)?http_request("where","s",""):"";
		// $extra= a single ')' or name search criterion
		$extra = '';
		if ($search!=='') $extra=" AND ( (PerroGuiaClub.Nombre LIKE '%$search%') 
				OR ( NombreClub LIKE '%$search%') OR ( NombreGuia LIKE '%$search%' ) ) ";
		$page=($useHttp)?http_request("page","i",1):0;
		$rows=($useHttp)?http_request("rows","i",50):0;
		$limit="";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit=" LIMIT ".$offset.",".$rows;
		};
		$order="Dorsal ASC";
		if ($useHttp){
			$order=getOrderString(
				http_request("sort","s",""),
				http_request("order","s",""),
				"NombreClub ASC, Categoria ASC, Grado ASC, Nombre ASC"
			);
		}
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
				NombreLargo, Genero, Raza, Chip, Licencia, LOE_RRC, Categoria , Grado , Celo , Guia , Club ,
				NombreGuia, NombreClub, Pais, Inscripciones.Observaciones AS Observaciones, Jornadas, Pagado
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
        $order=getOrderString(
            http_request("sort","s",""),
            http_request("order","s",""),
            "NombreClub ASC, Categoria ASC, Grado ASC, Nombre ASC"
        );
		// extraemos la lista de inscritos
        $tname=escapeString($teamobj->Nombre);
		$lista=$this->__select(
                /*select*/ "DISTINCT Resultados.Prueba,Resultados.Jornada, Resultados.Dorsal, Resultados.Perro,
                            Resultados.Nombre, PerroGuiaClub.NombreLargo, PerroGuiaClub.Genero, Resultados.Raza, Resultados.Licencia, Resultados.Categoria, Resultados.Grado,
                            Resultados.Celo,Resultados.NombreGuia,Resultados.NombreClub, Resultados.Equipo,
                            PerroGuiaClub.Club AS Club, PerroGuiaClub.Chip AS Chip, PerroGuiaClub.Guia AS Guia,PerroGuiaClub.LogoClub AS LogoClub,
                            Inscripciones.Observaciones AS Observaciones,
                            '$tname' AS NombreEquipo",
				/* from */	"Resultados,PerroGuiaClub,Inscripciones",
				/* where */ "( PerroGuiaClub.ID = Resultados.Perro)	
				            AND ( Resultados.Jornada={$teamobj->Jornada} ) 
				            AND ( Resultados.Equipo=$team )
				            AND ( Inscripciones.Prueba=Resultados.Prueba ) AND (Inscripciones.Perro=Resultados.Perro)",
				/* order */ $order,
				/* limit */ ""
			);
        $this->myLogger->leave();
        return $lista;
	}

	/*
	 * Change dorsal number for provided dog
	 * If dorsal is already assigned, swap dorsal with affected dog
	 */
	function setDorsal($perro,$curdorsal,$newdorsal){
		$this->myLogger->enter();
		if ( ($perro<=0) || ($newdorsal<=0))
			return $this->error("setDorsal(): invalid dogID:$perro or dorsal:$newdorsal requested");
		$this->myLogger->leave();
		$cmds= array(
			// preserve old dorsal if exists
			"UPDATE Inscripciones SET Dorsal=0 WHERE ( Prueba={$this->pruebaID} )  AND ( Dorsal={$newdorsal} )",
			"UPDATE Resultados SET Dorsal=0 WHERE ( Prueba={$this->pruebaID} )  AND ( Dorsal={$newdorsal} )",
			// set new dorsal
			"UPDATE Inscripciones SET Dorsal=$newdorsal WHERE ( Prueba={$this->pruebaID} )  AND ( Dorsal={$curdorsal} )",
			"UPDATE Resultados SET Dorsal=$newdorsal WHERE ( Prueba={$this->pruebaID} )  AND ( Dorsal={$curdorsal} )",
			// swap old dorsal
			"UPDATE Inscripciones SET Dorsal=$curdorsal WHERE ( Prueba={$this->pruebaID} )  AND ( Dorsal=0 )",
			"UPDATE Resultados SET Dorsal=$curdorsal WHERE ( Prueba={$this->pruebaID} )  AND ( Dorsal=0 )"
		);
		foreach ($cmds as $query) { $this->conn->query($query); }
		return "";
	}

	/*
	 * Reorder dorsales by mean of club,categoria,grado,nombre
	 */
	function reorder() {
		$this->myLogger->enter();
		$timeout=ini_get('max_execution_time');
		// ordenamos los perros por club, categoria grado
		$inscritos=$this->__select(
				"Perro,Nombre,NombreClub,Categoria,Grado",
				"Inscripciones,PerroGuiaClub",
				"(Inscripciones.Prueba={$this->pruebaID}) AND (Inscripciones.Perro=PerroGuiaClub.ID)", 
				"NombreClub ASC,Categoria ASC, Grado ASC, Nombre ASC",
				"");
		if (!is_array($inscritos))
			return $this->error("reorder(): Canot retrieve list of inscritos");

        // contador y variables de control de bucle
        $dorsal=1;
        $perro=0;
        $len=count($inscritos['rows']);

		//usaremos prepared statements para acelerar
		$str1="UPDATE Inscripciones SET Dorsal=? WHERE (Prueba={$this->pruebaID}) AND (Perro=?)";
		$str2="UPDATE Resultados SET DORSAL=? WHERE (Prueba={$this->pruebaID}) AND (Perro=?)";
			
		$stmt1=$this->conn->prepare($str1);
		if (!$stmt1) return $this->error($this->conn->error);
		$stmt2=$this->conn->prepare($str2);
		if (!$stmt2) return $this->error($this->conn->error);
			
		$res1=$stmt1->bind_param('ii',$dorsal,$perro);
		if (!$res1) return $this->error($stmt1->error);
		$res2=$stmt2->bind_param('ii',$dorsal,$perro);
		if (!$res2) return $this->error($stmt2->error);

		
		for($n=0;$n<$len;$n++,$dorsal++) {
			// avoid php to be killed on very slow systems
			set_time_limit($timeout);
			// actualizamos las tabla de inscripciones y resultados
			$perro=$inscritos['rows'][$n]['Perro'];
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
	 * @param {boolean} $pagination: use http_request to retrieve page and rows (true) or disable it(false)
	 */
	function inscritosByJornada($jornadaID,$pagination=true) {
		$this->myLogger->enter();
		$pruebaid=$this->pruebaID;
		// Cogemos la lista de jornadas abiertas de esta prueba
		$j=new Jornadas("inscripciones::inscritosByJornada()",$this->pruebaID);
		$jornadas=$j->searchByPrueba(1,1); // allowClosed=1, hideUnassigned=1
		if ( ($jornadas===null) || ($jornadas==="") ) {
			return $this->error("{$this->file}::updateOrdenSalida() cannot get list of open Jornadas for prueba:".$this->pruebaID);
		}
		// por cada jornada abierta, miramos a ver si la ID coincide con la que buscamos
		$mask=0;
		foreach($jornadas["rows"] as $jornada) {
			if ($jornada['ID']==$jornadaID) $mask=1<<($jornada['Numero']-1); // 1..8
		}
		if ($mask==0) {
			return $this->error("{$this->file}::inscritosByJornada() cannot find open Jornada ID: $jornadaID in prueba:".$this->pruebaID);
		}
		$limit="";
		if ($pagination) {
			$page=http_request("page","i",1);
			$rows=http_request("rows","i",50);
			if ($page!=0 && $rows!=0 ) {
				$offset=($page-1)*$rows;
				$limit=$offset.",".$rows;
			};
		}
		// obtenemos la lista de perros inscritos con sus datos
        $result=$this->__select(
			/* SELECT */"Inscripciones.ID AS ID, Inscripciones.Prueba AS Prueba, Inscripciones.Perro AS Perro, Raza,
				Dorsal, PerroGuiaClub.Nombre AS Nombre, PerroGuiaClub.NombreLargo AS NombreLargo,  Genero, Raza, Chip, Licencia, LOE_RRC, Categoria, Grado, Celo, Guia, Club, Pais, LogoClub,
				NombreGuia, NombreClub,	Inscripciones.Observaciones AS Observaciones, Jornadas, Pagado",
			/* FROM */	"Inscripciones,PerroGuiaClub",
			/* WHERE */ "( Inscripciones.Perro = PerroGuiaClub.ID) AND
				( Inscripciones.Prueba=$pruebaid ) AND ( ( Inscripciones.Jornadas&$mask ) != 0 ) ",
			/* ORDER BY */ "NombreClub ASC, Categoria ASC , Grado ASC, Nombre ASC, Celo ASC",
			/* LIMIT */ $limit
		);

		$this->myLogger->leave();
		return $result;
	}

    /**
     * Clear every inscriptions and related info from provided journey
     * posiblemente esto se pudiera hacer automaticamente con las foreign keys
     * pero por si acaso, así no hay fallo
     * @param {int} $jornada Jornada ID
     * @return string empty on success else error message
     */
	function clearInscripciones($jornada) {
	    if ($jornada<=0) throw new Exception("clearInscripciones: Invalid JornadaID");
        // borramos sesiones asociadas
        $this->query("DELETE FROM Sesiones WHERE Jornada=$jornada");
	    // borramos resultados
        $this->query("DELETE FROM Resultados WHERE Jornada=$jornada");
        // borramos ordensalida y ordenequipos de las mangas
        $jobj=$this->__getObject("Jornadas",$jornada);
        $this->query("UPDATE Mangas SET Orden_Salida='BEGIN,END',Orden_Equipos='BEGIN,{$jobj->Default_Team},END' WHERE Jornada=$jornada");
        // borramos equipos ! no borrar equipo por defecto !
        $this->query("DELETE FROM Equipos WHERE (Jornada=$jornada) AND (DefaultTeam=0)");
        // la jornada no se borra. Hay que obtener su numero de orden
        $jobj=$this->__getObject("Jornadas",$jornada);
        // y usarlo como mascara en las inscripciones
        $numero=1<<(intval($jobj->Numero)-1); // mascara de inscripciones
        $this->query("UPDATE Inscripciones SET Jornadas=(Jornadas & ~$numero) WHERE Prueba={$this->pruebaID}");
        return "";
    }

    /**
     * Clone all inscriptions from one journey to another
     * preserve existing inscriptions on destination journey
     * @param {int} $from Jornada ID to clone inscriptions from
     * @param {int} $jornada Jornada ID to be cloned
     * @return string empty on success else error message
     */
    function cloneInscripciones($from,$jornada) {
        $this->myLogger->enter();
        $timeout=ini_get('max_execution_time');
        $fobj=$this->__getObject("Jornadas",$from);
        if (!$fobj) throw new Exception("cloneInscripciones: Invalid JornadaID to clone from");
        $tobj=$this->__getObject("Jornadas",$jornada);
        if (!$tobj) throw new Exception("cloneInscripciones: Invalid JornadaID to clone into");
        $msg="";
        // si las jornadas no tienen las mismas mangas en junior, G2 o G3 no se pueden clonar
        if ($fobj->Junior!=$tobj->Junior) $msg="Junior";
        if ($fobj->Grado2!=$tobj->Grado2) $msg="Grado 2";
        if ($fobj->Grado3!=$tobj->Grado3) $msg="Grado 3";
        // grado 1 puede tener 1, dos, o tres mangas, siendo compatibles entre ellos
        if ( ($fobj->Grado1!=0) && ($fobj->Grado1==0) ) $msg="Grado 1";
        if ( ($fobj->Grado1==0) && ($fobj->Grado1!=0) ) $msg="Grado 1";
        // preagility puede tener una o dos mangas
        if ( ($fobj->PreAgility!=0) && ($fobj->PreAgility==0) ) $msg="PreAgility";
        if ( ($fobj->PreAgility==0) && ($fobj->PreAgility!=0) ) $msg="PreAgility";
        // Mangas especiales son compatibles entre si, con independencia del numero de mangas
        if ( ($fobj->Especial!=0) && ($fobj->Especial==0) ) $msg="Especial";
        if ( ($fobj->Especial==0) && ($fobj->Especial!=0) ) $msg="Especial";

        // en las competiciones por equipos se pueden clonar eq3 en eq4 y viceversa (i.e: open europeo)
        if ( ( $fobj->Equipos3!=0 ) || ( $fobj->Equipos4!=0 ) ) { // hay alguna prueba por equpos
            if ( ($tobj->Equipos3==0) && ($tobj->Equipos4==0) )  $msg="Teams";
        }
        if ( ( $fobj->Equipos3==0 ) && ( $fobj->Equipos4==0 ) ){ // no hay pruebas por equipos
            if ( ($tobj->Equipos3!=0) || ($tobj->Equipos4!=0) )  $msg="Teams";
        }
        // KO, open y Games son compatibles entre si
        if ( ($fobj->Open!=0) || ($fobj->KO!=0) ||($fobj->Games!=0) ) { // hay open,ko o games
            if ( ($tobj->Open==0) && ($tobj->KO==0) && ($tobj->Games==0))  $msg="Open/KO/Games";
        }
        if ( ($fobj->Open==0) && ($fobj->KO==0) && ($fobj->Games==0) ) { // no hay ni open ni ko ni games
            if ( ($tobj->Open!=0) || ($tobj->KO!=0) || ($tobj->Games!=0))  $msg="Open/KO/Games";
        }

        if ($msg!="")throw new Exception( "cloneInscripciones: "._("Round information missmatch").": $msg");
        // buscamos numero de orden de jornada origen y destino para actualizar la tabla de inscripciones
        $fmask=1<<(($fobj->Numero)-1);
        $tmask=1<<(($tobj->Numero)-1);
        $res=$this->query("UPDATE Inscripciones SET Jornadas=(Jornadas|$tmask) WHERE Prueba={$this->pruebaID} AND ((Jornadas&$fmask)!=0)");
        if (!$res) $this->myLogger->error($this->conn->error);

        // las tandas no se clonan, pues ya estan generadas. como mucho caso habría que clonar contenidos
        // pero es algo que no vamos a hacer hoy.

        // las sesiones no se clonan: no tiene sentido pues esta jornada no existe todavia
        // por lo que no puede tener sesiones asignadas

        // actualizamos lista de equipos. No se incluye defaultteam, que se define al crear la jornada
        $str  = "INSERT INTO Equipos ( Prueba,Jornada,Categorias,Nombre,Observaciones,Miembros,DefaultTeam ) "
                ."SELECT Prueba,$jornada AS Jornada,Categorias,Nombre,Observaciones,Miembros,DefaultTeam "
                ."FROM Equipos WHERE Jornada=$from AND DefaultTeam=0";
        $res=$this->query($str);
        if (!$res) $this->myLogger->error($this->conn->error);
        // actualizamos miembros del equipo por defecto ( que no se inserta, sino que tiene que venir por defecto
        $str = "UPDATE Equipos,(SELECT Miembros FROM Equipos WHERE Jornada=$from AND DefaultTeam=1) AS p2 "
               ."SET Equipos.Miembros=p2.Miembros WHERE Jornada=$jornada AND DefaultTeam=1";
        $res=$this->query($str);
        if (!$res) $this->myLogger->error($this->conn->error);

        // ahora cogemos e indexamos los datos de los equipos para poder actualizar convenientemente mangas y resultados
        $eqobj=$this->__select( "*","Equipos","Jornada IN ({$fobj->ID},{$tobj->ID})","Jornada ASC");
        $teamlistByName=array();
        $teamlistByID=array();
        foreach($eqobj['rows'] as $equipo) {
            $eqname=$equipo['Nombre'];
            if (!array_key_exists($eqname,$teamlistByName)) $teamlistByName[$eqname]=array(0,0); // from, to
            if ($equipo['Jornada']==$from) $teamlistByName[$eqname][0]=$equipo['ID'];
            if ($equipo['Jornada']==$jornada) $teamlistByName[$eqname][1]=$equipo['ID'];
            // this is very dirty, but works: if from not yet defined, to be lost value is stored at index '0'
            $teamlistByID[$teamlistByName[$eqname][0]]=$teamlistByName[$eqname][1];
        }

        // cogemos manga por manga y vamos actualizando datos y resultados
        $mangasfrom=$this->__select("*","Mangas","Jornada=$from");
        $mangasto=$this->__select("*","Mangas","Jornada=$jornada");
        foreach ($mangasfrom['rows'] as $f) {
            set_time_limit($timeout); // to avoid timeout in slow computers
            $found=false;
            foreach ($mangasto['rows'] as &$t) { // use reference instead of copy
                $flag=false;
                switch ($f['Tipo']) {
                    case 8: // agility 3-best
                    case 9: // agility 4 conjunta
                        $flag=( ($t['Tipo']==8) || ($t['Tipo']==9) );
                        break;
                    case 13: // jumping team 3-best
                    case 14: // jumping team 4 conjunta
                        $flag=( ($t['Tipo']==13) || ($t['Tipo']==14) );
                        break;
                    default: $flag=($t['Tipo']==$f['Tipo']);
                }
                if (!$flag)  continue; // no round type match
                $found=true;
                // update Orden_Equipos by mean of translate old to new
                $oe=explode(",",$f['Orden_Equipos']);
                $newoe=array();
                foreach($oe as &$item) {
                    if ($item==="BEGIN") $newoe[]='BEGIN';
                    else if ($item==="END") $newoe[]='END';
                    else $newoe[]=$teamlistByID[$item];
                }
                $t['Orden_Equipos']=implode(",",$newoe);
                // clone manga info with new team information
                $str = "UPDATE Mangas SET Mangas.Recorrido={$f['Recorrido']} ,"
                    ."Mangas.Dist_L={$f['Dist_L']}, Mangas.Obst_L={$f['Obst_L']}, Mangas.Dist_M={$f['Dist_M']}, Mangas.Obst_M={$f['Obst_M']}, "
                    ."Mangas.Dist_T={$f['Dist_T']}, Mangas.Obst_T={$f['Obst_T']}, Mangas.Dist_S={$f['Dist_S']}, Mangas.Obst_S={$f['Obst_T']}, "
                    ."Mangas.TRS_L_Tipo={$f['TRS_L_Tipo']}, Mangas.TRS_L_Factor={$f['TRS_L_Factor']}, Mangas.TRS_L_Unit='{$f['TRS_L_Unit']}', "
                    ."Mangas.TRM_L_Tipo={$f['TRM_L_Tipo']}, Mangas.TRM_L_Factor={$f['TRM_L_Factor']}, Mangas.TRM_L_Unit='{$f['TRM_L_Unit']}', "
                    ."Mangas.TRS_M_Tipo={$f['TRS_M_Tipo']}, Mangas.TRS_M_Factor={$f['TRS_M_Factor']}, Mangas.TRS_M_Unit='{$f['TRS_M_Unit']}', "
                    ."Mangas.TRM_M_Tipo={$f['TRM_M_Tipo']}, Mangas.TRM_M_Factor={$f['TRM_M_Factor']}, Mangas.TRM_M_Unit='{$f['TRM_M_Unit']}', "
                    ."Mangas.TRS_S_Tipo={$f['TRS_S_Tipo']}, Mangas.TRS_S_Factor={$f['TRS_S_Factor']}, Mangas.TRS_S_Unit='{$f['TRS_S_Unit']}', "
                    ."Mangas.TRM_S_Tipo={$f['TRM_S_Tipo']}, Mangas.TRM_S_Factor={$f['TRM_S_Factor']}, Mangas.TRM_S_Unit='{$f['TRM_S_Unit']}', "
                    ."Mangas.TRS_T_Tipo={$f['TRS_T_Tipo']}, Mangas.TRS_T_Factor={$f['TRS_T_Factor']}, Mangas.TRS_T_Unit='{$f['TRS_T_Unit']}', "
                    ."Mangas.TRM_T_Tipo={$f['TRM_T_Tipo']}, Mangas.TRM_T_Factor={$f['TRM_T_Factor']}, Mangas.TRM_T_Unit='{$f['TRM_T_Unit']}', "
                    ."Mangas.Juez1={$f['Juez1']}, Mangas.Juez2={$f['Juez2']}, Mangas.Observaciones='{$f['Observaciones']}', "
                    ."Mangas.Orden_Salida='{$f['Orden_Salida']}', Mangas.Orden_Equipos='{$t['Orden_Equipos']}' "
                    ."WHERE Jornada=$jornada AND Mangas.ID={$t['ID']} "; // importante lo de los tipos de mangas :-)
                $res=$this->query($str);
                if (!$res) $this->myLogger->error($this->conn->error);

                // ahora toca clonar los resultados. para ello, los vamos a volcar en un array,
                // y vamos a ajustar jornada, manga y equipo
                // como la tabla de equipos hay que ponerla a mano, no se puede hacer un update,
                // sino que hay que recuperarla, editarla y volverla a guardar
                $resultados=$this->__select("*","Resultados","Manga={$f['ID']}");
                $sqlvalues="";
                foreach($resultados['rows'] as &$resultado) {
                    unset($resultado['ID']);// remove id field. not really needed, but...
                    // scape strings
                    foreach($resultado as &$field) if (is_string($field)) $field=$this->conn->real_escape_string($field);
                    $resultado['Jornada']=$jornada;
                    $resultado['Manga']=$t['ID'];
                    $resultado['Equipo']=$teamlistByID[$resultado['Equipo']];
                    $sqlvalues.= " ('".implode("', '", $resultado)."'),";
                }
                $str  = "INSERT IGNORE INTO Resultados (`".implode("`, `", array_keys($resultados['rows'][0]))."`) VALUES $sqlvalues";
                $str = substr($str,0,-1); // remove last comma
                $res=$this->query($str);
                if (!$res) $this->myLogger->error($this->conn->error);
            }
            // arriving here means no brohter round found. this is an error.
            if (!$found) $this->myLogger->error("No equivalent round found for Journey $from round {$t['ID']} of type {$f['Tipo']} in journey $jornada");
        }

        $this->myLogger->leave();
        // ok. proceso completado
        return "";
    }


    /**
     * Inscribe every registered dogs for a contest into provided journey
     * preserve existing inscriptions
     * @param {int} $jornada Jornada ID
     * @return string empty on success else error message
     */
    function populateInscripciones($jornada) {
        $tobj=$this->__getArray("Jornadas",$jornada);
        if (!$tobj) throw new Exception("updateInscripciones: Invalid JornadaID to clone into");
        $timeout=ini_get('max_execution_time');
        $tmask=1<<(($tobj['Numero'])-1);
        // actualizamos tabla de inscripciones
        $res=$this->query("UPDATE Inscripciones SET Jornadas=(Jornadas|$tmask) WHERE Prueba={$this->pruebaID}");
        if (!$res) $this->myLogger->error($this->conn->error);
        // obtenemos la lista de inscripciones y de perros
        // el orden debe coincidir; si no tenemos un problema muy serio....
        $inscripciones=$this->__select("*","Inscripciones","Prueba={$this->pruebaID}","Perro ASC");
        $perros=$this->__select(
            "*",
            "PerroGuiaClub",
            "ID IN (SELECT Perro AS ID FROM Inscripciones WHERE Prueba={$this->pruebaID}) ",
            "ID ASC");
        for($n=0;$n<$inscripciones['total']; $n++) {
            set_time_limit($timeout);
            $this->myLogger->trace("Procesando inscripcion {$inscripciones['rows'][$n]['Perro']} del perro: {$perros['rows'][$n]['ID']} {$perros['rows'][$n]['Nombre']}");
            inscribePerroEnJornada($inscripciones['rows'][$n],$tobj,$perros['rows'][$n]);
        }

        return null;
    }
} /* end of class "Inscripciones" */

?>