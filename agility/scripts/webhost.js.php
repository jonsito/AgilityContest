/*
 webhost.js.php

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
require_once(__DIR__."/../server/tools.php");
?>

//***** tareas a realizar en primer arranque para instalaciones en un web hosting ************************

function wh_read_registrationFile(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#install_regdata').val(e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function wh_check_dbAccess(callback) {
    $.ajax({
        type: "POST",
        url: 'ajax/webhostingFunctions.php',
        data: {
            'Operation' : 'checkdbroot',
            'Server': $('#install_host').textbox('getText'),
            'Database': $('#install_dbname').textbox('getText'),
            'User': $('#install_dbuser').textbox('getText'),
            'Password': $('#install_dbpass').textbox('getText')
        },
        async: true,
        cache: false,
        dataType: 'json',
        success: function(data){
            if ( data.errorMsg ) {
                $.messager.alert('<?php _e("Error"); ?>',"checkDataBase(): " + strval(data.errorMsg),"error");
                $('#install_dbpass_match').html('<span style="color:red"><?php _e('Failed');?>!</span>');
            } else {
                $('#install_dbpass_match').html('<span style="color:green"><?php _e('Succeed');?>!</span>');
                $('#install_data').css('display','inherit');
                if ( typeof (callback) === 'function' ) callback();
            }
        },
        error: function(XMLHttpRequest,textStatus,errorThrown) {
            var msg= "check Database access error: "+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus + " "+ errorThrown ;
            $.messager.alert('<?php _e("Error"); ?>',"checkDataBase(): " + msg,"error");
            $('#install_dbpass_match').html('<span style="color:red"><?php _e('Failed');?>!</span>');
        }
    });
}

function wh_chkValidPassword(user,p1,p2) {
    var pw1=$(p1).textbox('getText');
    var pw2=$(p2).textbox('getText');
    if ( (pw1===pw2) && (pw1.length>=6) ) return true;
    $.messager.alert('<?php _e("Error"); ?>',user+': <?php _e("Invalid password provided");?>',"error");
    return false;
}

function wh_checkAndInstall() {
    // need to accept license terms
    if ($('#install_accept').prop('checked')!==true) {
        $.messager.alert('<?php _e("Error"); ?>','<?php _e("Must accept license terms");?>',"error");
        return false;
    }
    // check for registration file ok
    if( $('#install_regdata').val()==="") {
        $.messager.alert('<?php _e("Error"); ?>','<?php _e("Must provide registration file");?>',"error");
        return false;
    }
    // check for user data ok
    if (! wh_chkValidPassword('Admin','#install_admin','#install_admin2')) return false;
    if (! wh_chkValidPassword('Operator','#install_operator','#install_operator2')) return false;
    if (! wh_chkValidPassword('Assistant','#install_assistant','#install_assistant2')) return false;
    // check database access ok
    wh_check_dbAccess(function() {
        // database access ok.
        // so now comes system call to create database and register license
        $.ajax({
            type: "POST",
            url: 'ajax/webhostingFunctions.php',
            data: {
                // no need to base64 encode pass: we are using post over ssl
                // also do not hass pw at client side. it's prone to security failure point
                // https://stackoverflow.com/questions/9397268/passing-base64-encoded-username-and-password-through-a-https-ssl-connection-fo
                'Operation' : 'install',
                'Server': $('#install_host').textbox('getText'),
                'Database': $('#install_dbname').textbox('getText'),
                'User': $('#install_dbuser').textbox('getText'),
                'Password': $('#install_dbpass').textbox('getText'),
                'Admin': $('#install_admin').textbox('getText'),
                'Operator': $('#install_operator').textbox('getText'),
                'Assistant': $('#install_assistant').textbox('getText'),
                'License': $('#install_regdata').val()
            },
            async: true,
            cache: false,
            dataType: 'json',
            success: function(data){
                if ( data.errorMsg ) {
                    $.messager.alert('<?php _e("Error"); ?>',"initial_setup(): " + strval(data.errorMsg),"error");
                    return false;
                } else {
                    // show OK message
                    str="<?php _e("Initialization done. Press OK to start first launch"); ?>"
                    $.messager.alert('<?php _e("Done"); ?>', str , "info");
                    // close setup window
                    $('#install-window').window('close');
                    // and launch installdb initial page
                    // PENDING
                }
            },
            error: function(XMLHttpRequest,textStatus,errorThrown) {
                var msg= "AgilityContest setup error: "+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus + " "+ errorThrown ;
                $.messager.alert('<?php _e("Error"); ?>',"ac_setup(): " + msg,"error");
            }
        });
        // on success, reload page to installdb+set passwords
    });
}