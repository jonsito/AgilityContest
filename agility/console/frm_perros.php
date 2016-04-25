<!-- 
frm_perros.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
<div id="perros-excel-dialog" style="width:500px;height:auto;padding:10px; display=none;">
	<div style="width=100%">
		<p>
			<?php _e("Database backup is recommended before import");?> &nbsp; &nbsp;
			<input type="button" class="icon_button icon-db_backup" name="<?php _e('Backup');?>" value="<?php _e('Backup');?>" onClick="backupDatabase();"/>
		</p>
		<hr />
		<p>
			<?php _e("Select Excel file to retrieve Dog data from");?><br />
			<?php _e("Press return to start, or cancel to abort import"); ?>
			<br />&nbsp;<br />
			<input type="file" name="perros-excel" value="" id="perros-excel-fileSelect"
				   class="icon_button icon-search"
			   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" onchange="read_excelFile(this)">
			<input id="perros-excelData" type="hidden" name="excelData" value="">
			<br />&nbsp;<br />
			</p>
		<p>
			<span style="float:left"><?php _e('Import status'); ?>:	</span>
			<span id="perros-excel-progressbar" style="float:right;text-align:center;"></span>
		</p>
	</div>
</div>

<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
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
   		<input id="perros-datagrid-search" type="text" value="<?php _e('-- Search --'); ?>" class="search_textfield"	/>
   	</span>
   	<span style="float:right;padding:5px">
   		<a id="perros-excelBtn" href="#" class="easyui-linkbutton"
		   data-options="iconCls:'icon-db_restore'"
		   onclick="perros_importExportDogs()"><?php _e('Import/Export'); ?></a>
   		<a id="perros-printBtn" href="#" class="easyui-linkbutton"
		   data-options="iconCls:'icon-print'"
		   onclick="print_listaPerros('pdf')"><?php _e('Print'); ?></a>
   		<a id="perros-reloadBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-brush'"
   			onclick="
   	        	// clear selection and reload table
				reloadWithSearch('#perros-datagrid','select',true);
   	            "
   		><?php _e('Clear'); ?></a>
   	</span>
</div>
    
<?php require_once("dialogs/dlg_perros.inc"); ?>
<?php require_once("dialogs/dlg_guias.inc");?>
<?php require_once("dialogs/dlg_clubes.inc");?>
    
<script type="text/javascript">
        
        // tell jquery to convert declared elements to jquery easyui Objects
		$('#perros-excel-dialog').dialog( {
			title:'<?php _e('Excel import'); ?>',
			closed:true,
			modal:true,
			buttons:'#perros-excel-buttons',
			iconCls:'icon-table'
		} );

		$('#perros-excel-progressbar').progressbar({
			width: '70%',
			value: 0,
			//text: '{value} '+'<?php _e("entries"); ?>'
			text: '{value}'
		});

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
        	url: '/agility/server/database/dogFunctions.php',
        	queryParams: { Operation: 'select', Federation: workingData.federation },
        	loadMsg: '<?php _e('Updating dog list'); ?>...',
        	method: 'get',
            toolbar: '#perros-toolbar',
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
                { field:'ID',   hidden:true },
                { field:'Federation', hidden:true },
            	{ field:'Nombre',   width:30, sortable:true,  align: 'right', title: '<?php _e('Name'); ?>', formatter:formatBold },
				{ field:'NombreLargo', hidden:true },
				{ field:'Genero', hidden:true },
            	{ field:'Raza',     width:25,                align: 'right', title: '<?php _e('Breed'); ?>' },
            	{ field:'LOE_RRC',  width:20, sortable:true, align: 'right', title: '<?php _e('KC id'); ?>' },
            	{ field:'Licencia', width:15, sortable:true, align: 'right', title: '<?php _e('Lic'); ?>.' },
            	{ field:'Categoria',width:10,                 align:'center', title: '<?php _e('Cat'); ?>.' },
            	{ field:'Grado',    width:10,                 align:'center', title: '<?php _e('Grade'); ?>' },
            	{ field:'Guia',   hidden:true },
                { field:'NombreGuia',     width:50, sortable:true, title: '<?php _e('Handler name'); ?>'},
            	{ field:'Club',   hidden:true },
                { field:'NombreClub',     width:35, sortable:true, title: '<?php _e('Club name'); ?>'}
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler,
        	// on double click fireup editor dialog
            onDblClickRow:function() { 
                editDog('#perros-datagrid');
            }
        });
		
		// key handler
       	addKeyHandler('#perros-datagrid',null,newDog,editDog,deleteDog);
		// tooltips
		addTooltip($('#perros-newBtn').linkbutton(),'<?php _e("Insert new dog <br/>into database"); ?>');
		addTooltip($('#perros-editBtn').linkbutton(),'<?php _e("Modify data on selected dog"); ?>');
		addTooltip($('#perros-delBtn').linkbutton(),'<?php _e("Remove selected dog from database"); ?>');
		addTooltip($('#perros-excelBtn').linkbutton(),'<?php _e("Import/Export dog data from/to Excel file"); ?>');
		addTooltip($('#perros-printBtn').linkbutton(),'<?php _e("Print dog list with current search/sort criteria"); ?>');
        addTooltip($('#perros-reloadBtn').linkbutton(),'<?php _e("Clear search box. Update list"); ?>');
        addTooltip($('#perros-datagrid-search'),'<?php _e("Look into database for dogs matching search criteria"); ?>');
		addTooltip($('#perros-excel-okBtn').linkbutton(),'<?php _e("Import dog data from selected Excel file"); ?>');
		addTooltip($('#perros-excel-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
</script>