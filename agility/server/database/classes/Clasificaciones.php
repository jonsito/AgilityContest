<?php
/*
clasificaciones.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


require_once(__DIR__."/DBObject.php");
require_once(__DIR__ . "/../../modules/Federations.php");
require_once(__DIR__."/Mangas.php");
require_once(__DIR__."/Equipos.php");
require_once(__DIR__."/Resultados.php");

class Clasificaciones extends DBObject {
	protected $prueba; // object
	protected $jornada; // object
	protected $ronda;
	protected $mangas;
    protected $currentDog;
    protected $current=null;

    // to make inner objects public
    function getJornada(){ return $this->jornada; }
    function getPrueba(){ return $this->jornada; }

	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {object} $prueba prueba object
	 * @param {object} $jornada jornada object
     * @param {integer} $perro Dog id used to evaluate position
	 * @throws Exception if cannot contact database or invalid prueba/jornada ID
	 */
	function __construct($file,$prueba,$jornada,$perro=0) {
		parent::__construct($file);
		if ($prueba->ID <= 0) {
			$this->errormsg="Clasificaciones::Construct invalid prueba ID:$prueba";
			throw new Exception($this->errormsg);
		}
		$this->prueba=$prueba;
		$this->jornada=$jornada;
		$this->mangas=array();
        // when perro is requested, but we are in first round, it's possible that dog has no data yet,
        // and doesn't appears in clasification, so make sure that at least a dummy entry is returned
        $this->currentDog=$perro;
        $this->current=array('Perro' => $perro );
	}

    /**
     * Method to short final scores based in penalization/time
     * May be overriden for other subclases to change sorting method
     * @param {array} $final scores
     * @param {array} $c1 scores for round 1
     * @param {array} $c2 scores for round 2
     * @param {array} $c3 scores for round 3
     * @param {array} $c4 scores for round 4
     * @param {array} $c5 scores for round 5
     * @param {array} $c6 scores for round 6
     * @param {array} $c7 scores for round 7
     * @param {array} $c8 scores for round 8
     */
	protected function sortFinal(&$final,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8) {
        usort($final, function($a, $b) {
            if ( $a['Penalizacion'] == $b['Penalizacion'] )	return ($a['Tiempo'] > $b['Tiempo'])? 1:-1;
            return ( $a['Penalizacion'] > $b['Penalizacion'])?1:-1;
        });
    }

	/**
	 * genera la tabla de resultados finales y evalua el orden de clasificacion
	 * @param {array} $c1 clasificacion primera manga
     * @param {array} $c2 clasificacion segunda manga
     * @param {array} $c3 clasificacion tercera manga
     * @param {array} $c4 clasificacion cuarta manga
     * @param {array} $c5 clasificacion quinta manga
     * @param {array} $c6 clasificacion sexta manga
     * @param {array} $c7 clasificacion septima manga
     * @param {array} $c8 clasificacion ocatava manga
	 */
	function evalFinal($idmangas,$c1,$c2=null,$c3=null,$c4=null,$c5=null,$c6=null,$c7=null,$c8=null) {
		$this->myLogger->enter();
		$mangas=array();
		for ($i=0;$i<8;$i++) $mangas[$i]=$this->__getObject("mangas",$idmangas[$i]);
		$resultados=array($c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8);
        $final=array(); // puesto,dorsal, nombre, licencia,categoria,grado, nombreguia, nombreclub,
                        // F1,R1,T1,V1,P1,C1,F2,R2,T2,V2,P2,C2, [....] Penalizacion,Calificacion
        // procesamos cada una de las 8 posibles mangas
        for ($i=0;$i<8;$i++) {
            if($resultados[$i]===null) continue; // no info for round $i
            $dogcount=count($resultados[$i]['rows']);
            // iterate over every rounds and compose array
            foreach($resultados[$i]['rows'] as $item){
                $dogID=$item['Perro'];
                if (!array_key_exists($dogID,$final)) {
                    // creamos todos los posibles elementos de la tabla
                    $participante=array(
                        // datos del participante
                        'Participantes' => $dogcount,
                        'Perro' => $dogID,
                        'Dorsal' => $item['Dorsal'],
                        'Nombre' => $item['Nombre'],
                        'NombreLargo' => $item['NombreLargo'],
                        'Licencia' => $item['Licencia'],
                        'LOE_RRC' => $item['LOE_RRC'],
                        'Categoria' => $item['Categoria'],
                        'Grado' => $item['Grado'],
                        'Equipo' => $item['Equipo'],
                        'NombreEquipo' => $item['NombreEquipo'],
                        'CatGuia' => $item['CatGuia'],
                        'NombreGuia' => $item['NombreGuia'],
                        'NombreClub' => $item['NombreClub'],
                        'LogoClub' => $item['LogoClub'],
                        // global data to be evaluated
                        'Tiempo' => 0,
                        'Penalizacion' => 0,
                        'Calificacion' => '',
                        'Puntos' => 0,
                        'Estrellas' => 0,
                        'Extras' => 0,
                        'Puesto' => 0,
                        'Pcat' => 0
                    );
                    // anyadimos datos de cada manga
                    for($j=1;$j<9;$j++) {
                        if ($resultados[$j-1]===null) continue;
                        $participante["F{$j}"]=0;
                        $participante["R{$j}"]=0;
                        $participante["E{$j}"]=0;
                        $participante["N{$j}"]=0;
                        $participante["T{$j}"]=0;
                        $participante["V{$j}"]=0;
                        $participante["P{$j}"]=0;
                        $participante["C{$j}"]=0;
                        $participante["Pt{$j}"]=0; // points
                        $participante["St{$j}"]=0; // stars
                        $participante["Xt{$j}"]=0; // extras
                        $participante["Out{$j}"]=0;
                        $participante["Puesto{$j}"]=0;
                        $participante["Pcat{$j}"]=0;
                        $participante["Penalizacion"] +=400;// default to not processed
                    }
                    // insertamos el array en la lista de participantes
                    $final[$dogID]=$participante;
                    // do_log("round:{$mangas[$i]->ID} Create Participante:{$dogID}: ".json_encode($participante));
                }
                // una vez creado -si es necesario, claro - nos ponemos y rellenamos los elementos especificos de esta manga
                $j=$i+1;
                $final[$dogID]["F{$j}"] = $item['Faltas']+ $item['Tocados'];
                $final[$dogID]["R{$j}"] = $item['Rehuses'];
                $final[$dogID]["E{$j}"] = $item['Eliminado'];
                $final[$dogID]["N{$j}"] = $item['NoPresentado'];
                $final[$dogID]["T{$j}"] = floatval($item['Tiempo']);
                $final[$dogID]["V{$j}"] = floatval($item['Velocidad']);
                $final[$dogID]["P{$j}"] = $item['Penalizacion'];
                $final[$dogID]["C{$j}"] = $item['CShort'];
                $final[$dogID]["Pt{$j}"] = $item['Puntos'];
                $final[$dogID]["St{$j}"] = $item['Estrellas'];
                $final[$dogID]["Xt{$j}"] = $item['Extras'];
                $final[$dogID]["Out{$j}"]=0;
                $final[$dogID]["Puesto{$j}"] = $item['Puesto'];
                $final[$dogID]["Pcat{$j}"] = $item['Pcat'];
                $final[$dogID]["Tiempo"] += $final[$dogID]["T{$j}"];
                $final[$dogID]["Puntos"] += $final[$dogID]["Pt{$j}"];
                $final[$dogID]["Estrellas"] += $final[$dogID]["St{$j}"];
                $final[$dogID]["Extras"] += $final[$dogID]["Xt{$j}"];
                $final[$dogID]['Penalizacion'] = $final[$dogID]['Penalizacion'] - 400 + $final[$dogID]["P{$j}"];
                // do_log("round:{$mangas[$i]->ID} inserted Participante:{$dogID}: ".json_encode($participante));
            }
        }
		// una vez evaluados, reconstruimos el array eliminando el indice "perro"
        // no obstante, si $this->currentDog es distinto de cero, guardamos la entrada seleccionada
        // en $this->current para evaluar luego el tiempo requerido
		$final2=array();
		foreach($final as $perro => &$item) {
		    if ($this->currentDog==$perro) $this->current=$item;
		    array_push($final2,$item);
        }
		$final=$final2;

		// re-ordenamos los datos en base a la puntuacion
		$this->sortFinal($final,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8);

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
			$comp->evalFinalCalification($mangas,$resultados,$final[$idx],$puestocat);
		}

		// Estamos terminandooo
		$result=array();
		$result['total']=$size;
		$result['rows']=$final;
		$result['trs1']=($c1!=null)?$c1['trs']:null;
        $result['trs2']=($c2!=null)?$c2['trs']:null;
        $result['trs3']=($c3!=null)?$c3['trs']:null;
        $result['trs4']=($c4!=null)?$c4['trs']:null;
        $result['trs5']=($c5!=null)?$c5['trs']:null;
        $result['trs6']=($c6!=null)?$c6['trs']:null;
        $result['trs7']=($c7!=null)?$c7['trs']:null;
        $result['trs8']=($c8!=null)?$c8['trs']:null;
        // assume same juez in every mangas
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
	protected function eval_timeToBeFirst($result) {
        // if no dogs yet, just do nothing :-)
        if (count($result['rows'])==0)  { $this->current['toBeFirst']=""; return; }
        $first=$result['rows'][0]; // pointer to first dog in scores

        // on first round current dog has <at least 400 on penalty. detect dual NP on first
        if ( $first['Penalizacion'] >= 400 ) {// first round.
            // in first current no data yet for current dog, unless already run. so detect and handle
            if ($first['P1']==400) { // current tanda is jumping
                $fp=floatval($first['P2']);
                $ft=($fp>=100)?0:floatval($first['T2']);
                $cp=($this->current===null)? 0 : floatval($this->current['P2']);
                $ct=($this->current===null)? 0 : ($cp>=100)?0:floatval($this->current['T2']);
                $trs=$result['trs2']['trs'];
                $trm=$result['trs2']['trm'];
            } else { // current tanda is agility
                $fp=floatval($first['P1']);
                $ft=($fp>=100)?0:floatval($first['T1']);
                $cp=($this->current===null)? 0 : floatval($this->current['P1']);
                $ct=($this->current===null)? 0 : ($cp>=100)?0:floatval($this->current['T1']);
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
    }

    /**
     * Evalua el resultado de una competicion por equipos
     * @param {array} $r1 datos de la manga 1
     * @param {array} $r2 datos de la manga 2
     * @param {array} $c clasificacion final (individual)
     * @param {integer} $mindogs perros que contabilizan
     * @param {integer} $maxdogs perros que contabilizan
     * @param {integer} $mode modo de la prueba
     */
	function evalFinalEquipos($r1,$r2,&$c,$mindogs,$maxdogs,$mode) {

        // indexamos las clasificaciones por id de perro
        $indexedc=array();
        foreach ($c as &$item) {
            $indexedc[$item['Perro']]=&$item;
        }

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
            $r=array_merge($equipo,array('C1'=>0,'C2'=>0,'T1'=>0,'T2'=>0,'P1'=>0,'P2'=>0,'Puesto1'=>0,'Puesto2'=>0,'Tiempo'=>0,'Penalizacion'=>0,'Puesto'=>0,'Outs1'=>0,'Outs2'=>0));
            $teams[$id]=$r;
			$teams[$id]['Equipo']=$id; // guardamos el teamID 
        }
        // procesamos manga 1. Se asume que los resultados ya vienen ordenados por puesto,
        // de manera que se contabilizan solo los mindogs primeros perros de cada equipo
        if ($r1!==null) foreach($r1['rows'] as $resultado) {
            $eq=$resultado['Equipo'];
            if (!array_key_exists($eq,$teams)) {
                $this->myLogger->notice("evalFinalEquipos(): Prueba:{$this->prueba->ID} Jornada:{$this->jornada->ID} Manga:1 Equipo:$eq no existe");
                continue;
            }
            // si ya hemos registrado "mindogs" en el equipo, los siguientes perros del equipo no puntuan
            // anyadimos una marca "Out1" para que salgan en gris en el listado
            $indexedc[$resultado['Perro']]['Out1']=0; // marcar para imprimir en negro
            if ($teams[$eq]['C1']>=$mindogs) {
                $indexedc[$resultado['Perro']]['Out1']=1; // marcar para imprimir en gris
                $teams[$eq]['C1']++;
                if ($teams[$eq]['C1']>$maxdogs) { // si hay mas de maxdogs
                    // y el perro no esta marcado como "no presentado"
                    if ($resultado['Penalizacion']<200) {
                        // equipo descalificado
                        $teams[$eq]['T1']=0;
                        $teams[$eq]['P1']=400*$mindogs;
                        $teams[$eq]['Tiempo']=0;
                        $teams[$eq]['Penalizacion']+=400*$mindogs;
                    }
                }
                continue;
            }
            // llegando aqui hay que calcular los puntos de equipo, y si tienen tiempo valido
            // ( no hay Elim/NP dentro de mindogs )
            $teams[$eq]['C1']++;
            $teams[$eq]['T1']+=$resultado['Tiempo'];
            $teams[$eq]['P1']+=$resultado['Penalizacion'];
            $teams[$eq]['Tiempo']+=$resultado['Tiempo'];
            $teams[$eq]['Penalizacion']+=$resultado['Penalizacion'];
            // en el caso de que algun perro dentro de mindogs sea no presentado, se marca
            // pues hay que tenerlo en cuenta para evaluar el tiempo del equipo
            if ( (intval($resultado['NoPresentado'])!==0) ) {
                $teams[$eq]['Outs1']++;
            }
			// cogemos como logo del equipo el logo del primer perro que encontremos de dicho equipo
			if (!array_key_exists('LogoTeam',$teams[$eq])) $teams[$eq]['LogoTeam']=$resultado['LogoClub'];
        }
        // procesamos manga 2
        if ($r2!==null) foreach($r2['rows'] as $resultado) {
            $eq=$resultado['Equipo'];
            if (!array_key_exists($eq,$teams)) {
                $this->myLogger->notice("evalFinalEquipos(): Prueba:{$this->prueba->ID} Jornada:{$this->jornada->ID} Manga:2 Equipo:$eq no existe");
                continue;
            }
            // si ya hemos registrado "mindogs" en el equipo, los siguientes perros del equipo no puntuan
            // anyadimos una marca "Out2" para que salgan en gris en el listado
            $indexedc[$resultado['Perro']]['Out2']=0; // marcar para imprimir en negro
            // anyadimos una marca "Out2" para que salgan en gris en el listado
            if ($teams[$eq]['C2']>=$mindogs) {
                $indexedc[$resultado['Perro']]['Out2']=1; // marcar para imprimir en gris
                $teams[$eq]['C2']++;
                if ($teams[$eq]['C2']>$maxdogs) { // si hay mas de maxdogs
                    // y el perro no esta marcado como "no presentado"
                    if ($resultado['Penalizacion']<200) {
                        // equipo descalificado
                        $teams[$eq]['T2']=0;
                        $teams[$eq]['P2']=400*$mindogs;
                        $teams[$eq]['Tiempo']=0;
                        $teams[$eq]['Penalizacion']+=400*$mindogs;
                    }
                }
                continue;
            }
            // llegando aqui hay que calcular los puntos de equipo, y si tienen tiempo valido
            // ( no hay Elim/NP dentro de mindogs )
            $teams[$eq]['C2']++;
            $teams[$eq]['T2']+=$resultado['Tiempo'];
            $teams[$eq]['P2']+=$resultado['Penalizacion'];
            $teams[$eq]['Tiempo']+=$resultado['Tiempo'];
            $teams[$eq]['Penalizacion']+=$resultado['Penalizacion'];
            // en el caso de que algun perro dentro de mindogs sea no presentado se marca
            // pues hay que tenerlo en cuenta para evaluar el tiempo del equipo
            if ( (intval($resultado['NoPresentado'])!==0) ) {
                $teams[$eq]['Outs2']++;
            }
			// cogemos como logo del equipo el logo del primer perro que encontremos de dicho equipo
			if (!array_key_exists('LogoTeam',$teams[$eq])) $teams[$eq]['LogoTeam']=$resultado['LogoClub'];
        }
        // rellenamos huecos hasta completar mindogs
        foreach ($teams as &$team ) {
            // 100:Eliminado 200:NoPresentado 400:Pendiente
            for($n=$team['C1'];$n<$mindogs;$n++) { $team['P1']+=400; $team['Penalizacion']+=400; $team['Outs1']++;}
            for($n=$team['C2'];$n<$mindogs;$n++) { $team['P2']+=400; $team['Penalizacion']+=400; $team['Outs2']++;}
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
		$m1=$this->__getObject("mangas",$idmangas[0]);
		$m2=$this->__getObject("mangas",$idmangas[1]);
		$final=array(); // puesto,dorsal, nombre, licencia,LOE_RRC,categoria,grado, nombreguia, nombreclub,
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
		if ($c2!==null) { // Procesamos la segunda manga
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

	function getName($mangas,$mode) {
	    $fed=Federations::getFederation(intval($this->prueba->RSCE));
	    $mng=$this->__getObject("mangas",intval($mangas[0]));
	    $grad=$fed->getTipoManga($mng->Tipo,4);
	    $cat=$fed->get('IndexedModes')[intval($mode)];
	    $res=str_replace(" ","_","{$grad}_{$cat}");
        $res=str_replace("/","",$res);
        $res=str_replace("+","",$res);
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
            case 0x4000: // Junior
            case 0x8000: // Senior
			case 0x0020: // Open - Individual
			case 0x0018: // Conjunta GII - GIII
			case 0x0100: // ronda KO 1..8 vueltas
			case 0x0200: // manga especial (una vuelta)
				$this->errormsg= "ClasificacionEquipos(): choosen series ($rondas) is not a Team Series";
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
        $r1=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$idmangas[0]); // Agility Equipos
        $r2=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$idmangas[1]); // Jumping Equipos
        $c1=$r1->getResultadosIndividual($mode);
        $c2=$r2->getResultadosIndividual($mode);
        $res= $this->evalFinal($idmangas,$c1,$c2);
        $result=array();
        $result['individual']=$res['rows'];
        $result['trs1']=$res['trs2'];
        $result['trs2']=$res['trs1'];
        $result['jueces']=$res['jueces'];
        $result['equipos']=$this->evalFinalEquipos($c1,$c2,$result['individual'],$mindogs,$maxdogs,$mode);
        $result['total']=count($result['equipos']);
        if (array_key_exists('current',$res)) $result['current']=$res['current'];
        return $result;
	}

	/**
	 * Evalua las clasificaciones en funcion de los datos pedidos
	 * @param {integer} $rondas bitfield Jornadas::$tipo_ronda
	 * @param {array[{integer}]} $idmangas array con los ID's de las mangas a evaluar
	 * @param {integer} $mode Modo 0:L 1:M 2:S 3:M+S 4:L+M+S 5:T 6:L+M 7:S+T 8:L+M+S+T
     * @return {array} final clasification data
	 */
	function clasificacionFinal($rondas,$idmangas,$mode) {

        $c8=null;$c7=null;$c6=null;$c5=null;$c4=null;$c3=null;$c2=null;$c1=null;
		// vamos a ver que tipo de clasificacion nos estan pidiendo
		switch ($rondas) {
			case 0x0001: // 1- pre-agility a una vuelta
				$r1= Competitions::getResultadosInstance("Clasificaciones::Preagility 1",$idmangas[0]);
				$c1=$r1->getResultadosIndividual($mode);
				return $this->evalFinal($idmangas,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8);
			case 0x0004: // 4- Grado I
                $r1=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$idmangas[0]); // Agility manga 1
                $c1=$r1->getResultadosIndividual($mode);
                $c2=null;
                if ($idmangas[1]!=0) {
                    $r2=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$idmangas[1]); // Agility manga 2
                    $c2=$r2->getResultadosIndividual($mode);
                }
                $c3=null;
                if ($idmangas[2]!=0) {
                    $r3=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[2]}",$idmangas[2]); // Agility manga 3
                    $c3=$r3->getResultadosIndividual($mode);
                }
                return $this->evalFinal($idmangas,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8);
                break;
            case 0x0002: // 2- pre-agility a dos vueltas
			case 0x0008: // 8- Grado II
			case 0x0010: // 16- Grado III
			case 0x0020: // 32- Open - Individual
            case 0x4000: // 16384- Junior
            case 0x8000: // 32768- Senior
				$r1=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$idmangas[0]); // Agility
				$r2=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$idmangas[1]); // Jumping
				$c1=$r1->getResultadosIndividual($mode);
				$c2=$r2->getResultadosIndividual($mode);
                return $this->evalFinal($idmangas,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8);
				break;
			case 0x0018: // 24- Conjunta GII - GIII
				$r1=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$idmangas[0]); // Agility GII
				$r2=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$idmangas[1]); // Jumping GII
				$r3=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[2]}",$idmangas[2]); // Agility GIII
				$r4=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[3]}",$idmangas[3]); // Jumping GIII
				$c1=$this->combina( $r1->getResultadosIndividual($mode), $r3->getResultadosIndividual($mode));
				$c2=$this->combina( $r2->getResultadosIndividual($mode), $r4->getResultadosIndividual($mode));
                return $this->evalFinal($idmangas,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8);
			case 0x0400: // 1024- equipos 2 mejores de 3
			case 0x0040: // 64- equipos 3 mejores de 4
                $r1=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$idmangas[0]); // Agility Equipos best
                $r2=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$idmangas[1]); // Jumping Equipos best
                $c1=$r1->getResultadosIndividual($mode);
                $c2=$r2->getResultadosIndividual($mode);
                return $this->evalFinal($idmangas,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8);
			case 0x0800: // 2048- equipos 2 conjunta
			case 0x1000: // 4096- equipos 3 conjunta
			case 0x0080: // 128- equipos 4 conjunta
                $r1=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$idmangas[0]); // Agility Equipos combined
                $r2=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$idmangas[1]); // Jumping Equipos combined
                $c1=$r1->getResultadosIndividual($mode);
                $c2=$r2->getResultadosIndividual($mode);
                return $this->evalFinal($idmangas,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8);
			case 0x0100: // 256- ronda KO 1..8 vueltas
                // this should never happen, as KO rounds are handled in a separate Clasification module
				$this->errormsg= "Clasificaciones:: Ronda $rondas is not yet supported";
				return null;
			case 0x0200: // 512- manga especial (una vuelta)
				$r1= Competitions::getResultadosInstance("Clasificaciones::Manga Especial",$idmangas[0]);
				$c1=$r1->getResultadosIndividual($mode);
                return $this->evalFinal($idmangas,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8);
            case 0x2000: // 8192- manga games
                $tj= intval($this->jornada->Tipo_Competicion);
                // vemos la modalidad y extraemos las mangas relevantes para la clasificacion
                // existira un modulo de clasificacion para cada manga
                // las mangas reales tienen este indice
                // 0:AgilityA 1:AgilityB 2:JumpingA 3:JumpingB 4:Snooker 5:Gambler 6:SpeedStakes
                // pero Jornadas::roundsByJornada() las agrupa, con lo que los indices son los que se indican aqui
                switch($tj){
                    case 1: // penthatlon
                        $r5=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[4]}",$idmangas[4]); // speed stakes
                        $c5=$r5->getResultadosIndividual($mode);
                        // no break
                    case 2: // biathlon
                        $r4=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[3]}",$idmangas[3]); // Jumping B
                        $c4=$r4->getResultadosIndividual($mode);
                        $r3=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[2]}",$idmangas[2]); // Jumping A
                        $c3=$r3->getResultadosIndividual($mode);
                        $r2=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$idmangas[1]); // Agility B
                        $c2=$r2->getResultadosIndividual($mode);
                        $r1=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$idmangas[0]); // Agility A
                        $c1=$r1->getResultadosIndividual($mode);
                        break;
                    case 3: // Games
                        $r2=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[1]}",$idmangas[1]); // Gambler
                        $c2=$r2->getResultadosIndividual($mode);
                        $r1=Competitions::getResultadosInstance("Clasificaciones Ronda:$rondas manga:{$idmangas[0]}",$idmangas[0]); // Snooker
                        $c1=$r1->getResultadosIndividual($mode);
                        break;// games
                    default: $this->errormsg= "Clasificaciones:: invalid Tipo_Competicion:{$tj} in jornada:{$this->jornada->ID} in Games rounds";
                        return null;
                }
                return $this->evalFinal($idmangas,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8);
                break;
            default: $this->errormsg= "Clasificaciones:: Ronda $rondas is not yet supported";
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
        $r1= Competitions::getResultadosInstance("Clasificaciones::getPuestoFinal",$id1);
        $c1=$r1->getPenalizaciones($mode,($myManga==$id1)?$perro:null);
        $c2=null;
        if($hermanas[1]!==null) {
            $id2=intval($hermanas[1]->ID);
            $r2= Competitions::getResultadosInstance("Clasificaciones::getPuestoFinal",$id2);
            $c2=$r2->getPenalizaciones($mode,($myManga==$id2)?$perro:null);
        }
        $result= $this->evalPenalizacionFinal(array($id1,$id2),$c1,$c2);

		if($result===null) return null; // null result -> error
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