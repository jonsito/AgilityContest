<!-- 
dlg_configuracion.php

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

		<div title="<?php _e('Contest');?>" style="padding:10px">
			<h3><?php _e('Contest parameters'); ?></h3>

			<label for="proximity_alert"><?php _e('Proximity alert on starting order'); ?></label>
			<input type="number" min="1" max="20" size="2" value="5" name="proximity_alert" id="proximity_alert"/> <?php _e('Dogs'); ?><br />

			<label><?php _e('Time data precision shown'); ?></label>
			<label for="crono_cents" style="width:130px;margin-top:5px;">
				<input type="radio" id="crono_cents" name="crono_milliseconds" value="0" checked="checked" /><?php _e('cents'); ?>
			</label>
			<label for="crono_milliseconds" style="width:130px;margin-top:5px;">
				<input type="radio" id="crono_milliseconds" name="crono_milliseconds" value="1"/><?php _e('millis'); ?>
			</label><br />

            <h3><?php _e('Team competitions behaviour'); ?></h3>
            <label><?php _e('What to do when eliminated in Team-4'); ?>:</label>
            <select name="team4_mode" class="easyui-combobox" data-options="panelHeight:'auto'">
                <option value="0" selected="selected"><?php _e('100 penalty points and crono continues');?> </option>
                <option value="1"><?php _e('100 points and set time to MCT (EO mode)');?> </option>
                <option value="2"><?php _e('Whole team is eliminated');?> </option>
            </select>
            <br />

            <h3><?php _e('Games competitions behaviour'); ?></h3>
            <span style="display:inline-block;width:300px"><?php _e('Points for Gambler sequences'); ?></span>
            <input type="number" min="1" max="20" size="2" value="10" name="gambler_seq1" id="gambler_seq1" style="width:30px"/>
            <label for="gambler_seq1" style="width:100px"><?php _e('Sequence');?> 1</label>
            <input type="number" min="1" max="20" size="2" value="20" name="gambler_seq2" id="gambler_seq2" style="width:30px"/>
            <label for="gambler_seq2" style="width:100px"><?php _e('Sequence');?> 2</label>
            <br/>
            <table>
                <tr><td>&nbsp;</td></tr>
                <tr>
                    <td rowspan="2" style="width:300px;"><?php _e('Gambler additional points'); ?></td>
                    <td style="width:55px;">&nbsp;
                        <img src="/agility/images/getNumberedBall.php?Number=A&Color=0F0&Background=888" alt="green" width="30"/>
                    </td>
                    <td style="width:55px;">&nbsp;
                        <img src="/agility/images/getNumberedBall.php?Number=B&Color=841&Background=FFF" alt="brown" width="30"/>
                    </td>
                    <td style="width:55px;">&nbsp;
                        <img src="/agility/images/getNumberedBall.php?Number=C&Color=00F&Background=888" alt="blue" width="30"/>
                    </td>
                    <td style="width:55px;">&nbsp;
                        <img src="/agility/images/getNumberedBall.php?Number=D&Color=F19&Background=FFF" alt="pink" width="30"/>
                    </td>
                    <td style="width:55px;">&nbsp;
                        <img src="/agility/images/getNumberedBall.php?Number=E&Color=000&Background=888" alt="black" width="30"/>
                    </td>
                    <td style="width:60px;text-align:right;" rowspan="2"><?php _e('Bonus points');?></td>
                    <td style="width:55px;">&nbsp;
                        <img src="/agility/images/getNumberedBall.php?Number=X&Color=F00&Background=FFF" alt="red" width="30"/>
                    </td>
                </tr>
                <tr>
                    <td style="width:55px;"><input type="number" min="1" max="10" size="2" value="3" name="gambler_bonus1" id="gambler_bonus1" style="width:30px"/></td>
                    <td style="width:55px;"><input type="number" min="1" max="10" size="2" value="3" name="gambler_bonus2" id="gambler_bonus2" style="width:30px"/></td>
                    <td style="width:55px;"><input type="number" min="1" max="10" size="2" value="3" name="gambler_bonus3" id="gambler_bonus3" style="width:30px"/></td>
                    <td style="width:55px;"><input type="number" min="1" max="10" size="2" value="3" name="gambler_bonus4" id="gambler_bonus4" style="width:30px"/></td>
                    <td style="width:55px;"><input type="number" min="1" max="10" size="2" value="3" name="gambler_bonus5" id="gambler_bonus5" style="width:30px"/></td>
                    <!-- rowspan comes here -->
                    <td style="width:55px;"><input type="number" min="1" max="10" size="2" value="3" name="gambler_extra" id="gambler_extra" style="width:30px"/></td>
                </tr>
            </table>
            <br/>

            <h3><?php _e('Training session parameters'); ?></h3>
			<label><?php _e('Club/Country Training time assignment mode'); ?></label>
			<select name="training_type" class="easyui-combobox" data-options="panelHeight:'auto'">
				<option value="1" selected="selected"><?php _e('Individual time * Max number of dogs/category');?> </option>
				<option value="0"><?php _e('Individual time * Number of dogs on each County or Club');?> </option>
			</select>

			<label for=training_time"><?php _e('Training time for each dog');?> </label>
			<input type="number" min="60" max="300" size="2" value="180" step="15" style="width:50px" name="training_time" id="training_time"/> <?php _e('Seconds'); ?><br />

			<label for=training_grace"><?php _e('Training grace period to enter/leave ring');?> </label>
			<input type="number" min="5" max="60" size="2" value="15" step="5" style="width:50px" name="training_grace" id="training_grace"/> <?php _e('Seconds'); ?><br />
		</div>

		<div title="<?php _e('Desktop'); ?>" style="padding:10px">
			<h3><?php _e('Desktop look and feel'); ?></h3>
			<label for="config_theme"><?php _e('Dekstop theme'); ?></label>
			<select id="config_theme" name="easyui_theme" class="easyui-combobox" data-options="panelHeight:'auto'">
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

			<label for="easyui_rowcolor1"><?php _e('Color for even table rows'); ?></label>
			<input id="easyui_rowcolor1" name="easyui_rowcolor1" value="#e0e0e0" type="color"/>
			<label class="space" for="easyui_rowcolor2"><?php _e('Color for odd table rows'); ?></label>
			<input id="easyui_rowcolor2" name="easyui_rowcolor2" value="#c0c0c0" type="color"/><br />

			<label for="easyui_rowcolor3"><?php _e('Color for even secondary table rows'); ?></label>
			<input id="easyui_rowcolor3" name="easyui_rowcolor3" value="#c0c0f0" type="color"/>
			<label class="space" for="easyui_rowcolor4"><?php _e('Color for odd secondary table rows'); ?></label>
			<input id="easyui_rowcolor4" name="easyui_rowcolor4" value="#c0c0f0" type="color"/><br />
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

			<label for="tablet_dblckick"><?php _e('Action at double click in round list'); ?></label>
			<select id="tablet_dblclick" name="tablet_dblckick" class="easyui-combobox" data-options="panelHeight:'auto'">
				<option value="0"><?php _e('Cancel and select');?></option>
				<option value="1" selected="selected"><?php _e('Save and select');?></option>
			</select><br />

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
			<input id="crs1" type="radio" name="crono_resync" value="0"  checked="checked"/><?php _e('Start electronic chrono'); ?>
			<input id="crs2" type="radio" name="crono_resync" value="1"/><?php _e('Continue with manual chrono'); ?>
		</div>
		
		<div title="<?php _e('VideoWall'); ?>" style="padding:10px">
			<h3><?php _e('Video wall configurable parameters'); ?></h3>
			<label for="vw_polltime"><?php _e('AutoScroll period (seg.)'); ?></label>
			<input id="vw_polltime" name="vw_polltime" type="text" size="2" value="5" class="easyui-textbox"/><br />

			<label for="vw_hdrbg1"><?php _e('Background color for main header'); ?></label>
			<input id="vw_hdrbg1" name="vw_hdrbg1" type="color" value="#0000ff"/>
			<label class="space" for="vw_hdrfg1"><?php _e('Text color for main header'); ?></label>
			<input id="vw_hdrfg1" name="vw_hdrfg1" type="color" value="#ffffff"/><br />

			<label for="vw_hdrbg2"><?php _e('Background color for secondary header'); ?></label>
			<input id="vw_hdrbg2" name="vw_hdrbg2" type="color" value="#c0c0c0"/>
			<label class="space" for="vw_hdrfg2"><?php _e('Text color for secondary header'); ?></label>
			<input id="vw_hdrfg2" name="vw_hdrfg2" type="color" value="#ffffff"/><br />

            <label for="vw_hdrbg3"><?php _e('Background color for tertiary header'); ?></label>
            <input id="vw_hdrbg3" name="vw_hdrbg3" type="color" value="#c0c0c0"/>
            <label class="space" for="vw_hdrfg3"><?php _e('Text color for tertiary header'); ?></label>
            <input id="vw_hdrfg3" name="vw_hdrfg3" type="color" value="#000000"/><br />

			<label for="vw_rowcolor1"><?php _e('Background color for even table rows'); ?></label>
			<input id="vw_rowcolor1" name="vw_rowcolor1" type="color" value="#e0e0e0"/>
			<label class="space" for="vw_rowcolor2"><?php _e('Background color for odd table rows'); ?></label>
			<input id="vw_rowcolor2" name="vw_rowcolor2" type="color" value="#c0c0c0"/><br />

			<label for="vw_rowcolor3"><?php _e('Color for even secondary table rows'); ?></label>
			<input id="vw_rowcolor3" name="vw_rowcolor3" type="color" value="#e0e0e0"/>
			<label class="space" for="vw_rowcolor4"><?php _e('Color for odd secondary table rows'); ?></label>
			<input id="vw_rowcolor4" name="vw_rowcolor4" type="color" value="#c0c0c0"/><br />
			
			<h3><?php _e('Alternate (simplified) Video wall configurable parameters'); ?></h3>
			
			<label for="vws_polltime"><?php _e('Sponsors rotation period (seg.)'); ?></label>
			<input id="vws_polltime" name="vws_polltime" type="text" size="2" value="5" class="easyui-textbox"/><br />

			<label for="vws_uselogo"><?php _e('Use special logo instead of standard header');?></label>
			<input type="hidden" name="vws_uselogo" value="0"/> <!-- trick for checkboxes -->
			<input type="checkbox" name="vws_uselogo" id="vws_uselogo" value="1"/><br />
			<label for="vws_logourl"><?php _e('URL for logo image to be used');?></label>
			<input type="text" id="vws_logourl" name="vws_logourl" size="50" maxlength="90" class="easyui-textbox"/><br />

			<label for="vws_uselogo"><?php _e('Enable animated images into screen');?></label>
			<input type="hidden" name="vws_animation" value="0"/> <!-- trick for checkboxes -->
			<input type="checkbox" name="vws_animation" id="vws_animation" value="1"/><br />

			<label for="vws_font"><?php _e('Font family for text in cells'); ?></label>
			<select id="vws_font" name="vws_font" class="easyui-combobox" data-options="panelHeight:'auto'">
				<option value="Helvetica, monospace"><?php _e('Default system font'); ?></option>
				<option value="futura_condensedbold">Futura Condensed Bold</option>
				<option value="roadgeek_2005_engschriftRg">RoadGeek 2005 Engschrift</option>
				<option value="roadgeek_2005_series_b">RoadGeek 2005 Series B</option>
				<option value="Helvetica Neue LT Std 67 Medium Condensed">Helvetica Neue LT Std 67 Medium Condensed</option>
				<option value="Helvetica Neue LT Std 67 Bold Condensed">Helvetica Neue LT Std 67 Bold Condensed</option>
				<option value="Stint Ultra Condensed Regular">Stint Ultra Condensed Regular</option>
				<option value="Steelfish Regular" selected="selected">Steelfish Regular</option>
			</select>
			<label for="vws_fontsize"><?php _e('Default font size (vw units) '); ?></label>
			<input id="vws_fontsize" name="vws_fontsize" type="number" size="2" min="0.5" max="5.0" step="0.1" value="1.8"/>
			<br />
			
			<label for="vws_hdrbg1"><?php _e('Background color for main header'); ?></label>
			<input id="vws_hdrbg1" name="vws_hdrbg1" type="color" value="#0000ff"/>
			<label class="space" for="vws_hdrfg1"><?php _e('Text color for main header'); ?></label>
			<input id="vws_hdrfg1" name="vws_hdrfg1" type="color" value="#ffffff"/><br />

			<label for="vws_hdrbg2"><?php _e('Background color for table header'); ?></label>
			<input id="vws_hdrbg2" name="vws_hdrbg2" type="color" value="#c0c0c0"/>
			<label class="space" for="vws_hdrfg2"><?php _e('Text color table header'); ?></label>
			<input id="vws_hdrfg2" name="vws_hdrfg2" type="color" value="#ffffff"/><br />
			
			<label for="vws_linecolor"><?php _e('Text and lines color for tables'); ?></label>
			<input id="vws_linecolor" name="vws_linecolor" type="color" value="#0000ff"/><br />
			
			<label for="vws_rowcolor1"><?php _e('Call To ring background color (even rows)'); ?></label>
			<input id="vws_rowcolor1" name="vws_rowcolor1" type="color" value="#e0e0e0"/>
			<label class="space" for="vws_rowcolor2"><?php _e('Call to ring background color (odd rows)'); ?></label>
			<input id="vws_rowcolor2" name="vws_rowcolor2" type="color" value="#c0c0c0"/><br />

			<label for="vws_rowcolor3"><?php _e('Results background color (even rows)'); ?></label>
			<input id="vws_rowcolor3" name="vws_rowcolor3" type="color" value="#e0e0e0"/>
			<label class="space" for="vws_rowcolor4"><?php _e('Results background color (odd rows)'); ?></label>
			<input id="vws_rowcolor4" name="vws_rowcolor4" type="color" value="#c0c0c0"/><br />
			
			<label for="vws_rowcolor5"><?php _e('Current Team background color (even rows)'); ?></label>
			<input id="vws_rowcolor5" name="vws_rowcolor5" type="color" value="#c0c0c0"/>
			<label class="space" for="vws_rowcolor6"><?php _e('Current Team background color (odd rows)'); ?></label>
			<input id="vws_rowcolor6" name="vws_rowcolor6" type="color" value="#000000"/><br />
		</div>

		<div title="<?php _e('Live Stream'); ?>" style="padding:10px">
			<h3><?php _e('Live Stream configurable parameters'); ?></h3>

            <label for="ls_hdrbg1"><?php _e('Background color for main header'); ?></label>
            <input id="ls_hdrbg1" name="ls_hdrbg1" type="color" value="#0000ff"/>
            <label class="space" for="ls_hdrfg1"><?php _e('Text color for main header'); ?></label>
            <input id="ls_hdrfg1" name="ls_hdrfg1" type="color" value="#ffffff"/><br />

            <label for="ls_hdrbg2"><?php _e('Background color for secondary header'); ?></label>
            <input id="ls_hdrbg2" name="ls_hdrbg2" type="color" value="#c0c0c0"/>
            <label class="space" for="ls_hdrfg2"><?php _e('Text color for secondary header'); ?></label>
            <input id="ls_hdrfg2" name="ls_hdrfg2" type="color" value="#ffffff"/><br />

            <label for="ls_hdrbg3"><?php _e('Background color for tertiary header'); ?></label>
            <input id="ls_hdrbg3" name="ls_hdrbg3" type="color" value="#c0c0c0"/>
            <label class="space" for="ls_hdrfg3"><?php _e('Text color for tertiary header'); ?></label>
            <input id="ls_hdrfg3" name="ls_hdrfg3" type="color" value="#000000"/><br />

            <label for="ls_rowcolor1"><?php _e('Background color for even table rows'); ?></label>
            <input id="ls_rowcolor1" name="ls_rowcolor1" type="color" value="#e0e0e0"/>
            <label class="space" for="ls_rowcolor2"><?php _e('Background color for odd table rows'); ?></label>
            <input id="ls_rowcolor2" name="ls_rowcolor2" type="color" value="#c0c0c0"/><br />

            <label for="ls_rowcolor3"><?php _e('Color for even secondary table rows'); ?></label>
            <input id="ls_rowcolor3" name="ls_rowcolor3" type="color" value="#e0e0e0"/>
            <label class="space" for="ls_rowcolor4"><?php _e('Color for odd secondary table rows'); ?></label>
            <input id="ls_rowcolor4" name="ls_rowcolor4" type="color" value="#c0c0c0"/><br />

			<label for="ls_tobefirst"><?php _e('Evaluate and show time to get first place'); ?></label>
			<input type="hidden" name="ls_tobefirst" value="0"/>
			<input id="ls_tobefirst" name="ls_tobefirst" type="checkbox" value="1"/><br />

			<label for="ls_alpha"><?php _e('Transparency level for OnScreenDisplay'); ?></label>
			<input id="ls_alpha" name="ls_alpha" type="text" size="3" value="0.5" class="easyui-textbox"/> (0.0-1.0)<br />

			<label for="ls_evtdelay"><?php _e('Delay for LiveStream event parsing'); ?></label>
			<input id="ls_evtdelay" name="ls_evtdelay" type="number" style="width:40px;" value="1.0" min="0" max="5" step="0.1"/> (<?php _e('sec');?>)<br />

			<label for="ls_chromakey"><?php _e('Color for Chroma Keyer'); ?></label>
			<input id="ls_chromakey" name="ls_chromakey" type="color" value="#00ff00"/><br />

			<label for="ls_dataposition"><?php _e('Timming data screen location'); ?></label>
			<select id="ls_dataposition" name="ls_dataposition" class="easyui-combobox" data-options="panelHeight:'auto'">
				<option value="0"><?php _e('Not visible');?></option>
				<option value="1" selected="selected"><?php _e('Upper right corner');?></option>
				<option value="2"><?php _e('Lower right corner');?></option>
                <option value="3"><?php _e('Next to competitor info');?></option>
                <option value="4"><?php _e('User defined layout');?></option>
			</select><br />

			<label for="ls_infoposition"><?php _e('Round data screen location'); ?></label>
			<select id="ls_infoposition" name="ls_infoposition" class="easyui-combobox" data-options="panelHeight:'auto'">
				<option value="0"><?php _e('Not visible');?></option>
				<option value="1" selected="selected"><?php _e('Screen top');?></option>
                <option value="2"><?php _e('On top of competitor info');?></option>
                <option value="3"><?php _e('User defined layout');?></option>
			</select><br />
		</div>
		
		<div title="<?php _e('Reports'); ?>" style="padding:10px">
			<h3><?php _e('PDF forms and reports generation'); ?></h3>
			<label for="pdf_fontfamily"><?php _e('Font family'); ?></label>
			<select id="pdf_fontfamily" name="pdf_fontfamily" class="easyui-combobox" data-options="panelHeight:'auto'">
				<option value="DejaVu" selected="selected">DejaVu (UTF-8)</option>
				<option value="Free">GNU Free (UTF-8)</option>
				<option value="Courier">Courier (iso-latin1)</option>
				<option value="Helvetica">Helvetica (iso-latin1)</option>
				<option value="Times">Times (iso-latin1)</option>
			</select><br />
			<label for="pdf_journeys"><?php _e('Show journey info when printing catalog'); ?></label>
			<input type="hidden" name="pdf_journeys" value="0"/>
			<input id="pdf_journeys" name="pdf_journeys" type="checkbox" value="1"/><br />

			<label for="pdf_grades"><?php _e('Show level/grade info when printing catalog'); ?></label>
			<input type="hidden" name="pdf_grades" value="0"/>
			<input id="pdf_grades" name="pdf_grades" type="checkbox" value="1"/><br />

			<label for="pdf_hdrbg1"><?php _e('Background color for main header'); ?></label>
			<input id="pdf_hdrbg1" name="pdf_hdrbg1" type="color" value="#ff8020"/>
			<label class="space" for="pdf_hdrfg1"><?php _e('Text color on main header'); ?></label>
			<input id="pdf_hdrfg1" name="pdf_hdrfg1" type="color" value="#000000"/><br />

			<label for="pdf_hdrbg2"><?php _e('Background color for secondary header'); ?></label>
			<input id="pdf_hdrbg2" name="pdf_hdrbg2" type="color" value="#c0c0c0"/>
			<label class="space" for="pdf_hdrfg2"><?php _e('Text color for secondary header'); ?></label>
			<input id="pdf_hdrfg2" name="pdf_hdrfg2" type="color" value="#ffffff"/><br />

			<label for="pdf_rowcolor1"><?php _e('Background color for tables even rows'); ?></label>
			<input id="pdf_rowcolor1" name="pdf_rowcolor1" type="color" value="#ffffff"/>
			<label class="space" for="pdf_rowcolor2"><?php _e('Background color for tables odd rows'); ?></label>
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
            <label for="pdf_skipnpel"><?php _e('Skip labels for Not Present / Eliminated dogs'); ?></label>
            <input type="hidden" name="pdf_skipnpel" value="0"/>
            <input id="pdf_skipnpel" name="pdf_skipnpel" type="checkbox" value="1"/><br />

			<label for="pdf_topmargin"><?php _e('Label sheet upper margin (mmts)'); ?></label>
			<input id="pdf_topmargin" name="pdf_topmargin" type="text" size="4" value="10.0" class="easyui-textbox"/><br />
			<label for="pdf_leftmargin"><?php _e('Label sheet right margin (mmts)'); ?></label>
			<input id="pdf_leftmargin" name="pdf_leftmargin" type="text" size="4" value="10.0" class="easyui-textbox"/><br />
		</div>
		
		<div title="<?php _e('Internet'); ?>" style="padding:10px">
		    <h3><?php _e('Public access from internet'); ?></h3>
			<label for="web_refreshtime"><?php _e('Data auto-refresh interval'); ?></label>
			<input type="hidden" name="web_refreshtime" value="0"/> <!-- trick for select with value 0 -->
			<select id="web_refreshtime" name="web_refreshtime" class="easyui-combobox" data-options="panelHeight:'auto'">
				<option value="0" selected="selected"><?php _e('No refresh'); ?></option>
				<option value="30">30 sec.</option>
				<option value="45">45 sec.</option>
				<option value="60">1 min.</option>
				<option value="90">1,5 min.</option>
				<option value="120">2 min.</option>
			</select><br />
			<label for="pb_rotatelogos"><?php _e('Rotation period for sponsor logos'); ?></label>
			<input id="pb_rotatelogos" name="pb_rotatelogos" type="number" size="1" maxlength="1" value="5" min="1" max="9"/> (<?php _e('sec');?>)<br />

			<label for="pb_hdrbg1"><?php _e('Header background color'); ?></label>
			<input id="pb_hdrbg1" name="pb_hdrbg1" type="color" value="#0000ff"/>
			<label class="space" for="pb_hdrfg1"><?php _e('Header text color'); ?></label>
			<input id="pb_hdrfg1" name="pb_hdrfg1" type="color" value="#ffffff"/><br />

			<label for="pb_hdrbg2"><?php _e('Round data table background color'); ?></label>
			<input id="pb_hdrbg2" name="pb_hdrbg2" type="color" value="#0000ff"/>
			<label class="space" for="pb_hdrfg2"><?php _e('Round data table text color'); ?></label>
			<input id="pb_hdrfg2" name="pb_hdrfg2" type="color" value="#ffffff"/><br />

			<label for="pb_rowcolor1"><?php _e('Background color for even table rows'); ?></label>
			<input id="pb_rowcolor1" name="pb_rowcolor1" type="color" value="#e0e0e0"/>
			<label class="space" for="pb_rowcolor2"><?php _e('Background color for odd table rows'); ?></label>
			<input id="pb_rowcolor2" name="pb_rowcolor2" type="color" value="#c0c0c0"/><br />

			<label for="pb_rowcolor3"><?php _e('Color for even secondary table rows'); ?></label>
			<input id="pb_rowcolor3" name="pb_rowcolor3" type="color" value="#e0e0e0"/>
			<label class="space" for="pb_rowcolor4"><?php _e('Color for odd secondary table rows'); ?></label>
			<input id="pb_rowcolor4" name="pb_rowcolor4" type="color" value="#c0c0c0"/><br />

            <label for="pb_rowcolor5"><?php _e('Color for dog with selected dorsal'); ?></label>
            <input id="pb_rowcolor5" name="pb_rowcolor5" type="color" value="#e0d0e0"/><br />
		</div>

		<div title="<?php _e('E-mail'); ?>" style="padding:10px">
            <h3><?php _e('Options for sending electronic mail related functions'); ?></h3>
            <label for="email_address"><?php _e('Email address'); ?> (<?php _e('From license info'); ?>)</label>
            <input id="email_address" name="email_address" type="text" readonly="readonly" disabled="disabled" size="25" value="agilityContest@agilitycontest.es" class="easyui-textbox"/>
            <br/>
            <label for="email_server"><?php _e('SMTP Server'); ?></label>
            <input id="email_server" name="email_server" type="text" size="20" value="" class="easyui-textbox"/>
            <br/>
            <label for="email_port"><?php _e('Port'); ?></label>
            <select id="email_port" name="email_port" class="easyui-combobox" data-options="panelHeight:'auto'">
                <option value="25">25 - SMTP</option>
                <option value="465">465 - SMTP on SSL</option>
                <option value="587">587 - Mail Submission Agent</option>
            </select>
            <br/>
            <label for="email_crypt"><?php _e("Encryption method");?></label>
            <select id="email_crypt" name="email_crypt" class="easyui-combobox" data-options="panelHeight:'auto'">
                <option value="NONE"><?php _e("None");?></option>
                <option value="STARTTLS"><?php _e("StartTLS after connection");?></option>
                <option value="TLS"><?php _e("SSL/TLS on dedicated port");?></option>
            </select>
            <br/>
            <label for="email_auth"><?php _e('Authentication method'); ?></label>
            <input id="email_auth" name="email_auth" value="PLAIN"/> <!-- redefined bellow -->
            <br/>
            <label for="email_user"><?php _e('User'); ?></label>
            <input id="email_user" name="email_user" type="text" size="20" value="" class="easyui-textbox"/>
            <br/>
            <label for="email_pass"><?php _e('Password'); ?></label>
            <input id="email_pass" name="email_pass" size="20" type="password" value="" class="easyui-textbox"/>
            <br/>
            <label for="email_realm"><?php _e('NTLM auth realm'); ?></label>
            <input id="email_realm" name="email_realm" type="text" size="20" value="" class="easyui-textbox"/>
            <br/>
            <label for="email_workstation"><?php _e('NTLM auth workstation'); ?></label>
            <input id="email_workstation" name="email_workstation" size="20" type="password" value="" class="easyui-textbox"/>
            <br/>&nbsp;<br/>
            <a id="email-checkBtn" href="#" class="easyui-linkbutton"
               data-options="iconCls:'icon-mail'" onclick="emailCheck();"><?php _e('Check'); ?></a>
            <br/>
        </div>
		<div title="<?php _e('General'); ?>" style="padding:10px">
			<h3><?php _e('General configuration'); ?></h3>
			<label for="config_lang"><?php _e('Default language'); ?></label>
			<input id="config_lang" name="lang" /><br />
			<label for="accept_lang"><?php _e('Choose language from browser preferences'); ?></label>
			<input type="hidden" name="accept_lang" value="0"/> <!-- trick for checkboxes -->
			<input type="checkbox" name="accept_lang" id="accept_lang" value="1"/><br />
			<label for="debug_level"><?php _e('Debug level'); ?></label>
			<select id="debug_level" name="debug_level" class="easyui-combobox" data-options="panelHeight:'auto'">
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
			<input id="event_printer" name="event_printer" type="text" class="easyui-textbox" size="10" value=""/> <?php _e('Paper size'); ?>:
			<label for="wide_printer58" style="width:70px;">
				<input type="radio" id="wide_printer58" name="wide_printer" value="0" checked="checked"/> 58mm
			</label>
			<label for="wide_printer80" style="width:70px;">
				<input type="radio" id="wide_printer80" name="wide_printer" value="1"/>	80mm
			</label>
            <a id="printer-checkBtn" href="#" class="easyui-linkbutton"
               data-options="iconCls:'icon-print'" onclick="printerCheck();"><?php _e('Check'); ?></a>
			<br />

            <label for="reset_events"><?php _e('Clear event registration on tablet init session'); ?></label>
            <input type="hidden" name="reset_events" value="0"/> <!-- trick for checkboxes -->
            <input type="checkbox" name="reset_events" id="reset_events" value="1"/><br />

            <label for="full_backup"><?php _e('Include event history in backup'); ?></label>
            <input type="hidden" name="full_backup" value="0"/> <!-- trick for checkboxes -->
            <input type="checkbox" name="full_backup" id="full_backup" value="1"/><br />

            <label for="console_events"><?php _e('Track login events in console'); ?></label>
            <input type="hidden" name="console_events" value="0"/> <!-- trick for checkboxes -->
            <input type="checkbox" name="console_events" id="console_events" value="1"/><br />

            <label for="search_updates"><?php _e('Search for updates at startup'); ?></label>
            <input type="hidden" name="search_updates" value="0"/> <!-- trick for checkboxes -->
            <input type="checkbox" name="search_updates" id="search_updates" value="1"/><?php _e('Application'); ?>
            <span style="display:inline-block;width:50px">&nbsp;</span>
            <input type="hidden" name="search_updatedb" value="0"/> <!-- trick for checkboxes -->
            <input type="checkbox" name="search_updatedb" id="search_updatedb" value="1" onChange="warn_updatedb(this);"/><?php _e('Database'); ?><br />

            <h3><?php _e('Auto backup options'); ?></h3>

            <label for="backup_disabled"><?php _e('Disable auto-backup'); ?> </label>
            <input type="hidden" name="backup_disabled" value="0"/> <!-- trick for checkboxes -->
            <span  style="width:90px;display:inline-block;">
                <input type="checkbox" name="backup_disabled" id="backup_disabled" value="1" style="float:left"/>
            </span>
            <?php _e('Dangerous! Use at your own risk');?><br />

            <label for="backup_dogs"><?php _e('Perform database backup after '); ?></label>
            <input type="number" min="0" max="50" step="5" id="backup_dogs" name="backup_dogs" style="width:75px;"/> <?php _e('results entered');?><br/>

            <label for="backup_period"><?php _e('Perform database backup every'); ?></label>
            <input  type="number" min="0" max="50" step="2"id="backup_period" name="backup_period" style="width:75px;"/> <?php _e('minutes');?><br/>

            <label for="backup_dir"><?php _e('Folder to store user-defined backup'); ?></label>
            <input id="backup_dir" name="backup_dir" type="text" size="20" value="" class="easyui-textbox"/>
                (<?php _e('must exist in server');?>)
            <a id="backup-checkBtn" href="#" class="easyui-linkbutton"
               data-options="iconCls:'icon-db_backup'" onclick="backupCheck();"><?php _e('Check'); ?></a>
            <br/>

		</div>

		<div title="<?php _e('System'); ?>" style="padding:10px">
			<h3><?php _e('Additional server information'); ?></h3>
			<label for="ip_address"><?php _e('IP address(es)'); ?></label>
			<input id="ip_address" name="ip_address" type="text" class="easyui-textbox" readonly="readonly" size="45"
				   value="<?php $a=new networkInterfaces(); echo implode(", ",$a->get_interfaces());?>" />
			<br />
			<h3><?php _e('Save and Restore configuration'); ?></h3>
			<label for="config_backup"><?php _e('Download configuration to file'); ?></label>
			<input type="button" class="icon_button icon-db_backup" id="config_backup" name="config_backup" value="<?php _e('Backup');?>" onclick="backupConfig();">
			<br/>
			<label for="config_restore"><?php _e('Restore configuration from file'); ?></label>
			<input type="button" class="icon_button icon-db_restore" id="config_restore" name="config_restore" value="<?php _e('Restore');?>" onclick="restoreConfig();">
			<input type="file" class="icon_button icon-search" id="config_restoreFile"
				   name="restoreFile" accept=".ini,text/plain" required="required" style="width:365px;" value="<?php _e('Search');?>" onchange="loadConfigFile(this)">
			<input id="config_restoreData" type="hidden" name="config_restoreData" value="">
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

    // retrieve email sender address from registration info.
    $('#email_address').val(ac_regInfo.Email); // uneditable

	$('#config_lang').combobox({
        panelHeight:'auto',
        url:'/agility/server/adminFunctions.php?Operation=getAvailableLanguages',
        valueField:'ID',
        textField:'Name'
    });

	addTooltip($('#event_printer').linkbutton(),'<?php _e("Enter printer name/device or leave empty to disable"); ?>');
	addTooltip($('#config-resetBtn').linkbutton(),'<?php _e("Cancel changes. Restore information from server"); ?>');
	addTooltip($('#config-defaultBtn').linkbutton(),'<?php _e("Factory reset. Configure with default data"); ?>');
	addTooltip($('#config-okBtn').linkbutton(),'<?php _e("Accept changes. Save as new configuration"); ?>');
    addTooltip($('#printer-checkBtn').linkbutton(),'<?php _e("Send sample report to check printer"); ?>');
    addTooltip($('#email-checkBtn').linkbutton(),'<?php _e("Send yourself a sample mail to check parameters"); ?>');
    addTooltip($('#backup-checkBtn').linkbutton(),'<?php _e("Test backup into user provided directory"); ?>');

    // enable/disable NTLM auth boxes according auth type method
    $('#email_auth').combobox({
        panelHeight:'auto',
        valueField: 'val',
        textField: 'text',
        data:[{
            val:'PLAIN',text:"<?php _e('None');?>"
        },{
            val:'LOGIN',text:"<?php _e('User / Password');?>"
        },{
            val:'NTLM',text:"<?php _e('NTLM');?>"
        },{
            val:'CRAM-MD5',text:"<?php _e('CRAM-MD5');?>"
        }],
        onChange: function( newval, oldval) {
            $('#email_user').textbox((newval=="PLAIN")?'disable':'enable');
            $('#email_pass').textbox((newval=="PLAIN")?'disable':'enable');
            $('#email_realm').textbox((newval=="NTLM")?'enable':'disable');
            $('#email_workstation').textbox((newval=="NTLM")?'enable':'disable');
        }
    });

	function saveConfig() {
		$('#config-Operation').val('saveConfig');
	    $.ajax({
	        type: 'GET',
	        url: '/agility/server/adminFunctions.php',
	        data: $('#config-form').serialize(),
	        dataType: 'json',
	        success: function (result) {
	            if (result.errorMsg){
	                $.messager.show({ width:300, height:200, title: 'Error', msg: result.errorMsg });
	            } else {
		            $.messager.alert('<?php _e("Save"); ?>',
				            '<?php _e("New configuration successfully saved"); ?>'+" <br/> &nbsp;<br/> "+
				            '<?php _e("You should close session and reload page"); ?> '+
				            '<?php _e("for changes to become active"); ?>',
				            "info").window('resize',{width:400});
					loadConfiguration(function(config) { $('#config-form').form('load',config);	});
	            }
	        },
            error: function(XMLHttpRequest,textStatus,errorThrown) {
                $.messager.alert("Save configuration to file error","Error: "+textStatus + " "+ errorThrown,'error' );
            }
	    });
	}

    function emailCheck() {
        // check for registered application
        if (ac_regInfo.Serial==="00000000") {
            $.messager.alert('<?php _e("Mail services"); ?>',
                '<p><?php _e("Electronic mail operations<br/>are not allowed for unregistered licenses"); ?></p>',
                "info").window('resize',{width:480});
            return false;
        }
        // call to server
        $('#config-Operation').val('check');
        $.ajax({
            type: 'GET',
            url: '/agility/server/mailFunctions.php',
            data: $('#config-form').serialize(),
            dataType: 'json',
            success: function (result) {
                if (result.errorMsg){
                    $.messager.show({ width:300, height:200, title: 'Error', msg: result.errorMsg });
                } else {
                    $.messager.alert({
                        title: '<?php _e("Mail Check"); ?>',
                        msg:   '<?php _e("Mail has been successfully sent to address: "); ?><br/>'+$('#email_address').val()+"' <br />"+
                               '<?php _e("Please check email inbox for received mail"); ?> ',
                        icon:  "info",
                        width: 400
                    });
                }
            },
            error: function(XMLHttpRequest,textStatus,errorThrown) {
                $.messager.alert("Mailer check error","Error: "+textStatus + " "+ errorThrown,'error' );
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
					// loadConfiguration(function(config) { $('#config-form').form('load',config);	});
				}
			}
		});
	}

	/**
	 * perform a configuration factory reset
	 */
	function defaultConfig() {
		$.messager.confirm('<?php _e("Confirm"); ?>','<?php _e("Restore factory default values. Sure?"); ?>',function(r){
			if (r) {
				$('#config-form').form('load','/agility/server/adminFunctions.php?Operation=defaultConfig');
				$.messager.alert('<?php _e("Factory reset"); ?>',
					'<?php _e("Restored default configuration options"); ?>'+" <br />"+
					'<?php _e("You should close session and reload page"); ?> '+
					'<?php _e("for changes to become active"); ?>',
					"info",
					function() {loadConfiguration(function(config) { $('#config-form').form('load',config);	});});
			}
		});
	}

	/**
	 * discard config changes and restore browser configuration from server config
	 */
	function loadConfig() {
		loadConfiguration(function(config) { $('#config-form').form('load',config);	});
	}

	/**
	 * Download a backup of configuration file
	 */
	function backupConfig() {
		$.fileDownload(
			'/agility/server/adminFunctions.php',
			{
				httpMethod: 'GET',
				data: {
					Operation: 'backupConfig'
				},
				preparingMessageHtml:'<?php _e("Downloading configuration, please wait"); ?> ...',
				failMessageHtml:'<?php _e("There was a problem in configuration download. Please try again"); ?>'
			}
		);
		return false; //this is critical to stop the click event which will trigger a normal file download!
	}

	/**
	 * Upload and store configuration file
	 */
	function restoreConfig() {
		var data=$('#config_restoreData').val();
		if (data==="") {
			$.messager.alert("<?php _e('Error');?>","<?php _e('No config file selected');?>",'error');
			return false;
		}
		$.ajax({
			type:'POST', // use post to send file
			url:"/agility/server/adminFunctions.php",
			dataType:'json',
			data: { Operation: 'restoreConfig', Data: data },
			contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
			success: function(res) {
				if (res.errorMsg){
					$.messager.show({ width:300, height:150, title: '<?php _e('Restore configuration from file error'); ?><br />', msg: res.errorMsg });
				}
				$.messager.alert({
					title: '<?php _e("Restore"); ?>',
					msg: '<?php _e("Configuration file successfully restored"); ?> <br />'+
					'<?php _e("You should close session and reload page"); ?> <br/>' +
					'<?php _e("for changes to become active"); ?><br/><hr/><br/>'+res.data,
					icon: "info",
					width: 400
				});
				loadConfiguration(function(config) { $('#config-form').form('load',config);	});
			},
			error: function(XMLHttpRequest,textStatus,errorThrown) {
				$.messager.alert("Restore configuration from file error","Error: "+textStatus + " "+ errorThrown,'error' );
			}
		});
	}

	/**
	 * Load configuration file from client and store in browser
	 * @param input
	 */
	function loadConfigFile(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#config_restoreData').val(e.target.result);
			};
			reader.readAsDataURL(input.files[0]);
		}
	}

	function warn_updatedb(box) {
        if (! box.checked) return;
        $.messager.confirm("Legal",
            '<?php _e('Please read and accept legal <a target="lopd" href="http://www.agilitycontest.es/lopd.html">terms and conditions</a> on data sharing');?>',
            function(r){
                if (r) return true;
                // user cancelled operation: clear checkbox
                 box.checked=false;
            }
        );
    }

</script>