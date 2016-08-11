<!-- 
dlg_entrenamientos.inc

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
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<!--  dialogo de orden de sesiones de entrenamiento e introduccion de datos de una competicion -->
<div id="entrenamientos-dialog" style="width:800px;height:450px;padding:5px">
	<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
	<table id="entrenamientos-datagrid" style="padding:5px"></table>
</div>

<!-- BARRA DE TAREAS DE ORDEN DE Entrenamientos -->
<div id="entrenamientos-toolbar" style="width:100%;display:inline-block">
   	<!-- estos elementos deben estar alineados a la inzquierda -->
	<span style="float:left;padding:5px">
	   	<a id="entrenamientos-newBtn" href="#" class="easyui-linkbutton"
	   		data-options="iconCls:'icon-notes'" onclick="newEntrenamiento('#entrenamientos-datagrid','')"><?php _e('Insert'); ?></a>
	   	<a id="entrenamientos-removeBtn" href="#" class="easyui-linkbutton"
	   		data-options="iconCls:'icon-remove'" onclick="deleteEntrenamiento('#entrenamientos-datagrid')"><?php _e('Delete'); ?></a>
	   	<a id="entrenamientos-resetBtn" href="#" class="easyui-linkbutton"
		   data-options="iconCls:'icon-new'" onclick="resetEntrenamientos('#entrenamientos-datagrid')"><?php _e('Reset'); ?></a>
   	</span>
   	<!-- estos elementos deben estar alineados a la derecha -->
   	<span style="float:right;padding:5px">
        <a id="entrenamientos-excelBtn" href="#" class="easyui-linkbutton"
            data-options="iconCls:'icon-db_restore'" onclick="importExportEntrenamientos()"><?php _e('Import/Export'); ?></a>
	   	<a id="entrenamientos-reloadBtn" href="#" class="easyui-linkbutton"
	   		data-options="iconCls:'icon-reload'" onclick="reloadEntrenamientos();"><?php _e('Update'); ?></a>
   		<a id="entrenamientos-printBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-print'" onclick="printEntrenamientos()"><?php _e('Print'); ?></a>
	</span>
</div>

<div id="entrenamientos_newEntrenamiento-dialog" style="width:475px;height:auto;padding:10px 20px">
	<form id="entrenamientos_newEntrenamiento-form">
        <!-- TODO: write. Or better: use in-line editing datagrid features -->
	</form>
</div>

<div id="entrenamientos_newEntrenamiento-buttons">
	   	<a id="entrenamientos-okBtn" href="#" class="easyui-linkbutton"
	   		data-options="iconCls:'icon-ok'" onclick="saveEntrenamientos('#entrenamientos-datagrid')" ><?php _e('Accept'); ?></a>
   		<a id="entrenamientos-cancelBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-cancel'" onclick="$('#entrenamientos_newEntrenamiento-dialog').dialog('close')" ><?php _e('Cancel'); ?></a>
</div>

<script type="text/javascript">
$('#entrenamientos_newEntrenamiento-dialog').dialog({
	iconCls:'icon-notes',
	title:'<?php _e('New activity'); ?>',
	closed:true,
	buttons:'#entrenamientos_newEntrenamiento-buttons',
	modal:true,
	onClose: function () {$('#entrenamientos-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).focus();} // re-enable keybindings
});

$('#entrenamientos-dialog').dialog({
	collapsible:false,
	minimizable:false,
	maximizable:false,
	resizable:true,
	iconCls:'icon-updown',
	closed:true,
	modal:true
});

$('#entrenamientos-Nombre').textbox({ required:true, validType:'length[1,255]' } );

$('#entrenamientos-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: '/agility/server/database/trainingFunctions.php',
    queryParams: {
        Operation: 'select',
        Prueba: workingData.prueba,
    },
    loadMsg: '<?php _e("Updating training session timetable for contest"); ?> ...',
    toolbar: '#entrenamientos-toolbar',
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    columns:[[
        { field:'ID',		hidden:true },
        { field:'Prueba',	hidden:true },
		{ field:'LogoClub', width:50, title:'',formatter:formatLogo},
		{ field:'Club',		hidden:true},
		{ field:'NombreClub',width:150, title: isInternational(workingData.federation)? "<?php _e('Country');?>":"<?php _e('Club');?>" },
		{ field:'Fecha', 	width:100, title:'<?php  _e("Date");?>',formatter:formatYMD },
		{ field:'Firma', 	width:100, title:'<?php  _e("Check-in");?>',formatter:formatHM },
		{ field:'Veterinario',	width:100, title:'<?php _e("Veterinary");?>',formatter:formatHM},
		{ field:'Entrada',	width:100, title:'<?php _e("Start");?>',formatter:formatHMS},
		{ field:'Salida',	width:100, title:'<?php _e("End");?>',formatter:formatHMS},
		{ field:'L',	width:75, title:'<?php _e("Ring");?> 1'},
		{ field:'M',	width:75, title:'<?php _e("Ring");?> 2'},
		{ field:'S',	width:75, title:'<?php _e("Ring");?> 3'},
		{ field:'T',	width:75, title:'<?php _e("Ring");?> 4'},
		{ field:'Observaciones',	width:150, title:'<?php _e("Comments");?> '},
    ]],
    rowStyler:myRowStyler,
    onDblClickRow: function() { editEntrenamiento('#entrenamientos-datagrid'); },
    onLoadSuccess: function() { // get focus on datagrid (to bind keystrokes) and enable drag and drop
    	$(this).datagrid('enableDnd');
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
        if (howManyGrades(workingData.federation)<4) $(this).datagrid('hideColumn','T');
    },
    onDragEnter: function(dst,src) { return true; }, // default is not restriction
    onDrop: function(dst,src,updown) {
        dragAndDropEntrenamientos(src.ID,dst.ID,(updown==='top')?0:1);
    }
});

// key handler
addKeyHandler('#entrenamientos-datagrid','#entrenamientos-dialog',newEntrenamiento,editEntrenamiento,deleteEntrenamiento);

// botones
addTooltip($('#entrenamientos-newBtn').linkbutton(),'<?php _e("Insert a new training session entry"); ?>'+"<br/>"+"before selected one");
addTooltip($('#entrenamientos-removeBtn').linkbutton(),'<?php _e("Remove selected training session from timetable"); ?>');
addTooltip($('#entrenamientos-resetBtn').linkbutton(),'<?php _e("Re-create training table from inscription data"); ?>');
addTooltip($('#entrenamientos-reloadBtn').linkbutton(),'<?php _e("Refresh training session timetable from database"); ?>');
addTooltip($('#entrenamientos-excelBtn').linkbutton(),'<?php _e("Import/export session from/to Excel file"); ?>');
addTooltip($('#entrenamientos-printBtn').linkbutton(),'<?php _e("Print training session timetable"); ?>');
addTooltip($('#entrenamientos-okBtn').linkbutton(),'<?php _e("Store entry into database"); ?>');
addTooltip($('#entrenamientos-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');

</script>