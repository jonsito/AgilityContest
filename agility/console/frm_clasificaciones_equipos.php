<!-- 
frm_clasificaciones2.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->
 
<?php 
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>

<!-- CLASIFICACIONES DE PRUEBA/JORNADA/RONDA -->
<div id="resultados-info" style="width:100%">
	<div id="resultados-infolayout" class="easyui-layout" style="height:200px;">
		<div data-options="region:'west',title:'<?php _e('Contests data'); ?>',split:true,collapsed:false" style="width:300px;padding:10px;font-size:9px">
			<form class="result_forms" id="resultados-info-prueba" method="get">
			<table>
			<tr>
				<td colspan="2">
					<label for="Nombre"><?php _e('Title'); ?>:</label><br />
					<input id="resultados-info-nombre" type="text" class="result_forms" readonly="readonly" name="Nombre" size="30"/>
				</td>
			</tr>
			<tr>
				<td><label for="NombreClub"><?php _e('Organizing Club'); ?>:</label></td>
				<td><input id="resultados-info-club" type="text" class="result_forms" readonly="readonly" name="NombreClub"/></td>
			</tr>
			<tr>
				<td><label for="Jornada"><?php _e('Journey'); ?>:</label></td>
				<td><input id="resultados-info-jornada" type="text" class="result_forms" readonly="readonly" name="Jornada"/></td>
			</tr>
			<tr>
				<td><label for="Fecha"><?php _e('Date'); ?>:</label></td>
				<td><input id="resultados-info-fecha" type="text" class="result_forms" readonly="readonly" name="Fecha"/></td>
			</tr>
			<tr>
				<td><label for="Ronda"><?php _e('Round'); ?>:</label></td>
				<td><select id="resultados-info-ronda" name="Ronda" class="result_forms" style="width:150px"></select></td>
			</tr>
			<tr>
				<td><label for="Observaciones"><?php _e('Comments'); ?>:</label></td>
				<td><input id="resultados-info-observaciones" type="text" class="result_forms" readonly="readonly" name="Observaciones"/></td>
			</tr>
			</table>
			</form>
		</div> <!-- Datos de Prueba/Jornada/Ronda -->
	
		<div data-options="region:'center',title:'<?php _e('Technical data on current round series'); ?>'" style="width:500px;padding:10px;font-size:9px">
			<?php require('dialogs/inforesultados.inc')?>
		</div> <!-- Layout: center --> 
		
	</div> <!-- informacion de layout -->
	
</div> <!-- panel de informacion -->

<div id="resultados-data" style="width:100%;height:400px">
	<table id="resultados-datagrid">
		<thead>
			<tr>
				<th colspan="3"> <span class="main_theader"><?php _e('Team data'); ?></span></th>
				<th colspan="2"> <span class="main_theader" id="resultados_thead_m1"><?php _e('Round'); ?> 1</span></th>
				<th colspan="2"> <span class="main_theader" id="resultados_thead_m2"><?php _e('Round'); ?> 2</span></th>
				<th colspan="2"> <span class="main_theader"><?php _e('Final scores'); ?></span></th>
			</tr>
			<tr> <!--
                <th data-options="field:'ID',			hidden:true"></th>
                <th data-options="field:'Prueba',		hidden:true"></th>
                <th data-options="field:'Jornada',		hidden:true"></th>
                 -->
				<th data-options="field:'ID',		    width:'19%', sortable:false,formatter:formatTeamLogos">&nbsp</th>
				<th data-options="field:'Nombre',		width:'20.5%', sortable:false, formatter:formatBold"><?php _e('Team'); ?></th>
				<th data-options="field:'Categorias',	width:'4%', sortable:false"><?php _e('Cat'); ?></th>
				<th data-options="field:'T1',		    align:'center', width:'9.5%', sortable:false"><?php _e('Time'); ?> 1</th>
				<th data-options="field:'P1',		    align:'center',width:'10%', sortable:false"><?php _e('Penal'); ?> 1</th>
				<th data-options="field:'T2',		    align:'center',width:'9.5%', sortable:false"><?php _e('Time'); ?> 2</th>
				<th data-options="field:'P2',		    align:'center',width:'10%', sortable:false"><?php _e('Penal'); ?> 2</th>
				<th data-options="field:'Tiempo',		align:'center',width:'9%', sortable:false,formatter:formatBold"><?php _e('Time'); ?></th>
				<th data-options="field:'Penalizacion',	align:'center',width:'8.5%', sortable:false,formatter:formatBold"><?php _e('Penalization'); ?></th>
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
   			data-options="iconCls:'icon-reload'" onclick="reloadClasificaciones();"><?php _e('Refresh'); ?></a>
   		<a id="resultados-verifyBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-search'" onclick="verifyClasificaciones();"><?php _e('Verify'); ?></a>
   		<a id="resultados-printBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-print'" onclick="$('#resultados-printDialog').dialog('open');"><?php _e('Reports'); ?></a>
   	</span>
