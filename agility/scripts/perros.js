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

// ***** gestion de perros		*********************************************************

function reload_perrosDatagrid() {
	var w=$('#perros-datagrid-search').val();
	if (strpos(w,"Buscar")) w='';
	$('#perros-datagrid').datagrid('load',{ where: w });
}

/**
 * Abre el dialogo para insertar datos de un nuevo perro
 * @param {string} dg datagrid ID de donde se obtiene el perro
 * @param def nombre por defecto que se asigna al perro en el formulario
 */
function newDog(dg,def){
	$('#perros-dialog').dialog('open').dialog('setTitle','Nuevo perro');
	$('#perros-form').form('clear'); // start with an empty form
	if (!strpos(def,"Buscar")) $('#perros-Nombre').val(def);
	$('#perros-Operation').val('insert');
	$('#perros-warning').css('visibility','hidden');
	$('#perros-okBtn').one('click',reload_perrosDatagrid);
}

/**
 * Abre el dialogo para editar datos de un perro ya existente
 * @param {string} dg datagrid ID de donde se obtiene el perro
 */
function editDog(dg){
	if ($('#perros-datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Edit Error:","!No ha seleccionado ningún perro!","warning");
    	return; // no way to know which dog is selected
    }
    $('#perros-dialog').dialog('open').dialog('setTitle','Modificar datos del perro');
    // add extra required data to form dialog
    row.Operation='update';
    $('#perros-form').form('load',row);// load form with row data
	$('#perros-warning').css('visibility','visible');
	$('#perros-okBtn').one('click',reload_perrosDatagrid);
}

/**
 * Abre el dialogo para editar datos de un perro que se ha inscrito en una prueba
 * @param {string} dg datagrid ID de donde se obtiene el perro
 */
function editInscribedDog(dg){
	id=$('#edit_inscripcion-Perro').val();
	$('#perros-form').form('load','/agility/server/database/dogFunctions.php?Operation=getbyidperro&ID='+id);
    $('#perros-dialog').dialog('open').dialog('setTitle','Modificar datos del perro a inscribir');
    // add extra required data to form dialog
	$('#perros-warning').css('visibility','visible');
	$('#perros-okBtn').one('click',function() {
		// leave inscripcion dialog open
		saveInscripcion(false);
		// and refill dog changes with new data
		$.ajax({
			url: '/agility/server/database/dogFunctions.php?Operation=getbyidperro&ID='+id,
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
    	$.messager.alert("Delete Error:","!No ha seleccionado ningún perro!","info");
    	return; // no way to know which dog is selected
    }
    $.messager.confirm('Confirm','Borrar el perro: "'+ row.Nombre+'" de la base de datos.\n¿Seguro?',function(r){
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
 *@param {string} ID identificador del datagrid que se actualiza
 *@param {object} guia: datos del guia
 */
function assignPerroToGuia(dg,guia) {
	// clean previous dialog data
	$('#chperros-header').form('clear');
	$('#chperros-Search').combogrid('clear');
	$('#chperros-form').form('clear'); 
	// set up default guia data
	$('#chperros-newGuia').val(guia.ID);
	$('#chperros-Operation').val('update');
	// desplegar ventana y ajustar textos
	$('#chperros-title').text('Buscar perro / Declarar un nuevo perro y asignarlo a '+guia.Nombre);
	$('#chperros-dialog').dialog('open').dialog('setTitle',"Reasignar / Declarar perro");
	$('#chperros-okBtn').one('click',function () { $(dg).datagrid('reload'); } );
	$('#chperros-newPeBtn').one('click',function () { $(dg).datagrid('reload'); } );
}

/**
* Abre el formulario para anyadir perros a un guia
* @param {string} dg datagrid ID de donde se obtiene el perro
* @param {object} guia: datos del guia
* @param {function} onAccept what to do (only once) when window gets closed
*/
function editPerroFromGuia(dg,guia) {
	// try to locate which dog has been selected 
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Error","!No ha seleccionado ningún perro!","warning");
    	return; // no way to know which dog is selected
    }
    // add extra required data to form dialog
    row.Operation='update';
    $('#perros-form').form('load',row);	// load form with row data. onLoadSuccess will fix comboboxes
    // finally display composed data
    $('#perros-dialog').dialog('open').dialog('setTitle','Modificar datos del perro asignado a '+guia.Nombre);
	$('#perros-okBtn').one('click',function () { $(dg).datagrid('reload'); } );
}

/**
 * Quita la asignacion del perro marcado al guia indicado
 * @param {string} dg datagrid ID de donde se obtiene el perro
 * @param {object} guia: datos del guia
 * @param {function} onAccept what to do (only once) when window gets closed
 */
function delPerroFromGuia(dg,guia) {
    var row = $(dg).datagrid('getSelected');
    if (!row){
    	$.messager.alert("Error","!No ha seleccionado ningún perro!","warning");
    	return; // no way to know which dog is selected
    }
    $.messager.confirm('Confirm',"Borrar asignacion del perro '"+row.Nombre+"' al guia '"+guia.Nombre+"' ¿Seguro?'",function(r){
        if (r){
            $.get('/agility/server/database/dogFunctions.php',{Operation:'orphan',ID:row.ID},function(result){
                if (result.success){
                	$(dg).datagrid('reload');
                } else {
                    $.messager.show({title: 'Error', msg: result.errorMsg, width: 300,height:200 });
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
                // reload the dog data from inscripciones (if any)
            	if (isDefined('listaNoInscritos')) listaNoInscritos();
    	        // close the dialog
                $('#perros-dialog').dialog('close'); 
            }
        }
    });
}
