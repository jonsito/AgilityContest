
   <!-- FORMULARIO DE MODIFICACION DE UNA INSCRIPCION -->
    <div id="chinscripciones-dialog" class="easyui-dialog" style="width:725px;height:350px;padding:10px 20px">
        <div class="ftitle">Datos de la Inscripci&oacute;n</div>
        <div class="fitem">
        	<form id="chinscripciones-data" method="get" novalidate="novalidate">
            	<input id="chinscripciones-Dorsal" type="hidden" name="Dorsal"/>
        		<label for="Nombre">Nombre:</label>
            	<input id="chinscripciones-Nombre" name="Nombre" type="text" size="20" disabled="disabled" />
            	<label for="Licencia">Licencia: </label>
            	<input id="chinscripciones-Licencia" name="Licencia" type="text" size="5" disabled="disabled" />
            	<label for="Categoria">Categoria: </label>
            	<input id="chinscripciones-Categoria" name="Categoria" type="text" size="1" disabled="disabled"/>
            	<label for="Grado">Grado: </label>
            	<input id="chinscripciones-Grado" name="Grado" type="text" size="1" disabled="disabled" /><br />
            	<label for="Guia">Gu&iacute;a: </label>
           		<input id="chinscripciones-Guia" name="Guia" type="text" disabled="disabled" />
           		<label for="Club"> Club: </label>
           		<input id="chinscripciones-Club" name="Club" type="text" disabled="disabled" /><br />
        	</form>
        </div>
        
        <form id="chinscripciones-form" method="get" novalidate>
        	<!-- these two fields need to be filled before commit -->
        	<input id="chinscripciones-fDorsal" type="hidden" name="Dorsal"/>
        	<input id="chinscripciones-fPruebaID" type="hidden" name="ID"/>
        	<input id="chinscripciones-fOperation" type="hidden" name="Operation"/>
        	<!-- "standard" data fields -->
        	<div class="fitem">
            	<label for="Celo">Celo:</label>
            	<input id="chinscripciones-Celo" name="Celo" class="easyui-checkbox" type="checkbox" value="1"/>
            </div>
            <div class="fitem">
                <label for="Observaciones">Observaciones: </label>
                <input id="chinscripciones-Observaciones" name="Observaciones" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Equipo">Equipo: </label>
                <input id="chinscripciones-Equipo" name="Equipo" class="easyui-validatebox" type="text" style="width:200px"/>
            </div>
            <div class="fitem">
                <label for="Pagado">Pagado: </label>
                <input id="chinscripciones-Pagado" name="Pagado" class="easyui-validatebox" type="text" size="3" style="width:50px;text-align:right;padding-right:5px"/>&euro;
            </div>
            <div>Jornadas:</div>
            <div class="fitem">
            	<!-- these fields stores original values -->
            	<input id="chinscripciones-oldJ1" name="oldJ1" value="0" type="hidden"/>
            	<input id="chinscripciones-oldJ2" name="oldJ2" value="0" type="hidden"/>
            	<input id="chinscripciones-oldJ3" name="oldJ3" value="0" type="hidden"/>
            	<input id="chinscripciones-oldJ4" name="oldJ4" value="0" type="hidden"/>
            	<input id="chinscripciones-oldJ5" name="oldJ5" value="0" type="hidden"/>
            	<input id="chinscripciones-oldJ6" name="oldJ6" value="0" type="hidden"/>
            	<input id="chinscripciones-oldJ7" name="oldJ7" value="0" type="hidden"/>
            	<input id="chinscripciones-oldJ8" name="oldJ8" value="0" type="hidden"/>
            	<!-- visible fields -->
                <label for="J1"><input id="chinscripciones-J1" name="J1" class="easyui-checkbox" type="checkbox" value="1" />J1</label>
                <label for="J2"><input id="chinscripciones-J2" name="J2" class="easyui-checkbox" type="checkbox" value="1" />J2</label>
                <label for="J3"><input id="chinscripciones-J3" name="J3" class="easyui-checkbox" type="checkbox" value="1" />J3</label>
                <label for="J4"><input id="chinscripciones-J4" name="J4" class="easyui-checkbox" type="checkbox" value="1" />J4</label>
                <label for="J5"><input id="chinscripciones-J5" name="J5" class="easyui-checkbox" type="checkbox" value="1" />J5</label>
                <label for="J6"><input id="chinscripciones-J6" name="J6" class="easyui-checkbox" type="checkbox" value="1" />J6</label>
                <label for="J7"><input id="chinscripciones-J7" name="J7" class="easyui-checkbox" type="checkbox" value="1" />J7</label>
                <label for="J8"><input id="chinscripciones-J8" name="J8" class="easyui-checkbox" type="checkbox" value="1" />J8</label>
            </div>
        </form>
    
		<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
		<div id="chinscripciones-dlg-buttons">        	
			<a id="chinscripciones-cancelBtn" href="#" class="easyui-linkbutton" >Cerrar</a>
        	<a id="chinscripciones-okBtn" href="#" class="easyui-linkbutton" onclick="saveInscripcion()">Modificar</a>
    	</div>
    </div>

    <script type="text/javascript">
    // datos del formulario de nuevo/edit prueba
    $('#chinscripciones-dialog').dialog({
            closed:true,
            buttons:'#chinscripciones-dlg-buttons'
    });
    // - declaracion del formulario
    $('#chinscripciones-data').form();
    $('#chinscripciones-form').form();
    
    // - botones
    $('#chinscripciones-okBtn').linkbutton({iconCls:'icon-ok'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Aceptar datos. Registrar modificaciones</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#chinscripciones-cancelBtn').linkbutton({iconCls:'icon-cancel'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Cancelar edicion. Cerrar formulario</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#chinscripciones-cancelBtn').bind('click',function() {
        $('#chinscripciones-dialog').dialog('close');
    });
    
    // campos del formulario
    $('#chinscripciones-dialog').dialog({
        closed:true,
        buttons:'#chinscripciones-dlg-buttons'
    });

    </script>