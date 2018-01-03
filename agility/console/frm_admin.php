<!-- 
frm_admin.php

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
$config =Config::getInstance();
?>

 <div id="admin-tab" class="easyui-tabs" style="width:100%;height:550px;">
   	<div title="<?php _e('Users'); ?>" data-options="iconCls:'icon-users'" style="padding:5px;border:solid 1px #000000">
    	<?php require_once("dialogs/dlg_usuarios.inc")?>
   	</div>
   	<div title="<?php _e('Sessions'); ?>" data-options="iconCls:'icon-order'" style="padding:5px">
        <?php require_once("dialogs/dlg_sesiones.inc")?>
        <?php require_once("dialogs/dlg_remotecontrol.inc")?>
   	</div>
   	<div title="<?php _e('Preferences'); ?>" data-options="iconCls:'icon-setup'" style="padding:5px">
    	<?php require_once("dialogs/dlg_configuracion.inc")?>
   	</div>
	 <div title="<?php _e('Plugins'); ?>" data-options="iconCls:'icon-more'" style="padding:5px">
		 <?php require_once("dialogs/dlg_plugins.inc")?>
	 </div>
   	<div title="<?php _e('Tools'); ?>" data-options="iconCls:'icon-tools'" style="padding:5px">
    	<?php require_once("dialogs/dlg_tools.inc")?>
   	</div>
 </div>

<script type="text/javascript">
    $('#admin-tab').tabs({
        onSelect: function(title,index) {
            // when open tools tab, if configured check for db updates
            if (title!=="<?php _e('Tools'); ?>") return;
            if (parseInt(ac_config.search_updatedb)===0) { $('#tools-syncdbLbl').html(""); return }
            checkForDatabaseUpdates();
        }
    });
</script>