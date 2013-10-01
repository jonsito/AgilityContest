<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE Pruebas -->
    
    <!-- DECLARACION DE LA TABLA -->
    <table id="pruebas-datagrid" class="easyui-datagrid" style="width:800px;height:600px" />
    
    <!-- BARRA DE TAREAS -->
    <div id="pruebas-toolbar">
        <a id="pruebas-newBtn" href="#" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newPrueba()">Nueva prueba</a>
        <a id="pruebas-editBtn" href="#" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editPrueba()">Editar prueba</a>
        <a id="pruebas-delBtn" href="#" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyPrueba()">Borrar prueba</a>
        <input id="pruebas-search" type="text" onchange="doSearchPrueba()"/> 
        <a id="pruebas-searchBtn" href="#" class="easyui-linkbutton" plain="true" iconCls="icon-search" onclick="doSearchPrueba()">Buscar</a>
        <input id="pruebas-openBox" href="#" type="checkbox" value='1' class="easyui-checkbox" onclick="doSearchPrueba()">Incl. Cerradas</input>
    </div>
    
    <!-- FORMULARIO DE DECLARACION Y MODIFICACION DE PRUEBAS -->
    <div id="pruebas-dialog" class="easyui-dialog" style="width:400px;height:450px;padding:10px 20px"
            closed="true" buttons="#pruebas-dlg-buttons">
        <div class="ftitle">Informaci&oacute;n de la prueba</div>
        <form id="pruebas-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre">Denominaci&oacute;n de la prueba:</label>
                <input id="pruebas-Nombre" 
                	name="Nombre" 
                	type="text" 
                	class="easyui-validatebox" 
                	required="true"
                	style="width:325px" />
                <input id="pruebas-Viejo" name="Viejo" type="hidden" /> <!-- used to allow operator change prueba's name -->
            </div>
            <div class="fitem">
                <label for="Club">Club:</label>
                <select id="pruebas-Club" name="Club" class="easyui-combogrid" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Ubicacion">Lugar de realizaci&oacute;n</label>
                <input id="pruebas-Ubicacion" name="Ubicacion" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Triptico">URL del tr&iacute;ptico</label>
                <input id="pruebas-Triptico" name="Triptico" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Cartel">URL del cartel</label>
                <input id="pruebas-Cartel" name="Cartel" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Observaciones">Observaciones:</label>
                <input id="pruebas-Observaciones" name="Observaciones" type="textarea" style="height:50px;width:250px";/>
            </div>
            <div class="fitem">
                <label for="Cerrada">Prueba Cerrada:</label>
                <input id="pruebas-Cerrada" name="Cerrada" class="easyui-checkbox" type="checkbox" />
            </div>
        </form>
    </div>
    
    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    <div id="pruebas-dlg-buttons">
        <a id="pruebas-okBtn" href="#" class="easyui-linkbutton" iconCls="icon-ok" onclick="savePrueba()">Guardar</a>
        <a id="pruebas-cancelBtn" href="#" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#pruebas-dialog').dialog('close')">Cancelar</a>
    </div>

    
    <script language="javascript">
    
    	// set up operation header content
        $('#Header_Operation').html('<p>Creaci&oacute;n y edici&oacute;n de pruebas</p>');
        
        // tell jquery to convert declared elements to jquery easyui Objects
        
        // datos de la tabla de pruebas
        // - tabla
        $('#pruebas-datagrid').datagrid({
        	title: 'Gesti&oacute;n de datos de pruebas',
        	url: 'database/get_pruebas.php',
        	method: 'get',
            toolbar: '#pruebas-toolbar',
            pagination: true,
            rownumbers: true,
            fitColumns: true,
            singleSelect: true,
            view: detailview,
            columns: [[
            	{ field:'Nombre',		width:20, sortable:true,	title: 'Denominacion:' },
            	{ field:'Club',			width:12, sortable:true,	title: 'Club organizador' },
            	{ field:'Ubicacion',	width:22, sortable:true,    title: 'Lugar de la prueba' },
                { field:'Triptico',		width:12, sortable:true,	title: 'URL del Tr&iacute;ptico'},
                { field:'Cartel',		width:12, sortable:true,	title: 'URL del Cartel'},
                { field:'Observaciones',width:10,					title: 'Observaciones'},
                { field:'Cerrada',		width:6, sortable:true,		align: 'center', title: 'Cerrada'}
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:function(index,row) { 
                return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
            },
        	// on double click fireup editor dialog
            onDblClickRow:function() { 
                editPrueba();
            },        
            // especificamos un formateador especial para desplegar la tabla de perros por prueba
            detailFormatter:function(index,row){
                return '<div style="padding:2px"><table id="pruebas-dog-datagrid-' + index + '"></table></div>';
            },
            
            onExpandRow: function(index,row){
            	// - sub tabla de Joirnadas asignadass a una prueba
            	$('#pruebas-dog-datagrid-'+index).datagrid({
            		title: 'Jornadas de que consta la prueba '+row.Nombre,
            		url: 'database/select_JornadasByPrueba.php?Prueba='+row.Nombre,
            		method: 'get',
            		// definimos inline la sub-barra de tareas para que solo aparezca al desplegar el sub formulario
            		toolbar:  [{
                		text: 'Borrar jornada',
                		plain: true,
            			iconCls: 'icon-remove',
            			handler: function(){delJornadaFromPrueba(index,row.Nombre);}
            		},'-',{
                		text: 'A&ntilde;adir jornada',
                		plain: true,
            			iconCls: 'icon-flag',
            			handler: function(){addJornadaToPrueba(index,row.Nombre);}
            		}],
           		    pagination: false,
            	    rownumbers: false,
            	    fitColumns: true,
            	    singleSelect: true,
            	    loadMsg: '',
            	    height: 'auto',
            	    columns: [[
                	    { field:'ID',		width:4, sortable:true,	align:'center', title: 'ID'},
                		{ field:'Fecha',	width:7, sortable:true,	title: 'Fecha:' },
                		{ field:'Hora',		width:7, sortable:false,	title: 'Hora.' },
                		{ field:'Observaciones',width:12, sortable:false,   title: 'Observaciones' },
                		{ field:'A1GI',		width:6, sortable:false,   align:'center', title: 'Ag1GI' },
                		{ field:'A2GI',		width:6, sortable:false,   align:'center', title: 'Ag2GI ' },
                		{ field:'AGII',		width:6, sortable:false,   align:'center', title: 'AgGII ' },
                		{ field:'JGII',		width:6, sortable:false,   align:'center', title: 'JpGII ' },
                		{ field:'AGIII',	width:6, sortable:false,   align:'center', title: 'AgGIII' },
                		{ field:'JGIII',	width:6, sortable:false,   align:'center', title: 'JpGIII' },
                		{ field:'AEq',		width:6, sortable:false,   align:'center', title: 'A Eq. ' },
                		{ field:'JEq',		width:6, sortable:false,   align:'center', title: 'J Eq. ' },
                		{ field:'PreA',		width:6, sortable:false,   align:'center', title: 'PreAg ' },
                		{ field:'K.O.',		width:6, sortable:false,   align:'center', title: 'K.O.  ' },
                		{ field:'Show',		width:6, sortable:false,   align:'center', title: 'Exhib.' },
                		{ field:'Otras',	width:6, sortable:false,   align:'center', title: 'Otras ' },
                	]],
                	// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
                	rowStyler:function(index,row) { 
                	    return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
                	},
                    onResize:function(){
                        $('#pruebas-datagrid').datagrid('fixDetailRowHeight',index);
                    },
                    onLoadSuccess:function(){
                        setTimeout(function(){
                            $('#pruebas-datagrid').datagrid('fixDetailRowHeight',index);
                        },0);
                    } 
            	}); // end of pruebas-dog-datagrid
            	$('#pruebas-datagrid').datagrid('fixDetailRowHeight',index);
            } // end of onExpandRow
        }); // end of pruebas-datagrid
         
        // - botones de la toolbar de la tabla
        $('#pruebas-newBtn').linkbutton(); // nuevo prueba        
        $('#pruebas-newBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Dar de alta una nueva prueba</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        $('#pruebas-editBtn').linkbutton(); // editar prueba         
        $('#pruebas-editBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Editar los datos de la prueba seleccionada</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    	});
        $('#pruebas-delBtn').linkbutton(); // borrar prueba
        $('#pruebas-delBtn').tooltip({
            position: 'top',
            content: '<span style="color:#000">Eliminar la prueba seleccionada</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });

        $('#pruebas-searchBtn').linkbutton(); // buscar prueba
        $('#pruebas-searchBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Buscar pruebas coincidentes con el texto indicado</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        // $('#pruebas-openBox').checkbox(); /* no checkbox defined in easyui */
        $('#pruebas-openBox').tooltip({
            position: 'top',
            content: '<span style="color:#000">Incluir en el listado las pruebas finalizadas</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
        
        // botones de los sub-formularios
        $('#pruebas-addJornadaBtn').linkbutton(); // anyadir de jornadas de la prueba
        $('#pruebas-addJornadaBtn').tooltip({
            position: 'top',
            content: '<span style="color:#000">Crear una nueva jornada para la prueba</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });        
        $('#pruebas-delJornadaBtn').linkbutton(); // eliminar jornadas de la prueba
        $('#pruebas-delJornadaBtn').tooltip({
            position: 'top',
            content: '<span style="color:#000">Eliminar jornadas de una prueba</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });

        
        // datos del formulario de nuevo/edit jornada
        // - declaracion del formulario
        $('#pruebas-form').form();
        // - botones
        $('#pruebas-okBtn').linkbutton();        
        $('#pruebas-okBtn').tooltip({
            position: 'top',
            content: '<span style="color:#000">Aceptar datos y registrarlos en la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
        $('#pruebas-cancelBtn').linkbutton();        
        $('#pruebas-cancelBtn').tooltip({
            position: 'top',
            content: '<span style="color:#000">Anular operaci&oacute;n. Cerrar ventana</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
        
        // campos del formulario
        $('#pruebas-dialog').dialog();
        $('#pruebas-Nombre').validatebox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#pruebas-Triptico').validatebox({
            required: false,
            validType: 'url'
        });
        $('#pruebas-Cartel').validatebox({
            required: false,
            validType: 'url'
        });
        $('#pruebas-Club').combogrid({
			panelWidth: 350,
			panelHeight: 200,
			idField: 'Nombre',
			textField: 'Nombre',
			url: 'database/enumerate_Clubes.php',
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
