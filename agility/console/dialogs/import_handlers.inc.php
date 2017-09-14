<!-- 
import_handlers.inc

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

<!-- FORMULARIO DE IMPORTACION DE GUIAS -->
    <div id="importGuia-dialog" class="easyui-dialog" style="width:550px;height:auto;padding:10px 20px;"
         data-options="modal:true,closable:false,closed:true,buttons:'#importGuia-dlg-buttons',iconCls:'icon-users'">

        <div id="importGuia-title" class="ftitle"><?php _e('Handler import'); ?></div>
        <p><span id="importGuia-Text"></span></p>
        <form id="importGuia-header">
        	<div class="fitem">
                <label for="importGuia-Search"><?php _e('Search'); ?>: </label>
                <select id="importGuia-Search" name="Search" style="width:200px"></select>&nbsp;
                <a id="importGuia-clearBtn" href="#" class="easyui-linkbutton"
                	data-options="iconCls: 'icon-undo'"><?php _e('Clear'); ?></a>
                <input type="hidden" id="importGuia-HandlerID" value="0"/>
        	</div>
        </form>
    </div>

   	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
   	<div id="importGuia-dlg-buttons" style="display:inline-block">
   	    <span style="float:left">
        	<a id="importGuia-newBtn" href="#" class="easyui-linkbutton"
                onclick="importAction('Guia','create',$('#importGuia-HandlerID').val(),$('#importGuia-Search').combogrid('getValue'));"
        		data-options="iconCls:'icon-users'"><?php _e('Create'); ?></a>
        </span>
        <span style="float:right">
   	    	<a id="importGuia-okBtn" href="#" class="easyui-linkbutton"
                onclick="importAction('Guia','update',$('#importGuia-HandlerID').val(),$('#importGuia-Search').combogrid('getValue'));"
                data-options="iconCls:'icon-ok'"><?php _e('Select'); ?></a>
   	    	<a id="importGuia-cancelBtn" href="#" class="easyui-linkbutton"
                onclick="importAction('Guia','ignore',$('#importGuia-HandlerID').val(),$('#importGuia-Search').combogrid('getValue'));"
   	    		data-options="iconCls:'icon-cancel'"><?php _e('Ignore'); ?></a>
   	    </span>
   	</div>
   	
    <script type="text/javascript">
        // datos del formulario de nuevo/edit guia
        // - declaracion del formulario
        $('#importGuia-form').form();
        // - botones
    	addTooltip($('#importGuia-clearBtn').linkbutton(),'<?php _e("Clear selection"); ?>');
    	addTooltip($('#importGuia-okBtn').linkbutton(),'<?php _e("Use selected handler to be used in imported data"); ?>');
    	addTooltip($('#importGuia-newBtn').linkbutton(),'<?php _e("Use Excel imported data to create a new handler"); ?>');
    	addTooltip($('#importGuia-cancelBtn').linkbutton(),'<?php _e("Ignore handler and their entries from imported Excel data"); ?>');
    	$('#importGuia-clearBtn').bind('click',function() {
    	    $('#importGuia-header').form('reset'); //  restore to original values
    	});

        $('#importGuia-Search').combogrid({
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
    	        $('#importGuia-form').form('load','/agility/server/database/guiaFunctions.php?Operation=getbyid&ID='+id); // load form with json retrieved data
    			$('#importGuia-Club').val($('#importGuia-newClub').val()); // restore "Club" field
    		}
    	});

        // datos de la ventana
        $('#importGuia-dialog').dialog( {
            modal:true,
            closable: false,
            closed: true,
            buttons: '#importGuia-dlg-buttons',
            iconCls: 'icon-users'
    	});
</script>