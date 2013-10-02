     <!-- FORMULARIO DE DECLARACION Y MODIFICACION DE JORNADAS -->
    <div id="jornadas-dialog" class="easyui-dialog" style="width:350px;height:550px;padding:10px 20px"
            closed="true" buttons="#jornadas-dlg-buttons">
        <div class="ftitle">Informaci&oacute;n de la Jornada</div>
        <form id="jornadas-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre" style="width:250px">Nombre / Comentarios:</label>
                <input id="jornadas-Nombre" name="Nombre" type="text" class="easyui-validatebox" style="width:250px"/>
            </div>
        	<div class="fitem">
				<!-- nombre de la prueba asociada al formulario -->
				<input id="jornadas-Prueba" type="hidden" name="Prueba"/>
				<!-- ID de jornada; usado para updates y deletes -->
				<input id="jornadas-ID" type="hidden" name="ID"/> 
				<!-- Operacion a realizar con el formulario -->
				<input id="jornadas-Operation" type="hidden" name="Operation"/>
				<!-- Indice a la prueba seleccionada en el formulario -->
				<input id="jornadas-PruebaIndex" type="hidden" name="PruebaIndex"/>
        	</div>
            <div class="fitem">
                <label for="Fecha">Fecha</label>
                <select id="jornadas-Fecha" name="Fecha" class="easyui-datebox" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Hora">Hora</label>
                <input id="jornadas-Hora" name="Hora" class="easyui-timespinner" style="width:250px"/>
            </div>
            <div class="fitem">
            	<label style="width:250px">Mangas a realizar:</label>
            </div>
            <div class="fitem">
                <label for="Grado1" style="width:200px" >Grado 1</label>
                <input id="jornadas-Grado1" name="Grado1" class="easyui-checkbox" type="checkbox" checked="checked" value="1"/>
            </div>
            
            <div class="fitem">
                <label for="Grado2" style="width:200px" >Grado 2</label>
                <input id="jornadas-Grado2" name="Grado2" class="easyui-checkbox" type="checkbox" checked="checked" value="1"/>
            </div>
            
            <div class="fitem">
                <label for="Grado3" style="width:200px" >Grado 3</label>
                <input id="jornadas-Grado3" name="Grado3" class="easyui-checkbox" type="checkbox" checked="checked" value="1"/>
            </div>
            
            <div class="fitem">
                <label for="Equipos" style="width:200px" >Manga por Equipos</label>
                <input id="jornadas-Equipos" name="Equipos" class="easyui-checkbox" type="checkbox" />
            </div>
            
            <div class="fitem">
                <label for="PreAgility" style="width:200px" >Pre-Agility</label>
                <input id="jornadas-PreAgility" name="PreAgility" class="easyui-checkbox" type="checkbox" checked="checked" value="1"/>
            </div>
            
            <div class="fitem">
                <label for="KO" style="width:200px" >Prueba K.O.</label>
                <input id="jornadas-KO" name="KO" class="easyui-checkbox" type="checkbox" />
            </div>
            <div class="fitem">
                <label for="Exhibicion" style="width:200px" >Manga de Exhibici&oacute;n</label>
                <input id="jornadas-Exhibicion" name="Exhibicion" class="easyui-checkbox" type="checkbox" />
            </div>
            <div class="fitem">
                <label for="Otras" style="width:200px" >Otras (no especificada)</label>
                <input id="jornadas-Otras" name="Otras" class="easyui-checkbox" type="checkbox" />
            </div>
            <div class="fitem"><hr/></div>
            <div class="fitem">
                <label for="Cerrada" style="width:200px" >Cerrar jornada</label>
                <input id="jornadas-Cerrada" name="Cerrada" class="easyui-checkbox" type="checkbox" />
            </div>
        </form>
            
		<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
		<div id="jornadas-dlg-buttons">
			<a id="jornadas-okBtn" href="#" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveJornada()">Guardar</a>
			<a id="jornadas-cancelBtn" href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#jornadas-dialog').dialog('close')">Cancelar</a>
		</div>
    </div>
    
    <script type="text/javascript">
    // datos del formulario de nuevo/edit jornada
    $('#jornadas-dialog').dialog();
    // - declaracion del formulario
    $('#jornadas-form').form();

    // fechas
    $('#jornadas-Fecha').datebox({ required: true });
    $('#jornadas-Fecha').datebox('calendar').calendar({ firstDay:1 });
    // hora
    $('#jornadas-Hora').timespinner({
        required: true
    });
	// Nombre/comentarios
    $('#jornadas-Nombre').validatebox({
        required: true,
        validType: 'length[1,255]'
    });
    // - boton de "Aceptar"
    $('#jornadas-okBtn').linkbutton();        
    $('#jornadas-okBtn').tooltip({
        position: 'top',
        content: '<span style="color:#000">Registrar la entrada en la Base de Datos</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    // - boton de "Cancelar"
    $('#jornadas-cancelBtn').linkbutton();        
    $('#jornadas-cancelBtn').tooltip({
        position: 'top',
        content: '<span style="color:#000">Cancelar la operaci&oacute;n. Cerrar ventana</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    
    </script>
