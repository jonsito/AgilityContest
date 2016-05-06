<!-- 
dlg_chperros.inc

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
    <div id="chperros-dialog" style="width:550px;height:420px;padding:10px 20px">
        <div id="chperros-title" class="ftitle"><?php _e('Dog re-assignation'); ?></div>
        <form id="chperros-header">
        	<div class="fitem">
                <label for="chperros-Search"><?php _e('Search'); ?>: </label>
                <select id="chperros-Search" name="Search" style="width:250px"></select>&nbsp;
                <a id="chperros-clearBtn" href="#" class="easyui-linkbutton"
                	data-options="iconCls: 'icon-undo'"><?php _e('Clear'); ?></a>
        	</div>
        </form>
        <hr/>
        <form id="chperros-form" method="get" novalidate>
            <div class="fitem">
                <label for="chperros-Nombre"><?php _e('Name'); ?>:</label>
                <input id="chperros-ID" name="ID" type="hidden" /> <!-- dog id -->
                <input id="chperros-Federation" name="Federation" type="hidden" />
                <input id="chperros-Nombre" name="Nombre" class="easyui-validatebox" /> <!-- dog name -->
                <input id="chperros-Operation" name="Operation" type="hidden" value="update"/> <!-- inser/update/delete -->
                <input id="chperros-Guia" name="Guia" type="hidden" /> <!-- id of the guia -->
                <input id="chperros-newGuia" name="NewGuia" type="hidden" /> <!-- id original of the guia -->
            </div>
            <div class="fitem">
                <label for="chperros-NombreLargo"><?php _e('Pedigree Name'); ?>:</label>
                <input id="chperros-NombreLargo" class="easyui-validatebox" name="NombreLargo" type="text" style="width:350px;"/>
            </div>
            <div class="fitem">
                <label for="chperros-Genero"><?php _e('Gender'); ?>:</label>
                <select id="chperros-Genero" name="Genero" class="easyui-combobox" style="width:100px">
                    <option value="-" selected="selected">&nbsp;</option>
                    <option value="M"><?php _e('Male'); ?></option>
                    <option value="F"><?php _e('Female'); ?></option>
                </select>
            </div>
            <div class="fitem">
                <label for="chperros-Raza"><?php _e('Breed'); ?>:</label>
                <input id="chperros-Raza" class="easyui-validatebox" name="Raza" type="text" />
            </div>
            <div class="fitem">
                <label for="chperros-LOE_RRC"><?php _e('Kennel Club ID'); ?>:</label>
                <input id="chperros-LOE_RRC" class="easyui-validatebox" name="LOE_RRC" type="text" />
            </div>
            <div class="fitem">
                <label for="chperros-Licencia"><?php _e('Ag. License'); ?>:</label>
                <input id="chperros-Licencia" class="easyui-validatebox" name="Licencia" type="text" />
            </div>
            <div class="fitem">
                <label for="chperros-Categorias_Perro"><?php _e('Category'); ?>:</label>
                <select id="chperros-Categorias_Perro" 
                		name="Categoria" 
                		class="easyui-combobox" 
                		style="width:155px" ></select>
            </div>
            <div class="fitem">
                <label for="chperros-Grados_Perro"><?php _e('Grade'); ?>:</label>
                <select id="chperros-Grados_Perro" 
                		name="Grado" 
                		class="easyui-combobox" 
                		style="width:155px" ></select>
            </div>
        </form>
        <input id="chperros-parent" type="hidden" value="" />
    </div>
    
    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    <div id="chperros-dlg-buttons" style="display:inline-block">
    	<span style="float:left">
        	<a id="chperros-newBtn" href="#" class="easyui-linkbutton" onclick="saveChDog()"
        		data-options="iconCls:'icon-dog'"><?php _e('New'); ?></a>
        </span>
        <span style="float:right">
        	<a id="chperros-okBtn" href="#" class="easyui-linkbutton" onclick="assignDog()"
        		data-options="iconCls:'icon-ok'"><?php _e('Re-assign'); ?></a>
        	<a id="chperros-cancelBtn" href="#" class="easyui-linkbutton"  
        		onclick="$('#chperros-dialog').dialog('close')"
        		data-options="iconCls:'icon-cancel'"><?php _e('Cancel'); ?></a>
        </span>
    </div>
    
    <script type="text/javascript">

    // datos del formulario de nuevo/edit perros
    // - declaracion del formulario
    $('#chperros-form').form();
    // - botones
    addTooltip($('#chperros-clearBtn').linkbutton(),'<?php _e("Clear dog search to be re-assignated form"); ?>');
    addTooltip($('#chperros-newBtn').linkbutton(),'<?php _e("Declare a new dog belonging to selected handler"); ?>');
    addTooltip($('#chperros-okBtn').linkbutton(),'<?php _e("Re-assign select dog to belong this handler"); ?>');
    addTooltip($('#chperros-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
    $('#chperros-clearBtn').bind('click',function() {
        $('#chperros-header').form('clear'); // empty form
        $('#chperros-form').form('reset'); // restore to initial values
    });
    
    // campos del formulario
    $('#chperros-dialog').dialog({
    	closed: true,
    	buttons: '#chperros-dlg-buttons',
        iconCls: 'icon-dog'/*,
        onBeforeOpen: function() { 
            var grads="/agility/server/database/dogFunctions.php?Operation=grados&Federation="+workingData.federation;
            var cats="/agility/server/database/dogFunctions.php?Operation=categorias&Federation="+workingData.federation;
            $('#chperros-Grados_Perro').combobox('reload',grads);
    		$('#chperros-Categorias_Perro').combobox('reload',cats);
        }
        */
    });
    $('#chperros-dialog').dialog('dialog').attr('tabIndex','-1').bind('keydown',function(e){
    	if (e.keyCode == 27){ $('#chperros-dialog').dialog('close');
    	}
    });
    $('#chperros-Search').combogrid({
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
		singleSelect: true,
		selectOnNavigation: false ,
		onSelect: function(index,row) {
			var idperro=row.ID;
			if (!idperro) return;
	        $('#chperros-form').form('load','/agility/server/database/dogFunctions.php?Operation=getbyidperro&ID='+idperro); // load form with json retrieved data
		}
	});
    $('#chperros-Nombre').validatebox({
        required: true,
        validType: 'length[1,255]'
    });
    $('#chperros-Grados_Perro').combobox({
		panelHeight: 'auto',
    	valueField:'Grado',
    	textField:'Comentarios',
    	method: 'get',
    	mode: 'remote',
		required: true,
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
				if (row.selected){  
    				// alert('selected value is: '+row.Grado);
					$(this).combobox('setValue',row.Grado);
				}
			}
		}
    });
    $('#chperros-Categorias_Perro').combobox({
		panelHeight: 'auto',
		valueField:'Categoria',
		textField:'Observaciones',
		method: 'get',
		mode: 'remote',
		required: true,
        url:'/agility/server/database/dogFunctions.php',
        queryParams: {
        Operation:'categorias',
            Federation: workingData.federation
        }
    });
    </script>