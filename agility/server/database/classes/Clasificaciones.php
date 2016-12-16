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
require_once("Equipos.php");
require_once("Resultados.php");

class Clasificaciones extends DBObject {
	protected $prueba;
	protected $jornada;
	protected $ronda;
	protected $mangas;
    protected $currentDog;
    protected $current=null;

	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {integer} $prueba prueba ID
	 * @param {integer} $jornada jornada ID
	 * @throws Exception if cannot contact database or invalid prueba/jornada ID
	 */
	function __construct($file,$prueba,$jornada,$perro=0) {
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
        // when perro is requested, but we are in first round, it's possible that dog has no data yet,
        // and doesn't appears in clasification, so make sure that at least a dummy entry is returned
        $this->currentDog=$perro;
        $this->current=array('Perro' => $perro );
	}
	
	/**
	 * genera la tabla de resultados finales y evalua el orden de clasificacion
	 * @param {array} $c1 clasificacion primera manga
	 * @param {array} $c2 clasificacion segunda manga
	 */
	function evalFinal($idmangas,$c1,$c2) {
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
                'NombreLargo' => $item['NombreLargo'],
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
                'Out1' => 0,  // used in team to tell if included in points
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
                'Out2' => 0, // used in team to tell if included in points
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
                        'NombreLargo' => $item['NombreLargo'],
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
                        'Out1' => 0,  // used in team to tell if included in points
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
                $final[$item['Perro']]['Out2'] = 0;
                $final[$item['Perro']]['Puesto2'] = $item['Puesto'];
                $final[$item['Perro']]['Pcat2'] = $item['Pcat'];
				$final[$item['Perro']]['Tiempo'] = $final[$item['Perro']]['T1'] + $final[$item['Perro']]['T2'];
				$final[$item['Perro']]['Penalizacion'] = $final[$item['Perro']]['P1'] + $final[$item['Perro']]['P2'];
				$final[$item['Perro']]['Calificacion'] = '';
				$final[$item['Perro']]['Puntos'] = '';
			}
		}
		// una vez ordenados, reconstruimos el array eliminando el indice "perro"
        // no obstante, si $this->currentDog es distinto de cero, guardamos la entrada seleccionada
        // en $this->current para evaluar luego el tiempo requerido
		$final2=array();
		foreach($final as $perro => &$item) {
		    if ($this->currentDog==$perro) $this->current=$item;
		    array_push($final2,$item);
        }
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
        // evaluamos calificacion y puntos en funcion de la federacion y de si es o no selectiva
        $comp=Competitions::getCompetition($this->prueba,$this->jornada);
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
            // call to competition module to get calification points and related data
			$comp->evalFinalCalification($m1,$m2,$c1,$c2,$final[$idx],$puestocat);
		}

		// Esto es (casi) t odo, amigos
		$result=array();
		$result['total']=$size;
		$result['rows']=$final;
		$result['trs1']=$c1['trs'];
		$result['trs2']=$c2['trs'];
        $result['jueces']=array($c1['manga']->NombreJuez1,$c1['manga']->NombreJuez2);

        // when a dog is provided (!and found!), evaluate time required to get first position in final scores
        if ($this->currentDog!=0) {
            $this->eval_timeToBeFirst($result);
            $result['current']=$this->current;
            // $this->myLogger->trace("current dog timings: ".json_encode($result['current']));
        }
		return $result;
	}

    /**
     * When currentDog!=0 try to evaluate time required to get first (assume no course penalization :-)
     * @param {array} $result clasificaciones de la prueba
     */
	private function eval_timeToBeFirst($result) {
        // if no dogs yet, just do nothing :-)
        if (count($result['rows'])==0)  { $this->current['toBeFirst']=""; return; }
        $first=$result['rows'][0]; // pointer to first dog in scores

        // on first round current dog has <at least 400 on penalty. detect dual NP on first
        if ( $first['Penalizacion'] >= 400 ) {// first round.
            // in first current no data yet for current dog, unless already run. so detect and handle
            if ($first['P1']==400) { // current tanda is jumping
                $fp=floatval($first['P2']);
                $ft=($fp>=100)?0:floatval($first['T2']);
                $cp=($this->current==null)? 0 : floatval($this->current['P2']);
                $ct=($this->current==null)? 0 : ($cp>=100)?0:floatval($this->current['T2']);
                $trs=$result['trs2']['trs'];
                $trm=$result['trs2']['trm'];
            } else { // current tanda is agility
                $fp=floatval($first['P1']);
                $ft=($fp>=100)?0:floatval($first['T1']);
                $cp=($this->current==null)? 0 : floatval($this->current['P1']);
                $ct=($this->current==null)? 0 : ($cp>=100)?0:floatval($this->current['T1']);
                $trs=$result['trs1']['trs'];
                $trm=$result['trs1']['trm'];
            }
            // evaluate required data
        } else { // final round
            // detect no pending rounds for current dog
            if ($this->current['Penalizacion']<400) { $this->current['toBeFirst']=""; return; }
            if ( (intval($this->current['N1'])+intval($this->current['N2'])) >= 2 ) { $this->current['toBeFirst']=""; return; }

            // in final round. get global penalization
            // take care on eliminated when evaluating final time
            $fp = floatval($first['Penalizacion']);
            $ft = ( ($first['P1']>=100)?0:$first['T1'] ) + ( ($first['P2']>=100)?0:$first['T2'] );
            // current has a pending run with time=0
            $cp = floatval($this->current['Penalizacion'] - 400);
            $ct = ( ($this->current['P1']>=100)?0:$this->current['T1'] ) + ( ($this->current['P2']>=100)?0:$this->current['T2']);
            // and annotate trs and trm
            $trs= floatval( ($this->current['P1']>=400)?$result['trs1']['trs']:$result['trs2']['trs']);
            $trm= floatval( ($this->current['P1']>=400)?$result['trs1']['trm']:$result['trs2']['trm']);
        }
        // there is so many special cases that no real sense -and not enought time until awc
        // to get a full solution ( perhaps something like successive aproximations...
        // so let stay in simplest case: only show timeToFirst when current and first have no penalization
        // and then return time differences
        if ($fp!=0) $this->current['toBeFirst']="";
        else if ($cp!=0) $this->current['toBeFirst']="";
        else if ($ft==$ct) $this->current['toBeFirst']="";
        else  $this->current['toBeFirst']=min($trs,$ft-$ct);
        /*
        if ($fp<$cp) {// tiene mas penalizacion que el primero: no tiene nada que hacer
            $this->current['toBeFirst']="";
        }
        if ($fp==$cp) { // misma penalizacion: tiene que mejorar el tiempo sin pasarse del trs... salvo que ya sea el primero
            $this->current['toBeFirst']=min($trs,$ft-$ct);
            if ($ft-$ct==0) $this->current['toBeFirst']=""; // already the first: do nothing
        }
        if ($fp>$cp ) { // tiene menos penalizacion que el primero;
            // tenemos varias posibilidades, y tenemos que escoger la menor de ellas
            // que el tiempo no supere al del trm
            $c1=$trm;
            // que el perro no obtenga un NoClasificado
            $c2=$trs+26;
            // que la penalizacion por tiempo no sea mayor que la penalizacion que tiene el primero
            $c3=$trs+($fp-$cp);
            // que el tiempo + la penalizacion no supere al tiemp+penalizacion del primero
            $c4=($ft-$ct) + floor($fp-$cp); // redondear la penalizacion para no duplicar centesimas de segundo
            //  en este ultimo caso, hay que eliminar decimales en la penalizacion, porque se van a sumar al tiempo...
            $this->current['toBeFirst']=min($c4,$c3,$c2,$c1);
        }
        */
    }

    /**
     * Evalua el resultado de una competicion por equipos
     * @param {array} $r1 datos de la manga 1
     * @param {array} $r2 datos de la manga 2
     * @param {array} $c clasificacion final (individual)
     * @param {integer} $mindogs perros que contabilizan
     * @param {integer} $mode modo de la prueba
     */
	function evalFinalEquipos($r1,$r2,&$c,$mindogs,$mode) {
        // Datos de equipos de la jornada
        $eobj=new Equipos("evalFinalEquipos",$this->prueba->ID,$this->jornada->ID);
        $tbj=$eobj->getTeamsByJornada();
        // reindexamos equipos por ID y aniadimos campos para evaluar clasificacion
        $teams=array();
        foreach ($tbj as $equipo) {
			$id=$equipo['ID'];
            if ($equipo['Nombre']==="-- Sin asignar --") continue;
            // comprobamos la categoria. si no coincide tiramos el equipo
            $modes=array("L","M","S","MS","LMS","T","LM","ST","LMST");
            if ( ! category_match($equipo['Categorias'],$modes[$mode])) continue;
            $r=array_merge($equipo,array('C1'=>0,'C2'=>0,'T1'=>0,'T2'=>0,'P1'=>0,'P2'=>0,'Puesto1'=>0,'Puesto2'=>0,'Tiempo'=>0,'Penalizacion'=>0,'Puesto'=>0));
            $teams[$id]=$r;
			$teams[$id]['Equipo']=$id; // guardamos el teamID 
        }
        // procesamos manga 1. Se asume que los resultados ya vienen ordenados por puesto,
        // de manera que se contabilizan solo los mindogs primeros perros de cada equipo
        if ($r1!=null) foreach($r1['rows'] as $resultado) {
            $eq=$resultado['Equipo'];
            if (!array_key_exists($eq,$teams)) {
                $this->myLogger->notice("evalFinalEquipos(): Prueba:{$this->prueba->ID} Jornada:{$this->jornada->ID} Manga:1 Equipo:$eq no existe");
                continue;
            }
            if ($teams[$eq]['C1']>=$mindogs) {
                for ($fidx=0;$fidx < count($c); $fidx++) {
                    if ($resultado['Perro']!=$c[$fidx]['Perro']) continue;
                    $c[$fidx]['Out1']=1;
                    break;
                }
                continue;
            }
            $teams[$eq]['C1']++;
            $teams[$eq]['T1']+=$resultado['Tiempo'];
            $teams[$eq]['P1']+=$resultado['Penalizacion'];
            $teams[$eq]['Tiempo']+=$resultado['Tiempo'];
            $teams[$eq]['Penalizacion']+=$resultado['Penalizacion'];
			// cogemos como logo del equipo el logo del primer perro que encontremos de dicho equipo
			if (!array_key_exists('LogoTeam',$teams[$eq])) $teams[$eq]['LogoTeam']=$resultado['LogoClub'];
        }
        // procesamos manga 2
        if ($r2!=null) foreach($r2['rows'] as $resultado) {
            $eq=$resultado['Equipo'];
            if (!array_key_exists($eq,$teams)) {
                $this->myLogger->notice("evalFinalEquipos(): Prueba:{$this->prueba->ID} Jornada:{$this->jornada->ID} Manga:2 Equipo:$eq no existe");
                continue;
            }
            if ($teams[$eq]['C2']>=$mindogs) {
                for ($fidx=0;$fidx < count($c); $fidx++) {
                    if ($resultado['Perro']!=$c[$fidx]['Perro']) continue;
                    $c[$fidx]['Out2']=1;
                    break;
                }
                continue;
            }
            $teams[$eq]['C2']++;
            $teams[$eq]['T2']+=$resultado['Tiempo'];
            $teams[$eq]['P2']+=$resultado['Penalizacion'];
            $teams[$eq]['Tiempo']+=$resultado['Tiempo'];
            $teams[$eq]['Penalizacion']+=$resultado['Penalizacion'];
			// cogemos como logo del equipo el logo del primer perro que encontremos de dicho equipo
			if (!array_key_exists('LogoTeam',$teams[$eq])) $teams[$eq]['LogoTeam']=$resultado['LogoClub'];
        }
        // rellenamos huecos hasta completar mindogs
        foreach ($teams as &$team ) {
            // 100:Eliminado 200:NoPresentado 400:Pendiente
            for($n=$team['C1'];$n<$mindogs;$n++) { $team['P1']+=400; $team['Penalizacion']+=400; }
            for($n=$team['C2'];$n<$mindogs;$n++) { $team['P2']+=400; $team['Penalizacion']+=400; }
        }
		// calculamos y almacenamos puestos de manga 1
		$manga1=array_values($teams);
		usort($manga1, function($a, $b) {
			if ( $a['P1'] == $b['P1'] )	return ($a['T1'] > $b['T1'])? 1:-1;
			return ( $a['P1'] > $b['P1'])?1:-1;
		});
        // dado que array_values retorna una copia y no el array original
        // es preciso asignar valores usando éste e indexando segun el orden devuelto
        // por manga1
		for ($n=0;$n<count($manga1);$n++) $teams[$manga1[$n]['ID']]['Puesto1']=$n+1;
		// calculamos y almacenamos puestos de manga 2
		$manga2=array_values($teams);
		usort($manga2, function($a, $b) {
			if ( $a['P2'] == $b['P2'] )	return ($a['T2'] > $b['T2'])? 1:-1;
			return ( $a['P2'] > $b['P2'])?1:-1;
		});
		for ($n=0;$n<count($manga2);$n++) $teams[$manga2[$n]['ID']]['Puesto2']=$n+1;
		// calculamos y almacenamos puesto en la clasificacion final
        $final=array_values($teams);
        usort($final, function($a, $b) {
            if ( $a['Penalizacion'] == $b['Penalizacion'] )	return ($a['Tiempo'] > $b['Tiempo'])? 1:-1;
            return ( $a['Penalizacion'] > $b['Penalizacion'])?1:-1;
        });
		for ($n=0;$n<count($final);$n++) {
		    $final[$n]['Puesto']=$n+1; // at final Puesto use (now yes) evaluated value instead of original
		    // $teams[$final[$n]['ID']]['Puesto']=$n+1;
        }
        // retornamos resultado
        return $final;
	}

	/**
	 * Esta funcion evalua la penalización final, sin calcular puntuaciones, ni excelentes ni
     * nada de eso.
     * Se utiliza en getPuestoFinal para adivinar la posición en que ha quedado un perro,
     * con independencia de si tiene puntos o no
     * Es similar a evalFinal(), salvo que se excuye el calculo de puntos
     *
	 * @param {array} $c1 clasificacion primera manga
	 * @param {array} $c2 clasificacion segunda manga
	 * @param {integer} $mode Modo 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small
	 */
	function evalPenalizacionFinal($idmangas,$c1,$c2) {
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
	 * Evalua las clasificaciones finales por equipos
	 * @param {integer} $rondas bitfield Jornadas::$tipo_ronda
	 * @param {array[{integer}]} $idmangas array con los ID's de las mangas a evaluar
	 * @param {integer} $mode Modo 0:L 1:M 2:S 3:M+S 4:L+M+S 5:T 6:L+M 7:S+T 8:L+M+S+T
	 *
	 * Retorna varios arrays:
	 * - datos de las mangas
	 * - clasificacion final individual
	 * - clasificacion final por equipos
	 */
	function clasificacionFinalEquipos($rondas,$idmangas,$mode) {
		// vamos a ver que tipo de clasificacion nos estan pidiendo
		switch ($rondas) {
			case 0x0002: // pre-agility a dos vueltas
			case 0x0004: // Grado I
			case 0x0008: // Grado II
			case 0x0010: // Grado III
			case 0x0020: // Open - Individual
			case 0x0018: // Conjunta GII - GIII
			case 0x0100: // ronda KO 1..8 vueltas
			case 0x0200: // manga especial (una vuelta)
				$this->errormsg= "ClasificacionEquipos(): choosen series ($rondas) is not a Team Serie";
				return null;
			case 0x0400: // equipos 2 mejores de 3
                $mindogs=2;$maxdogs=3;break;
			case 0x0040: // equipos 3 mejores de 4
                $mindogs=3;$maxdogs=4;break;
			case 0x0800: // equipos 2 conjunta
                $mindogs=2;$maxdogs=2;break;
			case 0x1000: // equipos 3 conjunta
                $mindogs=3;$maxdogs=3;break;
			case 0x0080: // equipos 4 conjunta
                $mindogs=4;$maxdogs=4;break;
            default:
                // arriving here means error
                $this->errormsg= "ClasificacionEquipos(): Unknown series type ($rondas)";
                return null;
		}
        $r1=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$this->prueba->ID,$idmangas[0]); // Agility Equipos
        $r2=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$this->prueba->ID,$idmangas[1]); // Jumping Equipos
        $c1=$r1->getResultados($mode);
        $c2=$r2->getResultados($mode);
        $res= $this->evalFinal($idmangas,$c1,$c2);
        $result=array();
        $result['individual']=$res['rows'];
        $result['trs1']=$res['trs2'];
        $result['trs2']=$res['trs1'];
        $result['jueces']=$res['jueces'];
        $result['equipos']=$this->evalFinalEquipos($c1,$c2,$result['individual'],$mindogs,$mode);
        $result['total']=count($result['equipos']);
        if (array_key_exists('current',$res)) $result['current']=$res['current'];
        return $result;
	}

	/**
	 * Evalua las clasificaciones en funcion de los datos pedidos
	 * @param {integer} $rondas bitfield Jornadas::$tipo_ronda
	 * @param {array[{integer}]} $idmangas array con los ID's de las mangas a evaluar
	 * @param {integer} $mode Modo 0:L 1:M 2:S 3:M+S 4:L+M+S 5:T 6:L+M 7:S+T 8:L+M+S+T
	 */
	function clasificacionFinal($rondas,$idmangas,$mode) {
		// vamos a ver que tipo de clasificacion nos estan pidiendo
		switch ($rondas) {
			case 0x0001: // pre-agility a una vuelta
				$r1= new Resultados("Clasificaciones::Preagility 1",$this->prueba->ID,$idmangas[0]);
				$c1=$r1->getResultados($mode);
				return $this->evalFinal($idmangas,$c1,null);
			case 0x0002: // pre-agility a dos vueltas
			case 0x0004: // Grado I
			case 0x0008: // Grado II
			case 0x0010: // Grado III
			case 0x0020: // Open - Individual
				$r1=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$this->prueba->ID,$idmangas[0]); // Agility
				$r2=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$this->prueba->ID,$idmangas[1]); // Jumping
				$c1=$r1->getResultados($mode);
				$c2=$r2->getResultados($mode);
				return $this->evalFinal($idmangas,$c1,$c2);
				break;
			case 0x0018: // Conjunta GII - GIII
				$r1=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$this->prueba->ID,$idmangas[0]); // Agility GII
				$r2=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$this->prueba->ID,$idmangas[1]); // Jumping GII
				$r3=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[2]}",$this->prueba->ID,$idmangas[2]); // Agility GIII
				$r4=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[3]}",$this->prueba->ID,$idmangas[3]); // Jumping GIII
				$c1=$this->combina( $r1->getResultados($mode), $r3->getResultados($mode));
				$c2=$this->combina( $r2->getResultados($mode), $r4->getResultados($mode));
				return $this->evalFinal($idmangas,$c1,$c2);
			case 0x0400: // equipos 2 mejores de 3
			case 0x0040: // equipos 3 mejores de 4
                $r1=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$this->prueba->ID,$idmangas[0]); // Agility Equipos best
                $r2=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$this->prueba->ID,$idmangas[1]); // Jumping Equipos best
                $c1=$r1->getResultados($mode);
                $c2=$r2->getResultados($mode);
                return $this->evalFinal($idmangas,$c1,$c2);
			case 0x0800: // equipos 2 conjunta
			case 0x1000: // equipos 3 conjunta
			case 0x0080: // equipos 4 conjunta
                $r1=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$this->prueba->ID,$idmangas[0]); // Agility Equipos combined
                $r2=new Resultados("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$this->prueba->ID,$idmangas[1]); // Jumping Equipos combined
                $c1=$r1->getResultados($mode);
                $c2=$r2->getResultados($mode);
                return $this->evalFinal($idmangas,$c1,$c2);
			case 0x0100: // ronda KO 1..8 vueltas
				$this->errormsg= "Clasificaciones:: Ronda $rondas is not yet supported";
				return null;
			case 0x0200: // manga especial (una vuelta)
				$r1= new Resultados("Clasificaciones::Manga Especial",$this->prueba->ID,$idmangas[0]);
				$c1=$r1->getResultados($mode);
				return $this->evalFinal($idmangas,$c1,null);
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
	 *@param {integer} $mode Modo 0:L 1:M 2:S 3:M+S 4:L+M+S 5:T 6:L+M 7:S+T 8:L+M+S+T
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
        $result= $this->evalPenalizacionFinal(array($id1,$id2),$c1,$c2);

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
			return array( 'mejortiempo'=>$perro['Tiempo'],'success'=>true,'puesto'=>1,'penalizacion'=>0 /* no easy way to evaluate */);
		}
		// buscamos el puesto en el que finalmente ha quedado $myPerro y lo retornamos
		for ($idx=0;$idx<$size;$idx++ ){
			if ($table[$idx]['Perro']!=$idperro) continue;
			return array('mejortiempo'=>$table[0]['Tiempo'], 'success'=>true,'puesto'=>(1+$idx),'penalizacion'=>$table[$idx]['Penalizacion']);
		}
		//arriving here means error: perro not found
		return $this->error("Perro:$idperro not found in clasificaciones::getPuesto()");
	}
}
?>