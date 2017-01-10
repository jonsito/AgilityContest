<!-- 
frm_logout.php

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
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>

<!-- FORMULARIO DE finalizacion de sesion -->
<img class="mainpage" src="/agility/server/getRandomImage.php" alt="wallpaper" width="640" height="480" align="middle" />

<div id="logout-window" class="easyui-window" style="position:relative;width:500px;height:150px;padding:20px 20px">
	<div id="selprueba-Layout" class="easyui-layout" data-options="fit:true'">
		<!--  texto del panel de logout -->
		<div data-options="region:'center',border:'true'">
			<?php _e('Confirm logout on current session'); ?>: <span id="login-UserName"><?php _e('User'); ?></span>
		</div><!-- botones del menu de logout-->
		<div id="logout-Buttons" data-options="region:'south',border:false" style="text-align:right;padding:5px 0 0;">
		   	<a id="logout-okBtn" href="#" class="easyui-linkbutton" 
		    	data-options="iconCls: 'icon-ok'" onclick="acceptLogout()"><?php _e('Accept'); ?></a>
		   	<a id="logout-cancelBtn" href="#" class="easyui-linkbutton" 
		    	data-options="iconCls: 'icon-cancel'" onclick="cancelLogout()"><?php _e('Cancel'); ?></a>
		</div>
	</div>
</div> <!-- Dialog -->

<script type="text/javascript">

$('#logout-window').window({
	title:'<?php _e('Logout session');?>',
	iconCls:'icon-users',
	collapsible:false,
	minimizable:false,
	maximizable:false,
	closable:true,
	closed:false,
	shadow:true,
	modal:true,
	onBeforeOpen:function() {
		$('#login-UserName').html(ac_authInfo.Login);
	}
});

addTooltip($('#logout-okBtn').linkbutton(),'<?php _e("Close current user session");?>');
addTooltip($('#logout-cancelBtn').linkbutton(),'<?php _e("Cancel logout. Close window");?>');

</script>