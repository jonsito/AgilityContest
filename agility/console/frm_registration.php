<!-- 
frm_about.php

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

<div id="dlg_register" style="width:700px;padding:10px">
	<img src="/agility/images/AgilityContest.png" 
		width="150" height="100" alt="AgilityContest Logo" 
		style="border:1px solid #000000;margin:10px;float:right;padding:5px">
	<dl>
		<dt>
			<strong><?php _e('Version'); ?>: </strong><span id="reg_version">version</span> - <span id="reg_date">date</span>
		</dt>
		<dt>
			<strong>AgilityContest</strong> <?php _e('is Copyright &copy; 2013-2018 by'); ?> <em> Juan Antonio Martínez &lt;juansgaviota@gmail.com&gt;</em>
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
	<hr />
	<form id="registration_data">
	<table width="100%">
		<tr>
			<td><strong><?php _e('Licensing information'); ?>:</strong></td>
			<td><strong><?php _e('Current License Capabilities'); ?>:</strong><br/></td>
		</tr>
		<tr>
		<td>
			<div class="fitem">
				<label for="rd_User"><?php _e('Name'); ?>:</label>
				<input id="rd_User" type="text" readonly="readonly" name="User"/><br/>
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
            <div class="fitem">
                <label for="rd_Status"><?php _e('Status'); ?>:</label>
                <input id="rd_Status" type="text" readonly="readonly" name="Status" /><br/>
            </div>
		</td>
		<td>
			<input type="checkbox" disabled="disabled" value="1" name="ENABLE_IMPORT" /><?php _e("Import from Excel files");?><br />
			<input type="checkbox" disabled="disabled" value="2" name="ENABLE_TEAMS"/><?php _e("Team contests");?><br />
			<input type="checkbox" disabled="disabled" value="4" name="ENABLE_KO"/><?php _e("KO and Games rounds");?><br />
			<input type="checkbox" disabled="disabled" value="8" name="ENABLE_SPECIAL"/><?php _e("Series with more than 2 rounds");?><br />
			<input type="checkbox" disabled="disabled" value="16" name="ENABLE_VIDEOWALL"/><?php _e("VideoWall and ScoreBoard");?><br />
			<input type="checkbox" disabled="disabled" value="32" name="ENABLE_PUBLIC"/><?php _e("Internet/Wifi public Access");?><br />
			<input type="checkbox" disabled="disabled" value="64" name="ENABLE_CHRONO"/><?php _e("Chronometer connection");?><br />
			<input type="checkbox" disabled="disabled" value="128" name="ENABLE_ULIMIT"/><?php _e("Unlimited inscriptions in a contest");?><br />
			<input type="checkbox" disabled="disabled" value="256" name="ENABLE_LIVESTREAM"/><?php _e("LiveStream OnScreenDisplay signal");?><br />
            <input type="checkbox" disabled="disabled" value="512" name="ENABLE_TRAINING"/><?php _e("Trainning sessions handling");?><br />
            <input type="checkbox" disabled="disabled" value="1024" name="ENABLE_LEAGUES"/><?php _e("Federation leagues scoring");?><br />
		</td>
	</tr></table>
	</form>&nbsp;
	<br />
	<hr />
	<form id="register_file">
	<div>
		<span style="float:left">	
		<a id="registration-okButton" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-key'"
   			onclick="send_regFile()"><?php _e('Register'); ?></a>
        <input name="fichero" id="registration-fichero" style="width:350px;" onchange="read_regFile(this)"/>
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
    $('#rd_User').textbox();
    $('#rd_Email').textbox();
    $('#rd_Club').textbox();
    $('#rd_Serial').textbox();
    $('#rd_Expires').textbox();
    $('#rd_Status').textbox();
    var fb=$('#registration-fichero');
    fb.filebox({
        accept:  ".info",
        buttonText: '<?php _e("Select"); ?>',
        buttonAlign: 'left',
        buttonIcon: 'icon-search',
        onChange: function(newfile,oldfile) {
            read_regFile(fb.next().find('.textbox-value')[0]); // locate real input text
        }
    });
    fb.next().find('.textbox-value').attr('accept', '.info');

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

    addTooltip(fb.next().find('.textbox-button'),'<?php _e("Select license file to import"); ?>');
    addTooltip($('#registration-okButton').linkbutton(),'<?php _e("Import license file into application"); ?>');
	addTooltip($('#registration-cancelButton').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
</script>