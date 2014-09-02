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
		<?php require('dialogs/inforesultados.inc')?>
	</div> <!-- Layout: center --> 
</div> <!-- informacion de layout -->
</div> <!-- panel de informacion -->

<div id="resultados-data" class="easyui-panel" title="Clasificaciones">
	<div id="resultados-datatabs" class="easyui-tabs" style="height:325px;padding:0px 0px 10px 0px;" data-options="tools:'#resultados-toolbar'">
		<div title="Large" data-options="closable:false">
			<table id="resultados-large-datagrid" class="easyui-datagrid" style="padding:10px 20px"></table>
		</div>
		<div title="Medium" data-options="closable:false">
			<table id="resultados-medium-datagrid" class="easyui-datagrid" style="padding:10px 20px"></table>
		</div>
		<div title="Small" data-options="closable:false">
			<table id="resultados-small-datagrid" class="easyui-datagrid" style="padding:10px 20px"></table>
		</div>
	</div>
</div>

<div id="resultados-toolbar">
    <a id="resultados-refreshBtn" href="#" class="easyui-linkbutton" 
    	data-options="iconCls:'icon-reload'" onclick="reloadClasificacion();">Refrescar</a>
    <a id="resultados-printBtn" href="#" class="easyui-linkbutton" 
    	data-options="iconCls:'icon-print'" onclick="resultados_doPrint()">Imprimir</a>
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

// combogrid que presenta cada una de las rondas de la jornada
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
		{ field:'Recorrido1',	hidden:true }, // tipo de recorrido		
		{ field:'Recorrido2',	hidden:true }, // tipo de recorrido		
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

// form que contiene la informacion de la prueba
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
addTooltip($('#resultados-printBtn').linkbutton(),"Imprimir los resultados de la manga"); 

$('#resultados-large-datagrid').datagrid({// propiedades del panel asociado
		fit: true,
		border: false,
		closable: false,
		collapsible: false,
		collapsed: false,
		// propiedades del datagrid
		// no tenemos metodo get ni parametros: directamente cargamos desde el datagrid
	    loadMsg: "Actualizando resultados de la ronda: 'Large'",
	    pagination: false,
	    rownumbers: false,
	    fitColumns: true,
	    singleSelect: true,
	    columns:[[
			{ field:'Prueba',		hidden:true },
			{ field:'Jornada',		hidden:true },
			{ field:'Manga1',		hidden:true },
			{ field:'Manga2',		hidden:true },
	        { field:'Perro',		hidden:true },
	        { field:'Puesto',		width:10, align:'left',  title: ' # '},
	        { field:'Dorsal',		width:10, align:'left',  title: 'Dorsal'},
	        { field:'Nombre',		width:15, align:'left',  title: 'Nombre'},
	      	{ field:'Licencia',		width:10, align:'left',  title: 'Lic.' },
	      	{ field:'Categoria',	width:10, align:'center',title: 'Cat.' }, // categoria y grado
	        { field:'NombreGuia',	width:40, align:'right', title: 'Guia' },
	        { field:'NombreClub',	width:30, align:'right', title: 'Club' },
	      	{ field:'F1',			width:10, align:'center', title:'F/T'},
	      	{ field:'R1',			width:10, align:'center', title:'Reh.'},
	      	{ field:'T1',			width:15, align:'right', title: 'Tiempo'},
	      	{ field:'V1',			width:10, align:'right', title: 'Vel.'},
	      	{ field:'P1',			width:15, align:'right', title: 'Penal.'}, 
	      	{ field:'C1',			width:20, align:'center',title: 'Calif'},
	      	{ field:'F2',			width:10, align:'center', title:'F/T'},
	      	{ field:'R2',			width:10, align:'center', title:'Reh.'},
	      	{ field:'T2',			width:15, align:'right', title: 'Tiempo'},
	      	{ field:'V2',			width:10, align:'right', title: 'Vel.'},
	      	{ field:'P2',			width:15, align:'right', title: 'Penal.'}, 
	      	{ field:'C2',			width:20, align:'center',title: 'Calif'},
	      	{ field:'Penalizacion',	width:15, align:'right', title: 'Penalizacion'}, 
	      	{ field:'Calificacion',	width:25, align:'center',title: 'Calificacion'}
	      	
	    ]],
	    rowStyler:myRowStyler
	    });
