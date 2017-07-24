<?php
require_once(__DIR__."/../database/classes/DBObject.php");
require_once(__DIR__ . "/../modules/Federations.php");
require_once(__DIR__ . "/../modules/Competitions.php");
/*
MacroParser.php

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

class MacroParser {
    protected $myPrueba;
    protected $myJornadas;
    protected $templatelist;
    /**
     * MacroParser constructor.
     * @param {integer} $prueba id de la prueba
     */
    function __construct($prueba) {
        $prueba=intval($prueba);
        $myDBObject=new DBObject("MacroParser");
        $this->myPrueba=$myDBObject->__selectAsArray("Pruebas.*,Clubes.Nombre as NombreClub","Pruebas,Clubes","(Pruebas.ID=$prueba) AND (Pruebas.Club=Clubes.ID)");
        $this->myJornadas=array();
        $res=$myDBObject->__select("*","Jornadas","Prueba=$prueba");
        if(!$res) $res=array('total' =>0,'rows'=>array());
        foreach ($res['rows'] as $jornada) array_push($this->myJornadas,$jornada);
        do_log("Prueba: ".json_encode($this->myPrueba));
        // create function array
        $this->templatelist = array(
            "__CONTEST__" => function($p,$j,$key,$str) { return str_replace($key,$p['Nombre'],$str);},
            "__FEDERATION__" => function($p,$j,$key,$str) {
                $fed=Federations::getFederation(intval($p['RSCE']));
                return str_replace($key,$fed->get('LongName'),$str);
            },
            "__CLUB__" => function($p,$j,$key,$str) { return str_replace($key,$p['NombreClub'],$str);},
            "__LOCATION__" => function($p,$j,$key,$str) { return str_replace($key,$p['Ubicacion'],$str);},
            "__POSTER__" => function($p,$j,$key,$str) { return str_replace($key,$p['Cartel'],$str);},
            "__TRIPTYCH__" => function($p,$j,$key,$str) { return str_replace($key,$p['Triptico'],$str);},
            "__COMMENTS__" => function($p,$j,$key,$str) { return str_replace($key,$p['Observaciones'],$str);},
            "__J1_NAME__" => function($p,$j,$key,$str) { return str_replace($key,$j[0]['Nombre'],$str); },
            "__J2_NAME__" => function($p,$j,$key,$str) { return str_replace($key,$j[1]['Nombre'],$str); },
            "__J3_NAME__" => function($p,$j,$key,$str) { return str_replace($key,$j[2]['Nombre'],$str); },
            "__J4_NAME__" => function($p,$j,$key,$str) { return str_replace($key,$j[3]['Nombre'],$str); },
            "__J5_NAME__" => function($p,$j,$key,$str) { return str_replace($key,$j[4]['Nombre'],$str); },
            "__J6_NAME__" => function($p,$j,$key,$str) { return str_replace($key,$j[5]['Nombre'],$str); },
            "__J7_NAME__" => function($p,$j,$key,$str) { return str_replace($key,$j[6]['Nombre'],$str); },
            "__J8_NAME__" => function($p,$j,$key,$str) { return str_replace($key,$j[7]['Nombre'],$str); },
            "__J1_DATE__" => function($p,$j,$key,$str) { return str_replace($key,$j[0]['Fecha'],$str); },
            "__J2_DATE__" => function($p,$j,$key,$str) { return str_replace($key,$j[1]['Fecha'],$str); },
            "__J3_DATE__" => function($p,$j,$key,$str) { return str_replace($key,$j[2]['Fecha'],$str); },
            "__J4_DATE__" => function($p,$j,$key,$str) { return str_replace($key,$j[3]['Fecha'],$str); },
            "__J5_DATE__" => function($p,$j,$key,$str) { return str_replace($key,$j[4]['Fecha'],$str); },
            "__J6_DATE__" => function($p,$j,$key,$str) { return str_replace($key,$j[5]['Fecha'],$str); },
            "__J7_DATE__" => function($p,$j,$key,$str) { return str_replace($key,$j[6]['Fecha'],$str); },
            "__J8_DATE__" => function($p,$j,$key,$str) { return str_replace($key,$j[7]['Fecha'],$str); },
            "__J1_TIME__" => function($p,$j,$key,$str) { return str_replace($key,$j[0]['Fecha'],$str); },
            "__J2_TIME__" => function($p,$j,$key,$str) { return str_replace($key,$j[1]['Fecha'],$str); },
            "__J3_TIME__" => function($p,$j,$key,$str) { return str_replace($key,$j[2]['Fecha'],$str); },
            "__J4_TIME__" => function($p,$j,$key,$str) { return str_replace($key,$j[3]['Fecha'],$str); },
            "__J5_TIME__" => function($p,$j,$key,$str) { return str_replace($key,$j[4]['Fecha'],$str); },
            "__J6_TIME__" => function($p,$j,$key,$str) { return str_replace($key,$j[5]['Fecha'],$str); },
            "__J7_TIME__" => function($p,$j,$key,$str) { return str_replace($key,$j[6]['Fecha'],$str); },
            "__J8_TIME__" => function($p,$j,$key,$str) { return str_replace($key,$j[7]['Fecha'],$str); },
            "__J1_TYPE__" => function($p,$j,$key,$str) { return str_replace($key,Competitions::moduleInfo($p['RSCE'],$j[0]['Tipo_Competicion'])['ModuleName'],$str); },
            "__J2_TYPE__" => function($p,$j,$key,$str) { return str_replace($key,Competitions::moduleInfo($p['RSCE'],$j[1]['Tipo_Competicion'])['ModuleName'],$str); },
            "__J3_TYPE__" => function($p,$j,$key,$str) { return str_replace($key,Competitions::moduleInfo($p['RSCE'],$j[2]['Tipo_Competicion'])['ModuleName'],$str); },
            "__J4_TYPE__" => function($p,$j,$key,$str) { return str_replace($key,Competitions::moduleInfo($p['RSCE'],$j[3]['Tipo_Competicion'])['ModuleName'],$str); },
            "__J5_TYPE__" => function($p,$j,$key,$str) { return str_replace($key,Competitions::moduleInfo($p['RSCE'],$j[4]['Tipo_Competicion'])['ModuleName'],$str); },
            "__J6_TYPE__" => function($p,$j,$key,$str) { return str_replace($key,Competitions::moduleInfo($p['RSCE'],$j[5]['Tipo_Competicion'])['ModuleName'],$str); },
            "__J7_TYPE__" => function($p,$j,$key,$str) { return str_replace($key,Competitions::moduleInfo($p['RSCE'],$j[6]['Tipo_Competicion'])['ModuleName'],$str); },
            "__J8_TYPE__" => function($p,$j,$key,$str) { return str_replace($key,Competitions::moduleInfo($p['RSCE'],$j[7]['Tipo_Competicion'])['ModuleName'],$str); },
        );
    }

    function compile($text) {
        foreach($this->templatelist as $macro => $func) {
            // as functions in array are enclosed in closures cannot direct access to class variables, so pass by argument
            $text=  $this->templatelist[$macro]($this->myPrueba,$this->myJornadas,$macro,$text);
        }
        return $text;
    }
}
?>