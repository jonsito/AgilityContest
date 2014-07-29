<!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA LOS DATOS DE EQUIPOS DE UNA PRUEBA -->
<div id="team_edit_dialog" class="easyui-dialog" title="Nuevo/Editar equipo" style="width:450px;padding:10px"
	data-options="iconCls:'icon-huella',
				modal:true,
				closed:true,
				closable:true,
				collapsible:false,
				minimizable:false,
				maximizable:false,
				resizable:false,
				buttons:'#team_dialog-buttons'">
   	<div class="ftitle">Datos del equipo:</div>
   	<form id="team_edit_dialog-form" class="easyui-form">
   		<input id="team_edit_dialog-ID" type="hidden" name="ID" value="0"/>
   		<input id="team_edit_dialog-Prueba" type="hidden" name="Prueba" value="0"/>
   		<input id="team_edit_dialog-Operation" type="hidden" name="Operation" value="insert"/>
   	 	<div class="fitem">
			<label for="Nombre" style="width:100px">Nombre:</label>
			<input id="team_edit_dialog-Nombre" name="Nombre" class="easyui-validatebox" style="width:150px"/>
		</div>
		<div class="fitem">
			<label for="Observaciones" style="width:100px">Observaciones:</label>
			<input id="team_edit_dialog-Observaciones" name="Observaciones" class="easyui-validatebox" style="width:300px"/>
		</div>
   	</form>

	<div id="team_dialog-buttons">
	   	<a id="team_edit_dialog-okBtn" href="#" class="easyui-linkbutton" 
	   			iconCls="icon-save" onclick="saveTeam()">Guardar</a>
		<a id="team_edit_dialog-cancelBtn" href="#" class="easyui-linkbutton" 
				iconCls="icon-cancel" onclick="javascript:$('#team_edit_dialog').dialog('close')">Cerrar</a>
	</div>
	
</div>
   
<script type="text/javascript">

// declaracion del dialogo
// TODO: estudiar por qué el "closed:true" en el data-options no funciona
$('#team_edit_dialog').dialog({closed:true});
$('#team_edit_dialog-Nombre').validatebox({ required:true, validType:'length[1,100]'});
addTooltip($('#team_edit_dialog-okBtn').linkbutton(),"Registrar los datos del equipo.<br />Actualizar la BBDD");
addTooltip($('#team_edit_dialog-cancelBtn').linkbutton(),"Finalizar la operacion. Cerrar la ventana");
</script>