<!--
dlg_newInscripcion.inc

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<!-- FORMULARIO DE ALTA DE INSCRIPCIONES EN UNA PRUEBA -->
<div id="new_inscripcion-dialog" style="width:640px;height:400px;padding:5px">
   	<!--  tabla que contiene el resultado de la seleccion del formulario -->
	<table id="new_inscripcion-datagrid"></table>
</div>

<!-- barra de tareas del dialogo -->
<div id="new_inscripcion-datagrid-tools" style="width:100%;display:inline-block">
	<span style="float:left;padding:5px">
	   	<label for="Buscar"><?php _e('Search'); ?>: </label>
		<input name="Buscar" type="text" class="easyui-textbox" id="new_inscripcion-datagrid-search" style="width:150px" value="<?php _e('-- Search --'); ?>"
			class="search_textfield" onfocus="handleSearchBox(this,true);" onblur="handleSearchBox(this,false);"
			   onchange="reloadWithSearch('#new_inscripcion-datagrid','noinscritos',false);"/>
	</span>
	<span style="float:right;padding:5px">
		<a id="new_inscripcion-reloadBtn" href="#" class="easyui-linkbutton"
			data-options="iconCls:'icon-brush'"
			onclick="
	        	// clear selection and reload table
				$('#new_inscripcion-datagrid').datagrid('unselectAll');
				reloadWithSearch('#new_inscripcion-datagrid','noinscritos',true);
	            "
			><?php _e('Clear'); ?></a>
	</span>
</div>
   		
		
<!-- botones de datos auxiliares para la inscripcion -->
<div id=new_inscripcion-datagrid-footer style="width:100%;display:inline-block">
  	<form id="new_inscripcion-form">
		<span style="float:left;padding:5px">
   			<input id="new_inscripcion-Operation" name="Operation" type="hidden" value="insert"/>
   			<input id="new_inscripcion-Prueba" type="hidden" name="Prueba" value="0"/>
			<?php _e('Journeys'); ?>:
        	<input id="new_inscripcion-J1" 			name="J1" class="easyui-checkbox" type="checkbox" value="1" onclick="evalJornadas(this);"/>
        	<label id="new_inscripcion-J1lbl" 		for="new_inscripcion-J1">1</label>
        	<input id="new_inscripcion-J2" 			name="J2" class="easyui-checkbox" type="checkbox" value="2" onclick="evalJornadas(this);"/>
        	<label id="new_inscripcion-J2lbl" 		for="new_inscripcion-J2">2</label>
       		<input id="new_inscripcion-J3"			name="J3" class="easyui-checkbox" type="checkbox" value="4" onclick="evalJornadas(this);"/>
       		<label id="new_inscripcion-J3lbl"		for="new_inscripcion-J3">3</label>
       		<input id="new_inscripcion-J4"			name="J4" class="easyui-checkbox" type="checkbox" value="8" onclick="evalJornadas(this);"/>
 	   		<label id="new_inscripcion-J4lbl"		for="new_inscripcion-J4">4</label>
       		<input id="new_inscripcion-J5"			name="J5" class="easyui-checkbox" type="checkbox" value="16" onclick="evalJornadas(this);"/>
        	<label id="new_inscripcion-J5lbl" 		for="new_inscripcion-J5">5</label>
        	<input id="new_inscripcion-J6" 			name="J6" class="easyui-checkbox" type="checkbox" value="32" onclick="evalJornadas(this);"/>
        	<label id="new_inscripcion-J6lbl" 		for="new_inscripcion-J6">6</label>
        	<input id="new_inscripcion-J7" 			name="J7" class="easyui-checkbox" type="checkbox" value="64" onclick="evalJornadas(this);"/>
        	<label id="new_inscripcion-J7lbl" 		for="new_inscripcion-J7">7</label>
        	<input id="new_inscripcion-J8" 			name="J8" class="easyui-checkbox" type="checkbox" value="128" onclick="evalJornadas(this);"/>
       		<label id="new_inscripcion-J8lbl" 		for="new_inscripcion-J8">8</label>
        </span>
        <span style="float:right;padding:5px">
        	<label id="new_inscripcion-Celolbl"	for="new_inscripcion-Celo"><?php _e('Heat'); ?>:</label>
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
   		<a id="new_inscripcion-dogBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-dog'"
   			onclick="newDog('#new_inscripcion-datagrid',$('#new_inscripcion-datagrid-search').val())"><?php _e('New dog'); ?></a>
   	</span>
   	<span style="float:right">
		<a id="new_inscripcion-okBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-ok'" onclick="insertInscripcion('#new_inscripcion-datagrid')"><?php _e('Inscribe'); ?></a>
   		<a id="new_inscripcion-doneBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-cancel'" onclick="$('#new_inscripcion-dialog').dialog('close')"><?php _e('Done'); ?></a>
   	</span>
