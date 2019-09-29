<?php
require_once(__DIR__ . "/Puntuable_RFEC_2018.php");

/*
Puntuable_LigaNorte.php

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

require_once(__DIR__."/lib/ligas/Liga_RFEC_2018.php");

class Puntuable_LigaBalear_2020 extends Puntuable_RFEC_2018 {

    function __construct($name="Liga Balear RFEC 2019-2020") {
        parent::__construct($name);
        $this->federationID=1;
        $this->federationDefault=1;
        $this->competitionID=6;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20190808_1705";
        $this->federationLogoAllowed=true;
    }

    /*
    *
    * Normativa de la liga balear RFEC para la temporada 2019-2020

    - La calificacion es conjunta 60/50 y 25/30/40
    - El TRS se calcula como la media de los dos (2) mejores + 10%
    - Si el TRS final sobrepasa o no llega a los limites federativos, se ajusta a éstos:
      Limites federativos:
            Clases 25/30/40: entre 3.0 y 4.0 metros por segundo
            Clases 50/60:    entre 3.5 y 4.5 metros por segundo
    - Puntuación:
      Por cada Manga:
        Excelente 0 : 5 Puntos
        Excelente   : 4 Puntos
        Muy Bien    : 3 Puntos
        Bien        : 2 Puntos
        NC / Eliminado: no puntuan
      Por conjunta:
        10, 8, 6, 4, 3, 2, 1 por orden de clasificacion
        La penalización total tiene que ser inferior o igual a 11.98
        Los extranjeros no puntuan y corren turno

        Se presentan dos listados:
        El conjunto, 60/50 40/30/25 que sirve para evaluar el TRS y los podium
        El separado por clases, que utiliza los TRS del anterior, y se envía a Federación
    */
    function useLongNames() { return false; }

    function getModuleInfo($contact = null)  {
        return parent::getModuleInfo("yvonneagility@fecaza.com");
    }

    /**
     * Provide default TRS/TRM/Recorrido values for a given competitiona at
     * Round creation time
     * @param {integer} $tipo Round tipe as declared as Mangas::TipoManga
     * @return {array} trs array or null if no changes
     */
    public function presetTRSData($tipo) {
        // when not grade 2 use parent default
        if (!in_array($tipo,array(5,10))) return parent::presetTRSData($tipo);
        $manga=array();
        $manga['Recorrido']=2; // 0:separados 1:dos grupos 2:conjunto 3: tres grupos
        $manga['TRS_X_Tipo']=2;$manga['TRS_X_Factor']=10;  $manga['TRS_X_Unit']='%'; // best two dogs/2 + 10%
        $manga['TRM_X_Tipo']=1;$manga['TRM_X_Factor']=50;  $manga['TRM_X_Unit']='%'; // trs + 50 %
        $manga['TRS_L_Tipo']=2;$manga['TRS_L_Factor']=10;  $manga['TRS_L_Unit']='%'; // best two dogs/2 + 10%
        $manga['TRM_L_Tipo']=1;$manga['TRM_L_Factor']=50;  $manga['TRM_L_Unit']='%'; // trs + 50 %
        $manga['TRS_M_Tipo']=2;$manga['TRS_M_Factor']=10;  $manga['TRS_M_Unit']='%';
        $manga['TRM_M_Tipo']=1;$manga['TRM_M_Factor']=50;  $manga['TRM_M_Unit']='%';
        $manga['TRS_S_Tipo']=2;$manga['TRS_S_Factor']=10;  $manga['TRS_S_Unit']='%';
        $manga['TRM_S_Tipo']=1;$manga['TRM_S_Factor']=50;  $manga['TRM_S_Unit']='%';
        $manga['TRS_T_Tipo']=2;$manga['TRS_T_Factor']=10;  $manga['TRS_T_Unit']='%'; // not used but required
        $manga['TRM_T_Tipo']=1;$manga['TRM_T_Factor']=50;  $manga['TRM_T_Unit']='%';
        return $manga;
    }
    /**
     * Re-evaluate and fix -if required- results data used to evaluate TRS for
     * provided $prueba/$jornada/$manga
     * @param {object} $manga Round data and trs parameters. Passed by reference
     * @param {array} $data Original results provided for evaluation
     * @param {integer} $mode to evaluate which categories are to be used
     * @roundUp {boolean} tell if SCT and MCT should be rounded up to nearest second. Passed by reference
     * @return {array} final data to be used to evaluate trs/trm
     */
    public function checkAndFixTRSData(&$manga,$data,$mode,&$roundUp) {
        /*
        La liga balear utiliza para calcular el TRS la media de los dos (2) mejores
        tiempos mas un 10%, y ajusta por exceso o defecto para que el resultado no sobrepase
        los limites que marca la Federacion nacional para la velocidad.

        Como esto es un lio, lo que vamos a hacer es "engañar" a la función Resultados::evalTRS, y
        le vamos a proporcionar como $data la media de los "tres mejores",
        pero ajustada como primero + segundo + media del primero mas segundo
        */
        // suffix to use with data from $manga according mode
        //    heihgts   3   3   3   3   3    4   4   4   4    5    5    5     5
        //    cats      L   M   S   MS LMS   T  LM  ST  LMST  X   XL   MST  XLMST
        //    mode      0   1   2   3   4    5   6   7   8    9   10   11    12
        $suffix=array( 'L','M','S','M','L', 'T','L','S','L', 'X', 'X', 'M', 'X')[$mode];

        // fase 1: componer $data con los dos mejores datos y la media de ellos 2
        // si hay menos de dos resultados proceder en consecuencia
        if (count($data)<=1) return $data; // nothing to do yet
        if($manga->{"Dist_{$suffix}"}==0) return $data; // no distance provided. cannot evaluate anything
        // si el recorrido es individual y trs es fijo, no se hace nada
        if ( ($manga->Recorrido == 0) && ($manga->{"TRS_{$suffix}_Tipo"}==0) ) return $data;
        $res=array();
        $med=($data[0]['Tiempo']+$data[1]['Tiempo'])/2.0;
        $res[0]=array('Tiempo' =>$data[0]['Tiempo']);
        $res[1]=array('Tiempo' =>$med);
        $res[2]=array('Tiempo' =>$data[1]['Tiempo']);
        // fase 2: comprobar si la velocidad excede de los margenes federativos
        $evaltime=1.10*$med;
        $minspeed=3.0;
        $maxspeed=4.0;
        if (in_array($mode, array(0,4,6,8,9,10,12))) {
            $minspeed=3.5;
            $maxspeed=4.5;
        }
        $maxtime= $manga->{"Dist_{$suffix}"} / $minspeed;
        $mintime= $manga->{"Dist_{$suffix}"} / $maxspeed;
        $speed=0;
        if ($evaltime<$mintime) { $evaltime=$mintime; $speed=$maxspeed; }
        if ($evaltime>$maxtime) { $evaltime=$maxtime; $speed=$minspeed; }

        // fase 3: re-escribir parametros de TRS y TRM conforme a resultado de fase 2
        // vamos a poner como parametro fijos el trs calculado y trm +50%
        $roundUp=false; // no redondear trs
        if ($speed==0) { // estamos dentro de los limites de velocidad: trs por tiempo
            $manga->{"TRS_{$suffix}_Tipo"}=0; // fijo
            $manga->{"TRS_{$suffix}_Factor"}=$evaltime;
            $manga->{"TRS_{$suffix}_Unit"}='s';
        } else { // nos hemos pasado de los limites de velocidad: trs por velocidad (min o max)
            $manga->{"TRS_{$suffix}_Tipo"}=6; // velocidad
            $manga->{"TRS_{$suffix}_Factor"}=$speed;
            $manga->{"TRS_{$suffix}_Unit"}='s';
        }
        $manga->{"TRM_{$suffix}_Tipo"}=1; // TRS + XX
        $manga->{"TRM_{$suffix}_Factor"}=50;
        $manga->{"TRM_{$suffix}_Unit"}='%';
        return $res;
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

        /*
        En la liga balear no hay puntos por puesto en las parciales:
        Los puntos dependen exclusivamente de la calificación obtenida
        Ex0:5 Ex:4 MB:3 B:2 NC/Elim/NP:0
        */
        $pt1=0;
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
            $pt1=2;
            $perro['Calificacion'] = _("Good")." ".$pt1;
            $perro['CShort'] = _("Good")." ".$pt1;
        }
        else if ($perro['Penalizacion']>=6)	{
            $pt1=3;
            $perro['Calificacion'] = _("Very good")." ".$pt1;
            $perro['CShort'] = _("V.G.")." ".$pt1;
        }
        else if ($perro['Penalizacion']>0)	{
            $pt1=4;
            $perro['Calificacion'] = _("Excellent")." ".$pt1;
            $perro['CShort'] = _("Exc")." ".$pt1;
        }
        else if ($perro['Penalizacion']==0)	{
            $pt1=5;
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

        //puestos por general si tiene menos de 11.98 de penalizacion
        $ptsglobal = array("10", "8", "6", "4", "3", "2", "1");

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
        // si no ha participado en ambas mangas no puntua
        if ( ($resultados[0]===null) || ($resultados[1]===null)) {
            $perro['Calificacion']= "{$pt1} - {$pt2} - {$pfin}";
            $perro['Puntos']=0; // not really needed, but...
            return;
        }
        // si tiene mas de 11.98 de penalizacion total no puntua
        if ( ($perro['P1'] + $perro['P2'] )>=11.98 ) {
            $perro['Calificacion']= "{$pt1} - {$pt2} - {$pfin}";
            $perro['Puntos']=intval($pt1)+intval($pt2)+intval($pfin);
            return;
        }
        // evaluamos puesto real una vez eliminados los "extranjeros"
        $puesto=$puestocat[$cat] - $this->pfoffset[$cat];
        // si esta entre los 7 primeros cogemos los puntos
        if ($puesto<8) $pfin=$ptsglobal[$puesto-1];
        // y asignamos la calificacion final
        $perro['Calificacion']="{$pt1} - {$pt2} - {$pfin}";
        $perro['Puntos']=intval($pt1)+intval($pt2)+intval($pfin);
    }

}