<?php include_once("tablet_ordensalida.inc");?>
<?php include_once("tablet_entradadatos.inc");?>
 	
<!-- Gestion desde el tablet de el orden de salida y entrada de datos -->
<div id="tablet_competicion-Panel" class="easyui-panel" title="Tablet del ayudante del juez">

	<!-- paneles de lista de mangas y datos de cada manga -->
	<div id="tablet_competicion-Layout" class="easyui-layout">
	
		<div data-options="region:'west',title:'Tandas de la Jornada',split:true,collapsed:false" style="width:200px">
			<!-- Tabla que contiene la lista de tandas de la jornada -->
			<table id="tablet_competicion-ListaTandas" class="easyui-datagrid"></table>
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
    loadMsg: "Actualizando orden de salida...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    // toolbar: '#ordensalida-toolbar',
    columns:[[
      	{ field:'Prueba',	hidden:true }, // Prueba ID
    	{ field:'Jornada',	hidden:true }, // Jornada ID
        { field:'Manga',	hidden:true }, // Manga ID
        { field:'From',		hidden:true }, // separador inicial en manga::ordensalida
        { field:'To',		hidden:true }, // separador final en manga::ordensalida
        { field:'Nombre',	width:20, sortable:false,	align:'left',  title: 'Tanda'},
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    },
    onBeforeLoad: function(param) { return (workingData.Jornada<=0)?false:true; }, // do not load if no  selected
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
        tablet_dragAndDrop(src.From,dst.To,(updown==='top')?0:1);
    }
});
</script>