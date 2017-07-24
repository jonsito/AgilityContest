<!-- 
infomanga.inc

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__ . "/../../server/modules/Federations.php");
$config =Config::getInstance();
// retrieve federation info
$f=intval(http_request("Federation","i",0));
$fed=Federations::getFederation($f);
if (!$fed) die ("Internal error::Invalid Federation ID: $f");
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
				<label for="dmanga_Juez1"><span style="text-align:right"><?php _e('Judge'); ?> 1:</span></label>
				<select id="dmanga_Juez1" name="Juez1" style="width:200px"></select>
			</td>
			<td colspan="4">
				<label for="dmanga_Juez2"><span style="text-align:right"><?php _e('Judge'); ?> 2:</span></label>
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
				<label for="dmanga_Recorrido_0"><?php echo $fed->getRecorrido(0); ?></label>
			</td>
			<td colspan="3">
				<input type="radio" id="dmanga_Recorrido_1" name="Recorrido" value="1" onClick="dmanga_setRecorridos();"/>
				<label for="dmanga_Recorrido_1"><?php echo $fed->getRecorrido(1); ?></label>
			</td>
			<td colspan="3">
				<input type="radio" id="dmanga_Recorrido_2" name="Recorrido" value="0" onClick="dmanga_setRecorridos();"/>
				<label for="dmanga_Recorrido_2"><?php echo $fed->getRecorrido(2);  ?></label>
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
			<td id="dmanga_LargeLbl">Large</td>
			<td>
                <label for="dmanga_DistL"></label>
                <input type="text" id="dmanga_DistL" name="Dist_L" size="4" value="0"/>
            </td>
			<td>
                <label for="dmanga_ObstL"></label>
                <input type="text" id="dmanga_ObstL" name="Obst_L" size="4" value="0"/>
            </td>
			<!-- datos para TRS standard -->
			<td>
                <label for="dmanga_TRS_L_Tipo"></label>
				<select id="dmanga_TRS_L_Tipo" name="TRS_L_Tipo">
				<option value="0" selected="selected"><?php _e('Fixed SCT');?></option>
				<option value="1"><?php _e('Best result');?> + </option>
				<option value="2"><?php _e('3 best average');?> + </option>
				<option value="6"><?php _e('Velocity');?> </option>
				</select>
			</td>
			<td>
                <label for="dmanga_TRS_L_Factor"></label>
                <input type="text" id="dmanga_TRS_L_Factor" name="TRS_L_Factor" size="4" value="0"/>
            </td>
			<td>
                <label for="dmanga_TRS_L_Unit"></label>
				<select id="dmanga_TRS_L_Unit" name="TRS_L_Unit">
				<option value="s" selected="selected"><?php _e('Secs');?>.</option>
				<option value="%">%</option>
				<option value="m">m/s</option>
				</select>
			</td>
            <td>
                <input type="text" id="dmanga_TRS_L_TimeSpeed" name="TRS_L_TimeSpeed" readonly="readonly" disabled="disabled" size="5" value=""/>
            </td>
			<!-- datos para TRM standard -->
			<td>
                <label for="dmanga_TRM_L_Tipo"></label>
				<select id="dmanga_TRM_L_Tipo" name="TRM_L_Tipo">
				<option value="0" selected="selected"><?php _e('Fixed MCT');?></option>
				<option value="1"><?php _e('SCT');?> + </option>
				</select>
			</td>
			<td>
                <label for="dmanga_TRM_L_Factor"></label>
                <input type="text" id="dmanga_TRM_L_Factor" name="TRM_L_Factor" size="4" value="0"/>
            </td>
			<td>
                <label for="dmanga_TRM_L_Unit"></label>
				<select id="dmanga_TRM_L_Unit" name="TRM_L_Unit" >
				<option value="s" selected="selected"><?php _e('Secs');?>.</option>
				<option value="%">%</option>
				</select>
			</td>
		</tr>
		<tr id="dmanga_MediumRow"> <!-- fila 4: recorrido std / mini+midi datos midi -->
			<td id="dmanga_MediumLbl">Medium</td>
			<td>
                <label for="dmanga_DistM"></label>
                <input type="text" id="dmanga_DistM" name="Dist_M" size="4" value="0"/>
            </td>
			<td>
                <label for="dmanga_ObstM"></label>
                <input type="text" id="dmanga_ObstM" name="Obst_M" size="4" value="0"/>
            </td>
			<!-- datos para TRS medium -->
			<td>
                <label for="dmanga_TRS_M_Tipo"></label>
				<select id="dmanga_TRS_M_Tipo" name="TRS_M_Tipo">
				<option value="0" selected="selected"><?php _e('Fixed SCT');?></option>
				<option value="1"><?php _e('Best result');?> + </option>
				<option value="2"><?php _e('3 best average');?> + </option>
				<option value="3"><?php _e('SCT Standard');?> + </option>
				<option value="6"><?php _e('Velocity');?> </option>
				</select>
			</td>
			<td>
                <label for="dmanga_TRS_M_Factor"></label>
                <input type="text" id="dmanga_TRS_M_Factor" name="TRS_M_Factor" size="4" value="0"/>
            </td>
			<td>
                <label for="dmanga_TRS_M_Unit"></label>
				<select id="dmanga_TRS_M_Unit" name="TRS_M_Unit">
				<option value="s"><?php _e('Secs');?>.</option>
				<option value="%">%</option>
				<option value="m">m/s</option>
				</select>
			</td>
            <td>
                <input type="text" id="dmanga_TRS_M_TimeSpeed" name="TRS_M_TimeSpeed" readonly="readonly" disabled="disabled" size="5" value=""/>
            </td>
			<!-- datos para TRM medium -->
			<td>
                <label for="dmanga_TRM_M_Tipo"></label>
				<select id="dmanga_TRM_M_Tipo" name="TRM_M_Tipo">
				<option value="0" selected="selected"><?php _e('Fixed MCT');?></option>
				<option value="1"><?php _e('SCT');?> + </option>
				</select>
			</td>
			<td>
                <label for="dmanga_TRM_M_Factor"></label>
                <input type="text" id="dmanga_TRM_M_Factor" name="TRM_M_Factor" size="4" value="0"/>
            </td>
			<td>
                <label for="dmanga_TRM_M_Unit"></label>
				<select id="dmanga_TRM_M_Unit" name="TRM_M_Unit">
				<option value="s" selected="selected"><?php _e('Secs');?>.</option>
				<option value="%">%</option>
				</select>
			</td>		
		</tr>
		<tr id="dmanga_SmallRow"> <!-- fila 5: recorrido std / mini / midi + datos mini -->
			<td id="dmanga_SmallLbl">Small</td>
			<td>
                <label for="dmanga_DistS"></label>
                <input type="text" id="dmanga_DistS" name="Dist_S" size="4" value="0"/>
            </td>
			<td>
                <label for="dmanga_ObstS"></label>
                <input type="text" id="dmanga_ObstS" name="Obst_S" size="4" value="0"/>
            </td>
			<!-- datos para TRS small -->
			<td>
                <label for="dmanga_TRS_S_Tipo"></label>
				<select id="dmanga_TRS_S_Tipo" name="TRS_S_Tipo">
				<option value="0" selected="selected"><?php _e('Fixed SCT');?></option>
				<option value="1"><?php _e('Best result');?> + </option>
				<option value="2"><?php _e('3 best average');?> + </option>
				<option value="3"><?php _e('SCT Standard');?> + </option>
				<option value="4"><?php _e('SCT Medium');?> + </option>
				<option value="6"><?php _e('Velocity');?> </option>
				</select>
			</td>
			<td>
                <label for="dmanga_TRS_S_Factor"></label>
                <input type="text" id="dmanga_TRS_S_Factor" name="TRS_S_Factor" size="4" value="0"/>
            </td>
			<td>
                <label for="dmanga_TRS_S_Unit"></label>
				<select id="dmanga_TRS_S_Unit" name="TRS_S_Unit">
				<option value="s"><?php _e('Secs');?>.</option>
				<option value="%">%</option>
				<option value="m">m/s</option>
				</select>
			</td>
            <td>
                <input type="text" id="dmanga_TRS_S_TimeSpeed" name="TRS_S_TimeSpeed" readonly="readonly" disabled="disabled" size="5" value=""/>
            </td>
			<!-- datos para TRM small -->
			<td>
                <label for="dmanga_TRM_S_Tipo"></label>
				<select id="dmanga_TRM_S_Tipo" name="TRM_S_Tipo">
				<option value="0" selected="selected"><?php _e('Fixed MCT');?></option>
				<option value="1"><?php _e('SCT');?> + </option>
				</select>
			</td>
			<td>
                <label for="dmanga_TRM_S_Factor"></label>
                <input type="text" id="dmanga_TRM_S_Factor" name="TRM_S_Factor" size="4" value="0"/>
            </td>
			<td>
                <label for="dmanga_TRM_S_Unit"></label>
				<select id="dmanga_TRM_S_Unit" name="TRM_S_Unit">
				<option value="s" selected="selected"><?php _e('Secs');?>.</option>
				<option value="%">%</option>
				</select>
			</td>
		</tr>
	<?php if (intval($fed->get('Heights'))==4) { ?>
		<tr id="dmanga_TinyRow"> <!-- fila 6: recorrido std / mini / midi / tiny datos tiny -->
			<td id="dmanga_TinyLbl">Tiny</td>
			<td>
                <label for="dmanga_DistT"></label>
                <input type="text" id="dmanga_DistT" name="Dist_T" size="4" value="0"/>
            </td>
			<td>
                <label for="dmanga_ObstT"></label>
                <input type="text" id="dmanga_ObstT" name="Obst_T" size="4" value="0"/>
            </td>
			<!-- datos para TRS tiny -->
			<td>
                <label for="dmanga_TRS_T_Tipo"></label>
				<select id="dmanga_TRS_T_Tipo" name="TRS_T_Tipo">
				<option value="0" selected="selected"><?php _e('Fixed SCT'); ?></option>
				<option value="1"><?php _e('Best result'); ?> + </option>
				<option value="2"><?php _e('3 best average'); ?> + </option>
				<option value="3"><?php _e('SCT Standard'); ?> + </option>
                <option value="4"><?php _e('SCT Medium'); ?> + </option>
                <option value="5"><?php _e('SCT Small'); ?> + </option>
				<option value="6"><?php _e('Velocity'); ?> </option>
				</select>
			</td>
			<td>
                <label for="dmanga_TRS_T_Factor"></label>
                <input type="text" id="dmanga_TRS_T_Factor" name="TRS_T_Factor" size="4" value="0"/>
            </td>
			<td>
                <label for="dmanga_TRS_T_Unit"></label>
				<select id="dmanga_TRS_T_Unit" name="TRS_T_Unit">
				<option value="s"><?php _e('Secs'); ?>.</option>
				<option value="%">%</option>
				<option value="m">m/s</option>
				</select>
			</td>
            <td>
                <input type="text" id="dmanga_TRS_T_TimeSpeed" name="TRS_T_TimeSpeed" readonly="readonly" disabled="disabled" size="5" value=""/>
            </td>
			<!-- datos para TRM tiny -->
			<td>
                <label for="dmanga_TRM_T_Tipo"></label>
				<select id="dmanga_TRM_T_Tipo" name="TRM_T_Tipo">
				<option value="0" selected="selected"><?php _e('Fixed MCT'); ?></option>
				<option value="1"><?php _e('SCT'); ?> + </option>
				</select>
			</td>
			<td>
                <label for="dmanga_TRM_T_Factor"></label>
                <input type="text" id="dmanga_TRM_T_Factor" name="TRM_T_Factor" size="4" value="0"/>
            </td>
			<td>
                <label for="dmanga_TRM_T_Unit"></label>
				<select id="dmanga_TRM_T_Unit" name="TRM_T_Unit">
				<option value="s" selected="selected"><?php _e('Secs'); ?>.</option>
				<option value="%">%</option>
				</select>
			</td>
		</tr>
	<?php } ?>
		<tr> <!-- fila 7: observaciones -->
			<td colspan="2"><label for="dmanga_Observaciones"><?php _e('Comments'); ?></label></td>
			<td colspan="8"><input type="text" id="dmanga_Observaciones" name="Observaciones" size="75" value=""/></td>
		</tr>
		<tr> <!-- fila 7: botones reset y save -->
            <td>
                <a href="#" class="easyui-linkbutton" data-options="iconCls:'icon-print'"
                   id="dmanga_Templates" onclick="print_commonDesarrollo(3);"><?php _e('Templates'); ?></a>
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

