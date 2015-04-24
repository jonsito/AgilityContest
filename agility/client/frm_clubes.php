<!-- 
frm_clubes.php

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->
<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE CLUBES -->
<div style="width:975px;height:550px;">
    <!-- DECLARACION DE LA TABLA -->
    <table id="clubes-datagrid"></table>
</div>

<!-- BARRA DE TAREAS DE LA TABLA DE CLUBES-->
<div id="clubes-toolbar" style="width:100%;display:inline-block">
	<span style="float:left;padding:5px">
		<a id="clubes-newBtn" href="#" class="easyui-linkbutton"
			data-options="iconCls:'icon-flag'"
   			onclick="newClub('#clubes-datagrid',$('#clubes-datagrid-search').val())">Nuevo Club</a>
   		<a id="clubes-editBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-edit'"
   			onclick="editClub('#clubes-datagrid')">Editar Club</a>
   		<a id="clubes-delBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-trash'"
   			onclick="deleteClub('#clubes-datagrid')">Borrar Club</a>
   		<input id="clubes-datagrid-search" type="text" value="---- Buscar ----" class="search_textfield"/>
   	</span>
   	<span style="float:right;padding:5px">
   		<a id="clubes-reloadBtn" href="#" class="easyui-linkbutton"
   		data-options="iconCls:'icon-brush'"
   		onClick="reloadWithSearch('#clubes-datagrid','select',true);">Limpiar</a>
   	</span>
</div>   

<?php require_once("dialogs/dlg_clubes.inc")?>
<?php require_once("dialogs/dlg_guias.inc")?>
<?php require_once("dialogs/dlg_chguias.inc")?>
<?php require_once("dialogs/dlg_perros.inc")?>
<?php require_once("dialogs/dlg_chperros.inc")?>

