<!-- 
import_perros.inc

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

<!-- FORMULARIO DE REASIGNACION DE UN PERRO-->
    <div id="importperros-dialog" style="width:550px;height:420px;padding:10px 20px">
        <div id="importperros-title" class="ftitle"><?php _e('Dog re-assignation'); ?></div>
        <form id="importperros-header">
        	<div class="fitem">
                <label for="importperros-Search"><?php _e('Search'); ?>: </label>
                <select id="importperros-Search" name="Search" style="width:250px"></select>&nbsp;
                <a id="importperros-clearBtn" href="#" class="easyui-linkbutton"
                	data-options="iconCls: 'icon-undo'"><?php _e('Clear'); ?></a>
        	</div>
        </form>
    </div>
    
    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    <div id="importperros-dlg-buttons" style="display:inline-block">
    	<span style="float:left">
        	<a id="importperros-newBtn" href="#" class="easyui-linkbutton" onclick="importAction('dogs','create')"
        		data-options="iconCls:'icon-dog'"><?php _e('Create'); ?></a>
        </span>
        <span style="float:right">
        	<a id="importperros-okBtn" href="#" class="easyui-linkbutton" onclick="importAction('dogs','update')"
        		data-options="iconCls:'icon-ok'"><?php _e('Select'); ?></a>
        	<a id="importperros-cancelBtn" href="#" class="easyui-linkbutton" onclick="importAction('dogs','ignore')"
        		data-options="iconCls:'icon-cancel'"><?php _e('Cancel'); ?></a>
        </span>
    </div>
    
    <script type="text/javascript">

    // datos del formulario de nuevo/edit perros
    // - declaracion del formulario
    $('#importperros-form').form();
    // - botones
    addTooltip($('#importperros-newBtn').linkbutton(),'<?php _e("Create a new dog with Excel provided data"); ?>');
    addTooltip($('#importperros-okBtn').linkbutton(),'<?php _e("Use selected dog to be used in requested Excel import data"); ?>');
    addTooltip($('#importperros-cancelBtn').linkbutton(),'<?php _e("Ignore data. Do not import Excel dog entry into database"); ?>');
    addTooltip($('#importperros-clearBtn').linkbutton(),'<?php _e("Clear selection"); ?>');
    $('#importperros-clearBtn').bind('click',function() {
        $('#importperros-header').form('reset'); // restore to initial values
    });
    
    // campos del formulario
    $('#importperros-dialog').dialog({
    	closed: true,
    	buttons: '#importperros-dlg-buttons',
        iconCls: 'icon-dog'
    });
    $('#importperros-Search').combogrid({
		panelWidth: 350,
		panelHeight: 200,
		idField: 'ID',
        delay: 500,
		textField: 'Nombre',
		url: '/agility/server/database/dogFunctions.php',
		queryParams: { Operation:'enumerate', Federation: workingData.federation },
		method: 'get',
		mode: 'remote',
		columns: [[
			{field:'ID',hidden:'true'},
			{field:'Federation',hidden:'true'},
			{field:'Nombre',title:'<?php _e('Dog'); ?>',width:20,align:'right'},
			{field:'Categoria',title:'<?php _e('Cat'); ?>.',width:10,align:'center',formatter:formatCategoria},
			{field:'Grado',title:'<?php _e('Grade'); ?>',width:10,align:'center',formatter:formatGrado},
			{field:'NombreGuia',title:'<?php _e('Handler'); ?>',width:40,align:'right'},
			{field:'NombreClub',title:'<?php _e('Club'); ?>',width:20,align:'right'}
		]],
		multiple: false,
		fitColumns: true,
		singleSelect: true
	});
    </script>