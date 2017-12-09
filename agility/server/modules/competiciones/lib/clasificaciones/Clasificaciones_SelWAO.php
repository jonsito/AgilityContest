<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 11/10/17
 * Time: 13:28
 */

class Clasificaciones_SelWAO extends Clasificaciones {

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
     * Method to short final scores based in penalization/time
     *
     * for Penthatlon use Penalization scores
     *  - on same score decide by speedstakes
     *  - on same decide by AgilityB, Then JumpingB, then AgilityA and last JumpingA
     *
     * for biathlon use score points
     *  - on same scores decide AgilityA+B, then Jumping A+B points
     *
     * for games use score points as sorting method
     *  - on same score decide Gamblers
     *
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
        $tj= intval($this->jornada->Tipo_Competicion);
        switch ($tj) {
            case 1: // penthatlon
                parent::sortFinal($final,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8);
                break;
            case 2: // biathlon
                usort($final,function($a,$b){
                    // si no presentado an alguna no puntua
                    //$npa=intval($a['N1'])+intval($a['N2'])+intval($a['N3'])+intval($a['N4']);
                    /*+
                        // adicionalmente si tiene dos eliminados en agility o jumping tampoco
                        (intval($a['E1'])+intval($a['E2'])===2)?0:1+
                        (intval($a['E3'])+intval($a['E4'])===2)?0:1; */
                    //$npb=intval($b['N1'])+intval($b['N2'])+intval($b['N3'])+intval($b['N4']);
                    /*+
                        // adicionalmente si tiene dos eliminados en agility o jumping tampoco
                        (intval($b['E1'])+intval($b['E2'])===2)?0:1+
                        (intval($b['E3'])+intval($b['E4'])===2)?0:1; */
                    // se coge la suma del mejor agility con la del mejor jumping
                    $puntosa=max(intval($a['Pt1']),intval($a['Pt2'])) + max(intval($a['Pt3']),intval($a['Pt4']));
                    $puntosb=max(intval($b['Pt1']),intval($b['Pt2']))+ max(intval($b['Pt3']),intval($b['Pt4']));
                    $pagilitya=intval($a['Pt1'])+intval($a['Pt2']);
                    $pagilityb=intval($b['Pt1'])+intval($b['Pt2']);

                    // no presentado en alguna manga: cero puntos
                    //if ($npa!=0) {$puntosa=0;$pagilitya=0;}
                    //if ($npb!=0) {$puntosb=0;$pagilityb=0;}

                    // se ordena por puntos
                    if ($puntosa!=$puntosb) return ($puntosa<$puntosb)?1:-1;

                    // a igualdad de puntos se ordena por puntos de agility
                    if ($pagilitya!=$pagilityb) return ($pagilitya<$pagilityb)?1:-1;

                    // a igualdad de puntos de agility se ordena por penalizacion
                    $penala=floatval($a['P1'])+floatval($a['P2'])+floatval($a['P3'])+floatval($a['P4']);
                    $penalb=floatval($b['P1'])+floatval($b['P2'])+floatval($b['P3'])+floatval($b['P4']);
                    if ($penala!=$penalb) return ($penala>$penalb)?1:-1; // ojo: signo cambia

                    // a igualdad de penalizacion se ordena por tiempo
                    $tiempoa=floatval($a['T1'])+floatval($a['T2'])+floatval($a['T3'])+floatval($a['T4']);
                    $tiempob=floatval($b['T1'])+floatval($b['T2'])+floatval($b['T3'])+floatval($b['T4']);
                    return ($tiempoa>$tiempob)?1:-1;
                });
                break;
            case 3: // games
                usort($final, function($a, $b) {
                    // a igualdad de puntos retorna mejor tiempo
                    if ( $a['Penalizacion'] == $b['Penalizacion'] )	{
                        // a igualdad de tiempo retorna mejor gambler
                        if ($a['Tiempo']==$b['Tiempo'])  return ($a['P2']<$b['P2'])?1:-1;
                        else  return ($a['Tiempo'] > $b['Tiempo'])? 1:-1;
                    }
                    // el que mas puntos tiene gana
                    return ( $a['Penalizacion'] < $b['Penalizacion'])?1:-1;
                });
                break;
            default: // this is not supposed to occur. notify and use default
                $this->myLogger->error("Clasificaciones:: invalid Tipo_Competicion:{$tj} in jornada:{$this->jornada->ID} in Games rounds");
                parent::sortFinal($final,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8);
                break;
        }
    }

    /**
     * genera la tabla de resultados finales y evalua el orden de clasificacion
     *
     * Realmente en games solo se usan las mangas de snooker y gambler, pero por compatibilidad ponemos todas
     *
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
        // si no estamos en jornada games, usamos la funcion padre
        $tj= intval($this->jornada->Tipo_Competicion);
        if ($tj!==3) return parent::evalFinal($idmangas,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8);
        $mangas=array();
        for ($i=0;$i<8;$i++) $mangas[$i]=$this->__getObject("Mangas",$idmangas[$i]);
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
                        'Puesto' => 0,
                        'Pcat' => 0
                    );
                    // anyadimos datos de cada manga
                    for($j=1;$j<9;$j++) {
                        if ($resultados[$j-1]===null) continue;
                        $participante["F{$j}"]=0; // guardamos puntos de apertura en faltas
                        $participante["R{$j}"]=0; // guardamos puntos de cierre en rehuses
                        $participante["E{$j}"]=0;
                        $participante["N{$j}"]=0;
                        $participante["T{$j}"]=0;
                        $participante["V{$j}"]=0; // no se usa
                        $participante["P{$j}"]=0;
                        $participante["C{$j}"]=0; // no se usa
                        $participante["Pt{$j}"]=0; // points -  no se usa en games
                        $participante["St{$j}"]=0; // stars - no se usa en games
                        $participante["Out{$j}"]=0;
                        $participante["Puesto{$j}"]=0;
                        $participante["Pcat{$j}"]=0;
                        $participante["Penalizacion"]=0;// default to not processed
                    }
                    // insertamos el array en la lista de participantes
                    $final[$dogID]=$participante;
                    // do_log("round:{$mangas[$i]->ID} Create Participante:{$dogID}: ".json_encode($participante));
                }
                // una vez creado -si es necesario, claro - nos ponemos y rellenamos los elementos especificos de esta manga
                $j=$i+1;
                $final[$dogID]["F{$j}"] = $item['Faltas']; // en games usamos faltas para secuencia de apertura
                $final[$dogID]["R{$j}"] = $item['Tocados']; // en games no hay rehuses, lo usamos para guardar cierre
                $final[$dogID]["E{$j}"] = $item['Eliminado'];
                $final[$dogID]["N{$j}"] = $item['NoPresentado'];
                $final[$dogID]["T{$j}"] = floatval($item['Tiempo']);
                $final[$dogID]["V{$j}"] = floatval($item['Velocidad']); // no se usa en games
                $final[$dogID]["P{$j}"] = $item['Penalizacion'];
                $final[$dogID]["C{$j}"] = $item['CShort']; // no se usa en games
                $final[$dogID]["Pt{$j}"] = $item['Puntos']; // no se usa en games
                $final[$dogID]["St{$j}"] = $item['Estrellas']; // no se usa en games
                $final[$dogID]["Out{$j}"]=0;
                $final[$dogID]["Puesto{$j}"] = $item['Puesto'];
                $final[$dogID]["Pcat{$j}"] = $item['Pcat'];
                $final[$dogID]["Tiempo"] += $final[$dogID]["T{$j}"];
                $final[$dogID]["Puntos"] += $final[$dogID]["Pt{$j}"];
                $final[$dogID]['Penalizacion'] += $final[$dogID]["P{$j}"];
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
}