<p>
	<span id="infomanga_readonly" class="blink" style="display:none;color:#ff0000;text-align:center;font-size:17px">
		<?php _e('Current user has NO WRITE PERMISSIONS');?>
	</span>
</p>
<script type="text/javascript">

    //stupid easyui that does not parse from markup
    $('#dmanga_DistL').textbox({onChange:function(n,o){dmanga_setRecorridos();}});
    $('#dmanga_DistM').textbox({onChange:function(n,o){dmanga_setRecorridos();}});
    $('#dmanga_DistS').textbox({onChange:function(n,o){dmanga_setRecorridos();}});
    $('#dmanga_DistT').textbox({onChange:function(n,o){dmanga_setRecorridos();}});
    $('#dmanga_ObstL').textbox({onChange:function(n,o){dmanga_setRecorridos();}});
    $('#dmanga_ObstM').textbox({onChange:function(n,o){dmanga_setRecorridos();}});
    $('#dmanga_ObstS').textbox({onChange:function(n,o){dmanga_setRecorridos();}});
    $('#dmanga_ObstT').textbox({onChange:function(n,o){dmanga_setRecorridos();}});
    $('#dmanga_TRS_L_Tipo').combobox({valueField:'value',panelHeight:'auto',onChange:function(n,o){round_setUnit(n,'#dmanga_TRS_L_Unit')}});
    $('#dmanga_TRS_M_Tipo').combobox({valueField:'value',panelHeight:'auto',onChange:function(n,o){round_setUnit(n,'#dmanga_TRS_M_Unit')}});
    $('#dmanga_TRS_S_Tipo').combobox({valueField:'value',panelHeight:'auto',onChange:function(n,o){round_setUnit(n,'#dmanga_TRS_S_Unit')}});
    $('#dmanga_TRS_T_Tipo').combobox({valueField:'value',panelHeight:'auto',onChange:function(n,o){round_setUnit(n,'#dmanga_TRS_T_Unit')}});
    $('#dmanga_TRS_L_Factor').textbox(); $('#dmanga_TRS_M_Factor').textbox();
    $('#dmanga_TRS_S_Factor').textbox(); $('#dmanga_TRS_T_Factor').textbox();
    $('#dmanga_TRS_L_Unit').combobox({valueField:'value',panelHeight:'auto'});
    $('#dmanga_TRS_M_Unit').combobox({valueField:'value',panelHeight:'auto'});
    $('#dmanga_TRS_S_Unit').combobox({valueField:'value',panelHeight:'auto'});
    $('#dmanga_TRS_T_Unit').combobox({valueField:'value',panelHeight:'auto'});
    $('#dmanga_TRS_L_TimeSpeed').textbox(); $('#dmanga_TRS_M_TimeSpeed').textbox();
    $('#dmanga_TRS_S_TimeSpeed').textbox(); $('#dmanga_TRS_T_TimeSpeed').textbox();
    $('#dmanga_TRM_L_Tipo').combobox({valueField:'value',panelHeight:'auto',onChange:function(n,o){round_setUnit(n,'#dmanga_TRM_L_Unit')}});
    $('#dmanga_TRM_M_Tipo').combobox({valueField:'value',panelHeight:'auto',onChange:function(n,o){round_setUnit(n,'#dmanga_TRM_M_Unit')}});
    $('#dmanga_TRM_S_Tipo').combobox({valueField:'value',panelHeight:'auto',onChange:function(n,o){round_setUnit(n,'#dmanga_TRM_S_Unit')}});
    $('#dmanga_TRM_T_Tipo').combobox({valueField:'value',panelHeight:'auto',onChange:function(n,o){round_setUnit(n,'#dmanga_TRM_T_Unit')}});
    $('#dmanga_TRM_L_Factor').textbox(); $('#dmanga_TRM_M_Factor').textbox();
    $('#dmanga_TRM_S_Factor').textbox(); $('#dmanga_TRM_T_Factor').textbox();
    $('#dmanga_TRM_L_Unit').combobox({valueField:'value',panelHeight:'auto'});
    $('#dmanga_TRM_M_Unit').combobox({valueField:'value',panelHeight:'auto'});
    $('#dmanga_TRM_S_Unit').combobox({valueField:'value',panelHeight:'auto'});
    $('#dmanga_TRM_T_Unit').combobox({valueField:'value',panelHeight:'auto'});
    $('#dmanga_Observaciones').textbox();

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
	onLoadSuccess: function(data) {
		// fix appearance according mode, federation, and so
		dmanga_setRecorridos();
	},
	onLoadError: function() { alert("<?php _e('Error loading round information'); ?>"); }
});

