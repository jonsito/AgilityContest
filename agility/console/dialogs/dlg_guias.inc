 <!-- 
dlg_hguias.inc

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

    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE GUIAS -->
    <div id="guias-dialog" style="width:500px;height:350px;padding:10px 20px">
        <div class="ftitle"><?php _e('Handler information'); ?></div>
        <form id="guias-form" method="get" novalidate>
            <div class="fitem">
                <label for="guias-Nombre"><?php _e('Name'); ?>:</label>
                <input id="guias-Nombre" name="Nombre" type="text" class="easyui-textbox" style="width:300px" />
                <input id="guias-Federation" name="Federation" type="hidden" />
                <input id="guias-ID" name="ID" type="hidden" /> <!-- primary key for update -->
                <input id="guias-Parent" name="Parent" type="hidden" /> <!-- used to store parent datagrid -->
                <input id="guias-Operation" name="Operation" type="hidden" /> <!-- inser/update/delete -->
            </div>
            <div class="fitem">
                <label for="guias-Email"><?php _e('Electronic mail'); ?>:</label>
                <input id="guias-Email" name="Email" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="guias-Telefono"><?php _e('Telephone'); ?>:</label>
                <input id="guias-Telefono" name="Telefono" type="text" class="easyui-textbox" />
            </div>
            <div class="fitem">
                <label for="guias-Club"><?php _e('Club'); ?>:</label>
                <select id="guias-Club" name="Club" style="width:200px"></select>
            	<a id="guias-newClubBtn" href="#" class="easyui-linkbutton" 
            		data-options="iconCls:'icon-flag'" style="float:right"><?php _e('New club'); ?></a>
            </div>
            <div class="fitem">
                <label for="guias-Observaciones"><?php _e('Comments'); ?>:</label>
                <textarea id="guias-Observaciones" name="Observaciones" style="height:50px;width:300px"></textarea>
            </div>
        </form>
    </div>

    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    <div id="guias-dlg-buttons">
        <a id="guias-okBtn" href="#" class="easyui-linkbutton" 
        	data-options="iconCls:'icon-ok'" onclick="saveGuia()"><?php _e('Save'); ?></a>
        <a id="guias-cancelBtn" href="#" class="easyui-linkbutton" 
        	data-options="iconCls:'icon-cancel'" onclick="$('#guias-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
    </div>
    
    <script type="text/javascript">
        // datos del formulario de nuevo/edit guia
        // - declaracion del formulario
        $('#guias-form').form({
        	onLoadSuccess: function(data) {
                var gc= $('#guias-Club');
                gc.combogrid('clear');
        	    gc.combogrid('setValue',data.Club);
        	    gc.combogrid('setText',data.NombreClub);
        	}   
        });
        // - botones
        
		addTooltip($('#guias-okBtn').linkbutton(),'<?php _e("Aceptar handler data. Store into database"); ?>');
		addTooltip($('#guias-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
		addTooltip($('#guias-newClubBtn').linkbutton(),'<?php _e("Register new club into database"); ?>');
        $('#guias-newClubBtn').bind('click',function() {
            newClub(
                $('#guias-Parent').val(),
                $('#guias-Club').combogrid('getText'), // default name
                function() { // what to do on accept button pressed
                	$('#guias-Club').combogrid('reset');
                });    
        });
        $('#guias-dialog').dialog({
        	closed:true,
        	buttons:'#guias-dlg-buttons',
        	iconCls:'icon-users'
        });
        // campos del formulario
        $('#guias-dialog').dialog('dialog').attr('tabIndex','-1').bind('keydown',function(e){
        	if (e.keyCode == 27){ $('#guias-dialog').dialog('close');
        	}
        });
        $('#guias-Nombre').validatebox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#guias-Email').validatebox({
            required: false,
            validType: 'email'
        });
        $('#guias-Club').combogrid({
			panelWidth: 350,
			panelHeight: 200,
			idField: 'ID',
			textField: 'Nombre',
			url: '/agility/server/database/clubFunctions.php',
	    	queryParams: {Operation:'enumerate',Federation:workingData.federation},
			method: 'get',
			mode: 'remote',
			required: true,
			columns: [[
			    {field:'ID', hidden:true},
    			{field:'Nombre',title:'<?php _e('Club name'); ?>',width:80,align:'right'},
    			{field:'Provincia',title:'<?php _e('Province'); ?>',width:40,align:'right'}
			]],
			multiple: false,
			fitColumns: true,
			selectOnNavigation: false
        });
</script>