<!--  dialogo de orden de tandas e introduccion de datos de una competicion -->
<div id="ordentandas-window" class="easyui-window" style="width:400px;height:625px;padding:10px">
	<!-- BARRA DE TAREAS DE ORDEN DE tandas -->
	<div id="ordentandas-toolbar" style="padding:0px 10px 30px 10px">
 		<span style="float:left">
		   	<a id="ordentandas-reloadBtn" href="#" class="easyui-linkbutton" 
		   		data-options="iconCls:'icon-reload'" onclick="reloadOrdenTandas()">Actualizar</a>
    		</span>
    		<span style="float:right">
    		<!-- estos elementos deben estar alineados a la derecha -->
    		<a id="ordentandas-printBtn" href="#" 
    			data-options="iconCls:'icon-print'" class="easyui-linkbutton">Imprimir</a>
		</span>
	</div>
	<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
	<table id="ordentandas-datagrid" style="padding:10px"></table>
</div>

<script type="text/javascript">

$('#ordentandas-window').window({
	collapsible:	false,
	minimizable:	false,
	maximizable:	false,
	resizable:		false,
	iconCls:		'icon-updown',
	closed:			true,
	modal:			true
});

$('#ordentandas-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: 'server/database/ordenTandasFunctions.php',
    queryParams: {
        Operation: 'getTandas',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada
    },
    loadMsg: "Actualizando programa de la Jornada .....",
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    columns:[[
          	{ field:'ID',		hidden:true },
        	{ field:'Prueba',	hidden:true },
          	{ field:'Jornada',	hidden:true },
          	{ field:'Manga',	hidden:true },
      		{ field:'From',		hidden:true },
      		{ field:'To',		hidden:true },
      		{ field:'Nombre',	width:200, sortable:false, align:'right',title:'Secuencia de salida a pista'},
      		{ field:'Categoria',hidden:true },
      		{ field:'Grado',	hidden:true }
    ]],
    rowStyler:myRowStyler,
    onLoadSuccess: function() { // get focus on datagrid (to bind keystrokes) and enable drag and drop
    	$(this).datagrid('enableDnd');
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
    },
    onDragEnter: function(dst,src) { return true; }, // default is not restriction
    onDrop: function(dst,src,updown) {
        dragAndDropOrdenTandas(src.ID,dst.ID,(updown==='top')?0:1);
    }
});

// Activa tecla ESC (escape) para cerrar ventana
$('#ordentandas-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
	if (e.keyCode==27) $('#ordentandas-window').window('close'); 
	return false;
});

// botones
addTooltip($('#ordentandas-reloadBtn').linkbutton(),"Actualizar el programa de la jornada desde base de datos");
addTooltip($('#ordentandas-printBtn').linkbutton(),"Imprimir el programa de la jornada");

$('#ordentandas-printBtn').on("click", function () {
	$.fileDownload(
		'/agility/server/pdf/print_ordenDeTandas.php',
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