<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE GUIAS -->
    
    <!-- DECLARACION DE LA TABLA -->
    <table id="guias-datagrid" class="easyui-datagrid" style="width:800px;height:400px" />
    
    <!-- BARRA DE TAREAS -->
    <div id="guias-toolbar">
        <a id="guias-newBtn" href="#" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newGuia()">Nuevo Gu&iacute;a</a>
        <a id="guias-editBtn" href="#" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editGuia()">Editar Gu&iacute;a</a>
        <a id="guias-delBtn" href="#" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyGuia()">Borrar gu&iacute;a</a>
        <input id="guias-search" type="text" onchange="doSearchGuia()"/> 
        <a id="guias-searchBtn" href="#" class="easyui-linkbutton" plain="true" iconCls="icon-search" onclick="doSearchGuia()">Buscar</a>
    </div>
    
    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE GUIAS -->
    <div id="guias-dialog" class="easyui-dialog" style="width:450px;height:350px;padding:10px 20px"
            closed="true" buttons="#guias-dlg-buttons">
        <div class="ftitle">Informaci&oacute;n del guia</div>
        <form id="guias-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input id="guias-Nombre" 
                	name="Nombre" 
                	type="text" 
                	class="easyui-validatebox" 
                	required="true"
                	style="width:300px" />
                <input id="guias-Viejo" name="Viejo" type="hidden" /> <!-- used to allow operator change guia's name -->
            </div>
            <div class="fitem">
                <label for="Email">Correo electr&oacute;nico:</label>
                <input id="guias-Email" name="Email" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Telefono">Tel&eacute;fono:</label>
                <input id="guias-Telefono" class="easyui-validatebox" name="Telefono" type="text" />
            </div>
            <div class="fitem">
                <label for="Club">Club:</label>
                <select id="guias-Clubes" name="Club" class="easyui-combogrid" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Observaciones">Observaciones:</label>
                <input id="guias-Observaciones" name="Observaciones" type="textarea" style="height:50px;width:300px";/>
            </div>
        </form>
    </div>
    
    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    <div id="guias-dlg-buttons">
        <a id="guias-okBtn" href="#" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveGuia()">Guardar</a>
        <a id="guias-cancelBtn" href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#guias-dialog').dialog('close')">Cancelar</a>
    </div>

    
    <script language="javascript">
    
    	// set up operation header content
        $('#Header_Operation').html('<p>Gesti&oacute;n de Base de Datos de Gu&iacute;as</p>');
        
        // tell jquery to convert declared elements to jquery easyui Objects
        
        // datos de la tabla de guias
        // - tabla
        $('#guias-datagrid').datagrid({
        	title: 'Gesti&oacute;n de datos de Gu&iacute;as',
        	url: 'database/json/get_guias.php',
        	method: 'get',
            toolbar: '#guias-toolbar',
            pagination: true,
            rownumbers: true,
            fitColumns: true,
            singleSelect: true,
            view: detailview,
            columns: [[
            	{ field:'Nombre',		width:30, sortable:true,	title: 'Nombre:' },
            	{ field:'Telefono',		width:15, sortable:true,	title: 'Tel&eacute;fono' },
            	{ field:'Email',		width:25, sortable:true,    title: 'Correo Electr&oacute;nico' },
                { field:'Club',			width:15, sortable:true,	title: 'Club'},
                { field:'Observaciones',width:15,					title: 'Observaciones'}
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:function(index,row) { 
                return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
            },
        	// on double click fireup editor dialog
            onDblClickRow:function() { 
                editGuia();
            },        
            // especificamos un formateador especial para desplegar la tabla de perros por guia
            detailFormatter:function(index,row){
                return '<div style="padding:2px"><table id="guias-dog-datagrid-' + index + '"></table></div>';
            },
            
            onExpandRow: function(index,row){
            	// - sub tabla de perros asignados a un guia
            	$('#guias-dog-datagrid-'+index).datagrid({
            		title: 'Perros asignados al Gu&iacute;a',
            		url: 'database/json/enumerate_PerrosByGuia.php?Guia='+row.Nombre,
            		method: 'get',
            		// definimos inline la sub-barra de tareas para que solo aparezca al desplegar el sub formulario
            		toolbar:  [{
                		text: 'Borrar perro',
                		plain: true,
            			iconCls: 'icon-remove',
            			handler: function(){delPerroFromGuia();}
            		},'-',{
                		text: 'A&ntilde;adir perro',
                		plain: true,
            			iconCls: 'icon-dog',
            			handler: function(){addPerroToGuia(row.Nombre);}
            		}],
           		    pagination: false,
            	    rownumbers: false,
            	    fitColumns: true,
            	    singleSelect: true,
            	    loadMsg: '',
            	    height: 'auto',
            	    columns: [[
                	    { field:'Dorsal',	width:15, sortable:true,	title: 'Dorsal'},
                		{ field:'Nombre',	width:30, sortable:true,	title: 'Nombre:' },
                		{ field:'Categoria',width:15, sortable:false,	title: 'Cat.' },
                		{ field:'Grado',	width:25, sortable:false,   title: 'Grado' },
                		{ field:'Raza',		width:25, sortable:false,   title: 'Raza' },
                		{ field:'LOE_RRC',	width:25, sortable:true,    title: 'LOE / RRC' },
                		{ field:'Licencia',	width:25, sortable:true,    title: 'Licencia' }
                	]],
                	// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
                	rowStyler:function(index,row) { 
                	    return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
                	},
                    onResize:function(){
                        $('#guias-datagrid').datagrid('fixDetailRowHeight',index);
                    },
                    onLoadSuccess:function(){
                        setTimeout(function(){
                            $('#guias-datagrid').datagrid('fixDetailRowHeight',index);
                        },0);
                    }
            	});
            	$('#guias-datagrid').datagrid('fixDetailRowHeight',index);
            }
        });
         
        // - botones de la toolbar de la tabla
        $('#guias-newBtn').linkbutton(); // nuevo guia        
        $('#guias-newBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Dar de alta un nuevo gu&iacute;a en la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        $('#guias-editBtn').linkbutton(); // editar guia         
        $('#guias-editBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Editar los datos del gu&iacute;a seleccionado</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    	});
        $('#guias-delBtn').linkbutton(); // borrar guia
        $('#guias-delBtn').tooltip({
            position: 'top',
            content: '<span style="color:#000">Borrar el gu&iacute;a seleccionado de la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });

        $('#guias-searchBtn').linkbutton(); // buscar datos del guia

        // botones de los sub-formularios
        $('#guias-addPerroBtn').linkbutton(); // lista de perros del guia
        $('#guias-addPerroBtn').tooltip({
            position: 'top',
            content: '<span style="color:#000">Asignar un nuevo perro al guia</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });        
        $('#guias-delPerroBtn').linkbutton(); // lista de perros del guia
        $('#guias-delPerroBtn').tooltip({
            position: 'top',
            content: '<span style="color:#000">Eliminar asignaci&oacute;n del perro al gu&iacute;a</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });

        
        // datos del formulario de nuevo/edit guia
        // - declaracion del formulario
        $('#guias-form').form();
        // - botones
        $('#guias-okBtn').linkbutton();        
        $('#guias-okBtn').tooltip({
            position: 'top',
            content: '<span style="color:#000">Aceptar datos y registrarlos en la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
        $('#guias-cancelBtn').linkbutton();        
        $('#guias-cancelBtn').tooltip({
            position: 'top',
            content: '<span style="color:#000">Anular operaci&oacute;n. Cerrar ventana</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
        
        // campos del formulario
        $('#guias-dialog').dialog();
        // $('#guias-Practicas').checkbox(); // checkboxes doesn't exist in easyui ?
        // $('#guias-Internacional').checkbox(); // checkboxes doesn't exist in easyui ?
        $('#guias-Nombre').validatebox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#guias-Email').validatebox({
            required: false,
            validType: 'email'
        });
        $('#guias-Clubes').combogrid({
			panelWidth: 350,
			panelHeight: 200,
			idField: 'Nombre',
			textField: 'Nombre',
			url: 'database/json/enumerate_Clubes.php',
			method: 'get',
			mode: 'remote',
			required: true,
			columns: [[
    			{field:'Nombre',title:'Nombre del club',width:80,align:'right'},
    			{field:'Provincia',title:'Provincia',width:40,align:'right'},
			]],
			multiple: false,
			fitColumns: true,
			selectOnNavigation: false
        });

 
	</script>
