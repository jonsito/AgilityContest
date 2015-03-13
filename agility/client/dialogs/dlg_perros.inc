<!-- 
dlg_perros.inc

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
 
    <!-- FORMULARIO DE ALTA/MODIFICACION DE LA BBDD DE PERROS -->
    <div id="perros-dialog" style="width:550px;padding:10px 20px">
        <div class="ftitle">Informaci&oacute;n del perro</div>
        <form id="perros-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input id="perros-Nombre" 
                	name="Nombre" 
                	type="text" 
                	class="easyui-validatebox"/>
                <input id="perros-ID" name="ID" type="hidden" /> <!-- hide idperro, as only used for edit and is readonly -->
                <input id="perros-Operation" name="Operation" type="hidden" /> <!-- inser/update/delete -->
            </div>
            <div class="fitem">
                <label for="Raza">Raza:</label>
                <input id="perros-Raza" class="easyui-validatebox" name="Raza" type="text" />
            </div>
            <div class="fitem">
                <label for="LOE_RRC">Num. LOE / RRC:</label>
                <input id="perros-LOE_RRC" class="easyui-validatebox" name="LOE_RRC" type="text" />
            </div>
            <div class="fitem">
                <label for="Licencia">Num. Licencia:</label>
                <input id="perros-Licencia" class="easyui-validatebox" name="Licencia" type="text" />
            </div>
            <div class="fitem">
                <label for="Categoria">Categor&iacute;a:</label>
                <select id="perros-Categorias_Perro" 
                		name="Categoria" 
                		class="easyui-combobox" 
                		style="width:155px"></select>
            </div>
            <div class="fitem">
                <label for="Grado">Grado:</label>
                <select id="perros-Grados_Perro" 
                		name="Grado" 
                		class="easyui-combobox" 
                		style="width:155px"></select>
            </div>
            <div class="fitem">
                <label for="Guia">Gu&iacute;a:</label>
                <select id="perros-Guia" name="Guia" style="width:225px" class="easyui-combogrid"></select>
            	<a id="perros-newGuiaBtn" href="#" class="easyui-linkbutton" 
            		data-options="iconCls:'icon-users'" style="float:right">Nuevo Gu&iacute;a</a>
            </div>
        </form>
        <span id="perros-warning">
        <em>Aviso:</em><br/>
        Los cambios en los datos del perro se reflejar&aacute;n<br/>
        en todas las pruebas y jornadas en las que est&eacute; inscrito<br/>
        y no hayan sido marcadas como cerradas
        </span>
    </div>

    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    <div id="perros-dlg-buttons">
        <a id="perros-okBtn" href="#" class="easyui-linkbutton" 
        	data-options="iconCls:'icon-ok'" onclick="saveDog()">Guardar</a>
        <a id="perros-cancelBtn" href="#" class="easyui-linkbutton" 
        	data-options="iconCls:'icon-cancel'" onclick="javascript:$('#perros-dialog').dialog('close')">Cancelar</a>
    </div>
    
    <script type="text/javascript">

    // datos del formulario de nuevo/edit perros
    // - declaracion del formulario
    $('#perros-form').form({
    	onLoadSuccess: function(data) {
    	    $('#perros-Operation').val('update'); // mark "update" operation
    	    // stupid combogrid that doesn't display right data after form load
    	    $('#perros-Guia').combogrid('clear');
    	    $('#perros-Guia').combogrid('setValue',data.Guia);
    	    $('#perros-Guia').combogrid('setText',data.NombreGuia);
    	}
	});
    // - botones
	addTooltip($('#perros-okBtn').linkbutton(),"Aceptar datos del perro y actualizar la BBDD");
	addTooltip($('#perros-cancelBtn').linkbutton(),"Cancelar edicion. Cerrar ventana");
	addTooltip($('#perros-newGuiaBtn').linkbutton(),"Dar de alta un nuevo gu&iacute;a");
	$('#perros-newGuiaBtn').bind('click',function() {
        newGuia(
            $('#perros-Guia').combogrid('getText'), // default name
            function() { 
                // on accept clear box, to allow reload from database
            	// $('#perros-Guia').combogrid('reset');
            	// line bellow doesn't work: no easy way to retrieve guia ID from newly created guia
            	$('#perros-Guia').combogrid('setValue',$('#guias-ID').val());
            	$('#perros-Guia').combogrid('setText',$('#guias-Nombre').val());
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
    	url:'/agility/server/database/dogFunctions.php?Operation=grados',
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
		url:'/agility/server/database/dogFunctions.php?Operation=categorias'
    });
    $('#perros-Guia').combogrid({
		panelWidth: 350,
		panelHeight: 200,
		idField: 'ID',
		textField: 'Nombre',
		url: '/agility/server/database/guiaFunctions.php?Operation=enumerate',
		method: 'get',
		mode: 'remote',
		required: true,
		columns: [[
		    {field:'ID', hidden:true},
			{field:'Nombre',title:'Nombre del gu&iacute;a',width:80,align:'right'},
		    {field:'Club', hidden:true},
			{field:'NombreClub',title:'Club',width:40,align:'right'},
		]],
		multiple: false,
		fitColumns: true,
		selectOnNavigation: false
    });
    </script>