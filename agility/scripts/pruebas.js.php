/*
 pruebas.js

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

//***** gestion de pruebas		*********************************************************


/**
 * Recalcula el formulario de pruebas anyadiendo parametros de busqueda
 */
function doSearchPrueba() {
	var includeClosed= $('#pruebas-openBox').is(':checked')?'1':'0';
	// reload data adding search criteria
    $('#pruebas-datagrid').datagrid('load',{
        where: $('#pruebas-search').val(),
        closed: includeClosed
    });
}

/**
 *Open dialogo de creacion de pruebas
 *@param {string} dg datagrid ID de donde se obtiene la prueba
 *@param {string} def default value to insert into Nombre 
 *@param {function} onAccept what to do when a new prueba is created
 */
function newPrueba(dg,def,onAccept){
	$('#pruebas-dialog').dialog('open').dialog('setTitle','<?php _e('Nueva Prueba'); ?>');
	$('#pruebas-form').form('clear');
	switch(parseInt(workingData.federation)) {
		case 0:$('#pruebas-RSCE').prop('checked',true); break;
		case 1:$('#pruebas-RFEC').prop('checked',true); break;
		case 2:$('#pruebas-UCA').prop('checked',true); break;
		default: alert("Invalid federation.</br>Defaulting to RSCE");
	}
	if (!strpos(def,"Buscar")) $('#pruebas-Nombre').val(def);// fill prueba Name
	$('#pruebas-Operation').val('insert');
	if (onAccept!==undefined)$('#pruebas-okBtn').one('click',onAccept);
}

/**
 * If there are any inscriptions in a contest, disable change of federation
 * @param id
 * @returns
 */
function hasInscripciones(id,callback) {
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/inscripcionFunctions.php',
        data: { Operation: 'howmany', Prueba: id },
        dataType: 'json',
        // beforeSend: function(jqXHR,settings){ return frm.form('validate'); },
        success: function (result) {
            if (result.errorMsg){ 
            	$.messager.show({width:300, height:200, title:'Error',msg: result.errorMsg });
            } else {
            	var flag= (parseInt(result.Inscritos)!=0)?true:false;
            	callback(flag);
            }
        }
    });
}

/**
 * Open dialogo de modificacion de pruebas
 * @param {string} dg datagrid ID de donde se obtiene la prueba
 */
function editPrueba(dg){
	if ($('#pruebas-datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("Edit Error"); ?>','<?php _e("There is no contest selected"); ?>',"warning");
    	return; // no way to know which prueba is selected
    }
    $('#pruebas-dialog').dialog('open').dialog('setTitle','<?php _e('Modify contest data'); ?>');
    $('#pruebas-form').form('load',row);
}

/**
 * Ask server routines for add/edit a prueba into BBDD
 */
function savePrueba() {
	// take care on bool-to-int translation from checkboxes to database
    $('#pruebas-Cerrada').val( $('#pruebas-Cerrada').is(':checked')?'1':'0');
    var frm = $('#pruebas-form');
    if (!frm.form('validate')) return; // don't call inside ajax to avoid override beforeSend()
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/pruebaFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        // beforeSend: function(jqXHR,settings){ return frm.form('validate'); },
        success: function (result) {
            if (result.errorMsg){ 
            	$.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: result.errorMsg });
            } else {
                $('#pruebas-dialog').dialog('close');        // close the dialog
                $('#pruebas-datagrid').datagrid('reload');    // reload the prueba data
            }
        }
    });
}

/**
 * Delete data related with a prueba in BBDD
 * @param {string} dg datagrid ID de donde se obtiene la prueba
 */
function deletePrueba(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("Delete Error"); ?>','<?php _e("There is no contest selected"); ?>',"warning");
    	return; // no way to know which prueba is selected
    }
    $.messager.confirm('<?php _e('Confirm'); ?>',
    		"<p><strong><?php _e('Notice'); ?>:</strong></p>" +
    		'<?php _e("<p>By deleting this contest <b>youll loose</b> every associated data and scores"); ?>' +
    		"</p><p><?php _e('Do you really want to delete this contest'); ?>?</p>",function(r){
        if (r){
            $.get('/agility/server/database/pruebaFunctions.php',{Operation:'delete',ID:row.ID},function(result){
                if (result.success){
                    $(dg).datagrid('reload');    // reload the prueba data
                } else {
                    $.messager.show({ width:300, height:200, title:'<?php _e('Error'); ?>', msg:result.errorMsg });
                }
            },'json');
        }
    });
}

