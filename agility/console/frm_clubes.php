<!-- 
frm_clubes.php

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
<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE CLUBES -->
<?php
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>
<div style="width:100%;height:550px;">
    <!-- DECLARACION DE LA TABLA -->
    <table id="clubes-datagrid"></table>
</div>

<!-- BARRA DE TAREAS DE LA TABLA DE CLUBES-->
<div id="clubes-toolbar" style="width:100%;display:inline-block">
	<span style="float:left;padding:5px">
		<a id="clubes-newBtn" href="#" class="easyui-linkbutton"
			data-options="iconCls:'icon-flag'"
   			onclick="newClub('#clubes-datagrid',$('#clubes-datagrid-search').val())"><?php _e('New Club'); ?></a>
   		<a id="clubes-editBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-edit'"
   			onclick="editClub('#clubes-datagrid')"><?php _e('Edit Club'); ?></a>
   		<a id="clubes-delBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-trash'"
   			onclick="deleteClub('#clubes-datagrid')"><?php _e('Delete Club'); ?></a>
   		<input id="clubes-datagrid-search" type="text" value="<?php _e('-- Search --'); ?>" class="search_textfield"
			   onfocus="handleSearchBox(this,true);" onblur="handleSearchBox(this,false);"/>
   		<a id="clubes-reloadBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-brush'"
           onClick="reloadWithSearch('#clubes-datagrid','select',true);"><?php _e('Clear'); ?></a>
   	</span>
   	<span style="float:right;padding:5px">
   		<a id="clubes-printBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-print'"
           onclick="print_listaClubes('')"><?php _e('Print'); ?></a>
   	</span>
</div>   

<?php require_once("dialogs/dlg_guias.inc")?>
<?php require_once("dialogs/dlg_chguias.inc")?>
<?php require_once("dialogs/dlg_perros.inc")?>
<?php require_once("dialogs/dlg_chperros.inc")?>
<?php require_once("dialogs/dlg_clubes.inc")?>

