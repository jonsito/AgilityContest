<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE JUECES -->
    
    <!-- DECLARACION DE LA TABLA DE JUECES -->
    <table id="jueces-datagrid" class="easyui-datagrid">  </table>
    <!-- BARRA DE TAREAS DE LA TABLA DE JUECES -->
    <div id="jueces-toolbar">
    	<a id="jueces-newBtn" href="#" class="easyui-linkbutton" onclick="newJuez()">Nuevo Juez</a>
    	<a id="jueces-editBtn" href="#" class="easyui-linkbutton" onclick="editJuez()">Editar Juez</a>
    	<a id="jueces-delBtn" href="#" class="easyui-linkbutton" onclick="destroyJuez()">Borrar Juez</a>
    	<input id="jueces-search" type="text" onchange="doSearchJuez()"/> 
    	<a id="jueces-searchBtn" href="#" class="easyui-linkbutton" onclick="doSearchJuez()">Buscar</a>
    </div>
	<?php include_once("dialogs/dlg_jueces.inc")?>
    
    <script type="text/javascript">
    
    	// set up operation header content
        $('#Header_Operation').html('<p>Gesti&oacute;n de Jueces</p>');
        
        // tell jquery to convert declared elements to jquery easyui Objects
        
        // datos de la tabla de jueces
        // - tabla
        $('#jueces-datagrid').datagrid({
        	title: 'Gesti&oacute;n de datos de Jueces',
        	url: 'database/select_jueces.php',
        	method: 'get',
            toolbar: '#jueces-toolbar',
            pagination: true,
            rownumbers: true,
            fitColumns: true,
            singleSelect: true,
            columns: [[
            	{ field:'Nombre',		width:20, sortable:true,	title: 'Nombre:' },
            	{ field:'Direccion1',	width:12,					title: 'Direcci&oacute;n 1:' },
            	{ field:'Direccion2',	width:12,                   title: 'Direcci&oacute;n 2' },
            	{ field:'Telefono',		width:11, sortable:true,	title: 'Tel&eacute;fono' },
            	{ field:'Internacional',width:5, align:'center',	title: 'Int.' 	},
            	{ field:'Practicas',	width:5, align:'center',	title: 'Pract.' },
            	{ field:'Email',		width:18, sortable:true,    title: 'Correo Electr&oacute;nico' },
                { field:'Observaciones',width:15,					title: 'Observaciones'}
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
        // - botones de la toolbar de la tabla
        $('#jueces-newBtn').linkbutton({iconCls:'icon-add',plain:true }); // nuevo juez
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
        $('#jueces-delBtn').linkbutton({iconCls:'icon-remove',plain:true }); // borrar juez 
        $('#jueces-delBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Eliminar el juez seleccionado de la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});        
    	$('#jueces-searchBtn').linkbutton({iconCls:'icon-search',plain:true }); // buscar juez 
        $('#jueces-searchBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Buscar entradas que contengan el texto dado</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        

	</script>