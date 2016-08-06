<?php
/*
Equipos.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once("OrdenSalida.php");

class Equipos extends DBObject {

	protected $pruebaID=0;
	protected $jornadaID=0;
    protected $teamsByJornada=null;
    protected $defaultTeam=null;
	
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
        if ($this->teamsByJornada==null) {
            $p=$this->pruebaID;
            $j=$this->jornadaID;
            // obtenemos los equipos de esta jornada
            $res= $this->__select("*","Equipos","( Prueba = $p ) AND ( Jornada = $j )","","");
            if (!is_array($res)) {
                $this->myLogger->error("{$this->file}::getTeamsByJornada() cannot get team data for prueba:$p jornada:$j");
            }
            $this->teamsByJornada=$res['rows'];
        }
        return $this->teamsByJornada;
    }

	function getDefaultTeam() {
        if ($this->defaultTeam==null) {
            $prueba=$this->pruebaID;
            $jornada=$this->jornadaID;
            $this->defaultTeam=$this->__selectAsArray("*","Equipos","( Prueba=$prueba ) AND ( Jornada=$jornada ) AND (DefaultTeam=1)");
        }
        return $this->defaultTeam;
	}

	function insert() {
        // iniciamos los valores, chequeando su existencia
        $categorias = http_request("Categorias","s",null,false); // may be null
        $nombre 	= http_request("Nombre","s",null,false); // not null
        $observaciones= http_request('Observaciones',"s",null,false); // may be null
        return $this->realInsert($categorias,$nombre,$observaciones);
    }

	function realInsert($categorias,$nombre,$observaciones) {
		$this->myLogger->enter();
		$prueba=$this->pruebaID;
		$jornada=$this->jornadaID;
        $this->myLogger->info("Prueba:$prueba Jornada:$jornada Nombre:'$nombre' Observaciones:'$observaciones'");

        // look for duplicate team
        $res= $this->__selectObject( "*", "Equipos", "( Prueba=$prueba) AND ( Jornada=$jornada ) AND (nombre='$nombre')"  );
        if($res!=null){ // already created, try to update
            if (!is_object($res)) return $res; // error in trying locate team
            $this->myLogger->notice("El Equipo '$nombre' '$categorias' ya esta inscrito en la prueba:$prueba jornada:$jornada");
            return $this->real_update($res->ID,$nombre,$observaciones,$categorias);
        }

		// componemos un prepared statement
        // Miembros is no longer used. just set not null for DB integrity
		$sql ="INSERT INTO Equipos (Prueba,Jornada,Categorias,Nombre,Observaciones,DefaultTeam,Miembros)
					VALUES($prueba,$jornada,?,?,?,0,'BEGIN,END')";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('sss',$categorias,$nombre,$observaciones);
		if (!$res) return $this->error($stmt->error);
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error);
        $insert_id=$stmt->insert_id; // retrieve inserted team ID
		$stmt->close();

        // list of mangas for this jornada
        $mng=$this->__select("*","Mangas","(Jornada=$jornada)","","");
        foreach($mng['rows'] as $manga) {
            $this->myLogger->trace("Insertando al equipo $insert_id en Orden_Equipos jornada:$jornada manga:{$manga['ID']}");
            $osobj=new OrdenSalida("Equipos::insert",$manga['ID']);
            // add team to Orden_Equipos in every related mangas
            $osobj->insertIntoTeamList($insert_id);
        }
		$this->myLogger->leave();
		return "";
	}

	function update($id) {
        // iniciamos los valores, chequeando su existencia
        $n = http_request("Nombre","s",null,false); // not null
        $o = http_request('Observaciones',"s",'',false);
        $c = http_request('Categorias',"s",'',false);
        return $this->real_update($id,$n,$o,$c);
    }

	function real_update($id,$n,$o,$c) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Equipo ID provided");
        $this->myLogger->trace("Team:$id Prueba:{$this->pruebaID} Jornada:{$this->jornadaID} Nombre:'$n' Observ:'$o' Categ:'$c'");

		// componemos un prepared statement. Do not mofify any field that not matches current pruebaID
		$sql ="UPDATE Equipos SET Nombre=? , Observaciones=?, Categorias=? WHERE ( ID=$id )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('sss',$n,$o,$c);
		if (!$res) return $this->error($stmt->error);
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error); 
		$stmt->close();

        // successful exit
		$this->myLogger->leave();
		return "";
	}
	
	function delete($id) {
		$this->myLogger->enter();
        $jornada=$this->jornadaID;
		if ($id<0) return $this->error("Equipos::delete():Invalid Equipo ID:$id provided");

		// fase 1: buscamos datos del equipo a borrar
		$team=$this->__getArray("Equipos",$id);
		if (!is_array($team)){
			return $this->error("Equipos::delete(): No encuentro el equipo $id en la lista de equipos de esta jornada");
		}

		// fase 2: comprobamos que no sea el equipo por defecto
		if ( intval($team['DefaultTeam'])!=0 ) {
			return $this->error("Equipos::delete():Cannot delete default team for this Contest");
		}

		// fase 3: reasignamos los perros al equipo por defecto
        $dteam=$this->getDefaultTeam()['ID'];
        $res=$this->query("UPDATE Resultados SET Equipo=$dteam WHERE (Equipo=$id)");
        if (!$res) return $this->error($this->conn->error);

        // fase 4: borramos el equipo del orden de salida de equipos de la manga
        $mng=$this->__select("*","Mangas","(Jornada=$jornada)","",""); // list of mangas for this jornada
        foreach($mng['rows'] as $manga) {
            $this->myLogger->trace("Eliminando el equipo:$id de Orden_Equipos jornada:$jornada manga:{$manga['ID']}");
            $osobj=new OrdenSalida("Equipos::remove",$manga['ID']);
            // add team to Orden_Equipos in every related mangas
            $osobj->removeFromTeamList($id);
        }

        // fase 5: finalmente borramos el equipo antiguo de la base de datos
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
			"ID ASC" // "Orden" no longer exists, so default sort order is by ID
		);
		// evaluate if any search criteria
		$search=http_Request("where","s","");
		// evaluate offset and row count for query
		$page=http_request("page","i",1);
		$rows=http_request("rows","i",25);
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
	 * Inscribe a un perro en el equipo indicado.
	 * Si no se indica, lo inscribe en el equipo por defecto
     * TODO: Asumimos que la jornada no está cerrada....
     *
     * Nota: El campo "Miembros" ya no se usa.
     * Lo que haremos sera actualizar el campo "Equipo" de la tabla de resultados
	 * @param {integer} $idperro ID Perro
	 * @param {integer} $idteam ID equipo. 0: default team
	 * @return "" on success; else error String
	 */
	function updateTeam($idperro,$idteam=0) {
		if ($idteam==0) { // equipo no especificado: search default
            $idteam=$this->getDefaultTeam()['ID'];
		}
		// obtenemos datos del equipo solicitado
        $team=null;
		$teams=$this->getTeamsByJornada();
		foreach($teams as $equipo) {
			if ($equipo['ID']==$idteam) {$team=$equipo; break;}
		}
		if ($team==null) return $this->error("El equipo:$idteam NO pertenece a la jornada:{$this->jornadaID}");

        // asignamos el perro
        $res=$this->query("UPDATE Resultados SET Equipo=$idteam WHERE (Perro=$idperro) AND (Jornada={$this->jornadaID})");
        if (!$res) return $this->error($this->conn->error);

		return ""; // success
	}

    /**
     * Obtiene los datos del equipo en el que esta inscrito el perro para esta jornada
     * Si no los encuentra, indica error y retorna el equipo por defecto
     * @param $idperro
     * @return mixed|null|object|stdClass Errormsg o array con los datos
     */
	function getTeamByPerro($idperro) {
		$jornada=$this->jornadaID;
        $team=$this->__selectAsArray(
            "DISTINCT Equipos.*",
            "Equipos,Resultados",
            "(Resultados.Jornada=$jornada) AND (Resultados.Perro=$idperro) AND
             (Resultados.Jornada=Equipos.Jornada) AND (Resultados.Equipo=Equipos.ID)");
		if (is_array($team)) return $team; 
		// $this->myLogger->info("El perro $idperro no figura en ningun equipo de la jornada {$this->jornadaID}");
		return $this->getDefaultTeam();
	}

    // like Inscripciones::inscritosByTeam but no search nor order
    function getPerrosByTeam($team) {
        $this->myLogger->enter();
        // obtenemos los datos del equipo
        $teamobj=$this->__getObject("Equipos",$team);
        if (!is_object($teamobj))
            return $this->error("No puedo obtener datos del equipo con ID: $team");
        // vemos el numero de la jornada asociada
        $jornadaobj=$this->__getObject("Jornadas",$teamobj->Jornada);
        if (!is_object($jornadaobj))
            return $this->error("No puedo obtener datos de la jornada: {$teamobj->Jornada} asociada al equipo: $team");
        // extraemos la lista de inscritos
        $tname=escapeString($teamobj->Nombre);
        $lista=$this->__select(
        /*select*/ "DISTINCT Resultados.Prueba,Resultados.Jornada, Resultados.Dorsal, Resultados.Perro,
                            Resultados.Nombre, Resultados.Raza, Resultados.Licencia, Resultados.Categoria, Resultados.Grado,
                            Resultados.Celo,Resultados.NombreGuia,Resultados.NombreClub, Resultados.Equipo,
                            PerroGuiaClub.Club AS Club, PerroGuiaClub.Guia AS Guia,PerroGuiaClub.LogoClub AS LogoClub,
                            '$tname' AS NombreEquipo",
            /* from */	"Resultados,PerroGuiaClub",
            /* where */ "( PerroGuiaClub.ID = Resultados.Perro)	AND ( Resultados.Jornada={$teamobj->Jornada} ) AND ( Resultados.Equipo=$team )",
            /* order */ "NombreClub ASC, Categoria ASC, Grado ASC, Nombre ASC",
            /* limit */ ""
        );
        $this->myLogger->leave();
        return $lista['rows'];
    }

    /**
     * check teams on this journey and eval number of dogs belonging to each one
     */
    function verify() {
        $this->myLogger->enter();
        // comprobamos que la jornada sea correcta
        $j=$this->__getObject("Jornadas",$this->jornadaID);
        $max=4;
        $min=0;
		switch(intval($j->Equipos3)) {
			case 1:$min=3;$max=4; break; // old style 3 best of 4
			case 2:$min=2;$max=3; break; // 2 besto of 3
			case 3:$min=3;$max=4; break; // 3 best of 4
			default: break;
		}
		switch(intval($j->Equipos4)) {
			case 1:$min=4;$max=4; break; // old style 4 combined
			case 2:$min=2;$max=2; break; // 2 combined
			case 3:$min=3;$max=3; break; // 3 combined
			case 4:$min=4;$max=4; break; // 4 combined
			default: break;
		}
        if ($min==0) return "La jornada {$j->jornadaID} - '{$j->Nombre}' no tiene declaradas pruebas por equipos";
        $res=array();
        $res['default']=array();
        $res['teams']=array();
        $res['more']=array();
        $res['less']=array();
        // extraemos la cuenta de equipos y componentes por equipos
        $list=$this->__select(
            /*select */ "Equipos.*,Resultados.Equipo, CONVERT(count(*)/2,UNSIGNED) AS Numero",
            /* FROM */ "Equipos,Resultados",
            /* WHERE */ "(Equipos.ID=Resultados.Equipo) AND (Equipos.JORNADA={$this->jornadaID})",
            /* ORDER */ "",
            /* LIMIT */ "",
            /* GROUP */ "Equipo"
        );
        if (!is_array($list)) return $list; // means error
        foreach ($list['rows'] as $team) {
            array_push($res['teams'],$team);
            if ($team['DefaultTeam']==1) { // vemos el numero de perros que hay en el equipo por defecto
                array_push($res['default'],$team);
                continue;
            }
            if ($team['Numero']>$max) { // si el equipo pasa de 4 perros tomamos nota
                array_push($res['more'],$team);
                continue;
            }
            if ($team['Numero']<$min) { // si el equipo tiene menos de "min" perros tomamos nota
                array_push($res['less'],$team);
                continue;
            }
        }
        $this->myLogger->leave();
        return $res;
    }
}
	
?>