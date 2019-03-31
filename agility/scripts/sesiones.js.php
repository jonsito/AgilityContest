/*
 sesiones.js.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

//***** gestion de sesiones	*********************************************************

/**
 * Open "New Session dialog"
 *@param {string} dg datagrid ID de donde se obtiene la sesion
 *@param {string} def default value to insert into Nombre 
 *@param {function} onAccept what to do when a new Session is created
 */
function newSession(dg,def,onAccept){
	$('#sesiones-dialog').dialog('open').dialog('setTitle','<?php _e('New session'); ?>'); // open dialog
	$('#sesiones-form').form('clear');// clear old data (if any)
	if (strpos(def,"<?php _e('-- Search --'); ?>")===false) $('#sesiones-Nombre').textbox('setValue',def);// fill session Name
	$('#sesiones-Operation').val('insert');// set up operation
	$('#sesiones-Operador').val(1);// set default user id for new session
	$('#sesiones-Login').val('-- Sin asignar --');// set up default user name for new session
	$('#sesiones-Logout').linkbutton('disable'); // no sense to logout default user
	if (onAccept!==undefined) $('#sesiones-okBtn').one('click',onAccept);
}

/**
 * Open "Edit Session" dialog
 * @param {string} dg datagrid ID de donde se obtiene la sesion
 */
function editSession(dg){
	if ($('#sesiones-datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("Edit Error"); ?>','<?php _e("There is no session selected"); ?>',"warning");
    	return; // no way to know which dog is selected
    }
    if (row.ID<=1) {
        $.messager.alert('<?php _e("Edit Error"); ?>','<?php _e("This entry cannot be modified"); ?>',"error");
        return; // cannot delete default session
    }
    // set up operation properly
    row.Operation='update';
    // open dialog
    $('#sesiones-dialog').dialog('open').dialog('setTitle','<?php _e('Modify session data'); ?>');
    // and fill form with row data
    $('#sesiones-form').form('load',row);
	$('#sesiones-Logout').linkbutton('enable'); // let us logout user from session
}

/**
 * Call json to Ask for commit new/edit session to server
 */
function saveSession(){
    var frm = $('#sesiones-form');
    if (!frm.form('validate')) return; // don't call inside ajax to avoid override beforeSend()

    // disable button in ajax call to avoid recall twice
    $('#sesiones-okBtn').linkbutton('disable');
    $.ajax({
        type: 'GET',
        url: '../ajax/database/sessionFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){
                $.messager.show({ width:300, height:200, title: '<?php _e('Error'); ?>', msg: result.errorMsg });
            } else {
                $('#sesiones-dialog').dialog('close');        // close the dialog
                $('#sesiones-datagrid').datagrid('reload');    // reload the session data
            }
        },
        error: function(XMLHttpRequest,textStatus,errorThrown) {
            $.messager.alert("Save Sesion","Error:"+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus+" - "+errorThrown,'error' );
        }
    }).then(function(){
        $('#sesiones-okBtn').linkbutton('enable');
    });
}

/**
 * Delete session data in bbdd
 * @param {string} dg datagrid ID de donde se obtiene el session
 */
function deleteSession(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("Delete error"); ?>','<?php _e("There is no session selected"); ?>',"warning");
    	return; // no way to know which session is selected
    }
    if (row.ID<=2) {
    	$.messager.alert('<?php _e("Delete error"); ?>','<?php _e("This entry cannot be deleted"); ?>',"error");
    	return; // cannot delete default session
    }
    $.messager.confirm('<?php _e('Confirm'); ?>','<?php _e('Delete session'); ?>'+':'+row.Nombre+'\n '+'<?php _e('Sure?'); ?>',function(r){
      	if (!r) return;
        $.get('../ajax/database/sessionFunctions.php',{ Operation: 'delete', ID: row.ID },function(result){
            if (result.success){
                $(dg).datagrid('unselectAll').datagrid('reload');    // reload the session data
            } else {
            	// show error message
                $.messager.show({width:300,height:200,title: 'Error',msg: result.errorMsg});
            }
        },'json');
    });
}

function session_logout() {
	$('#sesiones-Operador').val(1);// set default user id for new session
	$('#sesiones-Login').val('-- Sin asignar --');// set up default user name for new session
}

function resetSession(dg) {
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("Delete error"); ?>','<?php _e("There is no session selected"); ?>',"warning");
    	return; // no way to know which session is selected
    }
    $.messager.confirm('<?php _e('Confirm'); ?>','<?php _e('Delete event history on session'); ?>'+':'+row.Nombre+'\n '+'<?php _e('Sure?'); ?>',function(r){
      	if (!r) return;
        $.get('../ajax/database/sessionFunctions.php',{ Operation: 'reset', ID: row.ID },function(result){
            if (result.success){
                $(dg).datagrid('reload');    // reload the session data
            } else {
            	// show error message
                $.messager.show({width:300,height:200,title:'<?php _e( 'Error'); ?>',msg: result.errorMsg});
            }
        },'json');
    });
}

/************************ funciones de manejo de control remoto de sesiones ********************/

