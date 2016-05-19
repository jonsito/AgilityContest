<?php
/*
clasificaciones.php

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
require_once("Pruebas.php");
require_once("Jornadas.php");
require_once("Mangas.php");
require_once("Resultados.php");

class Clasificaciones extends DBObject {
	protected $prueba;
	protected $jornada;
	protected $ronda;
	protected $mangas;

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
	function evalFinal($idmangas,$c1,$c2,$mode) {
		$this->myLogger->enter();
		$m1=$this->__getObject("Mangas",$idmangas[0]);
		$m2=$this->__getObject("Mangas",$idmangas[1]);
		$final=array(); // puesto,dorsal, nombre, licencia,categoria,grado, nombreguia, nombreclub,
						// F1,R1,T1,V1,P1,C1,F2,R2,T2,V2,P2,C2, Penalizacion,Calificacion
		// Procesamos la primera manga y generamos una segunda manga "fake"
		foreach($c1['rows'] as $item) {
			$participante=array(
				// datos del participante
				'Participantes' => count($c1['rows']),
                'Perro' => $item['Perro'],
                'Dorsal' => $item['Dorsal'],
				'Nombre' => $item['Nombre'],
				'Licencia' => $item['Licencia'],
				'Categoria' => $item['Categoria'],
                'Grado' => $item['Grado'],
                'Equipo' => $item['Equipo'],
                'NombreEquipo' => $item['NombreEquipo'],
                'NombreGuia' => $item['NombreGuia'],
                'NombreClub' => $item['NombreClub'],
                'LogoClub' => $item['LogoClub'],
				// datos manga 1
				'F1' => $item['Faltas'] + $item['Tocados'],
				'R1' => $item['Rehuses'],
                'E1' => $item['Eliminado'],
                'N1' => $item['NoPresentado'],
				'T1' => floatval($item['Tiempo']),
				'V1' => $item['Velocidad'],
				'P1' => $item['Penalizacion'],
				'C1' => $item['CShort'],
                'Puesto1' => $item['Puesto'], // puesto conjunto
                'Pcat1' => $item['Pcat'], // puesto por categoria
				// datos fake manga 2 ( to be filled if so )
				'F2' => 0,
				'R2' => 0,
                'E2' => 0,
                'N2' => 0,
				'T2' => 0,
				'V2' => 0,
				'P2' => 400,
				'C2' => '',
                'Puesto2' => 0,
                'Pcat2' => 0,
				// datos globales
				'Tiempo' => $item['Tiempo'],
				'Penalizacion' => $item['Penalizacion'] + 400,
				'Calificacion' => $item['CShort'],
				'Puntos' => '', // to be evaluated
                'Puesto' => 0, // to be evaluated
				'Pcat' => 0 // to be evaluated
			);
			$final[$item['Perro']]=$participante;
		}
		if ($c2!=null) { // Procesamos la segunda manga
			foreach($c2['rows'] as $item) {
				if (!isset($final[$item['Perro']])) {
					$this->myLogger->notice("El perro con ID:{$item['Perro']} no tiene datos en la primera manga.");
					$final[$item['Perro']]= array( // generamos datos de primera manga vacios
						// datos del participante
						'Participantes' => count($c2['rows']),
						'Perro' => $item['Perro'],
						'Dorsal' => $item['Dorsal'],
						'Nombre' => $item['Nombre'],
						'Licencia' => $item['Licencia'],
						'Categoria' => $item['Categoria'],
						'Grado' => $item['Grado'],
						'Equipo' => $item['Equipo'],
						'NombreEquipo' => $item['NombreEquipo'],
						'NombreGuia' => $item['NombreGuia'],
						'NombreClub' => $item['NombreClub'],
						'LogoClub' => $item['LogoClub'],
						// datos manga 1
						'F1' => 0,
						'R1' => 0,
						'E1' => 0,
						'N1' => 0,
						'T1' => 0,
						'V1' => 0,
						'P1' => 400,
						'C1' => '',
						'Puesto1' => 0, // puesto conjunto
						'Pcat1' => 0, // puesto por categoria
						'Puesto' => 0, // to be evaluated
						'Pcat' => 0 // to be evaluated
					);
				}
				$final[$item['Perro']]['F2'] = $item['Faltas'] + $item['Tocados'];
                $final[$item['Perro']]['R2'] = $item['Rehuses'];
                $final[$item['Perro']]['E2'] = $item['Eliminado'];
                $final[$item['Perro']]['N2'] = $item['NoPresentado'];
				$final[$item['Perro']]['T2'] = floatval($item['Tiempo']);
				$final[$item['Perro']]['V2'] = $item['Velocidad'];
				$final[$item['Perro']]['P2'] = $item['Penalizacion'];
				$final[$item['Perro']]['C2'] = $item['CShort'];
                $final[$item['Perro']]['Puesto2'] = $item['Puesto'];
                $final[$item['Perro']]['Pcat2'] = $item['Pcat'];
				$final[$item['Perro']]['Tiempo'] = $final[$item['Perro']]['T1'] + $final[$item['Perro']]['T2'];
				$final[$item['Perro']]['Penalizacion'] = $final[$item['Perro']]['P1'] + $final[$item['Perro']]['P2'];
				$final[$item['Perro']]['Calificacion'] = '';
				$final[$item['Perro']]['Puntos'] = '';
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
		// calculamos campo "Puesto", "Calificacion" y Puntos
        $puestocat=array( 'C'=>1, 'L' => 1, 'M'=>1, 'S'=>1, 'T'=>1); // ultimo puesto por cada categoria
        $lastcat=array( 'C'=>0, 'L' => 0, 'M'=>0, 'S'=>0, 'T'=>0);  // ultima puntuacion por cada categoria
        $countcat=array( 'C'=>0, 'L' => 0, 'M'=>0, 'S'=>0, 'T'=>0); // perros contabilizados de cada categoria
		$size=count($final);
		for($idx=0;$idx<$size;$idx++) {
            // vemos la categoria y actualizamos contadores de categoria
            $cat=$final[$idx]['Categoria'];
            $countcat['C']++; // Conjunta
            $countcat[$cat]++; // Por categoria

            // obtenemos la penalizacion del perro actual
            $now=100*$final[$idx]['Penalizacion']+$final[$idx]['Tiempo'];

			// ajustamos puesto conjunto y guardamos resultado
			if ($lastcat['C']!=$now) { $lastcat['C']=$now; $puestocat['C']=$countcat['C']; }
			$final[$idx]['Puesto']=$puestocat['C'];

            // ajustamos puesto por categoria y guardamos resultado
            if ($lastcat[$cat]!=$now) { $lastcat[$cat]=$now; $puestocat[$cat]=$countcat[$cat]; }
            $final[$idx]['Pcat']=$puestocat[$cat];

			// on special journeys do not evaluate calification
			if($this->jornada->Equipos3!=0) continue;
			if($this->jornada->Equipos4!=0) continue;
			if($this->jornada->KO!=0) continue;
			if($this->jornada->Open!=0) continue;
            // evaluamos calificacion y puntos en funcion de la federacion y de si es o no selectiva
			$fed=Federations::getFederation(intval($this->prueba->RSCE));
			$fed->evalFinalCalification($this->prueba,$this->jornada,$m1,$m2,$c1,$c2,$final[$idx],$puestocat);
		}

		// Esto es (casi) t odo, amigos
		$result=array();
		$result['total']=$size;
		$result['rows']=$final;
		$result['trs1']=$c1['trs'];
		$result['trs2']=$c2['trs'];
        $result['jueces']=array($c1['manga']->NombreJuez1,$c1['manga']->NombreJuez2);
		return $result;
	}

	/**
	 * genera la tabla de resultados finales y evalua el orden de clasificacion
	 * @param {array} $c1 clasificacion primera manga
	 * @param {array} $c2 clasificacion segunda manga
	 * @param {integer} $mode Modo 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small
	 */
	function evalPenalizacionFinal($idmangas,$c1,$c2,$mode) {
		$this->myLogger->enter();
		$m1=$this->__getObject("Mangas",$idmangas[0]);
		$m2=$this->__getObject("Mangas",$idmangas[1]);
		$final=array(); // puesto,dorsal, nombre, licencia,categoria,grado, nombreguia, nombreclub,
		// F1,R1,T1,V1,P1,C1,F2,R2,T2,V2,P2,C2, Penalizacion,Calificacion
		// Procesamos la primera manga y generamos una segunda manga "fake"
		foreach($c1['rows'] as $item) {
			$participante=array(
				// datos del participante
				'Participantes' => count($c1['rows']),
				'Perro' => $item['Perro'],
				// datos manga 1
				'T1' => floatval($item['Tiempo']),
				'P1' => $item['Penalizacion'],
				// datos fake manga 2 ( to be filled if so )
				'T2' => 0,
				'P2' => 400,
				// datos globales
				'Tiempo' => $item['Tiempo'],
				'Penalizacion' => $item['Penalizacion'] + 400
			);
			$final[$item['Perro']]=$participante;
		}
		if ($c2!=null) { // Procesamos la segunda manga
			foreach($c2['rows'] as $item) {
				if (!isset($final[$item['Perro']])) {
					$this->myLogger->notice("El perro con ID:{$item['Perro']} no tiene datos en la primera manga.");
					$final[$item['Perro']]= array( // generamos datos de primera manga vacios
						// datos del participante
						'Participantes' => count($c2['rows']),
						'Perro' => $item['Perro'],
						// datos manga 1
						'T1' => 0,
						'P1' => 400
					);
				}
				$final[$item['Perro']]['T2'] = floatval($item['Tiempo']);
				$final[$item['Perro']]['P2'] = $item['Penalizacion'];
				$final[$item['Perro']]['Tiempo'] = $final[$item['Perro']]['T1'] + $final[$item['Perro']]['T2'];
				$final[$item['Perro']]['Penalizacion'] = $final[$item['Perro']]['P1'] + $final[$item['Perro']]['P2'];
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

		// Esto es (casi) todo, amigos
		$result=array();
		$result['total']=count($final);
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
				return $this->evalFinal($idmangas,$c1,null,$mode);
			case 0x0002: // pre-agility a dos vueltas
			case 0x0004: // Grado I
			case 0x0008: // Grado II
			case 0x0010: // Grado III
			case 0x0020: // Open
				$r1=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$this->prueba->ID,$idmangas[0]); // Agility
				$r2=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$this->prueba->ID,$idmangas[1]); // Jumping
				$c1=$r1->getResultados($mode);
				$c2=$r2->getResultados($mode);
				return $this->evalFinal($idmangas,$c1,$c2,$mode);
				break;
			case 0x0018: // Conjunta GII - GIII
				$r1=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$this->prueba->ID,$idmangas[0]); // Agility GII
				$r2=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$this->prueba->ID,$idmangas[1]); // Jumping GII
				$r3=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[2]}",$this->prueba->ID,$idmangas[2]); // Agility GIII
				$r4=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[3]}",$this->prueba->ID,$idmangas[3]); // Jumping GIII
				$c1=$this->combina( $r1->getResultados($mode), $r3->getResultados($mode));
				$c2=$this->combina( $r2->getResultados($mode), $r4->getResultados($mode));
				return $this->evalFinal($idmangas,$c1,$c2,$mode);
			case 0x0400: // equipos 2 mejores de 3
			case 0x0040: // equipos 3 mejores de 4
                $r1=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$this->prueba->ID,$idmangas[0]); // Agility Equipos best
                $r2=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$this->prueba->ID,$idmangas[1]); // Jumping Equipos best
                $c1=$r1->getResultados($mode);
                $c2=$r2->getResultados($mode);
                return $this->evalFinal($idmangas,$c1,$c2,$mode);
			case 0x0800: // equipos 2 conjunta
			case 0x1000: // equipos 3 conjunta
			case 0x0080: // equipos 4 conjunta
                $r1=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$this->prueba->ID,$idmangas[0]); // Agility Equipos combined
                $r2=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$this->prueba->ID,$idmangas[1]); // Jumping Equipos combined
                $c1=$r1->getResultados($mode);
                $c2=$r2->getResultados($mode);
                return $this->evalFinal($idmangas,$c1,$c2,$mode);
			case 0x0100: // ronda KO 1..8 vueltas
				$this->errormsg= "Clasificaciones:: Ronda $rondas is not yet supported";
				return null;
			case 0x0200: // manga especial (una vuelta)
				$r1= new Resultados("Clasificaciones::Manga Especial",$this->prueba->ID,$idmangas[0]);
				$c1=$r1->getResultados($mode);
				return $this->evalFinal($idmangas,$c1,null,$mode);
		}
        // arriving here means error
        return null;
	}

	/**
	 * Evalua el puesto en que ha quedado un perro determinado en la clasificacion final
	 * hay que tener en cuenta que en esta clasificacion, el perro en cuestion todavia
	 * no tiene los datos de (al menos) una manga almacenados, con lo que si nos lo encontramos,
	 * habrá que quitar "1 pendiente" y substituirlo por los datos que tenemos
	 *
     * Esta funcion no tiene en cuenta pruebas por equipos ni ko. simplemente considera las dos primeras
     * mangas (o solo la primera, si no hay manga hermana
     *
	 *@param {integer} $mode Modo 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small
	 *@param {array} $perro datos del perro (Perro,Faltas,Tocados,Rehuses,Eliminado,NoPresentado,Tiempo,IDManga)
	 *@return {array} requested data or error
	 */
	function getPuestoFinal($mode,$perro) {
		$result=null;
		$myManga=$perro['Manga'];
        // buscamos la manga hermana
        $mng=new Mangas("getPuestoFinal",$this->jornada->ID);
        $hermanas=$mng->getHermanas($myManga);
        $id1=intval($hermanas[0]->ID);
        $id2=0;
        $r1= new Resultados("Clasificaciones::getPuestoFinal",$this->prueba->ID,$id1);
        $c1=$r1->getPenalizaciones($mode,($myManga==$id1)?$perro:null);
        $c2=null;
        if($hermanas[1]!=null) {
            $id2=intval($hermanas[1]->ID);
            $r2= new Resultados("Clasificaciones::getPuestoFinal",$this->prueba->ID,$id2);
            $c2=$r2->getPenalizaciones($mode,($myManga==$id2)?$perro:null);
        }
        $result= $this->evalPenalizacionFinal(array($id1,$id2),$c1,$c2,$mode);

		if($result==null) return null; // null result -> error
		if (!is_array($result)) {
			$this->myLogger->error($result);
			return $result;
		}

		// iterate result to find our dog
		$table=$result['rows'];
		$size=$result['total'];
		$idperro=intval($perro['Perro']);
		// en el caso de que todavia no haya clasificaciones, la tabla esta vacia y nuestro perro va el primero :-)
		if ($size==0) {
			return array( 'success'=>true,'puesto'=>1,'penalizacion'=>0 /* no easy way to evaluate */);
		}
		// buscamos el puesto en el que finalmente ha quedado $myPerro y lo retornamos
		for ($idx=0;$idx<$size;$idx++ ){
			if ($table[$idx]['Perro']!=$idperro) continue;
			return array( 'success'=>true,'puesto'=>(1+$idx),'penalizacion'=>$table[$idx]['Penalizacion']);
		}
		//arriving here means error: perro not found
		return $this->error("Perro:$idperro not found in clasificaciones::getPuesto()");
	}
}
?>