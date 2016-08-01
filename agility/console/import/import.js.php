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
    'progress_status': "running",
    'blind': 1, // default blind (non interactive ) import mode
    'word_upercase':1, // on blind mode, uppercase words in DB
    'db_priority':1, // blind mode: on match use database data instead of excel data
    'ignore_spaces':1 // blind mode: blank field are ignored, or used to override DB data
};

/*************************************** importacion de perros desde fichero excel **************************/

/**
 * Show/hide Blind options according checkbox status
 */
function import_showHideBlind() {
    ac_import.blind=$('#perros-excelBlindMode').prop('checked')?1:0;
    ac_import.db_priority=$('input[name=excelPreference]:checked').val();
    ac_import.word_upercase=$('input[name=excelUpperCase]:checked').val();
    ac_import.ignore_spaces=$('input[name=excelEmpty]:checked').val();
    $("#perros-excelBlindOptions").css("display",(ac_import.blind!=0)?"inherit":"none");
}

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
    params.Federation   =   workingData.federation;
    params.Blind        =   ac_import.blind;
    params.DBPriority   =   ac_import.db_priority;
    params.WordUpperCase=   ac_import.word_upercase;
    params.IgnoreWhitespaces=ac_import.ignore_spaces;
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
            setTimeout( function() {  perros_importHandleResult(res); },100);
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
    // if (data.operation!='progress') console.log("recv: "+data.operation);
    switch (data.operation){
        case "upload":
            pb.progressbar('setValue','<?php _e("Checking Excel File");?> : '); // beware ' : ' sequence
            setTimeout(function() {perros_importSendTask({'Operation':'check','Filename':data.filename})},0);
            ac_import.progress_status="running";
            setTimeout(function() {perros_importSendTask({'Operation':'progress'})} ,0); // start progress monitoring
            break;
        case "check":
            pb.progressbar('setValue','<?php _e("Starting data import");?>');
            setTimeout(function() { perros_importSendTask({'Operation':'parse'})},0);
            break;
        case "parse": // analyze next line
            if (data.success=='ok') { // if success==true parse again
                ac_import.progress_status='running'; // tell progress monitor to pause
                setTimeout(function(){perros_importSendTask({'Operation':'parse'})},0);
            }
            if (data.success=='fail') { // user action required. study cases
                var funcs={};
                ac_import.progress_status='paused'; // tell progress monitor to pause
                if (ac_import.blind=1) {
                    var str1='<?php _e("Club/Country not found or missmatch");?>: '+data.search.NombreClub;
                    var str2='<?php _e("This is not allowed when importing in blind mode");?>';
                    var str3='<?php _e("Import aborted.");?>';
                    $.messager.alert({
                        title: '<?php _e("Blind mode import");?>',
                        msg: str1+"<br />"+str2+"<br />"+str3,
                        icon: 'error',
                        width: 480
                    });
                    setTimeout(function(){perros_importSendTask({'Operation':'abort'})},0);
                    break;
                }
                if (parseInt(data.search.ClubID)==0) {
                    funcs= {'notf': clubNotFound,'miss':clubMissmatch,'multi':clubMustChoose};
                } else if (parseInt(data.search.HandlerID)==0) {
                    funcs= {'notf':handlerNotFound,'miss':handlerMissmatch,'multi':handlerMustChoose};
                } else {
                    funcs= {'notf':dogNotFound,'miss':dogMissmatch,'multi':dogMustChoose};
                }

                var len=data.found.length;
                if (len==0) funcs.notf(data.search,data.success);         // item not found: ask user to select existing or create new one
                else if (len==1) funcs.miss(data.search,data.success);    // item found, but data missmatch. ask user to fix
                else funcs.multi(data.search,data.success);                // several compatible items found. ask user to decide
            }
            if (data.success=='done') { // file parsed: start real import procedure
                setTimeout(function() { perros_importSendTask({'Operation':'import'})},0);
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
            break;
        case "abort": // cancel transaction
            dlg.dialog('close'); // close import dialog
            reloadWithSearch('#perros-datagrid','select',false); // and reload dogs datagrid
            break;
        case "import": // import finished. Tell server to cleanup
            setTimeout(function() { perros_importSendTask({'Operation':'close'}); },0);
            break;
        case "close":
            ac_import.progress_status="paused";
            $.messager.alert('<?php _e("Import Done");?>','<?php _e("Import from Excel File Done");?>','info');
            dlg.dialog('close'); // close import dialog
            reloadWithSearch('#perros-datagrid','select',false); // and reload dogs datagrid
            break;
        case "progress": // receive progress status from server
            // iterate until "Done." received
            if (data.status==="") return; // just created import progress file
            if (data.status==="Done.") return; // end of job
            var val=pb.progressbar('getValue');
            pb.progressbar('setValue',data.status);
            if (ac_import.progress_status==='running')
                setTimeout(perros_importSendTask({'Operation':'progress'}),1000);
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
    ac_import.blind=$('#perros-excelBlindMode').prop('checked')?1:0;
    ac_import.db_priority=$('input[name=excelPreference]:checked').val();
    ac_import.word_upercase=$('input[name=excelUpperCase]:checked').val();
    ac_import.ignore_spaces=$('input[name=excelEmpty]:checked').val();
    if (data=="") {
        $.messager.alert("<?php _e('Error');?>","<?php _e('No import file selected');?>",'error');
        return;
    }
    if (ac_import.blind==0) {
        $.messager.alert(
            "<?php _e('Error');?>",
            "<?php _e('Interactive import is not yet available');?>. "+"<?php _e('Sorry')?>",
            'error');
        return;
    }
    $('#perros-excel-progressbar').progressbar('setValue','Upload');
    return perros_importSendTask({ Operation: 'upload', Data: data});
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
