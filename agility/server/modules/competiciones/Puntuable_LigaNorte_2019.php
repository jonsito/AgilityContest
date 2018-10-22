<?php
require_once(__DIR__ . "/Puntuable_RFEC_2018.php");

/*
Puntuable_LigaNorte.php

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

require_once(__DIR__."/lib/ligas/Liga_RFEC_2018.php");

class Puntuable_LigaNorte_2019 extends Puntuable_RFEC_2018 {

    function __construct($name="Puntuable Liga Norte") {
        parent::__construct($name);
        $this->federationID=1;
        $this->federationDefault=1;
        $this->competitionID=3;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20181018_1139";
    }

    function useLongNames() { return false; }

    function getModuleInfo($contact = null)  {
        return parent::getModuleInfo("yvonneagility@fecaza.com");
    }

    /**
     * Evalua la calificacion final del perro
     * @param {array} $mangas informacion {object} de las diversas mangas
     * @param {array} $resultados informacion {array} de los resultados de cada manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalFinalCalification($mangas,$resultados,&$perro,$puestocat){
        $grad = $perro['Grado']; // cogemos el grado
        $cat = $perro['Categoria']; // cogemos la categoria
        if ( ($resultados[0]==null) || ($resultados[1]==null)) {
            $perro['Calificacion']= " ";
        } else { // se coge la peor calificacion
            $perro['Calificacion'] = $perro['C1'];
            if ($perro['P1'] < $perro['P2']) $perro['Calificacion'] = $perro['C2'];
        }
        $perro['Puntos']=0;
        $perro['Estrellas']=0;
        $perro['Extras']=0;
        // si no grado II utiliza los sistemas de calificacion de la RFEC
        if ($grad !== "GII") {
            parent::evalFinalCalification($mangas,$resultados,$perro,$puestocat);
            return;
        }

        // los "extranjeros" no puntuan
        if (!$this->isInLeague($perro)) {
            $this->pfoffset[$cat]++; // properly handle puestocat offset
            if ( ($resultados[0]==null) || ($resultados[1]==null)) {
                $perro['Calificacion']= " ";
            } else { // se coge la peor calificacion
                $perro['Calificacion'] = $perro['C1'];
                if ($perro['P1'] < $perro['P2']) $perro['Calificacion'] = $perro['C2'];
            }
            return;
        }

        $ptsglobal = array("15", "12", "9", "7", "6", "5", "4", "3", "2", "1"); //puestos por general si tiene excelente o muy bueno

        // manga 1
        $pt1 = "0";
        if ($resultados[0] !== null) { // extraemos los puntos de la primera manga
            $x=trim(substr($perro['C1'],-2));
            $pt1=(is_numeric($x))?$x:"0";
        }
        // manga 2
        $pt2="0";
        if ($resultados[1] !== null) { // extraemos los puntos de la segunda manga
            $x=trim(substr($perro['C2'],-2));
            $pt2=(is_numeric($x))?$x:"0";
        }
        // conjunta
        $pfin="0";
        // si falta alguna manga no puntua
        if ( ($resultados[0]===null) || ($resultados[1]===null)) {
            $perro['Calificacion']= "$pt1 - $pt2 - $pfin";
            // no need to evaluate points: just 0
            return;
        }
        // si eliminado o no presentado en _ambas_ mangas, no puntua
        if ( ($perro['P1']>=26.0) && ($perro['P2']>=26.0) ) {
            $perro['Calificacion']= "$pt1 - $pt2 - $pfin";
            $perro['Puntos']=intval($pt1)+intval($pt2)+intval($pfin);
            return;
        }
        // evaluamos puesto real una vez eliminados los "extranjeros"
        $puesto=$puestocat[$cat]-$this->pfoffset[$cat];
        // si esta entre los 10 primeros cogemos los puntos
        if ($puesto<11) $pfin=$ptsglobal[$puesto-1];
        // y asignamos la calificacion final
        $perro['Calificacion']="$pt1 - $pt2 - $pfin";
        $perro['Puntos']=intval($pt1)+intval($pt2)+intval($pfin);
    }

}