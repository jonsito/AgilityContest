<!-- 
frm_perros.php

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
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
$config =Config::getInstance();
?>

<!-- Ventana de seleccion de fichero para importacion de datos excel -->
<div id="perros-excel-dialog" style="width:640px;height:auto;padding:10px; display=none;">
	<?php require_once(__DIR__."/../lib/templates/import_dialog.inc.php"); ?>
</div>

<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO DE IMPORTACION -->
<div id="perros-excel-buttons">
	<a id="perros-excel-okBtn" href="#" class="easyui-linkbutton"
	   data-options="iconCls: 'icon-ok'" onclick="perros_excelImport()"><?php _e('Import'); ?></a>
	<a id="perros-excel-cancelBtn" href="#" class="easyui-linkbutton"
	   data-options="iconCls: 'icon-cancel'" onclick="$('#perros-excel-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
</div>

<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE PERROS -->
<div style="width:100%;height:550px;">
    <!-- DECLARACION DE LA TABLA -->
    <table id="perros-datagrid"></table>
</div>    
 
<!-- BARRA DE TAREAS -->
<div id="perros-toolbar" style="width:100%;display:inline-block">
   	<span style="float:left;padding:5px">
   		<a id="perros-newBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-dog'"
   			onclick="newDog('#perros-datagrid',$('#perros-datagrid-search').val())"><?php _e('New dog'); ?></a>
   		<a id="perros-editBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-edit'"
   			onclick="editDog('#perros-datagrid')"><?php _e('Edit dog'); ?></a>
   		<a id="perros-delBtn" href="#" class="easyui-linkbutton"
		   data-options="iconCls:'icon-trash'"
		   onclick="deleteDog('#perros-datagrid')"><?php _e('Delete dog'); ?></a>
   		<a id="perros-joinBtn" href="#" class="easyui-linkbutton"
		   data-options="iconCls:'icon-sum'"
		   onclick="joinDog('#perros-datagrid')"><?php _e('Join dog'); ?></a>
        <a id="perros-dupBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-duplicate'"
           onclick="reloadWithSearch('#perros-datagrid','duplicates',true);"><?php _e('Duplicates'); ?></a>
   		<input id="perros-datagrid-search" type="text" value="<?php _e('-- Search --'); ?>" class="search_textfield"
			   onfocus="handleSearchBox(this,true);" onblur="handleSearchBox(this,false);"/>
   		<a id="perros-reloadBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-brush'"
           onclick="
   	        	// clear selection and reload table
				reloadWithSearch('#perros-datagrid','select',true);
   	            "
        ><?php _e('Clear'); ?></a>
   	</span>
   	<span style="float:right;padding:5px">
   		<a id="perros-excelBtn" href="#" class="easyui-linkbutton"
		   data-options="iconCls:'icon-db_restore'"
		   onclick="perros_importExportDogs()"><?php _e('Import/Export'); ?></a>
   		<a id="perros-printBtn" href="#" class="easyui-linkbutton"
		   data-options="iconCls:'icon-print'"
		   onclick="print_listaPerros('pdf')"><?php _e('Print'); ?></a>
   	</span>
</div>
    
<?php require_once("dialogs/dlg_perros.inc"); ?>
<?php require_once("dialogs/dlg_guias.inc");?>
<?php require_once("dialogs/dlg_clubes.inc");?>
    
