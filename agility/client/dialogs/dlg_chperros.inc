<!-- 
dlg_chperros.inc

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

<!-- FORMULARIO DE REASIGNACION DE UN PERRO-->
    <div id="chperros-dialog" style="width:550px;height:400px;padding:10px 20px">
        <div id="chperros-title" class="ftitle">Reasignaci&oacute;n de un perro</div>
        <form id="chperros-header">
        	<div class="fitem">
                <label for="Search">Buscar: </label>
                <select id="chperros-Search" name="Search" style="width:250px"></select>&nbsp;
                <a id="chperros-clearBtn" href="#" class="easyui-linkbutton"
                	data-options="iconCls: 'icon-undo'">Limpiar</a>
        	</div>
        </form>
        <hr/>
        <form id="chperros-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input id="chperros-ID" name="ID" type="hidden" /> <!-- dog id -->
                <input id="chperros-Federation" name="Federation" type="hidden" />
                <input id="chperros-Nombre" name="Nombre" class="easyui-validatebox" /> <!-- dog name -->
                <input id="chperros-Operation" name="Operation" type="hidden" value="update"/> <!-- inser/update/delete -->
                <input id="chperros-Guia" name="Guia" type="hidden" /> <!-- id of the guia -->
                <input id="chperros-newGuia" name="NewGuia" type="hidden" /> <!-- id original of the guia -->
            </div>
            <div class="fitem">
                <label for="Raza">Raza:</label>
                <input id="chperros-Raza" class="easyui-validatebox" name="Raza" type="text" />
            </div>
            <div class="fitem">
                <label for="LOE_RRC">Num. LOE / RRC:</label>
                <input id="chperros-LOE_RRC" class="easyui-validatebox" name="LOE_RRC" type="text" />
            </div>
            <div class="fitem">
                <label for="Licencia">Num. Licencia:</label>
                <input id="chperros-Licencia" class="easyui-validatebox" name="Licencia" type="text" />
            </div>
            <div class="fitem">
                <label for="Categoria">Categor&iacute;a:</label>
                <select id="chperros-Categorias_Perro" 
                		name="Categoria" 
                		class="easyui-combobox" 
                		style="width:155px" ></select>
            </div>
            <div class="fitem">
                <label for="Grado">Grado:</label>
                <select id="chperros-Grados_Perro" 
                		name="Grado" 
                		class="easyui-combobox" 
                		style="width:155px" ></select>
            </div>
        </form>
    </div>
    
    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    <div id="chperros-dlg-buttons" style="display:inline-block">
    	<span style="float:left">
        	<a id="chperros-newBtn" href="#" class="easyui-linkbutton" onclick="saveChDog()"
        		data-options="iconCls:'icon-dog'">Nuevo</a>
        </span>
        <span style="float:right">
        	<a id="chperros-okBtn" href="#" class="easyui-linkbutton" onclick="assignDog()"
        		data-options="iconCls:'icon-ok'">Reasignar</a>
        	<a id="chperros-cancelBtn" href="#" class="easyui-linkbutton"  
        		onclick="javascript:$('#chperros-dialog').dialog('close')"
        		data-options="iconCls:'icon-cancel'">Cancelar</a>
        </span>
    </div>
    
    <script type="text/javascript">

    // datos del formulario de nuevo/edit perros
    // - declaracion del formulario
    $('#chperros-form').form();
    // - botones
    addTooltip($('#chperros-clearBtn').linkbutton(),"Limpiar formulario de reasignacion de perro");
    addTooltip($('#chperros-newBtn').linkbutton(),"Crear un nuevo perro para este guía en la base de datos");
    addTooltip($('#chperros-okBtn').linkbutton(),"Aceptar datos y actualizar la BBDD");
    addTooltip($('#chperros-cancelBtn').linkbutton(),"Cancelar asignacion. Cerrar ventana");
    $('#chperros-clearBtn').bind('click',function() {
        $('#chperros-form').form('clear'); 
        $('#chperros-Search').combogrid({ 'setValue' : '' });
		$('#chperros-Operation').val('insert'); // mark operation as 'insert'
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
		textField: 'Nombre',
		url: '/agility/server/database/dogFunctions.php',
		queryParams: { Operation:'enumerate', Federation: workingData.federation },
		method: 'get',
		mode: 'remote',
		columns: [[
			{field:'ID',hidden:'true'},
			{field:'Federation',hidden:'true'},
			{field:'Nombre',title:'Perro',width:20,align:'right'},
			{field:'Categoria',title:'Cat.',width:10,align:'center'},
			{field:'Grado',title:'Grado',width:10,align:'center'},
			{field:'NombreGuia',title:'Guia',width:40,align:'right'},
			{field:'NombreClub',title:'Club',width:20,align:'right'},
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
    	url:'/agility/server/database/dogFunctions.php?Operation=grados',
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
		url:'/agility/server/database/dogFunctions.php?Operation=categorias'
    });
    </script>