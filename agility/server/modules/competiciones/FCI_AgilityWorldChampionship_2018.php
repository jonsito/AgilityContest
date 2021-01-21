<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class FCI_AgilityWorldChampionship_2018 extends Competitions {
    function __construct() {
        parent::__construct("FCI Agility World Championship 2018");
        $this->federationID=9;
        $this->competitionID=6;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20171009_1841";
    }

    function useLongNames() { return true; }

    /**
     * Provide default TRS/TRM/Recorrido values for a given competitiona at
     * Round creation time
     *
     * Rules for awc fci 2018 from:
     * http://www.fci.be/medias/AGI-REG-2018-en-5990.pdf
     *
     * a) Determining the Standard Course Time (SCT)
     * In trials at international agility competitions (including AWC, EO, JAEO, CACIAg), the
     * SCT is determined by time of the fastest dog with the fewest course faults + 15% and
     * rounded up to the nearest second.
     * In trials at national competitions the SCT (in seconds) can be determined by dividing the
     * length of the course (in meters) by a chosen speed (in m/s). The speed chosen depends on
     * the standard of the competition, the degree of difficulty of the course and the surface the dog
     * has to run on.
     * Example: A course is160 m long and the chosen speed 4.0 m/s. The SCT is thus 40 seconds
     * (160 ÷ 4.0).
     * b) Determining the Maximum Course Time (MCT)
     * The MCT is determined by dividing the length of the course by 2.0 m/s in agility, 2.5
     * m/s in jumping.
     *
     * @param {integer} $tipo Round tipe as declared as Mangas::TipoManga
     * @return {array} trs array or null if no changes
     */
    public function presetTRSData($tipo) {
        if (!in_array($tipo,array(7,8,12,13))) return parent::presetTRSData($tipo); // Not open/teambest, use parent
        $manga=array();
        $manga['Recorrido']=0; // 0:separados 1:dos grupos 2:conjunto 3: tres grupos
        $manga['TRS_X_Tipo']=1;$manga['TRS_X_Factor']=15;$manga['TRS_X_Unit']='%';
        $manga['TRM_X_Tipo']=6;$manga['TRM_X_Factor']=2.0;$manga['TRM_X_Unit']='m';
        $manga['TRS_L_Tipo']=1;$manga['TRS_L_Factor']=15;$manga['TRS_L_Unit']='%'; // best dog + 15% round up
        $manga['TRM_L_Tipo']=6;$manga['TRM_L_Factor']=2.0;$manga['TRM_L_Unit']='m'; // 2.0ms-agility 2.5ms-jumping
        $manga['TRS_M_Tipo']=1;$manga['TRS_M_Factor']=15;$manga['TRS_M_Unit']='%';
        $manga['TRM_M_Tipo']=6;$manga['TRM_M_Factor']=2.0;$manga['TRM_M_Unit']='m';
        $manga['TRS_S_Tipo']=1;$manga['TRS_S_Factor']=15;$manga['TRS_S_Unit']='%';
        $manga['TRM_S_Tipo']=6;$manga['TRM_S_Factor']=2.0;$manga['TRM_S_Unit']='m';
        $manga['TRS_T_Tipo']=1;$manga['TRS_T_Factor']=15;$manga['TRS_T_Unit']='%'; // not used in AWC but required
        $manga['TRM_T_Tipo']=6;$manga['TRM_T_Factor']=2.0;$manga['TRM_T_Unit']='m'; // not used in AWC but required
        // In jumping rounds (individual/teambest) set SCT factor to 2.5 m/s
        if (in_array($tipo,array(8,13))) {
            $manga['TRM_L_Factor']=2.5;$manga['TRM_M_Factor']=2.5;$manga['TRM_S_Factor']=2.5;$manga['TRM_T_Factor']=2.5;
        }
        $manga['roundUp']=true;
        return $manga;
    }
}