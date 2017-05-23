<?php
/*
VideoWall.php

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

require_once(__DIR__ . "/../logging.php");
require_once(__DIR__ . "/../auth/Config.php");
require_once(__DIR__ . "/../database/classes/DBObject.php");
require_once(__DIR__ . "/../database/classes/Clubes.php");
require_once(__DIR__ . "/../database/classes/Tandas.php");
require_once(__DIR__ . "/../database/classes/Mangas.php");
require_once(__DIR__ . "/../database/classes/Sesiones.php");
require_once(__DIR__ . "/../database/classes/Jornadas.php");
require_once(__DIR__ . "/../database/classes/Inscripciones.php");

class VideoWall {
	protected $myLogger;
	protected $myDBObject;
	protected $sessionid;
	protected $session;
	protected $prueba;
	protected $jornada;
	protected $manga;
	protected $tanda;
	protected $config;
	protected $mangaid;
	protected $tandatype;
	protected $mode;
	protected $club;
    protected $ronda; // pre-agility, grado1,

	function __construct($sessionid,$pruebaid=0,$jornadaid=0,$mangaid=0,$tandatype=0,$mode=0) {
		$this->config=Config::getInstance();
		$this->myLogger=new Logger("VideoWall.php",$this->config->getEnv("debug_level"));
		$this->myDBObject=new DBObject("Videowall");
        $this->manga=null;
        $this->tanda=null;
        $this->ronda=null;
        $this->mangaid=$mangaid;
        $this->tandatype=$tandatype;
        $tandaid=0;
		if ($sessionid!=0) {
            // obtenemos los datos desde la sesion abierta en el tablet
			$this->session=$this->myDBObject->__getArray("Sesiones",$sessionid);
			$this->sessionid=$sessionid;
            $pruebaid=$this->session['Prueba'];
            $jornadaid=$this->session['Jornada'];
            $mangaid=$this->session['Manga'];
            $tandaid=$this->session['Tanda'];
			$this->mode=-1;
		} else {
            // obtenemos los datos desde las variables recibidas por http
			$this->session=null;
			$this->sessionid=0;
			$this->mode=$mode;
		}
        $this->prueba=$this->myDBObject->__getArray("Pruebas",$pruebaid);
        $this->prueba['LogoClub']=$this->myDBObject->__getArray("Clubes",intval($this->prueba['Club']))['Logo'];
        $this->jornada=$this->myDBObject->__getArray("Jornadas",$jornadaid);
        if ($mangaid!=0) {
            $this->manga=$this->myDBObject->__getArray("Mangas",$mangaid);
            $this->manga['Manga']=$mangaid; // manga['ID'] contains extra info and should not be used
            $this->mangaid=$mangaid;
        }
        if ($tandaid!=0) {
            $this->tanda=$this->myDBObject->__getArray("Tandas",$tandaid);
            $this->tandatype=$this->tanda['Tipo'];
        }
        // retrieve rounds for this journey
        if ($mangaid!=0){
            $rondas=Jornadas::enumerateRondasByJornada($jornadaid)['rows'];
            // and search current round
            foreach($rondas as $ronda) {
                $mngs=array ('Manga1','Manga2','Manga3','Manga4','Manga5','Manga6','Manga7','Manga8');
                $cat=Tandas::$tipo_tanda[$this->tandatype]['Categoria'];
                foreach ($mngs as $mng) {
                    if($this->mangaid!=$ronda[$mng]) continue;
                    foreach( str_split($cat) as $c) {
                        // TODO: revise Jornadas::__composeArray, to retrieve right short value for categoria (or create additional field)
                        if (strpos($ronda['Categoria'],$c)===false) continue;
                        $this->myLogger->trace("Found ronda: ".json_encode($ronda));
                        $this->ronda=$ronda;
                        break;
                    }
                    if ($this->ronda!=null) break;
                }
                if ($this->ronda!=null) break;
            }
        }
        $this->club= $this->myDBObject->__getArray("Clubes",$this->prueba['Club']);
        if ($this->manga!=null) { // elimina parentesis del nombre
            $fed=Federations::getFederation( intval($this->prueba['RSCE']) );
            $str=_(Mangas::getTipoManga($this->manga['Tipo'],1,$fed));
            $a=strpos($str,"(");
            if($a!==FALSE) $str=substr($str,0,$a);
            $this->manga['Nombre']=$str;
        }
		// $this->myLogger->info("sesion:$sessionid prueba:{$this->prueba['ID']} jornada:{$this->jornada['ID']} manga:{$this->mangaid} tanda:{$this->tandatype} mode:$mode");
	}

    function isTeam() {
        if (intval($this->jornada['Equipos3'])!=0) return true;
        if (intval($this->jornada['Equipos4'])!=0) return true;
        return false;
    }

    function hasGrades() {
        if (intval($this->jornada['Equipos3'])!=0) return false;
        if (intval($this->jornada['Equipos4'])!=0) return false;
        if (intval($this->jornada['Open'])!=0) return false;
        if (intval($this->jornada['KO'])!=0) return false;
        return true;
    }

    function videowall_llamada($pendientes) {
        // array ("Orden","Logo","Dorsal","Licencia","Nombre","Raza","Categoria","Grado","NombreGuia","NombreClub","Celo");
        $lastTanda="";
        $lastTeam=0;
        $otmgr=new Tandas("Llamada a pista",$this->prueba['ID'],$this->jornada['ID']);
        $lista = $otmgr->getData($this->sessionid,$this->tanda['ID'],$pendientes)['rows']; // obtiene los $pendientes primeros perros
        $orden=0;
        $data=array();
        foreach ($lista as $participante) {
            if ($lastTanda!==$participante['Tanda']){
                $lastTanda=$participante['Tanda'];
                $lastTeam=0; // make sure team's name is shown
                // Orden=-1 means Tanda info
                $item=array("Orden" => -1,"Logo" => "empty.png","Dorsal"=>"&nbsp","Licencia" => "&nbsp;","Nombre" => "&nbsp;",
                    "Raza","Categoria" => "&nbsp","Grado" => "---","NombreGuia" => $lastTanda,"NombreClub" => "---","Celo" => 0 );
                array_push($data,$item);
            }
            if ( $this->isTeam() && ($lastTeam!==$participante['Equipo']) ){
                $lastTeam=$participante['Equipo'];
                $team=$this->myDBObject->__getObject("Equipos",$lastTeam);
                // orden 0 means new team
                $item=array("Orden" => 0,"Logo" => "empty.png","Dorsal"=>"&nbsp","Licencia" => "Equipo:","Nombre" => $team->Nombre,
                    "Raza","Categoria" => "&nbsp","Grado" => "&nbsp;","NombreGuia" => "&nbsp;","NombreClub" => "&nbsp;","Celo" => 0 );
                array_push($data,$item);
            }
            $orden++;
            $item=array(
                "Orden" => $orden,
                "Logo" => $participante['Logo'],
                "Dorsal" => $participante['Dorsal'],
                "Licencia" => $participante['Licencia'],
                "Nombre" => $participante['Nombre'],
                "Raza" => $participante['Raza'],
                "Categoria" => $participante['Categoria'],
                "Grado" => $participante['Grado'],
                "NombreGuia" => $participante['NombreGuia'],
                "NombreClub" => $participante['NombreClub'],
                "Celo" => $participante['Celo'],
                "Observaciones" => $participante['Observaciones'],);
            array_push($data,$item);
        }
        //
        $res=array('rows'=>$data,'total'=>count($data));
        echo json_encode($res);
        return 0;
    }

	function videowall_livestream() {
		/* recupera los datos de un perro y le añade informacion de celo */
		$celo = http_request("Celo","i",0);
		$id= http_request("Perro","i",0);
		$pmgr= new Dogs("VideoWall_LiveSTream",$this->prueba['RSCE']);
		$data=$pmgr->selectByID($id);
		$data["Celo"]=$celo;
		return $data;
	}

    function videowall_infodata() {
        $res= array(
            'Prueba' => $this->prueba,
            'Jornada' => $this->jornada,
            'Manga' => ($this->manga===null)? array() : $this->manga,
            'Tanda' => ($this->tanda===null)? array() : $this->tanda,
            'Club' => $this->club, // club organizador
            'Sesion' => $this->session,
            'Ronda' => $this->ronda
        );
        echo json_encode($res);
        return 0;
    }

    private function getEmptyData() {
        return array(
            'Prueba' => $this->prueba['ID'],
            'Jornada' => $this->jornada['ID'],
            'Manga' => $this->mangaid,
            'LogoClub' => 'empty.png',
            'Orden' => "",
            'Dorsal' => 0,
            'Perro' => 0,
            'Equipo' => 0,
            'NombreEquipo' => "",
            'Nombre' => "",
            'NombreLargo' => "",
            'Raza' => "",
            'Licencia' => "",
            'Categoria' => "",
            'Grado' => "",
            'Celo' => 0,
            'NombreGuia' => "",
            'NombreClub' => "",
            'Entrada' => '1970-01-01 00:00:00',
            'Comienzo' => '1970-01-01 00:00:00',
            'Faltas' => 0,
            'Tocados' => 0,
            'Rehuses' => 0,
            'Games' => 0,
            'Eliminado' => 0,
            'NoPresentado' => 0,
            'Tiempo' => 0.0,
            'TIntermedio' => 0.0,
            'Observaciones' => "",
            'Pendiente' => 1,
            'Puesto' => ""
        );
    }
    
    /**
     * Obtiene $after+$before+1 perros ordenados segun el orden de salida
     * @param {integer}$perro ID de perro tomado como referencia
     *  if ID==0 means at begin of queue
     *  if ID<0 means at end of queue
     * @param {integer} $before numero de perros a buscar que hayan salido antes del de referencia
     * @param {integer} $after numero de perros a introducir que tengan que salir despues del de referencia
     */
    function videowall_windowCall($perro,$before,$after) {
        $this->myLogger->enter();
        $nitems=$before+$after+1;
        // obtenemos listado ordenado de perros de la manga
        $osobj=new OrdenSalida("VideoWall-ng",$this->mangaid);
        $os=$osobj->getData(false); // omit inserting team info rows
        // obtenemos categoria y grado de la tanda
        $catstr=Tandas::$tipo_tanda[$this->tandatype]['Categoria']; // categoria
        $gradostr =Tandas::$tipo_tanda[$this->tandatype]['Grado']; // grado ("-" means any grade)
        // componemos un array de $before+1+$after perros
        $result=array();
        // reserve $before +1 empty slots before dog list
        for($n=0;$n<$before+1;$n++) {
            array_unshift($result,$this->getEmptyData());
        }
        $found=-1;
        // add dog list, setting up starting orden
        $orden=0;
        // if dog found, mark index
        foreach ($os['rows'] as &$item) {
            if ( strstr($catstr,$item['Categoria'])===false) continue; // category does not match, ignore
            if ( ($gradostr!=='-') && ($gradostr!==$item['Grado']) ) continue; // grade differs, ignore entry
            // same category and (if required) grade
            $orden++;
            $item['Orden']=$orden;
            array_unshift($result,$item);
            // if item matches requested dog, cut
            if ($item['Perro']==$perro) $found=count($result)-1;
            if ($found<=0) continue; // not yet found
            if ( (count($result)-$found) > $after ) break; // enought dogs; iteration no longer needed
        }
        // fill array with $after empty rows
        for($n=0;$n<$after;$n++) {
            array_unshift($result,$this->getEmptyData());
            // if dogID<0 means seek at end of list
            if($perro>=0) continue;
            if($n!=0) continue;
            $found=count($result)-1;
        }
        // if dog is not provided nor found, just assume default
        if ($found<0) $found=$before;
        // and return 4 arrays:
        $res=array(
            // "total" => count($res),
            // "rows" => $res,
            "before" => array_slice($result,-$found,$before),
            "current" => array_slice($result,-($found+1),1),
            "after" => array_slice($result,-($found+1+$after),$after),
            // extra round results for current dog (if any)
            "results" =>$this->myDBObject->__select("*","Resultados","(Perro=$perro) AND (Jornada={$this->jornada['ID']})","","")['rows']
        );
        echo json_encode($res);
        return 0;
    }

    private function getEmptyTeamData() {
        return array(
            'Prueba' => $this->prueba['ID'],
            'Jornada' => $this->jornada['ID'],
            'Manga' => $this->mangaid,
            'LogoTeam' => 'empty.png',
            'Orden' => "",
            'Equipo' => 0,
            'NombreEquipo' => "",
            'Categoria' => "",
            'Grado' => "",
            'Celo' => 0
        );
    }

    /**
     * Obtiene $after+$before+1 equipos ordenados segun el orden de salida
     * @param {integer} $perro ID de perro tomado como referencia
     *  if ID==0 means at begin of queue
     *  if ID<0 means at end of queue
     * @param {integer} $before numero de equipos que hayan salido antes que el equipo del perro de referencia
     * @param {integer} $after numero de equipos que tengan que salir despues del equipo del perro de referencia
     * @return array (
     *             before: Array de $before equipos que han saludo antes
     *             after: Array de $equipos que tienen que salir despues
     *             current: Equipo en pista
     *             results: Resultados de los componentes del equipo en pista que tienen la misma categoria
     *      )
     * Notese que un equipo puede estar troceado. En ese caso "current" solo muestra los perros del equipo
     * que salen en ese "trozo"
     *
     * Por otro lado, dado que en el orden de salida se discrimina por categorias, el campo results
     * puede tener menos datos que $mindogs, con lo que hay que rellenar a ceros.
     * Realmente esta funcion esta pensada para rellenar la pantalla simplificada(equipos) en la que se
     * asume que hay cuatro perros por equipo y que todos son de la misma categoria 
     */
    function videowall_teamWindowCall($perro,$before,$after) {
        $this->myLogger->enter();
        $nitems=$before+$after+1;
        // obtenemos listado ordenado de perros de la manga
        $osobj=new OrdenSalida("VideoWall-ng",$this->mangaid);
        $os=$osobj->getData(false); // omit inserting team info rows
        // obtenemos categoria y grado de la tanda
        $catstr=Tandas::$tipo_tanda[$this->tandatype]['Categoria']; // categoria
        $gradostr =Tandas::$tipo_tanda[$this->tandatype]['Grado']; // grado ("-" means any grade)
        // componemos un array de $before+1+$after perros
        $result=array();
        // reserve $before +1 empty slots before team list
        for($n=0;$n<$before+1;$n++) {
            array_unshift($result,$this->getEmptyTeamData());
        }
        $found=-1;
        // add dog list, setting up starting orden
        $orden=0;
        $lastTeam=0; // ultimo equipo analizado
        $foundTeam=array( // create a default array without dogs for "current" team
            $this->getEmptyData(),$this->getEmptyData(),$this->getEmptyData(),$this->getEmptyData()
        ); // equipo que contiene el perro actual
        $teams=array(); // lista de equipos
        $team=null; // equipo que esta siendo analizado
        // if dog found, mark index
        foreach ($os['rows'] as &$item) {
            // si la categoria y (if required) el grado difieren, ignora la entrada
            if ( strstr($catstr,$item['Categoria'])===false) continue; // category does not match, ignore
            if ( ($gradostr!=='-') && ($gradostr!==$item['Grado']) ) continue; // grade differs, ignore entry
            // si el equipo es distinto, anyade equipo a la lista, y reinicia teamList
            if ( $item['Equipo']!=$lastTeam) {
                $lastTeam=$item['Equipo'];
                if (!isset($teams[$lastTeam])) $teams[$lastTeam]=array(); // to prevent override in celo
                $orden++;
                // creamos los datos del nuevo equipo, y lo insertamos en el orden de salida
                // los perros del mismo equipo comparten cagegoria y grado  con lo que
                // usamos los datos del primer perro encontrado del equipo
                // por otro lado, para el logo, cogemos el logo del primer perro. TODO: revise
                $team=array(
                    'Prueba' => $this->prueba['ID'],
                    'Jornada' => $this->jornada['ID'],
                    'Manga' => $this->mangaid,
                    'LogoTeam' =>  $item['LogoClub'],
                    'Orden' => $orden,
                    'Equipo' => $item['Equipo'],
                    'ID' => $item['Equipo'], // duplicate
                    'NombreEquipo' => $item['NombreEquipo'],
                    'Categoria' => $item['Categoria'],
                    'Grado' => $item['Grado']
                );
                array_unshift($result,$team); // insertamos equipo en orden al principio de la lista
            }
            // anyade el perro al equipo actual
            array_push($teams[$lastTeam],$item);

            // si perro encontrado, lo marcamos
            if ($item['Perro']==$perro) {
                $found=count($result)-1;
                $foundTeam=&$teams[$lastTeam]; // use reference cause may be more dogs in team
            }
            // do not try to optimize loop: heat bitches stands at the end,
            // so need to parse entire starting
            // order to fill teams with heat bitches
        }
        // fill array with $after empty rows to make sure data is available at last team
        for($n=0;$n<$after;$n++) {
            array_unshift($result,$this->getEmptyTeamData());
            // if dogID<0 means seek at end of list
            if($perro>=0) continue;
            if($n!=0) continue;
            $found=count($result)-1;
        }
        // if team is not provided nor found matching dog, just assume default
        if ($found<0) $found=$before;
        // fill $foundteam to fit 4 entries
        for (;count($foundTeam)<4;) array_push($foundTeam,$this->getEmptyData());
        // and return 4 arrays:
        $res=array(
            // "total" => count($res),
            // "rows" => $res,
            "before" => array_slice($result,-$found,$before),
            "current" => array_slice($result,-($found+1),1),
            "after" => array_slice($result,-($found+1+$after),$after),
            //results for dog matching team
            "results" =>$foundTeam,
        );
        echo json_encode($res);
        return 0;
    }
} 
?>
