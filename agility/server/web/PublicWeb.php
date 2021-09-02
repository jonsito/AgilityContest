<?php
/*
PublicWeb.php

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

require_once(__DIR__ . "/../logging.php");
require_once(__DIR__ . "/../auth/Config.php");
require_once(__DIR__ . "/../database/classes/DBObject.php");
require_once(__DIR__ . "/../database/classes/Clubes.php");
require_once(__DIR__ . "/../database/classes/Tandas.php");
require_once(__DIR__ . "/../database/classes/Mangas.php");
require_once(__DIR__ . "/../database/classes/Inscripciones.php");

class PublicWeb
{
    public $myLogger; // public to allow debugging
    protected $myDBObject;
    protected $prueba;
    protected $jornada;
    protected $manga;
    protected $tanda;
    protected $config;
    protected $mangaid;
    protected $mode;
    protected $club;

    function __construct($pruebaid, $jornadaid=0, $mangaid=0, $mode=0) {
        $this->config = Config::getInstance();
        $this->myLogger = new Logger("PublicWeb.php", $this->config->getEnv("debug_level"));
        $this->myDBObject = new DBObject("PublicWeb"); // also is a dbobject. used to retrieve logos
        // obtenemos los datos desde las variables recibidas por http
        $this->session = null;
        $this->sessionid = 0;
        $this->prueba = $this->myDBObject->__getArray("pruebas", $pruebaid);
        $this->jornada = null;
        if ($jornadaid != 0 ) $this->jornada = $this->myDBObject->__getArray("jornadas", $jornadaid);
        $this->manga = null;
        $this->mangaid = $mangaid;
        if ($mangaid != 0) $this->manga = $this->myDBObject->__getArray("mangas", $mangaid);
        $this->mode = $mode;
        $this->club = $this->myDBObject->__getArray("clubes", $this->prueba['Club']);
        $this->myLogger->info("prueba:{$this->prueba['ID']} jornada:{$jornadaid} manga:{$mangaid} mode:$mode");
    }

    function publicweb_infodata() {
        $res = array(
            'Prueba' => $this->prueba,
            'Jornada' => $this->jornada,
            'Manga' => ($this->manga == null) ? array() : $this->manga,
            'Club' => $this->club // club organizador
        );
        return $res;
    }

    /** deploy a contest->journeys->series->rounds tree */
    function publicweb_deploy() {
        $result=array();
        // retrieve contest data
        $result['Prueba']=$this->prueba;
        // retrieve journeys for this contest
        $result['Jornadas']=$this->myDBObject->__select("*","jornadas","(Prueba={$this->prueba['ID']}) AND (Nombre != '-- Sin asignar --') ","","" )['rows'];
        foreach($result['Jornadas'] as &$jornada) {
            // retrieve rounds for each series
            $jornada['Mangas']=Jornadas::enumerateMangasByJornada($jornada['ID'])['rows'];
            // retrieve series for each journey
            $tnd=new Tandas("publicweb_deploy",$this->prueba['ID'],$jornada['ID']);
            $jornada['Tandas']=$tnd->getTandas(0)['rows']; // incluye user defined rounds ( to display timetable )
            // retrieve final results index for each series
            $jornada['Series']=Jornadas::enumerateRondasByJornada($jornada['ID'])['rows'];
        }
        // obtenemos finalmente la sesion activa
        $res=$this->myDBObject->__select(
            /* select */    "*",
            /* from */      "eventos",
            /* where */     "(Type='open') AND (Data LIKE '%\"Pru\":{$this->prueba['ID']},%')",
            /* order by */  "ID DESC",
            /* limit */     "",
            /* group by */  ""
        );
        if ($res['total']==0) return $result;
        $ses=$res['rows'][0]['Data']; // remember that data is json-encoded
        $result['Current']=json_decode($ses);
        // $this->myLogger->trace(json_encode($result));
        return $result;
    }

    function getEvents($last) {
        $data=$this->myDBObject->__select(
            "*",
            "eventos",
            "( Session=1 ) AND (Type='command') AND (Data LIKE '%\"Pru\":{$this->prueba['ID']},%') AND (ID>{$last})",
            "ID ASC"
        );
        $res=array('total'=>0,'rows'=>array());
        if ($last==0) {
            // first call, no pending events: return dummy created one
            $res['total']=1;
            if ($data['total']==0) {
                $res['rows'][]=array(
                    'LastEvent'=>"1",
                    'Message' => "0:5:msg:"._("Web notification system started"),
                    'TimeStamp' => date('Y/m/d H:i:s')
                );
                return $res;
            }
            // arriving here means first call with events pending. Just return last event
            $data['rows']=array( $data['rows'][$data['total']-1] ); // array with last element
            $data['total']=1;
        }
        $res=array('total'=>0,'rows'=>array());
        foreach ($data['rows'] as $event) {
            $this->myLogger->trace("Parsing event: ".$event['Data']);
            $evtdata=json_decode($event['Data']);
            if (!isset($evtdata->Name) ) continue;
            if ($evtdata->Name !=='Internet') continue;
            $res['total']++;
            $res['rows'][]= array (
                'LastEvent'=>"{$event['ID']}",
                'Message' => $evtdata->Value,
                'TimeStamp' => $event['Timestamp']
            );
        }
        return $res;
    }
}
?>
