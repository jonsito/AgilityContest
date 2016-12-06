<!-- 
import_handlers.inc

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

<!-- FORMULARIO DE REASIGNACION DE GUIAS -->
    <div id="importhandlers-dialog" style="width:550px;height:350px;padding:10px 20px">
        <div id="importhandlers-title" class="ftitle"><?php _e('Handler re-asignation'); ?></div>
        <form id="importhandlers-header">
        	<div class="fitem">
                <label for="importhandlers-Search"><?php _e('Search'); ?>: </label>
                <select id="importhandlers-Search" name="Search" style="width:200px"></select>&nbsp;
                <a id="importhandlers-clearBtn" href="#" class="easyui-linkbutton"
                	data-options="iconCls: 'icon-undo'"><?php _e('Clear'); ?></a>
        	</div>
        </form>
    </div>

   	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
   	<div id="importhandlers-dlg-buttons" style="display:inline-block">
   	    <span style="float:left">
        	<a id="importhandlers-newBtn" href="#" class="easyui-linkbutton" onclick="importAction('handlers','create');"
        		data-options="iconCls:'icon-users'"><?php _e('Create'); ?></a>
        </span>
        <span style="float:right">
   	    	<a id="importhandlers-okBtn" href="#" class="easyui-linkbutton"
   	    		data-options="iconCls:'icon-ok'" onclick="importAction('handlers','update');"><?php _e('Select'); ?></a>
   	    	<a id="importhandlers-cancelBtn" href="#" class="easyui-linkbutton"
   	    		data-options="iconCls:'icon-cancel'" onclick="importAction('handlers','ignore');"><?php _e('Ignore'); ?></a>
   	    </span>
   	</div>
   	
    <script type="text/javascript">
        // datos del formulario de nuevo/edit guia
        // - declaracion del formulario
        $('#importhandlers-form').form();
        // - botones
    	addTooltip($('#importhandlers-clearBtn').linkbutton(),'<?php _e("Clear selection"); ?>');
    	addTooltip($('#importhandlers-okBtn').linkbutton(),'<?php _e("Use selected handler to be used in imported data"); ?>');
    	addTooltip($('#importhandlers-newBtn').linkbutton(),'<?php _e("Use Excel imported data to create a new handler"); ?>');
    	addTooltip($('#importhandlers-cancelBtn').linkbutton(),'<?php _e("Ignore handler and their entries from imported Excel data"); ?>');
    	$('#importhandlers-clearBtn').bind('click',function() {
    	    $('#importhandlers-header').form('reset'); //  restore to original values
    	});

        $('#importhandlers-Search').combogrid({
    		panelWidth: 350,
    		panelHeight: 200,
    		idField: 'ID',
            delay: 500,
    		textField: 'Nombre',
    		url: '/agility/server/database/guiaFunctions.php',
            queryParams: { Operation:'enumerate', Federation: workingData.federation },
    		method: 'get',
    		mode: 'remote',
    		columns: [[
    	    	{field:'ID',hidden:true},
    			{field:'Nombre',title:'<?php _e('Name'); ?>',sortable:true,width:60,align:'right'},
    			{field:'Club',hidden:true},
    			{field:'NombreClub',title:'<?php _e('Club'); ?>',sortable:true,width:40,align:'right'}
    		]],
    		multiple: false,
    		fitColumns: true,
    		singleSelect: true,
    		selectOnNavigation: false ,
    		onSelect: function(index,row) {
    			var id=row.ID;
    			if (id<=0) return;
    	        $('#importhandlers-form').form('load','/agility/server/database/guiaFunctions.php?Operation=getbyid&ID='+id); // load form with json retrieved data
    			$('#importhandlers-Club').val($('#importhandlers-newClub').val()); // restore "Club" field
    		}
    	});

        // datos de la ventana
        $('#importhandlers-dialog').dialog( {
            closed: true,
            buttons: '#importhandlers-dlg-buttons',
            iconCls: 'icon-users'
    	});
</script>