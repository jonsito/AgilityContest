/*
 usuarios.js

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

//***** gestion de usuarios		*********************************************************

function formatPermissions(val,row,idx) {
	switch (row.Perms) {
	case "0": return 'Root';
	case "1": return 'Administrador';
	case "2": return 'Operador';
	case "3": return 'Asistente';
	case "4": 
	case "5": return 'Invitado';
	default: return 'Desconocido';
	}
}

/**
 * Open "New User dialog"
 *@param {string} dg datagrid ID de donde se obtiene el usuario
 *@param {string} def default value to insert into Nombre 
 *@param {function} onAccept what to do when a new User is created
 */
function newUser(dg,def,onAccept){
	$('#usuarios-dialog').dialog('open').dialog('setTitle','Nuevo usuario'); // open dialog
	$('#usuarios-form').form('clear');// clear old data (if any)
	if (!strpos(def,"Buscar")) $('#usuarios-Nombre').val(def);// fill user Name
	$('#usuarios-Operation').val('insert');// set up operation
	if (onAccept!==undefined) $('#usuarios-okBtn').one('click',onAccept);
}

/**
 * Open "Edit User" dialog
 * @param {string} dg datagrid ID de donde se obtiene el usuario
 */
function editUser(dg){
	if ($('#usuarios-datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Edit Error:","!No ha seleccionado ningún Usuario!","warning");
    	return; // no way to know which dog is selected
    }
    // set up operation properly
    row.Operation='update';
    // open dialog
    $('#usuarios-dialog').dialog('open').dialog('setTitle','Modificar datos del usuario');
    // and fill form with row data
    $('#usuarios-form').form('load',row);
    // select proper combo data
}

/**
 * Call json to Ask for commit new/edit user to server
 */
function saveUser(){
	// take care on bool-to-int translation from checkboxes to database
    // do normal submit
    $('#usuarios-form').form('submit',{
        url: '/agility/server/database/userFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(res){
            var result = eval('('+res+')');
            if (result.errorMsg){
                $.messager.show({width:300,height:200,title: 'Error',msg: result.errorMsg});
            } else {
                $('#usuarios-dialog').dialog('close');        // close the dialog
                $('#usuarios-datagrid').datagrid('reload');    // reload the user data
            }
        }
    });
}

/**
 * Delete user data in bbdd
 * @param {string} dg datagrid ID de donde se obtiene el user
 */
function deleteUser(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Delete Error:","!No ha seleccionado ningún Usuario!","info");
    	return; // no way to know which user is selected
    }
    if (row.ID==1) {
    	$.messager.alert("Delete Error:","Esta entrada no se puede borrar","error");
    	return; // cannot delete default user
    }
    $.messager.confirm('Confirm','Borrar datos del usuario:'+row.Login+'\n ¿Seguro?',function(r){
      	if (!r) return;
        $.get('/agility/server/database/userFunctions.php',{Operation:'delete',ID:row.ID},function(result){
            if (result.success){
                $(dg).datagrid('reload');    // reload the user data
            } else {
            	// show error message
                $.messager.show({width:300,height:200,title: 'Error',msg: result.errorMsg});
            }
        },'json');
    });
}

function setPassword(dg) {
	alert("Usuarios::setPassword {PENDING}");
}