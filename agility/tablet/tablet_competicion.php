<?php include_once("tablet_ordensalida.inc");?>
<?php include_once("tablet_entradadatos.inc");?>
 	
<!-- Gestion desde el tablet de el orden de salida y entrada de datos -->
<div id="tablet_competicion-Panel" class="easyui-panel">

	<!-- paneles de lista de mangas y datos de cada manga -->
	<div id="tablet_competicion-Layout" class="easyui-layout" style="width:600px;height:1000px;">
	
		<div data-options="region:'west',title:'Tandas de la Jornada',split:true,collapsed:false" style="width:200px">
			<!-- Tabla que contiene la lista de tandas de la jornada -->
			<table id="tablet_competicion-ListaTandas" class="easyui-datagrid" style="padding:10px;"></table>
		</div> <!-- Orden de salida -->
		
		<div data-options="region:'center',title:'Entrada de Datos'" style="width:400px;">
			<!-- Tabla desplegable para la entrada de datos desde el tablet -->
			<table id="tablet_competicion-EntradaDatos" class="easyui-datagrid"></table>
		</div> <!-- Entrada de datos -->
		
	</div> <!-- tablet_competicion-Layout -->
	
</div> <!-- tablet_competicion-Panel -->  

<script type="text/javascript">
$('#tablet_competicion-Panel').panel({
	title:workingData.nombrePrueba+' -- '+workingData.nombreJornada,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false
});
$('#tablet_competicion-Layout').layout();
$('#tablet_competicion-ListaTandas').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: '/agility/database/mangaFunctions.php',
    queryParams: {
        Operation: 'getTandas',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada
    },
    loadMsg: "Actualizando orden de tandas...",
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    columns:[[
      	{ field:'Prueba',	hidden:true }, // Prueba ID
    	{ field:'Jornada',	hidden:true }, // Jornada ID
        { field:'Manga',	hidden:true }, // Manga ID
        { field:'From',		hidden:true }, // separador inicial en manga::ordensalida
        { field:'To',		hidden:true }, // separador final en manga::ordensalida
        { field:'ID',		hidden:true }, // ID de la tanda
        { field:'Nombre',	width:20, sortable:false,	align:'left',  title: 'Tanda'},
    ]],
    rowStyler:myRowStyler,
    onBeforeLoad: function(param) { return (workingData.Jornada<=0)?false:true; }, // do not load if no jornada selected
    onLoadSuccess: function() { // get focus on datagrid (to bind keystrokes) and enable drag and drop
    	$(this).datagrid('enableDnd');
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
    },
    onDragEnter: function(dst,src) { return true; }, // default is allow any tandas order
    onDrop: function(dst,src,updown) {
        // tablet_dragAndDrop(src.From,dst.To,(updown==='top')?0:1);
    }
});

$('#tablet_competicion-EntradaDatos').datagrid({
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
    loadMsg: "Actualizando datos de competicion ...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    // toolbar: '#competicion-toolbar',
    columns:[[
        { field:'Manga',		hidden:true },
        { field:'Perro',		hidden:true },
      	{ field:'Licencia',		hidden:true },
      	{ field:'Pendiente',	hidden:true },
        { field:'Dorsal',		width:10, align:'right',  title: '#', styler:checkPending },
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
      	{ field:'Observaciones',hidden:true }
    ]],
    rowStyler:function(index,row) { // colorize rows. Equivalent to "striped: true", but better look&feel
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    },
    onBeforeLoad: function(param) { return true; }, // TODO: write
	onLoadSuccess:function(){ 
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
	},
    onClickRow: function(index) {
        // TODO: open tablet window data
    }
});
</script>