</div>

<!-- barra de progreso de actualizacion de inscripciones -->
<div id="new_inscripcion-progresswindow" class="easyui-window"
	data-options="title:'<?php _e('Processing inscriptions'); ?>...',width:300,modal:true,collapsable:false,minimizable:false,maximizable:false,closable:false,closed:true">
	<p id="new_inscripcion-progresslabel" style="text-align:center"><?php _e('Enrolling'); ?>:</p>
	<div id="new_inscripcion-progressbar" class="easyui-progressbar" style="width:300px;text-align:center;" data-options="value:0"></div>
</div>

<script type="text/javascript">

// ajustamos el valor de la jornada en funcion de la seleccion
function evalJornadas(obj) {
	var val=$('#new_inscripcion-Jornadas').val();
	if (obj.checked) $('#new_inscripcion-Jornadas').val(val | obj.value);
	else $('#new_inscripcion-Jornadas').val(val & ~obj.value);
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
	title:'<?php _e("Add inscriptions to contest"); ?>'+": '"+workingData.datosPrueba.Nombre+"'",
	buttons:'#new_inscripcion-buttons',
	onOpen: newInscripcion_onOpenSuccess, // on display setup jornada's 'checkboxes info
	onClose: function() {
	        $('#new_inscripcion-datagrid').datagrid('unselectAll');
            autoBackupDatabase(1,"");
	    }
	});
	
// datagrid de inscripcion de participantes
$('#new_inscripcion-datagrid').datagrid({
    fit:true,
	url: '/agility/server/database/inscripcionFunctions.php',
	queryParams: {
		Operation: 'noinscritos',
		Prueba: workingData.prueba,
        Federation: workingData.federation
	},
	method: 'get',
	mode: 'remote',
	loadMsg: '<?php _e('Updating dog list'); ?>'+' ...',
	toolbar: '#new_inscripcion-datagrid-tools',
	footer: '#new_inscripcion-datagrid-footer',
	idField: 'ID',
	columns: [[
	   	{field:'ID',hidden:'true'},
		{field:'Guia',hidden:'true'},
		{field:'Club',hidden:'true'},
        {field:'Nombre', sortable:true, title:'<?php _e('Dog'); ?>',width:20,align:'right',formatter:formatBold},
        {field:'Licencia', sortable:true, title:'<?php _e('Lic'); ?>.',width:10,align:'right'},
		{field:'Categoria', sortable:true, title:'<?php _e('Cat'); ?>.',width:5,align:'center',formatter:formatCategoria},
		{field:'Grado', title:'<?php _e('Grade'); ?>', width:5,align:'center',formatter:formatGrado},
		{field:'NombreGuia', sortable:true, title:'<?php _e('Handler'); ?>',width:35,align:'right'},
		{field:'NombreClub',title:'<?php _e('Club'); ?>',width:20,align:'right'}
	]],
	rownumbers:true,
    rowStyler:myRowStyler,
	fitColumns: true,
	singleSelect: false,
	view: scrollview,
	multiSort: true,
	remoteSort: true,
	pageSize: 25,
	onDblClickRow: function(idx,row) {insertInscripcion('#new_inscripcion-datagrid'); },
	onLoadSuccess: function(data) {
	    if (data.total===1) $('#new_inscripcion-datagrid').datagrid('selectRow',0);
	    return true;
    }
});

