<!-- 
frm_equipos.php

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
 
<?php
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =new Config();
?>
 
<!-- ventana de presentacion de los datos de los equipos de una prueba -->
<div id="team_datagrid-dialog" style="width:800px;height:600px">
	<!-- DECLARACION DE LA TABLA DE EQUIPOS -->
	<table id="team_datagrid" class="easyui-datagrid"></table>
</div>	

<!-- BARRA DE TAREAS DE LA TABLA DE Equipos -->
<div id="team_datagrid-toolbar" style="width:100%;display:inline-block">
   	<span style="float:left;padding:5px">
   		<a id="team_datagrid-newBtn" href="#" class="easyui-linkbutton"	data-options="iconCls:'icon-add'"
   			onclick="newTeam('#team_datagrid',$('#team_datagrid-search').val())"><?php _e('Nuevo');?></a>
   		<a id="team_datagrid-editBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-edit'"
   			onclick="editTeam('#team_datagrid')"><?php _e('Editar');?></a>
   		<a id="team_datagrid-delBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-trash'"
   			onclick="deleteTeam('#team_datagrid')"><?php _e('Borrar');?></a>
   		<input id="team_datagrid-search" type="text" value="---- Buscar ----" class="search_textfield" onchange="buscaEquipos()" />
   	</span>
   	<span style="float:right;padding:5px">
   	   	<a id="team_datagrid-checkBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-help'"
   			onclick="deleteTeam('#team_datagrid')"><?php _e('Verificar');?></a>
   		<a id="team_datagrid-reloadBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-brush'"
   			onclick="
   	        	// clear selection and reload table
   	    		$('#team_datagrid-search').val('---- Buscar ----');	
   	            $('#team_datagrid').datagrid('load',{ where: '' });"
   			>Limpiar</a>
   	</span>
</div>

<!--  Botones de la tabla de equipos (solo el boton de cerrar) -->
<div id="team_datagrid-buttons" style="width:100%;display:inline-block">
	<span style="float:right;padding:5px">
   		<a id="team_datagrid-doneBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-cancel'"
   			onclick="javascript:$('#team_datagrid-dialog').dialog('close')"><?php _e('Hecho');?></a>
   	</span>
</div>

<!-- FORMULARIO DE ASIGNACION DE EQUIPO A UNA INSCRIPCION -->
<div id="selteam-window" class="easyui-window" style="position:relative;width:350px;height:auto;padding:10px 10px">
	<div id="selteam-Layout" class="easyui-layout" data-options="fit:true'">
		<div id="selteam-Content" data-options="region:'north',border:'true'">
			<form id="selteam-Team">
        		<div class="fitem">
        			<label for="Nombre"><?php _e('Perro');?>:</label>
        			<input type="text" id="selteam-Nombre" name="Nombre" readonly="readonly"/>
        		</div>
        		<div class="fitem">
        			<label for="NombreGuia"><?php _e('Gu&iacute;a');?>:</label>
        			<input type="text" id="selteam-NombreGuia" name="NombreGuia" readonly="readonly"/>
        			<input type="hidden" id="selteam-Guia" name="Guia"/>
        		</div>
        		<div class="fitem">
        			<label for="NombreClub"><?php _e('Club');?>:</label>
        			<input type="text" id="selteam-NombreClub" name="NombreClub" readonly="readonly"/>
        			<input type="hidden" id="selteam-Club" name="Club"/>
        		</div>
        		<div class="fitem">
            		<label for="Search"><?php _e('Equipo');?>:</label>
            		<select id="selteam-Search" name="Search"></select>
        		</div>
			</form>
		</div> <!-- contenido -->
		<div data-options="region:'center'"></div>
		<div id="selteam-Buttons" data-options="region:'south',border:false" style="text-align:right;padding:5px 0 0;">
    	    <a id="selteam-okBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls:'icon-ok'" onclick="acceptSelectTeam()"><?php _e('Aceptar');?></a>
    	    <a id="selteam-cancelBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls:'icon-cancel'" onclick="$('#selteam-window').window('close');"><?php _e('Cancelar');?></a>
		</div>	<!-- botones -->
	</div> <!-- Layout -->
</div> <!-- Window -->

<script type="text/javascript">

//TODO: estudiar por qué el "closed:true" en el data-options no funciona
$('#team_datagrid-dialog').dialog({
	iconCls:'icon-huella',
	modal:true,
	closable:true,
	collapsible:false,
	collapsed:false,
	minimizable:false,
	maximizable:false,
	resizable:true,
	title:"<?php _e('Gesti&oacute;n de Equipos de la prueba');?>: '"+workingData.datosPrueba.Nombre+"'",
	closed:true
});

