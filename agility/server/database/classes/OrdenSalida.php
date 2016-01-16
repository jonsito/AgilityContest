<?php
/*
OrdenSalida.php

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

require_once(__DIR__."/../../../modules/Federations.php");
require_once("DBObject.php");
require_once("Equipos.php");
require_once("Resultados.php");
require_once("Clasificaciones.php");
require_once("Inscripciones.php");

class OrdenSalida extends DBObject {

	// tablas utilizadas para componer e insertar los idperroes en el string de orden de salida
	public static $default_orden = "BEGIN,END";
	
	var $prueba=null; // {array} prueba data
	var $jornada=null; // {array} jornada data
	var $manga=null; // {array} manga data
	var $federation=null; // object federation
	
	/**
	 * Constructor
	 * @param {string} Name for this object
	 * @param {integer} $manga Manga ID
	 * @throws Exception when
	 * - cannot contact database
	 * - invalid manga ID
	 */
	function __construct($file,$manga) {
		parent::__construct($file);
		if ($manga<=0) {
			$this->errormsg="Resultados::Construct invalid Manga ID: $manga";
			throw new Exception($this->errormsg);
		}
		$this->manga=$this->__getArray("Mangas",$manga);
		if (!is_array($this->manga)) {
			$this->errormsg="OrdenSalida::construct(): Cannot get info on manga:$manga";
			throw new Exception($this->errormsg);
		}
		$this->jornada=$this->__getArray("Jornadas",$this->manga['Jornada']);
		if (!is_array($this->jornada)) {
			$this->errormsg="OrdenSalida::construct(): Cannot get jornada info on jornada:{$this->manga['Jornada']} manga:$manga";
			throw new Exception($this->errormsg);
		}
		$this->prueba=$this->__getArray("Pruebas",$this->jornada['Prueba']);
		if (!is_array($this->prueba)) {
			$this->errormsg="OrdenSalida::construct(): Cannot get prueba info on prueba:{$this->jornada['Prueba']} jornada:{$this->manga['Jornada']} manga:$manga";
			throw new Exception($this->errormsg);
		}
		$this->federation=Federations::getFederation(intval($this->prueba['RSCE']));
		if ($this->federation==null) {
			$this->errormsg="OrdenSalida::construct(): Cannot get federation info on prueba:{$this->jornada['Prueba']} jornada:{$this->manga['Jornada']} manga:$manga";
			throw new Exception($this->errormsg);
		}
	}
	
	/**
	 * Retrieve Mangas.Orden_Salida
	 * @return {string} orden de salida.
	 */
	function getOrden() {
        if ($this->manga['Orden_Salida']==="") {
            $this->manga['Orden_Salida']=OrdenSalida::$default_orden;
            $this->setOrden(OrdenSalida::$default_orden);
        }
		return $this->manga['Orden_Salida'];
	}

    /**
     * Retrieve Mangas.Orden_Equipos
     * @return {string} orden de salida.
     */
    function getOrdenEquipos() {
        $defOrden="BEGIN,{$this->jornada['Default_Team']},END";
        if ($this->manga['Orden_Equipos']==="") {
            $this->manga['Orden_Equipos']= $defOrden;
            $this->setOrdenEquipos($defOrden);
        }
        return $this->manga['Orden_Equipos'];
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
		$sql = "UPDATE Mangas SET Orden_Salida = '$orden' WHERE ( ID={$this->manga['ID']} )";
		$rs = $this->query ($sql);
		// do not call $rs->free() as no resultset returned
		if (!$rs) return $this->error($this->conn->error);
		$this->manga['Orden_Salida']=$orden;
		return "";
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
        $sql = "UPDATE Mangas SET Orden_Equipos = '$orden' WHERE ( ID={$this->manga['ID']} )";
        $rs = $this->query ($sql);
        // do not call $rs->free() as no resultset returned
        if (!$rs) return $this->error($this->conn->error);
        $this->manga['Orden_Equipos']=$orden;
        return "";
    }

	/**
	 *  coge el string con el ID del perro y lo inserta al final
	 *  @param {integer} idperro ID perro 
	 */
	function insertIntoList($idperro) {
		$ordensalida=$this->getOrden();
		// lo borramos para evitar una posible doble insercion
		$str = ",$idperro,";
		$nuevoorden = str_replace ( $str, ",", $ordensalida );
		// componemos el tag que hay que insertar
		$myTag="$idperro,END";
		// y lo insertamos en lugar que corresponde
		$ordensalida = str_replace ( "END", $myTag, $nuevoorden );
		// update database
		return $this->setOrden($ordensalida);
	}

    /**
     *  coge el string con el ID del equipo y lo inserta al final
     *  @param {integer} idteam ID del equipo
     */
    function insertIntoTeamList($idteam) {
        $ordenequipos=$this->getOrdenEquipos();
        // lo borramos para evitar una posible doble insercion
        $str = ",$idteam,";
        $nuevoorden = str_replace ( $str, ",", $ordenequipos );
        // componemos el tag que hay que insertar
        $myTag="$idteam,END";
        // y lo insertamos en lugar que corresponde
        $ordenequipos = str_replace ( "END", $myTag, $nuevoorden );
        // update database
        return $this->setOrdenEquipos($ordenequipos);
    }

	/**
	 * Elimina un idperro del orden de salida
	 * @param {integer} $idperro
	 * @return {string} "" on success; else error message
	 */
	function removeFromList($idperro) {
		$ordensalida=$this->getOrden();
		$str = ",$idperro,";
		$nuevoorden = str_replace ( $str, ",", $ordensalida );
		// update database
		return $this->setOrden($nuevoorden);
	}

    /**
     * Elimina un equipo del orden de equipos
     * @param {integer} $idteam
     * @return {string} "" on success; else error message
     */
    function removeFromTeamList($idteam) {
        $ordenequipos=$this->getOrdenEquipos();
        $str = ",$idteam,";
        $nuevoorden = str_replace ( $str, ",", $ordenequipos );
        // update database
        return $this->setOrdenEquipos($nuevoorden);
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
		// recuperamos el orden de salida
		$ordensalida = $this->getOrden();
		// extraemos "from" de donde este y lo guardamos
		$str = ",$from,";
		$ordensalida = str_replace ( $str , "," , $ordensalida );
		// insertamos 'from' encima o debajo de 'to' segun el flag 'where'
		$str1 = ",$to,";
		$str2 = ($where==0)? ",$from,$to," : ",$to,$from,";
		$ordensalida = str_replace( $str1 , $str2 , $ordensalida );
		// guardamos el resultado
		$this->setOrden($ordensalida);
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
        // recuperamos el orden de salida
        $ordenequipos = $this->getOrdenEquipos();
        // extraemos "from" de donde este y lo guardamos
        $str = ",$from,";
        $ordenequipos = str_replace ( $str , "," , $ordenequipos );
        // insertamos 'from' encima o debajo de 'to' segun el flag 'where'
        $str1 = ",$to,";
        $str2 = ($where==0)? ",$from,$to," : ",$to,$from,";
        $ordenequipos = str_replace( $str1 , $str2 , $ordenequipos );
        // guardamos el resultado
        $this->setOrdenEquipos($ordenequipos);
        return "";
    }

    /**
     * Obtiene la lista de equipos ordenada según el orden de salida de la manga
     */
    function getTeams() {
        // obtenemos los equipos de la manga y reindexamos segun el ID
        $eq=$this->__select("*","Equipos","(Jornada={$this->jornada['ID']})","","");
        if (!is_array($eq)) return $this->error($this->conn->error);
        $equipos=array();
        foreach($eq['rows'] as $equipo) { $equipos[$equipo['ID']]=$equipo; }
        // cogemos ahora el orden de salida de los equipos de esta manga
        $res=array();
        $oequipos=explode(',',$this->getOrdenEquipos());
        foreach($oequipos as $equipo) {
            // componemos la lista de equipos ordenada segun el orden de salida
            if ($equipo==="BEGIN") continue;
            if ($equipo==="END") continue;
            if (!array_key_exists($equipo,$equipos)) {
                $this->myLogger->error("El equipo $equipo no esta en la jornada {$this->jornada['ID']} pero figura en el orden de salida de la manga {$this->manga['ID']}");
            } else {
                $res[]=$equipos[$equipo];
            }
        }
        return array('rows'=>$res,'total'=>count($res));
    }

    /**
     * Obtiene la lista de perros del equipo indicado, ordenados segun el orden de salida
     * @param {integer} $team ID del equipo a listar
     */
    function getDataByTeam($team) {
        $this->myLogger->enter();
        // obtenemos datos del equipo
        $eqdata= $this->__selectAsArray("*","Equipos","(ID=$team) AND (Jornada={$this->jornada['ID']})");
        if (!is_array($eqdata)) return $this->error($this->conn->error);

        // obtenemos los perros de la manga/equipo
        $rs= $this->__select("*","Resultados","(Manga={$this->manga['ID']}) AND (Equipo=$team)","","");
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
                if ($perro['Celo']==$celo) array_push($p3,$perro);
            }
        }

        // tercera pasada: ordenar por categoria
        $p4=array();
        foreach(array('L','M','S','T') as $cat) {
            foreach ($p3 as $perro) {
                if ($perro['Categoria']==$cat) array_push($p4,$perro);
            }
        }
        $result = array('total'=>count($p4),'rows'=>$p4);
        $this->myLogger->leave();
        return $result;
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
	 */
	function getData($teamView=false) {
		// obtenemos los perros de la manga, anyadiendo los datos que faltan (NombreLargo y NombreEquipo) a partir de los ID's
		$rs= $this->__select(
			"Resultados.*,Equipos.Nombre AS NombreEquipo,PerroGuiaClub.NombreLargo AS NombreLargo,PerroGuiaClub.LogoClub AS Logo",
			"Resultados,Equipos,PerroGuiaClub",
			"(Manga={$this->manga['ID']}) AND (Resultados.Equipo=Equipos.ID) AND (Resultados.Perro=PerroGuiaClub.ID)",
			"",
			""
		);
		if(!is_array($rs)) return $this->error($this->conn->error);
		// recreamos el array de perros anyadiendo el ID del perro como clave
		$p1=array();
		foreach ($rs['rows'] as $resultado) {
			$p1[$resultado['Perro']]=$resultado; 
		}

		// primera pasada: ajustamos los perros segun el orden de salida que figura en Orden_Salida
		$p2=array();
		$orden=explode(',',$this->getOrden());
		foreach ($orden as $perro) {
			if ($perro==="BEGIN") continue;
			if ($perro==="END") continue;
			if (!array_key_exists($perro,$p1)) {
				$this->myLogger->error("El perro $perro esta en el orden de salida pero no en los resultados");
				// TODO: FIX this consistency error
			} else {
				array_push($p2,$p1[$perro]);
			}
		}

		// segunda pasada: ordenar segun el orden de equipos de la jornada
		$p3=array();
        $oequipos=explode(',',$this->getOrdenEquipos());
		foreach($oequipos as $equipo) {
            if ($equipo==="BEGIN") continue;
            if ($equipo==="END") continue;
			foreach ($p2 as $perro) {
				if ($perro['Equipo']==$equipo) array_push($p3,$perro);
			}
		}

        // en la modalidad equipos 4 los cuatro perros corren juntos,
        // con independencia de celo/categoria
        $p5=$p3;
        if ($this->jornada['Equipos4']==0) {
            // tercera pasada: ordenar por celo
            $p4=array();
            foreach(array(0,1) as $celo) {
                foreach ($p3 as $perro) {
                    if ($perro['Celo']==$celo) array_push($p4,$perro);
                }
            }

            // cuarta pasada: ordenar por categoria
            $p5=array();
            foreach(array('L','M','S','T') as $cat) {
                foreach ($p4 as $perro) {
                    if ($perro['Categoria']==$cat) array_push($p5,$perro);
                }
            }
        }

		// quinta: intercalar informacion de equipos si se precisa
		$p6=$p5;
		if ($teamView) {
            $p6=array();
			$equipo=0;
			foreach ($p5 as $perro) {
				if ($perro['Equipo']!=$equipo){
					$equipo=$perro['Equipo'];
					$a=array(
						'Dorsal' => '*',
						'Nombre'=>_('Equipo'),
						'NombreGuia'=>$perro['NombreEquipo'],
						'Eliminado'=>0,
						'NoPresentado'=>0
					);
					array_push($p6,$a); // intercala datos de equipo
				}
				array_push($p6,$perro); // introduce datos de perro
			}
		}
		$result = array();
		$result["total"] = count($p6);
		$result["rows"] = $p6;
		return $result;
	}
	
	/**
	 * Reordena el orden de salida de una manga al azar
	 * @param  	{int} $jornada ID de jornada
	 * @param	{int} $manga ID de manga
	 * @return {string} nuevo orden de salida
	 */
	function random() {
		// fase 1 aleatorizamos la manga
		$orden=$this->getOrden();
		$this->myLogger->debug("OrdenSalida::Random() Manga:{$this->manga['ID']} Orden inicial: \n$orden");
		$str=getInnerString($orden,"BEGIN,",",END");
		if ($str!=="") { // si hay datos, reordena; si no no hagas nada
			$str2 = implode(",",aleatorio(explode(",", $str)));
			$str="BEGIN,$str2,END";
			$this->setOrden($str);
		}
		$orden=$this->getOrden();
		$this->myLogger->debug("OrdenSalida::Random() Manga:{$this->manga['ID']} Orden final: \n$orden");

		// fase 2: aleatorizamos los equipos de la jornada
        $orden=$this->getOrdenEquipos();
        $this->myLogger->debug("OrdenSalida::RandomTeam() Manga:{$this->manga['ID']} Orden inicial: \n$orden");
        $str=getInnerString($orden,"BEGIN,",",END");
        if ($str!=="") { // si hay datos, reordena; si no no hagas nada
            $str2 = implode(",",aleatorio(explode(",", $str)));
            $str="BEGIN,$str2,END";
            $this->setOrdenEquipos($str);
        }
        $ordenequipos=$this->getOrdenEquipos();
        $this->myLogger->debug("OrdenSalida::RandomTeam() Manga:{$this->manga['ID']} Orden final: \n$ordenequipos");

		return $orden;
	}
	
	/**
	 * Evalua los resultados de la manga from segun mode
	 * y recalcula el orden de salida de la manga from
	 * @param {integer} $from manga donde buscar resultados
	 * @param {integer} $mode categorias de la manga (L,M,S,MS,LMS,T,LM,ST,LMST)
	 */
	private function invierteResultados($from,$mode) {

        // FASE 1: invertimos orden de salida de perros
		$r =new Resultados("OrdenSalida::invierteResultados", $this->prueba['ID'],$from->ID);
		$res=$r->getResultados($mode);
        $data=$res['rows'];
		$size= count($data);
		// recorremos los resultados en orden inverso
		$ordensalida=$this->getOrden();
		// y reinsertamos los perros actualizando el orden
		for($idx=$size-1; $idx>=0; $idx--) {
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
        if (intval($this->jornada['Equipos3'])==0 ) return;
        $this->myLogger->trace("invirtiendo orden de equipos");
        $tmode=($this->jornada['Equipos4']!=0)?4:3;
        $res=Resultados::getTeamResults($res['rows'],$this->prueba['ID'],$this->jornada['ID'],$tmode);
        $size= count($res);
        // recorremos los resultados en orden inverso generando el nuevo orden de equipos
        $ordenequipos=$this->getOrdenEquipos();
        // y reinsertamos los perros actualizando el orden
        for($idx=$size-1; $idx>=0; $idx--) {
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
	 * pone el mismo orden de salida que la manga hermana
	 * @return {string} nuevo orden de salida; null on error
	 */
	function sameorder() {
		$this->myLogger->enter();

		// fase 1: buscamos la "manga hermana"
		$mhandler=new Mangas("OrdenSalida::reverse()",$this->jornada['ID']);
		$hermanas=$mhandler->getHermanas($this->manga['ID']);
		if (!is_array($hermanas)) return $this->error("Error find hermanas info for jornada:{$this->jornada['ID']} and manga:{$this->manga['ID']}");
		if ($hermanas[1]==null) return $this->error("Cannot clone order: Manga:{$this->manga['ID']} of Jornada:{$this->jornada['ID']} has no brother");

		// fase 2: clonamos orden de salida y de equipos de la manga hermana
		$this->myLogger->trace("El orden de salida original para manga:{$this->manga['ID']} jornada:{$this->jornada['ID']} es:\n{$hermanas[0]->Orden_Salida}");
        $this->setOrden($hermanas[1]->Orden_Salida);
        $this->setOrdenEquipos($hermanas[1]->Orden_Equipos);
		$this->myLogger->leave();
		return $hermanas[1]->Orden_Salida;
	}
	
	/**
	 * Calcula el orden de salida de una manga en funcion del orden inverso al resultado de su manga "hermana"
	 * @return {string} nuevo orden de salida; null on error
	 */
	function reverse() {
		$this->myLogger->enter();
		// fase 1: buscamos la "manga hermana"
		$mhandler=new Mangas("OrdenSalida::reverse()",$this->jornada['ID']);
		$hermanas=$mhandler->getHermanas($this->manga['ID']);
		if (!is_array($hermanas)) return $this->error("Error find hermanas info for jornada:{$this->jornada['ID']} and manga:{$this->manga['ID']}");
		if ($hermanas[1]==null) return $this->error("Cannot reverse order: Manga:{$this->manga['ID']} of Jornada:{$this->jornada['ID']} has no brother");
	
		// fase 2: evaluamos resultados de la manga hermana
		$this->myLogger->trace("El orden de salida original para manga:{$this->manga['ID']} jornada:{$this->jornada['ID']} es:\n{$hermanas[0]->Orden_Salida}");
		// En funcion del tipo de recorrido tendremos que leer diversos conjuntos de Resultados
		switch($hermanas[0]->Recorrido) {
			case 0: // Large,medium,small (3-heighs) Large,medium,small,tiny (4-heights)
				$this->invierteResultados($hermanas[1],0);
				$this->invierteResultados($hermanas[1],1);
				$this->invierteResultados($hermanas[1],2);
				if ($this->federation->get('Heights')==4) $this->invierteResultados($hermanas[1],5);
				break;
			case 1: // Large,medium+small (3heights) Large+medium,Small+tiny (4heights)
				if ($this->federation->get('Heights')==3) {
					$this->invierteResultados($hermanas[1],0);
					$this->invierteResultados($hermanas[1],3);
				} else {
					$this->invierteResultados($hermanas[1],6);
					$this->invierteResultados($hermanas[1],7);
				}
				break;
			case 2: // conjunta L+M+S (3 heights) L+M+S+T (4heights)
				if ($this->federation->get('Heights')==3) {
					$this->invierteResultados($hermanas[1],4);
				} else  {
					$this->invierteResultados($hermanas[1],8);
				}
				break;
		}
		$nuevo=$this->getOrden();
		$this->myLogger->trace("El orden de salida nuevo para manga:{$this->manga['ID']} jornada:{$this->jornada['ID']} es:\n$nuevo");
		$this->myLogger->leave();
		return $nuevo;
	}

    /**
     * Reasigna los dorsales de manera que coincida con el orden de salida
     * ESTA FUNCION SOLO DEBE USARSE EN PRUEBAS EN QUE EL ORDEN DE SALIDA SEA EL MISMO PARA TODAS LAS MANGAS
     */
    function reassignDorsal() {
        $o=explode(',',$this->getOrden());
        $dorsal=1;
        foreach($o as $perro){
            if ($perro=="BEGIN") continue;
            if ($perro=="END") continue;
            echo "Prueba:{$this->prueba['ID']} Jornada:{$this->jornada['ID']} Manga:{$this->manga['ID']} Dorsal: $dorsal Perro:$perro\n";
            $str1="UPDATE Inscripciones SET DORSAL=$dorsal WHERE (Prueba={$this->prueba['ID']}) AND (Perro=$perro)";
            $this->query($str1);
            $str2="UPDATE Resultados SET DORSAL=$dorsal WHERE (Jornada={$this->jornada['ID']}) AND (Perro=$perro)";
            $this->query($str2);
            $dorsal++;
        }
    }

} // class

?>