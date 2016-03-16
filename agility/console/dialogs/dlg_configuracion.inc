<!-- 
dlg_configuracion.php

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
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<!-- Formularios del menu de configuracion de la aplicacion -->

 <div id="config-panel" class="easyui-panel" style="width:800px;height:300px;padding:10px 20px"
	data-options="fit:true,footer:'#config-dlg-buttons',onOpen:loadConfig">
	<form id="config-form" name="configuracion" class="easyui-form">
	<div id="config-tabs" class="easyui-tabs" data-options="tabPosition:'left'" style="width:100%;height:auto">
		<input id="config-Operation" type="hidden" name="Operation" value="saveConfig"/>
		<!-- TODO: Los parametros de la base de datos solo se deben presentar en el primer arranque -->

		<div title="<?php _e('Desktop'); ?>" style="padding:10px">
			<h3><?php _e('Desktop look and feel'); ?></h3>
			<label for="config_theme"><?php _e('Dekstop theme'); ?></label>
			<select id="config_theme" name="easyui_theme">
				<option value="default" selected="selected"><?php _e('Default theme'); ?></option>
				<option value="metro"><?php _e('Metro'); ?></option>
				<option value="gray"><?php _e('Grey'); ?></option>
				<option value="black"><?php _e('Black'); ?></option>
			</select><br />
			<label for="easyui_bgcolor"><?php _e('Desktop background color'); ?></label>
			<input id="easyui_bgcolor" name="easyui_bgcolor" value="#0000ff" type="color"/><br />
			<label for="easyui_hdrcolor"><?php _e('Header title color'); ?></label>
			<input id="easyui_hdrcolor" name="easyui_hdrcolor" value="#00ff00" type="color"/><br />
			<label for="easyui_opcolor"><?php _e('Active menu header color'); ?></label>
			<input id="easyui_opcolor" name="easyui_opcolor" value="opcolor" type="color"/><br />
			<label for="easyui_rowcolor1"><?php _e('Color for even rows in tables'); ?></label>
			<input id="easyui_rowcolor1" name="easyui_rowcolor1" value="#e0e0e0" type="color"/><br />
			<label for="easyui_rowcolor2"><?php _e('Color for odd rows in tables'); ?></label>
			<input id="easyui_rowcolor2" name="easyui_rowcolor2" value="#c0c0c0" type="color"/><br />
			<label for="easyui_rowcolor3"><?php _e('Color for extra rows in tables'); ?></label>
			<input id="easyui_rowcolor3" name="easyui_rowcolor3" value="#c0c0f0" type="color"/><br />
		</div>
	
		<div title="<?php _e('Tablet &amp; Chrono'); ?>" style="padding:10px">
			<h3><?php _e('Judge assistant tablet configuration'); ?></h3>

			<label for="tablet_beep"><?php _e('Emit beep on key pressed'); ?></label>
			<input type="hidden" name="tablet_beep" value="0"/>
			<input id="tablet_beep" name="tablet_beep" type="checkbox" value="1"/><br />
			<label for="tablet_dnd"><?php _e('Allow modify starting order from tablet'); ?></label>
			<input type="hidden" name="tablet_dnd" value="0"/>
			<input id="tablet_dnd" name="tablet_dnd" type="checkbox" value="1"/><br />
			<label for="tablet_next"><?php _e('Accept button save data and goes to next participant'); ?></label>
			<input type="hidden" name="tablet_next" value="0"/>
			<input id="tablet_next" name="tablet_next" type="checkbox" value="1"/><br />
			<label for="tablet_keyboard"><?php _e('Enable keyboard when running in laptop/netbook'); ?></label>
			<input type="hidden" name="tablet_keyboard" value="0"/>
			<input id="tablet_keyboard" name="tablet_keyboard" type="checkbox" value="1"/><br />


			<h3><?php _e('Electronic chronometer behaviour'); ?></h3>

			<label for="crono_rectime"><?php _e('Course Walk duration'); ?></label>
			<input type="number" min="3" max="12" size="2" value="7" style="width:50px" name="crono_rectime" id="crono_rectime"/> <?php _e('Minutes'); ?><br />
			<label for="tablet_chrono"><?php _e('Show chrono data on tablet'); ?></label>
			<input type="hidden" name="tablet_chrono" value="0"/>
			<input id="tablet_chrono" name="tablet_chrono" type="checkbox" value="1"/><br />


			<label for="crono_intermediate"><?php _e('Show intermediate time stats in videowall'); ?></label>
			<input type="hidden" name="crono_intermediate" value="0"/>
			<input id="crono_intermediate" name="crono_intermediate" type="checkbox" value="1"/><br />

			<label> <?php _e('Action to perform at 15sec countdown end'); ?></label>
            <input id="tcd1" type="radio" name="tablet_countdown" value="1" ><?php _e('Ignore'); ?>
            <input id="tcd2" type="radio" name="tablet_countdown" value="2" checked="checked"/><?php _e('Start manual chrono'); ?>
            <input id="tcd3" type="radio" name="tablet_countdown" value="3"/><?php _e('Eliminate team'); ?><br />

			<label for="crono_resync"><?php _e('On sensor detection when manual chrono is active'); ?></label>
			<input id="crs1" type="radio" name="crono_resync" value="0"  checked="checked"/><?php _e('Restart counter from zero'); ?>
			<input id="crs2" type="radio" name="crono_resync" value="1"/><?php _e('Continue with manual chrono count'); ?>
		</div>
		
		<div title="<?php _e('VideoWall'); ?>" style="padding:10px">
			<h3><?php _e('Video wall configurable parameters'); ?></h3>
			<label for="vw_polltime"><?php _e('Refresh period (seg.)'); ?></label>
			<input id="vw_polltime" name="vw_polltime" type="text" size="2" value="5"/><br />
			<label for="vw_hdrbg1"><?php _e('Background color for main header'); ?></label>
			<input id="vw_hdrbg1" name="vw_hdrbg1" type="color" value="#0000ff"/><br />
			<label for="vw_hdrfg1"><?php _e('Text color for main header'); ?></label>
			<input id="vw_hdrfg1" name="vw_hdrfg1" type="color" value="#ffffff"/><br />

			<label for="vw_hdrbg2"><?php _e('Background color for secondary header'); ?></label>
			<input id="vw_hdrbg2" name="vw_hdrbg2" type="color" value="#c0c0c0"/><br />
			<label for="vw_hdrfg2"><?php _e('Text color for secondary header'); ?></label>
			<input id="vw_hdrfg2" name="vw_hdrfg2" type="color" value="#ffffff"/><br />

            <label for="vw_hdrbg3"><?php _e('Background color for tertiary header'); ?></label>
            <input id="vw_hdrbg3" name="vw_hdrbg3" type="color" value="#c0c0c0"/><br />
            <label for="vw_hdrfg3"><?php _e('Text color for tertiary header'); ?></label>
            <input id="vw_hdrfg3" name="vw_hdrfg3" type="color" value="#000000"/><br />

			<label for="vw_rowcolor1"><?php _e('Background color for tables even rows'); ?></label>
			<input id="vw_rowcolor1" name="vw_rowcolor1" type="color" value="#e0e0e0"/><br />
			<label for="vw_rowcolor2"><?php _e('Background color for tables odd rows'); ?></label>
			<input id="vw_rowcolor2" name="vw_rowcolor2" type="color" value="#c0c0c0"/><br />

			<h3><?php _e('Live Stream configurable parameters'); ?></h3>
			<label for="vw_alpha"><?php _e('Transparency level for OnScreenDisplay'); ?></label>
			<input id="vw_alpha" name="vw_alpha" type="text" size="3" value="0.5"/> (0.0-1.0)<br />
			<label for="vw_chromakey"><?php _e('Color for Chroma Keyer'); ?></label>
			<input id="vw_chromakey" name="vw_chromakey" type="color" value="#00ff00"/><br />

		</div>
		
		<div title="<?php _e('Reports'); ?>" style="padding:10px">
			<h3><?php _e('PDF forms and reports generation'); ?></h3>
			<label for="pdf_fontfamiliy"><?php _e('Font family'); ?></label>
			<select id="pdf_fontfamily" name="pdf_fontfamily">
				<option value="DejaVu" selected="selected">DejaVu (UTF-8)</option>
				<option value="Free" selected="selected">GNU Free (UTF-8)</option>
				<option value="Courier">Courier (iso-latin1)</option>
				<option value="Helvetica">Helvetica (iso-latin1)</option>
				<option value="Times">Times (iso-latin1)</option>
			</select><br />
			<label for="pdf_journeys"><?php _e('Show journey info when printing catalog'); ?></label>
			<input type="hidden" name="pdf_journeys" value="0"/>
			<input id="pdf_journeys" name="pdf_journeys" type="checkbox" value="1"/><br />
			<label for="pdf_hdrbg1"><?php _e('Background color for main header'); ?></label>
			<input id="pdf_hdrbg1" name="pdf_hdrbg1" type="color" value="#ff8020"/><br />
			<label for="pdf_hdrfg1"><?php _e('Text color on main header'); ?></label>
			<input id="pdf_hdrfg1" name="pdf_hdrfg1" type="color" value="#000000"/><br />
			<label for="pdf_hdrbg2"><?php _e('Background color for secondary header'); ?></label>
			<input id="pdf_hdrbg2" name="pdf_hdrbg2" type="color" value="#c0c0c0"/><br />
			<label for="pdf_hdrfg2"><?php _e('Text color for secondary header'); ?></label>
			<input id="pdf_hdrfg2" name="pdf_hdrfg2" type="color" value="#ffffff"/><br />
			<label for="pdf_rowcolor1"><?php _e('Background color for tables even rows'); ?></label>
			<input id="pdf_rowcolor1" name="pdf_rowcolor1" type="color" value="#ffffff"/><br />
			<label for="pdf_rowcolor2"><?php _e('Background color for tables odd rows'); ?></label>
			<input id="pdf_rowcolor2" name="pdf_rowcolor2" type="color" value="#e0ebff"/><br />
			<label for="pdf_linecolor"><?php _e('Color for separation lines'); ?></label>
			<input id="pdf_linecolor" name="pdf_linecolor" type="color" value="#808080"/><br />
			<h3><?php _e('Label printing'); ?></h3>
            <label><?php _e('Label sheet format (# labels/sheet)'); ?></label>
            <label for="pdf_labelheight16" style="width:130px;">
                <input type="radio" id="pdf_labelheight16" name="pdf_labelheight" value="17" checked="checked" onclick="$('#pdf_topmargin').val('10.0');"/>
                145x17 (16)
            </label>
            <label for="pdf_labelheight13" style="width:130px;">
                <input type="radio" id="pdf_labelheight13" name="pdf_labelheight" value="20" onclick="$('#pdf_topmargin').val('15.0');"/>
                147x20 (13)
            </label>
            <br />
			<label for="pdf_topmargin"><?php _e('Label sheet upper margin (mmts)'); ?></label>
			<input id="pdf_topmargin" name="pdf_topmargin" type="text" size="4" value="10.0"/><br />
			<label for="pdf_leftmargin"><?php _e('Label sheet right margin (mmts)'); ?></label>
			<input id="pdf_leftmargin" name="pdf_leftmargin" type="text" size="4" value="10.0"/><br />
		</div>
		
		<div title="<?php _e('Internet'); ?>" style="padding:10px">
		<h3><?php _e('Public access from internet'); ?></h3>
			<label for="web_refreshtime"><?php _e('Data auto-refresh interval'); ?></label>
			<select id="web_refreshtime" name="web_refreshtime">
				<option value="0" selected="selected"><?php _e('No refresh'); ?></option>
				<option value="45">45 sec.</option>
				<option value="60">1 min.</option>
				<option value="90">1,5 min.</option>
				<option value="90">2 min.</option>
				<option value="300">5 min.</option>
			</select><br />
		</div>
				
		<div title="<?php _e('System'); ?>" style="padding:10px">
			<h3><?php _e('General configuration'); ?></h3>
			<label for="config_lang"><?php _e('Default language'); ?></label>
			<select id="config_lang" name="lang">
				<option value="es_ES" selected="selected"><?php _e('Spanish'); ?></option>
				<option value="en_US"><?php _e('English'); ?></option>
				<option value="de_DE"><?php _e('German'); ?></option>
			</select><br />
			<label for="accept_lang"><?php _e('Choose language from browser preferences'); ?></label>
			<input type="hidden" name="accept_lang" value="0"/> <!-- trick for checkboxes -->
			<input type="checkbox" name="accept_lang" id="accept_lang" value="1"/><br />
			<label for="debug_level"><?php _e('Debug level'); ?></label>
			<select id="debug_level" name="debug_level">
				<!-- "PANIC","ALERT","ERROR","WARN","NOTICE","INFO","DEBUG","TRACE","ALL" -->
				<option value="0" ><?php _e('None'); ?>.</option>
				<option value="1" ><?php _e('Unusable syetem'); ?></option>
				<option value="2" selected="selected"><?php _e('Non recoverable errors'); ?></option>
				<option value="3" ><?php _e('Recoverable error'); ?></option>
				<option value="4" ><?php _e('Notice alerts'); ?></option>
				<option value="5" ><?php _e('Informative messages'); ?></option>
				<option value="6" ><?php _e('Debugging information'); ?></option>
				<option value="7" ><?php _e('Execution trace registering'); ?></option>
				<option value="8" ><?php _e('Register everything'); ?></option>
			</select><br />
            <label for="register_events"><?php _e('Provide raw file copy for system events'); ?></label>
            <input type="hidden" name="register_events" value="0"/> <!-- trick for checkboxes -->
            <input type="checkbox" name="register_events" id="register_events" value="1"/><br />

			<label for="event_printer"><?php _e('Printer device name to write tablet reports'); ?></label>
			<input id="event_printer" name="event_printer" type="text" size="10" value=""/> <?php _e('Paper size'); ?>:
			<label for="wide_printer58" style="width:70px;">
				<input type="radio" id="wide_printer58" name="wide_printer" value="0" checked="checked"/> 58mm
			</label>
			<label for="wide_printer80" style="width:70px;">
				<input type="radio" id="wide_printer80" name="wide_printer" value="1"/>	80mm
			</label>
			<input id="printer-checkBtn" type="button" value="<?php _e("Check");?>" onclick="printerCheck();"/>
			<br />

            <label for="reset_events"><?php _e('Clear event registration on tablet init session'); ?></label>
            <input type="hidden" name="reset_events" value="0"/> <!-- trick for checkboxes -->
            <input type="checkbox" name="reset_events" id="reset_events" value="1"/><br />
			<label for="proximity_alert"><?php _e('Proximity alert on starting order'); ?></label>
			<input type="number" min="1" max="20" size="2" value="5" name="proximity_alert" id="proximity_alert"/> <?php _e('Dogs'); ?><br />

			<label><?php _e('Time data precision shown'); ?></label>
			<label for="crono_cents" style="width:130px;margin-top:5px;">
				<input type="radio" id="crono_cents" name="crono_miliseconds" value="0" checked="checked" /><?php _e('cents'); ?>
			</label>
			<label for="crono_milis" style="width:130px;margin-top:5px;">
				<input type="radio" id="crono_milis" name="crono_miliseconds" value="1"/><?php _e('millis'); ?>
			</label><br />
			<h3><?php _e('Additional server information'); ?></h3>
			<label for="ip_address"><?php _e('IP address(es)'); ?></label>
			<input id="ip_address" name="ip_address" type="text" readonly="readonly" size="45"
				   value="<?php $a=new networkInterfaces(); echo implode(", ",$a->get_interfaces());?>" />
		</div>
	</div>
	</form>
