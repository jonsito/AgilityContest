/*
 entrenamientos.js.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

//***** gestion de la sesion de entrenamientos de la prueba  ******************************************************

/**
 * Open "new Training session dialog"
 *@param {string} dg datagrid ID de donde se obtiene las sesiones de entrenamiento
 *@param {string} def default value to insert into Nombre 
 *@param {function} onAccept what to do when a new Actividad is created
 */
function newEntrenamiento(dg,def,onAccept){
	// get track on where to insert new data
	var row = $(dg).datagrid('getSelected');
	var insertID=0;
	if (row) insertID=row.ID;
	 // open dialog
	$('#entrenamientos_newEntrenamiento-dialog').dialog('open').dialog('setTitle','<?php _e('New activity'); ?>');
	// en nueva entrada, el nombre debe ser editable
	$('#entrenamientos-Nombre').textbox('readonly',false);
	// limpiamos formularios y ponemos valores por defecto para nueva entrada
	$('#entrenamientos_newtEntrenamiento-form').form('clear');// clear old data (if any)
	$('#entrenamientos-InsertID').val(insertID);// set up where to insert
	$('#entrenamientos-Operation').val('insert');// set up operation
	$('#entrenamientos-Tipo').val(0); // 0: user defined actividad
	$('#entrenamientos-Prueba').val(workingData.prueba);// prueba
	$('#entrenamientos-Jornada').val(workingData.jornada);// jornada
	$('#entrenamientos-Session').val(1);// set default session id for new actividad
	// si hay funcion accept definida, crear evento
	if (onAccept!==undefined) $('#entrenamientos-okBtn').one('click',onAccept);
}

/**
 * Open "Edit Actividad" dialog
 * @param {string} dg datagrid ID de donde se obtiene la actividad
 */
function editEntrenamiento(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("Edit Error"); ?>','<?php _e("There is no training session selected"); ?>',"warning");
    	return; // no way to know which dog is selected
    }
    // set up operation properly
    row.Operation='update';
    var type=parseInt(row.Tipo);
    $('#entrenamientos-Nombre').textbox('readonly',(type != 0));
    // open dialog
    $('#entrenamientos_newEntrenamiento-dialog').dialog('open').dialog('setTitle','<?php _e('Modify training entry information'); ?>');
    // and fill form with row data
    $('#entrenamientos_newEntrenamiento-form').form('load',row);
}

function resetEntrenamientos(dg) {
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/trainingFunctions.php',
        data: {
            Operation:'populate',
            Prueba: workingData.prueba
        },
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){
                $.messager.show({ width:300, height:200, title: '<?php _e('Error'); ?>', msg: result.errorMsg });
            }
            reloadEntrenamientos();
        }
    });
}

function reloadEntrenamientos() {
    var dg=$('#entrenamientos-datagrid');
    if (workingData.prueba==0) return;
    if (dg.datagrid('options').editIndex!=-1) {
        $.messager.alert("<php _('Edit active');?>","<?php _e('Please close edition before update');?>","error");
        return;
    }
    dg.datagrid( 'load', { Operation: 'enumerate', Prueba: workingData.prueba } );
}

/**
 * @param {string} dg datagrid ID de donde se obtiene la actividad
 * Call json to Ask for commit changes in training session to server
 */
function saveEditEntrenamiento(dg){
    // save data
    // tel datagrid to end edition
    $(dg).datagrid('endEdit',$(dg).datagrid('options').editIndex); // onAfterEdit will set editIndex
}

/**
 * Cancel editing, restore previous values
 * @param {string} dg datagrid ID de donde se obtiene la actividad
 */
function cancelEditEntrenamiento(dg){
    var editIndex=$(dg).datagrid('options').editIndex;
    // restore previous data
    var editRow=$(dg).datagrid('options').editRow;
    $(dg).datagrid('updateRow',{index:editIndex,row:editRow});
    // tell datagrid to end edition
    $(dg).datagrid('endEdit',editIndex); // onAfterEdit will set editIndex
}

function printEntrenamientos() {

}

function importExportEntrenamientos() {
    var dg=$('#entrenamientos-datagrid');
    if (workingData.prueba==0) return;
    if (dg.datagrid('options').editIndex!=-1) {
        $.messager.alert("<php _('Edit active');?>","<?php _e('Please close edition before import');?>","error");
        return;
    }
}

//reajusta el orden de las sesiones de entrenamiento
//poniendo la sesion "from" delante (where==0) o detras (where==1) de la sesion "to"
function dragAndDropEntrenamientos(from,to,where) {
    if (workingData.prueba==0) return;
    if (workingData.jornada==0) return;
    $.ajax({
        type:'GET',
        url:"/agility/server/database/trainingFunctions.php",
        dataType:'json',
        data: {
            Operation: 'dnd',
            Prueba: workingData.prueba,
            From: from,
            To: to,
            Where: where
        },
        success: function (result) {
            if (result.errorMsg){
                $.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: result.errorMsg });
            }
            reloadEntrenamientos();
        }
    });
}