// datos de la tabla de equipos
$('#team_datagrid').datagrid({
	fit: true,
	url: '/agility/server/database/equiposFunctions.php',
	queryParams: { Operation:'select', Prueba: workingData.prueba, where: ''	},
	loadMsg: '<?php _e('Actualizando lista de equipos ...');?>',
    footer: '#team_datagrid-buttons',
    toolbar: '#team_datagrid-toolbar',
	method: 'get',
	mode: 'remote',
    multiSort: true,
    remoteSort: true,
    idField: 'ID',
    columns: [[
        { field:'ID',			hidden:true },
        { field:'Prueba',		hidden:true },
        { field:'Nombre',		width:20, sortable:true,	title: '<?php _e('Nombre');?>:' },
        { field:'Observaciones',width:80, sortable:true,	title: '<?php _e('Observaciones');?>'}
    ]],
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    view: scrollview,
    pageSize: 10,
    rowStyler: myRowStyler, // function that personalize colors on alternate rows
    onDblClickRow:function() { editTeam('#team_datagrid'); }, // on double click fireup editor dialog        
    // especificamos un formateador especial para desplegar la tabla de inscritos por equipo
    detailFormatter:function(idx,row){
        return '<div style="padding:2px"><table id="team-incripcion-datagrid-' + replaceAll(' ','_',row.ID) + '"></table></div>';
    },
    onExpandRow: function(idx,row) { showInscripcionesByTeam(idx,row); },
    
});

// key handler
addKeyHandler('#team_datagrid',newTeam,editTeam,deleteTeam);
// - tooltips
addTooltip($('#team_datagrid-search'),"<?php _e('Mostrar equipos que coincidan con el criterio de busqueda');?>");
addTooltip($('#team_datagrid-newBtn').linkbutton(),"<?php _e('Declarar un nuevo equipo para la prueba');?>");
addTooltip($('#team_datagrid-editBtn').linkbutton(),"<?php _e('Editar nombre/observaciones del equipo seleccionado');?>");
addTooltip($('#team_datagrid-delBtn').linkbutton(),"<?php _e('Eliminar datos del equipo en la prueba');?>");
addTooltip($('#team_datagrid-reloadBtn').linkbutton(),"<?php _e('Borrar casilla de busqueda. Actualizar listado de equipos');?>");
addTooltip($('#team_datagrid-checkBtn').linkbutton(),"<?php _e('Comprobar equipos. Indicar los problemas encontrados');?>");
addTooltip($('#team_datagrid-doneBtn').linkbutton(),"<?php _e('Cerrar la ventana. Volver al menu anterior');?>");

