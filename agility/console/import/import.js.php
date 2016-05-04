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
require_once(__DIR__ . "/../../server/auth/Config.php");
require_once(__DIR__ . "/../../server/tools.php");
$config =Config::getInstance();
?>

/*
variables used to store import status
 */
var ac_import = {
    'progress_status': "running"
};

/*************************************** importacion de perros desde fichero excel **************************/

/**
 * prepare importclubes-dialog to ask for action when club not found
 * @param {object} search data to search for
 * @param {object} found not used; empty object
 * @returns {boolean} true or false according result
 */
function clubNotFound(search,found) {
    var msg1="<?php _e('Club');?> '";
    var msg2="' <?php _e('not found in database');?> <br/>";
    var msg3=" <?php _e('Please select/edit existing one or create new entry');?>";
    var msg=msg1+search.NombreClub+msg2+msg3;
    $("#importclubes_header-Text").html(msg);
    $("#importclubes-dialog").dialog('setTitle',"<?php _e('Entry not found')?>").dialog('open');
    return false;
}
function clubMissmatch(search,found) {
    var msg1="<?php _e('Club');?> '";
    var msg2="' <?php _e('data missmatch (*) with existing one in database');?> <br/>";
    var msg3=" <?php _e('Please enter right values and accept or create new entry');?>";
    var msg=msg1+search.NombreClub+msg2+msg3;
    $("#importclubes_header-Text").html(msg);
    $("#importclubes-dialog").dialog('setTitle',"<?php _e('Data missmatch')?>").dialog('open');
    return false;
}
function clubMustChoose(search,found) {
    var msg1="<?php _e('Club');?> '";
    var msg2="' <?php _e('provided data are compatible with');?> <br/>";
    var msg3=" <?php _e('more than one existing in database');?> <br/>";
    var msg4=" <?php _e('Please select right one or create new entry');?>";
    var msg=msg1+search.NombreClub+msg2+msg3+msg4;
    $("#importclubes_header-Text").html(msg);
    $("#importclubes-dialog").dialog('setTitle',"<?php _e('Must choose')?>").dialog('open');
    return false;
}

function handlerNotFound(search,found) {
    var msg1="<?php _e('Handler');?> '";
    var msg2="' <?php _e('not found in database');?> <br/>";
    var msg3=" <?php _e('Please select/edit existing one or create new entry');?>";
    var msg=msg1+search.NombreGuia+msg2+msg3;
    $("#importhandlers_header-Text").html(msg);
    $("#importhandlers-dialog").dialog('setTitle',"<?php _e('Entry not found')?>").dialog('open');
}
function handlerMissmatch(search,found) {
    var msg1="<?php _e('Handler');?> '";
    var msg2="' <?php _e('data missmatch (*) with existing one in database');?> <br/>";
    var msg3=" <?php _e('Please enter right values and accept or create new entry');?>";
    var msg=msg1+search.NombreClub+msg2+msg3;
    $("#importhandlers_header-Text").html(msg);
    $("#importhandlers-dialog").dialog('setTitle',"<?php _e('Data missmatch')?>").dialog('open');
    return false;
}
function handlerMustChoose(search,found) {
    var msg1="<?php _e('Handler');?> '";
    var msg2="' <?php _e('provided data are compatible with');?> <br/>";
    var msg3=" <?php _e('more than one existing in database');?> <br/>";
    var msg4=" <?php _e('Please select right one or create new entry');?>";
    var msg=msg1+search.NombreClub+msg2+msg3+msg4;
    $("#importhandlers_header-Text").html(msg);
    $("#importhandlers-dialog").dialog('setTitle',"<?php _e('Must choose')?>").dialog('open');
    return false;
}

function dogNotFound(search,found) {
    var msg1="<?php _e('Dog');?> '";
    var msg2="' <?php _e('not found in database');?> <br/>";
    var msg3=" <?php _e('Please select/edit existing one or create new entry');?>";
    var msg=msg1+search.Nombre+msg2+msg3;
    $("#importdogs_header-Text").html(msg);
    $("#importdogs-dialog").dialog('setTitle',"<?php _e('Entry not found')?>").dialog('open');
}
function dogMissmatch(search,found) {
    var msg1="<?php _e('Dog');?> '";
    var msg2="' <?php _e('data missmatch (*) with existing one in database');?> <br/>";
    var msg3=" <?php _e('Please enter right values and accept or create new entry');?>";
    var msg=msg1+search.NombreClub+msg2+msg3;
    $("#importdogs_header-Text").html(msg);
    $("#importdogs-dialog").dialog('setTitle',"<?php _e('Data missmatch')?>").dialog('open');
    return false;
}

