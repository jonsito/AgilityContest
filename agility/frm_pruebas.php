<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE Pruebas -->
    
    <!-- DECLARACION DE LA TABLA -->
    <table id="pruebas-datagrid" style="width:975px;height:550px"></table>     
    <!-- BARRA DE TAREAS DE LA TABLA DE PRUEBAS-->
    <div id="pruebas-toolbar" style="padding:5px 5px 35px 5px;">
    	<span style="float:left;">
    	    <a id="pruebas-newBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls:'icon-add'"
    	    	onclick="newPrueba('#pruebas-search')">Nueva prueba</a>
    	    <a id="pruebas-editBtn" href="#" class="easyui-linkbutton"
    	    	data-options="iconCls:'icon-edit'"
    	    	onclick="editPrueba('#pruebas-datagrid')">Editar prueba</a>
    	    <a id="pruebas-delBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls:'icon-remove'"
    	    	onclick="deletePrueba('#pruebas-datagrid')">Borrar prueba</a>
    		<input id="pruebas-search" type="text" value="---- Buscar ----" class="search_textfield"/>
    	    <input id="pruebas-openBox" type="checkbox" value="1" class="easyui-checkbox"
    	    	data-options="iconCls:'icon-search'" 
    	    	onclick="doSearchPrueba()"/>Incl. Cerradas
    	</span>
    	<span style="float:right;">
    		<a id="pruebas-reloadBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls:'icon-reload'"
    	    	onclick="$('#pruebas-datagrid').datagrid('reload')">Actualizar</a>
    	</span>
    </div>
    
 	<?php include_once("dialogs/dlg_clubes.inc");?>
 	<?php include_once("dialogs/dlg_pruebas.inc");?>
 	<?php include_once("dialogs/dlg_jornadas.inc");?>

    <script type="text/javascript">
    
    	// set up operation header content
        $('#Header_Operation').html('<p>Creaci&oacute;n y edici&oacute;n de pruebas</p>');
        
        // datos de la tabla de pruebas
        // - tabla
        $('#pruebas-datagrid').datagrid({

        	// propiedades del panel padre asociado
        	fit: false,
        	border: false,
        	closable: false,
        	collapsible: false,
            expansible: false,
        	collapsed: false,        	
        	title: 'Gesti&oacute;n de datos de pruebas',
        	url: 'database/pruebaFunctions.php?Operation=select',
        	loadMsg: 'Actualizando lista de Clubes ...',
        	method: 'get',
            toolbar: '#pruebas-toolbar',
            pagination: false,
            rownumbers: false,
            fitColumns: true,
            singleSelect: true,
            view: scrollview,
            pageSize: 50,
            columns: [[
                { field:'ID', hidden:true }, // primary key
            	{ field:'Nombre',		width:20,	sortable:true,	title:'Nombre de la prueba:' },
            	{ field:'Club',			hidden:true },
            	{ field:'NombreClub',	width:15,	sortable:true,	title:'Club organizador' },
            	{ field:'Ubicacion',	width:20,					title:'Lugar de celebraci&oacute;n' },
                { field:'Triptico',		width:10,					title:'URL del Tr&iacute;ptico'},
                { field:'Cartel',		width:10,					title:'URL del Cartel'},
                { field:'Observaciones',width:15,					title:'Observaciones'},
                { field:'Cerrada',		width:7,					title:'Cerrada', align: 'center'}
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler,
    		// on double click fireup editor dialog
        	onDblClickRow:function() { editPrueba('#pruebas-datagrid'); },
            // especificamos un formateador especial para desplegar la tabla de jornadas por prueba
            detailFormatter:function(index,row){
                return '<div style="padding:2px"><table id="jornadas-datagrid-' + row.ID + '"/></div>';
            },
            onExpandRow: function(index,row) {
                if (row.ID!=0) showJornadasByPrueba(index,row); 
            }
            
        }); // end of pruebas-datagrid
        
     	// activa teclas up/down para navegar por el panel
        $('#pruebas-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
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
            	t.datagrid('reload',{
            		where: $('#pruebas-search').val(),
                    closed: $('#pruebas-openBox').is(':checked')?'1':'0',
                    onLoadSuccess: function(data){
                    	t.datagrid('getPager').pagination('refresh',{pageNumber:curPage});
                    }
            	});
			}
        	var t = $('#pruebas-datagrid');
            switch(e.keyCode){
            case 38:	/* Up */	selectRow(t,true); return false;
            case 40:    /* Down */	selectRow(t,false); return false;
            case 13:	/* Enter */	editPrueba('#pruebas-datagrid'); return false;
            case 45:	/* Insert */ newPrueba('#pruebas-datagrid'); return false;
            case 46:	/* Supr */	deletePrueba('#pruebas-datagrid'); return false;
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
		addTooltip($('#pruebas-newBtn').linkbutton(),"Crear y guardar una nueva prueba<br/> en la Base de Datos"); 
		addTooltip($('#pruebas-editBtn').linkbutton(),"Editar los datos de la prueba seleccionada");
		addTooltip($('#pruebas-delBtn').linkbutton(),"Eliminar la prueba seleccionada");
		addTooltip($('#pruebas-reloadBtn').linkbutton(),"Borrar casilla de busqueda y actualizar tabla");
		addTooltip($('#pruebas-openBox').linkbutton(),"Incluir en el listado las pruebas finalizadas (cerradas)");
		addTooltip($('#pruebas-search'),"Buscar pruebas coincidentes con el criterio de busqueda indicado");

        // ------------- submenu de jornadas asociadas a una prueba --------------------- //
        function showJornadasByPrueba (index,prueba) {
            var datagridID='#jornadas-datagrid-'+prueba.ID;
            $(datagridID).datagrid({
        		title: "Jornadas de que consta la prueba '"+prueba.Nombre+"'",
        		url: 'database/jornadaFunctions.php',
        		queryParams: { Operation: 'select', Prueba: prueba.ID },
        		method: 'get',
       		    pagination: false,
        	    rownumbers: false,
        	    fitColumns: true,
        	    singleSelect: true,
        	    loadMsg: 'Loading list of journeys',
        	    height: 'auto',
        	    columns: [[
                   	{ field:'ID',			hidden:true }, // ID de la jornada
            	    { field:'Prueba',		hidden:true }, // ID de la prueba
            	    { field:'Numero',		width:4, sortable:true,		align:'center', title: '#'},
            		{ field:'Nombre',		width:20, sortable:false,   title: 'Nombre/Comentario' },
            		{ field:'Fecha',		width:12, sortable:true,	title: 'Fecha:' },
            		{ field:'Hora',			width:10, sortable:false,	title: 'Hora.' },
            		{ field:'PreAgility',	width:8, sortable:false,   align:'center', title: 'PreAg. ' },
            		{ field:'Grado1',		width:8, sortable:false,   align:'center', title: 'G-I    ' },
            		{ field:'Grado2',		width:8, sortable:false,   align:'center', title: 'G-II   ' },
            		{ field:'Grado3',		width:8, sortable:false,   align:'center', title: 'G-III  ' },
            		{ field:'Open',			width:8, sortable:false,   align:'center', title: 'Open   ' },
            		{ field:'Equipos3',		width:8, sortable:false,   align:'center', title: 'Eq. 3x4' },
            		{ field:'Equipos4',		width:8, sortable:false,   align:'center', title: 'Eq. 4x4' },
            		{ field:'KO',			width:8, sortable:false,   align:'center', title: 'K.O.   ' },
            		{ field:'Exhibicion',	width:8, sortable:false,   align:'center', title: 'Show   ' },
            		{ field:'Otras',		width:8, sortable:false,   align:'center', title: 'Otras  ' },
            		{ field:'Cerrada',		width:8, sortable:false,   align:'center', title: 'Cerrada' }
            	]],
            	// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            	rowStyler:myRowStyler,
            	// on double click fireup editor dialog
                onDblClickRow:function(idx,row) { //idx: selected row index; row selected row data
                    editJornadaFromPrueba(prueba.ID,datagridID);
                },
                onResize:function(){
                    $('#pruebas-datagrid').datagrid('fixDetailRowHeight',index);
                },
                onLoadSuccess:function(data){
                    setTimeout(function(){
                        $('#pruebas-datagrid').datagrid('fixDetailRowHeight',index);
                    },0);
                } 
        	}); // end of pruebas-jornada-datagrid

        	$('#pruebas-datagrid').datagrid('fixDetailRowHeight',index);
    		// definimos inline la sub-barra de tareas para que solo aparezca al desplegar el sub formulario
    		// por defecto, cada prueba tiene asociadas 8 jornadas que se crean automaticamente
    		// por consiguiente desde la aplicacion no se deben poder anyadir ni borrar jornadas
    		var toolbar=  [
    	        {
    				id: 'jornadasbyprueba-editBtn'+prueba.ID,
            		text: 'Editar jornada',
        			iconCls: 'icon-edit',
       				handler: function(){editJornadaFromPrueba(prueba.ID,datagridID);}
    			},{
    				id: 'jornadasbyprueba-closeBtn'+prueba.ID,
            		text: 'Cerrar jornada',
        			iconCls: 'icon-forbidden',
       				handler: function(){closeJornadaFromPrueba(prueba.ID,datagridID);}
    			},{
    				id: 'jornadasbyprueba-reloadBtn'+prueba.ID,
            		text: 'Actualizar',
        			iconCls: 'icon-reload',
        			align: 'right', // notice that this property is handled by our own 'buildToolbar extended method'
       				handler: function(){$(datagridID).datagrid('reload');}    // reload the pruebas data}
    			}
    			];
    		$(datagridID).datagrid('buildToolbar',toolbar);
			// tooltips de los sub-formularios
			addTooltip($('#jornadasbyprueba-editBtn'+prueba.ID).linkbutton(),"Editar los datos la jornada seleccionada"); 
			addTooltip($('#jornadasbyprueba-closeBtn'+prueba.ID).linkbutton(),"Cerrar la jornada seleccionada y Guardar datos permanentemente"); 
			addTooltip($('#jornadasbyprueba-reloadBtn'+prueba.ID).linkbutton(),"Actualizar la lista de jornadas de esta prueba");
        };
        
	</script>
