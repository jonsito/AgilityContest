<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE CLUBES -->
    
    <!-- DECLARACION DE LA TABLA -->
    <table id="clubes-datagrid" class="easyui-datagrid" style="width:975px;height:550px;"></table>
    
	<!-- BARRA DE TAREAS DE LA TABLA DE CLUBES-->
    <div id="clubes-toolbar" style="padding:5px 5px 25px 5px;">
    	<span style="float:left;">
    		<a id="clubes-newBtn" href="#" class="easyui-linkbutton" 
    			iconCls="icon-flag" plain=true
    			onclick="newClub($('#clubes-search').val())">Nuevo Club</a>
    		<a id="clubes-editBtn" href="#" class="easyui-linkbutton" 
    			iconCls="icon-edit" plain=true
    			onclick="editClub('#clubes-datagrid')">Editar Club</a>
    		<a id="clubes-delBtn" href="#" class="easyui-linkbutton" 
    			iconCls="icon-trash" plain=true
    			onclick="deleteClub('#clubes-datagrid')">Borrar Club</a>
    		<input id="clubes-search" type="text" value="---- Buscar ----" class="search_textfield"/>
    	</span>
    	<span style="float:right;">
    		<a id="clubes-reloadBtn" href="#" class="easyui-linkbutton"
    		plain="true" iconCls="icon-reload"
    		onClick="
        	// clear selection and reload table
    		$('#clubes-search').val('---- Buscar ----');
            $('#clubes-datagrid').datagrid('load',{ where: '' });">Actualizar</a>
    	</span>
    </div>   

    <?php include_once("dialogs/dlg_clubes.inc")?>
    <?php include_once("dialogs/dlg_guias.inc")?>
    <?php include_once("dialogs/dlg_chguias.inc")?>
    <?php include_once("dialogs/dlg_perros.inc")?>
    <?php include_once("dialogs/dlg_chperros.inc")?>

    <script type="text/javascript">

    	// set up operation header content
        $('#Header_Operation').html('<p>Gesti&oacute;n de Base de Datos de Clubes</p>');
        
        // tell jquery to convert declared elements to jquery easyui Objects
        
        // datos de la tabla de clubes
        // - tabla
        $('#clubes-datagrid').datagrid({
        	// propiedades del panel padre asociado
        	fit: false,
        	border: false,
        	closable: false,
        	collapsible: false,
            expansible: false,
        	collapsed: false,
        	title: 'Gesti&oacute;n de datos de Clubes',
        	url: 'database/clubFunctions.php?Operation=select',
        	loadMsg: 'Actualizando lista de Clubes ...',
        	method: 'get',
            toolbar: '#clubes-toolbar',
            pagination: false,
            rownumbers: true,
            fitColumns: true,
            singleSelect: true,
            view: scrollview,
            pageSize: 50,
            columns: [[
                  	{ field:'ID',			hidden:true},
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
                editClub('#clubes-datagrid');
            },        
            // especificamos un formateador especial para desplegar la tabla de guias por club
            detailFormatter:function(index,club){
                return '<div style="padding:2px"><table class="easyui-datagrid"	id="clubes-guias-datagrid-' + replaceAll(' ','_',club.ID) + '"></table></div>';
            },
            onExpandRow: function(idx,club) { showGuiasByClub(idx,club); }
        }); // end of '#clubes-datagrid' declaration

        // activa teclas up/down para navegar por el panel
        $('#clubes-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
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
			
        	var t = $('#clubes-datagrid');
            switch(e.keyCode){
            case 38:	/* Up */	selectRow(t,true); return false;
            case 40:    /* Down */	selectRow(t,false); return false;
            case 13:	/* Enter */	editClub('#clubes-datagrid'); return false;
            case 45:	/* Insert */ newClub($('#clubes-search').val()); return false;
            case 46:	/* Supr */	deleteClub('#clubes-datagrid'); return false;
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
		addTooltip($('#clubes-newBtn').linkbutton(),"Dar de alta un nuevo club en la BBDD"); 
		addTooltip($('#clubes-editBtn').linkbutton(),"Editar los datos del club seleccionado");
		addTooltip($('#clubes-delBtn').linkbutton(),"Borrar el club seleccionado de la BBDD");
		addTooltip($('#clubes-reloadBtn').linkbutton(),"Borrar casilla de busqueda y actualizar tabla");
		addTooltip($('#clubes-search'),"Mostrar clubes que coincidan con el criterio de busqueda");
        // - activar la tecla "Enter" en la casilla de busqueda
        $("#clubes-search").keydown(function(event){
            if(event.keyCode != 13) return;
          	// reload data adding search criteria
            $('#clubes-datagrid').datagrid('load',{
                where: $('#clubes-search').val()
            });
        });
        
    	
        function showGuiasByClub(index,club){
        	// - sub tabla de guias inscritos en un club
        	var mySelf='#clubes-guias-datagrid-'+replaceAll(' ','_',club.ID);
        	$(mySelf).datagrid({
            	width: 875,
            	fit:false,
       		    pagination: false,
        	    rownumbers: false,
        	    fitColumns: true,
        	    singleSelect: true,
                view: detailview,
        	    // height: 'auto',
        		title: 'Gu&iacute;as inscritos en el club '+club.Nombre,
        	    loadMsg: 'Cargando lista de guias....',
        		url: 'database/guiaFunctions.php?Operation=getbyclub&Club='+club.ID,
        		method: 'get',
        		// definimos inline la sub-barra de tareas para que solo aparezca al desplegar el sub formulario
        		toolbar: [{
            		id: 'guiasByClub-newBtn'+club.ID,
            		text: 'Asociar gu&iacute;a',
            		plain: true,
        			iconCls: 'icon-users',
        			handler: function(){ assignGuiaToClub( mySelf,club, function () { $(mySelf).datagrid('reload'); } ); }
        		},{
            		id: 'guiasByClub-editBtn'+club.ID,
            		text: 'Editar gu&iacute;a',
            		plain: true,
        			iconCls: 'icon-edit',
        			handler: function(){ editGuiaFromClub(mySelf,club, function () { $(mySelf).datagrid('reload'); } );}
        		},{
            		id: 'guiasByClub-delBtn'+club.ID,
            		text: 'Des-asociar gu&iacute;a',
            		plain: true,
        			iconCls: 'icon-remove',
        			handler: function(){ delGuiaFromClub(mySelf,club, function () { $(mySelf).datagrid('reload'); } );}
        		}],
        	    columns: [[
        	        { field:'ID',			hidden:true },	
        	    	{ field:'Nombre',		width:30, sortable:true,	title: 'Nombre:' },
        	    	{ field:'Telefono',	width:15, sortable:true,	title: 'Tel&eacute;fono' },
        	    	{ field:'Email',		width:25, sortable:true,    title: 'Correo Electr&oacute;nico' },
        	    	{ field:'Observaciones',width:15,					title: 'Observaciones'}
            	]],
            	// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            	rowStyler:function(idx,row) { 
            	    return ((idx&0x01)==0)?'background-color:#ccc;':'background-color:#eee;';
            	},
                onResize:function(){
                    $('#clubes-datagrid').datagrid('fixDetailRowHeight',index);
                },
                onLoadSuccess:function(){
                    setTimeout(function(){
                        $('#clubes-datagrid').datagrid('fixDetailRowHeight',index);
                    },0);
                },
            	// on double click fireup editor dialog
                onDblClickRow:function(idx,row) { //idx: selected row index; row selected row data
                    editGuiaFromClub(mySelf,club,	function () { $(mySelf).datagrid('reload'); } );
                },
                // especificamos un formateador especial para desplegar la tabla de perros por guia
                detailFormatter:function(index,guia){
                    return '<div style="padding:2px"><table class="easyui-datagrid" id="clubes-guias-perros-datagrid-' + replaceAll(' ','_',guia.ID) + '"></table></div>';
                },
                
                onExpandRow: function(idx,guia) { showPerrosByGuiaByClub(idx,guia,club); },
                /* end of clubes-guias-dog subtable */
                onResize:function(){
                    $('#clubes-datagrid').datagrid('fixDetailRowHeight',index);
                },
                onLoadSuccess:function(){
                    setTimeout(function(){
                        $('#clubes-datagrid').datagrid('fixDetailRowHeight',index);
                    },0);
                } 
        	}); // end of '#clubes-guias-datagrid' declaration
        	$('#clubes-datagrid').datagrid('fixDetailRowHeight',index);
			// tooltips de los sub-formularios
			addTooltip($('#guiasByClub-newBtn'+club.ID).linkbutton(),"Asociar/Crear nuevo guia en el club '"+club.Nombre+"'"); 
			addTooltip($('#guiasByClub-editBtn'+club.ID).linkbutton(),"Editar datos del gu&iacute;a seleccionado del club '"+club.Nombre+"'"); 
			addTooltip($('#guiasByClub-delBtn'+club.ID).linkbutton(),"Desasociar al gu&iacute;a seleccionado del club '"+club.Nombre+"'");
            	
        } // end of "showGuiasByClub"
        
		// mostrar los perros asociados a un guia
        function showPerrosByGuiaByClub(index,guia,club){
            var parent='#clubes-guias-datagrid-'+replaceAll(' ','_',club.ID);
            var mySelf='#clubes-guias-perros-datagrid-'+replaceAll(' ','_',guia.ID);
        	// - sub tabla de perros asignados a un guia
        	$(mySelf).datagrid({
            	fit:false,
            	width: 850,
       		    pagination: false,
        	    rownumbers: false,
        	    fitColumns: true,
        	    singleSelect: true,
        	    // height: 'auto',
        	    loadMsg: 'Loading list of dogs',
        		title: 'Perros registrados a nombre de '+guia.Nombre,
        		url: 'database/dogFunctions.php',
        		queryParams: { Operation: 'getbyguia', Guia: guia.ID },
        		method: 'get',
        		// definimos inline la sub-barra de tareas para que solo aparezca al desplegar el sub formulario
        		// toolbar: '#perrosbyguia-toolbar', 
				toolbar:  [{
					id: 'perrosByGuiaByClub-newBtn'+guia.ID+'_'+club.ID,
					text: 'Asignar perro',
					plain: true,
					iconCls: 'icon-dog',
					handler: function(){assignPerroToGuia(guia, function () { $(mySelf).datagrid('reload'); } );},
				},{
					id: 'perrosByGuiaByClub-editBtn'+guia.ID+'_'+club.ID,
					text: 'Editar datos',
					plain: true,
					iconCls: 'icon-edit',
					handler: function(){editPerroFromGuia(mySelf,guia, function () { $(mySelf).datagrid('reload'); } );}
				},{
					id: 'perrosByGuiaByClub-delBtn'+guia.ID+'_'+club.ID,
					text: 'Desasignar perro',
					plain: true,
					iconCls: 'icon-remove',
					handler: function(){delPerroFromGuia(mySelf,guia, function () { $(mySelf).datagrid('reload'); } );}
				}],
        	    columns: [[
            	    { field:'ID',		width:15, sortable:true,	title: 'ID'},
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
                    editPerroFromGuia(mySelf,guia, function () { $(mySelf).datagrid('reload'); } );
                },
                onResize:function(){
                    $(parent).datagrid('fixDetailRowHeight',index);
                },
                onLoadSuccess:function(){
                    setTimeout(function(){
                        $(parent).datagrid('fixDetailRowHeight',index);
                    },0);
                } 
        	}); // end of perrosbyguia-datagrid-Nombre_del_Guia
        	$(parent).datagrid('fixDetailRowHeight',index);

			// tooltips de los sub-formularios
			addTooltip($('#perrosByGuiaByClub-newBtn'+guia.ID+'_'+club.ID).linkbutton(),"Crear/Asignar un nuevo perro a '"+guia.Nombre+"'"); 
			addTooltip($('#perrosByGuiaByClub-editBtn'+guia.ID+'_'+club.ID).linkbutton(),"Editar los datos del perro asignado a '"+guia.Nombre+"'"); 
			addTooltip($('#perrosByGuiaByClub-delBtn'+guia.ID+'_'+club.ID).linkbutton(),"Eliminar asignaci&oacute;n del perro a '"+guia.Nombre+"'");
        } // end of showPerrosByGuia
	</script>

