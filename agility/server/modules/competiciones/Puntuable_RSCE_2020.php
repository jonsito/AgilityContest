<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
require_once(__DIR__ . "/Puntuable_RSCE_2020.php");

class Puntuable_RSCE_2019 extends Puntuable_RSCE_2018 {
    function __construct() {
        parent::__construct("Puntuable Temporada 2020");
        $this->competitionID=14;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20191108_1526";
    }

    /**
     * Provide default TRS/TRM/Recorrido values for a given competitiona at
     * Round creation time
     * @param {integer} $tipo Round tipe as declared as Mangas::TipoManga
     * @return {array} trs array or null if no changes
     */
    public function presetTRSData($tipo) {
        // when not grade 1, 2 or 3,use parent default
        $manga=parent::presetTRSData($tipo);
        if (!in_array($tipo,array(3,4,17 /*GI5*/ ,5,10 /*GII*/ ,6,11 /*GIII*/))) return $manga;

        // Evaluate TRM. According artículo  27:
        // El  “TIEMPO  MAXIMO  DE  RECORRIDO”  (TMR)  se  determinará  dividiendo  la  longitud  del recorrido
        // por 2.0 m/s en Agility, y por 2.5 m/s en Jumping.
        $manga['Recorrido']=1; // 0:separados 1:mixto(2 grupos) 2:conjunto 3:mixto(tres grupos). para GI: XL+L / M+S+XS
        $trmfactor=(in_array($tipo,array(3,17,5,6)))? 2.0 : 2.5; // Agility: 2%; Jumping: 2.5%
        $manga['TRM_X_Tipo']=6; $manga['TRM_X_Factor']=$trmfactor; $manga['TRM_X_Unit']='m';
        $manga['TRM_L_Tipo']=6; $manga['TRM_L_Factor']=$trmfactor; $manga['TRM_L_Unit']='m'; // 2 or 2.5 m/s
        $manga['TRM_M_Tipo']=6; $manga['TRM_M_Factor']=$trmfactor; $manga['TRM_M_Unit']='m';
        $manga['TRM_S_Tipo']=6; $manga['TRM_S_Factor']=$trmfactor; $manga['TRM_S_Unit']='m';
        $manga['TRM_T_Tipo']=6; $manga['TRM_T_Factor']=$trmfactor; $manga['TRM_T_Unit']='m';

        //Evaluate TRS
        // if not in grade 2 or 3 just return evaluated values
        if (!in_array($tipo,array(5,6,10,11))) return $manga;
        $factor=(in_array($tipo,array(5,10)))?25:15; // Grado 2:25%; grado 3: 15%
        $manga['Recorrido']=0; // 0:separados 1:mixto(2 grupos) 2:conjunto 3:mixto(tres grupos)
        $manga['TRS_X_Tipo']=1;$manga['TRS_X_Factor']=$factor;  $manga['TRS_X_Unit']='%';
        $manga['TRS_L_Tipo']=1;$manga['TRS_L_Factor']=$factor;  $manga['TRS_L_Unit']='%'; // best dog + 25 %
        $manga['TRS_M_Tipo']=1;$manga['TRS_M_Factor']=$factor;  $manga['TRS_M_Unit']='%';
        $manga['TRS_S_Tipo']=1;$manga['TRS_S_Factor']=$factor;  $manga['TRS_S_Unit']='%';
        $manga['TRS_T_Tipo']=1;$manga['TRS_T_Factor']=$factor;  $manga['TRS_T_Unit']='%';
        return $manga;
    }

}