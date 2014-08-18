<!--  dialogo de orden de salida e introduccion de datos de una competicion -->
<div id="ordensalida-window" class="easyui-window" style="width:800px;height:450px;padding:0px 10px 40px 10px">
	<!-- BARRA DE TAREAS DE ORDEN DE SALIDA -->
	<div id="ordensalida-toolbar" style="padding:5px">
 		<span style="float:left">
		   	<a id="ordensalida-randomBtn" href="#" class="easyui-linkbutton" 
		   		data-options="iconCls:'icon-dice'" onclick="evalOrdenSalida('random')">Aleatorio</a>
		   	<a id="ordensalida-resultBtn" href="#" class="easyui-linkbutton" 
		   		data-options="iconCls:'icon-updown'" onclick="evalOrdenSalida('reverse')">Manga anterior</a>
		   	<a id="ordensalida-saveBtn" href="#" class="easyui-linkbutton" 
		   		data-options="iconCls:'icon-save'" onclick="saveOrdenSalida()">Guardar</a>
		   	<a id="ordensalida-reloadBtn" href="#" class="easyui-linkbutton" 
		   		data-options="iconCls:'icon-reload'" onclick="reloadOrdenSalida()">Actualizar</a>
    		</span>
    		<span style="float:right">
    		<!-- estos elementos deben estar alineados a la derecha -->
    		<a id="ordensalida-printBtn" href="#" 
    			data-options="iconCls:'icon-print'" class="easyui-linkbutton">Imprimir</a>
		</span>
	</div>
	<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
	<table id="ordensalida-datagrid" style="padding:5px"></table>
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
	url: 'database/ordenSalidaFunctions.php',
    queryParams: {
        Operation: 'getData',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Manga: workingData.manga
    },
    loadMsg: "Actualizando orden de salida...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    // toolbar: '#ordensalida-toolbar',
    columns:[[
        	{ field:'Perro',		hidden:true },
          	{ field:'Dorsal',		width:10, sortable:false,	align:'left',  title: 'Dorsal'},
          	{ field:'Nombre',		width:20, sortable:false,	align:'left',  title: 'Nombre'},
      		{ field:'Licencia',		width:15, sortable:false,   align:'right', title: 'Licencia' },
      		{ field:'Categoria',	width:10, sortable:false,   align:'center', title: 'Cat.' },
      		{ field:'NombreGuia',	width:50, sortable:false,   align:'right', title: 'Guia' },
      		{ field:'NombreClub',	width:30, sortable:false,   align:'right', title: 'Club' },
      		{ field:'Celo',		    width:10, sortable:false,   align:'center', title: 'Celo' },
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
        dragAndDrop(src.IDPerro,dst.IDPerro,(updown==='top')?0:1);
    }
});

// Activa tecla ESC (escape) para cerrar ventana
$('#ordensalida-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
	if (e.keyCode==27) $('#ordensalida-window').window('close'); 
	return false;
});

// botones
addTooltip($('#ordensalida-reloadBtn').linkbutton(),"Actualizar el orden de salida desde base de datos");
addTooltip($('#ordensalida-randomBtn').linkbutton(),"Generar un nuevo orden de salida aleatorio");
addTooltip($('#ordensalida-resultBtn').linkbutton(),"Orden de salida inverso a resultados de primera manga");
addTooltip($('#ordensalida-saveBtn').linkbutton(),"Guardar orden de salida en base de datos");
addTooltip($('#ordensalida-printBtn').linkbutton(),"Imprimir el orden de salida");
$('#ordensalida-printBtn').on("click", function () {
	$.fileDownload(
		'pdf/print_ordenDeSalida.php',
		{
			httpMethod: 'GET',
			data: { 
				Prueba: workingData.prueba,
				Jornada: workingData.jornada,
				Manga: workingData.manga
			},
	        preparingMessageHtml: "We are preparing your report, please wait...",
	        failMessageHtml: "There was a problem generating your report, please try again."
		}
	);
    return false; //this is critical to stop the click event which will trigger a normal file download!
});
</script>