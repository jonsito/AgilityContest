<!-- 
frm_paises.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->
<!--
TABLA DE jquery-easyui para listar la tabla de paises
Realmente los paises estan declarados como clubes, pertenecientes a la federacion ID:9 y NO SON EDITABLES
-->
<?php
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>
<div style="width:100%;height:550px;">
    <!-- DECLARACION DE LA TABLA -->
    <table id="countries-datagrid"></table>
</div>

<!-- BARRA DE TAREAS DE LA TABLA DE CLUBES-->
<div id="countries-toolbar" style="width:100%;display:inline-block">
	<span style="float:left;padding:5px">
		<label for="countries-datagrid-search"></label>
   		<input id="countries-datagrid-search" type="text" value="<?php _e('-- Search --'); ?>" class="search_textfield"
			   onfocus="handleSearchBox(this,true);" onblur="handleSearchBox(this,false);"/>
   	</span>
   	<span style="float:right;padding:5px">
   		<a id="countries-reloadBtn" href="#" class="easyui-linkbutton"
   		data-options="iconCls:'icon-brush'"
   		onClick="reloadWithSearch('#countries-datagrid','select',true);"><?php _e('Clear'); ?></a>
   	</span>
</div>   

<?php require_once("dialogs/dlg_guias.inc")?>
<?php require_once("dialogs/dlg_chguias.inc")?>
<?php require_once("dialogs/dlg_perros.inc")?>
<?php require_once("dialogs/dlg_chperros.inc")?>

