/*
 perros.js

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

// ***** gestion de perros		*********************************************************

function reload_perrosDatagrid(dg) {
	var w=$(dg+'-search').val();
	if (strpos(w,"Buscar",0)) w='';
	// $(dg).datagrid('load',{Operation:'select', where: w, Federation: workingData.federation });
    $(dg).datagrid('reload');
}

/**
 * Abre el dialogo para insertar datos de un nuevo perro
 * @param {string} dg datagrid ID de donde se obtiene el perro
 * @param def nombre por defecto que se asigna al perro en el formulario
 */
function newDog(dg,def){
    $('#perros-form').form('clear'); // start with an empty form
	$('#perros-dialog').dialog('open').dialog('setTitle','<?php _e('New dog'); ?>'+' - '+fedName(workingData.federation));
	if (!strpos(def,"Buscar")) $('#perros-Nombre').val(def.capitalize());
	$('#perros-Operation').val('insert');
	$('#perros-warning').css('visibility','hidden');
	$('#perros-okBtn').one('click',function() {reload_perrosDatagrid(dg)});
}

/**
 * Abre el dialogo para editar datos de un perro ya existente
 * @param {string} dg datagrid ID de donde se obtiene el perro
 */
function editDog(dg){
	if ($('#perros-datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("Edit Error"); ?>','<?php _e("There is no selected dog"); ?>',"warning");
    	return; // no way to know which dog is selected
    }
    $('#perros-dialog').dialog('open').dialog('setTitle','<?php _e('Modify data on dog'); ?>'+' - '+fedName(workingData.federation));
    // add extra required data to form dialog
    row.Operation='update';
    $('#perros-form').form('load',row);// load form with row data
	$('#perros-warning').css('visibility','visible');
	$('#perros-okBtn').one('click',reload_perrosDatagrid);
}

/**
 * Abre el dialogo para editar datos de un perro que se ha inscrito en una prueba
 */
function editInscribedDog(){
	var id=$('#edit_inscripcion-Perro').val();
	$('#perros-form').form('load','/agility/server/database/dogFunctions.php?Operation=getbyidperro&ID='+id);
    $('#perros-dialog').dialog('open').dialog('setTitle','<?php _e('Modify data on dog to be inscribed'); ?>'+' - '+fedName(workingData.federation));
    // add extra required data to form dialog
	$('#perros-warning').css('visibility','visible');
	$('#perros-okBtn').one('click',function() {
		// leave inscripcion dialog open
		saveInscripcion(false);
		// and refill dog changes with new data
		$.ajax({
			url: '/agility/server/database/dogFunctions.php',
			data: { Operation: 'getbyidperro', ID: id },
			dataType: 'json',
			success: function(data) {
				$('#edit_inscripcion-Nombre').val(data.Nombre);
				$('#edit_inscripcion-Licencia').val(data.Licencia);
				$('#edit_inscripcion-Categoria').val(data.Categoria);
				$('#edit_inscripcion-Grado').val(data.Grado);
				$('#edit_inscripcion-NombreGuia').val(data.NombreGuia);
				$('#edit_inscripcion-NombreClub').val(data.NombreClub);
			}
		});
	});
}

/**
 * Borra el perro seleccionado de la base de datos
 * @param {string} dg datagrid ID de donde se obtiene el perro
 */
function deleteDog(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("Delete error"); ?>','<?php _e("There is no selected dog"); ?>',"info");
    	return; // no way to know which dog is selected
    }
    $.messager.confirm('<?php _e('Confirm'); ?>','<?php _e('Delete dog'); ?>'+': "'+ row.Nombre+'" '+'<?php _e('from database'); ?>'+'.\n'+'<?php _e('Sure?'); ?>',function(r){
       	if (!r) return;
        $.get('/agility/server/database/dogFunctions.php',{Operation:'delete',ID:row.ID},function(result){
            if (result.success){
                $('#perros-datagrid').datagrid('reload');    // reload the dog data
            } else { // show error message
                $.messager.show({ title: 'Error',  msg: result.errorMsg });
            }
        },'json');
    });
}

/**
 * Abre el formulario para anyadir/asignar perros a un guia
 *@param {string} dgstr identificador del datagrid que se actualiza
 *@param {object} guia datos del guia
 */
function assignPerroToGuia(dgstr,guia) {
	// clean previous dialog data
	$('#chperros-header').form('clear');
	$('#chperros-Search').combogrid('clear');
	$('#chperros-form').form('clear'); 
	// set up default guia data
	$('#chperros-newGuia').val(guia.ID);
    $('#chperros-Operation').val('update');
    $('#chperros-parent').val(dgstr);
	// desplegar ventana y ajustar textos
	$('#chperros-title').text('<?php _e('Search dog/Declare new dog and assign to'); ?>'+' '+guia.Nombre);
	$('#chperros-dialog').dialog('open').dialog('setTitle','<?php _e("Reassign / Declare dog"); ?>'+' - '+fedName(workingData.federation));
}

