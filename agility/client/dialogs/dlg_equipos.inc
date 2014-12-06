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

<!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA LOS DATOS DE EQUIPOS DE UNA PRUEBA -->
<div id="team_edit_dialog" title="Nuevo/Editar equipo" style="width:450px;padding:10px">
   	<div class="ftitle">Datos del equipo:</div>
   	<form id="team_edit_dialog-form" class="easyui-form">
   		<input id="team_edit_dialog-ID" type="hidden" name="ID" value="0"/>
   		<input id="team_edit_dialog-Prueba" type="hidden" name="Prueba" value="0"/>
   		<input id="team_edit_dialog-Operation" type="hidden" name="Operation" value="insert"/>
   	 	<div class="fitem">
			<label for="Nombre" style="width:100px">Nombre:</label>
			<input id="team_edit_dialog-Nombre" name="Nombre" class="easyui-validatebox" style="width:150px"
				data-options="required:true,validType:'length[1,100]'"/>
		</div>
		<div class="fitem">
			<label for="Observaciones" style="width:100px">Observaciones:</label>
			<input id="team_edit_dialog-Observaciones" name="Observaciones" class="easyui-validatebox" style="width:300px"/>
		</div>
   	</form>
	
</div>
   
<div id="team_dialog-buttons">
   	<a id="team_edit_dialog-okBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-save'" onclick="saveTeam()">Guardar</a>
	<a id="team_edit_dialog-cancelBtn" href="#" class="easyui-linkbutton" 
			data-options="iconCls:'icon-cancel'" onclick="javascript:$('#team_edit_dialog').dialog('close')">Cerrar</a>
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
addTooltip($('#team_edit_dialog-okBtn').linkbutton(),"Registrar los datos del equipo.<br />Actualizar la BBDD");
addTooltip($('#team_edit_dialog-cancelBtn').linkbutton(),"Finalizar la operacion. Cerrar la ventana");
</script>