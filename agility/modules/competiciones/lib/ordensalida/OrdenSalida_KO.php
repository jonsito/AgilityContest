<?php
/*
OrdenSalida_KO.php

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

require_once(__DIR__ . "/../../../Federations.php");
require_once(__DIR__ . "/../../../../server/database/classes/DBObject.php");
require_once(__DIR__ . "/../../../../server/database/classes/Equipos.php");
require_once(__DIR__ . "/../../../../server/database/classes/Resultados.php");
require_once(__DIR__ . "/../../../../server/database/classes/Clasificaciones.php");
require_once(__DIR__ . "/../../../../server/database/classes/Inscripciones.php");
require_once(__DIR__ . "/../../../../server/database/classes/OrdenSalida.php");

/**
 * Class OrdenSalida_KO
 *
 * Esta es una implementacion de la clase orden salida especifica para las pruebas KO
 *
 * En las pruebas KO _todos_ los perros inscritos aparecen en todas las mangas,
 * pero solo compiten en una manga concreta los que tienen el campo Resultados::Games==1
 *
 * En cada manga es preciso generar un orden de salida en base a los resultados anteriores:
 * - random: se cogen los perros clasificados de la manga anterior y se agrupan al azar conservando
 *   categorias
 * - same: se cogen los perros clasificados de la manga anterior respetando el orden de salida de dicha manga
 * - reverse: se ordenan por resultados los perros de la manga anterior de manera que los mejores compiten entre si
 *
 * En el metodo getData() no se ordenan por equipos (pues no hay equipos), pero se agrupan de dos en dos
 *
 */
class OrdenSalida_KO extends OrdenSalida {

	protected $mangas=null; // lista ordenada de las ocho posibles mangas de una jornada K.O.
	
	/**
	 * Constructor
     * @param {string} $file Name for this object
     * @param {object} $prueba Current prueba data
     * @param {object} $jornada Current prueba data
     * @param {object} $manga manga data
	 * @throws Exception when
	 * - cannot contact database
	 * - invalid manga ID
	 */
	function __construct($file,$prueba=null,$jornada=null,$manga=null) {
		parent::__construct("{$file} (K.O.)",$prueba,$jornada,$manga);
        // guardamos las mangas de la jornada.
        $res=$this->__select("*","Mangas","Jornada={$this->jornada->ID}","Tipo ASC");
        if (!$res) throw new Exception ("No rounds found for KO Journey {$this->jornada->ID}");
        // Debe retornar un array de ocho entradas ordenadas por tipo
        if ($res['total']!=8) throw new Exception ("KO Journey {$this->jornada->ID} must have 8 rounds");
        $this->mangas=$res['rows'];
	}

    /**
     * Search parent round for this ko journey
     * @return {object} parent row data, current round when first or null if not found
     * @throws Exception
     *
     * No podemos utilizar mangas::getHermanas, debido a que por necesidades del guion, cada manga KO
     * tiene un identificador de tipo distinto... afortunadamente en una jornada ko solo puede haber mangas ko
     * ( tipo_manga= 15,18,19,20,21,22,23,24 )
     */
	private function getParentRound() {
        $curr=$this->manga->ID;
	    for ($n=0;$n<7;$n++) { // mangas array is sorted by Tipo ( and possibly by ID, but unsure )
	        if ($this->mangas[$n]['ID'] != $curr ) continue;
	        // return current round if first one; else parent round (in object format)
	        return ($n==0)? $this->manga : json_decode(json_encode($this->mangas[$n-1]));
        }
        // arriving here means parent row not found. Should not occurs
        $this->myLogger->error("Cannot find parent round for journey:{$this->jornada->ID} round:{$this->manga->ID}");
	    return null;
    }

