<!-- CLASIFICACIONES DE PRUEBA/JORNADA/RONDA -->
<div id="resultados-info" class="easyui-panel" title="Informacion de la Ronda">
<div id="resultados-infolayout" class="easyui-layout" style="height:220px;">
	<div data-options="region:'west',title:'Datos de la Prueba',split:true,collapsed:false" style="width:300px;padding:10px;font-size:9px">
		<form class="result_forms" id="resultados-info-prueba" method="get">
		<table>
		<tr>
			<td colspan="2"><label for="Nombre">Denominaci&oacute;n:</label><br />
			<input id="resultados-info-nombre" type="text" class="result_forms" readonly="readonly" name="Nombre" size="30"/></td>
		</tr>
		<tr>
			<td><label for="NombreClub">Club Organizador:</label></td>
			<td><input id="resultados-info-club" type="text" class="result_forms" readonly="readonly" name="NombreClub"/></td>
		</tr>
		<tr>
			<td><label for="Jornada">Jornada:</label></td>
			<td><input id="resultados-info-jornada" type="text" class="result_forms" readonly="readonly" name="Jornada"/></td>
		</tr>
		<tr>
			<td><label for="Fecha">Fecha:</label></td>
			<td><input id="resultados-info-fecha" type="text" class="result_forms" readonly="readonly" name="Fecha"/></td>
		</tr>
		<tr>
			<td><label for="Ronda">Ronda:</label></td>
			<td><select id="resultados-info-ronda" name="Ronda" class="result_forms" style="width:150px"></select></td>
		</tr>
		<tr>
			<td><label for="Observaciones">Observaciones:</label></td>
			<td><input id="resultados-info-observaciones" type="text" class="result_forms" readonly="readonly" name="Observaciones"/></td>
		</tr>
		</table>
		</form>
	</div> <!-- Datos de Prueba/Jornada/Ronda -->
	
	<div data-options="region:'center',title:'Datos t&eacute;cnicos de las Mangas de esta ronda'" style="width:500px;padding:10px;font-size:9px">
		<?php require('dialogs/inforesultados.php')?>
	</div> <!-- Layout: center --> 
</div> <!-- informacion de layout -->
</div> <!-- panel de informacion -->

<div id="resultados-data" class="easyui-panel" title="Clasificaciones">
	<div id="resultados-datatabs" class="easyui-tabs" style="height:325px;padding:0px 0px 10px 0px;" data-options="tools:'#resultados-toolbar'">
		<div title="Manga 1" data-options="closable:false">
			<table id="resultados-manga1-datagrid" style="padding:10px 20px"></table>
		</div>
		<div title="Manga 2" data-options="closable:false">
			<table id="resultados-manga2-datagrid" style="padding:10px 20px"></table>
		</div>
		<div title="Final" data-options="closable:false">
			<table id="resultados-conjunta-datagrid" style="padding:10px 20px"></table>
		</div>
	</div>
</div>

<div id="resultados-toolbar">
    <a id="resultados-refreshBtn" href="#" class="easyui-linkbutton" 
    	data-options="iconCls:'icon-reload'" onclick="reloadClasificacion();">Refrescar</a>
    <a id="resultados-labelsBtn" href="#" class="easyui-linkbutton" 
    	data-options="iconCls:'icon-table'" onclick="printEtiquetas()">Etiquetas</a>
    <a id="resultados-printBtn" href="#" class="easyui-linkbutton" 
    	data-options="iconCls:'icon-print'" onclick="printResultados">Imprimir</a>
</div>

<script type="text/javascript">

//inicializamos formularios
$('#resultados-info').panel({
	border:true,
	closable:false,
	collapsible:true,
	collapsed:false
});
$('#resultados-data').panel({
	border:		true,
	closable:	false,
	collapsible:false,
	collapsed:	false,
});

$('#resultados-infolayout').layout();
$('#resultados-datatabs').tabs( {tools:'#resultados-toolbar'});

$('#resultados-info-ronda').combogrid({
	panelWidth: 200,
	panelHeight: 100,
	idField: 'ID',
	textField: 'Nombre',
	url: 'database/jornadaFunctions.php',
	method: 'get',
	mode: 'remote',
	required: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	columns: [[
	   	{ field:'Manga1',		hidden:true }, // ID de la manga1
		{ field:'Manga2',		hidden:true }, // ID de la manga2
		{ field:'Nombre',		width:40, sortable:false,   align:'right', title: 'Nombre' }
	]],
	onBeforeLoad: function(param) { 
		param.Operation='rounds', 
		param.Prueba=workingData.prueba; 
		param.ID=workingData.jornada; 
		return true;
	},	
	onSelect:function(index,row) {
		resultados_doSelectRonda(row);
	}
});


$('#resultados-info-prueba').form('load',{
	Nombre:	workingData.datosPrueba.Nombre,
	NombreClub:	workingData.datosPrueba.NombreClub,
	Jornada: workingData.datosJornada.Nombre,
	Fecha:	workingData.datosJornada.Fecha,
	Ronda:	workingData.datosRonda.Nombre,
	Observaciones: workingData.datosPrueba.Observaciones
});

$('#resultados-manga1-trs-form').form({
		onLoadSuccess: function (data) {
			var j=" - "+workingData.datosRonda.Juez12;
			$('#rm1t').val(workingData.datosRonda.Nombre1);
			if (j===" - -- Sin asignar --") j=""; 
			$('#rm1j').val(workingData.datosRonda.Juez11+j);
			// data.Observaciones=workingData.datosRonda.Observaciones1;
		}
	});
	
