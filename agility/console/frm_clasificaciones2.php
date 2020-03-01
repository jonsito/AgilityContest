<!-- 
frm_clasificaciones2.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

include_once(__DIR__ . "/../console/templates/scores_mail.inc.php");
?>

<!-- CLASIFICACIONES DE PRUEBA/JORNADA/RONDA -->
<div id="resultados-info" style="width:100%;height:550px;">
	<div id="resultados-infolayout" class="easyui-layout"  data-options="fit:true,border:true" style="padding:10px;">

        <!-- PANEL IZQUIERDO: DATOS DE LA PRUEBA -->
		<div data-options="region:'west',title:'<?php _e('Contests data'); ?>',split:true,collapsed:false,collapsible:false"
             style="width:30%;padding:10px;font-size:9px">
			<form class="result_forms" id="resultados-info-prueba" method="get">
			<table>
			<tr>
				<td colspan="2">
					<label for="resultados-info-nombre"><?php _e('Title'); ?>:</label><br />
					<input id="resultados-info-nombre" type="text" class="result_forms" readonly="readonly" name="Nombre" size="30"/>
				</td>
			</tr>
			<tr>
				<td><label for="resultados-info-club"><?php _e('Organizing Club'); ?>:</label></td>
				<td><input id="resultados-info-club" type="text" class="result_forms" readonly="readonly" name="NombreClub"/></td>
			</tr>
			<tr>
				<td><label for="resultados-info-jornada"><?php _e('Journey'); ?>:</label></td>
				<td><input id="resultados-info-jornada" type="text" class="result_forms" readonly="readonly" name="Jornada"/></td>
			</tr>
			<tr>
				<td><label for="resultados-info-fecha"><?php _e('Date'); ?>:</label></td>
				<td><input id="resultados-info-fecha" type="text" class="result_forms" readonly="readonly" name="Fecha"/></td>
			</tr>
			<tr>
				<td><label for="resultados-info-ronda"><?php _e('Grade'); ?>:</label></td>
				<td><select id="resultados-info-ronda" name="Ronda" class="result_forms" style="width:150px"></select></td>
			</tr>
			<tr>
				<td><label for="resultados-info-observaciones"><?php _e('Comments'); ?>:</label></td>
				<td><input id="resultados-info-observaciones" type="text" class="result_forms" readonly="readonly" name="Observaciones"/></td>
			</tr>
			</table>
			</form>
		</div> <!-- Datos de Prueba/Jornada/Ronda -->

        <!-- PANEL DERECHO: DATOS DE TRS/TRM DE LAS MANGAS -->
		<div data-options="region:'center',title:'<?php _e('Technical data on current round series'); ?>',split:true,collapsed:false,collapsible:false" style="width:70%;font-size:9px">
			<?php
                $mode=http_request("Mode","s","");
                if ($mode=="Games") require('dialogs/inforesultados.inc'); // may change in then future
                else if ($mode=="KO") require('dialogs/inforesultados.inc'); // may change in then future
                else require('dialogs/inforesultados.inc');
            ?>
		</div> <!-- Layout: center -->

        <!-- PANEL INFERIOR: CLASIFICACIONES -->
		<div id="resultados-data" class="scores_table"
             data-options="region:'south',split:true,collapsed:false,collapsible:false"
             style="height:70%;">

			<!-- tabla con las clasificaciones. se carga dinamicamente -->
            <?php _e('No round selected');?>
		</div>
	</div> <!-- informacion de layout -->
	
</div> <!-- panel de informacion -->