	/**
	 * Obtiene la lista (actualizada) de perros de una manga en el orden de salida correcto
	 *
	 * En AgilityContest-2.0 : el campo "Orden_Salida" de la tabla mangas no especifica el orden
	 * real, sino el orden relativo entre perros cuando tienen la misma categoria,celo,equipo; 
	 * De hecho perros de diferente categoria celo y equipo están mezclados, y hace falta que esta funcion
	 * los ordene segun el resultado final deseado
	 * 
	 * @param {boolean} teamview true->intercalar información de equipos en el listado 
	 * @param {integer} catmode categorias a tener en cuenta en el listado que hay que presentar
	 * @param {array} rs lista de resultados a presentar. Se utiliza para reordenar resultados en funcion del orden de salida
	 */
	function getData($teamView=false,$catmode=8,$rs=null) {
		// obtenemos los perros de la manga, anyadiendo los datos que faltan (NombreLargo y NombreEquipo) a partir de los ID's
		if (!$rs) $rs= $this->__select(
			"Resultados.*,Equipos.Nombre AS NombreEquipo,
			PerroGuiaClub.NombreLargo AS NombreLargo,PerroGuiaClub.LogoClub AS LogoClub,
			PerroGuiaClub.Pais,PerroGuiaClub.Genero,PerroGuiaClub.LOE_RRC AS LOE_RRC,
			Inscripciones.Observaciones AS Observaciones, 1 AS PerrosPorGuia",
			"Resultados,Equipos,PerroGuiaClub,Inscripciones",
			// solo se cogen los perros clasificados para dicha ronda, esto es con campo Games!=0
			"(Resultados.Games!=0) && (Inscripciones.Prueba={$this->prueba->ID}) AND (Inscripciones.Perro=Resultados.Perro) AND
			(Manga={$this->manga->ID}) AND (Resultados.Equipo=Equipos.ID) AND (Resultados.Perro=PerroGuiaClub.ID)",
			"",
			""
		);
		if(!is_array($rs)) return $this->error($this->conn->error);
		$p1=array();
		$guias=array();
		foreach ($rs['rows'] as $resultado) {
			// recreamos el array de perros anyadiendo el ID del perro como clave
			$p1[$resultado['Perro']]=$resultado;
			// generamos lista de guias y los perros que tiene cada uno
			if (array_key_exists($resultado['NombreGuia'],$guias)) $guias[$resultado['NombreGuia']]++;
			else $guias[$resultado['NombreGuia']]=1;
		}

		// primera pasada: ajustamos los perros segun el orden de salida que figura en Orden_Salida
		// excluyendo a aquellos cuya categoria no coincide con la solicitada
		$p2=array();
		$listas=$this->splitPerrosByMode($this->getOrden(),$catmode);
		$orden=explode(',',$listas[1]); // cogemos la lista de los perros incluidos
		foreach ($orden as $perro) {
			if ($perro==="BEGIN") continue;
			if ($perro==="END") continue;
			if ($perro==="") continue;
			// analizamos la lista. no todos los perros estaran en los resultados ( solo aquellos con Games!=0 )
			if (array_key_exists(intval($perro),$p1)) {
				// insertamos el numero de perros que tiene el guia
				$p1[$perro]['PerrosPorGuia']= $guias[$p1[$perro]['NombreGuia']];
				array_push($p2,$p1[$perro]);
			}
		}

		// en una prueba ko no hay equipos, luego nos saltamos la ordenacion por equipos :-)

		// segunda pasada: ordenar por celos. No es que tenga mucho sentido en KO, pero bueno...
		$p3=array();
        foreach(array(0,1) as $celo) {
            foreach ($p2 as $perro) {
                if ($perro['Celo']==$celo) array_push($p3,$perro);
            }
        }

		// tercera pasada: ordenar por categoria respetando el orden de tandas definido en el programa de la jornada

		// en las pruebas KO se intenta que perros de la misma categoria compitan entre si,
		// hasta que no haya mas remedio que mezclarlos
        $cats=implode(',',Tandas::getTandasByTipoManga($this->manga->Tipo)); // tipos de tanda asociados a la manga
        $this->myLogger->trace("Cats:'$cats' tipomanga:{$this->manga->Tipo} ");
        $res=$this->__select(
            "Categoria",
            "Tandas",
            "(Tandas.Jornada={$this->jornada->ID}) AND (Tandas.Tipo IN ($cats)) ","
				Orden ASC"
        );
        // ordenamos segun el orden de categorias establecido en las tandas
        $p4=array();
        foreach ($res['rows'] as $item) {
            if (strpos($item['Categoria'],"LMS")!==FALSE ) $item['Categoria']="-LMST";
            // si la tanda tiene mas de una categoria, hacemos un split y separamos internamente
            $cats=str_split(($item['Categoria']));
            foreach($cats as $cat) {
                foreach ($p3 as $perro) {
                    if ($cat==$perro['Categoria']) array_push($p4,$perro);
                }
            }
        }

		// cuarta: agrupar los perros de dos en dos para que aparezcan por parejas
		// en el menu de introduccion de datos de la consola
		//
		// adicionalmente, en las pruebas por equipos anyadimos al resultado un array
		// extra indicando los equipos y su orden de salida. Este array es distinto al
		// obtenido con getOrdenEquipos, pues si un equipo tiene perros en varias categorias,
		// el equipo aparecera varias veces en este resultado
        $p5=array();
        $count=0;
        foreach ($p4 as $perro) {
            if (($count%2) === 0){ // nueva pareja
                // intercala info de la pareja en orden de salida general
                $a=array(
                    'Dorsal' => '*',
                    'Nombre'=>_('Pair'),
                    'NombreGuia'=> strval(intval($count/2)+1),
                    'Eliminado'=>0,
                    'NoPresentado'=>0
                );
                array_push($p5,$a);
            }
            array_push($p5,$perro); // introduce datos de perro
			$count++;
        }
        // that's all folks. Compose result data and return
		return array("total" => count($p5), "rows" => $p5);
	}

