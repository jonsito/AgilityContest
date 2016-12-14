<?php
require_once(__DIR__."/../../server/database/classes/DBObject.php");
/*
Liga_RFEC.php

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

class Liga_RFEC extends Competitions {

    public static $leagueZones=array(
        "Castilla - La Mancha"  =>  0,
        "Comunitat Valenciana"  =>  1, // zona este
        "Andalucía"             =>  2, // zona sur
        "País Vasco"            =>  3, // zona norte
        "Cantabria"             =>  3, // zona norte
        "Asturias"              =>  3, // zona norte
        "Castilla y León"       =>  0, // junto castilla la mancha
        "Extremadura"           =>  2, // zona sur
        "Balears, Illes"        =>  5,
        "Cataluña"              =>  6,
        "Ceuta"                 =>  2, // zona sur
        "Galicia"               =>  4,
        "Aragón"                =>  9,
        "Madrid, Comunidad de"  =>  7,
        "Melilla"               =>  2, // zona sur
        "Murcia, Región de"     =>  1, // zona este
        "Navarra, Comunidad Foral de"  =>  8,
        "Canarias"              =>  10,
        "Rioja, La"             =>  11
    );

    private $poffset=array('L'=>0,'M'=>0,'S'=>0,'T'=>0); // to skip not-league competitors (partial scores)
    private $pfoffset=array('L'=>0,'M'=>0,'S'=>0,'T'=>0); // to skip not-league competitors (final scores)

    private $zonesByClub=array();
    private $leagueZone=-1;
    private $myDBObject;

    function __construct() {
        parent::__construct("Prueba puntuable Liga RFEC");
        $this->federationID=1;
        $this->competitionID=0;
        $this->myDBObject=new DBObject("Prueba untuable Liga RFEC");
    }

    /**
     * @param {array} $perro dog data
     * @return bool
     */
    private function isInLeague($perro) {
        // on first dog, evaluate competition zone for organizer club
        if ($this->leagueZone<0) { // first call, zone not yet evaluated
            $res=$this->myDBObject->__selectObject("Comunidad",
                "Clubes,Provincias"," (Clubes.ID={$this->prueba->Club}) AND (Clubes.Provincia=Provincias.Provincia)");
            if (!$res) {
                do_log("Cannot locate comunidad for organizer club: {$this->prueba->Club}");
                return false;
            }
            if (!array_key_exists($res->Comunidad,Liga_RFEC::$leagueZones)) {
                do_log("Cannot locate league zone for organizer comunidad: {$res->Comunidad}");
                return false;
            }
            $this->leagueZone=Liga_RFEC::$leagueZones[$res->Comunidad];
        }
        // retrieve club zone and test for matching with competition zone
        if(!array_key_exists($perro['NombreClub'],$this->zonesByClub)) {
            // club not yet in cache: parse it
            $res=$this->myDBObject->__selectObject("Comunidad",
                "Clubes,Provincias"," (Clubes.Nombre='{$perro['NombreClub']}') AND (Clubes.Provincia=Provincias.Provincia)");
            if (!$res) {
                do_log("Cannot locate comunidad for club: {$perro['NombreClub']}");
                return false;
            }
            if (!array_key_exists($res->Comunidad,Liga_RFEC::$leagueZones)) {
                do_log("Cannot locate league zone for club: {$perro['NombreClub']}");
                return false;
            }
            // store zone for this club in cache
            $this->zonesByClub[$perro['NombreClub']]=Liga_RFEC::$leagueZones[$res->Comunidad];
        }
        // return zone matching test result
        return ($this->zonesByClub[$perro['NombreClub']]===$this->leagueZone);
    }

    /**
     * Evalua la calificacion parcial del perro
     * @param {object} $p datos de la prueba
     * @param {object} $j datos de la jornada
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($p,$j,$m,&$perro,$puestocat) {
        $grad=$perro['Grado']; // cogemos el grado
        $cat=$perro['Categoria']; // cogemos la categoria
        if ($grad!=="GII") { // solo se puntua en grado II
            parent::evalPartialCalification($p,$j,$m,$perro,$puestocat);
            return;
        }
        if (!$this->isInLeague($perro)) { // do not get league points if competitor does not belong to current zone
            $this->poffset[$cat]++; // properly handle puestocat offset
            parent::evalPartialCalification($p,$j,$m,$perro,$puestocat);
            return;
        }
        $ptsmanga=array("5","4","3","2","1"); // puntos por manga y puesto
        $pt1=0;
        if ($perro['Penalizacion']<6.0) $pt1++; // 1 punto por excelente
        if ($perro['Penalizacion']==0.0) $pt1++; // 2 puntos por cero
        // puntos a los 5 primeros de la zona liguera por manga/categoria si no estan eliminados o NC
        $puesto=$puestocat[$cat]-$this->poffset[$cat];
        if ( ($puestocat[$cat]>0) && ($perro['Penalizacion']<26) && ($puesto<=5) ) {
            $pt1+= $ptsmanga[$puesto-1];
        } else { // no points or not qualified; discard
            parent::evalPartialCalification($p,$j,$m,$perro,$puestocat);
            return;
        }
        if ($perro['Penalizacion']>=400)  {
            $perro['Penalizacion']=400.0;
            $perro['Calificacion'] = "-";
            $perro['CShort'] = "-";
        }
        else if ($perro['Penalizacion']>=200)  {
            $perro['Penalizacion']=200.0;
            $perro['Calificacion'] = _("Not Present");
            $perro['CShort'] = _("N.P.");
        }
        else if ($perro['Penalizacion']>=100) {
            $perro['Penalizacion']=100.0;
            $perro['Calificacion'] = _("Eliminated");
            $perro['CShort'] = _("Elim");
        }
        else if ($perro['Penalizacion']>=26)	{
            $perro['Calificacion'] = _("Not Clasified");
            $perro['CShort'] = _("N.C.");
        }
        else if ($perro['Penalizacion']>=16)	{
            $perro['Calificacion'] = _("Good")." ".$pt1;
            $perro['CShort'] = _("Good")." ".$pt1;
        }
        else if ($perro['Penalizacion']>=6)	{
            $perro['Calificacion'] = _("Very good")." ".$pt1;
            $perro['CShort'] = _("V.G.")." ".$pt1;
        }
        else if ($perro['Penalizacion']>0)	{
            $perro['Calificacion'] = _("Excellent")." ".$pt1;
            $perro['CShort'] = _("Exc")." ".$pt1;
        }
        else if ($perro['Penalizacion']==0)	{
            $perro['Calificacion'] = _("Excellent")." ".$pt1;
            $perro['CShort'] = _("Exc")." ".$pt1;
        }
    }

    /**
     * Evalua la calificacion final del perro
     * @param {object} $p datos de la prueba
     * @param {object} $j datos de la jornada
     * @param {object} $m1 datos de la primera manga
     * @param {object} $m2 datos de la segunda manga
     * @param {array} $c1 datos de la primera manga
     * @param {array} $c2 datos de la segunda manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalFinalCalification($p,$j,$m1,$m2,$c1,$c2,&$perro,$puestocat)
    {
        do_log(json_encode($perro));
        $grad = $perro['Grado']; // cogemos el grado
        $cat = $perro['Categoria']; // cogemos la categoria

        // si no grado II no se puntua
        if ($grad !== "GII") {
            if ( ($c1==null) || ($c2==null)) {
                $perro['Calificacion']= " ";
            } else { // se coge la peor calificacion
                $perro['Calificacion'] = $perro['C1'];
                if ($perro['P1'] < $perro['P2']) $perro['Calificacion'] = $perro['C2'];
            }
            return;
        }

        // los "extranjeros no puntuan
        if (!$this->isInLeague($perro)) {
            $this->pfoffset[$cat]++; // properly handle puestocat offset
            if ( ($c1==null) || ($c2==null)) {
                $perro['Calificacion']= " ";
            } else { // se coge la peor calificacion
                $perro['Calificacion'] = $perro['C1'];
                if ($perro['P1'] < $perro['P2']) $perro['Calificacion'] = $perro['C2'];
            }
            return;
        }

        $ptsglobal = array("15", "12", "9", "7", "6", "5", "4", "3", "2", "1"); //puestos por general (si no NC o Elim en alguna manga)

        // manga 1
        $pt1 = "0";
        if ($c1 != null) { // extraemos los puntos de la primera manga
            $x=substr($perro['C1'],-1,1);
            $pt1=(is_numeric($x))?$x:"0";
        }
        // manga 2
        $pt2="0";
        if ($c2!=null) { // extraemos los puntos de la segunda manga
            $x=substr($perro['C2'],-1,1);
            $pt2=(is_numeric($x))?$x:"0";
        }
        // conjunta
        $pfin="0";
        if ( ($c1==null) || ($c2==null)) { // si falta alguna manga no puntua
            $perro['Calificacion']= "$pt1 - $pt2 - $pfin";
            return;
        }
        // si eliminado o no clasificado en alguna manga no puntua
        if ( ($perro['P1']>=26.0) || ($perro['P2']>=26.0) ) {
            $perro['Calificacion']= "$pt1 - $pt2 - $pfin";
            return;
        }
        // evaluamos puesto real una vez eliminados los "extranjeros"
        $puesto=$puestocat[$cat]-$this->pfoffset[$cat];
        // si esta entre los 10 primeros cogemos los puntos
        if ($puesto<11) $pfin=$ptsglobal[$puesto-1];
        // y asignamos la calificacion final
        $perro['Calificacion']="$pt1 - $pt2 - $pfin";
    }
}