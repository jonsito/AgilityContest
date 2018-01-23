<?php
/*
Ligas.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/
require_once("DBObject.php");
require_once("Jornadas.php");
require_once("Clasificaciones.php");

class Ligas extends DBObject {

    protected $jornadaObj; // object
    protected $pruebaObj; // object

    /**
     * Ligas constructor.
     * @param $file object name used for debbugging
     * @param $jornadaid Jornada ID
     * @throws Exception on invalid or not found jornada
     */
    function __construct($file,$jornadaid) {
        parent::__construct($file);
        if ($jornadaid <= 0) throw new Exception("Ligas::construct() negative or null journey ID");
        $this->jornadaObj = $this->__getObject("Jornadas", $jornadaid);
        if (!$this->jornadaObj) throw new Exception("Ligas::construct() Journey ID: {$jornadaid} not found");
    }

    function update() {
        $timeout=ini_get('max_execution_time');

        // create prepared statement
        $sql="INSERT INTO Ligas (Jornada,Grado,Perro,Pt1,Pt2,Pt3,Pt4,Pt5,Pt6,Pt7,Pt8,St1,St2,St3,St4,St5,St6,St7,St8,Puntos)".
            " VALUES (?,?,?,  ?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?, ?)".
            " ON DUPLICATE KEY UPDATE ".
            " Pt1=VALUES(Pt1), Pt2=VALUES(Pt2), Pt3=VALUES(Pt3), Pt4=VALUES(Pt4), ".
            " Pt5=VALUES(Pt5), Pt6=VALUES(Pt6), Pt7=VALUES(Pt7), Pt8=VALUES(Pt8), ".
            " St1=VALUES(St1), St2=VALUES(St2), St3=VALUES(St3), St4=VALUES(Pt4), ".
            " St5=VALUES(St5), St6=VALUES(St6), St7=VALUES(St7), St8=VALUES(Pt8), Puntos=VALUES(Puntos)";
        $stmt=$this->conn->prepare($sql);
        if (!$stmt) return $this->error($this->conn->error);

        // create a Clasification object
        $cobj = Competitions::getClasificacionesInstance("League::Update()", $this->jornadaObj->ID);
        // retrieve all rounds for current journey.
        $rondas=Jornadas::enumerateRondasByJornada($this->jornadaObj->ID)['rows'];
        foreach ($rondas as $s) {
            set_time_limit($timeout);
            $tipo=intval($s['Tipo1']);
            if (! in_array($tipo,array(/* G1 */ 3,4,17, /* G2 */ 5,10, /* G3 */ 6,11))) continue;
            //generate list of rounds
            $mangas=array(
                intval($s['Manga1']), intval($s['Manga2']), intval($s['Manga3']), intval($s['Manga4']),
                intval($s['Manga5']), intval($s['Manga6']), intval($s['Manga7']), intval($s['Manga8'])
            );
            // and evaluate clasificacion
            $clasificaciones=$cobj->clasificacionFinal($s['Rondas'],$mangas,$s['Mode']);
            // iterate on every item
            foreach ($clasificaciones['rows'] as $item) {
                $data=array();
                $data['Jornada']=$this->jornadaObj->ID;
                $data['Grado']=$item['Grado'];
                $data['Perro']=$item['Perro'];
                $data['Puntos']=array_key_exists("Puntos",$item)?$item["Puntos"]:0;
                $this->myLogger->trace("LIGAS: Jornada:{$data['Jornada']},Grado:{$data['Grado']},Perro:{$data['Perro']}");
                for ($n=1;$n<9;$n++) {
                    $data["Pt{$n}"]= array_key_exists("Pt{$n}",$item)?$item["Pt{$n}"]:0;
                    $data["St{$n}"]= array_key_exists("St{$n}",$item)?$item["St{$n}"]:0;
                }
                $res=$stmt->bind_param("isiiiiiiiiiiiiiiiiii",
                    $data['Jornada'],$data['Grado'],$data['Perro'],
                    $data['Pt1'],$data['Pt2'],$data['Pt3'],$data['Pt4'],
                    $data['Pt5'],$data['Pt6'],$data['Pt7'],$data['Pt8'],
                    $data['St1'],$data['St2'],$data['St3'],$data['St4'],
                    $data['St5'],$data['St6'],$data['St7'],$data['St8'],
                    $data['Puntos']
                    );
                if (!$res) return $this->error($stmt->error);
                $res=$stmt->execute();
                if (!$res) return $this->error($stmt->error);
            }
        }
        $stmt->close();
        return "";
    }

    /**
     * Retrieve short form ( global sums ) for all stored results
     * @param {string} $grado
     */
    function getShortData($grado) {
        if ($this->pruebaObj==null)
            $this->pruebaObj= $this->__getObject("Pruebas", $this->jornadaObj->Prueba);
        $fed=$this->pruebaObj->RSCE;
        // PENDING: make this federation dependent
        $res=$this->__select( // for rsce
            "PerroGuiaClub.Nombre AS Perro, PerroGuiaClub.Licencia, PerroGuiaClub.NombreGuia, PerroGuiaClub.NombreClub,".
                "SUM(Pt1) AS P_Agility, SUM(Pt2) aS P_Jumping, SUM(St1) AS PV_Agility, SUM(St2) AS PV_Jumping",
            "Ligas,PerroGuiaClub",
            "PerroGuiaClub.Federation={$fed} AND Ligas.Perro=PerroGuiaClub.ID AND Ligas.Grado='{$grado}'",
            "Licencia ASC",
            "",
            "Perro"
        );
        // PENDING: add extra entries for Selectivas RSCE

        $res2= $this->__select( // for RFEC
            "PerroGuiaClub.Nombre AS Perro, PerroGuiaClub.Licencia, PerroGuiaClub.NombreGuia, PerroGuiaClub.NombreClub,".
                "SUM(Pt1) + SUM(Pt2) + SUM(Puntos) AS Puntuacion", // pending: add global points to league table
            "Ligas,PerroGuiaClub",
            "PerroGuiaClub.Federation={$fed} AND Ligas.Perro=PerroGuiaClub.ID AND Ligas.Grado='{$grado}'",
            "Puntos ASC",
            "",
            "Perro"

        );
    }
}