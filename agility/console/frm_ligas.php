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
require_once(__DIR__ . "/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am=new AuthManager("Ligas");
?>

<div id="ligas-perro-dialog">
    <div id="ligas-layout">
        <div  style="height:55px;"
              data-options="region:'north',split:true">
            <form id="ligas-perro-datos">
                <label for="ligas-perro-Licencia"><?php _e('Lic');?>:</label>
                <input id="ligas-perro-Licencia" type="text" name="Licencia"/>
                <label for="ligas-perro-Categoria"><?php _e('Cat/Grad');?>:</label>
                <input id="ligas-perro-Categoria" type="text" name="Categoria"/> /
                <input id="ligas-perro-Grado" type="text" name="Grado"/>
                <label for="ligas-perro-Nombre"><?php _e('Name');?>:</label>
                <input id="ligas-perro-Nombre" type="text" name="Nombre"/> -
                <input id="ligas-perro-NombreLargo" type="text" name="NombreLargo""/><br/>
                <label for="ligas-perro-NombreGuia"><?php _e('Handler');?>:</label>
                <input id="ligas-perro-NombreGuia" type="text" name="NombreGuia"/>
                <label for="ligas-perro-NombreClub"><?php _e('Club');?>:</label>
                <input id="ligas-perro-NombreClub" type="text" name="NombreClub"/>
            </form>
        </div>
        <div data-options="region:'center'">
            <table id="ligas-perro-datagrid"></table>
        </div>
        <div data-options="region:'south',split:false" style="height:35px;">
            <span style=float:right;padding:5px;">
            <a id="ligas-perro-printBtn" href="#" onclick="printLeagueByDog();"><?php _e('Print'); ?></a>
            </span>
        </div>
    </div>
</div>

<div id="ligas-perro-buttons">
</div>

<div id="ligas-notallowed">
    <p><strong><?php _e('Current license permissions<br/> do not allow league scoring operations');?></strong></p>
    <img src="/agility/images/sad_dog.png" alt="triste"/>
</div>

<div id="ligas-tab" style="padding:5px;display:<?php echo $am->allowed(ENABLE_LEAGUES)?'inherit':'none';?>">
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
       onclick="printLeague($('#ligas-tab').tabs('getSelected'));"><?php _e('Print'); ?></a>
</div>

<script type="text/javascript">


    $('#ligas-perro-Licencia').textbox({disabled:true,readonly:true,width:70});
    $('#ligas-perro-Categoria').textbox({disabled:true,readonly:true,width:20});
    $('#ligas-perro-Grado').textbox({disabled:true,readonly:true,width:30});
    $('#ligas-perro-Nombre').textbox({disabled:true,readonly:true,width:100});
    $('#ligas-perro-NombreLargo').textbox({disabled:true,readonly:true,width:215});
    $('#ligas-perro-NombreGuia').textbox({disabled:true,readonly:true,width:300});
    $('#ligas-perro-NombreClub').textbox({disabled:true,readonly:true,width:240});

    $('#ligas-layout').layout({ fit:true });

    $('#ligas-perro-dialog').dialog({
        width:640,
        height:300,
        title:"<?php _e('League results for selected dog');?>",
        iconCls:'icon-dog',
        closed: true,
        modal:true
    });

    $('#ligas-notallowed').dialog({
     width:400,
     height:300,
     title:'<?php _e('Not allowed');?>',
     iconCls:'forbidden',
     closed: <?php echo $am->allowed(ENABLE_LEAGUES)?'true':'false'?>
    });

    $('#ligas-tab').tabs({
        width: 'auto',
        heignt: 550,
        tools: '#ligas-tools'
    });

    // download and create datagrid for Grade 1
    ligas_loadLeagueData(0,"GI",function(data){
        var tab = $('#ligas-tab').tabs('getTab', 0);
        var newTitle=workingData.datosFederation.ListaGrados['GI'];
        $('#ligas-tab').tabs('update', { tab: tab, options: { title: newTitle } });
        $('#ligas-g1-datagrid').datagrid({
            fit:true,
            fitColumns:true,
            columns: [data.header],
            data: data.rows,
            onDblClickRow: function(index,row) {
                $('#ligas-g1-datagrid').datagrid('selectRow',index);
                ligas_loadLeagueData(row.Perro,"GI",function(data){
                   ligas_showDogResults(data);
                });
            }
        });
    });
    // download and create datagrid for Grade 2
    ligas_loadLeagueData(0,"GII",function(data){
        var tab = $('#ligas-tab').tabs('getTab', 1);
        var newTitle=workingData.datosFederation.ListaGrados['GII'];
        $('#ligas-tab').tabs('update', { tab: tab, options: { title: newTitle } });
        $('#ligas-g2-datagrid').datagrid({
            fit:true,
            fitColumns:true,
            columns: [data.header],
            data: data.rows,
            onDblClickRow: function(index,row) {
                $('#ligas-g2-datagrid').datagrid('selectRow',index);
                ligas_loadLeagueData(row.Perro,"GII",function(data){
                    ligas_showDogResults(data);
                });
            }
        });
    });
    var ngrados=parseInt(workingData.datosFederation.Grades);
    if (ngrados===2) {
        // no grade 3: hide tab
        $('#ligas-tab').tabs('close',"<?php _e('Grade'); ?> 3");
    } else {
        var tab = $('#ligas-tab').tabs('getTab', 2);
        var newTitle=workingData.datosFederation.ListaGrados['GIII'];
        $('#ligas-tab').tabs('update', { tab: tab, options: { title: newTitle } });
        // download and create datagrid for Grade 3
        ligas_loadLeagueData(0,"GIII",function(data){
            $('#ligas-g3-datagrid').datagrid({
                fit:true,
                fitColumns:true,
                columns: [data.header],
                data: data.rows,
                onDblClickRow: function(index,row) {
                    $('#ligas-g3-datagrid').datagrid('selectRow',index);
                    ligas_loadLeagueData(row.Perro,"GIII",function(data){
                        ligas_showDogResults(data);
                    });
                }
            });
        });
    }

    // tooltips
    $('#ligas-perro-printBtn').linkbutton({ iconCls:'icon-print' });
    addTooltip($('#ligas-printBtn'),'<?php _e("Generate PDF or Excel file with League data"); ?>');
    addTooltip($('#ligas-perro-printBtn'),'<?php _e("Generate PDF or Excel file with dog League data"); ?>');

</script>