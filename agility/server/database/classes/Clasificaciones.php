<?php
/*
clasificaciones.php

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
require_once("Pruebas.php");
require_once("Jornadas.php");
require_once("Mangas.php");
require_once("Resultados.php");

class Clasificaciones extends DBObject {
	protected $prueba;
	protected $jornada;
	protected $ronda;
	protected $mangas;
	protected $mode;

	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {integer} $prueba prueba ID
	 * @param {integer} $jornada jornada ID
	 * @throws Exception if cannot contact database or invalid prueba/jornada ID
	 */
	function __construct($file,$prueba,$jornada) {
		parent::__construct($file);
		if ($prueba<=0) {
			$this->errormsg="Clasificaciones::Construct invalid prueba ID:$prueba";
			throw new Exception($this->errormsg);
		}
		$obj=$this->__getObject("Pruebas",$prueba);
		if (!is_object($obj)) throw new Exception($obj);
		$this->prueba=$obj;
	
		if ($jornada<=0) {
			$this->errormsg="Clasificaciones::Construct invalid jornada ID:$jornada";
			throw new Exception($this->errormsg);
		}
		$obj=$this->__getObject("Jornadas",$jornada);
		if (!is_object($obj)) throw new Exception($obj);
		$this->jornada=$obj;
		$this->mangas=array();
	}
	
	/**
	 * genera la tabla de resultados finales y evalua el orden de clasificacion
	 * @param {array} $c1 clasificacion primera manga
	 * @param {array} $c2 clasificacion segunda manga
	 * @param {integer} $mode Modo 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small
	 */
	function evalFinal($c1,$c2,$mode) {
		$final=array(); // puesto,dorsal, nombre, licencia,categoria,grado, nombreguia, nombreclub,
						// F1,R1,T1,V1,P1,C1,F2,R2,T2,V2,P2,C2, Penalizacion,Calificacion
		// Procesamos la primera manga y generamos una segunda manga "fake"
		foreach($c1['rows'] as $item) {
			$participante=array(
				// datos del participante
				'Participantes' => count($c1['rows']),
				'Dorsal' => $item['Dorsal'],
				'Nombre' => $item['Nombre'],
				'Licencia' => $item['Licencia'],
				'Categoria' => $item['Categoria'],
				'Grado' => $item['Grado'],
				'NombreGuia' => $item['NombreGuia'],
				'NombreClub' => $item['NombreClub'],
				// datos manga 1
				'F1' => $item['Faltas'] + $item['Tocados'],
				'R1' => $item['Rehuses'],
				'T1' => $item['Tiempo'],
				'V1' => $item['Velocidad'],
				'P1' => $item['Penalizacion'],
				'C1' => $item['CShort'],
				'Puesto1' => $item['Puesto'],
				// datos fake manga 2 ( to be filled if so )
				'F2' => 0,
				'R2' => 0,
				'T2' => 0,
				'P2' => 0,
				'V2' => 0,
				'C2' => '',
				'Puesto2' => 0,
				// datos globales
				'Tiempo' => $item['Tiempo'],
				'Penalizacion' => $item['Penalizacion'],
				'Calificacion' => $item['CShort'],
				'Puntos' => '', // to be evaluated
				'Puesto' => 0 // to be evaluated
			);
			$final[$item['Perro']]=$participante;
		}
		if ($c2!=null) { // Procesamos la segunda manga
			foreach($c2['rows'] as $item) {
				if (!isset($final[$item['Perro']])) {
					$this->myLogger->error("El perro con ID:{$item['Perro']} no tiene datos en la primera manga.");
					continue;
				}
				$final[$item['Perro']]['F2'] = $item['Faltas'] + $item['Tocados'];
				$final[$item['Perro']]['R2'] = $item['Rehuses'];
				$final[$item['Perro']]['T2'] = $item['Tiempo'];
				$final[$item['Perro']]['V2'] = $item['Velocidad'];
				$final[$item['Perro']]['P2'] = $item['Penalizacion'];
				$final[$item['Perro']]['C2'] = $item['CShort'];
				$final[$item['Perro']]['Puesto2'] = $item['Puesto'];
				$final[$item['Perro']]['Tiempo'] = $final[$item['Perro']]['T1'] + $final[$item['Perro']]['T2'];
				$final[$item['Perro']]['Penalizacion'] = $final[$item['Perro']]['P1'] + $final[$item['Perro']]['P2'];
				$final[$item['Perro']]['Calificacion'] = '';
				$final[$item['Perro']]['Puntos'] = '';
				// TODO: properly evaluate calificacion y puntos
				$c=$final[$item['Perro']]['Grado'];
				if (($c==="GII") || ($c=="GIII")) {
					$final[$item['Perro']]['Calificacion'] = 
						($final[$item['Perro']]['Penalizacion']==0.0)?'Pto.':'';
				}
			}
		}
		// una vez ordenados, el índice perro ya no tiene sentido, con lo que vamos a eliminarlo
		// y reconstruir el array
		$final2=array();
		foreach($final as $item) array_push($final2,$item);
		$final=$final2;
		
		// re-ordenamos los datos en base a la puntuacion 
		usort($final, function($a, $b) {
			if ( $a['Penalizacion'] == $b['Penalizacion'] )	return ($a['Tiempo'] > $b['Tiempo'])? 1:-1;
			return ( $a['Penalizacion'] > $b['Penalizacion'])?1:-1;
		});
		// calculamos campo "Puesto"
		$size=count($final);
		$puesto=1;
		$last=0;
		for($idx=0;$idx<$size;$idx++) {
			// ajustamos puesto
			$now=100*$final[$idx]['Penalizacion']+$final[$idx]['Tiempo'];
			if ($last!=$now) { $last=$now; $puesto=1+$idx; }
			$final[$idx]['Puesto']=$puesto;
		}
		// Esto es (casi) todo, amigos
		$result=array();
		$result['total']=$size;
		$result['rows']=$final;
		$result['trs1']=$c1['trs'];
		$result['trs2']=$c2['trs'];
		return $result;
	}
	
	/**
	 * mezcla los arrays de resultados de dos categorias en uno solo
	 * @param {array} $c1 array['trs'=> datos de trs,'manga' => datos de la manga,'rows' =>resultados]
	 * @param {array} $c2
	 */
	function combina($c1,$c2) {
		$res=array();
		$res['manga']=$c1['manga']; // realment solo interesan los datos de los jueces
		$res['trs']=$c1['trs']; // los datos de TRS son los mismos para GII y GIII
		$res['rows']=array_merge($c1['rows'],$c2['rows']); // la informacion de 'puesto' se ignorará...
		return $res;	
	}
	
	/**
	 * Evalua las clasificaciones en funcion de los datos pedidos
	 * @param {integer} $rondas bitfield Jornadas::$tipo_ronda
	 * @param {array[{integer}]} $idmangas array con los ID's de las mangas a evaluar
	 * @param {integer} $mode Modo 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small
	 */
	function clasificacionFinal($rondas,$idmangas,$mode) {
		// vamos a ver que tipo de clasificacion nos estan pidiendo
		switch ($rondas) {
			case 0x0001: // pre-agility a una vuelta
				$r1= new Resultados("Clasificaciones::Preagility 1",$this->prueba->ID,$idmangas[0]);
				$c1=$r1->getResultados($mode);
				return $this->evalFinal($c1,null,$mode);
			case 0x0002: // pre-agility a dos vueltas
			case 0x0004: // Grado I
			case 0x0008: // Grado II
			case 0x0010: // Grado III
			case 0x0020: // Open
				$r1=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$this->prueba->ID,$idmangas[0]);
				$r2=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$this->prueba->ID,$idmangas[1]);
				$c1=$r1->getResultados($mode);
				$c2=$r2->getResultados($mode);
				return $this->evalFinal($c1,$c2,$mode);
				break;
			case 0x0018: // Conjunta GII - GIII
				$r1=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$this->prueba->ID,$idmangas[0]); // Agility GII
				$r2=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$this->prueba->ID,$idmangas[1]); // Jumping GII
				$r3=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[2]}",$this->prueba->ID,$idmangas[2]); // Agility GIII
				$r4=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[3]}",$this->prueba->ID,$idmangas[3]); // Jumping GIII
				$c1=$this->combina( $r1->getResultados($mode), $r3->getResultados($mode));
				$c2=$this->combina( $r2->getResultados($mode), $r4->getResultados($mode));
				return $this->evalFinal($c1,$c2,$mode);
			case 0x0040: // equipos 3 mejores de 4
			case 0x0080: // equipos 4 conjunta
			case 0x0100: // ronda KO 1..9 vueltas
				$this->errormsg= "Clasificaciones:: Ronda $ronda is not yet supported";
				return null;
			case 0x0200: // manga especial (una vuelta)
				$r1= new Resultados("Clasificaciones::Manga Especial",$this->prueba->ID,$idmangas[0]);
				$c1=$r1->getResultados($mode);
				return $this->evalFinal($c1,null,$mode);
		}
	}
}
?>