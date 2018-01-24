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
     * Retrieve short form ( global sums ) for all stored results
     * may be overriden for special handling
     * @param {integer} $fed federation id
     * @param {string} $grado
     * @return {array} result in easyui-datagrid response format
     */
    function getShortData($fed,$grado) {
        $g3="";
        if ($grado==="GI") return parent::getShortData($fed,$grado); // no Pv nor Pa, just sum points
        if ($grado==="GII") $g3=", SUM(Xt1) AS PA_Agility, SUM(Xt2) AS PA_Jumping"; // promotion to GIII points
        $res=$this->__select( // for rsce
            "PerroGuiaClub.Nombre AS Perro, PerroGuiaClub.Licencia, PerroGuiaClub.NombreGuia, PerroGuiaClub.NombreClub,".
            "SUM(Pt1) AS P_Agility, SUM(Pt2) aS P_Jumping, SUM(St1) AS PV_Agility, SUM(St2) AS PV_Jumping {$g3}",
            "Ligas,PerroGuiaClub",
            "PerroGuiaClub.Federation={$fed} AND Ligas.Perro=PerroGuiaClub.ID AND Ligas.Grado='{$grado}'",
            "Licencia ASC",
            "",
            "Perro"
        );
        return $res;
    }

    function getLongData($perro) {
        // PENDING: write
        return array('total'=>0,'rows'=>array());
    }
}