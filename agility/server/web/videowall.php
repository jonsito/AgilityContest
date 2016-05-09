<?php
/*
videowall.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
        $this->jornada=$this->myDBObject->__getArray("Jornadas",$jornadaid);
        if ($mangaid!=0) {
            $this->manga=$this->myDBObject->__getArray("Mangas",$mangaid);
            $this->mangaid=$mangaid;
        }
        if ($tandaid!=0) {
            $this->tanda=$this->myDBObject->__getArray("Tandas",$tandaid);
            $this->tandatype=$this->tanda['Tipo'];
        }
        // retrieve rounds for this journey
        if ($mangaid!=0){
            $rondas=Jornadas::enumerateRondasByJornada($jornadaid)['rows'];
            foreach($rondas as $ronda) {
                $cat=Tandas::$tipo_tanda[$this->tandatype]['Categoria'];
                $this->myLogger->trace("MangaID {$this->mangaid} Cat: $cat Ronda: ".json_encode($ronda));
                if ($ronda['Manga1']==$this->mangaid) {
                    foreach( str_split($cat) as $c) {
                        if (strpos($ronda['Categoria'],$c)===false) continue;
                        $this->ronda=$ronda;
                        break;
                    }
                }
                if ($ronda['Manga2']==$this->mangaid){
                    foreach( str_split($cat) as $c) {
                        if (strpos($ronda['Categoria'],$c)===false) continue;
                        $this->ronda=$ronda;
                        break;
                    }
                }
            }
        }
        $this->club= $this->myDBObject->__getArray("Clubes",$this->prueba['Club']);
        if ($this->manga!=null) { // elimina parentesis del nombre
            $str=Mangas::$tipo_manga[$this->manga['Tipo']][1];
            $a=strpos($str,"(");
            if($a!==FALSE) $str=substr($str,0,$a);
            $this->manga['Nombre']=$str;
        }

		$this->myLogger->info("sesion:$sessionid prueba:{$this->prueba['ID']} jornada:{$this->jornada['ID']} manga:{$this->mangaid} tanda:{$this->tandatype} mode:$mode");
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
		$pmgr= new Dogs("VideoWall_LiveSTream");
		$data=$pmgr->selectByID($id);
		$data["Celo"]=$celo;
		return $data;
	}

    function videowall_infodata() {
        $res= array(
            'Prueba' => $this->prueba,
            'Jornada' => $this->jornada,
            'Manga' => ($this->manga==null)? array() : $this->manga,
            'Tanda' => ($this->tanda==null)? array() : $this->tanda,
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
            'Pendiente' => 1
        );
    }
    /**
     * Obtiene $after+$before+1 perros ordenados segun el orden de salida
     * @param $perro ID de perro tomado como referencia
     *  if ID==0 means at begin of queue
     *  if ID<0 means at end of queue
     * @param $before numero de perros a buscar que hayan salido antes del de referencia
     * @param $after numero de perros a introducir que tengan que salir despues del de referencia
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
        // and return 3 arrays:
        $res=array(
            // "total" => count($res),
            // "rows" => $res,
            "before" => array_slice($result,-$found,$before),
            "current" => array_slice($result,-($found+1),1),
            "after" => array_slice($result,-($found+1+$after),$after)
        );
        echo json_encode($res);
        return 0;
    }
} 

$sesion = http_request("Session","i",0);
$operacion = http_request("Operation","s",null);
$pendientes = http_request("Pendientes","i",10);
// on session==0, use this elements as IDentifiers
$prueba = http_request("Prueba","i",0);
$jornada = http_request("Jornada","i",0);
$manga = http_request("Manga","i",0);
$tanda = http_request("Tanda","i",0); // used on access from videowall
$mode = http_request("Mode","i",0); // used on access from public
$perro = http_request("Perro","i",0); // used on access from public
$before = http_request("Before","i",3); // to compose starting order window
$after = http_request("After","i",12); //  to compose starting order window

$vw=new VideoWall($sesion,$prueba,$jornada,$manga,$tanda,$mode);
try {
    if($operacion==="infodata") return $vw->videowall_infodata();
	if($operacion==="livestream") return $vw->videowall_livestream();
    if($operacion==="llamada") return $vw->videowall_llamada($pendientes); // pendientes por salir
    if($operacion==="window") return $vw->videowall_windowCall(intval($perro),intval($before),intval($after)); // 15 por detras y 4 por delante
} catch (Exception $e) {
	echo "<p>Error:<br />".$e->getMessage()."</p>";
    return 0;
}
