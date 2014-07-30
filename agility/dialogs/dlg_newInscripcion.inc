<!-- FORMULARIO DE ALTA DE INSCRIPCIONES EN UNA PRUEBA -->
<div id="new_inscripcion-dialog" class="easyui-dialog"
	data-options="iconCls:'icon-notes',
				modal:true,
				closed:true,
				closable:true,
				collapsible:false,
				minimizable:false,
				maximizable:false,
				resizable:false"
	style="width:700px;padding:10px 20px">
  	<form id="new_inscripcion-form">
   		<input id="new_inscripcion-Operation" name="Operation" type="hidden" value="insert"/>
   		<input id="new_inscripcion-Prueba" type="hidden" name="Prueba" value="0"/>
   		<div id="new_inscripcion-datagrid-tools">
   			<span style="float:left;padding:10px">
   			   	<label for="Buscar">Buscar: </label>
   				<input name="Buscar" type="text" id="new_inscripcion-search" style="width:150px" 
   					value="---- Buscar ----" class="search_textfield" onchange="listaNoInscritos();"/>
   			</span>
   			<span style="float:right;padding:10px">
   				<a id="new_inscripcion-reloadBtn" href="#" class="easyui-linkbutton"
	   				data-options="iconCls:'icon-brush'"
	   				onclick="
	   		        	// clear selection and reload table
	   		    		$('#new_inscripcion-search').val('---- Buscar ----');
   						listaNoInscritos();
	   		            "
	   				>Limpiar</a>
	   		</span>
   		</div>
   		<!--  tabla que contiene el resultado de la seleccion del formulario -->
		<table id="new_inscripcion-datagrid" class="easyui-datagrid"></table>
		<!-- botones de datos auxiliares para la inscripcion -->
		<span style="float:left;padding:5px">
		Jornadas:
        <input id="new_inscripcion-J1" 			name="J1" class="easyui-checkbox" type="checkbox" value="1" onclick="evalJornadas(this);"/>
        <label id="new_inscripcion-J1lbl" 		for="J1">1</label>
        <input id="new_inscripcion-J2" 			name="J2" class="easyui-checkbox" type="checkbox" value="2" onclick="evalJornadas(this);"/>
        <label id="new_inscripcion-J2lbl" 		for="J2">2</label>
        <input id="new_inscripcion-J3"			name="J3" class="easyui-checkbox" type="checkbox" value="4" onclick="evalJornadas(this);"/>
        <label id="new_inscripcion-J3lbl"		for="J3">3</label>
        <input id="new_inscripcion-J4"			name="J4" class="easyui-checkbox" type="checkbox" value="8" onclick="evalJornadas(this);"/>
 	    <label id="new_inscripcion-J4lbl"		for="J4">4</label>
        <input id="new_inscripcion-J5"			name="J5" class="easyui-checkbox" type="checkbox" value="16" onclick="evalJornadas(this);"/>
        <label id="new_inscripcion-J5lbl" 		for="J5">5</label>
        <input id="new_inscripcion-J6" 			name="J6" class="easyui-checkbox" type="checkbox" value="32" onclick="evalJornadas(this);"/>
        <label id="new_inscripcion-J6lbl" 		for="J6">6</label>
        <input id="new_inscripcion-J7" 			name="J7" class="easyui-checkbox" type="checkbox" value="64" onclick="evalJornadas(this);"/>
        <label id="new_inscripcion-J7lbl" 		for="J7">7</label>
        <input id="new_inscripcion-J8" 			name="J8" class="easyui-checkbox" type="checkbox" value="128" onclick="evalJornadas(this);"/>
        <label id="new_inscripcion-J8lbl" 		for="J8">8</label>
        </span>
        <span style="float:right;padding:5px">  
        <label id="new_inscripcion_Equipolbl"	for="Equipo">Equipo: </label>       	    
        <span id="new_inscripcion-Equiposp">
           	<select id="new_inscripcion-Equipo" name="Equipo" style="width:130px"></select>
        </span>
        <label id="new_inscripcion-Celolbl"	for="Celo">Celo:</label>
        <input id="new_inscripcion-Celo"		name="Celo" class="easyui-checkbox" type="checkbox" value="1" />
        <input id="new_inscripcion-Jornadas"	type="hidden" name="Jornadas" value="0" />
        </span>
   	</form>

	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
	<div id="new_inscripcion-buttons">
		<a id="new_inscripcion-okBtn" href="#" class="easyui-linkbutton" 
    		data-options="iconCls:'icon-ok'" onclick="insertInscripcion('#new_inscripcion-datagrid')">Inscribir</a>
    	<a id="new_inscripcion-doneBtn" href="#" class="easyui-linkbutton" 
    		data-options="iconCls:'icon-cancel'" onclick="javascript:$('#new_inscripcion-dialog').dialog('close')">Hecho</a>
	</div>
	
	<!-- barra de progreso de actualizacion de inscripciones -->
	<div id="new_inscripcion-progresswindow" class="easyui-window">
		<p id="new_incripcion-progresslabel" style="align:center">Inscribiendo a:</p>
		<div id="new_inscripcion-progressbar" class="easyui-progressbar" 
			data-options="width:300px,value:0" style="align:center;"></div>
	</div>
	
