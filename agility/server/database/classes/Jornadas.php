<?php
/*
Jornadas.php

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
require_once("Mangas.php");
require_once("Tandas.php");
require_once(__DIR__."/../../auth/AuthManager.php");

class Jornadas extends DBObject {
	
	// bitfield of 512:Esp 256:KO 128:Eq4 64:Eq3 32:Opn 16:G3 8:G2 4:G1 2:Pre2 1:Pre1
	public static $tipo_ronda= array(
		/* 0 */ array(0,	''),
		/* 1 */ array(1,	'Pre-Agility (1 Manga)'),
		/* 2 */ array(2,	'Pre-Agility (2 Mangas)'),
		/* 3 */ array(4,	'Grado I'),
		/* 4 */ array(8,	'Grado II'),
		/* 5 */ array(16,	'Grado III'),
		/* 6 */ array(32,	'Open'),
		/* 7 */ array(64,	'Equipos ( 3 mejores )'),
		/* 8 */ array(128,	'Equipos ( 4 conjunta )'),
		/* 9 */ array(256,	'Ronda K.O.'),
		/*10 */ array(512,	'Manga especial'),
		/*11 */ array(24,	'Grado II y III conjunta'),
		/*12 */ array(1024,	'Equipos ( 2 mejores )'),
		/*13 */ array(2048,	'Equipos ( 2 conjunta )'),
		/*14 */ array(4096,	'Equipos ( 3 conjunta )')
	);
	
	protected $prueba; // id de prueba
	protected $jueces; // cache para la funcion fetchJuez()
	
	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {integer} $prueba Prueba ID for these jornadas
	 * @throws Exception if cannot contact database
	 */
	function __construct($file,$prueba) {
		parent::__construct($file);
		if ($prueba<0){
			$this->errormsg="$file::construct() invalid prueba ID";
			throw new Exception($this->errormsg);
		}
		$this->prueba=$prueba;
		$this->jueces=array( "1" => "-- Sin asignar --");
	}
	
	/*****
	 * No insert required: a set of 8 journeys are created on each every new prueba 
	 * update, delete, select (by) functions
	 */
	
	/**
	 * Update journey data
	 * @param {integer} $jornadaid
	 * @return string
	 */
	function update($jornadaid) {
		$this->myLogger->enter();
		// if prueba or jornada are closed refuse to upate
		if ($jornadaid<=0) return $this->error("Invalid jornada ID");

        // iniciamos los valores, chequeando su existencia
        $prueba = $this->prueba;
        $nombre = http_request("Nombre","s",null,false); // Name or comment for jornada
        $fecha = str_replace("/","-",http_request("Fecha","s","",false)); // mysql requires format YYYY-MM-DD
        $hora = http_request("Hora","s","",false);
        $grado1 = http_request("Grado1","i",0);
        $grado2 = http_request("Grado2","i",0);
        $grado3 = http_request("Grado3","i",0);
        $open = http_request("Open","i",0);
        $equipos3 = http_request("Equipos3","i",0);
        $equipos4 = http_request("Equipos4","i",0);
        $preagility = http_request("PreAgility","i",0);
        $preagility2 = http_request("PreAgility2","i",0);
        $ko = http_request("KO","i",0);
        $especial = http_request("Especial","i",0);
        $observaciones = http_request("Observaciones","s","(sin especificar)",false);
        $cerrada = http_request("Cerrada","i",0);
        $id= $jornadaid;
        $this->myLogger->info("ID: $id Prueba: $prueba Nombre: $nombre Fecha: $fecha Hora: $hora");

		// componemos un prepared statement
		$sql ="UPDATE Jornadas
				SET Prueba=?, Nombre=?, Fecha=?, Hora=?, Grado1=?, Grado2=?, Grado3=?,
					Open=?, Equipos3=?, Equipos4=?, PreAgility=?, PreAgility2=?, KO=?, Especial=?, Observaciones=?, Cerrada=?
				WHERE ( ID=? );";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('isssiiiiiiiiiisii',
				$prueba,$nombre,$fecha,$hora,$grado1,$grado2,$grado3,$open,$equipos3,$equipos4,$preagility,$preagility2,$ko,$especial,$observaciones,$cerrada,$id);
		if (!$res) return $this->error($this->conn->error); 

		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error); 
		$stmt->close();
		if (!$cerrada) {
			$mangas =new Mangas("jornadaFunctions",$id);
			$mangas->prepareMangas($id,$grado1,$grado2,$grado3,$open,$equipos3,$equipos4,$preagility,$preagility2,$ko,$especial,$observaciones);
			$ot= new Tandas("jornadas::update",$this->prueba,$id);
			$ot->populateJornada();
        }
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Delete jornada with provided ID
	 * @param {integer} jornada name primary key
	 * @return "" on success ; otherwise null
	 */
	function delete($jornadaid) {
		$this->myLogger->enter();
		if ($jornadaid<=0) return $this->error("Invalid Jornada ID");
		// borramos cada una de las mangas de esta jornada
		$mng=new Mangas("deleteJornada",$jornadaid);
		$res=$mng->selectByJornada();
		if (!is_array($res)) return $res; // error
		foreach($res['rows'] as $manga) {
			$mng->deleteByID($manga['ID']);
		}
		// borramos cada una de las tandas de la jornada
		$tnd=new Tandas("jornadas::delete()",$this->prueba,$jornadaid);
		$tnd->removeJornada();
		// Borramos equipos de esta prueba/jornada
		$res=$this->query("DELETE FROM Equipos WHERE ( Jornada = $jornadaid );");
		if (!$res) return $this->error($this->conn->error);
		// y borramos la propia jornada
		$res= $this->query("DELETE FROM Jornadas WHERE ( ID = $jornadaid );");
		if (!$res) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	} 
	
	/**
	 * Close jornada with provided ID
	 * @param {integer} jornada name primary key
	 * @return "" on success ; otherwise null
	 */
	function close($jornadaid) {
		$this->myLogger->enter();
		if ($jornadaid<=0) return $this->error("Invalid Jornada ID");
		// marcamos la jornada con ID=$jornadaid como cerrada
		$res= $this->query("UPDATE Jornadas SET Cerrada=1 WHERE ( ID=$jornadaid ) ;");
		if (!$res) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * delete all journeys that belongs to current pruebaID
	 */
	function deleteByPrueba() {
		$this->myLogger->enter();
		$p=$this->prueba;
		if ($p <= 0 ) return $this->error("Invalid Prueba ID"); 
		if ($p == 1 ) return $this->error("Cannot delete Journeys linked to default Contest");
		// cogemos las jornadas de esta prueba
		$res=$this->selectByPrueba();
		if (!is_array($res)) return $res;
		// borramos cada una de las jornadas 
		foreach($res['rows'] as $jornada) {
			$this->delete($jornada['ID']);
		}
		$this->myLogger->leave();
		return "";
	}
	
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("Invalid Jornada ID");
		
		// make query
		$obj=$this->__getObject("Jornadas",$id);
		if (!is_object($obj))	return $this->error("No Jornada found with ID=$id");
		$data= json_decode(json_encode($obj), true); // convert object to array
		$this->myLogger->leave();
		return $data;
	}
	
	/**
	 * select all jornadas related to provided prueba 
	 * @return {array} requested result
	 */
	function selectByPrueba() {
		$this->myLogger->enter();
		// retrieve result from parent __select() call
		$result= $this->__select(
				/* SELECT */ "*",
				/* FROM */ "Jornadas",
				/* WHERE */ "( Prueba = ".$this->prueba." )",
				/* ORDER BY */ "Numero ASC",
				/* LIMIT */ ""
		);
		// return composed array
		$this->myLogger->leave();
		return $result;
	}	
	
	/**
	 * search all jornadas related to provided prueba that matches provided criteria
	 * @param {integer} $allowClosed 1:allow listing closed jornadas; 0:don't
	 * @param {integer} $hideUnassigned 1:exclude, 0:include '-- Sin asignar --' journeys
	 * @return {array} requested data
	 */
	function searchByPrueba($allowClosed=0,$hideUnassigned=0) {
		$this->myLogger->enter();
		// evaluate search terms
		$q=http_request("q","s","");
		$cerrada=($allowClosed==0)?" AND ( Cerrada=0 )":"";
		$unassigned=($hideUnassigned==1)?" AND ( Nombre <> '-- Sin asignar --' )":"";
		$where= "( Prueba = {$this->prueba} ) $cerrada $unassigned";
		if ($q!=="") $where= "( Prueba = {$this->prueba} ) $cerrada $unassigned AND ( (Nombre LIKE '%$q%') OR (Numero LIKE '%$q%') ) ";
		// retrieve result from parent __select() call
		$result= $this->__select(
				/* SELECT */ "*",
				/* FROM */ "Jornadas",
				/* WHERE */ $where,
				/* ORDER BY */ "Numero ASC",
				/* LIMIT */ ""
		);
		// return composed array
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Obtiene el ID de la manga de tipo $tipo asociada a la jornada $jornada
	 * @param {object} $mangas lista de las mangas de estajornada
	 * @param {integer} $tipo campo Tipo a buscar en la tabla de mangas
	 * @param {integer} $round En rondas K.O. indica el numero de la ronda deseada 0..7,8
	 * @return {array} datos de la manga pedida ; null if not found
	 */
	private function fetchManga($mangas,$jornadaid,$tipo,$round=0) {
		foreach ($mangas as $manga) {
			if ($manga["Tipo"]==$tipo) return $manga;
		}
		$this->myLogger->error("Cannot locate Mangas of Tipo:$tipo in Jornada:$jornadaid");
		return null;
	}
	
	/** 
	 * cache para evaluar los jueces de la llamada roundsByJornada
	 * @param {int} $id
	 */
	private function fetchJuez($id) {
		if (! array_key_exists("$id",$this->jueces)) {
			$obj=$this->__getObject("Jueces",$id);
			$this->jueces["$id"]=$obj->Nombre;
		}
		return $this->jueces["$id"];
	}

    /**
     * Check license for allow access to teams/KO events
     * @param {AuthManager} $am authManager object
     * @param {integer} $id jornada id
     */
    function checkAccess($am,$id,$perms=0) {
        if ($id<=0) return $this->error("Jornada::checkAccess(): invalid Jornada ID");
        $j=$this->__getObject("Jornadas",$id);
        if (intval($perms)!=0) $res=$am->allowed($perms); // check against user provided check access
        // else check against jornada-dependent access permissions
        else if (intval($j->Equipos3)!=0) $res=$am->allowed(ENABLE_TEAMS);
        else if (intval($j->Equipos4)!=0) $res=$am->allowed(ENABLE_TEAMS);
        else if (intval($j->KO)!=0) $res=$am->allowed(ENABLE_KO);
        else $res=true;
        if (!$res) {
            $this->errormsg='<img src="/agility/images/sad_dog.png" width="75" alt="sad dog" style="float:right;"/>
                    <p style="font-weight:bold;">Requested feature is disabled due to current license registration permissions</p>';
            return null;
        }
        return "";
    }

	/**
	 * Devuelve una lista de las rondas de que consta esta jornada (GI,GII,GIII, PreAgility..)
	 * @param {int} $jornadaid ID de jornada
	 * @return {array} null on error, result in combogrid format "info,idManga1,idmanga2"
	 */
	function roundsByJornada($jornadaid) {
		$this->myLogger->enter();
		// on start, no jornada id is provided, so don't throw error
		if ($jornadaid<=0) {
			$result=array();
			$result['total']=0;
			$result['rows']=array();
			$this->myLogger->notice("jornada ID is 0: return");
			return $result;
		}
		// obtenemos informacion de la jornada y de las mangas de esta jornada
		$row=$this->__getObject("Jornadas",$jornadaid);
		if (!is_object($row)) return $this->error("No Jornadas with ID=$jornadaid");
		$mangas=$this->__select("*","Mangas","Jornada=$jornadaid","","");
		if (!is_array($mangas)) return $this->error("No Mangas with Jornada ID=$jornadaid");
		// retrieve result into an array
		$data=array();
		if ($row->Grado1!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,3); // 'Agility-1 GI'
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,4); // 'Agility-2 GI'
			array_push($data,array( 
									"Rondas" => Jornadas::$tipo_ronda[3][0],
									"Nombre" => Jornadas::$tipo_ronda[3][1],
									"Manga1" => $manga1['ID'],
									"Manga2" => $manga2['ID'],
									"NombreManga1" => 'Agility-1 GI',
									"NombreManga2" => 'Agility-2 GI',
									"Recorrido1" => $manga1['Recorrido'],
									"Recorrido2" => $manga2['Recorrido'],
									"Juez11" => $this->fetchJuez($manga1['Juez1']),
									"Juez12" => $this->fetchJuez($manga1['Juez2']),
									"Juez21" => $this->fetchJuez($manga2['Juez1']),
									"Juez22" => $this->fetchJuez($manga2['Juez2'])
									) );
		}
		if ($row->Grado2!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,5); // 'Agility GII'
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,10); // 'Jumping GII'
			array_push($data,array( 
									"Rondas" => Jornadas::$tipo_ronda[4][0],
									"Nombre" => Jornadas::$tipo_ronda[4][1], 
									"Manga1" => $manga1['ID'],
									"Manga2" => $manga2['ID'],
									"NombreManga1" => 'Agility GII',
									"NombreManga2" => 'Jumping GII',
									"Recorrido1" => $manga1['Recorrido'],
									"Recorrido2" => $manga2['Recorrido'],
									"Juez11" => $this->fetchJuez($manga1['Juez1']),
									"Juez12" => $this->fetchJuez($manga1['Juez2']),
									"Juez21" => $this->fetchJuez($manga2['Juez1']),
									"Juez22" => $this->fetchJuez($manga2['Juez2'])
									) );
		}
		if ($row->Grado3!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,6); // 'Agility GIII'
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,11); // 'Jumping GIII'
			array_push($data,array( 
									"Rondas" => Jornadas::$tipo_ronda[5][0],
									"Nombre" => Jornadas::$tipo_ronda[5][1],
									"Manga1" => $manga1['ID'],
									"Manga2" => $manga2['ID'],
									"NombreManga1" => 'Agility GIII',
									"NombreManga2" => 'Jumping GIII',
									"Recorrido1" => $manga1['Recorrido'],
									"Recorrido2" => $manga2['Recorrido'],
									"Juez11" => $this->fetchJuez($manga1['Juez1']),
									"Juez12" => $this->fetchJuez($manga1['Juez2']),
									"Juez21" => $this->fetchJuez($manga2['Juez1']),
									"Juez22" => $this->fetchJuez($manga2['Juez2'])
									) );
		}
		if ($row->Open!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,7); // 'Agility Open'
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,12); // 'Jumping Open'
			array_push($data,array( 
									"Rondas" => Jornadas::$tipo_ronda[6][0],
									"Nombre" => Jornadas::$tipo_ronda[6][1], 
									"Manga1" => $manga1['ID'],
									"Manga2" => $manga2['ID'],
									"NombreManga1" => 'Agility',
									"NombreManga2" => 'Jumping',
									"Recorrido1" => $manga1['Recorrido'],
									"Recorrido2" => $manga2['Recorrido'],
									"Juez11" => $this->fetchJuez($manga1['Juez1']),
									"Juez12" => $this->fetchJuez($manga1['Juez2']),
									"Juez21" => $this->fetchJuez($manga2['Juez1']),
									"Juez22" => $this->fetchJuez($manga2['Juez2'])
									) );
		}
		if ($row->PreAgility!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,1); // 'Pre-Agility (1 manga)'
			$manga2= null;
			array_push($data,array( 
									"Rondas" => Jornadas::$tipo_ronda[1][0],
									"Nombre" => Jornadas::$tipo_ronda[1][1],
									"Manga1" => $manga1['ID'],
									"Manga2" => 0,
									"NombreManga1" => 'Pre-Agility 1',
									"NombreManga2" => '',
									"Recorrido1" => $manga1['Recorrido'],
									"Recorrido2" => -1,
									"Juez11" => $this->fetchJuez($manga1['Juez1']),
									"Juez12" => $this->fetchJuez($manga1['Juez2']),
									"Juez21" => $this->fetchJuez(1),
									"Juez22" => $this->fetchJuez(1)
									 ) );
		}			
		if ($row->PreAgility2!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,1); // 'Pre-Agility (2 mangas)
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,2); // 'Pre-Agility (2 mangas)
			array_push($data,array( 
									"Rondas" => Jornadas::$tipo_ronda[2][0],
									"Nombre" => Jornadas::$tipo_ronda[2][1], 
									"Manga1" => $manga1['ID'],
									"Manga2" => $manga2['ID'],
									"NombreManga1" => 'Pre-Agility 1',
									"NombreManga2" => 'Pre-Agility 2',
									"Recorrido1" => $manga1['Recorrido'],
									"Recorrido2" => $manga2['Recorrido'],
									"Juez11" => $this->fetchJuez($manga1['Juez1']),
									"Juez12" => $this->fetchJuez($manga1['Juez2']),
									"Juez21" => $this->fetchJuez($manga2['Juez1']),
									"Juez22" => $this->fetchJuez($manga2['Juez2'])
									) );
		}
		if ($row->Equipos3!=0) {
			switch($row->Equipos3) {
				case 1: /* 3 best of 4 (compatibility mode)  */ $idx=7; break;
				case 2: /* 2 best of 3	*/ $idx=12; break;
				case 3: /* 3 best of 4  */ $idx=7; break;
				default: $idx=7; break;
			}
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,8); // 'Agility Equipos (3 mejores)'
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,13); // 'Jumping Equipos (3 mejores)'
			array_push($data,array( 
									"Rondas" => Jornadas::$tipo_ronda[$idx][0],
									"Nombre" => Jornadas::$tipo_ronda[$idx][1],
									"Manga1" => $manga1['ID'],
									"Manga2" => $manga2['ID'],
									"NombreManga1" => 'Agility Eq.',
									"NombreManga2" => 'Jumping Eq.',
									"Recorrido1" => $manga1['Recorrido'],
									"Recorrido2" => $manga2['Recorrido'],
									"Juez11" => $this->fetchJuez($manga1['Juez1']),
									"Juez12" => $this->fetchJuez($manga1['Juez2']),
									"Juez21" => $this->fetchJuez($manga2['Juez1']),
									"Juez22" => $this->fetchJuez($manga2['Juez2'])
									) );
		}
		if ($row->Equipos4!=0) {
			switch($row->Equipos4) {
				case 1: /* 4 combined (compatibility mode)  */ $idx=8; break;
				case 2: /* 2 combined	*/ $idx=12; break;
				case 3: /* 3 combined  */ $idx=13; break;
				case 4: /* 4 combined  */ $idx=8; break;
				default: $idx=8; break;
			}
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,9); // 'Agility Equipos (conjunta)'
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,14); // 'Jumping Equipos (conjunta)'
			array_push($data,array( 
									"Rondas" => Jornadas::$tipo_ronda[$idx][0],
									"Nombre" => Jornadas::$tipo_ronda[$idx][1],
									"Manga1" => $manga1['ID'],
									"Manga2" => $manga2['ID'],
									"NombreManga1" => 'Agility Eq.',
									"NombreManga2" => 'Jumping Eq.',
									"Recorrido1" => $manga1['Recorrido'],
									"Recorrido2" => $manga2['Recorrido'],
									"Juez11" => $this->fetchJuez($manga1['Juez1']),
									"Juez12" => $this->fetchJuez($manga1['Juez2']),
									"Juez21" => $this->fetchJuez($manga2['Juez1']),
									"Juez22" => $this->fetchJuez($manga2['Juez2'])
									) );
		}
		if ($row->KO!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,15); // Ronda K.O.
			$manga2= null;
			array_push($data,array( 
									"Rondas" => Jornadas::$tipo_ronda[9][0],
									"Nombre" => Jornadas::$tipo_ronda[9][1], 
									"Manga1" => $manga1['ID'],
									"Manga2" => 0,
									"NombreManga1" => 'Manga K.O.',
									"NombreManga2" => '',
									"Recorrido1" => $manga1['Recorrido'],
									"Recorrido2" => -1,
									"Juez11" => $this->fetchJuez($manga1['Juez1']),
									"Juez12" => $this->fetchJuez($manga1['Juez2']),
									"Juez21" => $this->fetchJuez(1),
									"Juez22" => $this->fetchJuez(1)
									) );
		}
		if ($row->Especial!=0) {
			// TODO: $row->Special should indicante number of rounds. Current and default is 1
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,16); // 'Manga especial'
			$manga2= null;
			array_push($data,array( 
									"Rondas" => Jornadas::$tipo_ronda[10][0],
									"Nombre" => Jornadas::$tipo_ronda[10][1], 
									"Manga1" => $manga1['ID'],
									"Manga2" => 0,
									"NombreManga1" => 'Manga Especial',
									"NombreManga2" => '',
									"Recorrido1" => $manga1['Recorrido'],
									"Recorrido2" => -1,
									"Juez11" => $this->fetchJuez($manga1['Juez1']),
									"Juez12" => $this->fetchJuez($manga1['Juez2']),
									"Juez21" => $this->fetchJuez(1),
									"Juez22" => $this->fetchJuez(1)
									) );
		}
		$result=array();
		$result['total']=count($data);
		$result['rows']=$data;
		$this->myLogger->leave();
		return $result;
	}

	/**
	 * enumera las diversas mangas de la jornada indicando tipo y grado ( eg: jumping-GIII )
	 * @param $jornadaid
	 * @return array
	 */
	static function enumerateMangasByJornada($jornadaid) {
		if ($jornadaid<=0) { // no jornada id provided
			return array('total'=>0,'rows'=>array());
		}
		$dbobj=new DBObject("enumerateMangasByJornada");
		$dbobj->myLogger->enter();
		$jornada=$dbobj->__getArray("Jornadas",$jornadaid);
		$prueba=$dbobj->__getArray("pruebas",$jornada['Prueba']);
		$mangas=$dbobj->__select("*","Mangas","(Jornada=$jornadaid)","","")['rows'];
		$heights=intval(Federations::getFederation( intval($prueba['RSCE']) )->get('Heights'));
		$rows=array();
		foreach($mangas as $manga) {
			// datos comunes a todos los resultados posibles de una misma manga
			$item=array();
			$item['Prueba']=$prueba['ID'];
			$item['Jornada']=$jornadaid;
			$item['Manga']=$manga['ID'];
			$item['TipoManga']=$manga['Tipo'];
			$item['Juez1']=$manga['Juez1'];
			$item['Juez2']=$manga['Juez2'];
			$mid=$manga['ID'];
			switch($manga['Recorrido']){
				case 0: // recorridos separados
					$l	=array_merge( array('ID'=>$mid.',0', 'Mode'=>0,'Nombre'=>Mangas::$tipo_manga[$manga['Tipo']][1]." - ".Mangas::$manga_modes[0][0]),$item);
					array_push($rows,$l);
					$m	=array_merge( array('ID'=>$mid.',1','Mode'=>1,'Nombre'=>Mangas::$tipo_manga[$manga['Tipo']][1]." - ".Mangas::$manga_modes[1][0]),$item);
					array_push($rows,$m);
					$s	=array_merge( array('ID'=>$mid.',2','Mode'=>2,'Nombre'=>Mangas::$tipo_manga[$manga['Tipo']][1]." - ".Mangas::$manga_modes[2][0]),$item);
					array_push($rows,$s);
					if($heights==4) {
						$t=array_merge( array('ID'=>$mid.',5','Mode'=>5,'Nombre'=>Mangas::$tipo_manga[$manga['Tipo']][1]." - ".Mangas::$manga_modes[5][0]),$item);
						array_push($rows,$t);
					}
					break;
				case 1: // recorridos mixto
					if ($heights==3){
						$l	=array_merge( array('ID'=>$mid.',0','Mode'=>0,'Nombre'=>Mangas::$tipo_manga[$manga['Tipo']][1]." - ".Mangas::$manga_modes[0][0]),$item);
						array_push($rows,$l);
						$ms	=array_merge( array('ID'=>$mid.',3','Mode'=>3,'Nombre'=>Mangas::$tipo_manga[$manga['Tipo']][1]." - ".Mangas::$manga_modes[3][0]),$item);
						array_push($rows,$ms);
					} else {
						$lm	=array_merge( array('ID'=>$mid.',6','Mode'=>6,'Nombre'=>Mangas::$tipo_manga[$manga['Tipo']][1]." - ".Mangas::$manga_modes[6][0]),$item);
						array_push($rows,$lm);
						$st	=array_merge( array('ID'=>$mid.',7','Mode'=>7,'Nombre'=>Mangas::$tipo_manga[$manga['Tipo']][1]." - ".Mangas::$manga_modes[7][0]),$item);
						array_push($rows,$st);
					}
					break;
				case 2: // recorridos conjuntos
					if ($heights==3){
						$lms =array_merge( array('ID'=>$mid.',4','Mode'=>4,'Nombre'=>Mangas::$tipo_manga[$manga['Tipo']][1]." - ".Mangas::$manga_modes[4][0]),$item);
						array_push($rows,$lms);
					} else {
						$lmst=array_merge( array('ID'=>$mid.',8','Mode'=>8,'Nombre'=>Mangas::$tipo_manga[$manga['Tipo']][1]." - ".Mangas::$manga_modes[8][0]),$item);
						array_push($rows,$lmst);
					}
					break;
			}
		} /* foreach */
		$result=array('total'=>count($rows),'rows'=>$rows);
		return $result;
	}

	// version extendida de Jornadas::roundByJornada() en la que se genera una entrada por cada recorrido de cada categoria
	/*inner functions */
	static function __searchManga($tipo,$mangas) {
		foreach($mangas as $manga) {
			if ($manga['Tipo']==$tipo) return $manga;
		}
		return null;
	}

	static function __composeArray($p,$j,$t,$r,$m,$m1,$m2) {
		return array(
			'Prueba'=>$p,
			'Jornada'=>$j,
			'Rondas'=> Jornadas::$tipo_ronda[$t][0],
			'Nombre'=> Jornadas::$tipo_ronda[$t][1]." - ".Mangas::$manga_modes[$m][0],
			'Recorrido'=>$r,
			'Mode'=>$m,
			'Categoria'=>Mangas::$manga_modes[$m][1], // list of affected categories
			'Manga1'=>$m1['ID'],
			'Manga2'=>($m2!==null)?$m2['ID']:0,
			'NombreManga1'=>Mangas::$tipo_manga[$m1['Tipo']][1],
			'NombreManga2'=>($m2!==null)?Mangas::$tipo_manga[$m2['Tipo']][1]:''
		);
	}

	static function __compose(&$data,$prueba,$jornadaid,$tiporonda,$m1,$m2){
		$heights=intval(Federations::getFederation( intval($prueba['RSCE']) )->get('Heights'));
		switch(intval($m1['Recorrido'])){ // should be the same than $m2['Recorrido']
			case 0: // separado
				array_push($data,Jornadas::__composeArray($prueba['ID'],$jornadaid,$tiporonda,$m1['Recorrido'],0,$m1,$m2)); // large
				array_push($data,Jornadas::__composeArray($prueba['ID'],$jornadaid,$tiporonda,$m1['Recorrido'],1,$m1,$m2)); // medium
				array_push($data,Jornadas::__composeArray($prueba['ID'],$jornadaid,$tiporonda,$m1['Recorrido'],2,$m1,$m2)); // small
				if($heights==4) {
					array_push($data,Jornadas::__composeArray($prueba['ID'],$jornadaid,$tiporonda,$m1['Recorrido'],5,$m1,$m2)); // tiny
				}
				break;
			case 1: // mixto
				if($heights==3) {
					array_push($data,Jornadas::__composeArray($prueba['ID'],$jornadaid,$tiporonda,$m1['Recorrido'],0,$m1,$m2)); // large
					array_push($data,Jornadas::__composeArray($prueba['ID'],$jornadaid,$tiporonda,$m1['Recorrido'],3,$m1,$m2)); // m+s
				} else {
					array_push($data,Jornadas::__composeArray($prueba['ID'],$jornadaid,$tiporonda,$m1['Recorrido'],6,$m1,$m2)); // l+m
					array_push($data,Jornadas::__composeArray($prueba['ID'],$jornadaid,$tiporonda,$m1['Recorrido'],7,$m1,$m2)); // s+t
				}
				break;
			case 2: // conjunto
				if($heights==3) {
					array_push($data,Jornadas::__composeArray($prueba['ID'],$jornadaid,$tiporonda,$m1['Recorrido'],4,$m1,$m2)); // l+m+s
				} else {
					array_push($data,Jornadas::__composeArray($prueba['ID'],$jornadaid,$tiporonda,$m1['Recorrido'],8,$m1,$m2)); // l+m+s+t
				}
				break;
		}
	}

	/**
	 * enumera las diversas rondas (Pre-agility, Grado I, Grado II, etc de la jornada ) con sus mangas asociadas
	 * @param $jornadaid
	 * @return array|null
	 */
	static function enumerateRondasByJornada($jornadaid) {
		if ($jornadaid<=0) { // no jornada id provided
			return array('total'=>0,'rows'=>array());
		}
		$dbobj=new DBObject("enumerateRondasByJornada");
		$dbobj->myLogger->enter();
		$jornada=$dbobj->__getArray("Jornadas",$jornadaid);
		$prueba=$dbobj->__getArray("pruebas",$jornada['Prueba']);
		$mangas=$dbobj->__select("*","Mangas","(Jornada=$jornadaid)","TIPO ASC","")['rows'];
		$data=array();
		if ($jornada['PreAgility2']!=0) {
			// $dbobj->myLogger->trace("Procesando mangas de preagility-2");
			/* Pre-Agility siempre tiene recorrido comun para todas las categorias */
			$m1=Jornadas::__searchManga(1,$mangas); // PA-1
			$m2=Jornadas::__searchManga(2,$mangas); // PA-2
			Jornadas::__compose($data, $prueba, $jornadaid, 2, $m1, $m2);
		} else if ($jornada['PreAgility']!=0) {
			// $dbobj->myLogger->trace("Procesando mangas de preagility-1");
			/* Pre-Agility siempre tiene recorrido comun para todas las categorias */
			$m1=Jornadas::__searchManga(1,$mangas); // PA-1
			Jornadas::__compose($data, $prueba, $jornadaid, 1, $m1, null);
		}
		if ($jornada['Grado1']!=0) {  // Jornadas::tiporonda=3
			$m1 = Jornadas::__searchManga(3, $mangas); // Agility 1 Grado I
			$m2 = Jornadas::__searchManga(4, $mangas); // Agility 2 Grado I
			Jornadas::__compose($data, $prueba, $jornadaid, 3, $m1, $m2);
		}
		if ($jornada['Grado2']!=0) {  // Jornadas::tiporonda=4
			$m1 = Jornadas::__searchManga(5, $mangas); // Agility Grado II
			$m2 = Jornadas::__searchManga(10, $mangas); // Jumping Grado II
			Jornadas::__compose($data, $prueba, $jornadaid, 4, $m1, $m2);
		}
		if ($jornada['Grado3']!=0) { // Jornadas::tiporonda=5
			$m1 = Jornadas::__searchManga(6, $mangas); // Agility Grado III
			$m2 = Jornadas::__searchManga(11, $mangas); // Jumping Grado III
			Jornadas::__compose($data, $prueba, $jornadaid, 5, $m1, $m2);
		}
		if ($jornada['Open']!=0) { // Jornadas::tiporonda=6
			$m1 = Jornadas::__searchManga(7, $mangas); // Agility Open
			$m2 = Jornadas::__searchManga(12, $mangas); // Jumping Open
			Jornadas::__compose($data, $prueba, $jornadaid, 6, $m1, $m2);
		}
		if ($jornada['Equipos3']!=0) { // Jornadas::tiporonda=7
			$m1 = Jornadas::__searchManga(8, $mangas); // Agility Equipos3
			$m2 = Jornadas::__searchManga(13, $mangas); // Jumping Equipos3
			Jornadas::__compose($data, $prueba, $jornadaid, 7, $m1, $m2);
		}
		if ($jornada['Equipos4']!=0) { // Jornadas::tiporonda=8
			$m1 = Jornadas::__searchManga(9, $mangas); // Agility Equipos3
			$m2 = Jornadas::__searchManga(14, $mangas); // Jumping Equipos3
			Jornadas::__compose($data, $prueba, $jornadaid, 8, $m1, $m2);
		}
		if ($jornada['KO']!=0) {
			// $dbobj->myLogger->trace("Procesando mangas K.O.");
			// TODO: write
			return null;
		}
		if ($jornada['Especial']!=0) { // Jornadas::tiporonda=10
			$m1=Jornadas::__searchManga(16,$mangas); // Manga especial a una vuelta
			Jornadas::__compose($data,$prueba,$jornadaid,10,$m1,null);
		}
		// TODO: evaluate conjuntas Grado II y III
		$result=array('total'=>count($data),'rows'=>$data);
		return $result;
	}

	/**
	 * @param {mixed} $jobj JornadaID or JornadaObject as returned by _getObject() / _getArray()
	 * @return bool true or false
	 */
	static function hasGrades($jobj) {
		if (is_numeric($jobj)) {
			$obj=new Jornadas("hasGrades",0); // dummy prueba id
			$jobj=$obj->selectByID($jobj);
		}
		$flag=true;
		if (is_object($jobj)) {
			if (intval($jobj->Open)!=0) $flag=false;
			if (intval($jobj->Equipos3)!=0) $flag=false;
			if (intval($jobj->Equipos4)!=0) $flag=false;
			if (intval($jobj->KO)!=0) $flag=false;
		}
		if (is_array($jobj)) {
			if (intval($jobj['Open'])!=0) $flag=false;
			if (intval($jobj['Equipos3'])!=0) $flag=false;
			if (intval($jobj['Equipos4'])!=0) $flag=false;
			if (intval($jobj['KO'])!=0) $flag=false;

		}
		return $flag;
	}
}
?>