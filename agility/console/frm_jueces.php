<!-- 
frm_jueces.php

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

<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE JUECES -->
<div  style="width:100%;height:550px">
    <!-- DECLARACION DE LA TABLA DE JUECES -->
    <table id="jueces-datagrid">  </table>
</div> 

<!-- BARRA DE TAREAS DE LA TABLA DE JUECES -->
<div id="jueces-toolbar" style="width:100%;display:inline-block">
 	<span style="float:left;padding:5px">
   		<a id="jueces-newBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-whistle'"
   			onclick="newJuez('#jueces-datagrid',$('#jueces-datagrid-search').val())"><?php _e('New judge'); ?></a>
   		<a id="jueces-editBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-edit'"
   			onclick="editJuez('#jueces-datagrid')"><?php _e('Edit judge'); ?></a>
   		<a id="jueces-delBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-trash'"
   			onclick="deleteJuez('#jueces-datagrid')"><?php _e('Delete judge'); ?></a>
   		<input id="jueces-datagrid-search" type="text" value="<?php _e('-- Search --'); ?>" class="search_textfield"
			   onfocus="handleSearchBox(this,true);" onblur="handleSearchBox(this,false);"/>
   	</span>
   	<span style="float:right;padding:5px">
   		<a id="jueces-reloadBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-brush'"
   			onclick="
   	        	// clear selection and reload table
				reloadWithSearch('#jueces-datagrid','select',true);
				"
   			><?php _e('Clear'); ?></a>
   	</span>
</div>

<?php require_once("dialogs/dlg_jueces.inc")?>
    
<script type="text/javascript">
        
        // datos de la tabla de jueces
        $('#jueces-datagrid').datagrid({
            // datos del panel padre asociado
        	fit: true,
        	border: false,
        	closable: true,
        	collapsible: false,
            expansible: false,
        	collapsed: false,
        	title: '<?php _e('Judge database handling'); ?>',
        	// datos de la conexion ajax
        	url: '/agility/server/database/juezFunctions.php',
    		queryParams: { Operation: 'select' },
        	loadMsg: '<?php _e('Updating judge list'); ?> ...',
        	method: 'get',
            toolbar: '#jueces-toolbar',
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
                { field:'ID',			hidden:true },
                { field:'Nombre',		width:40, sortable:true,	title: '<?php _e('Name'); ?>',formatter:formatBold },
            	{ field:'Direccion1',	width:35,					title: '<?php _e('Address'); ?> 1' },
                { field:'Direccion2',	width:30,                   title: '<?php _e('Address'); ?> 2' },
                { field:'Pais',	        width:7, sortable:true,     align:'center',   title: '<?php _e('Country'); ?>' },
            	{ field:'Telefono',		width:25, sortable:true,	title: '<?php _e('Telephone'); ?>' },
              	{ field:'Federations',	hidden:true},
        		{ field:'RSCE',			width:6, sortable:true,    align: 'center', title: '<?php _e('RSCE'); ?>', formatter:juecesRSCE },
                { field:'RFEC',			width:6, sortable:true,    align: 'center', title: '<?php _e('RFEC'); ?>', formatter:juecesRFEC },
                { field:'CPC',			width:6, sortable:true,    align: 'center', title: '<?php _e('CPC');  ?>', formatter:juecesCPC },
                { field:'Nat3',			width:6, sortable:true,    align: 'center', title: '<?php _e('Nat3'); ?>', formatter:juecesNat3 },
                { field:'Nat4',			width:6, sortable:true,    align: 'center', title: '<?php _e('Nat4'); ?>', formatter:juecesNat4 },
            	{ field:'Internacional',width:7, align:'center',	title: '<?php _e('Intl'); ?>.' 	},
            	{ field:'Practicas',	width:7, align:'center',	title: '<?php _e('Pract'); ?>.' },
            	{ field:'Email',		width:30, sortable:true,   align:'right', title: '<?php _e('Electronic mail'); ?>' },
                { field:'Observaciones',hidden:true }
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler,
        	// on double click fireup editor dialog
            onDblClickRow:function() { 
                editJuez('#jueces-datagrid');
            }
        });

		// key handler
       	addKeyHandler('#jueces-datagrid',null,newJuez,editJuez,deleteJuez);
		// tooltips
		addTooltip($('#jueces-newBtn').linkbutton(),'<?php _e("Add a new judge to database"); ?>');
		addTooltip($('#jueces-editBtn').linkbutton(),'<?php _e("Modify data on selected judge"); ?>');
		addTooltip($('#jueces-delBtn').linkbutton(),'<?php _e("Remove selected judge from database"); ?>');
		addTooltip($('#jueces-reloadBtn').linkbutton(),'<?php _e("Clear search box. Update list"); ?>');
		addTooltip($('#jueces-datagrid-search'),'<?php _e("Look for judges matching search criteria"); ?>');

</script>