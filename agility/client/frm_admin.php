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

 <div id="admin-tab" class="easyui-tabs" style="width:100%;height:auto;">
   	<div title="Usuarios" data-options="iconCls:'icon-users'" style="padding:5px;border:solid 1px #000000">  
   	 	
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
   					onclick="newUser('#usuarios-datagrid',$('#usuarios-datagrid-search').val())">Nuevo Usuario</a>
   				<a id="usuarios-editBtn" href="#" class="easyui-linkbutton" 
   					data-options="iconCls:'icon-edit'"
   					onclick="editUser('#usuarios-datagrid')">Editar Usuario</a>
   				<a id="usuarios-delBtn" href="#" class="easyui-linkbutton" 
   					data-options="iconCls:'icon-trash'"
   					onclick="deleteUser('#usuarios-datagrid')">Borrar Juez</a>
   				<input id="usuarios-datagrid-search" type="text" value="---- Buscar ----" class="search_textfield"	/>
   			</span>
   			<span style="float:right;padding:5px">
   				<a id="usuarios-keyBtn" href="#" class="easyui-linkbutton" 
   					data-options="iconCls:'icon-key'"
   					onclick="setPassword('#usuarios-datagrid')">Contrase&ntilde;a</a>
   				<a id="usuarios-reloadBtn" href="#" class="easyui-linkbutton"
   					data-options="iconCls:'icon-brush'"
   					onclick="
   			        	// clear selection and reload table
   			    		$('#usuarios-datagrid-search').val('---- Buscar ----');
   			            $('#usuarios-datagrid').datagrid('load',{ where: '' });"
   					>Limpiar</a>
   			</span>
		</div>
    	<?php require_once("dialogs/dlg_usuarios.inc")?>
   	</div>
   	<div title="Sesiones" data-options="iconCls:'icon-order'" style="padding:5px">
    	<?php require_once("dialogs/dlg_sesiones.inc")?>
   	</div>
   	<div title="Configuraci&oacute;n" data-options="iconCls:'icon-setup'" style="padding:5px">
    	<?php require_once("dialogs/dlg_configuracion.inc")?>
   	</div>
   	<div title="Utilidades" data-options="iconCls:'icon-tools'" style="padding:5px">
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
 	title: 'Gesti&oacute;n de datos de Usuarios',
 	// datos de la conexion ajax
 	url: '/agility/server/database/userFunctions.php?Operation=select',
 	loadMsg: 'Actualizando lista de usuarios ...',
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
     	{ field:'Gecos',	width:55, sortable:true,	title:'Informacion' },
     	{ field:'Phone',	width:20, 					title:'Tel&eacute;fono' },
     	{ field:'Email',	width:30, sortable:true,   	title:'E-mail' },
        { field:'Perms',	width:20,					title:'Categoria', formatter:formatPermissions }
    ]],
    // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
    rowStyler:myRowStyler,
 	// on double click fireup editor dialog
    onDblClickRow:function() { 
         editUser('#usuarios-datagrid');
    }
 });

// key handler
addKeyHandler('#usuarios-datagrid',newUser,editUser,deleteUser);
// tooltips
addTooltip($('#usuarios-newBtn').linkbutton(),"Añadir un nuevo usuario<br/> a la Base de Datos"); 
addTooltip($('#usuarios-editBtn').linkbutton(),"Modificar los datos del usuario seleccionado");
addTooltip($('#usuarios-delBtn').linkbutton(),"Eliminar el usuario seleccionado de la BBDD");
addTooltip($('#usuarios-reloadBtn').linkbutton(),"Borrar casilla de busqueda y actualizar tabla");
addTooltip($('#usuarios-keyBtn').linkbutton(),"Cambiar la contraseña del usuario");
addTooltip($('#usuarios-datagrid-search'),"Buscar usuarios que coincidan con el criterio de busqueda");

 </script>