<div id="resultados-printDialog">
	<form style="padding:10px" id="resultados-printForm">
	<input type="radio" name="r_prformat" value="0" onclick="r_selectOption(0);"/><?php _e('Podium'); ?> (PDF)<br />
        <input type="radio" name="r_prformat" value="6" onclick="r_selectOption(6);"/><?php _e('Contest Hall Of Fame'); ?> (PDF)
    <br />&nbsp;<hr/><br/>
	<input type="radio" name="r_prformat" value="1" onclick="r_selectOption(1);"/><?php _e('Export in text format'); ?> (CSV)<br />
    <input type="radio" name="r_prformat" value="3" onclick="r_selectOption(3);"/><?php _e('Export as spreadsheet'); ?> (Excel)<br />
    <span  style="display:inline-block;width:100%">
        <span style="float:left">
            <input type="radio" name="r_prformat" value="8" onclick="r_selectOption(8);"/><?php _e('Global Scores'); ?> (PDF)<br />
        </span>
        <span style="float:right" id="r_mergecats_span"style="display:none"> <!-- changed in runtime -->
            <label id="r_mergecatsLbl" for="r_mergecats"><?php _e('Combine sub-categories'); ?></label>
            <select id="r_mergecats" style="width:125px" name="r_mergecats" class="easyui-combobox"></select>
        </span>
    </span>
    <span  style="display:inline-block;width:100%">
		<span style="float:left">
	        <input type="radio" id="r_prformat4" name="r_prformat" value="4" checked="checked" onclick="r_selectOption(4);"/>
            <label for="r_prformat4"><?php _e('Round Scores'); ?> (PDF)</label>
		</span>
		<span style="float:right;">
            <label id="r_prstatslbl" for="r_prstats"><?php _e('Include Statistics'); ?>:</label>
            <input id="r_prstats" style="width:78px" name="r_prstats" class="easyui-checkbox" type="checkbox" value="1" checked="checked"/>
            <br/>
            <span id="r_junior"> <!-- to hide when not in children+junior rounds -->
                <label id="r_childrenlbl" for="r_children"><?php _e('Split Children/Junior'); ?></label>
                <input id="r_children" style="width:78px" name="r_children" class="easyui-checkbox" type="checkbox" value="1" checked="checked"/>
            </span>
        </span>
	</span>
    <br />&nbsp;<hr /><br/>
	<span  style="display:inline-block;width:100%">
		<span style="float:left">
			<input type="radio" name="r_prformat" value="5" onclick="r_selectOption(5);"/><?php _e('CNEAC Qualification forms'); ?>&nbsp;<br/>
			<input type="radio" name="r_prformat" value="2" onclick="r_selectOption(2);"/><?php _e('RSCE Label sheets'); ?>&nbsp; <br/>
			&nbsp;<br />&nbsp;<br/>
		</span>
		<span style="float:right">
			<label id="r_prlistLbl" for="list"><?php _e('Dorsal list'); ?>:</label>
			<input id="r_prlist" style="width:85px" name="list" type="text" value="" disabled="disabled"/>
            <br />
            <label id="r_prfirstLbl" for="first"><?php _e('Initial label'); ?>:&nbsp;</label>
			<input id="r_prfirst" style="width:45px" type="text" value="1" disabled="disabled" name="first"/>
            <br />
            <label id="r_globalLbl" for="r_global"><?php _e('All series'); ?>:</label>
            <input id="r_global" style="width:78px" name="r_global" class="easyui-checkbox" type="checkbox" value="1"/>
            <br />
            <label id="r_discriminateLbl" for="r_discriminate"><?php _e('Filter country'); ?>:</label>
            <input id="r_discriminate" style="width:78px" name="r_discriminate" class="easyui-checkbox" type="checkbox" value="1" checked="checked"/><br/>
		</span>
	</span>
	<span  style="display:inline-block;width:100%">
		<a id="resultados-cancelDlgBtn" href="#" class="easyui-linkbutton" style="float:left"
           data-options="iconCls:'icon-cancel'" onclick="$('#resultados-printDialog').dialog('close');"><?php _e('Cancel'); ?></a>
		<a id="resultados-printDlgBtn" href="#" class="easyui-linkbutton" style="float:right"
           data-options="iconCls:'icon-print'" onclick="clasificaciones_doPrint();"><?php _e('Print'); ?></a>
	</span>
	</form>
</div>

<script type="text/javascript">

$('#r_prlist').textbox();
$('#r_prfirst').numberspinner({
    max: 16,
    min: 1,
    value: 1
});

$('#r_mergecats').combobox({
    panelHeight:'auto',
    valueField: 'value',
    textField: 'text',
    data:[
        {value:0,text:"<?php _e('Separate listings');?>" },
        {value:3,text:"<?php _e('XL+L / M / S+XS');?>"},
        {value:1,text:"<?php _e('XL+L / M+S+XS');?>"},
        {value:2,text:"<?php _e('Single listing');?>"}
    ]
});

$('#resultados-printDialog').dialog({
    title:'<?php _e('Select format'); ?>',
    modal:true,
    closable:true,
    closed:true,
    width:'600px',
    height:'400px',
    onBeforeOpen: function() {
        // mira si hay que activar boton de split Junior/Senior
        var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
        var ch= ((ronda.Rondas & 16384)!==0) && hasChildren(workingData.federation);
        $('#r_junior').css('display',(ch)?'inherit':'none');
        $('#r_prformat4').prop('checked',true); // default is print category scores, not global ones
        $('#r_mergecats_span').css('display','none');
        return true;
    }
});

// combogrid que presenta cada una de las rondas de la jornada
$('#resultados-info-ronda').combogrid({
	panelWidth: 200,
	panelHeight: 100,
	idField: 'ID',
	textField: 'Nombre',
	url: '../ajax/database/jornadaFunctions.php',
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
addTooltip($('#resultados-printDlgBtn').linkbutton(),'<?php _e("Print data in selected format"); ?>');
addTooltip($('#resultados-cancelDlgBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
addTooltip($('#r_prfirstLbl'),'<?php _e("where to start printing<br/>in labels sheet"); ?>');
addTooltip($('#r_prlistLbl'),'<?php _e("Comma separated list of dorsals to be printed"); ?>');
addTooltip($('#r_global').linkbutton(),'<?php _e("Print every series, not just selected one"); ?>');
addTooltip($('#r_discriminate').linkbutton(),'<?php _e("Omit label on country missmatch"); ?>');
addTooltip($('#r_children').linkbutton(),'<?php _e("Create separate listings for Children and Junior"); ?>');
addTooltip($('#r_mergecatsLbl'),'<?php _e("Merge categories in listing retaining TRS on each height"); ?>');

</script>
