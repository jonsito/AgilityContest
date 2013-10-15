
   <!-- FORMULARIO DE NUEVA INSCRIPCION -->
    <div id="inscripciones-dialog" class="easyui-dialog" style="width:640px;height:350px;padding:10px 20px">
        <div class="ftitle">Datos de la Inscripci&oacute;n</div>
        <form id="inscripciones-data" method="get" novalidate>
            <div class="fitem">
                <label for="Dorsal">Participante:</label>
                <input type="hidden" name="Dorsal"/>
                <select id="inscripciones-search" name="Search" class="easyui-combogrid" style="width:250px"></select>
            </div>
            <div>
            <input id="inscripciones-Dorsal" type="hidden" name="Dorsal" disabled="disabled"/>
            Nombre: <input id="inscripciones-Nombre" name="Nombre" disabled="disabled" />&nbsp;
            Categoria: <input id="inscripciones-Categoria" name="Categoria" disabled="disabled"/>&nbsp;
            Grado: <inpur id="inscripciones-Grado" name="Grado" disabled="disabled" />&nbsp;
            Gu&iacute;a: <input id="inscripciones-Guia" name="Guia" disabled="disabled" />&nbsp;
            Club: <input id="inscripciones-Club" name="Club" disabled="disabled" />&nbsp;
        </form>
        <form id="inscripciones-form" method="get" novalidate>
            Celo: <input id="inscripciones-Celo" name="Celo" class="easyui-checkbox" type="checkbox" />
            </div>
            <div class="fitem">
                <label for="Observaciones">Observaciones: </label>
                <input id="inscripciones-Observaciones" name="Observaciones" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Equipo">Equipo: </label>
                <input id="inscripciones-Equipo" name="Equipo" class="easyui-validatebox" type="text" style="width:200px"/>
            </div>
            <div class="fitem">
                <label for="Pagado">Pagado: </label>
                <input id="inscripciones-Pagado" name="Pagado" class="easyui-validatebox" type="text" style="width:50px">&euro;</input>
            </div>
            <div>Jornadas</div>
            <div class="fitem">
                <label for="J1">J1</label>
                <input id="inscripciones-J1" name="J1" class="easyui-checkbox" type="checkbox" />
                <label for="J2">J2</label>
                <input id="inscripciones-J2" name="J2" class="easyui-checkbox" type="checkbox" />
                <label for="J3">J3</label>
                <input id="inscripciones-J3" name="J3" class="easyui-checkbox" type="checkbox" />
                <label for="J4">J4</label>
                <input id="inscripciones-J4" name="J4" class="easyui-checkbox" type="checkbox" />
                <label for="J5">J5</label>
                <input id="inscripciones-J5" name="J5" class="easyui-checkbox" type="checkbox" />
                <label for="J6">J6</label>
                <input id="inscripciones-J6" name="J6" class="easyui-checkbox" type="checkbox" />
                <label for="J7">J7</label>
                <input id="inscripciones-J7" name="J7" class="easyui-checkbox" type="checkbox" />
                <label for="J8">J8</label>
                <input id="inscripciones-J8" name="J8" class="easyui-checkbox" type="checkbox" />
            </div>
        </form>
    
		<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
		<div id="inscripciones-dlg-buttons">        	
			<a id="inscripciones-cancelBtn" href="#" class="easyui-linkbutton" onclick="javascript:$('#inscripciones-dialog').dialog('close')">Cerrar</a>
        	<a id="inscripciones-okBtn" href="#" class="easyui-linkbutton" onclick="saveInscripcion()">Inscribir</a>
        	<a id="inscripciones-clearBtn" href="#" class="easyui-linkbutton" onclick="clearForm()">Limpiar</a>
    	</div>
    </div>

    <script type="text/javascript">
    // datos del formulario de nuevo/edit prueba
    // - declaracion del formulario
    $('#inscripciones-form').form();
    // - botones
    $('#inscripciones-okBtn').linkbutton({iconCls:'icon-save'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Aceptar datos. Inscribir perro en la prueba</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#inscripciones-cancelBtn').linkbutton({iconCls:'icon-undo'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Cerrar formulario de inscripciones</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#inscripciones-clearBtn').linkbutton({iconCls:'icon-back'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Limpiar formulario</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    
    // campos del formulario
    $('#inscripciones-dialog').dialog({
        closed:true,
        buttons:'#inscripciones-dlg-buttons'
    });
    $('#inscripciones-Dorsal').combogrid({
		panelWidth: 350,
		panelHeight: 200,
		idField: 'Nombre',
		textField: 'Nombre',
		url: 'database/enumerate_Perros.php',
		method: 'get',
		mode: 'remote',
		columns: [[
			{field:'Dorsal',hidden:'true'},
			{field:'Nombre',title:'Perro',width:20,align:'right'},
			{field:'Categoria',title:'Cat.',width:10,align:'center'},
			{field:'Grado',title:'Grado',width:10,align:'center'},
			{field:'Guia',title:'Guia',width:40,align:'right'},
			{field:'Club',title:'Club',width:20,align:'right'},
		]],
		multiple: false,
		fitColumns: true,
		singleSelect: true,
		selectOnNavigation: false ,
		onSelect: function(index,row) {
			var dorsal=row.Dorsal;
			if (!dorsal) return;
	        $('#inscripciones-data').form('load','database/get_dogByDorsal.php?Dorsal='+dorsal); // load form with json retrieved data
		}
	});

    </script>