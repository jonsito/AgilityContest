<!-- FORMULARIO DE SELECCION DE PRUEBAS ABIERTAS-->
<div id="seljornada-window" class="easyui-window" style="position:relative,width:500px;height:200px;padding:20px 20px">
	<div id="seljornada-Layout" class="easyui-layout" data-options="fit:'true'">
		<form id="seljornada-Selection">
			<div id="seljornada-Content" data-options="region:'north',border:'true'">
        		<div class="fitem">
            		<label for="Prueba">Selecciona Prueba:</label>
            		<select id="seljornada-Prueba" name="Prueba" style="width:200px"></select>
        		</div>        		
        		<div class="fitem">
            		<label for="Jornada">Selecciona Jornada:</label>
            		<select id="seljornada-Jornada" name="Jornada" style="width:200px"></select>
        		</div>
			</div> <!-- contenido -->
		</form>
		<div data-options="region:'center'"></div>
		<div id="seljornada-Buttons" data-options="region:'south',border:false" style="text-align:right;padding:5px 0 0;">
    	    <a id="seljornada-okBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls: 'icon-ok'" onclick="acceptSelectJornada()">Aceptar</a>
    	    <a id="seljornada-cancelBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls: 'icon-cancel'" onclick="cancelSelectJornada()">Cancelar</a>
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

addTooltip($('#seljornada-okBtn').linkbutton(),"Trabajar con la jornada seleccionada");
addTooltip($('#seljornada-cancelBtn').linkbutton(),"Cancelar selecci&oacute;n. Cerrar ventana");

$('#seljornada-Prueba').combogrid({
	panelWidth: 400,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: 'server/database/pruebaFunctions.php?Operation=enumerate',
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
			{field:'Club',hidden:true},
			{field:'NombreClub',title:'Club',width:20,align:'right'},
			{field:'Observaciones',title:'Observaciones.',width:30,align:'right'}
	]],
	onChange:function(value){
		workingData.prueba=Number(value);
		var g = $('#seljornada-Jornada').combogrid('grid');
		g.datagrid('load',{Prueba:value});
	}
});

$('#seljornada-Jornada').combogrid({
	panelWidth: 550,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: 'server/database/jornadaFunctions.php',
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
		{ field:'Nombre',		width:30, sortable:false,   align:'right',  title: 'Nombre/Comentario' },
		{ field:'Fecha',		hidden:true},
		{ field:'Hora',			hidden:true},
		{ field:'Grado1',		width:8, sortable:false,	align:'center', title: 'G-I    ' },
		{ field:'Grado2',		width:8, sortable:false,	align:'center', title: 'G-II   ' },
		{ field:'Grado3',		width:8, sortable:false,	align:'center', title: 'G-III  ' },
		{ field:'Open',		    width:8, sortable:false,	align:'center', title: 'Open   ' },
		{ field:'Equipos3',		width:8, sortable:false,	align:'center', title: 'Eq.3x4 ' },
		{ field:'Equipos4',		width:8, sortable:false,	align:'center', title: 'Eq.Conj' },
		{ field:'PreAgility',	width:8, sortable:false,	align:'center', title: 'Pre. 1 ' },
		{ field:'PreAgility2',	width:8, sortable:false,	align:'center', title: 'Pre. 2 ' },
		{ field:'KO',			width:8, sortable:false,	align:'center', title: 'K.O.   ' },
		{ field:'Especial',		width:8, sortable:false,	align:'center', title: 'Show   ' },
	]],
	onBeforeLoad: function(param) { 
		param.Operation='enumerate', 
		param.Prueba=workingData.prueba;
		param.AllowClosed=0;
		return true;
	}
});

function acceptSelectJornada() {
	// si prueba invalida cancelamos operacion
	var p=$('#seljornada-Prueba').combogrid('grid').datagrid('getSelected');
	var j=$('#seljornada-Jornada').combogrid('grid').datagrid('getSelected');
	if ( (p==null) || (j==null) ) {
		// indica error
		$.messager.alert("Error","Debe indicar una prueba y jornada v&aacute;lidas","error");
		return;
	}
	// clear selection to make sure next time gets empty
	$('#seljornada-Prueba').combogrid('setValue','');
	$('#seljornada-Jornada').combogrid('setValue','');
	// store selected data into global structure
	workingData.prueba=p.ID;
	workingData.nombrePrueba=p.Nombre;
	workingData.datosPrueba=p;
	workingData.datosJornada=j;
	workingData.jornada=j.ID;
	workingData.nombreJornada=j.Nombre;
	workingData.datosJornada=j;
	$('#seljornada-window').window('close');	
}

function cancelSelectJornada() {
	initWorkingData();
	$('#seljornada-Prueba').combogrid('setValue','');
	$('#seljornada-Jornada').combogrid('setValue','');
	$('#seljornada-window').window('close');
}
</script>