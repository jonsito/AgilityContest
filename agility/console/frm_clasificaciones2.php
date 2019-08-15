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

include_once(__DIR__."/../lib/templates/scores_mail.inc.php");
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
				<td><label for="resultados-info-ronda"><?php _e('Round'); ?>:</label></td>
				<td><select id="resultados-info-ronda" name="Ronda" class="result_forms" style="width:150px"></select></td>
			</tr>
			<tr>
				<td><label for="resultados-info-observaciones"><?php _e('Comments'); ?>:</label></td>
				<td><input id="resultados-info-observaciones" type="text" class="result_forms" readonly="readonly" name="Observaciones"/></td>
			</tr>
			</table>
			</form>
		</div> <!-- Datos de Prueba/Jornada/Ronda -->
	
		<div data-options="region:'center',title:'<?php _e('Technical data on current round series'); ?>',split:true,collapsed:false,collapsible:false" style="width:70%;font-size:9px">
			<?php
                $mode=http_request("Mode","s","");
                if ($mode=="Games") require('dialogs/inforesultados.inc'); // may change in then future
                else if ($mode=="KO") require('dialogs/inforesultados.inc'); // may change in then future
                else require('dialogs/inforesultados.inc');
            ?>
		</div> <!-- Layout: center -->

		<div id="resultados-dataregion" class="scores_table" data-options="region:'south',split:true,collapsed:false,collapsible:false" style="height:70%;">
            <div id="resultados-toolbar" style="width:100%;display:none"> <!-- hide until datagrid gets loaded -->
                <table style="width:100%;padding:2px;">
                    <tr>
                        <td><label for="resultados-selectCategoria"><?php _e('Category');?></label></td>
                        <td>
                            <input id="resultados-selectCategoria" name="Categoria">
                        </td>
                        <td style="width:10%">&nbsp;</td>
                        <td>
                            <a id="resultados-competicionBtn" href="#" class="easyui-linkbutton"
                               data-options="iconCls:'icon-endflag'" onclick="loadCompetitionWindow();"><?php _e('Competition'); ?></a>
                        </td>
                        <td style="width:10%">&nbsp;</td>
                        <td>
                            <a id="resultados-refreshBtn" href="#" class="easyui-linkbutton"
                               data-options="iconCls:'icon-reload'" onclick="reloadClasificaciones();"><?php _e('Refresh'); ?></a>
                        </td>
                        <td>
                            <a id="resultados-verifyBtn" href="#" class="easyui-linkbutton"
                               data-options="iconCls:'icon-search'" onclick="verifyClasificaciones();"><?php _e('Verify'); ?></a>
                        </td>
                        <td>
                            <a id="resultados-emailBtn" href="#" class="easyui-linkbutton"
                               data-options="iconCls:'icon-mail'" onclick="emailClasificaciones(false);"><?php _e('Mail'); ?></a>
                        </td>
                        <td>
                            <a id="resultados-printBtn" href="#" class="easyui-linkbutton"
                               data-options="iconCls:'icon-print'" onclick="$('#resultados-printDialog').dialog('open');"><?php _e('Reports'); ?></a>
                        </td>
                    </tr>
                </table>
            </div>
			<!-- tabla con las clasificaciones. se carga dinamicamente -->
            <div id="resultados-data" class="scores_table">
                <?php _e('No round selected');?>
            </div>
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
	        <input type="radio" id="r_prformat4" name="r_prformat" value="4" checked="checked" onclick="r_selectOption(4);"/>
            <label for="r_prformat4"><?php _e('Scores'); ?> (PDF)</label>
		</span>
		<span style="float:right">
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
            <label id="r_discriminateLbl" for="r_discriminate"><?php _e('Filter country'); ?>:</label>
            <input id="r_discriminate" style="width:78px" name="r_discriminate" class="easyui-checkbox" type="checkbox" value="1" checked="checked"/><br/>
		</span>
	</span>
	<span  style="display:inline-block;width:100%">
		<a id="resultados-cancelDlgBtn" href="#" class="easyui-linkbutton" style="float:left"
           data-options="iconCls:'icon-print'" onclick="$('#resultados-printDialog').dialog('close');"><?php _e('Cancel'); ?></a>
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
$('#resultados-printDialog').dialog({
    title:'<?php _e('Select format'); ?>',
    modal:true,
    closable:true,
    closed:true,
    width:'475px',
    height:'400px',
    onBeforeOpen: function() {
        var ronda=$('#resultados-info-ronda').combogrid('grid').datagrid('getSelected');
        var ch= ((ronda.Rondas & 16384)!==0) && hasChildren(workingData.federation);
        $('#r_junior').css('display',(ch)?'inherit':'none');
        return true;
    }
});

$('#resultados-selectCategoria').combobox({
    width:125,
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
addTooltip($('#resultados-competicionBtn').linkbutton(),'<?php _e("Jump to Journey development window"); ?>');
addTooltip($('#resultados-refreshBtn').linkbutton(),'<?php _e("Update score tables"); ?>');
addTooltip($('#resultados-verifyBtn').linkbutton(),'<?php _e("Check for dogs without registered data"); ?>');
addTooltip($('#resultados-printBtn').linkbutton(),'<?php _e("Print scores on current round"); ?>');
addTooltip($('#resultados-emailBtn').linkbutton(),'<?php _e("Share results and scores by electronic mail"); ?>');
addTooltip($('#resultados-printDlgBtn').linkbutton(),'<?php _e("Print data in selected format"); ?>');
addTooltip($('#resultados-cancelDlgBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
addTooltip($('#r_prfirstLbl'),'<?php _e("where to start printing<br/>in labels sheet"); ?>');
addTooltip($('#r_prlistLbl'),'<?php _e("Comma separated list of dorsals to be printed"); ?>');
addTooltip($('#r_discriminate').linkbutton(),'<?php _e("Omit label on country missmatch"); ?>');
addTooltip($('#r_children').linkbutton(),'<?php _e("Create separate listings for Children and Junior"); ?>');

</script>
