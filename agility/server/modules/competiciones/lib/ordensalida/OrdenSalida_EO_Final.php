<?php
/*
OrdenSalida_EO_Final.php

Copyright  2013-2020 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

class OrdenSalida_EO_Final extends OrdenSalida {

    // lista ordenada de las mangas (Agility/Jumping) de la clasificatoria individual
	protected $mangas=null;
	
	/**
	 * Constructor
	 * @param {string} Name for this object
	 * @param {integer} $manga Manga ID
	 * @throws Exception when
	 * - cannot contact database
	 * - invalid manga ID
	 */
	function __construct($file,$prueba=null,$jornada=null,$manga=null) {
		parent::__construct("{$file} (EO Final Individual)",$prueba,$jornada,$manga);

	}

    /**
	 * Busca la jornada y mangas de las que extraer el orden de salida
	 * Hay que buscar el agility y el jumping de una jornada tipo EO Qualification Individual
     */
	private function getParentRounds() {
	    // if no list of rounds (yet) create and store it
	    if ($this->mangas==null) {
            // guardamos las mangas de la jornada. Debe retornar un array de ocho entradas
            $res=$this->__select(
            	"mangas.*",
				"mangas,jornadas",
				"mangas.Jornada=jornadas.ID ".
						" AND jornadas.Prueba={$this->prueba->ID}".
						" AND jornadas.Tipo_Competicion=4",
				"mangas.ID ASC");
            if (!$res) {
                $this->myLogger->error("No EO Ind. Qualification Rounds found on prueba {$this->prueba->ID}");
                return null;
            }
            if ($res['total']!=2) {
                $this->myLogger->error ("EO Individual Qualification Journey must have 2 rounds");
                return null;
            }
            // now assign Agility to round 0 and Jumping to Round 1
			$result=array(null,null);
			if (Mangas::isAgility($res['rows'][0]['Tipo'])) $result[0]=$res['rows'][0]; else $result[1]=$res['rows'][0];
            if (Mangas::isAgility($res['rows'][1]['Tipo'])) $result[0]=$res['rows'][1]; else $result[1]=$res['rows'][1];
            $this->mangas=$result;
        }
        return $this->mangas;
    }

	/**
	 * Pone el mismo orden de salida que el Agility de la manga de clasificacion
	 * omitiendo los perros que no se han clasificado
	 * @param	{int} $catmode categorias a las que tiene que afectar este cambio
     * @param {boolean} reverse on true return selected dogs/teams in reverse order
	 * @return {string} nuevo orden de salida; null on error
	 */
	function sameOrder($catmode,$reverse,$range) {
		$this->myLogger->enter();
        assertClosedJourney($this->jornada); // throw exception on closed journeys
        // fase 1: buscamos la "manga padre"
        $mpadre=$this->getParentRounds();
        if(!$mpadre || !$mpadre[0])
            return $this->error("Error: Cannot find Qualification Agility Round in this contest");

		// spliteamos manga propia y hermana, y las mezclamos en funcion de la categoria
		$lista=$this->splitPerrosByMode($this->getOrden(),$catmode,false,$range); // manga actual "splitteada"
		$lista2=$this->splitPerrosByMode($mpadre[0]['Orden_Salida'],$catmode,$reverse,$range); // manga padre "splitteada"
        $str1=$lista[2];
        $str2=$lista2[1];
        $ordensalida=$this->joinOrders($str1,$str2);
        $this->setOrden($ordensalida);

        // NO SE TOCA EL ORDEN DE LOS EQUIPOS:
		// - Esta es una prueba individual
		// - Los id del equipo por defecto difieren
		$this->myLogger->leave();
		return $this->getOrdenEquipos();
	}

	/**
	 * Calcula el orden de salida de la(s) categoria(s) indicadas para la final del European open
	 * de manga en funcion del orden inverso al resultado de la ronda clasificatoria
	 * Para ello
	 * - Cogemos el orden inverso de resultados (Agility y Jumping)
	 * - Vamos alternando empezando por jumping y siguiendo por agility, omitiendo los
	 *   perros que no esten clasificados
	 *
	 * @param {string} catmode categorias que hay que ordenar en la manga (X,L,M,S,T)
	 * @param {string} range ( rango de perros a ordenar. No usado aqui )
	 * @return {string} nuevo orden de salida; null on error
	 * @throws Exception on invalid journey
	 */
	function orderByResults($catmode,$range) {
		$this->myLogger->enter();
        assertClosedJourney($this->jornada); // throw exception on closed journeys
		// fase 1: buscamos la "manga padre"
        $mpadre=$this->getParentRounds();
        if(!$mpadre || !$mpadre[0] || !$mpadre[1])
            return $this->error("Cannot find EO Qualification Journey for contest: {$this->prueba->ID}");
		// fase 2: evaluamos resultados de la manga padre
		$this->myLogger->trace("El orden de salida original para manga:{$this->manga->ID} ".
                                    "jornada:{$this->jornada->ID} es:\n{$this->manga->Orden_Salida}");

		// En funcion del tipo de recorrido tendremos que leer diversos conjuntos de Resultados

        $orden_agility=$this->getOrden();
        $orden_jumping=$this->getOrden(); // just initialize with current data
		// invertimos el resultado para la manga de agility
        $magility=$clasa = json_decode(json_encode($mpadre[0]));
        $mjumping=$clasa = json_decode(json_encode($mpadre[1]));
        $heights=Competitions::getHeights($this->prueba->ID,$this->jornada->ID,$this->manga->ID);
		switch($this->manga->Recorrido) {
			case 0: // Large,medium,small (3-heighs) Large,medium,small,tiny (4-heights)

				// agility
				$this->invierteResultados($magility,0,$catmode); // L
				$this->invierteResultados($magility,1,$catmode); // M
				$this->invierteResultados($magility,2,$catmode); // S
				if ($heights!=3) $this->invierteResultados($magility,5,$catmode); // T
				if ($heights==5) $this->invierteResultados($magility,9,$catmode); // X
                $orden_agility=$this->getOrden();

                // jumping
                $this->invierteResultados($mjumping,0,$catmode); // L
                $this->invierteResultados($mjumping,1,$catmode); // M
                $this->invierteResultados($mjumping,2,$catmode); // S
				if ($heights!=3) $this->invierteResultados($mjumping,5,$catmode); // T
				if ($heights==5) $this->invierteResultados($mjumping,9,$catmode); // X
                $orden_jumping=$this->getOrden();
				break;
			case 1: // Large,medium+small (3heights) Large+medium,Small+tiny (4heights) XL,MST (5heights)
				if ($heights==3) {
					$this->invierteResultados($magility,0,$catmode); // L
					$this->invierteResultados($magility,3,$catmode); // MS
					$orden_agility=$this->getOrden();
					$this->invierteResultados($mjumping,0,$catmode); // L
					$this->invierteResultados($mjumping,3,$catmode); // MS
					$orden_jumping=$this->getOrden();
				}
				if ($heights==4) {
					$this->invierteResultados($magility,6,$catmode); // LM
					$this->invierteResultados($magility,7,$catmode); // ST
					$orden_agility=$this->getOrden();
					$this->invierteResultados($mjumping,6,$catmode); // LM
					$this->invierteResultados($mjumping,7,$catmode); // ST
					$orden_jumping=$this->getOrden();
				}
				if ($heights==5) {
					$this->invierteResultados($magility,10,$catmode); // XL
					$this->invierteResultados($magility,11,$catmode); // MST
					$orden_agility=$this->getOrden();
					$this->invierteResultados($mjumping,10,$catmode); // XL
					$this->invierteResultados($mjumping,11,$catmode); // MST
					$orden_jumping=$this->getOrden();
				}
				break;
			case 2: // conjunta L+M+S (3 heights) L+M+S+T (4heights) X+L+M+S+T (5heights)
				if ($heights==3) {
					$this->invierteResultados($magility,4,$catmode); // LMS
					$orden_agility=$this->getOrden();
					$this->invierteResultados($mjumping,4,$catmode);
					$orden_jumping=$this->getOrden();
				}
				if ($heights==4) {
					$this->invierteResultados($magility,8,$catmode); // LMST
					$orden_agility=$this->getOrden();
					$this->invierteResultados($mjumping,8,$catmode);
					$orden_jumping=$this->getOrden();
				}
				if ($heights==5) {
					$this->invierteResultados($magility,12,$catmode); // XLMST
					$orden_agility=$this->getOrden();
					$this->invierteResultados($mjumping,12,$catmode);
					$orden_jumping=$this->getOrden();

				}
				break;
			case 3: // 3 groups XL,M,ST 5heights
				$this->invierteResultados($magility,10,$catmode); // XL
				$this->invierteResultados($magility,1,$catmode); // M
				$this->invierteResultados($magility,7,$catmode); // ST
				$orden_agility=$this->getOrden();
				$this->invierteResultados($mjumping,10,$catmode); // XL
				$this->invierteResultados($mjumping,1,$catmode); // M
				$this->invierteResultados($mjumping,7,$catmode); // ST
				$orden_jumping=$this->getOrden();
				break;
		}

        // ok. ahora tenemos los ordenes de salida de agility y jumping invertidos
		// con unicamente los perros clasificados
		// generamos el orden final alternando mangas de agility y jumping
        $oagility=explode(",",getInnerString($orden_agility,"BEGIN,",",END"));
        $ojumping=explode(",",getInnerString($orden_jumping,"BEGIN,",",END"));
        $orden="BEGIN,END";
        $size=max(count($oagility),count($ojumping));
        for($n=0;$n<$size;$n++) {
            if (array_key_exists($n,$oagility)) $orden=list_insert($oagility[$n],list_remove($oagility[$n],$orden));
            if (array_key_exists($n,$ojumping)) $orden=list_insert($ojumping[$n],list_remove($ojumping[$n],$orden));
		}
		// ok. retornamos nuevo orden
		$this->myLogger->trace("El nuevo orden de salida manga:{$this->manga->ID} jornada:{$this->jornada->ID} es:\n$orden");
		$this->setOrden($orden);
		$this->myLogger->leave();
		return $orden;
	}

} // class

?>
