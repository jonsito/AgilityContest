<!-- 
frm_about.php

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

<div id="dlg_register" style="width:640px;padding:10px">
	<img src="/agility/images/AgilityContest.png" 
		width="150" height="100" alt="AgilityContest Logo" 
		style="border:1px solid #000000;margin:10px;float:right;padding:5px">
	<dl>
		<dt>
			<strong><?php _e('Version'); ?>: </strong><span id="reg_version">version</span> - <span id="reg_date">date</span>
		</dt>
		<dt>
			<strong>AgilityContest</strong> <?php _e('is Copyright &copy; 2013-2015 by'); ?> <em> Juan Antonio Martínez &lt;juansgaviota@gmail.com&gt;</em>
		</dt>
		<dd>
		<?php _e('Source code is available at'); ?> <a href="https://github.com/jonsito/AgilityContest">https://github.com/jonsito/AgilityContest</a><br />
		<?php _e('You can use, copy, modify and re-distribute under terms of'); ?>
		<a target="license" href="/agility/License"><?php _e('GNU General Public License'); ?></a>
		</dd>
	</dl>
	<p>
	<?php _e('Registered at'); ?> 'Registro Territorial de la Propiedad Intelectual de Madrid'. <em>Expediente: 09-RTPI-09439.4/2014</em>
	</p>
	<hr />&nbsp;<br />
	<strong><?php _e('Licensing information'); ?>:</strong>
	<form id="registration_data">
		<div class="fitem">
			<label for="rd_User"><?php _e('Name'); ?>:</label>
			<input id="rd_User" type="text" readonly="readonly" name="User" /><br/>
		</div>
		<div class="fitem">
			<label for="rd_Email"><?php _e('E-mail'); ?>:</label>
			<input id="rd_Email" type="text" readonly="readonly" name="Email" /><br/>
		</div>
		<div class="fitem">
			<label for="rd_Club"><?php _e('Club'); ?>:</label>
			<input id="rd_Club" type="text" readonly="readonly" name="Club" /><br/>
		</div>
		<div class="fitem">
			<label for="rd_Serial"><?php _e('Serial num'); ?>:</label>
			<input id="rd_Serial" type="text" readonly="readonly" name="Serial" /><br/>
		</div>
		<div class="fitem">
			<label for="rd_Expires"><?php _e('Expiration date'); ?>:</label>
			<input id="rd_Expires" type="text" readonly="readonly" name="Expires" /><br/>
		</div>
	</form>
	<br /><hr />&nbsp;<br />
	<form id="register_file">
	<div>
		<span style="float:left">	
		<a id="registration-okButton" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-key'"
   			onclick="send_regFile()"><?php _e('Register'); ?></a>
		<input type="file" name="fichero" required="required" accept=".info" onchange="read_regFile(this)"/><br/>
		<input id="registrationData" type="hidden" name="Data" value="">
		</span> 
		<span style="float:right">
			<a id="registration-cancelButton" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-cancel'"
   			onclick="$('#dlg_register').window('close');"><?php _e('Close'); ?></a>
		</span>
	</div>
	</form>
</div>

<script type="text/javascript">
        $('#dlg_register').window({
            title: '<?php _e("Licensing information"); ?>',
            collapsible:false,
            minimizable:false,
            maximizable:false,
            resizable:false,
            closable:true,
            modal:true,
            iconCls: 'icon-dog',
            onOpen: function() { 
                $('#reg_version').html(ac_config.version_name);
                $('#reg_date').html(ac_config.version_date);
                $('#registration_data').form('load','/agility/server/adminFunctions.php?Operation=reginfo');
            },  
            onClose: function() {loadContents('/agility/console/frm_main.php','',{'registration':'#dlg_register'});
            }
        });
        
		addTooltip($('#registration-okButton').linkbutton(),'<?php _e("Import license file into application"); ?>');
		addTooltip($('#registration-cancelButton').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
</script>