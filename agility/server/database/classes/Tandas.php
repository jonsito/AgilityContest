<?php
/*
Tandas.php

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
require_once(__DIR__."/../../../modules/Federations.php");
require_once("DBObject.php");
require_once("OrdenSalida.php");
require_once("Clubes.php");

class Tandas extends DBObject {
	
	protected $prueba;
	protected $jornada;
	protected $sesiones; // used to store current sesions
	protected $mangas; // used to store mangas of this journey
	
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
	public static $tipo_tanda = array (
			0	=> array('Tipo'=>0,		'TipoManga'=>0,		'Nombre'=>'-- Sin especificar --',  'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'-',	'Grado'=>'-'),
			// en pre-agility no hay categorias
			1	=> array('Tipo'=>1,		'TipoManga'=> 1,	'Nombre'=>'Pre-Agility 1',			'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'-LMST','Grado'=>'P.A.'),
			2	=> array('Tipo'=>2,		'TipoManga'=> 2,	'Nombre'=>'Pre-Agility 2',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'-LMST','Grado'=>'P.A.'),
			3	=> array('Tipo'=>3,		'TipoManga'=> 3,	'Nombre'=>'Agility-1 GI Large',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'GI'),
			4	=> array('Tipo'=>4,		'TipoManga'=> 3,	'Nombre'=>'Agility-1 GI Medium',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'GI'),
			5	=> array('Tipo'=>5,		'TipoManga'=> 3,	'Nombre'=>'Agility-1 GI Small',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'GI'),
			6	=> array('Tipo'=>6,		'TipoManga'=> 4,	'Nombre'=>'Agility-2 GI Large',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'GI'),
			7	=> array('Tipo'=>7,		'TipoManga'=> 4,	'Nombre'=>'Agility-2 GI Medium',	'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'GI'),
			8	=> array('Tipo'=>8,		'TipoManga'=> 4,	'Nombre'=>'Agility-2 GI Small',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'GI'),
			9	=> array('Tipo'=>9,		'TipoManga'=> 5,	'Nombre'=>'Agility GII Large',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'GII'),
			10	=> array('Tipo'=>10,	'TipoManga'=> 5,	'Nombre'=>'Agility GII Medium',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'GII'),
			11	=> array('Tipo'=>11,	'TipoManga'=> 5,	'Nombre'=>'Agility GII Small',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'GII'),
			12	=> array('Tipo'=>12,	'TipoManga'=> 6,	'Nombre'=>'Agility GIII Large',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'GIII'),
			13	=> array('Tipo'=>13,	'TipoManga'=> 6,	'Nombre'=>'Agility GIII Medium',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'GIII'),
			14	=> array('Tipo'=>14,	'TipoManga'=> 6,	'Nombre'=>'Agility GIII Small',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'GIII'),
			15	=> array('Tipo'=>15,	'TipoManga'=> 7,	'Nombre'=>'Agility Large',			'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'-'), // Individual-Open
			16	=> array('Tipo'=>16,	'TipoManga'=> 7,	'Nombre'=>'Agility Medium',			'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'-'), //  Individual-Open
			17	=> array('Tipo'=>17,	'TipoManga'=> 7,	'Nombre'=>'Agility Small',			'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'-'), //  Individual-Open
			18	=> array('Tipo'=>18,	'TipoManga'=> 8,	'Nombre'=>'Agility team Large',		'isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'L',	'Grado'=>'-'), // team best
			19	=> array('Tipo'=>19,	'TipoManga'=> 8,	'Nombre'=>'Agility team Medium',	'isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'M',	'Grado'=>'-'), // team best
			20	=> array('Tipo'=>20,	'TipoManga'=> 8,	'Nombre'=>'Agility team Small',		'isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'S',	'Grado'=>'-'), // team best
        	// en jornadas por equipos conjunta tres alturas se mezclan categorias M y S
			21	=> array('Tipo'=>21,	'TipoManga'=> 9,	'Nombre'=>'Ag. Teams Large',		'isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'L',	'Grado'=>'-'), // team combined
			22	=> array('Tipo'=>22,	'TipoManga'=> 9,	'Nombre'=>'Ag. Teams Med/Small',	'isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'MS',	'Grado'=>'-'), // team combined
			23	=> array('Tipo'=>23,	'TipoManga'=> 10,	'Nombre'=>'Jumping GII Large',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'GII'),
			24	=> array('Tipo'=>24,	'TipoManga'=> 10,	'Nombre'=>'Jumping GII Medium',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'GII'),
			25	=> array('Tipo'=>25,	'TipoManga'=> 10,	'Nombre'=>'Jumping GII Small',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'GII'),
			26	=> array('Tipo'=>26,	'TipoManga'=> 11,	'Nombre'=>'Jumping GIII Large',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'GIII'),
			27	=> array('Tipo'=>27,	'TipoManga'=> 11,	'Nombre'=>'Jumping GIII Medium',	'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'GIII'),
			28	=> array('Tipo'=>28,	'TipoManga'=> 11,	'Nombre'=>'Jumping GIII Small',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'GIII'),
			29	=> array('Tipo'=>29,	'TipoManga'=> 12,	'Nombre'=>'Jumping Large',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'-'), //  Individual-Open
			30	=> array('Tipo'=>30,	'TipoManga'=> 12,	'Nombre'=>'Jumping Medium',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'-'), //  Individual-Open
			31	=> array('Tipo'=>31,	'TipoManga'=> 12,	'Nombre'=>'Jumping Small',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'-'), //  Individual-Open
			32	=> array('Tipo'=>32,	'TipoManga'=> 13,	'Nombre'=>'Jumping team Large',		'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'L',	'Grado'=>'-'), // team best
			33	=> array('Tipo'=>33,	'TipoManga'=> 13,	'Nombre'=>'Jumping team Medium',	'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'M',	'Grado'=>'-'), // team best
			34	=> array('Tipo'=>34,	'TipoManga'=> 13,	'Nombre'=>'Jumping team Small',		'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'S',	'Grado'=>'-'), // team best
			// en jornadas por equipos conjunta 3 alturas se mezclan categorias M y S
			35	=> array('Tipo'=>35,	'TipoManga'=> 14,	'Nombre'=>'Jp. Teams Large',		'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'L',	'Grado'=>'-'), // team combined
			36	=> array('Tipo'=>36,	'TipoManga'=> 14,	'Nombre'=>'Jp. Teams Med/Small',	'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'MS',	'Grado'=>'-'), // team combined
			// en las rondas KO, los perros compiten todos contra todos
			37	=> array('Tipo'=>37,	'TipoManga'=> 15,	'Nombre'=>'Manga K.O.',				'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'-LMST','Grado'=>'-'),
			38	=> array('Tipo'=>38,	'TipoManga'=> 16,	'Nombre'=>'Special Round Large',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'-'),
			39	=> array('Tipo'=>39,	'TipoManga'=> 16,	'Nombre'=>'Special Round Medium',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'-'),
			40	=> array('Tipo'=>40,	'TipoManga'=> 16,	'Nombre'=>'Special Round Small',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'-'),
	
			// "Tiny" support for Pruebas de cuatro alturas
			41	=> array('Tipo'=>41,	'TipoManga'=> 3,	'Nombre'=>'Agility-1 GI Tiny',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'GI'),
			42	=> array('Tipo'=>42,	'TipoManga'=> 4,	'Nombre'=>'Agility-2 GI Tiny',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'GI'),
			43	=> array('Tipo'=>43,	'TipoManga'=> 5,	'Nombre'=>'Agility GII Tiny',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'GII'),
			44	=> array('Tipo'=>44,	'TipoManga'=> 6,	'Nombre'=>'Agility GIII Tiny',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'GIII'), // no existe
			45	=> array('Tipo'=>45,	'TipoManga'=> 7,	'Nombre'=>'Agility Tiny',			'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'-'), //  Individual-Open
			46	=> array('Tipo'=>46,	'TipoManga'=> 8,	'Nombre'=>'Agility team Tiny',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'-'), // team best
			// en equipos4  cuatro alturas  agrupamos por LM y ST
			47	=> array('Tipo'=>47,	'TipoManga'=> 9,	'Nombre'=>'Ag. teams Large/Medium', 'isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'LM',	'Grado'=>'-'), // team combined
			48	=> array('Tipo'=>48,	'TipoManga'=> 9,	'Nombre'=>'Ag. teams Small/Tiny',	'isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'ST',	'Grado'=>'-'), // team combined
			49	=> array('Tipo'=>49,	'TipoManga'=> 10,	'Nombre'=>'Jumping GII Tiny',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'GII'),
			50	=> array('Tipo'=>50,	'TipoManga'=> 11,	'Nombre'=>'Jumping GIII Tiny',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'GIII'), // no existe
			51	=> array('Tipo'=>51,	'TipoManga'=> 12,	'Nombre'=>'Jumping Tiny',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'-'), //  Individual-Open
			52	=> array('Tipo'=>52,	'TipoManga'=> 13,	'Nombre'=>'Jumping team Tiny',		'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'T',	'Grado'=>'-'), // team best
			53	=> array('Tipo'=>53,	'TipoManga'=> 14,	'Nombre'=>'Jp. teams Large/Medium', 'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'LM',	'Grado'=>'-'), // team combined
			54	=> array('Tipo'=>54,	'TipoManga'=> 14,	'Nombre'=>'Jp. teams Small/Tiny',	'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'ST',	'Grado'=>'-'), // team combined
			55	=> array('Tipo'=>55,	'TipoManga'=> 16,	'Nombre'=>'Special round Tiny',	    'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'-'),
            56	=> array('Tipo'=>56,	'TipoManga'=> 17,	'Nombre'=>'Agility-3 GI Large',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'GI'), // extra rounds for GI RFEC
            57	=> array('Tipo'=>57,	'TipoManga'=> 17,	'Nombre'=>'Agility-3 GI Medium',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'GI'),
            58	=> array('Tipo'=>58,	'TipoManga'=> 17,	'Nombre'=>'Agility-3 GI Small',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'GI'),
            59	=> array('Tipo'=>59,	'TipoManga'=> 17,	'Nombre'=>'Agility-3 GI Tiny',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'GI')
	);

    static function isAgility($tipo) {
        if (!array_key_exists($tipo,Tandas::$tipo_tanda)) return false; // key not found: return false
        return Tandas::$tipo_tanda[$tipo]['isAgility'];
    }

    static function isTeam($tipo) {
        if (!array_key_exists($tipo,Tandas::$tipo_tanda)) return false; // key not found: return false
        return Tandas::$tipo_tanda[$tipo]['isTeam'];
    }

    // obtiene la lista de tipos de tanda que coinciden con un tipo de manga determinado
    // usado para evaluar el orden de las categorias segun el programa de la jornada
    static function getTandasByTipoManga($tipo){
        $res=array();
        foreach(Tandas::$tipo_tanda as $key => $value) {
            if ($value['TipoManga']==$tipo) array_push($res,$key);
        }
        return $res;
    }
	/**
	 * return every array items that matches with provided key
	 * @param {string} $key
	 * @param {value} $value
	 * @return {array} List of Tandas that matches with requested key/value pair
	*/
	static function getTandasInfo($key,$value) {
		$res=array();
		if (!array_key_exists($key,Tandas::$tipo_tanda[0])) { // use index0 to check valid key
            // key not found: notify and return empty array
		    do_log("Invalid search key for Tandas array:$key");
		    return $res;
        }
		foreach(Tandas::$tipo_tanda as $item) {
			if ($item[$key]==$value) array_push($res,$item);
		}
		return $res;
	}
	
	function getSessionName($id){
		foreach($this->sesiones as $sesion) {
			if ($sesion['ID']==$id) return $sesion['Nombre'];
		}
		$this->myLogger->error("No session found with ID:$id");
		return ""; // no session name found
	}
	
	function getMangaByTipo($tipomanga) {
		foreach($this->mangas as $manga) {
			if ($manga['Tipo']==$tipomanga) return $manga;
		}
		$this->myLogger->error("No mangas found with Tipo:$tipomanga");
		return null;
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
		$s=$this->__select("*","Sesiones","","","");
		$this->sesiones=$s['rows'];
		$m=$this->__select("*","Mangas","(Jornada=$jornada)","","");
		$this->mangas=$m['rows'];
	}
	
	function getHttpData() {
		$data=array();
		$data['Prueba']=$this->prueba->ID;
		$data['Jornada']=$this->jornada->ID;
		$data['ID']=http_request("ID","i",0);
		$data['InsertID']=http_request("InsertID","i",0);
		$data['Tipo']=http_request("Tipo","i",0);
		$data['Nombre']=http_request("Nombre","s","-- Sin nombre --");
		$data['Sesion']=http_request("Sesion","i",2); // defaults to Ring 1
		$data['Horario']=http_request("Horario","s","");
		$data['Comentario']=http_request("Comentario","s","");
		return $data;
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
		$o=($obj!==null)?1+intval($obj->Last):1; // evaluate latest in order
		$s=$data['Sesion'];
		$n=$data['Nombre'];
		$h=$data['Horario'];
		$c=$data['Comentario'];
		$str="INSERT INTO Tandas (Tipo,Prueba,Jornada,Sesion,Orden,Nombre,Horario,Comentario) VALUES (0,$p,$j,$s,$o,'$n','$h','$c')";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// obtenemos el ID del registro insertado
		$from=$this->conn->insert_id;
		$to=$data['InsertID'];
		if ($to==0) {
			// buscamos el id del programa con menor orden
			$str="SELECT ID From Tandas ORDER BY Orden ASC LIMIT 0,1";
			$rs=$this->query($str);
			if (!$rs) return $this->error($this->conn->error);
			$obj=$rs->fetch_object();
			$rs->free();
			$to=intval($obj->ID);
		}
		// insertamos DELANTE del la tanda seleccionada
		if( ($to!=0) && ($from!=$to) )return $this->dragAndDrop($from,$to,false);
		$this->myLogger->info("Tandas::insert() WARN: cannot insert Tanda $from before requested one");
		return "";
	}
	
	/**
	 * Update Tanda in database
	 * Only allow change "Nombre" field when tipo==0
	 * @param {array} $data
	 * @throws Exception on invalid tanda ID
	 */
	function update($id,$data){
		if ($id<=0) throw new Exception ("Invalid Tanda ID:$id");
		$s=$data['Sesion'];
		$n=$data['Nombre'];
		$h=$data['Horario'];
		$c=$data['Comentario'];
        $str= "UPDATE Tandas SET Sesion=$s, Horario='$h', Comentario='$c' WHERE (ID=$id)";
        $rs=$this->query($str);
        if (!$rs) return $this->error($this->conn->error);
        // if tipo!=0 cannot change name
        $str= "UPDATE Tandas SET Nombre='$n' WHERE (ID=$id) AND (Tipo=0)";
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
		$p=$this->prueba->ID;
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
	 * This dnd routine uses a Orden shift'ng: increase every remaining row order, 
	 * and assign moved row orden to created hole 
	 * @param {integer} $from id to move
	 * @param {integer} $to id to insert arounn
	 * @param {boolean} $where false:insert before  / true:insert after
	 */
	function dragAndDrop($from,$to,$where) {
		$this->myLogger->enter();
		$p=$this->prueba->ID;
		$j=$this->jornada->ID;
		// get from/to Tanda's ID
		$f=$this->__selectObject("*","Tandas","(Prueba=$p) AND (Jornada=$j) AND (ID=$from)");
		$t=$this->__selectObject("*","Tandas","(Prueba=$p) AND (Jornada=$j) AND (ID=$to)");
		if(!$f || !$t) {
			$this->myLogger->error("Error: no ID for tanda's order '$from' and/or '$to' on prueba:$p jornada:$j");
			return $this->errormsg;
		}
		$torder=$t->Orden;
		$neworder=($where)?$torder+1/*after*/:$torder/*before*/;
		$comp=($where)?">"/*after*/:">="/*before*/;
		$str="UPDATE Tandas SET Orden=Orden+1 WHERE ( Prueba = $p ) AND ( Jornada = $j ) AND ( Orden $comp $torder )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$str="UPDATE Tandas SET Orden=$neworder WHERE ( Prueba = $p ) AND ( Jornada = $j ) AND ( ID = $from )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		return "";
	}
	
	/**
	 * Swap orden between requested tandas
	 * @param {integer} $from Tanda ID 1
	 * @param {integer} $to Tanda ID 2
	 * @return {string} error message or "" on success
	 */
	function swap($from,$to) {
		$this->myLogger->enter();
		$p=$this->prueba->ID;
		$j=$this->jornada->ID;
		// get from/to Tanda's ID
		$f=$this->__selectObject("*","Tandas","(Prueba=$p) AND (Jornada=$j) AND (ID=$from)");
		$t=$this->__selectObject("*","Tandas","(Prueba=$p) AND (Jornada=$j) AND (ID=$to)");
		if(!$f || !$t) {
			$this->myLogger->error("Error: no ID for tanda's order '$from' and/or '$to' on prueba:$p jornada:$j");
			return $this->errormsg;
		}
		$forden=$f->Orden;
		$torden=$t->Orden;
		// perform swap update. 
		// TODO: make it inside a transaction
		$str="UPDATE Tandas SET Orden=$torden WHERE (ID=$from)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		$str="UPDATE Tandas SET Orden=$forden WHERE (ID=$to)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return ""; // mark success
	}

	/**
	 * Obtiene el programa de la jornada
	 * @param {integer} $s session id.
     *    0: ANY sesion
     *    1: ANY BUT User defined sessions
     *   -1: User defined sessions
     *    n: Session number "n"
     *   -n: Session number "n" PLUS User defined sessions
	 * @return {array} easyui-aware array or string on error
	 */
	function getTandas($sessid=0){
        $s=intval($sessid);
		$p=$this->prueba->ID;
		$j=$this->jornada->ID;
        if ($s==0) $ses="";
        if ($s==1) $ses= " AND (Tipo!=0)";
        if ($s==-1) $ses= " AND (Tipo=0)";
        if ($s>1) $ses= " AND (Sesion=$s)";
        if ($s<(-1)) {
            $s=-$s;
            $ses= " AND ( (Sesion=$s) OR (Sesion=1) )";
        }
		// Ask dadabase to retrieve list of Tandas
		$res= $this->__select(
				/* SELECT */	"*",
				/* FROM */		"Tandas",
				/* WHERE */		"(Prueba=$p) AND (Jornada=$j) $ses",
				/* ORDER BY */	"Orden ASC",
				/* LIMIT */		""
		);
		if(!is_array($res)){
			return $this->error("No encuentro tandas para la prueba:$p jornada:$j sesion:$sessid");
		}
		
		// merge retrieved data with tipotanda info
		foreach ($res['rows'] as $key => $item) {
			// merge tipo_tanda info into result
			$res['rows'][$key]=array_merge(Tandas::$tipo_tanda[$item['Tipo']],$item);
			// evaluate and insert Manga ID
			if ($res['rows'][$key]['TipoManga']==0) { // User-Provided tandas has no Manga ID
                $res['rows'][$key]['Manga']=0;
                $res['rows'][$key]['Participantes']='';
			} else {
			    // retrieve Manga ID and merge into result
				$manga=$this->getMangaByTipo($res['rows'][$key]['TipoManga']);	

				// add extra info to result
				$res['rows'][$key]['Manga']=(is_array($manga))? $manga['ID']: 0 ;

                // and finally add number of participants
                $str="( Prueba={$this->prueba->ID} ) AND ( Jornada={$this->jornada->ID} ) AND (Manga={$res['rows'][$key]['Manga']})";
                $result=$this->__select("*","Resultados",$str,"","");
                if (!is_array($result)) {
                    $this->myLogger->error($result); return $result;
                }
                $count=0;
                foreach($result['rows'] as $itm) { // comparamos categoria y grado
                    // si el grado es '-' se contabiliza. else si coincide grado se contabiliza
                    if (($res['rows'][$key]['Grado']!=='-') && ($itm['Grado']!==$res['rows'][$key]['Grado']) ) continue;
                    // comparamos categorias
                    if ( strstr($res['rows'][$key]['Categoria'],$itm['Categoria'])===false ) continue;
                    $count++;
                }
                $res['rows'][$key]['Participantes']=strval($count);// datos del participacion
			}
			$res['rows'][$key]['NombreSesion']=$this->getSessionName($res['rows'][$key]['Sesion']);
		}
		return $res;
	}
	
	/**
	 * Obtiene la lista ordenada de perros de esta jornada asociadas a la sesion, y tandas especificadas
     * @param {integer} $s session id.
     *    0: ANY sesion
     *    1: ANY BUT User defined sessions
     *   -1: User defined sessions
     *    n: Session number "n"
     *   -n: Session number "n" PLUS User defined sessions
	 * @param {number} $t Tanda ID.
	 *     $t=0; mira todos los perros de todas las tandas de la sesion indicada
	 *     $t>0; mira SOLO los perros de la tanda
	 *     $t<0; mira todos los perros A PARTIR DE la tanda (-$t)
	 * @param {number} $pendientes Pendientes $p==0 -> muestra todos los perros; else muestra los $p primeros pendientes de salir
	 */
	private function getListaPerros($s=0,$t=0,$pendientes=0){
		$count=$pendientes;	// contador de perros pendientes de listar
		$manga=0;		// variable para controlar manga "activa"
		$perrosmanga=null;	// {array} lista de perros ordenada segun ordensalida de la manga
		$do_iterate=false;	// indica si debe analizar los perros de la tanda
		$rows=array();		// donde iremos guardando los resultados
		$result=array();	// resultado a devolver en formato json
		
		// obtenemos la lista de tandas
		$lista_tandas=$this->getTandas($s);
		$club= new Clubes("Tandas::getListaPerros",$this->prueba->RSCE);
		// iteramos la lista de tandas
		foreach ($lista_tandas['rows'] as $tanda) {
			// $this->myLogger->info("Analizando tanda \n".json_encode($tanda));
			// Comprobamos si debemos analizar la tanda
			if ($t>0) { $do_iterate= ( $tanda['ID'] == abs($t) )? true:false; } // iterar solo la tanda
			if ($t<0) { if ( $tanda['ID'] == abs($t) ) $do_iterate=true; } 		// iterar a partir de la tanda
			if ($t==0) $do_iterate=true;										// iterar TODAS las tandas
			if (!$do_iterate) continue; // this tanda is not the one we are looking for
			if ($tanda['Manga']==0) continue; // user defined tandas, has no manga associated
			// comprobamos ahora si hay cambio de manga
			if ($manga!=$tanda['Manga']) { // cambio de manga
				$manga=$tanda['Manga'];
				// en cada manga cogemos  los perros ordenados segun el orden de salida
				$os=new OrdenSalida("Tandas::getListaPerros()",$manga);
				$perrosmanga=$os->getData(false); // false: do not include extra team information row
			}
			// OK ya tenemos la lista ordenada de los perros de cada manga
			// Ahora vamos a sacar la lista por cada tanda
            foreach($perrosmanga['rows'] as &$perro) {
                // si el perro no pertenece a la tanda skip (comprobar categoria)
                if (strpos($tanda['Categoria'],$perro['Categoria'])===false) continue;
                $perro['Tanda']=$tanda['Nombre'];
                $perro['ID']=$tanda['ID']; // replace resultadoID por tandaID TODO: revise why
                if ($pendientes==0) { // include all
                    $perro['LogoClub']=$club->getLogoName('NombreClub',$perro['NombreClub']);
                    array_push($rows,$perro);
                    continue;
                }
                if ($perro['Pendiente']==0) continue; // not pendiente: skip
                if ($count > 0) {  // not yet at count: insert
                    $count--;
                    $perro['LogoClub']=$club->getLogoName('NombreClub',$perro['NombreClub']);
                    array_push($rows,$perro);
                    continue;
                }
                // arriving here means that every requested dogs are filled
                $this->myLogger->debug("Tandas::getListaPerros() Already have $pendientes dogs");
                // so return
                $result['rows']=$rows;
                $result['total']=count($rows);
                $this->myLogger->leave();
                return $result;
            }
            // no more dogs in this tanda. go to next
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
		$page=http_request("page","i",0);
		$rows=http_request("rows","i",0);
		$res=$this->getListaPerros($s,$t,0);
		if (($page==0) || ($rows==0)) return $res;
		if($res['total']==0) return  $res;
		// on scrollview, $res['total'] returns total number of rows
		// but only $rows starting at $page*$rows are returned
		$r=array_slice($res['rows'],($page-1)*$rows,$rows,false);
		$res['rows']=$r;
		return $res;
	}

	function getDataByDorsal($s,$t,$d) {
		$res=$this->getListaPerros($s,$t,0);
		if($res['total']==0) return  $res; // no data
		$count=0;
		foreach ($res['rows'] as $row) {
			if ($row['Dorsal']==$d) { $row['RowIndex']="$count"; return $row; }
			$count++;
		}
		// arriving here means that dorsal is not found in this tanda; notify error
		$this->myLogger->info("Requested Dorsal:$d not found in Tanda:$t");
		return array('RowIndex' => "-1" );
	}

	// fed: federation data
    // tipomanga: manga type:
    // oper: false:remove true:insert
	private function insert_remove($fed,$tipomanga,$oper) {
		$alturas=intval($fed->get('Heights'));
		$grados=intval($fed->get('Grades'));
        $tandas=$this->getTandasInfo('TipoManga',$tipomanga);
		foreach( $tandas as $item) {
            $tipo=$item['Tipo'];
			if( ($alturas==3) && ($item['Categoria']==='T') ) {
				// remove every "tiny" tandas on RSCE contests
				$this->removeFromList($tipo);
				continue;
			}
			if( ($grados==2) && ($item['Grado']==='GIII') ) {
				// remove every "Grado III" tandas on non RSCE contests
				$this->removeFromList($tipo);
				continue;
			}
            // equipos 4 (tipomanga=9,14) tienen tratamiento especial
            // pues mezclan categorias
            if( ($alturas==3) && ( ($tipomanga==9)||($tipomanga==14) ) ) {
                // remove every 4-heights rounds on 3-heights 4team modes
                if (($tipo==47) || ($tipo==48) || ($tipo==53) || ($tipo==54)) {
                    $this->removeFromList($tipo);
                    continue;
                }
            }
            if( ($alturas==4) && ( ($tipomanga==9)||($tipomanga==14) ) ) {
                // remove every 3-heights rounds on 4-heights 4team modes
                if (($tipo==21) || ($tipo==22) || ($tipo==35) || ($tipo==36)) {
                    $this->removeFromList($tipo);
                    continue;
                }
            }
            // explicit remove requested
			if ($oper==false) {
				$this->removeFromList($tipo);
				continue;
			} 
			// arriving here means update and/or insert
			$p=$this->prueba->ID;
			$j=$this->jornada->ID;
			// locate latest order in manga
			$obj=$this->__selectObject("MAX(Orden) AS Last","Tandas","(Prueba=$p) AND (Jornada=$j)");
			$last=1;
			if ($obj!==null) $last=1+intval($obj->Last); // evaluate latest in order
			// check for already inserted into Tandas
			$obj=$this->__selectObject("*","Tandas","(Prueba=$p) AND (Jornada=$j) AND (Tipo=$tipo)");
			if ($obj===null) { // insert into list at end.
                $n=$fed->getTandaName($tipo);
				// $n=_(Tandas::$tipo_tanda[$tipo]['Nombre']); // should be handled by federation module
				$c=Tandas::$tipo_tanda[$tipo]['Categoria'];
				$g=Tandas::$tipo_tanda[$tipo]['Grado'];
				$str="INSERT INTO Tandas (Tipo,Prueba,Jornada,Sesion,Orden,Nombre,Categoria,Grado) 
					VALUES ($tipo,$p,$j,2,$last,'$n','$c','$g')"; // Default session is 2->Ring 1
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
		$f=Federations::getFederation(intval(intval($p->RSCE)));
		// $this->myLogger->trace("call to getFederation({$p->RSCE}) returns: ".print_r($f,true));
		// actualizamos la lista de tandas de cada ronda
		
		// preagility necesita tratamiento especial
		if (($j->PreAgility2 != 0)){ // preagility2 also handles preagility1
			$this->insert_remove($f,1,true);	// Pre-Agility Manga 1
			$this->insert_remove($f,2,true);	// Pre-Agility Manga 2
		} else 	if (($j->PreAgility != 0)){
			$this->insert_remove($f,1,true);	// Pre-Agility Manga 1
			$this->insert_remove($f,2,false);	// Pre-Agility Manga 2
		} else {
            $this->insert_remove($f,1,false);	// Pre-Agility Manga 1
            $this->insert_remove($f,2,false);	// Pre-Agility Manga 2
        }
        // grado 1 puede tener 1, 2 o 3 mangas
        switch($j->Grado1) {
            case 3: // 3- round grado1
                $this->insert_remove($f,3,true);$this->insert_remove($f,4,true);$this->insert_remove($f,17,true);
                break;
            case 2: // 1- round grado1
                $this->insert_remove($f,3,true);$this->insert_remove($f,4,false);$this->insert_remove($f,17,false);
                break;
            case 1: // 2- round grado1
                $this->insert_remove($f,3,true);$this->insert_remove($f,4,true);$this->insert_remove($f,17,false);
                break;
            default: // no grado1
            $this->insert_remove($f,3,false);$this->insert_remove($f,4,false);$this->insert_remove($f,17,false);
        }
        $this->insert_remove($f,5,($j->Grado2 != 0)?true:false);		// Agility Grado II
		$this->insert_remove($f,10,($j->Grado2 != 0)?true:false);		// Jumping Grado II
		$this->insert_remove($f,6,($j->Grado3 != 0)?true:false);		// Agility Grado III
		$this->insert_remove($f,11,($j->Grado3 != 0)?true:false);		// Jumping Grado III
		$this->insert_remove($f,7,($j->Open != 0)?true:false);			// Agility Abierta
		$this->insert_remove($f,12,($j->Open != 0)?true:false);			// Jumping Abierta
		$this->insert_remove($f,8,($j->Equipos3 != 0)?true:false);		// Agility Equipos (3 mejores)
		$this->insert_remove($f,13,($j->Equipos3 != 0)?true:false);		// Jumping Equipos (3 mejores)
		$this->insert_remove($f,9,($j->Equipos4 != 0)?true:false);		// Agility Equipos (Conjunta)
		$this->insert_remove($f,14,($j->Equipos4 != 0)?true:false);		// Jumping Equipos (Conjunta)
		$this->insert_remove($f,15,($j->KO != 0)?true:false);			// Ronda K.O.
		$this->insert_remove($f,16,($j->Especial != 0)?true:false);		// Manga especial
		$this->myLogger->leave();
	}
	
	/**
	 * Remove all associated Tandas on provided Jornada/Prueba
	 */
	function removeJornada(){
		$p=$this->prueba->ID;
		$j=$this->jornada->ID;
		$str="DELETE FROM Tandas WHERE (Prueba=$p) AND (Jornada=$j)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		return ""; // mark success
	}
}
?>