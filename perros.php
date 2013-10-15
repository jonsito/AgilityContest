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
    	<a id="perros-delBtn" href="#" class="easyui-linkbutton" onclick="destroyDog()">Borrar Perro</a>
    	<input id="perros-search" type="text" onchange="doSearchPerro()"/> 
    	<a id="perros-searchBtn" href="#" class="easyui-linkbutton" onclick="doSearchPerro()">Buscar</a>
    </div>
    
	<?php include_once("dialogs/dlg_perros.inc"); ?>
    
    <script type="text/javascript">
    
    	// set up operation header content
        $('#Header_Operation').html('<p>Gesti&oacute;n de Perros</p>');
        
        // tell jquery to convert declared elements to jquery easyui Objects
        
        // datos de la tabla de perros
        // - tabla
        $('#perros-datagrid').datagrid({
        	title: 'Gesti&oacute;n de datos de Perros',
        	url: 'database/select_dogs.php',
        	method: 'get',
            toolbar: '#perros-toolbar',
            pagination: true,
            rownumbers: false,
            fitColumns: true,
            singleSelect: true,
            columns: [[
            	{ field:'Dorsal',   width:5,  sortable:true, align: 'right', title: 'Dorsal' },
            	{ field:'Nombre',   width:10, sortable:true,                 title: 'Nombre' },
            	{ field:'Raza',     width:15,                                title: 'Raza' },
            	{ field:'LOE_RRC',  width:10, sortable:true, align: 'right', title: 'LOE / RRC' },
            	{ field:'Licencia', width:10, sortable:true, align: 'right', title: 'Lic.' },
            	{ field:'Categoria',width:5,                 align:'center', title: 'Cat.' },
            	{ field:'Grado',    width:5,                 align:'center', title: 'Grado' },
                { field:'Guia',     width:25, sortable:true,                 title: 'Nombre del Gu&iacute;a'},
                { field:'Club',     width:15, sortable:true,                 title: 'Club'}
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