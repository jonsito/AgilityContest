/*
 import.js.php

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
    'mode' :'perros', // perros, inscripciones, pruebas
    'progress_status': "paused",
    'progress_timeout': 2000, // default is 2 seconds for progress bar monitoring
    'blind': 1, // default blind (non interactive ) import mode
    'word_upercase':1, // on blind mode, uppercase words in DB
    'db_priority':1, // blind mode: on match use database data instead of excel data
    'ignore_spaces':1, // blind mode: blank field are ignored, or used to override DB data
    'parse_coursedata': 1, // on result imports also read (if available) course data
    'ignore_notpresent': 1, // on result imports skip entries that states "No Presentado"
    'suffix': '',
    'prefix': '', // used to avoid duplicate id's in DOM
    'count': 0 // sequence counter
};

var ac_import_table = {
    'perros' :          [ '#perros-excel-dialog','#perros-datagrid' ],
    'inscripciones' :   [ '#inscripciones-excel-dialog','#inscripciones-datagrid' ],
    'entrenamientos' :  [ '#entrenamientos-excel-dialog','#entrenamientos-datagrid' ],
    'resultados' :      [ '#resultadosmanga-excel-dialog','#resultados-datagrid' ] // resultados-datagrid is runtime replaced
};

function import_setProgressStatus(status) {
    switch (status){
        case "running": ac_import.progress_status=status; ac_import.progress_timeout=2000; break;
        case "paused": ac_import.progress_status=status; ac_import.progress_timeout=10000; break;
        case "stopped": ac_import.progress_status=status; ac_import.progress_timeout=2000; break;
        default: console.log("invalid progress status requested: ".status);
    }
}

/*************************************** importacion de datos desde fichero excel **************************/

function searchDataToString(search) {
    var lic=search.Licencia;
    if (lic!=="") lic="Lic:"+lic+" - ";
    return search.Nombre+" - "+lic+ search.Categoria+""+search.Grado+" ( "+search.NombreGuia+" - "+search.NombreClub+" )";
}

/**
 * prepare importClub-dialog to ask for action when club not found
 * @param {object} search data to search for
 * @param {object} found not used; empty object
 * @returns {boolean} true or false according result
 */
function clubNotFound(search) {
    var data=searchDataToString(search);
    var hdr="<p><?php _e('Analyzing Excel Entry')?>: <em>"+data+"</em></p>";

    var msg1="<p><?php _e('Club');?> '";
    var msg2="': <?php _e('not found in database');?> <br/>";
    var msg3=" <?php _e('Please select/edit existing one or create new entry');?></p>";
    var msg=hdr+msg1+search.NombreClub+msg2+msg3;
    $("#importClub-Text").html(msg);
    $("#importClub-ClubID").val(search.ID);
    $("#importClub-dialog").dialog('setTitle',"<?php _e('Entry not found')?>").dialog('open');
    return false;
}
function clubMissmatch(search) {
    var data=searchDataToString(search);
    var hdr="<p><?php _e('Analyzing Excel Entry')?>: <em>"+data+"</em></p>";

    var msg1="<p><?php _e('Club');?> '";
    var msg2="': <?php _e('data missmatch (*) with existing one in database');?> <br/>";
    var msg3=" <?php _e('Please enter right values and accept or create new entry');?></p>";
    var msg=hdr+msg1+search.NombreClub+msg2+msg3;
    $("#importClub-Text").html(msg);
    $("#importClub-ClubID").val(search.ID);
    $("#importClub-dialog").dialog('setTitle',"<?php _e('Data missmatch')?>").dialog('open');
    return false;
}
function clubMustChoose(search) {
    var data=searchDataToString(search);
    var hdr="<p><?php _e('Analyzing Excel Entry')?>: <em>"+data+"</em></p>";

    var msg1="<p><?php _e('Club');?> '";
    var msg2="': <?php _e('provided data are compatible with');?> <br/>";
    var msg3=" <?php _e('more than one existing in database');?> <br/>";
    var msg4=" <?php _e('Please select right one or create new entry');?></p>";
    var msg=hdr+msg1+search.NombreClub+msg2+msg3+msg4;
    $("#importClub-Text").html(msg);
    $("#importClub-ClubID").val(search.ID);
    $("#importClub-dialog").dialog('setTitle',"<?php _e('Must choose')?>").dialog('open');
    return false;
}

