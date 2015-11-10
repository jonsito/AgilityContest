<!-- 
infomanga_rsce.inc

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

<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<!-- Formulario que contiene los datos de una manga -->

<form id="competicion-formdatosmanga">
	<input type="hidden" id="dmanga_Operation" name="Operation" value=""/>
	<input type="hidden" id="dmanga_Jornada" name="Jornada" value=""/>
	<input type="hidden" id="dmanga_Manga" name="Manga" value=""/>
	<input type="hidden" id="dmanga_ID" name="ID" value=""/>
	<input type="hidden" id="dmanga_Tipo" name="Tipo" value=""/>
	<table id="competicion-tabladatosmanga">
		<tr>
			<td colspan="10">&nbsp;</td>
		</tr>
		<tr> <!-- fila 0: datos de los jueces -->
			<td colspan="4">
				<span style="text-align:right"><?php _e('Judge'); ?> 1:</span>
				<select id="dmanga_Juez1" name="Juez1" style="width:200px"></select>
			</td>
			<td colspan="4">
				<span style="text-align:right"><?php _e('Judge'); ?> 2:</span>
				<select id="dmanga_Juez2" name="Juez2" style="width:200px"></select>
			</td>
			<td>&nbsp;</td>
			<td colspan="1">
				<a href="#" class="easyui-linkbutton" data-options="iconCls:'icon-whistle'" 
					id="dmanga_SameJuez" onclick="dmanga_shareJuez();"><?php _e('Replicate'); ?></a>
			</td>
		</tr>
		<tr>
			<td colspan="10">&nbsp;</td>
		</tr>
		<tr> <!-- fila 1 tipos de recorrido -->
			<td><?php _e('Courses'); ?>: </td>
			<td colspan="3">
				<input type="radio" id="dmanga_Recorrido_0" name="Recorrido" value="2" onClick="dmanga_setRecorridos();"/>
				<label for="dmanga_Recorrido_0"><?php _e('Common course'); ?></label>
			</td>
			<td colspan="3">
				<input type="radio" id="dmanga_Recorrido_1" name="Recorrido" value="1" onClick="dmanga_setRecorridos();"/>
				<label for="dmanga_Recorrido_1">Std / Mini-Midi</label>
			</td>
			<td colspan="3">
				<input type="radio" id="dmanga_Recorrido_2" name="Recorrido" value="0" onClick="dmanga_setRecorridos();"/>
				<label for="dmanga_Recorrido_2">Std / Midi / Mini</label>
			</td>
		</tr>
		<tr>
			<td colspan="10">&nbsp;</td>
		</tr>
		<tr style="background-color:#c0c0c0"> <!-- fila 2: titulos  -->
			<td><?php _e('Category'); ?></td>
			<td><?php _e('Distance'); ?></td>
			<td><?php _e('Obstacles'); ?></td>
			<td colspan="4"><?php _e('Standard Course Time'); ?></td>
			<td colspan="3"><?php _e('Maximum Course Time'); ?></td>
		</tr>
		<tr id="dmanga_LargeRow"> <!-- fila 3: recorrido comun datos standard -->
			<td id="dmanga_LargeLbl">Standard</td>
			<td><input type="text" id="dmanga_DistL" name="Dist_L" size="4" value="0" onChange="dmanga_setRecorridos();"/></td>
			<td><input type="text" id="dmanga_ObstL" name="Obst_L" size="4" value="0" onChange="dmanga_setRecorridos();"/></td>
			<!-- datos para TRS standard -->
			<td colspan="2"> 
				<select id="dmanga_TRS_L_Tipo" name="TRS_L_Tipo" 
					onChange="if(this.value==0) $('#dmanga_TRS_L_Unit').val('s');">
				<option value="0" selected="selected"><?php _e('Fixed SCT');?></option>
				<option value="1"><?php _e('Best result');?> + </option>
				<option value="2"><?php _e('3 best average');?> + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRS_L_Factor" name="TRS_L_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRS_L_Unit" name="TRS_L_Unit">
				<option value="s" selected="selected"><?php _e('Secs');?>.</option>
				<option value="%">%</option>
				</select>
			</td>
			<!-- datos para TRM standard -->
			<td>
				<select id="dmanga_TRM_L_Tipo" name="TRM_L_Tipo" 
					onChange="if(this.value==0) $('#dmanga_TRM_L_Unit').val('s');">
				<option value="0" selected="selected"><?php _e('Fixed MCT');?></option>
				<option value="1"><?php _e('SCT');?> + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRM_L_Factor" name="TRM_L_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRM_L_Unit" name="TRM_L_Unit">
				<option value="s" selected="selected"><?php _e('Secs');?>.</option>
				<option value="%">%</option>
				</select>
			</td>
		</tr>
		<tr id="dmanga_MediumRow"> <!-- fila 4: recorrido std / mini+midi datos midi -->
			<td id="dmanga_MediumLbl">Midi</td>
			<td><input type="text" id="dmanga_DistM" name="Dist_M" size="4" value="0" onChange="dmanga_setRecorridos();"/></td>
			<td><input type="text" id="dmanga_ObstM" name="Obst_M" size="4" value="0" onChange="dmanga_setRecorridos();"/></td>
			<!-- datos para TRS medium -->
			<td colspan="2"> 
				<select id="dmanga_TRS_M_Tipo" name="TRS_M_Tipo" 
					onChange="if(this.value==0) $('#dmanga_TRS_M_Unit').val('s');">
				<option value="0" selected="selected"><?php _e('Fixed SCT');?></option>
				<option value="1"><?php _e('Best result');?> + </option>
				<option value="2"><?php _e('3 best average');?> + </option>
				<option value="3"><?php _e('SCT Standard');?> + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRS_M_Factor" name="TRS_M_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRS_M_Unit" name="TRS_M_Unit">
				<option value="s"><?php _e('Secs');?>.</option>
				<option value="%">%</option>
				</select>
			</td>
			<!-- datos para TRM medium -->
			<td>
				<select id="dmanga_TRM_M_Tipo" name="TRM_M_Tipo"
					onChange="if(this.value==0) $('#dmanga_TRM_M_Unit').val('s');">
				<option value="0" selected="selected"><?php _e('Fixed MCT');?></option>
				<option value="1"><?php _e('SCT');?> + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRM_M_Factor" name="TRM_M_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRM_M_Unit" name="TRM_M_Unit">
				<option value="s" selected="selected"><?php _e('Secs');?>.</option>
				<option value="%">%</option>
				</select>
			</td>		
		</tr>
		<tr id="dmanga_SmallRow"> <!-- fila 5: recorrido std / mini / midi + datos mini -->
			<td id="dmanga_SmallLbl">Mini</td>
			<td><input type="text" id="dmanga_DistS" name="Dist_S" size="4" value="0" onChange="dmanga_setRecorridos();"/></td>
			<td><input type="text" id="dmanga_ObstS" name="Obst_S" size="4" value="0" onChange="dmanga_setRecorridos();"/></td>
			<!-- datos para TRS small -->
			<td colspan="2"> 
				<select id="dmanga_TRS_S_Tipo" name="TRS_S_Tipo"
					onChange="if(this.value==0) $('#dmanga_TRS_S_Unit').val('s');">
				<option value="0" selected="selected"><?php _e('Fixed SCT');?></option>
				<option value="1"><?php _e('Best result');?> + </option>
				<option value="2"><?php _e('3 best average');?> + </option>
				<option value="3"><?php _e('SCT Standard');?> + </option>
				<option value="4"><?php _e('SCT Medium');?> + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRS_S_Factor" name="TRS_S_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRS_S_Unit" name="TRS_S_Unit">
				<option value="s"><?php _e('Secs');?>.</option>
				<option value="%">%</option>
				</select>
			</td>
			<!-- datos para TRM small -->
			<td>
				<select id="dmanga_TRM_S_Tipo" name="TRM_S_Tipo"
					onChange="if(this.value==0) $('#dmanga_TRM_S_Unit').val('s');">
				<option value="0" selected="selected"><?php _e('Fixed MCT');?></option>
				<option value="1"><?php _e('SCT');?> + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRM_S_Factor" name="TRM_S_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRM_S_Unit" name="TRM_S_Unit">
				<option value="s" selected="selected"><?php _e('Secs');?>.</option>
				<option value="%">%</option>
				</select>
			</td>
		</tr>
		<tr> <!-- fila 6: observaciones -->
			<td colspan="2">Observaciones</td>
			<td colspan="8"><input type="text" id="dmanga_Observaciones" name="Observaciones" size="75" value=""/></td>
		</tr>
		<tr> <!-- fila 7: botones reset y save -->
            <td>
                <a href="#" class="easyui-linkbutton" data-options="iconCls:'icon-print'"
                   id="dmanga_Templates" onclick="print_commonDesarrollo(2);"><?php _e('Templates'); ?></a>
            </td>
            <td colspan="4">&nbsp;</td>
			<td>
				<a href="#" class="easyui-linkbutton" data-options="iconCls:'icon-reload'" 
					id="dmanga_Restaurar" onclick="reload_manga(workingData.manga);"><?php _e('Restore'); ?></a>
			</td>
			<td colspan="3">&nbsp;</td>
			<td>
				<a href="#" class="easyui-linkbutton" data-options="iconCls:'icon-save'" 
					id="dmanga_Guardar" onclick="save_manga(workingData.manga);"><?php _e('Save'); ?></a>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">

