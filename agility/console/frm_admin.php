<!-- 
frm_admin.php

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

<?php
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
$config =Config::getInstance();
?>

 <div id="admin-tab" class="easyui-tabs" style="width:100%;height:550px;">
   	<div title="<?php _e('Users'); ?>" data-options="iconCls:'icon-users'" style="padding:5px;border:solid 1px #000000">

	   	<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE USUARIOS -->
		<div  style="width:100%;height:500px">   
	    <!-- DECLARACION DE LA TABLA DE USUARIOS -->
	    	<table id="usuarios-datagrid">  </table>
		</div> 

		<!-- BARRA DE TAREAS DE LA TABLA DE USUARIOS -->
		<div id="usuarios-toolbar" style="width:100%;display:inline-block">
 			<span style="float:left;padding:5px">
   				<a id="usuarios-newBtn" href="#" class="easyui-linkbutton"
   					data-options="iconCls:'icon-users'"
   					onclick="newUser('#usuarios-datagrid',$('#usuarios-datagrid-search').val())"><?php _e('New user'); ?></a>
   				<a id="usuarios-editBtn" href="#" class="easyui-linkbutton" 
   					data-options="iconCls:'icon-edit'"
   					onclick="editUser('#usuarios-datagrid')"><?php _e('Edit user'); ?></a>
   				<a id="usuarios-delBtn" href="#" class="easyui-linkbutton" 
   					data-options="iconCls:'icon-trash'"
   					onclick="deleteUser('#usuarios-datagrid')"><?php _e('Delete user'); ?></a>
   				<input id="usuarios-datagrid-search" type="text" value="<?php _e('-- Search --'); ?>" class="search_textfield"	/>
   			</span>
   			<span style="float:right;padding:5px">
   				<a id="usuarios-keyBtn" href="#" class="easyui-linkbutton" 
   					data-options="iconCls:'icon-key'"
   					onclick="setPassword('#usuarios-datagrid')"><?php _e('Password'); ?></a>
   				<a id="usuarios-reloadBtn" href="#" class="easyui-linkbutton"
   					data-options="iconCls:'icon-brush'"
   					onclick="
   			        	// clear selection and reload table
   			    		$('#usuarios-datagrid-search').val('<?php _e('-- Search --'); ?>');
   			            $('#usuarios-datagrid').datagrid('load',{ where: '' });"
   					><?php _e('Clear'); ?></a>
   			</span>
		</div>
    	<?php require_once("dialogs/dlg_usuarios.inc")?>
   	</div>
   	<div title="<?php _e('Sessions'); ?>" data-options="iconCls:'icon-order'" style="padding:5px">
   	   	 	
	   	<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE SESIONES -->
		<div  style="width:100%;height:500px">   
	    <!-- DECLARACION DE LA TABLA DE SESIONES -->
	    	<table id="sesiones-datagrid">  </table>
		</div> 

		<!-- BARRA DE TAREAS DE LA TABLA DE SESIONES -->
		<div id="sesiones-toolbar" style="width:100%;display:inline-block">
 			<span style="float:left;padding:5px">
   				<a id="sesiones-newBtn" href="#" class="easyui-linkbutton"
   					data-options="iconCls:'icon-order'"
   					onclick="newSession('#sesiones-datagrid',$('#sesiones-datagrid-search').val())"><?php _e('New session'); ?></a>
   				<a id="sesiones-editBtn" href="#" class="easyui-linkbutton" 
   					data-options="iconCls:'icon-edit'"
   					onclick="editSession('#sesiones-datagrid')"><?php _e('Edit session'); ?></a>
   				<a id="sesiones-delBtn" href="#" class="easyui-linkbutton" 
   					data-options="iconCls:'icon-trash'"
   					onclick="deleteSession('#sesiones-datagrid')"><?php _e('Delete session'); ?></a>
   				<a id="sesiones-resetBtn" href="#" class="easyui-linkbutton" 
   					data-options="iconCls:'icon-redo'"
   					onclick="resetSession('#sesiones-datagrid')"><?php _e('Reset session'); ?></a>
   				<input id="sesiones-datagrid-search" type="text" value="<?php _e('-- Search --'); ?>" class="search_textfield"	/>
   			</span>
   			<span style="float:right;padding:5px">
   				<a id="sesiones-reloadBtn" href="#" class="easyui-linkbutton"
   					data-options="iconCls:'icon-brush'"
   					onclick="
   			        	// clear selection and reload table
   			    		$('#sesiones-datagrid-search').val('<?php _e('-- Search --'); ?>');
   			            $('#sesiones-datagrid').datagrid('load',{ where: '' });"
   					><?php _e('Clear'); ?></a>
   			</span>
		</div>
    	<?php require_once("dialogs/dlg_sesiones.inc")?>
   	</div>
   	<div title="<?php _e('Preferences'); ?>" data-options="iconCls:'icon-setup'" style="padding:5px">
    	<?php require_once("dialogs/dlg_configuracion.inc")?>
   	</div>
   	<div title="<?php _e('Tools'); ?>" data-options="iconCls:'icon-tools'" style="padding:5px">
    	<?php require_once("dialogs/dlg_tools.inc")?>
   	</div>
 </div>
 
 <script type="text/javascript">

 // datos de la tabla de usuarios
 $('#usuarios-datagrid').datagrid({
     // datos del panel padre asociado
 	fit: true,
 	border: false,
 	closable: false,
 	collapsible: false,
    expansible: false,
 	collapsed: false,
 	title: '<?php _e('User data management'); ?>',
 	// datos de la conexion ajax
 	url: '/agility/server/database/userFunctions.php?Operation=select',
 	loadMsg: '<?php _e('Updating users list'); ?>',
 	method: 'get',
    toolbar: '#usuarios-toolbar',
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    view: scrollview,
    pageSize: 50,
    multiSort: true,
    remoteSort: true,
    columns: [[
        { field:'ID',		hidden:true },
        { field:'Login',	width:25, sortable:true,	title:'Login' },
     	{ field:'Gecos',	width:55, sortable:true,	title:'<?php _e('Information'); ?>' },
     	{ field:'Phone',	width:20, 					title:'<?php _e('Telephone'); ?>' },
     	{ field:'Email',	width:30, sortable:true,   	title:'E-mail' },
        { field:'Perms',	width:20,					title:'<?php _e('Category'); ?>', formatter:formatPermissions }
    ]],
    // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    rowStyler:myRowStyler,
 	// on double click fireup editor dialog
    onDblClickRow:function() { 
         editUser('#usuarios-datagrid');
    }
 });
 
 // datos de la tabla de usuarios
 $('#sesiones-datagrid').datagrid({
     // datos del panel padre asociado
 	fit: true,
 	border: false,
 	closable: false,
 	collapsible: false,
    expansible: false,
 	collapsed: false,
 	title: '<?php _e('Session (ring) data management'); ?>',
 	// datos de la conexion ajax
 	url: '/agility/server/database/sessionFunctions.php?Operation=select',
 	loadMsg: '<?php _e('Updating session list'); ?>',
 	method: 'get',
    toolbar: '#sesiones-toolbar',
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    view: scrollview,
    pageSize: 50,
    multiSort: true,
    remoteSort: true,
    columns: [[
        { field:'ID',		hidden:true },
        { field:'Nombre',		width:25, sortable:true,title:'<?php _e('Name'); ?>' },
     	{ field:'Comentario',	width:55, sortable:true,title:'<?php _e('Description'); ?>' },
        { field:'Operador',		hidden:true },
     	{ field:'Login',		width:25, sortable:true,title:'<?php _e('User'); ?>' },
     	{ field:'Background',	width:30,				title:'Stream MJPEG' },
     	{ field:'LiveStream',	width:30,				title:'Stream h264' },
     	{ field:'LiveStream2',	width:30,   			title:'Stream Ogg' },
        { field:'LiveStream3',	width:30,				title:'Stream WebM' }
    ]],
    // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    rowStyler:myRowStyler,
 	// on double click fireup editor dialog
    onDblClickRow:function() { 
         editSession('#sesiones-datagrid');
    }
 });

