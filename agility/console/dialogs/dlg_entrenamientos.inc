<!-- 
dlg_entrenamientos.inc

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

<!-- Ventana de seleccion de fichero para importacion de datos excel -->
<div id="entrenamientos-excel-dialog" style="width:640px;height:auto;padding:10px; display=none;">
	<form id="entrenamientos-import-importOpts" name="entrenamientos-importOpts">
		<p>
			<?php _e("Import training session timetable from Excel file");?><br/>
			<?php _e("Database backup is recommended before import");?> &nbsp; &nbsp;
			<input type="button" class="icon_button icon-db_backup" name="<?php _e('Backup');?>" value="<?php _e('Backup');?>" onClick="backupDatabase();"/>
		</p>
		<hr />
		<p>
			<?php _e("Select Excel file to retrieve Dog data from");?><br />
			<?php _e("Press import to start, or cancel to abort import"); ?>
			<br />&nbsp;<br />
			<input type="file" id="entrenamientos-import-excelFileSelect" name="entrenamientos-excelFileSelect" value=""
				   class="icon_button icon-search"
				   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" onchange="read_excelFile(this,'entrenamientos-')">
			<br />
			<input id="entrenamientos-import-excelData" type="hidden" name="entrenamientos-excelData" value="">
			<!-- modo blind (no interactivo -->
			<span id="entrenamientos-import_excelBlindCheck" style="display:none">
               <br />
               <label for="entrenamientos-import-excelBlindMode"><?php _e("Blind (non-interactive) mode");?></label>
               <input id="entrenamientos-import-excelBlindMode" type="checkbox" name="entrenamientos-excelBlindMode" value="1" checked="checked">
                <br />
           </span>
			<!-- opciones para el modo blind -->
			<span id="entrenamientos-import-excelBlindOptions">
				<br/> <?php _e("Options for non-interactive import");?>:<br/>

                <span style="display:inline-block;width:275px"><?php _e("Precedence on DB/Excel entry match");?>:</span>
				<input id="entrenamientos-import-excelPrefDB"   type="radio" name="entrenamientos-excelPreference" value="1">
				<label for="entrenamientos-import-excelPrefDB"><?php _e('Database')?></label>
				<input id="entrenamientos-import-excelPrefFile" type="radio" name="entrenamientos-excelPreference" value="0" checked="checked">
				<label for="entrenamientos-import-excelPrefFile"><?php _e('Excel file')?></label><br/>

				<span style="display:inline-block;width:275px"><?php _e("Text Conversion");?>:</span>
				<input id="entrenamientos-import-excelUpperCase" type="radio" name="entrenamientos-excelUpperCase" value="1" checked="checked">
				<label for="entrenamientos-import-excelUpperCase"><?php _e("Capitalize words");?></label>
				<input id="entrenamientos-import-excelLeave" type="radio" name="entrenamientos-excelUpperCase" value="0">
				<label for="entrenamientos-import-excelLeave"><?php _e("Leave as is");?></label><br/>

                <span style="display:inline-block;width:275px;"><?php _e('Action on empty fields');?>:</span>
				<input id="entrenamientos-import-excelEmptyIgnore"   type="radio" name="entrenamientos-excelEmpty" value="0" checked="checked">
				<label for="entrenamientos-import-excelEmptyIgnore"><?php _e('Ignore')?></label>
				<input id="entrenamientos-import-excelEmptyUse" type="radio" name="entrenamientos-excelEmpty" value="1">
				<label for="entrenamientos-import-excelEmptyUse"><?php _e('Overwrite')?></label><br/>
			</span>
			<br />
		</p>
	</form>
	<p>
		<span style="float:left"><?php _e('Import status'); ?>:	</span>
		<span id="entrenamientos-import-excel-progressbar" style="float:right;text-align:center;"></span>
	</p>

</div>
<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO DE IMPORTACION -->
<div id="entrenamientos-import-excel-buttons">
    <a id="entrenamientos-import-excel-cancelBtn" href="#" class="easyui-linkbutton"
       data-options="iconCls: 'icon-cancel'" onclick="$('#entrenamientos-excel-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
	<a id="entrenamientos-import-excel-okBtn" href="#" class="easyui-linkbutton"
	   data-options="iconCls: 'icon-ok'" onclick="entrenamientos_excelImport()"><?php _e('Import'); ?></a>
</div>


<!--  dialogo de orden de sesiones de entrenamiento e introduccion de datos de una competicion -->
<div id="entrenamientos-dialog" style="width:850px;height:450px;padding:5px">
	<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
	<table id="entrenamientos-datagrid" style="padding:5px"></table>
</div>

<!-- BARRA DE TAREAS DE ORDEN DE Entrenamientos -->
<div id="entrenamientos-toolbar" style="width:100%;display:inline-block">
   	<!-- estos elementos deben estar alineados a la inzquierda -->
	<span style="float:left;padding:5px">
	   	<a id="entrenamientos-saveBtn" href="#" class="easyui-linkbutton"
	   		data-options="iconCls:'icon-save',disabled:true" onclick="saveEditEntrenamiento('#entrenamientos-datagrid','')"><?php _e('Save'); ?></a>
	   	<a id="entrenamientos-undoBtn" href="#" class="easyui-linkbutton"
	   		data-options="iconCls:'icon-undo',disabled:true" onclick="cancelEditEntrenamiento('#entrenamientos-datagrid')"><?php _e('Cancel'); ?></a>
	   	<a id="entrenamientos-resetBtn" href="#" class="easyui-linkbutton"
		   data-options="iconCls:'icon-new'" onclick="resetEntrenamientos('#entrenamientos-datagrid')"><?php _e('Reset'); ?></a>
	   	<a id="entrenamientos-evalBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-table'" onclick="evalEntrenamientos('#entrenamientos-datagrid')" ><?php _e('Eval'); ?></a>
   	</span>
   	<!-- estos elementos deben estar alineados a la derecha -->
   	<span style="float:right;padding:5px">
        <a id="entrenamientos-excelBtn" href="#" class="easyui-linkbutton"
            data-options="iconCls:'icon-db_restore'" onclick="importExportEntrenamientos()"><?php _e('Import/Export'); ?></a>
	   	<a id="entrenamientos-reloadBtn" href="#" class="easyui-linkbutton"
	   		data-options="iconCls:'icon-reload'" onclick="reloadEntrenamientos();"><?php _e('Update'); ?></a>
   		<a id="entrenamientos-printBtn" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-print'" onclick="print_entrenamientos('pdf')"><?php _e('Print'); ?></a>
	</span>
</div>

<script type="text/javascript">

$('#entrenamientos-import-excel-progressbar').progressbar({
	width: '70%',
	value: 0,
	text: '{value}'
});

$('#entrenamientos-excel-dialog').dialog( {
	title:' <?php _e('Import training session timetable from Excel file'); ?>',
	closed:true,
	modal:true,
	buttons:'#entrenamientos-import-excel-buttons',
	iconCls:'icon-table',
	onOpen: function() {
		ac_import.type='entrenamientos';
		$('#entrenamientos-import-excel-progressbar').progressbar('setValue',"");
	},
	onClose: function() { ac_import.progress_status='paused'; }
} );

$('#entrenamientos-dialog').dialog({
	collapsible:false,
	minimizable:false,
	maximizable:false,
	resizable:true,
	iconCls:'icon-updown',
	closed:true,
	modal:true,
    onBeforeOpen: function () {
	    prepareCompetitionDialogs("entrenamientos");
        return true;
    }
});

$('#entrenamientos-Nombre').textbox({ required:true, validType:'length[1,255]' } );

$('#entrenamientos-datagrid').datagrid({
	// added by me to handle editing
    editRow: {},
	editIndex: -1,
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: '../ajax/database/trainingFunctions.php',
    queryParams: {
        Operation: 'select',
        Prueba: workingData.prueba
    },
    loadMsg: '<?php _e("Updating training session timetable for contest"); ?> ...',
    toolbar: '#entrenamientos-toolbar',
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
	idField: 'ID',
    columns:[[
        { field:'ID',		hidden:true },
        { field:'Prueba',	hidden:true },
		{ field:'Orden',    width:20, title:'',formatter:formatTrainingState},
		{ field:'LogoClub', width:30, title:'',formatter:formatLogo},
		{ field:'Club',		hidden:true},
		{ field:'NombreClub',width:80, title: isInternational(workingData.federation)? "<?php _e('Country');?>":"<?php _e('Club');?>" },
		{ field:'Fecha', 	width:60, title:'<?php  _e("Date");?>',formatter:formatYMD,
			editor:{ type:'datebox', options: { required:true,formatter: dateToMysql } }
		},
		{ field:'Firma', 	align:'center', width:50, title:'<?php  _e("Check-in");?>',formatter:formatHM,
            editor:{ type:'timespinner', options: { required:true,showSeconds:false } }
        },
		{ field:'Veterinario',align:'center',	width:50, title:'<?php _e("Veterinary");?>',formatter:formatHM,
            editor:{ type:'timespinner', options: { required:true,showSeconds:false } }
        },
		{ field:'Comienzo', align:'center',	width:50, title:'<?php _e("Start");?>',formatter:formatHM,
            editor:{ type:'timespinner', options: { required:true,showSeconds:true} }
        },
		{ field:'Duracion',  align:'center',	width:45, title:'<?php _e("Duration");?>',formatter:formatMinSecs,
			editor:{ type: 'numberspinner',options: {required:true,min:0,precision:0,increment:15,formatter:formatMinSecs} }
		},
		{ field:'Key1',	width:40, title:'<?php _e("Ring");?> 1', formatter: formatCategoria,
			editor:{ type:'combobox', options:{ required:true,valueField:'Key',textField:'Value',data:toKeyValue(ac_fedInfo[workingData.federation].ListaCategorias)} }
		},
		{ field:'Value1',width:30, title:'<?php _e("Dogs");?> 1', align:'center',
			editor:{ type: 'numberspinner',options: {required:true,min:0,precision:0} }
		},
		{ field:'Key2',	width:40, title:'<?php _e("Ring");?> 2', formatter: formatCategoria,
			editor:{ type:'combobox', options:{ required:true,valueField:'Key',textField:'Value',data:toKeyValue(ac_fedInfo[workingData.federation].ListaCategorias)} }
		},
		{ field:'Value2',width:30, title:'<?php _e("Dogs");?> 2', align:'center',
			editor:{ type: 'numberspinner',options: {required:true,min:0,precision:0} }
		},
		{ field:'Key3',	width:40, title:'<?php _e("Ring");?> 3', formatter: formatCategoria,
			editor:{ type:'combobox', options:{ required:true,valueField:'Key',textField:'Value',data:toKeyValue(ac_fedInfo[workingData.federation].ListaCategorias)} }
		},
		{ field:'Value3',width:30, title:'<?php _e("Dogs");?> 3', align:'center',
			editor:{ type: 'numberspinner',options: {required:true,min:0,precision:0} }
		},
		{ field:'Key4',	width:40, title:'<?php _e("Ring");?> 4', formatter: formatCategoria,
			editor:{ type:'combobox', options:{ required:true,valueField:'Key',textField:'Value',data:toKeyValue(ac_fedInfo[workingData.federation].ListaCategorias)} }
		},
		{ field:'Value4',width:30, title:'<?php _e("Dogs");?> 4', align:'center',
			editor:{ type: 'numberspinner',options: {required:true,min:0,precision:0} }
		},
		{ field:'Estado',	hidden:true }
    ]],
    rowStyler:myRowStyler,
	onBeforeLoad: function(params) {
		// do not try to load if current license has no permissions
		// Notice: server side also checks capabilities. this is just to avoid javascript error
		// in populate datagrid when server rejects request cause of licensing
		return (parseInt(ac_regInfo.Options,2) & access_perms.ENABLE_TRAINING) !== 0;
	},
    onDblClickRow: function(index,row) {
            // ignore dblclick on edit active
		    if ($(this).datagrid('options').editIndex!=-1) return;
            // clone current row data to allow restore
            $(this).datagrid('options').editRow = JSON.parse(JSON.stringify(row));
			$(this).datagrid('beginEdit',index);
		},
    onLoadSuccess: function() { // get focus on datagrid (to bind keystrokes) and enable drag and drop
		var cats=howManyHeights();
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
		$(this).datagrid((cats==4)?'showColumn':'hideColumn','Key4');
		$(this).datagrid((cats==4)?'showColumn':'hideColumn','Value4');
		$(this).datagrid('enableDnd');
    },
    onDragEnter: function(dst,src) {  // do not allow drag and drop when edit active
    	return($(this).datagrid('options').editIndex==-1);
    },
    onDrop: function(dst,src,updown) {
        dragAndDropEntrenamientos(src.ID,dst.ID,(updown==='top')?0:1);
    },
	onBeforeSelect: function(index,row) {
        var edtidx=$(this).datagrid('options').editIndex;
        if (edtidx==-1) return true;
		return(edtidx==index);
	},
	onBeforeEdit: function(index,row) {
		// save row content, enable buttons, and mark index
        $('#entrenamientos-undoBtn').linkbutton('enable');
        $('#entrenamientos-saveBtn').linkbutton('enable');
		$(this).datagrid('options').editIndex = index;
        $(this).datagrid('disableDnd');
        return true;
	},
    onBeginEdit: function(index,row){
	    // set up initial values for editable cells
        var ed = $(this).datagrid('getEditor',{index:index,field:'Fecha'});
        $(ed.target).datebox('setValue', row.Fecha.split(' ')[0].replace("-","/"));
        ed = $(this).datagrid('getEditor',{index:index,field:'Firma'});
        $(ed.target).timespinner('setValue', row.Firma.split(' ')[1]);
        ed = $(this).datagrid('getEditor',{index:index,field:'Veterinario'});
        $(ed.target).timespinner('setValue', row.Veterinario.split(' ')[1]);
        ed = $(this).datagrid('getEditor',{index:index,field:'Entrada'});
        $(ed.target).timespinner('setValue', row.Comienzo.split(' ')[1]);
        ed = $(this).datagrid('getEditor',{index:index,field:'Observaciones'});
        $(ed.target).textbox('setValue', row.Observaciones);
    },
	onEndEdit: function(index,row,changes) {
        $('#entrenamientos-undoBtn').linkbutton('disable');
        $('#entrenamientos-saveBtn').linkbutton('disable');
		$(this).datagrid('options').editIndex = -1;
        $(this).datagrid('disableDnd');
	}
});

// botones
addTooltip($('#entrenamientos-saveBtn').linkbutton(),'<?php _e("Save changes to training session being edited"); ?>');
addTooltip($('#entrenamientos-undoBtn').linkbutton(),'<?php _e("Cancel changes. Restore previous values"); ?>');
addTooltip($('#entrenamientos-resetBtn').linkbutton(),'<?php _e("Re-create training table from inscription data"); ?>');
addTooltip($('#entrenamientos-reloadBtn').linkbutton(),'<?php _e("Refresh training session timetable from database"); ?>');
addTooltip($('#entrenamientos-excelBtn').linkbutton(),'<?php _e("Import/export session from/to Excel file"); ?>');
addTooltip($('#entrenamientos-printBtn').linkbutton(),'<?php _e("Print training session timetable"); ?>');
addTooltip($('#entrenamientos-evalBtn').linkbutton(),'<?php _e("Recalculate timetable values starting at selected row"); ?>');
addTooltip($('#entrenamientos-import-excel-okBtn').linkbutton(),'<?php _e("Import training session data from selected Excel file"); ?>');
addTooltip($('#entrenamientos-import-excel-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');

</script>