<?php
/*
Tandas.php

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

class Tandas extends DBObject {
	
	protected $prueba;
	protected $jornada;
	
	/**
	 * Tandas database only contains 'Tipo' field. Extract remaining data from this table
	 * Tipo: 		Tanda type
	 * TipoManga:	Manga type. From Database and Mangas::$tipo_manga
	 * From:		Starting point from Mangas::OrdenSalida
	 * To:			Ending point from Mangas::OrdenSalida
	 * Nombre:		Tanda's name: User defined if 'Tipo'==0
	 * Categoria:	List of supported categorias in this tanda
	 * Grado:		Tanda's grado 
	 */
	static $tipo_tanda = array (
			0	=> array('Tipo'=>0,		'TipoManga'=>0,		'From'=>'',			'To'=>'',			'Nombre'=>'-- Sin especificar --','Categoria'=>'-',	'Grado'=>'-'),
			// en pre-agility no hay categorias
			1	=> array('Tipo'=>1,		'TipoManga'=> 1,	'From'=>'BEGIN,',	'To'=>',END',		'Nombre'=>'Pre-Agility 1',			'Categoria'=>'-LMST','Grado'=>'P.A.'),
			2	=> array('Tipo'=>2,		'TipoManga'=> 2,	'From'=>'BEGIN,',	'To'=>',END',		'Nombre'=>'Pre-Agility 2',			'Categoria'=>'-LMST','Grado'=>'P.A.'),
			3	=> array('Tipo'=>3,		'TipoManga'=> 3,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Agility-1 GI Large',		'Categoria'=>'L',	'Grado'=>'GI'),
			4	=> array('Tipo'=>4,		'TipoManga'=> 3,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Agility-1 GI Medium',	'Categoria'=>'M',	'Grado'=>'GI'),
			5	=> array('Tipo'=>5,		'TipoManga'=> 3,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Agility-1 GI Small',		'Categoria'=>'S',	'Grado'=>'GI'),
			6	=> array('Tipo'=>6,		'TipoManga'=> 4,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Agility-2 GI Large',		'Categoria'=>'L',	'Grado'=>'GI'),
			7	=> array('Tipo'=>7,		'TipoManga'=> 4,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Agility-2 GI Medium',	'Categoria'=>'M',	'Grado'=>'GI'),
			8	=> array('Tipo'=>8,		'TipoManga'=> 4,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Agility-2 GI Small',		'Categoria'=>'S',	'Grado'=>'GI'),
			9	=> array('Tipo'=>9,		'TipoManga'=> 5,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Agility GII Large',		'Categoria'=>'L',	'Grado'=>'GII'),
			10	=> array('Tipo'=>10,	'TipoManga'=> 5,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Agility GII Medium',		'Categoria'=>'M',	'Grado'=>'GII'),
			11	=> array('Tipo'=>11,	'TipoManga'=> 5,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Agility GII Small',		'Categoria'=>'S',	'Grado'=>'GII'),
			12	=> array('Tipo'=>12,	'TipoManga'=> 6,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Agility GIII Large',		'Categoria'=>'L',	'Grado'=>'GIII'),
			13	=> array('Tipo'=>13,	'TipoManga'=> 6,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Agility GIII Medium',	'Categoria'=>'M',	'Grado'=>'GIII'),
			14	=> array('Tipo'=>14,	'TipoManga'=> 6,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Agility GIII Small',		'Categoria'=>'S',	'Grado'=>'GIII'),
			15	=> array('Tipo'=>15,	'TipoManga'=> 7,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Agility Open Large',		'Categoria'=>'L',	'Grado'=>'-'),
			16	=> array('Tipo'=>16,	'TipoManga'=> 7,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Agility Open Medium',	'Categoria'=>'M',	'Grado'=>'-'),
			17	=> array('Tipo'=>17,	'TipoManga'=> 7,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Agility Open Small',		'Categoria'=>'S',	'Grado'=>'-'),
			18	=> array('Tipo'=>18,	'TipoManga'=> 8,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Agility Eq. 3 Large',	'Categoria'=>'L',	'Grado'=>'-'),
			19	=> array('Tipo'=>19,	'TipoManga'=> 8,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Agility Eq. 3 Medium',	'Categoria'=>'M',	'Grado'=>'-'),
			20	=> array('Tipo'=>20,	'TipoManga'=> 8,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Agility Eq. 3 Small',	'Categoria'=>'S',	'Grado'=>'-'),
			21	=> array('Tipo'=>21,	'TipoManga'=> 9,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Ag. Equipos 4 Large',	'Categoria'=>'M',	'Grado'=>'-'),
			// en jornadas por equipos conjunta se mezclan categorias M y S
			22	=> array('Tipo'=>22,	'TipoManga'=> 9,	'From'=>'TAG_M0,',	'To'=>',TAG_T0',	'Nombre'=>'Ag. Equipos 4 Med/Small','Categoria'=>'MS',	'Grado'=>'-'),
			23	=> array('Tipo'=>23,	'TipoManga'=> 10,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Jumping GII Large',		'Categoria'=>'L',	'Grado'=>'GII'),
			24	=> array('Tipo'=>24,	'TipoManga'=> 10,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Jumping GII Medium',		'Categoria'=>'M',	'Grado'=>'GII'),
			25	=> array('Tipo'=>25,	'TipoManga'=> 10,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Jumping GII Small',		'Categoria'=>'S',	'Grado'=>'GII'),
			26	=> array('Tipo'=>26,	'TipoManga'=> 11,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Jumping GIII Large',		'Categoria'=>'L',	'Grado'=>'GIII'),
			27	=> array('Tipo'=>27,	'TipoManga'=> 11,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Jumping GIII Medium',	'Categoria'=>'M',	'Grado'=>'GIII'),
			28	=> array('Tipo'=>28,	'TipoManga'=> 11,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Jumping GIII Small',		'Categoria'=>'S',	'Grado'=>'GIII'),
			29	=> array('Tipo'=>29,	'TipoManga'=> 12,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Jumping Open Large',		'Categoria'=>'L',	'Grado'=>'-'),
			30	=> array('Tipo'=>30,	'TipoManga'=> 12,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Jumping Open Medium',	'Categoria'=>'M',	'Grado'=>'-'),
			31	=> array('Tipo'=>31,	'TipoManga'=> 12,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Jumping Open Small',		'Categoria'=>'S',	'Grado'=>'-'),
			32	=> array('Tipo'=>32,	'TipoManga'=> 13,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Jumping Eq. 3 Large',	'Categoria'=>'L',	'Grado'=>'-'),
			33	=> array('Tipo'=>33,	'TipoManga'=> 13,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Jumping Eq. 3 Medium',	'Categoria'=>'M',	'Grado'=>'-'),
			34	=> array('Tipo'=>34,	'TipoManga'=> 13,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Jumping Eq. 3 Small',	'Categoria'=>'S',	'Grado'=>'-'),
			// en jornadas por equipos conjunta se mezclan categorias M y S
			35	=> array('Tipo'=>35,	'TipoManga'=> 14,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Jp. Equipos 4 Large',	'Categoria'=>'M',	'Grado'=>'-'),
			36	=> array('Tipo'=>36,	'TipoManga'=> 14,	'From'=>'TAG_M0,',	'To'=>',TAG_T0',	'Nombre'=>'Jp. Equipos 4 Med/Small','Categoria'=>'MS',	'Grado'=>'-'),
			// en las rondas KO, los perros compiten todos contra todos
			37	=> array('Tipo'=>37,	'TipoManga'=> 15,	'From'=>'BEGIN,',	'To'=>',END',		'Nombre'=>'Manga K.O.',				'Categoria'=>'-LMST','Grado'=>'-'),
			38	=> array('Tipo'=>38,	'TipoManga'=> 16,	'From'=>'TAG_L0,',	'To'=>',TAG_M0',	'Nombre'=>'Manga Especial Large',	'Categoria'=>'L',	'Grado'=>'-'),
			39	=> array('Tipo'=>39,	'TipoManga'=> 16,	'From'=>'TAG_M0,',	'To'=>',TAG_S0',	'Nombre'=>'Manga Especial Medium',	'Categoria'=>'M',	'Grado'=>'-'),
			40	=> array('Tipo'=>40,	'TipoManga'=> 16,	'From'=>'TAG_S0,',	'To'=>',TAG_T0',	'Nombre'=>'Manga Especial Small',	'Categoria'=>'S',	'Grado'=>'-'),
	
			// "Tiny" support for Pruebas RFEC
			41	=> array('Tipo'=>41,	'TipoManga'=> 3,	'From'=>'TAG_T0,',	'To'=>',END',	'Nombre'=>'Agility-1 GI Tiny',		'Categoria'=>'T',	'Grado'=>'GI'),
			42	=> array('Tipo'=>42,	'TipoManga'=> 4,	'From'=>'TAG_T0,',	'To'=>',END',	'Nombre'=>'Agility-2 GI Tiny',		'Categoria'=>'T',	'Grado'=>'GI'),
			43	=> array('Tipo'=>43,	'TipoManga'=> 5,	'From'=>'TAG_T0,',	'To'=>',END',	'Nombre'=>'Agility GII Tiny',		'Categoria'=>'T',	'Grado'=>'GII'),
			44	=> array('Tipo'=>44,	'TipoManga'=> 6,	'From'=>'TAG_T0,',	'To'=>',END',	'Nombre'=>'Agility GIII Tiny',		'Categoria'=>'T',	'Grado'=>'GIII'),
			45	=> array('Tipo'=>45,	'TipoManga'=> 7,	'From'=>'TAG_T0,',	'To'=>',END',	'Nombre'=>'Agility Open Tiny',		'Categoria'=>'T',	'Grado'=>'-'),
			46	=> array('Tipo'=>46,	'TipoManga'=> 8,	'From'=>'TAG_T0,',	'To'=>',END',	'Nombre'=>'Agility Eq. 3 Tiny',		'Categoria'=>'T',	'Grado'=>'-'),
			// en equipos4  RFEC agrupamos por LM y ST
			47	=> array('Tipo'=>47,	'TipoManga'=> 9,	'From'=>'TAG_L0,',	'To'=>',TAG_S0','Nombre'=>'Ag. Equipos 4 Large/Medium',	'Categoria'=>'LM',	'Grado'=>'-'),
			48	=> array('Tipo'=>48,	'TipoManga'=> 9,	'From'=>'TAG_T0,',	'To'=>',END',	'Nombre'=>'Ag. Equipos 4 Small/Tiny','Categoria'=>'ST',		'Grado'=>'-'),
			49	=> array('Tipo'=>49,	'TipoManga'=> 10,	'From'=>'TAG_T0,',	'To'=>',END',	'Nombre'=>'Jumping GII Tiny',		'Categoria'=>'T',		'Grado'=>'GII'),
			50	=> array('Tipo'=>50,	'TipoManga'=> 11,	'From'=>'TAG_T0,',	'To'=>',END',	'Nombre'=>'Jumping GIII Tiny',		'Categoria'=>'T',		'Grado'=>'GIII'),
			51	=> array('Tipo'=>51,	'TipoManga'=> 12,	'From'=>'TAG_T0,',	'To'=>',END',	'Nombre'=>'Jumping Open Tiny',		'Categoria'=>'T',		'Grado'=>'-'),
			52	=> array('Tipo'=>52,	'TipoManga'=> 13,	'From'=>'TAG_T0,',	'To'=>',END',	'Nombre'=>'Jumping Eq. 3 Tiny',		'Categoria'=>'T',		'Grado'=>'-'),
			53	=> array('Tipo'=>53,	'TipoManga'=> 14,	'From'=>'TAG_L0,',	'To'=>',TAG_S0','Nombre'=>'Jp. Equipos 4 Large/Medium',	'Categoria'=>'LM',	'Grado'=>'-'),
			54	=> array('Tipo'=>54,	'TipoManga'=> 14,	'From'=>'TAG_S0,',	'To'=>',END',	'Nombre'=>'Jp. Equipos 4 Small/Tiny','Categoria'=>'ST',		'Grado'=>'-'),
			55	=> array('Tipo'=>55,	'TipoManga'=> 16,	'From'=>'TAG_T0,',	'To'=>',END',	'Nombre'=>'Manga Especial Tiny',	'Categoria'=>'T',		'Grado'=>'-'),
	);
	
	/**
	 * return every array items that matches with provided key
	 * @param {string} $key
	 * @param {value} $value
	 * @return {array} List of Tandas that matches with requested key/value pair
	*/
	function getTandasInfo($key,$value) {
		$res=array();
		if (!array_key_exists(Tandas::$tipo_tanda[0],$key)) return $res; // key not found: return empty array
		foreach(Tandas::$tipo_tanda as $item) {
			if ($item[$key]==$value) array_push($res,$item);
		}
		return $res;
	}
	
	/**
	 * Constructor
	 * @param {string} $file Caller's indentification
	 * @param {integer} $prueba Prueba ID
	 * @param {integer} $jornada Jornada ID
	 * @throws Exception on invalid data or database connection error
	 */
	function __construct($file,$prueba,$jornada) {
		parent::__construct($file);
		if ( $prueba<=0 ) {
			$this->errormsg="$file::construct() invalid prueba:$prueba ID";
			throw new Exception($this->errormsg);
		}
		if ( $jornada<=0 ) {
			$this->errormsg="$file::construct() invalid jornada:$jornada ID";
			throw new Exception($this->errormsg);
		}
		$this->prueba=$this->__getObject("Pruebas",$prueba);
		$this->jornada=$this->__getObject("Jornadas",$jornada);
		if ($this->jornada->Prueba!=$prueba) {
			$this->errormsg="$file::construct() jornada:$jornada is not owned by prueba:$prueba";
			throw new Exception($this->errormsg);
		}
	}
	
	/**
	 * Insert a new 'Tipo=0' data into database
	 * @param {array} $data
	 */
	function insert($data) {
		// arriving here means update and/or insert
		$p=$this->prueba->ID;
		$j=$this->jornada->ID;
		// locate latest order in manga
		$obj=$this->__selectObject("MAX(Orden) AS Last","Tandas","(Prueba=$p) AND (Jornada=$j)");
		$o=($obj!=null)?1+intval($obj->Last):1; // evaluate latest in order
		$s=(array_key_exists(data,"Sesion"))?$data['Sesion']:1;
		$n=(array_key_exists(data,"Nombre"))?$data['Nombre']:'-- Sin nombre --';
		$h=(array_key_exists(data,"Horario"))?$data['Horario']:'';
		$c=(array_key_exists(data,"Comentario"))?$data['Comentario']:'';
		$str="INSERT INTO Tandas (Tipo,Prueba,Jornada,Sesion,Orden,Nombre,Horario,Comentario) VALUES (0,$p,$j,$s,$o,'$n','$h','$c')";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		return "";
	}
	
	/**
	 * Update Tanda in database
	 * Only allow change "Nombre" field when tipo==0
	 * @param {array} $data
	 */
	function update($id,$data){
		$str="UPDATE Tandas ";
		$set=array();
		$tipo="";
		if (array_key_exists(data,"Sesion")) array_push($set,"Sesion={$data['Sesion']}");
		if (array_key_exists(data,"Nombre")) { array_push($set,"Nombre='{$data['Nombre']}'"); $tipo=" AND (Tipo=0)"; }
		if (array_key_exists(data,"Horario")) array_push($set,"Horario='{$data['Horario']}'");
		if (array_key_exists(data,"Comentario")) array_push($set,"Comentario='{$data['Comentario']}'");
		if (count($set)==0) return; // no data to update
		$sets=imploder(",",$set);
		$str= "$str SET $set WHERE (ID=$id) $tipo"; //  if tipo!=0 cannot change name
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		return "";
	}
	
	function delete($id){
		// only remove those tandas with "Tipo"=0
		// for remaining tipos, removeFromList must be issued
		$str="DELETE FROM Tandas WHERE (ID=$id) AND (Tipo=0)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		return ""; // mark success
	}
	
	function removeFromList($tipo) {
		$p=$this->jornada->ID;
		$j=$this->jornada->ID;
		$str="DELETE FROM Tandas WHERE (Prueba=$p) AND (Jornada=$j) AND (Tipo=$tipo)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		return ""; // mark success
	}
	
	function select($id){
		return $this->__getArray("Tandas",$id);
	}
	
	/**
	 * insert $from before(where==false) or after(where=true) $to
	 * @param unknown $from
	 * @param unknown $to
	 * @param unknown $where
	 */
	function dnd($from,$to,$where) {
		$this->myLogger->enter();
		$p=$this->prueba->ID;
		$j=$this->jornada->ID;
		// get from/to Tanda's ID
		$f=$this->__selectObject("*","Tandas","(Prueba=$p) AND (Jornada=$j) AND (Orden=$from)");
		$t=$this->__selectObject("*","Tandas","(Prueba=$p) AND (Jornada=$j) AND (Orden=$to)");
		if(!$f || !$t) {
			$this->myLogger->error("Error: no ID for tanda's order '$from' and/or '$to' on prueba:$p jornada:$j");
			return $this->errormsg;
		}
		$fid=$f->ID;
		$tid=$t->ID;
		// TODO: write
	}
	
	function swap($from,$to) {
		$this->myLogger->enter();
		$p=$this->prueba->ID;
		$j=$this->jornada->ID;
		// get from/to Tanda's ID
		$f=$this->__selectObject("*","Tandas","(Prueba=$p) AND (Jornada=$j) AND (Orden=$from)");
		$t=$this->__selectObject("*","Tandas","(Prueba=$p) AND (Jornada=$j) AND (Orden=$to)");
		if(!$f || !$t) {
			$this->myLogger->error("Error: no ID for tanda's order '$from' and/or '$to' on prueba:$p jornada:$j");
			return $this->errormsg;
		}
		$fid=$f->ID;
		$tid=$t->ID;
		// perform swap update. 
		// TODO: make it inside a transaction
		$str="UPDATE Tandas SET Orden=$from WHERE (ID=$tid)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		$str="UPDATE Tandas SET Orden=$to WHERE (ID=$fid)";	
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return ""; // mark success
	}

	/**
	 * Obtiene el programa de la jornada
	 * @param {integer} $s session id. $s==0 means "any session"
	 * @return json aware array or string on error
	 */
	function getTandas($s=0){
		$p=$this->jornada->ID;
		$j=$this->jornada->ID;
		
		// prepared statement to retrieve mangas id
		$sql="SELECT ID FROM Mangas WHERE (Jornada=$jornada) AND (Tipo=?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('i',$tipo);
		if (!$res) return $this->error($stmt->error);
		
		// Ask dadabase to retrieve list of Tandas
		$ses=(intval($s)==0)?"":" AND (Sesion=0) ";
		$res= $this->__select(
				/* SELECT */	"*",
				/* FROM */		"Tandas",
				/* WHERE */		"(Prueba=$p) AND (Jornada=$j) $ses",
				/* ORDER BY */	"Orden ASC",
				/* LIMIT */		""
		);
		if(!is_array($res)){
			return $this->error("No encuentro tandas para la prueba:$p jornada:$j");
		}
		
		// merge retrieved data with tipotanda info
		foreach ($res['rows'] as $key => $item) {
			// merge tipo_tanda info into result
			$res['rows'][$key]=array_merge($item,Tandas::$tipo_tanda[$item['Tipo']]);
			// evaluate and insert Manga ID
			if ($item['Tipo']==0) { // User-Provided tandas has no Manga ID
				$res['rows'][$key]['Manga']=0;
			} else { // retrieve Manga ID and merge into result		
				$tipo=$item['TipoManga'];
				$rs=$stmt->execute();
				if (!$rs) return $this->error($stmt->error);
				$stmt->bind_result($mangaid);
				$stmt->fetch();
				$res['rows'][$key]['Manga']=$mangaid;
			}
		}
		return $res;
	}
	
	/**
	 * Obtiene la lista ordenada de perros de esta jornada
	 * @param number $s Sesion ID. $s!=0 -> muestra solo los perros de dicha sesion
	 * @param number $t Tanda ID.
	 *     $t=0; mira todos los perros de todas las tandas
	 *     $t>0; mira SOLO los perros de la tanda (-$t)
	 *     $t<0; mira todos los perros A PARTIR DE la tanda $t
	 * @param number $p Pendientes $p=0 -> muestra todos los perros; else muestra los $p primeros pendientes de salir
	 */
	private function getListaPerros($s=0,$t=0,$p=0){
		$count=$p;			// contador de perros pendientes de listar
		$oldmanga=0;		// variable para controlar manga "activa"
		$perrosmanga=null;	// lista de perros inscritos en una manga indexada por PerroID
		$ordenmanga=null;	// CSV list of perros inscritos en una manga
		$do_iterate=false;	// indica si debe analizar los perros de la tanda
		$rows=array();		// donde iremos guardando los resultados
		$result=array();	// resultado a devolver en formato json
		
		// obtenemos la lita de tandas
		$lista_tandas=$this->getTandas($s);
		
		// iteramos la lista de tandas
		foreach ($lista_tandas as $tanda) {
			// Comprobamos si debemos analizar la tanda
			if ($t>0) { $do_iterate= ( $tanda['ID'] == abs($t) )? true:false; } // iterar solo la tanda
			if ($t<0) { if ( $tanda['ID'] == abs($t) ) $do_iterate=true; } 		// iterar a partir de la tanda
			if ($t==0) $do_iterate=true;										// iterar TODAS las tandas
			if (!$do_iterate) continue; // this tanda is not the one we are looking for
			
			// comprobamos ahora si hay cambio de manga
			if ($oldManga!=$tanda['Manga']) { // cambio de manga
				$oldmanga=$manga;
				// en cada manga cogemos el orden de salida asociado
				$os=new OrdenSalida("ordenTandas::getData()",$prueba,$jornada,$manga);
				$ordenmanga=$os->getOrden($manga);
				// cogemos tambien la lista de perros de cada manga, y la reindexamos segun el orden del perro
				$res=$this->__select("*", "Resultados","(Prueba=$prueba) AND (Jornada=$jornada) AND (Manga=$manga)","","");
				if (!is_array($res)) return $this->error($this->conn->error);
				$perrosmanga=array();
				foreach($res['rows'] as $item) {
					$perrosmanga[$item['Perro']]=$item;
				}
			}
			
			// OK ya tenemos los perros de la manga. Ahora vamos a sacar la lista por cada tanda

			// de cada tanda extraemos el substring definido entre 'from' y 'to'
			$ordentanda=getInnerString($ordenmanga,$tanda['From'],$tanda['To']);
				
			// y generamos la lista ordenada de los perros inscritos a partir de estos datos
			if($ordentanda==="") continue; // skip empty tandas
			$orden=explode(',',$ordentanda);
			$celo=0;
			foreach($orden as $perro) {
				// from manual: don't compare strpos against 'true'
				if (strpos($perro,'TAG')!==false) { // separator. check for 'Celo' field
					if (strpos($perro,'1')===false) $celo=0;
					if (strpos($perro,'0')===false) $celo=1;
					continue; // next search
				}
				$perrosmanga[$perro]['Celo']=$celo; // store celo info
				$perrosmanga[$perro]['Tanda']=$tanda['Nombre'];
				$perrosmanga[$perro]['ID']=$tanda['ID'];
				if ($pendientes==0) { array_push($rows,$perrosmanga[$perro]); continue; } // include all
				if ($perrosmanga[$perro]['Pendiente']==0) continue; // not pendiente: skip
				if ($count > 0) { $count--; array_push($rows,$perrosmanga[$perro]); continue; } // not yet at count: insert
				// arriving here means that every requested dogs are filled
				$this->myLogger->debug("OrdenTandas::getData() Already have $pendientes dogs");
				// so return
				$result['rows']=$rows;
				$result['total']=count($rows);
				$this->myLogger->leave();
				return $result;
			}			 
		}
		$result['rows']=$rows;
		$result['total']=count($rows);
		$this->myLogger->leave();
		return $result;
	}

	function getData($s,$t,$p) {
		return $this->getListaPerros($s,-($t),$p);
	}
	
	function getDataByTanda($s,$t) {
		return $this->getListaPerros($$p,$j,s,$t,0);
	}
	
	private function insert_remove($rsce,$tipo,$oper) {
		foreach( $this->getTandasInfo('TipoManga',$tipo) as $item) {
			if( ($rsce==0) && ($item['Categoria']==='T') ) {
				// remove every "tiny" tandas on RSCE contests
				$this->removeFromList($item['Tipo']);
				continue;
			}
			if( ($rsce!=0) && ($item['Grado']==='GIII') ) {
				// remove every "Grado III" tandas on non RSCE contests
				$this->removeFromList($item['Tipo']);
				continue;
			}
			if ($oper==false) { // remove requested
				$this->removeFromList($item['Tipo']);
				continue;
			} 
			// arriving here means update and/or insert
			$p=$this->prueba->ID;
			$j=$this->jornada->ID;
			// locate latest order in manga
			$obj=$this->__selectObject("MAX(Orden) AS Last","Tandas","(Prueba=$p) AND (Jornada=$j)");
			$last=1;
			if ($obj!=null) $last=1+intval($obj->Last); // evaluate latest in order
			// check for already inserted into Tandas
			$obj=$this->__selectObject("*","Tandas","(Prueba=$p) AND (Jornada=$j) AND (Tipo=$tipo)");
			if ($obj==null) { // insert into list at end
				$n=Tandas::$tipo_tanda[$tipo]['Nombre'];
				$str="INSERT INTO Tandas (Tipo,Prueba,Jornada,Sesion,Orden,Nombre) VALUES ($tipo,$p,$j,1,$last,'$n')";
				$rs=$this->query($str);
				if (!$rs) return $this->error($this->conn->error); 
			} else { // move to the end of the list
				$str="UPDATE Tandas SET Orden=$last WHERE (Prueba=$p) AND (Jornada=$j) AND (Tipo=$tipo)";
				$rs=$this->query($str);
				if (!$rs) return $this->error($this->conn->error); 
			}
		}
		return ""; // success
	}
	
	/**
	 * Insert or update Tandas according Jornada Data
	 */
	function populateJornada(){
		$this->myLogger->enter();
		// obtenemos datos de la jornada y prueba
		$j=$this->jornada;
		$p=$this->prueba;
		$r=$p->RSCE;
		// actualizamos la lista de tandas de cada ronda
		
		// preagility necesita tratamiento especial. primero borramos
		$this->insert_remove($r,1,false);
		$this->insert_remove($r,2,false);
		if (($j->PreAgility2 != 0)){ // preagility2 also handles preagility1
			$orden= $this->insert_remove($r,1,true);
			$orden= $this->insert_remove($r,2,true);
		}
		if (($j->PreAgility != 0)){
			$orden= $this->insert_remove($r,1,true);
			$orden= $this->insert_remove($r,2,false);
		}
		$this->insert_remove($r,3,($j->Grado1 != 0)?true:false);
		$this->insert_remove($r,4,($j->Grado1 != 0)?true:false);
		$this->insert_remove($r,5,($j->Grado2 != 0)?true:false);
		$this->insert_remove($r,10,($j->Grado2 != 0)?true:false);
		$this->insert_remove($r,6,($j->Grado3 != 0)?true:false);
		$this->insert_remove($r,11,($j->Grado3 != 0)?true:false);
		$this->insert_remove($r,7,($j->Open != 0)?true:false);
		$this->insert_remove($r,12,($j->Open != 0)?true:false);
		$this->insert_remove($r,8,($j->Equipos3 != 0)?true:false);
		$this->insert_remove($r,13,($j->Equipos3 != 0)?true:false);
		$this->insert_remove($r,9,($j->Equipos4 != 0)?true:false);
		$this->insert_remove($r,14,($j->Equipos4 != 0)?true:false);
		$this->insert_remove($r,15,($j->KO != 0)?true:false);
		$this->insert_remove($r,16,($j->Especial != 0)?true:false);
		$this->myLogger->leave();
	}
	
	/**
	 * Remove all associated Tandas on provided Jornada/Prueba
	 */
	function removeJornada(){
		$p=$this->prueba->ID;
		$j=$this->prueba->ID;
		$str="DELETE FROM Tandas WHERE (Prueba=$p) AND (Jornada=$j)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		return ""; // mark success
	}
}
?>