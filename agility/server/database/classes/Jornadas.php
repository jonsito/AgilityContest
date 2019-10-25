<?php
/*
Jornadas.php

Copyright  2013-2019 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__."/../../auth/AuthManager.php");
require_once("Mangas.php");
require_once("Tandas.php");

class Jornadas extends DBObject {

	protected $prueba; // {integer} id de prueba
    protected $pruebaobj; // {object} datos de la prueba
    protected $federation; // {object} datos de la federacion
	protected $jueces; // cache para la funcion fetchJuez()
	
	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {integer} $prueba Prueba ID for these jornadas
	 * @throws Exception if cannot contact database
	 */
	function __construct($file,$prueba) {
		parent::__construct($file);
		if ($this->prueba<0){
			$this->errormsg="$file::construct() invalid prueba ID";
			throw new Exception($this->errormsg);
		}
        $this->prueba= ($prueba==0)?1:$prueba; // make sure there is a valid contest ID
		$this->pruebaobj=$this->__getObject("pruebas",$this->prueba);
		$this->myLogger->trace("Prueba: ".json_encode($this->pruebaobj));
		$this->federation=Federations::getFederation($this->pruebaobj->RSCE);
        $this->myLogger->trace("Federation: ".json_encode($this->federation));
		$this->jueces=array( "1" => "-- Sin asignar --");
	}

    /**
     * Search for duplicate name in jornadas
     * This should be solved marking jornadas(Prueba,Nombre) unique not null, but to maintain compatibility
     *@param {string} $name New Jornada Name
     *@param {$id} Jornada ID being updated
     *@return 0: no duplicates else number of dups
     */
    function checkForDuplicates($name,$id) {
        $name=$this->conn->real_escape_string($name); // name is unscaped ( for prepared stmt )
        $res=$this->__selectObject(
        /* select */ "count(*) AS Items",
            /* from */   "jornadas",
            /* where */  "(Prueba={$this->prueba}) AND (Nombre!='-- Sin asignar --') AND (Nombre='{$name}') AND (ID!={$id})"
        );
        return $res->Items;
    }

	/*****
	 * No insert required: a set of 8 journeys are created on each every new prueba 
	 * update, delete, select (by) functions
	 */
	
	/**
	 * Update journey data
	 * @param {integer} $jornadaid
     * @param {object} $am AuthManager Object
	 * @return string
	 */
	function update($jornadaid,$am) {
		$this->myLogger->enter();
		// if prueba or jornada are closed refuse to upate
		if ($jornadaid<=0) return $this->error("Invalid jornada ID");

        // iniciamos los valores, chequeando su existencia
        $prueba = $this->prueba;
        $nombre = http_request("Nombre","s",null,false); // Name or comment for jornada

        // make sure that no existing entry with same name
        $dups=$this->checkForDuplicates($nombre,$jornadaid);
        if ($dups!=0) {
            return $this->error(_("There is already a journey with provided name"));
        }
        // read remaining parameters from request
        $fecha = str_replace("/","-",http_request("Fecha","s","",false)); // mysql requires format YYYY-MM-DD
        $hora = http_request("Hora","s","",false);
        $grado1 = http_request("Grado1","i",0);
        $grado2 = http_request("Grado2","i",0);
        $grado3 = http_request("Grado3","i",0);
        $open = http_request("Open","i",0);
        $equipos3 = http_request("Equipos3","i",0);
        $equipos4 = http_request("Equipos4","i",0);
        $preagility = http_request("PreAgility","i",0);
        $junior = http_request("Junior","i",0);
        $senior = http_request("Senior","i",0);
        $ko = http_request("KO","i",0);
        $games = http_request("Games","i",0);
        $especial = http_request("Especial","i",0);
        $observaciones = http_request("Observaciones","s","(sin especificar)",false);
        $cerrada = http_request("Cerrada","i",0);
        $slaveof = http_request("SlaveOf","i",0);
        $tipo_competicion = http_request("Tipo_Competicion","i",0);
        $id= $jornadaid; // prepared statements cannot handle function parameters as bind variables
        $this->myLogger->info("ID: $id Prueba: $prueba Nombre: $nombre Fecha: $fecha Hora: $hora");
        // check permissions
        if ( ($slaveof!=0) && (! $am->allowed(ENABLE_SPECIAL)) ) {
            $this->myLogger->notice("Jornada::update() Current license does not allow Subordinate journeys");
            $slaveof=0; // ignore, but allow to continue as un-subordinate journey
        }
        if ( ($games!=0) && (! $am->allowed(ENABLE_SPECIAL)) ) {
            return $this->error("Jornada::update() Current license does not allow Games/Wao journeys");
        }
        if ( ($ko!=0) && (! $am->allowed(ENABLE_KO)) ) {
            return $this->error("Jornada::update() Current license does not allow K.O journeys");
        }
        if ( ( ($equipos3!=0) || ($equipos4!=0) )&& (! $am->allowed(ENABLE_TEAMS)) ) {
            return $this->error("Jornada::update() Current license does not allow Team journeys");
        }
		// componemos un prepared statement
		$sql ="UPDATE jornadas
				SET Prueba=?, Nombre=?, Fecha=?, Hora=?, SlaveOf=?, Tipo_Competicion=?,Grado1=?, Grado2=?, Grado3=?, Junior=?,
					Senior=?, Open=?, Equipos3=?, Equipos4=?, PreAgility=?, KO=?, Games=?,Especial=?, Observaciones=?, Cerrada=?
				WHERE ( ID=? );";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error); 
		$res=$stmt->bind_param('isssiiiiiiiiiiiiiisii',
				$prueba,$nombre,$fecha,$hora,$slaveof,$tipo_competicion,$grado1,$grado2,$grado3,$junior,$senior,$open,$equipos3,$equipos4,$preagility,$ko,$games,$especial,$observaciones,$cerrada,$id);
		if (!$res) return $this->error($stmt->error);

		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error);
		$stmt->close();
		if (!$cerrada) {
			$mangas =new Mangas("jornadaFunctions",$id);
			$mangas->prepareMangas($id,$grado1,$grado2,$grado3,$junior,$senior,$open,$equipos3,$equipos4,$preagility,$ko,$games,$especial,$observaciones);
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
		$res=$this->__delete("equipos","( Jornada = $jornadaid )");
		if (!$res) return $this->error($this->conn->error);
		// y borramos la propia jornada
		$res= $this->__delete("jornadas","( ID = $jornadaid )");
		if (!$res) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	} 
	
	/**
	 * Close jornada with provided ID
	 * @param {integer} jornada name primary key
     * @param {boolean} closeflag 0:open 1:close
	 * @return "" on success ; otherwise null
	 */
	function close($jornadaid,$closeflag=1) {
		$this->myLogger->enter();
		if ($jornadaid<=0) return $this->error("Invalid Jornada ID");
		// marcamos la jornada con ID=$jornadaid como cerrada
		$res= $this->query("UPDATE jornadas SET Cerrada=$closeflag WHERE ( ID=$jornadaid ) ;");
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
		$obj=$this->__getObject("jornadas",$id);
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
				/* FROM */ "jornadas",
				/* WHERE */ "( Prueba = ".$this->prueba." )",
				/* ORDER BY */ "Numero ASC",
				/* LIMIT */ ""
		);
		// return composed array
		$this->myLogger->leave();
		return $result;
	}

    /**
     * Enumerate list of declared journeys in current contest that can be used as parents of provided journey
     */
	function getAvailableParents($id) {
        $this->myLogger->enter();
        // retrieve result from parent __select() call
        $items= $this->__select(
        /* SELECT */ "*",
            /* FROM */ "jornadas",
            /* WHERE */ "( Prueba = ".$this->prueba." )" ,
            /* ORDER BY */ "Numero ASC",
            /* LIMIT */ ""
        );
        // parse received data
        $list=array();
        // add null journey ( no subordinate )
        $list[] = array('ID' => 0, 'Nombre' => _('Independent Journey'));
        foreach($items['rows'] as $j) {
            // skip undeclared journeys
            if ($j['Nombre']=="-- Sin asignar --") continue;
            // skip mySelf
            if ($j['ID']==$id) continue;
            // check for recursiveness. At this moment, only one level deep
            if ($j['SlaveOf']==$id) continue;
            // everything ok. add to result
            $list[]=array('ID' => $j['ID'], 'Nombre' => $j['Nombre']);
        }
        // return composed array ( in json "combobox" format ( just array, not total/rows )
        $this->myLogger->leave();
        return $list;
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
				/* FROM */ "jornadas",
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
	 * @param {object} $mangas lista de las mangas de esta jornada
	 * @param {integer} $tipo campo Tipo a buscar en la tabla de mangas
	 * @return {array} datos de la manga pedida ; null if not found
	 */
	private function fetchManga($mangas,$jornadaid,$tipo) {
		foreach ($mangas as $manga) {
			if ($manga["Tipo"]==$tipo) return $manga;
		}
		// this may fail in grade 1 with variable number of rows
		$this->myLogger->notice("Cannot locate Mangas of Tipo:$tipo in Jornada:$jornadaid");
		return null;
	}
	
	/** 
	 * cache para evaluar los jueces de la llamada roundsByJornada
	 * @param {int} $id
	 */
	private function fetchJuez($id) {
		if (! array_key_exists("$id",$this->jueces)) {
			$obj=$this->__getObject("jueces",$id);
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
        $j=$this->__getObject("jornadas",$id);
        if (intval($perms)!=0) $res=$am->allowed($perms); // check against user provided check access
        // else check against jornada-dependent access permissions
        else if (intval($j->Equipos3)!=0) $res=$am->allowed(ENABLE_TEAMS);
        else if (intval($j->Equipos4)!=0) $res=$am->allowed(ENABLE_TEAMS);
        else if (intval($j->KO)!=0) $res=$am->allowed(ENABLE_KO);
        else if (intval($j->Games)!=0) $res=$am->allowed(ENABLE_SPECIAL); // mangas multiples/games
        else $res=true;
        if (!$res) {
            // notice the img path is relative to current web page "/agility/console", not php current path
            $this->errormsg='<img src="../images/sad_dog.png" width="75" alt="sad dog" style="float:right;"/>
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
		$row=$this->__getObject("jornadas",$jornadaid);
		if (!is_object($row)) return $this->error("No Jornadas with ID=$jornadaid");
		$mangas=$this->__select("*","mangas","Jornada=$jornadaid","","");
		if (!is_array($mangas)) return $this->error("No Mangas with Jornada ID=$jornadaid");
		// retrieve result into an array
		$data=array();
        if ($row->Junior!=0) {
            $manga1= $this->fetchManga($mangas['rows'],$jornadaid,32); // Junior Manga 1
            $manga2= $this->fetchManga($mangas['rows'],$jornadaid,33); // Junior Manga 2
            array_push($data,array(
                "Rondas" => $this->federation->getTipoRondas()[16][0],
                "Nombre" => $this->federation->getTipoRondas()[16][1],
                "Manga1" => $manga1['ID'],
                "Manga2" => $manga2['ID'],
                "Manga3" => 0,"Manga4" => 0,"Manga5" => 0,"Manga6" => 0,"Manga7" => 0,"Manga8" => 0,
                "NombreManga1" => $this->federation->getTipoManga(32,3), // 'Junior Manga 1',
                "NombreManga2" => $this->federation->getTipoManga(33,3), // 'Junior Manga 2',
                "Recorrido1" => $manga1['Recorrido'],
                "Recorrido2" => $manga2['Recorrido'],
                "Juez11" => $this->fetchJuez($manga1['Juez1']),
                "Juez12" => $this->fetchJuez($manga1['Juez2']),
                "Juez21" => $this->fetchJuez($manga2['Juez1']),
                "Juez22" => $this->fetchJuez($manga2['Juez2'])
            ) );
        }
        if ($row->Senior!=0) {
            $manga1= $this->fetchManga($mangas['rows'],$jornadaid,34); // Senior Manga 1
            $manga2= $this->fetchManga($mangas['rows'],$jornadaid,35); // Senior Manga 2
            array_push($data,array(
                "Rondas" => $this->federation->getTipoRondas()[17][0],
                "Nombre" => $this->federation->getTipoRondas()[17][1],
                "Manga1" => $manga1['ID'],
                "Manga2" => $manga2['ID'],
                "Manga3" => 0,"Manga4" => 0,"Manga5" => 0,"Manga6" => 0,"Manga7" => 0,"Manga8" => 0,
                "NombreManga1" => $this->federation->getTipoManga(34,3), // 'Senior Manga 1',
                "NombreManga2" => $this->federation->getTipoManga(35,3), // 'Senior Manga 2',
                "Recorrido1" => $manga1['Recorrido'],
                "Recorrido2" => $manga2['Recorrido'],
                "Juez11" => $this->fetchJuez($manga1['Juez1']),
                "Juez12" => $this->fetchJuez($manga1['Juez2']),
                "Juez21" => $this->fetchJuez($manga2['Juez1']),
                "Juez22" => $this->fetchJuez($manga2['Juez2'])
            ) );
        }
		if ($row->Grado1==1) { // g1 double round (1 for compatibility)
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,3); // 'Agility-1 GI'
            $manga2= $this->fetchManga($mangas['rows'],$jornadaid,4); // 'Agility-2 GI'
			array_push($data,array( 
				"Rondas" => $this->federation->getTipoRondas()[3][0],
				"Nombre" => $this->federation->getTipoRondas()[3][1],
				"Manga1" => $manga1['ID'],
                "Manga2" => $manga2['ID'],
                "Manga3" => 0,"Manga4" => 0,"Manga5" => 0,"Manga6" => 0,"Manga7" => 0,"Manga8" => 0,
				"NombreManga1" => $this->federation->getTipoManga(3,3), // 'Agility-1 GI',
				"NombreManga2" => $this->federation->getTipoManga(4,3), // 'Agility-2 GI',
				"Recorrido1" => $manga1['Recorrido'],
                "Recorrido2" => $manga2['Recorrido'],
				"Juez11" => $this->fetchJuez($manga1['Juez1']),
                "Juez12" => $this->fetchJuez($manga1['Juez2']),
                "Juez21" => $this->fetchJuez($manga2['Juez1']), // --sin asignar--
                "Juez22" => $this->fetchJuez($manga2['Juez2']),
            ) );
        }
        if ($row->Grado1==2) { // GI single round ( 2 instead 1 for compatibility )
            $manga1= $this->fetchManga($mangas['rows'],$jornadaid,3); // 'Agility-1 GI'
            array_push($data,array(
                "Rondas" => $this->federation->getTipoRondas()[3][0],
                "Nombre" => $this->federation->getTipoRondas()[3][1],
                "Manga1" => $manga1['ID'],
                "Manga2" => 0,
                "Manga3" => 0,"Manga4" => 0,"Manga5" => 0,"Manga6" => 0,"Manga7" => 0,"Manga8" => 0,
                "NombreManga1" => $this->federation->getTipoManga(3,3), // 'Agility-1 GI',
                "NombreManga2" => '',
                "Recorrido1" => $manga1['Recorrido'],
                "Recorrido2" => -1,
                "Juez11" => $this->fetchJuez($manga1['Juez1']),
                "Juez12" => $this->fetchJuez($manga1['Juez2']),
                "Juez21" => $this->fetchJuez(1), // --sin asignar--
                "Juez22" => $this->fetchJuez(1)
            ) );
        }
        if ($row->Grado1==3) { // g1 triple round
            $manga1= $this->fetchManga($mangas['rows'],$jornadaid,3); // 'Agility-1 GI'
            $manga2= $this->fetchManga($mangas['rows'],$jornadaid,4); // 'Agility-2 GI'
            $manga3= $this->fetchManga($mangas['rows'],$jornadaid,17); // 'Agility-3 GI'
            array_push($data,array(
                "Rondas" => $this->federation->getTipoRondas()[3][0],
                "Nombre" => $this->federation->getTipoRondas()[3][1],
                "Manga1" => $manga1['ID'],
                "Manga2" => $manga2['ID'],
                "Manga3" => $manga3['ID'],
                "Manga4" => 0,"Manga5" => 0,"Manga6" => 0,"Manga7" => 0,"Manga8" => 0,
                "NombreManga1" => $this->federation->getTipoManga(3,3), // 'Agility-1 GI',
                "NombreManga2" => $this->federation->getTipoManga(4,3), // 'Agility-2 GI',
                "NombreManga3" => $this->federation->getTipoManga(17,3), // 'Agility-3 GI',
                "Recorrido1" => $manga1['Recorrido'],
                "Recorrido2" => $manga2['Recorrido'],
                "Recorrido3" => $manga3['Recorrido'],
                "Juez11" => $this->fetchJuez($manga1['Juez1']),
                "Juez12" => $this->fetchJuez($manga1['Juez2']),
                "Juez21" => $this->fetchJuez($manga2['Juez1']),
                "Juez22" => $this->fetchJuez($manga2['Juez2']),
                "Juez31" => $this->fetchJuez($manga3['Juez1']),
                "Juez32" => $this->fetchJuez($manga3['Juez2'])
            ) );
        }
		if ($row->Grado2!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,5); // 'Agility GII'
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,10); // 'Jumping GII'
			array_push($data,array( 
				"Rondas" => $this->federation->getTipoRondas()[4][0],
				"Nombre" => $this->federation->getTipoRondas()[4][1],
				"Manga1" => $manga1['ID'],
				"Manga2" => $manga2['ID'],
                "Manga3" => 0,"Manga4" => 0,"Manga5" => 0,"Manga6" => 0,"Manga7" => 0,"Manga8" => 0,
				"NombreManga1" => $this->federation->getTipoManga(5,3), // 'Agility GII',
				"NombreManga2" => $this->federation->getTipoManga(10,3), // 'Jumping GII',
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
				"Rondas" => $this->federation->getTipoRondas()[5][0],
				"Nombre" => $this->federation->getTipoRondas()[5][1],
				"Manga1" => $manga1['ID'],
				"Manga2" => $manga2['ID'],
                "Manga3" => 0,"Manga4" => 0,"Manga5" => 0,"Manga6" => 0,"Manga7" => 0,"Manga8" => 0,
				"NombreManga1" => $this->federation->getTipoManga(6,3), // 'Agility GIII',
				"NombreManga2" => $this->federation->getTipoManga(11,3), // 'Jumping GIII',
				"Recorrido1" => $manga1['Recorrido'],
				"Recorrido2" => $manga2['Recorrido'],
				"Juez11" => $this->fetchJuez($manga1['Juez1']),
				"Juez12" => $this->fetchJuez($manga1['Juez2']),
				"Juez21" => $this->fetchJuez($manga2['Juez1']),
				"Juez22" => $this->fetchJuez($manga2['Juez2'])
			) );
		}
		if ($row->Open!=0) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,7); // 'Agility Individual'
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,12); // 'Jumping Individual'
            array_push($data,array(
                "Rondas" => $this->federation->getTipoRondas()[6][0],
                "Nombre" => $this->federation->getTipoRondas()[6][1],
                "Manga1" => $manga1['ID'],
                "Manga2" => $manga2['ID'],
                "Manga3" => 0,"Manga4" => 0,"Manga5" => 0,"Manga6" => 0,"Manga7" => 0,"Manga8" => 0,
                "NombreManga1" => $this->federation->getTipoManga(7,3), // 'Agility',
                "NombreManga2" => $this->federation->getTipoManga(12,3), // 'Jumping',
                "Recorrido1" => $manga1['Recorrido'],
                "Recorrido2" => $manga2['Recorrido'],
                "Juez11" => $this->fetchJuez($manga1['Juez1']),
                "Juez12" => $this->fetchJuez($manga1['Juez2']),
                "Juez21" => $this->fetchJuez($manga2['Juez1']),
                "Juez22" => $this->fetchJuez($manga2['Juez2'])
            ) );
		}
		if ($row->PreAgility==1) {
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,1); // 'Pre-Agility (1 manga)'
			$manga2= null;
            array_push($data,array(
                "Rondas" => $this->federation->getTipoRondas()[1][0],
                "Nombre" => $this->federation->getTipoRondas()[1][1],
                "Manga1" => $manga1['ID'],
                "Manga2" => 0,"Manga3" => 0,"Manga4" => 0,"Manga5" => 0,"Manga6" => 0,"Manga7" => 0,"Manga8" => 0,
                "NombreManga1" => $this->federation->getTipoManga(1,3), // 'Pre-Agility 1',
                "NombreManga2" => '',
                "Recorrido1" => $manga1['Recorrido'],
                "Recorrido2" => -1,
                "Juez11" => $this->fetchJuez($manga1['Juez1']),
                "Juez12" => $this->fetchJuez($manga1['Juez2']),
                "Juez21" => $this->fetchJuez(1),
                "Juez22" => $this->fetchJuez(1)
            ) );
		}
        if ($row->PreAgility==2) {
            $manga1= $this->fetchManga($mangas['rows'],$jornadaid,1); // 'Pre-Agility (2 mangas)
            $manga2= $this->fetchManga($mangas['rows'],$jornadaid,2); // 'Pre-Agility (2 mangas)
            array_push($data,array(
                "Rondas" => $this->federation->getTipoRondas()[2][0],
                "Nombre" => $this->federation->getTipoRondas()[2][1],
                "Manga1" => $manga1['ID'],
                "Manga2" => $manga2['ID'],
                "Manga3" => 0,"Manga4" => 0,"Manga5" => 0,"Manga6" => 0,"Manga7" => 0,"Manga8" => 0,
                "NombreManga1" => $this->federation->getTipoManga(1,3), // 'Pre-Agility 1',
                "NombreManga2" => $this->federation->getTipoManga(2,3), // 'Pre-Agility 2',
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
                "Rondas" => $this->federation->getTipoRondas()[$idx][0],
                "Nombre" => $this->federation->getTipoRondas()[$idx][1],
                "Manga1" => $manga1['ID'],
                "Manga2" => $manga2['ID'],
                "Manga3" => 0,"Manga4" => 0,"Manga5" => 0,"Manga6" => 0,"Manga7" => 0,"Manga8" => 0,
                "NombreManga1" => $this->federation->getTipoManga(8,3), // 'Agility Eq.',
                "NombreManga2" => $this->federation->getTipoManga(13,3), // 'Jumping Eq.',
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
				case 2: /* 2 combined	*/ $idx=13; break;
				case 3: /* 3 combined  */ $idx=14; break;
				case 4: /* 4 combined  */ $idx=8; break;
				default: $idx=8; break;
			}
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,9); // 'Agility Equipos (conjunta)'
			$manga2= $this->fetchManga($mangas['rows'],$jornadaid,14); // 'Jumping Equipos (conjunta)'
            array_push($data,array(
                "Rondas" => $this->federation->getTipoRondas()[$idx][0],
                "Nombre" => $this->federation->getTipoRondas()[$idx][1],
                "Manga1" => $manga1['ID'],
                "Manga2" => $manga2['ID'],
                "Manga3" => 0,"Manga4" => 0,"Manga5" => 0,"Manga6" => 0,"Manga7" => 0,"Manga8" => 0,
                "NombreManga1" => $this->federation->getTipoManga(9,3), // 'Agility Eq.',
                "NombreManga2" => $this->federation->getTipoManga(14,3), // 'Jumping Eq.',
                "Recorrido1" => $manga1['Recorrido'],
                "Recorrido2" => $manga2['Recorrido'],
                "Juez11" => $this->fetchJuez($manga1['Juez1']),
                "Juez12" => $this->fetchJuez($manga1['Juez2']),
                "Juez21" => $this->fetchJuez($manga2['Juez1']),
                "Juez22" => $this->fetchJuez($manga2['Juez2'])
            ) );
		}
		if ($row->KO!=0) {
            $manga1= $this->fetchManga($mangas['rows'],$jornadaid,15); // Ronda K.O. 1
            $manga2= $this->fetchManga($mangas['rows'],$jornadaid,18); // Ronda K.O. 2
            $manga3= $this->fetchManga($mangas['rows'],$jornadaid,19); // Ronda K.O. 3
            $manga4= $this->fetchManga($mangas['rows'],$jornadaid,20); // Ronda K.O. 4
            $manga5= $this->fetchManga($mangas['rows'],$jornadaid,21); // Ronda K.O. 5
            $manga6= $this->fetchManga($mangas['rows'],$jornadaid,22); // Ronda K.O. 6
            $manga7= $this->fetchManga($mangas['rows'],$jornadaid,23); // Ronda K.O. 7
            $manga8= $this->fetchManga($mangas['rows'],$jornadaid,24); // Ronda K.O. 8
            array_push($data,array(
                "Rondas" => $this->federation->getTipoRondas()[9][0],
                "Nombre" => $this->federation->getTipoRondas()[9][1],
                "Manga1" => $manga1['ID'],"Manga2" => $manga2['ID'],"Manga3" => $manga3['ID'],"Manga4" => $manga4['ID'],
                "Manga5" => $manga5['ID'],"Manga6" => $manga6['ID'],"Manga7" => $manga7['ID'],"Manga8" => $manga8['ID'],
                "NombreManga1" => $this->federation->getTipoManga(15,3), // 'Manga K.O. 1',
                "NombreManga2" => $this->federation->getTipoManga(18,3), // 'Manga K.O. 2',
                "NombreManga3" => $this->federation->getTipoManga(19,3), // 'Manga K.O. 3',
                "NombreManga4" => $this->federation->getTipoManga(20,3), // 'Manga K.O. 4',
                "NombreManga5" => $this->federation->getTipoManga(21,3), // 'Manga K.O. 5',
                "NombreManga6" => $this->federation->getTipoManga(22,3), // 'Manga K.O. 6',
                "NombreManga7" => $this->federation->getTipoManga(23,3), // 'Manga K.O. 7',
                "NombreManga8" => $this->federation->getTipoManga(24,3), // 'Manga K.O. 8',
                "Recorrido1" => $manga1['Recorrido'],
                "Recorrido2" => $manga2['Recorrido'],
                "Recorrido3" => $manga3['Recorrido'],
                "Recorrido4" => $manga4['Recorrido'],
                "Recorrido5" => $manga5['Recorrido'],
                "Recorrido6" => $manga6['Recorrido'],
                "Recorrido7" => $manga7['Recorrido'],
                "Recorrido8" => $manga8['Recorrido'],
                "Juez11" => $this->fetchJuez($manga1['Juez1']),
                "Juez12" => $this->fetchJuez($manga1['Juez2']),
                "Juez21" => $this->fetchJuez($manga2['Juez1']),
                "Juez22" => $this->fetchJuez($manga2['Juez2']),
                "Juez31" => $this->fetchJuez($manga3['Juez1']),
                "Juez32" => $this->fetchJuez($manga3['Juez2']),
                "Juez41" => $this->fetchJuez($manga4['Juez1']),
                "Juez42" => $this->fetchJuez($manga4['Juez2']),
                "Juez51" => $this->fetchJuez($manga5['Juez1']),
                "Juez52" => $this->fetchJuez($manga5['Juez2']),
                "Juez61" => $this->fetchJuez($manga6['Juez1']),
                "Juez62" => $this->fetchJuez($manga6['Juez2']),
                "Juez71" => $this->fetchJuez($manga7['Juez1']),
                "Juez72" => $this->fetchJuez($manga7['Juez2']),
                "Juez81" => $this->fetchJuez($manga8['Juez1']),
                "Juez82" => $this->fetchJuez($manga8['Juez2'])
            ) );
		}
        if ($row->Games!=0) {
            $tipo = intval($row->Tipo_Competicion);
            // depending of games journey type, need to select wich round to peek
            if ($tipo === 1) { // Penthatlon
                $manga1= $this->fetchManga($mangas['rows'],$jornadaid,25); // Agility A
                $manga2= $this->fetchManga($mangas['rows'],$jornadaid,26); // Agility B
                $manga3= $this->fetchManga($mangas['rows'],$jornadaid,27); // Jumpìng A
                $manga4= $this->fetchManga($mangas['rows'],$jornadaid,28); // Jumping B
                $manga5= $this->fetchManga($mangas['rows'],$jornadaid,31); // SpeedStakes
                $manga6= null;
                $manga7= null;
                $manga8= null;
                array_push($data,array(
                    "Rondas" => $this->federation->getTipoRondas()[15][0],
                    "Nombre" => $this->federation->getTipoRondas()[15][1],
                    "Manga1" => $manga1['ID'],"Manga2" => $manga2['ID'],
                    "Manga3" => $manga3['ID'],"Manga4" => $manga4['ID'],
                    "Manga5" => $manga5['ID'],"Manga6" => 0,"Manga7" => 0,"Manga8" => 0,
                    "NombreManga1" => $this->federation->getTipoManga(25,3), // Agility A
                    "NombreManga2" => $this->federation->getTipoManga(26,3), // Agility B
                    "NombreManga3" => $this->federation->getTipoManga(27,3), // Jumping A
                    "NombreManga4" => $this->federation->getTipoManga(28,3), // Jumping B
                    "NombreManga5" => $this->federation->getTipoManga(31,3), // SpeedStakes
                    "Recorrido1" => $manga1['Recorrido'],
                    "Recorrido2" => $manga2['Recorrido'],
                    "Recorrido3" => $manga3['Recorrido'],
                    "Recorrido4" => $manga4['Recorrido'],
                    "Recorrido5" => $manga5['Recorrido'],
                    "Juez11" => $this->fetchJuez($manga1['Juez1']),
                    "Juez12" => $this->fetchJuez($manga1['Juez2']),
                    "Juez21" => $this->fetchJuez($manga2['Juez1']),
                    "Juez22" => $this->fetchJuez($manga2['Juez2']),
                    "Juez31" => $this->fetchJuez($manga3['Juez1']),
                    "Juez32" => $this->fetchJuez($manga3['Juez2']),
                    "Juez41" => $this->fetchJuez($manga4['Juez1']),
                    "Juez42" => $this->fetchJuez($manga4['Juez2']),
                    "Juez51" => $this->fetchJuez($manga5['Juez1']),
                    "Juez52" => $this->fetchJuez($manga5['Juez2'])
                ) );
            } else if ($tipo === 2) { // Biathlon
                $manga1= $this->fetchManga($mangas['rows'],$jornadaid,25); // Agility A
                $manga2= $this->fetchManga($mangas['rows'],$jornadaid,26); // Agility B
                $manga3= $this->fetchManga($mangas['rows'],$jornadaid,27); // Jumpìng A
                $manga4= $this->fetchManga($mangas['rows'],$jornadaid,28); // Jumping B
                $manga5= null; $manga6= null; $manga7= null; $manga8= null;
                array_push($data,array(
                    "Rondas" => $this->federation->getTipoRondas()[15][0],
                    "Nombre" => $this->federation->getTipoRondas()[15][1],
                    "Manga1" => $manga1['ID'],"Manga2" => $manga2['ID'],
                    "Manga3" => $manga3['ID'],"Manga4" => $manga4['ID'],
                    "Manga5" => 0,"Manga6" => 0,"Manga7" => 0,"Manga8" => 0,
                    "NombreManga1" => $this->federation->getTipoManga(25,3), // Agility A
                    "NombreManga2" => $this->federation->getTipoManga(26,3), // Agility B
                    "NombreManga3" => $this->federation->getTipoManga(27,3), // Jumping A
                    "NombreManga4" => $this->federation->getTipoManga(28,3), // Jumping B
                    "Recorrido1" => $manga1['Recorrido'],
                    "Recorrido2" => $manga2['Recorrido'],
                    "Recorrido3" => $manga3['Recorrido'],
                    "Recorrido4" => $manga4['Recorrido'],
                    "Juez11" => $this->fetchJuez($manga1['Juez1']),
                    "Juez12" => $this->fetchJuez($manga1['Juez2']),
                    "Juez21" => $this->fetchJuez($manga2['Juez1']),
                    "Juez22" => $this->fetchJuez($manga2['Juez2']),
                    "Juez31" => $this->fetchJuez($manga3['Juez1']),
                    "Juez32" => $this->fetchJuez($manga3['Juez2']),
                    "Juez41" => $this->fetchJuez($manga4['Juez1']),
                    "Juez42" => $this->fetchJuez($manga4['Juez2'])
                ) );
            } else if ($tipo === 3) { // Games
                $manga1= $this->fetchManga($mangas['rows'],$jornadaid,29); // Snooker
                $manga2= $this->fetchManga($mangas['rows'],$jornadaid,30); // Gambler
                $manga3=null;$manga4=null;$manga5=null;$manga6=null;$manga7=null;$manga8=null;
                array_push($data,array(
                    "Rondas" => $this->federation->getTipoRondas()[15][0],
                    "Nombre" => $this->federation->getTipoRondas()[15][1],
                    "Manga1" => $manga1['ID'],"Manga2" => $manga2['ID'],"Manga3" => 0,"Manga4" => 0,
                    "Manga5" => 0,"Manga6" => 0,"Manga7" => 0,"Manga8" => 0,
                    "NombreManga1" => $this->federation->getTipoManga(29,3), // Snooker
                    "NombreManga2" => $this->federation->getTipoManga(30,3), // Gambler
                    "Recorrido1" => $manga1['Recorrido'],
                    "Recorrido2" => $manga2['Recorrido'],
                    "Juez11" => $this->fetchJuez($manga1['Juez1']),
                    "Juez12" => $this->fetchJuez($manga1['Juez2']),
                    "Juez21" => $this->fetchJuez($manga2['Juez1']),
                    "Juez22" => $this->fetchJuez($manga2['Juez2'])
                ) );
            } else {
                $this->myLogger->error("Invalid Tipo_competicion in games journey");
            }
        }
		if ($row->Especial!=0) {
			// TODO: $row->Special indicates number of rounds. Current and default is 1
			$manga1= $this->fetchManga($mangas['rows'],$jornadaid,16); // 'Manga especial'
            $manga2= null;$manga3= null;$manga4= null;$manga5= null;$manga6= null;$manga7= null;$manga8= null;
            array_push($data,array(
                "Rondas" => $this->federation->getTipoRondas()[10][0],
                "Nombre" => $this->federation->getTipoRondas()[10][1],
                "Manga1" => $manga1['ID'],
                "Manga2" => 0,"Manga3" => 0,"Manga4" => 0,"Manga5" => 0,"Manga6" => 0,"Manga7" => 0,"Manga8" => 0,
                "NombreManga1" => $this->federation->getTipoManga(16,3), // 'Manga Especial',
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
		$jornada=$dbobj->__getArray("jornadas",$jornadaid);
		$prueba=$dbobj->__getArray("pruebas",$jornada['Prueba']);
		$mangas=$dbobj->__select("*","mangas","(Jornada=$jornadaid)","","")['rows'];
		$rows=array();
		$mangasInfo=null;
		foreach($mangas as $manga) {
		    // en la primera manga obtenemos informacion del tipo de competicion
            if ($mangasInfo==null) $mangasInfo=Mangas::getMangaInfo($manga['ID']);
            // evaluamos las alturas en funcion de la manga
            $fed=$mangasInfo->Federation;
            $heights=$mangasInfo->Competition->getRoundHeights($manga);
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
					$l	=array_merge( array('ID'=>$mid.',0', 'Mode'=>0,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(0,0,$fed)),$item);
					array_push($rows,$l);
					$m	=array_merge( array('ID'=>$mid.',1','Mode'=>1,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(1,0,$fed)),$item);
					array_push($rows,$m);
					$s	=array_merge( array('ID'=>$mid.',2','Mode'=>2,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(2,0,$fed)),$item);
					array_push($rows,$s);
                    if($heights!=3) {
                        $t=array_merge( array('ID'=>$mid.',5','Mode'=>5,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(5,0,$fed)),$item);
                        array_push($rows,$t);
                    }
                    if($heights==5) {
                        $x=array_merge( array('ID'=>$mid.',9','Mode'=>9,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(9,0,$fed)),$item);
                        array_push($rows,$x);
                    }
					break;
				case 1: // 2 grupos (l+ms) (lm+st) (xl+mst)
					if ($heights==3){
						$l	=array_merge( array('ID'=>$mid.',0','Mode'=>0,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(0,0,$fed)),$item);
						array_push($rows,$l);
						$ms	=array_merge( array('ID'=>$mid.',3','Mode'=>3,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(3,0,$fed)),$item);
						array_push($rows,$ms);
					}
					if ($heights==4) {
						$lm	=array_merge( array('ID'=>$mid.',6','Mode'=>6,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(6,0,$fed)),$item);
						array_push($rows,$lm);
						$st	=array_merge( array('ID'=>$mid.',7','Mode'=>7,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(7,0,$fed)),$item);
						array_push($rows,$st);
					}
					if ($heights==5) {
                        $xl	=array_merge( array('ID'=>$mid.',10', 'Mode'=>10,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(10,0,$fed)),$item);
                        array_push($rows,$xl);
                        $mst	=array_merge( array('ID'=>$mid.',11','Mode'=>11,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(11,0,$fed)),$item);
                        array_push($rows,$mst);
                    }
					break;
				case 2: // recorridos conjuntos
					if ($heights==3){
						$lms =array_merge( array('ID'=>$mid.',4','Mode'=>4,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(4,0,$fed)),$item);
						array_push($rows,$lms);
					}
					if ($heights==4){
						$lmst=array_merge( array('ID'=>$mid.',8','Mode'=>8,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(8,0,$fed)),$item);
						array_push($rows,$lmst);
					}
					if ($heights==5) {
                        $xlmst=array_merge( array('ID'=>$mid.',12','Mode'=>12,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(12,0,$fed)),$item);
                        array_push($rows,$xlmst);
                    }
					break;
                case 3: // 3 grupos. implica $heights==5
                    $xl	=array_merge( array('ID'=>$mid.',10', 'Mode'=>10,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(10,0,$fed)),$item);
                    array_push($rows,$xl);
                    $m	=array_merge( array('ID'=>$mid.',1','Mode'=>1,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(1,0,$fed)),$item);
                    array_push($rows,$m);
                    $st	=array_merge( array('ID'=>$mid.',7','Mode'=>7,'Nombre'=>_(Mangas::getTipoManga($manga['Tipo'],1,$fed))." - ".Mangas::getMangaMode(7,0,$fed)),$item);
                    array_push($rows,$st);
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

	static function __composeArray($p,$j,$t,$r,$m,$m1,$m2=null,$m3=null,$m4=null,$m5=null,$m6=null,$m7=null,$m8=null) {
        $fed=Federations::getFederation( intval($p['RSCE']) );
		return array(
			'Prueba'=>$p['ID'],
			'Jornada'=>$j['ID'],
			'Rondas'=> $fed->getTipoRondas()[$t][0],
			'Nombre'=> $fed->getTipoRondas()[$t][1]." - ".Mangas::getMangaMode($m,0,$fed),
			'Recorrido'=>$r,
			'Mode'=>$m,
            'Categoria'=>Mangas::getMangaMode($m,1,$fed), // list of affected categories
			'Manga1'=>$m1['ID'],
            'Manga2'=>($m2!==null)?$m2['ID']:0,
            'Manga3'=>($m3!==null)?$m3['ID']:0,
            'Manga4'=>($m4!==null)?$m4['ID']:0,
            'Manga5'=>($m5!==null)?$m5['ID']:0,
            'Manga6'=>($m6!==null)?$m6['ID']:0,
            'Manga7'=>($m7!==null)?$m7['ID']:0,
            'Manga8'=>($m8!==null)?$m8['ID']:0,
			'NombreManga1'=>_(Mangas::getTipoManga($m1['Tipo'],1,$fed)),
            'NombreManga2'=>($m2!==null)?_(Mangas::getTipoManga($m2['Tipo'],1,$fed)):'',
            'NombreManga3'=>($m3!==null)?_(Mangas::getTipoManga($m3['Tipo'],1,$fed)):'',
            'NombreManga4'=>($m4!==null)?_(Mangas::getTipoManga($m4['Tipo'],1,$fed)):'',
            'NombreManga5'=>($m5!==null)?_(Mangas::getTipoManga($m5['Tipo'],1,$fed)):'',
            'NombreManga6'=>($m6!==null)?_(Mangas::getTipoManga($m6['Tipo'],1,$fed)):'',
            'NombreManga7'=>($m7!==null)?_(Mangas::getTipoManga($m7['Tipo'],1,$fed)):'',
            'NombreManga8'=>($m8!==null)?_(Mangas::getTipoManga($m8['Tipo'],1,$fed)):'',
            'Tipo1' => $m1['Tipo'],
            'Tipo2' => ($m2!==null)?$m2['Tipo']:0,
            'Tipo3' => ($m3!==null)?$m3['Tipo']:0,
            'Tipo4' => ($m4!==null)?$m4['Tipo']:0,
            'Tipo5' => ($m5!==null)?$m5['Tipo']:0,
            'Tipo6' => ($m6!==null)?$m6['Tipo']:0,
            'Tipo7' => ($m7!==null)?$m7['Tipo']:0,
            'Tipo8' => ($m8!==null)?$m8['Tipo']:0,
		);
	}

	static function __compose(&$data,$prueba,$jornada,$tiporonda,$m1,$m2=null,$m3=null,$m4=null,$m5=null,$m6=null,$m7=null,$m8=null){
	    $heights=Competitions::getHeights($prueba->ID,$jornada->ID,$m1['ID']);
		switch(intval($m1['Recorrido'])){ // should be the same than $m2['Recorrido']
			case 0: // separado
                // shared on all heights
				array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],0,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // large
				array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],1,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // medium
				array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],2,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // small
                if($heights!=3) {
                    array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],5,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // tiny
                }
                if($heights==5) {
                    array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],9,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // XtraLarge
                }
				break;
			case 1: // dos grupos
				if($heights==3) { // l+ms
					array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],0,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // large
					array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],3,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // m+s
				}
				if($heights==4) { // lm+st
					array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],6,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // l+m
					array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],7,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // s+t
				}
				if($heights==5) { // xl+mst
                    array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],10,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // x+l
                    array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],11,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // m+s+t
                }
				break;
			case 2: // conjunto
				if($heights==3) {
					array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],4,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // l+m+s
				}
				if ($heights==4) {
					array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],8,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // l+m+s+t
				}
				if ($heights==5) {
                    array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],12,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // x+l+m+s+t
                }
				break;
            case 3: // 3 grupos ( implica $heights==5 )
                array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],10,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // x+l
                array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],1,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // m
                array_push($data,Jornadas::__composeArray($prueba,$jornada,$tiporonda,$m1['Recorrido'],7,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8)); // s+t
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
		$jornada=$dbobj->__getArray("jornadas",$jornadaid);
		$prueba=$dbobj->__getArray("pruebas",$jornada['Prueba']);
		$mangas=$dbobj->__select("*","mangas","(Jornada=$jornadaid)","TIPO ASC","")['rows'];
		$data=array();
		if ($jornada['PreAgility']==2) { // pre-Agility 2 mangas
			/* Pre-Agility siempre tiene recorrido comun para todas las categorias */
			$m1=Jornadas::__searchManga(1,$mangas); // PA-1
			$m2=Jornadas::__searchManga(2,$mangas); // PA-2
			Jornadas::__compose($data, $prueba, $jornada, 2, $m1, $m2);
		}
		if ($jornada['PreAgility']==1) { // pre-Agility 1 manga
			/* Pre-Agility siempre tiene recorrido comun para todas las categorias */
			$m1=Jornadas::__searchManga(1,$mangas); // PA-1
			Jornadas::__compose($data, $prueba, $jornada, 1, $m1, null);
		}
		// remember that Junior includes Young and Children
        if ($jornada['Junior']!=0) {  // Jornadas::tiporonda=16
            $m1 = Jornadas::__searchManga(32, $mangas); // Junior Manga 1
            $m2 = Jornadas::__searchManga(33, $mangas); // Junior Manga 2
            Jornadas::__compose($data, $prueba, $jornada, 16, $m1, $m2);
        }
		if ($jornada['Grado1']!=0) {  // Jornadas::tiporonda=3 (0:no G1 1:2rounds 2:1round 3:3rounds )
            $a=intval($jornada['Grado1']);
            $m1=null; $m2=null; $m3=null;
            switch($a){
                case 3: // 3 rounds
                    $m3 = Jornadas::__searchManga(17, $mangas); // Agility 3 Grado I
                    // no break
                case 1: // 2 rounds  (notice value "1" due to historical reasons
                    $m2 = Jornadas::__searchManga(4, $mangas); // Agility 2 Grado I
                    // no break
                case 2: // 1 round
                    $m1 = Jornadas::__searchManga(3, $mangas); // Agility 1 Grado I
                    break;
                default: $dbobj->myLogger->error("Invalid value '{$a}' for Grado 1 field");
            }
			Jornadas::__compose($data, $prueba, $jornada, 3, $m1, $m2, $m3);
		}
		if ($jornada['Grado2']!=0) {  // Jornadas::tiporonda=4
			$m1 = Jornadas::__searchManga(5, $mangas); // Agility Grado II
			$m2 = Jornadas::__searchManga(10, $mangas); // Jumping Grado II
			Jornadas::__compose($data, $prueba, $jornada, 4, $m1, $m2);
		}
		if ($jornada['Grado3']!=0) { // Jornadas::tiporonda=5
			$m1 = Jornadas::__searchManga(6, $mangas); // Agility Grado III
			$m2 = Jornadas::__searchManga(11, $mangas); // Jumping Grado III
			Jornadas::__compose($data, $prueba, $jornada, 5, $m1, $m2);
		}
		if ($jornada['Open']!=0) { // Jornadas::tiporonda=6
			$m1 = Jornadas::__searchManga(7, $mangas); // Agility Individual
			$m2 = Jornadas::__searchManga(12, $mangas); // Jumping Individual
			Jornadas::__compose($data, $prueba, $jornada, 6, $m1, $m2);
		}
		if ($jornada['Equipos3']!=0) { // Jornadas::tiporonda=7
			$m1 = Jornadas::__searchManga(8, $mangas); // Agility Equipos3
			$m2 = Jornadas::__searchManga(13, $mangas); // Jumping Equipos3
			Jornadas::__compose($data, $prueba, $jornada, 7, $m1, $m2);
		}
		if ($jornada['Equipos4']!=0) { // Jornadas::tiporonda=8
			$m1 = Jornadas::__searchManga(9, $mangas); // Agility Equipos3
			$m2 = Jornadas::__searchManga(14, $mangas); // Jumping Equipos3
			Jornadas::__compose($data, $prueba, $jornada, 8, $m1, $m2);
		}
		if ($jornada['KO']!=0) { // Jornadas::tiporonda=9
            $m1 = Jornadas::__searchManga(15, $mangas); // KO manga 1
            $m2 = Jornadas::__searchManga(18, $mangas); // KO manga 2
            $m3 = Jornadas::__searchManga(19, $mangas); // KO manga 3
            $m4 = Jornadas::__searchManga(20, $mangas); // KO manga 4
            $m5 = Jornadas::__searchManga(21, $mangas); // KO manga 5
            $m6 = Jornadas::__searchManga(22, $mangas); // KO manga 6
            $m7 = Jornadas::__searchManga(23, $mangas); // KO manga 7
            $m8 = Jornadas::__searchManga(24, $mangas); // KO manga 8
            Jornadas::__compose($data, $prueba, $jornada, 9, $m1, $m2,$m3,$m4,$m5,$m6,$m7,$m8);
		}
		if ($jornada['Especial']!=0) { // Jornadas::tiporonda=10
			$m1=Jornadas::__searchManga(16,$mangas); // Manga especial a una vuelta
			Jornadas::__compose($data,$prueba,$jornada,10,$m1,null);
		}

        if ($jornada['Games']!=0) { // Jornadas::tiporonda=15
            $m1 = Jornadas::__searchManga(25, $mangas); // Agility A
            $m2 = Jornadas::__searchManga(26, $mangas); // Agility B
            $m3 = Jornadas::__searchManga(27, $mangas); // Jumping A
            $m4 = Jornadas::__searchManga(28, $mangas); // Jumping B
            $m5 = Jornadas::__searchManga(29, $mangas); // Snooker
            $m6 = Jornadas::__searchManga(30, $mangas); // Gambler
            $m7 = Jornadas::__searchManga(31, $mangas); // SpeedStakes
            Jornadas::__compose($data, $prueba, $jornada, 15, $m1, $m2,$m3,$m4,$m5,$m6,$m7);
        }
		// TODO: evaluate conjuntas Grado II y III
		$result=array('total'=>count($data),'rows'=>$data);
		return $result;
	}

    /**
     * ask for team journey
     * @param {mixed} $jobj JornadaID or JornadaObject as returned by _getObject() / _getArray()
     * @return bool true or false
     */
	static function hasTeams($jobj) {
        if (is_numeric($jobj)) {
            $obj=new DBObject("hasTeams");
            $jobj=$obj->__selectObject("*","jornadas","ID=$jobj");
        }
        $flag=false;
        if (is_object($jobj)) {
            if (intval($jobj->Equipos3)!=0) $flag=true;
            if (intval($jobj->Equipos4)!=0) $flag=true;
        }
        if (is_array($jobj)) {
            if (intval($jobj['Equipos3'])!=0) $flag=true;
            if (intval($jobj['Equipos4'])!=0) $flag=true;
        }
        return $flag;
    }

	/**
     * as for std (grade based) journey
	 * @param {mixed} $jobj JornadaID or JornadaObject as returned by _getObject() / _getArray()
	 * @return bool true or false
	 */
	static function hasGrades($jobj) {
		if (is_numeric($jobj)) {
		    $obj=new DBObject("hasGrades");
		    $jobj=$obj->__selectObject("*","jornadas","ID=$jobj");
		}
		$flag=true;
		if (is_object($jobj)) {
			if (intval($jobj->Open)!=0) $flag=false;
			if (intval($jobj->Equipos3)!=0) $flag=false;
			if (intval($jobj->Equipos4)!=0) $flag=false;
            if (intval($jobj->KO)!=0) $flag=false;
            if (intval($jobj->Games)!=0) $flag=false;
            if (intval($jobj->Junior)!=0) $flag=false; // en junior no hay grados... por ahora
		}
		if (is_array($jobj)) {
			if (intval($jobj['Open'])!=0) $flag=false;
			if (intval($jobj['Equipos3'])!=0) $flag=false;
			if (intval($jobj['Equipos4'])!=0) $flag=false;
            if (intval($jobj['KO'])!=0) $flag=false;
            if (intval($jobj['Games'])!=0) $flag=false;
            if (intval($jobj['Junior'])!=0) $flag=false;
		}
		return $flag;
	}

	/**
	 * Evalua en numero minimo y maximo de perros por equipo y jornada
	 * @param {mixed} $jobj objeto/array de tipo jornada
	 * @return {array} (mindogs,maxdogs)
	 */
	static function getTeamDogs($jornada) {
		$eq3=0;$eq4=0;
		if (is_object($jornada)) {
			$eq3=$jornada->Equipos3;
			$eq4=$jornada->Equipos4;
		}
		if (is_array($jornada)) {
			$eq3=$jornada['Equipos3'];
			$eq4=$jornada['Equipos4'];
		}
		switch ($eq3) {
			case 1: return array(3,4); // old style 3 mejores de cuatro
			case 2: return array(2,3); // 2 best of 3
			case 3: return array(3,4); // 3 best of 4
		}
		switch($eq4) {
			case 1: return array(4,4); // old style 4 conjunta
			case 2: return array(2,2); // 2 conjunta
			case 3: return array(3,3); // 3 conjunta
			case 4: return array(4,4); // 4 conjunta
		}
		// arriving here means no team journey or invalid team mode
		return array(1,1);
	}
}
?>