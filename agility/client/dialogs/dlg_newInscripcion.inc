<!-- 
dlg_newInscripcion.inc

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

<!-- FORMULARIO DE ALTA DE INSCRIPCIONES EN UNA PRUEBA -->
<div id="new_inscripcion-dialog" style="width:640px;height:400px;padding:5px">
   	<!--  tabla que contiene el resultado de la seleccion del formulario -->
	<table id="new_inscripcion-datagrid"></table>
</div>

<!-- barra de tareas del dialogo -->
<div id="new_inscripcion-datagrid-tools" style="width:100%;display:inline-block">
	<span style="float:left;padding:5px">
	   	<label for="Buscar">Buscar: </label>
		<input name="Buscar" type="text" id="new_inscripcion-search" style="width:150px" 
			value="---- Buscar ----" class="search_textfield" onchange="listaNoInscritos();"/>
	</span>
	<span style="float:right;padding:5px">
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
   		
		
<!-- botones de datos auxiliares para la inscripcion -->
<div id=new_inscripcion-datagrid-footer style="width:100%;display:inline-block">
  	<form id="new_inscripcion-form">
		<span style="float:left;padding:5px">
   			<input id="new_inscripcion-Operation" name="Operation" type="hidden" value="insert"/>
   			<input id="new_inscripcion-Prueba" type="hidden" name="Prueba" value="0"/>
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
   			<input id="new_inscripcion-Celo" type="hidden" name="Celo" value="0"/>
        	<input id="new_inscripcion-Celo2"		name="Celo2" class="easyui-checkbox" type="checkbox" value="1" 
        		onclick="$('#new_inscripcion-Celo').val( $('#new_inscripcion-Celo2').is(':checked')?'1':'0');"/>
        	<input id="new_inscripcion-Jornadas"	type="hidden" name="Jornadas" value="0" />
        </span>
   	</form>
</div>

<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
<div id="new_inscripcion-buttons" style="width:100%;display:inline-block">
   	<span style="float:left">
   		<a id="new_inscripcion-teamBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-huella'"
   			onclick="newTeam2('#new_inscripcion-Equipo','nuevo equipo')">Nuevo Equipo</a>
   		<a id="new_inscripcion-dogBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-dog'"
   			onclick="newDog('#new_inscripcion-datagrid',$('#new_inscripcion-search').val())">Nuevo Perro</a>
   	</span>
   	<span style="float:right">
		<a id="new_inscripcion-okBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-ok'" onclick="insertInscripcion('#new_inscripcion-datagrid')">Inscribir</a>
   		<a id="new_inscripcion-doneBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-cancel'" onclick="javascript:$('#new_inscripcion-dialog').dialog('close')">Hecho</a>
   	</span>
</div>
	
<!-- barra de progreso de actualizacion de inscripciones -->
<div id="new_inscripcion-progresswindow">
	<p id="new_inscripcion-progresslabel" style="align:center">Inscribiendo a:</p>
	<div id="new_inscripcion-progressbar" style="align:center;"></div>
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
	iconCls:'icon-notes',
	modal:true,
	closed:true,
	closable:true,
	collapsible:false,
	minimizable:false,
	maximizable:false,
	resizable:false,
	title:"A&ntilde;adir inscripciones a la prueba: '"+workingData.datosPrueba.Nombre+"'",
	buttons:'#new_inscripcion-buttons',
	onOpen: newInscripcion_onOpenSuccess // on display setup jornada's 'checkboxes info
	});
	
// datagrid de inscripcion de participantes
$('#new_inscripcion-datagrid').datagrid({
    fit:true,
	url: '/agility/server/database/inscripcionFunctions.php',
	queryParams: {
		Operation: 'noinscritos',
		Prueba: workingData.prueba,
		where: ($('#new_inscripcion-search').val()==='---- Buscar ----')?'':$('#new_inscripcion-search').val()
	},
	method: 'get',
	mode: 'remote',
	loadMsg: 'Actualizando lista de perros ...',
	toolbar: '#new_inscripcion-datagrid-tools',
	footer: '#new_inscripcion-datagrid-footer',
	idField: 'ID',
	columns: [[
	   	{field:'ID',hidden:'true'},
		{field:'Guia',hidden:'true'},
		{field:'Club',hidden:'true'},
		{field:'Nombre', sortable:true, title:'Perro',width:20,align:'right'},
		{field:'Categoria', sortable:true, title:'Cat.',width:10,align:'center'},
		{field:'Grado', title:'Grado', width:10,align:'center'},
		{field:'NombreGuia', sortable:true, title:'Guia',width:40,align:'right'},
		{field:'NombreClub',title:'Club',width:20,align:'right'}
	]],
    rowStyler:myRowStyler,
	multiple: true,
	fitColumns: true,
	singleSelect: false,
	view: scrollview,
	pageSize: 10,
    multiSort: true,
    remoteSort: true,
    remoteFilter: true,
	selectOnNavigation: false
});

