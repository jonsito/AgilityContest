<!--
frm_ligas.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

<?php
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
$config =Config::getInstance();
?>

<div id="ligas-tab" style="padding:5px">
    <div title="<?php _e('Grade'); ?> 1" data-options="iconCls:'icon-huella'" style="padding:5px;border:solid 1px #000000">
        <div style="width:100%;height:500px">
            <table id="ligas-g1-datagrid"></table>
        </div>
    </div>
    <div title="<?php _e('Grade'); ?> 2" data-options="iconCls:'icon-dog'" style="padding:5px;border:solid 1px #000000">
        <div style="width:100%;height:500px">
            <table id="ligas-g2-datagrid"></table>
        </div>
    </div>
    <div title="<?php _e('Grade'); ?> 3" data-options="iconCls:'icon-order'" style="padding:5px;border:solid 1px #000000">
        <div style="width:100%;height:500px">
            <table id="ligas-g3-datagrid"></table>
        </div>
    </div>
</div>
<div id="ligas-tools">
    <a id="ligas-printBtn" href="#" class="easyui-linkbutton"
       data-options="iconCls:'icon-print'"
       onclick="printLeague($('#ligas-tab').tabs('getSelected'))"><?php _e('Print'); ?></a>
</div>

<script type="text/javascript">
    $('#ligas-tab').tabs({
        width: 'auto',
        heignt: 550,
        tools: '#ligas-tools'
    });
    // download and create datagrid for Grade 1
    loadLeagueData("GI",function(data){
        $('#ligas-g1-datagrid').datagrid({
            fit:true,
            fitColumns:true,
            columns: [data.header],
            data: data.rows
        });
    });
    // download and create datagrid for Grade 2
    loadLeagueData("GII",function(data){
        $('#ligas-g2-datagrid').datagrid({
            fit:true,
            fitColumns:true,
            columns: [data.header],
            data: data.rows
        });
    });
    var ngrados=parseInt(workingData.datosFederation.Grades);
    if (ngrados===2) {
        // no grade 3: hide tab
        $('#ligas-tab').tabs('close',"<?php _e('Grade'); ?> 3");
    } else {
        // download and create datagrid for Grade 3
        loadLeagueData("GIII",function(data){
            $('#ligas-g3-datagrid').datagrid({
                fit:true,
                fitColumns:true,
                columns: [data.header],
                data: data.rows
            });
        });
    }
    // tooltips
    addTooltip($('#ligas-printBtn'),'<?php _e("Generate PDF or Excel file wiht League data"); ?>');
</script>