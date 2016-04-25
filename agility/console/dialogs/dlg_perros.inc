<!-- 
dlg_perros.inc

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

    <!-- FORMULARIO DE ALTA/MODIFICACION DE LA BBDD DE PERROS -->
    <div id="perros-dialog" style="width:550px;padding:10px 20px">
        <div class="ftitle"><?php _e('Dog information'); ?></div>
        <form id="perros-form" method="get" novalidate>
            <div class="fitem">
                <label for="perros-Nombre"><?php _e('Name'); ?>:</label>
                <input id="perros-Nombre" 
                	name="Nombre" 
                	type="text" 
                	class="easyui-validatebox"/>
                <input id="perros-Federation" name="Federation" type="hidden" />
                <input id="perros-ID" name="ID" type="hidden" /> <!-- hide idperro, as only used for edit and is readonly -->
                <input id="perros-Operation" name="Operation" type="hidden" value="insert"/> <!-- inser/update/delete -->
            </div>
            <div class="fitem">
                <label for="perros-NombreLargo"><?php _e('Pedigree Name'); ?>:</label>
                <input id="perros-NombreLargo" class="easyui-validatebox" name="NombreLargo" type="text" style="width:350px;"/>
            </div>
            <div class="fitem">
                <label for="perros-Genero"><?php _e('Gender'); ?>:</label>
                <select id="perros-Genero" name="Genero" class="easyui-combobox" style="width:100px">
                    <option value="-" selected="selected">&nbsp;</option>
                    <option value="M"><?php _e('Male'); ?></option>
                    <option value="F"><?php _e('Female'); ?></option>
                </select>
            </div>
            <div class="fitem">
                <label for="perros-Raza"><?php _e('Breed'); ?>:</label>
                <input id="perros-Raza" class="easyui-validatebox" name="Raza" type="text" />
            </div>
            <div class="fitem">
                <label for="perros-LOE_RRC"><?php _e('Kennel Club ID'); ?>:</label>
                <input id="perros-LOE_RRC" class="easyui-validatebox" name="LOE_RRC" type="text" />
            </div>
            <div class="fitem">
                <label for="perros-Licencia"><?php _e('Ag. License'); ?>:</label>
                <input id="perros-Licencia" class="easyui-validatebox" name="Licencia" type="text" />
            </div>
            <div class="fitem">
                <label for="perros-Categoria"><?php _e('Category'); ?>:</label>
                <select id="perros-Categorias_Perro" 
                		name="Categoria" 
                		class="easyui-combobox" 
                		style="width:155px"></select>
            </div>
            <div class="fitem">
                <label for="perros-Grado"><?php _e('Grade'); ?>:</label>
                <select id="perros-Grados_Perro" 
                		name="Grado" 
                		class="easyui-combobox" 
                		style="width:155px"></select>
            </div>
            <div class="fitem">
                <label for="perros-Guia"><?php _e('Handler'); ?>:</label>
                <select id="perros-Guia" name="Guia" style="width:225px" class="easyui-combogrid"></select>
            	<a id="perros-newGuiaBtn" href="#" class="easyui-linkbutton" 
            		data-options="iconCls:'icon-users'" style="float:right"><?php _e('New handler'); ?></a>
            </div>
        </form>
        <span id="perros-warning">
        <em><?php _e('Notice'); ?>:</em><br/>
			<?php _e('Changes on Grade or Category for a dog'); ?><br/>
			<?php _e('will affect to every non closed contest and journeys'); ?><br/>
			<?php _e('where dog have been inscribed'); ?>
        </span>
    </div>

    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    <div id="perros-dlg-buttons">
        <a id="perros-okBtn" href="#" class="easyui-linkbutton" 
        	data-options="iconCls:'icon-ok'" onclick="saveDog()"><?php _e('Save'); ?></a>
        <a id="perros-cancelBtn" href="#" class="easyui-linkbutton" 
        	data-options="iconCls:'icon-cancel'" onclick="$('#perros-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
    </div>
    
    <script type="text/javascript">

    // datos del formulario de nuevo/edit perros
    // - declaracion del formulario
    $('#perros-form').form({
    	onLoadSuccess: function(data) {
            var pg=$('#perros-Guia');
    	    $('#perros-Operation').val('update'); // mark "update" operation
    	    // stupid combogrid that doesn't display right data after form load
    	    pg.combogrid('clear');
    	    pg.combogrid('setValue',data.Guia);
    	    pg.combogrid('setText',data.NombreGuia);
    	}
	});
    // - botones
	addTooltip($('#perros-okBtn').linkbutton(),'<?php _e("Register dog information. Update database"); ?>');
	addTooltip($('#perros-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
	addTooltip($('#perros-newGuiaBtn').linkbutton(),'<?php _e("Register a new handler"); ?>');
	$('#perros-newGuiaBtn').bind('click',function() {
        newGuia(
            $('#perros-Guia').combogrid('getText'), // default name
            function() { // on accept repaint
            	$('#perros-Guia').combogrid('reset');
            });
    });
    
    // campos del formulario
    $('#perros-dialog').dialog({
        closed:true,
        buttons:'#perros-dlg-buttons',
        iconCls: 'icon-dog'
    });

    $('#perros-dialog').dialog('dialog').attr('tabIndex','-1').bind('keydown',function(e){
    	if (e.keyCode == 27){ $('#perros-dialog').dialog('close');
    	}
    });
    $('#perros-Nombre').validatebox({
        required: true,
        validType: 'length[1,255]'
    });
    $('#perros-Grados_Perro').combobox({
		panelHeight: 'auto',
    	valueField:'Grado',
    	textField:'Comentarios',
    	method: 'get',
    	mode: 'remote',
    	url:'/agility/server/database/dogFunctions.php',
        queryParams: {
            Operation:'grados',
            Federation: workingData.federation
        },
    	// TODO: this should work. study why doesn't
		onLoadSuccess: function(data){
			for(var i=0; i<data.length; i++){
				var row = data[i];
				// the row with 'selected' property set to true will be acted as the selected row
				if (row.selected==1){  
    				// alert('selected value is: '+row.Grado);
					$(this).combobox('setValue',row.Grado);
				}
			}
		}
    });

    $('#perros-Categorias_Perro').combobox({
		panelHeight: 'auto',
		valueField:'Categoria',
		textField:'Observaciones',
		method: 'get',
		mode: 'remote',
		url:'/agility/server/database/dogFunctions.php',
        queryParams: {
            Operation:'categorias',
            Federation: workingData.federation
        }
    });

    $('#perros-Guia').combogrid({
		panelWidth: 350,
		panelHeight: 200,
		idField: 'ID',
		textField: 'Nombre',
		url: '/agility/server/database/guiaFunctions.php',
    	queryParams: {Operation:'enumerate',Federation:workingData.federation},
		method: 'get',
		mode: 'remote',
		required: true,
		columns: [[
		    {field:'ID', hidden:true},
			{field:'Nombre',title:'<?php _e('Handler name'); ?>',width:80,align:'right'},
		    {field:'Club', hidden:true},
			{field:'NombreClub',title:'<?php _e('Club'); ?>',width:40,align:'right'}
		]],
		multiple: false,
		fitColumns: true,
		selectOnNavigation: false
    });
    </script>
