<?php include_once("tablet_entradadatos.inc");?>
 	
<!-- Gestion desde el tablet de el orden de salida y entrada de datos -->
<div id="tablet_competicion-Panel" class="easyui-panel">

	<!-- paneles de lista de mangas y datos de cada manga -->
	<div id="tablet_competicion-Layout" class="easyui-layout" style="width:1024px;height:600px;">
		<!-- Ventana para ver y ajustar el orden de tandas de la jornada -->
		
		<div data-options="region:'west',title:'Tandas de la Jornada',split:true,collapsed:false" style="width:250px">
			<table id="ordentandas-datagrid" class="easyui-datagrid" style="padding:10px;"></table>
			<!-- toolbar para orden de tandas -->
			<div id="ordentandas-toolbar" style="padding:0px 10px 30px 10px">
 				<span style="float:left">
		   			<a id="ordentandas-reloadBtn" href="#" class="easyui-linkbutton" 
		   				data-options="iconCls:'icon-reload'" onclick="reloadOrdenTandas()">Actualizar</a>
    			</span>
			</div>		
		</div> <!-- Orden de tandas  -->
		
		<div data-options="region:'center',title:'Entrada de Datos'" style="width:774px;">
			<!-- Tabla desplegable para la entrada de datos desde el tablet -->
			<table id="tablet_competicion-EntradaDatos" class="easyui-datagrid" style="padding:10px;"></table>
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

$('#ordentandas-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	toolbar:'#ordentandas-toolbar',
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
    	$('#tablet_competicion-EntradaDatos').datagrid('reload');
    },
    onDragEnter: function(dst,src) { return true; }, // default is not restriction
    onDrop: function(dst,src,updown) {
        dragAndDropOrdenTandas(src.ID,dst.ID,(updown==='top')?0:1);
    }
});

addTooltip($('#ordentandas-reloadBtn').linkbutton(),"Actualizar el programa de la jornada desde base de datos");

$('#tablet_competicion-EntradaDatos').datagrid({
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
    loadMsg: "Actualizando datos de los participantes ...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    view: groupview,
    groupField: "Tanda",
    groupFormatter: function(value,rows){
    	return value + ' - ' + rows.length + ' participante(s)';
    },
    columns:[[
        { field:'Manga',		hidden:true },
        { field:'Perro',		hidden:true },
      	{ field:'Licencia',		hidden:true },
      	{ field:'Pendiente',	hidden:true },
      	{ field:'Tanda',		hidden:true },
        { field:'Dorsal',		width:10, align:'right',  title: '#', styler:checkPending },
      	{ field:'Celo',			width:10, align:'center', title: 'Celo', 
          			formatter:function(val,row){return (parseInt(val)==0)?" ":"X";}},
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
    rowStyler:myRowStyler,
    onBeforeLoad: function(param) { return true; }, // TODO: write
	onLoadSuccess:function(){ 
    	$(this).datagrid('enableDnd');
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
	},
    onClickRow: function(index) {
        // TODO: open tablet window data
    },
    onDblClickRow: function(idx,row) {
        $('#tdialog-dialog').dialog('open');
        $('#tdialog-form').form('load',row);
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
                function()  { $('#tablet_competicion-EntradaDatos').datagrid('reload'); }
         	);
    }
});
</script>