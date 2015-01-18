/*
 tandas.js

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

//***** gestion de actividades (programa de la jornada)	*********************************************************

/**
 * Open "New Tandas dialog"
 *@param {string} dg datagrid ID de donde se obtiene la tanda
 *@param {string} def default value to insert into Nombre 
 *@param {function} onAccept what to do when a new Actividad is created
 */
function newTanda(dg,def,onAccept){
	$('#ordentandas_newtanda-dialog').dialog('open').dialog('setTitle','Nueva Actividad'); // open dialog
	// TODO: revise $('#ordentandas_nt-Nombre').prop('editable',true);
	$('#ordentandas_newtanda-form').form('clear');// clear old data (if any)
	$('#ordentandas_nt-Operation').val('insert');// set up operation
	$('#ordentandas_nt-Tipo').val(0);// set default tanda type
	$('#ordentandas_nt-Session').val(1);// set default session id for new actividad
	if (onAccept!==undefined) $('#ordentandas_nt-okBtn').one('click',onAccept);
}

/**
 * Open "Edit Actividad" dialog
 * @param {string} dg datagrid ID de donde se obtiene la actividad
 */
function editTanda(dg){
    var row = $(dg).datagrid('getSelected');
    var type=parseInt(row.Tipo);
    if (!row) {
    	$.messager.alert("Edit Error:","!No ha seleccionado ninguna actividad","warning");
    	return; // no way to know which dog is selected
    }
    // set up operation properly
    row.Operation='update';
    // TODO: revise $('#ordentandas_nt-Nombre').prop('editable',(type==0)?true:false);
    // open dialog
    $('#ordentandas_newtanda-dialog').dialog('open').dialog('setTitle','Modificar datos de la actividad');
    // and fill form with row data
    $('#ordentandas_newtanda-form').form('load',row);
}

/**
 * Call json to Ask for commit new/edit actividad to server
 */
function saveTanda($dg){
    var frm = $('#sordentandas_newtanda-form');
    if (!frm.form('validate')) return; // don't call inside ajax to avoid override beforeSend()
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/tandasFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){
                $.messager.show({ width:300, height:200, title: 'Error', msg: result.errorMsg });
            } else {
                $('#ordentandas_newtanda-dialog').dialog('close');        // close the dialog
                $(dg).datagrid('reload');    // reload the session data
            }
        }
    });
}

/**
 * Delete actividad data in bbdd
 * @param {string} dg datagrid ID de donde se obtiene la actividad
 */
function deleteTanda(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Delete Error:","!No ha seleccionado ninguna actividad","info");
    	return; // no way to know which session is selected
    }
    if (row.Type!=0) {
    	$.messager.alert("Delete Error:","Esta entrada no se puede borrar","error");
    	return; // cannot delete default session
    }
    $.messager.confirm('Confirm','Eliminar la actividad '+row.Nombre+'\n ¿Seguro?',function(r){
      	if (!r) return;
        $.get('/agility/server/database/tandasFunction.php',{Operation:'delete',ID:row.ID},function(result){
            if (result.success){
                $(dg).datagrid('reload');    // reload the session data
            } else {
            	// show error message
                $.messager.show({width:300,height:200,title: 'Error',msg: result.errorMsg});
            }
        },'json');
    });
}

function printTandas() {
	$.fileDownload(
			'/agility/server/pdf/print_ordenTandas.php',
			{
				httpMethod: 'GET',
				data: { 
					Prueba: workingData.prueba,
					Jornada: workingData.jornada
				},
		        preparingMessageHtml: "We are preparing your report, please wait...",
		        failMessageHtml: "There was a problem generating your report, please try again."
			}
		);
	return false; //this is critical to stop the click event which will trigger a normal file download!
}