function reloadRemoteClientList() {
    $('#remote-tablet-datagrid').datagrid('unselectAll').datagrid('load');
    $('#remote-videowall-datagrid').datagrid('unselectAll').datagrid('load');
    $('#remote-livestream-datagrid').datagrid('unselectAll').datagrid('load');
    $('#remote-chronometer-datagrid').datagrid('unselectAll').datagrid('load');
    // no list available for internet displays :-)
}

// retrieve ring name from val:session_id
function formatRingName(val,row,index) {
    var data=$('#sesiones-datagrid').datagrid('getData')['rows'];
    for (var n=0; n<data.length;n++) if (data[n]['ID']==val) return data[n]['Nombre'];
    return "<?php _e('Ring session id');?>: "+val;
}

// retrieve view mode for Videowall val:mode
function formatLiveStreamView(val,row,index) {
    switch (parseInt(val)) {
        case 0: return '<?php _e("Starting order"); ?>';
        case 1: return '<?php _e("Live Stream"); ?>';
        case 2: return '<?php _e("Partial scores"); ?>';
        case 3: return '<?php _e("Final scores"); ?>';
        case 4: return '<?php _e("Training session"); ?>';
    }
    // default: ( should not occurs ) return session id as string
    return "<?php _e('View mode');?>: "+val;
}

// retrieve View mode from livestream display val:mode
function formatVideowallView(val,row,index) {
    switch (parseInt(val)) {
        case 0: return "<?php _e('Starting order'); ?>";
        case 1: return "<?php _e('Training session'); ?>";
        case 2: return "<?php _e('Partial scores'); ?>";
        case 3: return "<?php _e('Partial Scores'); ?> (<?php _e('simplified'); ?>)";
        case 4: return "<?php _e('Final scores'); ?>";
        case 5: return "<?php _e('Advertising videos'); ?>";
        case 6: return "<?php _e('Training session'); ?> (<?php _e('simplified'); ?>)";
        case 7: return "<?php _e('Call to ring '); ?> / <?php _e('Partial scores'); ?>";
        case 8: return "<?php _e('Call to ring '); ?> / <?php _e('Final scores'); ?>";
        case 9: return "<?php _e('Final Scores'); ?> (<?php _e('simplified'); ?>)";
        case 10:return "<?php _e('Standby screen'); ?>";
    }
    // default: ( should not occurs ) return session id as string
    return "<?php _e('View mode');?>: "+val;
}

function remoteAllorNone(val, dg,all,none) {
    if (val==1) { dg.datagrid('selectAll'); none.prop('checked',false); }
    if (val==0) { dg.datagrid('unselectAll'); all.prop('checked',false); }
}

function remoteTabletAllorNone(val) {
    remoteAllorNone( val, $('#remote-tablet-datagrid') , $('#remote-tablet-all') ,$('#remote-tablet-none') );
}
function remoteVideowallAllorNone(val) {
    remoteAllorNone( val, $('#remote-videowall-datagrid') , $('#remote-videowall-all') ,$('#remote-videowall-none') );
}
function remoteLivestreamAllorNone(val) {
    remoteAllorNone( val, $('#remote-livestream-datagrid') , $('#remote-livestream-all') ,$('#remote-livestream-none') );
}
function remoteChronometerAllorNone(val) {
    remoteAllorNone( val, $('#remote-chronometer-datagrid') , $('#remote-chronometer-all') ,$('#remote-chronometer-none') );
}

// when a single row is selected in remote control window, clone status to change dialog
function remoteSetOnSingleSelection(name) {
    var rows=$(name+'-datagrid').datagrid('getSelections');
    if (rows.length!==1) return; // no single selection
    $(name+'-ring').combogrid('setValue',rows[0].Session);
    $(name+'-view').combobox('setValue',rows[0].View);
    $(name+'-mode').val(rows[0].Mode);
}

/**
 * send events
 * @param {object} data Event data
 */
function remote_putEvent(data){
    // setup default elements for this event
    var obj= {
        // global objects
        ID:         0, // not used, just for enumeration
        Operation:  'putEvent',
        Type:       'command',
        TimeStamp:  Math.floor(Date.now() / 1000),
        // event inner parameters
        Source:     ac_clientOpts.Source,
        Destination: '', /* not specified, use name or session */
        Session:    data.Session,
        Prueba:     (typeof data.Prueba==="undefined")?0:data.Prueba,
        Jornada:    (typeof data.Jornada==="undefined")?0:data.Jornada,
        Manga:      (typeof data.Manga==="undefined")?0:data.Manga,
        Tanda:      (typeof data.Tanda==="undefined")?0:data.Tanda,
        // not used in "command" event
        Perro:	    0,
        Dorsal:	    0,
        Equipo:	    0,
        Celo:		0,
        // command event parameters. will be overriden with 'data contents
        Name:       '', // display name
        SessionName: '', // full path event name source:sessid:view:mode:name
        Oper:       0, // operation to be requested for 'command' event
        Value:      0   // csv parameter list
    };
    // send "update" event to every session listeners
    $.ajax({
        type:'GET',
        url:"../ajax/database/eventFunctions.php",
        dataType:'json',
        data: $.extend({},obj,data)
    });
}

