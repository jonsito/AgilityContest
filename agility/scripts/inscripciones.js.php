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
$config =new Config();
?>

//***** gestion de inscripciones de una prueba	*****************************************************

/**
 *Abre dialogo de registro de inscripciones
 *@param {string} dg datagrid ID de donde se obtiene el id de la prueba
 *@param {string} def default value to insert into search field
 *@param {function} onAccept what to do when a new inscription is created
 */
function newInscripcion(dg,def,onAccept) {
	$('#new_inscripcion-dialog').dialog('open').dialog('setTitle','<?php _e('Nueva(s) inscripciones');?>');
	// let openEvent on dialog fire up form setup
	if (onAccept!==undefined)$('#new_inscripcion-okBtn').one('click',onAccept);
}

function editInscripcion() {
	if ($('#inscripciones-datagrid-search').is(":focus")) return; // on enter key in search input ignore
	// obtenemos datos de la inscripcion seleccionada
	var row= $('#inscripciones-datagrid').datagrid('getSelected');
    if (!row) {
    	$.messager.alert("No selection","<?php _e('!No ha seleccionado ninguna inscripción!');?>","warning");
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
    	$.messager.alert("No selection","<?php _e('!No ha seleccionado ninguna inscripción!');?>","warning");
    	return; // no hay ninguna inscripcion seleccionada. retornar
    }
	$.messager.confirm('Confirm',
			"<p><b><?php _e('Importante');?>:</b></p>" +
			"<p><?php _e('Si decide borrar la inscripci&oacute;n');?> <br/>" +
			"<b><?php _e('se perder&aacute;n</b> todos los datos y resultados de este participante');?><br />" +
			"<?php _e('que afecten a jornadas que no hayan sido marcadas como <em>Cerradas</em>');?><br/>" +
			"<?php _e('Desea realmente borrar la inscripción seleccionada?');?></p>",
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
		}).window({width:475});
}

/**
 * Ask for commit new inscripcion to server
 * @param {string} dg datagrid to retrieve selections from
 */
function insertInscripcion(dg) {
	var selectedRows= $(dg).datagrid('getSelections');
	var count=1;
	var size=selectedRows.length;
	if(size==0) {
    	$.messager.alert("No selection","<?php _e('!No ha marcado ningún perro para proceder a su inscripción!');?>","warning");
    	return; // no hay ninguna inscripcion seleccionada. retornar
	}
	if (authInfo.Perms>2) {
    	$.messager.alert("No permission","<?php _e('Sesi&oacute;n con insuficientes permisos para realizar inscripciones');?>","error");
    	return; // no tiene permiso para realizar inscripciones. retornar
	}
	$('#new_inscripcion-progresswindow').window('open');
	$.each(selectedRows, function(index,row) {
		$('#new_inscripcion-progresslabel').text("<?php _e('Inscribiendo a');?>: "+row.Nombre);
		$('#new_inscripcion-progressbar').progressbar('setValue',count*(100/size));
		$.ajax({
	        async: false,
	        cache: false,
	        timeout: 10000, // 10 segundos
			type:'GET',
			url:"/agility/server/database/inscripcionFunctions.php",
			dataType:'json',
			data: {
				Prueba: workingData.prueba,
				Operation: 'insert',
				Perro: row.ID,
				Jornadas: $('#new_inscripcion-Jornadas').val(),
				Celo: $('#new_inscripcion-Celo').val(),
				Equipo: $('#new_inscripcion-Equipo').combogrid('getValue'),
				Pagado: $('#new_inscripcion-Pagado').val()
			}
		});
		count++;
	});
	$('#new_inscripcion-progresswindow').window('close');
    // notice that some of these items may fail if dialog is not deployed. just ignore
	// foreach finished, clean, close and refresh
	$(dg).datagrid('clearSelections');
	listaNoInscritos();
    // reload the inscripciones table
	$('#inscripciones-datagrid').datagrid('reload');
}

/**
 * Reajusta los dorsales de los perros inscritos ordenandolos por club,categoria,grado,nombre
 * @param idprueba ID de la prueba
 */
function reorderInscripciones(idprueba) {
	$.ajax({
        async: false,
        cache: false,
        timeout: 10000, // 10 segundos
		type:'GET',
		url:"/agility/server/database/inscripcionFunctions.php",
		dataType:'json',
		data: {
			Prueba: idprueba,
			Operation: 'reorder'
		},
		success: function(data) {$('#inscripciones-datagrid').datagrid('reload'); }
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
		'<?php _e('Selecciona modelo');?>',
		'<?php _e('Selecciona el tipo de documento a generar');?>:',
		{ 0:'<?php _e('Listado simple');?>',1:'<?php _e('Cat&aacute;logo');?>',2:'<?php _e('Estad&iacute;sticas');?>'}, 
		function(r){
			if (!r) return;
			var mode=parseInt(r);
			$.fileDownload(
				'/agility/server/pdf/print_inscritosByPrueba.php',
				{
					httpMethod: 'GET',
					data: { Prueba: workingData.prueba, Mode: mode },
			        preparingMessageHtml: "<?php _e('Imprimiendo inscripciones; por favor espere...');?>",
			        failMessageHtml: "<?php _e('Problemas en la generaci&oacute;n del fichero PDF. Por favor reintente');?>"
				}
			);
		}
	).window({width:250});
    return false; //this is critical to stop the click event which will trigger a normal file download!
};

