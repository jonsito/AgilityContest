<!-- 
infomanga_rfec.inc

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
				<span style="text-align:right">Juez 1:</span>
				<select id="dmanga_Juez1" name="Juez1" style="width:200px"></select>
			</td>
			<td colspan="4">
				<span style="text-align:right">Juez 2:</span>
				<select id="dmanga_Juez2" name="Juez2" style="width:200px"></select>
			</td>
			<td>&nbsp;</td>
			<td colspan="1">
				<a href="#" class="easyui-linkbutton" data-options="iconCls:'icon-whistle'" 
					id="dmanga_SameJuez" onclick="dmanga_shareJuez();">Replicar</a>
			</td>
		</tr>
		<tr>
			<td colspan="10">&nbsp;</td>
		</tr>
		<tr> <!-- fila 1 tipos de recorrido -->
			<td>Recorridos: </td>
			<td colspan="3">
				<input type="radio" id="dmanga_Recorrido_0" name="Recorrido" value="2" onClick="dmanga_setRecorridos_rfec();"/>
				<label for="dmanga_Recorrido_0">Recorrido com&uacute;n</label>
			</td>
			<td colspan="3">
				<input type="radio" id="dmanga_Recorrido_1" name="Recorrido" value="1" onClick="dmanga_setRecorridos_rfec();"/>
				<label for="dmanga_Recorrido_1">Large+Medium / Small+Tiny</label>
			</td>
			<td colspan="3">
				<input type="radio" id="dmanga_Recorrido_2" name="Recorrido" value="0" onClick="dmanga_setRecorridos_rfec();"/>
				<label for="dmanga_Recorrido_2">Large / Medium / Small / Tiny </label>
			</td>
		</tr>
		<tr>
			<td colspan="10">&nbsp;</td>
		</tr>
		<tr style="background-color:#c0c0c0"> <!-- fila 2: titulos  -->
			<td>Categor&iacute;a</td>
			<td>Distancia</td>
			<td>Obst&aacute;culos</td>
			<td colspan="4">Tiempo de recorrido Standard</td>
			<td colspan="3">Tiempo de recorrido M&aacute;ximo</td>
		</tr>
		<tr id="dmanga_LargeRow"> <!-- fila 3: recorrido comun datos standard -->
			<td id="dmanga_LargeLbl">Large</td>
			<td><input type="text" id="dmanga_DistL" name="Dist_L" size="4" value="0" onChange="dmanga_setRecorridos_rfec();"/></td>
			<td><input type="text" id="dmanga_ObstL" name="Obst_L" size="4" value="0" onChange="dmanga_setRecorridos_rfec();"/></td>
			<!-- datos para TRS standard -->
			<td colspan="2"> 
				<select id="dmanga_TRS_L_Tipo" name="TRS_L_Tipo"
					onChange="if(this.value==0) $('#dmanga_TRS_L_Unit').val('s');">
				<option value="0" selected="selected">TRS Fijo</option>
				<option value="1">Mejor recorrido + </option>
				<option value="2">Media 3 mejores + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRS_L_Factor" name="TRS_L_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRS_L_Unit" name="TRS_L_Unit">
				<option value="s" selected="selected">Segs.</option>
				<option value="%">%</option>
				</select>
			</td>
			<!-- datos para TRM standard -->
			<td>
				<select id="dmanga_TRM_L_Tipo" name="TRM_L_Tipo"
					onChange="if(this.value==0) $('#dmanga_TRM_L_Unit').val('s');">
				<option value="0" selected="selected">TRM Fijo</option>
				<option value="1">TRS + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRM_L_Factor" name="TRM_L_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRM_L_Unit" name="TRM_L_Unit">
				<option value="s" selected="selected">Segs.</option>
				<option value="%">%</option>
				</select>
			</td>
		</tr>
		<tr id="dmanga_MediumRow"> <!-- fila 4: recorrido std / mini+midi datos midi -->
			<td id="dmanga_MediumLbl">Medium</td>
			<td><input type="text" id="dmanga_DistM" name="Dist_M" size="4" value="0" onChange="dmanga_setRecorridos_rfec();"/></td>
			<td><input type="text" id="dmanga_ObstM" name="Obst_M" size="4" value="0" onChange="dmanga_setRecorridos_rfec();"/></td>
			<!-- datos para TRS medium -->
			<td colspan="2"> 
				<select id="dmanga_TRS_M_Tipo" name="TRS_M_Tipo"
					onChange="if(this.value==0) $('#dmanga_TRS_M_Unit').val('s');">
				<option value="0" selected="selected">TRS Fijo</option>
				<option value="1">Mejor recorrido + </option>
				<option value="2">Media 3 mejores + </option>
				<option value="3">TRS Standard + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRS_M_Factor" name="TRS_M_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRS_M_Unit" name="TRS_M_Unit">
				<option value="s">Segs.</option>
				<option value="%">%</option>
				</select>
			</td>
			<!-- datos para TRM medium -->
			<td>
				<select id="dmanga_TRM_M_Tipo" name="TRM_M_Tipo"
					onChange="if(this.value==0) $('#dmanga_TRM_M_Unit').val('s');">
				<option value="0" selected="selected">TRM Fijo</option>
				<option value="1">TRS + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRM_M_Factor" name="TRM_M_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRM_M_Unit" name="TRM_M_Unit">
				<option value="s" selected="selected">Segs.</option>
				<option value="%">%</option>
				</select>
			</td>		
		</tr>
		<tr id="dmanga_SmallRow"> <!-- fila 5: recorrido std / mini / midi + datos mini -->
			<td id="dmanga_SmallLbl">Small</td>
			<td><input type="text" id="dmanga_DistS" name="Dist_S" size="4" value="0" onChange="dmanga_setRecorridos_rfec();"/></td>
			<td><input type="text" id="dmanga_ObstS" name="Obst_S" size="4" value="0" onChange="dmanga_setRecorridos_rfec();"/></td>
			<!-- datos para TRS small -->
			<td colspan="2"> 
				<select id="dmanga_TRS_S_Tipo" name="TRS_S_Tipo"
					onChange="if(this.value==0) $('#dmanga_TRS_S_Unit').val('s');">
				<option value="0" selected="selected">TRS Fijo</option>
				<option value="1">Mejor recorrido + </option>
				<option value="2">Media 3 mejores + </option>
				<option value="3">TRS Standard + </option>
				<option value="4">TRS Medium + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRS_S_Factor" name="TRS_S_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRS_S_Unit" name="TRS_S_Unit">
				<option value="s">Segs.</option>
				<option value="%">%</option>
				</select>
			</td>
			<!-- datos para TRM small -->
			<td>
				<select id="dmanga_TRM_S_Tipo" name="TRM_S_Tipo"
					onChange="if(this.value==0) $('#dmanga_TRM_S_Unit').val('s');">
				<option value="0" selected="selected">TRM Fijo</option>
				<option value="1">TRS + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRM_S_Factor" name="TRM_S_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRM_S_Unit" name="TRM_S_Unit">
				<option value="s" selected="selected">Segs.</option>
				<option value="%">%</option>
				</select>
			</td>
		</tr>
		<tr id="dmanga_TinyRow"> <!-- fila 6: recorrido std / mini / midi / tiny datos tiny -->
			<td id="dmanga_TinyLbl">Tiny</td>
			<td><input type="text" id="dmanga_DistT" name="Dist_T" size="4" value="0" onChange="dmanga_setRecorridos_rfec();"/></td>
			<td><input type="text" id="dmanga_ObstT" name="Obst_T" size="4" value="0" onChange="dmanga_setRecorridos_rfec();"/></td>
			<!-- datos para TRS small -->
			<td colspan="2"> 
				<select id="dmanga_TRS_T_Tipo" name="TRS_T_Tipo"
					onChange="if(this.value==0) $('#dmanga_TRS_T_Unit').val('s');">
				<option value="0" selected="selected">TRS Fijo</option>
				<option value="1">Mejor recorrido + </option>
				<option value="2">Media 3 mejores + </option>
				<option value="3">TRS Standard + </option>
				<option value="4">TRS Medium + </option>
				<option value="5">TRS Small + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRS_T_Factor" name="TRS_T_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRS_T_Unit" name="TRS_T_Unit">
				<option value="s">Segs.</option>
				<option value="%">%</option>
				</select>
			</td>
			<!-- datos para TRM tiny -->
			<td>
				<select id="dmanga_TRM_T_Tipo" name="TRM_T_Tipo"
					onChange="if(this.value==0) $('#dmanga_TRM_T_Unit').val('s');">
				<option value="0" selected="selected">TRM Fijo</option>
				<option value="1">TRS + </option>
				</select>
			</td>
			<td><input type="text" id="dmanga_TRM_T_Factor" name="TRM_T_Factor" size="4" value="0"/></td>
			<td>
				<select id="dmanga_TRM_T_Unit" name="TRM_T_Unit">
				<option value="s" selected="selected">Segs.</option>
				<option value="%">%</option>
				</select>
			</td>
		</tr>
		<tr> <!-- fila 7: observaciones -->
			<td colspan="2">Observaciones</td>
			<td colspan="8"><input type="text" id="dmanga_Observaciones" name="Observaciones" size="75" value=""/></td>
		</tr>
		<tr> <!-- fila 7: botones reset y save -->
            <td>
                <a href="#" class="easyui-linkbutton" data-options="iconCls:'icon-print'"
                   id="dmanga_Templates" onclick="print_commonDesarrollo(2);">Plantillas</a>
            </td>
			<td colspan="4">&nbsp;</td>
			<td>
				<a href="#" class="easyui-linkbutton" data-options="iconCls:'icon-reload'" 
					id="dmanga_Restaurar" onclick="reload_manga(workingData.manga);">Restaurar</a>
			</td>
			<td colspan="3">&nbsp;</td>
			<td>
				<a href="#" class="easyui-linkbutton" data-options="iconCls:'icon-save'" 
					id="dmanga_Guardar" onclick="save_manga(workingData.manga);">Guardar</a>
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
	url: '/agility/server/database/juezFunctions.php?Operation=enumerate&Federation=1',
	method: 'get',
	mode: 'remote',
	required: false,
	columns: [[
	    {field:'ID', hidden:true},
		{field:'Nombre',title:'Nombre del juez',width:70,align:'right'},
		{field:'Email',title:'E-mail',width:50,align:'right'}
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
	url: '/agility/server/database/juezFunctions.php?Operation=enumerate&Federation=1',
	method: 'get',
	mode: 'remote',
	required: false,
	columns: [[
	   	{field:'ID', hidden:true},
		{field:'Nombre',title:'Nombre del juez',width:70,align:'right'},
		{field:'Email',title:'E-mail',width:50,align:'right'}
    ]],
	multiple: false,
	fitColumns: true,
	selectOnNavigation: false
});