/**
* Abre el formulario para anyadir perros a un guia
* @param {string} dgstr datagrid ID de donde se obtiene el perro
* @param {object} guia datos del guia
*/
function editPerroFromGuia(dgstr,guia) {
	// try to locate which dog has been selected
    var dg=$(dgstr);
    var row = dg.datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("Error"); ?>','<?php _e("There is no selected dog"); ?>',"warning");
    	return; // no way to know which dog is selected
    }
    // add extra required data to form dialog
    row.Operation='update';
    $('#perros-form').form('load',row);	// load form with row data. onLoadSuccess will fix comboboxes
    // finally display composed data
    $('#perros-dialog').dialog('open').dialog('setTitle','<?php _e('Modify data on dog assigned to'); ?>'+' '+guia.Nombre+' - '+fedName(workingData.federation));
	$('#perros-okBtn').one('click',function () { dg.datagrid('reload'); } );
}

/**
 * Quita la asignacion del perro marcado al guia indicado
 * @param {string} dgstr datagrid ID de donde se obtiene el perro
 * @param {object} guia datos del guia
 */
function delPerroFromGuia(dgstr,guia) {
    var dg=$(dgstr);
    var row = dg.datagrid('getSelected');
    if (!row){
    	$.messager.alert('<?php _e("Error"); ?>','<?php _e("There is no selected dog"); ?>',"warning");
    	return; // no way to know which dog is selected
    }
    $.messager.confirm('<?php _e('Confirm'); ?>','<?php _e("Delete assignment from dog"); ?>'+" '"+row.Nombre+"' '+'<?php _e('to handler');?>'+' '"+guia.Nombre+"' "+'<?php _e('Sure?');?>',function(r){
        if (r){
            $.get('/agility/server/database/dogFunctions.php',{Operation:'orphan',ID:row.ID},function(result){
                if (result.success){
                	dg.datagrid('reload');
                } else {
                    $.messager.show({title: '<?php _e('Error'); ?>', msg: result.errorMsg, width: 300,height:200 });
                }
            },'json');
        }
    });
}

/** 
 * Actualiza (reassign+edit) los datos de un perro pre-asignado a otro guia
 */
function assignDog() {
	// set up guia
	$('#chperros-Guia').val($('#chperros-newGuia').val());
    $('#chperros-Operation').val('update');
    $('#chperros-Federation').val(workingData.federation);
    var frm = $('#chperros-form');
    if (!frm.form('validate')) return; // don't call inside ajax to avoid override beforeSend()
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/dogFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){
                $.messager.show({ width:300,height:200, title: 'Error', msg: result.errorMsg });
            } else {
                // TODO: this leave focus datagrid handling buggy. study why
                var dg=$('#chperros-parent').val();
                if (dg!="") $(dg).datagrid('reload');
            	$('#chperros-Search').combogrid('clear');  // clear search field
                $('#chperros-dialog').dialog('close');        // close the dialog
            }
        }
    });
}

/**
 * Anyade (new) un nuevo perro desde el menu de reasignacion de perros
 */
function saveChDog(){
    var frm = $('#chperros-form');
    $('#chperros-Guia').val($('#chperros-newGuia').val());
    $('#chperros-Operation').val('insert');
    $('#chperros-Federation').val(workingData.federation);
    if (!frm.form('validate')) return; // don't call inside ajax to avoid override beforeSend()
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/dogFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){
                $.messager.show({ width:300,height:200, title: '<?php _e('Error'); ?>', msg: result.errorMsg });
            } else {
                // TODO: this leave focus datagrid handling buggy. study why
                var dg=$('#chperros-parent').val();
                if (dg!="") $(dg).datagrid('load');
            	if (result.insert_id ) $('#chperros-ID').val(result.insert_id);
            	$('#chperros-Search').combogrid('clear');  // clear search field
                $('#chperros-dialog').dialog('close');    // close the dialog
            }
        }
    });
}

/**
 * Ejecuta la peticion json para anyadir/editar un perro
 */
function saveDog(){
    var frm = $('#perros-form');
    $('#perros-Federation').val(workingData.federation);
    if (!frm.form('validate')) return; // don't call inside ajax to avoid override beforeSend()
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/dogFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){
                $.messager.show({ width:300,height:200, title: '<?php _e('Error'); ?>', msg: result.errorMsg });
            } else {
                // reload the dog data from inscripciones (if any)
            	if (isDefined('listaNoInscritos')) listaNoInscritos();
            	if(result.insert_id && (frm.operation==="insert") ) $('#perros-ID').val(result.insert_id);
    	        // close the dialog
                $('#perros-dialog').dialog('close'); 
            }
        }
    });
}
