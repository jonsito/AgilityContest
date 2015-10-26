/*
admin.js

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

/*
* Admin related functions from dlg_tools.inc
*/

function checkForAdmin() {
    if (parseInt(authInfo.Perms)>1) {
        $.messager.alert('<?php _e("Invalid user"); ?>','<?php _e("Current user"); ?>'+" '"+authInfo.Login+"' "+'<?php _e("has not enought privileges"); ?>',"error");
        return false;
    }
    return true;
}

function backupDatabase(){
    $.fileDownload(
        '/agility/server/adminFunctions.php',
        {
            httpMethod: 'GET',
            data: {
                Operation: 'backup'
            },
            preparingMessageHtml: '<?php _e("We are preparing your backup, please wait"); ?>'+"...",
            failMessageHtml: '<?php _e("There was a problem generating your backup, please try again."); ?>'
        }
    );
    return false;
}

function performClearDatabase(oper,pass,callback) {
    // comprobamos si el password es correcto
    checkPassword(authInfo.Login,pass,function(data) {
        if (data.errorMsg) { // error
            $.messager.alert("Error",data.errorMsg,"error");
        } else { // success:
            // si password correcto invocamos la operacion
            $.ajax({
                type:'GET',
                url:"/agility/server/adminFunctions.php",
                dataType:'json',
                data: {
                    Operation: oper
                },
                success: function(res) { callback(res); },
                error: function(XMLHttpRequest,textStatus,errorThrown) {
                    $.messager.alert("Error: "+oper,"Error: "+textStatus + " "+ errorThrown,'error' );
                }
            });
        }
    });
}

function read_restoreFile(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#tools-restoreData').val(e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function restoreDatabase(){
    var l1='<?php _e("<strong>Notice:</strong><br/>"); ?>';
    var l2='<?php _e("This operation <strong>WILL ERASE <em>EVERY</em> CURRENT DATA</strong>. before trying restore<br/>"); ?>';
    var l3='<?php _e("Be aware of making a backup copy before continue<br/><br/>"); ?>';
    var l4='<?php _e("To continue enter administrator password and press <em>Accept</em>"); ?>';
    if (!checkForAdmin()) return;
    if ($('#tools-restoreFile').val()=="") {
        $.messager.alert("Restore",'<?php _e("You should specify an <em>.sql</em> file with a previous backup"); ?>',"error");
        return false;
    }
    $.messager.password('<?php _e('DataBase restore'); ?>',l1+l2+l3+l4 , function(pass){
        if (pass){
            // comprobamos si el password es correcto
            checkPassword(authInfo.Login,pass,function(data) {
                if (data.errorMsg) { // error
                    $.messager.alert("Error",data.errorMsg,"error");
                } else { // success:
                    $.messager.progress({
                        title: 'Restore',
                        msg: '<?php _e('Restoreing database'); ?>',
                        interval: 0
                    });
                    // si password correcto invocamos la operacion
                    $.ajax({
                        type:'POST', // use post to send file
                        url:"/agility/server/adminFunctions.php",
                        dataType:'json',
                        data: {
                            Operation: 'restore',
                            Data: $('#tools-restoreData').val()
                        },
                        contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
                        success: function(data) {
                            if (data.errorMsg){
                                $.messager.show({ width:300, height:150, title: '<?php _e('Database Restore Error'); ?>', msg: data.errorMsg });
                            } else {
                                $.messager.alert(
                                    '<?php _e("Restore Database"); ?>',
                                    '<?php _e("Database restore success<br />Press Accept to re-init application"); ?>',
                                    "info",
                                    function(){window.location.reload();} // reload application main page
                                );
                            }
                            $.messager.progress('close');
                        },
                        error: function(XMLHttpRequest,textStatus,errorThrown) {
                            $.messager.alert("DBRestore Error","Error: "+textStatus + " "+ errorThrown,'error' );
                            $.messager.progress('close');
                        }
                    });
                    // en paralelo arrancamos una tarea para leer el progreso de la operacion
                    function getProgress(){
                        $.ajax({
                            url:"/agility/server/adminFunctions.php",
                            dataType:'json',
                            data: {
                                Operation: 'progress'
                            },
                            success: function(data) {
                                if(data.progress!=="Done"){
                                    var bar = $.messager.progress('bar');  // get the progressbar object
                                    bar.progressbar('setValue', data.progress);  // set new progress value
                                    setTimeout(getProgress,200);
                                } else {
                                    $.messager.progress('close');
                                }
                            }
                        });
                    }
                    setTimeout(getProgress,200);
                }
            });
        }
    }).window('resize',{width:640});
}