//tooltips
addTooltip($('#dmanga_Juez1').combogrid('textbox'),'<?php _e("Main judge data"); ?>');
addTooltip($('#dmanga_Juez2').combogrid('textbox'),'<?php _e("Auxiliar/Practice judge data"); ?>');
addTooltip($('#dmanga_Recorrido_0'),'<?php _e("Same course for every categories"); ?>');
<?php if (intval($fed->get('Heights'))==3) {?>
addTooltip($('#dmanga_Recorrido_1'),'<?php _e("Separate courses Standard and Midi/mini"); ?>');
<?php } else { ?>
addTooltip($('#dmanga_Recorrido_1'),'<?php _e("Separate courses Standard/Medium and Small/Tiny"); ?>');
<?php } ?>
addTooltip($('#dmanga_Recorrido_2'),'<?php _e("Independent courses for all categories"); ?>');
addTooltip($('#dmanga_Restaurar').linkbutton(),'<?php _e("Restore original round info from database"); ?>');
addTooltip($('#dmanga_Templates').linkbutton(),'<?php _e("Open print form selection dialog"); ?>');
addTooltip($('#dmanga_Guardar').linkbutton(),'<?php _e("Save round technical data into database"); ?>');
addTooltip($('#dmanga_SameJuez').linkbutton(),'<?php _e("Clone judge information on every rounds for this journey"); ?>');

// if user has no write permission, show proper message info
// TODO: force reload on logout session
$('#infomanga_readonly').css('display',(check_softLevel(access_level.PERMS_OPERATOR,null))?'none':'inline-block');
</script>