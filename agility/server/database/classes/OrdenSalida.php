<?php
/*
OrdenSalida.php

Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__ . "/../../modules/Federations.php");
require_once(__DIR__ . "/../../modules/Competitions.php");
require_once("Resultados.php");

class OrdenSalida extends DBObject {

	// tablas utilizadas para componer e insertar los idperroes en el string de orden de salida
	public static $default_orden = "BEGIN,END";
	
	protected $prueba=null; // {object} prueba data
	protected $jornada=null; // {object} jornada data
	protected $manga=null; // {object} manga data
	protected $federation=null; // {object} federation info
	protected $heights;
	
	/**
	 * Constructor
	 * @param {string} $file Name for this object
     * @param {object} $prueba Current prueba data
     * @param {object} $jornada Current prueba data
	 * @param {object} $manga manga data
	 * @throws Exception when
	 * - cannot contact database
	 * - invalid prueba/jornada/manga
	 */
	function __construct($file,$prueba,$jornada=null,$manga=null) {
		parent::__construct($file);
		if ($manga==null) {
			$this->errormsg="OrdenSalida::construct(): manga is null";
			throw new Exception($this->errormsg);
		}
        $this->manga=$manga;
		if ($jornada==null) {
			$this->errormsg="OrdenSalida::construct(): jornada is null on  manga:{$this->manga->ID}";
			throw new Exception($this->errormsg);
		}
        $this->jornada=$jornada;
		if ($prueba==null) {
			$this->errormsg="OrdenSalida::construct(): prueba is null on jornada:{$this->jornada->ID} manga:{$this->manga->ID}";
			throw new Exception($this->errormsg);
		}
        $this->prueba=$prueba;
		$this->federation=Federations::getFederation(intval($this->prueba->RSCE));
		if ($this->federation===null) {
			$this->errormsg="OrdenSalida::construct(): Cannot get federation info on prueba:{$this->prueba->ID} ".
                            "jornada:{$this->jornada->ID} manga:{$this->manga->ID}";
			throw new Exception($this->errormsg);
		}
		$this->heights=Competitions::getHeights($this->prueba->ID,$this->jornada->ID,$this->manga->ID);
	}
	
	/**
	 * Retrieve Mangas.Orden_Salida
	 * @return {string} orden de salida.
	 */
	function getOrden() {
        if ($this->manga->Orden_Salida==="") {
            $this->manga->Orden_Salida=OrdenSalida::$default_orden;
            $this->setOrden(OrdenSalida::$default_orden);
        }
		return $this->manga->Orden_Salida;
	}

	/**
	 * Update Mangas.Orden_Salida with new value
	 * @param {string} $orden new orden_salida
	 * @return {string} "" if success; errormsg on error
	 */
	function setOrden($orden) {
		if (preg_match("/BEGIN,([0-9]+,)*END/",$orden)!==1) {
			$this->errormsg="OrdenSalida::setOrden(): orden de salida invalido:'$orden'";
			$this->myLogger->error($this->errormsg);
			return $this->errormsg;
		}
		$sql = "UPDATE mangas SET Orden_Salida = '$orden' WHERE ( ID={$this->manga->ID} )";
		$rs = $this->query ($sql);
		// do not call $rs->free() as no resultset returned
		if (!$rs) return $this->error($this->conn->error);
		$this->manga->Orden_Salida=$orden;
		return "";
	}

	/**
	 * Retrieve Mangas.Orden_Equipos
	 * @return {string} orden de salida.
	 */
	function getOrdenEquipos() {
		$defOrden="BEGIN,{$this->jornada->Default_Team},END";
		if ($this->manga->Orden_Equipos==="") {
			$this->manga->Orden_Equipos = $defOrden;
			$this->setOrdenEquipos($defOrden);
		}
		return $this->manga->Orden_Equipos;
	}

    /**
     * Update Mangas.Orden_Equipos with new value
     * @param {string} $orden new orden_equipos
     * @return {string} "" if success; errormsg on error
     */
    function setOrdenEquipos($orden) {
        if (preg_match("/BEGIN,([0-9]+,)*END/",$orden)!==1) {
            $this->errormsg="OrdenSalida::setOrdenEquipos(): orden de equipos invalido:'$orden'";
            $this->myLogger->error($this->errormsg);
            // return $this->errormsg;
            $orden=$this->getOrdenEquipos(); // use default order for backward compatibility
        }
        $sql = "UPDATE mangas SET Orden_Equipos = '$orden' WHERE ( ID={$this->manga->ID} )";
        $rs = $this->query ($sql);
        // do not call $rs->free() as no resultset returned
        if (!$rs) return $this->error($this->conn->error);
        $this->manga->Orden_Equipos=$orden;
        return "";
    }

	/**
	 * Join two order strings into one
     * NOTICE: input data comes WITHOUT BEGIN/END tags
	 * @param {string} $s1
	 * @param {string} $s2
	 */
	protected function joinOrders($s1,$s2) {
		$a=($s1==="")?"":",$s1";
		$b=($s2==="")?"":",$s2";
		$res="BEGIN".$a.$b.",END";
		return $res;
	}

	/**
	 *  coge el string con el ID del perro y lo inserta al final
	 *  @param {integer} idperro ID perro 
	 */
	function insertIntoList($idperro) {
		$ordensalida=$this->getOrden();
		$res=list_insert($idperro,$ordensalida);
		return $this->setOrden($res);
	}

    /**
     *  coge el string con el ID del equipo y lo inserta al final
     *  @param {integer} idteam ID del equipo
     */
    function insertIntoTeamList($idteam) {
        $ordenequipos=$this->getOrdenEquipos();
        $res=list_insert($idteam,$ordenequipos);
        return $this->setOrdenEquipos($res);
    }

	/**
	 * Elimina un idperro del orden de salida
	 * @param {integer} $idperro
	 * @return {string} "" on success; else error message
	 */
	function removeFromList($idperro) {
		$ordensalida=$this->getOrden();
		$res=list_remove($idperro,$ordensalida);
		return $this->setOrden($res);
	}

    /**
     * Elimina un equipo del orden de equipos
     * @param {integer} $idteam
     * @return {string} "" on success; else error message
     */
    function removeFromTeamList($idteam) {
        $ordenequipos=$this->getOrdenEquipos();
        $res=list_remove($idteam,$ordenequipos);
        return $this->setOrdenEquipos($res);
    }

    /**
     * Inserta un perro en el espacio indicado, sacandolo del sitio inicial
     * @param {integer} $from sitio inicial (dog ID)
     * @param {integer} $to sitio final
     * @param {integer} $where insertart "encima" (0) o "debajo" (1)
     * @return null|string
     */
	function dragAndDrop($from,$to,$where) {
		if ( ($from<=0) || ($to<=0) ) {
			return $this->error("dnd: SrcIDPerro:$from or DestIDPerro:$to are invalid");
		}
        assertClosedJourney($this->jornada); // throw exception on closed journeys
		$ordensalida = $this->getOrden();
		$res=list_move($from,$to,$where,$ordensalida);
		$this->setOrden($res);
		return "";
	}

    /**
     * Inserta un equipo en el espacio indicado, sacandolo del sitio inicial
     * @param {integer} $from sitio inicial (team ID)
     * @param {integer} $to sitio final
     * @param {integer} $where insertart "encima" (0) o "debajo" (1)
     * @return null|string
     */
    function dragAndDropEquipos($from,$to,$where) {
        if ( ($from<=0) || ($to<=0) ) {
            return $this->error("dnd: SrcIDTeam:$from or DestIDTeam:$to are invalid");
        }
        assertClosedJourney($this->jornada); // throw exception on closed journeys
        $ordenequipos = $this->getOrdenEquipos();
        $res=list_move($from,$to,$where,$ordenequipos);
        $this->setOrdenEquipos($res);
        return "";
    }

    /**
     * Obtiene la lista de equipos ordenada según el orden de salida de la manga
     */
    function getTeams() {
    	$this->myLogger->enter();
        // obtenemos los equipos de la manga y reindexamos segun el ID
        $eq=$this->__select("*","equipos","(Jornada={$this->jornada->ID})","","");
        if (!is_array($eq)) return $this->error($this->conn->error);

		// fase 1 ordenamos en funcion del id del equipo
		$f1=array();
		foreach($eq['rows'] as $equipo) { $f1[intval($equipo['ID'])]=$equipo; }

		// fase 2 ordenamos segun el orden de equipos
		$f2=array();
        $ord=explode(',',$this->getOrdenEquipos());
        foreach($ord as $eid) {
			if ($eid==="BEGIN") continue;
			if ($eid==="END") continue;
			if (!array_key_exists($eid,$f1)) {
				$this->myLogger->error("El equipo $eid no esta en la jornada {$this->jornada->ID} ".
					"pero figura en el orden de equipos de la manga {$this->manga->ID}");
			} else {
				$f2[]=$f1[$eid];
			}
		}

		// fase 3finalmente ordenamos por categorias
		$sorder=array('X','L','M','S','T'); // vemos si hay que invertir orden de categorias
		if ($this->federation->get('ReverseXLMST')===true) $sorder=array('T','S','M','L','X');

		$res=array();
		foreach( $sorder as $cat ) {
			foreach( $f2 as $equipo) {
				if (strpos($equipo['Categorias'],$cat)===FALSE) continue; // category doesn't match
				if (in_array($equipo,$res)) continue; // already inserted
				$res[]=$equipo; // add to team list
			}
		}
		// that's all folks
		$result=array('total'=>count($res),'rows'=>$res);
		$this->myLogger->leave();
		return $result;
    }

    /**
     * Obtiene la lista de perros del equipo indicado, ordenados segun el orden de salida
     * @param {integer} $team ID del equipo a listar
     */
    function getDataByTeam($team) {
        $this->myLogger->enter();
        // obtenemos datos del equipo
        $eqdata= $this->__selectAsArray("*","equipos","(ID=$team) AND (Jornada={$this->jornada->ID})");
        if (!is_array($eqdata)) return $this->error($this->conn->error);

        // obtenemos los perros de la manga/equipo
        $rs= $this->__select("*","resultados","(Manga={$this->manga->ID}) AND (Equipo=$team)","","");
        if(!is_array($rs)) return $this->error($this->conn->error);
        // recreamos el array de perros anyadiendo el ID del perro como clave, así como el nombre del equipo
        $p1=array();
        foreach ($rs['rows'] as $resultado) {
            $resultado['NombreEquipo']=$eqdata['Nombre'];
            $p1[$resultado['Perro']]=$resultado;
        }

        // NOTA: realmente el ajustar el orden solo es significativo en las competiciones de eq3
        // pero en dicho caso ya existe un botón específico para ajustar el orden
        // por consiguiente, estas pasadas se pueden eliminar, pero las dejamos para que la informacion
        // quede coherente en ambas ventanas

        // primera pasada: ajustamos los perros del equipo segun el orden de salida que figura en Orden_Salida
        $p2=array();
        $orden=explode(',',$this->getOrden());
        foreach ($orden as $perro) {
            // esto es una guarreria: realmente parsea todos los perros de la manga
            // para al final extraer solo cuatro perros.... pendiente de mejorar un poco
            if ($perro==="BEGIN") continue;
            if ($perro==="END") continue;
            if (array_key_exists($perro,$p1)) array_push($p2,$p1[$perro]);
        }

        // segunda pasada: ordenar por celo
        $p3=array();
        foreach(array(0,1) as $celo) {
            foreach ($p2 as $perro) {
                if ( (1&intval($perro['Celo']))===$celo) array_push($p3,$perro);
            }
        }

        // tercera pasada: ordenar por categoria
        $p4=array();
		$sorder=array('X','L','M','S','T'); // vemos si hay que invertir orden de categorias
		if ($this->federation->get('ReverseXLMST')===true) $sorder=array('T','S','M','L','X');
        foreach($sorder as $cat) {
            foreach ($p3 as $perro) {
                if ($perro['Categoria']==$cat) array_push($p4,$perro);
            }
        }
        $result = array('total'=>count($p4),'rows'=>$p4);
        $this->myLogger->leave();
        return $result;
    }

	/**
	 * Separa los perros de la lista en funcion del modo,
	 * manteniendo el orden.
	 * Se utiliza para poder ajustar el orden de salida por categorias
	 * @param {string} $lista lista original de la base de datos
     * @param {int} $mode 0:L 1:M 2:S 3:MS 4:LMS 5:T 6:LM 7:ST 8:LMST 9:X 10:XL 11:MST 12:XLMST
     * @param {boolean} $reverse On true return included dogs in reverse order
	 * @param {string} $range dog range to be included in result
	 * @return {array} 0:original 1:included 2:excluded 3:doglist
	 */
	function splitPerrosByMode($lista,$mode,$reverse,$range) {
		$this->myLogger->enter();
		// cogemos todos los perros de la manga e indexamos en función del perroID
		$res=$this->__select("*","resultados","Manga={$this->manga->ID}","","");
		$listaperros=array();
		foreach ($res['rows'] as $perro)  $listaperros[$perro['Perro']]=$perro;
		// split de los datos originales
		$ordenperros=explode(",",getInnerString($lista,"BEGIN,",",END"));

		// ajustamos el rango de dorsales a evaluar
        $fromItem=1;
        $toItem=99999;
        if (preg_match('/^\d+-\d+$/',$range)) {
            $a=explode("-",$range);
            $fromItem=( intval($a[0]) <= 0 )? 1 : intval($a[0]);
            $toItem=( intval($a[1]) > 99999)? 99999 : intval($a[1]);
            $this->myLogger->trace("range:$range from:$fromItem to:$toItem");
        }
		// clasificamos los perros por categorias
		$listas=array( 0=>array(),1=>array(),2=>array());
		foreach($ordenperros as $perro) {
			if ($perro=="") continue; // skip if no data
			// skip items that not belong to current round
			// this happens in final rounds where starting order depends on qualification round results
			if (!array_key_exists($perro,$listaperros)) continue;
            // add unconditionally to main list
            array_push($listas[0],$perro);
            // compare dorsals
			$d=intval($listaperros[$perro]['Dorsal']);
			if ( ($d<$fromItem) || ($d>$toItem)) {
				// out of dorsals, insert into exclude list
                array_push($listas[2],$perro);
			} else {
                // dorsal match, compare categories, and insert in proper list
                if (category_match($listaperros[$perro]['Categoria'],$this->heights,$mode)) {
                    array_push($listas[1],$perro);
                } else {
                    array_push($listas[2],$perro);
                }
			}
		}
		// retornamos el array de strings
        // solo se incluyen aquellos perros que aparecen en la lista de resultados de la manga
        $str0=implode(",",$listas[0]); // lista original
        $str1=implode(",",($reverse)?array_reverse($listas[1]):$listas[1]); // perros incluidos en lista nueva
        $str2=implode(",",$listas[2]); // perros excluidos de lista nueva
		$this->myLogger->leave();
		return array($str0,$str1,$str2,$listaperros);
	}

	/**
	 * A partir de una lista de equipos separa estos en funcion de la categoria
	 * en listas separadas
	 * @param {string} $lista orden de equipos tal y como se almacena en agility::Mangas
	 * @param {int} $mode 0:L 1:M 2:S 3:MS 4:LMS 5:T 6:LM 7:ST 8:LMST 9:X 10:XL 11:MST 12:XLMST
     * @param {boolean} $reverse On true return included teams in reverse order
	 * @return {array} 0:original 1:included 2:excluded
	 */
	function splitEquiposByMode($lista,$mode,$reverse=false) {
		$this->myLogger->enter();
		// buscamos los equipos de la jornada y lo reindexamos en funcion del ID
		$res=$this->__select("*","equipos","Jornada={$this->jornada->ID}","","");
		$listaequipos=array();
		foreach($res['rows'] as $equipo) $listaequipos[$equipo['ID']]=$equipo;
		// cogemos el orden del equipo
		$ordenequipos=explode(",",getInnerString($lista,"BEGIN,",",END"));
		// clasificamos los equipos por categorias
		$listas=array( 0=>array(),1=>array(),2=>array());
		foreach($ordenequipos as $equipo) {
		    if ($equipo=='') continue; // empty entry
            // skip items that not belong to current round
            // this happens in final rounds where starting order depends on qualification round results
            if (!array_key_exists($equipo,$listaequipos)) continue;
            // add unconditionally to main list
			array_push($listas[0],$equipo);
            // compare categories, and insert in proper list
			if (category_match($listaequipos[$equipo]['Categorias'],$this->heights,$mode)) {
                array_push($listas[1],$equipo);
            } else {
                array_push($listas[2],$equipo);
            }
		}
		// retornamos el array de strings
        $str0=implode(",",$listas[0]);
        $str1=implode(",",($reverse)?array_reverse($listas[1]):$listas[1]);
        $str2=implode(",",$listas[2]);
		$this->myLogger->leave();
		return array($str0,$str1,$str2,$listaequipos);
	}

	/**
	 * Obtiene la lista (actualizada) de perros de una manga en el orden de salida correcto
	 * En el proceso de inscripcion ya hemos creado la tabla de resultados, y el orden de salida
	 * con lo que la cosa es sencilla
	 * Se cogen de la tabla de resultados todos los perros de la manga
	 * Se coge el orden de equipos
	 * Se ordena segun categoria,celo,equipo
	 * 
	 * IMPORTANTE: en AgilityContest-2.0 : el campo "Orden_Salida" de la tabla mangas no especifica el orden
	 * real, sino el orden relativo entre perros cuando tienen la misma categoria,celo,equipo; 
	 * De hecho perros de diferente categoria celo y equipo están mezclados, y hace falta que esta funcion
	 * los ordene segun el resultado final deseado
	 * 
	 * @param {boolean} teamview true->intercalar información de equipos en el listado 
	 * @param {integer} catmode categorias a tener en cuenta en el listado que hay que presentar
	 * @param {array} rs lista de resultados a presentar. Se utiliza para reordenar resultados en funcion del orden de salida
	 */
	function getData($teamView=false,$catmode=12,$rs=null,$range="0-99999") {
		$this->myLogger->enter();
		// obtenemos los perros de la manga, anyadiendo los datos que faltan (NombreLargo y NombreEquipo) a partir de los ID's
		if (!$rs) $rs= $this->__select(
			"resultados.*,equipos.Nombre AS NombreEquipo,
					perroguiaclub.NombreLargo AS NombreLargo,perroguiaclub.LogoClub AS LogoClub,
					perroguiaclub.Pais,perroguiaclub.Genero,perroguiaclub.LOE_RRC AS LOE_RRC,
					perroguiaclub.CatGuia,1 AS PerrosPorGuia",
			"resultados,equipos,perroguiaclub,inscripciones",
			"(inscripciones.Prueba={$this->prueba->ID}) AND (inscripciones.Perro=resultados.Perro) AND
			(Manga={$this->manga->ID}) AND (resultados.Equipo=equipos.ID) AND (resultados.Perro=perroguiaclub.ID)",
			"FIELD (perroguiaclub.Categoria, '-','X','L','M','S','T')",
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
		// 0:original 1:included 2:excluded 3:listaperros
		$listas=$this->splitPerrosByMode($this->getOrden(),$catmode,false,$range);
		$orden=explode(',',$listas[1]); // cogemos la lista de los perros incluidos
		// PENDING: This is a bypass for some obscure error in orden_salida data corruption.
		// NEED TO BE PROPERLY FIXED
		$orden=array_unique($orden, SORT_NUMERIC);
		foreach ($orden as $perro) {
			if ($perro==="BEGIN") continue;
			if ($perro==="END") continue;
			if ($perro==="") continue;
			if (!array_key_exists(intval($perro),$p1)) {
				$this->myLogger->error("El perro $perro esta en el orden de salida pero no en los resultados");
				// TODO: FIX this consistency error
			} else {
				// insertamos el numero de perros que tiene el guia
				$p1[$perro]['PerrosPorGuia']= $guias[$p1[$perro]['NombreGuia']];
				array_push($p2,$p1[$perro]);
			}
		}

		// segunda pasada: ordenar segun el orden de equipos de la jornada
		$p3=array();
		$oequipos=$this->getTeams();
		foreach($oequipos['rows'] as $equipo) {
			$eid=$equipo['ID'];
			foreach ($p2 as $perro) {
				if ($perro['Equipo']==$eid) array_push($p3,$perro);
			}
		}

        // en la modalidad equipos 4 los cuatro perros corren juntos,
        // con independencia de celo/categoria
		// en caso contrario hay que separar por categoria/celo
        $p5=$p3;
		if (! Jornadas::isJornadaEqConjunta($this->jornada)) {
            // tercera pasada: ordenar por celo
            $p4=array();
            foreach(array(0,1) as $celo) {
                foreach ($p3 as $perro) {
                    if ((1&intval($perro['Celo']))===$celo) array_push($p4,$perro);
                }
            }

            // cuarta pasada: ordenar por categoria
			// respetando el orden definido en el programa de la jornada
			// miramos el orden de tandas:
			$catsorderedbytanda=implode(',',Tandas::getTandasByTipoManga($this->manga->Tipo,$this->heights)); // tipos de tanda asociados a la manga
            $res=$this->__select(
            	"Categoria",
				 "tandas",
				"(tandas.Jornada={$this->jornada->ID}) AND (tandas.Tipo IN ($catsorderedbytanda)) ",
                "Orden ASC"
			);

            // ordenamos segun el orden de categorias establecido en las tandas
            $p5=array();
            foreach ($res['rows'] as $item) {
            	$ord="XLMST";
				if ($this->federation->get('ReverseXLMST')===true) $ord="TSMLX";
            	// hack to get compatibility with oldest database entries that stored "no_cats" tanda categories as LMS
            	if (strpos($item['Categoria'],"LMS")!==FALSE ) $item['Categoria']=$ord;
            	if ($item['Categoria']==="-") $item['Categoria']=$ord;
            	// si la tanda tiene mas de una categoria, hacemos un split y separamos internamente
				$cats=str_split(($item['Categoria']));
				foreach($cats as $cat) {
                    foreach ($p4 as $perro) {
                    	$ccats=compatible_categories($this->heights,$cat);
                    	// do_log("perro:{$perro['Perro']} categoria:{$perro['Categoria']} tanda:{$cat} ccats:{$ccats} heights:{$this->heights}");
                        if ( category_match($perro['Categoria'],$this->heights,$ccats)) array_push($p5,$perro);
                    }
				}
			}
        }

		// quinta: intercalar informacion de equipos si se precisa
		// para que aparezca en el menu de introduccion de datos de la consola
		//
		// adicionalmente, en las pruebas por equipos anyadimos al resultado un array
		// extra indicando los equipos y su orden de salida. Este array es distinto al
		// obtenido con getOrdenEquipos, pues si un equipo tiene perros en varias categorias,
		// el equipo aparecera varias veces en este resultado

		$p6=$p5;
		if ($teamView) {
            $p6=array();
			$equipo=0;
			foreach ($p5 as $perro) {
				if ($perro['Equipo']!=$equipo){ // cambio de equipo en el orden de salida
					// intercala datos de equipo en orden de salida general
					$equipo=$perro['Equipo'];
					$a=array(
						'Dorsal' => '*',
						'Nombre'=>_('Team'),
						'NombreGuia'=>$perro['NombreEquipo'],
						'Eliminado'=>0,
						'NoPresentado'=>0
					);
					array_push($p6,$a);
				}
				array_push($p6,$perro); // introduce datos de perro
			}
		}
		$result = array();

		$result["total"] = count($p6);
		$result["rows"] = $p6;
		$this->myLogger->leave();
		return $result;
	}

	/**
	 * Reordena el orden de salida de las categorias indicadas de una manga al azar
	 * @param	{int} $catmode categorias a las que tiene que afectar este cambio
	 * @return {string} nuevo orden de salida
	 */
	function randomOrder($catmode,$range) {
		$this->myLogger->enter();
        assertClosedJourney($this->jornada); // throw exception on closed journeys
		// fase 1 aleatorizamos la manga
		$orden=$this->getOrden();
		// buscamos los perros de la categoria seleccionada
		$listas=$this->splitPerrosByMode($orden,$catmode,false,$range);
        $str1=$listas[2];
        $str2=implode(",",aleatorio(explode(",", $listas[1])));
        $ordensalida=$this->joinOrders($str1,$str2);
        $this->setOrden($ordensalida);

		// fase 2: aleatorizamos los equipos de la jornada
        $orden=$this->getOrdenEquipos();
        $listas=$this->splitEquiposByMode($orden,$catmode);
        $str1=$listas[2];
        $str2=implode(",",aleatorio(explode(",", $listas[1])));
        $ordenequipos=$this->joinOrders($str1,$str2);
        $this->setOrdenEquipos($ordenequipos);
        $this->myLogger->leave();
		return $ordensalida;
	}

	/**
	 * Reordena el orden de salida de las categorias indicadas de una manga al azar,
	 * pero agrupando los perros por clubes
	 * El resultado es un orden aleatorio de clubes
	 * @param	{int} $catmode categorias a las que tiene que afectar este cambio
	 * @return {string} nuevo orden de salida
	 */
	function randomClubesOrder($catmode,$range) {
		$this->myLogger->enter();
		assertClosedJourney($this->jornada); // throw exception on closed journeys
		// fase 1 aleatorizamos la manga
		$orden=$this->getOrden();
		// buscamos los perros de la categoria seleccionada
		$listas=$this->splitPerrosByMode($orden,$catmode,false,$range);
		$str1=$listas[2]; // perros que no se reordenan
		// aleatorizamos perros a ordenar
		$toorder=aleatorio(explode(",", $listas[1]));
		// en listas[3] tenemos los datos de los perros indexados por id.
		// buscamos el club de cada uno de los que estan en toorder y vamos componiendo el array
		$ordered=array();
		foreach($toorder as $dogid) {
			$dogClub=$listas[3][$dogid]['NombreClub'];
			$done=false;
			for ($n=0;$n<count($ordered);$n++) {
				$itemClub=$listas[3][$ordered[$n]]['NombreClub'];
				if ($dogClub==$itemClub) { // same club: insert in array at this point
					array_splice($ordered,$n,0,$dogid);
					$done=true;
					break;
				}
			}
			// arriving here insert dog at end of array
			if (!$done) array_push($ordered,$dogid);
		}
		// componemos la lista con el orden definitivo
		$str2=implode(',',$ordered);
		$ordensalida=$this->joinOrders($str1,$str2);
		$this->setOrden($ordensalida);

		// En este caso no tiene sentido aleatorizar los equipos: el paso anterior ya lo hace

		$this->myLogger->leave();
		return $ordensalida;
	}
    /**
     * pone el mismo orden de salida que la manga hermana en las categorias solicitadas
     * @param	{int} $catmode categorias a las que tiene que afectar este cambio
     * @param {boolean} reverse on true return selected dogs/teams in reverse order
     * @return {string} nuevo orden de salida; null on error
     */
    function sameOrder($catmode,$reverse,$range) {
        $this->myLogger->enter();
        assertClosedJourney($this->jornada); // throw exception on closed journeys
        // buscamos la "manga hermana"
        $mhandler=new Mangas("OrdenSalida::sameOrder()",$this->jornada);
        $hermanas=$mhandler->getHermanas($this->manga->ID);
        if (!is_array($hermanas))
            return $this->error("Error find hermanas info for jornada:{$this->jornada->ID} and manga:{$this->manga->ID}");
        if ($hermanas[1]===null)
            return $this->error("Cannot clone order: Manga:{$this->manga->ID} of Jornada:{$this->jornada->ID} has no brother");

        // spliteamos manga propia y hermana, y las mezclamos en funcion de la categoria
        $lista=$this->splitPerrosByMode($hermanas[0]->Orden_Salida,$catmode,false,$range); // manga actual "splitteada"
        $lista2=$this->splitPerrosByMode($hermanas[1]->Orden_Salida,$catmode,$reverse,$range); // manga hermana "splitteada"
        $str1=$lista[2];
        $str2=$lista2[1];
        $ordensalida=$this->joinOrders($str1,$str2);
        $this->setOrden($ordensalida);

        // hacemos lo mismo con el orden de equipos
        $lista=$this->splitEquiposByMode($hermanas[0]->Orden_Equipos,$catmode); // manga actual "splitteada"
        $lista2=$this->splitEquiposByMode($hermanas[1]->Orden_Equipos,$catmode,$reverse); // manga hermana "splitteada"
        $str1=$lista[2];
        $str2=$lista2[1];
        $ordenequipos=$this->joinOrders($str1,$str2);
        $this->setOrdenEquipos($ordenequipos);

        $this->myLogger->leave();
        return $ordenequipos;
    }

    /**
     * pone el orden de salida inverso respecto de la manga hermana en las categorias solicitadas
     * @param	{int} $catmode categorias a las que tiene que afectar este cambio
     * @return {string} nuevo orden de salida; null on error
     */
    function reverseOrder($catmode,$range) {
        return $this->sameOrder($catmode,true,$range);
    }

    /**
	 * Ordena por orden alfabetico de guias en la categoria indicada
     * @param int $catmode
     */
    function alphaOrder($catmode,$range) {
        $this->myLogger->enter();
        assertClosedJourney($this->jornada); // throw exception on closed journeys
		$data=$this->getData(false,$catmode)['rows'];
        // Ordenamos los perros por orden alfabetico y extraemos la lista de dogID's
        usort($data, function($a, $b) {	return strcasecmp($a['NombreGuia'],$b['NombreGuia']); });
        // generamos la lista de perros
		$str="";
		foreach($data as $item) { $str.=",{$item['Perro']}"; }
		$str= substr($str,1); // quitamos coma inicial

		// ahora cogemos la manga, extraemos lo que no queremos y metemos el nuevo orden
        $orden=$this->getOrden();
        // buscamos los perros de la categoria seleccionada
        $listas=$this->splitPerrosByMode($orden,$catmode,false,$range); // 0:All 1:toChange 2:toRemain
        $str1=$listas[2]; // los que se van a quedar "como están"
        $ordensalida=$this->joinOrders($str1,$str);
        $this->setOrden($ordensalida);
		$this->myLogger->leave();
	}

    /**
     * Ordena por orden de dorsales en la categoria indicada
     * @param int $catmode
     */
    function dorsalOrder($catmode,$range) {
        $this->myLogger->enter();
        assertClosedJourney($this->jornada); // throw exception on closed journeys

		// cogemos la lista de perros inscritos en esta jornada
		$mask=1<<($this->jornada->Numero - 1 );
		$data=$this->__select(
			"*",
			"inscripciones",
			"(Prueba={$this->prueba->ID}) AND ( ( Jornadas & {$mask} ) != 0 )",
			"Dorsal ASC"
		)['rows'];

        // El query ya nos proporciona la lista ordenada por dorsales
        $orden=$this->getOrden();
        $str="";
        foreach($data as $item) { // recomponemos la lista, eliminando los que no esten en la manga
			if (strpos($orden,",{$item['Perro']},")===FALSE) continue; // not present, skip
        	$str.=",{$item['Perro']}";
        }
        $str= substr($str,1); // quitamos coma inicial

        // extraemos del orden los perros de la categoria seleccionada e insertamos el nuevo orden
        $listas=$this->splitPerrosByMode($orden,$catmode,false,$range); // 0:All 1:toChange 2:toRemain
        $str1=$listas[2]; // los que se van a quedar "como están"
        $ordensalida=$this->joinOrders($str1,$str);
        $this->setOrden($ordensalida);
        $this->myLogger->leave();
    }

	/**
	 * Evalua los resultados de la manga from segun mode
	 * y recalcula el orden de salida de la manga from
	 * @param {object} $from manga donde buscar resultados
	 * @param {integer} $mode categorias de la manga (L,M,S,MS,LMS,T,LM,ST,LMST,X,XL,MST,XLMST)
	 * @param {integer} $catmode categorias que hay que ordenar en la manga (X,L,M,S,T,XLMST)
	 */
	protected function invierteResultados($from,$mode,$catmode,$range="0-99999") {

        // FASE 1: invertimos orden de salida de perros
		$r =Competitions::getResultadosInstance("OrdenSalida::invierteResultados",$from->ID);
		$res=$r->getResultadosIndividual($mode);
        $data=$res['rows'];
		$size= count($data);
		// recorremos los resultados en orden inverso
		$ordensalida=$this->getOrden();
		// y reinsertamos los perros actualizando el orden si la categoria coincide
		for($idx=$size-1; $idx>=0; $idx--) {
		    // si el resultado indica un perro que no existe en orden de salida actual, skip
            // esto ocurre cuando from corresponde a una manga de calificacion
			if (! category_match($data[$idx]['Categoria'],$this->heights,$catmode) ) continue;
			$idperro=$data[$idx]['Perro'];
			// lo borramos para evitar una posible doble insercion
			$str = ",$idperro,";
			$nuevoorden = str_replace ( $str, ",", $ordensalida );
			// componemos el tag que hay que insertar
			$str="$idperro,END";
			// y lo insertamos en lugar que corresponde (al final)
			$ordensalida = str_replace ( "END", $str, $nuevoorden );
		}
		// salvamos datos
		$this->setOrden($ordensalida);

        // FASE 2: ahora invertimos el orden de los equipos en funcion del resultado
		$minmax=Jornadas::getTeamDogs($this->jornada);
		if ($minmax[0]<=1 || $minmax[0]==$minmax[1]) return; // si no estamos en team best, return

        $this->myLogger->trace("invirtiendo orden de equipos");
        $res=$r->getResultadosEquipos($res);
        $size= count($res);
        // recorremos los resultados en orden inverso generando el nuevo orden de equipos
        $ordenequipos=$this->getOrdenEquipos();
        // y reinsertamos los perros actualizando el orden si la categoria del equipo coincide
        for($idx=$size-1; $idx>=0; $idx--) {
			if (! category_match($res[$idx]['Categorias'],$this->heights,$catmode)) continue;
            $equipo=intval($res[$idx]['ID']);
            $this->myLogger->trace("Equipo: $equipo - ,{$res[$idx]['Nombre']}");
            // eliminamos el equipo del puesto donde esta
            $str=",$equipo,";
            $ordenequipos = str_replace ( $str, ",", $ordenequipos );
            // reinsertamos equipo al final
            $str=",$equipo,END";
            // y lo insertamos en lugar que corresponde (al final)
            $ordenequipos = str_replace ( ",END", $str, $ordenequipos );
        }
        // salvamos datos
        $this->setOrdenEquipos($ordenequipos);
	}

	/**
	 * Calcula el orden de salida de la(s) categoria(s) indicadas
	 * de manga en funcion del orden inverso al resultado de su manga "hermana"
	 * @return {string} nuevo orden de salida; null on error
	 */
	function orderByResults($catmode,$range) {
		$this->myLogger->enter();
        assertClosedJourney($this->jornada); // throw exception on closed journeys
		// fase 1: buscamos la "manga hermana"
		$mhandler=new Mangas("OrdenSalida::reverse()",$this->jornada);
		$hermanas=$mhandler->getHermanas($this->manga->ID);
		if (!is_array($hermanas))
		    return $this->error("Error find hermanas info for jornada:{$this->jornada->ID} and manga:{$this->manga->ID}");
		if ($hermanas[1]===null)
		    return $this->error("Cannot reverse order: Manga:{$this->manga->ID} of Jornada:{$this->jornada->ID} has no brother");
	
		// fase 2: evaluamos resultados de la manga hermana
		$this->myLogger->trace("El orden de salida original para manga:{$this->manga->ID} ".
                                    "jornada:{$this->jornada->ID} es:\n{$hermanas[0]->Orden_Salida}");
		// En funcion del tipo de recorrido tendremos que leer diversos conjuntos de Resultados
		switch($hermanas[0]->Recorrido) {
			case 0: // Large,medium,small (3-heighs) Large,medium,small,tiny (4-heights) X,L,M,S,T (5-heigths)
				$this->invierteResultados($hermanas[1],0,$catmode); // L
				$this->invierteResultados($hermanas[1],1,$catmode); // M
				$this->invierteResultados($hermanas[1],2,$catmode); // S
				if ($this->heights!=3) $this->invierteResultados($hermanas[1],5,$catmode); // T
				if ($this->heights==5) $this->invierteResultados($hermanas[1],9,$catmode); // X
				break;
			case 1: // Large,medium+small (3heights) Large+medium,Small+tiny (4heights) XLarge+Large,medium,+small+toy (5heights)
				if ($this->heights==3) {
					$this->invierteResultados($hermanas[1],0,$catmode); // L
					$this->invierteResultados($hermanas[1],3,$catmode); // MS
				}
				if ($this->heights==4)  {
					$this->invierteResultados($hermanas[1],6,$catmode); // LM
					$this->invierteResultados($hermanas[1],7,$catmode); // ST
				}
				if ($this->heights==5)  {
					$this->invierteResultados($hermanas[1],10,$catmode); // XL
					$this->invierteResultados($hermanas[1],11,$catmode); // MST
				}
				break;
			case 2: // conjunta L+M+S (3 heights) L+M+S+T (4heights)
				if ($this->heights==3) {
					$this->invierteResultados($hermanas[1],4,$catmode); // LMS
				}
				if ($this->heights==4)  {
					$this->invierteResultados($hermanas[1],8,$catmode); // LMST
				}
				if ($this->heights==5)  {
					$this->invierteResultados($hermanas[1],12,$catmode); // XLMST
				}
				break;
			case 3: // 5-heigts 3 groups mode (XL+L, M,  S+T)
				$this->invierteResultados($hermanas[1],10,$catmode);
				$this->invierteResultados($hermanas[1],1,$catmode);
				$this->invierteResultados($hermanas[1],7,$catmode);
				break;
		}
		$nuevo=$this->getOrden();
		$this->myLogger->trace("El orden de salida nuevo para manga:{$this->manga->ID} jornada:{$this->jornada->ID} es:\n$nuevo");
		$this->myLogger->leave();
		return $nuevo;
	}

	/**
	 * Invierte el orden de alturas
	 * IMPORTANTE:
	 * Si el operador ha cambiado "a mano" el orden de las tandas, esto dará resultados inesperados
	 * @return {string} nuevo orden de salida (realmente el mismo, solo cambian las tandas)
	 */
	private function reverseHeightsOrder() {
		// buscamos las tandas de la manga actual
		$tandas=$this->__select(
			"*",
			"tandas",
			"Prueba={$this->prueba->ID} AND Jornada={$this->jornada->ID} and Grado='{$this->manga->Grado}'",
			"ORDEN DESC",
			""
		);
		// vemos si la manga actual es agility o jumping, y seleccionamos solo las tandas que coinciden con el tipo
		$m_isAgility=Mangas::isAgility($this->manga->Tipo);
		$t=array();
		foreach ($tandas['rows'] as $tanda) {
			$t_isAgility=Tandas::isAgility($tanda['Tipo']);
			if ($t_isAgility==$m_isAgility) array_push($t,$tanda);
		}
		// ahora tenemos las mangas que nos interesan indexadas por el orden. vamos a invertirlo
		switch (count($t)) {
			case 1:
				// pre-agility solo tiene una tanda, por lo que el procedimiento de invertir las tandas
				// no puede funcionar. Lo que haremos será invertir el orden de salida
			case 3:
				$o=$t[0]['Orden']; $t[0]['Orden']=$t[2]['Orden'];$t[2]['Orden']=$o;
				break;
			case 4:
				$o=$t[0]['Orden']; $t[0]['Orden']=$t[3]['Orden'];$t[3]['Orden']=$o;
				$o=$t[1]['Orden']; $t[1]['Orden']=$t[2]['Orden'];$t[2]['Orden']=$o;
				break;
			case 5:
				$o=$t[0]['Orden']; $t[0]['Orden']=$t[4]['Orden'];$t[4]['Orden']=$o;
				$o=$t[1]['Orden']; $t[1]['Orden']=$t[3]['Orden'];$t[3]['Orden']=$o;
				break;
		}
		// y actualizamos la base de datos con las tandas recalculadas
		foreach ($t as $tanda) {
			$str="update tandas SET Orden={$tanda['Orden']} WHERE ID={$tanda['ID']}";
			$rs=$this->query($str);
			if (!$rs) return $this->error($this->conn->error);
		}
		// Ya hemos terminado; retornamos nuevo orden
		return $this->getOrden();
	}

    /**
	 * Sort according provided method
     * @param {string} $method
     * @param {string} $catmode
     */
	function setOrder($method,$catmode,$range) {
		switch($method) {
			case "rheights": return $this->reverseHeightsOrder(); break;
			case "random": return $this->randomOrder($catmode,$range); break;
			case "rclubes": return $this->randomClubesOrder($catmode,$range); break;
            case "reverse": return $this->reverseOrder($catmode,$range); break;
            case "results": return $this->orderByResults($catmode,$range); break;
            case "clone": return $this->sameOrder($catmode,false,$range); break;
            case "alpha": return $this->alphaOrder($catmode,$range); break;
            case "dorsal": return $this->dorsalOrder($catmode,$range); break;
			default:return $this->error("Invalid Sorting method: {$method}");
		}
	}

    /**
     * Reasigna los dorsales de manera que coincida con el orden de salida
     * ESTA FUNCION SOLO DEBE USARSE EN PRUEBAS EN QUE EL ORDEN DE SALIDA SEA EL MISMO PARA TODAS LAS MANGAS
     */
    function reassignDorsal() {
        assertClosedJourney($this->jornada); // throw exception on closed journeys
        $o=explode(',',$this->getOrden());
        $dorsal=1;
        foreach($o as $perro){
            if ($perro=="BEGIN") continue;
            if ($perro=="END") continue;
            echo "Prueba:{$this->prueba->ID} Jornada:{$this->jornada->ID} Manga:{$this->manga->ID} Dorsal: $dorsal Perro:$perro\n";
            $str1="UPDATE inscripciones SET DORSAL=$dorsal WHERE (Prueba={$this->prueba->ID}) AND (Perro=$perro)";
            $this->query($str1);
            $str2="UPDATE resultados SET DORSAL=$dorsal WHERE (Jornada={$this->jornada->ID}) AND (Perro=$perro)";
            $this->query($str2);
            $dorsal++;
        }

    }

} // class

?>
