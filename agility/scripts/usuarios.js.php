/*
 usuarios.js

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

//***** gestion de usuarios		*********************************************************

function formatPermissions(val,row,idx) {
	switch (row.Perms) {
	case "0": return 'Root';
	case "1": return '<?php _e('Administrator'); ?>';
	case "2": return '<?php _e('Operator'); ?>';
	case "3": return '<?php _e('Assistant'); ?>';
	case "4": 
	case "5": return '<?php _e('Guest'); ?>';
	default: return '<?php _e('Unknown'); ?>';
	}
}

/**
 * Open "New User dialog"
 *@param {string} dg datagrid ID de donde se obtiene el usuario
 *@param {string} def default value to insert into Nombre 
 *@param {function} onAccept what to do when a new User is created
 */
function newUser(dg,def,onAccept){
	$('#usuarios-dialog').dialog('open').dialog('setTitle','<?php _e('New user'); ?>'); // open dialog
	$('#usuarios-form').form('clear');// clear old data (if any)
	if (strpos(def,"<?php _e('-- Search --'); ?>")===false) $('#usuarios-Nombre').textbox('setValue',def.capitalize());// fill user Name
	$('#usuarios-Operation').val('insert');// set up operation
	if (onAccept!==undefined) $('#usuarios-okBtn').one('click',onAccept);
	$('#usuarios-passwdBtn').linkbutton('disable');
}

/**
 * Open "Edit User" dialog
 * @param {string} dg datagrid ID de donde se obtiene el usuario
 */
function editUser(dg){
	if ($('#usuarios-datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("Edit Error"); ?>','<?php _e("There is no user selected"); ?>',"warning");
    	return; // no way to know which dog is selected
    }
    // set up operation properly
    row.Operation='update';
    // open dialog
    $('#usuarios-dialog').dialog('open').dialog('setTitle','<?php _e('Modify user data'); ?>');
    // and fill form with row data
    $('#usuarios-form').form('load',row);
	$('#usuarios-passwdBtn').linkbutton('enable');
}

/**
 * Call json to Ask for commit new/edit user to server
 */
function saveUser(){
    var frm = $('#usuarios-form');
    if (!frm.form('validate')) return; // don't call inside ajax to avoid override beforeSend()

    $('#usuarios-okBtn').linkbutton('disable');
    $.ajax({
        type: 'GET',
        url: '../ajax/database/userFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){
                $.messager.show({ width:300, height:200, title: '<?php _e('Error'); ?>', msg: result.errorMsg });
            } else {
                $('#usuarios-dialog').dialog('close');        // close the dialog
                $('#usuarios-datagrid').datagrid('reload');    // reload the user data
            }
        },
        error: function(XMLHttpRequest,textStatus,errorThrown) {
            $.messager.alert("Save User","Error:"+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus+" - "+errorThrown,'error' );
        }
    }).then(function(){
        $('#usuarios-okBtn').linkbutton('enable');
    });
}

/**
 * Delete user data in bbdd
 * @param {string} dg datagrid ID de donde se obtiene el user
 */
function deleteUser(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("Delete error"); ?>','<?php _e("There is no user selected"); ?>',"warning");
    	return; // no way to know which user is selected
    }
    if (row.ID==1) {
    	$.messager.alert('<?php _e("Delete error"); ?>','<?php _e("This entry cannot be deleted"); ?>',"error");
    	return; // cannot delete default user
    }
    $.messager.confirm('<?php _e('Confirm'); ?>','<?php _e('Delete data on user'); ?>'+': '+row.Login+'\n '+'<?php _e('Sure?'); ?>',function(r){
      	if (!r) return;
        $.get('../ajax/database/userFunctions.php',{Operation:'delete',ID:row.ID},function(result){
            if (result.success){
                $(dg).datagrid('unselectAll').datagrid('reload');    // reload the user data
            } else {
            	// show error message
                $.messager.show({width:300,height:200,title: 'Error',msg: result.errorMsg});
            }
        },'json');
    });
}

function setPassword(dg) {
	var row = $(dg).datagrid('getSelected');
	if (!row) {
		$.messager.alert('<?php _e("Password Error"); ?>','<?php _e("This entry cannot be deleted"); ?>',"error");
		return; // no way to know which user is selected
	}
	if (ac_authInfo.Perms<=1) {
		// if user perms>=admin hide "old password"field
		$('#password-SameUser').css('display','none');
	} else {
		// else if current user != selected user forbid operation
		$('#password-SameUser').css('display','inherit');
		if (ac_authInfo.ID != row.ID) {
			$.messager.alert('<?php _e("Not enought permissions"); ?>','<?php _e("Only admin users can change passwords on another user"); ?>',"error");
			return;
		}
	}
	$('#password-form').form('clear');
	$('#password-UserID').val(row.ID);
	$('#password-dialog').dialog('open');
}

function savePassword() {
	var id=$('#password-UserID').val();
	var op=$('#password-CurrentPassword').textbox('getValue');
	var np=$('#password-NewPassword').textbox('getValue');
	var np2=$('#password-NewPassword2').textbox('getValue');
	if (np!=np2) {
		$.messager.show({ width:300, height:200, title: '<?php _e('Error'); ?>', msg: '<?php _e('Passwords does not match'); ?>' });
		return;
	}
    $.ajax({
        type: 'GET',
    	// get server host name to compose https request
        url: '../ajax/database/userFunctions.php',
        data: {
        	Operation: 'password',
        	ID: 			id,
        	Password:		op,
        	NewPassword:	np,
        	NewPassword2:	np2,
        	// not sure why, but seems that default's ajaxSetup() 
        	// doesn't work fine with cors, so force it
        	SessionKey: ac_authInfo.SessionKey
        },
        dataType: 'jsonp',
        success: function (result) {
            if (result.errorMsg){
                $.messager.show({ width:300, height:200, title: 'Error', msg: result.errorMsg });
            } else {
            	$.messager.alert('<?php _e('Info'); ?>', '<?php _e('Password changed successfully'); ?>','info');
            }
        	$('#password-dialog').dialog('close');
        }
    });
}