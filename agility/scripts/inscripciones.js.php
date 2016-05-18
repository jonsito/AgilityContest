/*
 inscripciones.js

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

//***** gestion de inscripciones de una prueba	*****************************************************

/**
 *Abre dialogo de registro de inscripciones
 *@param {string} dg datagrid ID de donde se obtiene el id de la prueba
 *@param {string} def default value to insert into search field
 *@param {function} onAccept what to do when a new inscription is created
 */
function newInscripcion(dg,def,onAccept) {
	$('#new_inscripcion-dialog').dialog('open').dialog('setTitle','<?php _e('New inscriptions'); ?>');
	// let openEvent on dialog fire up form setup
	if (onAccept!==undefined)$('#new_inscripcion-okBtn').one('click',onAccept);
}

function editInscripcion() {
	if ($('#inscripciones-datagrid-search').is(":focus")) return; // on enter key in search input ignore
	// obtenemos datos de la inscripcion seleccionada
	var row= $('#inscripciones-datagrid').datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("No selection"); ?>','<?php _e("There is no inscription(s) selected"); ?>',"warning");
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
            	$.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: result.errorMsg });
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
		$.messager.alert('<?php _e("No selection"); ?>','<?php _e("There is no inscription(s) selected"); ?>',"warning");
    	return; // no hay ninguna inscripcion seleccionada. retornar
    }
	$.messager.confirm('Confirm',
			"<p><b><?php _e('Notice'); ?>:</b></p>" +
			"<p><?php _e('If you delete this inscription'); ?> <br/>" +
			"<?php _e('<b>youll loose</b> every related results and data on this contest'); ?><br />" +
			"<?php _e('afecting journeys not marked as <em>closed</em>'); ?><br/>" +
			"<?php _e('Really want to delete selected inscription'); ?>?</p>",
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
		$('#new_inscripcion-progresslabel').text('<?php _e("Enrolling"); ?>'+": "+rows[index].Nombre);
		$('#new_inscripcion-progressbar').progressbar('setValue', (100.0*(index+1)/size).toFixed(2));
		$.ajax({
			cache: false,
			timeout: 20000, // 20 segundos
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
		$.messager.alert('<?php _e("No selection"); ?>','<?php _e("There is no marked dog to be inscribed"); ?>',"warning");
    	return; // no hay ninguna inscripcion seleccionada. retornar
	}
	if (ac_authInfo.Perms>2) {
    	$.messager.alert('<?php _e("No permission"); ?>','<?php _e("Current user has not enought permissions to handle inscriptions"); ?>',"error");
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
        timeout: 60000, // 60 segundos
		type:'GET',
		url:"/agility/server/database/inscripcionFunctions.php",
		dataType:'json',
		data: {
			Prueba: idprueba,
			Operation: 'reorder'
		},
		success: function(data) {
			if(data.errorMsg) {
				$.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: data.errorMsg });
			} else {
				$('#inscripciones-datagrid').datagrid('reload');
			}
            $.messager.progress('close');
        },
		error:function(jqXHR, textStatus, errorThrown) {
            // console.log(textStatus, errorThrown);
			$.messager.progress('close');
		}
	});
}

/**
 * cambia el dorsal
 * @param idprueba ID de la prueba
 */
function setDorsal() {
	var row = $('#inscripciones-datagrid').datagrid('getSelected');
	if (!row) {
		$.messager.alert('<?php _e("No selection"); ?>','<?php _e("There is no inscription(s) selected"); ?>',"warning");
		return; // no hay ninguna inscripcion seleccionada. retornar
	}
	$.messager.prompt(
		'<?php _e("Set dorsal"); ?>',
		'<?php _e("Please type new dorsal<br />If already assigned, <br/>dorsals will be swapped"); ?>',
		function(r) {
			if (!r || isNaN(parseInt(r))) return;
			$.messager.progress({title:'<?php _e("Set dorsal"); ?>',text:'<?php _e("Setting new dorsal...");?>'});
			$.ajax({
				cache: false,
				timeout: 60000, // 60 segundos
				type:'GET',
				url:"/agility/server/database/inscripcionFunctions.php",
				dataType:'json',
				data: {
					Prueba: row.Prueba,
					Perro: row.Perro,
					Dorsal: row.Dorsal,
					NewDorsal: parseInt(r),
					Operation: 'setdorsal'
				},
				success: function(data) {
					if(data.errorMsg) {
						$.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: data.errorMsg });
					} else {
						$('#inscripciones-datagrid').datagrid('reload');
					}
					$.messager.progress('close');
				},
				error:function(jqXHR, textStatus, errorThrown) {
					// console.log(textStatus, errorThrown);
					$.messager.progress('close');
				}
			});
		}
	);
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
	// en el caso de que haya alguna jornada seleccionada.
	// anyadir al menu la posibilidad de imprimir solo los inscritos en dicha jornada
	var options= { 0:'<?php _e('Simple listing'); ?>',1:'<?php _e('Catalogue'); ?>',2:'<?php _e('Statistics'); ?>',4:'<?php _e("Export to Excel");?>'};
	// buscamos la jornada seleccionada
	var row=$('#inscripciones-jornadas').datagrid('getSelected');
    var jornada=0;
	// si hay jornada seleccionada la anyadimos a la lista
	if (row!==null && row.Nombre!=="-- Sin asignar --") {
		options[3]='<?php _e('Inscriptions for journey'); ?>: "'+row.Nombre+'"';
        jornada=row.ID;
	}
	$.messager.radio(
		'<?php _e('Select form'); ?>',
		'<?php _e('Select type of document to be generated'); ?>:',
		options,
		function(r){
			if (!r) return;
			var opt=parseInt(r);
			var url='/agility/server/pdf/print_inscritosByPrueba.php';
			if (opt==4) url='/agility/server/excel/inscription_writer.php';
			$.fileDownload(
					url,
					{
						httpMethod: 'GET',
						data: {
                            Prueba: workingData.prueba,
                            Jornada: jornada,
                            Mode: opt
                        },
						preparingMessageHtml: '<?php _e("Printing inscriptions. Please wait"); ?> ...',
						failMessageHtml: '<?php _e("There was a problem generating your report, please try again"); ?>.'
					}
			);
		}
	).window('resize',{width:(jornada==0)?250:350});
	return false; //this is critical to stop the click event which will trigger a normal file download!
}
