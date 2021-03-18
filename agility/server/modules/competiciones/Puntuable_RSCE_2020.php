<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
require_once(__DIR__ . "/Puntuable_RSCE_2020.php");

class Puntuable_RSCE_2020 extends Puntuable_RSCE_2018 {
    function __construct($name="Puntuable Temporada 2020") {
        parent::__construct($name);
        $this->federationID=0;
        $this->competitionID=19;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20191108_1526";
        $this->puntos=array(
            // en la temporada 2018 desaparecen los puntos dobles
            // se anyade un campo extra para los puntos de ascenso a grado 3
            /* grado      puntos  AgL     AgM    AgS    JpL     JpM     JpS    pts  stars  extras(g3) */
            array("GII",    "Pv",  4.0,    3.8,   3.8,   4.2,    4.0,    4.0,   0,  1,      0 ),
            array("GII",    "Pa",  4.7,    4.5,   4.5,   4.9,    4.7,    4.7,   0,  1,      1 ), // same as g3
            array("GIII",   "Pm",  4.0,    3.8,   3.8,   4.2,    4.0,    4.0,   0,  1,      0 ), // same as pvG2
            array("GIII",   "Pv",  4.7,    4.5,   4.5,   4.9,    4.7,    4.7,   0,  1,      0 )
        );
    }

    function getRoundHeights($mangaid) {
        // overrride parent functions as older RSCE seasons had 3 heights
        return Federations::getFederation($this->federationID)->get('Heights');
    }

    /**
     * Provide default TRS/TRM/Recorrido values for a given competitiona at
     * Round creation time
     * @param {integer} $tipo Round tipe as declared as Mangas::TipoManga
     * @return {array} trs array or null if no changes
     */
    public function presetTRSData($tipo) {
        // when not grade 1, 2 or 3,use parent default
        if (!in_array($tipo,array(3,4,17 /*GI*/ ,5,10 /*GII*/ ,6,11 /*GIII*/))) return parent::presetTRSData($tipo);
        $manga=array();
        // Evaluate TRM. According artículo  27:
        // El  “TIEMPO  MAXIMO  DE  RECORRIDO”  (TMR)  se  determinará  dividiendo  la  longitud  del recorrido
        // por 2.0 m/s en Agility, y por 2.5 m/s en Jumping.
        $manga['Recorrido']=1; // 0:separados 1:mixto(2 grupos) 2:conjunto 3:mixto(tres grupos)
        $trmfactor=(in_array($tipo,array(3,17,5,6)))? 2.0 : 2.5; // Agility: 2%; Jumping: 2.5%
        $manga['TRM_X_Tipo']=6; $manga['TRM_X_Factor']=$trmfactor; $manga['TRM_X_Unit']='m';
        $manga['TRM_L_Tipo']=6; $manga['TRM_L_Factor']=$trmfactor; $manga['TRM_L_Unit']='m'; // 2 or 2.5 m/s
        $manga['TRM_M_Tipo']=6; $manga['TRM_M_Factor']=$trmfactor; $manga['TRM_M_Unit']='m';
        $manga['TRM_S_Tipo']=6; $manga['TRM_S_Factor']=$trmfactor; $manga['TRM_S_Unit']='m';
        $manga['TRM_T_Tipo']=6; $manga['TRM_T_Factor']=$trmfactor; $manga['TRM_T_Unit']='m';

        //Evaluate TRS
        if (in_array($tipo,array(3,4,17))) { // grado 1.
            // para GI ajustamos recorrido a mixto 2 grupos  XL+L / M+S+XS y no configuramos TRS
            $manga['Recorrido']=1; // 0:separados 1:mixto(2 grupos) 2:conjunto 3:mixto(tres grupos)
            $manga['TRS_X_Tipo']=0;$manga['TRS_X_Factor']=0;  $manga['TRS_X_Unit']='s';
            $manga['TRS_L_Tipo']=0;$manga['TRS_L_Factor']=0;  $manga['TRS_L_Unit']='s';
            $manga['TRS_M_Tipo']=0;$manga['TRS_M_Factor']=0;  $manga['TRS_M_Unit']='s';
            $manga['TRS_S_Tipo']=0;$manga['TRS_S_Factor']=0;  $manga['TRS_S_Unit']='s';
            $manga['TRS_T_Tipo']=0;$manga['TRS_T_Factor']=0;  $manga['TRS_T_Unit']='s';
        } else { // grados 2 o 3
            $factor=(in_array($tipo,array(5,10)))?25:15; // Grado 2:25%; grado 3: 15%
            // recorridos separados Para GII y GIII
            $manga['Recorrido']=0; // 0:separados 1:mixto(2 grupos) 2:conjunto 3:mixto(tres grupos).
            $manga['TRS_X_Tipo']=1;$manga['TRS_X_Factor']=$factor;  $manga['TRS_X_Unit']='%';
            $manga['TRS_L_Tipo']=1;$manga['TRS_L_Factor']=$factor;  $manga['TRS_L_Unit']='%'; // best dog + 25 %
            $manga['TRS_M_Tipo']=1;$manga['TRS_M_Factor']=$factor;  $manga['TRS_M_Unit']='%';
            $manga['TRS_S_Tipo']=1;$manga['TRS_S_Factor']=$factor;  $manga['TRS_S_Unit']='%';
            $manga['TRS_T_Tipo']=1;$manga['TRS_T_Factor']=$factor;  $manga['TRS_T_Unit']='%';
        }
        return $manga;
    }

