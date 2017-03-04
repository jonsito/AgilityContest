<!-- 
dlg_chguias.inc

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

<!-- FORMULARIO DE REASIGNACION DE GUIAS -->
    <div id="chguias-dialog" style="padding:10px 20px">
        <div id="chguias-title" class="ftitle"><?php _e('Handler re-asignation'); ?></div>
        <form id="chguias-header">
        	<div class="fitem">
                <label for="chguias-Search"><?php _e('Search'); ?>: </label>
                <select id="chguias-Search" name="Search" style="width:200px"></select>&nbsp;
                <a id="chguias-clearBtn" href="#" class="easyui-linkbutton"
                	data-options="iconCls: 'icon-undo'"><?php _e('Clear'); ?></a>
        	</div>
        </form>
        <hr/>
        <form id="chguias-form" method="get" novalidate>
            <div class="fitem">
                <label for="chguias-Nombre"><?php _e('Name'); ?>:</label>
                <input id="chguias-Federation" name="Federation" type="hidden" />
                <input id="chguias-ID" name="ID" type="hidden" value="" /> <!-- Guia ID -->
                <input id="chguias-Nombre" name="Nombre" style="width:300px" />
                <input id="chguias-Club" name="Club" type="hidden" value="" /> <!-- Club ID -->
                <input id="chguias-newClub" name="newClub" type="hidden" value="" /> <!-- new Club ID -->
                <input id="chguias-Operation" name="Operation" type="hidden" value=""/> <!-- inser/update/delete -->
            </div>
            <div class="fitem">
                <label for="chguias-Email"><?php _e('Electronic mail'); ?>:</label>
                <input id="chguias-Email" name="Email" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="chguias-Telefono"><?php _e('Telephone'); ?>:</label>
                <input id="chguias-Telefono" name="Telefono" type="text" style="width:200px"/>
            </div>
            <div class="fitem">
                <label for="chguias-Observaciones"><?php _e('Comments'); ?>:</label>
                <textarea id="chguias-Observaciones" name="Observaciones" style="height:50px;width:300px"></textarea>
            </div>
        </form>
        <input id="chguias-parent" type="hidden" value="" />
    </div>

   	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
   	<div id="chguias-dlg-buttons" style="display:inline-block">
   	    <span style="float:left">
        	<a id="chguias-newBtn" href="#" class="easyui-linkbutton" onclick="saveChGuia()"
        		data-options="iconCls:'icon-users'"><?php _e('New'); ?></a>
        </span>
        <span style="float:right">
   	    	<a id="chguias-okBtn" href="#" class="easyui-linkbutton" 
   	    		data-options="iconCls:'icon-ok'" onclick="assignGuia()"><?php _e('Reassign'); ?></a>
   	    	<a id="chguias-cancelBtn" href="#" class="easyui-linkbutton" 
   	    		data-options="iconCls:'icon-cancel'" onclick="$('#chguias-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
   	    </span>
   	</div>
   	
    <script type="text/javascript">
        // datos del formulario de nuevo/edit guia
        // - declaracion del formulario
        $('#chguias-form').form();
        // - botones
    	addTooltip($('#chguias-clearBtn').linkbutton(),'<?php _e("Clear form"); ?>');
    	addTooltip($('#chguias-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
    	addTooltip($('#chguias-newBtn').linkbutton(),'<?php _e("Use provided data to create a new club member"); ?>');
    	addTooltip($('#chguias-okBtn').linkbutton(),'<?php _e("Re-assign selected handler to be member of club"); ?>');
    	$('#chguias-clearBtn').bind('click',function() {
    	    $('#chguias-header').form('clear'); // emtpy
    	    $('#chguias-form').form('reset'); // restore to original values 
    	});
        
        // campos del formulario

        $('#chguias-Nombre').textbox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#chguias-Email').textbox({
            required: false,
            validType: 'email'
        });
        $('#chguias-Telefono').textbox();
        $('#chguias-Search').combogrid({
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
    	        $('#chguias-form').form('load','/agility/server/database/guiaFunctions.php?Operation=getbyid&ID='+id); // load form with json retrieved data
    			$('#chguias-Club').val($('#chguias-newClub').val()); // restore "Club" field
    		}
    	});

        // datos de la ventana
        $('#chguias-dialog').dialog( {
            width:550,
            height:'auto',
            closed: true,
            buttons: '#chguias-dlg-buttons',
            iconCls: 'icon-users'
    	}).dialog('dialog').attr('tabIndex','-1').bind('keydown',function(e){
    		if (e.keyCode == 27){ $('#chguias-dialog').dialog('close');	}
    	});
</script>