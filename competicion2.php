
<?php include_once("dialogs/dlg_perros.inc");?>
<?php include_once("dialogs/dlg_guias.inc");?>
<?php include_once("dialogs/dlg_clubes.inc");?>
<?php include_once("dialogs/dlg_jornadas.inc");?>
 	
<!-- PANEL INFORMATIVO SOBRE LA MANGAS DE ESTA JORNADA -->
<div id="competicion_info" class="easyui-panel" title="Informaci&oacute;n de la jornada de competici&oacute;n">
<div id="competicion_infolayout" class="easyui-layout" style="height:200px">
	<div data-options="region:'west',title:'Mangas de la jornada',split:true,collapsed:false" style="width:200px">
		<table id="competicion-listamangas" class="easyui-datagrid"></table>
	</div>
	<div data-options="region:'center',title:'Datos de la manga'" style="width:600px;">
		<font size="11"> <!--  take care on some stupid browsers -->
		<span id="competicion-datosmanga" class="c_competicion-datosmanga"></span>
		</font>
	</div> <!-- datos de la manga -->
</div> <!-- informacion de layout -->
</div> <!-- panel de informacion -->

<!-- DECLARACION DEL ORDEN DE SALIDA DE CADA MANGA -->
<div id="competicion_ordensalida" class="easyui-panel" title="Orden de salida de los participantes en la manga">
<table id="competicion-orden-datagrid" class="competicion-orden-datagrid"></table>
    <!-- BARRA DE TAREAS DE ORDEN DE SALIDA -->
    <div id="competicion-orden-toolbar">
    	<span style="float:left">
    	<a id="competicion-orden-randomBtn" href="#" class="easyui-linkbutton" onclick="newOrdenSalida()">Aleatorio</a>
    	<a id="competicion-orden-saveBtn" href="#" class="easyui-linkbutton" onclick="saveOrdenSalida()">Guardar</a>
    	<a id="competicion-orden-reloadBtn" href="#" class="easyui-linkbutton" onclick="reloadOrdenSalida()">Actualizar</a>
    	</span>
    	<span style="float:right">
    	<!-- estos elementos deben estar alineados a la derecha -->
    	<a id="competicion-orden-printBtn" href="#" class="easyui-linkbutton" onclick="printOrdenSalida()">Imprimir</a>
	   	</span>
    </div>
</div> <!-- panel del orden de salida -->

<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
<div id="competicion_resultados" class="easyui-panel" title="Introducci&oacute;n de resultados de cada participante">
<table id="competicion-resultados-datagrid" class="competicion-resultados-datagrid"></table>
    <!-- BARRA DE TAREAS DE TABLA DE RESULTADOS-->
    <div id="competicion-resultados-toolbar">
    	<span style="float:right">
    	<!-- estos elementos deben estar alineados a la derecha -->
    	<a id="competicion-resultados-printBtn" href="#" class="easyui-linkbutton" onclick="printResultados()">Imprimir</a>
	   	</span>
    </div>
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
$('#competicion_ordensalida').panel({
	border:true,
	closable:false,
	collapsible:true,
	collapsed:false
});
$('#competicion_resultados').panel({
	border:true,
	closable:false,
	collapsible:true,
	collapsed:true
});

$('#competicion-listamangas').datagrid({
	url: 'database/select_MangasByJornada.php?Jornada='+workingData.jornada,
	method: 'get',
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    showHeader: false,
    columns:[[
            { field:'ID',			hidden:true }, // ID de la jornada
      	    { field:'Tipo',			hidden:true }, // ID de la prueba
      	    { field:'Descripcion',	width:50, sortable:false, align:'right'},
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    },
    onSelect: function (index,row) {
        if (index<0) { // no manga selected
            $('#competicion-datosmanga').html("");
            // TODO: clear & collapse panels
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
        // cargamos y desplegamos panel de orden de salida
        // TODO: write
        
        // cargamos (sin desplegar) panel de resultados
        // TODO: write
    }
});

$('#competicion-orden-datagrid').datagrid({
	url: 'database/select_JornadasByPrueba.php?Prueba='+workingData.prueba,
	method: 'get',
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    toolbar: '#competicion-orden-toolbar',
    columns:[[
            { field:'ID',			hidden:true }, // ID de la jornada
      	    { field:'Prueba',		hidden:true }, // ID de la prueba
      	    { field:'Numero',		width:4, sortable:false,	align:'center', title: '#'},
      		{ field:'Nombre',		width:40, sortable:false,   align:'right', title: 'Nombre/Comentario' },
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    }
});

$('#competicion-resultados-datagrid').datagrid({
	url: 'database/select_JornadasByPrueba.php?Prueba='+workingData.prueba,
	method: 'get',
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    toolbar: '#competicion-resultados-toolbar',
    columns:[[
            { field:'ID',			hidden:true }, // ID de la jornada
      	    { field:'Prueba',		hidden:true }, // ID de la prueba
      	    { field:'Numero',		width:4, sortable:false,	align:'center', title: '#'},
      		{ field:'Nombre',		width:40, sortable:false,   align:'right', title: 'Nombre/Comentario' },
    ]],
    rowStyler:function(index,row) { // colorize rows
        return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
    }
});

//- botones del panel de orden de mangas
$('#competicion-orden-reloadBtn').linkbutton({plain:true,iconCls:'icon-reload'}); // nueva inscricion 
$('#competicion-orden-reloadBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Actualizar el orden de salida desde base de datos</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#competicion-orden-randomBtn').linkbutton({plain:true,iconCls:'icon-dice'}); // nueva inscricion 
$('#competicion-orden-randomBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Generar un nuevo orden de salida aleatorio</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#competicion-orden-saveBtn').linkbutton({plain:true,iconCls:'icon-save'}); // editar inscripcion      
$('#competicion-orden-saveBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Guardar orden de salida en base de datos</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#competicion-orden-printBtn').linkbutton({plain:true,iconCls:'icon-print'}); // imprimir listado 
$('#competicion-orden-printBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Imprimir el orden de salida</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});

// botones del panel de resultados
$('#competicion-resultados-printBtn').linkbutton({plain:true,iconCls:'icon-print'}); // imprimir listado 
$('#competicion-resultados-printBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Imprimir resultados de la manga</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
</script>
    