// key handler ( notify "null" dialog cause we dont want to close tabs on escape :-)
addKeyHandler('#usuarios-datagrid',null,newUser,editUser,deleteUser);
addKeyHandler('#sesiones-datagrid',null,newSession,editSession,deleteSession);
// tooltips
addTooltip($('#usuarios-newBtn').linkbutton(),'<?php _e("Create and insert new user<br/> into database"); ?>');
addTooltip($('#usuarios-editBtn').linkbutton(),'<?php _e("Modify data on selected user"); ?>');
addTooltip($('#usuarios-delBtn').linkbutton(),'<?php _e("Remove selected user from database"); ?>');
addTooltip($('#usuarios-reloadBtn').linkbutton(),'<?php _e("Clear search box. Update data"); ?>');
addTooltip($('#usuarios-keyBtn').linkbutton(),'<?php _e("Change user password"); ?>');
addTooltip($('#usuarios-datagrid-search'),"Look for users matching search criteria");
// tooltips
addTooltip($('#sesiones-newBtn').linkbutton(),'<?php _e("Add a new session (ring)<br/>into data base"); ?>');
addTooltip($('#sesiones-editBtn').linkbutton(),'<?php _e("Modify data on selected session (ring)"); ?>');
addTooltip($('#sesiones-delBtn').linkbutton(),'<?php _e("Remove selected session from database"); ?>');
addTooltip($('#sesiones-resetBtn').linkbutton(),'<?php _e("Reset/Clear event log from selected session"); ?>');
addTooltip($('#sesiones-reloadBtn').linkbutton(),'<?php _e("Clear search box. Update data"); ?>');
addTooltip($('#sesiones-datagrid-search'),'<?php _e("Look for sessions matching search criteria"); ?>');

 </script>