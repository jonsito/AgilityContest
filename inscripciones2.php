<!-- PANEL INFORMATIVO SOBRE LA PRUEBA Y JORNADAS ASOCIADAS -->

<div id="inscripciones_info" class="easyui-panel" title="Informaci&oacute;n de la prueba">
<div id="inscripciones_infolayout" class="easyui-layout" style="height:180px">
	<div data-options="region:'west',title:'Datos de la Prueba',split:true,collapsed:false" style="width:300px">
		<p>Denominacion: <span id="inscripciones-pnombre"></span></p>
		<p>Club Organizador: <span id="inscripciones-pclub"></span></p>
		<p>Observaciones: <span id="inscripciones-pcomments"></span></p>
	</div>
	<div data-options="region:'center',title:'Lista de jornadas de la prueba'" style="width:500px">
		<ol>
			<li><span id="j1">-- No definida --</span></li>
			<li><span id="j2">-- No definida --</span></li>
			<li><span id="j3">-- No definida --</span></li>
			<li><span id="j4">-- No definida --</span></li>
			<li><span id="j5">-- No definida --</span></li>
			<li><span id="j6">-- No definida --</span></li>
			<li><span id="j7">-- No definida --</span></li>
			<li><span id="j8">-- No definida --</span></li>
		</ol>
	</div>
</div> <!-- informacion de layout -->
</div> <!-- panel de informacion -->

<!-- DECLARACION DE LA TABLA DE INSCRIPCIONES -->
<table id="inscripciones-datagrid" class="inscripciones-datagrid" ></table>
    <!-- BARRA DE TAREAS -->
    <div id="inscripciones-toolbar">
    	<a id="inscripciones-newBtn" href="#" class="easyui-linkbutton" onclick="newDog()">Nueva inscripci&oacute;</a>
    	<a id="inscripciones-editBtn" href="#" class="easyui-linkbutton" onclick="editDog()">Editar Registro</a>
    	<a id="inscripciones-delBtn" href="#" class="easyui-linkbutton" onclick="destroyDog()">Borrar inscripci&oacute;n</a>
    	<input id="inscripciones-search" type="text" onchange="doSearchPerro()"/> 
    	<a id="inscripciones-searchBtn" href="#" class="easyui-linkbutton" onclick="doSearchPerro()">Buscar</a>
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

// datos de la tabla de inscripciones
// - tabla
$('#inscripciones-datagrid').datagrid({
	title: 'Gesti&oacute;n de datos de inscripciones',
	url: 'database/get_dogs.php',
	method: 'get',
    toolbar: '#inscripciones-toolbar',
    pagination: true,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    columns: [[
        { field:'ID', 	hidden:true },  // primary key. not really needed (index: dorsal)
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
        editDog();
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