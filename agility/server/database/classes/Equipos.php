<?php
/*
Equipos.php

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

class Equipos extends DBObject {

	protected $pruebaID;
	protected $jornadaID;
	protected $teamsByJornada; // {array} array de datos de todos los equipos de esta jornada
	protected $defaultTeam; //  {array} datos del equipo por defecto para esta prueba
	
	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {integer} $prueba Prueba ID
	 * @throws Exception if cannot contact database or invalid prueba/jornada ID
	 */
	function __construct($file,$prueba,$jornada) {
		parent::__construct($file);
		if ( $prueba<=0 ) {
			$this->errormsg="$file::construct() invalid prueba:$prueba ID";
			throw new Exception($this->errormsg);
		}
		$this->pruebaID=$prueba;
		$this->jornadaID=$jornada;
		$this->teamsByJornada=null;
		$this->defaultTeam=null;
		if ( $jornada<=0 ) { // a trick to handle special functions (queryByJornada)
			$this->myLogger->info("Constructor with invalid jornada ID:0");
			return;
		} 
		// obtenemos los equipos de esta jornada
		$res= $this->__select(
				/* SELECT */ 	"*",
				/* FROM */   	"Equipos",
				/* WHERE */ 	"( Prueba = $prueba ) AND ( Jornada = $jornada )",
				/* ORDER BY */	"",
				/* LIMIT */ 	""
		);
		if (!is_array($res)) {
			$this->errormsg="$file::construct() cannot get team data for prueba:$prueba jornada:$jornada" ;
			throw new Exception($this->errormsg);
		}
		$this->teamsByJornada=$res['rows'];
		foreach ($this->teamsByJornada as $team) {
			if ($team['DefaultTeam']==1) $this->defaultTeam=$team;
		}
	}
	
	function insert() {
		$this->myLogger->enter();
		$prueba=$this->pruebaID;
		$jornada=$this->jornadaID;
		// obtenemos el orden a insertar
		$obj=$this->__selectObject("MAX(Orden) AS Last","Equipos","(Prueba=$prueba) AND (Jornada=$jornada)");
		$ord=($obj!=null)?1+intval($obj->Last):1; // evaluate latest in order
		
		// componemos un prepared statement
		$sql ="INSERT INTO Equipos (Prueba,Jornada,Orden,Categorias,Nombre,Observaciones,DefaultTeam,Miembros) 
					VALUES($prueba,$jornada,?,?,?,?,0,'BEGIN,END')";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('isss',$orden,$categorias,$nombre,$observaciones);
		if (!$res) return $this->error($stmt->error);  
		
		// iniciamos los valores, chequeando su existencia
		$orden		= $ord;
		$categorias = http_request("Categorias","s",null,false); // may be null
		$nombre 	= http_request("Nombre","s",null,false); // not null
		$observaciones= http_request('Observaciones',"s",null,false); // may be null
		$this->myLogger->info("Prueba:$prueba Jornada:$jornada Nombre:'$nombre' Observaciones:'$observaciones'");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error); 
		$stmt->close();
		$this->myLogger->leave();
		return "";
	}
	
	function update($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Equipo ID provided");
		// componemos un prepared statement. Do not mofify any field that not matches current pruebaID
		$sql ="UPDATE Equipos SET Nombre=? , Observaciones=?, Categorias=? WHERE ( ID=$id )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('sss',$n,$o,$c);
		if (!$res) return $this->error($stmt->error); 
		
		// iniciamos los valores, chequeando su existencia
		$n = http_request("Nombre","s",null,false); // not null
		$o = http_request('Observaciones',"s",'',false);
		$c = http_request('Categorias',"s",'',false);
		
		$this->myLogger->info("Team:$id Prueba:{$this->pruebaID} Jornada:{$this->jornadaID} Nombre:'$n' Observ:'$o' Categ:'$c'");
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error); 
		$stmt->close();
		$this->myLogger->leave();
		return "";
	}
	
	function delete($id) {
		$this->myLogger->enter();
		$def=$this->defaultTeam['ID'];
		if ($id<0) return $this->error("Invalid Equipo ID:$id provided");
		if ($id==$def) return $this->error("Cannot delete default team for this Contest");

		// fase 1: desasignamos los perros de este equipo (los asignamos al equipo por defecto de la jornada)
		$lista="";
		foreach ($this->teamsByJornada as $team) {
			if ($team['ID']==$id) {$lista=$team['Miembros']; break; }
		}
		if ($lista==="") return $this->error("No encuentro el equipo $id en la lista de equipos de esta jornada");
		$perros=explode(",",$lista);
		foreach($perros as $perro) {
			if ($perro==="BEGIN") continue;
			if ($perro==="END") continue;
			$this->insertInscripcion(intval($perro)); // don't use update, as no need to preserve old team
		}
		// fase 2: borramos el equipo antiguo de la base de datos
		$res= $this->query("DELETE FROM Equipos WHERE (ID=$id)");
		if (!$res) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}
	
	function select() {
		$this->myLogger->enter();
		//needed to properly handle multisort requests from datagrid
		$sort=getOrderString( 
			http_request("sort","s",""),
			http_request("order","s",""),
			"Nombre ASC"
		);
		// evaluate if any search criteria
		$search=http_Request("where","s","");
		// evaluate offset and row count for query
		$page=http_request("page","i",1);
		$rows=http_request("rows","i",50);
		$limit="";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit="".$offset.",".$rows;
		}
		$where = "(Equipos.Prueba={$this->pruebaID}) AND (Equipos.Jornada={$this->jornadaID})";
		if ($search!=='') $where=$where." AND ( (Equipos.Nombre LIKE '%$search%') OR ( Equipos.Observaciones LIKE '%$search%') ) ";
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "Equipos",
				/* WHERE */ $where,
				/* ORDER BY */ $sort,
				/* LIMIT */ $limit
		);
		$this->myLogger->leave();
		return $result;
	}
	
	function enumerate() { // like select but do not provide order query. Used in comboboxes
		$this->myLogger->enter();
		// evaluate search string
		$q=http_request("q","s","");
		$where = "(Equipos.Prueba={$this->pruebaID}) AND (Equipos.Jornada={$this->jornadaID})";
		if ($q!=="") $where=$where." AND ( ( Equipos.Nombre LIKE '%$q%' ) OR ( Equipos.Observaciones LIKE '%$q%' ) )";
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "Equipos",
				/* WHERE */ $where,
				/* ORDER BY */ "Nombre ASC",
				/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Select a (single) entry that matches with provided Equipo ID
	 * @param {integer} $id Equipo ID (primary key)
	 * @return {array} result on success; null on error
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Provided Equipo ID");
		$obj=$this->__getObject("Equipos",$id);
		if (!is_object($data))	return $this->error("No Equipo found with ID=$id");
		$data= json_decode(json_encode($obj), true); // convert object to array
		$data['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		return $data;
	}
	
	/**
	 * insert $from before(where==false) or after(where=true) $to
	 * This dnd routine uses a Orden shift'ng: increase every remaining row order,
	 * and assign moved row orden to created hole
	 * @param {integer} $from id to move
	 * @param {integer} $to id to insert arounn
	 * @param {boolean} $where false:insert before  / true:insert after
	 */
	function dragAndDrop($from,$to,$where) {
		$this->myLogger->enter();
		$p=$this->pruebaID;
		$j=$this->jornadaID;
		// get from/to Tanda's ID
		$f=$this->__selectObject("*","Equipos","(Prueba=$p) AND (Jornada=$j) AND (ID=$from)");
		$t=$this->__selectObject("*","Equipos","(Prueba=$p) AND (Jornada=$j) AND (ID=$to)");
		if(!$f || !$t) {
			$this->myLogger->error("Error: no ID for equipo's order '$from' and/or '$to' on prueba:$p jornada:$j");
			return $this->errormsg;
		}
		$torder=$t->Orden;
		$neworder=($where)?$torder+1/*after*/:$torder/*before*/;
		$comp=($where)?">"/*after*/:">="/*before*/;
		$str="UPDATE Equipos SET Orden=Orden+1 WHERE ( Prueba = $p ) AND ( Jornada = $j ) AND ( Orden $comp $torder )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$str="UPDATE Equipos SET Orden=$neworder WHERE ( Prueba = $p ) AND ( Jornada = $j ) AND ( ID = $from )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		return "";
	}
	
	/**
	 * Swap orden between requested equipos
	 * @param {integer} $from Equipo ID 1
	 * @param {integer} $to Equipo ID 2
	 * @return {string} error message or "" on success
	 */
	function swap($from,$to) {
		$this->myLogger->enter();
		$p=$this->pruebaID;
		$j=$this->jornadaID;
		// get from/to Tanda's ID
		$f=$this->__selectObject("*","Equipos","(Prueba=$p) AND (Jornada=$j) AND (ID=$from)");
		$t=$this->__selectObject("*","Equipos","(Prueba=$p) AND (Jornada=$j) AND (ID=$to)");
		if(!$f || !$t) {
			$this->myLogger->error("Error: no ID for equipo's order '$from' and/or '$to' on prueba:$p jornada:$j");
			return $this->errormsg;
		}
		$forden=$f->Orden;
		$torden=$t->Orden;
		// perform swap update.
		// TODO: make it inside a transaction
		$str="UPDATE Equipos SET Orden=$torden WHERE (ID=$from)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$str="UPDATE Equipos SET Orden=$forder WHERE (ID=$to)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return ""; // mark success
	}
	
	/**
	 * Inscribe a un perro en el equipo indicado.
	 * Si no se indica, lo inscribe en el equipo por defecto
	 * @param {integer} $idperro ID Perro
	 * @param {integer} $idteam ID equipo. 0: default team
	 * @return "" on success; else error String
	 */
	function insertIntoTeam($idperro,$idteam=0) {
		// si no idteam se coge el valor por defecto
		if ($idteam==0) $idteam=$this->defaultTeam['ID'];
		// comprobamos si el equipo pertenece a esta jornada
		$team=null;
		foreach($this->teamsByJornada as $equipo) {
			if ($equipo['ID']==$idteam) {$team=&$equipo; break;}
		}
		if ($team==null) return $this->error("El equipo:$idteam NO pertenece a la jornada:{$this->jornadaID}");
		$ordensalida=$team['Miembros'];
		// lo borramos para evitar una posible doble insercion
		$str = ",$idperro,";
		$nuevoorden = str_replace ( $str, ",", $ordensalida );
		// componemos el tag que hay que insertar
		$myTag="$idperro,END";
		// y lo insertamos en lugar que corresponde
		$ordensalida = str_replace ( "END", $myTag, $nuevoorden );
		// update database
		$team['Miembros']=$ordensalida;
		$str="UPDATE Equipos SET Miembros='$ordensalida' WHERE (ID=$idteam)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		return ""; // success
	}
	
	/**
	 * Borra a un perro de los equipos de la jornada
	 * @param {integer} $perro IDPerro
	 */
	function removeFromTeam($idperro) {
		$str = ",$idperro,";
		foreach($this->teamsByJornada as &$team) {
			$ordensalida=$team['Miembros'];
			$nuevoorden = str_replace ( $str, ",", $ordensalida );
			if ($ordensalida===$nuevoorden) continue; // not inscribed in this team
			// update database
			$team['Miembros']=$nuevoorden;
			$str="UPDATE Equipos SET Miembros='$nuevoorden' WHERE (ID={$team['ID']})";
			$rs=$this->query($str);
			if (!$rs) return $this->error($this->conn->error);
		}
		return "";
	}
	
	/**
	 * Cambia un perro de equipo
	 * @param {integer} $idperro
	 * @param {integer} $idequipo
	 */
	function updateTeam($idperro,$idequipo) {
		// eliminamos inscripcion anterior
		$this->removeFromTeam($idperro);
		// insertamos el nueva
		$this->insertIntoTeam($idperro,$idequipo);
		// actualizamos tabla de resultados
		$str="UPDATE Resultados SET Equipo=$idequipo WHERE (Perro=$idperro) AND (Jornada={$this->jornadaID})";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		return "";
	}
	
	function getTeamByPerro($idperro) {
		$p=$this->pruebaID;
		$j=$this->jornadaID;
		$res=$this->__selectAsArray("*","Equipos","(Prueba=$p) AND (Jornada=$j) AND (Miembros LIKE ',$idperro,') ");
		return $res;
	}
	
	/**
	 * Obtiene la lista de equipos de una jornada ajustada por orden de salida
	 */
	function getTeamOrder() {
		return usort( $this->teamsByJornada,function($a,$b){return $a['Orden'] - $b['Orden'];});
	}
	
	/**
	 * Obtiene los datos del equipo por defecto
	 * @return {array} default Team data
	 */
	function getDefaultTeam() {
		return $this->defaultTeam;
	}
	
	/**
	 * Reordena al azar el campo 'orden' de los equipos de esta jornada
	 */
	function random() {
		// reordenamos al azar el array de equipos
		suffle($this->teamsByJornada);
		// componemos un prepared statement
		$sql ="UPDATE Equipos SET Orden=? WHERE (ID=?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ii',$orden,$equipo);
		if (!$res) return $this->error($stmt->error);
		// recorremos los equipos renumerando el orden
		$count=1;
		foreach ($this->teamsByJornada as $team) {
			$orden=$count;
			$equipo=$team['ID'];
			$res=$stmt->execute();
			if (!$res) return $this->error($stmt->error);
			$count++;
		} 
		$stmt->close();
		return "";
	}
}
	
?>