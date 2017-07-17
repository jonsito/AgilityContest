<?php
require_once("Selectiva_PastorBelga.php");
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class Copa_PastorBelga extends Selectiva_PastorBelga  {

    /*
    * En copa del pastor belga, compiten pastores belgas de cuaquier grado
    * - puntuan tanto en individual como en conjunta los 10 primeros no eliminados.
    * - para puntuar en conjunta no puede estar eliminado en ninguna manga
    * - En caso de empate comparten los mismos puntos
    * - el baremo es: 20,18,16,14,12,10,8,6,4,2 ( el doble que en la selectiva
    */

    function __construct() {
        parent::__construct("Copa del Pastor Belga");
        $this->federationID=0;
        $this->competitionID=4;
        // solo puntuan los 10 primeros, pero anyadimos campos extra por si hay empate en el decimo
        $this->ptsmanga=array("20","18","16","14","12", "10"," 8"," 6"," 4"," 2","0","0"); // puntos por manga y puesto
        $this->ptsglobal=array("20","18","16","14","12", "10"," 8"," 6"," 4"," 2","0","0"); // puntos conjunta
    }

    function getModuleInfo($contact = null)  {
        return parent::getModuleInfo("agility@ceppb.es");
    }

    /**
     * Provide default TRS/TRM/Recorrido values for a given competitiona at
     * Round creation time
     * @param {integer} $tipo Round tipe as declared as Mangas::TipoManga
     * @return {array} trs array or null if no changes
     */
    public function presetTRSData($tipo) {
        // on KO rounds preset to TRS=0:TRM=100 mode=8
        if ( in_array ($tipo, array(15,18,19,20,21,22,23,24) ) ) return parent::presetTRSData($tipo);
        // in std copa pastor belga any grade is allowed, so no check tipo
        $manga=array();
        $manga['Recorrido']=0; // 0:separados 1:mixto 2:conjunto
        $manga['TRS_L_Tipo']=1;$manga['TRS_L_Factor']=15;$manga['TRS_L_Unit']='%'; // mejor + 15%  roundup
        $manga['TRM_L_Tipo']=1;$manga['TRM_L_Factor']=50;$manga['TRM_L_Unit']='%'; // trs + 50 %
        $manga['TRS_M_Tipo']=1;$manga['TRS_M_Factor']=15;$manga['TRS_M_Unit']='%';
        $manga['TRM_M_Tipo']=1;$manga['TRM_M_Factor']=50;$manga['TRM_M_Unit']='%';
        $manga['TRS_S_Tipo']=1;$manga['TRS_S_Factor']=15;$manga['TRS_S_Unit']='%';
        $manga['TRM_S_Tipo']=1;$manga['TRM_S_Factor']=50;$manga['TRM_S_Unit']='%';
        $manga['TRS_T_Tipo']=1;$manga['TRS_T_Factor']=15;$manga['TRS_T_Unit']='%'; // not used but required
        $manga['TRM_T_Tipo']=1;$manga['TRM_T_Factor']=50;$manga['TRM_T_Unit']='%'; // not used but required
        return $manga;
    }

    function checkAndFixTRSData($manga,$data,$mode=0) {
        // override selectiva declaration: just use default ( do nothing )
        return $data;
    }
}