// busqueda dinamica de equipo
$('#new_inscripcion-Equipo').combogrid({
	panelWidth: 300,
	panelHeight: 100,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/equiposFunctions.php?Operation=enumerate&Prueba='+workingData.prueba,
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

// setup form on windows open
function newInscripcion_onOpenSuccess() {
    // retrieve info on jornadas
	var jornadas = $('#inscripciones-jornadas').datagrid('getData')['rows'];
	// default dog name from search text string
	var def = $('#inscripciones-datagrid-search').val();
	var val=0;

	// clear form
	$('#new_inscripcion-form').form('clear');
	
	// setup initial search string, prueba ID and Operation
	if (!strpos(def,"Buscar")) $('#new_inscripcion-search').val(def);// fill perro Name
	$('#new_inscripcion-Operation').val('insert');
	$('#new_inscripcion-Prueba').val(workingData.prueba);

	// reload datagrid with evaluated search value
	listaNoInscritos();
	
    // mark checked as default every named jornadas
	$('#new_inscripcion-J1').prop('checked',(jornadas[0].Nombre==="-- Sin asignar --")?false:true);
	$('#new_inscripcion-J2').prop('checked',(jornadas[1].Nombre==="-- Sin asignar --")?false:true);
	$('#new_inscripcion-J3').prop('checked',(jornadas[2].Nombre==="-- Sin asignar --")?false:true);
	$('#new_inscripcion-J4').prop('checked',(jornadas[3].Nombre==="-- Sin asignar --")?false:true);
	$('#new_inscripcion-J5').prop('checked',(jornadas[4].Nombre==="-- Sin asignar --")?false:true);
	$('#new_inscripcion-J6').prop('checked',(jornadas[5].Nombre==="-- Sin asignar --")?false:true);
	$('#new_inscripcion-J7').prop('checked',(jornadas[6].Nombre==="-- Sin asignar --")?false:true);
	$('#new_inscripcion-J8').prop('checked',(jornadas[7].Nombre==="-- Sin asignar --")?false:true);
	// evaluate enabled/disabled status by mean of check "Cerrada" and "-- Sin asignar --"
	$('#new_inscripcion-J1').prop('disabled', ! canInscribe(jornadas[0]) );
	$('#new_inscripcion-J2').prop('disabled', ! canInscribe(jornadas[1]) );
	$('#new_inscripcion-J3').prop('disabled', ! canInscribe(jornadas[2]) );
	$('#new_inscripcion-J4').prop('disabled', ! canInscribe(jornadas[3]) );
	$('#new_inscripcion-J5').prop('disabled', ! canInscribe(jornadas[4]) );
	$('#new_inscripcion-J6').prop('disabled', ! canInscribe(jornadas[5]) );
	$('#new_inscripcion-J7').prop('disabled', ! canInscribe(jornadas[6]) );
	$('#new_inscripcion-J8').prop('disabled', ! canInscribe(jornadas[7]) );
	// fix tooltip for jornadas
	addTooltip($('#new_inscripcion-J1'),'Jornada 1: '+jornadas[0].Nombre);
	addTooltip($('#new_inscripcion-J2'),'Jornada 2: '+jornadas[1].Nombre);
	addTooltip($('#new_inscripcion-J3'),'Jornada 3: '+jornadas[2].Nombre);
	addTooltip($('#new_inscripcion-J4'),'Jornada 4: '+jornadas[3].Nombre);
	addTooltip($('#new_inscripcion-J5'),'Jornada 5: '+jornadas[4].Nombre);
	addTooltip($('#new_inscripcion-J6'),'Jornada 6: '+jornadas[5].Nombre);
	addTooltip($('#new_inscripcion-J7'),'Jornada 7: '+jornadas[6].Nombre);
	addTooltip($('#new_inscripcion-J8'),'Jornada 8: '+jornadas[7].Nombre);
	// evaluate initial state for "Jornadas" field
	if ( canInscribe(jornadas[0]) ) val+=1;
	if ( canInscribe(jornadas[1]) ) val+=2;
	if ( canInscribe(jornadas[2]) ) val+=4;
	if ( canInscribe(jornadas[3]) ) val+=8;
	if ( canInscribe(jornadas[4]) ) val+=16;
	if ( canInscribe(jornadas[5]) ) val+=32;
	if ( canInscribe(jornadas[6]) ) val+=64;
	if ( canInscribe(jornadas[7]) ) val+=128;
	$('#new_inscripcion-Jornadas').val(val);
	// select up default team (as alphabetic order '-- Sin asignar --' comes first :-)
	$('#new_inscripcion-Equipo').combogrid('grid').datagrid('selectRow',0);
	// validation flag makes this tooltip disappear, so reinit on every window open
	addTooltip($('#new_inscripcion-search').linkbutton(),"Buscar posibles participantes<br /> que coincidan con el criterio de busqueda"); 
}

// tooltips de los sub-formularios
addTooltip($('#new_inscripcion-okBtn').linkbutton(),"Inscribir el/los perro(s) seleccionado(s) en la prueba"); 
addTooltip($('#new_inscripcion-doneBtn').linkbutton(),"Cerrar la ventana<br /> Volver a la ventana de gestion de inscripciones");
addTooltip($('#new_inscripcion-reloadBtn').linkbutton(),"Borrar seleccion. Refrescar lista");
addTooltip($('#new_inscripcion-teamBtn').linkbutton(),"Definir un nuevo equipo en esta prueba");
addTooltip($('#new_inscripcion-dogBtn').linkbutton(),"Dar de alta un nuevo perro en la BBDD");
addTooltip($('#new_inscripcion-Equipo').combogrid('textbox'),"Seleccionar el equipo <br />en el que se inscriben el/los perros marcados");

//ventana y barra de progreso de las inscripciones
$('#new_inscripcion-progressbar').progressbar({
	width: 300,
	value: 0
});
$('#new_inscripcion-progresswindow').window({
	 title:	'Procesando inscripciones...',
	 width:		450,
	 modal:		true,
	 collapsable:false,
	 minimizable:false,
	 maximizable:false,
	 closable:	false,
	 closed:	true
});

</script>