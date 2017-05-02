<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<div style="width=100%">
    <form name="excel-importOpts">
        <p>
            <?php _e("Current federation to import data into");?>:
            <span style="font-style:italic;" id="import-excelFederation"></span><br/><br/>
            <?php _e("Database backup is recommended before import");?> &nbsp; &nbsp;
            <input type="button" class="icon_button icon-db_backup" name="<?php _e('Backup');?>" value="<?php _e('Backup');?>" onClick="backupDatabase();"/>
        </p>
        <hr />
        <p>
            <?php _e("Select Excel file to retrieve Dog data from");?><br />
            <?php _e("Press import to start, or cancel to abort import"); ?>
            <br />&nbsp;<br />
            <input type="file" name="import-excel" value="" id="import-excel-fileSelect"
                   class="icon_button icon-search"
                   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" onchange="read_excelFile(this)">
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
				<br/> <?php _e("Options for non-interactive import");?>:<br/>
				<span style="display:inline-block;width:275px"><?php _e("Precedence on DB/Excel entry match");?>:</span>
				<input id="import-excelPrefDB"   type="radio" name="excelPreference" value="1">
				<label for="import-excelPrefDB"><?php _e('Database')?></label>
				<input id="import-excelPrefFile" type="radio" name="excelPreference" value="0" checked="checked">
				<label for="import-excelPrefFile"><?php _e('Excel file')?></label><br/>

				<span style="display:inline-block;width:275px"><?php _e("Text Conversion");?>:</span>
				<input id="import-excelUpperCase" type="radio" name="excelUpperCase" value="1" checked="checked">
				<label for="import-excelUpperCase"><?php _e("Capitalize words");?></label>
				<input id="import-excelLeave" type="radio" name="excelUpperCase" value="0">
				<label for="import-excelLeave"><?php _e("Leave as is");?></label><br/>

				<span style="display:inline-block;width:275px;"><?php _e('Action on empty fields');?>:</span>
				<input id="import-excelEmptyIgnore"   type="radio" name="excelEmpty" value="0" checked="checked">
				<label for="import-excelEmptyIgnore"><?php _e('Ignore')?></label>
				<input id="import-excelEmptyUse" type="radio" name="excelEmpty" value="1">
				<label for="import-excelEmptyUse"><?php _e('Overwrite')?></label><br/>
			</span>
            <br />
        </p>
    </form>
    <p>
        <span style="float:left"><?php _e('Import status'); ?>:	</span>
        <span id="import-excel-progressbar" style="float:right;text-align:center;"></span>
    </p>
</div>


<script type="text/javascript">

    $('#import-excel-progressbar').progressbar({
        width: '70%',
        value: 0,
        text: '{value}'
    });

    $('#import-excelFederation').html(workingData.datosFederation.LongName);

    addTooltip($('#import-excelBlindMode'),'<?php _e("Assume no coherency errors with current database data"); ?>');
</script>