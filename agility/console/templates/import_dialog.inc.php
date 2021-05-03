<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<div id="importdialog" class="easyui-dialog" style="width:640px;height:auto;padding:5px;">
    <form name="excel-importOpts">
        <p>
            <?php _e("Current federation to import data into");?>:
            <span style="font-style:italic;" id="import-excelFederation"></span><br/><br/>
            <?php _e("Database backup is recommended before import");?>&nbsp;&nbsp;
            <input type="button" class="icon_button icon-db_backup" name="<?php _e('Backup');?>" value="<?php _e('Backup');?>" onClick="backupDatabase();"/>
        </p>
        <hr />
        <p>
            <?php _e("Select Excel file to retrieve Dog data from");?><br />
            <?php _e("Press import to start, or cancel to abort import"); ?>
            <br />&nbsp;<br />
            <input type="file" name="import-excel-fileSelect" value="" id="import-excel-fileSelect"
                   class="icon_button icon-search"
                   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" onchange="read_excelFile(this,'')">
            <br />
            <input id="import-excelData" type="hidden" name="excelData" value="">
            <!-- modo blind (no interactivo -->
            <span id="import_excelBlindCheck">
                <br />
                <label for="import-excelBlindMode"><?php _e("Blind (non-interactive) mode");?></label>
                <input id="import-excelBlindMode" type="checkbox" name="excelBlindMode" value="1">
                <br />
            </span>
            <!-- opciones para el modo blind -->
            <span id="import-excelBlindOptions">
				<br/><strong><?php _e("Excel import options");?>:</strong> <br/>
				<span style="display:inline-block;width:275px"><?php _e("Precedence on DB/Excel entry match");?>:</span>
				<input id="import-excelPrefDB"   type="radio" name="excelPreference" value="1"/>
				<label for="import-excelPrefDB"><?php _e('Database')?></label>
				<input id="import-excelPrefFile" type="radio" name="excelPreference" value="0" checked="checked"/>
				<label for="import-excelPrefFile"><?php _e('Excel file')?></label>
                <br/>
				<span style="display:inline-block;width:275px"><?php _e("Text Conversion");?>:</span>
				<input id="import-excelUpperCase" type="radio" name="excelUpperCase" value="1" checked="checked"/>
				<label for="import-excelUpperCase"><?php _e("Capitalize words");?></label>
				<input id="import-excelLeave" type="radio" name="excelUpperCase" value="0"/>
				<label for="import-excelLeave"><?php _e("Leave as is");?></label>
                <br/>
				<span style="display:inline-block;width:275px;"><?php _e('Action on empty fields');?>:</span>
				<input id="import-excelEmptyIgnore"   type="radio" name="excelEmpty" value="0" checked="checked"/>
				<label for="import-excelEmptyIgnore"><?php _e('Ignore')?></label>
				<input id="import-excelEmptyUse" type="radio" name="excelEmpty" value="1"/>
				<label for="import-excelEmptyUse"><?php _e('Overwrite')?></label>
                <br/>&nbsp;<br/>
			</span>
            <span style="display:none">
                <label for="import-excelParseCourseData"><?php _e("Also read (if available) course data");?></label>
                <input id="import-excelParseCourseData"  type=checkbox name="excelParseCourseData" value="1" checked="checked"/>
                <label for="import-excelIgnoreNotPresent"><?php _e("Try to deal entry when no license provided");?></label>
                <input id="import-excelIgnoreNotPresent" type="checkbox" name="excelIgnoreNotPresent" value="1" checked="checked"/>
                <br />
            </span>
        </p>
        <span style="width:100px;"><?php _e('Import status'); ?>:	</span>
        <div id="import-excel-progressbar" style="width:350px;"></div>
    </form>
</div>

<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO DE IMPORTACION -->
<div id="import-excel-buttons">
    <a id="import-excel-okBtn" href="#" class="easyui-linkbutton"
       data-options="iconCls: 'icon-ok'" onclick="do_excelImport()"><?php _e('Import'); ?></a>
    <a id="import-excel-cancelBtn" href="#" class="easyui-linkbutton"
       data-options="iconCls: 'icon-cancel'" onclick="$('#importdialog').dialog('close')"><?php _e('Cancel'); ?></a>
</div>

<script type="text/javascript">

    $('#import-excel-progressbar').progressbar({
        value: 0,
        text: '{value}'
    });
    $('#import-excelFederation').html(""); // to be filled later

    // tell jquery to convert declared elements to jquery easyui Objects
    $('#importdialog').dialog( {
        title:' <?php _e('Import data from Excel file'); ?>',
        closed:true,
        modal:true,
        buttons:'#import-excel-buttons',
        iconCls:'icon-table',
        onOpen: function() {
            $('#import-excel-progressbar').progressbar('setValue',"");
        },
        onClose: function() {
            ac_import.progress_status='paused';
            autoBackupDatabase(1,"");
        }
    } );

    addTooltip($('#import-excel-okBtn').linkbutton(),'<?php _e("Import data from selected Excel file"); ?>');
    addTooltip($('#import-excel-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
    addTooltip($('#import-excelBlindMode'),'<?php _e("Assume no coherency errors with current database data"); ?>');
</script>