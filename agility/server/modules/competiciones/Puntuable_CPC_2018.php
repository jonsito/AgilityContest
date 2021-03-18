<?php
require_once(__DIR__ . "/../../database/classes/DBObject.php");
/*
Puntuable_CPC_2018.php

Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

class Puntuable_CPC_2018 extends Competitions {

    // asignacion de puntos en la temporada 2018

    // solo se obtienen puntos en grado 3, infantil y veteranos
    // en infantil y veteranos se sigue el esquema 'B' de puntos
    // NOTA IMPORTANTE: PENDING esta implementacion no asigna puntos a infantil/veteranos

    // en grado 3 el esquema a usar depende del numero de perros con licencia en cada categoria
    // grado 1 y 2 siguen el sistema habitual de excelentes

    // puntos por cada manga
    // mestizos y extranjeros no puntuan
    protected $pts_agility=array(
        'A'=>array(20,17,14,12,10,8,6,4,2,1), // cuando hay mas de 10 perros censados en la categoria/manga
        'B'=>array(11,8,5,3,1) // cuando hay 10 o menos perros en la categoria/manga
    );
    protected $pts_jumping=array(
        'A'=>array(17,14,11,9,7,5,3,2,1), // cuando hay mas de 10 perros censados en la categoria/manga
        'B'=>array(8,5,3,1) // cuando hay 10 o menos perros censados en la categoria/manga
    );
    // Adicionalmente, en cada manga
    // 3 puntos por cero
    // 2 punto por excelente no cero

    // en conjunta adicionalmente si tienen doble cero se puntua el podium tal que
    protected $ptsglobal = array("3", "2", "1"); //puntos por general (si no NC o Elim en alguna manga)

    protected $poffset=array('X'=>0,'L'=>0,'M'=>0,'S'=>0,'T'=>0); // to skip not-league competitors (partial scores)
    protected $pfoffset=array('X'=>0,'L'=>0,'M'=>0,'S'=>0,'T'=>0); // to skip not-league competitors (final scores)

    protected $myDBObject;

    function __construct($name="Prova Pontuável Campeonato CPC") {
        parent::__construct($name);
        $this->federationID=4;
        $this->competitionID=0;
        $this->moduleRevision="20170930_1427";
        $this->myDBObject=new DBObject("Prueba puntuable CPC 2018");
        $this->federationLogoAllowed=true;
    }

    /**
     * Evaluate if a dog is able to obtain qualification points
     * @param {array} $perro dog data
     * @return bool
     */
    protected function isInLeague($perro) {
        // PENDING los perros mestizos no puntuan

        // como los datos del perro vienen de la tabla resultado, el DogID se obtiene del campo 'Perro'
        $str="SELECT Pais from perroguiaclub WHERE ID={$perro['Perro']}";
        $obj=$this->myDBObject->__selectObject("*","perroguiaclub","ID={$perro['Perro']}");
        // si el perro no es de un club portugues se retorna false; else true
        return ($obj->Pais==="POR");
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
        if ($grad!=="GIII") { // solo se puntua en grado III
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        if (!$this->isInLeague($perro)) { // do not get league points if competitor does not belong to current zone
            $this->poffset[$cat]++; // properly handle puestocat offset
            parent::evalPartialCalification($m,$perro,$puestocat);
            return;
        }
        switch ($cat) {
            // en la temporada 2018 Standard usa el esquema 'A', y mini-midi usan el esquema 'B'
            case 'L': $ptsmanga=(isMangaAgility($m->Tipo))? $this->pts_agility['A']: $this->pts_jumping['A']; break;
            case 'M': $ptsmanga=(isMangaAgility($m->Tipo))? $this->pts_agility['B']: $this->pts_jumping['B']; break;
            case 'S': $ptsmanga=(isMangaAgility($m->Tipo))? $this->pts_agility['B']: $this->pts_jumping['B']; break;
            default: $ptsmanga=array(); // should not happen
        }
        $pt1=0;
        if ($perro['Penalizacion']<6.0) $pt1+=2; // 2 punto por excelente
        if ($perro['Penalizacion']==0.0) $pt1++; // 3 puntos por cero
        // puntos a los count(ptsmanga) primeros por manga/categoria si no estan eliminados o NC
        $puesto=$puestocat[$cat]-$this->poffset[$cat];
        if ( ($puestocat[$cat]>0) && ($perro['Penalizacion']<26) ) {
            // if puesto has points assign them
            if ($puesto<=count($ptsmanga)) $pt1+= $ptsmanga[$puesto-1];
        } else {
            // no points or not qualified; discard
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

        // si no grado III no se puntua
        if ($grad !== "GIII") {
            if ( ($resultados[0]==null) || ($resultados[1]==null)) {
                $perro['Calificacion']= " ";
            } else { // se coge la peor calificacion
                $perro['Calificacion'] = $perro['C1'];
                if ($perro['P1'] < $perro['P2']) $perro['Calificacion'] = $perro['C2'];
            }
            return;
        }

        // los "extranjeros" y "mestizos" no puntuan
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
            return;
        }
        // si no tiene doble excelente no puntua en la general
        if ( ($perro['P1']!=0) || ($perro['P2']!=0) ) {
            $perro['Calificacion']= "$pt1 - $pt2 - $pfin";
            return;
        }
        // evaluamos puesto real una vez eliminados los "extranjeros"
        $puesto=$puestocat[$cat]-$this->pfoffset[$cat];
        // si esta entre los puestos con derecho a punto, se coge
        if ($puesto<=count($this->ptsglobal)) $pfin=$this->ptsglobal[$puesto-1];
        // y asignamos la calificacion final
        $perro['Calificacion']="$pt1 - $pt2 - $pfin";
    }
}