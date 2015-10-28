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
			0	=> array('Tipo'=>0,		'TipoManga'=>0,		'Nombre'=>'-- Sin especificar --',  'Categoria'=>'-',	'Grado'=>'-'),
			// en pre-agility no hay categorias
			1	=> array('Tipo'=>1,		'TipoManga'=> 1,	'Nombre'=>'Pre-Agility 1',			'Categoria'=>'-LMST','Grado'=>'P.A.'),
			2	=> array('Tipo'=>2,		'TipoManga'=> 2,	'Nombre'=>'Pre-Agility 2',			'Categoria'=>'-LMST','Grado'=>'P.A.'),
			3	=> array('Tipo'=>3,		'TipoManga'=> 3,	'Nombre'=>'Agility-1 GI Large',		'Categoria'=>'L',	'Grado'=>'GI'),
			4	=> array('Tipo'=>4,		'TipoManga'=> 3,	'Nombre'=>'Agility-1 GI Medium',	'Categoria'=>'M',	'Grado'=>'GI'),
			5	=> array('Tipo'=>5,		'TipoManga'=> 3,	'Nombre'=>'Agility-1 GI Small',		'Categoria'=>'S',	'Grado'=>'GI'),
			6	=> array('Tipo'=>6,		'TipoManga'=> 4,	'Nombre'=>'Agility-2 GI Large',		'Categoria'=>'L',	'Grado'=>'GI'),
			7	=> array('Tipo'=>7,		'TipoManga'=> 4,	'Nombre'=>'Agility-2 GI Medium',	'Categoria'=>'M',	'Grado'=>'GI'),
			8	=> array('Tipo'=>8,		'TipoManga'=> 4,	'Nombre'=>'Agility-2 GI Small',		'Categoria'=>'S',	'Grado'=>'GI'),
			9	=> array('Tipo'=>9,		'TipoManga'=> 5,	'Nombre'=>'Agility GII Large',		'Categoria'=>'L',	'Grado'=>'GII'),
			10	=> array('Tipo'=>10,	'TipoManga'=> 5,	'Nombre'=>'Agility GII Medium',		'Categoria'=>'M',	'Grado'=>'GII'),
			11	=> array('Tipo'=>11,	'TipoManga'=> 5,	'Nombre'=>'Agility GII Small',		'Categoria'=>'S',	'Grado'=>'GII'),
			12	=> array('Tipo'=>12,	'TipoManga'=> 6,	'Nombre'=>'Agility GIII Large',		'Categoria'=>'L',	'Grado'=>'GIII'),
			13	=> array('Tipo'=>13,	'TipoManga'=> 6,	'Nombre'=>'Agility GIII Medium',	'Categoria'=>'M',	'Grado'=>'GIII'),
			14	=> array('Tipo'=>14,	'TipoManga'=> 6,	'Nombre'=>'Agility GIII Small',		'Categoria'=>'S',	'Grado'=>'GIII'),
			15	=> array('Tipo'=>15,	'TipoManga'=> 7,	'Nombre'=>'Agility Open Large',		'Categoria'=>'L',	'Grado'=>'-'),
			16	=> array('Tipo'=>16,	'TipoManga'=> 7,	'Nombre'=>'Agility Open Medium',	'Categoria'=>'M',	'Grado'=>'-'),
			17	=> array('Tipo'=>17,	'TipoManga'=> 7,	'Nombre'=>'Agility Open Small',		'Categoria'=>'S',	'Grado'=>'-'),
			18	=> array('Tipo'=>18,	'TipoManga'=> 8,	'Nombre'=>'Agility Eq. 3 Large',	'Categoria'=>'L',	'Grado'=>'-'),
			19	=> array('Tipo'=>19,	'TipoManga'=> 8,	'Nombre'=>'Agility Eq. 3 Medium',	'Categoria'=>'M',	'Grado'=>'-'),
			20	=> array('Tipo'=>20,	'TipoManga'=> 8,	'Nombre'=>'Agility Eq. 3 Small',	'Categoria'=>'S',	'Grado'=>'-'),
        // en jornadas por equipos conjunta se mezclan categorias M y S
			21	=> array('Tipo'=>21,	'TipoManga'=> 9,	'Nombre'=>'Ag. Equipos 4 Large',	'Categoria'=>'L',	'Grado'=>'-'),
			22	=> array('Tipo'=>22,	'TipoManga'=> 9,	'Nombre'=>'Ag. Equipos 4 Med/Small','Categoria'=>'MS',	'Grado'=>'-'),
			23	=> array('Tipo'=>23,	'TipoManga'=> 10,	'Nombre'=>'Jumping GII Large',		'Categoria'=>'L',	'Grado'=>'GII'),
			24	=> array('Tipo'=>24,	'TipoManga'=> 10,	'Nombre'=>'Jumping GII Medium',		'Categoria'=>'M',	'Grado'=>'GII'),
			25	=> array('Tipo'=>25,	'TipoManga'=> 10,	'Nombre'=>'Jumping GII Small',		'Categoria'=>'S',	'Grado'=>'GII'),
			26	=> array('Tipo'=>26,	'TipoManga'=> 11,	'Nombre'=>'Jumping GIII Large',		'Categoria'=>'L',	'Grado'=>'GIII'),
			27	=> array('Tipo'=>27,	'TipoManga'=> 11,	'Nombre'=>'Jumping GIII Medium',	'Categoria'=>'M',	'Grado'=>'GIII'),
			28	=> array('Tipo'=>28,	'TipoManga'=> 11,	'Nombre'=>'Jumping GIII Small',		'Categoria'=>'S',	'Grado'=>'GIII'),
			29	=> array('Tipo'=>29,	'TipoManga'=> 12,	'Nombre'=>'Jumping Open Large',		'Categoria'=>'L',	'Grado'=>'-'),
			30	=> array('Tipo'=>30,	'TipoManga'=> 12,	'Nombre'=>'Jumping Open Medium',	'Categoria'=>'M',	'Grado'=>'-'),
			31	=> array('Tipo'=>31,	'TipoManga'=> 12,	'Nombre'=>'Jumping Open Small',		'Categoria'=>'S',	'Grado'=>'-'),
			32	=> array('Tipo'=>32,	'TipoManga'=> 13,	'Nombre'=>'Jumping Eq. 3 Large',	'Categoria'=>'L',	'Grado'=>'-'),
			33	=> array('Tipo'=>33,	'TipoManga'=> 13,	'Nombre'=>'Jumping Eq. 3 Medium',	'Categoria'=>'M',	'Grado'=>'-'),
			34	=> array('Tipo'=>34,	'TipoManga'=> 13,	'Nombre'=>'Jumping Eq. 3 Small',	'Categoria'=>'S',	'Grado'=>'-'),
			// en jornadas por equipos conjunta se mezclan categorias M y S
			35	=> array('Tipo'=>35,	'TipoManga'=> 14,	'Nombre'=>'Jp. Equipos 4 Large',	'Categoria'=>'L',	'Grado'=>'-'),
			36	=> array('Tipo'=>36,	'TipoManga'=> 14,	'Nombre'=>'Jp. Equipos 4 Med/Small','Categoria'=>'MS',	'Grado'=>'-'),
			// en las rondas KO, los perros compiten todos contra todos
			37	=> array('Tipo'=>37,	'TipoManga'=> 15,	'Nombre'=>'Manga K.O.',				'Categoria'=>'LMST','Grado'=>'-'),
			38	=> array('Tipo'=>38,	'TipoManga'=> 16,	'Nombre'=>'Manga Especial Large',	'Categoria'=>'L',	'Grado'=>'-'),
			39	=> array('Tipo'=>39,	'TipoManga'=> 16,	'Nombre'=>'Manga Especial Medium',	'Categoria'=>'M',	'Grado'=>'-'),
			40	=> array('Tipo'=>40,	'TipoManga'=> 16,	'Nombre'=>'Manga Especial Small',	'Categoria'=>'S',	'Grado'=>'-'),
	
			// "Tiny" support for Pruebas RFEC
			41	=> array('Tipo'=>41,	'TipoManga'=> 3,	'Nombre'=>'Agility-1 GI Tiny',		'Categoria'=>'T',	'Grado'=>'GI'),
			42	=> array('Tipo'=>42,	'TipoManga'=> 4,	'Nombre'=>'Agility-2 GI Tiny',		'Categoria'=>'T',	'Grado'=>'GI'),
			43	=> array('Tipo'=>43,	'TipoManga'=> 5,	'Nombre'=>'Agility GII Tiny',		'Categoria'=>'T',	'Grado'=>'GII'),
			44	=> array('Tipo'=>44,	'TipoManga'=> 6,	'Nombre'=>'Agility GIII Tiny',		'Categoria'=>'T',	'Grado'=>'GIII'), // no existe
			45	=> array('Tipo'=>45,	'TipoManga'=> 7,	'Nombre'=>'Agility Open Tiny',		'Categoria'=>'T',	'Grado'=>'-'),
			46	=> array('Tipo'=>46,	'TipoManga'=> 8,	'Nombre'=>'Agility Eq. 3 Tiny',		'Categoria'=>'T',	'Grado'=>'-'),
			// en equipos4  RFEC agrupamos por LM y ST
			47	=> array('Tipo'=>47,	'TipoManga'=> 9,	'Nombre'=>'Ag. Equipos 4 Large/Medium',	'Categoria'=>'LM',	'Grado'=>'-'),
			48	=> array('Tipo'=>48,	'TipoManga'=> 9,	'Nombre'=>'Ag. Equipos 4 Small/Tiny','Categoria'=>'ST',		'Grado'=>'-'),
			49	=> array('Tipo'=>49,	'TipoManga'=> 10,	'Nombre'=>'Jumping GII Tiny',		'Categoria'=>'T',		'Grado'=>'GII'),
			50	=> array('Tipo'=>50,	'TipoManga'=> 11,	'Nombre'=>'Jumping GIII Tiny',		'Categoria'=>'T',		'Grado'=>'GIII'), // no existe
			51	=> array('Tipo'=>51,	'TipoManga'=> 12,	'Nombre'=>'Jumping Open Tiny',		'Categoria'=>'T',		'Grado'=>'-'),
			52	=> array('Tipo'=>52,	'TipoManga'=> 13,	'Nombre'=>'Jumping Eq. 3 Tiny',		'Categoria'=>'T',		'Grado'=>'-'),
			53	=> array('Tipo'=>53,	'TipoManga'=> 14,	'Nombre'=>'Jp. Equipos 4 Large/Medium',	'Categoria'=>'LM',	'Grado'=>'-'),
			54	=> array('Tipo'=>54,	'TipoManga'=> 14,	'Nombre'=>'Jp. Equipos 4 Small/Tiny','Categoria'=>'ST',		'Grado'=>'-'),
			55	=> array('Tipo'=>55,	'TipoManga'=> 16,	'Nombre'=>'Manga Especial Tiny',	'Categoria'=>'T',		'Grado'=>'-'),
	);

	// matriz de modos a evaluar en funcion del tipo de recorrido y de la tanda
	// recorridos:
	// RSCE (0: l/m/s separados 1: l/m+s 2: l+m+s conjunto) RFEC( 3:l/m/s/t separados  4:l+m/s+t  5:l+m+s+t conjunto )
	// modos:
	// 0:large 1:medium 2:small 3:m+s 4:l+m+s 5:tiny 6:l+m 7:s+t 8:l+m+s+t -1:no valido
	public static $modes_by_tanda=array(
			0	=> array(-1, -1, -1, -1,-1,-1, '-- Sin especificar --'), // tanda definida por el usuario
			// en pre-agility no hay categorias
			1	=> array(-1, -1,  4, -1, -1,  8, 'Pre-Agility 1'), // en pre agility-compiten todos juntos
			2	=> array(-1, -1,  4, -1, -1,  8, 'Pre-Agility 2'),
			3	=> array( 0,  0,  4,  0,  6,  8, 'Agility Grado I Manga 1'/* Large */),
			4	=> array( 1,  3,  4,  1,  6,  8, 'Agility Grado I Manga 1'/* Medium */),
			5	=> array( 2,  3,  4,  2,  7,  8, 'Agility Grado I Manga 1'/* Small */),
			6	=> array( 0,  0,  4,  0,  6,  8, 'Agility Grado I Manga 2'/* Large */),
			7	=> array( 1,  3,  4,  1,  6,  8, 'Agility Grado I Manga 2'/* Medium */),
			8	=> array( 2,  3,  4,  2,  7,  8, 'Agility Grado I Manga 2'/* Small */),
			9	=> array( 0,  0,  4,  0,  6,  8, 'Agility Grado II'/* Large */),
			10	=> array( 1,  3,  4,  1,  6,  8, 'Agility Grado II'/* Medium */),
			11	=> array( 2,  3,  4,  2,  7,  8, 'Agility Grado II'/* Small */),
			12	=> array( 0,  0,  4,  0,  6,  8, 'Agility Grado III'/* Large */),
			13	=> array( 1,  3,  4,  1,  6,  8, 'Agility Grado III'/* Medium */),
			14	=> array( 2,  3,  4,  2,  7,  8, 'Agility Grado III'/* Small */),
			15	=> array( 0,  0,  4,  0,  6,  8, 'Agility Abierta'/* Large */),
			16	=> array( 1,  3,  4,  1,  6,  8, 'Agility Abierta'/* Medium */),
			17	=> array( 2,  3,  4,  2,  7,  8, 'Agility Abierta'/* Small */),
			18	=> array( 0,  0,  4,  0,  6,  8, 'Agility Eq. (3 mejores)'/* Large */),	// en equipos compiten l y m juntos
			19	=> array(-1,  3,  4, -1,  6,  8, 'Agility Eq. (3 mejores)'/* Medium */),
			20	=> array(-1,  3,  4, -1,  7,  8, 'Agility Eq. (3 mejores)'/* Small */), // en equipos compiten s y t juntos
			21	=> array( 0,  0,  4,  0,  6,  8, 'Agility. Eq. (4 conjunta)'/* Large */),
			// en jornadas por equipos conjunta RSCE se mezclan categorias M y S
			22	=> array(-1,  3,  4, -1,  6,  8, 'Agility Eq. (4 conjunta)'/* Med/Small */),
			23	=> array( 0,  0,  4,  0,  6,  8, 'Jumping Grado II'/* Large */),
			24	=> array( 1,  3,  4,  1,  6,  8, 'Jumping Grado II'/* Medium */),
			25	=> array( 2,  3,  4,  2,  7,  8, 'Jumping Grado II'/* Small */),
			26	=> array( 0,  0,  4,  0,  6,  8, 'Jumping Grado III'/* Large */),
			27	=> array( 1,  3,  4,  1,  6,  8, 'Jumping Grado III'/* Medium */),
			28	=> array( 2,  3,  4,  2,  7,  8, 'Jumping Grado III'/* Small */),
			29	=> array( 0,  0,  4,  0,  6,  8, 'Jumping Abierta'/* Large */),
			30	=> array( 1,  3,  4,  1,  6,  8, 'Jumping Abierta'/* Medium */),
			31	=> array( 2,  3,  4,  2,  7,  8, 'Jumping Abierta'/* Small */),
			32	=> array( 0,  0,  4,  0,  6,  8, 'Jumping Eq. (3 mejores)'/* Large */),
			33	=> array(-1,  3,  4, -1,  6,  8, 'Jumping Eq. (3 mejores)'/* Medium */),
			34	=> array(-1,  3,  4, -1,  7,  8, 'Jumping Eq. (3 mejores)'/* Small */),
			// en jornadas por equipos conjunta se mezclan categorias M y S
			35	=> array( 0,  0,  4,  0,  6,  8, 'Jumping. Eq. (4 conjunta)'/* Large */),
			36	=> array(-1,  3,  4, -1,  6,  8, 'Jumping. Eq. (4 conjunta)'/* Med/Small */),
			// en las rondas KO, los perros compiten todos contra todos
			37	=> array(-1, -1,  4, -1, -1,  8, 'Manga K.O.'),
			38	=> array( 0,  0,  4,  0,  6,  8, 'Manga Especial'/* Large */),
			39	=> array( 1,  3,  4,  1,  6,  8, 'Manga Especial'/* Medium */),
			40	=> array( 2,  3,  4,  2,  7,  8, 'Manga Especial'/* Small */),
				
			// "Tiny" support for Pruebas RFEC
			41	=> array( 5,  7,  8,  5,  7,  8, 'Agility-1 GI' /* Tiny */),
			42	=> array( 5,  7,  8,  5,  7,  8, 'Agility-2 GI' /* Tiny */),
			43	=> array( 5,  7,  8,  5,  7,  8, 'Agility GII' /* Tiny */),
			44	=> array( 5,  7,  8,  5,  7,  8, 'Agility GIII' /* Tiny */),
			45	=> array( 5,  7,  8,  5,  7,  8, 'Agility Open' /* Tiny */),
			46	=> array( 5,  7,  8,  5,  7,  8, 'Agility Eq. 3' /* Tiny */),
			// en equipos4  RFEC agrupamos por LM y ST
			47	=> array( -1, 6,  8,  -1, 6,  8, 'Ag. Equipos 4'/* Large/Medium*/),
			48	=> array( -1, 7,  8,  -1, 7,  8, 'Ag. Equipos 4'/* Small/Tiny*/),
			49	=> array( 5,  7,  8,  5,  7,  8, 'Jumping GII' /* Tiny */),
			50	=> array( 5,  7,  8,  5,  7,  8, 'Jumping GIII' /* Tiny */),
			51	=> array( 5,  7,  8,  5,  7,  8, 'Jumping Open' /* Tiny */),
			52	=> array( 5,  7,  8,  5,  7,  8, 'Jumping Eq. 3' /* Tiny */),
			53	=> array( -1, 6,  8,  -1, 6,  8, 'Jp. Equipos 4'/* Large/Medium*/),
			54	=> array( -1, 7,  8,  -1, 7,  8, 'Jp. Equipos 4'/* Small/Tiny*/),
			55	=> array( 5,  7,  8,  5,  7,  8, 'Manga Especial' /* Tiny */),
	);
	
	
	/**
	 * return every array items that matches with provided key
	 * @param {string} $key
	 * @param {value} $value
	 * @return {array} List of Tandas that matches with requested key/value pair
	*/
	static function getTandasInfo($key,$value) {
		$res=array();
		if (!array_key_exists($key,Tandas::$tipo_tanda[0])) return $res; // key not found: return empty array
		foreach(Tandas::$tipo_tanda as $item) {
			if ($item[$key]==$value) array_push($res,$item);
		}
		return $res;
	}
	
	/**
	 * Retrieve mode based on rsce recorrido and tanda type
	 * @param {integer} $rsce 0:rsce 1:rfec
	 * @param {integer} $recorrido 0:separate 1:mixed 2:grouped
	 * @param {integer} $tanda Tanda Type
	 */
	static function getModeByTanda($rsce,$recorrido,$tanda){
		if (!array_key_exists($tanda,Tandas::$modes_by_tanda)) return -1;
		return Tandas::$modes_by_tanda[$tanda][3*$rsce+$recorrido];
	}
	
	/**
	 * Retrieve Manga String based on and tanda type
	 * @param {integer} $tanda Tanda Type
	 */
	static function getMangaStringByTanda($tanda){
		if (!array_key_exists($tanda,Tandas::$modes_by_tanda)) return "";
		return Tandas::$modes_by_tanda[$tanda][6];
	}
	
	/**
	 * Retrieve Tanda's name by type
	 * @param {integer} $tanda type
	 * @return NULL if not found, else requested name
	 */
	static function getTandaString($tanda){ 
		if (!array_key_exists($tanda,Tandas::$tipo_tanda)) return null;
		return Tandas::$tipo_tanda[$tanda]['Nombre']; 
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
		$o=($obj!=null)?1+intval($obj->Last):1; // evaluate latest in order
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
			$this->myLogger->info("Analizando tanda \n".json_encode($tanda));
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
				$perrosmanga=$os->getData(false); // false: do not include extra team information rows
			}
			// OK ya tenemos la lista ordenada de los perros de cada manga
			// Ahora vamos a sacar la lista por cada tanda
            foreach($perrosmanga['rows'] as &$perro) {
                // si el perro no pertenece a la tanda skip (comprobar categoria)
                if (strpos($tanda['Categoria'],$perro['Categoria'])===false) continue;
                $perro['Tanda']=$tanda['Nombre'];
                $perro['ID']=$tanda['ID']; // replace resultadoID por tandaID TODO: revise why
                if ($pendientes==0) { // include all
                    $perro['Logo']=$club->getLogoName('NombreClub',$perro['NombreClub']);
                    array_push($rows,$perro);
                    continue;
                }
                if ($perro['Pendiente']==0) continue; // not pendiente: skip
                if ($count > 0) {  // not yet at count: insert
                    $count--;
                    $perro['Logo']=$club->getLogoName('NombreClub',$perro['NombreClub']);
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

	private function insert_remove($rsce,$tipomanga,$oper) {
		foreach( $this->getTandasInfo('TipoManga',$tipomanga) as $item) {
            $tipo=$item['Tipo'];
			if( ($rsce==0) && ($item['Categoria']==='T') ) {
				// remove every "tiny" tandas on RSCE contests
				$this->removeFromList($tipo);
				continue;
			}
			if( ($rsce!=0) && ($item['Grado']==='GIII') ) {
				// remove every "Grado III" tandas on non RSCE contests
				$this->removeFromList($tipo);
				continue;
			}
            // equipos 4 (tipomanga=9,14) tienen tratamiento especial
            // pues mezclan categorias
            if( ($rsce==0) && ( ($tipomanga==9)||($tipomanga==14) ) ) {
                // remove every "uca/rfec" tandas on RSCE contests for equipos4 mode
                if (($tipo==47) || ($tipo==48) || ($tipo==53) || ($tipo==54)) {
                    $this->removeFromList($tipo);
                    continue;
                }
            }
            if( ($rsce!=0) && ( ($tipomanga==9)||($tipomanga==14) ) ) {
                // remove every RSCE in contests for "uca/rfec" equipos4 mode
                if (($tipo==21) || ($tipo==21) || ($tipo==35) || ($tipo==36)) {
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
			if ($obj!=null) $last=1+intval($obj->Last); // evaluate latest in order
			// check for already inserted into Tandas
			$obj=$this->__selectObject("*","Tandas","(Prueba=$p) AND (Jornada=$j) AND (Tipo=$tipo)");
			if ($obj==null) { // insert into list at end.
				$n=Tandas::$tipo_tanda[$tipo]['Nombre'];
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
		$r=$p->RSCE;
		// actualizamos la lista de tandas de cada ronda
		
		// preagility necesita tratamiento especial
		if (($j->PreAgility2 != 0)){ // preagility2 also handles preagility1
			$this->insert_remove($r,1,true);	// Pre-Agility Manga 1
			$this->insert_remove($r,2,true);	// Pre-Agility Manga 2
		} else 	if (($j->PreAgility != 0)){
			$this->insert_remove($r,1,true);	// Pre-Agility Manga 1
			$this->insert_remove($r,2,false);	// Pre-Agility Manga 2
		} else {
            $this->insert_remove($r,1,false);	// Pre-Agility Manga 1
            $this->insert_remove($r,2,false);	// Pre-Agility Manga 2
        }
		$this->insert_remove($r,3,($j->Grado1 != 0)?true:false);		// Agility Grado I Manga 1
		$this->insert_remove($r,4,($j->Grado1 != 0)?true:false);		// Agility Grado I Manga 2
		$this->insert_remove($r,5,($j->Grado2 != 0)?true:false);		// Agility Grado II
		$this->insert_remove($r,10,($j->Grado2 != 0)?true:false);		// Jumping Grado II
		$this->insert_remove($r,6,($j->Grado3 != 0)?true:false);		// Agility Grado III
		$this->insert_remove($r,11,($j->Grado3 != 0)?true:false);		// Jumping Grado III
		$this->insert_remove($r,7,($j->Open != 0)?true:false);			// Agility Abierta
		$this->insert_remove($r,12,($j->Open != 0)?true:false);			// Jumping Abierta
		$this->insert_remove($r,8,($j->Equipos3 != 0)?true:false);		// Agility Equipos (3 mejores)
		$this->insert_remove($r,13,($j->Equipos3 != 0)?true:false);		// Jumping Equipos (3 mejores)
		$this->insert_remove($r,9,($j->Equipos4 != 0)?true:false);		// Agility Equipos (Conjunta)
		$this->insert_remove($r,14,($j->Equipos4 != 0)?true:false);		// Jumping Equipos (Conjunta)
		$this->insert_remove($r,15,($j->KO != 0)?true:false);			// Ronda K.O.
		$this->insert_remove($r,16,($j->Especial != 0)?true:false);		// Manga especial
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