$('#competicion-formdatosmanga').form({
	onLoadSuccess: function(data) { dmanga_setRecorridos_rfec(); },
	onLoadError: function() { alert('error en carga de datos de manga');}
});

//tooltips
addTooltip($('#dmanga_Juez1').combogrid('textbox'),"Datos del juez titular");
addTooltip($('#dmanga_Juez2').combogrid('textbox'),"Datos del juez auxiliar/pr&aacute;cticas");
addTooltip($('#dmanga_Recorrido_0'),"Recorrido &uacute;nico para las cuatro categor&iacute;as");
addTooltip($('#dmanga_Recorrido_1'),"Recorridos separados para Standard/Medium y Small/Tiny");
addTooltip($('#dmanga_Recorrido_2'),"Recorridos independientes para cada cagegor&iacute;a");
addTooltip($('#dmanga_Restaurar').linkbutton(),"Restaurar datos originales de la manga");
addTooltip($('#dmanga_Templates').linkbutton(),"Imprimir plantillas de calculo/datos del TRS");
addTooltip($('#dmanga_Guardar').linkbutton(),"Guardar los datos t&eacute;cnicos de la manga");
addTooltip($('#dmanga_SameJuez').linkbutton(),"Asignar estos jueces a TODAS las mangas de la jornada");

</script>