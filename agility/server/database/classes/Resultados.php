<?php
/*
Resultados.php

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


require_once(__DIR__."/DBObject.php");
require_once(__DIR__."/../../../modules/Federations.php");
require_once(__DIR__."/OrdenSalida.php");
require_once(__DIR__."/Mangas.php");
require_once(__DIR__."/Jornadas.php");
require_once(__DIR__."/Clubes.php");
require_once(__DIR__."/Jueces.php");

class Resultados extends DBObject {
	protected $IDManga; // ID de la manga
	protected $IDJornada; // ID de la jornada
	protected $dmanga=null; // datos de la manga
	protected $djornada=null;  // datos de la jornada
	protected $dequipos=null; // datos de los equipos
	protected $dprueba=null; // datos de la prueba
	protected $federation=null;

	function getDatosManga() {
		if ($this->dmanga!=null) return $this->dmanga;
		$idmanga=$this->IDManga;
		// si no los tenemos todavia consultamos la base de datos
		$obj=$this->__getObject("Mangas", $idmanga);
		if (!is_object($obj)) {
			$this->error("Manga $idmanga does not exists in database");
			return null;
		}
		// add some extra info
        $obj->NombreJuez1=$this->__getObject("Jueces",$obj->Juez1)->Nombre;
        $obj->NombreJuez2=$this->__getObject("Jueces",$obj->Juez2)->Nombre;
		$obj->TipoManga=Mangas::$tipo_manga[$obj->Tipo][1];
		$this->dmanga=$obj;
		return $this->dmanga;
	}
	
	function getDatosJornada() {
		if ($this->djornada!=null) return $this->djornada;
		$manga=$this->getDatosManga();
		$this->IDJornada=$manga->Jornada;
		$obj=$this->__getObject("Jornadas", $this->IDJornada);
		if (!is_object($obj)) {
			$this->error("Cannot locate JornadaID: {$this->IDJornada} for MangaID:{$this->IDManga} in database");
			return null;
		}
		$this->djornada=$obj;
		return $this->djornada;
	}

	function getDatosPrueba() {
		if ($this->dprueba!=null) return $this->dprueba;
		$obj=$this->__getObject("Pruebas", $this->IDPrueba);
		if (!is_object($obj)) {
			$this->error("Cannot locate PruebaID: {$this->IDPrueba} in database");
			return null;
		}
		$this->dprueba=$obj;
		return $this->dprueba;
	}

	function getDatosEquipos() {
        if ($this->dequipos!=null) return $this->dequipos;
        if ($this->IDJornada==0) $this->getDatosJornada();
        $eqobj=new Equipos("Resultados",$this->IDPrueba,$this->IDJornada);
        $teams=$eqobj->getTeamsByJornada();
        $this->dequipos=array(); // reindex teams by ID
        foreach($teams as $team) $this->dequipos[$team['ID']]=$team;
        return $this->dequipos;
    }

	function getFederation() {
		if ($this->federation != null) return $this->federation;
        $prb=$this->getDatosPrueba();
		$this->federation= Federations::getFederation(intval($prb->RSCE));
        $this->myLogger->trace("Datos prueba: ".json_encode($prb)." Datos federacion ".json_encode($this->federation));
		return $this->federation;
	}

	function isCerrada() {
		$jrd=$this->getDatosJornada();
		return 	($jrd->Cerrada!=0)? true:false;
	}

	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {integer} $manga Manga ID
	 * @throws Exception when
	 * - cannot contact database
	 * - invalid manga ID
	 * - manga is closed
	 */
	function __construct($file,$prueba,$manga) {
		parent::__construct($file);
		if ($manga<=0) {
			$this->errormsg="Resultados::Construct invalid Manga ID: $manga";
			throw new Exception($this->errormsg);
		}
		if ($prueba<=0) {
			$this->errormsg="Resultados::Construct invalid Prueba ID: $prueba";
			throw new Exception($this->errormsg);
		}
		$this->IDPrueba=$prueba;
		$this->IDJornada=0; // to be filled
		$this->IDManga=$manga;
		$this->dmanga=null;
		$this->djornada=null;
        $this->dequipos=null;
	}
	
	/**
	 * gets distance, obstacles, trs and trm
	 * @param {integer} $mode 0:Large 1:Medium 2:Small 3:M+S 4:L+M+S
	 * @param {array} $data current _ordered_ results data according $mode
	 * @return array('dist','obst','trs','trm','vel') or null on error
	 */
	private function evalTRS($mode,$data) {
		$dmanga=(array) $this->getDatosManga();
		$result= array();
		// vemos de donde tenemos que tomar los datos
		$suffix='L';
		switch($mode) {
			case 0: $suffix='L'; break; // L
 			case 1: $suffix='M'; break; // M
			case 2: $suffix='S'; break; // S
			case 3: $suffix='M'; break; // M+S
			case 4: $suffix='L'; break; // L+M+S
			// extra values for 4-heights contests
			case 5: $suffix='T'; break; // T
			case 6: $suffix='L'; break; // L+M
			case 7: $suffix='S'; break; // S+T
			case 8: $suffix="L"; break; // L+M+S+T
		}
		// evaluamos distancia y obstaculos
		$result['dist']= $dmanga["Dist_$suffix"]; 
		$result['obst']= $dmanga["Obst_$suffix"];
		// evaluamos mejor tiempo y media de los tres mejores
		$best1=0;
		$best3=0;
		if (count($data)==0) { $best1=0.0; $best3=0.0;} // no hay ni resultados ni tiempos
		if (count($data)==1) { $best1=$data[0]['Tiempo']; $best3=$data[0]['Tiempo'];}
		if (count($data)==2) { $best1=$data[0]['Tiempo']; $best3=($data[0]['Tiempo']+$data[1]['Tiempo'])/2.0;}
		if (count($data)>=3) { $best1=$data[0]['Tiempo']; $best3=($data[0]['Tiempo']+$data[1]['Tiempo']+$data[2]['Tiempo'])/3.0;}
		// Evaluamos TRS
        $factor=floatval($dmanga["TRS_{$suffix}_Factor"]);
		switch ($dmanga["TRS_{$suffix}_Tipo"]) {
			// NOTA IMPORTANTE: 
			// No se hace chequeo de tipos, con lo que si por error en un calculo de TRS standard se pide un tipo STD+XX
			// la aplicacion entrará en un bucle infinito
			case 0: // tiempo fijo
				$result['trs']=$factor;
				break;
			case 1: // mejor tiempo
				if ($dmanga["TRS_{$suffix}_Unit"]==="s") $result['trs']= $best1 + $factor; // ( + X segundos )
				else $result['trs']= $best1 * ( (100.0+$factor) / 100.0) ; // (+ X por ciento)
				break;
			case 2: // media de los tres mejores tiempos
				if ($dmanga["TRS_{$suffix}_Unit"]==="s") $result['trs']= $best3 + $factor; // ( + X segundos )
				else $result['trs']= $best3 * ( (100.0+$factor) / 100.0) ; // (+ X por ciento)
				break;
			case 3: // trs standard +xxx						
				$result_std=$this->getResultados(0)['trs'];
				if ($dmanga["TRS_{$suffix}_Unit"]==="s") 
					$result['trs']= $result_std['trs'] + $factor; // ( + X segundos )
				else $result['trs']= $result_std['trs'] * ( (100.0+$factor) / 100.0) ; // (+ X por ciento)
				break;
			case 4: // trs medium + xx						
				$result_med=$this->getResultados(1)['trs'];
				if ($dmanga["TRS_{$suffix}_Unit"]==="s") 
					$result['trs']= $result_med['trs'] + $factor; // ( + X segundos )
				else $result['trs']= $result_med['trs'] * ( (100.0+$factor) / 100.0) ; // (+ X por ciento)
				break;
			case 5: // trs small + xx
				$result_med=$this->getResultados(2)['trs'];
				if ($dmanga["TRS_{$suffix}_Unit"]==="s")
					$result['trs']= $result_med['trs'] + $factor; // ( + X segundos )
				else $result['trs']= $result_med['trs'] * ( (100.0+$factor) / 100.0) ; // (+ X por ciento)
				break;
			case 6: // en lugar de tiempo nos proporcionan velocidad
				$result['vel']=$factor;
				$result['trs']=ceil($result['dist']/$factor);
				break;
		}
		// si estamos en una selectiva RSCE, y el factor es 0.0 _NO_ se redondea
		$roundUp=true;
		if ( ($this->getDatosPrueba()->Selectiva==1) && ($dmanga["TRS_{$suffix}_Factor"]==0)) $roundUp=false;
		if ($roundUp) $result['trs']=ceil($result['trs']); // redondeamos hacia arriba
		// Evaluamos TRM
		switch($dmanga["TRM_{$suffix}_Tipo"]) {
			case 0: // TRM Fijo
				$result['trm']=$dmanga["TRM_{$suffix}_Factor"];
				break;
			case 1: // TRS + (segs o porcentaje)
				if ($dmanga["TRM_{$suffix}_Unit"]==="s") $result['trm']=$result['trs'] + $dmanga["TRM_{$suffix}_Factor"]; // ( + X segundos )
				else $result['trm'] = $result['trs'] * ( (100.0+$dmanga["TRM_{$suffix}_Factor"]) / 100.0) ; // (+ X por ciento)
				break;
		}
		if ($roundUp) $result['trm']=ceil($result['trm']); // redondeamos hacia arriba
		if (! array_key_exists('vel',$result) ) {
			// Finalmente, si no nos la han dado, evaluamos la velocidad de la ronda con dos decimales
			$result['vel']= ($result['trs']==0)?0:/*'&asymp;'.*/number_format($result['dist']/$result['trs'],2);
		}
		// esto es todo amigos
		return $result;
	}

		
	/**
	 * Inserta perro en la lista de resultados de la manga
	 * los datos del perro se toman de la tabla perroguiaclub
	 * @param {array} $objperro datos perroguiaclub
	 * @param {array} $inscripcion datos de la inscripcion
	 * @param {array} $eqdata datos del equipo por defecto de la jornada
	 * @return "" on success; else error string
	 */
	function insertByData($objperro,$inscripcion,$eqdata) {
		$this->myLogger->enter();
        $prueba=$this->IDPrueba;
        $this->getDatosJornada(); // make sure that inner data is filled
        $jornada=$this->IDJornada;
        $manga=$this->IDManga;
        $perro=$objperro['ID'];
        $equipo=$eqdata['ID'];
        $dorsal=$inscripcion['Dorsal'];
        $nombre=escapeString($objperro['Nombre']);
        $raza=escapeString($objperro['Raza']);
        $licencia=$objperro['Licencia'];
        $categoria=$objperro['Categoria'];
        $grado=$objperro['Grado'];
        $celo=$inscripcion['Celo'];
        $guia=escapeString($objperro['NombreGuia']);
        $club=escapeString($objperro['NombreClub']);
		if ($this->isCerrada()) 
			return $this->error("Manga $manga comes from closed Jornada:".$this->IDJornada);
		// If row pkey(manga,perro) exists, just update; else insert
        // remember Primary key: (manga,perro)
		$sql="INSERT INTO Resultados (Prueba,Jornada,Manga,Equipo,Dorsal,Perro,Raza,Nombre,Licencia,Categoria,Grado,Celo,NombreGuia,NombreClub)
                VALUES ($prueba,$jornada,$manga,$equipo,$dorsal,$perro,'$raza','$nombre','$licencia','$categoria','$grado',$celo,'$guia','$club')
                ON DUPLICATE KEY UPDATE Equipo=$equipo, Dorsal=$dorsal, Raza='$raza', Nombre='$nombre', Licencia='$licencia', Categoria='$categoria',
                                        Grado='$grado', Celo=$celo, NombreGuia='$guia', NombreClub='$club' ";
        $rs= $this->query($sql);
        if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Borra el idperro de la lista de resultados de la manga
	 * @param {integer} $idperro
	 * @return "" on success; null on error
	 */
	function delete($idperro) {
		$this->myLogger->enter();
		$idmanga=$this->IDManga;
		if ($idperro<=0) return $this->error("No Perro ID specified");
		if ($this->isCerrada()) 
			return $this->error("Manga $idmanga comes from closed Jornada:".$this->IDJornada);
		$str="DELETE FROM Resultados WHERE ( Perro=$idperro ) AND ( Manga=$idmanga)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}

	function reset() {
		$this->myLogger->enter();
		$idmanga=$this->IDManga;
		if ($this->isCerrada())
			return $this->error("Manga $idmanga comes from closed Jornada:".$this->IDJornada);
		$str="UPDATE Resultados
				SET Faltas=0, Tocados=0, Rehuses=0, Eliminado=0, NoPresentado=0, Tiempo=0, TIntermedio=0, Observaciones='', Pendiente=1
				WHERE ( Manga=$idmanga)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}

	/**
	 * selecciona los datos del idperro indicado desde la lista de resultados de la manga
	 * @param {integer} $idperro
	 * @return {array} [key=>value,...] on success; null on error
	 */
	function select($idperro) {
		$this->myLogger->enter();
		$idmanga=$this->IDManga;
		if ($idperro<=0) return $this->error("No Perro ID specified");
		$row=$this->__selectAsArray("*", "Resultados", "(Perro=$idperro) AND (Manga=$idmanga)");
		if(!is_array($row)) 
			return $this->error("No Results for Perro:$idperro on Manga:$idmanga");
		$this->myLogger->leave();
		return $row;
	}
	
	/**
	 * Actualiza los resultados de la manga para el idperro indicado
	 * @param {integer} $idperro
	 * @return {array} datos actualizados desde la DB; null on error
	 */
	function update($idperro) {
		$this->myLogger->enter();
		$idmanga=$this->IDManga;
		if ($idperro<=0) return $this->error("No Perro ID specified");
		if ($this->isCerrada()) 
			return $this->error("Manga $idmanga comes from closed Jornada:".$this->IDJornada);
		// buscamos la lista de parametros a actualizar
		$entrada=http_request("Entrada","s",date("Y-m-d H:i:s"));
		$comienzo=http_request("Comienzo","s",date("Y-m-d H:i:s"));
		$faltas=http_request("Faltas","i",0);
		$rehuses=http_request("Rehuses","i",0);
		$tocados=http_request("Tocados","i",0);
		$nopresentado=http_request("NoPresentado","i",0);
		$eliminado=http_request("Eliminado","i",0);
		$tiempo=http_request("Tiempo","d",0.0);
		$tintermedio=http_request("TIntermedio","d",0.0);
		$observaciones=http_request("Observaciones","s","");
		// si la actualizacion esta marcada como pendiente
		$pendiente=http_request("Pendiente","i",1);
		if ($pendiente==0) {
            // cuando pendiente es !=0 tenemos datos del recorrido definitivos.
            //
            // comprobamos la coherencia de los datos recibidos y ajustamos
            // NOTA: el orden de estas comprobaciones es MUY importantee
            $djornada=$this->getDatosJornada();
            if ($djornada->Equipos4!=0) { // pruebas por equipos en modalidad de cuatro conjunta
                if ($rehuses>=3) { $tiempo=0; $faltas=0; $tocados=0; $eliminado=1; $nopresentado=0;}
                if ($tiempo>0) {$nopresentado=0;}
                if ($eliminado==1) { $tiempo=0; $faltas=0; $tocados=0; $rehuses=0; $nopresentado=0; }
                if ($nopresentado==1) { $tiempo=0; $eliminado=0; $faltas=0; $rehuses=0; $tocados=0; }
                // en este tipo de pruebas, el tiempo puede ser cero, pues solo se le apunta al ultimo del equipo
                // if ( ($tiempo==0) && ($eliminado==0)) { $nopresentado=1; $faltas=0; $rehuses=0; $tocados=0; }
                if ( ($tiempo==0) && ($eliminado==1)) { $nopresentado=0; }
            } else { // pruebas "normales" y mangas ko
                if ($rehuses>=3) { $tiempo=0; $eliminado=1; $nopresentado=0;}
                if ($tiempo>0) {$nopresentado=0;}
                if ($eliminado==1) { $tiempo=0; $nopresentado=0; }
                if ($nopresentado==1) { $tiempo=0; $eliminado=0; $faltas=0; $rehuses=0; $tocados=0; }
                if ( ($tiempo==0) && ($eliminado==0)) { $nopresentado=1; $faltas=0; $rehuses=0; $tocados=0; }
                if ( ($tiempo==0) && ($eliminado==1)) { $nopresentado=0; }
            }
		}
        $this->myLogger->trace("Tiempo es '$tiempo' '");
		// efectuamos el update, marcando "pendiente" como false
		$sql="UPDATE Resultados 
			SET Entrada='$entrada' , Comienzo='$comienzo' , 
				Faltas=$faltas , Rehuses=$rehuses , Tocados=$tocados ,
				NoPresentado=$nopresentado , Eliminado=$eliminado , 
				Tiempo='$tiempo' , Observaciones='$observaciones' , Pendiente=$pendiente
			WHERE (Perro=$idperro) AND (Manga=$this->IDManga)";
		$rs=$this->query($sql);
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return $this->select($idperro);
	}

	/**
	 * Elabora una lista con los perros pendientes de salir en la manga
	 */
	function getPendientes($mode) {
		$this->myLogger->enter();
		$idmanga=$this->IDManga;
		$where="(Manga=$idmanga) AND (Pendiente=1) "; // para comprobar pendientes
		$cat="";
		switch ($mode) {
			case 0: /* Large */		$cat= "AND (Categoria='L')"; break;
			case 1: /* Medium */	$cat= "AND (Categoria='M')"; break;
			case 2: /* Small */		$cat= "AND (Categoria='S')"; break;
			case 3: /* Med+Small */ $cat= "AND ( (Categoria='M') OR (Categoria='S') )"; break;
			case 4: /* L+M+S */ 	$cat= "AND ( (Categoria='L') OR (Categoria='M') OR (Categoria='S') )"; break;
			case 5: /* Tiny */		$cat= "AND (Categoria='T')"; break;
			case 6: /* L+M */		$cat= "AND ( (Categoria='L') OR (Categoria='M') )"; break;
			case 7: /* S+T */		$cat= "AND ( (Categoria='S') OR (Categoria='T') )"; break;
			case 8: /* L+M+S+T */	break; // no check categoria
			default: return $this->error("modo de recorrido desconocido:$mode");
		}
        $this->myLogger->leave();
		// FASE 0: comprobamos si hay perros pendientes de salir
		return $this->__select(
			/* SELECT */	"*",
			/* FROM */		"Resultados",
			/* WHERE */		"$where $cat",
			/* ORDER BY */	"Nombre ASC",
			/* LIMIT */		""
		);
	}

	/**
	 * Retrieve best intermediate and final times on this round
	 * @param $mode 0:L 1:M 2:S 3:MS 4:LMS.
	 * @return {array} requested data or error
	 */
	function bestTimes($mode) {
		$this->myLogger->enter();

		// FASE 0: en funcion del tipo de recorrido y modo pedido
		// ajustamos el criterio de busqueda de la tabla de resultados
		$where="(Manga={$this->IDManga}) AND (Pendiente=0) ";
		$cat="";
		switch ($mode) {
			case 0: /* Large */		$cat= "AND (Categoria='L')"; break;
			case 1: /* Medium */	$cat= "AND (Categoria='M')"; break;
			case 2: /* Small */		$cat= "AND (Categoria='S')"; break;
			case 3: /* Med+Small */ $cat= "AND ( (Categoria='M') OR (Categoria='S') )"; break;
			case 4: /* L+M+S */ 	$cat= "AND ( (Categoria='L') OR (Categoria='M') OR (Categoria='S') )"; break;
			case 5: /* Tiny */		$cat= "AND (Categoria='T')"; break;
			case 6: /* L+M */		$cat= "AND ( (Categoria='L') OR (Categoria='M') )"; break;
			case 7: /* S+T */		$cat= "AND ( (Categoria='S') OR (Categoria='T') )"; break;
			case 8: /* L+M+S+T */	break; // no check categoria
			default: return $this->error("modo de recorrido desconocido:$mode");
		}
		//  evaluamos mejores tiempos intermedios y totales
		$best=$this->__(
			"min(TIntermedio) AS BestIntermedio, min(Tiempo) AS BestFinal",
			"Resultados",
			"(Tiempo>0) AND $where $cat",
			"",
			""
		);
		if (!is_array($best))
			return $this->error($this->conn->error);
		return $best;
	}

	/**
	 * Presenta una tabla ordenada segun los resultados de la manga
	 *@param {integer} $mode 0:L 1:M 2:S 3:MS 4:LMS.
	 *@return {array} requested data or error
	 */
	function getResultados($mode) {
		$this->myLogger->enter();
		$idmanga=$this->IDManga;
		
		// FASE 0: en funcion del tipo de recorrido y modo pedido
		// ajustamos el criterio de busqueda de la tabla de resultados
		$where="(Manga=$idmanga) AND (Pendiente=0) ";
		$cat="";
		switch ($mode) {
			case 0: /* Large */		$cat= "AND (Categoria='L')"; break;
			case 1: /* Medium */	$cat= "AND (Categoria='M')"; break;
			case 2: /* Small */		$cat= "AND (Categoria='S')"; break;
			case 3: /* Med+Small */ $cat= "AND ( (Categoria='M') OR (Categoria='S') )"; break;
			case 4: /* L+M+S */ 	$cat= "AND ( (Categoria='L') OR (Categoria='M') OR (Categoria='S') )"; break;
			case 5: /* Tiny */		$cat= "AND (Categoria='T')"; break;
			case 6: /* L+M */		$cat= "AND ( (Categoria='L') OR (Categoria='M') )"; break;
			case 7: /* S+T */		$cat= "AND ( (Categoria='S') OR (Categoria='T') )"; break;
			case 8: /* L+M+S+T */	break; // no check categoria
			default: return $this->error("modo de recorrido desconocido:$mode");
		}
		// FASE 1: recogemos resultados ordenados por precorrido y tiempo
		$res=$this->__select(
				"Dorsal,Perro,Nombre,Raza,Equipo,Licencia,Categoria,Grado,NombreGuia,NombreClub,Faltas,Tocados,Rehuses,Tiempo,Eliminado,NoPresentado,
					( 5*Faltas + 5*Rehuses + 5*Tocados + 100*Eliminado + 200*NoPresentado ) AS PRecorrido,
					0 AS PTiempo, 0 AS Penalizacion, '' AS Calificacion, 0 AS Velocidad", 
				"Resultados", 
				"$where $cat",
				" PRecorrido ASC, Tiempo ASC", 
				"");
		if (!is_array($res))
			return $this->error($this->conn->error);
		$table=$res['rows'];
		$this->myLogger->leave();
		// FASE 2: evaluamos TRS Y TRM
		$tdata=$this->evalTRS($mode,$table); // array( 'dist' 'obst' 'trs' 'trm', 'vel')
		$res['trs']=$tdata; // store trs data into result
		$trs=$tdata['trs'];
		$trm=$tdata['trm'];
		// FASE 3: añadimos ptiempo, puntuacion, clasificacion y logo
        $clubes=new Clubes("Resultados::getResultados",$this->getDatosPrueba()->RSCE);
		$size=count($table);
		for ($idx=0;$idx<$size;$idx++ ){
            $table[$idx]['Puntos'] = 0; // to be re-evaluated later
			// importante: las asignaciones se hacen en base a $table[$idx], 
			// pues si no solo se actualiza la copia
			
			if ($trs==0) {
				// si TRS==0 no hay penalizacion por tiempo
				$table[$idx]['PTiempo']		= 	0.0; 
				$table[$idx]['Penalizacion']=	$table[$idx]['PRecorrido'];
			} else {
				// evaluamos penalizacion por tiempo y penalizacion final
				if ($table[$idx]['Tiempo']<$trs) { // Por debajo del TRS
					$table[$idx]['PTiempo']		= 	0.0; 
					$table[$idx]['Penalizacion']=	$table[$idx]['PRecorrido'];
				}
				if ($table[$idx]['Tiempo']>=$trs) { // Superado TRS
					$table[$idx]['PTiempo']		=	$table[$idx]['Tiempo'] 		-	$trs; 
					$table[$idx]['Penalizacion']=	floatval($table[$idx]['PRecorrido'])	+	$table[$idx]['PTiempo'];
				}
				if ($table[$idx]['Tiempo']>$trm) { // Superado TRM: eliminado
					$table[$idx]['Penalizacion']=	100.0;
				}
			}
				
			// evaluamos velocidad 
			if ($table[$idx]['Tiempo']==0)	$table[$idx]['Velocidad'] = 0;
			else 	$table[$idx]['Velocidad'] =  $tdata['dist'] / $table[$idx]['Tiempo'];

            // anyadimos nombre del equipo
            $dequipos=$this->getDatosEquipos();
            $eqinfo=$dequipos[]=$dequipos[$table[$idx]['Equipo']];
            $table[$idx]['NombreEquipo']=$eqinfo['Nombre'];
            // anyadimos logotipo del club
            $table[$idx]['LogoClub']=$clubes->getLogoName('NombreClub',$table[$idx]['NombreClub']);
		}
		// FASE 4: re-ordenamos los datos en base a la puntuacion y calculamos campo "Puesto"
		usort($table, function($a, $b) {
			if ( $a['Penalizacion'] == $b['Penalizacion'] )	return ($a['Tiempo'] > $b['Tiempo'])? 1:-1;
			return ( $a['Penalizacion'] > $b['Penalizacion'])?1:-1;
		});
		
		// format output data and take care con duplicated penalizacion and time
        // calculamos campo "Puesto", "Calificacion" y Puntos
        $puestocat=array( 'C'=>1, 'L' => 1, 'M'=>1, 'S'=>1, 'T'=>1); // ultimo puesto por cada categoria
        $lastcat=array( 'C'=>0, 'L' => 0, 'M'=>0, 'S'=>0, 'T'=>0);  // ultima puntuacion por cada categoria
        $countcat=array( 'C'=>0, 'L' => 0, 'M'=>0, 'S'=>0, 'T'=>0); // perros contabilizados de cada categoria
        $fed=$this->getFederation();
		for($idx=0;$idx<$size;$idx++) {
            // vemos la categoria y actualizamos contadores de categoria
            $cat=$table[$idx]['Categoria'];
            $countcat['C']++; // Conjunta
            $countcat[$cat]++; // Por categoria

            // obtenemos la penalizacion del perro actual
            $now=100*$table[$idx]['Penalizacion']+$table[$idx]['Tiempo'];

            // ajustamos puesto conjunto y guardamos resultado
            if ($lastcat['C']!=$now) { $lastcat['C']=$now; $puestocat['C']=$countcat['C']; }
            $table[$idx]['Puesto']=$puestocat['C'];

            // ajustamos puesto por categoria y guardamos resultado
            if ($lastcat[$cat]!=$now) { $lastcat[$cat]=$now; $puestocat[$cat]=$countcat[$cat]; }
            $table[$idx]['Pcat']=$puestocat[$cat];

			// la calificacion depende de categoria, grado y federacion
			$fed->evalPartialCalification($this->getDatosPrueba(),$this->getDatosJornada(),$this->getDatosManga(),$table[$idx],$puestocat);
		}

        // componemos datos del array a retornar
        $res['rows']=$table;
        $res['manga']=$this->getDatosManga();
        $res['trs']=$tdata;
        $this->myLogger->leave();
		return $res;
	}
	
	function getTRS($mode) {
		$this->myLogger->enter();
		$trs=$this->getResultados($mode)['trs'];
		$this->myLogger->leave();
		return $trs;
	}

    /**
     * Gestion de resultados en Equipos3/Equipos4
     * Agrupa los resultados por equipos y genera una lista de equipos ordenados por resultados
     * @param {array} resultados de la manga ordenados por participante
     * @param {int} prueba PruebaID
     * @param {int} jornada JornadaID
     * @param {int} $tmode 3 o 4
     * @return {array} datos de equipos de la manga ordenados por resultados de equipo
     */
    static function getTeamResults($resultados,$prueba,$jornada,$tmode=3) {
        // Datos de equipos de la jornada. obtenemos prueba y jornada del primer elemento del array
        $m=new Equipos("getTeamResults",$prueba,$jornada);
        $teams=$m->getTeamsByJornada();

        // reindexamos por ID y anyadimos un campos extra Tiempo, penalizacion y el array de resultados del equipo
        $equipos=array();
        foreach ($teams as &$equipo) {
            $equipo['Resultados']=array();
            $equipo['Tiempo']=0.0;
            $equipo['Penalizacion']=0.0;
            $equipos[$equipo['ID']]=$equipo;
        }
        // now fill team members array.
        // notice that $resultados is already sorted by results
        foreach($resultados as &$result) {
            $teamid=$result['Equipo'];
            $equipo=&$equipos[$teamid];
            array_push($equipo['Resultados'],$result);
            // suma el tiempo y penalizaciones de los tres/cuatro primeros
            if (count($equipo['Resultados'])<=$tmode) {
                $equipo['Tiempo']+=floatval($result['Tiempo']);
                $equipo['Penalizacion']+=floatval($result['Penalizacion']);
            }
        }

        // rastrea los equipos con menos de tres participantes y marca los que faltan
        // no presentados
        $teams=array();
        foreach($equipos as &$equipo) {
            switch(count($equipo['Resultados'])){
                case 0: continue; // ignore team
                case 1: $equipo['Penalizacion']+=200.0; // add pending "No presentado"
                // no break
                case 2: $equipo['Penalizacion']+=200.0; // add pending "No presentado"
                // no break;
                case 3: if ($tmode==4) $equipo['Penalizacion']+=200.0; // add pending "No presentado"
                // no break;
                case 4:
                    array_push($teams,$equipo); // add team to result to remove unused/empty teams
                    break;
                default:
                    $myLogger=new Logger("Resultados::getTreamResults()");
                    $myLogger->error("Equipo {$equipo['ID']} : '{$equipo['Nombre']}' con exceso de participantes:".count($equipo['Resultados']));
                    break;
            }
        }
        // finally sort equipos by result instead of id
        usort($teams,function($a,$b){
            if ($a['Penalizacion']==$b['Penalizacion']) return ($a['Tiempo']>$b['Tiempo'])?1:-1;
            return ($a['Penalizacion']>$b['Penalizacion'])?1:-1;
        });
        return $teams;
    }



}
?>