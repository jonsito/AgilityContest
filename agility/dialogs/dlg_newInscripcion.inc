<!-- FORMULARIO DE ALTA DE INSCRIPCIONES EN UNA PRUEBA -->
<div id="new_inscripcion-dialog" class="easyui-dialog" title="Nueva(s) inscripciones"
	data-options="buttons:new_inscripcion-buttons,closable:true,closed:true" 
	style="width:500px;height:350px;padding:10px 20px">
  	<form id="new_inscripcion-form">
   		<input id="new_inscripcion-Operation" name="Operation" type="hidden" value="insert"/>
   		<input id="new_inscripcion-IDPrueba" type="hidden" name="IDPrueba" value="0"/>
   		<label for="Buscar">Buscar: </label>
   		<input type="text" id="new_inscripcion-search" style="width:150px" value="---- Buscar ----" class="search_textfield"/>
   	</form>
   	<hr />
   	<!--  tabla que contiene el resultado de la seleccion del formulario -->
	<table id="new_inscripcion-datagrid" class="easyui-datagrid"</table>

	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
	<div id="new_inscripcion-buttons">
		<a id="new_inscripcion-okBtn" href="#" class="easyui-linkbutton" 
    		data-options="iconCls:'icon-ok'" onclick="insertInscripcion('#new_inscripcion-datagrid')">Inscribir</a>
    	<a id="new_inscripcion-cancelBtn" href="#" class="easyui-linkbutton" 
    		data-options="iconCls:'icon-cancel'" onclick="javascript:$('#new_inscripcion-dialog').dialog('close')">Cancelar</a>
	</div>
<!-- 
	<div id="new_inscripcion-progresswindow" class="easyui-window">
		<p id="new_incripcion-progresslabel" style="align:center">Inscribiendo a:</p>
		<div id="new_inscripcioin-progressbar" class="easyui-progressbar" 
			data-options="width:300px,value:0" style="align:center;"></div>
	</div>
--> 
</div>

<script type="text/javascript">

//TODO: estudiar por qué el "closed:true" en el data-options no funciona
$('#new_inscripcion-dialog').dialog({closed:true});

// combogrid de inscripcion de participantes
$('#new_inscripcion-datagrid').datagrid({
    fit:true,
    delay: 100, // dont search on every keystroke
	panelWidth: 400,
	panelHeight: 150,
	idField: 'Perro',
	textField: 'Nombre',
	url: 'database/inscripcionFunctions.php?Operation=noinscritos&IDPrueba='+workingData.prueba,
	method: 'get',
	mode: 'remote',
	required: false,
	value: '- Nuevas inscripciones -',
	columns: [[
	   	{field:'ID',hidden:'true'},
		{field:'Guia',hidden:'true'},
		{field:'Club',hidden:'true'},
		{field:'Nombre',title:'Perro',width:20,align:'right'},
		{field:'Categoria',title:'Cat.',width:10,align:'center'},
		{field:'Grado',title:'Grado',width:10,align:'center'},
		{field:'NombreGuia',title:'Guia',width:40,align:'right'},
		{field:'NombreClub',title:'Club',width:20,align:'right'}
	]],
	multiple: true,
	fitColumns: true,
	singleSelect: false,
	selectOnNavigation: false
});
// tooltips de los sub-formularios
addTooltip($('#new_inscripcion-search').linkbutton(),"Buscar posibles participantes<br /> que coincidan con el criterio de busqueda"); 
addTooltip($('#new_inscripcion-okBtn').linkbutton(),"Inscribir el/los perro(s) seleccionado(s) en la prueba"); 
addTooltip($('#new_inscripcion-cancelBtn').linkbutton(),"Cerrar la ventana<br /> Volver a la ventana de gestion de inscripciones");

/*
//ventana de progreso de las inscripciones
$('#new_inscripcion-progresswindow').window({
	 width:450,
	 height:200,
	 modal:true,
	 collapsable:false,
	 minimizable:false,
	 maximizable:false,
	 closable:false,
	 closed:true
});
*/
</script>