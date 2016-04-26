/*
 import.js.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

<?php
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>

/*************************************** importacion de perros desde fichero excel **************************/

/**
 * Send command to excel importer
 * @param params list of parameters to be sent to server
 */
function perros_importSendTask(params) {
    var dlg=$('#perros-excel-dialog');
    params.Federation=workingData.federation;
    $.ajax({
        type:'POST', // use post to send file
        url:"/agility/server/excel/dog_reader.php",
        dataType:'json',
        data: params,
        contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
        success: function(res) {
            if (res.errorMsg){
                $.messager.show({ width:300, height:150, title: '<?php _e('Import from Excel error'); ?><br />', msg: res.errorMsg });
                dlg.dialog('close');
            }
            // valid data received fire up client-side import parser
            setTimeout( function() {  perros_importHandleResult(res); },0);
        },
        error: function(XMLHttpRequest,textStatus,errorThrown) {
            $.messager.alert("Import from Excel error","Error: "+textStatus + " "+ errorThrown,'error' );
            dlg.dialog('close');
        }
    });
}

/**
 * Parse response to sendTask
 * @param data received response from server
 * @returns {boolean}
 */
function perros_importHandleResult(data) {
    var dlg=$('#perros-excel-dialog');
    var pb=$('#perros-excel-progressbar');
    if (data.errorMsg) {
        $.messager.show({ width:300, height:150, title: '<?php _e('Import from Excel error'); ?><br />', msg: data.errorMsg });
        dlg.dialog('close');
    }
    switch (data.operation){
        case "upload":
            pb.progressbar('setValue','<?php _e("Checking Excel File");?> : '); // beware ' : ' sequence
            perros_importSendTask({'Operation':'check','Filename':data.filename});
            setTimeout(perros_importSendTask({'Operation':'progress'}),500);
            break;
        case "check":
            pb.progressbar('setValue','<?php _e("Starting data import");?>');
            perros_importSendTask({'Operation':'open'});
            break;
        case "open":
            break;
        case "accept":
            break;
        case "ignore":
            break;
        case "cancel":
            break;
        case "progress": // receive progress status from server
            // iterate until "Done." received
            if (data.status==="Done.") return;
            var val=pb.progressbar('getValue');
            var str=val.substring(0,val.indexOf(' : '));
            pb.progressbar('setValue',str+" : "+data.status);
            setTimeout(perros_importSendTask({'Operation':'progress'}),500);
            break;
        case "close":
            dlg.dialog('close');
            break;
        default:
            $.messager.alert("Excel import error","Invalid operation received from server: "+data.operation );
            dlg.dialog('close');
    }

    return false;
}

/**
 * Llamada al servidor para importar datos de perros
 * desde el fichero excel seleccionado
 */
function perros_excelImport() {
    var data=$('#perros-excelData').val();
    if (data=="") {
        $.messager.alert("<?php _e('Error');?>","<?php _e('No import file selected');?>",'error');
    } else {
        $('#perros-excel-progressbar').progressbar('setValue','Upload');
        return perros_importSendTask({ Operation: 'upload', Data: $('#perros-excelData').val() });
    }
}

// retrieve excel file for imput file button and store into a temporary variable
function read_excelFile(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#perros-excelData').val(e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}