<script type="text/javascript">
        
	// datos de la tabla de clubes
	// - tabla
	$(function(){
		var hp=isInternational(workingData.federation); // on international contests hide province
		var fnames=[];
		for (var n=0; n<10;n++) {
		    fnames[n]={'Title':'','Hidden':true,'Width':0};
			if (typeof(ac_fedInfo[n])!=="undefined") { // on declared feds, fill with propper data
				fnames[n].Title=ac_fedInfo[n].Name;  //change column name to proper federation name
				fnames[n].Hidden=false;
				fnames[n].Width=4;
			}
		}
		$('#clubes-datagrid').datagrid({
			// propiedades del panel padre asociado
			fit: true,
			border: false,
			closable: false,
			collapsible: false,
			expansible: false,
			collapsed: false,
			title: '<?php _e('Clubs data management'); ?>' + ' - ' + fedName(workingData.federation),
			url: '../ajax/database/clubFunctions.php',
			queryParams: { Operation: 'select', Federation: workingData.federation },
			loadMsg: '<?php _e('Updating Clubs list'); ?>'+' ...',
			method: 'get',
			toolbar: '#clubes-toolbar',
			pagination: false,
			rownumbers: true,
			fitColumns: true,
			idField: 'ID',
			singleSelect: true,
			view: scrollview,
			pageSize: 50,
			multiSort: true,
			remoteSort: true,
			columns: [[
				{ field:'ID',			hidden:true},
				{ field:'Nombre',		width:16, sortable:true,	title: '<?php _e('Name'); ?>',formatter:formatBold},
				{ field:'Direccion1',	width:10, sortable:true,	title: '<?php _e('Address'); ?>'+' 1' },
				{ field:'Direccion2',	width:10, sortable:false,	title: '<?php _e('Address'); ?>'+' 2' },
				{ field:'Provincia',	width:8, sortable:false,    title: '<?php _e('State'); ?>' , hidden:hp},
				{ field:'Pais',	        width:3, sortable:false,    align: 'center', title: '<?php _e('Country'); ?>' },
				{ field:'Contacto1',	width:9, sortable:false,   title: '<?php _e('Contact'); ?>'+' 1' },
				{ field:'Contacto2',	hidden:true, width:0, sortable:true,    title: '<?php _e('Contact'); ?>'+' 2' },
                { field:'Contacto3',	hidden:true},
				{ field:'GPS',			hidden:true},
				{ field:'Web',			hidden:true},
                { field:'Email',	    width:12, sortable:true,    title: '<?php _e('Email'); ?>' },
				{ field:'Federations',	width:0, hidden:true},
				{ field:'F1',/*RSCE*/   width:fnames[0].Width, align:'center', title:fnames[0].Title, hidden:fnames[0].Hidden, formatter:clubes_Fed1 },
				{ field:'F2',/*RFEC*/   width:fnames[1].Width, align:'center', title:fnames[1].Title, hidden:fnames[1].Hidden, formatter:clubes_Fed2 },
                { field:'F5',/*CPC */	width:fnames[4].Width, align:'center', title:fnames[4].Title, hidden:fnames[4].Hidden, formatter:clubes_Fed5 },
                { field:'F6',/*Nat5*/	width:fnames[5].Width, align:'center', title:fnames[5].Title, hidden:fnames[5].Hidden, formatter:clubes_Fed6 },
				{ field:'F3',/*Nat4*/	width:fnames[2].Width, align:'center', title:fnames[2].Title, hidden:fnames[2].Hidden, formatter:clubes_Fed3 },
				{ field:'F4',/*Nat3*/	width:fnames[3].Width, align:'center', title:fnames[3].Title, hidden:fnames[3].Hidden, formatter:clubes_Fed4 },
				// { field:'Logo',		width:2, sortable:true,    title: 'Logo club' }, // to be removed in a rewrite
				// { field:'LogoClub',		width:2, sortable:true,    title: 'Logo club' },
				//{ field:'Observaciones',width:2, sortable:true,    title: 'Observaciones' },
				{ field:'Baja',			width:2, sortable:true,    align: 'center', title: '<?php _e('Out'); ?>', formatter:clubesBaja }
			]],
			// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
			rowStyler:myRowStyler,
			// on double click fireup editor dialog
			onDblClickRow:function(index,row) {
				editClub('#clubes-datagrid',row);
			},
			// especificamos un formateador especial para desplegar la tabla de guias por club
			detailFormatter:function(index,row){
				var dg = "clubes-guias-datagrid-" + replaceAll(' ', '_', row.ID);
				return '<div style="padding:2px"><table id="'+dg+'"></table></div>';
			},
			onExpandRow: function(idx,row) { // on scrollview row may be undefined, so ignore
                var r = $(this).datagrid('getRow',idx);
			    showGuiasByClub(idx,r);
			}/*,
			onCollapseRow: function(idx,row) {
			    var dg = "#clubes-guias-datagrid-" + replaceAll(' ', '_', row.ID);
			    $(dg).remove();
			}*/
		}); // end of '#clubes-datagrid' declaration
	});

		// key handler
       	addKeyHandler('#clubes-datagrid',"",newClub,editClub,deleteClub);
		// tooltips
		addTooltip($('#clubes-newBtn').linkbutton(),'<?php _e("Create a new club <br/>and insert into database"); ?>');
		addTooltip($('#clubes-editBtn').linkbutton(),'<?php _e("Edit data on selected club"); ?>');
		addTooltip($('#clubes-delBtn').linkbutton(),'<?php _e("Remove club from database"); ?>');
		addTooltip($('#clubes-reloadBtn').linkbutton(),'<?php _e("Clear search box. Update table"); ?>');
        addTooltip($('#clubes-datagrid-search'),'<?php _e("Look for clubes matching search criteria"); ?>');
        addTooltip($('#clubes-printBtn'),'<?php _e("Generate listing with current selection"); ?>');

    	
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
        		title: '<?php _e('Handlers belonging to club'); ?>'+' '+club.Nombre+ ' - '+fedName(workingData.federation),
        	    loadMsg: '<?php _e('Loading handlers list'); ?>' +' ....',
        		url: '../ajax/database/guiaFunctions.php',
        		queryParams: { 
            		Operation: 'getbyclub',
            		Club: club.ID, 
            		Federation: workingData.federation 
            	},
        		method: 'get',
        	    columns: [[
        	        { field:'ID',			hidden:true },	
        	    	{ field:'Nombre',		width:30, sortable:true,	title: '<?php _e('Name'); ?>',formatter:formatBold },
                    { field:'Categoria',     width:10, sortable:true,    title: '<?php _e('Category');?>',formatter:formatCatGuia },
        	    	{ field:'Telefono',	    width:15, sortable:true,	title: '<?php _e('Telephone'); ?>' },
        	    	{ field:'Email',		width:25, sortable:true,    title: '<?php _e('Electronic mail'); ?>' },
        	    	{ field:'Observaciones',width:15,					title: '<?php _e('Comments'); ?>'}
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
                detailFormatter:function(index,row){
                    var dg = "clubes-guias-perros-datagrid-" + replaceAll(' ', '_', row.ID);
                    return '<div style="padding:2px"><table id="'+dg+'"></table></div>';
                },
                onExpandRow: function(idx,row) {
                    showPerrosByGuiaByClub(idx,row,club);
                } /*,
                onCollapseRow: function(idx,row) {
                    var dg = "#clubes-guias-perros-datagrid-" + replaceAll(' ', '_', row.ID);
                    $(dg).remove();
                } */
                /* end of clubes-guias-dog subtable */
        	}); // end of '#clubes-guias-datagrid' declaration

        	// definimos inline la sub-barra de tareas para que solo aparezca al desplegar el sub formulario
        	var	toolbar= [{
            		id: 'guiasByClub-newBtn'+club.ID,
            		text: '<?php _e('Join handler'); ?>',
        			iconCls: 'icon-users',
        			handler: function(){ assignGuiaToClub(mySelf,club); }
        		},{
            		id: 'guiasByClub-editBtn'+club.ID,
            		text: '<?php _e('Edit handler'); ?>',
        			iconCls: 'icon-edit',
        			handler: function(){ editGuiaFromClub(mySelf,club); }
        		},{
            		id: 'guiasByClub-delBtn'+club.ID,
            		text: '<?php _e('Dettach handler'); ?>',
        			iconCls: 'icon-remove',
        			handler: function(){ delGuiaFromClub(mySelf,club); }
        		},{
    				id: 'guiasByClub-reloadBtn'+club.ID,
            		text: '<?php _e('Update'); ?>',
        			iconCls: 'icon-reload',
        			align: 'right', // notice that this property is handled by our own 'buildToolbar extended method'
       				handler: function(){ $(mySelf).datagrid('reload'); }    // reload the clubs data}
    			}];
    		$(mySelf).datagrid('buildToolbar',toolbar);
        	$('#clubes-datagrid').datagrid('fixDetailRowHeight',index);
			// tooltips de los sub-formularios
			addTooltip($('#guiasByClub-newBtn'+club.ID).linkbutton(),'<?php _e("Create/Assign handler to club"); ?>'+" '"+club.Nombre+"'");
			addTooltip($('#guiasByClub-editBtn'+club.ID).linkbutton(),'<?php _e("Edit data on handler belonging club"); ?>'+" '"+club.Nombre+"'");
			addTooltip($('#guiasByClub-delBtn'+club.ID).linkbutton(),'<?php _e("Unassign selected handler from club"); ?>'+" '"+club.Nombre+"'");
			addTooltip($('#guiasByClub-reloadBtn'+club.ID).linkbutton(),'<?php _e("Update handlers list on club"); ?>'+" '"+club.Nombre+"'");
            	
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
        	    loadMsg: '<?php _e('Loading list of dogs'); ?>',
        		title: '<?php _e('Registered dogs belonging to'); ?>'+' '+guia.Nombre+' - '+fedName(workingData.federation),
        		url: '../ajax/database/dogFunctions.php',
        		queryParams: { Operation: 'getbyguia', Guia: guia.ID, Federation: workingData.federation },
        		method: 'get',
        	    columns: [[
            	    { field:'ID',		width:15, sortable:true,	title: 'ID' },
            		{ field:'Nombre',	width:30, sortable:true,	title: '<?php _e('Name'); ?>',formatter:formatBoldDog },
                    { field:'NombreLargo',hidden:true},
                    { field:'Baja',hidden:true},
            		{ field:'Categoria',width:15, sortable:false,	title: '<?php _e('Cat.'); ?>' ,formatter:formatCategoria},
            		{ field:'Grado',	width:15, sortable:false,   title: '<?php _e('Grade'); ?>', formatter:formatGrado },
            		{ field:'Raza',		width:25, sortable:false,   title: '<?php _e('Breed'); ?>' },
					{ field:'Genero',	width:10, sortable:false,   title: '<?php _e('Gender'); ?>' },
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
				id: 'perrosByGuiaByClub-newBtn'+guia.ID+'_'+club.ID,
				text: '<?php _e('Assign dog'); ?>',
				iconCls: 'icon-dog',
				handler: function(){ assignPerroToGuia(mySelf,guia ); }
            },{
				id: 'perrosByGuiaByClub-editBtn'+guia.ID+'_'+club.ID,
				text: '<?php _e('Edit dog'); ?>',
				iconCls: 'icon-edit',
				handler: function(){editPerroFromGuia(mySelf,guia);}
			},{
				id: 'perrosByGuiaByClub-delBtn'+guia.ID+'_'+club.ID,
				text: '<?php _e('Dettach dog'); ?>',
				iconCls: 'icon-remove',
				handler: function(){delPerroFromGuia(mySelf,guia);}
			},{
				id: 'perrosByGuiaByClub-reloadBtn'+guia.ID+'_'+club.ID,
        		text: '<?php _e('Update'); ?>',
    			iconCls: 'icon-reload',
    			align: 'right', // notice that this property is handled by our own 'buildToolbar extended method'
   				handler: function(){ $(mySelf).datagrid('reload'); }    // reload the clubs data}
			}];
			// add toolbar to datagrid
    		$(mySelf).datagrid('buildToolbar',toolbar);
    		// tell parent to fix rendering ob subgrid
        	$(parent).datagrid('fixDetailRowHeight',index);

			// tooltips de los sub-formularios
			addTooltip($('#perrosByGuiaByClub-newBtn'+guia.ID+'_'+club.ID).linkbutton(),'<?php _e("Declare/Assign a dog to"); ?>'+" '"+guia.Nombre+"'");
			addTooltip($('#perrosByGuiaByClub-editBtn'+guia.ID+'_'+club.ID).linkbutton(),'<?php _e("Edit data on dog belonging to"); ?>'+" '"+guia.Nombre+"'");
			addTooltip($('#perrosByGuiaByClub-delBtn'+guia.ID+'_'+club.ID).linkbutton(),'<?php _e("Unassign selected dog from handler"); ?>'+" '"+guia.Nombre+"'");
			addTooltip($('#perrosByGuiaByClub-reloadBtn'+guia.ID+'_'+club.ID).linkbutton(),'<?php _e("Update list of dogs belonging to"); ?>'+" '"+guia.Nombre+"'");
        } // end of showPerrosByGuia
</script>

