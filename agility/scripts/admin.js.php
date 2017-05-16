/*
admin.js

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
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>

/*
* Admin related functions from dlg_tools.inc
*/

function checkForAdmin() {
    if (parseInt(ac_authInfo.Perms)>1) {
        $.messager.alert('<?php _e("Invalid user"); ?>','<?php _e("Current user"); ?>'+" '"+ac_authInfo.Login+"' "+'<?php _e("has not enought privileges"); ?>',"error");
        return false;
    }
    return true;
}

function viewLogFile(){
    if (ac_authInfo.Perms>access_level.PERMS_OPERATOR) {
        $.messager.alert('<?php _e("Error"); ?>',"<?php _e("ViewLog(): Must log in with 'admin' or 'operator' access level"); ?>","error");
        return;
    }
    $.fileDownload(
        '/agility/server/adminFunctions.php',
        {
            httpMethod: 'GET',
            data: {
                Operation: 'viewlog'
            },
            preparingMessageHtml: '<?php _e("We are preparing your log file download, please wait"); ?>'+"...",
            failMessageHtml: '<?php _e("There was a problem generating your log file, please report author."); ?>'
        }
    );
    return false;
}

function resetLogFile(){
    if (ac_authInfo.Perms>access_level.PERMS_ADMIN) {
        $.messager.alert('<?php _e("Error"); ?>',"<?php _e("ResetLog(): Must log in with 'admin' access level"); ?>","error");
        return;
    }
    $.messager.confirm('<?php _e('Confirm'); ?>','<?php _e('Clear trace and debugging log file');?>'+'<br/>'+'<?php _e('Sure?'); ?>',function(r){
        if (r){
            $.ajax({
                type:'GET',
                url:"/agility/server/adminFunctions.php",
                dataType:'json',
                data: {
                    Operation: 'resetlog'
                },
                success: function(res) {
                    $.messager.alert({ width:300, height:150, title: '<?php _e('Log Cleared'); ?>', msg: '<?php _e('Debug and trace log file successfully cleared');?>' });
                },
                error: function(XMLHttpRequest,textStatus,errorThrown) {
                    $.messager.alert("Error: "+oper,"Error: "+textStatus + " "+ errorThrown,'error' );
                }
            });
        }
    });
}

