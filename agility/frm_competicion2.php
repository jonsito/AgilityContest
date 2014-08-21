<?php include_once("dialogs/dlg_ordensalida.inc");?>
<?php include_once("dialogs/dlg_competicion.inc");?>
<?php include_once("dialogs/dlg_resultadosManga.inc");?>
 	
<!-- PANEL INFORMATIVO SOBRE LA MANGAS DE ESTA JORNADA -->
<div id="competicion_info" class="easyui-panel" title="Informaci&oacute;n de la jornada de competici&oacute;n">

	<!-- paneles de lista de mangas y datos de cada manga -->
	<div id="competicion_infolayout" class="easyui-layout" style="height:400px">
		<div data-options="region:'west',title:'Mangas de la jornada',split:true,collapsed:false" style="width:250px">
		
			<!-- Tabla que contiene la lista de Mangas de la jornada -->
			<table id="competicion-listamangas" style="padding:50px"></table>
		
			<!-- BARRA DE TAREAS DE LA LISTA DE MANGAS-->
			<div id="competicion-listamanga-toolbar">
	   			<span style="float:left;padding:10px 10px 10px 20px">
	    			<a id="competicion-ordensalidaBtn" href="#" class="easyui-linkbutton"
	    				data-options="iconCls:'icon-order'" style="width:185px"
	    				onclick="competicionDialog('ordensalida');">Orden de salida</a>
	    			<a id="competicion-competicionBtn" href="#" class="easyui-linkbutton"
	    				data-options="iconCls:'icon-table'" style="width:185px"
	    				onclick="competicionDialog('competicion');">Entrada de datos</a>
	    			<a id="competicion-resultmangaBtn" href="#" class="easyui-linkbutton"
	    				data-options="iconCls:'icon-endflag'" style="width:185px"
	    				onclick="competicionDialog('resultadosmanga');">Resultados de la manga</a>
				</span>
			</div>
			
		</div>
		<div data-options="region:'center',title:'Datos de la manga'" style="width:600px;">
			<font size="11"> <!--  take care on some stupid browsers -->
			<span id="competicion-datosmanga" class="c_competicion-datosmanga"></span>
			</font>
		</div> <!-- datos de la manga -->
	</div> <!-- informacion de layout -->



</div> <!-- panel de informacion -->  
<script type="text/javascript">

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
	fit: true,
	method: 'get',
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    showHeader: false,
    toolbar: '#competicion-listamanga-toolbar',
    columns:[[
            { field:'ID',			hidden:true }, // ID de la jornada
      	    { field:'Tipo',			hidden:true }, // Tipo de manga
      	    { field:'Grado',		hidden:true }, // Grado de los perros de la manga
      	    { field:'Recorrido',	hidden:true }, // 0:L/M/S 1:L/M+S 2:L+M+S
      	    { field:'Descripcion',	width:200, sortable:false, align:'right'}, // texto del tipo de manga
    ]],
    rowStyler:myRowStyler,
    onSelect: function (index,row) {
        if (index<0) { // no manga selected
            $('#competicion-datosmanga').html("");
        	workingData.manga=0;
        	workingData.nombreManga="";
        	workingData.datosManga=null;
            return; 
        }
        // guardamos el id y el nombre de la manga
        workingData.datosManga=row;
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
        reloadResultadosManga(row.Recorrido);
    }
});

//tooltips
addTooltip($('#competicion-ordensalidaBtn').linkbutton(),"Ver/Editar el Orden de salida de la manga");
addTooltip($('#competicion-competicionBtn').linkbutton(),"Insertar resultados de los participantes en la manga");
addTooltip($('#competicion-resultmangaBtn').linkbutton(),"Ver los resultados parciales de la manga"); 

</script>
    