// setup form on windows open
function newInscripcion_onOpenSuccess() {
    // retrieve info on jornadas
	var jornadas = $('#inscripciones-jornadas').datagrid('getData')['rows'];

	// clear form
	$('#new_inscripcion-form').form('clear');
	
	// setup initial search string, prueba ID and Operation
	$('#new_inscripcion-Operation').val('insert');
	$('#new_inscripcion-Prueba').val(workingData.prueba);

	var val=0;
    // mark checked as default every named jornadas
    var j1=$('#new_inscripcion-J1');
    var j2=$('#new_inscripcion-J2');
    var j3=$('#new_inscripcion-J3');
    var j4=$('#new_inscripcion-J4');
    var j5=$('#new_inscripcion-J5');
    var j6=$('#new_inscripcion-J6');
    var j7=$('#new_inscripcion-J7');
    var j8=$('#new_inscripcion-J8');
	j1.prop('checked', canInscribe(jornadas[0]) );
	j2.prop('checked', canInscribe(jornadas[1]) );
	j3.prop('checked', canInscribe(jornadas[2]) );
	j4.prop('checked', canInscribe(jornadas[3]) );
	j5.prop('checked', canInscribe(jornadas[4]) );
	j6.prop('checked', canInscribe(jornadas[5]) );
	j7.prop('checked', canInscribe(jornadas[6]) );
	j8.prop('checked', canInscribe(jornadas[7]) );
	// evaluate enabled/disabled status by mean of check "Cerrada" and "-- Sin asignar --"
	j1.prop('disabled', ! canInscribe(jornadas[0]) );
	j2.prop('disabled', ! canInscribe(jornadas[1]) );
	j3.prop('disabled', ! canInscribe(jornadas[2]) );
	j4.prop('disabled', ! canInscribe(jornadas[3]) );
	j5.prop('disabled', ! canInscribe(jornadas[4]) );
	j6.prop('disabled', ! canInscribe(jornadas[5]) );
	j7.prop('disabled', ! canInscribe(jornadas[6]) );
	j8.prop('disabled', ! canInscribe(jornadas[7]) );
	// fix tooltip for jornadas
	addTooltip(j1,'Jornada 1: '+jornadas[0].Nombre);
	addTooltip(j2,'Jornada 2: '+jornadas[1].Nombre);
	addTooltip(j3,'Jornada 3: '+jornadas[2].Nombre);
	addTooltip(j4,'Jornada 4: '+jornadas[3].Nombre);
	addTooltip(j5,'Jornada 5: '+jornadas[4].Nombre);
	addTooltip(j6,'Jornada 6: '+jornadas[5].Nombre);
	addTooltip(j7,'Jornada 7: '+jornadas[6].Nombre);
	addTooltip(j8,'Jornada 8: '+jornadas[7].Nombre);
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
	// validation flag makes this tooltip disappear, so reinit on every window open
	addTooltip($('#new_inscripcion-datagrid-search').linkbutton(),'<?php _e("Search available competitors"); ?>'+"<br />"+'<?php _e("matching search criteria"); ?>');
}

// tooltips de los sub-formularios
addTooltip($('#new_inscripcion-okBtn').linkbutton(),'<?php _e("Inscribe selected dog(s) into contest"); ?>');
addTooltip($('#new_inscripcion-doneBtn').linkbutton(),'<?php _e("Close window"); ?>'+"<br />"+'<?php _e("Return to inscription handling form"); ?>');
addTooltip($('#new_inscripcion-reloadBtn').linkbutton(),'<?php _e("Clear selection. Refresh list"); ?>');
addTooltip($('#new_inscripcion-dogBtn').linkbutton(),'<?php _e("Insert new dog into database"); ?>');



</script>