$('#resultados-manga1-trs-form').form(
		'load',
		"database/mangaFunctions.php?Operation=getTRS&Jornada="+workingData.jornada+"&Manga="+workingData.datosRonda.Manga1
		);
$('#resultados-manga2-trs-form').form({
	onLoadSuccess: function (data) {
		var j=" - "+workingData.datosRonda.Juez22;
		$('#rm2t').val(workingData.datosRonda.Nombre2);
		if (j===" - -- Sin asignar --") j=""; 
		$('#rm2j').val(workingData.datosRonda.Juez21+j);
		// data.Observaciones=workingData.datosRonda.Observaciones2;
	}
});
$('#resultados-manga2-trs-form').form(
		'load',
		"database/mangaFunctions.php?Operation=getTRS&Jornada="+workingData.jornada+"&Manga="+workingData.datosRonda.Manga2
		);

//tooltips
addTooltip($('#resultados-refreshBtn').linkbutton(),"Actualizar la tabla de resultados");
addTooltip($('#resultados-labelsBtn').linkbutton(),"Generar patron CSV para componer etiquetas");
addTooltip($('#resultados-printBtn').linkbutton(),"Imprimir los resultados de la manga"); 

// declaracion de las diversas tablas de resultados
$('#resultados-manga1-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: 'database/clasificacionesFunctions.php',
    queryParams: {
        Jornada: workingData.jornada,
        Manga: workingData.manga,
        Operation: 'parcial',
        Categorias: $('#resultados-cat-form-select').val()
    },
    loadMsg: "Actualizando resultados de la manga 1 ....",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    // toolbar: '#resultadosmanga-toolbar',
    columns:[[
        { field:'Manga',		hidden:true },
        { field:'IDPerro',		hidden:true },
        // { field:'IDPerro',		width:10, align:'left',  title: 'IDPerro'},
      	{ field:'Licencia',		hidden:true },
        { field:'Puesto',		width:10, align:'left',  title: 'Puesto'},
        { field:'Nombre',		width:15, align:'left',  title: 'Nombre'},
        { field:'Guia',			width:40, align:'right', title: 'Guia' },
        { field:'Club',			width:25, align:'right', title: 'Club' },
      	{ field:'Categoria',	width:10, align:'center',title: 'Cat.' },
      	{ field:'Faltas',		width:10, align:'center', title: 'Faltas'},
      	{ field:'Rehuses',		width:10, align:'center', title: 'Rehuses'},
      	{ field:'Tocados',		width:10, align:'center', title: 'Tocados'},
      	{ field:'Tiempo',		width:15, align:'right', title: 'Tiempo'},
      	{ field:'Velocidad',	width:10, align:'right', title: 'Vel.'},
      	{ field:'Penalizacion',	width:15, align:'right', title: 'Penal.'}, 
      	{ field:'Calificacion',	width:25, align:'center',title: 'Calificacion'}
    ]],
    rowStyler:myRowStyler,
    onBeforeLoad: function(param) {
        param.Categorias=$('#resultados-cat-form-select').val();
        // do not load if no manga selected
        return (workingData.manga<=0)?false:true; 
    } 
});

$('#resultados-manga2-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: 'database/clasificacionesFunctions.php',
    queryParams: {
        Jornada: workingData.jornada,
        Manga: workingData.manga2,
        Operation: 'parcial',
        Categorias: $('#resultados-cat-form-select').val()
    },
    loadMsg: "Actualizando resultados de la manga 2 ....",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    columns:[[
        { field:'Manga',		hidden:true },
        { field:'IDPerro',		hidden:true },
        // { field:'IDPerro',		width:10, align:'left',  title: 'IDPerro'},
      	{ field:'Licencia',		hidden:true },
        { field:'Puesto',		width:10, align:'left',  title: 'Puesto'},
        { field:'Nombre',		width:15, align:'left',  title: 'Nombre'},
        { field:'NombreGuia',			width:40, align:'right', title: 'Guia' },
        { field:'NombreClub',			width:25, align:'right', title: 'Club' },
      	{ field:'Categoria',	width:10, align:'center',title: 'Cat.' },
      	{ field:'Faltas',		width:10, align:'center', title: 'Faltas'},
      	{ field:'Rehuses',		width:10, align:'center', title: 'Rehuses'},
      	{ field:'Tocados',		width:10, align:'center', title: 'Tocados'},
      	{ field:'Tiempo',		width:15, align:'right', title: 'Tiempo'},
      	{ field:'Velocidad',	width:10, align:'right', title: 'Vel.'},
      	{ field:'Penalizacion',	width:15, align:'right', title: 'Penal.'}, 
      	{ field:'Calificacion',	width:25, align:'center',title: 'Calificacion'}
    ]],
    rowStyler:myRowStyler,
    onBeforeLoad: function(param) {
        param.Categorias=$('#resultados-cat-form-select').val();
        // do not load if no manga selected
        return (workingData.manga<=0)?false:true; 
    } 
});

$('#resultados-conjunta-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: 'database/clasificacionesFunctions.php',
    queryParams: {
        Jornada: workingData.jornada,
        Manga: workingData.manga,
        Manga2: workingData.manga2,
        Operation: 'final',
        Categorias: $('#resultados-cat-form-select').val()
    },
    loadMsg: "Actualizando resultados de la clasificacion conjunta ....",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    rowStyler:myRowStyler,
    onBeforeLoad: function(param) { // do not load if no manga selected
        if (workingData.manga<=0) return false;
        if (workingData.manga2<=0) return false;
        param.Categorias=$('#resultados-cat-form-select').val();
        return true;
    }
});

</script>