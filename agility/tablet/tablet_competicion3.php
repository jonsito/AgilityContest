<?php include_once(__DIR__."/tablet_entradadatos.inc");?>
<!-- Ventana para ver y ajustar el orden de tandas de la jornada -->
<div id="tablet_ordenTandas-panel" class="easyui-panel"
	style="width:1280px;height:800px;">
	<!-- toolbar para orden de tandas -->
	<div id="tablet_ordenTandas-toolbar" style="padding:5px">
		<a id="tablet_ordenTandas-reloadBtn" href="#" class="easyui-linkbutton" 
			data-options="iconCls:'icon-reload'" onclick="$('#tablet_ordenTandas-datagrid').datagrid('reload');">Actualizar</a>
		<a id="tablet_ordenTandas-printBtn" href="#" class="easyui-linkbutton" 
			data-options="iconCls:'icon-print'" onclick="tablet_printOrdenTandas()">Imprimir</a>
		<a id="tablet_ordenTandas-ordenBtn" href="#" class="easyui-linkbutton" 
			data-options="iconCls:'icon-updown'" onclick="tablet_showOrdenSalida()">Orden de Salida</a>
	</div>
	<!--  datagrid con el orden de tandas -->
	<table id="tablet_ordenTandas-datagrid" class="easyui-datagrid" style="padding:10px;"></table>
</div> <!-- Orden de tandas  -->
		
<div id="tablet_ordenSalida-panel" class="easyui-panel"
	style="width:1280px;height:800px;">
	<!-- toolbar para orden de tandas -->
	<div id="tablet_ordenSalida-toolbar" style="padding:5px">
		<a id="tablet_ordenSalida-reloadBtn" href="#" class="easyui-linkbutton" 
			data-options="iconCls:'icon-reload'" onclick="$('#tablet_ordenSalida-datagrid').datagrid('reload');">Actualizar</a>
		<a id="tablet_ordenSalida-printBtn" href="#" class="easyui-linkbutton" 
			data-options="iconCls:'icon-print'" onclick="tablet_printOrdenSalida()">Imprimir</a>
		<a id="tablet_ordenSalida-ordenBtn" href="#" class="easyui-linkbutton" 
			data-options="iconCls:'icon-updown'" onclick="tablet_showOrdenTandas()">Programa</a>
	</div>
	<!-- Tabla desplegable para la entrada de datos desde el tablet -->
	<table id="tablet_ordenSalida-datagrid" class="easyui-datagrid" style="padding:10px;"></table>
</div> <!-- orden de salida -->
		
<script type="text/javascript">

$('#tablet_ordenTandas-reloadBtn').linkbutton();
$('#tablet_ordenTandas-printBtn').linkbutton();
$('#tablet_ordenTandas-ordenBtn').linkbutton();
$('#tablet_ordenSalida-reloadBtn').linkbutton();
$('#tablet_ordenSalida-printBtn').linkbutton();
$('#tablet_ordenSalida-ordenBtn').linkbutton();

$('#tablet_ordenTandas-panel').panel({
	title: 'Programa de la jornada',
	collapsible:	false,
	minimizable:	false,
	maximizable:	false,
	resizable:		false,
	closable:		false,
	iconCls:		'icon-table',
	closed:			false,
	modal:			false
});

$('#tablet_ordenSalida-panel').panel({
	title: 'Orden de salida',
	collapsible:	false,
	minimizable:	false,
	maximizable:	false,
	resizable:		false,
	closable:		false,
	iconCls:		'icon-table',
	closed:			true,
	modal:			false
});

$('#tablet_ordenTandas-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	toolbar:'#tablet_ordenTandas-toolbar',
	method: 'get',
	url: '/agility/database/ordenTandasFunctions.php',
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
    autoRowHeight: false,
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
    // rowStyler:myRowStyler,
    onLoadSuccess: function() { // get focus on datagrid (to bind keystrokes) and enable drag and drop
    	$(this).datagrid('enableDnd');
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
    	$('#tablet_ordenSalida-datagrid').datagrid('reload');
    },
    onDragEnter: function(dst,src) { return true; }, // default is not restriction
    onDrop: function(dst,src,updown) {
        dragAndDropOrdenTandas(src.ID,dst.ID,(updown==='top')?0:1);
    }
});


$('#tablet_ordenSalida-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: '/agility/database/ordenTandasFunctions.php',
    queryParams: {
        Operation: 'getData',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada
    },
	toolbar:'#tablet_ordenSalida-toolbar',
    loadMsg: "Actualizando datos de los participantes ...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    autoRowHeight: false,
    view: groupview,
    groupField: "Tanda",
    groupFormatter: function(value,rows){
    	return value + ' - ' + rows.length + ' participante(s)';
    },
    columns:[[
        { field:'Parent',		width:0, hidden:true }, // self reference to row index
        { field:'Prueba',		width:0, hidden:true }, // extra field to be used on form load/save
        { field:'Jornada',		width:0, hidden:true }, // extra field to be used on form load/save
        { field:'ID',			width:0, hidden:true },
        { field:'Manga',		width:0, hidden:true },
        { field:'Perro',		width:0, hidden:true },
      	{ field:'Licencia',		width:0, hidden:true },
      	{ field:'Pendiente',	width:0, hidden:true },
      	{ field:'Tanda',		width:0, hidden:true },
        { field:'Dorsal',		width:12, align:'center',  title: '#', styler:checkPending },
      	{ field:'Celo',			width:10, align:'center', title: 'Celo', formatter:checkCelo},
        { field:'Nombre',		width:20, align:'left',  title: 'Nombre'},
        { field:'NombreGuia',	width:45, align:'right', title: 'Guia' },
        { field:'NombreClub',	width:30, align:'right', title: 'Club' },
      	{ field:'Categoria',	width:10, align:'center',title: 'Cat.' },
      	{ field:'Grado',		width:10, align:'center',title: 'Grd.' },
      	{ field:'Faltas',		width:5, align:'center', title: 'F'},
      	{ field:'Tocados',		width:5, align:'center', title: 'T'},
      	{ field:'Rehuses',		width:5, align:'center', title: 'R'},
      	{ field:'Tiempo',		width:15, align:'right', title: 'Tmp'	}, 
      	{ field:'Eliminado',	width:5, align:'center',title: 'EL.'},
      	{ field:'NoPresentado',	width:5, align:'center',title: 'NP'},		
      	{ field:'Observaciones',width:0, hidden:true }
    ]],
    // rowStyler:myRowStyler,
    onBeforeLoad: function(param) { return true; }, // TODO: write
	onLoadSuccess:function(){ 
    	$(this).datagrid('enableDnd');
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
	},
    onClickRow: function(index,row) {
        row.Prueba=workingData.prueba;
        row.Jornada=workingData.jornada;
        row.Parent=index; // store index
        $('#tdialog-form').form('load',row);
        $('#tablet_ordenTandas-panel').panel('close');
        $('#tablet_ordenSalida-panel').panel('close');
        $('#tdialog-panel').panel('open');
    },
    onDragEnter: function(dst,src) {
        if (dst.Manga!=src.Manga) return false;
        if (dst.Categoria!=src.Categoria) return false;
        if (dst.Grado!=src.Grado) return false;
        if (dst.Celo!=src.Celo) return false;
        return true;
    }, 
    onDrop: function(dst,src,updown) {
        // reload el orden de salida en la manga asociada
        workingData.manga=src.Manga;
        dragAndDropOrdenSalida(
                src.Perro,
                dst.Perro,
                (updown==='top')?0:1,
                function()  { $('#tablet_ordenSalida-datagrid').datagrid('reload'); }
         	);
    }
});
</script>