
   <!-- FORMULARIO DE NUEVA INSCRIPCION -->
    <div id="inscripciones-dialog" class="easyui-dialog" style="width:725px;height:375px;padding:10px 20px">
        <div class="ftitle">Datos de la Inscripci&oacute;n</div>
        <div class="fitem">
            <form name="inscripciones-buscar" method="get">
            <label for="Participante">Participante:</label>
            <select id="inscripciones-Participante" name="Participante" class="easyui-combogrid" style="width:250px"></select>
            </form>
        </div>
        <div class="fitem">
        	<form id="inscripciones-data" method="get" novalidate="novalidate">
            	<input id="inscripciones-Dorsal" type="hidden" name="Dorsal"/>
        		<label for="Nombre">Nombre:</label>
            	<input id="inscripciones-Nombre" name="Nombre" type="text" size="20" disabled="disabled" />
            	<label for="Licencia">Licencia: </label>
            	<input id="inscripciones-Licencia" name="Licencia" type="text" size="5" disabled="disabled" />
            	<label for="Categoria">Categoria: </label>
            	<input id="inscripciones-Categoria" name="Categoria" type="text" size="1" disabled="disabled"/>
            	<label for="Grado">Grado: </label>
            	<input id="inscripciones-Grado" name="Grado" type="text" size="1" disabled="disabled" /><br />
            	<label for="Guia">Gu&iacute;a: </label>
           		<input id="inscripciones-Guia" name="Guia" type="text" disabled="disabled" />
           		<label for="Club"> Club: </label>
           		<input id="inscripciones-Club" name="Club" type="text" disabled="disabled" /><br />
        	</form>
        </div>
        
        <form id="inscripciones-form" method="get" novalidate>
        	<div class="fitem">
            	<label for="Celo">Celo:</label>
            	<input id="inscripciones-Celo" name="Celo" class="easyui-checkbox" type="checkbox" />
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
                <input id="inscripciones-Pagado" name="Pagado" class="easyui-validatebox" type="text" size="3" style="width:50px;text-align:right;padding-right:5px"/>&euro;
            </div>
            <div>Jornadas:</div>
            <div class="fitem">
                <label for="J1"><input id="inscripciones-J1" name="J1" class="easyui-checkbox" type="checkbox" />J1</label>
                <label for="J2"><input id="inscripciones-J2" name="J2" class="easyui-checkbox" type="checkbox" />J2</label>
                <label for="J3"><input id="inscripciones-J3" name="J3" class="easyui-checkbox" type="checkbox" />J3</label>
                <label for="J4"><input id="inscripciones-J4" name="J4" class="easyui-checkbox" type="checkbox" />J4</label>
                <label for="J5"><input id="inscripciones-J5" name="J5" class="easyui-checkbox" type="checkbox" />J5</label>
                <label for="J6"><input id="inscripciones-J6" name="J6" class="easyui-checkbox" type="checkbox" />J6</label>
                <label for="J7"><input id="inscripciones-J7" name="J7" class="easyui-checkbox" type="checkbox" />J7</label>
                <label for="J8"><input id="inscripciones-J8" name="J8" class="easyui-checkbox" type="checkbox" />J8</label>
            </div>
        </form>
    
		<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
		<div id="inscripciones-dlg-buttons">        	
			<a id="inscripciones-cancelBtn" href="#" class="easyui-linkbutton" >Cerrar</a>
        	<a id="inscripciones-okBtn" href="#" class="easyui-linkbutton" onclick="saveInscripcion()">Inscribir</a>
        	<a id="inscripciones-clearBtn" href="#" class="easyui-linkbutton">Limpiar</a>
    	</div>
    </div>

    <script type="text/javascript">
    // datos del formulario de nuevo/edit prueba
    $('#inscripciones-dialog').dialog({
        closed:true,
        buttons:'#inscripciones-dlg-buttons'
    });
    // - declaracion del formulario
    $('#inscripciones-buscar').form();
    $('#inscripciones-data').form();
    $('#inscripciones-form').form();
    
    // - botones
    $('#inscripciones-okBtn').linkbutton({iconCls:'icon-save'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Aceptar datos. Inscribir perro en la prueba</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#inscripciones-cancelBtn').linkbutton({iconCls:'icon-cancel'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Cerrar formulario de inscripciones</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#inscripciones-cancelBtn').bind('click',function() {
        $('#inscripciones-buscar').form('clear'); 
        $('#inscripciones-data').form('clear'); 
        $('#inscripciones-form').form('clear'); 
        $('#inscripciones-Participante').combogrid({ 'setValue' : '' });
        $('#inscripciones-dialog').dialog('close');
    });
    $('#inscripciones-clearBtn').linkbutton({iconCls:'icon-undo'}).tooltip({
        position: 'top',
        content: '<span style="color:#000">Limpiar formulario</span>',
    	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    });
    $('#inscripciones-clearBtn').bind('click',function() {
        $('#inscripciones-buscar').form('clear'); 
        $('#inscripciones-data').form('clear'); 
        $('#inscripciones-form').form('clear'); 
        $('#inscripciones-Participante').combogrid({ 'setValue' : '' });
    });
    
    // campos del formulario

    $('#inscripciones-Participante').combogrid({
		panelWidth: 350,
		panelHeight: 200,
		idField: 'Dorsal',
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