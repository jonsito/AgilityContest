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
require_once(__DIR__ . "/../database/classes/Inscripciones.php");

class PublicWeb
{
    protected $myLogger;
    protected $myDBObject;
    protected $prueba;
    protected $jornada;
    protected $manga;
    protected $tanda;
    protected $config;
    protected $mangaid;
    protected $mode;
    protected $club;

    function __construct($pruebaid, $jornadaid, $mangaid, $mode)
    {
        $this->config = Config::getInstance();
        $this->myLogger = new Logger("PublicWeb.php", $this->config->getEnv("debug_level"));
        $this->myDBObject = new Clubes("PublicWeb"); // also is a dbobject. used to retrieve logos
        // obtenemos los datos desde las variables recibidas por http
        $this->session = null;
        $this->sessionid = 0;
        $this->prueba = $this->myDBObject->__getArray("Pruebas", $pruebaid);
        $this->jornada = $this->myDBObject->__getArray("Jornadas", $jornadaid);
        $this->manga = null;
        $this->mangaid = $mangaid;
        if ($mangaid != 0) $this->manga = $this->myDBObject->__getArray("Mangas", $mangaid);
        $this->mode = $mode;
        $this->club = $this->myDBObject->__getArray("Clubes", $this->prueba['Club']);
        $this->myLogger->info("prueba:{$this->prueba['ID']} jornada:{$this->jornada['ID']} manga:{$this->mangaid} mode:$mode");
    }

    function publicweb_infodata()
    {
        $res = array(
            'Prueba' => $this->prueba,
            'Jornada' => $this->jornada,
            'Manga' => ($this->manga == null) ? array() : $this->manga,
            'Club' => $this->club // club organizador
        );
        echo json_encode($res);
    }
}

$operacion = http_request("Operation","s",null);
$pendientes = http_request("Pendientes","i",10);
// on session==0, use this elements as IDentifiers
$prueba = http_request("Prueba","i",0);
$jornada = http_request("Jornada","i",0);
$manga = http_request("Manga","i",0);
$mode = http_request("Mode","i",0); // used on access from public

$vw=new PublicWeb($prueba,$jornada,$manga,$mode);
try {
    if($operacion==="infodata") $vw->publicweb_infodata();
    else throw new Exception("public.php: operacion invalida:'$operacion'");
} catch (Exception $e) {
	echo "<p>Error:<br />".$e->getMessage()."</p>";
}
return 0;
