<!-- 
frm_jueces.php

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
 
<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE JUECES -->
<div  style="width:975px;height:550px">   
    <!-- DECLARACION DE LA TABLA DE JUECES -->
    <table id="jueces-datagrid">  </table>
</div> 

<!-- BARRA DE TAREAS DE LA TABLA DE JUECES -->
<div id="jueces-toolbar" style="width:100%;display:inline-block">
 	<span style="float:left;padding:5px">
   		<a id="jueces-newBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-whistle'"
   			onclick="newJuez('#jueces-datagrid',$('#jueces-datagrid-search').val())">Nuevo Juez</a>
   		<a id="jueces-editBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-edit'"
   			onclick="editJuez('#jueces-datagrid')">Editar Juez</a>
   		<a id="jueces-delBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-trash'"
   			onclick="deleteJuez('#jueces-datagrid')">Borrar Juez</a>
   		<input id="jueces-datagrid-search" type="text" value="---- Buscar ----" class="search_textfield"	/>
   	</span>
   	<span style="float:right;padding:5px">
   		<a id="jueces-reloadBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-brush'"
   			onclick="
   	        	// clear selection and reload table
   	    		$('#jueces-datagrid-search').val('---- Buscar ----');
   	            $('#jueces-datagrid').datagrid('load',{ where: '' });"
   			>Limpiar</a>
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
        	title: 'Gesti&oacute;n de datos de Jueces',
        	// datos de la conexion ajax
        	url: '/agility/server/database/juezFunctions.php?Operation=select',
        	loadMsg: 'Actualizando lista de jueces ...',
        	method: 'get',
            toolbar: '#jueces-toolbar',
            pagination: false,
            rownumbers: true,
            fitColumns: true,
            singleSelect: true,
            view: scrollview,
            pageSize: 50,
            multiSort: true,
            remoteSort: true,
            columns: [[
                { field:'ID',			hidden:true },
                { field:'Nombre',		width:40, sortable:true,	title: 'Nombre:' },
            	{ field:'Direccion1',	width:35,					title: 'Direcci&oacute;n 1:' },
            	{ field:'Direccion2',	width:35,                   title: 'Direcci&oacute;n 2' },
            	{ field:'Telefono',		width:20, sortable:true,	title: 'Tel&eacute;fono' },
            	{ field:'Internacional',width:10, align:'center',	title: 'Int.' 	},
            	{ field:'Practicas',	width:10, align:'center',	title: 'Pract.' },
            	{ field:'Email',		width:35, sortable:true,   align:'right', title: 'Correo Electr&oacute;nico' },
                { field:'Observaciones',width:30,					title: 'Observaciones'}
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler,
        	// on double click fireup editor dialog
            onDblClickRow:function() { 
                editJuez('#jueces-datagrid');
            }
        });

		// key handler
       	addKeyHandler('#jueces-datagrid',newJuez,editJuez,deleteJuez);
		// tooltips
		addTooltip($('#jueces-newBtn').linkbutton(),"Añadir un nuevo juez<br/> a la Base de Datos"); 
		addTooltip($('#jueces-editBtn').linkbutton(),"Modificar los datos del juez seleccionado");
		addTooltip($('#jueces-delBtn').linkbutton(),"Eliminar el juez seleccionado de la BBDD");
		addTooltip($('#jueces-reloadBtn').linkbutton(),"Borrar casilla de busqueda y actualizar tabla");
		addTooltip($('#jueces-datagrid-search'),"Buscar jueces que coincidan con el criterio de busqueda");

</script>