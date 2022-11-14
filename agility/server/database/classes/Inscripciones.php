<?php
/*
Inscripciones.php

Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
    protected $PruebaObj;
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
        $this->pruebaObj=$this->__selectObject("*","pruebas","pruebas.ID={$prueba}");
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

    /**
     * Create new inscription or add to an existing inscription provided dog into selected journey
     * @param $idperro
     * @param $idjornada or journeyNumber (1..8)
     * @param $jmode: 0:JornadaID 1:JourneyNumber
     * @return string
     */
    function insertIntoJourney($idperro,$idjornada,$jmode=0) {
        if (($idperro<=0) || ($idjornada<=0))
            return $this->error("deleteFromJourney(): invalid dog:{$idperro} or jornada:{$idjornada} ID");

        if ($jmode!==0) { // need to eval jornada ID from prueba and jornada index
            $num=$idjornada;
            $jobj=$this->__selectObject("*","jornadas","Prueba={$this->pruebaID} AND Numero={$num}");
            if (!$jobj) return $this->error("insertIntoJourney() Cannot locate Journey for Prueba ($this->pruebaID} and index:{$idjornada}");
        } else {
            // retrieve journey info and mask
            $jobj=$this->__getObject("jornadas",$idjornada);
            if (!$jobj) return $this->error("deleteFromJourney(): non-existent jornada:{$idjornada}");
        }
        $mask=1<<( intval($jobj->Numero) - 1 );
        // check if dog is already inscribed
        $iobj=$this->__selectObject("*","inscripciones","( Prueba={$this->pruebaID} ) AND ( Perro={$idperro} )");
        if (!$iobj) { // not yet inscribed: insert()
            $this->realInsert($idperro,$this->pruebaID,$mask,0,0,'');
        } else {  // already inscribed: fix journey mask and update()
            $jornadas= $iobj->Jornadas | $mask;
            $this->real_update($jornadas,$iobj->Celo,$iobj->Observaciones,$iobj->Pagado,$iobj->ID);
        }
	    return "";
    }

    /**
     * Remove dog from existing inscription providing dog and selected journey
     * @param $idperro
     * @param $idjornada or journeyNumber (1..8)
     * @param $jmode: 0:JornadaID 1:JourneyNumber
     * @return string
     */
    function deleteFromJourney($idperro,$idjornada,$jmode=0) {
        if (($idperro<=0) || ($idjornada<=0))
            return $this->error("deleteFromJourney(): invalid dog:{$idperro} or jornada:{$idjornada} ID");

        if ($jmode!==0) { // need to eval jornada ID from prueba and jornada index
            $num=$idjornada;
            $jobj=$this->__selectObject("*","jornadas","Prueba={$this->pruebaID} AND Numero={$num}");
            if (!$jobj) return $this->error("insertIntoJourney() Cannot locate Journey for Prueba ($this->pruebaID} and index:{$idjornada}");
        } else {
            // retrieve journey info and mask
            $jobj=$this->__getObject("jornadas",$idjornada);
            if (!$jobj) return $this->error("deleteFromJourney(): non-existent jornada:{$idjornada}");
        }
        $mask=1<<( intval($jobj->Numero) - 1 );
        // check if dog is already inscribed
        $iobj=$this->__selectObject("*","inscripciones","( Prueba={$this->pruebaID} ) AND ( Perro={$idperro} )");
        if (! $iobj) {
            // not inscribed: notice error and return
            $this->myLogger->warn("deleteFromJourney(): Dog {$idperro} is not inscribed in Prueba {$this->pruebaID}");
            return "";
        }
        $jornadas= $iobj->Jornadas & ~$mask;
        return $this->real_update($jornadas,$iobj->Celo,$iobj->Observaciones,$iobj->Pagado,$iobj->ID);
    }

	function realInsert($idperro,$prueba,$jornadas,$pagado,$celo,$observaciones) {
		$this->myLogger->enter();
		if ($idperro<=0) return $this->error("Invalid IDPerro ID");
		$res= $this->__selectObject(
			/* SELECT */ "*",
			/* FROM */ "inscripciones",
			/* WHERE */ "( Prueba=".$this->pruebaID.") AND ( Perro=$idperro )"
		);
		if($res!==null){ // already inscribed, try to update
            if (!is_object($res)) return $res;
            $this->myLogger->notice("El perro con ID:$idperro ya esta inscrito en la prueba:{$this->pruebaID}");
            $this->real_update($jornadas,$celo,$observaciones,$pagado,$res->ID);
            return $res->Dorsal;
        }

		// ok, ya tenemos lo necesario. Vamos a inscribirle... pero solo en las jornadas abiertas
		$str= "INSERT INTO inscripciones (Prueba,Perro,Celo,Observaciones,Jornadas,Pagado)
			VALUES ($prueba,$idperro,$celo,'$observaciones',$jornadas,$pagado)";
		$res=$this->query($str);
		$this->insertid=$this->conn->insert_id;
		if (!$res) return $this->error($this->conn->error);
		// vamos a evaluar el Dorsal. Se supone que hay un trigger que ya lo hace,
		// pero se ha debido perder por el camino en alguna actualizacion
        // so get last dorsal, increase and update.... but only if trigger does not work
		$obj=$this->__selectObject("1 + Max(Dorsal) AS LastDorsal","inscripciones","(Prueba=$prueba)");
		$str="UPDATE inscripciones SET Dorsal={$obj->LastDorsal} WHERE (Prueba=$prueba) AND (Perro=$idperro) AND (Dorsal=0)";
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
        $res=$this->__selectObject("*","inscripciones","(Perro=$idperro) AND (Prueba=$p)");
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
		$str="UPDATE inscripciones 
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
		$res=$this->__selectAsArray("ID", "inscripciones", "(Perro=$idperro) AND (Prueba=$p)");
		if (!is_array($res)) return $this->error("inscripciones::delete(): El perro con id:$idperro no esta inscrito en la prueba:$p");
		$i=$res['ID'];
		// fase 1: actualizamos la DB para indicar que el perro no esta inscrito en ninguna jornada
		$sql="UPDATE inscripciones SET Jornadas = 0  WHERE (ID={$i})";
		$res=$this->query($sql);
		if (!$res) return $this->error($this->conn->error);
		// fase 2: eliminamos informacion del perro en los ordenes de salida y tabla de resultados
		procesaInscripcion($p, $i);
		// fase 3: finalmente eliminamos el perro de la tabla de inscripciones
		$res=$this->__delete("inscripciones","(ID={$i})");
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
				/* FROM */   "inscripciones",
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
		$obj=$this->__getObject("inscripciones",$id);
		if (!is_object($obj))	return $this->error("No Inscripcion found with ID=$id");
		$data= json_decode(json_encode($obj), true); // convert object to array
		$this->myLogger->leave();
		return $data;
	}
	
	/**
	 * retrieve all dogs that has no inscription on this contest
     * if journey is provided use journey id instead of contest id for seach
     * @param  {integer} $jornada Jornada ID or zero if not specified
	 */
	function noinscritos($jornada=0) {
		$this->myLogger->enter();
		
		$id = $this->pruebaID;
		$fed =  http_request("Federation","i",0);
		$search =  http_request("where","s","");
		$extra = "AND (perroguiaclub.Baja=0) AND (perroguiaclub.Grado<>'Baja') AND (perroguiaclub.Grado<>'Ret.') " ;
		$extra .= "AND ( Guia>1 ) AND (Club>1) "; // exclude dogs wihtout handler or handlers w/o club

        // trick to allow search also by category or grade
        if ($search !=="" ) {
            $e="
		        AND ( (perroguiaclub.Nombre LIKE '%$search%') OR ( perroguiaclub.NombreLargo LIKE '%$search%') 
		        OR ( NombreGuia LIKE '%$search%') OR ( Licencia LIKE '%$search%') 
		        OR ( NombreClub LIKE '%$search%') )";
            $g=parseGrade($search); $grad=($g==="-")? "":" AND ( Grado='{$g}' ) ";
            $c=parseCategory($search,$fed); $cat=($c==="-")? "":" AND ( Categoria='{$c}' ) ";
            if ($g!=="-" ) $extra.=$grad;
            else if ($c!=="-" ) $extra.=$cat;
            else $extra.=$e;
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
		$inner="SELECT Perro FROM inscripciones WHERE (Prueba=$id)";
		if (intval($jornada)!==0) $inner="SELECT DISTINCT Perro FROM resultados WHERE (Jornada=$jornada)";
		$result= $this->__select(
			/* SELECT */	"*",
			/* FROM */		"perroguiaclub",
			/* WHERE */		"( Federation = $fed ) AND ID NOT IN ( {$inner} ) $extra ",
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
	function enumerate($order="Dorsal") {
		$this->myLogger->enter();
		// evaluate offset and row count for query
		$id = $this->pruebaID;
		// FASE 1: obtener lista de perros inscritos con sus datos
		$str="SELECT inscripciones.ID AS ID, inscripciones.Prueba AS Prueba, Dorsal , 
				inscripciones.Perro AS Perro , perroguiaclub.Nombre AS Nombre, NombreLargo,
				Genero, Raza,Chip, Licencia, LOE_RRC, Categoria , Grado , Celo , Guia , Club ,
				NombreGuia, CatGuia, NombreClub, Pais,inscripciones.Observaciones AS Observaciones, Jornadas, Pagado
			FROM inscripciones,perroguiaclub
			WHERE ( inscripciones.Perro = perroguiaclub.ID) 
				AND ( inscripciones.Prueba=$id )
			ORDER BY {$order} ASC";
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
            // stupid datagrid that must have non-empty fields to allow displaying data
            $row['NC']=$row['Celo']; // NC -> not competing, just run NC:0x02 Celo:0x01 mask
			array_push($data,$row);
		}
		$rs->free();
		$result=array('total'=>count($data), 'rows'=>$data);
		$this->myLogger->leave();
		return $result;
	
	}

	function enumerateDups() {
	    $this->myLogger->enter();
	    // obtenemos la lista total de inscritos
        $from=$this->enumerate("NombreClub ASC, NombreGuia ASC, Categoria ASC, Grado ")['rows'];
        // elaboramos la tabla de guias y veces que aparecen
        $guias=array();
        foreach ($from as $item) {
            if (!array_key_exists($item['Guia'],$guias)) $guias[$item['Guia']]=0;
            $guias[$item['Guia']]++;
        }
        // re-escribimos la lista de inscritos omitiendo los que no tienen guia duplicado
        $dups=array();
        foreach ($from as $item) {
            if ($guias[$item['Guia']]>1) array_push($dups,$item);
        }
        // componemos resultado
        $result=array('total'=>count($dups),'rows'=>$dups);
        $this->myLogger->leave();
        return $result;
    }

	/**
	 * Tell how many dogs have inscription in this contest
	 */
	function howMany() {
		return $this->__selectObject("count(*) AS Inscritos","inscripciones","(Prueba={$this->pruebaID})");
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

		// trick to allow search also by category or grade
        $extra = '';
        if ($search !=="" ) {
            $extra="
		        AND ( (perroguiaclub.Nombre LIKE '%$search%') OR ( perroguiaclub.NombreLargo LIKE '%$search%') 
		        OR ( NombreGuia LIKE '%$search%') OR ( Licencia LIKE '%$search%') 
		        OR ( NombreClub LIKE '%$search%') )";
            $g=parseGrade($search); $grad=($g==="-")? "":" AND ( Grado='{$g}' ) ";
            $c=parseCategory($search,$this->pruebaObj->RSCE); $cat=($c==="-")? "":" AND ( Categoria='{$c}' ) ";
            if ($g!=="-" ) $extra=$grad;
            if ($c!=="-" ) $extra=$cat;
        }

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
		FROM inscripciones,perroguiaclub
		WHERE ( inscripciones.Perro = perroguiaclub.ID) 
			AND ( inscripciones.Prueba=$id ) $extra";
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
		$str="SELECT inscripciones.ID AS ID, inscripciones.Prueba AS Prueba, Dorsal, inscripciones.Perro AS Perro , perroguiaclub.Nombre AS Nombre,
				NombreLargo, Genero, Raza, Chip, Licencia, LOE_RRC, Categoria , Grado , Celo , Guia , Club ,
				NombreGuia, CatGuia, NombreClub, Pais, inscripciones.Observaciones AS Observaciones, Jornadas, Pagado
			FROM inscripciones,perroguiaclub
			WHERE ( inscripciones.Perro = perroguiaclub.ID) AND ( inscripciones.Prueba=$id ) $extra 
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
            // stupid datagrid that must have non-empty fields to allow displaying data
            $row['NC']=$row['Celo']; // N.C. -> not competing, just run NC:0x02 Celo:0x01 mask
			array_push($data,$row);
		}
		$rs->free();
		// ! do not hardcode 'total' as we are using pagination in request !!
		// $result['total']=count($data);
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
		$teamobj=$this->__getObject("equipos",$team);
		if (!is_object($teamobj))
			return $this->error("No puedo obtener datos del equipo con ID: $team");
		// vemos el numero de la jornada asociada
		$jornadaobj=$this->__getObject("jornadas",$teamobj->Jornada);
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
                /*select*/ "DISTINCT resultados.Prueba,resultados.Jornada, resultados.Dorsal, resultados.Perro,
                            resultados.Nombre, perroguiaclub.NombreLargo, perroguiaclub.Genero, resultados.Raza, 
                            resultados.Licencia, resultados.Categoria, resultados.Grado,
                            resultados.Celo,resultados.NombreGuia,resultados.NombreClub, resultados.Equipo,
                            perroguiaclub.Club AS Club, perroguiaclub.Chip AS Chip, 
                            perroguiaclub.Guia AS Guia,perroguiaclub.LogoClub AS LogoClub,
                            inscripciones.Observaciones AS Observaciones,
                            '$tname' AS NombreEquipo",
				/* from */	"resultados,perroguiaclub,inscripciones",
				/* where */ "( perroguiaclub.ID = resultados.Perro)	
				            AND ( resultados.jornada={$teamobj->Jornada} ) 
				            AND ( resultados.Equipo=$team )
				            AND ( inscripciones.Prueba=resultados.Prueba ) AND (inscripciones.Perro=resultados.Perro)",
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
			"UPDATE inscripciones SET Dorsal=0 WHERE ( Prueba={$this->pruebaID} )  AND ( Dorsal={$newdorsal} )",
			"UPDATE resultados SET Dorsal=0 WHERE ( Prueba={$this->pruebaID} )  AND ( Dorsal={$newdorsal} )",
			// set new dorsal
			"UPDATE inscripciones SET Dorsal=$newdorsal WHERE ( Prueba={$this->pruebaID} )  AND ( Dorsal={$curdorsal} )",
			"UPDATE resultados SET Dorsal=$newdorsal WHERE ( Prueba={$this->pruebaID} )  AND ( Dorsal={$curdorsal} )",
			// swap old dorsal
			"UPDATE inscripciones SET Dorsal=$curdorsal WHERE ( Prueba={$this->pruebaID} )  AND ( Dorsal=0 )",
			"UPDATE resultados SET Dorsal=$curdorsal WHERE ( Prueba={$this->pruebaID} )  AND ( Dorsal=0 )"
		);
		foreach ($cmds as $query) { $this->conn->query($query); }
		return "";
	}

	/*
	 * Reorder dorsales by mean of club,categoria,grado,nombre
	 */
	function reorder() {
		$this->myLogger->enter();
		$order=getOrderString(
            http_request("sort","s",""),
            http_request("order","s",""),
            "NombreClub ASC, Categoria ASC, Grado ASC, Nombre ASC"
        );
		$timeout=ini_get('max_execution_time');
		// ordenamos los perros por club, categoria grado
		$inscritos=$this->__select(
				"Perro,Nombre,NombreClub,Categoria,Grado",
				"inscripciones,perroguiaclub",
				"(inscripciones.Prueba={$this->pruebaID}) AND (inscripciones.Perro=perroguiaclub.ID)",
                $order,
				"");
		if (!is_array($inscritos))
			return $this->error("reorder(): Canot retrieve list of inscritos");

        // contador y variables de control de bucle
        $dorsal=1;
        $perro=0;
        $len=count($inscritos['rows']);

		//usaremos prepared statements para acelerar
		$str1="UPDATE inscripciones SET Dorsal=? WHERE (Prueba={$this->pruebaID}) AND (Perro=?)";
		$str2="UPDATE resultados SET Dorsal=? WHERE (Prueba={$this->pruebaID}) AND (Perro=?)";
			
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
     * @param {boolean} $byclub: use club as main sorting criteria
	 */
	function inscritosByJornada($jornadaID,$pagination=true,$byclub=true) {
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
		$byclubstr=($byclub)? "NombreClub ASC,":"";
		// obtenemos la lista de perros inscritos con sus datos
        $result=$this->__select(
			/* SELECT */"inscripciones.ID AS ID, inscripciones.Prueba AS Prueba, inscripciones.Perro AS Perro, Raza,
				Dorsal, perroguiaclub.Nombre AS Nombre, perroguiaclub.NombreLargo AS NombreLargo,  Genero, Raza, Chip, Licencia, LOE_RRC, Categoria, Grado, Celo, Guia, Club, Pais, LogoClub,
				NombreGuia, CatGuia, NombreClub, inscripciones.Observaciones AS Observaciones, Jornadas, Pagado",
			/* FROM */	"inscripciones,perroguiaclub",
			/* WHERE */ "( inscripciones.Perro = perroguiaclub.ID) AND
				( inscripciones.Prueba=$pruebaid ) AND ( ( inscripciones.Jornadas&$mask ) != 0 ) ",
			/* ORDER BY */ "{$byclubstr} Categoria ASC , Grado ASC, Nombre ASC, Celo ASC",
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
	    $this->myLogger->enter();
	    if ($jornada<=0) throw new Exception("clearInscripciones: Invalid JornadaID");
        // borramos sesiones asociadas
        $this->__delete("sesiones","Jornada={$jornada}");
	    // borramos resultados
        $this->__delete("resultados","Jornada={$jornada}");
        // borramos ordensalida y ordenequipos de las mangas
        $jobj=$this->__getObject("jornadas",$jornada);
        $this->query("UPDATE mangas SET Orden_Salida='BEGIN,END',Orden_Equipos='BEGIN,{$jobj->Default_Team},END' WHERE Jornada=$jornada");
        // Borramos equipos. No borrar equipo por defecto, solo limpiar la lista de miembros
        $this->__delete("equipos","(Jornada={$jornada}) AND (DefaultTeam=0)");
        $this->query("UPDATE equipos SET Miembros='BEGIN,END' WHERE (Jornada=$jornada) AND (DefaultTeam=1)");
        // la jornada no se borra. Hay que obtener su numero de orden
        $jobj=$this->__getObject("jornadas",$jornada);
        // y usarlo como mascara en las inscripciones
        $numero=1<<(intval($jobj->Numero)-1); // mascara de inscripciones
        $this->query("UPDATE inscripciones SET Jornadas=(Jornadas & ~$numero) WHERE Prueba={$this->pruebaID}");
        $this->myLogger->leave();
        return "";
    }

    /**
     * Clone all inscriptions from one journey to another
     * preserve existing inscriptions on destination journey
     * @param {int} $from Jornada ID to clone inscriptions from
     * @param {int} $jornada Jornada ID to be cloned
     * @return string empty on success else error message
     * @throws Exception when invalid data provided
     */
    function cloneInscripciones($from,$jornada) {
        $this->myLogger->enter();
        // esto es un clonado, por lo que hay que borrar las inscripciones anteriores
        $this->clearInscripciones($jornada);
        $fobj=$this->__getArray("jornadas",$from);
        $tobj=$this->__getArray("jornadas",$jornada);
        if (!$fobj) throw new Exception("updateInscripciones: Invalid JornadaID:{$from} to clone from");
        if (!$tobj) throw new Exception("updateInscripciones: Invalid JornadaID:{$jornada} to clone into");
        $timeout=ini_get('max_execution_time');
        $fmask=1<<(($fobj['Numero'])-1);
        $tmask=1<<(($tobj['Numero'])-1);

        // actualizamos tabla de inscripciones
        // esto es sencillo: basta con actualizar la mascar de inscripciones
        $sql="UPDATE inscripciones SET Jornadas=(Jornadas|{$tmask}) ".
                    "WHERE (Prueba={$this->pruebaID}) AND ((Jornadas & {$fmask}) != 0)";
        $res=$this->query($sql);
        if (!$res) $this->myLogger->error($this->conn->error);

        // obtenemos la lista de inscripciones y de perros a clonar
        // el orden debe coincidir; si no tenemos un problema muy serio....
        $inscripciones=$this->__select(
            "*",
            "inscripciones",
            "Prueba={$this->pruebaID}",
            "Perro ASC");
        $perros=$this->__select(
            "*",
            "perroguiaclub",
            "ID IN (SELECT Perro AS ID FROM inscripciones WHERE Prueba={$this->pruebaID}) ",
            "ID ASC");
        // procesamos la inscripcion de los perros seleccionados
        for($n=0;$n<$inscripciones['total']; $n++) {
            $inscripcion=$inscripciones['rows'][$n];
            if ( (intval($inscripcion['Jornadas']) & $tmask ) == 0 ) {  // not inscribed in journey, so no need to process
                $this->myLogger->trace("Dog {$perros['rows'][$n]['ID']} {$perros['rows'][$n]['Nombre']} Not inscribed. Skip clone");
                continue;
            }
            set_time_limit($timeout);
            $this->myLogger->trace("Procesando inscripcion {$inscripcion['Perro']} del perro: {$perros['rows'][$n]['ID']} {$perros['rows'][$n]['Nombre']}");
            inscribePerroEnJornada($inscripcion,$tobj,$perros['rows'][$n]);
        }

        // ahora clonamos los equipos. Recuerda que se ha hecho un clearInscripciones primero, por lo que
        // se parte de una tabla "en blanco"

        // obtenemos el defaultTeam de la jornada nueva
        // obtener el id del equipo por defecto de la nueva jornada
        $defteam=$this->__selectAsArray("*","equipos","Jornada={$jornada} AND DefaultTeam=1");
        // si la jornada destino no tiene pruebas por equipos,
        // entonces NO clonamos los equipos de la jornada original
        if (Jornadas::isJornadaEquipos($jornada)) {
            // obtenemos la lista de equipos de la jornada antigua
            $equipos=$this->__select("*","equipos","Jornada={$from}");
            $ordenEquipos="BEGIN,";
            // copiamos cada equipo de la antigua en la nueva, haciendo la traslación de ID's correspondiente
            foreach($equipos['rows'] as $equipo) {
                $members=getInnerString($equipo['Miembros'],'BEGIN,',',END');
                // si se trata del equipo por defecto no se inserta: ya viene pre-definido
                if ($equipo['DefaultTeam']!=0) {
                    $teamID=$defteam['ID'];
                } else {
                    $nequipo=$this->conn->real_escape_string($equipo['Nombre']);
                    $obsequipo=$this->conn->real_escape_string($equipo['Observaciones']);
                    // insertamos nuevo equipo en la lista de equipos
                    $sql="INSERT INTO equipos (Prueba,Jornada,Nombre,Observaciones,Miembros,Categorias,DefaultTeam)
				      VALUES ({$fobj['Prueba']},{$jornada},'{$nequipo}','{$obsequipo}','BEGIN,END','{$equipo['Categorias']}',0 )";
                    $res=$this->query($sql);
                    if (!$res) $this->myLogger->error($this->conn->error);
                    $teamID=$this->conn->insert_id;
                }
                // vamos componiendo el nuevo orden de equipos con los datos que acabamos de obtener
                $ordenEquipos.="${teamID},";
                // actualiza los miembros del equipo, clonandolos del original
                $sql="UPDATE equipos SET Miembros='{$equipo['Miembros']}' WHERE ID={$teamID}";
                $res=$this->query($sql);
                if (!$res) $this->myLogger->error($this->conn->error);
                // ahora actualizamos el campo equipo de los resultados de la jornada
                // en que el ID del perro esta en la lista de miembros
                if ($members!="") { // skip empty team lists
                    $sql="UPDATE resultados SET Equipo={$teamID} WHERE Jornada={$jornada} AND Perro IN ($members)";
                    $res=$this->query($sql);
                }
                if (!$res) $this->myLogger->error($this->conn->error);
            }

            // finalmente ajustamos el orden del equipos que hemos evaluado
            // Como clonar el orden de equipos de cada manga es complicado, ( ya que las dos jornadas no tienen por qué
            // tener las mismas mangas, vamos a poner el mismo orden para todas las mangas de la nueva jornada
            $ordenEquipos.="END";
            $sql="UPDATE mangas SET Orden_Equipos='{$ordenEquipos}' WHERE Jornada={$jornada}";
            $res=$this->query($sql);
            if (!$res) $this->myLogger->error($this->conn->error);
        } else {
            // clone to a journey without team rounds

            // results are by defaul assigned to default team; no need to update

            // also no team members are needed for default team, just default "BEGIN,END"

            // So just update mangas Orden_Equipos field.
            // In this case there are no teams but default one, so it's easy :-)
            $ordenEquipos="BEGIN,{$defteam['ID']},END";
            $sql="UPDATE mangas SET Orden_Equipos='{$ordenEquipos}' WHERE Jornada={$jornada}";
            $res=$this->query($sql);
            if (!$res) $this->myLogger->error($this->conn->error);
        }

        // las tandas no se clonan, (las tandas por defecto ya estan generadas )
        // como mucho caso habría que clonar el orden y anyadir las tandas definidas por el usuario
        // pero es algo que no vamos a hacer hoy.... ademas solo tiene sentido si ambas jornadas son compatibles

        // del mismo modo, nada de clonar resultados, ni datos de TRS: solo seria posible si ambas jornadas fueran
        // del mismo tipo y tuvieran las mismas mangas, lo que no se puede garantizar

        // ok. proceso completado
        $this->myLogger->leave();
        return "";
    }


    /**
     * Inscribe every registered dogs for a contest into provided journey
     * preserve existing inscriptions
     * @param {int} $jornada Jornada ID
     * @return string empty on success else error message
     */
    function populateInscripciones($jornada) {
        $this->myLogger->enter();
        $tobj=$this->__getArray("jornadas",$jornada);
        if (!$tobj) throw new Exception("updateInscripciones: Invalid JornadaID to clone into");
        $timeout=ini_get('max_execution_time');
        $tmask=1<<(($tobj['Numero'])-1);
        // actualizamos tabla de inscripciones
        $res=$this->query("UPDATE inscripciones SET Jornadas=(Jornadas|$tmask) WHERE Prueba={$this->pruebaID}");
        if (!$res) $this->myLogger->error($this->conn->error);
        // obtenemos la lista de inscripciones y de perros
        // el orden debe coincidir; si no tenemos un problema muy serio....
        $inscripciones=$this->__select("*","inscripciones","Prueba={$this->pruebaID}","Perro ASC");
        $perros=$this->__select(
            "*",
            "perroguiaclub",
            "ID IN (SELECT Perro AS ID FROM inscripciones WHERE Prueba={$this->pruebaID}) ",
            "ID ASC");
        for($n=0;$n<$inscripciones['total']; $n++) {
            set_time_limit($timeout);
            $this->myLogger->trace("Procesando inscripcion {$inscripciones['rows'][$n]['Perro']} del perro: {$perros['rows'][$n]['ID']} {$perros['rows'][$n]['Nombre']}");
            inscribePerroEnJornada($inscripciones['rows'][$n],$tobj,$perros['rows'][$n]);
        }
        $this->myLogger->leave();
        return null;
    }
} /* end of class "Inscripciones" */

?>