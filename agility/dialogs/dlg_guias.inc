    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE GUIAS -->
    <div id="guias-dialog" class="easyui-dialog" style="width:500px;height:350px;padding:10px 20px">
        <div class="ftitle">Informaci&oacute;n del guia</div>
        <form id="guias-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input id="guias-Nombre" 
                	name="Nombre" 
                	type="text" 
                	class="easyui-validatebox" 
                	style="width:300px" />
                <input id="guias-Viejo" name="Viejo" type="hidden" /> <!-- used to allow operator change guia's name -->
                <input id="guias-Parent" name="Parent" type="hidden" /> <!-- used to store parent datagrid -->
                <input id="guias-Operation" name="Operation" type="hidden" /> <!-- inser/update/delete -->
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
                <select id="guias-Club" name="Club" class="easyui-combogrid" style="width:200px"></select>
            	<a id="guias-newClubBtn" href="#" class="easyui-linkbutton" style="float:right">Nuevo Club</a>
            </div>
            <div class="fitem">
                <label for="Observaciones">Comentarios:</label>
                <textarea id="guias-Observaciones" name="Observaciones" style="height:50px;width:300px"></textarea>
            </div>
        </form>
    	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    	<div id="guias-dlg-buttons">
    	    <a id="guias-okBtn" href="#" class="easyui-linkbutton" onclick="saveGuia()">Guardar</a>
    	    <a id="guias-cancelBtn" href="#" class="easyui-linkbutton" onclick="javascript:$('#guias-dialog').dialog('close')">Cancelar</a>
    	</div>
    </div>

    <script type="text/javascript">
        // datos del formulario de nuevo/edit guia
        // - declaracion del formulario
        $('#guias-form').form();
        // - botones
        $('#guias-okBtn').linkbutton({iconCls:'icon-ok'}).tooltip({
            position: 'top',
            content: '<span style="color:#000">Aceptar datos y registrarlos en la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
        $('#guias-cancelBtn').linkbutton({iconCls:'icon-cancel'}).tooltip({
            position: 'top',
            content: '<span style="color:#000">Anular operaci&oacute;n. Cerrar ventana</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
        $('#guias-newClubBtn').linkbutton({iconCls:'icon-flag'}).tooltip({
            position: 'top',
            content: '<span style="color:#000">Registrar nuevo club</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
        $('#guias-newClubBtn').bind('click',function() {
            $('#guias-Club').combogrid({ 'setValue' : '' });
            newClub();
        });
        
        // campos del formulario
        $('#guias-dialog').dialog({
            closed:true,
            buttons: '#guias-dlg-buttons',
            iconCls: 'icon-users'
        });
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
			idField: 'Nombre',
			textField: 'Nombre',
			url: 'database/clubFunctions.php?Operation=enumerate',
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