<!-- 
frm_inscripciones2.php

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
 
<!-- este panel se divide en dos partes:
- La primera, desplegable contiene la información de la prueba y jornadas disponibles
- La segunda, contiene la lista de inscritos a la prueba y la barra de botones de gestion de inscripciones
-->

<?php
require_once(__DIR__."/dialogs/dlg_perros.inc");
require_once(__DIR__."/dialogs/dlg_guias.inc");
require_once(__DIR__."/dialogs/dlg_clubes.inc");
require_once(__DIR__."/dialogs/dlg_jornadas.inc");
require_once(__DIR__."/dialogs/dlg_equipos.inc");
require_once(__DIR__."/frm_equipos.inc");
require_once(__DIR__."/dialogs/dlg_newInscripcion.inc");
require_once(__DIR__."/dialogs/dlg_editInscripcion.inc");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>

<!-- PANEL INFORMATIVO SOBRE LA PRUEBA Y JORNADAS ASOCIADAS -->
<div id="inscripciones-info" style="width:100%">
	
	<div id="inscripciones-infolayout" class="easyui-layout" style="height:150px">
	
		<!-- PANEL IZQUIERDO: DATOS DE LA PRUEBA -->
		<div data-options="region:'west',title:'<?php _e('Datos de la Prueba');?>',split:true,collapsed:false,collapsible:false" 
			style="width:30%;padding:10px" class="c_inscripciones-datosprueba">
			<form id="inscripciones-pruebas" method="get" >
			<input type="hidden" name="ID"/>
			<input type="hidden" name="Club"/>
			<input type="hidden" name="Ubicacion"/>
			<input type="hidden" name="Triptico"/>
			<input type="hidden" name="Cartel"/>
			<input type="hidden" name="Cerrada"/>
			<p>
			<label for="Nombre" style="font-weight:bold">Denominaci&oacute;n:</label>
			<input id="inscripciones-pnombre" type="text" name="Nombre" disabled="disabled" size="19"/>
			</p>
			<p>
			<label for="Club" style="font-weight:bold">Club Organizador:</label>
			<input id="inscripciones-pclub" type="text" name="NombreClub" disabled="disabled" size="15"/>
			</p>
			<p>
			<label for="Observaciones" style="font-weight:bold">Observaciones:</label>
			<input id="inscripciones-pcomments" type="text" name="Observaciones" disabled="disabled" size="33"/>
			</p>
			</form>
		</div>
		
		<!-- PANEL DERECHO: LISTA DE JORNADAS -->
		<div data-options="region:'center',title:'Lista de jornadas de la prueba'" style="width:70%">
			<table id="inscripciones-jornadas"></table>
		</div>
		
	</div> 
</div> 

<!-- PANEL INFORMATIVO SOBRE LAS INSCRIPCIONES -->
<div id="inscripciones-list" class="easyui-panel" style="width:100%;height:auto"
	data-options="noHeader:true,border:true,closable:false,collapsible:false,collapsed:false">
	<!-- DECLARACION DE LA TABLA DE INSCRIPCIONES -->
	<table id="inscripciones-datagrid"></table>
</div>

<!-- BARRA DE TAREAS DE LA TABLA DE INSCRIPCIONES -->
<div id="inscripciones-toolbar" style="width:100%;display:inline-block">
   	<span style="float:left;padding:5px"> <!-- estos elementos deben estar alineados a la izquierda -->
   		<a id="inscripciones-newBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-notes'"
   			onclick="newInscripcion($('#inscripciones-datagrid','#inscripciones-datagrid-search').val())">Nuevo</a>
   		<a id="inscripciones-editBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-edit'"
   			onclick="editInscripcion('#inscripciones-datagrid')">Editar</a>
   		<a id="inscripciones-delBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-trash'"
   			onclick="deleteInscripcion('#inscripciones-datagrid')">Borrar</a>
   		<input id="inscripciones-datagrid-search" type="text" value="---- Buscar ----" class="search_textfield"	/>
   	</span>
   	<span style="float:right;padding:5px"> 	<!-- estos elementos deben estar alineados a la derecha -->
    	<a id="inscripciones-reorderBtn" href="#" class="easyui-linkbutton"
    		data-options="iconCls:'icon-updown'" 
    		onclick="reorderInscripciones(workingData.prueba)">Reordenar</a>
    	<a id="inscripciones-teamBtn" href="#" class="easyui-linkbutton"
    		data-options="iconCls:'icon-huella'" 
    		onclick="openTeamWindow(workingData.prueba)">Equipos</a>
    	<a id="inscripciones-printBtn" href="#" class="easyui-linkbutton"
    		data-options="iconCls:'icon-print'" onclick="printInscripciones();"
    		>Imprimir</a>
   		<a id="inscripciones-reloadBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-brush'"
   			onclick="
   	        	// clear selection and reload table
   	    		$('#inscripciones-datagrid-search').val('---- Buscar ----');
				reloadWithSearch('#inscripciones-datagrid','inscritos');
   	       " >Limpiar</a>
   	</span>
