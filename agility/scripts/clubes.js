/*
 clubes.js

Copyright 2013-2014 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

// ***** gestion de clubes		*********************************************************

/**
 * Vista preliminar del logo
 */
function setLogoPreview(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function(e) {
			$('#clubes-logo-preview').attr('src', e.target.result);
		}
		reader.readAsDataURL(input.files[0]);
	}
}

function acceptLogoPreview() {
	// import logo to back window
	$('#clubes-Logo').attr('src', $('#clubes-logo-preview').attr('src'));
	// and close logo dialog
	$('#clubes-logo-dialog').dialog('close')
}

/*
 * Export logo image to server and store into database
 */
function saveLogo() {
	// TODO: write
	var fd = new FormData();
	fd.append('file', $('#clubes-Logo')[0].files[0]);
	$.ajax({
		url: '/agility/database/clubFunctions.php',
		data: fd,
		processData: false,
		contentType: false,
		type: 'POST',
		success: function (data) {
			alert(data);
		}
	});
}

/**
 * Recalcula la tabla de clubes anyadiendo parametros de busqueda
 */
function doSearchClub() {
	// reload data adding search criteria
    $('#clubes-datagrid').datagrid('load',{
        where: $('#clubes-datagrid-search').val()
    });
}

/**
 * Abre el dialogo para crear un nuevo club
 *@param {string} dg datagrid id
 *@param {string} def nombre por defecto del club
 *@param {function} onAccept what to do when a new club is created
 */
function newClub(dg,def,onAccept){
	$('#clubes-dialog').dialog('open').dialog('setTitle','Nuevo club');
	$('#clubes-form').form('clear');
	// si el nombre del club contiene "Buscar" ignoramos
	if (!strpos(def,"Buscar")) $('#clubes-Nombre').val(def);
	$('#clubes-Operation').val('insert');
	// select ID=1 to get default logo
	var nombre="/agility/server/database/clubFunctions.php?Operation=logo&ID=1";
    $('#clubes-Logo').attr("src",nombre);
    // add onAccept related function if any
	if (onAccept!==undefined)
		$('#clubes-okBtn').one('click',onAccept);
}

/**
 * Abre el dialogo para editar un club existente
 * @var {string} dg current active datagrid ID
 */
function editClub(dg){
	if ($('#clubes-datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Update Error:","!No ha seleccionado ningún Club!","warning");
    	return; // no way to know which dog is selected
    }
    row.Operation='update';
	var nombre="/agility/server/database/clubFunctions.php?Operation=logo&ID="+row.ID;
    $('#clubes-Logo').attr("src",nombre);
    $('#clubes-dialog').dialog('open').dialog('setTitle','Modificar datos del club');
    $('#clubes-form').form('load',row);
}

/**
 * Funcion invocada cuando se pulsa "OK" en el dialogo de clubes
 * Ask for commit new/edit club to server
 */
function saveClub(){
    // do normal submit
    $('#clubes-form').form('submit',{
        url: 'server/database/clubFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(res){
            var result = eval('('+res+')');
            if (result.errorMsg){
                $.messager.show({
                    title: 'Error',
                    msg: result.errorMsg
                });
            } else {
                $('#clubes-dialog').dialog('close');        // close the dialog
                $('#clubes-datagrid').datagrid('reload');    // reload the clubes data
            }
        }
    });
}

/**
 * Pide confirmacion para borrar un club de la base de datos
 * En caso afirmativo lo borra
 * @var {string} dg current active datagrid ID
 */
function deleteClub(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Delete Error:","!No ha seleccionado ningún Club!","warning");
    	return; // no way to know which dog is selected
    }
    if (row.ID==1) {
    	$.messager.alert("Delete Error:","Esta entrada no se puede borrar","error");
    	return; // cannot delete default club
    }
    $.messager.confirm('Confirm','Borrar el club "'+row.Nombre+'" de la base de datos ¿Seguro?',function(r){
        if (!r) return;
        $.get('/agility/server/database/clubFunctions.php',{Operation:'delete',ID:row.ID},function(result){
            if (result.success){
                $(dg).datagrid('reload');    // reload the provided datagrid
            } else {
                $.messager.show({ width:300,height:200,title: 'Error',msg: result.errorMsg });
            }
        },'json');
    });
}
