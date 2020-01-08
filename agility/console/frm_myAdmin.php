<!-- 
frm_myAdmin.php

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
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
$config =Config::getInstance();
?>

<img class="mainpage" src="../ajax/images/getRandomImage.php" alt="wallpaper" width="700" height="480" align="middle" />

<!-- FORMULARIO DE introduccion de usuario y contrasenya -->
<div id="myAdmin-window" style="position:relative;width:750px;height:auto;padding:10px 10px">
<!-- panel de myAdmin -->
	<div id="myAdmin-Layout" class="easyui-layout" data-options="fit:true'">
	<div style="padding:5px;" data-options="region:'north',border:'false'">
		<h2><?php _e('Notice'); ?>:</h2>
		<p>
		<strong>
		<?php _e('Direct database access is a delicate operation'); ?>
		<?php _e('and should be performed <br />by people with <em>real</em> knowledge about AgilityContest internals'); ?>
		</strong>
		</p>
		<p>
		<?php _e('Incorrect handing of database contents may result in data loss and inconsistencies'); ?>,<br />
		<?php _e('and let the application un-usable'); ?>
		</p>
		<p>
		<?php _e('Please provide username and password for an user with<em> admin permissions</em> to continue'); ?>
		</p>
	</div> 
	<!-- formulario de datos de myAdmin -->
	<div id="myAdmin-Form" style="padding:5px;" data-options="region:'center',border:'true'">
		<form id="myAdmin-Selection">
    		<div class="fitem">
    	   		<label for="myAdmin-Username"><?php _e('Admin user'); ?>:</label>
       	   		<input id="myAdmin-Username" name="Username" style="width:200px" type="text"/>
       		</div>        		
       		<div class="fitem">
       	   		<label for="myAdmin-Password"><?php _e('Password'); ?>:</label>
       	   		<input id="myAdmin-Password" name="Password" style="width:200px" type="password"/>
       		</div>
		</form>
	</div>
	
	<!-- botones del menu de myAdmin-->
	<div id="myAdmin-Buttons" data-options="region:'south',border:false" style="text-align:right;padding:5px 0 0;">
		<a id="myAdmin-okBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls: 'icon-ok'" onclick="acceptMyAdmin()"><?php _e('Accept'); ?></a>
		<a id="myAdmin-cancelBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls: 'icon-cancel'" onclick="cancelMyAdmin()"><?php _e('Cancel'); ?></a>
	</div>
	</div>
</div> <!-- Dialog -->
<script type="text/javascript">

$('#myAdmin-Username').textbox({
    required:true,
    validType:'length[1,255]',
    iconCls:'icon-man'
});

$('#myAdmin-Password').textbox({
    required:true,
    validType:'length[1,255]',
    iconCls:'icon-lock'
});

$('#myAdmin-window').window({
	title:'<?php _e('Direct DB Access'); ?>',
	iconCls:'icon-users',
	collapsible:false,
	minimizable:false,
	maximizable:false,
	closable:false,
	closed:false,
	shadow:false,
	modal:true,
	onBeforeOpen: function () {
		$('#myAdmin-Usuario').val('');
		$('#myAdmin-Password').val('');
		return true;
	}
});
		
addTooltip($('#myAdmin-okBtn').linkbutton(),'<?php _e("Validate user. Open phpMyAdmin database manager panel"); ?>');
addTooltip($('#myAdmin-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');

</script>