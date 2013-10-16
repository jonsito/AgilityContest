    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE JUECES -->
    <div id="jueces-dialog" class="easyui-dialog" style="width:450px;height:450px;padding:10px 20px">
        <div class="ftitle">Informaci&oacute;n del juez</div>
        <form id="jueces-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input id="jueces-Nombre" 
                	name="Nombre" 
                	type="text" 
                	class="easyui-validatebox" 
                	style="width:250px" />
                <input id="jueces-Viejo" name="Viejo" type="hidden" /> <!-- used to allow operator change juez name -->
                <input id="jueces-Operation" name="Operation" type="hidden" /> <!-- to store insert/update/delete operation -->
            </div>
            <div class="fitem">
                <label for="Direccion1">Direccion 1:</label>
                <input id="jueces-Direccion1" class="easyui-validatebox" name="Direccion1" type="text" style="width:250px" />
            </div>
            <div class="fitem">
                <label for="Direccion2">Direccion 2:</label>
                <input id="jueces-Direccion2" class="easyui-validatebox" name="Direccion2" type="text" style="width:250px" />
            </div>
            <div class="fitem">
                <label for="Telefono">Tel&eacute;fono:</label>
                <input id="jueces-Telefono" class="easyui-validatebox" name="Telefono" type="text" />
            </div>
            <div class="fitem">
                <label for="Internacional">Juez internacional:</label>
                <input id="jueces-Internacional" name="Internacional" class="easyui-checkbox" type="checkbox" value="1" />
            </div>
            <div class="fitem">
                <label for="Practicas">Juez en pr&aacute;cticas:</label>
                <input id="jueces-Practicas" name="Practicas" class="easyui-checkbox" type="checkbox" value="1" />
            </div>
            <div class="fitem">
                <label for="Email">Correo electr&oacute;nico:</label>
                <input id="jueces-Email" name="Email" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Observaciones">Observaciones:</label>
                <textarea id="jueces-Observaciones" name="Observaciones" style="height:50px;width:300px"></textarea>
            </div>
        </form>
    	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    	<div id="jueces-dlg-buttons">
    	    <a id="jueces-okBtn" href="#" class="easyui-linkbutton" onclick="saveJuez()">Guardar</a>
    	    <a id="jueces-cancelBtn" href="#" class="easyui-linkbutton" onclick="javascript:$('#jueces-dialog').dialog('close')">Cancelar</a>
    	</div>
    </div>
    
	<script type="text/javascript">
        // datos del formulario de nuevo/edit jueces
        // - declaracion del formulario
        $('#jueces-form').form();
        // - botones
        $('#jueces-okBtn').linkbutton({iconCls:'icon-ok'}).tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Aceptar datos y actualizar la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        $('#jueces-cancelBtn').linkbutton({iconCls:'icon-cancel'}).tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Cancelar edicion. Cerrar ventana</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        // campos del formulario
        $('#jueces-dialog').dialog({
            closed:true,
            buttons:'#jueces-dlg-buttons'
        });
        $('#jueces-Nombre').validatebox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#jueces-Email').validatebox({
            required: false,
            validType: 'email'
        });
        </script>