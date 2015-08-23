/*
 inscripciones.js

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

//***** gestion de inscripciones de una prueba	*****************************************************

/**
 *Abre dialogo de registro de inscripciones
 *@param {string} dg datagrid ID de donde se obtiene el id de la prueba
 *@param {string} def default value to insert into search field
 *@param {function} onAccept what to do when a new inscription is created
 */
function newInscripcion(dg,def,onAccept) {
	$('#new_inscripcion-dialog').dialog('open').dialog('setTitle','Nueva(s) inscripciones');
	// let openEvent on dialog fire up form setup
	if (onAccept!==undefined)$('#new_inscripcion-okBtn').one('click',onAccept);
}

function editInscripcion() {
	if ($('#inscripciones-datagrid-search').is(":focus")) return; // on enter key in search input ignore
	// obtenemos datos de la inscripcion seleccionada
	var row= $('#inscripciones-datagrid').datagrid('getSelected');
    if (!row) {
    	$.messager.alert("No selection","!No ha seleccionado ninguna inscripción!","warning");
    	return; // no hay ninguna inscripcion seleccionada. retornar
    }
    row.Operation='update';
    $('#edit_inscripcion-form').form('load',row);
    $('#edit_inscripcion-dialog').dialog('open');
}

/**
 * Save Inscripcion being edited, as result of doneBtn.onClick()
 * On success refresh every related datagrids
 * if (done) close dialog, else reload
 */
function saveInscripcion(close) {
	// make sure that "Celo" field has correct value
	$('#edit_inscripcion-Celo').val( $('#edit_inscripcion-Celo2').is(':checked')?'1':'0');
    var frm = $('#edit_inscripcion-form');
    if (!frm.form('validate')) return;
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/inscripcionFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){ 
            	$.messager.show({width:300, height:200, title:'Error',msg: result.errorMsg });
            } else {
            	// on save done refresh related data/combo grids and close dialog
            	$('#inscripciones-datagrid').datagrid('reload');
            	if (close) $('#edit_inscripcion-dialog').dialog('close');
            }
        }
    });
}

/**
 * Delete data related with an inscription in BBDD
 */
function deleteInscripcion() {
	var row = $('#inscripciones-datagrid').datagrid('getSelected');    
	if (!row) {
    	$.messager.alert("No selection","!No ha seleccionado ninguna inscripcion!","warning");
    	return; // no hay ninguna inscripcion seleccionada. retornar
    }
	$.messager.confirm('Confirm',
			"<p><b>Importante:</b></p>" +
			"<p>Si decide borrar la inscripcion <br/>" +
			"<b>se perder&aacute;n</b> todos los datos y resultados de este participante<br />" +
			"que afecten a jornadas que no hayan sido marcadas como <em>Cerradas</em><br/>" +
			"Desea realmente borrar la inscripción seleccionada?</p>",
			function(r){
				if (r){
					$.get(
						// URL
						'/agility/server/database/inscripcionFunctions.php',
						// arguments
						{ 
							Operation:'delete',
							ID:row.ID, // id de la inscripcion
							Perro:row.Perro, // id del perro
							Prueba:row.Prueba // id de la prueba
						},
						// on Success function
						function(result){
							if (result.success) {
								$('#inscripciones-datagrid').datagrid('reload',{ // reload the inscripciones table
									where: $('#inscripciones-search').val()
								});
							} else {
								$.messager.show({ width:300, height:200, title:'Error', msg:result.errorMsg });
							}
						},
						// expected datatype format for response
						'json'
					);
				} // if (r)
		}).window('resize',{width:475});
}

/**
 * Ask for commit new inscripcion to server
 * @param {string} dg datagrid to retrieve selections from
 */
function insertInscripcion(dg) {
	function handleInscription(rows,index,size) {
		if (index>=size){
            // recursive call finished, clean, close and refresh
            pwindow.window('close');
            $(dg).datagrid('clearSelections');
            reloadWithSearch('#new_inscripcion-datagrid','noinscritos');
            reloadWithSearch('#inscripciones-datagrid','inscritos');
			return;
		}
		$('#new_inscripcion-progresslabel').text("Inscribiendo a: "+rows[index].Nombre);
		$('#new_inscripcion-progressbar').progressbar('setValue', (100.0*(index+1)/size).toFixed(2));
		$.ajax({
			cache: false,
			timeout: 10000, // 10 segundos
			type:'GET',
			url:"/agility/server/database/inscripcionFunctions.php",
			dataType:'json',
			data: {
				Prueba: workingData.prueba,
				Operation: 'insert',
				Perro: rows[index].ID,
				Jornadas: $('#new_inscripcion-Jornadas').val(),
				Celo: $('#new_inscripcion-Celo').val(),
				Pagado: $('#new_inscripcion-Pagado').val()
			},
			success: function(result) {
                handleInscription(rows,index+1,size);
            }
		});
	}

	var pwindow=$('#new_inscripcion-progresswindow');
	var selectedRows= $(dg).datagrid('getSelections');
	var size=selectedRows.length;
	if(size==0) {
    	$.messager.alert("No selection","!No ha marcado ningún perro para proceder a su inscripción!","warning");
    	return; // no hay ninguna inscripcion seleccionada. retornar
	}
	if (authInfo.Perms>2) {
    	$.messager.alert("No permission","Sesion con insuficiente permiso para realizar inscripciones","error");
    	return; // no tiene permiso para realizar inscripciones. retornar
	}
	pwindow.window('open');
	handleInscription(selectedRows,0,size);
}

/**
 * Reajusta los dorsales de los perros inscritos ordenandolos por club,categoria,grado,nombre
 * @param idprueba ID de la prueba
 */
function reorderInscripciones(idprueba) {
    $.messager.progress({title:'<?php _e("Sort"); ?>',text:'<?php _e("Re-ordering Dorsals");?>'});
	$.ajax({
        cache: false,
        timeout: 10000, // 10 segundos
		type:'GET',
		url:"/agility/server/database/inscripcionFunctions.php",
		dataType:'json',
		data: {
			Prueba: idprueba,
			Operation: 'reorder'
		},
		success: function(data) {
            $('#inscripciones-datagrid').datagrid('reload');
            $.messager.progress('close');
        }
	});
}

/**
 * Comprueba si un participante se puede o no inscribir en una jornada
 * @param {object} jornada, datos de la jornada
 */
function canInscribe(jornada) {
	var result=true;
	if (jornada.Cerrada==1) result=false;
	if (jornada.Nombre === '-- Sin asignar --') result=false;
	return result;
}

/**
 * Imprime las inscripciones
 * @returns {Boolean} true on success, otherwise false
 */
function printInscripciones() {
	$.messager.radio(
		'Selecciona modelo',
		'Selecciona el tipo de documento a generar:',
		{ 0:'Listado simple',1:'Catálogo',2:'Estadisticas'}, 
		function(r){
			if (!r) return;
			var mode=parseInt(r);
			$.fileDownload(
					'/agility/server/pdf/print_inscritosByPrueba.php',
					{
						httpMethod: 'GET',
						data: { Prueba: workingData.prueba, Mode: mode },
						preparingMessageHtml: "Imprimiendo inscripciones; por favor espere...",
						failMessageHtml: "There was a problem generating your report, please try again."
					}
			);
		}
	).window('resize',{width:250});
	return false; //this is critical to stop the click event which will trigger a normal file download!
}