</div>

<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE CAMBIO DE CONTRASENYA -->
<div id="config-dlg-buttons">
	<span  style="float:left;padding:5px">
    <a id="config-resetBtn" href="#" class="easyui-linkbutton" 
    	data-options="iconCls:'icon-reload'" onclick="loadConfig()"><?php _e('Restore'); ?></a>
    <a id="config-defaultBtn" href="#" class="easyui-linkbutton" 
    	data-options="iconCls:'icon-setup'" onclick="defaultConfig()"><?php _e('Default'); ?></a>
    </span>
	<span  style="float:right;padding:5px">
    <a id="config-okBtn" href="#" class="easyui-linkbutton" 
    	data-options="iconCls:'icon-save'" onclick="saveConfig()"><?php _e('Save'); ?></a>
    </span>
</div>

<script type="text/javascript">
	addTooltip($('#event_printer').linkbutton(),'<?php _e("Enter printer name/device or leave empty to disable"); ?>');
	addTooltip($('#config-resetBtn').linkbutton(),'<?php _e("Cancel changes. Restore information from server"); ?>');
	addTooltip($('#config-defaultBtn').linkbutton(),'<?php _e("Factory reset. Configure with default data"); ?>');
	addTooltip($('#config-okBtn').linkbutton(),'<?php _e("Accept changes. Save as new configuration"); ?>');
	addTooltip($('#printer-checkBtn').linkbutton(),'<?php _e("Send sample report to check printer"); ?>');
	
	function saveConfig() {
		$('#config-Operation').val('saveConfig');
	    var frm = $('#config-form');
	    $.ajax({
	        type: 'GET',
	        url: '/agility/server/adminFunctions.php',
	        data: frm.serialize(),
	        dataType: 'json',
	        success: function (result) {
	            if (result.errorMsg){
	                $.messager.show({ width:300, height:200, title: 'Error', msg: result.errorMsg });
	            } else {
		            $.messager.alert('<?php _e("Save"); ?>',
				            '<?php _e("New configuration successfully saved"); ?>'+" <br />"+
				            '<?php _e("You should close session and reload page"); ?> '+
				            '<?php _e("for changes to become active"); ?>',
				            "info");
					loadConfiguration();
	            }
	        }
	    });
	}	

	function printerCheck() {
		$('#config-Operation').val('printerCheck');
		var frm = $('#config-form');
		$.ajax({
			type: 'GET',
			url: '/agility/server/adminFunctions.php',
			data: frm.serialize(),
			dataType: 'json',
			success: function (result) {
				if (result.errorMsg){
					$.messager.show({ width:300, height:200, title: 'Error', msg: result.errorMsg });
				} else {
					$.messager.alert({
						title: '<?php _e("Check"); ?>',
						msg: '<?php _e("A test page has been sent to event recording printer"); ?>'+" <br />"+
						'<?php _e("Please check for proper output"); ?> '+ "<br /><br/>" +
						'<?php _e("Remember to save changes before exiting"); ?>',
						icon: "info",
						width: 500
					});
					loadConfiguration();
				}
			}
		});
	}

	function defaultConfig() {
		$.messager.confirm('<?php _e("Confirm"); ?>','<?php _e("Restore factory default values. Sure?"); ?>',function(r){
			if (r) {
				$('#config-form').form('load','/agility/server/adminFunctions.php?Operation=defaultConfig');
			}
		});
	}
	
	function loadConfig() {
		$('#config-form').form('load','/agility/server/adminFunctions.php?Operation=loadConfig');
	}
</script>