<?php
/*
videowall.php

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

require_once(__DIR__ . "/../logging.php");
require_once(__DIR__ . "/../auth/Config.php");
require_once(__DIR__ . "/../database/classes/DBObject.php");
require_once(__DIR__ . "/../database/classes/Clubes.php");
require_once(__DIR__ . "/../database/classes/Tandas.php");
require_once(__DIR__ . "/../database/classes/Mangas.php");
require_once(__DIR__ . "/../database/classes/Sesiones.php");
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

	function __construct($sessionid,$pruebaid,$jornadaid,$mangaid,$tandatype,$mode) {
		$this->config=Config::getInstance();
		$this->myLogger=new Logger("VideoWall.php",$this->config->getEnv("debug_level"));
		$this->myDBObject=new DBObject("Videowall");
		if ($sessionid!=0) {
            // obtenemos los datos desde la sesion abierta en el tablet
			$this->session=$this->myDBObject->__getArray("Sesiones",$sessionid);
			$this->sessionid=$sessionid;
			$this->prueba=$this->myDBObject->__getArray("Pruebas",$this->session['Prueba']);
			$this->jornada=$this->myDBObject->__getArray("Jornadas",$this->session['Jornada']);
            $this->tanda=$this->myDBObject->__getArray("Tandas",$this->session['Tanda']);
            $this->tandatype=$this->tanda['Tipo'];
            if ($this->session['Manga']==0) {
                // take care on User-defined Tandas (Manga=0)
                $this->manga=null;
                $this->mangaid=0;
            } else {
                // normal Tandas
                $this->manga=$this->myDBObject->__getArray("Mangas",$this->session['Manga']);
                $this->mangaid=$this->manga['ID'];
            }
			$this->mode=-1;
		} else {
            // obtenemos los datos desde las variables recibidas por http
			$this->session=null;
			$this->sessionid=0;
			$this->prueba=$this->myDBObject->__getArray("Pruebas",$pruebaid);
			$this->jornada=$this->myDBObject->__getArray("Jornadas",$jornadaid);
			$this->manga=$this->myDBObject->__getArray("Mangas",$mangaid);
			$this->mangaid=$this->manga['ID'];
			$this->tanda=null;
			$this->tandatype=$tandatype;
			$this->mode=$mode;	
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
        if (intval($this->jornada['Equipos3'])==1) return true;
        if (intval($this->jornada['Equipos4'])==1) return true;
        return false;
    }

    function hasGrades() {
        if (intval($this->jornada['Equipos3'])==1) return false;
        if (intval($this->jornada['Equipos4'])==1) return false;
        if (intval($this->jornada['Open'])==1) return false;
        if (intval($this->jornada['KO'])==1) return false;
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
            'Sesion' => $this->session
        );
        echo json_encode($res);
        return 0;
    }

    private function getEmptyData($orden,$cat,$grad) {
        return array(
            'Prueba' => $this->prueba['ID'],
            'Jornada' => $this->jornada['ID'],
            'Manga' => $this->mangaid,
            'Orden' => $orden,
            'Dorsal' => 0,
            'Perro' => 0,
            'Equipo' => 0,
            'NombreEquipo' => "",
            'Nombre' => "",
            'NombreLargo' => "",
            'Raza' => "",
            'Licencia' => "",
            'Categoria' => $cat,
            'Grado' => $grad,
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
        $order=0;
        $already=false;
        // fill data for $after slice
        for($n=0;$n<$before;$n++) array_push($result,$this->getEmptyData($order,"-","-")); // fill "after" items with empty data
        // $perro=0 means no dog being called yet. So fill $current slice properly
        if ($perro==0) {
            $order++;
            array_push($result,$this->getEmptyData($order,"-","-"));
            $already=true;
        }
        // now iterate dog list extracting requested dogs and filling array
        foreach ($os['rows'] as $item) {
            if ( strstr($catstr,$item['Categoria'])===false) continue; // category does not match, ignore
            if ( ($gradostr!=='-') && ($gradostr!==$item['Grado']) ) continue; // grade differs, ignore entry
            // same category and (if required) grade
            $order++;
            $item['Orden']=$order;
            array_push($result,$item);
            // if item matches requested dog, cut
            if ($item['Perro']==$perro) {
                $already=true;
                $result=array_slice($result,1+$after);
            }
            if( $already && ( count($result)>= $nitems) ) break;
        }
        // at the end... fill data till requested size
        while (count($result)<$nitems) {
            $order++;
            array_push($result,$this->getEmptyData($order,"-","-"));
        }
        // reverse array so first entered dogs become last
        $res=array_reverse($result);
        // and return 3 arrays:
        $result=array(
            // "total" => count($res),
            // "rows" => $res,
            "before" => array_slice($res,0,$before),
            "current" => array_slice($res,$before,1),
            "after" => array_slice($res,1+$before,$after)
        );
        echo json_encode($result);
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
$before = http_request("Before","i",4); // to compose starting order window
$after = http_request("After","i",15); //  to compose starting order window

$vw=new VideoWall($sesion,$prueba,$jornada,$manga,$tanda,$mode);
try {
    if($operacion==="infodata") return $vw->videowall_infodata();
	if($operacion==="livestream") return $vw->videowall_livestream();
    if($operacion==="llamada") return $vw->videowall_llamada($pendientes); // pendientes por salir
    if($operacion==="window") return $vw->videowall_windowCall($perro,$before,$after); // 15 por detras y 4 por delante
} catch (Exception $e) {
	echo "<p>Error:<br />".$e->getMessage()."</p>";
    return 0;
}