$('#resultados-medium-datagrid').datagrid({// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	// no tenemos metodo get ni parametros: directamente cargamos desde el datagrid
    loadMsg: "Actualizando resultados de la ronda: 'Medium'",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    columns:[[
		{ field:'Prueba',		hidden:true },
		{ field:'Jornada',		hidden:true },
		{ field:'Manga1',		hidden:true },
		{ field:'Manga2',		hidden:true },
        { field:'Perro',		hidden:true },
        { field:'Puesto',		width:10, align:'left',  title: ' # '},
        { field:'Dorsal',		width:10, align:'left',  title: 'Dorsal'},
        { field:'Nombre',		width:15, align:'left',  title: 'Nombre'},
      	{ field:'Licencia',		width:10, align:'left',  title: 'Lic.' },
      	{ field:'Categoria',	width:10, align:'center',title: 'Cat.' }, // categoria y grado
        { field:'NombreGuia',	width:40, align:'right', title: 'Guia' },
        { field:'NombreClub',	width:30, align:'right', title: 'Club' },
      	{ field:'F1',			width:10, align:'center', title:'F/T'},
      	{ field:'R1',			width:10, align:'center', title:'Reh.'},
      	{ field:'T1',			width:15, align:'right', title: 'Tiempo'},
      	{ field:'V1',			width:10, align:'right', title: 'Vel.'},
      	{ field:'P1',			width:15, align:'right', title: 'Penal.'}, 
      	{ field:'C1',			width:20, align:'center',title: 'Calif'},
      	{ field:'F2',			width:10, align:'center', title:'F/T'},
      	{ field:'R2',			width:10, align:'center', title:'Reh.'},
      	{ field:'T2',			width:15, align:'right', title: 'Tiempo'},
      	{ field:'V2',			width:10, align:'right', title: 'Vel.'},
      	{ field:'P2',			width:15, align:'right', title: 'Penal.'}, 
      	{ field:'C2',			width:20, align:'center',title: 'Calif'},
      	{ field:'Penalizacion',	width:15, align:'right', title: 'Penalizacion'}, 
      	{ field:'Calificacion',	width:25, align:'center',title: 'Calificacion'}
      	
    ]],
    rowStyler:myRowStyler
    });
$('#resultados-small-datagrid').datagrid({// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	// no tenemos metodo get ni parametros: directamente cargamos desde el datagrid
    loadMsg: "Actualizando resultados de la ronda: 'Small'",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    columns:[[
		{ field:'Prueba',		hidden:true },
		{ field:'Jornada',		hidden:true },
		{ field:'Manga1',		hidden:true },
		{ field:'Manga2',		hidden:true },
        { field:'Perro',		hidden:true },
        { field:'Puesto',		width:10, align:'left',  title: ' # '},
        { field:'Dorsal',		width:10, align:'left',  title: 'Dorsal'},
        { field:'Nombre',		width:15, align:'left',  title: 'Nombre'},
      	{ field:'Licencia',		width:10, align:'left',  title: 'Lic.' },
      	{ field:'Categoria',	width:10, align:'center',title: 'Cat.' }, // categoria y grado
        { field:'NombreGuia',	width:40, align:'right', title: 'Guia' },
        { field:'NombreClub',	width:30, align:'right', title: 'Club' },
      	{ field:'F1',			width:10, align:'center', title:'F/T'},
      	{ field:'R1',			width:10, align:'center', title:'Reh.'},
      	{ field:'T1',			width:15, align:'right', title: 'Tiempo'},
      	{ field:'V1',			width:10, align:'right', title: 'Vel.'},
      	{ field:'P1',			width:15, align:'right', title: 'Penal.'}, 
      	{ field:'C1',			width:20, align:'center',title: 'Calif'},
      	{ field:'F2',			width:10, align:'center', title:'F/T'},
      	{ field:'R2',			width:10, align:'center', title:'Reh.'},
      	{ field:'T2',			width:15, align:'right', title: 'Tiempo'},
      	{ field:'V2',			width:10, align:'right', title: 'Vel.'},
      	{ field:'P2',			width:15, align:'right', title: 'Penal.'}, 
      	{ field:'C2',			width:20, align:'center',title: 'Calif'},
      	{ field:'Penalizacion',	width:15, align:'right', title: 'Penalizacion'}, 
      	{ field:'Calificacion',	width:25, align:'center',title: 'Calificacion'}
      	
    ]],
    rowStyler:myRowStyler
    });

</script>