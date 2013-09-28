<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE PERROS -->

    <!-- INFORMACION ADICIONAL 
    <div class="demo-info" style="margin-bottom:10px">
        <div class="demo-tip icon-tip">&nbsp;</div>
        <div>Selecciona con el rat&oacute;n las acciones a realizar en la barra de tareas</div>
    </div>
    -->
    
    <!-- DECLARACION DE LA TABLA -->
    <table id="perros-datagrid" class="easyui-datagrid" style="width:800px;height:400px" />
    
    <!-- BARRA DE TAREAS -->
    <div id="perros-toolbar">
        <a id="perros-newBtn" href="#" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newDog()">Nuevo Perro</a>
        <a id="perros-editBtn" href="#" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editDog()">Editar Perro</a>
        <a id="perros-delBtn" href="#" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyDog()">Borrar Perro</a>
    </div>
    
    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE PERROS -->
    <div id="perros-dialog" class="easyui-dialog" style="width:500px;height:375px;padding:10px 20px"
            closed="true" buttons="#perros-dlg-buttons">
        <div class="ftitle">Informaci&oacute;n del perro</div>
        <form id="perros-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input id="perros-Nombre" 
                	name="Nombre" 
                	type="text" 
                	class="easyui-validatebox" 
                	required="true"/>
                <input name="Dorsal" type="hidden" /> <!-- hide dorsal, as only used for edit and is readonly -->
            </div>
            <div class="fitem">
                <label for="Raza">Raza:</label>
                <input id="perros-Raza" class="easyui-validatebox" name="Raza" type="text" />
            </div>
            <div class="fitem">
                <label for="LOE_RRC">Num. LOE / RRC:</label>
                <input id="perros-LOE_RRC" class="easyui-validatebox" name="LOE_RRC" type="text" />
            </div>
            <div class="fitem">
                <label for="Licencia">Num. Licencia:</label>
                <input id="perros-Licencia" class="easyui-validatebox" name="Licencia" type="text" />
            </div>
            <div class="fitem">
                <label for="Categoria">Categor&iacute;a:</label>
                <select id="perros-Categorias_Perro" 
                		name="Categoria" 
                		class="easyui-combobox" 
                		style="width:155px"
                		required="true" />
            </div>
            <div class="fitem">
                <label for="Grado">Grado:</label>
                <select id="perros-Grados_Perro" 
                		name="Grado" 
                		class="easyui-combobox" 
                		style="width:155px"
                		required="true" />
            </div>
            <div class="fitem">
                <label for="Guia">Nombre del Gu&iacute;a:</label>
                <select id="perros-Guia" name="Guia" class="easyui-combogrid" style="width:300px"/>
            </div>
        </form>
    </div>
    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    <div id="perros-dlg-buttons">
        <a id="perros-okBtn" href="#" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveDog()">Guardar</a>
        <a id="perros-cancelBtn" href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#perros-dialog').dialog('close')">Cancelar</a>
    </div>
    
    <script language="javascript">
    
    	// set up operation header content
        $('#Header_Operation').html('<p>Gesti&oacute;n de Perros</p>');
        
        // tell jquery to convert declared elements to jquery easyui Objects
        
        // datos de la tabla de perros
        // - tabla
        $('#perros-datagrid').datagrid({
        	title: 'Gesti&oacute;n de datos de Perros',
        	url: 'database/json/get_dogs.php',
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
                { field:'Guia',     width:40, sortable:true,                 title: 'Nombre del Gu&iacute;a'}
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
        $('#perros-newBtn').linkbutton(); // nuevo perro       
        $('#perros-newBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Dar de alta un nuevo perro en la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        $('#perros-editBtn').linkbutton(); // editar perro      
        $('#perros-editBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Modificar los datos del perro seleccionado</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        $('#perros-delBtn').linkbutton(); // borrar perro     
        $('#perros-delBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Eliminar el perro seleccionado de la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        
        // datos del formulario de nuevo/edit perros
        // - declaracion del formulario
        $('#perros-form').form();
        // - botones
        $('#perros-okBtn').linkbutton();    
        $('#perros-okBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Aceptar datos y actualizar la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        $('#perros-cancelBtn').linkbutton();    
        $('#perros-cancelBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Cancelar edicion. Cerrar ventana</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        // campos del formulario
        $('#perros-dialog').dialog();
        $('#perros-Nombre').validatebox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#perros-Grados_Perro').combobox({
    		panelHeight: 'auto',
        	valueField:'Grado',
        	textField:'Comentarios',
        	method: 'get',
        	mode: 'remote',
        	url:'database/json/enumerate_Grados_Perro.php',
        	// TODO: this should work. study why doesn't
    		onLoadSuccess: function(data){
    			for(var i=0; i<data.length; i++){
    				var row = data[i];
    				// the row with 'selected' property set to true will be acted as the selected row
    				if (row.selected){  
        				// alert('selected value is: '+row.Grado);
    					$(this).combobox('setValue',row.Grado);
    				}
    			}
    		}
        });
        $('#perros-Categorias_Perro').combobox({
			panelHeight: 'auto',
    		valueField:'Categoria',
    		textField:'Observaciones',
    		method: 'get',
    		mode: 'remote',
    		url:'database/json/enumerate_Categorias_Perro.php'
        });
        $('#perros-Guia').combogrid({
			panelWidth: 350,
			panelHeight: 200,
			idField: 'Nombre',
			textField: 'Nombre',
			url: 'database/json/enumerate_GuiasClub.php',
			method: 'get',
			mode: 'remote',
			required: true,
			columns: [[
    			{field:'Nombre',title:'Nombre del gu&iacute;a',width:80,align:'right'},
    			{field:'Club',title:'Club',width:40,align:'right'},
			]],
			multiple: false,
			fitColumns: true,
			selectOnNavigation: false
        });
	</script>