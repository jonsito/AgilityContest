<!-- Formulario que contiene los datos de una manga -->

<form id="competicion-formdatosmanga">
	<input type="hidden" id="dmanga_Manga" name="Manga"/>
	<table id="competicion-tabladatosmanga">
		<tr> <!-- fila 0: datos de los jueces -->
			<td>Juez 1:</td>
			<td colspan="4"><input id="dmanga_Juez1" type="text" name="Juez1"></td>
			<td>Juez 2:</td>
			<td colspan="4"><input id="dmanga_Juez2" type="text" name="Juez2"></td>
		</tr>
		<tr> <!-- fila 1 tipos de recorrido -->
			<td colspan="2">Recorridos: </td>
			<td colspan="3">
				<input type="radio" id="dmanga_Recorrido_0" name="Recorrido" value="0" onClick="setRecorridos();"/>
				<label for="dmanga_Recorrido_0">Recorrido com&uacute;n</label>
			</td>
			<td colspan="3">
				<input type="radio" id="dmanga_Recorrido_1" name="Recorrido" value="1" onClick="setRecorridos();"/>
				<label for="dmanga_Recorrido_1">Std / Mini-Midi</label>
			</td>
			<td colspan="3">
				<input type="radio" id="dmanga_Recorrido_2" name="Recorrido" value="2" onClick="setRecorridos();"/>
				<label for="dmanga_Recorrido_2">Std / Midi / Mini</label>
			</td>
		</tr>
		<tr style="background-color:#c0c0c0"> <!-- fila 2: titulos  -->
			<td>Categor&iacute;a</td>
			<td>Distancia</td>
			<td>Obst&aacute;culos</td>
			<td colspan="4">Tiempo de recorrido Standard</td>
			<td colspan="3">Tiempo de recorrido M&aacute;ximo</td>
		</tr>
		<tr> <!-- fila 3: recorrido comun datos standard -->
			<td>Standard</td>
			<td><input type="text" id="dmanga_DistL" name="DistanciaL" size="4" value="0" onChange="setRecorridos();"/></td>
			<td><input type="text" id="dmanga_ObstL" name="ObstaculosL" size="4" value="0" onChange="setRecorridos();"/></td>
			<!-- datos para TRS standard -->
			<td colspan="2"> 
				<select id="dmanga_TRS_L_Tipo" name="TRS_L_Tipo">
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
				<select id="dmanga_TRM_L_Tipo" name="TRS_M_Tipo">
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
		<tr> <!-- fila 4: recorrido std / mini+midi datos midi -->
			<td>Medium</td>
			<td><input type="text" id="dmanga_DistM" name="DistanciaM" size="4" value="0" onChange="setRecorridos();"/></td>
			<td><input type="text" id="dmanga_ObstM" name="ObstaculosM" size="4" value="0" onChange="setRecorridos();"/></td>
			<!-- datos para TRS medium -->
			<td colspan="2"> 
				<select id="dmanga_TRS_M_Tipo" name="TRS_M_Tipo">
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
				<select id="dmanga_TRM_M_Tipo" name="TRS_M_Tipo">
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
		<tr> <!-- fila 5: recorrido std / mini / midi + datos mini -->
			<td>Small</td>
			<td><input type="text" id="dmanga_DistS" name="DistanciaS" size="4" value="0" onChange="setRecorridos();"/></td>
			<td><input type="text" id="dmanga_ObstS" name="ObstaculosS" size="4" value="0" onChange="setRecorridos();"/></td>
			<!-- datos para TRS small -->
			<td colspan="2"> 
				<select id="dmanga_TRS_S_Tipo" name="TRS_S_Tipo">
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
				<select id="dmanga_TRM_S_Tipo" name="TRS_S_Tipo">
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
		<tr> <!-- fila 6: observaciones -->
			<td colspan="2">Observaciones</td>
			<td colspan="11"><input type="text" id="dmanga_Observaciones" name="Observaciones" size="70" value=""/></td>
		</tr>
		<tr> <!-- fila 7: manga cerrada. botones reset y save -->
			<td colspan="2">
				<label for="dmanga_Cerrada">Cerrar manga</label>
				<input type="checkbox" id="dmanga_Cerrada" name="Cerrada" value="1">
			</td>
			<td colspan="3">&nbsp;</td>
			<td>
				<input type="button" id="dmanga_Restaurar" name="Restaurar" value="Restaurar">
			</td>
			<td colspan="2">&nbsp;</td>
			<td>
				<input type="button" id="dmanga_Guardar" name="Guardar" value="Guardar">
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">

function setRecorridos() {
	var val=$("input:radio[name=Recorrido]:checked").val(); 
	switch (val) {
	case '0':
		var distl=$('#dmanga_DistL').val();
		var obstl=$('#dmanga_ObstL').val();
		$('#dmanga_DistM').attr('disabled',true);
		$('#dmanga_DistM').val(distl);
		$('#dmanga_ObstM').attr('disabled',true);
		$('#dmanga_ObstM').val(obstl);
		$('#dmanga_DistS').attr('disabled',true);
		$('#dmanga_DistS').val(distl);
		$('#dmanga_ObstS').attr('disabled',true);
		$('#dmanga_ObstS').val(obstl);
		break;
	case '1':
		var distm=$('#dmanga_DistM').val();
		var obstm=$('#dmanga_ObstM').val();
		$('#dmanga_DistM').removeAttr('disabled');
		$('#dmanga_ObstM').removeAttr('disabled');
		$('#dmanga_DistS').attr('disabled',true);
		$('#dmanga_DistS').val(distm);
		$('#dmanga_ObstS').attr('disabled',true);
		$('#dmanga_ObstS').val(obstm);
		break;
	case '2':
		$('#dmanga_DistM').removeAttr('disabled');
		$('#dmanga_ObstM').removeAttr('disabled');
		$('#dmanga_DistS').removeAttr('disabled');
		$('#dmanga_ObstS').removeAttr('disabled');
		break;
	}
}
</script>