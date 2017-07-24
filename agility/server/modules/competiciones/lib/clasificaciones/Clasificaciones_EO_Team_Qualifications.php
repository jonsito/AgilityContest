<?php
/*
clasificaciones.php

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

class Clasificaciones_EO_Team_Qualifications extends Clasificaciones {
	protected $prueba; // object
	protected $jornada; // object
	protected $ronda;
	protected $mangas;
    protected $currentDog;
    protected $current=null;

	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {object} $prueba prueba object
	 * @param {object} $jornada jornada object
     * @param {integer} $perro Dog id used to evaluate position
	 * @throws Exception if cannot contact database or invalid prueba/jornada ID
	 */
	function __construct($file,$prueba,$jornada,$perro=0) {
		parent:: __construct($file,$prueba,$jornada,$perro);
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
		for ($i=0;$i<8;$i++) $mangas[$i]=$this->__getObject("Mangas",$idmangas[$i]);
		$resultados=array($c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8);
        $final=array(); // puesto,dorsal, nombre, licencia,categoria,grado, nombreguia, nombreclub,
                // F1,R1,T1,V1,P1,C1,Pt1,St1,F2,R2,T2,V2,P2,C2,Pt2,St2,
                // Tiempo, Penalizacion,Calificacion,Puntos,Estrellas
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
                        'Puesto' => 0,
                        'Pcat' => 0
                    );
                    // anyadimos datos (vacios) de cada manga
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
                        $participante["Out{$j}"]=0;
                        $participante["Puesto{$j}"]=0;
                        $participante["Pcat{$j}"]=0;
                        $participante["Penalizacion"] +=400;// default to not processed
                    }
                    // insertamos el array en la lista de participantes
                    $final[$dogID]=$participante;
                } //if !array_key_exists
                // una vez creado -si es necesario, claro - nos ponemos y rellenamos los elementos especificos
                // de esta manga
                // recuerda que $i=0..7, pero los nombres de las mangas van de 1..8
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
                $final[$dogID]["Out{$j}"]=0;
                $final[$dogID]["Puesto{$j}"] = $item['Puesto'];
                $final[$dogID]["Pcat{$j}"] = $item['Pcat'];
                $final[$dogID]["Tiempo"] += $final[$dogID]["T{$j}"];
                $final[$dogID]["Puntos"] += $final[$dogID]["Pt{$j}"];
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

		// re-ordenamos los datos en base a los puntos obtenidos
		usort($final, function($a, $b) {
			return ( $a['Puntos'] < $b['Puntos'])?1:-1;
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
            $now=$final[$idx]['Puntos'];
            // $now=100*$final[$idx]['Penalizacion']+$final[$idx]['Tiempo'];

			// ajustamos puesto conjunto y guardamos resultado
			if ($lastcat['C']!=$now) { $lastcat['C']=$now; $puestocat['C']=$countcat['C']; }
			$final[$idx]['Puesto']=$puestocat['C'];

            // ajustamos puesto por categoria y guardamos resultado
            if ($lastcat[$cat]!=$now) { $lastcat[$cat]=$now; $puestocat[$cat]=$countcat[$cat]; }
            $final[$idx]['Pcat']=$puestocat[$cat];

            // call to competition module to get calification points and related data
            // notice that this module has implicit 3-Team rounds
			$comp->evalFinalCalification($mangas,$resultados,$final[$idx],$puestocat);
		}

		// Esto es (casi) t odo, amigos
		$result=array();
		$result['total']=$size;
		$result['rows']=$final;
		$result['trs1']=$c1['trs'];
        $result['trs2']=$c2['trs'];
        $result['trs3']=$c3['trs'];
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
            $r=array_merge($equipo,array('C1'=>0,'C2'=>0,'T1'=>0,'T2'=>0,'P1'=>0,'P2'=>0,'Puesto1'=>0,'Puesto2'=>0,
                'Tiempo'=>0,'Penalizacion'=>0,'Puesto'=>0,'Puntos'=>0,'Extra'=>0,'Best'=>0));
            $teams[$id]=$r;
			$teams[$id]['Equipo']=$id; // guardamos el teamID 
        }

        // indexamos las clasificaciones por id de perro. Almacenamos el mejor perro de cada equipo
        $indexedc=array();
        foreach ($c as &$item) {
            $eq=$item['Equipo'];
            if ($teams[$eq]['Best']==0) $teams[$eq]['Best']=$item['Puntos'];
            $indexedc[$item['Perro']]=&$item;
        }

        // procesamos manga 1. Se asume que los resultados ya vienen ordenados por puesto,
        // de manera que se contabilizan solo los puntos de los "mindogs" primeros perros de cada equipo
        // y se almacenan los restantes para caso de empate
        if ($r1!==null) foreach($r1['rows'] as $resultado) {
            $eq=$resultado['Equipo'];
            if (!array_key_exists($eq,$teams)) {
                $this->myLogger->notice("evalFinalEquipos(): Prueba:{$this->prueba->ID} Jornada:{$this->jornada->ID} Manga:1 Equipo:$eq no existe");
                continue;
            }
            // si ya hemos registrado "mindogs" en el equipo, los siguientes perros del equipo no puntuan
            // anyadimos una marca "Out1" para que salgan en gris en el listado
            // adicionalmente, para el EO hay que tener en cuenta la puntuacion del cuarto perro y del mejor del equipo
            if ($teams[$eq]['C1']>=$mindogs) {
                $teams[$eq]['Extra']+=$resultado['Puntos']; // resultado del cuarto perro
                $indexedc[$resultado['Perro']]['Out1']=1; // marcar para imprimir en gris
                continue;
            }
            $teams[$eq]['C1']++; // numero de perros que han sido registrados
            $teams[$eq]['T1']+=$resultado['Tiempo'];
            $teams[$eq]['P1']+=$resultado['Penalizacion'];
            $teams[$eq]['Tiempo']+=$resultado['Tiempo'];
            $teams[$eq]['Penalizacion']+=$resultado['Penalizacion'];
            $teams[$eq]['Puntos']+=$resultado['Puntos'];
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
            // adicionalmente, para el EO hay que tener en cuenta la puntuacion del cuarto perro y del mejor del equipo
            if ($teams[$eq]['C2']>=$mindogs) {
                $teams[$eq]['Extra']+=$resultado['Puntos']; // resultado del cuarto perro
                $indexedc[$resultado['Perro']]['Out2']=1; // marcar para imprimir en gris
                continue;
            }
            $teams[$eq]['C2']++; //count 2
            $teams[$eq]['T2']+=$resultado['Tiempo'];
            $teams[$eq]['P2']+=$resultado['Penalizacion'];
            $teams[$eq]['Tiempo']+=$resultado['Tiempo'];
            $teams[$eq]['Penalizacion']+=$resultado['Penalizacion'];
            $teams[$eq]['Puntos']+=$resultado['Puntos'];
			// cogemos como logo del equipo el logo del primer perro que encontremos de dicho equipo
			if (!array_key_exists('LogoTeam',$teams[$eq])) $teams[$eq]['LogoTeam']=$resultado['LogoClub'];
        }
        // rellenamos huecos hasta completar mindogs
        foreach ($teams as &$team ) {
            // 100:Eliminado 200:NoPresentado 400:Pendiente
            for($n=$team['C1'];$n<$mindogs;$n++) { $team['P1']+=400; $team['Penalizacion']+=400; }
            for($n=$team['C2'];$n<$mindogs;$n++) { $team['P2']+=400; $team['Penalizacion']+=400; }
        }

		// calculamos y almacenamos puesto en la clasificacion final

        // localizamos los mejores de cada equipo y guardarlos en el campo "Best"
        $final=array_values($teams);
        usort($final, function($a, $b) {
            if ( $a['Puntos'] == $b['Puntos'] )	{
                if ($a['Extra']==$b['Extra']) {
                    return ($a['Best'] < $b['Best'])? 1:-1;
                }
                return ($a['Extra'] < $b['Extra'])? 1:-1;
            }
            return ( $a['Puntos'] < $b['Puntos'])?1:-1;
        });
		for ($n=0;$n<count($final);$n++) {
		    $final[$n]['Puesto']=$n+1; // at final Puesto use (now yes) evaluated value instead of original
		    // $teams[$final[$n]['ID']]['Puesto']=$n+1;
        }
        // retornamos resultado
        return $final;
	}

}
?>