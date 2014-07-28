<!-- ventana de presentacion de los datos de los euipos de una prueba -->
<div id="team_datagrid-dialog" class="easyui-dialog" style="width:600px;height:300px"
	data-options="iconCls:'icon-huella',
				modal:true,
				closed:true,
				closable:true,
				collapsible:false,
				minimizable:false,
				maximizable:false,
				resizable:false">
	<!-- DECLARACION DE LA TABLA DE EQUIPOS -->
    <table id="team_datagrid" class="easyui-datagrid"></table>
    
    <!-- BARRA DE TAREAS DE LA TABLA DE Equipos -->
	<div id="team_toolbar" style="padding:5px 5px 35px 5px;">
	   	<span style="float:left;">
	   		<a id="team_datagrid-newBtn" href="#" class="easyui-linkbutton"
	   			data-options="iconCls:'icon-add'"
	   			onclick="newTeam('#team_datagrid',$('#team_datagrid-search').val())">Nuevo</a>
	   		<a id="team_datagrid-editBtn" href="#" class="easyui-linkbutton" 
	   			data-options="iconCls:'icon-edit'"
	   			onclick="editTeam('#team_datagrid')">Editar</a>
	   		<a id="team_datagrid-delBtn" href="#" class="easyui-linkbutton" 
	   			data-options="iconCls:'icon-trash'"
	   			onclick="deleteTeam('#team_datagrid')">Borrar</a>
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
</div>

<script type="text/javascript">

//TODO: estudiar por qué el "closed:true" en el data-options no funciona
$('#team_datagrid-dialog').dialog({
	title:"Gestion de Equipos de la prueba: '"+workingData.datosPrueba.Nombre+"'",
	closed:true
	});

// datos de la tabla de equipos
$('#team_datagrid').datagrid({
    // datos del panel padre asociado
	fit: true,
	border: false,
	closable: false,
	closed:false,
	collapsible: false,
    expansible: false,
	collapsed: false,
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
        { field:'Prueba',		hidden:true },
        { field:'Nombre',		width:20, sortable:true,	title: 'Nombre:' },
        { field:'Observaciones',width:80,					title: 'Observaciones'}
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
addTooltip($('#team_datagrid-search'),"Buscar equipos que coincidan con el criterio de busqueda");
addTooltip($('#team_datagrid-newBtn').linkbutton(),"Declarar un nuevo equipo para la prueba");
addTooltip($('#team_datagrid-editBtn').linkbutton(),"Editar nombre/observaciones del equipo seleccionado");
addTooltip($('#team_datagrid-delBtn').linkbutton(),"Eliminar datos del equipo en la prueba");
addTooltip($('#team_datagrid-reloadBtn').linkbutton(),"Actualizar listado de equipos de la prueba");
</script>