// ***** gestion de jornadas	*********************************************************

/**
 * Edita la jornada seleccionada
 *@param pruebaID objeto que contiene los datos de la prueba
 *@param row datos de la jornada
 */
function editJornadaFromPrueba(pruebaID,row) {
    if (!row) {
        $.messager.alert('<?php _e("No selection"); ?>','<?php _e("There is no journey selected"); ?>',"warning");
    	return; // no hay ninguna jornada seleccionada. retornar
    }
    if (row.Cerrada==true) { // no permitir la edicion de pruebas cerradas
    	$.messager.alert('<?php _e("Invalid selection"); ?>','<?php _e("Cannot edit a journey stated as closed"); ?>',"error");
        return;
    }
    // todo ok: abrimos ventana de dialogo
    $('#jornadas-dialog').dialog('open').dialog('setTitle','<?php _e('Modify journey data'); ?>');
    $('#jornadas-form').form('load',row); // will trigger onLoadSuccess in dlg_pruebas
}

/**
 * Cierra la jornada seleccionada
 *@param pruebaID objeto que contiene los datos de la prueba
 *@param datagridID identificador del datagrid del que se toman los datos
 */
function closeJornadaFromPrueba(pruebaID,datagridID) {
	// obtenemos datos de la JORNADA seleccionada
	var row= $(datagridID).datagrid('getSelected');
    // var row = $('#jornadas-datagrid-'+prueba.ID).datagrid('getSelected');
    if (!row) {
        $.messager.alert('<?php _e("No selection"); ?>','<?php _e("There is no journey selected"); ?>',"warning");
    	return; // no hay ninguna jornada seleccionada. retornar
    }
    if (row.Cerrada==true) { // no permitir la edicion de pruebas cerradas
    	$.messager.alert('<?php _e("Invalid selection"); ?>','<?php _e("Cannot close an already closed journey"); ?>',"error");
        return;
    }
    // $.messager.defaults={ ok:"Cerrar", cancel:"Cancelar" };
    var w=$.messager.confirm(
    		'<?php _e("Notice"); ?>',
    		'<?php _e("Setting a journey as closed"); ?><br/>' +
    		'<?php _e("Youll be no longer able to edit,delete or modify,"); ?><br/>' +
    		'<?php _e("inscriptions or scores"); ?><br/>' +
    		'<?php _e("Continue?"); ?>',
    		function(r) { 
    	    	if(r) {
    	            $.get('/agility/server/database/jornadaFunctions.php',{Operation:'close',ID:row.ID},function(result){
    	                if (result.success){
    	                    $(datagridID).datagrid('reload');    // reload the pruebas data
    	                } else {
    	                    $.messager.show({ width:300, height:200, title:'<?php _e('Error'); ?>', msg:result.errorMsg });
    	                }
    	            },'json');
    	    	}
    		});
    w.window('resize',{width:400,height:150}).window('center');
}

/**
 * Ask for commit new/edit jornada to server
 */
function saveJornada(){
	// take care on bool-to-int translation from checkboxes to database
    if ($('#jornadas-PreAgilityChk').is(':checked')) {
        $('#jornadas-PreAgility').val( ($('#jornadas-MangasPreAgility').val()==1)?'1':'0');
        $('#jornadas-PreAgility2').val( ($('#jornadas-MangasPreAgility').val()==2)?'1':'0');
    } else {
    	$('#jornadas-PreAgility').val(0);
    	$('#jornadas-PreAgility2').val(0);
    }
    $('#jornadas-Grado1').val( $('#jornadas-Grado1').is(':checked')?'1':'0');
    $('#jornadas-Grado2').val( $('#jornadas-Grado2').is(':checked')?'1':'0');
    $('#jornadas-Grado3').val( $('#jornadas-Grado3').is(':checked')?'1':'0');
    $('#jornadas-Open').val( $('#jornadas-Open').is(':checked')?'1':'0');    
    if ($('#jornadas-EquiposChk').is(':checked')) {
        $('#jornadas-Equipos3').val( ($('#jornadas-MangasEquipos').val()==1)?'1':'0');
        $('#jornadas-Equipos4').val( ($('#jornadas-MangasEquipos').val()==2)?'1':'0');
    } else {
    	$('#jornadas-Equipos3').val(0);
    	$('#jornadas-Equipos4').val(0);
    }
    $('#jornadas-KO').val( $('#jornadas-KO').is(':checked')?'1':'0');
    $('#jornadas-Especial').val( $('#jornadas-Especial').is(':checked')?'1':'0');
    $('#jornadas-Cerrada').val( $('#jornadas-Cerrada').is(':checked')?'1':'0');
    // handle fecha
    // do normal submit
    $('#jornadas-Operation').val('update');
    
    var frm = $('#jornadas-form');
    if (!frm.form('validate')) return; // don't call inside ajax to avoid override beforeSend()
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/jornadaFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){ 
            	$.messager.show({width:300, height:200, title:'Error',msg: result.errorMsg });
            } else {
            	var id=$('#jornadas-Prueba').val();
                $('#jornadas-dialog').dialog('close');        // close the dialog
                // notice that some of these items may fail if dialog is not deployed. just ignore
                $('#jornadas-datagrid-'+id).datagrid('reload',{ Prueba:id , Operation:'select' }); // reload the prueba data
                $('#inscripciones-jornadas').datagrid('reload');    // reload the prueba data
            }
        }
    });
}