function dogMustChoose(search,found) {
    var msg1="<?php _e('Dog');?> '";
    var msg2="' <?php _e('provided data are compatible with');?> ";
    var msg3=" <?php _e('more than one existing in database');?> <br/>";
    var msg4=" <?php _e('Please select right one or create new entry');?>";
    var msg=msg1+search.NombreClub+msg2+msg3+msg4;
    $("#importdogs_header-Text").html(msg);
    $("#importdogs-dialog").dialog('setTitle',"<?php _e('Must choose')?>").dialog('open');
    return false;
}

/**
 * Send command to excel importer
 * @param params list of parameters to be sent to server
 */
function perros_importSendTask(params) {
    var dlg=$('#perros-excel-dialog');
    params.Federation=workingData.federation;
    if (params.Operation!='progress') console.log("send: "+params.Operation);
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
 *
 * This code acts as a state machine, sending and receive messages from server
 * @param data received response from server
 * @returns {boolean} false on fail; otherwise true
 */
function perros_importHandleResult(data) {
    var dlg=$('#perros-excel-dialog');
    var pb=$('#perros-excel-progressbar');
    if (data.errorMsg) {
        $.messager.show({ width:300, height:150, title: '<?php _e('Import from Excel error'); ?><br />', msg: data.errorMsg });
        dlg.dialog('close');
    }
    if (data.operation!='progress') console.log("recv: "+data.operation);
    switch (data.operation){
        case "upload":
            pb.progressbar('setValue','<?php _e("Checking Excel File");?> : '); // beware ' : ' sequence
            perros_importSendTask({'Operation':'check','Filename':data.filename});
            ac_import.progress_status="running";
            setTimeout(perros_importSendTask({'Operation':'progress'}),1000);
            break;
        case "check":
            pb.progressbar('setValue','<?php _e("Starting data import");?>');
            perros_importSendTask({'Operation':'parse'});
            break;
        case "parse": // analyze next line
            if (data.success=='ok') { // if success==true parse again
                perros_importSendTask({'Operation':'parse'});
            }
            if (data.success=='fail') { // user action required. study cases
                var funcs={};
                ac_import.progress_status='paused'; // tell progress monitor to pause
                if (parseInt(data.search.ClubID)==0) funcs= {'notf': clubNotFound,'miss':clubMissmatch,'multi':clubMustChoose};
                else if (parseInt(data.search.HandlerID)==0) funcs= {'notf':handlerNotFound,'miss':handlerMissmatch,'multi':handlerMustChoose};
                else funcs= {'notf':dogNotFound,'miss':dogMissmatch,'multi':dogMustChoose};

                var len=data.found.length;
                if (len==0) funcs.notf(data.search,data.success);         // item not found: ask user to select existing or create new one
                else if (len==1) funcs.miss(data.search,data.success);    // item found, but data missmatch. ask user to fix
                else funcs.multi(data.search,data.success);                // several compatible items found. ask user to decide
            }
            if (data.success=='done') { // file parsed: start real import procedure
                perros_importSendTask({'Operation':'import'});
            }
            break;
        case "create": // create a new entry with provided data for current line
            // no break;
        case "update": // accept changes to existing entry for current line
            // no break;
        case "ignore": // ignore data from excel file in current line
            // continue parsing
            setTimeout(function() { perros_importSendTask({'Operation':'parse'}); },0);
            // re-start progress monitoring
            ac_import.progress_status="running";
            setTimeout(function() { perros_importSendTask({'Operation':'progress'}); },1000);
            break;
        case "abort": // cancel transaction
            break;
        case "import": // import finished. Tell server to cleanup
            perros_importSendTask({'Operation':'close'});
            break;
        case "close":
            ac_import.progress_status="paused";
            dlg.dialog('close');
            break;
        case "progress": // receive progress status from server
            // iterate until "Done." received
            if (data.status==="Done.") return;
            var val=pb.progressbar('getValue');
            var str=val.substring(0,val.indexOf(' : '));
            pb.progressbar('setValue',str+" : "+data.status);
            if (ac_import.progress_status==='running') setTimeout(perros_importSendTask({'Operation':'progress'}),1000);
            break;
        default:
            $.messager.alert("Excel import error","Invalid operation received from server: "+data.operation );
            dlg.dialog('close');
    }
    return false;
}

/**
 * Respond to user actions when required
 * @param {string} item dialog class 'clubs', 'handlers', 'dogs'
 * @param {string} action 'create', 'update', 'ignore'
 */
function importAction(item,action) {

    // close dialog
    var label="#import"+item+"-dialog";
    $(label).dialog('close');

    // ask server to perform proper action
    // to be revisited
    $.ajax({
        type:'POST', // use post to send file
        url:"/agility/server/excel/dog_reader.php",
        dataType:'json',
        data: {
            Item: item,
            Operation: action,
            Federation: workingData.federation
            // add received and parsed data
        },
        success: function(res) {
            setTimeout (function() {perros_importHandleResult(res);},0);
        },
        error: function(XMLHttpRequest,textStatus,errorThrown) {
        }
    });
    // return false to dont't propagate event chain
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
