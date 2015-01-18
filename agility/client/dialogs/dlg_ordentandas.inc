<!-- 
dlg_ordentandas.inc

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

<!--  dialogo de orden de tandas e introduccion de datos de una competicion -->
<div id="ordentandas-dialog" style="width:700px;height:450px;padding:5px">
	<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
	<table id="ordentandas-datagrid" style="padding:5px"></table>
</div>

<!-- BARRA DE TAREAS DE ORDEN DE tandas -->
<div id="ordentandas-toolbar" style="width:100%;display:inline-block">
   	<!-- estos elementos deben estar alineados a la inzquierda -->
	<span style="float:left;padding:5px">
	   	<a id="ordentandas-newdBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-add'" onclick="newTanda('#ordentandas-datagrid')">Insertar</a>
	   	<a id="ordentandas-editBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-edit'" onclick="editTanda('#ordentandas-datagrid')">Editar</a>
	   	<a id="ordentandas-removeBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-remove'" onclick="deleteTanda('#ordentandas-datagrid')">Borrar</a>
   	</span>
   	<!-- estos elementos deben estar alineados a la derecha -->
   	<span style="float:right;padding:5px">
	   	<a id="ordentandas-reloadBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-reload'" onclick="$('#ordentandas-datagrid').datagrid('reload');">Actualizar</a>
   		<a id="ordentandas-printBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-print'" onclick="printTandas() >Imprimir</a>
	</span>
</div>

<div id="ordentandas_newtanda-dialog" class="easyui-dialog" style="width:550px;height:400px;padding:10px 20px"
		data-options="title:'Nueva actividad',closed:true,footer:'ordentandas_newtanda-footer'">
	<form id="ordentandas_newtanda-form">
	            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input id="ordentandas_nt-Nombre" 
                	name="Nombre" 
                	type="text" 
                	class="easyui-validatebox" 
                	style="width:250px" />
                <input id="ordentandas_nt-ID" name="ID" type="hidden" /> 
                <input id="ordentandas_nt-Tipo" name="Tipo" type="hidden" /> 
                <input id="ordentandas_nt-Operation" name="Operation" type="hidden" /> <!-- to store insert/update/delete operation -->
            </div>
            <div class="fitem">
                <label for="Sesion">Sesi&oacute;n/Ring</label>
                <select id="ordentandas_nt-Session" 
                		name="Session" 
                		class="easyui-combobox" 
                		style="width:155px"></select>
            </div>
            <div class="fitem">
                <label for="Horario">Horario</label>
                <input id="ordentandas_nt-Horario" name="Horario" type="text" style="width:250px" />
            </div>
            <div class="fitem">
                <label for="Comentarios">Comentarios</label>
                <input id="ordentandas_nt-Comentarios" name="Comentarios" type="text" />
            </div>
	</form>
</div>

<div id="ordentandas_newtanda-footer">
   	<span style="float:right;padding:5px">
	   	<a id="ordentandas-okBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-accept'" onclick="saveTanda()" >Aceptar</a>
   		<a id="ordentandas-cancelBtn" href="#" 
   			data-options="iconCls:'icon-cancel'" class="easyui-linkbutton" onclick="$('#ordentanda_newtanda-dialog').dialog('close')" >Cancelar</a>
	</span>
</div>

<script type="text/javascript">

$('#ordentandas-dialog').dialog({
	collapsible:false,
	minimizable:false,
	maximizable:false,
	resizable:true,
	iconCls:'icon-updown',
	closed:true,
	modal:true
});

$('#ordentandas-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: '/agility/server/database/tandasFunctions.php',
    queryParams: {
        Operation: 'getTandas',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada
    },
    loadMsg: "Actualizando programa de la Jornada .....",
    toolbar: '#ordentandas-toolbar',
    pagination: false,
    rownumbers: true,
    fitColumns: true,
    singleSelect: true,
    columns:[[
          	{ field:'ID',		hidden:true },
        	{ field:'Prueba',	hidden:true },
          	{ field:'Jornada',	hidden:true },
          	{ field:'Manga',	hidden:true },
      		{ field:'From',		hidden:true },
      		{ field:'To',		hidden:true },
      		{ field:'Nombre',	width:200, sortable:false, align:'left',title:'Actividad'},
      		{ field:'Sesion',	hidden:true },
      		{ field:'NombreSesion',width:120, sortable:false, align:'left',title:'Ring'},
      		{ field:'Horario',	width:70, sortable:false, align:'right',title:'Horario'},
      		{ field:'Observaciones',width:110, sortable:false, align:'right',title:'Observaciones'},
      		{ field:'Categoria',hidden:true },
      		{ field:'Grado',	hidden:true }
    ]],
    rowStyler:myRowStyler,
    onLoadSuccess: function() { // get focus on datagrid (to bind keystrokes) and enable drag and drop
    	$(this).datagrid('enableDnd');
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
    },
    onDragEnter: function(dst,src) { return true; }, // default is not restriction
    onDrop: function(dst,src,updown) {
        dragAndDropOrdenTandas(src.ID,dst.ID,(updown==='top')?0:1);
    }
});

// Activa tecla ESC (escape) para cerrar ventana
$('#ordentandas-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus().bind('keydown',function(e){
	if (e.keyCode==27) $('#ordentandas-dialog').dialog('close'); 
	return false;
});

// botones
addTooltip($('#ordentandas-reloadBtn').linkbutton(),"Actualizar el programa de la jornada desde base de datos");
addTooltip($('#ordentandas-printBtn').linkbutton(),"Imprimir el programa de la jornada");

$('#ordentandas-printBtn').on("click", function () {

});
</script>