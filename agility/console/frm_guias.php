<!-- 
frm_guias.php

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

<?php
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>
 
<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE GUIAS -->
<div style="width:975px;height:550px;padding:5px">
    <!-- DECLARACION DE LA TABLA -->
    <table id="guias-datagrid">  </table>
</div>

<!-- BARRA DE TAREAS DE LA TABLA DE GUIAS -->
<div id="guias-toolbar" style="width:100%;display:inline-block">
   	<span style="float:left;padding:5px">
       	<a id="guias-newBtn" href="#" class="easyui-linkbutton"
       		data-options="iconCls:'icon-users'"
       		onclick="newGuia($('#guias-datagrid-search').val(),reload_guiasDatagrid)"><?php _e('New handler'); ?></a>
       	<a id="guias-editBtn" href="#" class="easyui-linkbutton"
       		data-options="iconCls:'icon-edit'"
       		onclick="editGuia('#guias-datagrid')"><?php _e('Edit handler'); ?></a>
       	<a id="guias-delBtn" href="#" class="easyui-linkbutton" 
       		data-options="iconCls:'icon-trash'"
       		onclick="deleteGuia('#guias-datagrid')"><?php _e('Delete handler'); ?></a>
   		<input id="guias-datagrid-search" type="text" value="<?php _e('-- Search --'); ?>" class="search_textfield"
			   onfocus="handleSearchBox(this,true);" onblur="handleSearchBox(this,false);"/>
	</span>
	<span style="float:right;padding:5px">
   		<a id="guias-reloadBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-brush'"
   			onClick="
   	        	// clear selection and reload table
   				reloadWithSearch('#guias-datagrid','select',true);
   				"
   			><?php _e('Clear'); ?></a>
	</span>
</div>
    
<?php require_once("dialogs/dlg_guias.inc"); ?>
<?php require_once("dialogs/dlg_clubes.inc"); ?>
<?php require_once("dialogs/dlg_perros.inc"); ?>
<?php require_once("dialogs/dlg_chperros.inc"); ?>
    
