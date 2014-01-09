
<?php include_once("dialogs/dlg_perros.inc");?>
<?php include_once("dialogs/dlg_guias.inc");?>
<?php include_once("dialogs/dlg_clubes.inc");?>
<?php include_once("dialogs/dlg_jornadas.inc");?>
 	
<!-- PANEL INFORMATIVO SOBRE LA MANGAS DE ESTA JORNADA -->
<div id="competicion_info" class="easyui-panel" title="Informaci&oacute;n de la jornada de competici&oacute;n">
<div id="competicion_infolayout" class="easyui-layout" style="height:180px">
	<div data-options="region:'west',title:'Mangas de la jornada',split:true,collapsed:false" style="width:300px;padding:10px">
		<table id="competicion_listamangas" class="easyui-datagrid"></table>
	</div>
	<div data-options="region:'center',title:'Datos de la manga'" style="width:500px">
		<table id="competicion_datosmanga" class="easyui-datagrid"></table>
	</div>
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
    
<script type="text/javascript">
        $('#Header_Operation').html('<p>Desarrollo de la jornada</p>');

// declaracion de cada elemento grafico
$('#competicion_info').panel({
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
$('#competicion-orden-randomBtn').linkbutton({plain:true,iconCls:'icon-add'}); // nueva inscricion 
$('#competicion-orden-randomBtn').tooltip({
	position: 'top',
	content: '<span style="color:#000">Generar un nuevo orden de salida aleatorio</span>',
	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
	}
});
$('#competicion-orden-saveBtn').linkbutton({plain:true,iconCls:'icon-filesave'}); // editar inscripcion      
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
    