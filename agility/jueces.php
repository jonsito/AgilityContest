<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE JUECES -->
    
    <!-- DECLARACION DE LA TABLA DE JUECES -->
    <table id="jueces-datagrid" class="easyui-datagrid">  </table>
    <!-- BARRA DE TAREAS DE LA TABLA DE JUECES -->
    <div id="jueces-toolbar">
    	<span style="float:left;">
    		<a id="jueces-newBtn" href="#" class="easyui-linkbutton" onclick="newJuez($('#jueces-search').val())">Nuevo Juez</a>
    		<a id="jueces-editBtn" href="#" class="easyui-linkbutton" onclick="editJuez()">Editar Juez</a>
    		<a id="jueces-delBtn" href="#" class="easyui-linkbutton" onclick="deletJuez()">Borrar Juez</a>
    		<input id="jueces-search" type="text" value="----- Buscar -----" class="search_textfield"/>
    	</span>
    	<span style="float:right;">
    		<a id="jueces-reloadBtn" href="#" class="easyui-linkbutton"">Actualizar</a>
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
        	method: 'get',
            toolbar: '#jueces-toolbar',
            pagination: true,
            rownumbers: false,
            fitColumns: true,
            singleSelect: true,
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
            rowStyler:function(index,row) { 
                return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
            },
        	// on double click fireup editor dialog
            onDblClickRow:function() { 
                editJuez();
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
                	where: $('#jueces-search').val(),
                	onloadSuccess: function(data) {
                		t.datagrid('getPager').pagination('refresh',{pageNumber:curPage});
                	}
                });
			}
        	var t = $('#jueces-datagrid');
            switch(e.keyCode){
                case 38:	/* Up */	selectRow(t,true); return false;
                case 40:    /* Down */	selectRow(t,false); return false;
                case 13:	/* Enter */	editJuez(); return false;
                case 45:	/* Insert */ newJuez(('#jueces-search').val()); return false;
                case 46:	/* Supr */	deleteJuez(); return false;
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
        $('#jueces-newBtn').linkbutton({iconCls:'icon-whistle',plain:true }); // nuevo juez
        $('#jueces-newBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Dar de alta un nuevo juez en la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        $('#jueces-editBtn').linkbutton({iconCls:'icon-edit',plain:true }); // editar juez
        $('#jueces-editBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Modificar los datos del juez seleccionado</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        $('#jueces-delBtn').linkbutton({iconCls:'icon-trash',plain:true }); // borrar juez 
        $('#jueces-delBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Eliminar el juez seleccionado de la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	}); 
        $('#jueces-reloadBtn').linkbutton({plain:true,iconCls:'icon-reload'}); // borrar perro     
        $('#jueces-reloadBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Borrar casilla de busqueda y actualizar tabla</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
    	$('#jueces-reloadBtn').on("click", function () {
        	// clear selection and reload table
    		$('#jueces-search').val('---- Buscar -----');
            $('#jueces-datagrid').datagrid('load',{ where: '' });
    	});
        $("#jueces-search").keydown(function(event){
            if(event.keyCode != 13) return;
          	// reload data adding search criteria
            $('#jueces-datagrid').datagrid('load',{
                where: $('#jueces-search').val()
            });
        });
        $('#jueces-search').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Buscar entradas que contengan el texto dado</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        

	</script>