<script type="text/javascript">
    // tell jquery to convert declared elements to jquery easyui Objects
    // datos de la tabla de guias
    // - tabla
    $(function(){
        $('#guias-datagrid').datagrid({
            // propiedades del panel padre asociado
            fit: true,
            border: false,
            closable: true,
            collapsible: false,
            expansible: false,
            collapsed: false,
            title: '<?php _e('Handlers data management');?>'+' - '+fedName(workingData.federation),
            url: '../ajax/database/guiaFunctions.php',
            queryParams: { Operation:'select', Federation: workingData.federation },
            loadMsg: '<?php _e('Updating handlers list');?>'+'...',
            method: 'get',
            toolbar: '#guias-toolbar',
            pagination: false,
            rownumbers: true,
            singleSelect: true,
            fitColumns: true,
            idField: 'ID',
            view: scrollview,
            pageSize: 50,
            multiSort: true,
            remoteSort: true,
            columns: [[
                { field:'ID',			hidden:true },
                { field:'Federation',	hidden:true },
                { field:'Nombre',		width:30, sortable:true,	title: '<?php _e('Name');?>', formatter:formatBold },
                { field:'Categoria',    width:10, sortable:true,    title: '<?php _e('Category');?>',formatter:formatCatGuia },
                { field:'Club',			hidden:true},
                { field:'NombreClub',	width:20, sortable:true,	title: '<?php _e('Club');?>'},
                { field:'Telefono',		width:10, sortable:true,	title: '<?php _e('Phone');?>' },
                { field:'Email',		width:15, sortable:true,    title: '<?php _e('E-mail');?>' },
                { field:'Observaciones',width:30,					title: '<?php _e('Comments');?>'}
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler,
            // on double click fireup editor dialog
            onDblClickRow:function() {
                editGuia('#guias-datagrid');
            },
            // especificamos un formateador especial para desplegar la tabla de perros por guia
            detailFormatter:function(idx,row){
                var dg="guias-perros-datagrid-" + replaceAll(' ','_',row.ID);
                return '<div style="padding:2px"><table id="'+dg+'"></table></div>';
            },
            onExpandRow: function(idx,row) { // on scrollview row may be undefined, so ignore
                var r = $(this).datagrid('getRow',idx);
                showPerrosByGuia(idx,r);
            } /*,
            onCollapseRow: function(idx,row) {
                var dg = "#guias-perros-datagrid-" + replaceAll(' ', '_', row.ID);
                $(dg).remove();
            }*/

        }); // end of guias-datagrid
    });

		// key handler
       	addKeyHandler('#guias-datagrid',"",newGuia,editGuia,deleteGuia);
		// tooltips
		addTooltip($('#guias-newBtn').linkbutton(),'<?php _e("Insert a new handler<br/>into DataBase");?>');
		addTooltip($('#guias-editBtn').linkbutton(),'<?php _e("Edit data on selected handler");?>');
		addTooltip($('#guias-delBtn').linkbutton(),'<?php _e("Remove selected handler from Database");?>');
		addTooltip($('#guias-reloadBtn').linkbutton(),'<?php _e("Clear search box. Update table");?>');
		addTooltip($('#guias-datagrid-search'),'<?php _e("Show handlers matching search criteria");?>');

		// mostrar los perros asociados a un guia
        function showPerrosByGuia(index,guia){
        	// - sub tabla de perros asignados a un guia
        	var mySelf='#guias-perros-datagrid-'+replaceAll(' ','_',guia.ID);
			$(mySelf).datagrid({
            	width: 875,
        	    height: 'auto',
        		title: '<?php _e('Dogs registered to');?>'+' '+guia.Nombre+ ' - '+fedName(workingData.federation),
       		    pagination: false,
        	    rownumbers: false,
        	    fitColumns: true,
        	    singleSelect: true,
				loadMsg: '<?php _e('Updating dogs list');?>',
        		url: '../ajax/database/dogFunctions.php',
        		queryParams: { Operation: 'getbyguia', Guia: guia.ID, Federation: workingData.federation },
        		method: 'get',
        	    columns: [[
                   	{ field:'ID',	width:15, sortable:true,	title: 'ID'},
                	{ field:'Federation',hidden:true},
            		{ field:'Nombre',	width:30, sortable:true,	title: '<?php _e('Name');?>', formatter:formatBold },
					{ field:'NombreLargo',hidden:true},
            		{ field:'Categoria',width:15, sortable:false,	title: '<?php _e('Cat');?>' ,formatter:formatCategoria},
            		{ field:'Grado',	width:15, sortable:false,   title: '<?php _e('Grade');?>', formatter:formatGrado },
            		{ field:'Raza',		width:25, sortable:false,   title: '<?php _e('Breed');?>' },
					{ field:'Genero',	width:10, sortable:false,   align:'center', title: '<?php _e('Gender');?>' },
            		{ field:'LOE_RRC',	width:25, sortable:true,    title: '<?php _e('KC id');?>' },
            		{ field:'Licencia',	width:25, sortable:true,    title: '<?php _e('License');?>' }
            	]],
            	// colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            	rowStyler:myRowStyler,
            	// on double click fireup editor dialog
                onDblClickRow:function(idx,row) { //idx: selected row index; row selected row data
                    editPerroFromGuia(mySelf,guia);
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

    		// definimos inline la sub-barra de tareas para que solo aparezca al desplegar el sub formulario
    		// toolbar: '#perrosbyguia-toolbar', 
			var toolbar = [{
				id: 'perrosbyguia-newBtn'+guia.ID,
				text: '<?php _e('Assign dog');?>',
				iconCls: 'icon-dog',
				handler: function(){assignPerroToGuia(mySelf,guia); }
			},{
				id: 'perrosbyguia-editBtn'+guia.ID,
				text: '<?php _e('Edit dog');?>',
				iconCls: 'icon-edit',
				handler: function(){editPerroFromGuia(mySelf,guia); }
			},{
				id: 'perrosbyguia-delBtn'+guia.ID,
				text: '<?php _e('Dettach dog');?>',
				iconCls: 'icon-trash',
				handler: function(){delPerroFromGuia(mySelf,guia); }
			},{
				id: 'perrosbyguia-reloadBtn'+guia.ID,
        		text: '<?php _e('Update');?>',
    			iconCls: 'icon-reload',
    			align: 'right', // notice that this property is handled by our own 'buildToolbar extended method'
   				handler: function(){ $(mySelf).datagrid('reload'); }    // reload the clubs data}
			}];
    		$(mySelf).datagrid('buildToolbar',toolbar); // programmatically add toolbar to datagrid
        	$('#guias-datagrid').datagrid('fixDetailRowHeight',index);
			// tooltips de los sub-formularios
			addTooltip($('#perrosbyguia-newBtn'+guia.ID).linkbutton(),'<?php _e("Assign dog to handler");?>'+" '"+guia.Nombre+"'");
			addTooltip($('#perrosbyguia-delBtn'+guia.ID).linkbutton(),'<?php _e("Dettach dog from handler");?>'+" '"+guia.Nombre+"'");
			addTooltip($('#perrosbyguia-editBtn'+guia.ID).linkbutton(),'<?php _e("Editar data on dog assigned to");?>'+" '"+guia.Nombre+"'");
			addTooltip($('#perrosbyguia-reloadBtn'+guia.ID).linkbutton(),'<?php _e("Update dog list from handler");?>'+" '"+guia.Nombre+"'");
        } // end of showPerrosByGuia
</script>
