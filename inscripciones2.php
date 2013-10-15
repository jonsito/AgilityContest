<!-- PANEL INFORMATIVO SOBRE LA PRUEBA Y JORNADAS ASOCIADAS -->

<?php include_once("dialogs/dlg_jornadas.inc");?>
<?php include_once("dialogs/dlg_inscripciones.inc");?>
<?php include_once("dialogs/dlg_chinscripciones.inc");?>
 	
<div id="inscripciones_info" class="easyui-panel" title="Informaci&oacute;n de la prueba">
<div id="inscripciones_infolayout" class="easyui-layout" style="height:180px">
	<div data-options="region:'west',title:'Datos de la Prueba',split:true,collapsed:false" style="width:300px;padding:10px">
		<form id="inscripciones_pruebas" method="get">
		<input type="hidden" name="ID"/>
		<input type="hidden" name="Ubicacion"/>
		<input type="hidden" name="Triptico"/>
		<input type="hidden" name="Cartel"/>
		<input type="hidden" name="Cerrada"/>	
		<p>
		<label for="Nombre" style="font-weight:bold">Denominaci&oacute;n:</label>
		<input id="inscripciones-pnombre" type="text" name="Nombre" disabled="disabled" />
		</p>
		<p>
		<label for="Club" style="font-weight:bold">Club Organizador:</label>
		<input id="inscripciones-pclub" type="text" name="Club" disabled="disabled"/>
		</p>
		<p>
		<label for="Observaciones" style="font-weight:bold">Observaciones:</label>
		<input id="inscripciones-pcomments" type="text" name="Observaciones" disabled="disabled"/>
		</p>
		</form>
	</div>
	<div data-options="region:'center',title:'Lista de jornadas de la prueba'" style="width:500px">
		<table id="inscripciones_jornadas" class="easyui-datagrid"></table>
	</div>
</div> <!-- informacion de layout -->
</div> <!-- panel de informacion -->

<!-- DECLARACION DE LA TABLA DE INSCRIPCIONES -->
<table id="inscripciones-datagrid" class="inscripciones-datagrid" ></table>
    <!-- BARRA DE TAREAS -->
    <div id="inscripciones-toolbar">
    	<span style="float:left">
    	<a id="inscripciones-newBtn" href="#" class="easyui-linkbutton" onclick="newInscripcion()">Nueva inscripci&oacute;n</a>
    	<a id="inscripciones-editBtn" href="#" class="easyui-linkbutton" onclick="editInscripcion()">Editar Registro</a>
    	<a id="inscripciones-delBtn" href="#" class="easyui-linkbutton" onclick="destroyInscripcion()">Borrar inscripci&oacute;n</a>
    	<input id="inscripciones-search" type="text" onchange="doSearchInscripcion()"/> 
    	<a id="inscripciones-searchBtn" href="#" class="easyui-linkbutton" onclick="doSearchInscripcion()">Buscar</a>
    	</span>
    	<span style="float:right">
    	<!-- estos elementos deben estar alineados a la derecha -->
    	<a id="inscripciones-printBtn" href="#" class="easyui-linkbutton" onclick="printInscripciones()">Imprimir</a>
	   	<a id="inscripciones-reloadBtn" href="#" class="easyui-linkbutton" onclick="reloadInscripcion()">Refrescar</a>
	   	</span>
    </div>
    
<script type="text/javascript">
// cabecera de la pagina
$('#Header_Operation').html('<p>Inscripciones - Formulario de registro</p>');
// inicializamos formularios
$('#inscripciones_info').panel({
	border:true,
	closable:false,
	collapsible:true,
	collapsed:true
});
$('#inscripciones_infolayout').layout();
$('#inscripciones_pruebas').form('load','database/get_pruebaByID.php?ID='+workingData.prueba);
$('#inscripciones_jornadas').datagrid({
	url: 'database/select_JornadasByPrueba.php?ID='+workingData.prueba,
	method: 'get',
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    columns:[[
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
      		{ field:'Otras',		width:7, sortable:false,	align:'center', title: 'Otras ' },
      		{ field:'Cerrada',		width:7, sortable:false,	align:'center', title: 'Cerrada' }
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    },
	// on double click fireup editor dialog
	onDblClickRow:function(idx,row) { //idx: selected row index; row selected row data
    	editJornadaFromPrueba(workingData.prueba,'#inscripciones_jornadas');
	}
});

// datos de la tabla de inscripciones
// - tabla
$('#inscripciones-datagrid').datagrid({
	title: 'Gesti&oacute;n de datos de inscripciones',
	url: 'database/select_InscritosByPrueba.php?ID='+workingData.prueba,
	method: 'get',
    toolbar: '#inscripciones-toolbar',
    pagination: true,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    columns: [[
        { field:'Dorsal', hidden:true }, // dog ID
        { field:'Equipo', hidden:true }, // only used on Team contests
        { field:'Pagado', hidden:true }, // to store if handler paid :-)
    	{ field:'Nombre',	width:10, sortable:true, align: 'right',	title: 'Nombre' },
    	{ field:'Categoria',width:4,  sortable:true, align: 'center',  	title: 'Cat' },
    	{ field:'Grado',	width:4,  sortable:true, align: 'center',  	title: 'Grado' },
    	{ field:'Guia',		width:23, sortable:true, align: 'right',	title: 'Guia' },
    	{ field:'Club',		width:18, sortable:true, align: 'right',	title: 'Club' },
    	{ field:'Observaciones',width:15,            title: 'Observaciones' },
    	{ field:'Celo',		width:4,  lign:'center', title: 'Celo' },
        { field:'J1',		width:4, align:'center', title: 'J1'},
        { field:'J2',		width:4, align:'center', title: 'J2'},
        { field:'J3',		width:4, align:'center', title: 'J3'},
        { field:'J4',		width:4, align:'center', title: 'J4'},
        { field:'J5',		width:4, align:'center', title: 'J5'},
        { field:'J6',		width:4, align:'center', title: 'J6'},
        { field:'J7',		width:4, align:'center', title: 'J7'},
        { field:'J8',		width:4, align:'center', title: 'J8'},
    ]],
    // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    rowStyler:function(index,row) { 
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    },
	// on double click fireup editor dialog
    onDblClickRow:function() { 
        editInscripcion();
    }
});
// - botones de la cabecera de la tabla
$('#inscripciones-reloadBtn').linkbutton({plain:true,iconCls:'icon-reload'}); // nueva inscricion 
$('#inscripciones-reloadBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Refrescar la lista de inscripciones</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#inscripciones-printBtn').linkbutton({plain:true,iconCls:'icon-print'}); // imprimir listado 
$('#inscripciones-printBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Imprimir la lista de inscripciones</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
// - botones de la toolbar de la tabla
$('#inscripciones-newBtn').linkbutton({plain:true,iconCls:'icon-add'}); // nueva inscricion 
$('#inscripciones-newBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Registrar una nueva inscripcion</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#inscripciones-editBtn').linkbutton({plain:true,iconCls:'icon-edit'}); // editar inscripcion      
$('#inscripciones-editBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Modificar el registro seleccionado</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#inscripciones-delBtn').linkbutton({plain:true,iconCls:'icon-remove'}); // borrar perro     
$('#inscripciones-delBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Eliminar el registro seleccionado de la BBDD</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});

$('#inscripciones-searchBtn').linkbutton({plain:true,iconCls:'icon-search'} ); // buscar perro
$('#inscripciones-searchBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Buscar entradas que contengan el texto dado</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});

// cargamos datos de la prueba
// cargamos datos de las jornadas de la prueba
// cargamos formulario de inscripcion
</script>