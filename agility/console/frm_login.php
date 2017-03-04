<!-- 
frm_login.php

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

<img class="mainpage" src="/agility/server/getRandomImage.php" alt="wallpaper" width="640" height="480" align="middle" />

<!-- FORMULARIO DE introduccion de usuario y contrasenya -->
<div id="login-window" class="easyui-window" style="position:relative;width:500px;height:auto;padding:20px 20px">
<!-- panel de login -->
	<div id="login-Layout" class="easyui-layout" data-options="fit:true'">
	<div style="padding:5px;" data-options="region:'north',border:'false'">
		<p>
		<?php _e('Some operations require a valid user to be logged in'); ?>.<br />
		<?php _e('If no user is entered, youll be logged as "guest"'); ?>
		</p>
	</div> 
	<!-- formulario de datos de login -->
	<div id="login-Form" style="padding:5px;" data-options="region:'center',border:'true'">
		<form id="login-Selection">
    		<div class="fitem">
    	   		<label for="login-Username"><?php _e('User name'); ?>:</label>
       	   		<input id="login-Username" name="Username" style="width:200px" type="text"/>
       		</div>        		
       		<div class="fitem">
       	   		<label for="login-Password"><?php _e('Password'); ?>:</label>
       	   		<input id="login-Password" name="Password" style="width:200px" type="password"/>
       		</div>
       		<div class="fitem">
       	   		<label for="login-Federation"><?php _e('Federation'); ?>:</label>
				<select id="login-Federation" name="Federation" style="width:200px"></select>
       		</div>
		</form>
	</div>
	
	<!-- botones del menu de login-->
	<div id="login-Buttons" data-options="region:'south',border:false" style="text-align:right;padding:5px 0 0;">
		<a id="login-okBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls: 'icon-ok'" onclick="acceptLogin()"><?php _e('Accept'); ?></a>
		<a id="login-cancelBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls: 'icon-cancel'" onclick="cancelLogin()"><?php _e('Cancel'); ?></a>
	</div>
	</div>
</div> <!-- Dialog -->
<script type="text/javascript">

$('#login-Password').textbox({
    required:true,
    validType:'length[1,255]',
    iconCls:'icon-lock'
}).textbox('textbox').bind('keypress', function (evt) {
    //on Enter key on passwd field click on accept
    if (evt.keyCode != 13) return true;
    $('#login-okBtn').click();
    return false;
});

$('#login-Username').textbox({
    required:true,
    validType:'length[1,255]',
    iconCls:'icon-man'
}).textbox('textbox').bind('keypress', function (evt) {
// on Enter key on login field focus on password
    if (evt.keyCode != 13) return true;
    $('#login-Password').textbox('textbox').focus();
    return false;
});


$('#login-window').window({
	title:'<?php _e('Session init'); ?>',
	iconCls:'icon-users',
	collapsible:false,
	minimizable:false,
	maximizable:false,
	closable:true,
	closed:false,
	shadow:true,
	modal:true,
	onBeforeOpen: function () {
		$('#login-Usuario').val('');
		$('#login-Password').val('');
		$('#login-Federation').val(workingData.federation);
		return true;
	}
});

$('#login-Federation').combogrid({
	panelWidth: 300,
	panelHeight: 150,
	idField: 'ID',
	textField: 'LongName',
	url: '/agility/modules/moduleFunctions.php?Operation=enumerate',
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
		{field:'LongName',        title:'<?php _e('Name'); ?>',width:'250',align:'right'},
		{field:'Logo',hidden:true},
		{field:'ParentLogo',hidden:true}
	]],
	onChange:function(value){ setFederation(value); }
});

addTooltip($('#login-okBtn').linkbutton(),'<?php _e("Start session with provided user privileges"); ?>');
addTooltip($('#login-cancelBtn').linkbutton(),'<?php _e("Start session as <em>guest</em> user. Close window"); ?>');

</script>