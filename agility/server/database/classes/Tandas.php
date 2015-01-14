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
	 * Insert a new tanda into database
	 * @param {array} $data
	 */
	function insert($data) {
		
	}
	
	function update($id){
		
	}
	
	function delete($id){
		
	}
	
	function selectByJornada(){
		return $this->__select(
				"*",
				"Tandas",
				"(Jornada={$this->jornada->ID})",
				"Orden ASC",
				""
				);
	}
	
	function selectBySession($id){
		return $this->__select(
				"*",
				"Tandas",
				"(Jornada={$this->jornada->ID}) AND (Sesion=$id)",
				"Orden ASC",
				""
				);
	}
	
	/**
	 * Insert or update Tandas according Jornada Data
	 */
	function populateJornada(){
		$this->insertRemove("PreAgility")
	}
	
	/**
	 * Remove all associated Tandas on provided Jornada
	 */
	function removeJornada(){
		$str="DELETE FROM Tandas WHERE Jornada={$this->jornada->ID}";
		
	}
}
?>