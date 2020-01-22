/*
 tandas.js

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
header('Content-Type: text/javascript');
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>

//***** gestion de actividades (programa de la jornada)	*********************************************************

/**
 * Open "New Tandas dialog"
 *@param {string} dg datagrid ID de donde se obtiene la tanda
 *@param {string} def default value to insert into Nombre 
 *@param {function} onAccept what to do when a new Actividad is created
 */
function newTanda(dg,def,onAccept){    
	// get track on where to insert new data
	var row = $(dg).datagrid('getSelected');
	var insertID=0;
	if (row) insertID=row.ID;
	 // open dialog
	$('#ordentandas_newtanda-dialog').dialog('open').dialog('setTitle','<?php _e('New activity'); ?>');
	// en nueva entrada, el nombre debe ser editable
	$('#ordentandas_nt-Nombre').textbox('readonly',false);
	// limpiamos formularios y ponemos valores por defecto para nueva entrada
	$('#ordentandas_newtanda-form').form('clear');// clear old data (if any)
	$('#ordentandas_nt-InsertID').val(insertID);// set up where to insert
	$('#ordentandas_nt-Operation').val('insert');// set up operation
	$('#ordentandas_nt-Tipo').val(0); // 0: user defined actividad
	$('#ordentandas_nt-Prueba').val(workingData.prueba);// prueba
	$('#ordentandas_nt-Jornada').val(workingData.jornada);// jornada
	$('#ordentandas_nt-Session').val(1);// set default session id for new actividad
	// si hay funcion accept definida, crear evento
	if (onAccept!==undefined) $('#ordentandas_nt-okBtn').one('click',onAccept);
}

/**
 * Open "Edit Actividad" dialog
 * @param {string} dg datagrid ID de donde se obtiene la actividad
 */
function editTanda(dg){
    var rows = $(dg).datagrid('getSelections');
    if (rows.length==0) {
        $.messager.alert('<?php _e("Edit Error"); ?>','<?php _e("There is no activity selected"); ?>',"warning");
        return; // no way to know which dog is selected
    }
    if (rows.length>1) {
        $.messager.alert('<?php _e("Edit Error"); ?>','<?php _e("Too many activities selected"); ?>',"warning");
        return; // no way to know which dog is selected
    }
    var row=rows[0];
    // set up operation properly
    row.Operation='update';
    var type=parseInt(row.Tipo);
    $('#ordentandas_nt-Nombre').textbox('readonly',(type != 0));
    // open dialog
    $('#ordentandas_newtanda-dialog').dialog('open').dialog('setTitle','<?php _e('Modify activity information'); ?>');
    // and fill form with row data
    $('#ordentandas_newtanda-form').form('load',row);
}

function reloadOrdenTandas() {
    if (workingData.prueba==0) return;
    if (workingData.jornada==0) return;
    $('#ordentandas-datagrid').datagrid('unselectAll').datagrid(
        'load',
        {
            Prueba: workingData.prueba,
            Jornada: workingData.jornada ,
            Operation: 'getTandas',
            Sesion: 0 // 0: Any
        }
    );
}

/**
 * Call json to Ask for commit new/edit actividad to server
 */
function saveTanda(dg){
    var frm = $('#ordentandas_newtanda-form');
    if (!frm.form('validate')) return; // don't call inside ajax to avoid override beforeSend()

    $('#ordentandas-okBtn').linkbutton('disable');
    $.ajax({
        type: 'GET',
        url: '../ajax/database/tandasFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){
                $.messager.show({ width:300, height:200, title: '<?php _e('Error'); ?>', msg: result.errorMsg });
            } else {
                $('#ordentandas_newtanda-dialog').dialog('close');
                reloadOrdenTandas();
            }
        },
        error: function(XMLHttpRequest,textStatus,errorThrown) {
            $.messager.alert("Save Tanda","Error:"+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus+" - "+errorThrown,'error' );
        }
    }).then(function(){
        $('#ordentandas-okBtn').linkbutton('enable');
    });
}

/**
 * Delete actividad data in bbdd
 * @param {string} dg datagrid ID de donde se obtiene la actividad
 */
function deleteTanda(dg){
    var rows = $(dg).datagrid('getSelections');
    if (rows.length==0) {
        $.messager.alert('<?php _e("Edit Error"); ?>','<?php _e("There is no activity selected"); ?>',"warning");
        return; // no way to know which dog is selected
    }
    if (rows.length>1) {
        $.messager.alert('<?php _e("Edit Error"); ?>','<?php _e("Too many activities selected"); ?>',"warning");
        return; // no way to know which dog is selected
    }
    var row=rows[0];
    if (row.Tipo!=0) {
    	$.messager.alert('<?php _e("Delete error"); ?>','<?php _e("This entry cannot be deleted"); ?>',"error");
    	return; // cannot delete default session
    }
    $.messager.confirm('<?php _e('Confirm'); ?>','<?php _e('Remove activity'); ?>'+' '+row.Nombre+'\n '+'<?php _e('Sure?'); ?>',function(r){
      	if (!r) return;
        $.get('../ajax/database/tandasFunctions.php',{ Operation: 'delete', ID: row.ID },function(result){
            if (result.success){
                reloadOrdenTandas();
            } else {
            	// show error message
                $.messager.show({width:300,height:200,title: '<?php _e('Error'); ?>',msg: result.errorMsg});
            }
        },'json');
    });
}