// mostrar las inscripciones agrupadas por equipos
function showInscripcionesByTeam(index,team){
	// - sub tabla de participantes asignados a un equipo
	var mySelf='#team-incripcion-datagrid-'+replaceAll(' ','_',team.ID);
	$(mySelf).datagrid({
    	width: '100%',
    	height: 100,
		title: '<?php _e('Inscripciones registradas en el equipo');?>: '+team.Nombre,
		    pagination: false,
	    rownumbers: false,
	    fitColumns: true,
	    singleSelect: true,
	    loadMsg: '<?php _e('Leyendo inscripciones....');?>',
	    height: 'auto',
		url: '/agility/server/database/inscripcionFunctions.php',
		queryParams: { Operation: 'inscritosbyteam', Prueba: workingData.prueba, Equipo: team.ID },
		method: 'get',
	    columns: [[
	            { field:'ID',		hidden:true }, // inscripcion ID
	            { field:'Prueba',	hidden:true }, // prueba ID
	            { field:'Jornadas',	hidden:true }, // bitmask de jornadas inscritas
	            { field:'Perro',	hidden:true }, // dog ID
	            { field:'Equipo',	hidden:true }, // only used on Team contests
	            { field:'Pagado', 	hidden:true }, // to store if handler paid :-)
	            { field:'Guia', 	hidden:true }, // Guia ID
	            { field:'Club',		hidden:true }, // Club ID
	            { field:'LOE_RRC',	hidden:true }, // LOE/RRC
	            { field:'Licencia', hidden:true }, // LOE/RRC
	            { field:'Club',		hidden:true }, // Club ID
	           	{ field:'Dorsal',	width:6,  sortable:true, align: 'right',	title: '<?php _e('Dorsal');?>' },
	           	{ field:'Nombre',	width:15, sortable:true, align: 'right',	title: '<?php _e('Nombre');?>' },
	           	{ field:'Categoria',width:4,  sortable:true, align: 'center',  	title: '<?php _e('Cat');?>.' },
	           	{ field:'Grado',	width:6,  sortable:true, align: 'center',  	title: '<?php _e('Grado');?>' },
	           	{ field:'NombreGuia',	width:25, sortable:true, align: 'right',	title: '<?php _e('Guía');?>' },
	           	{ field:'NombreClub',	width:15, sortable:true, align: 'right',	title: '<?php _e('Club');?>' },
	           	{ field:'NombreEquipo',	hidden:true },
	           	{ field:'Observaciones',width:15,            title: '<?php _e('Observaciones');?>' },
	           	{ field:'Celo',		width:4, align:'center', formatter: formatCelo,	 title: '<?php _e('Celo');?>' },
	            { field:'J1',		hidden:true},
	            { field:'J2',		hidden:true},
	            { field:'J3',		hidden:true},
	            { field:'J4',		hidden:true},
	            { field:'J5',		hidden:true},
	            { field:'J6',		hidden:true},
	            { field:'J7',		hidden:true},
	            { field:'J8',		hidden:true},
    	]],
        pagination: false,
        rownumbers: false,
        fitColumns: true,
    	// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    	rowStyler:myRowStyler,
    	// on double click fireup editor dialog
        onResize:function(){
            $('#team-datagrid').datagrid('fixDetailRowHeight',index);
        },
        onLoadSuccess:function(){
            setTimeout(function(){
                $('#team-datagrid').datagrid('fixDetailRowHeight',index);
            },0);
        },

        onDblClickRow:function() { $('#selteam-window').window('open'); }, // on double click fireup selteam dialog
	}); // end of inscritos-by-team_team_id
/*
	// definimos inline la sub-barra de tareas para que solo aparezca al desplegar el sub formulario
	// toolbar: '#perrosbyguia-toolbar', 
	var toolbar = [{
		id: 'perrosbyguia-newBtn'+guia.ID,
		text: 'Asignar perro',
		iconCls: 'icon-dog',
		handler: function(){assignPerroToGuia(mySelf,guia); }
	},{
		id: 'perrosbyguia-editBtn'+guia.ID,
		text: 'Editar datos',
		iconCls: 'icon-edit',
		handler: function(){editPerroFromGuia(mySelf,guia); }
	},{
		id: 'perrosbyguia-delBtn'+guia.ID,
		text: 'Desasignar perro',
		iconCls: 'icon-trash',
		handler: function(){delPerroFromGuia(mySelf,guia); }
	},{
		id: 'perrosbyguia-reloadBtn'+guia.ID,
		text: 'Actualizar',
		iconCls: 'icon-reload',
		align: 'right', // notice that this property is handled by our own 'buildToolbar extended method'
			handler: function(){ $(mySelf).datagrid('reload'); }    // reload the clubs data}
	}];
	$(mySelf).datagrid('buildToolbar',toolbar); // programmatically add toolbar to datagrid
	$('#guias-datagrid').datagrid('fixDetailRowHeight',index);
	// tooltips de los sub-formularios
	addTooltip($('#perrosbyguia-newBtn'+guia.ID).linkbutton(),"Asignar un nuevo perro a '"+guia.Nombre+"'"); 
	addTooltip($('#perrosbyguia-delBtn'+guia.ID).linkbutton(),"Eliminar asignaci&oacute;n del perro a '"+guia.Nombre+"'"); 
	addTooltip($('#perrosbyguia-editBtn'+guia.ID).linkbutton(),"Editar los datos del perro asignado a '"+guia.Nombre+"'");
	addTooltip($('#perrosbyguia-reloadBtn'+guia.ID).linkbutton(),"Actualizar la lista de perros del gu&iacute;a '"+guia.Nombre+"'");
*/
} // end of showPerrosByGuia

$('#selteam-window').window({
	title: '<?php _e('Selecciona nuevo equipo');?>',
	collapsible: false,
	minimizable: false,
	maximizable: false,
	closable: true,
	closed: true,
	shadow: true,
	modal: true
});

addTooltip($('#selteam-okBtn').linkbutton(),"<?php _e('Asignar el participante al equipo seleccionado');?>");
addTooltip($('#selteam-cancelBtn').linkbutton(),"<?php _e('Cancelar selecci&oacute;n. Cerrar ventana');?>");

$('#selteam-Search').combogrid({
	panelWidth: 450,
	panelHeight: 100,
	idField: 'ID',
	textField: 'Nombre',
	fit: true,
	url: '/agility/server/database/equiposFunctions.php',
	queryParams: { Operation:'select', Prueba: workingData.prueba, where: ''	},
	loadMsg: '<?php _e('Actualizando lista de equipos ...');?>',
	method: 'get',
	mode: 'remote',
	required: true,
	editable: isMobileDevice()?false:true, //disable keyboard deploy on mobile devices
	columns: [[
		{ field:'ID',			hidden:true },
		{ field:'Prueba',		hidden:true },
		{ field:'Nombre',		width:20, sortable:true,	title: '<?php _e('Nombre');?>:' },
		{ field:'Observaciones',width:80, sortable:true,	title: '<?php _e('Observaciones');?>'}
	]],
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	selectOnNavigation: false,
	onLoadSuccess: function(data) {
	}
});

function acceptSelectTeam() {
	// si no hay ninguna equipo valido seleccionada aborta
	var p=$('#selteam-Search').combogrid('grid').datagrid('getSelected');
	if (p==null) {
		// indica error
		$.messager.alert("Error","<?php _e('Debe indicar un equipo v&aacute;lida');?>","error");
		return;
	} else {
		alert("TODO: AcceptSelectTeam() write");
	}
	$('#selteam-window').window('close');
}
</script>