</div>

<div id="resultados-printDialog" class="easyui-dialog" 
	data-options="title:'<?php _e('Select format'); ?>',closable:true,closed:true,width:'450px',height:'250px'">
	<form style="padding:10px" id="resultados-printForm">
        <input type="radio" name="r_prformat" value="0" onclick="r_selectOption(0);"/><?php _e('Podium'); ?> (PDF)<br />
        <input type="radio" name="r_prformat" value="1" onclick="r_selectOption(1);"/><?php _e('Text export'); ?> (CSV)<br />
        <input type="radio" name="r_prformat" value="3" onclick="r_selectOption(3);"/><?php _e('R.S.C.E. Report'); ?> (Excel)<br />
        <input type="radio" name="r_prformat" value="4" checked="checked" onclick="r_selectOption(4);"/><?php _e('Scores'); ?> (PDF)<br />
	    <span  style="display:inline-block;width:100%">
		    <span style="float:left">
			    <input type="radio" name="r_prformat" value="2" onclick="r_selectOption(2);"/><?php _e('Label sheets'); ?> (PDF). <br/>
			    <input type="radio" name="r_prformat" value="5" onclick="r_selectOption(5);"/><?php _e('Isolated labels'); ?> (PDF)
		    </span>
		    <span style="float:right">
			    <label id="r_prfirstLbl" for="first"><?php _e('Initial label'); ?>:</label>
			    <input id="r_prfirst" style="width:45px" name="first" class="easyui-numberspinner"
				    data-options="value:1,min:1,max:16,disabled:true"/><br />
			    <label id="r_prlistLbl" for="list"><?php _e('Dorsal list'); ?>:</label>
			    <input id="r_prlist" style="width:85px" name="list" class="easyui-textbox" data-options="value:'',disabled:true"/><br />
		    </span>
	    </span>
	    &nbsp;<br />
	    <span  style="display:inline-block;width:100%">
		    <a id="resultados-printDlgBtn" href="#" class="easyui-linkbutton" style="float:right"
               data-options="iconCls:'icon-print'" onclick="clasificaciones_doPrint();"><?php _e('Print'); ?></a>
	    </span>
	</form>
</div>

<script type="text/javascript">

$('#resultados-data').panel({
	closable:false,
	collapsible:false,
	collapsed:false
});

$('#resultados-info').panel({
    title:'<?php _e('Journey scores'); ?>',
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
        { field:'Nombre',		width:40, sortable:false,   align:'right', title: '<?php _e('Name'); ?>' },
	   	{ field:'Juez11',		hidden:true }, // Nombre primer juez primera manga
		{ field:'Juez12',		hidden:true }, // Nombre segundo juez primera manga
	   	{ field:'Juez21',		hidden:true }, // Nombre primer juez segunda manga
		{ field:'Juez22',		hidden:true }  // Nombre segundo juez segunda manga 
	]],
	onBeforeLoad: function(param) { 
		param.Operation='rounds';
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
	Ronda:	"", // to be filled later
	Observaciones: workingData.datosPrueba.Observaciones
});

//tooltips
addTooltip($('#resultados-refreshBtn').linkbutton(),'<?php _e("Update score tables"); ?>');
addTooltip($('#resultados-verifyBtn').linkbutton(),'<?php _e("Check for dogs without registered data"); ?>');
addTooltip($('#resultados-printBtn').linkbutton(),'<?php _e("Print scores on current round"); ?>');
addTooltip($('#resultados-printDlgBtn').linkbutton(),'<?php _e("Print data in selected format"); ?>');
addTooltip($('#r_prfirstLbl'),'<?php _e("where to start printing<br/>in labels sheet"); ?>');
addTooltip($('#r_prlistLbl'),'<?php _e("Comma separated list of dorsals to be printed"); ?>');

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
    loadMsg: "<?php _e('Updating round scores');?>...",
	pagination: false,
	rownumbers: false,
	fitColumns: true,
	singleSelect: true,
	rowStyler:myRowStyler,
	view:detailview,
	pageSize: 500, // enought big to make it senseless
	// especificamos un formateador especial para desplegar la tabla de perros por equipos
	detailFormatter:function(idx,row){
		var dgname="resultados-datagrid-"+parseInt(row.ID);
		return '<div style="padding:2px"><table id="'+dgname+'"></table></div>';
	},
	onExpandRow: function(idx,row) {
		$(this).datagrid('options').expandCount++;
		showClasificacionesByTeam('#resultados-datagrid',idx,row);
	},
	onCollapseRow: function(idx,row) {
		$(this).datagrid('options').expandCount--;
		var dg="#resultados-datagrid-" + parseInt(row.ID);
		$(dg).remove();
	}
});

</script>
