<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 24/01/18
 * Time: 10:36

Liga_RSCE_2018.php

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
            $jor="jornadas,";
            $filter=" ( jornadas.Tipo_Competicion IN ( {$lista} ) ) AND ligas.Jornada=jornadas.ID AND ";
        }
        if ($grado==="GII") $g3=", SUM(Xt1) AS PA_Agility, SUM(Xt2) AS PA_Jumping"; // promotion to GIII points
        $res=$this->__select( // for rsce
            "perroguiaclub.ID AS Perro, perroguiaclub.Nombre AS Nombre, ".
                    "perroguiaclub.NombreLargo, perroguiaclub.Categoria, ".
                    "perroguiaclub.Licencia, perroguiaclub.NombreGuia, perroguiaclub.NombreClub,".
                    "SUM(Pt1) AS P_Agility, SUM(Pt2) aS P_Jumping, SUM(St1) AS PV_Agility, SUM(St2) AS PV_Jumping {$g3}",
            "{$jor} ligas, perroguiaclub",
            "{$filter} perroguiaclub.Federation={$fed} AND ligas.Perro=perroguiaclub.ID AND ligas.Grado='{$grado}'",
            "Categoria ASC, Licencia ASC",
            "",
            "Perro"
        );
        // rewrite categoria, as cannot pass "formatCategoria" formatter as function ( passed as string :-( )
        foreach ($res['rows'] as &$row) $row['Categoria']=$cats[$row['Categoria']];
        // add datagrid header
        $res['header']= array(
            array('field' => 'Perro',    'hidden'=>'true'),
            array('field' => 'Licencia',    'title'=>_('Lic'),  'width' => 11, 'align' => 'center'),
            array('field' => 'Categoria',    'title'=>_('Cat'),  'width' => 11, 'align' => 'center'),
            array('field' => 'Nombre',      'title'=>_('Name'),     'width' => 15, 'align' => 'left'),
            array('field' => 'NombreLargo', 'title'=>_('Pedigree'), 'width' => 25, 'align' => 'left'),
            array('field' => 'NombreGuia',  'title'=>_('Handler'),  'width' => 33, 'align' => 'right'),
            array('field' => 'NombreClub',  'title'=>_('Club'),     'width' => 25, 'align' => 'right'),
            array('field' => 'P_Agility',  'title'=>_('Pt Ag'),    'width' => 10,  'align' => 'center'),
            array('field' => 'P_Jumping',  'title'=>_('Pt Jp'),   'width' => 10,  'align' => 'center'),
            array('field' => 'PV_Agility',  'title'=>_('Pv Ag'),  'width' => 10,  'align' => 'center'),
            array('field' => 'PV_Jumping',  'title'=>_('Pv Jp'),  'width' => 10,  'align' => 'center')
        );
        if ($grado==="GII") {
            array_push($res['header'],array('field' => 'PA_Agility',  'title'=>_('Pa Ag'),  'width' => 10,  'align' => 'center'));
            array_push($res['header'],array('field' => 'PA_Jumping',  'title'=>_('Pa Jp'),  'width' => 10,  'align' => 'center'));
        }
        return $res;
    }


    function getLongData($perro,$federation,$grado) {
        $res=parent::getLongData($perro,$federation,$grado);
        // rewrite fields array
        $res['header']= array(
            array('field' => 'Fecha',     'title'=>_('Date'),    'width' => 20, 'align' => 'right'),
            array('field' => 'Prueba',    'title'=>_('Contest'), 'width' => 35, 'align' => 'left'),
            array('field' => 'Jornada',   'title'=>_('Journey'), 'width' => 20, 'align' => 'right'),
            array('field' => 'NombreClub','title'=>_('Club'),    'width' => 30, 'align' => 'right')
        );
        if ($grado==="GI") {
            array_push($res['header'],array('field' => 'C1','title'=>_('Agility')." 1",'width' => 10, 'align' => 'center'));
            array_push($res['header'],array('field' => 'C2','title'=>_('Agility')." 2",'width' => 10, 'align' => 'center'));
        } else {
            array_push($res['header'],array('field' => 'C1','title'=>_('Agility'),'width' => 10, 'align' => 'center'));
            array_push($res['header'],array('field' => 'C2','title'=>_('Jumping'),'width' => 10, 'align' => 'center'));

        }
        return $res;
    }
}