</div>

<script type="text/javascript">

// ajustamos el valor de la jornada en funcion de la seleccion
function evalJornadas(obj) {
	var val=$('#new_inscripcion-Jornadas').val();
	if (obj.checked) $('#new_inscripcion-Jornadas').val(val | obj.value);
	else $('#new_inscripcion-Jornadas').val(val & ~obj.value);
}

function listaNoInscritos() {
	$('#new_inscripcion-datagrid').datagrid('reload',{
		Operation: 'noinscritos',
		Prueba: workingData.prueba,
		where: ($('#new_inscripcion-search').val()==='---- Buscar ----')?'':$('#new_inscripcion-search').val()
	});
}

//TODO: estudiar por qué el "closed:true" en el data-options no funciona
$('#new_inscripcion-dialog').dialog({
	title:"A&ntilde;adir inscripciones a la prueba: '"+workingData.datosPrueba.Nombre+"'",
	closed:true,
	toolbar: '#new_inscripcion-datagrid-tools',
	buttons:'#new_inscripcion-buttons'
	});
	
// datagrid de inscripcion de participantes
$('#new_inscripcion-datagrid').datagrid({
    fit:true,
	url: 'database/inscripcionFunctions.php',
	queryParams: {
		Operation: 'noinscritos',
		Prueba: workingData.prueba,
		where: ($('#new_inscripcion-search').val()==='---- Buscar ----')?'':$('#new_inscripcion-search').val()
	},
	method: 'get',
	mode: 'remote',
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
    rowStyler:myRowStyler,
	multiple: true,
	fitColumns: true,
	singleSelect: false,
	view: scrollview,
	pageSize: 10,
	selectOnNavigation: false
});

// busqueda dinamica de equipo
$('#new_inscripcion-Equipo').combogrid({
	panelWidth: 300,
	panelHeight: 100,
	idField: 'ID',
	textField: 'Nombre',
	url: 'database/equiposFunctions.php',
	queryParams: {
		Prueba: workingData.prueba,
		Operation: 'enumerate'
	},
	method: 'get',
	mode: 'remote',
	required: true,
	columns: [[
	   	{field:'ID',hidden:'true'},
		{field:'Prueba',hidden:'true'},
		{field:'Nombre',title:'Nombre',width:20,align:'right'},
		{field:'Observaciones',title:'Observaciones',width:40,align:'right'},
	]],
	multiple: false,
	fitColumns: true,
	selectOnNavigation: false
});

// tooltips de los sub-formularios
addTooltip($('#new_inscripcion-search').linkbutton(),"Buscar posibles participantes<br /> que coincidan con el criterio de busqueda"); 
addTooltip($('#new_inscripcion-okBtn').linkbutton(),"Inscribir el/los perro(s) seleccionado(s) en la prueba"); 
addTooltip($('#new_inscripcion-doneBtn').linkbutton(),"Cerrar la ventana<br /> Volver a la ventana de gestion de inscripciones");
addTooltip($('#new_inscripcion-reloadBtn').linkbutton(),"Borrar seleccion. Refrescar lista");


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

</script>