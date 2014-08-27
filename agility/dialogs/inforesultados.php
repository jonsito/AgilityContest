<!-- sub Formulario de la ventana de resultados que contiene la informacion tecnica de las mangas -->

<!-- TABLA DE DATOS DE TRS Y TRM DE LA PRIMERA MANGA -->
<div id="datos_manga1-trs">
	<form id="datos_manga1-trs-form" method="get">
		<table style="float:left;" width="100%">
			<tr id="datos_manga1-InfoRow">
				<td colspan="3">
					<span style="font-weight:bold">Manga 1:</span>
					<input type="text" size="20" class="result_forms" readonly="readonly" id="dm1_Nombre">
				</td>
				<td colspan="4">
					<span style="font-weight:bold">Juez:</span>
					<input type="text" size="20" class="result_forms" readonly="readonly" id="dm1_Juez1"> &nbsp;
					<input type="text" size="20" class="result_forms" readonly="readonly" id="dm1_Juez2">
				</td>
			</tr>
			<tr id="datos_manga1-LargeRow">
				<td><input type="radio" name="rRecorrido" id="datos_manga1-LargeBtn" value="0" onclick="reloadClasificaciones(1,0)"></td>
				<th id="datos_manga1-LargeLbl">Large</th>
				<td>Distancia <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_DIST_L"></td>
				<td>Obst&aacute;culos <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_OBST_L"></td>
				<td>TRS <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_TRS_L"></td>
				<td>TRM <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_TRM_L"></td>
				<td>Vel. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_VEL_L"></td>
			</tr>
			<tr id="datos_manga1-MediumRow">
				<td><input type="radio" name="rRecorrido" id="datos_manga1-LargeBtn" value="1" onclick="reloadClasificaciones(1,1)"></td>
				<th id="datos_manga1-MediumLbl">Medium</th>
				<td>Distancia <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_DIST_M"></td>
				<td>Obst&aacute;culos <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_OBST_M"></td>
				<td>TRS <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_TRS_M"></td>
				<td>TRM <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_TRM_M"></td>
				<td>Vel. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_VEL_M"></td>
			</tr>
			<tr id="datos_manga1-SmallRow">
				<td><input type="radio" name="rRecorrido" id="datos_manga1-LargeBtn" value="2" onclick="reloadClasificaciones(1,2)"></td>
				<th id="datos_manga1-SmallLbl">Small</th>
				<td>Distancia <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_DIST_S"></td>
				<td>Obst&aacute;culos <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_OBST_S"></td>
				<td>TRS <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_TRS_S"></td>
				<td>TRM <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_TRM_S"></td>
				<td>Vel. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_VEL_S"></td>
			</tr>
			<tr><td colspan="7"><hr /></td></tr>
		</table>
	</form>
</div>
<!-- TABLA DE DATOS DE TRS Y TRM DE LA SEGUNDA MANGA -->
<div id="datos_manga2-trs">
	<form id="datos_manga2-trs-form" method="get">
		<table style="float:left;" width="100%">
			<tr id="datos_manga2-InfoRow">
				<td colspan="3">
					<span style="font-weight:bold">Manga 2:</span>
					<input type="text" size="20" class="result_forms" readonly="readonly" id="dm2_Nombre">
				</td>
				<td colspan="4">
					<span style="font-weight:bold">Juez:</span>
					<input type="text" size="20" class="result_forms" readonly="readonly" id="dm2_Juez1"> &nbsp;
					<input type="text" size="20" class="result_forms" readonly="readonly" id="dm2_Juez2">
				</td>
			</tr>
			<tr id="datos_manga2-LargeRow">
				<td><input type="radio" name="rRecorrido" id="datos_manga2-LargeBtn" value="0" onclick="reloadClasificaciones(2,0)"></td>
				<th id="datos_manga2-LargeLbl">Large</th>
				<td>Distancia <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_DIST_L"></td>
				<td>Obst&aacute;culos <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_OBST_L"></td>
				<td>TRS <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_TRS_L"></td>
				<td>TRM <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_TRM_L"></td>
				<td>Vel. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_VEL_L"></td>
			</tr>
			<tr id="datos_manga2-MediumRow">
				<td><input type="radio" name="rRecorrido" id="datos_manga2-LargeBtn" value="2" onclick="reloadClasificaciones(2,1)"></td>
				<th id="datos_manga2-MediumLbl">Medium</th>
				<td>Distancia <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_DIST_M"></td>
				<td>Obst&aacute;culos <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_OBST_M"></td>
				<td>TRS <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_TRS_M"></td>
				<td>TRM <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_TRM_M"></td>
				<td>Vel. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_VEL_M"></td>
			</tr>
			<tr id="datos_manga2-SmallRow">
				<td><input type="radio" name="rRecorrido" id="datos_manga2-LargeBtn" value="2" onclick="reloadClasificaciones(2,2)"></td>
				<th id="datos_manga2-SmallLbl">Small</th>
				<td>Distancia <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_DIST_S"></td>
				<td>Obst&aacute;culos <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_OBST_S"></td>
				<td>TRS <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_TRS_S"></td>
				<td>TRM <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_TRM_S"></td>
				<td>Vel. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_VEL_S"></td>
			</tr>
		</table>
	</form>
</div>