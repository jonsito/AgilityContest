/*
 guias.js

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

// ***** gestion de guias		*********************************************************

/**
 * Abre el formulario para anyadir guias a un club
 *@param {String} dgname: Identificador del elemento ( datagrid) desde el que se invoca esta funcion
 *@param {object} club: datos del club
 */
function assignGuiaToClub(dgname,club) {
	// clear data forms
	$('#chguias-header').form('clear'); // erase header form
	$('#chguias-Search').combogrid('clear'); // reset header combogrid
	$('#chguias-form').form('clear'); // erase data form
	// fill default values
	$('#chguias-newClub').val(club.ID); // id del club to assign
	$('#chguias-Operation').val('update'); // operation
    $('#chguias-parent').val(dgname);

    // finalmente desplegamos el formulario y ajustamos textos
	$('#chguias-title').text('<?php _e('Reassign/Declare a handler as belonging to club'); ?>'+' '+club.Nombre);
	$('#chguias-dialog').dialog('open').dialog('setTitle','<?php _e('Assign/Register a handler'); ?>'+' - '+fedName(workingData.federation));
}

/**
 * Abre el formulario de edicion de guias para cambiar los datos de un guia preasignado a un club
 * @param {string} dg datagrid ID de donde se obtiene el guia
 * @param {object} club datos del club
 */
function editGuiaFromClub(dg, club) {
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("Delete Error"); ?>','<?php _e("There is no handler selected"); ?>',"warning");
    	return; // no way to know which guia is selected
    }
    // add extra needed parameters to dialog
    row.Club=club.ID;
    row.NombreClub=club.Nombre;
    row.Operation='update';
    $('#guias-form').form('load',row);
    $('#guias-dialog').dialog('open').dialog('setTitle','<?php _e('Modify data on handler belonging to club'); ?>'+' '+club.Nombre+' - '+fedName(workingData.federation));
	// on click OK button, close dialog and refresh data
	$('#guias-okBtn').one('click',function () { $(dg).datagrid('reload'); } ); 
}

/**
 * Quita la asignacion del guia marcado al club indicado
 * Invocada desde el menu de clubes
 * @param {string} dg datagrid ID de donde se obtiene el guia
 * @param {object} club datos del club
 * @param {function} onAccept what to do (only once) when window gets closed
 */
function delGuiaFromClub(dg,club) {
    var row = $(dg).datagrid('getSelected');
    if (!row){
    	$.messager.alert('<?php _e("Delete Error"); ?>','<?php _e("There is no handler selected"); ?>',"warning");
    	return; // no way to know which guia is selected
    }
    $.messager.confirm('<?php _e('Confirm'); ?>','<?php _e("Delete assignation for handler"); ?>'+" '"+row.Nombre+"' "+'<?php _e("to club"); ?>'+" '"+club.Nombre+"' "+'<?php _e("Sure?"); ?>'+"'",function(r){
        if (r){
            $.get('/agility/server/database/guiaFunctions.php',{'Operation':'orphan','ID':row.ID},function(result){
                if (result.success){
                	$(dg).datagrid('reload');
                } else {
                	// show error message
                    $.messager.show({ title: '<?php _e('Error'); ?>', width: 300, height: 200, msg: result.errorMsg });
                }
            },'json');
        }
    });
}

function reload_guiasDatagrid() {
	var w=$('#guias-datagrid-search').val();
	if (strpos(w,"Buscar")) w='';
	$('#guias-datagrid').datagrid('load',{ Operation: 'select', where: w, Federation: workingData.federation });
}

/**
 * Abre el dialogo para crear un nuevo guia
 * @param {string} def valor por defecto para el campo nombre
 * @param {function} onAccept what to do (only once) when window gets closed
 */
function newGuia(def,onAccept){
	$('#guias-dialog').dialog('open').dialog('setTitle','<?php _e('New handler'); ?>'+' - '+fedName(workingData.federation));
	$('#guias-form').form('clear');
	if (!strpos(def,"Buscar")) $('#guias-Nombre').val(def.capitalize());
	$('#guias-Operation').val('insert');
	$('#guias-Parent').val('');
	if (onAccept!==undefined)
		$('#guias-okBtn').one('click',onAccept);
}

/**
 * Abre el dialogo para editar un guia ya existente
 * @param {string} dg datagrid ID de donde se obtiene el guia
 */
