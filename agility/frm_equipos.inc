<!-- ventana de presentacion de los datos de los euipos de una prueba -->
<div id="team_datagrid-dialog" class="easyui-dialog" title="Gestion de Equipos" style="width:400px;height:300px"
	data-options="iconCls:'icon-user',
				modal:true,
				closed:true,
				closable:true,
				collapsible:false,
				minimizable:false,
				maximizable:false,
				resizable:false">
	<!-- DECLARACION DE LA TABLA DE EQUIPOS -->
    <table id="team_datagrid" style="width:975px;height:550px">  </table>
</div>
  
<!-- BARRA DE TAREAS DE LA TABLA DE Equipos -->
<div id="team_toolbar" style="padding:5px 5px 35px 5px;">
   	<span style="float:left;">
   		<a id="team_datagrid-newBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-add'"
   			onclick="newTeam($('#team_datagrid','#team_datagrid-search').val())">Nuevo equipo</a>
   		<a id="team_datagrid-editBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-edit'"
   			onclick="editTeam('#team_datagrid')">Editar equipo</a>
   		<a id="team_datagrid-delBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-trash'"
   			onclick="deleteTeam('#team_datagrid')">Borrar equipo</a>
   		<input id="team_datagrid-search" type="text" value="---- Buscar ----" class="search_textfield"	/>
   	</span>
   	<span style="float:right;">
   		<a id="team_datagrid-reloadBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-reload'"
   			onclick="
   	        	// clear selection and reload table
   	    		$('#team_datagrid-search').val('---- Buscar ----');
   	            $('#team_datagrid').datagrid('load',{ where: '' });"
   			>Actualizar</a>
   	</span>
</div>

<!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA LOS DATOS DE EQUIPOS DE UNA PRUEBA -->
<div id="team_edit-dialog" class="easyui-dialog" title="Nuevo/Editar equipo" style="width:400px;height:150px;padding:10px 20px"
	data-options="iconCls:'icon-save',
				modal:true,
				closed:true,
				closable:true,
				collapsible:false,
				minimizable:false,
				maximizable:false,
				resizable:false,
				buttons:'#team_dialog-buttons'">
   	<div class="ftitle">Nuevo equipo</div>
   	<form id="team_edit_dialog-form" class="easyui-form">
   	 	<div class="fitem">
			<label for="Nombre">Nombre:</label>
			<input name="NombreEquipo" class="easyui-textbox" required="true">
		</div>
		<div class="fitem">
			<label for="Observaciones">Observaciones:</label>
			<input name="Observaciones" class="easyui-textbox" required="false">
		</div>
   	</form>
</div>


<div id="team_dialog-buttons">
   	<a id="team_dialog-okBtn" href="#" class="easyui-linkbutton" 
   			iconCls="icon-save" onclick="saveTeam()">Aceptar</a>
	<a id="team_dialog-cancelBtn" href="#" class="easyui-linkbutton" 
			iconCls="icon-cancel" onclick="javascript:$('#team_edit-dialog').dialog('close')">Cancelar</a>
</div>
   
<script type="text/javascript">

// datos de la tabla de equipos
$('#team_datagrid').datagrid({
    // datos del panel padre asociado
	fit: false,
	border: false,
	closable: true,
	closed:true,
	collapsible: false,
    expansible: false,
	collapsed: false,
	title: 'Gesti&oacute;n de equipos de una prueba',
	// datos de la conexion ajax
	url: 'database/equiposFunctions.php?Operation=select&IDPrueba='+workingData.prueba,
	loadMsg: 'Actualizando lista de equipos ...',
	method: 'get',
    toolbar: '#team_toolbar',
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    view: scrollview,
    pageSize: 50,
    columns: [[
        { field:'ID',			hidden:true },
        { field:'Nombre',		width:40, sortable:true,	title: 'Nombre:' },
        { field:'Observaciones',width:30,					title: 'Observaciones'}
    ]],
    // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    rowStyler:myRowStyler,
	// on double click fireup editor dialog
    onDblClickRow:function() { 
        editTeam('#team_datagrid');
    }
});

// key handler
addKeyHandler('#team_datagrid',newTeam,editTeam,deleteTeam);
// - tooltips
addTooltip($('#team_datagrid-newBtn').linkbutton(),"Declarar un nuevo equipo para la prueba");
addTooltip($('#team_datagrid-editBtn').linkbutton(),"Editar nombre/observaciones del equipo seleccionado");
addTooltip($('#team_datagrid-delBtn').linkbutton(),"Eliminar datos del equipo en la prueba");
addTooltip($('#team_datagrid-reloadBtn').linkbutton(),"Actualizar listado de equipos de la prueba");
addTooltip($('#team_dialog-okBtn').linkbutton(),"Registrar los datos del equipo<br />. actualizar la BBDD");
addTooltip($('#team_dialog-cancelBtn').linkbutton(),"Cancelar la operaci&oacute;n. Cerrar ventana");
</script>