function handlerNotFound(search) {
    var data=searchDataToString(search);
    var hdr="<p><?php _e('Analyzing Excel Entry')?>: <em>"+data+"</em></p>";

    var msg1="<p><?php _e('Handler');?> '";
    var msg2="': <?php _e('not found in database');?> <br/>";
    var msg3=" <?php _e('Please select/edit existing one or create new entry');?></p>";
    var msg=hdr+msg1+search.NombreGuia+msg2+msg3;
    $("#importGuia-Text").html(msg);
    $("#importGuia-HandlerID").val(search.ID);
    $("#importGuia-dialog").dialog('setTitle',"<?php _e('Entry not found')?>").dialog('open');
}
function handlerMissmatch(search) {
    var data=searchDataToString(search);
    var hdr="<p><?php _e('Analyzing Excel Entry')?>: <em>"+data+"</em></p>";

    var msg1="<p><?php _e('Handler');?> '";
    var msg2="': <?php _e('data missmatch (*) with existing one in database');?> <br/>";
    var msg3=" <?php _e('Please enter right values and accept or create new entry');?></p>";
    var msg=hdr+msg1+search.NombreGuia+msg2+msg3;
    $("#importGuia-Text").html(msg);
    $("#importGuia-HandlerID").val(search.ID);
    $("#importGuia-dialog").dialog('setTitle',"<?php _e('Data missmatch')?>").dialog('open');
    return false;
}
function handlerMustChoose(search) {
    var data=searchDataToString(search);
    var hdr="<p><?php _e('Analyzing Excel Entry')?>: <em>"+data+"</em></p>";

    var msg1="<p><?php _e('Handler');?> '";
    var msg2="': <?php _e('provided data are compatible with');?> <br/>";
    var msg3=" <?php _e('more than one existing in database');?> <br/>";
    var msg4=" <?php _e('Please select right one or create new entry');?></p>";
    var msg=hdr+msg1+search.NombreGuia+msg2+msg3+msg4;
    $("#importGuia-Text").html(msg);
    $("#importGuia-HandlerID").val(search.ID);
    $("#importGuia-dialog").dialog('setTitle',"<?php _e('Must choose')?>").dialog('open');
    return false;
}

function dogNotFound(search) {
    var data=searchDataToString(search);
    var hdr="<p><?php _e('Analyzing Excel Entry')?>: <em>"+data+"</em></p>";

    var msg1="<p><?php _e('Dog');?> '";
    var msg2="': <?php _e('not found in database');?> <br/>";
    var msg3=" <?php _e('Please select/edit existing one or create new entry');?></p>";
    var msg=hdr+msg1+search.Nombre+msg2+msg3;
    $("#importPerro-Text").html(msg);
    $("#importPerro-DogID").val(search.ID);
    $("#importPerro-dialog").dialog('setTitle',"<?php _e('Entry not found')?>").dialog('open');
}

function dogMissmatch(search) {
    var data=searchDataToString(search);
    var hdr="<p><?php _e('Analyzing Excel Entry')?>: <em>"+data+"</em></p>";

    var msg1="<p><?php _e('Dog');?> '";
    var msg2="': <?php _e('data missmatch (*) with existing one in database');?> <br/>";
    var msg3=" <?php _e('Please enter right values and accept or create new entry');?></p>";
    var msg=hdr+msg1+search.Nombre+msg2+msg3;
    $("#importPerro-Text").html(msg);
    $("#importPerro-DogID").val(search.ID);
    $("#importPerro-dialog").dialog('setTitle',"<?php _e('Data missmatch')?>").dialog('open');
    return false;
}

function dogMustChoose(search) {
    var data=searchDataToString(search);
    var hdr="<p><?php _e('Analyzing Excel Entry')?>: <em>"+data+"</em></p>";

    var msg1="<p><?php _e('Dog');?> '";
    var msg2="': <?php _e('provided data are compatible with');?> ";
    var msg3=" <?php _e('more than one existing in database');?> <br/>";
    var msg4=" <?php _e('Please select right one or create new entry');?></p>";
    var msg=hdr+msg1+search.Nombre+msg2+msg3+msg4;
    $("#importPerro-Text").html(msg);
    $("#importPerro-DogID").val(search.ID);
    $("#importPerro-dialog").dialog('setTitle',"<?php _e('Must choose')?>").dialog('open');
    return false;
}

