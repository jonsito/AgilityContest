<!-- 
dlg_resultadosIndividual.inc

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
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<!--  dialogo de presentacion de resultados parciales de una manga -->
<div id="resultadosmanga-dialog" style="width:960px;height:450px;padding:0 10px 100px 10px">

	<!-- TABLA DE DATOS DE TRS Y TRM DE LA MANGA -->
	<div id="resultadosmanga-trs">
		<form id="resultadosmanga-trs-form" method="get">
			<table width="100%">
				<tr><th colspan="6"><?php _e('Technical data on selected round'); ?>:</th></tr>
				<tr id="resultadosmanga-LargeRow">
					<td><input type="radio" name="rRecorrido" id="resultadosmanga-LargeBtn" value="0" onclick="consoleReloadParcial(0,true)"></td>
					<th id="resultadosmanga-LargeLbl" style="width:150px">Large</th>
					<td><?php _e('Distance'); ?> <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_DIST_L"></td>
					<td><?php _e('Obstacles'); ?> <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_OBST_L"></td>
					<td><?php _e('SCT'); ?> <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRS_L"></td>
					<td><?php _e('MCT'); ?> <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRM_L"></td>
					<td><?php _e('Vel'); ?>. <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_VEL_L"></td>
				</tr>
				<tr id="resultadosmanga-MediumRow">
					<td><input type="radio" name="rRecorrido" id="resultadosmanga-MediumBtn" value="1" onclick="consoleReloadParcial(1,true)"></td>
					<th id="resultadosmanga-MediumLbl">Medium</th>
					<td><?php _e('Distance'); ?> <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_DIST_M"></td>
					<td><?php _e('Obstacles'); ?> <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_OBST_M"></td>
					<td><?php _e('SCT'); ?> <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRS_M"></td>
					<td><?php _e('MCT'); ?> <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRM_M"></td>
					<td><?php _e('Vel'); ?>. <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_VEL_M"></td>
				</tr>
				<tr id="resultadosmanga-SmallRow">
					<td><input type="radio" name="rRecorrido" id="resultadosmanga-SmallBtn" value="2" onclick="consoleReloadParcial(2,true)"></td>
					<th id="resultadosmanga-SmallLbl">Small</th>
					<td><?php _e('Distance'); ?> <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_DIST_S"></td>
					<td><?php _e('Obstacles'); ?> <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_OBST_S"></td>
					<td><?php _e('SCT'); ?> <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRS_S"></td>
					<td><?php _e('MCT'); ?> <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRM_S"></td>
					<td><?php _e('Vel'); ?>. <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_VEL_S"></td>
				</tr>
				<tr id="resultadosmanga-TinyRow">
					<td><input type="radio" name="rRecorrido" id="resultadosmanga-TinyBtn" value="3" onclick="consoleReloadParcial(3,true)"></td>
					<th id="resultadosmanga-TinyLbl">Tiny</th>
					<td><?php _e('Distance'); ?> <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_DIST_T"></td>
					<td><?php _e('Obstacles'); ?> <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_OBST_T"></td>
					<td><?php _e('SCT'); ?> <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRS_T"></td>
					<td><?php _e('MCT'); ?> <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_TRM_T"></td>
					<td><?php _e('Vel'); ?>. <input type="text" size="4" class="trstrm" readonly="readonly" id="rm_VEL_T"></td>
				</tr>
			</table>
		</form>
	</div>
	<!-- BARRA DE TAREAS DE RESULTADOS PARCIALES DE MANGA -->
	<div id="resultadosmanga-toolbar">
		<span style="float:left;padding:5px">
			<strong><?php _e('Results on selected categories'); ?>:</strong>
		</span>
		<span style="float:right;padding:5px">
		   	<a id="resultadosmanga-reloadBtn" href="#" class="easyui-linkbutton"
			   data-options="iconCls:'icon-reload'" onclick="consoleReloadParcial($('input:radio[name=rRecorrido]:checked').val(),true)"><?php _e('Update'); ?></a>
	   		<a id="resultadosmanga-printBtn" href="#" class="easyui-linkbutton"
			   data-options="iconCls:'icon-print'" onclick="checkAndPrintParcial($('input:radio[name=rRecorrido]:checked').val())"><?php _e('Print'); ?></a>
		</span>
	</div>
	<!-- TABLA DE PRESENTACION DE RESULTADOS DE CADA MANGA -->
	<div id="resultadosmanga-data">
		<?php include_once(__DIR__."/../../lib/templates/parcial_individual.inc.php"); ?>
	</div>
</div>
<script type="text/javascript">

$('#resultadosmanga-dialog').dialog({
	collapsible:	false,
	minimizable:	false,
	maximizable:	false,
	resizable:		true,
	iconCls:		'icon-endflag',
	closed:			true,
	modal:			true
});

$('#parciales_individual-datagrid').datagrid({
	toolbar: '#resultadosmanga-toolbar',
    onBeforeLoad: function(param) { return (param.Manga > 0); }, // do not load if no manga selected
    onLoadSuccess: function() { // get focus on datagrid (to bind keystrokes)
    	$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
    }
});

//Activa tecla ESC (escape) para cerrar ventana,up,down y control+enter para ver fila
addSimpleKeyHandler('#parciales_individual-datagrid','#resultadosmanga-dialog');
//- botones del panel de resultados de la manga
addTooltip($('#resultadosmanga-reloadBtn').linkbutton(),'<?php _e("Update results on selected round/category"); ?>');
addTooltip($('#resultadosmanga-printBtn').linkbutton(),'<?php _e("Print round results"); ?>');

</script>