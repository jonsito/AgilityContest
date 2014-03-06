 
    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE CLUBES -->
    <div id="clubes-dialog" class="easyui-dialog" style="width:550px;height:600px;padding:10px 20px">
        <div class="ftitle">Informaci&oacute;n del club</div>
        <form id="clubes-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input id="clubes-Nombre" 
                	name="Nombre" 
                	type="text" 
                	class="easyui-validatebox" 
                	style="width:350px" />
                <input id="clubes-Viejo" name="Viejo" type="hidden" /> <!-- used to allow operator change guia's name -->
                <input id="clubes-Operation" name="Operation" type="hidden" /> <!-- inser/update/delete -->
            </div>
            <div class="fitem">
                <label for="Direccion1">Direcci&oacute;n 1</label>
                <input id="clubes-Direccion1" class="easyui-validatebox" name="Direccion1" type="text" style="width:350px"/>
            </div>
            <div class="fitem">
                <label for="Direccion2">Direcci&oacute;n 2</label>
                <input id="clubes-Direccion2" class="easyui-validatebox" name="Direccion2" type="text" style="width:350px"/>
            </div>
            <div class="fitem">
                <label for="Provincia">Provincia</label>
                <select id="clubes-Provincia" name="Provincia" class="easyui-combogrid" style="width:200px"></select>
                <input id="Clubes-Logo"
                   	type="image"
                	name="Logo" 
                	src="/agility/database/clubFunctions.php?Operation=logo&Nombre=rsce"
                	width="120" style="float:right; margin-right:50px"/>
            </div>
            <div class="fitem">
                <label for="Contacto1">Contacto 1</label>
                <input id="clubes-Contacto1" class="easyui-validatebox" name="Contacto1" type="text" style="width:200px"/>
            </div>
            <div class="fitem">
                <label for="Contacto2">Contacto 2</label>
                <input id="clubes-Contacto2" class="easyui-validatebox" name="Contacto2" type="text" style="width:200px"/>
            </div>
            <div class="fitem">
                <label for="Contacto3">Contacto 3</label>
                <input id="clubes-Contacto3" class="easyui-validatebox" name="Contacto3" type="text" style="width:200px"/>
            </div>
            <div class="fitem">
                <label for="GPS">Coordenadas GPS</label>
                <input id="clubes-GPS" class="easyui-validatebox" name="GPS" type="text" style="width:200px"/>
            </div>
            <div class="fitem">
                <label for="Web">P&aacute;gina Web</label>
                <input id="clubes-Web" class="easyui-validatebox" name="Web" type="text" style="width:350px"/>
            </div>
            <div class="fitem">
                <label for="Email">Correo electr&oacute;nico:</label>
                <input id="clubes-Email" name="Email" class="easyui-validatebox" type="text" style="width:350px"/>
            </div>
            <div class="fitem">
                <label for="Facebook">Cuenta de Facebook:</label>
                <input id="clubes-Facebook" class="easyui-validatebox" name="Facebook" type="text" style="width:350px"/>
            </div>
            <div class="fitem">
                <label for="Google">Cuenta de Google+:</label>
                <input id="clubes-Google" class="easyui-validatebox" name="Google" type="text" style="width:350px"/>
            </div>
            <div class="fitem">
                <label for="Twitter">Cuenta de Twitter:</label>
                <input id="clubes-Twitter" class="easyui-validatebox" name="Twitter" type="text" style="width:350px"/>
            </div>
            <div class="fitem">
                <label for="Observaciones">Observaciones:</label>
                <textarea id="clubes-Observaciones" name="Observaciones" style="height:50px;width:350px"></textarea>
            </div>
            <div class="fitem">
                <label for="Baja">Baja del club:</label>
                <input id="clubes-Baja" name="Baja" class="easyui-checkbox" type="checkbox" value="1" />
            </div>
        </form>    
        
    	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    	<div id="clubes-dlg-buttons">
    	    <a id="clubes-okBtn" href="#" class="easyui-linkbutton"  onclick="saveClub()">Guardar</a>
    	    <a id="clubes-cancelBtn" href="#" class="easyui-linkbutton" onclick="javascript:$('#clubes-dialog').dialog('close')">Cancelar</a>
    	</div>
    </div>
    
    <script type="text/javascript">
        // datos del formulario de nuevo/edit club
        // - declaracion del formulario
        $('#clubes-form').form();
        // - botones
        $('#clubes-okBtn').linkbutton( {iconCls: 'icon-ok' } ).tooltip({
            position: 'top',
            content: '<span style="color:#000">Aceptar datos y registrarlos en la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
        $('#clubes-cancelBtn').linkbutton( {iconCls: 'icon-cancel' } ).tooltip({
            position: 'top',
            content: '<span style="color:#000">Anular operaci&oacute;n. Cerrar ventana</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
        $('#clubes-Provincia').combogrid({
			panelWidth: 300,
			panelHeight: 200,
			idField: 'Provincia',
			textField: 'Provincia',
			url: 'database/enumerate_Provincias.php?Operation=select',
			method: 'get',
			mode: 'remote',
			required: true,
			columns: [[
    			{field:'Provincia',title:'Provincia',width:20,align:'right'},
    			{field:'Comunidad',title:'Comunidad Autonoma',width:40,align:'right'},
			]],
			multiple: false,
			fitColumns: true,
			selectOnNavigation: false
        });
        // campos del formulario
        $('#clubes-dialog').dialog({
			closed: true,
			buttons: '#clubes-dlg-buttons',
			iconCls: 'icon-flag'
        });
        $('#clubes-dialog').dialog('dialog').attr('tabIndex','-1').bind('keydown',function(e){
        	if (e.keyCode == 27){ $('#clubes-dialog').dialog('close');
        	}
        });
        $('#clubes-Nombre').validatebox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#clubes-Email').validatebox({
            required: false,
            validType: 'email'
        });
    </script>
