<?php
require_once(__DIR__ . "/Puntuable_RFEC_2018.php");

/*
Puntuable_FMC_2018.php

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

class Puntuable_FMC_2018 extends Puntuable_RFEC_2018 {

    function __construct() {
        parent::__construct("Prueba puntuable Liga FMC - 2018");
        $this->federationID=1;
        $this->federationDefault=1;
        $this->competitionID=2;
        $this->moduleVersion="1.2.0";
        $this->moduleRevision="20180125_1113";
    }

    function getModuleInfo($contact = null)  {
        return parent::getModuleInfo("yvonneagility@fecaza.com");
    }

    /**
     * Evalua la calificacion parcial del perro
     *
     * Notice that we cannot call parent::evalPartialCalification() cause parent class is Liga_RFEC
     * And has his own point assignment code. So invoke directly Competitions:: to get generic code
     *
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($m,&$perro,$puestocat) {
        $grad=$perro['Grado']; // cogemos el grado
        $cat=$perro['Categoria']; // cogemos la categoria

        $tipo=$m->Tipo;
        if (($tipo==8) || ($tipo==9) || ($tipo==13)|| ($tipo==14)) { // equipos
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        $penal=floatval($perro['Penalizacion']);
        if ($grad!=="GII") { // solo se puntua en grado II
            Competitions::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        if (!$this->isInLeague($perro)) { // do not get league points if competitor does not belong to current zone
            $this->poffset[$cat]++; // properly handle puestocat offset
            Competitions::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        $ptsmanga=array("7","5","3","2","1"); // puntos por manga y puesto
        $pt1=0;
        if ($penal<6.0) $pt1++; // 1 punto por excelente
        if ($penal==0.0) $pt1+=2; // 3 puntos por cero
        // puntos a los 5 primeros de la zona liguera por manga/categoria tienen excelente o muy bueno
        // en madrid se permite que los perros NC puntuen
        $puesto=$puestocat[$cat]-$this->poffset[$cat];
        if ( ($puestocat[$cat]>0) && ($penal<16) ) {
            if ($puesto<=5) $pt1+= $ptsmanga[$puesto-1];
        } else { // no points or not qualified; discard
            Competitions::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        if ($penal>=400)  {
            $perro['Penalizacion']=400.0;
            $perro['Calificacion'] = "-";
            $perro['CShort'] = "-";
        }
        else if ($penal>=200)  {
            $perro['Penalizacion']=200.0;
            $perro['Calificacion'] = _("Not Present");
            $perro['CShort'] = _("N.P.");
        }
        else if ($penal>=100) {
            $perro['Penalizacion']=100.0;
            $perro['Calificacion'] = _("Eliminated");
            $perro['CShort'] = _("Elim");
        }
        else if ($penal>=26)	{
            $perro['Calificacion'] = _("Not Clasified");
            $perro['CShort'] = _("N.C.");
        }
        else if ($penal>=16)	{ // en el 2018 solo puntuan excelentes y muy buenos
            $perro['Calificacion'] = _("Good");
            $perro['CShort'] = _("Good");
        }
        else if ($penal>=6)	{
            $perro['Calificacion'] = _("Very good")." ".$pt1;
            $perro['CShort'] = _("V.G.")." ".$pt1;
        }
        else if ($penal>0)	{
            $perro['Calificacion'] = _("Excellent")." ".$pt1;
            $perro['CShort'] = _("Exc")." ".$pt1;
        }
        else if ($penal==0)	{
            $perro['Calificacion'] = _("Excellent")." ".$pt1;
            $perro['CShort'] = _("Exc")." ".$pt1;
        }
        // datos para la exportacion de parciales en excel
        $perro['Puntos'] = $pt1;
        $perro['Estrellas']= 0;
        $perro['Extras']= 0;
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
        // Temporada 2018 no puntuan en conjunta si tienen alguna manga con mas de 15.99
        if ( ($perro['P1']>=16.0) || ($perro['P2']>=16.0) ) {
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

    /**
     * Retrieve handler for manage Ligas functions.
     * Default is use standard Ligas, but may be overriden ( eg wao. Rounds )
     * @param {string} $file
     * @return {Ligas} instance of requested Ligas object
     * @throws Exception on invalid prueba/jornada/manga
     */
    protected function getLigasObject($file) {
        return new Liga_RFEC_2018($file);
    }
}