<!-- 
dlg_pruebas.inc

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

   <!-- FORMULARIO DE DECLARACION Y MODIFICACION DE PRUEBAS -->
    <div id="pruebas-dialog" style="width:500px;height:450px;padding:10px 20px">
        <div class="ftitle"><?php _e('Contest information'); ?></div>
        <form id="pruebas-form" method="get" novalidate>
            <div class="fitem">
                <label for="pruebas-Nombre" style="width:325px"><?php _e('Name of this contest'); ?>:</label>
                <input id="pruebas-Nombre" 
                	name="Nombre" 
                	type="text" 
                	class="easyui-validatebox" 
                	style="width:350px" />
                <input id="pruebas-ID" name="ID" type="hidden" /> <!-- primary key -->
                <input id="pruebas-Operation" name="Operation" type="hidden" /> <!-- used to store operation type -->
                <input id="pruebas-Cerrada" name="Cerrada" type="hidden" /> <!-- preserve "closed" status -->
            </div>
            <div>&nbsp;<br /></div>
            <div class="fitem">
            	<label for="pruebas-Federation"><?php _e('Federation'); ?>:</label>
                <select id="pruebas-Federation" name="RSCE" style="width:200px"></select>
            	<span style="float:right">
            		<input id="pruebas-Selectiva" type="checkbox" name="Selectiva" value="1"/><?php _e('Selective'); ?>
            	</span>
            </div>
            <div>&nbsp;<br /></div>
            <div class="fitem">
                <label for="pruebas-Club"><?php _e('Club'); ?>:</label>
                <select id="pruebas-Club" name="Club" style="width:175px"></select>
                <a id="pruebas-newClubBtn" href="#" class="easyui-linkbutton" 
                	data-options="iconCls:'icon-flag'" style="float:right"><?php _e('New Club'); ?></a>
            </div>
            <div class="fitem">
                <label for="pruebas-Ubicacion"><?php _e('Event location'); ?></label>
                <input id="pruebas-Ubicacion" name="Ubicacion" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="pruebas-Triptico"><?php _e('Triptych URL'); ?></label>
                <input id="pruebas-Triptico" name="Triptico" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="pruebas-Cartel"><?php _e('Brochure URL'); ?></label>
                <input id="pruebas-Cartel" name="Cartel" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="pruebas-Observaciones"><?php _e('Comments'); ?>:</label>
                <textarea id="pruebas-Observaciones" name="Observaciones" style="height:50px;width:250px"></textarea>
            </div>
        </form>
    </div>

    
	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
	<div id="pruebas-dlg-buttons">
       	<a id="pruebas-okBtn" href="#" class="easyui-linkbutton" 
       		data-options="iconCls:'icon-ok'" onclick="savePrueba()"><?php _e('Save'); ?></a>
       	<a id="pruebas-cancelBtn" href="#" class="easyui-linkbutton" 
       		data-options="iconCls:'icon-cancel'" onclick="$('#pruebas-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
    </div>
    
    <script type="text/javascript">
    // datos del formulario de nuevo/edit prueba
    // - declaracion del formulario
    $('#pruebas-form').form({
    	onLoadSuccess: function(data) {
        	// alert(JSON.stringify(data));
    	    $('#pruebas-Operation').val('update');
    	    $('#pruebas-Selectiva').prop('checked',(data.Selectiva == 1));
    	    hasInscripciones(
    	    	    data.ID,
    	    	    function(flag) {
                        $('#pruebas-Federation').combogrid('readonly',flag);
    	    		}
    	    	);
    	}
    });
    
    // - botones
	addTooltip($('#pruebas-okBtn').linkbutton(),'<?php _e("Accept data. Register contest into database"); ?>');
	addTooltip($('#pruebas-cancelBtn').linkbutton(),"<?php _e('Cancel operation. Close window'); ?>");
	addTooltip($('#pruebas-newClubBtn').linkbutton(),'<?php _e("Register new club into database"); ?>');
    $('#pruebas-newClubBtn').bind('click',function() {
        $('#pruebas-Club').combogrid({ 'setValue' : '' });
        newClub();
    });
    
    // campos del formulario
    $('#pruebas-dialog').dialog({
        closed:true,
        buttons:'#pruebas-dlg-buttons'
    });
    $('#pruebas-Nombre').validatebox({
        required: true,
        validType: 'length[1,255]'
    });
    $('#pruebas-Triptico').validatebox({
        required: false,
        validType: 'url'
    });
    $('#pruebas-Cartel').validatebox({
        required: false,
        validType: 'url'
    });
    $('#pruebas-Club').combogrid({
		panelWidth: 350,
		panelHeight: 200,
		idField: 'ID',
		textField: 'Nombre',
		url: '/agility/server/database/clubFunctions.php?Operation=enumerate',
		method: 'get',
		mode: 'remote',
		required: true,
		columns: [[
		    {field:'ID',hidden:true}, 
			{field:'Nombre',title:'<?php _e('Club name'); ?>',width:80,align:'right'},
			{field:'Provincia',title:'<?php _e('Province'); ?>',width:40,align:'right'}
		]],
		multiple: false,
		fitColumns: true,
		selectOnNavigation: false
    });

    $('#pruebas-Federation').combogrid({
        panelWidth: 300,
        panelHeight: 150,
        idField: 'ID',
        textField: 'LongName',
        url: '/agility/modules/moduleFunctions.php?Operation=enumerate',
        method: 'get',
        mode: 'remote',
        required: true,
        multiple: false,
        fitColumns: true,
        singleSelect: true,
        editable: false,  // to disable tablet keyboard popup
        selectOnNavigation: true, // let use cursor keys to interactive select
        columns: [[
            {field:'ID',  title:'<?php _e('ID'); ?>',width:'20',align:'left'},
            {field:'Name',hidden:true},
            {field:'LongName',        title:'<?php _e('Name'); ?>',width:'250',align:'right'},
            {field:'Logo',hidden:true},
            {field:'ParentLogo',hidden:true}
        ]]
    });
    </script>