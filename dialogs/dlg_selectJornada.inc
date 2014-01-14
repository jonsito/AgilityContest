<!-- FORMULARIO DE SELECCION DE PRUEBAS ABIERTAS-->
<div id="seljornada-window" class="easyui-window" style="position:relative,width:500px;height:200px;padding:20px 20px">
	<div id="seljornada-Layout" class="easyui-layout" fit="true">
		<form id="seljornada-Selection">
			<div id="seljornada-Content" region="north" border="true">
        		<div class="fitem">
            		<label for="Prueba">Selecciona Prueba:</label>
            		<select id="seljornada-Prueba" name="Prueba" class="easyui-combogrid" style="width:200px">&nbsp;</select>
        		</div>        		
        		<div class="fitem">
            		<label for="Jornada">Selecciona Jornada:</label>
            		<select id="seljornada-Jornada" name="Jornada" class="easyui-combogrid" style="width:200px">&nbsp;</select>
        		</div>
			</div> <!-- contenido -->
		</form>
		<div region="center"></div>
		<div id="seljornada-Buttons" data-options="region:'south',border:false" style="text-align:right;padding:5px 0 0;">
    	    <a id="seljornada-okBtn" href="#" class="easyui-linkbutton" onclick="acceptSelectJornada()">Aceptar</a>
    	    <a id="seljornada-cancelBtn" href="#" class="easyui-linkbutton" onclick="cancelSelectJornada()">Cancelar</a>
		</div>	<!-- botones -->
	</div> <!-- Layout -->
</div> <!-- Window -->

<script type="text/javascript">
$('#seljornada-window').window({
	title: 'Selecciona la Prueba y Jornada a desplegar',
	collapsible: false,
	minimizable: false,
	maximizable: false,
	closable: true,
	closed: true,
	shadow: true,
	modal: true
});

$('#seljornada-form').form();
$('#seljornada-layout').layout();

$('#seljornada-okBtn').linkbutton({ iconCls: 'icon-ok' }).tooltip({
	position: 'top',
	content: '<span style="color:#000">Trabajar con la jornada seleccionada</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});

$('#seljornada-cancelBtn').linkbutton({ iconCls: 'icon-cancel' }).tooltip({
	position: 'top',
	content: '<span style="color:#000">Cancelar selecci&oacute;n. Cerrar ventana</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});

$('#seljornada-Prueba').combogrid({
	panelWidth: 450,
	panelHeight: 100,
	idField: 'ID',
	textField: 'Nombre',
	url: 'database/enumerate_PruebasAbiertas.php',
	method: 'get',
	mode: 'remote',
	required: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	selectOnNavigation: true, // let use cursor keys to interactive select
	columns: [[
	    {field:'ID',hidden:true},
		{field:'Nombre',title:'Nombre',width:50,align:'right'},
		{field:'Club',title:'Club',width:20,align:'right'},
		{field:'Observaciones',title:'Observaciones.',width:30,align:'right'}
	]],
	onChange:function(value){
		workingData.prueba=Number(value);
		var g = $('#seljornada-Jornada').combogrid('grid');
		g.datagrid('load',{Prueba:value});
	}
});

$('#seljornada-Jornada').combogrid({
	panelWidth: 500,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: 'database/enumerate_J_AbiertasByPrueba.php',
	method: 'get',
	mode: 'remote',
	required: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	columns: [[
	    { field:'ID',			hidden:true }, // ID de la jornada
	    { field:'Prueba',		hidden:true }, // ID de la prueba
	    { field:'Numero',		width:4, sortable:false,	align:'center', title: '#'},
		{ field:'Nombre',		width:40, sortable:false,   align:'right', title: 'Nombre/Comentario' },
		{ field:'Fecha',		width:20, sortable:false,	align:'right', title: 'Fecha:' },
		{ field:'Hora',			width:15, sortable:false,	align:'right', title: 'Hora.' },
		{ field:'Grado1',		width:7, sortable:false,	align:'center', title: 'G-I   ' },
		{ field:'Grado2',		width:7, sortable:false,	align:'center', title: 'G-II  ' },
		{ field:'Grado3',		width:7, sortable:false,	align:'center', title: 'G-III ' },
		{ field:'Equipos',		width:7, sortable:false,	align:'center', title: 'Eq.   ' },
		{ field:'PreAgility',	width:7, sortable:false,	align:'center', title: 'Pre.  ' },
		{ field:'KO',			width:7, sortable:false,	align:'center', title: 'K.O.  ' },
		{ field:'Exhibicion',	width:7, sortable:false,	align:'center', title: 'Show  ' },
		{ field:'Otras',		width:7, sortable:false,	align:'center', title: 'Otras ' }
	]],
	onBeforeLoad: function(param) { param.Prueba=workingData.prueba; return true; }
});

function acceptSelectJornada() {
	// si prueba invalida cancelamos operacion
	var p=$('#seljornada-Prueba').combogrid('grid').datagrid('getSelected');
	var j=$('#seljornada-Jornada').combogrid('grid').datagrid('getSelected');
	if ( (p==null) || (j==null) ) {
		// indica error
		$.messager.aler("Error","Debe indicar una prueba y jornada v&aacute;lidas","error");
		return;
	}
	// clear selection to make sure next time gets empty
	$('#seljornada-Prueba').combogrid('setValue','');
	$('#seljornada-Jornada').combogrid('setValue','');
	// store selected data into global structure
	workingData.prueba=p.ID;
	workingData.nombrePrueba=p.Nombre;
	workingData.jornada=j.ID;
	workingData.nombreJornada=j.Nombre;
	$('#seljornada-window').window('close');	
}

function cancelSelectJornada() {
	workingData.prueba='';
	workingData.jornada='';
	$('#seljornada-Prueba').combogrid('setValue','');
	$('#seljornada-Jornada').combogrid('setValue','');
	$('#seljornada-window').window('close');
}
</script>