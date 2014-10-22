<!-- este panel se divide en dos partes:
- La primera, desplegable contiene la información de la prueba y jornadas disponibles
- La segunda, contiene la lista de inscritos a la prueba y la barra de botones de gestion de inscripciones
-->

<?php
require_once("dialogs/dlg_perros.inc");
require_once("dialogs/dlg_guias.inc");
require_once("dialogs/dlg_clubes.inc");
require_once("dialogs/dlg_jornadas.inc");
require_once("dialogs/dlg_equipos.inc");
require_once("frm_equipos.inc");
require_once("dialogs/dlg_newInscripcion.inc");
require_once("dialogs/dlg_editInscripcion.inc");
?>

<div id="hola">
<!-- PANEL INFORMATIVO SOBRE LA PRUEBA Y JORNADAS ASOCIADAS -->
<div id="inscripciones-info" class="easyui-panel" title="Informaci&oacute;n de la prueba">
	
	<div id="inscripciones-infolayout" class="easyui-layout" style="height:150px">
	
		<!-- PANEL IZQUIERDO: DATOS DE LA PRUEBA -->
		<div data-options="region:'west',title:'Datos de la Prueba',split:true,collapsed:false" 
			style="width:300px;padding:10px" class="c_inscripciones-datosprueba">
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
		<div data-options="region:'center',title:'Lista de jornadas de la prueba'" style="width:500px">
			<table id="inscripciones-jornadas"></table>
		</div>
		
	</div> 
</div> 

<!-- PANEL INFORMATIVO SOBRE LAS INSCRIPCIONES -->
<div id="inscripciones-list" class="easyui-panel" style="width:auto;height:400px;"
	data-options="noHeader:true, border:true, closable:false, collapsible:false, collapsed:false,">
	
	<!-- DECLARACION DE LA TABLA DE INSCRIPCIONES -->
	<table id="inscripciones-datagrid"></table>

	<!-- BARRA DE TAREAS DE LA TABLA DE INSCRIPCIONES -->
	<div id="inscripciones-toolbar" style="padding:5px 5px 35px 5px">
	   	<span style="float:left"> <!-- estos elementos deben estar alineados a la izquierda -->
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
	   	<span style="float:right"> 	<!-- estos elementos deben estar alineados a la derecha -->
	    	<a id="inscripciones-reorderBtn" href="#" class="easyui-linkbutton"
	    		data-options="iconCls:'icon-updown'" 
	    		onclick="reorderInscripciones(workingData.prueba)">Reordenar</a>
	    	<a id="inscripciones-teamBtn" href="#" class="easyui-linkbutton"
	    		data-options="iconCls:'icon-huella'" 
	    		onclick="openTeamWindow(workingData.prueba)">Equipos</a>
	    	<a id="inscripciones-printBtn" href="#" class="easyui-linkbutton"
	    		data-options="iconCls:'icon-print'" 
	    		>Imprimir</a> <!-- onClick() is handled below -->
	   		<a id="inscripciones-reloadBtn" href="#" class="easyui-linkbutton"
	   			data-options="iconCls:'icon-brush'"
	   			onclick="
	   	        	// clear selection and reload table
	   	    		$('#inscripciones-datagrid-search').val('---- Buscar ----');
	   	            $('#inscripciones-datagrid').datagrid('load',{ where: '' });"
	   		>Limpiar</a>
	   	</span>
	</div>
</div>

</div> <!-- id="hola" -->

<script type="text/javascript">

$('#inscripciones-info').panel({
	border:true,
	closable:false,
	closed:false,
	collapsible:true,
	collapsed:true
});

$('#inscripciones-infolayout').layout();
$('#inscripciones-pruebas').form('load','/agility/server/database/pruebaFunctions.php?Operation=getbyid&ID='+workingData.prueba);
$('#inscripciones-jornadas').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: true,
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
      		{ field:'Nombre',		width:70, sortable:false,   align:'right',  title: 'Nombre/Comentario' },
      		{ field:'Fecha',		width:40, sortable:false,	align:'right',  title: 'Fecha: ' },
      		{ field:'Hora',			width:30, sortable:false,	align:'right',  title: 'Hora.  ' },
      		{ field:'Grado1',		width:15, sortable:false,	align:'center', title: 'G-I    ' },
      		{ field:'Grado2',		width:15, sortable:false,	align:'center', title: 'G-II   ' },
      		{ field:'Grado3',		width:15, sortable:false,	align:'center', title: 'G-III  ' },
      		{ field:'Equipos3',		width:15, sortable:false,	align:'center', title: 'Eq. 3/4' },
      		{ field:'Equipos4',		width:15, sortable:false,	align:'center', title: 'Eq. Conj.' },
      		{ field:'PreAgility',	width:15, sortable:false,	align:'center', title: 'P.A. -1' },
      		{ field:'PreAgility2',	width:15, sortable:false,	align:'center', title: 'P.A. -2' },
      		{ field:'KO',			width:15, sortable:false,	align:'center', title: 'K.O.   ' },
      		{ field:'Especial',		width:15, sortable:false,	align:'center', title: 'Especial' },
      		{ field:'Cerrada',		width:20, sortable:false,	align:'center', title: 'Cerrada', formatter:identificaJornada }
    ]],
    rowStyler:myRowStyler,
	// on double click fireup editor dialog
	onDblClickRow:function(idx,row) { //idx: selected row index; row selected row data
    	editJornadaFromPrueba(workingData.prueba,'#inscripciones-jornadas');
	}
});