    /**
     * Esta funcion
	 * - Prepara las mangas iniciales ( si estamos en primera manga )
	 * - Clona el orden de salida en las mangas siguientes
	 *
	 * En el caso de la primera manga no hay resultados anteriores; en ese caso, lo que haremos sera componer
	 * la primera y segunda mangas, aplicando el criterio:
	 * - participantes manga 2: potencia de dos mas proxima al numero de participantes menos sobrantes
	 * - participantes manga 1: doble de los sobrantes en la manga 2
	 * - los participantes en la manga 1 se ponen con Games=1 en la manga 1 y Games=0 en manga 2
	 * - los participantes en la manga 2 se ponen con Games=0 en la manga 1 y Games=1 en manga 2
     */
	private function prepareMangas() {
		// if not first round, nothing to do
		if ($this->manga->ID==$this->mangas[0]['ID']) {

            // marcamos todos los perros de la jornada con Games=0 excepto la segunda que se pone a 1
            $sql="UPDATE Resultados SET Games=0 WHERE Jornada = {$this->manga->Jornada}";
            $res=$this->query($sql);
            if (!$res) $this->myLogger->error($this->conn->error);
            $sql="UPDATE Resultados SET Games=1 WHERE Manga = {$this->mangas[1]['ID']}";
            $res=$this->query($sql);
            if (!$res) $this->myLogger->error($this->conn->error);

            // contamos los participantes de la primera manga. metodo simple: hacer un count(split(ordensalida)) - 2
            $number=count(explode(',',$this->getOrden()))-2;

            // buscamos la potencia de dos mas proxima al numero de participantes y cogemos la diferencia
            $powerof2=256; // to revise

            if ($number==$powerof2) {
                // si el numero de participantes es potencia de dos, la primera manga ya esta lista;
                // ajustamos el valor de games a 1 en primera manga y a cero en todas las demas
                $sql="UPDATE Resultados SET Games=0 WHERE Jornada = {$this->manga->Jornada}";
                $res=$this->query($sql);
                if (!$res) $this->myLogger->error($this->conn->error);
                $sql="UPDATE Resultados SET Games=1 WHERE Manga = {$this->manga->ID}";
                $res=$this->query($sql);
                if (!$res) $this->myLogger->error($this->conn->error);
            } else {
                // si no, por cada perro que sobra, cogemos dos perros de la segunda manga
                // y los movemos a la primera setGames(perro,round1)=1; setGames(perro,round2)=0
            }
        }

		// clonamos el orden de salida de esta manga en todas las siguientes
		// En esta jornada solo puede haber mangas KO, y que el tipo de manga esta ordenado por rondas
		// por lo que esta query es suficiente
		$orden=$this->getOrden();
		$sql="UPDATE Mangas SET OrdenSalida = '{$orden}' WHERE Jornada={$this->manga->Jornada} AND Tipo>{$this->manga->Tipo}";
        $res=$this->query($sql);
        if (!$res) $this->myLogger->error($this->conn->error);
        // that's all
        return;
	}

	/**
	 * Reordena el orden de salida de las categorias indicadas de una manga al azar
	 * En mangas KO catsmode se ignora (entran todos )
	 * @param	{int} $catmode categorias a las que tiene que afectar este cambio
	 * @return {string} nuevo orden de salida
	 */
	function random($catmode=8) {
		// si estamos en la primera manga hay que hacer sorteo completo
		// si no, hay que ver los ganadores de la manga anterior, y marcar su campo Games de la manga actual a 1

		// fase 1 aleatorizamos la manga
		$orden=$this->getOrden();
		// buscamos los perros de la categoria seleccionada. En mangas KO catsmode se ignora ( entran todos )
		$listas=$this->splitPerrosByMode($orden,8);
        $str1=$listas[2];
        $str2=implode(",",aleatorio(explode(",", $listas[1])));
        $ordensalida=$this->joinOrders($str1,$str2);
        $this->setOrden($ordensalida);

		// En rondas KO solo esta el equipo por defecto. no hay que reordenar equipos
		return $ordensalida;
	}