function editGuia(dg){
	if ($('#guias-datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("Edit Error"); ?>','<?php _e("There is no handler selected"); ?>',"warning");
    	return; // no way to know which dog is selected
    }
    $('#guias-dialog').dialog('open').dialog('setTitle','<?php _e('Modify handler data'); ?>'+' - '+fedName(workingData.federation));
    // add extra required parameters to dialog
    row.Parent='';
    row.Operation='update';
    // stupid trick to make dialog's clubs combogrid display right data
    $('#guias-form').form('load',row); // load row data into guia edit form
    // on accept, display correct data
    $('#guias-okBtn').one('click',reload_guiasDatagrid);
}

/**
 * Borra de la BBDD los datos del guia seleccionado. 
 * Invocada desde el menu de guias
 * @param {string} dg datagrid ID de donde se obtiene el guia
 */
function deleteGuia(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert('<?php _e("Delete Error"); ?>','<?php _e("There is no handler selected"); ?>',"warning");
    	return; // no way to know which dog is selected
    }
    if (row.ID==1) {
    	$.messager.alert('<?php _e("Delete Error"); ?>','<?php _e("This entry cannot be deleted"); ?>',"error");
    	return; // cannot delete default entry
    }
    $.messager.confirm('<?php _e('Confirm'); ?>','<?php _e('Delete data on handler'); ?>'+': '+ row.Nombre+'\n'+'<?php _e('Sure?'); ?>',function(r){
    	if (!r) return;
    	$.get('/agility/server/database/guiaFunctions.php',{Operation:'delete',ID:row.ID},function(result){
    		if (result.success){
    			$(dg).datagrid('reload');    // reload the guia data
    		} else {
    			// show error message
    			$.messager.show({ title:'<?php _e('Error'); ?>', width:300, height:200, msg:result.errorMsg });
    		}
    	},'json');
    });
}

/**
 * Invoca a json para añadir/editar los datos del guia seleccionado en el formulario
 * Ask for commit new/edit guia to server
 */
function assignGuia(){
	$('#chguias-Club').val($('#chguias-newClub').val());
    $('#chguias-Operation').val('update');
    $('#chguias-Federation').val(workingData.federation);
    var frm = $('#chguias-form');
    if (! frm.form('validate')) return;
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/guiaFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){ 
            	$.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: result.errorMsg });
            } else {
                // TODO: study why datagrid loses focus handling
                var dg=$('#chguias-parent').val();
                if (dg!="") $(dg).datagrid('load');
                $('#chguias-Search').combogrid('clear');  // clear search field
                $('#chguias-dialog').dialog('close');        // close the dialog
            }
        }
    });
}

/**
 * Anyade (new) un nuevo guia desde el menu de reasignacion de guia
 */
function saveChGuia(){
    var frm = $('#chguias-form');
	$('#chguias-Club').val($('#chguias-newClub').val());
    $('#chguias-Operation').val('insert');
    $('#chguias-Federation').val(workingData.federation);
    if (!frm.form('validate')) return; // don't call inside ajax to avoid override beforeSend()
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/guiaFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){
                $.messager.show({ width:300,height:200, title: '<?php _e('Error'); ?>', msg: result.errorMsg });
            } else {
                // TODO: study why load makes next use focus fail on new datagrid
                var dg=$('#chguias-parent').val();
                if (dg!="") $(dg).datagrid('load');
            	if (result.insert_id ) $('#guias-ID').val(result.insert_id);
            	$('#chguias-Search').combogrid('clear');  // clear search field
                $('#chguias-dialog').dialog('close');    // close the dialog
            }
        }
    });
}

/**
 * Invoca a json para añadir/editar los datos del guia seleccionado en el formulario
 * Ask for commit new/edit guia to server
 */
function saveGuia(){
	// use $.ajax() instead of form('submit') to assure http request header is sent
    $('#guias-Federation').val(workingData.federation);
    var frm = $('#guias-form');
    if (!frm.form('validate')) return;
    
	$.ajax({
        type: 'GET',
        url: '/agility/server/database/guiaFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){ 
            	$.messager.show({width:300, height:200, title:'<?php _e('Error'); ?>',msg: result.errorMsg });
            } else {
            	var oper=$('#guias-Operation').val();
            	// notice that onAccept() already refresh parent dialog
            	if(result.insert_id && (oper==="insert") ) $('#guias-ID').val(result.insert_id);
                $('#guias-dialog').dialog('close');        // close the dialog
            }
        }
    });
}
