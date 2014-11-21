/*
 jueces.js

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

//***** gestion de jueces		*********************************************************

/**
 * Open "New Juez dialog"
 *@param {string} dg datagrid ID de donde se obtiene el juez
 *@param {string} def default value to insert into Nombre 
 *@param {function} onAccept what to do when a new Juez is created
 */
function newJuez(dg,def,onAccept){
	$('#jueces-dialog').dialog('open').dialog('setTitle','Nuevo juez'); // open dialog
	$('#jueces-form').form('clear');// clear old data (if any)
	if (!strpos(def,"Buscar")) $('#jueces-Nombre').val(def);// fill juez Name
	$('#jueces-Operation').val('insert');// set up operation
	if (onAccept!==undefined) $('#jueces-okBtn').one('click',onAccept);
}

/**
 * Open "Edit Juez" dialog
 * @param {string} dg datagrid ID de donde se obtiene el juez
 */
function editJuez(dg){
	if ($('#jueces-datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Edit Error:","!No ha seleccionado ningún Juez!","warning");
    	return; // no way to know which dog is selected
    }
    // set up operation properly
    row.Operation='update';
    // open dialog
    $('#jueces-dialog').dialog('open').dialog('setTitle','Modificar datos del juez');
    // and fill form with row data
    $('#jueces-form').form('load',row);
}

/**
 * Call json to Ask for commit new/edit juez to server
 */
function saveJuez(){
	// take care on bool-to-int translation from checkboxes to database
    $('#jueces-Internacional').val( $('#jueces-Internacional').is(':checked')?'1':'0');
    $('#jueces-Practicas').val( $('#jueces-Practicas').is(':checked')?'1':'0');
    // do normal submit
    $('#jueces-form').form('submit',{
        url: '/agility/server/database/juezFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(res){
            var result = eval('('+res+')');
            if (result.errorMsg){
                $.messager.show({width:300,height:200,title: 'Error',msg: result.errorMsg});
            } else {
                $('#jueces-dialog').dialog('close');        // close the dialog
                $('#jueces-datagrid').datagrid('reload');    // reload the juez data
            }
        }
    });
}

/**
 * Delete juez data in bbdd
 * @param {string} dg datagrid ID de donde se obtiene el juez
 */
function deleteJuez(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Delete Error:","!No ha seleccionado ningún Juez!","info");
    	return; // no way to know which juez is selected
    }
    if (row.ID==1) {
    	$.messager.alert("Delete Error:","Esta entrada no se puede borrar","error");
    	return; // cannot delete default juez
    }
    $.messager.confirm('Confirm','Borrar datos del juez:'+row.Nombre+'\n ¿Seguro?',function(r){
      	if (!r) return;
        $.get('/agility/server/database/juezFunctions.php',{Operation:'delete',ID:row.ID},function(result){
            if (result.success){
                $(dg).datagrid('reload');    // reload the juez data
            } else {
            	// show error message
                $.messager.show({width:300,height:200,title: 'Error',msg: result.errorMsg});
            }
        },'json');
    });
}
