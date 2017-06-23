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
		parent::__construct("{$file} (K.O.)",$prueba,$jornada,$manga);

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
            	"Mangas.*",
				"Mangas,Jornadas",
				"Mangas.Jornada=Jornadas.ID ".
						" AND Jornadas.Prueba={$this->prueba->ID}".
						" AND Jornadas.Tipo_Competicion=4",
				"Mangas.ID ASC");
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
			if (Mangas::isAgility($res['rows'][0])) $result[0]=$res['rows'][0]; else $result[1]=$res['rows'][0];
            if (Mangas::isAgility($res['rows'][1])) $result[0]=$res['rows'][1]; else $result[1]=$res['rows'][1];
            $this->mangas=$result;
        }
        return $this->mangas;
    }

	/**
	 * Pone el mismo orden de salida que el Agility de la manga de clasificacion
	 * omitiendo los perros que no se han clasificado
	 * @param	{int} $catmode categorias a las que tiene que afectar este cambio
	 * @return {string} nuevo orden de salida; null on error
	 */
	function sameorder($catmode=8) {
		$this->myLogger->enter();

        // fase 1: buscamos la "manga padre"
        $mpadre=$this->getParentRounds();
        if(!$mpadre || !$mpadre[0])
            return $this->error("Error: Cannot find Qualification Agility Round in this contest");

		// spliteamos manga propia y hermana, y las mezclamos en funcion de la categoria
		$lista=$this->splitPerrosByMode($this->manga->Orden_Salida,$catmode); // manga actual "splitteada"
		$lista2=$this->splitPerrosByMode($mpadre[0]->Orden_Salida,$catmode); // manga padre "splitteada"
        $str1=$lista[2];
        $str2=$lista2[1];
        $ordensalida=$this->joinOrders($str1,$str2);
        $this->setOrden($ordensalida);

        // hacemos lo mismo con el orden de equipos
        $lista=$this->splitEquiposByMode($this->manga->Orden_Equipos,$catmode); // manga actual "splitteada"
        $lista2=$this->splitEquiposByMode($mpadre[0]->Orden_Equipos,$catmode); // manga padre "splitteada"
        $str1=$lista[2];
        $str2=$lista2[1];
        $ordenequipos=$this->joinOrders($str1,$str2);
        $this->setOrdenEquipos($ordenequipos);

		$this->myLogger->leave();
		return $ordenequipos;
	}

	/**
	 * Calcula el orden de salida de la(s) categoria(s) indicadas para la final del European open
	 * de manga en funcion del orden inverso al resultado de la ronda clasificatoria
	 * Para ello
	 * - Cogemos el orden inverso de resultados (Agility y Jumping)
	 * - Vamos alternando empezando por jumping y siguiendo por agility, omitiendo los
	 *   perros que no esten clasificados
	 *
	 * @return {string} nuevo orden de salida; null on error
	 */
	function reverse($catmode=8) {
		$this->myLogger->enter();
		// fase 1: buscamos la "manga padre"
        $mpadre=$this->getParentRounds();
        if(!$mpadre || !$mpadre[0] || !$mpadre[1])
            return $this->error("Cannot find EO Qualification Journey for contest: {$this->prueba->ID}");
		// fase 2: evaluamos resultados de la manga padre
		$this->myLogger->trace("El orden de salida original para manga:{$this->manga->ID} ".
                                    "jornada:{$this->jornada->ID} es:\n{$this->manga->Orden_Salida}");

		// En funcion del tipo de recorrido tendremos que leer diversos conjuntos de Resultados

        $orden_agility=$this->getOrden();
        $orden_jumping=$this->getOrden();
		// invertimos el resultado para la manga de agility
		switch($this->manga->Recorrido) {
			case 0: // Large,medium,small (3-heighs) Large,medium,small,tiny (4-heights)

				// agility
				$this->invierteResultados($mpadre[0],0,$catmode);
				$this->invierteResultados($mpadre[0],1,$catmode);
				$this->invierteResultados($mpadre[0],2,$catmode);
				if ($this->federation->get('Heights')==4)
					$this->invierteResultados($mpadre[0],5,$catmode);
                $orden_agility=$this->getOrden();

                // jumping
                $this->invierteResultados($mpadre[1],0,$catmode);
                $this->invierteResultados($mpadre[1],1,$catmode);
                $this->invierteResultados($mpadre[1],2,$catmode);
                if ($this->federation->get('Heights')==4)
                    $this->invierteResultados($mpadre[1],5,$catmode);
                $orden_jumping=$this->getOrden();
				break;
			case 1: // Large,medium+small (3heights) Large+medium,Small+tiny (4heights)

				// agility
				if ($this->federation->get('Heights')==3) {
					$this->invierteResultados($mpadre[0],0,$catmode);
					$this->invierteResultados($mpadre[0],3,$catmode);
				} else {
					$this->invierteResultados($mpadre[0],6,$catmode);
					$this->invierteResultados($mpadre[0],7,$catmode);
				}
                $orden_agility=$this->getOrden();

				// jumping
                if ($this->federation->get('Heights')==3) {
                    $this->invierteResultados($mpadre[1],0,$catmode);
                    $this->invierteResultados($mpadre[1],3,$catmode);
                } else {
                    $this->invierteResultados($mpadre[1],6,$catmode);
                    $this->invierteResultados($mpadre[1],7,$catmode);
                }
                $orden_jumping=$this->getOrden();
				break;
			case 2: // conjunta L+M+S (3 heights) L+M+S+T (4heights)

				// agility
				if ($this->federation->get('Heights')==3) {
					$this->invierteResultados($mpadre[0],4,$catmode);
				} else  {
					$this->invierteResultados($mpadre[0],8,$catmode);
				}
                $orden_agility=$this->getOrden();

				// jumping
                if ($this->federation->get('Heights')==3) {
                    $this->invierteResultados($mpadre[1],4,$catmode);
                } else  {
                    $this->invierteResultados($mpadre[1],8,$catmode);
                }
                $orden_jumping=$this->getOrden();
				break;
		}

        // ok. ahora tenemos los ordenes de salida de agility y jumping invertidos
		// con unicamente los perros clasificados
		// generamos el orden final alternando mangas de jumping y agility
        $oagility=explode(",",getInnerString($orden_agility,"BEGIN,",",END"));
        $ojumping=explode(",",getInnerString($orden_jumping,"BEGIN,",",END"));
        $orden="BEGIN,END";
        $size=max(count($oagility),count($ojumping));
        for($n=0;$n<$size;$n++) {
            if (array_key_exists($n,$ojumping)) $orden=list_insert($ojumping[$n],$orden);
            if (array_key_exists($n,$oagility)) $orden=list_insert($oagility[$n],$orden);
		}
		// ok. retornamos nuevo orden
		$this->myLogger->trace("El nuevo orden de salida manga:{$this->manga->ID} jornada:{$this->jornada->ID} es:\n$orden");
		$this->setOrden($orden);
		$this->myLogger->leave();
		return $orden;
	}

} // class

?>
