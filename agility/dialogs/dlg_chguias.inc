    <!-- FORMULARIO DE REASIGNACION DE GUIAS -->
    <div id="chguias-dialog" class="easyui-dialog" style="width:550px;height:350px;padding:10px 20px">
        <div id="chguias-title" class="ftitle">Reasignaci&oacute;n de un guia</div>
        <form id="chguias-header">
        	<div class="fitem">
                <label for="Search">Buscar: </label>
                <select id="chguias-Search" name="Search" style="width:200px"></select>&nbsp;
                <a id="chguias-clearBtn" href="#" class="easyui-linkbutton"
                	data-options="iconCls: 'icon-undo'">Limpiar</a>
        	</div>
        </form>
        <hr/>
        <form id="chguias-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input id="chguias-ID" name="ID" type="hidden" value="" /> <!-- Guia ID -->
                <input id="chguias-Nombre" name="Nombre" class="easyui-validatebox" style="width:300px" />
                <input id="chguias-Club" name="Club" type="hidden" value="" /> <!-- Club ID -->
                <input id="chguias-newClub" name="newClub" type="hidden" value="" /> <!-- new Club ID -->
                <input id="chguias-Operation" name="Operation" type="hidden" value=""/> <!-- inser/update/delete -->
            </div>
            <div class="fitem">
                <label for="Email">Correo electr&oacute;nico:</label>
                <input id="chguias-Email" name="Email" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Telefono">Tel&eacute;fono:</label>
                <input id="chguias-Telefono" class="easyui-validatebox" name="Telefono" type="text" />
            </div>
            <div class="fitem">
                <label for="Observaciones">Observaciones:</label>
                <textarea id="chguias-Observaciones" name="Observaciones" style="height:50px;width:300px"></textarea>
            </div>
        </form>
    	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    	<div id="chguias-dlg-buttons">
    	    <a id="chguias-okBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls:'icon-ok'" onclick="assignGuia()">Guardar</a>
    	    <a id="chguias-cancelBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls:'icon-cancel'" onclick="javascript:$('#chguias-dialog').dialog('close')">Cancelar</a>
    	</div>
    </div>

    <script type="text/javascript">
        // datos del formulario de nuevo/edit guia
        // - declaracion del formulario
        $('#chguias-form').form();
        // - botones
    	addTooltip($('#chguias-clearBtn').linkbutton(),"Limpiar formulario");
    	addTooltip($('#chguias-cancelBtn').linkbutton(),"Anular operaci&oacute;n. Cerrar ventana");
    	addTooltip($('#chguias-okBtn').linkbutton(),"Aceptar datos y registrarlos en la BBDD");
    	$('#chguias-clearBtn').bind('click',function() {
    	    $('#chguias-form').form('clear'); 
    	    $('#chguias-Search').combogrid({ 'setValue' : '' });
			$('#chguias-Operation').val('insert'); // mark operation as 'insert'
			$('#chguias-Club').val($('#chguias-newClub').val()); // restore "Club" field
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
    		url: 'server/database/guiaFunctions.php?Operation=enumerate',
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
    	        $('#chguias-form').form('load','server/database/guiaFunctions.php?Operation=getbyid&ID='+id); // load form with json retrieved data
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