</div>

<script type="text/javascript">
$('#inscripciones-info').panel({
	title:'Informaci&oacute;n de la prueba',
	border:true,
	closable:true,
	closed:false,
	collapsible:false,
	collapsed:false,
	// TODO: get this working :-(
	// onExpand: function() {$('#inscripciones-list').panel('options').height='450px';},
	// onCollapse: function() {$('#inscripciones-list').panel('options').height='600px';}
	onClose: function() {$('#inscripciones-datagrid').datagrid('getPanel').panel('close'); }
});

$('#inscripciones-infolayout').layout();
$('#inscripciones-pruebas').form('load','/agility/server/database/pruebaFunctions.php?Operation=getbyid&ID='+workingData.prueba);
$('#inscripciones-jornadas').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades especificas del datagrid
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
	url: '/agility/server/database/jornadaFunctions.php?Operation=select&Prueba='+workingData.prueba,
	method: 'get',
	loadMsg: 'Actualizando datos de las jornadas...',
    columns:[[
            { field:'ID',			hidden:true }, // ID de la jornada
      	    { field:'Prueba',		hidden:true }, // ID de la prueba
      	    { field:'Numero',		width:10, sortable:false,	align:'center', title: '#'},
			{ field:'Nombre',		width:60, sortable:false,   align:'right',  title: '<?php _e('Nombre/Comentario');?>' },
			{ field:'Fecha',		width:30, sortable:false,	align:'right',  title: '<?php _e('Fecha');?>: ' },
			{ field:'Hora',			width:25, sortable:false,	align:'right',  title: '<?php _e('Hora');?>:  ' },
			{ field:'Grado1',		width:15, sortable:false, formatter: formatOk,	align:'center', title: 'G-I  ' },
			{ field:'Grado2',		width:15, sortable:false, formatter: formatOk,	align:'center', title: 'G-II ' },
			{ field:'Grado3',		width:15, sortable:false, formatter: formatOk,	align:'center', title: 'G-III' },
			{ field:'Equipos3',		width:15, sortable:false, formatter: formatOk,	align:'center', title: 'Eq. 3' },
			{ field:'Equipos4',		width:15, sortable:false, formatter: formatOk,	align:'center', title: 'Eq. 4' },
			{ field:'Open',			width:15, sortable:false, formatter: formatOk,	align:'center', title: 'Open ' },
			{ field:'PreAgility',	width:15, sortable:false, formatter: formatOk,	align:'center', title: 'PA. 1' },
			{ field:'PreAgility2',	width:15, sortable:false, formatter: formatOk,	align:'center', title: 'PA. 2' },
			{ field:'KO',			width:15, sortable:false, formatter: formatOk,	align:'center', title: 'K.O. ' },
			{ field:'Especial',		width:15, sortable:false, formatter: formatOk,	align:'center', title: '<?php _e('Especial');?>' },
			{ field:'Cerrada',		width:10, sortable:false, formatter: formatCerrada,	align:'center', title: '<?php _e('Cerrada');?>' }
    ]],
    rowStyler:myRowStyler,
	// on click mark as active; on double click fireup editor dialog
	onClickRow: function(idx,row) { setJornada(row); },	
	onDblClickRow:function(idx,row) { //idx: selected row index; row selected row data
		setJornada(row);
    	editJornadaFromPrueba(workingData.prueba,row);
	}
});

