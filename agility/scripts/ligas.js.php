/*
 ligas.js

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

<?php
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>

function loadLeagueData(grado,callback) {
    var mode=0; // PENDING rework
    $.ajax({
        url: '/agility/server/database/ligaFunctions.php',
        data: {
            Operation: (mode==0)?'shortData':'longData',
            Grado:  grado,
            Perro:  0, // in long mode show results by perro
            Federation: workingData.datosFederation.ID
        },
        dataType: 'json',
        success: function(result) {
            if (result.errorMsg) {
                $.messager.show({ width:300,height:200, title: 'Error', msg: result.errorMsg });
                return;
            }
            callback(result);
        },
        error: function(XMLHttpRequest,textStatus,errorThrown) {
            alert("Load league data error: "+textStatus + " "+ errorThrown );
        }
    });
}