<!-- 
frm_equipos.php

Copyright 2013-2014 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->
<!-- ventana de presentacion de los datos de los equipos de una prueba -->
<div id="team_datagrid-dialog" class="easyui-dialog" style="width:600px"
	data-options="iconCls:'icon-huella',modal:true,
				closed:true, closable:true,	collapsible:false, collapsed:false,
				minimizable:false, maximizable:false, resizable:false">
    
    <!-- BARRA DE TAREAS DE LA TABLA DE Equipos -->
	<div id="team_datagrid-toolbar" style="padding:5px 5px 35px 5px;">
	   	<span style="float:left;">
	   		<a id="team_datagrid-newBtn" href="#" class="easyui-linkbutton"	data-options="iconCls:'icon-add'"
	   			onclick="newTeam('#team_datagrid',$('#team_datagrid-search').val())">Nuevo</a>
	   		<a id="team_datagrid-editBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-edit'"
	   			onclick="editTeam('#team_datagrid')">Editar</a>
	   		<a id="team_datagrid-delBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-trash'"
	   			onclick="deleteTeam('#team_datagrid')">Borrar</a>
	   		<input id="team_datagrid-search" type="text" value="---- Buscar ----" class="search_textfield" onchange="buscaEquipos()" />
	   	</span>
	   	<span style="float:right;">
	   		<a id="team_datagrid-reloadBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-brush'"
	   			onclick="
	   	        	// clear selection and reload table
	   	    		$('#team_datagrid-search').val('---- Buscar ----');	
	   	            $('#team_datagrid').datagrid('load',{ where: '' });"
	   			>Limpiar</a>
	   	</span>
	</div>
	
	<!-- DECLARACION DE LA TABLA DE EQUIPOS -->
	<div style="height:250px">
		<table id="team_datagrid" class="easyui-datagrid"></table>
    </div>
	<!--  Botones de la tabla de equipos (solo el boton de cerrar) -->
	<div id="team_datagrid-buttons">
		<span style="float:right;padding:5px">
    		<a id="team_datagrid-doneBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-cancel'"
    			onclick="javascript:$('#team_datagrid-dialog').dialog('close')">Hecho</a>
    	</span>
	</div>
	
</div>	

<script type="text/javascript">

function buscaEquipos() {
	$('#team_datagrid').datagrid( 'load', { 
			where: ($('#team_datagrid-search').val()==='---- Buscar ----')? '' : $('#team_datagrid-search').val()
		});
}

//TODO: estudiar por qué el "closed:true" en el data-options no funciona
$('#team_datagrid-dialog').dialog({
	title:"Gestion de Equipos de la prueba: '"+workingData.datosPrueba.Nombre+"'",
    buttons: '#team_datagrid-buttons',
    toolbar: '#team_datagrid-toolbar',
	closed:true
	});

// datos de la tabla de equipos
$('#team_datagrid').datagrid({
	fit: true,
	url: '/agility/server/database/equiposFunctions.php?Operation=select&Prueba='+workingData.prueba,
	queryParams: { where: ''	},
	loadMsg: 'Actualizando lista de equipos ...',
	method: 'get',
	mode: 'remote',
    multiSort: true,
    remoteSort: true,
    columns: [[
        { field:'ID',			hidden:true },
        { field:'Prueba',		hidden:true },
        { field:'Nombre',		width:20, sortable:true,	title: 'Nombre:' },
        { field:'Observaciones',width:80, sortable:true,	title: 'Observaciones'}
    ]],
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    view: scrollview,
    pageSize: 10,
    rowStyler: myRowStyler, // function that personalize colors on alternate rows
    onDblClickRow:function() { editTeam('#team_datagrid'); } // on double click fireup editor dialog
});

// key handler
addKeyHandler('#team_datagrid',newTeam,editTeam,deleteTeam);
// - tooltips
addTooltip($('#team_datagrid-search'),"Mostrar equipos que coincidan con el criterio de busqueda");
addTooltip($('#team_datagrid-newBtn').linkbutton(),"Declarar un nuevo equipo para la prueba");
addTooltip($('#team_datagrid-editBtn').linkbutton(),"Editar nombre/observaciones del equipo seleccionado");
addTooltip($('#team_datagrid-delBtn').linkbutton(),"Eliminar datos del equipo en la prueba");
addTooltip($('#team_datagrid-reloadBtn').linkbutton(),"Borrar casilla de busqueda. Actualizar listado de equipos");
addTooltip($('#team_datagrid-doneBtn').linkbutton(),"Cerrar la ventana. Volver al menu anterior");
</script>
