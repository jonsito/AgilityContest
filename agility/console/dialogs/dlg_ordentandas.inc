<!-- 
dlg_ordentandas.inc

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
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<!--  dialogo de orden de tandas e introduccion de datos de una competicion -->
<div id="ordentandas-dialog" style="width:800px;height:500px;padding:5px">
	<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
	<table id="ordentandas-datagrid" style="padding:5px"></table>
</div>

<!-- BARRA DE TAREAS DE ORDEN DE tandas -->
<div id="ordentandas-toolbar" style="width:100%;display:inline-block">
   	<!-- estos elementos deben estar alineados a la inzquierda -->
	<span style="float:left;padding:5px">
	   	<a id="ordentandas-newBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-notes'" onclick="newTanda('#ordentandas-datagrid','')"><?php _e('Insert'); ?></a>
	   	<a id="ordentandas-editBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-edit'" onclick="editTanda('#ordentandas-datagrid')"><?php _e('Edit'); ?></a>
	   	<a id="ordentandas-removeBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-remove'" onclick="deleteTanda('#ordentandas-datagrid')"><?php _e('Delete'); ?></a>
   	</span>
   	<!-- estos elementos deben estar alineados a la derecha -->
   	<span style="float:right;padding:5px">
	   	<a id="ordentandas-reloadBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-reload'" onclick="reloadOrdenTandas();"><?php _e('Update'); ?></a>
   		<a id="ordentandas-printBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-print'" onclick="print_commonDesarrollo(0)"><?php _e('Print'); ?></a>
	</span>
</div>

<div id="ordentandas_newtanda-dialog" style="width:500px;height:auto;padding:10px 20px">
	<form id="ordentandas_newtanda-form">
	            <div class="fitem">
                <label for="ordentandas_nt-Nombre"><?php _e('Name'); ?>:</label>
                <input id="ordentandas_nt-Nombre" name="Nombre"	type="text"	style="width:250px" />
                <input id="ordentandas_nt-ID" name="ID" type="hidden" /> 
                <input id="ordentandas_nt-Tipo" name="Tipo" type="hidden" /> 
                <input id="ordentandas_nt-Prueba" name="Prueba" type="hidden" /> 
                <input id="ordentandas_nt-Jornada" name="Jornada" type="hidden" /> 
                <input id="ordentandas_nt-InsertID" name="InsertID" type="hidden" /> 
                <input id="ordentandas_nt-Operation" name="Operation" type="hidden" /> <!-- to store insert/update/delete operation -->
            </div>
            <div class="fitem">
                <label for="ordentandas_nt-Sesion"><?php _e('Session - Ring'); ?></label>
                <select id="ordentandas_nt-Sesion" name="Sesion" style="width:150px"></select>
            </div>
            <div class="fitem">
                <label for="ordentandas_nt-Horario"><?php _e('Timetable'); ?></label>
                <input id="ordentandas_nt-Horario" name="Horario" type="text" style="width:150px" />
            </div>
            <div class="fitem">
                <label for="ordentandas_nt-Comentario"><?php _e('Comments'); ?></label>
                <input id="ordentandas_nt-Comentario" name="Comentario" type="text" style="width:275px"/>
            </div>
	</form>
</div>

<div id="ordentandas_newtanda-buttons">
	   	<a id="ordentandas-okBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-ok'" onclick="saveTanda('#ordentandas-datagrid')" ><?php _e('Accept'); ?></a>
   		<a id="ordentandas-cancelBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-cancel'" onclick="$('#ordentandas_newtanda-dialog').dialog('close')" ><?php _e('Cancel'); ?></a>
</div>

<script type="text/javascript">
$('#ordentandas_newtanda-dialog').dialog({
	iconCls:'icon-notes',
	title:'<?php _e('New activity'); ?>',
	closed:true,
	buttons:'#ordentandas_newtanda-buttons',
	modal:true,
	onClose: function () {$('#ordentandas-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus();} // re-enable keybindings
});

$('#ordentandas-dialog').dialog({
	collapsible:false,
	minimizable:false,
	maximizable:false,
	resizable:true,
	iconCls:'icon-updown',
	closed:true,
	modal:true,
    onBeforeOpen: function () {
        prepareCompetitionDialogs("ordentandas");
        return true;
    }
});

$('#ordentandas_nt-Nombre').textbox({ required:true, validType:'length[1,255]' } );
$('#ordentandas_nt-Horario').textbox();
$('#ordentandas_nt-Comentario').textbox();

$('#ordentandas-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: '../ajax/database/tandasFunctions.php',
    queryParams: {
        Operation: 'getTandas',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Sesion: 0 // 0: Any session
    },
    loadMsg: '<?php _e("Updating activities and timetable on journey"); ?> ...',
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
      		{ field:'Nombre',	width:200, sortable:false, align:'left',title:'<?php _e('Activity'); ?>',formatter:formatBold},
      		{ field:'Sesion',	hidden:true },
      		{ field:'NombreSesion',width:100, sortable:false, align:'left',title:'<?php _e('Session - Ring'); ?>'},
      		{ field:'Horario',	width:90, sortable:false, align:'center',title:'<?php _e('Timetable'); ?>'},
      		{ field:'Comentario',width:120, sortable:false, align:'left',title:'<?php _e('Comments'); ?>'},
      		{ field:'Categoria',hidden:true },
      		{ field:'Grado',	hidden:true }
    ]],
    rowStyler:myRowStyler,
    onDblClickRow: function() { editTanda('#ordentandas-datagrid'); },
    onLoadSuccess: function() { // get focus on datagrid (to bind keystrokes) and enable drag and drop
    	$(this).datagrid('enableDnd');
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
    },
    onDragEnter: function(dst,src) { return true; }, // default is not restriction
    onDrop: function(dst,src,updown) {
        dragAndDropOrdenTandas(src.ID,dst.ID,(updown==='top')?0:1);
    }
});

$('#ordentandas_nt-Sesion').combogrid({
	panelWidth: 500,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '../ajax/database/sessionFunctions.php',
	method: 'get',
	mode: 'remote',
	required: true,
	rownumber: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	editable: false,  // to disable tablet keyboard popup
	columns: [[
	   	    { field:'ID',			width:'5%', sortable:false, align:'center', title:'ID' }, // Session ID
			{ field:'Nombre',		width:'25%', sortable:false,   align:'center',  title: '<?php _e('Name'); ?>' },
			{ field:'Comentario',	width:'60%', sortable:false,   align:'left',  title: '<?php _e('Comments'); ?>' }
	]],
	onBeforeLoad: function(param) { 
		param.Operation='select';
		param.Hidden=0;
		return true;
	}
});

// key handler
addKeyHandler('#ordentandas-datagrid','#ordentandas-dialog',newTanda,editTanda,deleteTanda);

// botones
addTooltip($('#ordentandas-newBtn').linkbutton(),'<?php _e("Add a new activity into journey timetable"); ?>'+"<br/>"+"before selected activity");
addTooltip($('#ordentandas-editBtn').linkbutton(),'<?php _e("Edit selected activity"); ?>');
addTooltip($('#ordentandas-removeBtn').linkbutton(),'<?php _e("Remove activity from journey timetable"); ?>');
addTooltip($('#ordentandas-reloadBtn').linkbutton(),'<?php _e("Refresh journey timetable from database"); ?>');
addTooltip($('#ordentandas-printBtn').linkbutton(),'<?php _e("Print journey activities timetable"); ?>');
addTooltip($('#ordentandas-okBtn').linkbutton(),'<?php _e("Store activity information into database"); ?>');
addTooltip($('#ordentandas-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');

</script>