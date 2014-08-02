<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE PERROS -->    
    <!-- DECLARACION DE LA TABLA -->
    <table id="perros-datagrid" style="width:975px;height:550px;">    </table>
    <!-- BARRA DE TAREAS -->
    <div id="perros-toolbar" style="padding:5px 5px 35px 5px">
    	<span style="float:left;">
    		<a id="perros-newBtn" href="#" class="easyui-linkbutton"
    			data-options="iconCls:'icon-dog'"
    			onclick="newDog($('#perros-datagrid','#perros-datagrid-search').val())">Nuevo Perro</a>
    		<a id="perros-editBtn" href="#" class="easyui-linkbutton"
    			data-options="iconCls:'icon-edit'"
    			onclick="editDog('#perros-datagrid')">Editar Perro</a>
    		<a id="perros-delBtn" href="#" class="easyui-linkbutton"
    			data-options="iconCls:'icon-trash'"
    			onclick="deleteDog('#perros-datagrid')">Borrar Perro</a>
    		<input id="perros-datagrid-search" type="text" value="---- Buscar ----" class="search_textfield"	/>
    	</span>
    	<span style="float:right;">
    		<a id="perros-reloadBtn" href="#" class="easyui-linkbutton"
    			data-options="iconCls:'icon-brush'"
    			onclick="
    	        	// clear selection and reload table
    	    		$('#perros-datagrid-search').val('---- Buscar ----');
    	            $('#perros-datagrid').datagrid('load',{ where: '' });"
    		>Limpiar</a>
    	</span>
    </div>
    
	<?php include_once("dialogs/dlg_perros.inc"); ?>
	<?php include_once("dialogs/dlg_guias.inc");?>
	<?php include_once("dialogs/dlg_clubes.inc");?>
    
    <script type="text/javascript">
    
    	// set up operation header content
    	setHeader('Gesti&oacute;n de Perros');
        
        // tell jquery to convert declared elements to jquery easyui Objects
        
        // datos de la tabla de perros
        // - tabla
        $('#perros-datagrid').datagrid({
        	// propiedades del panel padre asociado
        	fit: false,
        	border: false,
        	closable: false,
        	collapsible: false,
            expansible: false,
        	collapsed: false,
        	title: 'Gesti&oacute;n de datos de Perros',
        	url: 'database/dogFunctions.php?Operation=select',
        	loadMsg: 'Actualizando lista de perros ...',
        	method: 'get',
            toolbar: '#perros-toolbar',
            pagination: false,
            rownumbers: true,
            fitColumns: true,
            singleSelect: true,
            view: scrollview,
            pageSize: 50,
            multiSort: true,
            remoteSort: true,
            columns: [[
            	{ field:'ID',   hidden:true },
            	{ field:'Nombre',   width:30, sortable:true,  align: 'right', title: 'Nombre' },
            	{ field:'Raza',     width:25,                align: 'right', title: 'Raza' },
            	{ field:'LOE_RRC',  width:20, sortable:true, align: 'right', title: 'LOE / RRC' },
            	{ field:'Licencia', width:15, sortable:true, align: 'right', title: 'Lic.' },
            	{ field:'Categoria',width:10,                 align:'center', title: 'Cat.' },
            	{ field:'Grado',    width:10,                 align:'center', title: 'Grado' },
            	{ field:'Guia',   hidden:true },
                { field:'NombreGuia',     width:50, sortable:true, title: 'Nombre del Gu&iacute;a'},
            	{ field:'Club',   hidden:true },
                { field:'NombreClub',     width:35, sortable:true, title: 'Nombre del Club'}
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler,
        	// on double click fireup editor dialog
            onDblClickRow:function() { 
                editDog('#perros-datagrid');
            }
        });
		
		// key handler
       	addKeyHandler('#perros-datagrid',newDog,editDog,deleteDog);
		// tooltips
		addTooltip($('#perros-newBtn').linkbutton(),"Registrar un nuevo perro <br/>en la Base de Datos"); 
		addTooltip($('#perros-editBtn').linkbutton(),"Modificar los datos del perro seleccionado");
		addTooltip($('#perros-delBtn').linkbutton(),"Eliminar el perro seleccionado de la BBDD");
		addTooltip($('#perros-reloadBtn').linkbutton(),"Borrar casilla de busqueda y actualizar tabla");
		addTooltip($('#perros-datagrid-search'),"Buscar perros que cumplan con el criterio de busqueda");
	</script>