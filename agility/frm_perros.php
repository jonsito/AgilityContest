<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE PERROS -->    
    <!-- DECLARACION DE LA TABLA -->
    <table id="perros-datagrid" style="width:975px;height:550px;">    </table>
    <!-- BARRA DE TAREAS -->
    <div id="perros-toolbar" style="padding:5px 5px 35px 5px">
    	<span style="float:left;">
    		<a id="perros-newBtn" href="#" class="easyui-linkbutton"
    			data-options="iconCls:'icon-dog'"
    			onclick="newDog($('#perros-search').val())">Nuevo Perro</a>
    		<a id="perros-editBtn" href="#" class="easyui-linkbutton"
    			data-options="iconCls:'icon-edit'"
    			onclick="editDog('#perros-datagrid')">Editar Perro</a>
    		<a id="perros-delBtn" href="#" class="easyui-linkbutton"
    			data-options="iconCls:'icon-trash'"
    			onclick="deleteDog('#perros-datagrid')">Borrar Perro</a>
    		<input id="perros-search" type="text" value="---- Buscar ----" class="search_textfield"	/>
    	</span>
    	<span style="float:right;">
    		<a id="perros-reloadBtn" href="#" class="easyui-linkbutton"
    			data-options="iconCls:'icon-reload'"
    			onclick="
    	        	// clear selection and reload table
    	    		$('#perros-search').val('---- Buscar ----');
    	            $('#perros-datagrid').datagrid('load',{ where: '' });"
    		>Actualizar</a>
    	</span>
    </div>
    
	<?php include_once("dialogs/dlg_perros.inc"); ?>
	<?php include_once("dialogs/dlg_guias.inc");?>
	<?php include_once("dialogs/dlg_clubes.inc");?>
    
    <script type="text/javascript">
    
    	// set up operation header content
        $('#Header_Operation').html('<p>Gesti&oacute;n de Perros</p>');
        
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
            remoteFilter: true,
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
        // activa teclas up/down para navegar por el panel
        $('#perros-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
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
			
        	var t = $('#perros-datagrid');
            switch(e.keyCode){
            case 38:	/* Up */	selectRow(t,true); return false;
            case 40:    /* Down */	selectRow(t,false); return false;
            case 13:	/* Enter */	editDog('#perros-datagrid'); return false;
            case 45:	/* Insert */ newDog($('#perros-search').val()); return false;
            case 46:	/* Supr */	deleteDog('#perros-datagrid'); return false;
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
		addTooltip($('#perros-newBtn').linkbutton(),"Registrar un nuevo perro <br/>en la Base de Datos"); 
		addTooltip($('#perros-editBtn').linkbutton(),"Modificar los datos del perro seleccionado");
		addTooltip($('#perros-delBtn').linkbutton(),"Eliminar el perro seleccionado de la BBDD");
		addTooltip($('#perros-reloadBtn').linkbutton(),"Borrar casilla de busqueda y actualizar tabla");
		addTooltip($('#perros-search'),"Buscar perros que cumplan con el criterio de busqueda");
        // - activar la tecla "Enter" en la casilla de busqueda
        $("#perros-search").keydown(function(event){
            if(event.keyCode != 13) return;
          	// reload data adding search criteria
            $('#perros-datagrid').datagrid('load',{
                where: $('#perros-search').val()
            });
        });
	</script>