<!-- 
frm_clasificaciones2.php

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->
 <!-- CLASIFICACIONES DE PRUEBA/JORNADA/RONDA -->
<div id="resultados-info" style="width:100%">
	<div id="resultados-infolayout" class="easyui-layout" style="height:200px;">
		<div data-options="region:'west',title:'Datos de la Prueba',split:true,collapsed:false" style="width:300px;padding:10px;font-size:9px">
			<form class="result_forms" id="resultados-info-prueba" method="get">
			<table>
			<tr>
				<td colspan="2">
					<label for="Nombre">Denominaci&oacute;n:</label><br />
					<input id="resultados-info-nombre" type="text" class="result_forms" readonly="readonly" name="Nombre" size="30"/>
				</td>
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

<div id="resultados-data" style="width:100%;height:400px">
	<table id="resultados-datagrid">
		<thead>
			<tr>
				<th colspan="7"> <span class="resultados_theader">Datos del participante</span></th>
			    <th colspan="6"> <span class="resultados_theader" id="resultados_thead_m1">Manga 1</span></th>
			    <th colspan="6"> <span class="resultados_theader" id="resultados_thead_m2">Manga 2</span></th>
			    <th colspan="4"> <span class="resultados_theader">Clasificaci&oacute;n</span></th>
		    </tr>
		    <tr>
		    	<!-- 
	    		<th data-options="field:'Perro',		hidden:true " ></th>
	     		-->
	    		<th data-options="field:'Dorsal',		width:20, align:'left'" > Dors.</th>
	    		<th data-options="field:'Nombre',		width:35, align:'left'" > Nombre</th>
	   			<th data-options="field:'Licencia',		width:15, align:'center'" > Lic.</th>
	   			<th data-options="field:'Categoria',	width:15, align:'center'" > Cat.</th>
	   			<th data-options="field:'Grado',		width:15, align:'center'" > Grd.</th>
	    		<th data-options="field:'NombreGuia',	width:50, align:'right'" > Guia</th>
	    		<th data-options="field:'NombreClub',	width:45, align:'right'" > Club</th>
	  			<th data-options="field:'F1',			width:15, align:'center',styler:formatBorder"> F/T</th>
	  			<th data-options="field:'R1',			width:15, align:'center'"> R.</th>
	  			<th data-options="field:'T1',			width:25, align:'right',formatter:formatT1"> Tmp.</th>
	   			<th data-options="field:'V1',			width:15, align:'right',formatter:formatV1"> Vel</th>
	   			<th data-options="field:'P1',			width:20, align:'right',formatter:formatP1"> Penal.</th>
	   			<th data-options="field:'C1',			width:25, align:'center'"> Cal.</th>
	   			<th data-options="field:'F2',			width:15, align:'center',styler:formatBorder"> F/T</th>
	  			<th data-options="field:'R2',			width:15, align:'center'"> R.</th>
	   			<th data-options="field:'T2',			width:25, align:'right',formatter:formatT2"> Tmp.</th>
	    		<th data-options="field:'V2',			width:15, align:'right',formatter:formatV2"> Vel.</th>
	    		<th data-options="field:'P2',			width:20, align:'right',formatter:formatP2"> Penal.</th>
	    		<th data-options="field:'C2',			width:25, align:'center'"> Cal.</th> 
	    		<th data-options="field:'Tiempo',		width:25, align:'right',formatter:formatTF,styler:formatBorder">Tiempo</th>
	    		<th data-options="field:'Penalizacion',	width:25, align:'right',formatter:formatPenalizacionFinal" > Penaliz.</th>
	    		<th data-options="field:'Calificacion',	width:20, align:'center'" > Calif.</th>
	    		<th data-options="field:'Puesto',		width:15, align:'center',formatter:formatPuestoFinal" > Puesto </th>
	    	</tr>
		</thead>
	</table>
</div>

<div id="resultados-toolbar" style="width:100%;display:inline-block">
   	<span style="float:left;padding:5px">
   	    <input id="resultados-selectCategoria" name="Categoria">
   	</span>
   	<span style="float:right;padding:5px">
   		<a id="resultados-refreshBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-reload'" onclick="reloadClasificaciones();">Refrescar</a>
   		<a id="resultados-printBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-print'" onclick="resultados_doPrint()">Informes</a>
   	</span>
</div>

<script type="text/javascript">

$('#resultados-data').panel({
	closable:false,
	collapsible:false,
	collapsed:false
});

$('#resultados-info').panel({
	title:'Clasificaciones de la Jornada',
	closable:true,
	collapsible:false,
	collapsed:false,
	onClose:function(){$('#resultados-data').panel('close');}
});

$('#resultados-infolayout').layout();
$('#resultados-selectCategoria').combobox({
		valueField:'mode',
		textField:'text',
		panelHeight:75,
		onSelect:function (index,row) {	reloadClasificaciones(); }
});


// combogrid que presenta cada una de las rondas de la jornada
$('#resultados-info-ronda').combogrid({
	panelWidth: 200,
	panelHeight: 100,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/jornadaFunctions.php',
	method: 'get',
	mode: 'remote',
	required: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	columns: [[
	   	{ field:'Manga1',		hidden:true }, // ID de la manga1
		{ field:'Manga2',		hidden:true }, // ID de la manga2
		{ field:'NombreManga1',		hidden:true }, // Nombre de la manga1
		{ field:'NombreManga2',		hidden:true }, // Nombre de la manga2
		{ field:'Recorrido1',	hidden:true }, // tipo de recorrido	manga 1	
		{ field:'Recorrido2',	hidden:true }, // tipo de recorrido	manga 2
		{ field:'Rondas',		hidden:true }, // bitfield del tipo de rondas
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
		"/agility/server/database/mangaFunctions.php?Operation=getTRS&Jornada="+workingData.jornada+"&Manga="+workingData.datosRonda.Manga1
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
		"/agility/server/database/mangaFunctions.php?Operation=getTRS&Jornada="+workingData.jornada+"&Manga="+workingData.datosRonda.Manga2
		);

//tooltips
addTooltip($('#resultados-refreshBtn').linkbutton(),"Actualizar la tabla de resultados");
addTooltip($('#resultados-printBtn').linkbutton(),"Imprimir los resultados de la manga"); 

$('#resultados-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	toolbar:'#resultados-toolbar',
	// no tenemos metodo get ni parametros: directamente cargamos desde el datagrid
	loadMsg: "Actualizando resultados de la ronda...",
	pagination: false,
	rownumbers: false,
	fitColumns: true,
	singleSelect: true,
	rowStyler:myRowStyler
});

</script>
