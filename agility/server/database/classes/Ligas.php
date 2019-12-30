<?php
/*
Ligas.php

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
require_once("DBObject.php");
require_once("Jornadas.php");
require_once("Clasificaciones.php");

class Ligas extends DBObject {

    // used to analyze only valid competitions modules on a given federation
    protected $validCompetitions;
    protected $federation;

    /**
     * Ligas constructor.
     * @param $file object name used for debbugging
     * @throws Exception on invalid or not found jornada
     */
    function __construct($file) {
        parent::__construct($file);
        $this->federation=null; // to be intialized later
        $this->validCompetitions=array();
    }

    function update($jornada,$mode){
        return ($mode)===0? $this->delete($jornada) : $this->insert($jornada);
    }

    /**
     * delete current journey's data from league table
     * @return null|string
     */
    function delete($jornada){
        $res=$this->__delete("ligas","Jornada={$jornada}");
        if (!$res) return $this->error($this->conn->error);
        return "";
    }

    /**
     * insert points stars and extras on current journey into league table
     * @return null|string
     */
    function insert($jornada) {
        $this->myLogger->enter();
        $timeout=ini_get('max_execution_time');

        // create prepared statement
        $sql="INSERT INTO ligas (".
            "Jornada,Grado,Perro,".
            "Pt1,Pt2,Pt3,Pt4,Pt5,Pt6,Pt7,Pt8,St1,St2,St3,St4,St5,St6,St7,St8,".
            "Xt1,Xt2,Xt3,Xt4,Xt5,Xt6,Xt7,Xt8,C1,C2,C3,C4,C5,C6,C7,C8,".
            "Puntos,Estrellas,Extras,Calificacion)".
            " VALUES (?,?,?,  ?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?, ?,?,?,?,?,?,?,?, ?,?,?, ?)".
            " ON DUPLICATE KEY UPDATE ".
            " Pt1=VALUES(Pt1), Pt2=VALUES(Pt2), Pt3=VALUES(Pt3), Pt4=VALUES(Pt4), ".
            " Pt5=VALUES(Pt5), Pt6=VALUES(Pt6), Pt7=VALUES(Pt7), Pt8=VALUES(Pt8), ".
            " St1=VALUES(St1), St2=VALUES(St2), St3=VALUES(St3), St4=VALUES(St4), ".
            " St5=VALUES(St5), St6=VALUES(St6), St7=VALUES(St7), St8=VALUES(St8), ".
            " Xt1=VALUES(Xt1), Xt2=VALUES(Xt2), Xt3=VALUES(Xt3), Xt4=VALUES(Xt4), ".
            " Xt5=VALUES(Xt5), Xt6=VALUES(Xt6), Xt7=VALUES(Xt7), Xt8=VALUES(Xt8), ".
            " C1=VALUES(C1), C2=VALUES(C2), C3=VALUES(C3), C4=VALUES(C4), ".
            " C5=VALUES(C5), C6=VALUES(C6), C7=VALUES(C7), C8=VALUES(C8), ".
            "Puntos=VALUES(Puntos),Estrellas=VALUES(Estrellas),Extras=VALUES(Extras), ".
            "Calificacion=VALUES(Calificacion)";
        $stmt=$this->conn->prepare($sql);
        if (!$stmt) return $this->error($this->conn->error);

        // create a Clasification object
        $cobj = Competitions::getClasificacionesInstance("League::Update()", $jornada);
        // retrieve all rounds for current journey.
        $rondas=Jornadas::enumerateRondasByJornada($jornada)['rows'];
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
                $data['Jornada']=$jornada;
                $data['Grado']=$item['Grado'];
                $data['Perro']=$item['Perro'];
                $data['Puntos']=array_key_exists("Puntos",$item)?$item["Puntos"]:0;
                $data['Estrellas']=array_key_exists("Estrellas",$item)?$item["Estrellas"]:0;
                $data['Extras']=array_key_exists("Extras",$item)?$item["Extras"]:0;
                $data['Calificacion']=array_key_exists("Calificacion",$item)?$item["Calificacion"]:'';
                $this->myLogger->trace("LIGAS: Jornada:{$data['Jornada']},Grado:{$data['Grado']},Perro:{$data['Perro']}");
                for ($n=1;$n<9;$n++) {
                    $data["Pt{$n}"]= array_key_exists("Pt{$n}",$item)?$item["Pt{$n}"]:0; // puntos
                    $data["St{$n}"]= array_key_exists("St{$n}",$item)?$item["St{$n}"]:0; // estrellas
                    $data["Xt{$n}"]= array_key_exists("Xt{$n}",$item)?$item["Xt{$n}"]:0; // extras
                    $data["C{$n}"]= array_key_exists("C{$n}",$item)?$item["C{$n}"]:''; // calificaciones
                }
                $res=$stmt->bind_param("isiiiiiiiiiiiiiiiiiiiiiiiiissssssssiiis",
                    $data['Jornada'],$data['Grado'],$data['Perro'],
                    $data['Pt1'],$data['Pt2'],$data['Pt3'],$data['Pt4'],
                    $data['Pt5'],$data['Pt6'],$data['Pt7'],$data['Pt8'],
                    $data['St1'],$data['St2'],$data['St3'],$data['St4'],
                    $data['St5'],$data['St6'],$data['St7'],$data['St8'],
                    $data['Xt1'],$data['Xt2'],$data['Xt3'],$data['Xt4'],
                    $data['Xt5'],$data['Xt6'],$data['Xt7'],$data['Xt8'],
                    $data['C1'],$data['C2'],$data['C3'],$data['C4'],
                    $data['C5'],$data['C6'],$data['C7'],$data['C8'],
                    $data['Puntos'],$data['Estrellas'],$data['Extras'],$data['Calificacion']
                    );
                if (!$res) return $this->error($stmt->error);
                $res=$stmt->execute();
                if (!$res) return $this->error($stmt->error);
            }
        }
        $stmt->close();
        $this->myLogger->leave();
        return "";
    }

    /**
     * Retrieve short form ( global sums ) for all stored results
     * may be overriden for special handling
     * @param {integer} $fed federation ID
     * @param {string} $grado
     */
    function getShortData($fed,$grado) {
        if ($this->federation==null) {
            $this->federation=Federations::getFederation($fed);
        }
        $cats=$this->federation->get('ListaCategorias');
        $jor="";
        $filter="";
        // filter only valid league modules
        if (count($this->validCompetitions)!==0) {
            $lista=implode(",",$this->validCompetitions);
            $jor="jornadas,";
            $filter=" ( jornadas.Tipo_Competicion IN ( {$lista} ) ) AND ligas.Jornada=jornadas.ID AND ";
        }
        $res= $this->__select( // default implementation: just show points sumatory
            "perroguiaclub.ID AS Perro,perroguiaclub.Nombre AS Nombre, ".
                    "perroguiaclub.NombreLargo AS NombreLargo, perroguiaclub.Licencia, ".
                    "perroguiaclub.Categoria, perroguiaclub.NombreGuia, perroguiaclub.NombreClub,".
                    "SUM(Pt1) + SUM(Pt2) AS Puntuacion", // pending: add global points to league table
            "{$jor} ligas, perroguiaclub",
            "{$filter} perroguiaclub.Federation={$fed} AND ligas.Perro=perroguiaclub.ID AND ligas.Grado='{$grado}'",
            "Categoria ASC, Puntos ASC",
            "",
            "Perro"
        );
        // rewrite categoria, as cannot pass "formatCategoria" formatter as function ( passed as string :-( )
        foreach ($res['rows'] as &$row) $row['Categoria']=$cats[$row['Categoria']];
        // add datagrid header
        $res['header']= array(
            array('field' => 'Perro',    'hidden'=>'true'),
            array('field' => 'Licencia',    'title'=>_('License'),  'width' => 7, 'align' => 'right'),
            array('field' => 'Categoria',   'title'=>_('Category'), 'width' => 8, 'align' => 'left'),
            array('field' => 'Nombre',      'title'=>_('Name'),     'width' => 15, 'align' => 'left'),
            array('field' => 'NombreLargo', 'title'=>_('Pedigree'), 'width' => 30, 'align' => 'left'),
            array('field' => 'NombreGuia',  'title'=>_('Handler'),  'width' => 35, 'align' => 'right'),
            array('field' => 'NombreClub',  'title'=>_('Club'),     'width' => 20, 'align' => 'right'),
            array('field' => 'Puntuacion',  'title'=>_('Zeroes'),   'width' => 5,  'align' => 'center')
        );
        return $res;
    }

    function getLongData($perro,$federation,$grado) {
        if ($this->federation==null) {
            $this->federation=Federations::getFederation($federation);
        }
        $filter="";
        // filter only valid league modules
        if (count($this->validCompetitions)!==0) {
            $lista=implode(",",$this->validCompetitions);
            $filter=" ( jornadas.Tipo_Competicion IN ( {$lista} ) ) AND ";
        }
        // fase 2:datos de la liga
        $res= $this->__select(
          "pruebas.ID AS PruebaID, pruebas.Nombre AS Prueba, jornadas.Fecha AS Fecha, ".
                "jornadas.ID AS JornadaID, jornadas.Nombre AS Jornada, clubes.Nombre as NombreClub, ".
                "ligas.C1, ligas.C2, ligas.C3, ligas.C4, ligas.C5, ligas.C6, ligas.C7, ligas.C8, ligas.Calificacion ",
          "pruebas,jornadas,ligas,clubes",
          "{$filter} clubes.ID=pruebas.Club AND pruebas.ID=jornadas.Prueba AND jornadas.ID=ligas.Jornada AND ligas.Perro={$perro} and ligas.Grado='{$grado}'",
          "jornadas.Fecha",
          "",
          ""
        );
        // fase 2 add dog and grade data
        $res['dog']=$this->__getObject("perroguiaclub",$perro);
        $res['grado']=$grado;
        // add datagrid header
        $res['header']= array(
            array('field' => 'Prueba',    'title'=>_('Contest'),  'width' => 40, 'align' => 'left'),
            array('field' => 'NombreClub','title'=>_('Club'),     'width' => 30, 'align' => 'right'),
            array('field' => 'Jornada',   'title'=>_('Journey'),  'width' => 20, 'align' => 'right'),
            array('field' => 'C1',        'title'=>_('Round')." 1",  'width' => 10, 'align' => 'center'),
            array('field' => 'C2',        'title'=>_('Round')." 2",  'width' => 10, 'align' => 'center'),
            array('field' => 'C3',        'title'=>_('Round')." 3",  'width' => 10, 'align' => 'center'),
            array('field' => 'C4',        'title'=>_('Round')." 4",  'width' => 10, 'align' => 'center'),
            array('field' => 'C5',        'title'=>_('Round')." 5",  'width' => 10, 'align' => 'center'),
            array('field' => 'C6',        'title'=>_('Round')." 6",  'width' => 10, 'align' => 'center'),
            array('field' => 'C7',        'title'=>_('Round')." 7",  'width' => 10, 'align' => 'center'),
            array('field' => 'C8',        'title'=>_('Round')." 8",  'width' => 10, 'align' => 'center'),
            array('field' => 'Calificacion','title'=>_('Final'),  'width' => 15, 'align' => 'center')
        );
        return $res;
    }
}