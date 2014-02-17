<!--  dialogo de orden de salida e introduccion de datos de una competicion -->
<div id="ordensalida-window" class="easyui-window" style="width:800px;height:450px;padding:15px 10px 20px 10px">
	<!-- BARRA DE TAREAS DE ORDEN DE SALIDA -->
	<div id="ordensalida-toolbar">
 		<span style="float:left">
		   	<a id="ordensalida-randomBtn" href="#" class="easyui-linkbutton" onclick="evalOrdenSalida('random')">Aleatorio</a>
		   	<a id="ordensalida-resultBtn" href="#" class="easyui-linkbutton" onclick="evalOrdenSalida('reverse')">Manga anterior</a>
		   	<a id="ordensalida-saveBtn" href="#" class="easyui-linkbutton" onclick="saveOrdenSalida()">Guardar</a>
		   	<a id="ordensalida-reloadBtn" href="#" class="easyui-linkbutton" onclick="reloadOrdenSalida()">Actualizar</a>
    		</span>
    		<span style="float:right">
    		<!-- estos elementos deben estar alineados a la derecha -->
    		<a id="ordensalida-printBtn" href="#" class="easyui-linkbutton" onclick="printOrdenSalida()">Imprimir</a>
		</span>
	</div>
	<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
	<table id="ordensalida-datagrid" class="easyui-datagrid"></table>
</div>

<script type="text/javascript">

$('#ordensalida-window').window({
	collapsible:	false,
	minimizable:	false,
	maximizable:	false,
	resizable:		false,
	iconCls:		'icon-order',
	closed:			true,
	modal:			false
});

$('#ordensalida-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: 'database/ordenSalidaFunctions.php?Operation=getData',
    queryParams: {
        Jornada: workingData.jornada,
        Manga: workingData.manga
    },
    loadMsg: "Actualizando orden de salida...",
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    // toolbar: '#ordensalida-toolbar',
    columns:[[
        	{ field:'Dorsal',		hidden:true },
          	// { field:'Dorsal',		width:10, sortable:false,	align:'left',  title: 'Dorsal'},
          	{ field:'Nombre',		width:20, sortable:false,	align:'left',  title: 'Nombre'},
      		{ field:'Licencia',		width:15, sortable:false,   align:'right', title: 'Lic.' },
      		{ field:'Categoria',	width:10, sortable:false,   align:'center', title: 'Cat.' },
      		{ field:'Guia',			width:50, sortable:false,   align:'right', title: 'Guia' },
      		{ field:'Club',		    width:30, sortable:false,   align:'right', title: 'Club' },
      		{ field:'Celo',		    width:10, sortable:false,   align:'right', title: 'Celo' },
      		{ field:'Observaciones',width:30, sortable:false,   align:'right', title: 'Observaciones' }
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    },
    onBeforeLoad: function(param) { return (workingData.manga<=0)?false:true; }, // do not load if no manga selected
    onLoadSuccess: function() { // get focus on datagrid (to bind keystrokes) and enable drag and drop
    	$(this).datagrid('enableDnd');
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
    },
    onDragEnter: function(dst,src) {
        var from=src.Categoria+src.Celo;
        var to=dst.Categoria+dst.Celo;
        return (from==to)?true:false;
    },
    onDrop: function(dst,src,updown) {
        dragAndDrop(src.Dorsal,dst.Dorsal,(updown==='top')?0:1);
    }
});

// Activa tecla ESC (escape) para cerrar ventana
$('#ordensalida-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
	if (e.keyCode==27) $('#ordensalida-window').window('close'); 
	return false;
});

//- botones del panel de orden de mangas
$('#ordensalida-reloadBtn').linkbutton({plain:true,iconCls:'icon-reload'}); // nueva inscricion 
$('#ordensalida-reloadBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Actualizar el orden de salida desde base de datos</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#ordensalida-randomBtn').linkbutton({plain:true,iconCls:'icon-dice'}); // nueva inscricion 
$('#ordensalida-randomBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Generar un nuevo orden de salida aleatorio</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#ordensalida-resultBtn').linkbutton({plain:true,iconCls:'icon-updown'}); // nueva inscricion 
$('#ordensalida-resultBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Orden de salida inverso a resultados de primera manga</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#ordensalida-saveBtn').linkbutton({plain:true,iconCls:'icon-save'}); // editar inscripcion      
$('#ordensalida-saveBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Guardar orden de salida en base de datos</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#ordensalida-printBtn').linkbutton({plain:true,iconCls:'icon-print'}); // imprimir listado 
$('#ordensalida-printBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Imprimir el orden de salida</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});

</script>