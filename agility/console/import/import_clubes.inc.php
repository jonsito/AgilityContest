 <!-- 
import_clubes.inc

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
 <div id="importclubes-dialog" style="width:550px;height:600px;padding:10px 20px" >

    <div class="ftitle">
        <?php _e('Club data Import'); ?>
    </div>
     <form id="importclubes-header">
         <p id="importclubes_header-Text"> </p> <!-- to be filled -->
         <div class="fitem">
             <label for="importclubes-Search"><?php _e('Search'); ?>: </label>
             <select id="importclubes-Search" name="Search" style="width:250px"></select>&nbsp;
             <a id="importclubes-clearBtn" href="#" class="easyui-linkbutton"
                data-options="iconCls: 'icon-brush'"><?php _e('Clear'); ?></a>
         </div>
     </form>
</div>  
        
<!-- BOTONES DE IMPORTAR / SELECCIONAR / IGNORAR DEL CUADRO DE DIALOGO -->
<div id="importclubes-dlg-buttons" style="display:inline-block">
    <span style="float:left">
        <a id="importclubes-newBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-new'"><?php _e('New'); ?></a>
    </span>
    <span style="float:right">
        <a id="importclubes-selectBtn" href="#" class="easyui-linkbutton"
            data-options="iconCls: 'icon-ok'" onclick="importAction('clubs','update');"><?php _e('Select'); ?></a>
        <a id="importclubes-ignoreBtn" href="#" class="easyui-linkbutton"
            data-options="iconCls:'icon-cancel'" onclick="importAction('clubs','ignore');"><?php _e('Ignore'); ?></a>
    </span>
</div>
    
<script type="text/javascript">

        $('#importclubes-dialog').dialog( {
            closed:true,
            modal:true,
            buttons:'#importclubes-dlg-buttons',
            iconCls:'icon-flag'
        } );

        // - declaracion del formulario
        $('#importclubes-form').form();
        // - botones
    	addTooltip($('#importclubes-newBtn').linkbutton(),'<?php _e("Open dialog to create new club"); ?>');
        $('#importclubes-newBtn').bind('click',function() {
            newClub(
                null, // no datagrid to refresh
                $('#importclubes-Search').combogrid('getText'), // default name
                function() { // what to do on accept button pressed
                    $('#importclubes-Search').combogrid('reset');
                });
        });
    	addTooltip($('#importclubes-selectBtn').linkbutton(),'<?php _e("Use selected club and update club info"); ?>');
    	addTooltip($('#importclubes-ignoreBtn').linkbutton(),'<?php _e("Ignore entry. do not import into database"); ?>');
        addTooltip($('#importclubes-clearBtn').linkbutton(),'<?php _e("Clear club selection"); ?>');
        $('#importclubes-clearBtn').linkbutton().bind('click',function() {
            $('#importclubes-header').form('reset'); // empty form
        });

        // combogrid de seleccion de clubes/paises
        $('#importclubes-Search').combogrid({
            panelWidth: 350,
            panelHeight: 200,
            delay: 500,
            idField: 'ID',
            textField: 'Nombre',
            url: '/agility/server/database/clubFunctions.php',
            queryParams: { Operation:'enumerate', Federation: workingData.federation },
            method: 'get',
            mode: 'remote',
            columns: [[
                {field:'ID',hidden:'true'},
                {field:'Nombre',    title:'<?php _e("Club name"); ?>',  width:'60%',align:'left'},
                {field:'Provincia', title:'<?php _e("Province"); ?>',   width:'25%',align:'right'},
                {field:'Pais',      title:'<?php _e("Country"); ?>',    width:'15%',align:'right'}
            ]],
            multiple: false,
            fitColumns: true,
            singleSelect: true
        });

</script>
