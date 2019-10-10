<?php
/*
Resultados.php

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
require_once(__DIR__ . "/../../modules/Competitions.php");
require_once(__DIR__."/OrdenSalida.php");
require_once(__DIR__."/Mangas.php");
require_once(__DIR__."/Jornadas.php");
require_once(__DIR__."/Clubes.php");

class Resultados extends DBObject {
    protected $IDPrueba; // ID de la manga
    protected $IDJornada; // ID de la jornada
    protected $IDManga; // ID de la manga
	protected $dmanga=null; // datos de la manga
	protected $djornada=null;  // datos de la jornada
	protected $dequipos=null; // datos de los equipos
	protected $dprueba=null; // datos de la prueba
    protected $dcompetition=null; // datos del modulo de competicion
    protected $mangaInfo=null; // datos globales prueba,jornada, manga,federacion,competicion

	protected $federation=null;

	function getDatosManga() { return $this->dmanga; }
	function getDatosJornada() { return $this->djornada; }
	function getDatosPrueba() { return $this->dprueba; }

	function getCompetitionObject() {
        if ($this->dcompetition==null) {
            $this->dcompetition=Competitions::getCompetition($this->getDatosPrueba(),$this->getDatosJornada());
        }
        return $this->dcompetition;
    }

	function getDatosEquipos() {
        if ($this->dequipos!==null) return $this->dequipos;
        $eqobj=new Equipos("Resultados",$this->getDatosPrueba()->ID,$this->getDatosJornada()->ID);
        $teams=$eqobj->getTeamsByJornada();
        $res=array(); // reindex teams by ID
        foreach($teams as $team) $res[$team['ID']]=$team;
        $this->dequipos=$res;
        return $this->dequipos;
    }

	function getFederation() {
		if ($this->federation !== null) return $this->federation;
        $prb=$this->getDatosPrueba();
		$this->federation= Federations::getFederation(intval($prb->RSCE));
        // $this->myLogger->trace("Datos prueba: ".json_encode($prb)." Datos federacion ".json_encode($this->federation));
		return $this->federation;
	}

	function isCerrada() {
		$jrd=$this->getDatosJornada();
		return 	($jrd->Cerrada!=0)? true:false;
	}

	/**
	 * Constructor
	 * @param {string} $file caller for this object
     * @param {object} $prueba Prueba
     * @param {object} $jornada Jornada
     * @param {object} $manga Manga
	 * @throws Exception when
	 * - cannot contact database
	 * - invalid manga ID
	 * - manga is closed
	 */
	function __construct($file,$prueba,$jornada,$manga) {
		parent::__construct($file);
        $this->dprueba=$prueba;
        $this->IDPrueba=$prueba->ID;
        $this->djornada=$jornada;
        $this->IDJornada=$jornada->ID;
		// add additional info to manga
        $manga->NombreJuez1=$this->__getObject("jueces",$manga->Juez1)->Nombre;
        $manga->NombreJuez2=$this->__getObject("jueces",$manga->Juez2)->Nombre;
        $manga->TipoManga=_(Mangas::getTipoManga($manga->Tipo,1,$this->getFederation()));
		$this->dmanga=$manga;
        $this->IDManga=$manga->ID;
        $this->mangaInfo=Mangas::getMangaInfo($manga->ID);
	}
	
	/**
	 * gets distance, obstacles, trs, trm, and velocity
	 * @param {integer} $mode 0:Large 1:Medium 2:Small 3:M+S 4:L+M+S ....
	 * @param {array} $data current _ordered_ results data according $mode
	 * @return array('dist','obst','trs','trm','vel') or null on error
	 */
	protected function evalTRS($mode,$data) {
		$result= array();
		// vemos de donde tenemos que tomar los datos
        // suffix to use with data from $manga according mode
        //    heihgts   3   3   3   3   3    4   4   4   4    5    5    5     5
        //    cats      L   M   S   MS LMS   T  LM  ST  LMST  X   XL   MST  XLMST
        //    mode      0   1   2   3   4    5   6   7   8    9   10   11    12
        $suffix=array( 'L','M','S','M','L', 'T','L','S','L', 'X', 'X', 'M', 'X')[$mode];

        // en el caso de pruebas subordinadas ( por ejemplo, selectiva del pastor belga),
        // puede ocurrir que los datos ( mejor o tres mejores ) no haya que tomarlos de la
        // manga actual, sino de la manga padre.
        // para contemplarlo, hacemos un bypass, que nos devolvera los datos correctos
        $roundUp=true; // default is round up SCT and MCT to nearest integer second
        $comp=$this->getCompetitionObject();
        $data=$comp->checkAndFixTRSData($this->dmanga,$data,$mode,$roundUp);

        // si el trs no va por velocidad y esta especificado con decimales, no se redondea
        // $t=$this->getDatosManga()->{"TRS_{$suffix}_Factor"};
        // if ( $t - (int)$t != 0) $roundUp=false;

		// evaluamos distancia y obstaculos
		$result['dist']= $this->getDatosManga()->{"Dist_$suffix"};
		$result['obst']= $this->getDatosManga()->{"Obst_$suffix"};
		// evaluamos mejor tiempo y media de los tres mejores
		$best1=0;
		$best3=0;
		if (count($data)==0) { $best1=0.0; $best3=0.0;} // no hay ni resultados ni tiempos
		if (count($data)==1) { $best1=$data[0]['Tiempo']; $best3=$data[0]['Tiempo'];}
		if (count($data)==2) { $best1=$data[0]['Tiempo']; $best3=($data[0]['Tiempo']+$data[1]['Tiempo'])/2.0;}
		if (count($data)>=3) { $best1=$data[0]['Tiempo']; $best3=($data[0]['Tiempo']+$data[1]['Tiempo']+$data[2]['Tiempo'])/3.0;}
		// Evaluamos TRS
        $factor=floatval($this->getDatosManga()->{"TRS_{$suffix}_Factor"});
		switch ($this->getDatosManga()->{"TRS_{$suffix}_Tipo"}) {
			// NOTA IMPORTANTE: 
			// No se hace chequeo de tipos, con lo que si por error en un calculo de TRS standard se pide un tipo STD+XX
			// la aplicacion entrará en un bucle infinito
			case 0: // tiempo fijo
				$result['trs']=$factor;
				break;
			case 1: // mejor tiempo
				if ($this->getDatosManga()->{"TRS_{$suffix}_Unit"}==="s") $result['trs']= $best1 + $factor; // ( + X segundos )
				else $result['trs']= $best1 * ( (100.0+$factor) / 100.0) ; // (+ X por ciento)
				break;
			case 2: // media de los tres mejores tiempos
				if ($this->getDatosManga()->{"TRS_{$suffix}_Unit"}==="s") $result['trs']= $best3 + $factor; // ( + X segundos )
				else $result['trs']= $best3 * ( (100.0+$factor) / 100.0) ; // (+ X por ciento)
				break;
            case 7: // trs xl +xxx
                $result_xl=$this->getResultadosIndividual(9)['trs'];
                if ($this->getDatosManga()->{"TRS_{$suffix}_Unit"}==="s")
                    $result['trs']= $result_xl['trs'] + $factor; // ( + X segundos )
                else $result['trs']= $result_xl['trs'] * ( (100.0+$factor) / 100.0) ; // (+ X por ciento)
                break;
			case 3: // trs standard +xxx						
				$result_std=$this->getResultadosIndividual(0)['trs'];
				if ($this->getDatosManga()->{"TRS_{$suffix}_Unit"}==="s")
					$result['trs']= $result_std['trs'] + $factor; // ( + X segundos )
				else $result['trs']= $result_std['trs'] * ( (100.0+$factor) / 100.0) ; // (+ X por ciento)
				break;
			case 4: // trs medium + xx						
				$result_med=$this->getResultadosIndividual(1)['trs'];
				if ($this->getDatosManga()->{"TRS_{$suffix}_Unit"}==="s")
					$result['trs']= $result_med['trs'] + $factor; // ( + X segundos )
				else $result['trs']= $result_med['trs'] * ( (100.0+$factor) / 100.0) ; // (+ X por ciento)
				break;
			case 5: // trs small + xx
				$result_med=$this->getResultadosIndividual(2)['trs'];
				if ($this->getDatosManga()->{"TRS_{$suffix}_Unit"}==="s")
					$result['trs']= $result_med['trs'] + $factor; // ( + X segundos )
				else $result['trs']= $result_med['trs'] * ( (100.0+$factor) / 100.0) ; // (+ X por ciento)
				break;
			case 6: // en lugar de tiempo nos proporcionan velocidad
				$result['vel']=$factor;
				if ($roundUp==true) $result['trs']=ceil($result['dist']/$factor);
				else $result['trs']=$result['dist']/$factor;
				break;
		}

		// Evaluamos TRM
        $trm_factor=$this->getDatosManga()->{"TRM_{$suffix}_Factor"};
		switch($this->getDatosManga()->{"TRM_{$suffix}_Tipo"}) {
            case 6:
                // Velocidad ( se aplica en wao, donde el trm es 2.8 - 3.0 m/s )
                // si factor==0 error: ignora y asume trm fijo=0, para que cante en el listado
                if ($trm_factor!=0) { $result['trm']=ceil($result['dist']/$trm_factor); break; }
                // no break;
			case 0: // TRM Fijo
				$result['trm']=$trm_factor;
				break;
			case 1: // TRS + (segs o porcentaje)
				if ($this->getDatosManga()->{"TRM_{$suffix}_Unit"}==="s")
				    $result['trm']=$result['trs'] + $trm_factor; // ( + X segundos )
				else $result['trm'] = $result['trs'] * ( (100.0+$trm_factor) / 100.0) ; // (+ X por ciento)
				break;
		}
		// ajustamos los datos finales en funcion de si hay que redondear o no
		if ($roundUp==true) { // redondeamos hacia arriba
            $result['trs']=ceil($result['trs']);
		    $result['trm']=ceil($result['trm']);
        }  else { // si no, ajustamos a dos decimales
            $result['trs']=number_format2($result['trs'],2);
            $result['trm']=number_format2($result['trm'],2);
        }
		if (! array_key_exists('vel',$result) ) {
			// Finalmente, si no nos la han dado, evaluamos la velocidad de la ronda con dos decimales
			$result['vel']= ($result['trs']==0)?0:/*'&asymp;'.*/number_format2($result['dist']/$result['trs'],2);
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
		$sql="INSERT INTO resultados (Prueba,Jornada,Manga,Equipo,Dorsal,Perro,Raza,Nombre,Licencia,Categoria,Grado,Celo,NombreGuia,NombreClub)
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
		$rs=$this->__delete("resultados","( Perro={$idperro} ) AND ( Manga={$idmanga})");
		if (!$rs) return $this->error($this->conn->error);
		$this->myLogger->leave();
		return "";
	}

    /**
     * Used in excel import to find round entries that matches with provided search criteria
     */
    function enumerate($mode=12) { // 12 (XLMST) includes 4 (LMS) and 8 (LMST)
        $this->myLogger->enter();
        // evaluate search criteria for query
        $q=http_request("q","s","");
        $where="1";
        if ($q!=="") $where="( Nombre LIKE '%".$q."%' )";
        $cats="";
        $heights=$this->mangaInfo->Competition->getHeights($this->mangaInfo);
        if ($mode!=12) $cats=sqlFilterCategoryByMode($mode, $heights,"");
        $result=$this->__select(
        /* SELECT */ "*",
            /* FROM */ "resultados",
            /* WHERE */ "( Manga={$this->IDManga} ) {$cats} AND {$where}",
            /* ORDER BY */ "Nombre ASC",
            /* LIMIT */ ""
        );
        $this->myLogger->leave();
        return $result;
    }

	function reset($catsmode) {
		$this->myLogger->enter();
		$where="";
		$heights=$this->mangaInfo->Competition->getHeights($this->mangaInfo);
		if ($catsmode!=12) $where=sqlFilterCategoryByMode($catsmode, $heights,""); // 12(XLMST) includes 4(LMS) and 8(LMST)
		$this->getDatosJornada(); // also implies getDatosManga
		$idmanga=$this->IDManga;
		if ($this->isCerrada())
			return $this->error("Manga $idmanga comes from closed Jornada:".$this->IDJornada);
		$str="UPDATE resultados
				SET Faltas=0, Tocados=0, Rehuses=0, Eliminado=0, NoPresentado=0, Tiempo=0, TIntermedio=0, Observaciones='', Pendiente=1
				WHERE ( Manga=$idmanga) $where";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
        // also reset every related rounds on subordinate journeys in a recursive way
        $mobj= new Mangas("Resultados::reset()",$this->IDJornada);
        $lst=$mobj->getSubordinates($idmanga);
        foreach ($lst['rows'] as $mng) {
            $jid=$mng['Jornada'];
            $mid=$mng['ID'];
            $subRes=Competitions::getResultadosInstance("reset round:$mid on journey:$jid childOf:{$this->IDJornada}",$mid);
            $res=$subRes->reset($catsmode);
            if ($res!="") return $this->error($res);
        }
		$this->myLogger->leave();
		return "";
	}

	/**
	 * Intercambia los resultados de la manga actual con la manga hermana
	 * @param {integer} $id ID De manga origen
	 * @param {string} $cat "-XLMST" (una letra) que indica las categorias a las que afecta el swap
     * @param {integer} $tipo tipomanga cuando el swap se puede hacer sobre mas de una manga
	 * @return {string} "" in success else error string
	 */
	function swapMangas($cats,$tipo=0) {
		$this->myLogger->enter();
        assertClosedJourney($this->getDatosJornada());
		$tipo1=$this->getDatosManga()->Tipo;
		$hermanas=Mangas::$manga_hermana[$tipo1]; // get array of compatible mangas
        $tipo2=($tipo==0)? $tipo2=$hermanas[0]:$tipo; // when no type specified get first brother
		if ($tipo2==0) {
			return $this->error("La manga:{$this->IDManga} de tipo:$tipo1 no tiene hermana asociada");
		}
		// Obtenemos __Todas__ las mangas de esta jornada que tienen el tipo buscado ninguna, una o hasta 8(k.O.)
		$result2=$this->__select("*","mangas","( Jornada={$this->getDatosJornada()->ID} ) AND ( Tipo=$tipo2)","","");
		if (!is_array($result2)) { // inconsistency error muy serio
			return $this->error("Falta la manga hermana de tipo:$tipo2 para manga:{$this->IDmanga} de tipo:$tipo1");
		}
		if (count($result2['rows'])!=1) { // no sense swap in single or multi-round series
			return $this->error("Tiene que haber una y solo una hermana de tipo:$tipo2 para la manga:{$this->IDManga} de tipo:$tipo1");
		}
		$manga2=$result2['rows'][0];
        $where="";
        switch ($cats) {
            case 'X':  $where= " AND ( Categoria='X' )"; break;
            case 'L':  $where= " AND ( Categoria='L' )"; break;
            case 'M':  $where= " AND ( Categoria='M' )"; break;
            case 'S':  $where= " AND ( Categoria='S' )"; break;
			case 'T':  $where= " AND ( Categoria='T' )"; break;
			case '-':  $where= ""; break;
			default: $this->myLogger->error("resultados::swap() invalid category value: '$cats''");
        }
		// para intercambiar las mangas y debido a que mysql realiza comprobaciones de integridad en cada registro
		// al hacer un update multiple, es preciso desactivar la primary key, y luego volverla  a activar
		$rconn=DBConnection::getRootConnection();
		$rconn->query("ALTER Table `resultados` DROP PRIMARY KEY");
        // ejecutamos el query
        $str="UPDATE resultados SET Manga =
                CASE 
                    WHEN Manga={$this->IDManga} THEN {$manga2['ID']} 
                    WHEN Manga={$manga2['ID']} THEN {$this->IDManga} 
                END
                WHERE Manga IN ({$this->IDManga},{$manga2['ID']}) $where";
		$res=$this->query($str);
        if (!$res) return $this->error($this->conn->error);
		// restauramos claves primarias
		$rconn->query("ALTER Table `resultados` ADD PRIMARY KEY (`Manga`,`Perro`)");
		DBConnection::closeConnection($rconn);
        // also reset every related rounds on subordinate journeys in a recursive way
        $mobj= new Mangas("Resultados::swap()",$this->IDJornada);
        $lst=$mobj->getSubordinates($this->IDManga);
        foreach ($lst['rows'] as $mng) {
            $jid=$mng['Jornada'];
            $mid=$mng['ID'];
            $subRes=Competitions::getResultadosInstance("swap round:$mid on journey:$jid childOf:{$this->IDJornada}",$mid);
            $res=$subRes->swapMangas($cats);
            if ($res!="") return $this->error($res);
        }
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
		$row=$this->__selectAsArray("*", "resultados", "(Perro=$idperro) AND (Manga=$idmanga)");
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
        // buscamos la lista de parametros a actualizar
        $data['Entrada'] = http_request("Entrada", "s", date("Y-m-d H:i:s"));
        $data['Comienzo'] = http_request("Comienzo", "s", date("Y-m-d H:i:s"));
        $data['Faltas'] = http_request("Faltas", "i", 0);
        $data['Rehuses'] = http_request("Rehuses", "i", 0);
        $data['Tocados'] = http_request("Tocados", "i", 0);
        $data['Games'] = http_request("Games", "i", 0);
        $data['NoPresentado'] = http_request("NoPresentado", "i", 0);
        $data['Eliminado'] = http_request("Eliminado", "i", 0);
        $data['Tiempo'] = http_request("Tiempo", "d", 0.0);
        $data['TIntermedio'] = http_request("TIntermedio", "d", 0.0);
        $data['Observaciones'] = http_request("Observaciones", "s", "");
        $data['Pendiente'] = http_request("Pendiente", "i", 1);
        // invocamos a la funcion que "hace las cosas" :-)
        return $this->real_update($idperro,$data);
    }

	function real_update($idperro,$data) {
        $this->myLogger->enter();
        $idmanga = $this->IDManga;
        $idjornada = $this->IDJornada;
        if ($idperro <= 0) return $this->error("No Perro ID specified");
        if ($this->isCerrada())
            return $this->error("Manga $idmanga comes from closed Jornada:" . $this->IDJornada);

        // buscamos la lista de parametros a actualizar

        // algunos parametros opcionales los describimos como texto sql con coma al final
        // los demas los ponemos con su valor numerico, por si hay que modificarlos
        $entrada=isset($data['Entrada'])?" Entrada='{$data['Entrada']}' ,":""; // texto sql
        $comienzo=isset($data['Comienzo'])?" Comienzo='{$data['Comienzo']}' ,":""; // texto sql
        $faltas=$data['Faltas'];
        $rehuses=$data['Rehuses'];
        $tocados=isset($data['Tocados'])?$data['Tocados']:0;
        $games=isset($data['Games'])?" Games={$data['Games']} ,":""; // texto sql
        $nopresentado=$data['NoPresentado'];
        $eliminado=$data['Eliminado'];
        $tiempo=$data['Tiempo'];
        $tintermedio=isset($data['TIntermedio'])?$data['TIntermedio']:0;
        $observaciones=isset($data['Observaciones'])?" Observaciones='{$data['Observaciones']}' ,":""; // texto sql
        $pendiente=$data['Pendiente'];
		if ($pendiente==0) {
            // cuando pendiente es ==0 tenemos datos del recorrido definitivos ( ya no esta pendiente :-)
            //
            // comprobamos la coherencia de los datos recibidos y ajustamos
            // NOTA: el orden de estas comprobaciones es MUY importantee
            $djornada=$this->getDatosJornada();
            if (intval($djornada->Equipos4)!==0) { // pruebas por equipos en modalidad de cuatro conjunta
                if ($rehuses>=3) { $tiempo=0; $tintermedio=0; $faltas=0; $tocados=0; $eliminado=1; $nopresentado=0;}
                if ($tiempo>0) {$nopresentado=0;}
                if ($eliminado==1) { $tiempo=0; $tintermedio=0; $faltas=0; $tocados=0; $rehuses=0; $nopresentado=0; }
                if ($nopresentado==1) { $tiempo=0; $tintermedio=0; $eliminado=0; $faltas=0; $rehuses=0; $tocados=0; }
                // en este tipo de pruebas, el tiempo puede ser cero, pues solo se le apunta al ultimo del equipo
                if ( ($tiempo==0) && ($eliminado==0)) { $nopresentado=0; }
            } else { // pruebas "normales" y mangas ko
                if ($rehuses>=3) { $tiempo=0; $tintermedio=0; $eliminado=1; $nopresentado=0;}
                if ($tiempo>0) {$nopresentado=0;}
                if ($eliminado==1) { $tiempo=0; $tintermedio=0; $nopresentado=0; }
                if ($nopresentado==1) { $tiempo=0; $tintermedio=0; $eliminado=0; $faltas=0; $rehuses=0; $tocados=0; }
                if ( ($tiempo==0) && ($eliminado==0)) { $nopresentado=1; $faltas=0; $rehuses=0; $tocados=0; }
                if ( ($tiempo==0) && ($eliminado==1)) { $nopresentado=0; }
            }
		}
		// efectuamos el update, marcando "pendiente" como false
		$sql="UPDATE resultados 
			SET $entrada $comienzo 
				Faltas=$faltas , Rehuses=$rehuses , Tocados=$tocados , $games
				NoPresentado=$nopresentado , Eliminado=$eliminado , 
				Tiempo='$tiempo' , TIntermedio='$tintermedio' , $observaciones Pendiente=$pendiente
			WHERE (Perro=$idperro) AND (Manga=$this->IDManga)";
		$rs=$this->query($sql);
		if (!$rs) return $this->error($this->conn->error);

		// si el perro esta marcado como no presentado y pendiente es cero, (le han llamado 3 veces, vamos)
        // ponemos en las observaciones del resto de las mangas de la jornada
        // que este perro no se ha presentado, para que se muestre en los listados
        // ojo, que el campo pendiente se usa en las mangas KO para otra cosa, por lo que lo obviamos
        if ( ($pendiente==0) && !isMangaKO($this->dmanga->Tipo)) {
            $str=Mangas::getTipoManga($this->dmanga->Tipo,6,$this->getFederation()). " "._('Not Present');
            if ($nopresentado==1) { // pon no presentado en los comentarios
                $sql="UPDATE resultados
                  SET Observaciones='{$str}' 
                  WHERE (Perro={$idperro}) AND (Jornada=${idjornada}) AND (Pendiente=1)";
            } else { // borra el posible "no presentado" si existiera, pero sin borrar otras observaciones
                $sql="UPDATE resultados
                  SET Observaciones='' 
                  WHERE (Perro={$idperro}) AND (Jornada=${idjornada}) AND (Pendiente=1) AND (Observaciones='{$str}')";
            }
            $rs=$this->query($sql);
            if (!$rs) return $this->error($this->conn->error);
        }

        // also propagate results in every rounds on subordinate journeys in a recursive way
        $mobj= new Mangas("Resultados::update()",$this->IDJornada);
        $lst=$mobj->getSubordinates($idmanga);
        foreach ($lst['rows'] as $mng) {
            $jid=$mng['Jornada'];
            $mid=$mng['ID'];
            $subRes=Competitions::getResultadosInstance("update round:$mid on journey:$jid childOf:{$this->IDJornada}",$mid);
            $res=$subRes->real_update($idperro,$data);
            if (is_string($res)) return $this->error($res);
        }
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
        $heights=$this->mangaInfo->Competition->getHeights($this->mangaInfo);
		if ($mode!=12) $cat=sqlFilterCategoryByMode($mode, $heights,"");
		if ($cat===null) return $this->error("modo de recorrido desconocido:$mode");
		// comprobamos si hay perros pendientes de salir
		$res= $this->__select(
			/* SELECT */	"*",
			/* FROM */		"resultados",
			/* WHERE */		"$where $cat",
			/* ORDER BY */	"Nombre ASC",
			/* LIMIT */		""
		);
        $this->myLogger->leave();
        return $res;
	}

	/**
	 * Retrieve best intermediate and final times on this round
	 * @param $mode 0:L 1:M 2:S 3:MS 4:LMS 5:T 6:LM 7:ST 8:LMST 9:X 10:XL 11:MST 12:XLMST
	 * @return {array} requested data or error
	 */
	function bestTimes($mode=12) {
		$this->myLogger->enter();

		// FASE 0: en funcion del tipo de recorrido y modo pedido
		// ajustamos el criterio de busqueda de la tabla de resultados
		$where="(Manga={$this->IDManga}) AND (Pendiente=0) ";
		$cat="";
        $heights=$this->mangaInfo->Competition->getHeights($this->mangaInfo);
		if ($mode!=12) $cat=sqlFilterCategoryByMode($mode, $heights,"");
		if ($cat===null) return $this->error("modo de recorrido desconocido:$mode");
		//  evaluamos mejores tiempos intermedios y totales
		$best=$this->__select(
			/* SELECT */ "min(TIntermedio) AS BestIntermedio, min(Tiempo) AS BestFinal",
			/* FROM */  "resultados",
			/* WHERE */ "(Tiempo>0) AND $where $cat",
			/* ORDER */ "",
			/* LIMIT */ ""
		);
		if (!is_array($best))
			return $this->error($this->conn->error);
		return $best;
	}

	/**
	 * Compone un array de tiempo/penalizaciones anyadiendo el perro que le indicamos
     * Se utiliza para obtener el puesto de un perro cuando todavia NO ha sido insertado en la base de datos
     * Si perro es null entonces no se anyade nada
	 *@param {integer} $mode 0:L 1:M 2:S 3:MS 4:LMS 5:T 6:L+M 7:S+T 8:L+M+S+T 9:X 10:X+L 11:M+S+T 12:X+L+M+S+T
	 *@param {array} { perro,faltas,tocados,rehuses,eliminado,nopresentado,tiempo }
	 *@return {array} dog data with penal/time ordered
	 */
	function getPenalizaciones($mode,$perro=null) {
		$this->myLogger->enter();
		$idmanga=$this->IDManga;

        // FASE 0: en funcion del tipo de recorrido y modo pedido
        $idperro=0;
        $where="(Manga=$idmanga) AND (Pendiente=0) ";
        if ($perro!==null) {
            $idperro=intval($perro['Perro']);
            // ajustamos el criterio de busqueda de la tabla de resultados
            $where="(Manga=$idmanga) AND (Pendiente=0) AND (Perro!=$idperro)";
        }
		$cat="";
        $heights=$this->mangaInfo->Competition->getHeights($this->mangaInfo);
        if ($mode!=12) $cat=sqlFilterCategoryByMode($mode, $heights,""); // 12:XLMST includes 4:LMS and 8:LMST
        if ($cat===null)  return $this->error("modo de recorrido desconocido:$mode");
		// FASE 1: recogemos resultados ordenados por precorrido y tiempo
		$res=$this->__select(
			"Perro,	GREATEST(200*NoPresentado,100*Eliminado,5*(Tocados+Faltas+Rehuses)) AS PRecorrido, Tiempo, 0 AS PTiempo, 0 AS Penalizacion",
			"resultados",
			"$where $cat",
			" PRecorrido ASC, Tiempo ASC",
			"");
		if (!is_array($res)){
			$this->myLogger->leave();
			return $this->error($this->conn->error);
		}
		$table=&$res['rows']; // reference copy to economize memory and time
		$size=$res['total'];

		// FASE 2: si es necesario inserFtamos datos de nuestro perro.
		// Dado que el array anterior ya esta ordenado,el metodo mas rapido es el de insercion directa
        if ($idperro!=0) {
            $myPerro=array(
                'Perro' => $idperro,
                'Tiempo' => $perro['Tiempo'],
                'PRecorrido' => max(5*$perro['Faltas'] + 5*$perro['Rehuses'] + 5*$perro['Tocados'] , 100*$perro['Eliminado'] , 200*$perro['NoPresentado']),
                'PTiempo' => 0.0,
                'Penalizacion' => 0.0,
            );
			// on empty table directly insert perro
			if ($size==0) {
				array_push($table,$myPerro);
				$size++;
			} else {
				for ($n=0;$n<$size;$n++) {
					if ($table[$n]['PRecorrido']<$myPerro['PRecorrido']) continue;
					if ($table[$n]['PRecorrido']==$myPerro['PRecorrido']) {
						if ($table[$n]['Tiempo']<$myPerro['Tiempo']) continue;
					}
					// arriving here means need to insert $myPerro at index $n
					array_splice( $table, $n, 0, array($myPerro) ); // notice the "array(myPerro)" closure to preserva myPerro as a single element
					$size++;
					break;
				}
				if ($n>=$size) { // perro at last position. insert. Notice that Elim/NP should not arrive here as method not invoked
					array_push($table,$myPerro);
					$size++;
				}
			}
        }

		// FASE 3: evaluamos TRS Y TRM
		$tdata=$this->evalTRS($mode,$table); // array( 'dist' 'obst' 'trs' 'trm', 'vel')

		// FASE 4: añadimos ptiempo, penalizacion total
        $comp=$this->getCompetitionObject();
		for ($idx=0;$idx<$size;$idx++ ){
		    $comp->evalPartialPenalization($table[$idx],$tdata);
		}
		// FASE 4: re-ordenamos los datos en base a la puntuacion y calculamos campo "Puesto"
		usort($table, function($a, $b) {
			if ( $a['Penalizacion'] == $b['Penalizacion'] )	{
			    // para el caso de los eliminados, ponemos primero a los que se pasan de TRM
                // el programa asigna PTiempo=0 a los eliminados "normales" y PTiempo=X a
                // y PTiempo=XX a los
                if ($a['Penalizacion']==100.0) {
                    $pta=($a['PTiempo']==0)?150:$a['PTiempo'];
                    $ptb=($b['PTiempo']==0)?150:$b['PTiempo'];
                    return ($pta > $ptb)? 1:-1;
                }
                // para los demas casos comparamos tiempo
			    return ($a['Tiempo'] > $b['Tiempo'])? 1:-1;
            }
			return ( $a['Penalizacion'] > $b['Penalizacion'])?1:-1;
		});

		// FASE 5 finalmente componemos datos del array a retornar
		$result=array(
			'rows' => $table,
			'total' => count($table),
			'manga' => $this->getDatosManga(),
			'trs' => $tdata
		);
		$this->myLogger->leave();
		return $result;
	}

    /**
     * Obtiene el puesto de un perro cuando todavia NO ha sido insertado en la base de datos
     *@param {integer} $mode 0:L 1:M 2:S 3:MS 4:LMS 5:T 6:L+M 7:S+T 8:L+M+S+T 9:X 10:X+L 11:M+S+T 12:X+L+M+S+T
     *@param {array} { perro,faltas,tocados,rehuses,eliminado,nopresentado,tiempo }
     *@return {array} dog data with penal/time ordered
     */
	function getPuesto($mode,$perro) {
		$res=$this->getPenalizaciones($mode,$perro);
		if (!is_array($res)) return $res;
		$table=$res['rows'];
		$size=$res['total'];
		$idperro=$perro['Perro'];
		// FASE 5: buscamos el puesto en el que finalmente ha quedado $myPerro y lo retornamos
		for ($idx=0;$idx<$size;$idx++ ){
			if ($table[$idx]['Perro']!=$idperro) continue;
			return array( 
				'success'		=> true,
				'puesto'		=>(1+$idx),
				'penalizacion'	=>$table[$idx]['Penalizacion'],
				'mejortiempo' 	=>$table[0]['Tiempo']
			);
		}
		//arriving here means error: perro not found
		return $this->error("Perro:$idperro not found in resultados::getPuesto()");
	}

	/**
	 * Presenta una tabla ordenada segun los resultados de la manga
	 *@param {integer} $mode 0:L 1:M 2:S 3:MS 4:LMS 5:T 6:L+M 7:S+T 8:L+M+S+T 9:X 10:X+L 11:M+S+T 12:X+L+M+S+T
	 *@return {array} requested data or error
	 */
	function getResultadosIndividual($mode) {
		$this->myLogger->enter();
		$idmanga=$this->IDManga;
		
		// FASE 0: en funcion del tipo de recorrido y modo pedido
		// ajustamos el criterio de busqueda de la tabla de resultados
		$where="(Manga=$idmanga) AND (Pendiente=0) AND (perroguiaclub.ID=resultados.Perro) ";
		$cat="";
		// mode 12 is XLMST
        $heights=$this->mangaInfo->Competition->getHeights($this->mangaInfo);
		if ($mode<12) $cat=sqlFilterCategoryByMode($mode,$heights,"resultados."); // notice the ending dot '.'
        if ($cat===null) return $this->error("modo de recorrido desconocido:$mode");
		// FASE 1: recogemos resultados ordenados por precorrido y tiempo
		$res=$this->__select(
				"resultados.Dorsal,resultados.Perro,resultados.Nombre,NombreLargo,resultados.Raza,Equipo,resultados.Licencia,resultados.Categoria,resultados.Grado,
				    resultados.NombreGuia,resultados.NombreClub, perroguiaclub.Pais,perroguiaclub.LOE_RRC,perroguiaclub.CatGuia,
				    Faltas,Tocados,Rehuses,Tiempo,Eliminado,NoPresentado,resultados.Celo, resultados.Games,
					GREATEST(200*NoPresentado,100*Eliminado,5*(Tocados+Faltas+Rehuses)) AS PRecorrido,
					0 AS PTiempo, 0 AS Penalizacion, '' AS Calificacion, 0 AS Velocidad, resultados.Observaciones",
				"resultados,perroguiaclub",
				"$where $cat",
				" PRecorrido ASC, Tiempo ASC", 
				"");
		if (!is_array($res)){
			$this->myLogger->leave();
			return $this->error($this->conn->error);
		}

		$table=$res['rows'];
		// FASE 2: evaluamos TRS Y TRM
		$tdata=$this->evalTRS($mode,$table); // array( 'dist' 'obst' 'trs' 'trm', 'vel')
		$res['trs']=$tdata; // store trs data into result

		// FASE 3: añadimos ptiempo, puntuacion, clasificacion y logo
        $clubes=new Clubes("Resultados::getResultadosIndividual",$this->getDatosPrueba()->RSCE);
		$size=count($table);
		$comp=$this->getCompetitionObject();
		for ($idx=0;$idx<$size;$idx++ ){
            $table[$idx]['Puntos'] = 0; // to be re-evaluated later
            $table[$idx]['Estrellas'] = 0; // to be re-evaluated later
            $table[$idx]['Extras'] = 0; // to be re-evaluated later
            // evaluate penalization
			$comp->evalPartialPenalization($table[$idx],$tdata);
			// evaluamos velocidad 
			if ($table[$idx]['Tiempo']==0)	$table[$idx]['Velocidad'] = 0;
			else 	$table[$idx]['Velocidad'] =  $tdata['dist'] / $table[$idx]['Tiempo'];

            // anyadimos nombre del equipo
            $dequipos=$this->getDatosEquipos();
            $eqinfo=$dequipos[$table[$idx]['Equipo']];
            $table[$idx]['NombreEquipo']=$eqinfo['Nombre'];
            // anyadimos logotipo del club
            // los cambios en los nombres del perro, guia y club no se propagan a la tabla de resultados
            // por lo que la primera llamada puede fallar. usamos la segunda llamada de backup
            $logo=$clubes->getLogoName('NombreClub',$table[$idx]['NombreClub']);
            if ($logo=="") $logo=$clubes->getLogoName('Perros',$table[$idx]['Perro']);
            $table[$idx]['LogoClub']=$logo;
		}
		// FASE 4: re-ordenamos los datos en base a la puntuacion y calculamos campo "Puesto"
		usort($table, function($a, $b) {
			if ( $a['Penalizacion'] == $b['Penalizacion'] )	{
                // para el caso de los eliminados, ponemos primero a los que se pasan de TRM
                // el programa asigna PTiempo=0 a los eliminados "normales" y PTiempo=X a
                // y PTiempo=XX a los que se pasan de TRM. Nosotros le damos la vuelta asignando
                // ptiempo=150 a los eliminados que no se pasan de trm (ptiempo=0)
                if ($a['Penalizacion']==100.0) {
                    $pta=($a['PTiempo']==0)?150:$a['PTiempo'];
                    $ptb=($b['PTiempo']==0)?150:$b['PTiempo'];
                    return ($pta > $ptb)? 1:-1;
                }
                // para los demas casos comparamos tiempo
                return ($a['Tiempo'] > $b['Tiempo'])? 1:-1;

            }
			return ( $a['Penalizacion'] > $b['Penalizacion'])?1:-1;
		});
		
		// format output data and take care con duplicated penalizacion and time
        // calculamos campo "Puesto", "Calificacion" y Puntos
        $puestocat=array( 'C'=>1, 'X'=>1, 'L' => 1, 'M'=>1, 'S'=>1, 'T'=>1); // ultimo puesto por cada categoria
        $lastcat=array(   'C'=>0, 'X'=>0, 'L' => 0, 'M'=>0, 'S'=>0, 'T'=>0);  // ultima puntuacion por cada categoria
        $countcat=array(  'C'=>0, 'X'=>0, 'L' => 0, 'M'=>0, 'S'=>0, 'T'=>0); // perros contabilizados de cada categoria

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

            // finalmente llamamos al modulo de la competicion para evaluar la calificacion
			$comp->evalPartialCalification($this->getDatosManga(),$table[$idx],$puestocat);
		}

        // componemos datos del array a retornar
        $res['rows']=$table;
        $res['manga']=$this->getDatosManga();
        $res['trs']=$tdata;
        $this->myLogger->leave();
		return $res;
	}

    function getResultadosIndividualyEquipos($mode) {
        // obtenemos resultados individuales
        $resultados=$this->getResultadosIndividual($mode);
        $resultados['individual']=$resultados['rows'];
        $resultados['equipos']=$this->getResultadosEquipos($resultados);
        return $resultados;
    }

    /**
     * Gestion de resultados en Equipos3/Equipos4
     * Agrupa los resultados por equipos y genera una lista de equipos ordenados por resultados
     * @param {array} results obtenidos de getResultadosIndividual($mode)
     * @return {array} datos de equipos de la manga ordenados por resultados de equipo
     */
    function getResultadosEquipos($results) {
        $resultados=$results['rows'];
        // evaluamos mindogs
        $mindogs=Jornadas::getTeamDogs($this->getDatosJornada())[0]; // get mindogs
        $maxdogs=Jornadas::getTeamDogs($this->getDatosJornada())[1]; // get maxdogs

        // Datos de equipos de la jornada. obtenemos prueba y jornada del primer elemento del array
        $m=new Equipos("getResultadosEquipos",$this->IDPrueba,$this->IDJornada);
        $teams=$m->getTeamsByJornada();

        // reindexamos por ID y anyadimos un campos extra Tiempo, penalizacion y el array de resultados del equipo
        $equipos=array();
        foreach ($teams as &$equipo) {
            $equipo['Resultados']=array();
            $equipo['Tiempo']=0.0;
            $equipo['Penalizacion']=0.0;
            $equipo['Eliminados']=0;
            $equipos[$equipo['ID']]=$equipo;
        }
        // now fill team members array.
        // notice that $resultados is already sorted by results
        foreach($resultados as &$result) {
            $teamid=$result['Equipo'];
            $equipo=&$equipos[$teamid];
            array_push($equipo['Resultados'],$result);
            // suma el tiempo y penalizaciones de los tres/cuatro primeros
            $numdogs=count($equipo['Resultados']);
            if ($numdogs<=$mindogs) {
                // apunta resultado
                $equipo['Tiempo']+=floatval($result['Tiempo']);
                $equipo['Penalizacion']+=floatval($result['Penalizacion']);
                if ($result['Penalizacion']>=100) $equipo['Eliminados']++;
            }
            if($numdogs>$maxdogs) {
                if ($result['Penalizacion']<200) {
                    // mas resultados de lo permitido: equipo eliminado
                    $this->myLogger->info("Team {$equipo['Nombre']} excess Dogs:{$numdogs}. Disqualified");
                    $equipo['Tiempo']=0.0;
                    $equipo['Penalizacion']=400*$mindogs; // todos no presentados, por listos
                    $equipo['Eliminados']=$mindogs;
                }
            }
        }

        // rastrea los equipos con menos de $mindogs participantes y marca los que faltan
        // no presentados
        $teams=array();
        foreach($equipos as &$equipo) {
            $numdogs=count($equipo['Resultados']);
            $this->myLogger->trace("Checking NumDogs {$equipo['Nombre']} : {$numdogs}");
            switch($numdogs){
                case 0: // ignore team
					break;
                case 1: $equipo['Penalizacion']+=400.0; // required team member undeclared
                // no break
                case 2: if ($mindogs==3) $equipo['Penalizacion']+=400.0; // required team member undeclared
                // no break;
                case 3: if ($mindogs==4) $equipo['Penalizacion']+=400.0; // required team member undeclared
                // no break;
                default:
                    if($numdogs>$maxdogs) {
                        $this->myLogger->warn("Equipo {$equipo['ID']} : '{$equipo['Nombre']}' con exceso de participantes:".count($equipo['Resultados']));
                    }
                    $t4m=intval($this->myConfig->getEnv('team4_mode'));
                    $this->myLogger->trace("team4 mode is: $t4m");
                    // in team4 check what to do in Team4mode when a team member is eliminated
                    if ( ($mindogs==$maxdogs) && ($equipo['Eliminados']>0) ) {
                        // 0: 100 penalty points. Team time goes on. Nothing to do

                        // 1: 100 penalty points. Team time set to MCT ( European open mode )
                        if ($t4m===1){
                            $equipo['Tiempo']=$results['trs']['trm'];
                        }
                        // 2: entire team is eliminated
                        if ($t4m===2){
                            $equipo['Penalizacion']=max(100*$mindogs,$equipo['Penalizacion']);
                        }
                    }
                    array_push($teams,$equipo); // add team to result to remove unused/empty teams
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

    /**
     * Instead of using direct constructor use factory to get proper instance of ordensalida
     * By this way we can override main function to rewrite clone/random/reverse and so methods
     * to be used in special rounds
     *
     * @param {string} $file Filename to be used in debug functions
     * @param {integer} $manga Manga ID
     * @return {class} Resultados instance
     */
    public static function getInstance($file="Resultados",$manga) {
        // just call propper competition module
        return Competitions::getResultadosInstance($file,$manga);
    }
}
?>