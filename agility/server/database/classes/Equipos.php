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
	}
	
	function getTeamsByJornada(){
		$prueba=$this->pruebaID;
		$jornada=$this->jornadaID;
		// obtenemos los equipos de esta jornada
		$res= $this->__select("*","Equipos","( Prueba = $prueba ) AND ( Jornada = $jornada )","Nombre ASC","");
		if (!is_array($res)) {
			return $this->error("{$this->$file}::getTeamsByJornada() cannot get team data for prueba:$prueba jornada:$jornada");
		}
		return $res['rows'];
	}
	
	function getDefaultTeam() {
		$prueba=$this->pruebaID;
		$jornada=$this->jornadaID;
		return $this->__selectAsArray("*","Equipos","( Prueba=$prueba ) AND ( Jornada=$jornada ) AND (DefaultTeam=1)");
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
		if ($id<0) return $this->error("Equipos::delete():Invalid Equipo ID:$id provided");
		// fase 1: buscamos datos del equipo
		$team=$this->__getArray("Equipos",$id);
		if (!is_array($team)){
			return $this->error("Equipos::delete(): No encuentro el equipo $id en la lista de equipos de esta jornada");
		}
		// fase 2: comprobamos que no sea el equipo por defecto
		if ( intval($team['DefaultTeam'])==0 ) {
			return $this->error("Equipos::delete():Cannot delete default team for this Contest");
		}
		// fase 3: si este no es el equipo por defecto, reasignamos los perros
		$perros=explode(",",$team['Miembros']);
		foreach($perros as $perro) {
			if ($perro==="BEGIN") continue;
			if ($perro==="END") continue;
			$this->insertInscripcion(intval($perro)); // don't use update, as no need to preserve old team
		}
		// fase 4: borramos el equipo antiguo de la base de datos
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
		$data=$this->__getObject("Equipos",$id);
		if (!is_object($data))	return $this->error("No Equipo found with ID=$id");
		$data= json_decode(json_encode($data), true); // convert object to array
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
		$str="UPDATE Equipos SET Orden=$torden WHERE (ID=$from)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$str="UPDATE Equipos SET Orden=$forden WHERE (ID=$to)";
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
		$team=null;
		if ($idteam==0) {
			// si no id team buscamos el equipo al que pertenece (o el default)
			$team=$this->getTeamByPerro($idperro);
			$idteam=$team['ID'];
		} else {
			// obtenemos datos del equipo solicitado
			$teams=$this->getTeamsByJornada();
			foreach($teams as $equipo) { 
				if ($equipo['ID']==$idteam) {$team=$equipo; break;}
			}
			if ($team==null) return $this->error("El equipo:$idteam NO pertenece a la jornada:{$this->jornadaID}");
		}
		// borramos el perro de todos los equipos de la jornada
		$this->removeFromTeam($idperro);
		$ordensalida=$team['Miembros'];
		// como esta copia no esta actualizada, hay que remover tambien de la lista
		$str = ",$idperro,";
		$nuevoorden = str_replace ( $str, ",", $ordensalida );
		// componemos el tag que hay que insertar
		$myTag=",$idperro,END";
		// y lo insertamos en lugar que corresponde
		$ordensalida = str_replace ( ",END", $myTag, $nuevoorden );
		// update database
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
		$teams=$this->getTeamsByJornada();
		foreach($teams as $team) {
			$idequipo=$team['ID'];
			$ordensalida=$team['Miembros'];
			$nuevoorden = str_replace ( $str, ",", $ordensalida );
			if ($ordensalida===$nuevoorden) continue; // not inscribed in this team. no teed to update DB
			// update database
			$str="UPDATE Equipos SET Miembros='$nuevoorden' WHERE (ID=$idequipo)";
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
		// insertamos el nueva (remove implicito)
		$this->insertIntoTeam($idperro,$idequipo);
		// actualizamos tabla de resultados
		$str="UPDATE Resultados SET Equipo=$idequipo WHERE (Perro=$idperro) AND (Jornada={$this->jornadaID})";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		return "";
	}
	
	function getTeamByPerro($idperro) {
		$prueba=$this->pruebaID;
		$jornada=$this->jornadaID;
		$team=$this->__selectAsArray("*","Equipos","( Prueba=$prueba ) AND ( Jornada=$jornada ) AND (Miembros LIKE '%,$idperro,%')");
		if (is_array($team)) return $team; 
		$this->myLogger->info("El perro $idperro no figura en ningun equipo de la jornada {$this->jornadaID}");
		return $this->getDefaultTeam();
	}
	
	/**
	 * Obtiene la lista de equipos de una jornada ajustada por orden de salida
	 */
	function getTeamOrder() {
		return usort( $this->getTeamsByJornada(),function($a,$b){return $a['Orden'] - $b['Orden'];});
	}

	/**
	 * Reordena al azar el campo 'orden' de los equipos de esta jornada
	 */
	function random() {
		// reordenamos al azar el array de equipos
		$teams=$this->getTeamsByJornada();
		shuffle($teams);
		// componemos un prepared statement
		$sql ="UPDATE Equipos SET Orden=? WHERE (ID=?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ii',$orden,$equipo);
		if (!$res) return $this->error($stmt->error);
		// recorremos los equipos renumerando el orden
		$count=1;
		foreach ($teams as $team) {
			$orden=$count;
			$equipo=$team['ID'];
			$res=$stmt->execute();
			if (!$res) return $this->error($stmt->error);
			$count++;
		} 
		$stmt->close();
		return "";
	}

    /**
     * check teams on this journey and eval number of dogs belonging to each one
     */
    function verify() {
        $this->myLogger->enter();
        // comprobamos que la jornada sea correcta
        $j=$this->__getObject("Jornadas",$this->jornadaID);
        $max=4;
        $min=3;
        if ( intval($j->Equipos3)!=0) $min=3;
        else  if ( intval($j->Equipos4)!=0) $min=4;
        else return "La jornada {$j->jornadaID} - '{$j->Nombre}' no tiene declaradas pruebas por equipos";
        $obj=$this->getTeamsByJornada();
        if (!is_array($obj)) return $obj; // means error
        $res=array();
        $res['default']=array();
        $res['teams']=array();
        $res['more']=array();
        $res['less']=array();
        foreach ($obj as $team) {
            $item= array('Nombre' => $team['Nombre'], 'Numero' => count(explode(",",$team['Miembros']))-2);
            array_push($res['teams'],$item);
            if ($team['DefaultTeam']==1) { // vemos el numero de perros que hay en el equipo por defecto
                array_push($res['default'],$item);
                continue;
            }
            if ($item['Numero']>$max) { // si el equipo pasa de 4 perros tomamos nota
                array_push($res['more'],$item);
                continue;
            }
            if ($item['Numero']<$min) { // si el equipo tiene menos de "min" perros tomamos nota
                array_push($res['less'],$item);
                continue;
            }
        }
        $this->myLogger->leave();
        return $res;
    }
}
	
?>