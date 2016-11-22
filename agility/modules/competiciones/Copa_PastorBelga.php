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

    function checkAndFixTRSData($prueba,$jornada,$manga,$data) {
        // best * 1.15 round up. must be Declared in round development
        // override selectiva declaration: just use default ( do nothing )
        return $data;
    }
}