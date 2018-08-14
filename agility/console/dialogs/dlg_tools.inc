<h3><?php _e("Tools");?></h3>
<div id="tools-uploader"></div>

<div id="tools-dlg-admintab">
	<p> 
		<span style="display:inline-block;width:350px"><?php _e('Create database backup'); ?>: </span>
    	<a id="tools-backupBtn" href="#" class="easyui-linkbutton" style="width:85px"
	   		data-options="iconCls:'icon-db_backup'" onclick="backupDatabase()"><?php _e('Backup'); ?></a>
	</p>
	<p> 
		<span style="display:inline-block;width:350px"><?php _e('Restore database backup'); ?></span>
    	<a id="tools-restoreBtn" href="#" class="easyui-linkbutton" style="width:85px"
			data-options="iconCls:'icon-db_restore'" onclick="restoreDatabase(true)"><?php _e('Restore'); ?></a>
		<input type="file" class="icon_button icon-search" id="tools-restoreFile"
               name="restoreFile" accept="application/sql" required="required">
		<input id="tools-restoreData" type="hidden" name="restoreData" value="">
        <span id="tools-restoreProgress"></span>
	</p>
    <p>
        <span style="display:inline-block;width:350px"><?php _e('Update database fom Internet'); ?></span>
        <a id="tools-resInternetBtn" href="#" class="easyui-linkbutton" style="width:85px"
           data-options="iconCls:'icon-earth'" onclick="restoreDatabase(false)"><?php _e('Download'); ?></a>
    </p>
	<p> 
		<span style="display:inline-block;width:350px"><?php _e('Erase database contents'); ?></span>
    	<a id="tools-resetBtn" href="#" class="easyui-linkbutton" style="width:85px"
	   		data-options="iconCls:'icon-on'" onclick="clearDatabase()"><?php _e('Erase'); ?></a>
        <label for="tools-Federation"><?php _e('Federation'); ?>:</label>
        <select id="tools-Federation" name="Federation" style="width:200px"></select>
	</p>
	<p>
		<span style="display:inline-block;width:350px"><?php _e('Delete stored contests'); ?></span>
		<a id="tools-eraseBtn" href="#" class="easyui-linkbutton" style="width:85px"
		   data-options="iconCls:'icon-brush'" onclick="removePruebas()"><?php _e('Clear'); ?></a>
	</p>
	<p>
		<span style="display:inline-block;width:350px"><?php _e('Search for updates'); ?></span>
		<a id="tools-upgradeBtn" href="#" class="easyui-linkbutton" style="width:85px"
		   data-options="iconCls:'icon-reload'" onclick="checkForUpgrades()"><?php _e('Upgrade')?></a>
	</p>
    <p>
        <span style="display:inline-block;width:350px"><?php _e('Synchronize database with server'); ?></span>
        <a id="tools-syncdbBtn" href="#" class="easyui-linkbutton" style="width:85px"
           data-options="iconCls:'icon-updown'" onclick="synchronizeDatabase(true)"><?php _e('Sync')?></a>
        &nbsp;<span id="tools-syncdbLbl"></span>
    </p>
    <p>
        <span style="display:inline-block;width:350px"><?php _e('Debug and log trace file'); ?></span>
        <a id="tools-logResetBtn" href="#" class="easyui-linkbutton" style="width:85px"
           data-options="iconCls:'icon-brush'" onclick="resetLogFile()"><?php _e('Reset');?></a>
        <a id="tools-logViewBtn" href="#" class="easyui-linkbutton" style="width:85px"
           data-options="iconCls:'icon-search'" onclick="viewLogFile()"><?php _e('View');?></a>
    </p>
    <p>
        <span style="display:inline-block;width:350px"><?php _e('Clear temporary directory'); ?></span>
        <a id="tools-emptyTrashBtn" href="#" class="easyui-linkbutton" style="width:85px"
           data-options="iconCls:'icon-trash'" onclick="clearTempDir()"><?php _e('Trash')?></a>
    </p>
</div>

<script type="text/javascript">
    $('#tools-Federation').combogrid({
        width:275,
        panelWidth: 300,
        panelHeight: 150,
        idField: 'ID',
        textField: 'LongName',
        url: '../ajax/modules/moduleFunctions.php',
        queryParams: { 'Operation' : 'enumerate' },
        method: 'get',
        mode: 'remote',
        required: true,
        multiple: false,
        fitColumns: true,
        singleSelect: true,
        editable: false,  // to disable tablet keyboard popup
        selectOnNavigation: true, // let use cursor keys to interactive select
        columns: [[
            {field:'ID',  title:'<?php _e('ID'); ?>',width:'20',align:'left'},
            {field:'Name',hidden:true},
            {field:'LongName',        title:'<?php _e('Name'); ?>',width:'250',align:'right'}
        ]],
        onLoadSuccess: function(data) {
            var dg=$('#tools-Federation').combogrid('grid');
            dg.datagrid('insertRow',{
               index:0,
               row: { ID:-1, Name:'all', LongName:'<?php _e('All federations'); ?>'}
            });
            dg.datagrid('selectRow',0);
        }
    });

addTooltip($('#tools-backupBtn').linkbutton(),'<?php _e("Create and download a database backup copy"); ?>');
addTooltip($('#tools-restoreBtn').linkbutton(),'<?php _e("Restore database contents from previous backup"); ?>');
addTooltip($('#tools-resInternetBtn').linkbutton(),'<?php _e("Download and install latest database update from internet"); ?>');
addTooltip($('#tools-resetBtn').linkbutton(),'<?php _e("Clear EVERY database content for selected federation"); ?>');
addTooltip($('#tools-eraseBtn').linkbutton(),'<?php _e("Clear all contests from database"); ?>'+"<br />"+'<?php _e("leaving only dogs/handlers/judges/clubs information"); ?>');
addTooltip($('#tools-upgradeBtn').linkbutton(),'<?php _e("Search and install AgilityContest updates"); ?>');
addTooltip($('#tools-syncdbBtn').linkbutton(),'<?php _e("Synchronize current database content with<br/>stored data from AgilityContest server"); ?>');
addTooltip($('#tools-logResetBtn').linkbutton(),'<?php _e("Clear debugging and trace file"); ?>');
addTooltip($('#tools-logViewBtn').linkbutton(),'<?php _e("Download debug and trace file"); ?>');
addTooltip($('#tools-emptyTrashBtn').linkbutton(),'<?php _e("Remove every unneeded files from temporary directory"); ?>');

// disable some buttons when running in server
if (checkForServer()) {
    $('#tools-restoreBtn').linkbutton('disable'); // disable restore from app. use mysqldump'ed files to force reindexes
    $('#tools-resInternetBtn').linkbutton('disable'); // disable restore from internet
    $('#tools-syncdbBtn').linkbutton('disable'); // disable download database updates
}

</script>