$('#dmanga_Juez1').combogrid({
	panelWidth: 400,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/juezFunctions.php',
	queryParams: {
		Operation: 'enumerate',
		Federation: workingData.federation
	},
	method: 'get',
	mode: 'remote',
	required: false,
	columns: [[
	    {field:'ID', hidden:true},
		{field:'Nombre',title:"<?php _e('Judge name'); ?>",width:70,align:'right'},
		{field:'Email',title:"<?php _e('E-mail'); ?>",width:50,align:'right'}
    ]],
	multiple: false,
	fitColumns: true,
	selectOnNavigation: false
});

$('#dmanga_Juez2').combogrid({
	panelWidth: 400,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/juezFunctions.php',
	queryParams: {
		Operation: 'enumerate',
		Federation: workingData.federation
	},
	method: 'get',
	mode: 'remote',
	required: false,
	columns: [[
	   	{field:'ID', hidden:true},
		{field:'Nombre',title:"<?php _e('Judge name'); ?>",width:70,align:'right'},
		{field:'Email',title:"<?php _e('E-mail'); ?>",width:50,align:'right'}
    ]],
	multiple: false,
	fitColumns: true,
	selectOnNavigation: false
});

$('#competicion-formdatosmanga').form({
	onLoadSuccess: function(data) { dmanga_setRecorridos(); },
	onLoadError: function() { alert("<?php _e('Error loading round information'); ?>"); }
});

//tooltips
addTooltip($('#dmanga_Juez1').combogrid('textbox'),'<?php _e("Main judge data"); ?>');
addTooltip($('#dmanga_Juez2').combogrid('textbox'),'<?php _e("Auxiliar/Practice judge data"); ?>');
addTooltip($('#dmanga_Recorrido_0'),'<?php _e("Same course for every categories"); ?>');
addTooltip($('#dmanga_Recorrido_1'),'<?php _e("Separate courses Standard and Midi/mini"); ?>');
addTooltip($('#dmanga_Recorrido_2'),'<?php _e("Independent courses for all categories"); ?>');
addTooltip($('#dmanga_Restaurar').linkbutton(),'<?php _e("Restore original round info from database"); ?>');
addTooltip($('#dmanga_Templates').linkbutton(),'<?php _e("Print template sheet for evaluate SCT"); ?>');
addTooltip($('#dmanga_Guardar').linkbutton(),'<?php _e("Save round technical data into database"); ?>');
addTooltip($('#dmanga_SameJuez').linkbutton(),'<?php _e("Clone judge information on every rounds for this journey"); ?>');

</script>