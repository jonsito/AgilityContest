<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 24/01/18
 * Time: 10:36

Liga_RSCE_2018.php

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
require_once (__DIR__."/../../../../database/classes/Ligas.php");

class Liga_RSCE_2018 extends Ligas {

    /**
     * Ligas constructor.
     * @param $file object name used for debbugging
     * @throws Exception on invalid or not found jornada
     */
    function __construct($file) {
        parent::__construct($file);
        // valid competition types are puntuables 2018 and selectivas 2019
        $this->validCompetitions=array(10,11,12);
    }

    /**
     * Retrieve short form ( global sums ) for all stored results
     * may be overriden for special handling
     * @param {integer} $fed federation id
     * @param {string} $grado
     * @return {array} result in easyui-datagrid response format
     */
    function getShortData($fed,$grado) {
        if ($this->federation==null) {
            $this->federation=Federations::getFederation($fed);
        }
        $cats=$this->federation->get('ListaCategorias');
        $g3="";
        if ($grado==="GI") return parent::getShortData($fed,$grado); // no Pv nor Pa, just sum points
        $jor="";
        $filter="";
        // filter only valid league modules
        if (count($this->validCompetitions)!==0) {
            $lista=implode(",",$this->validCompetitions);
            $jor="Jornadas,";
            $filter=" ( Jornadas.Tipo_Competicion IN ( {$lista} ) ) AND Ligas.Jornada=Jornadas.ID AND ";
        }
        if ($grado==="GII") $g3=", SUM(Xt1) AS PA_Agility, SUM(Xt2) AS PA_Jumping"; // promotion to GIII points
        $res=$this->__select( // for rsce
            "PerroGuiaClub.ID AS Perro, PerroGuiaClub.Nombre AS Nombre, PerroGuiaClub.Categoria, ".
                    "PerroGuiaClub.Licencia, PerroGuiaClub.NombreGuia, PerroGuiaClub.NombreClub,".
                    "SUM(Pt1) AS P_Agility, SUM(Pt2) aS P_Jumping, SUM(St1) AS PV_Agility, SUM(St2) AS PV_Jumping {$g3}",
            "{$jor} Ligas, PerroGuiaClub",
            "{$filter} PerroGuiaClub.Federation={$fed} AND Ligas.Perro=PerroGuiaClub.ID AND Ligas.Grado='{$grado}'",
            "Categoria ASC, Licencia ASC",
            "",
            "Perro"
        );
        // rewrite categoria, as cannot pass "formatCategoria" formatter as function ( passed as string :-( )
        foreach ($res['rows'] as &$row) $row['Categoria']=$cats[$row['Categoria']];
        // add datagrid header
        $res['header']= array(
            array('field' => 'Perro',    'hidden'=>'true'),
            array('field' => 'Licencia',    'title'=>_('License'),  'width' => 15, 'align' => 'right'),
            array('field' => 'Categoria',    'title'=>_('Category'),  'width' => 15, 'align' => 'right'),
            array('field' => 'Nombre',      'title'=>_('Name'),     'width' => 20, 'align' => 'center'),
            array('field' => 'NombreGuia',  'title'=>_('Handler'),  'width' => 40, 'align' => 'right'),
            array('field' => 'NombreClub',  'title'=>_('Club'),     'width' => 30, 'align' => 'right'),
            array('field' => 'P_Agility',  'title'=>_('Pt<br/>Agilty'),    'width' => 10,  'align' => 'center'),
            array('field' => 'P_Jumping',  'title'=>_('Pt<br/>Jumping'),   'width' => 10,  'align' => 'center'),
            array('field' => 'PV_Agility',  'title'=>_('Pv<br/>Agility'),  'width' => 10,  'align' => 'center'),
            array('field' => 'PV_Jumping',  'title'=>_('Pv<br/>Jumping'),  'width' => 10,  'align' => 'center')
        );
        if ($grado==="GII") {
            array_push($res['header'],array('field' => 'PA_Agility',  'title'=>_('Pa<br/>Agility'),  'width' => 10,  'align' => 'center'));
            array_push($res['header'],array('field' => 'PA_Jumping',  'title'=>_('Pa<br/>Jumping'),  'width' => 10,  'align' => 'center'));
        }
        return $res;
    }

    function getLongData($perro) {
        // PENDING: write
        return array('total'=>0,'rows'=>array());
    }
}