function clearDatabase(){
    var l1='<?php _e("<strong>Notice:</strong><br/>"); ?>';
    var l2='<?php _e("This operation <strong>WILL ERASE <em>EVERY</em> CURRENT DATA</strong>.<br/>"); ?>'+
        '<?php _e("Including contests, inscriptions, scores, judges, dogs, handlers and clubs<br/>"); ?>' +
        '<?php _e("This is intended to be used ONLY as a previous step from importing new data from Excel file<br/> "); ?>';
    var l3='<?php _e("Be aware of making a backup copy before continue<br/><br/>"); ?>';
    var l4='<?php _e("To continue enter administrator password and press <em>Accept</em>"); ?>';
    if (!checkForAdmin()) return;
    $.messager.password('<?php _e('Factory Reset'); ?>',l1+l2+l3+l4 , function(pass){
        if (pass){
            performClearDatabase('reset',pass,function(data){
                if (data.errorMsg){
                    $.messager.show({ width:300, height:150, title:'<?php _e( 'Database Reset Error'); ?>', msg: data.errorMsg });
                } else {
                    $.messager.alert('<?php _e("Reset Database"); ?>','<?php _e("Data base cleared<br />Please reinit application"); ?>',"info");
                }
            });
        }
    }).window('resize',{width:640});
}

function removePruebas(){
    var l1='<?php _e("<strong>Notice:</strong><br/>"); ?>';
    var l2='<?php _e("This operation WILL ERASE every contests,"); ?>';
    var l3='<?php _e("inscriptions and scores from data base<br/><br/>"); ?>';
    var l4='<?php _e("To continue enter administrator password and press <em>Accept</em>"); ?>';
    if (!checkForAdmin()) return;
    $.messager.password('<?php _e('Erase contests'); ?>',l1+l2+l3+l4, function(pass){
        if (pass){
            performClearDatabase('clear',pass,function(data){
                if (data.errorMsg){
                    $.messager.show({ width:300, height:150, title: '<?php _e('Contests clear Error'); ?>', msg: data.errorMsg });
                } else {
                    $.messager.alert('<?php _e("Erase contests"); ?>','<?php _e("Every contests have been erased<br />Please, reinit application"); ?>',"info");
                }
            });
        }
    }).window('resize',{width:640});
}

function askForUpgrade(msg){
    if (ac_regInfo.Serial==="00000000") {
        $.messager.alert('<?php _e("Update AgilityContest"); ?>','<?php _e("Auto-Update is not allowed for unregistered installs"); ?>',"error");
        return;
    }
    var l1='<?php _e("<strong>Notice:</strong><br/>"); ?>';
    var l2='<?php _e("Be aware of making a backup copy before continue<br/><br/>"); ?>';
    var l3='<?php _e("To proceed with AgilityContest update, enter administrator password and press <em>Accept</em>"); ?>';
    if (!checkForAdmin()) return;
    $.messager.password('<?php _e('Update AgilityContest'); ?>',msg+l1+l2+l3 , function(pass) {
        if (pass) {
            // comprobamos si el password es correcto
            checkPassword(authInfo.Login,pass,function(data) {
                if (data.errorMsg) { // error
                    $.messager.alert("Error", data.errorMsg, "error");
                } else {
                    window.location='/agility/upgrade.php?sessionkey='+authInfo.SessionKey;
                }
            });
        }
    }).window('resize',{width:480});
}

function checkForUpgrades() {
    var msg="<p>"+'<?php _e("Current Version"); ?>'+": "+ac_config.version_name+"<br />"+'<?php _e("Current Release"); ?>'+": "+ac_config.version_date+"</p>";
    $.ajax({
        url:"/agility/server/adminFunctions.php",
        dataType:'json',
        data: {
            Operation: 'upgrade'
        },
        success: function(data) {
            if (typeof(data.errorMsg)!=="undefined") {
                $.messager.alert('<?php _e("Check for Upgrades"); ?>',data.errorMsg,"error");
                return;
            }
            if (data.version_date==ac_config.version_date) {
                msg = msg +'<?php _e("<p>AgilityContest is up to date</p>"); ?>';
                $.messager.alert("Version Info",msg,"info");
            }
            msg = msg +"<p>"+'<?php _e("Last Version"); ?>'+": "+data.version_name+"<br />"+'<?php _e('Last Release');?>'+": "+data.version_date+"</p>";
            if (data.version_date>ac_config.version_date) askForUpgrade(msg);
        }
    });
}