function resultNotFound(search) {
    var data=searchDataToString(search);
    var hdr="<p><?php _e('Analyzing Excel Entry')?>: <em>"+data+"</em></p>";

    var msg1="<p><?php _e('Entry');?> '";
    var msg2="': <?php _e('Not found in inscriptions for this round');?> <br/>";
    var msg3=" <?php _e('Please select existing one, or ignore entry');?></p>";
    var msg=hdr+msg1+search.Nombre+msg2+msg3;
    $("#importResult-Text").html(msg);
    $("#importResult-Perro").val(search.Perro);
    $("#importResult-dialog").dialog('setTitle',"<?php _e('Entry not found')?>").dialog('open');
}

function resultMissmatch(search) {
    var data=searchDataToString(search);
    var hdr="<p><?php _e('Analyzing Excel Entry')?>: <em>"+data+"</em></p>";

    var msg1="<p><?php _e('Entry');?> '";
    var msg2="': <?php _e('data missmatch with existing one for this round');?> <br/>";
    var msg3=" <?php _e('Please fix it in inscription menu and select right values');?></p>";
    var msg=hdr+msg1+search.Nombre+msg2+msg3;
    $("#importResult-Text").html(msg);
    $("#importResult-Perro").val(search.Perro);
    $("#importResult-dialog").dialog('setTitle',"<?php _e('Data missmatch')?>").dialog('open');
    return false;
}

function resultMustChoose(search) {
    var data=searchDataToString(search);
    var hdr="<p><?php _e('Analyzing Excel Entry')?>: <em>"+data+"</em></p>";

    var msg1="<p><?php _e('Entry');?> '";
    var msg2="': <?php _e('provided data are compatible with');?> ";
    var msg3=" <?php _e('more than one entry found on this round');?> <br/>";
    var msg4=" <?php _e('Please select right one or ask to ignore entry');?></p>";
    var msg=hdr+msg1+search.Nombre+msg2+msg3+msg4;
    $("#importResult-Text").html(msg);
    $("#importResult-Perro").val(search.Perro);
    $("#importResult-dialog").dialog('setTitle',"<?php _e('Must choose')?>").dialog('open');
    return false;
}
/**
 * Send command to excel importer
 * @param params list of parameters to be sent to server
 */
