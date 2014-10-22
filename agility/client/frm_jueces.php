<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE JUECES -->
    
    <!-- DECLARACION DE LA TABLA DE JUECES -->
    <table id="jueces-datagrid" style="width:975px;height:550px">  </table>
    <!-- BARRA DE TAREAS DE LA TABLA DE JUECES -->
    <div id="jueces-toolbar" style="padding:5px 5px 35px 5px;">
    	<span style="float:left;">
    		<a id="jueces-newBtn" href="#" class="easyui-linkbutton"
    			data-options="iconCls:'icon-whistle'"
    			onclick="newJuez('#jueces-datagrid',$('#jueces-datagrid-search').val())">Nuevo Juez</a>
    		<a id="jueces-editBtn" href="#" class="easyui-linkbutton" 
    			data-options="iconCls:'icon-edit'"
    			onclick="editJuez('#jueces-datagrid')">Editar Juez</a>
    		<a id="jueces-delBtn" href="#" class="easyui-linkbutton" 
    			data-options="iconCls:'icon-trash'"
    			onclick="deleteJuez('#jueces-datagrid')">Borrar Juez</a>
    		<input id="jueces-datagrid-search" type="text" value="---- Buscar ----" class="search_textfield"	/>
    	</span>
    	<span style="float:right;">
    		<a id="jueces-reloadBtn" href="#" class="easyui-linkbutton"
    			data-options="iconCls:'icon-brush'"
    			onclick="
    	        	// clear selection and reload table
    	    		$('#jueces-datagrid-search').val('---- Buscar ----');
    	            $('#jueces-datagrid').datagrid('load',{ where: '' });"
    			>Limpiar</a>
    	</span>
    </div>
	<?php require_once("dialogs/dlg_jueces.inc")?>
    
    <script type="text/javascript">
        
        // datos de la tabla de jueces
        $('#jueces-datagrid').datagrid({
            // datos del panel padre asociado
        	fit: false,
        	border: false,
        	closable: false,
        	collapsible: false,
            expansible: false,
        	collapsed: false,
        	title: 'Gesti&oacute;n de datos de Jueces',
        	// datos de la conexion ajax
        	url: '/agility/server/database/juezFunctions.php?Operation=select',
        	loadMsg: 'Actualizando lista de jueces ...',
        	method: 'get',
            toolbar: '#jueces-toolbar',
            pagination: false,
            rownumbers: true,
            fitColumns: true,
            singleSelect: true,
            view: scrollview,
            pageSize: 50,
            multiSort: true,
            remoteSort: true,
            columns: [[
                { field:'ID',			hidden:true },
                { field:'Nombre',		width:40, sortable:true,	title: 'Nombre:' },
            	{ field:'Direccion1',	width:35,					title: 'Direcci&oacute;n 1:' },
            	{ field:'Direccion2',	width:35,                   title: 'Direcci&oacute;n 2' },
            	{ field:'Telefono',		width:20, sortable:true,	title: 'Tel&eacute;fono' },
            	{ field:'Internacional',width:10, align:'center',	title: 'Int.' 	},
            	{ field:'Practicas',	width:10, align:'center',	title: 'Pract.' },
            	{ field:'Email',		width:35, sortable:true,   align:'right', title: 'Correo Electr&oacute;nico' },
                { field:'Observaciones',width:30,					title: 'Observaciones'}
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler,
        	// on double click fireup editor dialog
            onDblClickRow:function() { 
                editJuez('#jueces-datagrid');
            }
        });

		// key handler
       	addKeyHandler('#jueces-datagrid',newJuez,editJuez,deleteJuez);
		// tooltips
		addTooltip($('#jueces-newBtn').linkbutton(),"Añadir un nuevo juez<br/> a la Base de Datos"); 
		addTooltip($('#jueces-editBtn').linkbutton(),"Modificar los datos del juez seleccionado");
		addTooltip($('#jueces-delBtn').linkbutton(),"Eliminar el juez seleccionado de la BBDD");
		addTooltip($('#jueces-reloadBtn').linkbutton(),"Borrar casilla de busqueda y actualizar tabla");
		addTooltip($('#jueces-datagrid-search'),"Buscar jueces que coincidan con el criterio de busqueda");

	</script>