//activa teclas up/down para navegar por el panel de gestion de jornadas
$('#inscripciones-jornadas').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
    function selectRow(t,up){
    	var count = t.datagrid('getRows').length;    // row count
    	var selected = t.datagrid('getSelected');
    	if (selected){
        	var index = t.datagrid('getRowIndex', selected);
        	index = index + (up ? -1 : 1);
        	if (index < 0) index = 0;
        	if (index >= count) index = count - 1;
        	t.datagrid('clearSelections');
        	t.datagrid('selectRow', index);
    	} else {
        	t.datagrid('selectRow', (up ? count-1 : 0));
    	}
	}
	var t = $('#inscripciones-jornadas');
    switch(e.keyCode){
    case 38:	/* Up */	selectRow(t,true); return false;
    case 40:    /* Down */	selectRow(t,false); return false;
    case 13:	/* Enter */	editJornadaFromPrueba(workingData.prueba,'#inscripciones-jornadas'); return false;
    }
});

// esta funcion anyade un id al campo de jornada de manera que sea identificable
function identificaJornada(val,row,index) {
	return '<span id="jornada_cerrada-'+parseInt(index+1)+'" >'+val+'</span>';
}

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
        { field:'Equipo',	hidden:true }, // only used on Team contests
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
    	{ field:'NombreEquipo',	width:10, sortable:true, align: 'right',	title: 'Equipo' },
    	{ field:'Observaciones',width:15,            title: 'Observaciones' },
    	{ field:'Celo',		width:4, align:'center', title: 'Celo' },
        { field:'J1',		width:2, align:'center', title: 'J1'},
        { field:'J2',		width:2, align:'center', title: 'J2'},
        { field:'J3',		width:2, align:'center', title: 'J3'},
        { field:'J4',		width:2, align:'center', title: 'J4'},
        { field:'J5',		width:2, align:'center', title: 'J5'},
        { field:'J6',		width:2, align:'center', title: 'J6'},
        { field:'J7',		width:2, align:'center', title: 'J7'},
        { field:'J8',		width:2, align:'center', title: 'J8'},
    ]],
    // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    rowStyler:myRowStyler,
	// on double click fireup editor dialog
    onDblClickRow:function() { 
        editInscripcion();
    }
});


// key handler
addKeyHandler('#inscripciones-datagrid',newInscripcion,editInscripcion,deleteInscripcion);
// tooltips
addTooltip($('#inscripciones-newBtn').linkbutton(),"Registrar nueva(s) inscripciones"); 
addTooltip($('#inscripciones-editBtn').linkbutton(),"Modificar la inscripción seleccionada");
addTooltip($('#inscripciones-delBtn').linkbutton(),"Eliminar la inscripción seleccionada de la BBDD");
addTooltip($('#inscripciones-reorderBtn').linkbutton(),"Reasignar Dorsales por orden de Club,Categoria,Grado, y Nombre");
addTooltip($('#inscripciones-teamBtn').linkbutton(),"Abrir la ventana de gest&oacute;n de equipos de esta prueba");
addTooltip($('#inscripciones-printBtn').linkbutton(),"Imprimir la lista de inscritos en la prueba");
addTooltip($('#inscripciones-reloadBtn').linkbutton(),"Borrar la casilla de b&uacute;squeda<br/>Actualizar la lista de inscripciones para la prueba");
addTooltip($('#inscripciones-datagrid-search'),"Buscar inscripciones que coincidan con el texto indicado");

// special handling for printing inscritos
$('#inscripciones-printBtn').on("click", function () {
	$.fileDownload(
		'/agility/server/pdf/print_inscritosByPrueba.php',
		{
			httpMethod: 'GET',
			data: { Prueba: workingData.prueba},
	        preparingMessageHtml: "We are preparing your report, please wait...",
	        failMessageHtml: "There was a problem generating your report, please try again."
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
});


</script>