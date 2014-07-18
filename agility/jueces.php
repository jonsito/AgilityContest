<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE JUECES -->
    
    <!-- DECLARACION DE LA TABLA DE JUECES -->
    <table id="jueces-datagrid" class="easyui-datagrid" style="width:975px;height:550px">  </table>
    <!-- BARRA DE TAREAS DE LA TABLA DE JUECES -->
    <div id="jueces-toolbar" style="padding:5px 5px 25px 5px;">
    	<span style="float:left;">
    		<a id="jueces-newBtn" href="#" class="easyui-linkbutton"
    			iconCls="icon-whistle" plain="true"
    			onclick="newJuez($('#jueces-search').val())">Nuevo Juez</a>
    		<a id="jueces-editBtn" href="#" class="easyui-linkbutton" 
    			iconCls="icon-edit" plain="true"
    			onclick="editJuez('#jueces-datagrid')">Editar Juez</a>
    		<a id="jueces-delBtn" href="#" class="easyui-linkbutton" 
    			iconCls="icon-trash" plain="true"
    			onclick="deleteJuez('#jueces-datagrid')">Borrar Juez</a>
    		<input id="jueces-search" type="text" value="---- Buscar ----" class="search_textfield"
    			/>
    	</span>
    	<span style="float:right;">
    		<a id="jueces-reloadBtn" href="#" class="easyui-linkbutton"
    			plain="true" iconCls="icon-reload"
    			onclick="
    	        	// clear selection and reload table
    	    		$('#jueces-search').val('---- Buscar ----');
    	            $('#jueces-datagrid').datagrid('load',{ where: '' });"
    			>Actualizar</a>
    	</span>
    </div>
	<?php include_once("dialogs/dlg_jueces.inc")?>
    
    <script type="text/javascript">
    
    	// set up operation header content
        $('#Header_Operation').html('<p>Gesti&oacute;n de Jueces</p>');
        
        // tell jquery to convert declared elements to jquery easyui Objects
        
        // datos de la tabla de jueces
        // - tabla
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
        	url: 'database/juezFunctions.php?Operation=select',
        	loadMsg: 'Actualizando lista de jueces ...',
        	method: 'get',
            toolbar: '#jueces-toolbar',
            pagination: false,
            rownumbers: true,
            fitColumns: true,
            singleSelect: true,
            view: scrollview,
            pageSize: 50,
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
        // activa teclas up/down para navegar por el panel
        $('#jueces-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
            function selectRow(t,up){
            	var count = t.datagrid('getRows').length;    // row count
            	var selected = t.datagrid('getSelected');
            	if (selected){
                	var index = t.datagrid('getRowIndex', selected);
                	index = index + (up ? -1 : 1);
                	if (index < 0) index = 0;
                	if (index >= count) index = count - 1;
                	t.datagrid('clearSelections');
                	t.datagrid('selectRow', index);
            	} else {
                	t.datagrid('selectRow', (up ? count-1 : 0));
            	}
        	}
        	
			function selectPage(t,offset) {
            	var count = t.datagrid('getRows').length;    // row count
            	var selected = t.datagrid('getSelected');
            	if (selected){
                	var index = t.datagrid('getRowIndex', selected);
                	switch(offset) {
                	case 1: index+=10; break;
                	case -1: index-=10; break;
                	case 2: index=count -1; break;
                	case -2: index=0; break;
                	}
                	if (index<0) index=0;
                	if (index>=count) index=count-1;
                	t.datagrid('clearSelections');
                	t.datagrid('selectRow', index);
            	} else {
                	t.datagrid('selectRow', 0);
            	}
			}
			
        	var t = $('#jueces-datagrid');
            switch(e.keyCode){
                case 38:	/* Up */	selectRow(t,true); return false;
                case 40:    /* Down */	selectRow(t,false); return false;
                case 13:	/* Enter */	editJuez('#jueces-datagrid'); return false;
                case 45:	/* Insert */newJuez($('#jueces-search').val()); return false;
                case 46:	/* Supr */	deleteJuez('#jueces-datagrid'); return false;
                case 33:	/* Re Pag */ selectPage(t,-1); return false;
                case 34:	/* Av Pag */ selectPage(t,1); return false;
                case 35:	/* Fin */    selectPage(t,2); return false;
                case 36:	/* Inicio */ selectPage(t,-2); return false;
                case 9: 	/* Tab */
                    // if (e.shiftkey) return false; // shift+Tab
                    return false;
                case 16:	/* Shift */
                case 17:	/* Ctrl */
                case 18:	/* Alt */
                case 27:	/* Esc */
                    return false;
            }
		});
		// tooltips
		addTooltip($('#jueces-newBtn').linkbutton(),"Dar de alta un nuevo juez en la BBDD"); 
		addTooltip($('#jueces-editBtn').linkbutton(),"Modificar los datos del juez seleccionado");
		addTooltip($('#jueces-delBtn').linkbutton(),"Eliminar el juez seleccionado de la BBDD");
		addTooltip($('#jueces-reloadBtn').linkbutton(),"Borrar casilla de busqueda y actualizar tabla");
		addTooltip($('#jueces-search'),"Buscar jueces que coincidan con el criterio de busqueda");
        $("#jueces-search").keydown(function(event){
            if(event.keyCode != 13) return;
          	// reload data adding search criteria
            $('#jueces-datagrid').datagrid('load',{
                where: $('#jueces-search').val()
            });
        });
        

	</script>