// datos de la tabla de inscripciones
// - tabla
$('#inscripciones-datagrid').datagrid({
	title: 'Listado de inscritos en la prueba',
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
    expansible: false,
	collapsed: false,
	// propiedades especificas del datagrid
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    view: scrollview,
    pageSize: 50,
    multiSort: true,
    remoteSort: true,
	url: '/agility/server/database/inscripcionFunctions.php?Operation=inscritos&Prueba='+workingData.prueba,
	method: 'get',
	loadMsg: 'Actualizando datos de inscripciones....',
    toolbar: '#inscripciones-toolbar',
    columns: [[
        { field:'ID',		hidden:true }, // inscripcion ID
        { field:'Prueba',	hidden:true }, // prueba ID
        { field:'Jornadas',	hidden:true }, // bitmask de jornadas inscritas
        { field:'Perro',	hidden:true }, // dog ID
        { field:'Pagado', 	hidden:true }, // to store if handler paid :-)
        { field:'Guia', 	hidden:true }, // Guia ID
        { field:'Club',		hidden:true }, // Club ID
        { field:'LOE_RRC',	hidden:true }, // LOE/RRC
        { field:'Licencia', hidden:true }, // LOE/RRC
        { field:'Club',		hidden:true }, // Club ID
    	{ field:'Dorsal',	width:6,  sortable:true, align: 'right',	title: 'Dorsal' },
    	{ field:'Nombre',	width:15, sortable:true, align: 'right',	title: 'Nombre' },
    	{ field:'Categoria',width:4,  sortable:true, align: 'center',  	title: 'Cat.' },
    	{ field:'Grado',	width:6,  sortable:true, align: 'center',  	title: 'Grado' },
    	{ field:'NombreGuia',	width:25, sortable:true, align: 'right',	title: 'Guia' },
    	{ field:'NombreClub',	width:15, sortable:true, align: 'right',	title: 'Club' },
		{ field:'Observaciones',width:10,            title: '<?php _e('Observaciones');?>' },
    	{ field:'Celo',		width:4, align:'center', formatter: formatCelo,	 title: 'Celo' },
        { field:'J1',		width:2, align:'center', formatter: formatOk,	 title: 'J1'},
        { field:'J2',		width:2, align:'center', formatter: formatOk,	 title: 'J2'},
        { field:'J3',		width:2, align:'center', formatter: formatOk,	 title: 'J3'},
        { field:'J4',		width:2, align:'center', formatter: formatOk,	 title: 'J4'},
        { field:'J5',		width:2, align:'center', formatter: formatOk,	 title: 'J5'},
        { field:'J6',		width:2, align:'center', formatter: formatOk,	 title: 'J6'},
        { field:'J7',		width:2, align:'center', formatter: formatOk,	 title: 'J7'},
        { field:'J8',		width:2, align:'center', formatter: formatOk,	 title: 'J8'},
    ]],
    // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    rowStyler:myRowStyler,
	// on double click fireup editor dialog
    onDblClickRow:function() { 
        editInscripcion();
    }
});


// key handler
addSimpleKeyHandler('#inscripciones-jornadas',null,editJornadaFromPrueba);
addKeyHandler('#inscripciones-datagrid',newInscripcion,editInscripcion,deleteInscripcion);

// tooltips
addTooltip($('#inscripciones-newBtn').linkbutton(),"Registrar nueva(s) inscripciones"); 
addTooltip($('#inscripciones-editBtn').linkbutton(),"Modificar la inscripción seleccionada");
addTooltip($('#inscripciones-delBtn').linkbutton(),"Eliminar la inscripción seleccionada de la BBDD");
addTooltip($('#inscripciones-reorderBtn').linkbutton(),"Reasignar dorsales por orden de Club,Categoria,Grado, y Nombre");
addTooltip($('#inscripciones-teamBtn').linkbutton(),"Abrir la ventana de gesti&oacute;n de equipos de esta prueba");
addTooltip($('#inscripciones-printBtn').linkbutton(),"Imprimir la lista de inscritos en la prueba");
addTooltip($('#inscripciones-reloadBtn').linkbutton(),"Borrar la casilla de b&uacute;squeda<br/>Actualizar la lista de inscripciones para la prueba");
addTooltip($('#inscripciones-datagrid-search'),"Buscar inscripciones que coincidan con el texto indicado");

</script>