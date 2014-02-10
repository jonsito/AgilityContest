<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE PERROS -->

    <!-- INFORMACION ADICIONAL 
    <div class="demo-info" style="margin-bottom:10px">
        <div class="demo-tip icon-tip">&nbsp;</div>
        <div>Selecciona con el rat&oacute;n las acciones a realizar en la barra de tareas</div>
    </div>
    -->
    
    <!-- DECLARACION DE LA TABLA -->
    <table id="perros-datagrid" class="easyui-datagrid" >
    </table>
    <!-- BARRA DE TAREAS -->
    <div id="perros-toolbar">
    	<a id="perros-newBtn" href="#" class="easyui-linkbutton" onclick="newDog()">Nuevo Perro</a>
    	<a id="perros-editBtn" href="#" class="easyui-linkbutton" onclick="editDog()">Editar Perro</a>
    	<a id="perros-delBtn" href="#" class="easyui-linkbutton" onclick="deleteDog()">Borrar Perro</a>
    	<input id="perros-search" type="text" onchange="doSearchPerro()"/> 
    	<a id="perros-searchBtn" href="#" class="easyui-linkbutton" onclick="doSearchPerro()">Buscar</a>
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
        	title: 'Gesti&oacute;n de datos de Perros',
        	url: 'database/dogFunctions.php?Operation=select',
        	method: 'get',
            toolbar: '#perros-toolbar',
            pagination: true,
            rownumbers: false,
            fitColumns: true,
            singleSelect: true,
            columns: [[
            	{ field:'Dorsal',   width:10,  sortable:true, align: 'right', title: 'Dorsal' },
            	{ field:'Nombre',   width:30, sortable:true,  align: 'right', title: 'Nombre' },
            	{ field:'Raza',     width:25,                align: 'right', title: 'Raza' },
            	{ field:'LOE_RRC',  width:15, sortable:true, align: 'right', title: 'LOE / RRC' },
            	{ field:'Licencia', width:10, sortable:true, align: 'right', title: 'Lic.' },
            	{ field:'Categoria',width:5,                 align:'center', title: 'Cat.' },
            	{ field:'Grado',    width:7,                 align:'center', title: 'Grado' },
                { field:'Guia',     width:45, sortable:true,                 title: 'Nombre del Gu&iacute;a'},
                { field:'Club',     width:35, sortable:true,                 title: 'Club'}
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:function(index,row) { 
                return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
            },
        	// on double click fireup editor dialog
            onDblClickRow:function() { 
                editDog();
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
				var p=t.datagrid('getPager').pagination('options');
				var curPage=p.pageNumber;
				var lastPage=1+parseInt(p.total/p.pageSize);
				if (offset==-2) curPage=1;
				if (offset==2) curPage=lastPage;
				if ((offset==-1) && (curPage>1)) curPage=curPage-1;
				if ((offset==1) && (curPage<lastPage)) curPage=curPage+1;
            	t.datagrid('clearSelections');
            	p.pageNumber=curPage;
            	t.datagrid('options').pageNumber=curPage;
            	t.datagrid('reload', {
                	where: $('#perros-search').val(),
                	onloadSuccess: function(data) {
                		t.datagrid('getPager').pagination('refresh',{pageNumber:curPage});
                	}
                });
			}
        	var t = $('#perros-datagrid');
            switch(e.keyCode){
            case 38:	/* Up */	selectRow(t,true); return false;
            case 40:    /* Down */	selectRow(t,false); return false;
            case 13:	/* Enter */	editDog(); return false;
            case 45:	/* Insert */ newDog(); return false;
            case 46:	/* Supr */	deleteDog(); return false;
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
        // - botones de la toolbar de la tabla
        $('#perros-newBtn').linkbutton({plain:true,iconCls:'icon-add'}); // nuevo perro       
        $('#perros-newBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Dar de alta un nuevo perro en la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        $('#perros-editBtn').linkbutton({plain:true,iconCls:'icon-edit'}); // editar perro      
        $('#perros-editBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Modificar los datos del perro seleccionado</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        $('#perros-delBtn').linkbutton({plain:true,iconCls:'icon-remove'}); // borrar perro     
        $('#perros-delBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Eliminar el perro seleccionado de la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});

        $('#perros-searchBtn').linkbutton({plain:true,iconCls:'icon-search'} ); // buscar perro
        $('#perros-searchBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Buscar entradas que contengan el texto dado</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
	</script>