	/**
	 * Pone el mismo orden de salida que la manga KO anterior en las categorias solicitadas
     * Si se trata de la primera manga equivale a un random()
	 * @param	{int} $catmode categorias a las que tiene que afectar este cambio
	 * @return {string} nuevo orden de salida; null on error
	 */
	function sameorder($catmode=8) {
		$this->myLogger->enter();

        // fase 1: buscamos la "manga padre"
        $mpadre=$this->getParentRound();
        if(!$mpadre)
            return $this->error("Error find parent round for KO journey:{$this->jornada->ID} and manga:{$this->manga->ID}");
        if ($mpadre==$this->manga) {
            $this->myLogger->notice("Current round:{$this->manga->ID} is already first for KO Journey:{$this->jornada->ID} ");
            return $this->getOrden();
        }

		// spliteamos manga propia y hermana, y las mezclamos en funcion de la categoria
		$lista=$this->splitPerrosByMode($this->manga->Orden_Salida,$catmode); // manga actual "splitteada"
		$lista2=$this->splitPerrosByMode($mpadre->Orden_Salida,$catmode); // manga hermana "splitteada"
        $str1=$lista[2];
        $str2=$lista2[1];
        $ordensalida=$this->joinOrders($str1,$str2);
        $this->setOrden($ordensalida);

        // hacemos lo mismo con el orden de equipos
        $lista=$this->splitEquiposByMode($this->manga->Orden_Equipos,$catmode); // manga actual "splitteada"
        $lista2=$this->splitEquiposByMode($mpadre->Orden_Equipos,$catmode); // manga hermana "splitteada"
        $str1=$lista[2];
        $str2=$lista2[1];
        $ordenequipos=$this->joinOrders($str1,$str2);
        $this->setOrdenEquipos($ordenequipos);

		$this->myLogger->leave();
		return $ordenequipos;
	}

	/**
	 * Calcula el orden de salida de la(s) categoria(s) indicadas
	 * de manga en funcion del orden inverso al resultado de la manga KO anterior
     * Si estamos en la primera manga retorna la manga actual
	 * @return {string} nuevo orden de salida; null on error
	 */
	function reverse($catmode=8) {
		$this->myLogger->enter();
		// fase 1: buscamos la "manga padre"
        $mpadre=$this->getParentRound();
        if(!$mpadre)
            return $this->error("Error find parent round for KO journey:{$this->jornada->ID} and manga:{$this->manga->ID}");
        if ($mpadre==$this->manga) {
            $this->myLogger->notice("Current round:{$this->manga->ID} is already first for KO Journey:{$this->jornada->ID} ");
            return $this->getOrden();
        }
	
		// fase 2: evaluamos resultados de la manga padre
		$this->myLogger->trace("El orden de salida original para manga:{$this->manga->ID} ".
                                    "jornada:{$this->jornada->ID} es:\n{$this->manga->Orden_Salida}");
		// En funcion del tipo de recorrido tendremos que leer diversos conjuntos de Resultados
		switch($this->manga->Recorrido) {
			case 0: // Large,medium,small (3-heighs) Large,medium,small,tiny (4-heights)
				$this->invierteResultados($mpadre,0,$catmode);
				$this->invierteResultados($mpadre,1,$catmode);
				$this->invierteResultados($mpadre,2,$catmode);
				if ($this->federation->get('Heights')==4) $this->invierteResultados($mpadre,5,$catmode);
				break;
			case 1: // Large,medium+small (3heights) Large+medium,Small+tiny (4heights)
				if ($this->federation->get('Heights')==3) {
					$this->invierteResultados($mpadre,0,$catmode);
					$this->invierteResultados($mpadre,3,$catmode);
				} else {
					$this->invierteResultados($mpadre,6,$catmode);
					$this->invierteResultados($mpadre,7,$catmode);
				}
				break;
			case 2: // conjunta L+M+S (3 heights) L+M+S+T (4heights)
				if ($this->federation->get('Heights')==3) {
					$this->invierteResultados($mpadre,4,$catmode);
				} else  {
					$this->invierteResultados($mpadre,8,$catmode);
				}
				break;
		}
		$nuevo=$this->getOrden();
		$this->myLogger->trace("El orden de salida nuevo para manga:{$this->manga->ID} jornada:{$this->jornada->ID} es:\n$nuevo");
		$this->myLogger->leave();
		return $nuevo;
	}

} // class

?>