function excel_importSendTask(params) {
    var dlg=$(ac_import_table[ac_import.mode][0]);
    $.ajax({
        type:'POST', // use post to send file
        url:"/agility/server/excel/excelReaderFunctions.php",
        dataType:'json',
        data: {
            Operation    :   params.Operation,
            Filename     :   (typeof(params.Filename)==="undefined")?"":params.Filename,
            Data         :   (typeof(params.Data)==="undefined")?"":params.Data,
            Mode         :   ac_import.mode,
            DatabaseID   :   (typeof(params.DatabaseID)==="undefined")?0:params.DatabaseID,
            ExcelID      :   (typeof(params.ExcelID)==="undefined")?0:params.ExcelID,
            Object       :   (typeof(params.Object)==="undefined")?"":params.Object,
            Prueba       :   workingData.prueba,
            Jornada      :   workingData.jornada,
            Manga        :   workingData.manga,
            Federation   :   workingData.federation,
            Blind        :   ac_import.blind,
            DBPriority   :   ac_import.db_priority,
            WordUpperCase:   ac_import.word_upercase,
            IgnoreWhitespaces:ac_import.ignore_spaces,
            ParseCoursedata: ac_import.parse_coursedata,
            IgnoreNotPresent: ac_import.ignore_notpresent,
            Suffix       :   ac_import.suffix,
            Count        :   ac_import.count
        },
        contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
        success: function(res) {
            if (res.errorMsg){
                $.messager.show({ width:300, height:150, title: '<?php _e('Import from Excel error'); ?><br />', msg: res.errorMsg });
                dlg.dialog('close');
            }
            // valid data received fire up client-side import parser
            excel_importHandleResult(res);
            if (ac_import.count==0) { // start progress monitoring
                setTimeout(function() {excel_importSendTask({'Operation':'progress'})} ,ac_import.progress_timeout);
            }
            ac_import.count++;
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
function excel_importHandleResult(data) {

    var dlg=$(ac_import_table[ac_import.mode][0]);
    var datagrid=ac_import_table[ac_import.mode][1];
    var pb=$('#'+ac_import.prefix+'import-excel-progressbar');
    if (data.errorMsg) {
        $.messager.show({ width:300, height:150, title: '<?php _e('Import from Excel error'); ?><br />', msg: data.errorMsg });
        import_setProgressStatus('stopped'); // tell progress monitor to pause
        dlg.dialog('close');
        return false;
    }
    switch (data.operation){
        case "upload":
            import_setProgressStatus("running");
            pb.progressbar('setValue','<?php _e("Checking Excel File");?> : '); // beware ' : ' sequence
            setTimeout(function() {excel_importSendTask({'Operation':'check','Filename':data.filename})},0);
            break;
        case "check":
            import_setProgressStatus("running");
            pb.progressbar('setValue','<?php _e("Starting data import");?>');
            setTimeout(function() { excel_importSendTask({'Operation':'parse'})},0);
            break;
        case "parse": // analyze next line
            if (data.success=='ok') { // if success==true parse again
                import_setProgressStatus("running");
                setTimeout(function(){excel_importSendTask({'Operation':'parse'})},0);
            }
            if (data.success=='fail') { // user action required. study cases
                var funcs={};
                import_setProgressStatus('paused'); // tell progress monitor to pause progress bar refresh
                if (ac_import.mode==="resultados") {
                    funcs= {'notf':resultNotFound,'miss':resultMissmatch,'multi':resultMustChoose};
                } else if (parseInt(data.search.ClubID)==0) {
                    if (ac_import.blind==1) {
                        var str1='<?php _e("Club/Country not found or missmatch");?>: '+data.search.NombreClub;
                        var str2='<?php _e("This is not allowed when importing in blind mode");?>';
                        var str3='<?php _e("Import aborted.");?>';
                        $.messager.alert({
                            title: '<?php _e("Blind mode import");?>',
                            msg: str1+"<br />"+str2+"<br />"+str3,
                            icon: 'error',
                            width: 480
                        });
                        setTimeout(function(){excel_importSendTask({'Operation':'abort'})},0);
                        break;
                    }
                    funcs= {'notf': clubNotFound,'miss':clubMissmatch,'multi':clubMustChoose};
                } else if (parseInt(data.search.HandlerID)==0) {
                    funcs= {'notf':handlerNotFound,'miss':handlerMissmatch,'multi':handlerMustChoose};
                } else { // arriving here means need to handle with perros
                    funcs= {'notf':dogNotFound,'miss':dogMissmatch,'multi':dogMustChoose};
                }

                var len=data.found.length;
                if (len==0) funcs.notf(data.search);         // item not found: ask user to select existing or create new one
                else if (len==1) funcs.miss(data.search);    // item found, but data missmatch. ask user to fix
                else funcs.multi(data.search);                // several compatible items found. ask user to decide
            }
            if (data.success=='done') { // file parsed: start real import procedure
                import_setProgressStatus("running");
                setTimeout(function() { excel_importSendTask({'Operation':'import'})},0);
            }
            break;
        case "create": // create a new entry with provided data for current line
            // no break;
        case "update": // accept changes to existing entry for current line
            // no break;
        case "ignore": // ignore data from excel file in current line
            // continue parsing and restart progress monitoring
            import_setProgressStatus("running");
            setTimeout(function() { excel_importSendTask({'Operation':'parse'}); },0);
            break;
        case "abort": // cancel transaction
            import_setProgressStatus("running");
            dlg.dialog('close'); // close import dialog
            reloadWithSearch(datagrid,'select',false); // and reload imported datagrid
            break;
        case "import": // import dogs finished.
            import_setProgressStatus("running");
            var op=data.success; // success field tells what to do now : close,teams, inscribe
            setTimeout(function() { excel_importSendTask({'Operation':op}); },0);
            break;
        case "close":
            import_setProgressStatus("stopped");
            $.messager.alert('<?php _e("Import Done");?>','<?php _e("Import from Excel File Done");?>','info');
            dlg.dialog('close'); // close import dialog
            reloadWithSearch(datagrid,'select',false); // and reload imported datagrid
            break;
        case "progress": // receive progress status from server
            // iterate until "Done." received
            if (data.status==="Done.") return; // end of job
            // check for update progress bar and/or continue progress polling
            if (ac_import.progress_status==='running') pb.progressbar('setValue',data.status);
            if (ac_import.progress_status!=='stopped') setTimeout(function() {excel_importSendTask({'Operation':'progress'})},ac_import.progress_timeout);
            break;
        default:
            import_setProgressStatus('stopped');
            $.messager.alert("Excel import error","Invalid operation received from server: "+data.operation );
            dlg.dialog('close');
    }
    return false;
}

/**
 * Respond to user actions when required
 * @param {string} item dialog class 'clubs', 'handlers', 'dogs', 'results'
 * @param {string} action 'create', 'update', 'ignore'
 * @param {number} fromkey  ID of temporary table row being parsed
 * @param {number} dbkey ID of database row to be used in matching, 0 on create/ignore
 */
function importAction(item,action,fromkey,dbkey) {
    var dlg="#import"+item+"-dialog";
    var search="#import"+item+"-Search";
    var key="key"; // original search key from server
    var value=0; // database object ID from choosen item (if any), or zero
    var options = {
      Operation: action, // create update ignore
      Object: item, // Perro Guia Club
      ExcelID: fromkey,
      DatabaseID: dbkey
    };
    if (!dbkey || !dbkey.length) dbkey=0; // prevent null or empty values in dbkey
    if ( (action==="update") && (parseInt(dbkey)==0) ) {
        $.messager.alert("No selection","<?php _e('Must select a valid entry for requesting update');?> "+item );
        return false;
    }
    // tell server to handle this entry, with provided item, action and parameters
    setTimeout(function() { excel_importSendTask(options); },0);
    // close selection window
    $(search).combogrid('clear');
    $(dlg).dialog('close');
    // restart progressbar polling
    import_setProgressStatus("running");
    // return false to dont't propagate event chain
    return false;
}

// retrieve excel file for imput file button and store into a temporary variable
function read_excelFile(input,prefix) {
    ac_import.prefix=prefix;
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#'+ac_import.prefix+'import-excelData').val(e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Llamada al servidor para importar datos de perros
 * desde el fichero excel seleccionado
 * @param{string} mode 'perros', 'inscripciones' 'resultados' 'entrenamientos'
 * @param{string} prefix prefijo de las variables del doom
 */
function real_excelImport(mode,prefix) {
    var data=$('#'+prefix+'import-excelData').val();
    ac_import.mode=mode;
    ac_import.prefix=prefix;
    // checkboxes
    ac_import.blind=$('#'+prefix+'import-excelBlindMode').prop('checked')?1:0;
    ac_import.ignore_notpresent=$('#'+prefix+'import-excelIgnoreNotPresent').prop('checked')?1:0;
    ac_import.parse_coursedata=$('#'+prefix+'import-excelParseCourseData').prop('checked')?1:0;
    // radiobuttons: use checked value
    ac_import.db_priority=$('input[name='+prefix+'excelPreference]:checked').val();
    ac_import.word_upercase=$('input[name='+prefix+'excelUpperCase]:checked').val();
    ac_import.ignore_spaces=$('input[name='+prefix+'excelEmpty]:checked').val();
    // prepare randon string for report notifier
    ac_import.suffix=getRandomString(8);
    ac_import.count=0;
    if (data=="") {
        $.messager.alert("<?php _e('Error');?>","<?php _e('No import file selected');?>",'error');
        return;
    }
    $('#'+prefix+'import-excel-progressbar').progressbar('setValue','Upload');
    import_setProgressStatus("running");
    excel_importSendTask({ Operation: 'upload', Data: data});
}

// to avoid duplicate ids in doom , some import dialogs needs add a prefix to provided variables
function perros_excelImport() { return real_excelImport('perros',''); }
function inscripciones_excelImport() { return real_excelImport('inscripciones',''); }
function entrenamientos_excelImport() { return real_excelImport('entrenamientos','entrenamientos-'); }
function resultadosmanga_excelImport() { return real_excelImport('resultados',''); }
