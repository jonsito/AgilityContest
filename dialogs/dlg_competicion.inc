<!--  dialogo de orden de salida e introduccion de datos de una competicion -->
<div id="competicion-window" class="easyui-window" style="width:750px;height:500px;padding:10px 20px">

	<div id="competicion-window-layout" class="easyui-layout" data-options="fit:true">
		<div data-options="region:'west',title:'Orden de salida',split:true,collapsed:false" style="width:600px">
			<!-- DECLARACION DEL ORDEN DE SALIDA DE CADA MANGA -->
			<table id="competicion-orden-datagrid" class="easyui-datagrid"></table>
		</div>
		<div data-options="region:'center',title:'Resultados'" style="width:600px;">
			<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
			<table id="competicion-resultados-datagrid" class="easyui-datagrid"></table>
		</div> <!-- datos de la manga -->
	</div> <!-- informacion de layout -->

	<!-- BARRA DE TAREAS DE ORDEN DE SALIDA -->
	<div id="competicion-orden-toolbar">
 		<span style="float:left">
		   	<a id="competicion-orden-randomBtn" href="#" class="easyui-linkbutton" onclick="randomOrdenSalida()">Aleatorio</a>
		   	<a id="competicion-orden-saveBtn" href="#" class="easyui-linkbutton" onclick="saveOrdenSalida()">Guardar</a>
		   	<a id="competicion-orden-reloadBtn" href="#" class="easyui-linkbutton" onclick="reloadOrdenSalida()">Actualizar</a>
    		</span>
    		<span style="float:right">
    		<!-- estos elementos deben estar alineados a la derecha -->
    		<a id="competicion-orden-printBtn" href="#" class="easyui-linkbutton" onclick="printOrdenSalida()">Imprimir</a>
		</span>
	</div>

	<!-- BARRA DE TAREAS DE TABLA DE RESULTADOS-->
	<div id="competicion-resultados-toolbar">
	   	<span style="float:right">
	    	<a id="competicion-resultados-printBtn" href="#" class="easyui-linkbutton" onclick="printResultados()">Imprimir</a>
	   	</span>
	</div>
</div>

<script type="text/javascript">

$('#competicion-window').window({
	collapsible:	false,
	minimizable:	false,
	maximizable:	false,
	resizable:		true,
	closed:			true,
	modal:			true
});

$('#competicion-window-layout').layout();

$('#competicion-orden-datagrid').datagrid({
	// propiedades del panel asociado
	noheader: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	url: 'database/ordenSalidaFunctions.php',
    queryParams: {
        Jornada: workingData.jornada,
        Manga: workingData.manga,
        Orden: false,
        Operacion: 'getData'
    },
	method: 'get',
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    pageSize:10,
    toolbar: '#competicion-orden-toolbar',
    columns:[[
        	{ field:'Dorsal',		hidden:true },
          	{ field:'Nombre',		width:20, sortable:false,	align:'left',  title: 'Nombre'},
      		{ field:'Licencia',		width:15, sortable:false,   align:'right', title: 'Lic.' },
      		{ field:'Categoria',	width:10, sortable:false,   align:'center', title: 'Cat.' },
      		{ field:'Guia',			width:40, sortable:false,   align:'right', title: 'Guia' },
      		{ field:'Club',		    width:30, sortable:false,   align:'right', title: 'Club' },
      		{ field:'Celo',		    width:10, sortable:false,   align:'right', title: 'Celo' },
      		{ field:'Observaciones',width:30, sortable:false,   align:'right', title: 'Observaciones' }
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    },
    onBeforeLoad: function(param) { return (workingData.manga<=0)?false:true; }, // do not load if no manga selected
    loadMsg: "Actualizando orden de salida...",
});

$('#competicion-resultados-datagrid').datagrid({
	// datos del panel asociado
	noheader:true,
	border:true,
	closable:false,
	collapsible:false,
	collapsed:false,
	// datos del datagrid
	url: 'database/jornadaFunctions.php?Operation=select&Prueba='+workingData.prueba,
	method: 'get',
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    toolbar: '#competicion-resultados-toolbar',
    columns:[[
            { field:'ID',			hidden:true }, // ID de la jornada
      	    { field:'Prueba',		hidden:true }, // ID de la prueba
      	    { field:'Numero',		width:4, sortable:false,	align:'center', title: '#'},
      		{ field:'Nombre',		width:40, sortable:false,   align:'right', title: 'Nombre/Comentario' },
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    }
});

//- botones del panel de orden de mangas
$('#competicion-orden-reloadBtn').linkbutton({plain:true,iconCls:'icon-reload'}); // nueva inscricion 
$('#competicion-orden-reloadBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Actualizar el orden de salida desde base de datos</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#competicion-orden-randomBtn').linkbutton({plain:true,iconCls:'icon-dice'}); // nueva inscricion 
$('#competicion-orden-randomBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Generar un nuevo orden de salida aleatorio</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#competicion-orden-saveBtn').linkbutton({plain:true,iconCls:'icon-save'}); // editar inscripcion      
$('#competicion-orden-saveBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Guardar orden de salida en base de datos</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#competicion-orden-printBtn').linkbutton({plain:true,iconCls:'icon-print'}); // imprimir listado 
$('#competicion-orden-printBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Imprimir el orden de salida</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});

// botones del panel de resultados
$('#competicion-resultados-printBtn').linkbutton({plain:true,iconCls:'icon-print'}); // imprimir listado 
$('#competicion-resultados-printBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Imprimir resultados de la manga</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
</script>