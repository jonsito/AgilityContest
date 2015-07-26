<!-- 
dlg_chguias.inc

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

<!-- FORMULARIO DE REASIGNACION DE GUIAS -->
    <div id="chguias-dialog" style="width:550px;height:350px;padding:10px 20px">
        <div id="chguias-title" class="ftitle">Reasignaci&oacute;n de un gu&iacute;a</div>
        <form id="chguias-header">
        	<div class="fitem">
                <label for="chguias-Search">Buscar: </label>
                <select id="chguias-Search" name="Search" style="width:200px"></select>&nbsp;
                <a id="chguias-clearBtn" href="#" class="easyui-linkbutton"
                	data-options="iconCls: 'icon-undo'">Limpiar</a>
        	</div>
        </form>
        <hr/>
        <form id="chguias-form" method="get" novalidate>
            <div class="fitem">
                <label for="chguias-Nombre">Nombre:</label>
                <input id="chguias-Federation" name="Federation" type="hidden" />
                <input id="chguias-ID" name="ID" type="hidden" value="" /> <!-- Guia ID -->
                <input id="chguias-Nombre" name="Nombre" style="width:300px" />
                <input id="chguias-Club" name="Club" type="hidden" value="" /> <!-- Club ID -->
                <input id="chguias-newClub" name="newClub" type="hidden" value="" /> <!-- new Club ID -->
                <input id="chguias-Operation" name="Operation" type="hidden" value=""/> <!-- inser/update/delete -->
            </div>
            <div class="fitem">
                <label for="chguias-Email">Correo electr&oacute;nico:</label>
                <input id="chguias-Email" name="Email" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="chguias-Telefono">Tel&eacute;fono:</label>
                <input id="chguias-Telefono" name="Telefono" type="text" />
            </div>
            <div class="fitem">
                <label for="chguias-Observaciones">Observaciones:</label>
                <textarea id="chguias-Observaciones" name="Observaciones" style="height:50px;width:300px"></textarea>
            </div>
        </form>
    </div>

   	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
   	<div id="chguias-dlg-buttons" style="display:inline-block">
   	    <span style="float:left">
        	<a id="chguias-newBtn" href="#" class="easyui-linkbutton" onclick="saveChGuia()"
        		data-options="iconCls:'icon-users'">Nuevo</a>
        </span>
        <span style="float:right">
   	    	<a id="chguias-okBtn" href="#" class="easyui-linkbutton" 
   	    		data-options="iconCls:'icon-ok'" onclick="assignGuia()">Reasignar</a>
   	    	<a id="chguias-cancelBtn" href="#" class="easyui-linkbutton" 
   	    		data-options="iconCls:'icon-cancel'" onclick="$('#chguias-dialog').dialog('close')">Cancelar</a>
   	    </span>
   	</div>
   	
    <script type="text/javascript">
        // datos del formulario de nuevo/edit guia
        // - declaracion del formulario
        $('#chguias-form').form();
        // - botones
    	addTooltip($('#chguias-clearBtn').linkbutton(),"Limpiar formulario");
    	addTooltip($('#chguias-cancelBtn').linkbutton(),"Anular operaci&oacute;n. Cerrar ventana");
    	addTooltip($('#chguias-newBtn').linkbutton(),"Crear nuevo miembro del club con los datos especificados");
    	addTooltip($('#chguias-okBtn').linkbutton(),"Re-asignar el guia con los datos especificados al club");
    	$('#chguias-clearBtn').bind('click',function() {
    	    $('#chguias-header').form('clear'); // emtpy
    	    $('#chguias-form').form('reset'); // restore to original values 
    	});
        
        // campos del formulario

        $('#chguias-Nombre').validatebox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#chguias-Email').validatebox({
            required: false,
            validType: 'email'
        });
        $('#chguias-Search').combogrid({
    		panelWidth: 350,
    		panelHeight: 200,
    		idField: 'ID',
    		textField: 'Nombre',
    		url: '/agility/server/database/guiaFunctions.php',
            queryParams: { Operation:'enumerate', Federation: workingData.federation },
    		method: 'get',
    		mode: 'remote',
    		columns: [[
    	    	{field:'ID',hidden:true},
    			{field:'Nombre',title:'Nombre',sortable:true,width:60,align:'right'},
    			{field:'Club',hidden:true},
    			{field:'NombreClub',title:'Club',sortable:true,width:40,align:'right'}
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
            closed: true,
            buttons: '#chguias-dlg-buttons',
            iconCls: 'icon-users'
    	});
    	$('#chguias-dialog').dialog('dialog').attr('tabIndex','-1').bind('keydown',function(e){
    		if (e.keyCode == 27){ $('#chguias-dialog').dialog('close');	}
    	});
</script>