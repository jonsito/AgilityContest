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
		$this->myLogger->info("sesion:$sessionid prueba:{$this->prueba['ID']} jornada:{$this->jornada['ID']} manga:{$this->mangaid} tanda:{$this->tandatype} mode:$mode");
	}

    function isTeam() {
        if (intval($this->jornada['Equipos3'])==1) return true;
        if (intval($this->jornada['Equipos4'])==1) return true;
        return false;
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
            'Club' => $this->club // club organizador
        );
        echo json_encode($res);
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

$vw=new VideoWall($sesion,$prueba,$jornada,$manga,$tanda,$mode);
try {
    if($operacion==="infodata") return $vw->videowall_infodata();
	if($operacion==="livestream") return $vw->videowall_livestream();
	if($operacion==="llamada") return $vw->videowall_llamada($pendientes);
} catch (Exception $e) {
	echo "<p>Error:<br />".$e->getMessage()."</p>";
    return 0;
}
