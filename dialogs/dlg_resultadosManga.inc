<!--  dialogo de presentacion de resultados parciales de una manga -->
<div id="resultadosmanga-window" class="easyui-window" style="width:800px;height:400px;padding:10px 10px">
	<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
	<table id="resultadosmanga-datagrid" class="easyui-datagrid"></table>
	<!-- BARRA DE TAREAS DE ORDEN DE SALIDA -->
	<div id="resultadosmanga-toolbar">
 		<span style="float:left">
		   	<a id="resultadosmanga-reloadBtn" href="#" class="easyui-linkbutton" onclick="reloadResultadosManga()">Actualizar</a>
    		</span>
    		<span style="float:right">
    		<!-- estos elementos deben estar alineados a la derecha -->
    		<a id="resultadosmanga-printBtn" href="#" class="easyui-linkbutton" onclick="printResultadosManga()">Imprimir</a>
		</span>
	</div>
</div>

<script type="text/javascript">

$('#resultadosmanga-window').window({
	collapsible:	false,
	minimizable:	false,
	maximizable:	false,
	resizable:		true,
	closed:			true,
	modal:			true
});

$('#resultadosmanga-datagrid').datagrid({
	// propiedades del panel asociado
	noheader: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	url: 'database/resultadosFunctions.php',
    queryParams: {
        Jornada: workingData.jornada,
        Manga: workingData.manga,
        Operation: 'resultados',
        Categorias: 'LMS'
    },
	method: 'get',
    pagination: false,
    rownumbers: false, // 
    fitColumns: true,
    singleSelect: true,
    pageSize:10,
    toolbar: '#resultadosmanga-toolbar',
    columns:[[
        { field:'Manga',		hidden:true },
        { field:'Dorsal',		hidden:true },
      	{ field:'Licencia',		hidden:true },
        { field:'Orden',		width:10, align:'left',  title: 'Puesto'},
        { field:'Nombre',		width:15, align:'left',  title: 'Nombre'},
        { field:'Guia',			width:40, align:'right', title: 'Guia' },
        { field:'Club',			width:25, align:'right', title: 'Club' },
      	{ field:'Categoria',	width:10, align:'center',title: 'Cat.' },
      	{ field:'Faltas',		width:10, align:'center', title: 'Faltas'},
      	{ field:'Rehuses',		width:10, align:'center', title: 'Rehuses'},
      	{ field:'Tocados',		width:10, align:'center', title: 'Tocados'},
      	{ field:'Tiempo',		width:15, align:'right', title: 'Tiempo'},
      	{ field:'Velocidad',	width:10, align:'right', title: 'Vel.'},
      	{ field:'Penalizacion',	width:15, align:'right', title: 'Ptos.'}, 
      	{ field:'Calificacion',	width:25, align:'center',title: 'Calificacion'}
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    },
    onBeforeLoad: function(param) { return (workingData.manga<=0)?false:true; }, // do not load if no manga selected
    loadMsg: "Actualizando resultados de la manga ...",
    onLoadSuccess: function() { // get focus on datagrid (to bind keystrokes)
    	$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
    }
});

//Activa tecla ESC (escape) para cerrar ventana
$('#resultadosmanga-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
	if (e.keyCode==27) $('#resultadosmanga-window').window('close'); 
	return false;
});

//- botones del panel de resultados de la manga
$('#resultadosmanga-reloadBtn').linkbutton({plain:true,iconCls:'icon-reload'}); // recargar resultados      
$('#resultadosmanga-reloadBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Recargar resultados de la manga</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#resultadosmanga-printBtn').linkbutton({plain:true,iconCls:'icon-print'}); // imprimir listado 
$('#resultadosmanga-printBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Imprimir resultados de la manga</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});

</script>