<script type="text/javascript">

    // tell jquery to convert declared elements to jquery easyui Objects
	$('#perros-excel-dialog').dialog( {
		title:' <?php _e('Import dogs information from Excel file'); ?>',
		closed:true,
		modal:true,
		buttons:'#perros-excel-buttons',
		iconCls:'icon-table',
		onOpen: function() {
			ac_import.type='perros';
			$('#import-excel-progressbar').progressbar('setValue',"");
		},
		onClose: function() { ac_import.progress_status='paused'; }
	} );

        // datos de la tabla de perros
        // - tabla
        $('#perros-datagrid').datagrid({
        	// propiedades del panel padre asociado
        	fit: true,
        	border: false,
        	closable: true,
        	collapsible: false,
            expansible: false,
        	collapsed: false,
        	title: '<?php _e('Database dog handling'); ?>'+' - '+fedName(workingData.federation),
        	url: '../ajax/database/dogFunctions.php',
        	queryParams: { Operation: 'select', Federation: workingData.federation },
        	loadMsg: '<?php _e('Updating dog list'); ?>...',
        	method: 'get',
            toolbar: '#perros-toolbar',
            pagination: false,
            rownumbers: true,
            fitColumns: true,
			idField: 'ID',
            singleSelect: false,
            view: scrollview,
            pageSize: 50,
            multiSort: true,
            remoteSort: true,
            columns: [[
                { field:'ID',   hidden:true },
                { field:'Federation', hidden:true },
            	{ field:'Nombre',   width:18, sortable:true,  align: 'left', title: '<?php _e('Name'); ?>', formatter:formatBoldDog },
				{ field:'NombreLargo', width:35, sortable:true,  align: 'left', title: '<?php _e('Pedigree'); ?>', formatter:formatBold },
            	{ field:'Raza',     width:25,                align: 'right', title: '<?php _e('Breed'); ?>' },
				{ field:'Genero', 	width:10, sortable:true, align: 'center', title: '<?php _e('Gender'); ?>.' },
            	{ field:'LOE_RRC',  width:16, sortable:true, align: 'right', title: '<?php _e('KC id'); ?>' },
            	{ field:'Licencia', width:10, sortable:true, align: 'right', title: '<?php _e('Lic'); ?>.' },
            	{ field:'Categoria',width:8, sortable:true,  align:'center', title: '<?php _e('Cat'); ?>.',formatter:formatCategoria },
            	{ field:'Grado',    width:8, sortable:true,  align:'center', title: '<?php _e('Grade'); ?>', formatter:formatGrado },
                { field:'Baja',   hidden:true },
                { field:'Guia',   hidden:true },
                { field:'NombreGuia',     width:40, sortable:true, title: '<?php _e('Handler name'); ?>'},
            	{ field:'Club',   hidden:true },
                { field:'NombreClub',     width:25, sortable:true, title: '<?php _e('Club name'); ?>'}
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler,
        	// on double click fireup editor dialog
            onDblClickRow:function() { 
                editDog('#perros-datagrid');
            }
        });
		
		// key handler
       	addKeyHandler('#perros-datagrid',"",newDog,editDog,deleteDog);
		// tooltips
		addTooltip($('#perros-newBtn').linkbutton(),'<?php _e("Declare new dog <br/>into database"); ?>');
		addTooltip($('#perros-editBtn').linkbutton(),'<?php _e("Modify data on selected dog"); ?>');
		addTooltip($('#perros-delBtn').linkbutton(),'<?php _e("Remove selected dog(s) from database"); ?>');
        addTooltip($('#perros-joinBtn').linkbutton(),'<?php _e("Mark dogs as duplicated and join them in the database"); ?>');
        addTooltip($('#perros-dupBtn').linkbutton(),'<?php _e("List dogs having duplicate license number"); ?>');
		addTooltip($('#perros-excelBtn').linkbutton(),'<?php _e("Import/Export dog data from/to Excel file"); ?>');
		addTooltip($('#perros-printBtn').linkbutton(),'<?php _e("Print dog list with current search/sort criteria"); ?>');
        addTooltip($('#perros-reloadBtn').linkbutton(),'<?php _e("Clear search box. Update list"); ?>');
        addTooltip($('#perros-datagrid-search'),'<?php _e("Look into database for dogs matching search criteria"); ?>');
		addTooltip($('#perros-excel-okBtn').linkbutton(),'<?php _e("Import dog data from selected Excel file"); ?>');
		addTooltip($('#perros-excel-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
</script>