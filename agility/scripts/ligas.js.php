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

/**
 * Call server to retrieve dog league results
 *
 * @param perro 0: to get global data; dogID to get per-dog data
 * @param grado: grado to call. Notice that same dog may exists in several grades
 * @param callback: what to do with response
 */
function ligas_loadLeagueData(perro,grado,callback) {
    $.ajax({
        url: '/agility/server/database/ligaFunctions.php',
        data: {
            Operation: (perro===0)?'shortData':'longData',
            Grado:  grado,
            Perro:  perro, // in long mode show results by perro
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

function ligas_showDogResults(data) {
    $('#ligas-perro-datagrid').datagrid({
        fit:true,
        fitColumns:true,
        singleSelect:true,
        columns: [data.header],
        data: data.rows
        // pending: on double click show califications pdf
    });
    $('#ligas-perro-datos').form('load',data.dog);
    $('#ligas-perro-dialog').dialog('open');
}
