<?php

/*
Selectiva_awc_2019.php

Copyright  2013-2020 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/
require_once( __DIR__."/Selectiva_awc_2018.php");
class Selectiva_awc_2020 extends Selectiva_awc_2018 {

    function __construct($name="Selectiva AWC 2020") {
        parent::__construct($name);
        $this->federationID=0;
        $this->competitionID=17;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20191111_2300";
        $this->selectiva=1;
        $this->federationLogoAllowed=true;
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
        }
        if (in_array($tipo,array(5,10))) { // grado 2
            $manga['Recorrido']=0; // 0:separados 1:mixto(2 grupos) 2:conjunto 3:mixto(tres grupos).
            $manga['TRS_X_Tipo']=1;$manga['TRS_X_Factor']=25;  $manga['TRS_X_Unit']='%';
            $manga['TRS_L_Tipo']=1;$manga['TRS_L_Factor']=25;  $manga['TRS_L_Unit']='%'; // best dog + 25 %
            $manga['TRS_M_Tipo']=1;$manga['TRS_M_Factor']=25;  $manga['TRS_M_Unit']='%';
            $manga['TRS_S_Tipo']=1;$manga['TRS_S_Factor']=25;  $manga['TRS_S_Unit']='%';
            $manga['TRS_T_Tipo']=1;$manga['TRS_T_Factor']=25;  $manga['TRS_T_Unit']='%';
        }
        if (in_array($tipo,array(6,11))) { // grado 3 ( tres alturas )
            // en la selectiva el tiempo lo marca el mejor perro + 0 segundos
            $manga['Recorrido']=0; // 0:separados 1:mixto(2 grupos) 2:conjunto 3:mixto(tres grupos).
            $manga['TRS_X_Tipo']=1;$manga['TRS_X_Factor']=0;  $manga['TRS_X_Unit']='s';
            $manga['TRS_L_Tipo']=1;$manga['TRS_L_Factor']=0;  $manga['TRS_L_Unit']='s';
            $manga['TRS_M_Tipo']=1;$manga['TRS_M_Factor']=0;  $manga['TRS_M_Unit']='s';
            $manga['TRS_S_Tipo']=1;$manga['TRS_S_Factor']=0;  $manga['TRS_S_Unit']='s';
            $manga['TRS_T_Tipo']=1;$manga['TRS_T_Factor']=0;  $manga['TRS_T_Unit']='s';
        }
        return $manga;
    }

    // grados 1 y 2 son a cinco alturas; la selectiva (grado 3) es a 3 alturas
    public function getRoundHeights($mangaid) {
        $myDbObject= new DBObject("getRoundHeights");
        $result=$myDbObject->__getObject('mangas',$mangaid);
        if (!is_object($result)) return parent::getRoundHeights($mangaid);
        if(in_array($result->Tipo,array(6,11) ) ) return 3; // grado 3
        return 5;
    }
}