/**
 * Open a new navigator window on specified server
 *@param {string} source datagrid to operate with
 */
function remoteOpenWebConsole(source) {
    // if no chrono device selected, alert and ignore
    var rows=$(source+'-datagrid').datagrid('getSelections');
    if (rows.length===0) { // no display selected
        $.messager.alert("<?php _e('No Selection');?>","<?php _e('There are no item(s) selected');?>","error");
        return false;
    }
    if (rows.length!==1) {
        $.messager.alert("<?php _e('Multiple select');?>","<?php _e('Can only open one web console at a time');?>","error");
        return false;
    }
    if ($.inArray( rows[0]['IPAddr'], [ "localhost","127.0.0.1","127.0.1.1",",,1" ] ) >= 0 ) {
        $.messager.alert("<?php _e('Site invalid');?>","<?php _e('Cannot open web console from localhost\'d device');?>","error");
        return false;
    }
    window.open(
        'http://'+rows[0]['IPAddr']+'/',
        'remote_'+rows[0]['IP'],
        "height=600,width=800,toolbar=no,menubar=no,status=no,resizable=yes"
    );
}

function remote_handleEvents(source,data){
    // if no display selected, alert and ignore
    var rows=$(source+'-datagrid').datagrid('getSelections');
    if (rows.length===0) { // no display selected
        $.messager.alert("<?php _e('No Selection');?>","<?php _e('There are no item(s) selected');?>","error");
        return false;
    }
    var sesiones=$('#sesiones-datagrid').datagrid('getData')['rows'];

    for (var n=0; n<rows.length;n++) {
        // retrieve session info ( contest, journey and so ) from proper datagrid
        var evtdata= $.extend( { Session:0 ,Name:rows[n]['Name']},data )
        for (var s=0; s<sesiones.length;s++) { // find session info matching named display session
            if (sesiones[s]['ID']==rows[n]['Session']) { // found: retrieve session data
                evtdata.Session=sesiones[s]['ID'];
                evtdata.Prueba=sesiones[s]['Prueba'];
                evtdata.Jornada=sesiones[s]['Jornada'];
                evtdata.Manga=sesiones[s]['Manga'];
                evtdata.Tanda=sesiones[s]['Tanda'];
            }
        }
        if (evtdata.Session===0) { // cannot find session for named display
            $.messager.alert("<?php _e('No session');?>","Internal error: cannot locate session data for display:"+rows[n].Name,"error");
            continue;
        }
        // send an event for each display
        remote_putEvent(evtdata);
    }
}

/**
 * Send videowall change switch command
 * @param source 'videowall'
 * @returns {*}
 */
function remoteSendChangeEvent(source) {
    var ring=$(source+'-ring').combogrid('getValue');
    var view=$(source+'-view').combobox('getValue');
    var mode=$(source+'-mode').val();
    var id=$(source+'-playlist').combogrid('getValue');
    var data= {
        Oper: EVTCMD_SWITCH_SCREEN,
        Value: ""+ring+":"+view+":"+mode+":"+id
    }
    return remote_handleEvents(source,data);
}

function remoteSendKeyValueEvent(source,key,value) {
    var data = {
        Oper: key,
        Value: value
    }
    return remote_handleEvents(source, data);
}

function remoteSendButtonEvent(source,keyevent) {
    return remoteSendKeyValueEvent(source,keyevent,0);
}

function remoteSendMessageEvent(source) {
    var t=$(source+'-msgtimeout').slider('getValue');
    var m=$(source+'-msg').textbox('getValue');
    var data = {
        Oper: EVTCMD_MESSAGE,
        Value: ''+t+':'+m
    }
    return remote_handleEvents(source, data);
}

function remoteSendInternetNotification(source) {
    // en el acceso por internet no hay sesiones abiertas, luego no tiene sentido
    // especificar destino. Por consiguiente, lo que haremos sera mandar un evento
    // de tipo CMD_MESSAGE a la sesion 1 de nombre "Internet" con la prueba seleccionada
    // indicamos tambien el timeout y la lista de dorsales a notificar (o cero)
    var p=$('#remote-internet-prueba').combogrid('grid').datagrid('getSelected');
    if (p==null) {
        // indica error
        $.messager.alert("Error",'<?php _e("No contest selected");?>',"error");
        return;
    }
    var t=$(source+'-msgtimeout').slider('getValue');
    var m=$(source+'-msg').textbox('getValue');
    var d=$(source+'-dorsals').textbox('getValue');
    var evtdata = {
        Oper: EVTCMD_MESSAGE,
        Value: ((d==='')?'0':d)+':'+t+':msg:'+m,
        Session: 1,
        Name: 'Internet',
        Prueba: p.ID,
        Jornada: 0,
        Manga: 0,
        Tanda: 0
    }
    // send event
    // every 30 seconds internet clients request for new cmd_events addressed to "Internet" and display them
    // a bit of work is needed in order to avoid receiving tons of (outdated) message data
    //
    // also: notice that internet server does not use event messaging system (unpractical with thousands of client
    // connections), just poll event database table
    remote_putEvent(evtdata);
}