/**
 * Comprueba si se puede seleccionar la prueba elegida en base a las mangas pre-existentes
 * @param {checkbox} id checkbox que se acaba de (de) seleccionar
 * @param {mask} mascara de la prueba marcada (seleccionada o de-seleccionada)
 * 0x0001, 'PreAgility 1 Manga'
 * 0x0002, 'PreAgility 2 Mangas'
 * 0x0004, 'Grado1',
 * 0x0008, 'Grado2',
 * 0x0010, 'Grado3',
 * 0x0020, 'Open',
 * 0x0040, 'Equipos3',
 * 0x0080, 'Equipos4',
 * 0x0100, 'KO',
 * 0x0200, 'Especial'
 */
function checkPrueba(id,mask) {
	var pruebas=0;
	// mascara de pruebas seleccionadas
	if ( $('#jornadas-PreAgilityChk').is(':checked') ) {
		$('#jornadas-MangasPreAgility').prop('disabled',false);
		pruebas |= 0x0003; // 1:manga simple 2:dos mangas
	} else {
		$('#jornadas-MangasPreAgility').prop('disabled','disabled');
	}
	// pruebas |= $('#jornadas-PreAgility').is(':checked')?0x0001:0;
	// pruebas |= $('#jornadas-PreAgility2').is(':checked')?0x0002:0;
	pruebas |= $('#jornadas-Grado1').is(':checked')?0x0004:0;
	pruebas |= $('#jornadas-Grado2').is(':checked')?0x0008:0;
	pruebas |= $('#jornadas-Grado3').is(':checked')?0x0010:0;
	pruebas |= $('#jornadas-Open').is(':checked')?0x0020:0;

	if ( $('#jornadas-EquiposChk').is(':checked') ) {
		$('#jornadas-MangasEquipos').prop('disabled',false);
		pruebas |= 0x00C0; // eq3:64 eq4:128
	} else {
		$('#jornadas-MangasEquipos').prop('disabled','disabled');
	}
	// pruebas |= $('#jornadas-Equipos3').is(':checked')?0x0040:0;
	// pruebas |= $('#jornadas-Equipos4').is(':checked')?0x0080:0;
	pruebas |= $('#jornadas-KO').is(':checked')?0x0100:0;
	if ( $('#jornadas-Especial').is(':checked') ) {
		$('#jornadas-Observaciones').prop('disabled',false);
		pruebas |= 0x0200;
	} else {
		$('#jornadas-Observaciones').prop('disabled','disabled');
	}
	// si no hay prueba seleccionada no hacer nada
	if (pruebas==0) return;
	// si estamos seleccionando una prueba ko/open/equipos, no permitir ninguna otra
	if ( (mask & 0x01E0) != 0 ) {
		if (mask!=pruebas) {
			$.messager.alert('<?php _e('Error'); ?>','<?php _e('KO, Open, or team rounds must be declared in a separate journey'); ?>','error');
			$(id).prop('checked',false);
			if (id==='#jornadas-EquiposChk') $('#jornadas-MangasEquipos').prop('disabled','disabled');
		}
	} else {
		if ( (pruebas & 0x01E0) != 0 ) {
			$.messager.alert('<?php _e('Error'); ?>','<?php _e('You cannot add additional rounds when KO,Open or team rounds are already declared in a journey'); ?>','error');
			$(id).prop('checked',false);
			if (id==='#jornadas-PreAgilityChk') $('#jornadas-MangasPreAgility').prop('disabled','disabled');
			if (id==='#jornadas-Especial') $('#jornadas-Observaciones').prop('disabled','disabled');
		}
	}
}
