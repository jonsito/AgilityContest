     <!-- FORMULARIO DE DECLARACION Y MODIFICACION DE JORNADAS -->
    <div id="jornadas-dialog" class="easyui-dialog" style="width:350px;height:525px;padding:25px">
        <div class="ftitle">Informaci&oacute;n de la Jornada<br /></div>
        <form id="jornadas-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre" style="width:250px">Nombre / Comentarios:</label>
                <input id="jornadas-Nombre" name="Nombre" type="text" class="easyui-validatebox" style="width:250px"/>
            </div>
        	<div class="fitem">
				<!-- ID de la prueba asociada al formulario -->
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
                <select id="jornadas-Fecha" name="Fecha" class="easyui-datebox" style="width:250px"></select>
            </div>
            <div class="fitem">
                <label for="Hora">Hora</label>
                <input id="jornadas-Hora" name="Hora" class="easyui-timespinner" style="width:250px"/>
            </div>
            <div class="fitem">
            	<label style="width:250px">Mangas a realizar:</label>
            </div>
            
            <div class="fitem">
                <label for="PreAgility" style="width:200px" >Pre-Agility</label>
                <input id="jornadas-PreAgility" name="PreAgility" class="easyui-checkbox" 
                type="checkbox" checked="checked" value="1"  onClick="checkPrueba('#jornadas-PreAgility',0x0002)"/>
            </div>
            <div class="fitem">
                <label for="Grado1" style="width:200px" >Grado 1</label>
                <input id="jornadas-Grado1" name="Grado1" class="easyui-checkbox" 
                type="checkbox" checked="checked" value="1" onClick="checkPrueba('#jornadas-Grado1',0x0004)"/>
            </div>
            
            <div class="fitem">
                <label for="Grado2" style="width:200px" >Grado 2</label>
                <input id="jornadas-Grado2" name="Grado2" class="easyui-checkbox" 
                type="checkbox" checked="checked" value="1" onClick="checkPrueba('#jornadas-Grado2',0x0008)"/>
            </div>
            
            <div class="fitem">
                <label for="Grado3" style="width:200px" >Grado 3</label>
                <input id="jornadas-Grado3" name="Grado3" class="easyui-checkbox" 
                type="checkbox" checked="checked" value="1" onClick="checkPrueba('#jornadas-Grado3',0x0010)"/>
            </div>
            
            <div class="fitem">
                <label for="Open" style="width:200px" >Prueba Abierta (Open)</label>
                <input id="jornadas-Open" name="Open" class="easyui-checkbox" 
                type="checkbox" value="1"  onClick="checkPrueba('#jornadas-Open',0x0020)"/>
            </div>
            
            <div class="fitem">
                <label for="Equipos3" style="width:200px" >Equipos (3 mejores)</label>
                <input id="jornadas-Equipos3" name="Equipos3" class="easyui-checkbox" 
                type="checkbox" value="1" onClick="checkPrueba('#jornadas-Equipos3',0x0040)"/>
            </div>
            <div class="fitem">
                <label for="Equipos4" style="width:200px" >Equipos (Conjunta)</label>
                <input id="jornadas-Equipos4" name="Equipos4" class="easyui-checkbox" 
                type="checkbox" value="1" onClick="checkPrueba('#jornadas-Equipos4',0x0080)"/>
            </div>
            
            <div class="fitem">
                <label for="KO" style="width:200px" >Prueba K.O.</label>
                <input id="jornadas-KO" name="KO" class="easyui-checkbox" 
                	type="checkbox" value="1" onClick="checkPrueba('#jornadas-KO',0x0100);"/>
            </div>
            
            <div class="fitem">
                <label for="Exhibicion" style="width:200px" >Manga de Exhibici&oacute;n</label>
                <input id="jornadas-Exhibicion" name="Exhibicion" class="easyui-checkbox" 
                type="checkbox" value="1"  onClick="checkPrueba('#jornadas-Exhibicion',0x0200)"/>
            </div>
            
            <div class="fitem">
                <label for="Otras" style="width:200px" >Otras (no especificada)</label>
                <input id="jornadas-Otras" name="Otras" class="easyui-checkbox" 
                type="checkbox" value="1"  onClick="checkPrueba('#jornadas-Otras',0x0001)"/>
            </div>
        </form>
            
		<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
		<div id="jornadas-dlg-buttons" style="padding-bottom:15px">
			<a id="jornadas-okBtn" href="#" class="easyui-linkbutton" onclick="saveJornada()">Guardar</a>
			<a id="jornadas-cancelBtn" href="#" class="easyui-linkbutton" onclick="javascript:$('#jornadas-dialog').dialog('close')">Cancelar</a>
		</div>
    </div>
    
    <script type="text/javascript">
    // datos del formulario de nuevo/edit jornada
    $('#jornadas-dialog').dialog({
        closed:true,
        buttons:'#jornadas-dlg-buttons'
    });
    // - declaracion del formulario
    $('#jornadas-form').form({
        onLoadSuccess : function(data) {
            // fix date and checkboxes value into datebox in "onLoadSuccess" event declaration
            $('#jornadas-PreAgility').prop('checked',(data.PreAgility==1)?true:false);
            $('#jornadas-Grado1').prop('checked',(data.Grado1==1)?true:false);
            $('#jornadas-Grado2').prop('checked',(data.Grado2==1)?true:false);
            $('#jornadas-Grado3').prop('checked',(data.Grado3==1)?true:false);
            $('#jornadas-Equipos3').prop('checked',(data.Equipos3==1)?true:false);
            $('#jornadas-Equipos4').prop('checked',(data.Equipos4==1)?true:false);
            $('#jornadas-Open').prop('checked',(data.Open==1)?true:false);
            $('#jornadas-KO').prop('checked',(data.KO==1)?true:false);
            $('#jornadas-Exhibicion').prop('checked',(data.Exhibicion==1)?true:false);
            $('#jornadas-Otras').prop('checked',(data.Otras==1)?true:false);
            $('#jornadas-Cerrada').prop('checked',(data.Cerrada==1)?true:false);
        	$('#jornadas-Prueba').val(data.Prueba);
        	$('#jornadas-Operation').val('update');
        }
    });

    // fechas
    $('#jornadas-Fecha').datebox({ 
        required: true,
        formatter: function(date) {
            		var y = date.getFullYear();
            		var m = date.getMonth()+1;
            		var d = date.getDate();
            		return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
        		},
        parser:  function (s) {
            		if (!s) return new Date();
            		var ss = (s.split('-'));
            		var y = parseInt(ss[0],10);
            		var m = parseInt(ss[1],10);
            		var d = parseInt(ss[2],10);
            		if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
            		    return new Date(y,m-1,d);
            		} else {
            		    return new Date();
            		}
        		}
    });
    $('#jornadas-Fecha').datebox('calendar').calendar({ firstDay:1 });
    $('#jornadas-Fecha').datebox('options').keyHandler.query = function(q) {
    	if (q.indexOf('-') >= 0) { q = replaceAll('-', '/',q);	}
    	$(this).datebox('setValue', q);
    };
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
    $('#jornadas-okBtn').linkbutton({iconCls:'icon-ok'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Registrar la entrada en la Base de Datos</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    // - boton de "Cancelar"
    $('#jornadas-cancelBtn').linkbutton({iconCls:'icon-cancel'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Cancelar la operaci&oacute;n. Cerrar ventana</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    
    </script>
