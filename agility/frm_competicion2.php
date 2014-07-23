<?php include_once("dialogs/dlg_ordensalida.inc");?>
<?php include_once("dialogs/dlg_competicion.inc");?>
<?php include_once("dialogs/dlg_resultadosManga.inc");?>
 	
<!-- PANEL INFORMATIVO SOBRE LA MANGAS DE ESTA JORNADA -->
<div id="competicion_info" class="easyui-panel" title="Informaci&oacute;n de la jornada de competici&oacute;n">
<div id="competicion_infolayout" class="easyui-layout" style="height:400px">
	<div data-options="region:'west',title:'Mangas de la jornada',split:true,collapsed:false" style="width:200px">
		<table id="competicion-listamangas" class="easyui-datagrid" style="padding:10px 20px"></table>
	</div>
	<div data-options="region:'center',title:'Datos de la manga'" style="width:600px;">
		<font size="11"> <!--  take care on some stupid browsers -->
		<span id="competicion-datosmanga" class="c_competicion-datosmanga"></span>
		</font>
	</div> <!-- datos de la manga -->
</div> <!-- informacion de layout -->
</div> <!-- panel de informacion -->

<!-- BARRA DE TAREAS DE LA LISTA DE MANGAS-->
<div id="competicion-listamanga-toolbar">
   	<span style="float:left">
    	<a id="competicion-ordensalidaBtn" href="#" class="easyui-linkbutton"
    		plain="true" iconCls="icon-order" 
    		onclick="competicionDialog('ordensalida');">Orden de salida</a>
    	<a id="competicion-competicionBtn" href="#" class="easyui-linkbutton"
    		plain="true" iconCls="icon-table"
    		onclick="competicionDialog('competicion');">Entrada de datos</a>
    	<a id="competicion-resultmangaBtn" href="#" class="easyui-linkbutton"
    		plain="true" iconCls="icon-endflag"
    		onclick="competicionDialog('resultadosmanga');">Resultados de la manga</a>
	</span>
</div>

    
<script type="text/javascript">
// cargamos nombre de la jornada y de la prueba
$('#Header_Operation').html('<p>Desarrollo de la prueba</p>');

// declaracion de cada elemento grafico
$('#competicion_info').panel({
	title:workingData.nombrePrueba+' -- '+workingData.nombreJornada,
	border:true,
	closable:false,
	collapsible:false,
	collapsed:false
});
$('#competicion_infolayout').layout();

$('#competicion-listamangas').datagrid({
	url: 'database/mangaFunctions.php?Operation=enumerate&Jornada='+workingData.jornada,
	method: 'get',
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    showHeader: false,
    toolbar: '#competicion-listamanga-toolbar',
    columns:[[
            { field:'ID',			hidden:true }, // ID de la jornada
      	    { field:'Tipo',			hidden:true }, // Tipo de manga
      	    { field:'Descripcion',	width:120, sortable:false, align:'right'}, // texto del tipo de manga
    ]],
    rowStyler:myRowStyler,
    onSelect: function (index,row) {
        if (index<0) { // no manga selected
            $('#competicion-datosmanga').html("");
        	workingData.manga=0;
        	workingData.nombreManga="";
            return; 
        }
        // guardamos el id y el nombre de la manga
        workingData.manga=row.ID;
        workingData.nombreManga=row.Descripcion;
        // cannot use loadcontents, because need to execute commands, _after_ html document load success
        $('#competicion-datosmanga').load("dialogs/infomanga.php", function() {
            // titulo del panel lateral con la informacion de la manga
        	$('#competicion_infolayout').layout('panel','center').panel('setTitle','Datos de la manga -- '+workingData.nombreManga);
        	// datos del panel lateral con informacion de la manga
        	reload_manga(workingData.manga);
        });
        // refresh orden de salida/competicion/resultados
        reloadOrdenSalida();
        reloadCompeticion();
        reloadResultadosManga('LMS');
    }
});

//tooltips
addTooltip($('#competicion-ordensalidaBtn').linkbutton(),"Ver/Editar el Orden de salida de la manga");
addTooltip($('#competicion-competicionBtn').linkbutton(),"Insertar datos de los recorridos de la manga");
addTooltip($('#competicion-resultmangaBtn').linkbutton(),"Ver los resultados parciales de la manga"); 

</script>
    