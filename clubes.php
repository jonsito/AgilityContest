<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE CLUBES -->
    
    <!-- DECLARACION DE LA TABLA -->
    <table id="clubes-datagrid" class="easyui-datagrid"></table>
	<!-- BARRA DE TAREAS -->
    <div id="clubes-toolbar">
        <a id="clubes-newBtn" href="#" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newClub()">Nuevo club</a>
        <a id="clubes-editBtn" href="#" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editClub()">Editar club</a>
        <a id="clubes-delBtn" href="#" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyClub()">Borrar club</a>
        <input id="clubes-search" type="text" onchange="doSearchClub()"/> 
        <a id="clubes-searchBtn" href="#" class="easyui-linkbutton" plain="true" iconCls="icon-search" onclick="doSearchClub()">Buscar</a>
    </div>   

    <?php include_once("dialogs/dlg_clubes.inc")?>

    
    <script type="text/javascript">

    	// set up operation header content
        $('#Header_Operation').html('<p>Gesti&oacute;n de Base de Datos de Clubes</p>');
        
        // tell jquery to convert declared elements to jquery easyui Objects
        
        // datos de la tabla de clubes
        // - tabla
        $('#clubes-datagrid').datagrid({
        	title: 'Gesti&oacute;n de datos de Clubes',
        	url: 'database/get_clubes.php',
        	method: 'get',
            toolbar: '#clubes-toolbar',
            pagination: true,
            rownumbers: true,
            fitColumns: true,
            singleSelect: true,
            view: detailview,
            columns: [[
               	    { field:'Nombre',		width:10, sortable:true,	title: 'Nombre:'},
            		{ field:'Direccion1',	width:15, sortable:true,	title: 'Direcci&oacute;n 1:' },
            		{ field:'Direccion2',	width:10, sortable:false,	title: 'Direcci&oacute;n 2' },
            		{ field:'Provincia',	width:7, sortable:false,   title: 'Provincia' },
            		{ field:'Contacto1',	width:10, sortable:false,   title: 'Contacto 1' },
            		{ field:'Contacto2',	width:5, sortable:true,    title: 'Contacto 2' },
            		{ field:'Contacto3',	width:5, sortable:true,    title: 'Contacto 3' },
            		{ field:'GPS',			width:7, sortable:true,    title: 'GPS' },
            		{ field:'Web',			width:5, sortable:true,    title: 'Direcci&oacute;n Web' },
            		{ field:'Email',		width:5, sortable:true,    title: 'Correo Electr&oacute;nico' },
            		{ field:'Facebook',		width:2, sortable:true,    title: 'Facebook' },
            		{ field:'Google',		width:2, sortable:true,    title: 'Google +' },
            		{ field:'Twitter',		width:2, sortable:true,    title: 'Twitter' },
            		// { field:'Logo',			width:2, sortable:true,    title: 'Logo club' },
            		//{ field:'Observaciones',width:2, sortable:true,    title: 'Observaciones' },
            		{ field:'Baja',			width:2, sortable:true,    title: 'Baja' }
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:function(index,row) { 
                return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
            },
        	// on double click fireup editor dialog
            onDblClickRow:function() { 
                editClub();
            },        
            // especificamos un formateador especial para desplegar la tabla de guias por club
            detailFormatter:function(index,row){
                return '<div style="padding:2px"><table class="easyui-datagrid" id="clubes-guias-datagrid-' + index + '"></table></div>';
            },
            
            onExpandRow: function(index,row){
            	// - sub tabla de guias inscritos en un club
            	$('#clubes-guias-datagrid-'+index).datagrid({
            		title: 'Gu&iacute;as inscritos en el club '+row.Nombre,
            		url: 'database/select_GuiasByClub.php?Club='+row.Nombre,
            		method: 'get',
            		// definimos inline la sub-barra de tareas para que solo aparezca al desplegar el sub formulario
            		toolbar:  [{
                		text: 'Borrar gu&iacute;a',
                		plain: true,
            			iconCls: 'icon-remove',
            			handler: function(){delGuiaFromClub(index,row.Nombre);}
            		},'-',{
                		text: 'A&ntilde;adir gu&iacute;a',
                		plain: true,
            			iconCls: 'icon-users',
            			handler: function(){addGuiaToClub(index,row.Nombre);}
            		}],
           		    pagination: false,
            	    rownumbers: false,
            	    fitColumns: true,
            	    singleSelect: true,
                    view: detailview,
            	    loadMsg: 'Cargando lista de guias....',
            	    height: 'auto',
            	    columns: [[
            	          { field:'Nombre',		width:30, sortable:true,	title: 'Nombre:' },
            	          { field:'Telefono',	width:15, sortable:true,	title: 'Tel&eacute;fono' },
            	          { field:'Email',		width:25, sortable:true,    title: 'Correo Electr&oacute;nico' },
            	          { field:'Observaciones',width:15,					title: 'Observaciones'}
                	]],
                	// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
                	rowStyler:function(index,row) { 
                	    return ((index&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
                	},
                    onResize:function(){
                        $('#clubes-datagrid').datagrid('fixDetailRowHeight',index);
                    },
                    onLoadSuccess:function(){
                        setTimeout(function(){
                            $('#clubes-datagrid').datagrid('fixDetailRowHeight',index);
                        },0);
                    },
                    
					/* start of clubes-guias-dog subtable */
                    
                    // especificamos un formateador especial para desplegar la tabla de perros por guia
                    detailFormatter:function(index,row){
                        return '<div style="padding:5px"><table class="easyui-datagrid" id="guias-dog-datagrid-' + index + '"></table></div>';
                    },
                    
                    onExpandRow: function(guiaindex,row){
                    	// - sub tabla de perros asignados a un guia
                    	$('#guias-dog-datagrid-'+guiaindex).datagrid({
                    		title: 'Perros registrados a nombre de '+row.Nombre,
                    		url: 'database/select_PerrosByGuia.php?Guia='+row.Nombre,
                    		method: 'get',
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
                                $('#clubes-guias-datagrid-'+index).datagrid('fixDetailRowHeight',index+guiaindex);
                            },
                            onLoadSuccess:function(){
                                setTimeout(function(){
                                    $('#clubes-guias-datagrid-'+index).datagrid('fixDetailRowHeight',index+guiaindex);
                                },0);
                            } 
                    	}); // end of guias-dog-datagrid
                    	$('#clubes-guias-datagrid-'+index).datagrid('fixDetailRowHeight',index+guiaindex);
                    } // end of onExpandRow
                    
 				/* end of clubes-guias-dog subtable */
                    
            	}); // end of '#clubes-guias-datagrid' declaration
            	$('#clubes-datagrid').datagrid('fixDetailRowHeight',index);
            } // end of "onExpand" 
        }); // end of '#clubes-datagrid' declaration
         
        // - botones de la toolbar de la tabla
        $('#clubes-newBtn').linkbutton(); // nuevo club        
        $('#clubes-newBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Dar de alta un nuevo club en la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        $('#clubes-editBtn').linkbutton(); // editar club         
        $('#clubes-editBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Editar los datos del club seleccionado</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    	});
        $('#clubes-delBtn').linkbutton(); // borrar club
        $('#clubes-delBtn').tooltip({
            position: 'top',
            content: '<span style="color:#000">Borrar el club seleccionado de la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });

        $('#clubes-searchBtn').linkbutton(); // buscar clubes que coincidan
        $('#clubes-searchBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Buscar entradas que contengan el texto indicado</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
    	
        //** botones de los sub-formularios
        $('#clubes-addGuiaBtn').linkbutton(); // anyadir entrada a lalista de guias del club
        $('#clubes-addGuiaBtn').tooltip({
            position: 'top',
            content: '<span style="color:#000">Asignar un nuevo perro al guia</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });        
        $('#clubes-delGuiaBtn').linkbutton(); // eliminar entrada lista de guias del club
        $('#clubes-delGuiaBtn').tooltip({
            position: 'top',
            content: '<span style="color:#000">Eliminar asignaci&oacute;n del perro al gu&iacute;a</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
	</script>

