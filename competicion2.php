<?php include_once("dialogs/dlg_competicion.inc");?>
 	
<!-- PANEL INFORMATIVO SOBRE LA MANGAS DE ESTA JORNADA -->
<div id="competicion_info" class="easyui-panel" title="Informaci&oacute;n de la jornada de competici&oacute;n">
<div id="competicion_infolayout" class="easyui-layout" style="height:250px">
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
    	<a id="competicion-displaydialogBtn" href="#" class="easyui-linkbutton" onclick="competicionDialog();">Edici&oacute;n</a>
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
	collapsible:true,
	collapsed:false
});
$('#competicion_infolayout').layout();

$('#competicion-listamangas').datagrid({
	url: 'database/select_MangasByJornada.php?Jornada='+workingData.jornada,
	method: 'get',
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    showHeader: false,
    toolbar: '#competicion-listamanga-toolbar',
    columns:[[
            { field:'ID',			hidden:true }, // ID de la jornada
      	    { field:'Tipo',			hidden:true }, // ID de la prueba
      	    { field:'Descripcion',	width:70, sortable:false, align:'right'},
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    },
    onSelect: function (index,row) {
        if (index<0) { // no manga selected
            $('#competicion-datosmanga').html("");
            // TODO: clear & collapse panels
        	workingData.manga=-1;
        	workingData.nombreManga="";
            return; 
        }
        // guardamos el id y el nombre de la manga
        workingData.manga=row.ID;
        workingData.nombreManga=row.Descripcion;
        // cannot use loadcontents, because need to execute commands, _after_ html document load success
        $('#competicion-datosmanga').load("infomanga.php", function() {
            // cargamos el panel lateral con la informacion de la manga
        	$('#competicion_infolayout').layout('panel','center').panel('setTitle','Datos de la manga -- '+workingData.nombreManga);
 	        $('#competicion-formdatosmanga').form('load','database/get_mangaByID.php?ID='+workingData.manga);
        });
    },
});

//- boton de despliegue de la ventana de ordenSalida/resultados
$('#competicion-displaydialogBtn').linkbutton({plain:true,iconCls:'icon-table'}); // nueva inscricion 
$('#competicion-displaydialogBtn').tooltip({
	position: 'right',
	content: '<span style="color:#000">Editar el Orden de salida y/o Resultados</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
</script>
    