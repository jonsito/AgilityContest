<!-- 
frm_pruebas.php

Copyright 2013-2014 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE Pruebas -->
<div style="width:975px;height:550px">    
    <!-- DECLARACION DE LA TABLA -->
    <table id="pruebas-datagrid"></table> 
</div>
    
<!-- BARRA DE TAREAS DE LA TABLA DE PRUEBAS-->
<div id="pruebas-toolbar" style="width:100%;display:inline-block">
   	<span style="float:left;padding:5px">
   	    <a id="pruebas-newBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-add'"
   	    	onclick="newPrueba('#pruebas-datagrid',$('#pruebas-datagrid-search').val())">Nueva prueba</a>
   	    <a id="pruebas-editBtn" href="#" class="easyui-linkbutton"
   	    	data-options="iconCls:'icon-edit'"
   	    	onclick="editPrueba('#pruebas-datagrid')">Editar prueba</a>
   	    <a id="pruebas-delBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-remove'"
   	    	onclick="deletePrueba('#pruebas-datagrid')">Borrar prueba</a>
   		<input id="pruebas-datagrid-search" type="text" value="---- Buscar ----" class="search_textfield"/>
   	    <input id="pruebas-openBox" type="checkbox" value="1" class="easyui-checkbox"
   	    	data-options="iconCls:'icon-search'" 
   	    	onclick="doSearchPrueba()"/>Incl. Cerradas
   	</span>
   	<span style="float:right;padding:5px">
   		<a id="pruebas-reloadBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-brush'"
   	    	onclick="
        		// clear selection and reload table
    			$('#pruebas_datagrid-search').val('---- Buscar ----');
            	$('#pruebas_datagrid').datagrid('load',{ where: '' });"
		>Limpiar</a>
   	</span>
</div>
    
<?php require_once("dialogs/dlg_clubes.inc");?>
<?php require_once("dialogs/dlg_pruebas.inc");?>
<?php require_once("dialogs/dlg_jornadas.inc");?>

<script type="text/javascript">
        
        // datos de la tabla de pruebas
        $('#pruebas-datagrid').datagrid({
        	// propiedades del panel padre asociado
        	fit: true,
        	border: false,
        	closable: true,
        	collapsible: false,
            expansible: false,
        	collapsed: false,        	
        	title: 'Gesti&oacute;n de datos de pruebas',
        	url: '/agility/server/database/pruebaFunctions.php?Operation=select',
        	loadMsg: 'Actualizando lista de Clubes ...',
        	method: 'get',
            toolbar: '#pruebas-toolbar',
            pagination: false,
            rownumbers: false,
            fitColumns: true,
            singleSelect: true,
            view: scrollview,
            pageSize: 50,
            multiSort: true,
            remoteSort: true,
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
        
		// key handler
       	addKeyHandler('#pruebas-datagrid',newPrueba,editPrueba,deletePrueba);
		// tooltips
		addTooltip($('#pruebas-newBtn').linkbutton(),"Crear y guardar una nueva prueba<br/> en la Base de Datos"); 
		addTooltip($('#pruebas-editBtn').linkbutton(),"Editar los datos de la prueba seleccionada");
		addTooltip($('#pruebas-delBtn').linkbutton(),"Eliminar la prueba seleccionada");
		addTooltip($('#pruebas-reloadBtn').linkbutton(),"Borrar casilla de busqueda y actualizar tabla");
		addTooltip($('#pruebas-openBox').linkbutton(),"Incluir en el listado las pruebas finalizadas (cerradas)");
		addTooltip($('#pruebas-datagrid-search'),"Buscar pruebas coincidentes con el criterio de busqueda indicado");

        // ------------- submenu de jornadas asociadas a una prueba --------------------- //
        function showJornadasByPrueba (index,prueba) {
            var datagridID='#jornadas-datagrid-'+prueba.ID;
            $(datagridID).datagrid({
        		title: "Jornadas de que consta la prueba '"+prueba.Nombre+"'",
        		url: '/agility/server/database/jornadaFunctions.php',
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
            		{ field:'PreAgility',	width:8, sortable:false,   align:'center', title: 'P.A. -1' },
            		{ field:'PreAgility2',	width:8, sortable:false,   align:'center', title: 'P.A. -2' },
            		{ field:'Grado1',		width:8, sortable:false,   align:'center', title: 'G-I    ' },
            		{ field:'Grado2',		width:8, sortable:false,   align:'center', title: 'G-II   ' },
            		{ field:'Grado3',		width:8, sortable:false,   align:'center', title: 'G-III  ' },
            		{ field:'Open',			width:8, sortable:false,   align:'center', title: 'Open   ' },
            		{ field:'Equipos3',		width:8, sortable:false,   align:'center', title: 'Eq. 3x4' },
            		{ field:'Equipos4',		width:8, sortable:false,   align:'center', title: 'Eq. 4x4' },
            		{ field:'KO',			width:8, sortable:false,   align:'center', title: 'K.O.   ' },
            		{ field:'Especial',	    width:8, sortable:false,   align:'center', title: 'Especial'},
            	    { field:'Observaciones',hidden:true }, // texto para el caso de Manga especial
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
