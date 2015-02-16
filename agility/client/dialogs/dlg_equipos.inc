<!-- 
dlg_equipos.inc

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
$config =new Config();
?>

<!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA LOS DATOS DE EQUIPOS DE UNA PRUEBA -->
<div id="team_edit_dialog" title="Nuevo/Editar equipo" style="width:450px;padding:10px">
   	<div class="ftitle"><?php _e('Datos del equipo');?>:</div>
   	<form id="team_edit_dialog-form" class="easyui-form">
   		<input id="team_edit_dialog-ID" type="hidden" name="ID" value="0"/>
   		<input id="team_edit_dialog-Prueba" type="hidden" name="Prueba" value="0"/>
   		<input id="team_edit_dialog-Operation" type="hidden" name="Operation" value="insert"/>
   	 	<div class="fitem">
			<label for="Nombre" style="width:100px"><?php _e('Nombre');?>:</label>
			<input id="team_edit_dialog-Nombre" name="Nombre" style="width:150px"
				data-options="required:true,validType:'length[1,100]'"/>
		</div>
		<div class="fitem">
			<label for="Observaciones" style="width:100px"><?php _e('Observaciones');?>:</label>
			<input id="team_edit_dialog-Observaciones" name="Observaciones" style="width:300px"/>
		</div>
   	</form>
	
</div>
   
<div id="team_dialog-buttons">
   	<a id="team_edit_dialog-okBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-save'" onclick="saveTeam()"><?php _e('Guardar');?></a>
	<a id="team_edit_dialog-cancelBtn" href="#" class="easyui-linkbutton" 
			data-options="iconCls:'icon-cancel'" onclick="javascript:$('#team_edit_dialog').dialog('close')"><?php _e('Cerrar');?></a>
</div>

<script type="text/javascript">

// declaracion del dialogo
$('#team_edit_dialog').dialog({
	iconCls:'icon-huella',
	modal:true,
	closed:true,
	closable:true,
	collapsible:false,
	minimizable:false,
	maximizable:false,
	resizable:false,
	buttons:'#team_dialog-buttons'
});

$('#team_edit_dialog-Nombre').validatebox({
    required: true,
    validType: 'length[1,100]'
});

addTooltip($('#team_edit_dialog-okBtn').linkbutton(),"<?php _e('Registrar los datos del equipo.<br />Actualizar la BBDD');?>");
addTooltip($('#team_edit_dialog-cancelBtn').linkbutton(),"<?php _e('Finalizar la operacion. Cerrar la ventana');?>");
</script>