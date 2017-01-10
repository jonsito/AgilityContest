<!-- 
dlg_resultados_equipos.inc

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
$config =Config::getInstance();
?>

<!--  dialogo de presentacion de resultados parciales de una manga -->
<div id="resultadosmanga-dialog" style="width:800px;height:450px;padding:0 10px 100px 10px">

	<!-- TABLA DE DATOS DE TRS Y TRM DE LA MANGA -->
	<div id="resultadosmanga-trs">
		<?php include_once(__DIR__."/../../lib/templates/parcial_console_round_data.inc.php"); ?>
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
		<?php include_once(__DIR__."/../../lib/templates/parcial_teams.inc.php"); ?>
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

$('#parciales_equipos-datagrid').datagrid({
	toolbar:'#resultadosmanga-toolbar',
    onBeforeLoad: function(param) { return (param.Manga > 0); }, // do not load if no manga selected
    onLoadSuccess: function() { // get focus on datagrid (to bind keystrokes)
    	$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
    }
});

//Activa tecla ESC (escape) para cerrar ventana,up,down y control+enter para ver fila
addSimpleKeyHandler('#parciales_equipos-datagrid','#resultadosmanga-dialog');
//- botones del panel de resultados de la manga
addTooltip($('#resultadosmanga-reloadBtn').linkbutton(),'<?php _e("Update results on selected round/category"); ?>');
addTooltip($('#resultadosmanga-printBtn').linkbutton(),'<?php _e("Print round results"); ?>');

</script>