<script type="text/javascript">
        
	// datos de la tabla de countries
	// - tabla
	$(function(){
		$('#countries-datagrid').datagrid({
			// propiedades del panel padre asociado
			fit: true,
			border: false,
			closable: true,
			collapsible: false,
			expansible: false,
			collapsed: false,
			title: '<?php _e('Country database'); ?>' + ' - ' + fedName(workingData.federation),
			url: '../ajax/database/clubFunctions.php',
			queryParams: { Operation: 'select', Federation: workingData.federation },
			loadMsg: '<?php _e('Updating Country list'); ?>'+' ...',
			method: 'get',
			toolbar: '#countries-toolbar',
			pagination: false,
			rownumbers: true,
			fitColumns: true,
			idField: 'ID',
			autoRowHeight:true,
			singleSelect: true,
			view: scrollview,
			pageSize: 50,
			multiSort: true,
			remoteSort: true,
			columns: [[
				{ field:'ID',			hidden:true},
				{ field:'Pais',	        width:'5%', sortable:true,    align: 'center', title: '<?php _e('Country'); ?>' },
				{ field:'Nombre',		width:'65%', sortable:true,	title: '<?php _e('Name'); ?>',formatter:formatBold},
				{ field:'Direccion1',	hidden:true},
				{ field:'Direccion2',	hidden:true},
				{ field:'Provincia',	hidden:true},
				{ field:'Contacto1',	hidden:true},
				{ field:'Contacto2',	hidden:true},
				{ field:'Contacto3',	hidden:true},
				{ field:'GPS',			hidden:true},
				{ field:'Web',			hidden:true},
				{ field:'Email',		hidden:true},
				{ field:'Federations',	hidden:true},
				{ field:'LogoClub',			width:'20%', sortable:false,    title: '', formatter: format_countryFlag },
				//{ field:'Observaciones',width:2, sortable:true,    title: 'Observaciones' },
				{ field:'Baja',			hidden:true }
			]],
			// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
			rowStyler:country_styler,
			// especificamos un formateador especial para desplegar la tabla de guias por pais
			detailFormatter:function(index,row){
				var dg = "countries-guias-datagrid-" + replaceAll(' ', '_', row.ID);
				return '<div style="padding:2px"><table id="'+dg+'"></table></div>';
			},
			onExpandRow: function(idx,row) { // on scrollview row may be undefined, so ignore
                var r = $(this).datagrid('getRow',idx);
			    showGuiasByCountry(idx,r);
			}
		}); // end of '#countries-datagrid' declaration
	});

		// key handler
       	addKeyHandler('#countries-datagrid',null,null,null,null);
		addTooltip($('#countries-reloadBtn').linkbutton(),'<?php _e("Clear search box. Update table"); ?>');
		addTooltip($('#countries-datagrid-search'),'<?php _e("Look for countries matching search criteria"); ?>');
        
    	
        function showGuiasByCountry(index,country){
        	// - sub tabla de guias de un pais
        	var mySelf='#countries-guias-datagrid-'+replaceAll(' ','_',country.ID);
        	$(mySelf).datagrid({
            	width: 875,
            	fit:false,
       		    pagination: false,
        	    rownumbers: false,
        	    fitColumns: true,
        	    singleSelect: true,
                view: detailview,
        	    // height: 'auto',
        		title: '<?php _e('Handlers from country'); ?>'+' '+country.Nombre+ ' - '+fedName(workingData.federation),
        	    loadMsg: '<?php _e('Loading handlers list'); ?>' +' ....',
        		url: '../ajax/database/guiaFunctions.php',
        		queryParams: { 
            		Operation:'getbyclub', // remember that clubes and countries share same DDBB table
            		Club: country.ID,
            		Federation: workingData.federation 
            	},
        		method: 'get',
        	    columns: [[
        	        { field:'ID',			hidden:true },	
        	    	{ field:'Nombre',		width:30, sortable:true,	title: '<?php _e('Name'); ?>'+':' },
        	    	{ field:'Telefono',	width:15, sortable:true,	title: '<?php _e('Telephone'); ?>'+':' },
        	    	{ field:'Email',		width:25, sortable:true,    title: '<?php _e('Electronic mail'); ?>'+':' },
        	    	{ field:'Observaciones',width:15,					title: '<?php _e('Comments'); ?>'+':'}
            	]],
            	// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            	rowStyler:myRowStyler,
                onResize:function(){
                    $('#countries-datagrid').datagrid('fixDetailRowHeight',index);
                },
                onLoadSuccess:function(){
                    setTimeout(function(){
                        $('#countries-datagrid').datagrid('fixDetailRowHeight',index);
                    },0);
                },
            	// on double click fireup editor dialog
                onDblClickRow:function(idx,row) { //idx: selected row index; row selected row data
                    editGuiaFromClub(mySelf,country );
                },
                // especificamos un formateador especial para desplegar la tabla de perros por guia
                detailFormatter:function(index,row){
                    var dg = "countries-guias-perros-datagrid-" + replaceAll(' ', '_', row.ID);
                    return '<div style="padding:2px"><table id="'+dg+'"></table></div>';
                },
                onExpandRow: function(idx,row) { showPerrosByGuiaByCountry(idx,row,country); } /*,
                onCollapseRow: function(idx,row) {
                    var dg = "#countries-guias-perros-datagrid-" + replaceAll(' ', '_', row.ID);
                    $(dg).remove();
                } */
                /* end of countries-guias-dog subtable */
        	}); // end of '#countries-guias-datagrid' declaration

        	// definimos inline la sub-barra de tareas para que solo aparezca al desplegar el sub formulario
        	var	toolbar= [{
            		id: 'guiasByCountry-newBtn'+country.ID,
            		text: '<?php _e('Join handler'); ?>',
        			iconCls: 'icon-users',
        			handler: function(){ assignGuiaToClub(mySelf,country); }
        		},{
            		id: 'guiasByCountry-editBtn'+country.ID,
            		text: '<?php _e('Edit handler'); ?>',
        			iconCls: 'icon-edit',
        			handler: function(){ editGuiaFromClub(mySelf,country); }
        		},{
            		id: 'guiasByCountry-delBtn'+country.ID,
            		text: '<?php _e('Dettach handler'); ?>',
        			iconCls: 'icon-remove',
        			handler: function(){ delGuiaFromClub(mySelf,country); }
        		},{
    				id: 'guiasByCountry-reloadBtn'+country.ID,
            		text: '<?php _e('Update'); ?>',
        			iconCls: 'icon-reload',
        			align: 'right', // notice that this property is handled by our own 'buildToolbar extended method'
       				handler: function(){ $(mySelf).datagrid('reload'); }    // reload the countries data}
    			}];
    		$(mySelf).datagrid('buildToolbar',toolbar);
        	$('#countries-datagrid').datagrid('fixDetailRowHeight',index);
			// tooltips de los sub-formularios
			addTooltip($('#guiasByCountry-newBtn'+country.ID).linkbutton(),'<?php _e("Create/Assign handler to country"); ?>'+" '"+country.Nombre+"'");
			addTooltip($('#guiasByCountry-editBtn'+country.ID).linkbutton(),'<?php _e("Edit data on handler belonging country"); ?>'+" '"+country.Nombre+"'");
			addTooltip($('#guiasByCountry-delBtn'+country.ID).linkbutton(),'<?php _e("Unassign selected handler from country"); ?>'+" '"+country.Nombre+"'");
			addTooltip($('#guiasByCountry-reloadBtn'+country.ID).linkbutton(),'<?php _e("Update handlers list on country"); ?>'+" '"+country.Nombre+"'");
            	
        } // end of "showGuiasByCountry"
        
		// mostrar los perros asociados a un guia
        function showPerrosByGuiaByCountry(index,guia,country){
            var parent='#countries-guias-datagrid-'+replaceAll(' ','_',country.ID);
            var mySelf='#countries-guias-perros-datagrid-'+replaceAll(' ','_',guia.ID);
        	// - sub tabla de perros asignados a un guia
        	$(mySelf).datagrid({
            	fit:false,
            	width: 850,
       		    pagination: false,
        	    rownumbers: false,
        	    fitColumns: true,
        	    singleSelect: true,
        	    // height: 'auto',
        	    loadMsg: '<?php _e('Loading list of dogs'); ?>',
        		title: '<?php _e('Registered dogs belonging to'); ?>'+' '+guia.Nombre+' - '+fedName(workingData.federation),
        		url: '../ajax/database/dogFunctions.php',
        		queryParams: { Operation: 'getbyguia', Guia: guia.ID, Federation: workingData.federation },
        		method: 'get',
        	    columns: [[
            	    { field:'ID',		width:15, sortable:true,	title: 'ID' },
            		{ field:'Nombre',	width:30, sortable:true,	title: '<?php _e('Name'); ?>' },
					{ field:'NombreLargo',hidden:true},
					{ field:'Genero',	hidden:true},
            		{ field:'Categoria',width:15, sortable:false,	title: '<?php _e('Cat.'); ?>',formatter:formatCategoria },
            		{ field:'Grado',	width:25, sortable:false,   title: '<?php _e('Grade'); ?>',formatter:formatGrado },
            		{ field:'Raza',		width:25, sortable:false,   title: '<?php _e('Breed'); ?>' },
            		{ field:'LOE_RRC',	width:25, sortable:true,    title: '<?php _e('KC id'); ?>' },
            		{ field:'Licencia',	width:25, sortable:true,    title: '<?php _e('License'); ?>' }
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
				id: 'perrosByGuiaByCountry-newBtn'+guia.ID+'_'+country.ID,
				text: '<?php _e('Assign dog'); ?>',
				iconCls: 'icon-dog',
				handler: function(){ assignPerroToGuia(mySelf,guia ); }
            },{
				id: 'perrosByGuiaByCountry-editBtn'+guia.ID+'_'+country.ID,
				text: '<?php _e('Edit dog'); ?>',
				iconCls: 'icon-edit',
				handler: function(){editPerroFromGuia(mySelf,guia);}
			},{
				id: 'perrosByGuiaByCountry-delBtn'+guia.ID+'_'+country.ID,
				text: '<?php _e('Dettach dog'); ?>',
				iconCls: 'icon-remove',
				handler: function(){delPerroFromGuia(mySelf,guia);}
			},{
				id: 'perrosByGuiaByCountry-reloadBtn'+guia.ID+'_'+country.ID,
        		text: '<?php _e('Update'); ?>',
    			iconCls: 'icon-reload',
    			align: 'right', // notice that this property is handled by our own 'buildToolbar extended method'
   				handler: function(){ $(mySelf).datagrid('reload'); }    // reload the countries data}
			}];
			// add toolbar to datagrid
    		$(mySelf).datagrid('buildToolbar',toolbar);
    		// tell parent to fix rendering ob subgrid
        	$(parent).datagrid('fixDetailRowHeight',index);

			// tooltips de los sub-formularios
			addTooltip($('#perrosByGuiaByCountry-newBtn'+guia.ID+'_'+country.ID).linkbutton(),'<?php _e("Declare/Assign a dog to"); ?>'+" '"+guia.Nombre+"'");
			addTooltip($('#perrosByGuiaByCountry-editBtn'+guia.ID+'_'+country.ID).linkbutton(),'<?php _e("Edit data on dog belonging to"); ?>'+" '"+guia.Nombre+"'");
			addTooltip($('#perrosByGuiaByCountry-delBtn'+guia.ID+'_'+country.ID).linkbutton(),'<?php _e("Unassign selected dog from handler"); ?>'+" '"+guia.Nombre+"'");
			addTooltip($('#perrosByGuiaByCountry-reloadBtn'+guia.ID+'_'+country.ID).linkbutton(),'<?php _e("Update list of dogs belonging to"); ?>'+" '"+guia.Nombre+"'");
        } // end of showPerrosByGuia
</script>

