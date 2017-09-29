 <!-- 
import_clubes.inc

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
 require_once(__DIR__ . "/../../server/tools.php");
 require_once(__DIR__ . "/../../server/auth/Config.php");
 $config =Config::getInstance();
 ?>

<!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE importclubes -->
 <div id="importClub-dialog" class="easyui-dialog" style="width:550px;height:auto;padding:10px 20px;"
    data-options="modal:true,closable:false,closed:true,buttons:'#importClub-dlg-buttons',iconCls:'icon-flag'">
    <div class="ftitle"><?php _e('Club data Import'); ?></div>
     <p><span id="importClub-Text"></span></p>
     <form id="importClub-header">
         <p id="importClub-Text"> </p> <!-- to be filled -->
         <div class="fitem">
             <label for="importClub-Search"><?php _e('Search'); ?>: </label>
             <select id="importClub-Search" name="Search" style="width:250px"></select>&nbsp;
             <a id="importClub-clearBtn" href="#" class="easyui-linkbutton"
                data-options="iconCls: 'icon-brush'"><?php _e('Clear'); ?></a>
             <input type="hidden" id="importClub-ClubID" value="0"/>
             <input type="hidden" name="importClub-UseExcelNames" value="0"/>
         </div>
     </form>
</div>  
        
<!-- BOTONES DE IMPORTAR / SELECCIONAR / IGNORAR DEL CUADRO DE DIALOGO -->
<div id="importClub-dlg-buttons" style="display:inline-block">
    <span style="float:left">
        <a id="importClub-newBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-new'"><?php _e('New'); ?></a>
    </span>
    <span style="float:right">
        <a id="importClub-selectBtn" href="#" class="easyui-linkbutton"
            onclick="importAction('Club','update',$('#importClub-ClubID').val(),$('#importClub-Search').combogrid('getValue'));"
            data-options="iconCls: 'icon-ok'" ><?php _e('Select'); ?></a>
        <a id="importClub-ignoreBtn" href="#" class="easyui-linkbutton"
            onclick="importAction('Club','ignore',$('#importClub-ClubID').val(),$('#importClub-Search').combogrid('getValue'));"
            data-options="iconCls:'icon-cancel'" ><?php _e('Ignore'); ?></a>
    </span>
</div>
    
<script type="text/javascript">

        // - declaracion del formulario
        $('#importClub-form').form();

        // - botones
    	addTooltip($('#importClub-newBtn').linkbutton(),'<?php _e("Open dialog to create new club"); ?>');
        $('#importClub-newBtn').bind('click',function() {
            newClub(
                null, // no datagrid to refresh
                $('#importClub-Search').combogrid('getText'), // default name
                function() { // what to do on accept button pressed
                    $('#importClub-Search').combogrid('reset');
                });
        });
    	addTooltip($('#importClub-selectBtn').linkbutton(),'<?php _e("Use selected club and update club info"); ?>');
    	addTooltip($('#importClub-ignoreBtn').linkbutton(),'<?php _e("Ignore entry. do not import into database"); ?>');
        addTooltip($('#importClub-clearBtn').linkbutton(),'<?php _e("Clear club selection"); ?>');
        $('#importClub-clearBtn').linkbutton().bind('click',function() {
            $('#importClub-header').form('reset'); // empty form
        });

        // combogrid de seleccion de clubes/paises
        $('#importClub-Search').combogrid({
            panelWidth: 350,
            panelHeight: 200,
            delay: 500,
            idField: 'ID',
            textField: 'Nombre',
            url: '/agility/server/database/clubFunctions.php',
            queryParams: { Operation:'enumerate' },
            method: 'get',
            mode: 'remote',
            columns: [[
                {field:'ID',hidden:'true'},
                {field:'Nombre',    title:'<?php _e("Club name"); ?>',  width:'55%',align:'left'},
                {field:'Provincia', title:'<?php _e("Province"); ?>',   width:'25%',align:'right'},
                {field:'Pais',      title:'<?php _e("Country"); ?>',    width:'15%',align:'right'}
            ]],
            onBeforeLoad:function(params) {
                params.Federation=workingData.federation;
                params.Operation='enumerate';
                return true;
            },
            multiple: false,
            fitColumns: true,
            singleSelect: true
        });

</script>
