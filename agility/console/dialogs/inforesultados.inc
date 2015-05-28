<!--
inforesultados.inc

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
require_once(__DIR__."/../../server/auth/Config.php");
require_once(__DIR__."/../../server/tools.php");
$config =Config::getInstance();
?>

<!-- sub Formulario de la ventana de resultados que contiene la informacion tecnica de las mangas -->

<!-- TABLA DE DATOS DE TRS Y TRM DE LA PRIMERA MANGA -->
<div id="datos_manga1-trs">
	<form id="datos_manga1-trs-form" method="get">
		<table style="float:left;width:100%">
			<tr id="datos_manga1-InfoRow">
				<td colspan="3">
					<span style="font-weight:bold">Manga 1:</span>
					<input type="text" size="20" class="result_forms" readonly="readonly" id="dm1_Nombre">
				</td>
				<td colspan="3">
					<span style="font-weight:bold">Jueces:</span>
					<input type="text" size="15" class="result_forms" readonly="readonly" id="dm1_Juez1"> &nbsp;
					<input type="text" size="15" class="result_forms" readonly="readonly" id="dm1_Juez2">
				</td>
			</tr>
			<tr id="datos_manga1-LargeRow">
				<th id="datos_manga1-LargeLbl">Large</th>
				<td>Distancia <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_DIST_L"></td>
				<td>Obst&aacute;culos <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_OBST_L"></td>
				<td>TRS <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_TRS_L"></td>
				<td>TRM <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_TRM_L"></td>
				<td>Vel. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_VEL_L"></td>
			</tr>
			<tr id="datos_manga1-MediumRow">
				<th id="datos_manga1-MediumLbl">Medium</th>
				<td>Distancia <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_DIST_M"></td>
				<td>Obst&aacute;culos <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_OBST_M"></td>
				<td>TRS <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_TRS_M"></td>
				<td>TRM <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_TRM_M"></td>
				<td>Vel. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_VEL_M"></td>
			</tr>
			<tr id="datos_manga1-SmallRow">
				<th id="datos_manga1-SmallLbl">Small</th>
				<td>Distancia <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_DIST_S"></td>
				<td>Obst&aacute;culos <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_OBST_S"></td>
				<td>TRS <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_TRS_S"></td>
				<td>TRM <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_TRM_S"></td>
				<td>Vel. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_VEL_S"></td>
			</tr>
			<tr id="datos_manga1-TinyRow">
				<th id="datos_manga1-TinyLbl">Tiny</th>
				<td>Distancia <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_DIST_T"></td>
				<td>Obst&aacute;culos <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_OBST_T"></td>
				<td>TRS <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_TRS_T"></td>
				<td>TRM <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_TRM_T"></td>
				<td>Vel. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm1_VEL_T"></td>
			</tr>
			<tr><td colspan="7"><hr /></td></tr>
		</table>
	</form>
</div>
<!-- TABLA DE DATOS DE TRS Y TRM DE LA SEGUNDA MANGA -->
<div id="datos_manga2-trs">
	<form id="datos_manga2-trs-form" method="get">
		<table style="float:left;width:100%">
			<tr id="datos_manga2-InfoRow">
				<td colspan="3">
					<span style="font-weight:bold">Manga 2:</span>
					<input type="text" size="20" class="result_forms" readonly="readonly" id="dm2_Nombre">
				</td>
				<td colspan="3">
					<span style="font-weight:bold">Jueces:</span>
					<input type="text" size="15" class="result_forms" readonly="readonly" id="dm2_Juez1"> &nbsp;
					<input type="text" size="15" class="result_forms" readonly="readonly" id="dm2_Juez2">
				</td>
			</tr>
			<tr id="datos_manga2-LargeRow">
				<th id="datos_manga2-LargeLbl">Large</th>
				<td>Distancia <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_DIST_L"></td>
				<td>Obst&aacute;culos <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_OBST_L"></td>
				<td>TRS <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_TRS_L"></td>
				<td>TRM <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_TRM_L"></td>
				<td>Vel. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_VEL_L"></td>
			</tr>
			<tr id="datos_manga2-MediumRow">
				<th id="datos_manga2-MediumLbl">Medium</th>
				<td>Distancia <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_DIST_M"></td>
				<td>Obst&aacute;culos <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_OBST_M"></td>
				<td>TRS <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_TRS_M"></td>
				<td>TRM <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_TRM_M"></td>
				<td>Vel. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_VEL_M"></td>
			</tr>
			<tr id="datos_manga2-SmallRow">
				<th id="datos_manga2-SmallLbl">Small</th>
				<td>Distancia <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_DIST_S"></td>
				<td>Obst&aacute;culos <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_OBST_S"></td>
				<td>TRS <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_TRS_S"></td>
				<td>TRM <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_TRM_S"></td>
				<td>Vel. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_VEL_S"></td>
			</tr>
			<tr id="datos_manga2-TinyRow">
				<th id="datos_manga2-TinyLbl">Tiny</th>
				<td>Distancia <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_DIST_T"></td>
				<td>Obst&aacute;culos <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_OBST_T"></td>
				<td>TRS <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_TRS_T"></td>
				<td>TRM <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_TRM_T"></td>
				<td>Vel. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm2_VEL_T"></td>
			</tr>
		</table>
	</form>
</div>