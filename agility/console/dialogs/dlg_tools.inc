<h3><?php _e("Tools");?></h3>

<div id="tools-dlg-admintab">
	<p> 
		<span style="display:inline-block;width:350px"><?php _e('Create database backup'); ?>: </span>
    	<a id="tools-backupBtn" href="#" class="easyui-linkbutton" style="width:85px"
	   		data-options="iconCls:'icon-db_backup'" onclick="backupDatabase()"><?php _e('Backup'); ?></a>
	</p>
	<p> 
		<span style="display:inline-block;width:350px"><?php _e('Restore database backup'); ?></span>
    	<a id="tools-restoreBtn" href="#" class="easyui-linkbutton" style="width:85px"
			data-options="iconCls:'icon-db_restore'" onclick="restoreDatabase()"><?php _e('Restore'); ?></a>
		<input type="file" class="icon_button icon-search "id="tools-restoreFile" name="restoreFile" accept="application/sql" required="required" onchange="read_restoreFile(this)">
		<input id="tools-restoreData" type="hidden" name="restoreData" value="">
	</p>
	<p> 
		<span style="display:inline-block;width:350px"><?php _e('Erase database contents'); ?></span>
    	<a id="tools-resetBtn" href="#" class="easyui-linkbutton" style="width:85px"
	   		data-options="iconCls:'icon-on'" onclick="clearDatabase()"><?php _e('Reset'); ?></a>
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

addTooltip($('#tools-backupBtn').linkbutton(),'<?php _e("Create and download a database backup copy"); ?>');
addTooltip($('#tools-restoreBtn').linkbutton(),'<?php _e("Restore database contents from previous backup"); ?>');
addTooltip($('#tools-resetBtn').linkbutton(),'<?php _e("Factory reset. Clear EVERY content on database"); ?>');
addTooltip($('#tools-eraseBtn').linkbutton(),'<?php _e("Clear contests from database"); ?>'+"<br />"+'<?php _e("leaving only dogs/handlers/judges/clubs information"); ?>');
addTooltip($('#tools-upgradeBtn').linkbutton(),'<?php _e("Search and install AgilityContest updates"); ?>');
addTooltip($('#tools-logResetBtn').linkbutton(),'<?php _e("Clear debugging and trace file"); ?>');
addTooltip($('#tools-logViewBtn').linkbutton(),'<?php _e("Download debug and trace file"); ?>');
addTooltip($('#tools-emptyTrashBtn').linkbutton(),'<?php _e("Remove every unneeded files from temporary directory"); ?>');
</script>