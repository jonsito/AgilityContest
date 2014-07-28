<!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA LOS DATOS DE EQUIPOS DE UNA PRUEBA -->
<div id="team_edit_dialog" class="easyui-dialog" title="Nuevo/Editar equipo" style="width:400px;height:250px;padding:10px 20px"
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
   		<input id="team_edit_dialog-Operation" type="hidden" name="Operation" value="insert/>
   	 	<div class="fitem">
			<label for="Nombre">Nombre:</label>
			<input type="text" id="team_edit_dialog-Nombre" name="NombreEquipo" class="easyui-textbox" required="true"/>
		</div>
		<div class="fitem">
			<label for="Observaciones">Observaciones:</label>
			<input type="text" id="team_edit_dialog-Observaciones" name="Observaciones" class="easyui-textbox" required="false"/>
		</div>
   	</form>

	<div id="team_dialog-buttons">
	   	<a id="team_edit_dialog-okBtn" href="#" class="easyui-linkbutton" 
	   			iconCls="icon-save" onclick="saveTeam()">Aceptar</a>
		<a id="team_edit_dialog-cancelBtn" href="#" class="easyui-linkbutton" 
				iconCls="icon-cancel" onclick="javascript:$('#team_edit_dialog').dialog('close')">Cancelar</a>
	</div>
	
</div>
   
<script type="text/javascript">

// declaracion del dialogo
// TODO: estudiar por qué el "closed:true" en el data-options no funciona
$('#team_edit_dialog').dialog({closed:true});

addTooltip($('#team_edit_dialog-okBtn').linkbutton(),"Registrar los datos del equipo.<br />Actualizar la BBDD");
addTooltip($('#team_edit_dialog-cancelBtn').linkbutton(),"Cancelar la operaci&oacute;n. Cerrar ventana");
</script>