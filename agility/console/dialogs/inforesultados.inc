<!--
inforesultados.inc

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
require_once(__DIR__."/../../server/auth/Config.php");
require_once(__DIR__."/../../server/tools.php");
$config =Config::getInstance();
?>

<!-- sub Formulario de la ventana de resultados que contiene la informacion tecnica de las mangas -->
<?php for ($nmanga=1;$nmanga<9;$nmanga++) { ?>
<!-- TABLA DE DATOS DE TRS Y TRM DE LA MANGA <?php echo $nmanga; ?>-->
<div id="datos_manga<?php echo $nmanga; ?>-trs">
	<form id="datos_manga<?php echo $nmanga; ?>-trs-form" method="get">
		<table style="float:left;width:100%">
			<tr id="datos_manga<?php echo $nmanga; ?>-InfoRow">
				<td colspan="3">
					<span style="font-weight:bold"><?php _e('Round');?> <?php echo $nmanga; ?>:</span>
					<input type="text" size="20" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_Nombre">
				</td>
				<td colspan="3">
					<span style="font-weight:bold"><?php _e('Judges');?>: </span>
					<input type="text" size="15" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_Juez1"> &nbsp;
					<input type="text" size="15" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_Juez2">
				</td>
			</tr>
			<tr id="datos_manga<?php echo $nmanga; ?>-LargeRow">
				<th id="datos_manga<?php echo $nmanga; ?>-LargeLbl">Large</th>
				<td><?php _e('Distance');?>: <input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_DIST_L"></td>
				<td><?php _e('Obstacles');?>: <input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_OBST_L"></td>
				<td><?php _e('SCT');?>:<input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_TRS_L"></td>
				<td><?php _e('MCT');?>:<input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_TRM_L"></td>
				<td><?php _e('Vel');?>. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_VEL_L"></td>
			</tr>
			<tr id="datos_manga<?php echo $nmanga; ?>-MediumRow">
				<th id="datos_manga<?php echo $nmanga; ?>-MediumLbl">Medium</th>
				<td><?php _e('Distance');?>: <input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_DIST_M"></td>
				<td><?php _e('Obstacles');?>: <input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_OBST_M"></td>
				<td><?php _e('SCT');?>:<input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_TRS_M"></td>
				<td><?php _e('MCT');?>:<input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_TRM_M"></td>
				<td><?php _e('Vel');?>. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_VEL_M"></td>
			</tr>
			<tr id="datos_manga<?php echo $nmanga; ?>-SmallRow">
				<th id="datos_manga<?php echo $nmanga; ?>-SmallLbl">Small</th>
				<td><?php _e('Distance');?>: <input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_DIST_S"></td>
				<td><?php _e('Obstacles');?>: <input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_OBST_S"></td>
				<td><?php _e('SCT');?>:<input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_TRS_S"></td>
				<td><?php _e('MCT');?>:<input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_TRM_S"></td>
				<td><?php _e('Vel');?>. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_VEL_S"></td>
			</tr>
			<tr id="datos_manga<?php echo $nmanga; ?>-TinyRow">
				<th id="datos_manga<?php echo $nmanga; ?>-TinyLbl">Tiny</th>
				<td><?php _e('Distance');?>: <input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_DIST_T"></td>
				<td><?php _e('Obstacles');?>: <input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_OBST_T"></td>
				<td><?php _e('SCT');?>:<input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_TRS_T"></td>
				<td><?php _e('MCT');?>:<input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_TRM_T"></td>
				<td><?php _e('Vel');?>. <input type="text" size="4" class="result_forms" readonly="readonly" id="dm<?php echo $nmanga; ?>_VEL_T"></td>
			</tr>
			<tr id="datos_manga<?php echo $nmanga; ?>-Separator"><td colspan="7"><hr /></td></tr>
		</table>
	</form>
</div>
<?php } ?>