<!-- 
frm_perros.php

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
   			onclick="newDog('#perros-datagrid',$('#perros-datagrid-search').val())">Nuevo Perro</a>
   		<a id="perros-editBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-edit'"
   			onclick="editDog('#perros-datagrid')">Editar Perro</a>
   		<a id="perros-delBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-trash'"
   			onclick="deleteDog('#perros-datagrid')">Borrar Perro</a>
   		<input id="perros-datagrid-search" type="text" value="---- Buscar ----" class="search_textfield"	/>
   	</span>
   	<span style="float:right;padding:5px">
   		<a id="perros-reloadBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-brush'"
   			onclick="
   	        	// clear selection and reload table
   	    		$('#perros-datagrid-search').val('---- Buscar ----');
				reloadWithSearch('#perros-datagrid','select');
   	            "
   		>Limpiar</a>
   	</span>
</div>
    
<?php require_once("dialogs/dlg_perros.inc"); ?>
<?php require_once("dialogs/dlg_guias.inc");?>
<?php require_once("dialogs/dlg_clubes.inc");?>
    
<script type="text/javascript">
        
        // tell jquery to convert declared elements to jquery easyui Objects
        
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
        	title: 'Gesti&oacute;n de datos de Perros'+' - '+fedName(workingData.federation),
        	url: '/agility/server/database/dogFunctions.php',
        	queryParams: { Operation: 'select', Federation: workingData.federation },
        	loadMsg: 'Actualizando lista de perros ...',
        	method: 'get',
            toolbar: '#perros-toolbar',
            pagination: false,
            rownumbers: true,
            fitColumns: true,
            singleSelect: true,
            view: scrollview,
            pageSize: 50,
            multiSort: true,
            remoteSort: true,
            columns: [[
                { field:'ID',   hidden:true },
                { field:'Federation', hidden:true },
            	{ field:'Nombre',   width:30, sortable:true,  align: 'right', title: 'Nombre' },
            	{ field:'Raza',     width:25,                align: 'right', title: 'Raza' },
            	{ field:'LOE_RRC',  width:20, sortable:true, align: 'right', title: 'LOE / RRC' },
            	{ field:'Licencia', width:15, sortable:true, align: 'right', title: 'Lic.' },
            	{ field:'Categoria',width:10,                 align:'center', title: 'Cat.' },
            	{ field:'Grado',    width:10,                 align:'center', title: 'Grado' },
            	{ field:'Guia',   hidden:true },
                { field:'NombreGuia',     width:50, sortable:true, title: 'Nombre del Gu&iacute;a'},
            	{ field:'Club',   hidden:true },
                { field:'NombreClub',     width:35, sortable:true, title: 'Nombre del Club'}
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler,
        	// on double click fireup editor dialog
            onDblClickRow:function() { 
                editDog('#perros-datagrid');
            }
        });
		
		// key handler
       	addKeyHandler('#perros-datagrid',newDog,editDog,deleteDog);
		// tooltips
		addTooltip($('#perros-newBtn').linkbutton(),"Registrar un nuevo perro <br/>en la Base de Datos"); 
		addTooltip($('#perros-editBtn').linkbutton(),"Modificar los datos del perro seleccionado");
		addTooltip($('#perros-delBtn').linkbutton(),"Eliminar el perro seleccionado de la BBDD");
		addTooltip($('#perros-reloadBtn').linkbutton(),"Borrar casilla de busqueda y actualizar tabla");
		addTooltip($('#perros-datagrid-search'),"Buscar perros que cumplan con el criterio de busqueda");
</script>