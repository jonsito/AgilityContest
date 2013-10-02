    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE GUIAS -->
    <div id="guias-dialog" class="easyui-dialog" style="width:450px;height:350px;padding:10px 20px"
            closed="true" buttons="#guias-dlg-buttons">
        <div class="ftitle">Informaci&oacute;n del guia</div>
        <form id="guias-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input id="guias-Nombre" 
                	name="Nombre" 
                	type="text" 
                	class="easyui-validatebox" 
                	required="true"
                	style="width:300px" />
                <input id="guias-Viejo" name="Viejo" type="hidden" /> <!-- used to allow operator change guia's name -->
                <input id="clubes-Operation" name="Operation" type="hidden" /> <!-- inser/update/delete -->
            </div>
            <div class="fitem">
                <label for="Email">Correo electr&oacute;nico:</label>
                <input id="guias-Email" name="Email" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Telefono">Tel&eacute;fono:</label>
                <input id="guias-Telefono" class="easyui-validatebox" name="Telefono" type="text" />
            </div>
            <div class="fitem">
                <label for="Club">Club:</label>
                <select id="guias-Clubes" name="Club" class="easyui-combogrid" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Observaciones">Observaciones:</label>
                <input id="guias-Observaciones" name="Observaciones" type="textarea" style="height:50px;width:300px";/>
            </div>
        </form>
    	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    	<div id="guias-dlg-buttons">
    	    <a id="guias-okBtn" href="#" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveGuia()">Guardar</a>
    	    <a id="guias-cancelBtn" href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#guias-dialog').dialog('close')">Cancelar</a>
    	</div>
    </div>

    <script type="text/javascript">
        // datos del formulario de nuevo/edit guia
        // - declaracion del formulario
        $('#guias-form').form();
        // - botones
        $('#guias-okBtn').linkbutton();        
        $('#guias-okBtn').tooltip({
            position: 'top',
            content: '<span style="color:#000">Aceptar datos y registrarlos en la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
        $('#guias-cancelBtn').linkbutton();        
        $('#guias-cancelBtn').tooltip({
            position: 'top',
            content: '<span style="color:#000">Anular operaci&oacute;n. Cerrar ventana</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
        
        // campos del formulario
        $('#guias-dialog').dialog();
        $('#guias-Nombre').validatebox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#guias-Email').validatebox({
            required: false,
            validType: 'email'
        });
        $('#guias-Clubes').combogrid({
			panelWidth: 350,
			panelHeight: 200,
			idField: 'Nombre',
			textField: 'Nombre',
			url: 'database/enumerate_Clubes.php',
			method: 'get',
			mode: 'remote',
			required: true,
			columns: [[
    			{field:'Nombre',title:'Nombre del club',width:80,align:'right'},
    			{field:'Provincia',title:'Provincia',width:40,align:'right'},
			]],
			multiple: false,
			fitColumns: true,
			selectOnNavigation: false
        });
</script>