<script type="text/javascript">
        
        // datos de la tabla de clubes
        // - tabla
        $('#clubes-datagrid').datagrid({
        	// propiedades del panel padre asociado
        	fit: true,
        	border: false,
        	closable: true,
        	collapsible: false,
            expansible: false,
        	collapsed: false,
        	title: 'Gesti&oacute;n de datos de Clubes'+' - '+fedName(workingData.federation),
        	url: '/agility/server/database/clubFunctions.php',
    		queryParams: { Operation: 'select' },
        	loadMsg: 'Actualizando lista de Clubes ...',
        	method: 'get',
            toolbar: '#clubes-toolbar',
            pagination: false,
            rownumbers: true,
            fitColumns: true,
            singleSelect: true,
            view: scrollview,
            pageSize: 50,
            multiSort: true,
            remoteSort: true,
            columns: [[
                  	{ field:'ID',			hidden:true},
                   	{ field:'Nombre',		width:15, sortable:true,	title: 'Nombre:'},
            		{ field:'Direccion1',	width:15, sortable:true,	title: 'Direcci&oacute;n 1:' },
            		{ field:'Direccion2',	width:10, sortable:false,	title: 'Direcci&oacute;n 2' },
            		{ field:'Provincia',	width:7, sortable:false,   title: 'Provincia' },
            		{ field:'Contacto1',	width:10, sortable:false,   title: 'Contacto 1' },
            		{ field:'Contacto2',	width:10, sortable:true,    title: 'Contacto 2' },
            		{ field:'Contacto3',	width:10, sortable:true,    title: 'Contacto 3' },
            		{ field:'GPS',			hidden:true},
            		{ field:'Web',			hidden:true},
            		{ field:'Email',		hidden:true},
                  	{ field:'Federations',	hidden:true},
            		{ field:'RSCE',			width:3, sortable:true,    align: 'center', title: 'RSCE', formatter:clubesRSCE },
            		{ field:'RFEC',			width:3, sortable:true,    align: 'center', title: 'RFEC', formatter:clubesRFEC },
            		{ field:'UCA',			width:3, sortable:true,    align: 'center', title: 'UCA',  formatter:clubesUCA },
            		// { field:'Logo',		width:2, sortable:true,    title: 'Logo club' },
            		//{ field:'Observaciones',width:2, sortable:true,    title: 'Observaciones' },
            		{ field:'Baja',			width:2, sortable:true,    align: 'center', title: 'Baja', formatter:clubesBaja }
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler,
        	// on double click fireup editor dialog
            onDblClickRow:function() { 
                editClub('#clubes-datagrid');
            },        
            // especificamos un formateador especial para desplegar la tabla de guias por club
            detailFormatter:function(index,club){
                return '<div style="padding:2px"><table id="clubes-guias-datagrid-' + replaceAll(' ','_',club.ID) + '"></table></div>';
            },
            onExpandRow: function(idx,club) { showGuiasByClub(idx,club); }
        }); // end of '#clubes-datagrid' declaration

		// key handler
       	addKeyHandler('#clubes-datagrid',newClub,editClub,deleteClub);
		// tooltips
		addTooltip($('#clubes-newBtn').linkbutton(),"Dar de alta un nuevo club <br/>en la Base de Datos"); 
		addTooltip($('#clubes-editBtn').linkbutton(),"Editar los datos del club seleccionado");
		addTooltip($('#clubes-delBtn').linkbutton(),"Borrar el club seleccionado de la BBDD");
		addTooltip($('#clubes-reloadBtn').linkbutton(),"Borrar casilla de busqueda y actualizar tabla");
		addTooltip($('#clubes-datagrid-search'),"Mostrar clubes que coincidan con el criterio de busqueda");
        
    	
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
        		title: 'Gu&iacute;as inscritos en el club '+club.Nombre+ ' - '+fedName(workingData.federation),
        	    loadMsg: 'Cargando lista de guias....',
        		url: '/agility/server/database/guiaFunctions.php',
        		queryParams: { 
            		Operation:'getbyclub',
            		Club: club.ID, 
            		Federation: workingData.federation 
            	},
        		method: 'get',
        	    columns: [[
        	        { field:'ID',			hidden:true },	
        	    	{ field:'Nombre',		width:30, sortable:true,	title: 'Nombre:' },
        	    	{ field:'Telefono',	width:15, sortable:true,	title: 'Tel&eacute;fono' },
        	    	{ field:'Email',		width:25, sortable:true,    title: 'Correo Electr&oacute;nico' },
        	    	{ field:'Observaciones',width:15,					title: 'Observaciones'}
            	]],
            	// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            	rowStyler:myRowStyler,
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
                    editGuiaFromClub(mySelf,club );
                },
                // especificamos un formateador especial para desplegar la tabla de perros por guia
                detailFormatter:function(index,guia){
                    return '<div style="padding:2px"><table id="clubes-guias-perros-datagrid-' + replaceAll(' ','_',guia.ID) + '"></table></div>';
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
        	// definimos inline la sub-barra de tareas para que solo aparezca al desplegar el sub formulario
        	var	toolbar= [{
            		id: 'guiasByClub-newBtn'+club.ID,
            		text: 'Asociar gu&iacute;a',
        			iconCls: 'icon-users',
        			handler: function(){ assignGuiaToClub(mySelf,club); }
        		},{
            		id: 'guiasByClub-editBtn'+club.ID,
            		text: 'Editar gu&iacute;a',
        			iconCls: 'icon-edit',
        			handler: function(){ editGuiaFromClub(mySelf,club); }
        		},{
            		id: 'guiasByClub-delBtn'+club.ID,
            		text: 'Des-asociar gu&iacute;a',
        			iconCls: 'icon-remove',
        			handler: function(){ delGuiaFromClub(mySelf,club); }
        		},{
    				id: 'guiasByClub-reloadBtn'+club.ID,
            		text: 'Actualizar',
        			iconCls: 'icon-reload',
        			align: 'right', // notice that this property is handled by our own 'buildToolbar extended method'
       				handler: function(){ $(mySelf).datagrid('reload'); }    // reload the clubs data}
    			}];
    		$(mySelf).datagrid('buildToolbar',toolbar);
        	$('#clubes-datagrid').datagrid('fixDetailRowHeight',index);
			// tooltips de los sub-formularios
			addTooltip($('#guiasByClub-newBtn'+club.ID).linkbutton(),"Asociar/Crear nuevo guia en el club '"+club.Nombre+"'"); 
			addTooltip($('#guiasByClub-editBtn'+club.ID).linkbutton(),"Editar datos del gu&iacute;a seleccionado del club '"+club.Nombre+"'"); 
			addTooltip($('#guiasByClub-delBtn'+club.ID).linkbutton(),"Desasociar al gu&iacute;a seleccionado del club '"+club.Nombre+"'"); 
			addTooltip($('#guiasByClub-reloadBtn'+club.ID).linkbutton(),"Actualizar la lista de gu&iacute;s del club '"+club.Nombre+"'");
            	
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
        		title: 'Perros registrados a nombre de '+guia.Nombre+' - '+fedName(workingData.federation),
        		url: '/agility/server/database/dogFunctions.php',
        		queryParams: { Operation: 'getbyguia', Guia: guia.ID, Federation: workingData.federation },
        		method: 'get',
        	    columns: [[
            	    { field:'ID',		width:15, sortable:true,	title: 'ID' },
            		{ field:'Nombre',	width:30, sortable:true,	title: 'Nombre:' },
            		{ field:'Categoria',width:15, sortable:false,	title: 'Cat.' },
            		{ field:'Grado',	width:25, sortable:false,   title: 'Grado' },
            		{ field:'Raza',		width:25, sortable:false,   title: 'Raza' },
            		{ field:'LOE_RRC',	width:25, sortable:true,    title: 'LOE / RRC' },
            		{ field:'Licencia',	width:25, sortable:true,    title: 'Licencia' }
            	]],
            	// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            	rowStyler: myRowStyler,
            	// on double click fireup editor dialog
                onDblClickRow: function(idx,row) { //idx: selected row index; row selected row data
                    editPerroFromGuia(mySelf,guia );
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
        	
    		// definimos inline la sub-barra de tareas para que solo aparezca al desplegar el sub formulario
    		// toolbar: '#perrosbyguia-toolbar', 
			var toolbar=  [{
				id: 'perrosByGuiaByClub-newBtn'+guia.ID+'_'+club.ID,
				text: 'Asignar perro',
				iconCls: 'icon-dog',
				handler: function(){ assignPerroToGuia(mySelf,guia ); },
			},{
				id: 'perrosByGuiaByClub-editBtn'+guia.ID+'_'+club.ID,
				text: 'Editar datos',
				iconCls: 'icon-edit',
				handler: function(){editPerroFromGuia(mySelf,guia);}
			},{
				id: 'perrosByGuiaByClub-delBtn'+guia.ID+'_'+club.ID,
				text: 'Desasignar perro',
				iconCls: 'icon-remove',
				handler: function(){delPerroFromGuia(mySelf,guia);}
			},{
				id: 'perrosByGuiaByClub-reloadBtn'+guia.ID+'_'+club.ID,
        		text: 'Actualizar',
    			iconCls: 'icon-reload',
    			align: 'right', // notice that this property is handled by our own 'buildToolbar extended method'
   				handler: function(){ $(mySelf).datagrid('reload'); }    // reload the clubs data}
			}];
			// add toolbar to datagrid
    		$(mySelf).datagrid('buildToolbar',toolbar);
    		// tell parent to fix rendering ob subgrid
        	$(parent).datagrid('fixDetailRowHeight',index);

			// tooltips de los sub-formularios
			addTooltip($('#perrosByGuiaByClub-newBtn'+guia.ID+'_'+club.ID).linkbutton(),"Crear/Asignar un nuevo perro a '"+guia.Nombre+"'"); 
			addTooltip($('#perrosByGuiaByClub-editBtn'+guia.ID+'_'+club.ID).linkbutton(),"Editar los datos del perro asignado a '"+guia.Nombre+"'"); 
			addTooltip($('#perrosByGuiaByClub-delBtn'+guia.ID+'_'+club.ID).linkbutton(),"Eliminar asignaci&oacute;n del perro a '"+guia.Nombre+"'");
			addTooltip($('#perrosByGuiaByClub-reloadBtn'+guia.ID+'_'+club.ID).linkbutton(),"Actualizar la lista de perros del gu&iacute;a '"+guia.Nombre+"'");
        } // end of showPerrosByGuia
</script>

