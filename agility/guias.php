<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE GUIAS -->
    
    <!-- DECLARACION DE LA TABLA -->
    <table id="guias-datagrid" class="easyui-datagrid" style="width:975px;height:550px">  </table>
    <!-- BARRA DE TAREAS DE LA TABLA DE GUIAS -->
    <div id="guias-toolbar" style="padding:5px 5px 25px 5px">
    	<span style="float:left;">
        	<a id="guias-newBtn" href="#" class="easyui-linkbutton" onclick="newGuia($('#guias-search').val())">Nuevo Gu&iacute;a</a>
        	<a id="guias-editBtn" href="#" class="easyui-linkbutton" onclick="editGuia()">Editar Gu&iacute;a</a>
        	<a id="guias-delBtn" href="#" class="easyui-linkbutton" onclick="deleteGuia()">Borrar gu&iacute;a</a>
    		<input id="guias-search" type="text" value="----- Buscar -----" class="search_textfield"/>
    	</span>
    	<span style="float:right;">
    		<a id="guias-reloadBtn" href="#" class="easyui-linkbutton"">Actualizar</a>
    	</span>
    </div>
    
	<?php include_once("dialogs/dlg_guias.inc"); ?>
	<?php include_once("dialogs/dlg_clubes.inc"); ?>
	<?php include_once("dialogs/dlg_perros.inc"); ?>
	<?php include_once("dialogs/dlg_chperros.inc"); ?>
    
    <script type="text/javascript">
    
    	// set up operation header content
        $('#Header_Operation').html('<p>Gesti&oacute;n de Base de Datos de Gu&iacute;as</p>');
        
        // tell jquery to convert declared elements to jquery easyui Objects
        // datos de la tabla de guias
        // - tabla
        $('#guias-datagrid').datagrid({
        	// propiedades del panel padre asociado
        	fit: false,
        	border: false,
        	closable: false,
        	collapsible: false,
            expansible: false,
        	collapsed: false,
        	title: 'Gesti&oacute;n de datos de Gu&iacute;as',
        	url: 'database/guiaFunctions.php?Operation=select',
        	loadMsg: 'Actualizando lista de Gu&iacute;as...',
        	method: 'get',
            toolbar: '#guias-toolbar',
            pagination: false,
            rownumbers: true,
            singleSelect: true,
            fitColumns: true,
            view: detailview,
            columns: [[
                { field:'ID',			hidden:true },
            	{ field:'Nombre',		width:30, sortable:true,	title: 'Nombre:' },
                { field:'Club',			hidden:true},
                { field:'NombreClub',	width:20, sortable:true,	title: 'Club'},
            	{ field:'Telefono',		width:10, sortable:true,	title: 'Tel&eacute;fono' },
            	{ field:'Email',		width:15, sortable:true,    title: 'Correo Electr&oacute;nico' },
                { field:'Observaciones',width:30,					title: 'Observaciones'}
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:function(idx,row) { 
                return ((idx&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
            },
        	// on double click fireup editor dialog
            onDblClickRow:function() { 
                editGuia();
            },        
            // especificamos un formateador especial para desplegar la tabla de perros por guia
            detailFormatter:function(idx,row){
                return '<div style="padding:2px"><table id="perros-datagrid-' + replaceAll(' ','_',row.ID) + '"></table></div>';
            },
            onExpandRow: function(idx,row) { showPerrosByGuia(idx,row); },

        }); // end of guias-datagrid

        // activa teclas up/down para navegar por el panel
        $('#guias-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
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
			
        	var t = $('#guias-datagrid');
            switch(e.keyCode){
            case 38:	/* Up */	selectRow(t,true); return false;
            case 40:    /* Down */	selectRow(t,false); return false;
            case 13:	/* Enter */	editGuia(); return false;
            case 45:	/* Insert */ newGuia($('#guias-search').val()); return false;
            case 46:	/* Supr */	deleteGuia(); return false;
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
        $('#guias-newBtn').linkbutton({iconCls:'icon-users',plain:true}).tooltip({ // nuevo guia 
        	position: 'top',
        	content: '<span style="color:#000">Dar de alta un nuevo gu&iacute;a en la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
        $('#guias-editBtn').linkbutton({iconCls:'icon-edit',plain:true}).tooltip({ // editar guia  
        	position: 'top',
        	content: '<span style="color:#000">Editar los datos del gu&iacute;a seleccionado</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
    	});
        $('#guias-delBtn').linkbutton({iconCls:'icon-trash',plain:true}).tooltip({ // borrar guia
            position: 'top',
            content: '<span style="color:#000">Borrar el gu&iacute;a seleccionado de la BBDD</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
        });
        $('#guias-reloadBtn').linkbutton({plain:true,iconCls:'icon-reload'}); // borrar perro     
        $('#guias-reloadBtn').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Borrar casilla de busqueda y actualizar tabla</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});
    	$('#guias-reloadBtn').on("click", function () {
        	// clear selection and reload table
    		$('#guias-search').val('----- Buscar -----');
            $('#guias-datagrid').datagrid('load',{ where: '' });
    	});
        $("#guias-search").keydown(function(event){
            if(event.keyCode != 13) return;
          	// reload data adding search criteria
            $('#guias-datagrid').datagrid('load',{
                where: $('#guias-search').val()
            });
        });
        $('#guias-search').tooltip({
        	position: 'top',
        	content: '<span style="color:#000">Buscar entradas que contengan el texto dado</span>',
        	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});
        	}
    	});

		// mostrar los perros asociados a un guia
        function showPerrosByGuia(index,guia){
        	// - sub tabla de perros asignados a un guia
        	$('#perros-datagrid-'+replaceAll(' ','_',guia.ID)).datagrid({
            	width: 875,
        		title: 'Perros registrados a nombre de '+guia.Nombre,
       		    pagination: false,
        	    rownumbers: false,
        	    fitColumns: true,
        	    singleSelect: true,
        	    loadMsg: 'Loading list of dogs',
        	    height: 'auto',
        		url: 'database/dogFunctions.php',
        		queryParams: { Operation: 'getbyguia', Guia: guia.ID },
        		method: 'get',
        		// definimos inline la sub-barra de tareas para que solo aparezca al desplegar el sub formulario
        		// toolbar: '#perrosbyguia-toolbar', 
				toolbar:  [{
					id: 'perrosbyguia-newBtn',
					text: 'Asignar perro',
					plain: true,
					iconCls: 'icon-dog',
					handler: function(){assignPerroToGuia(guia);},
				},{
					id: 'perrosbyguia-editBtn',
					text: 'Editar datos',
					plain: true,
					iconCls: 'icon-edit',
					handler: function(){editPerroFromGuia(guia);}
				},{
					id: 'perrosbyguia-delBtn',
					text: 'Desasignar perro',
					plain: true,
					iconCls: 'icon-trash',
					handler: function(){delPerroFromGuia(guia);}
				}],
        	    columns: [[
            	    { field:'ID',	width:15, sortable:true,	title: 'ID'},
            		{ field:'Nombre',	width:30, sortable:true,	title: 'Nombre:' },
            		{ field:'Categoria',width:15, sortable:false,	title: 'Cat.' },
            		{ field:'Grado',	width:25, sortable:false,   title: 'Grado' },
            		{ field:'Raza',		width:25, sortable:false,   title: 'Raza' },
            		{ field:'LOE_RRC',	width:25, sortable:true,    title: 'LOE / RRC' },
            		{ field:'Licencia',	width:25, sortable:true,    title: 'Licencia' }
            	]],
            	// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            	rowStyler:function(idx,row) { 
            	    return ((idx&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
            	},
            	// on double click fireup editor dialog
                onDblClickRow:function(idx,row) { //idx: selected row index; row selected row data
                    editPerroFromGuia(guia);
                },
                onResize:function(){
                    $('#guias-datagrid').datagrid('fixDetailRowHeight',index);
                },
                onLoadSuccess:function(){
                    setTimeout(function(){
                        $('#guias-datagrid').datagrid('fixDetailRowHeight',index);
                    },0);
                } 
        	}); // end of perrosbyguia-datagrid-Nombre_del_Guia
        	$('#guias-datagrid').datagrid('fixDetailRowHeight',index);

            // botones de los sub-formularios
            $('#perrosbyguia-newBtn').linkbutton().tooltip({ // anyadir nuevo perro al guia
                position: 'top',
                content: '<span style="color:#000">Asignar un nuevo perro al guia</span>',
            	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
            });     
            $('#perrosbyguia-delBtn').linkbutton().tooltip({ // desasignar perro al guia
                position: 'top',
                content: '<span style="color:#000">Eliminar asignaci&oacute;n del perro al gu&iacute;a</span>',
            	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
            });        
            $('#perrosbyguia-editBtn').linkbutton().tooltip({ // editar datos del perro asignado al guia
                position: 'top',
                content: '<span style="color:#000">Editar los datos del perro asignado</span>',
            	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
            });
            $('#perrosbyguia-assignBtn').linkbutton().tooltip({ // asignar un perro de la bbdd a este guia
                position: 'top',
                content: '<span style="color:#000">Reasignar un perro existente a este gu&iacute;a</span>',
            	onShow: function(){	$(this).tooltip('tip').css({backgroundColor: '#ef0',borderColor: '#444'	});}
            });
        } // end of showPerrosByGuia
	</script>
