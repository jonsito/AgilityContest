<?php
require_once(__DIR__ . "/../../database/classes/DBObject.php");
/*
Puntuable_RFEC_2018.php

Copyright  2013-2019 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

class Puntuable_RFEC_2018 extends Competitions {

    public static $leagueZones=array(
        "Castilla - La Mancha"  =>  0, // Liga Dos Castillas
        "Comunitat Valenciana"  =>  1, // zona este
        "Andalucía"             =>  2, // Liga Sur
        "País Vasco"            =>  3, // Liga Norte
        "Cantabria"             =>  3, // Liga Norte
        "Asturias"              =>  3, // Liga Norte
        "Castilla y León"       =>  0, // Liga Dos Castillas
        "Extremadura"           =>  2, // Liga Sur
        "Balears, Illes"        =>  5,
        "Cataluña"              =>  6,
        "Ceuta"                 =>  2, // Liga Sur
        "Galicia"               =>  4,
        "Aragón"                =>  9,
        "Madrid, Comunidad de"  =>  7,
        "Melilla"               =>  2, // Liga Sur
        "Murcia, Región de"     =>  1, // zona este
        "Navarra, Comunidad Foral de"  =>  8,
        "Canarias"              =>  10,
        "Rioja, La"             =>  3 // temporalmente en Liga Norte
    );

    protected $poffset=array('G'=>0,'X'=>0,'L'=>0,'M'=>0,'S'=>0,'T'=>0); // to skip not-league competitors (partial scores)
    protected $pfoffset=array('G'=>0,'X'=>0,'L'=>0,'M'=>0,'S'=>0,'T'=>0); // to skip not-league competitors (final scores)

    protected $zonesByClub=array();
    protected $leagueZone=-1;
    protected $myDBObject;

    function __construct($name="Puntuable Liga RFEC (Nacional)") {
        parent::__construct($name);
        $this->federationID=1;
        $this->competitionID=0;
        $this->myDBObject=new DBObject("Prueba puntuable Liga RFEC");
        $this->federationLogoAllowed=true;
    }

    /**
     * @param {array} $perro dog data
     * @return bool
     */
    protected function isInLeague(&$perro) {
        // on first dog, evaluate competition zone for organizer club
        if ($this->leagueZone<0) { // first call, zone not yet evaluated
            $res=$this->myDBObject->__selectObject("Comunidad",
                "clubes,provincias"," (clubes.ID={$this->prueba->Club}) AND (clubes.Provincia=provincias.Provincia)");
            if (!$res) {
                do_log("Cannot locate comunidad for organizer club: {$this->prueba->Club}");
                return false;
            }
            if (!array_key_exists($res->Comunidad,Puntuable_RFEC_2018::$leagueZones)) {
                do_log("Cannot locate league zone for organizer comunidad: {$res->Comunidad}");
                return false;
            }
            $this->leagueZone=Puntuable_RFEC_2018::$leagueZones[$res->Comunidad];
        }
        // retrieve club zone and test for matching with competition zone
        if(!array_key_exists($perro['NombreClub'],$this->zonesByClub)) {
            // club not yet in cache: parse it
            // As club name changes doesn't propagate to table "Resultados" cannot use NombreClub to search provincia
            $res=$this->myDBObject->__selectObject("Comunidad",
                "perroguiaclub,provincias",
                " (perroguiaclub.ID LIKE '%{$perro['Perro']}%') AND (perroguiaclub.Provincia=provincias.Provincia)"
            );
            if (!$res) {
                do_log("Cannot locate comunidad for club: {$perro['NombreClub']}");
                return false;
            }
            if (!array_key_exists($res->Comunidad,Puntuable_RFEC_2018::$leagueZones)) {
                do_log("Cannot locate league zone for club: {$perro['NombreClub']}");
                return false;
            }
            // store zone for this club in cache
            $this->zonesByClub[$perro['NombreClub']]=Puntuable_RFEC_2018::$leagueZones[$res->Comunidad];
        }
        // return zone matching test result
        if ($this->zonesByClub[$perro['NombreClub']]===$this->leagueZone) return true;
        $perro['Licencia']="Open";
        return false;
    }

    /**
     * Evalua la calificacion parcial del perro
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
        if ($grad!=="GII") { // solo se puntua en grado II
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        if (!$this->isInLeague($perro)) { // do not get league points if competitor does not belong to current zone
            $this->poffset[$cat]++; // properly handle puestocat offset
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        $ptsmanga=array("5","4","3","2","1"); // puntos por manga y puesto
        $pt1=0;
        if ($perro['Penalizacion']<6.0) $pt1++; // 1 punto por excelente
        if ($perro['Penalizacion']==0.0) $pt1++; // 2 puntos por cero
        // puntos a los 5 primeros de la zona liguera por manga/categoria si no estan eliminados o NC
        $puesto=$puestocat[$cat]-$this->poffset[$cat];
        if ( ($puestocat[$cat]>0) && ($perro['Penalizacion']<26) ) {
            // PENDING: study why some puestos have negative value
            if (($puesto>0) && ($puesto<=5)) $pt1+= $ptsmanga[$puesto-1];
        } else { // no points or not qualified; discard
            parent::evalPartialCalification($m,$perro,$puestocat);
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

        // calificaciones por defecto
        if ( ($resultados[0]==null) || ($resultados[1]==null)) {
            $perro['Calificacion']= " ";
        } else { // se coge la peor calificacion
            $perro['Calificacion'] = $perro['C1'];
            if ($perro['P1'] < $perro['P2']) $perro['Calificacion'] = $perro['C2'];
        }
        $perro['Puntos']=0;
        $perro['Estrellas']=0;
        $perro['Extras']=0;

        // en grado 1 e Infantil/Junior contabilizamos excelentes(Puntos) y ceros(estrellas)
        // NO se calculan puntos de clasificacion
        if ($grad !== "GII") {
            foreach (array(1,2,3) as $m){
                if ($resultados[intval($m) - 1 ]==null) continue;
                if ( ($perro["T{$m}"]!=0) && ($perro["P{$m}"]<6) ) $perro['Puntos']++;
                if ( ($perro["T{$m}"]!=0) && ($perro["P{$m}"]==0) ) $perro['Estrellas']++;
            }
            return;
        }

        // los "extranjeros no puntuan, perro corren turno
        if (!$this->isInLeague($perro)) {
            $this->pfoffset[$cat]++; // properly handle puestocat offset
            return;
        }

        $ptsglobal = array("15", "12", "9", "7", "6", "5", "4", "3", "2", "1"); //puestos por general (si no NC o Elim en alguna manga)

        // manga 1
        $pt1 = "0";
        if ($resultados[0] !== null) { // extraemos los puntos de la primera manga
            $x=trim(substr($perro['C1'],-2));
            $pt1=(is_numeric($x))?$x:"0";
        }
        // manga 2
        $pt2="0";
        if ($resultados[1]!==null) { // extraemos los puntos de la segunda manga
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
        // si eliminado o no clasificado en alguna manga no puntua
        if ( ($perro['P1']>=26.0) || ($perro['P2']>=26.0) ) {
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