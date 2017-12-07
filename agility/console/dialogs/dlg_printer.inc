<!--
dlg_printer.inc

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

<div id="printer_dialog-dialog" style="padding:10px;">
    <h4><?php _e('Select document type to be generated'); ?>:</h4>
    <p>
        <span id="<printer_dialog-currentcat"></span>
    </p>
    <form id="printer_dialog-options" name="printer-options">
        <input type="hidden" name="printer_dialog-cats" id="printer_dialog-cats" value=""/>
        <input type="radio" name="printer_dialog-option" id="printer_dialog-option0" value="0" onclick="print_changeSelection(0)"/>
        <label for="printer_dialog-option0"><?php _e('Activities and timetable on this journey'); ?></label><br/>
        <input type="radio" name="printer_dialog-option" id="printer_dialog-option1" value="1" onclick="print_changeSelection(1)"/>
        <label for="printer_dialog-option1"><?php _e('Starting order on this round'); ?></label><br/>
        <input type="radio" name="printer_dialog-option" id="printer_dialog-option2" value="2" onclick="print_changeSelection(2)"/>
        <label for="printer_dialog-option2"><?php _e('Starting order on this round'); ?> (<?php _e("Excel format");?>)</label><br/><br/>
        <input type="radio" name="printer_dialog-option" id="printer_dialog-option3" value="3" onclick="print_changeSelection(3)"/>
        <label for="printer_dialog-option3"><?php _e('SheetCalc to evaluate SCT and MCT'); ?></label><br/>
        <input type="radio" name="printer_dialog-option" id="printer_dialog-option4" value="4" onclick="print_changeSelection(4)"/>
        <label for="printer_dialog-option4"><?php _e('Sheet to anotate data on rounds'); ?></label><br/>
        <input type="radio" name="printer_dialog-option" id="printer_dialog-option5" value="5" onclick="print_changeSelection(5)"/>
        <label for="printer_dialog-option5"><?php _e('Judge assistant empty sheet'); ?></label><br/><br/>
        <input type="radio" name="printer_dialog-option" id="printer_dialog-option6" value="6" onclick="print_changeSelection(6)"/>
        <label for="printer_dialog-option6"><?php _e('Judge assistant sheets (1 dog/page)'); ?></label><br/>
        <input type="radio" name="printer_dialog-option" id="printer_dialog-option7" value="7" onclick="print_changeSelection(7)"/>
        <label for="printer_dialog-option7"><?php _e('Judge assistant sheets (5 dogs/page)'); ?></label><br/>
        <input type="radio" name="printer_dialog-option" id="printer_dialog-option10" value="10" onclick="print_changeSelection(10)"/>
        <label for="printer_dialog-option8"><?php _e('Judge assistant sheets (10 dogs/page)'); ?></label><br/>
        <input type="radio" name="printer_dialog-option" id="printer_dialog-option8" value="8" onclick="print_changeSelection(8)"/>
        <label for="printer_dialog-option8"><?php _e('Judge assistant sheets (15 dogs/page)'); ?></label><br/>
        <span id="printer_dialog-team4" >
            <input type="radio" name="printer_dialog-option" id="printer_dialog-option9" value="9" onclick="print_changeSelection(9)"/>
            <label for="printer_dialog-option9"><?php _e('Judge assistant sheets (combined for team 4)'); ?></label><br/><br/>
        </span>
        <span id="printer_dialog-ko" >
            <input type="radio" name="printer_dialog-option" id="printer_dialog-option11" value="11" onclick="print_changeSelection(11)"/>
            <label for="printer_dialog-option11"><?php _e('Judge assistant sheets (KO rounds)'); ?></label><br/><br/>
        </span>
        <span id="printer_dialog-games" >
            <input type="radio" name="printer_dialog-option" id="printer_dialog-option12" value="12" onclick="print_changeSelection(12)"/>
            <label for="printer_dialog-option12"><?php _e('Judge assistant sheets (Games)'); ?></label><br/>
            <br/>
        </span>
        <p>
            <label for="printer_dialog-comments"><?php _e('PDF Header comment');?> </label>
            <input type="text" id="printer_dialog-comments" value=""/><br/>
            <label for="printer_dialog-range"><?php _e('First and last dog/team to print');?> </label>
            <input type="text" id="printer_dialog-range" name="Range" value="1-99999"/>
        </p>
    </form>
</div>

<div id="printer_dialog-buttons">
    <a id="printer_dialog-okBtn" href="#" class="easyui-linkbutton"
       data-options="iconCls:'icon-print'" onclick="print_performCommonDesarollo()" ><?php _e('Print'); ?></a>
    <a id="printer_dialog-cancelBtn" href="#" class="easyui-linkbutton"
       data-options="iconCls:'icon-cancel'" onclick="$('#printer_dialog-dialog').dialog('close')" ><?php _e('Cancel'); ?></a>
</div>

<script type="text/javascript">

    // set up textboxes
    $('#printer_dialog-range').textbox({
        width:100,
        required:false,
        validType:'intrange' // created by me at easyui-patches
    });
    // set up textboxes
    $('#printer_dialog-comments').textbox({
        width:200,
        required:false,
        validType:'length[0,128]'
    });

    $('#printer_dialog-dialog').dialog({
        width:450,
        height:'auto',
        iconCls:'icon-print',
        title:'<?php _e("Print form"); ?>',
        closed:true,
        closable:true,
        buttons:'#printer_dialog-buttons',
        modal:true
    });

    addTooltip($('#printer_dialog-okBtn').linkbutton(),'<?php _e("Generate selected document"); ?>');
    addTooltip($('#printer_dialog-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
</script>