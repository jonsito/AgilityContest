
   <!-- FORMULARIO DE DECLARACION Y MODIFICACION DE PRUEBAS -->
    <div id="pruebas-dialog" class="easyui-dialog" style="width:475px;height:425px;padding:10px 20px">
        <div class="ftitle">Informaci&oacute;n de la prueba</div>
        <form id="pruebas-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre" style="width:300px">Denominaci&oacute;n de la prueba:</label>
                <input id="pruebas-Nombre" 
                	name="Nombre" 
                	type="text" 
                	class="easyui-validatebox" 
                	style="width:325px" />
                <input id="pruebas-ID" name="ID" type="hidden" /> <!-- primary key -->
                <input id="pruebas-Operation" name="Operation" type="hidden" /> <!-- used to store operation type -->
                <input id="pruebas-Cerrada" name="Cerrada" type="hidden" /> <!-- preserve "closed" status -->
            </div>
            <div class="fitem">
                <label for="Club">Club:</label>
                <select id="pruebas-Club" name="Club" style="width:200px"></select>
                <a id="pruebas-newClubBtn" href="#" class="easyui-linkbutton" 
                	data-options="iconCls:'icon-flag'" style="float:right">Nuevo Club</a>
            </div>
            <div class="fitem">
                <label for="Ubicacion">Lugar de realizaci&oacute;n</label>
                <input id="pruebas-Ubicacion" name="Ubicacion" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Triptico">URL del tr&iacute;ptico</label>
                <input id="pruebas-Triptico" name="Triptico" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Cartel">URL del cartel</label>
                <input id="pruebas-Cartel" name="Cartel" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Observaciones">Observaciones:</label>
                <textarea id="pruebas-Observaciones" name="Observaciones" style="height:50px;width:250px"></textarea>
            </div>
        </form>
    
		<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
		<div id="pruebas-dlg-buttons">
        	<a id="pruebas-okBtn" href="#" class="easyui-linkbutton" 
        		data-options="iconCls:'icon-ok'" onclick="savePrueba()">Guardar</a>
        	<a id="pruebas-cancelBtn" href="#" class="easyui-linkbutton" 
        		data-options="iconCls:'icon-cancel'" onclick="javascript:$('#pruebas-dialog').dialog('close')">Cancelar</a>
    	</div>
    </div>

    <script type="text/javascript">
    // datos del formulario de nuevo/edit prueba
    // - declaracion del formulario
    $('#pruebas-form').form({
    	onLoadSuccess: function(data) {
        	// alert(JSON.stringify(data));
    	    $('#pruebas-Operation').val('update');
    	}
    });
    
    // - botones
	addTooltip($('#pruebas-okBtn').linkbutton(),"Aceptar datos. Registrar prueba en la BBDD");
	addTooltip($('#pruebas-cancelBtn').linkbutton(),"Anular operaci&oacute;n. Cerrar ventana");
	addTooltip($('#pruebas-newClubBtn').linkbutton(),"Registrar un nuevo club en la BBDD");
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
		url: 'server/database/clubFunctions.php?Operation=enumerate',
		method: 'get',
		mode: 'remote',
		required: true,
		columns: [[
		    {field:'ID',hidden:true}, 
			{field:'Nombre',title:'Nombre del club',width:80,align:'right'},
			{field:'Provincia',title:'Provincia',width:40,align:'right'},
		]],
		multiple: false,
		fitColumns: true,
		selectOnNavigation: false
    });

    </script>