function clearTempDir(){
    if (ac_authInfo.Perms>access_level.PERMS_ADMIN) {
        $.messager.alert('<?php _e("Error"); ?>',"<?php _e("ClearTempDir(): Must log in with 'admin' access level"); ?>","error");
        return;
    }
    $.messager.confirm({
        title: '<?php _e('Clear tempdir'); ?>',
        msg: '<p><?php _e('This will remove every unneeded temporary files and logs');?></p>' +
        '<p><?php _e('Be sure that no update, import, restore, mail or similar action is in progress');?></p>' +
        '<p><?php _e('Continue?'); ?></p>',
        fn: function (r) {
            if (!r) return false;
            $.ajax({
                type: 'GET',
                url: "/agility/server/adminFunctions.php",
                dataType: 'json',
                data: {
                    Operation: 'cleartmpdir'
                },
                success: function (res) {
                    if (res.errorMsg) {
                        $.messager.alert("Error","ClearTempDir() Error: " + res.errorMsg, 'error');
                    }
                    $.messager.alert({
                        width: 400,
                        height: 125,
                        title: '<?php _e('TmpDir Cleared'); ?>',
                        msg: '<?php _e('Temporary directory successfully cleared');?>'
                    });
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    $.messager.alert("Error","ClearTempDir() Error: " + textStatus + " " + errorThrown, 'error');
                }
            });
        },
        width: 600,
        height: 200
    });
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

/**
 * call server to perform automatic backup
 * @param {integer} mode -1:system 0:user-datetime 1:user-usbcopy
 * @param {string} dir server  directory to dump user backup, or "" to use configuration settings
 */
function autoBackupDatabase(mode,dir) {
    if (parseInt(ac_config.backup_disabled)!==0) {
        console.log("auto-backup is disabled. Skip");
        return;
    }
    setTimeout(function(){
        $.ajax({
            type: 'GET',
            url: "/agility/server/adminFunctions.php",
            dataType: 'json',
            data: {
                Operation: 'autobackup',
                Mode: mode,
                Directory: dir
            },
            success: function (res) {
                if (res.errorMsg) {
                    // warn user on error update
                    $.messager.show({title:"Error",msg:"Autobackup() Error: <br/>" + res.errorMsg,timeout:3000});
                    return;
                }
                // reset counters
                ac_config.dogs_before_backup=0;
                ac_config.time_of_last_backup=Math.floor(new Date().getTime() / 1000);
                console.log('autobackup done at '+ac_config.time_of_last_backup);
                // and inform user on backup done
                $.messager.show({
                    width: 300,
                    height: 75,
                    timeout: 1000,
                    title: '<?php _e('AutoBackup'); ?>',
                    msg: '<?php _e('Automatic database backup done');?>'
                });
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $.messager.show({title:"Error",msg:"AutoBakup() Error: " + textStatus + " " + errorThrown,timeout:3000});
            }
        });
    },0);
}

function backupCheck() {
    var dir=$('#backup_dir').textbox('getValue');
    if (dir==="") {
        $.messager.alert('No data','<?php _e("Must provide a valid directory in Agiltiycontest server");?>','error');
        return false;
    }
    autoBackupDatabase(1,dir);
}

function trigger_autoBackup(minutes) {
    var last=ac_config.time_of_last_backup;              // last backup, seconds
    var current=Math.floor(new Date().getTime() / 1000); // current time, seconds
    var next=last+minutes*60;                            // next pending backup, seconds
    // check for timeout
    if (next <= current){
        autoBackupDatabase(1,"");         // it's time to trigger backup
        ac_config.backup_handler=setTimeout(function(){trigger_autoBackup(minutes)},minutes*60*1000); // wait minutes to fire again
    } else {
        // trigger next backup
        ac_config.backup_handler=setTimeout(function(){trigger_autoBackup(minutes)},(next-current)*1000); // miliseconds till next backup
    }

}

function performClearDatabase(oper,pass,callback) {
    // comprobamos si el password es correcto
    checkPassword(ac_authInfo.Login,pass,function(data) {
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
            checkPassword(ac_authInfo.Login,pass,function(data) {
                if (data.errorMsg) { // error
                    $.messager.alert("Error",data.errorMsg,"error");
                } else { // success:
                    var suffix=getRandomString(8);
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
                            Data: $('#tools-restoreData').val(),
                            Suffix: suffix
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
                                ).window('resize',{width:350});
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
                                Operation: 'progress',
                                Suffix: suffix
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

function askForUpgrade(msg,name,release){
    var l1='<?php _e("<strong>Notice:</strong><br/>"); ?>';
    if (ac_regInfo.Serial==="00000000") {
        $.messager.alert('<?php _e("Update AgilityContest"); ?>',
            l1+msg+'<br/><?php _e("Auto-Update is not allowed for unregistered installs"); ?>',
            "info").window('resize',{width:480});
        return;
    }
    var l2='<?php _e("Be aware of making a backup copy before continue<br/><br/>"); ?>';
    var l3='<?php _e("To proceed with AgilityContest update, enter administrator password and press <em>Accept</em>"); ?>';
    var suffix=getRandomString(8);
    if (!checkForAdmin()) return;
    $.messager.password('<?php _e('Update AgilityContest'); ?>',msg+l1+l2+l3 , function(pass) {
        if (pass) {
            // comprobamos si el password es correcto
            checkPassword(ac_authInfo.Login,pass,function(data) {
                if (data.errorMsg) { // error
                    $.messager.alert("Error", data.errorMsg, "error");
                    return false;
                }
                $.messager.progress({
                    title: '<?php _e("Downloading");?>',
                    msg: '<?php _e("Downloading new version into server");?>: '+name+'-'+release,
                    interval: 0 // do not auto refresh
                });
                $.messager.progress('bar').progressbar({text: '{value}' }); // remove '%' sign at progress var
                $.ajax({
                    url:"/agility/server/adminFunctions.php",
                    dataType:'json',
                    data: {
                        Operation: 'download',
                        Version: release,
                        Suffix: suffix
                    },
                    success: function(data) {
                        $.messager.progress('close');
                        if (typeof(data.errorMsg)!=="undefined") {
                            $.messager.alert('<?php _e("Download update failed"); ?>',data.errorMsg,"error");
                            return false;
                        }
                        $.messager.confirm("<?php _e('Upgrade');?>","<?php _e('Download complete. Press Acccept to start upgrade');?>",function(r){
                            if (r) window.location='/agility/upgrade.php?sessionkey='+ac_authInfo.SessionKey;
                        });
                    },
                    error: function(XMLHttpRequest,textStatus,errorThrown) {
                        $.messager.progress('close');
                        $.messager.alert("<?php _e('Error');?>","<?php _e('Error');?>: "+textStatus + " "+ errorThrown,'error' );
                    }
                });

                // en paralelo arrancamos una tarea para leer el progreso de la operacion
                function getProgress(){
                    $.ajax({
                        url:"/agility/server/adminFunctions.php",
                        dataType:'json',
                        data: {
                            Operation: 'progress',
                            Suffix: suffix
                        },
                        success: function(data) {
                            var value=data.progress;
                            if(value!=="Done"){
                                var bar=$.messager.progress('bar');
                                bar.progressbar('setValue', value);  // set new progress value
                                setTimeout(getProgress,2000);
                            } else {
                                $.messager.progress('close');
                            }
                        }
                    });
                }
                setTimeout(getProgress,2000);
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
            if (data.version_date>ac_config.version_date) askForUpgrade(msg,data.version_name,data.version_date);
        }
    });
}