    /**
     * Gets Course penalization, Time, and SCT data and compose penalization
     *
     * Normal mode is that Penalization= CoursePenalization + TimeOverTRS
     * But some competitions resolves Penalization = CoursePenalization+Time
     * So this module is required to be overriden in case of
     * @param {object} $manga datos de la manga
     * @param {array} $perro dog data . Passed by reference
     * @param {array} $tdata sct data
     * @return void
     */
    public function evalPartialPenalization($manga,&$perro,$tdata) {
        $trs=floatval($tdata['trs']);
        // si TRS==0 no hay penalizacion por tiempo
        if ($trs==0) {
            $perro['PTiempo']		= 	0.0;
            $perro['Penalizacion']=	$perro['PRecorrido'];
            return;
        }
        // si la penalización de recorrido no es cero,
        // la penalizacion por tiempo se calcula como siempre
        if ($perro['PRecorrido']!=0) {
            parent::evalPartialPenalization($manga,$perro,$tdata);
            return;
        }
        // si no estamos en grado 2 o 3 procedemos como siempre
        if ( ($perro['Grado']!=='GII') && ($perro['Grado']!=='GIII')) {
            parent::evalPartialPenalization($manga,$perro,$tdata);
            return;
        }
        // si el guia es para-agility no penaliza por tiempo:
        if ($perro['CatGuia']=='P') {
            $perro['PTiempo']		= 	0.0;
            $perro['Penalizacion']=	$perro['PRecorrido'];
            return;
        }
        // si llegando aqui estamos en grado 3, penaliza como siempre
        if ( $perro['Grado']==='GIII') {
            parent::evalPartialPenalization($manga,$perro,$tdata);
            return;
        }
        // llegando aqui tenemos un perro de grado II que ha penalizado cero de recorrido
        // si la velocidad supera el valor de Pv, no penaliza en tiempo
        $distancia = $tdata['dist'];
        $tiempo = floatval($perro['Tiempo']);
        // si no nos han dado distancia,o tiempo no se puede calcular velocidad,
        // por lo que procederemos como siempre
        if ($distancia==0 || $tiempo==0.0) {
            parent::evalPartialPenalization($manga,$perro,$tdata);
            return;
        }
        // ok. calculamos la velocidad y vemos si supera el Pv para su altura
        $vel=floatval($distancia)/$tiempo;
        $offset=( (Mangas::$tipo_manga[$manga->Tipo][5]) == 1)?0/*agility*/:3/*jumping*/;
        $base=2;
        // buscamos cual es el Pv correspondiente a la altura
        switch($perro['Categoria']) {
            case "X": case "L": $base=2; break;
            case "M": $base=3; break;
            case "S":case "T": $base=4; break;
        }
        $pv=$this->puntos[0 /*GradoII*/ ][$base+$offset];
        if ($vel<$pv) { // no supera la velocidad de Pv: procede normalmente
            parent::evalPartialPenalization($manga,$perro,$tdata);
            return;
        }
        // si llega hasta aquí supera la velocidad de Pv: no penaliza en tiempo
        $perro['PTiempo']		= 	0.0;
        $perro['Penalizacion']=	$perro['PRecorrido'];
        return;
    }
}