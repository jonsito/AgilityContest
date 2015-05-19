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
				'T1' => floatval($item['Tiempo']),
				'V1' => $item['Velocidad'],
				'P1' => $item['Penalizacion'],
				'C1' => $item['CShort'],
				'Puesto1' => $item['Puesto'],
                'Pt1' => $item['Puntos'],
				// datos fake manga 2 ( to be filled if so )
				'F2' => 0,
				'R2' => 0,
				'T2' => 0,
				'P2' => 0,
				'V2' => 0,
				'C2' => '',
				'Puesto2' => 0,
                'Pt2' => 0,
				// datos globales
				'Tiempo' => $item['Tiempo'],
				'Penalizacion' => $item['Penalizacion'],
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
					$this->myLogger->error("El perro con ID:{$item['Perro']} no tiene datos en la primera manga.");
					continue;
				}
				$final[$item['Perro']]['F2'] = $item['Faltas'] + $item['Tocados'];
				$final[$item['Perro']]['R2'] = $item['Rehuses'];
				$final[$item['Perro']]['T2'] = floatval($item['Tiempo']);
				$final[$item['Perro']]['V2'] = $item['Velocidad'];
				$final[$item['Perro']]['P2'] = $item['Penalizacion'];
				$final[$item['Perro']]['C2'] = $item['CShort'];
                $final[$item['Perro']]['Puesto2'] = $item['Puesto'];
                $final[$item['Perro']]['Pt2'] = $item['Puntos'];
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
            $countcat['C']++;
            $countcat[$cat]++;
            // obtenemos la penalizacion del perro actual
            $now=100*$final[$idx]['Penalizacion']+$final[$idx]['Tiempo'];
			// ajustamos puesto conjunto y guardamos resultado
			if ($lastcat['C']!=$now) { $lastcat['C']=$now; $puestocat['C']=$countcat['C']; }
			$final[$idx]['Puesto']=$puestocat['C'];
            // ajustamos puesto por categoria y guardamos resultado
            if ($lastcat[$cat]!=$now) { $lastcat[$cat]=$now; $puestocat[$cat]=$countcat[$cat]; }

            // ajustamos puesto de su categoria

            // evaluamos calificacion y puntos en funcion de la federacion y de si es o no selectiva
            switch(intval($this->prueba->RSCE)) {
                case 0: // RSCE
                    $c=$final[$idx]['Grado'];
                    if (($c==="GII") || ($c=="GIII")) {
                        $final[$idx]['Calificacion'] =
                            ($final[$idx]['Penalizacion']==0.0)?'Pto.':'';
                    }
                    if (intval($this->prueba->Selectiva)==0) break;
                    // TODO: evaluate puntos in selectivas.
                    // Tener en cuenta mestizos y extranjeros
                    break;
                case 1: // RFEC
                    $ptsmanga=array("5","4","3","2","1"); // puntos por manga y puesto
                    $ptsglobal=array("15","12","9","7","6","5","4","3","2","1"); //puestos por general (si no NC o Elim en alguna manga)
                    // manga 1
                    $pt1=0;
                    if ($final[$idx]['P1']<6.0) $pt1++; // 1 punto por excelente
                    if ($final[$idx]['P1']==0.0) $pt1++; // 2 puntos por cero
                    if ($final[$idx]['Puesto1']<6) $pt1+= $ptsmanga[$final[$idx]['Puesto1']-1]; // puntos a los cinco primeros de la manga
                    // manga 2
                    $pt2=0;
                    if ($final[$idx]['P2']<6.0) $pt2++; // 1 punto por excelente
                    if ($final[$idx]['P2']==0.0) $pt2++; // 2 puntos por cero
                    if ($final[$idx]['Puesto2']<6) $pt2+= $ptsmanga[$final[$idx]['Puesto2']-1]; // puntos a los cinco primeros de la manga
                    // conjunta
                    $pfin=0;
                    if ($puestocat[$cat]<11) {
                        // solo puntuan los 10 primeros que no se hayan eliminado o no clasificado en algna manga
                       if ( ($final[$idx]['P1']<=26.0) && ($final[$idx]['P2']<=26.0) ) {
                            $pfin=$ptsglobal[$puestocat[$cat]-1];
                        }
                    }
                    $final[$idx]['Calificacion']=$str=strval($pt1)."-".strval($pt2)."-".strval($pfin);
                    break;
                case 2: // UCA
                    $pts=array("10","8","6","4","3","2","1");
                    $pt1=$final[$idx]['Pt1'];
                    $pt2=$final[$idx]['Pt2'];
                    $str=($pt1==0)?" ":strval($pt1);
                    $str.="-";
                    $str.=($pt2==0)?" ":strval($pt2);
                    $str.="-";
                    // solo puntuan en la global los siete primeros con dobles excelentes
                    if (($pt1<4) || ($pt2<4) || ($puestocat[$cat]>7) ) {
                        $final[$idx]['Calificacion']=$str;
                    } else {
                        // TODO fix real value of puesto by categoria
                        $final[$idx]['Calificacion']= $str . $pts[$puestocat[$cat]-1];
                    }
                    break;
            }
		}

		// Esto es (casi) todo, amigos
		$result=array();
		$result['total']=$size;
		$result['rows']=$final;
		$result['trs1']=$c1['trs'];
		$result['trs2']=$c2['trs'];
        $result['jueces']=array($c1['manga']->NombreJuez1,$c1['manga']->NombreJuez2);
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
				$r1=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$this->prueba->ID,$idmangas[0]); // Agility
				$r2=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$this->prueba->ID,$idmangas[1]); // Jumping
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
                $r1=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$this->prueba->ID,$idmangas[0]); // Agility Equipos 3
                $r2=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$this->prueba->ID,$idmangas[1]); // Jumping Equipos 3
                $c1=$r1->getResultados($mode);
                $c2=$r2->getResultados($mode);
                return $this->evalFinal($c1,$c2,$mode);
			case 0x0080: // equipos 4 conjunta
			case 0x0100: // ronda KO 1..9 vueltas
				$this->errormsg= "Clasificaciones:: Ronda $rondas is not yet supported";
				return null;
			case 0x0200: // manga especial (una vuelta)
				$r1= new Resultados("Clasificaciones::Manga Especial",$this->prueba->ID,$idmangas[0]);
				$c1=$r1->getResultados($mode);
				return $this->evalFinal($c1,null,$mode);
		}
        // arriving here means error
        return null;
	}
}
?>