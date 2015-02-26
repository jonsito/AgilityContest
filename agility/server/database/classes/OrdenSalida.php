<?php
/*
OrdenSalida.php

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
require_once("Equipos.php");
require_once("Resultados.php");
require_once("Clasificaciones.php");
require_once("Inscripciones.php");

class OrdenSalida extends DBObject {

	// tablas utilizadas para componer e insertar los idperroes en el string de orden de salida
	public static $default_orden = "BEGIN,END";
	
	var $prueba=null; // {array} prueba data
	var $jornada=null; // {array} jornada data
	var $manga=null; // {array} manga data
	
	/**
	 * Constructor
	 * @param {string} Name for this object
	 * @param {integer} $manga Manga ID
	 * @throws Exception when
	 * - cannot contact database
	 * - invalid manga ID
	 */
	function __construct($file,$manga) {
		parent::__construct($file);
		if ($manga<=0) {
			$this->errormsg="Resultados::Construct invalid Manga ID: $manga";
			throw new Exception($this->errormsg);
		}
		$this->manga=$this->__getArray("Mangas",$manga);
		if (!is_array($this->manga)) {
			$this->errormsg="OrdenSalida::construct(): Cannot get info on manga:$manga";
			throw new Exception($this->errormsg);
		}
		$this->jornada=$this->__getArray("Jornadas",$this->manga['Jornada']);
		if (!is_array($this->jornada)) {
			$this->errormsg="OrdenSalida::construct(): Cannot get info on jornada:{$this->manga['Jornada']} manga:$manga";
			throw new Exception($this->errormsg);
		}
		$this->prueba=$this->__getArray("Pruebas",$this->jornada['Prueba']);
		if (!is_array($this->prueba)) {
			$this->errormsg="OrdenSalida::construct(): Cannot get info on prueba:{$this->jornada['Prueba']} jornada:{$this->manga['Jornada']} manga:$manga";
			throw new Exception($this->errormsg);
		}
	}
	
	/**
	 * Retrieve Mangas.Orden_Salida
	 * @return {string} orden de salida.
	 */
	function getOrden() {
		return ($this->manga['Orden_Salida']==="")?OrdenSalida::$default_orden:$this->manga['Orden_Salida'];
	}
	
	/**
	 * Update Mangas.Orden_Salida with new value
	 * @param {string} $orden new ordensalida
	 * @return {string} "" if success; errormsg on error
	 */
	function setOrden($orden) {
		// TODO: check that $orden matches BEGIN,*,END
		if (preg_match("/BEGIN,([0-9]+,)*END/",$orden)!==1) {
			$this->errormsg="OrdenSalida::setOrden(): orden de salida invalido:'$orden'";
			$this->myLogger->error($this->errormsg);
			return $this->errormsg;
		}
		$sql = "UPDATE Mangas SET Orden_Salida = '$orden' WHERE ( ID={$this->manga['ID']} )";
		$rs = $this->query ($sql);
		// do not call $rs->free() as no resultset returned
		if (!$rs) return $this->error($this->conn->error);
		$this->manga['Orden_Salida']=$orden;
		return "";
	}
	
	/**
	 *  coge el string con el ID del perro y lo inserta al final
	 *  @param {integer} idperro ID perro 
	 */
	function insertIntoList($idperro) {
		$ordensalida=$this->getOrden();
		// lo borramos para evitar una posible doble insercion
		$str = ",$idperro,";
		$nuevoorden = str_replace ( $str, ",", $ordensalida );
		// componemos el tag que hay que insertar
		$myTag="$idperro,END";
		// y lo insertamos en lugar que corresponde
		$ordensalida = str_replace ( "END", $myTag, $nuevoorden );
		// update database
		return $this->setOrden($ordensalida);
	}	
	
	/**
	 * Elimina un idperro del orden de salida
	 * @param {integer} $idperro
	 * @return {string} "" on success; else error message
	 */
	function removeFromList($idperro) {
		$ordensalida=$this->getOrden();
		$str = ",$idperro,";
		$nuevoorden = str_replace ( $str, ",", $ordensalida );
		// update database
		return $this->setOrden($ordensalida);
	}

	function dragAndDrop($from,$to,$where) {
		if ( ($from<=0) || ($to<=0) ) {
			return $this->error("dnd: SrcIDPerro:$from or DestIDPerro:$to are invalid");
		}
		// recuperamos el orden de salida
		$ordensalida = $this->getOrden();
		// extraemos "from" de donde este y lo guardamos
		$str = ",$from,";
		$ordensalida = str_replace ( $str , "," , $ordensalida );
		// insertamos 'from' encima o debajo de 'to' segun el flag 'where'
		$str1 = ",$to,";
		$str2 = ($where==0)? ",$from,$to," : ",$to,$from,";
		$ordensalida = str_replace( $str1 , $str2 , $ordensalida );
		// guardamos el resultado
		$this->setOrden($ordensalida);
		return "";
	}
	
	/**
	 * Obtiene la lista (actualizada) de perros de una manga en el orden de salida correcto
	 * En el proceso de inscripcion ya hemos creado la tabla de resultados, y el orden de salida
	 * con lo que la cosa es sencilla
	 * Se cogen de la tabla de resultados todos los perros de la manga
	 * Se coge el orden de equipos
	 * Se ordena segun categoria,celo,equipo
	 * 
	 * IMPORTANTE: en AgilityContest-2.0 : el campo "Orden_Salida" de la tabla mangas no especifica el orden
	 * real, sino el orden relativo entre perros cuando tienen la misma categoria,celo,equipo; 
	 * De hecho perros de diferente categoria celo y equipo están mezclados, y hace falta que esta funcion
	 * los ordene segun el resultado final deseado
	 * 
	 * La ordenación final la haremos de abajo a arriba
	 */
	function getData() {
		// obtenemos el orden de los equipos
		$eq=$this->__select("*","Equipos","Jornada=$this->jornada['ID']","Orden ASC","");
		if (!is_array($eq)) return $this->error($this->conn->error);
		$equipos=$eq['rows'];
		// obtenemos los perros de la manga
		$rs= $this->__select("*","Resultados","(Manga={$this->manga['ID']}","","");
		if(!is_array($rs)) return $this->error($this->conn->error);
		// recreamos el array de perros anyadiendo el ID como clave
		$p1=array();
		foreach ($rs['rows'] as $perro) { $p1[$perro['ID']]=$perro; }
		// primera pasada: ajustamos los perros segun el orden de salida que figura en Orden_Salida
		$p2=array();
		$orden=explode(',',$this->getOrden());
		foreach ($orden as $perro) {
			if ($perro==="BEGIN") continue;
			if ($perro==="END") continue;
			if (!array_key_exists($perro,$p1)) {
				$this->myLogger->error("El perro $perro esta en el orden de salida pero no en los resultados");
			} else {
				array_push($p2,$p1[$perro]);
			}
		}
		// segunda pasada: ordenar segun el orden de equipos de la jornada
		$p3=array();
		foreach($equipos as $equipo) {
			foreach ($p2 as $perro) {
				if ($perro['Equipo']==$equipo['ID']) array_push($p3,$perro);
			}
		}
		// tercera pasada: ordenar por celo
		usort($p3,function($a,$b){return ($a['Celo']<$b['Celo'])?1:-1;});
		// cuarta pasada: ordenar por categoria
		usort($p3,function($a,$b){return strcmp($a['Categoria'],$b['Categoria']);});
		$result = array();
		$result["total"] = count($p3);
		$result["rows"] = $p3;
		return $result;
	}
	
	/**
	 * Reordena el orden de salida de una manga al azar
	 * @param  	{int} $jornada ID de jornada
	 * @param	{int} $manga ID de manga
	 * @return {string} nuevo orden de salida
	 */
	function random($jornada, $manga) {
		// fase 1 aleatorizamos la manga
		$orden=$this->getOrden();
		$this->myLogger->debug("OrdenSalida::Random() Manga:$manga Orden inicial: \n$orden");
		$str=getInnerString($ordensalida,"BEGIN,",",END");
		if ($str!=="") { // si hay datos, reordena; si no no hagas nada
			$str2 = implode(",",aleatorio(explode(",", $str)));
			$str="BEGIN,$str2,END";
			$this->setOrden($str);
		}
		$orden=$this->getOrden();
		$this->myLogger->debug("OrdenSalida::Random() Manga:$manga Orden final: \n$orden");
		// fase 2: aleatorizamos los equipos de la jornada
		$eq=new Equipos("OrdenSalida::random()",$this->prueba,$this->jornada);
		$eq->random();
		return $orden;
	}
	
	/**
	 * Evalua los resultados de la manga from segun mode
	 * y recalcula el orden de salida de la manga from
	 * @param {integer} $from manga donde buscar resultados
	 * @param {integer} $mode categorias de la manga (L,M,S,MS,LMS,T,LM,ST,LMST)
	 */
	private function invierteResultados($from,$mode) {
		$r =new Resultados("OrdenSalida::invierteResultados", $this->prueba['ID'],$from->ID);
		$data=$r->getResultados($mode)['rows'];
		$size= count($data);
		// recorremos los resultados en orden inverso
		$ordensalida=$this->getOrden();
		// y reinsertamos los perros actualizando el orden
		for($idx=$size-1; $idx>=0; $idx--) {
			$idperro=$data[$idx]['Perro'];
			// lo borramos para evitar una posible doble insercion
			$str = ",$idperro,";
			$nuevoorden = str_replace ( $str, ",", $ordensalida );
			// componemos el tag que hay que insertar
			$str="$idperro,END";
			// y lo insertamos en lugar que corresponde (al final)
			$ordensalida = str_replace ( "END", $str, $nuevoorden );
		}
		// salvamos datos
		$this->setOrden($ordensalida);
	}
	
	/**
	 * Calcula el orden de salida de una manga en funcion del orden inverso al resultado de su manga "hermana"
	 * @return {string} nuevo orden de salida; null on error
	 */
	function reverse($jornada,$manga) {
		$this->myLogger->enter();
		// fase 1: buscamos la "manga hermana"
		$mhandler=new Mangas("OrdenSalida::reverse()",$this->jornada['ID']);
		$hermanas=$mhandler->getHermanas($this->manga['ID']);
		if (!is_array($hermanas)) return $this->error("Error find hermanas info for jornada:{$this->jornada['ID']} and manga:{$this->manga['ID']}");
		if ($hermanas[1]==null) return $this->error("Cannot reverse order: Manga:{$this->manga['ID']} of Jornada:{$this->jornada['ID']} has no brother");
	
		// fase 2: evaluamos resultados de la manga hermana
		$this->myLogger->trace("El orden de salida original para manga:{$this->manga['ID']} jornada:{$this->jornada['ID']} es:\n{$hermanas[0]->Orden_Salida}");
		// En funcion del tipo de recorrido tendremos que leer diversos conjuntos de Resultados
		switch($hermanas[0]->Recorrido) {
			case 0: // Large,medium,small (rsce) Large,medium,small,tiny (rfec)
				$this->invierteResultados($hermanas[1],0);
				$this->invierteResultados($hermanas[1],1);
				$this->invierteResultados($hermanas[1],2);
				if ($this->prueba['RSCE']!=0) $this->invierteResultados($hermanas[1],5);
				break;
			case 1: // Large,medium+small (rsce) Large+medium,Small+tiny (rfec)
				if ($this->prueba['RSCE']==0) {
					$this->invierteResultados($hermanas[1],0);
					$this->invierteResultados($hermanas[1],3);
				} else {
					$this->invierteResultados($hermanas[1],6);
					$this->invierteResultados($hermanas[1],7);
				}
				break;
			case 2: // conjunta L+M+S (rsce) L+M+S+T (rfec)
				if ($this->prueba['RSCE']==0) {
					$this->invierteResultados($hermanas[1],4);
				} else  {
					$this->invierteResultados($hermanas[1],8);
				}
				break;
		}
		$nuevo=$this->getOrden();
		$this->myLogger->trace("El orden de salida nuevo para manga:{$this->manga['ID']} jornada:{$this->jornada['ID']} es:\n$nuevo");
		$this->myLogger->leave();
		return $nuevo;
	}


} // class

?>