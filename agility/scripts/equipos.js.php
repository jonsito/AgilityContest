/*
 equipos.js

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

// ***** gestion de equipos de una prueba	*****************************************************
 
function buscaEquipos() {
	$('#team_datagrid').datagrid( 'load', { 
		where: ($('#team_datagrid-search').val()==='---- Buscar ----')? '' : $('#team_datagrid-search').val()
		}
	);
}

/**
 * Abre un dialogo para declarar un nuevo equipo para la prueba 
 */
function openTeamWindow(pruebaID) {
	// buscamos la jornada seleccionada
	var row=$('#inscripciones-jornadas').datagrid('getSelected');
	// si no hay jornada por equipos seleccionada indicamos error
	if (row===null) {
		$.messager.alert("Error:","<?php _e('Debe seleccionar una jornada con competiciones por equipos');?>","error");
		return;
	}
	if ( (row.Equipos3==0) && (row.Equipos4==0) ) {
		$.messager.alert("Error:","<?php _e('La jornada seleccionada no tiene competiciones por equipos');?>","error");
	}
	// allright: set Jornada as active and open window
	setJornada(row);
	$('#team_datagrid-dialog').dialog('open');
	$('#team_datagrid').datagrid('load',{ Operation:'select', Prueba:workingData.prueba, Jornada:workingData.jornada, where:''});
}

/**
 *Open dialogo de alta de equipos
 *@param {string} dg datagrid ID de donde se obtiene el equipo y el id de prueba
 *@param {string} def default value to insert into Nombre 
 *@param {function} onAccept what to do when a new team is created
 */
function newTeam(dg,def,onAccept){
	var firstRow=$(dg).datagrid('getRows')[0]; // first row ('-- Sin asignar --') allways exist
	$('#team_edit_dialog').dialog('open').dialog('setTitle','A&ntilde;adir nuevo equipo');
	$('#team_edit_dialog-form').form('clear');
	if (!strpos(def,"Buscar")) $('#team_edit_dialog-Nombre').val(def);// fill team Name
	$('#team_edit_dialog-Operation').val('insert');
	$('#team_edit_dialog-Prueba').val(firstRow.Prueba);
	$('#team_edit_dialog-Jornada').val(firstRow.Jornada);
    // notice that on "new" window must be explicitely closed, so don't add close-on-ok() code
	if (onAccept!==undefined)$('#team_edit_dialog-okBtn').one('click',onAccept);
}

/**
 * Open dialogo de modificacion de equipos
 * @param {string} dg datagrid ID de donde se obtiene el equipo a editar
 */
function editTeam(dg){
	if ($('#team_datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Edit Error:","!No ha seleccionado ningun Equipo!","info");
    	return; // no way to know which prueba is selected
    }
    if (row.Nombre==="-- Sin asignar --") {
    	$.messager.alert("Edit Error:","El equipo por defecto NO se puede editar","info");
    	return; // no way to know which prueba is selected
    }
    $('#team_edit_dialog').dialog('open').dialog('setTitle','Modificar datos del equipo');
	row.Operation="update";
    // tel window to be closed when "OK" clicked
    $('#team_edit_dialog-okBtn').one('click',function() {$('#team_edit_dialog').dialog('close');});
	// load team edit dialog with provided data   
	$('#team_edit_dialog-form').form('load',row);
	// fill properly checkboxes
	$('#team_edit_dialog-L').prop('checked',(row.Categorias.indexOf('L')<0)?false:true);
	$('#team_edit_dialog-M').prop('checked',(row.Categorias.indexOf('M')<0)?false:true);
	$('#team_edit_dialog-S').prop('checked',(row.Categorias.indexOf('S')<0)?false:true);
	$('#team_edit_dialog-T').prop('checked',(row.Categorias.indexOf('T')<0)?false:true);
}

/**
 * Delete data related with a team in BBDD
 * @param {string} dg datagrid ID de donde se obtiene el teamID y la pruebaID
 */
function deleteTeam(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Delete Error:","!No ha seleccionado ningun Equipo!","info");
    	return; // no way to know which prueba is selected
    }
    if (row.Nombre==="-- Sin asignar --") {
    	$.messager.alert("Delete Error:","El equipo por defecto no puede borrarse","info");
    	return; // no way to know which prueba is selected
    }
    $.messager.confirm('Confirm',
			"<p><?php _e('Esta operaci&oacute;n borrar&aacute; el equipo de la jornada');?><br />"+
			"<p><?php _e('Desea realmente eliminar el equipo');?> '"+row.Nombre+"' <?php _e('de esta jornada');?>?</p>",function(r){
        if (r){
            $.get('/agility/server/database/equiposFunctions.php',{Operation:'delete',ID:row.ID,Prueba:row.Prueba},function(result){
                if (result.success){
                    $(dg).datagrid('load',{ Operation:'select', Prueba:workingData.prueba, Jornada:workingData.jornada, where:''});    // reload the prueba data 
                    $('#new_inscripcion-Equipo').combogrid('grid').datagrid('load'); 
                    $('#edit_inscripcion-Equipo').combogrid('grid').datagrid('load'); 
                } else {
                    $.messager.show({ width:300, height:200, title:'Error', msg:result.errorMsg });
                }
            },'json');
        }
    }).window({width:500});
}

/**
 * Save Team being edited, as result of doneBtn.onClick()
 * On success refresh every related datagrids
 */
function saveTeam() {
	// get and validate form data
    var frm = $('#team_edit_dialog-form');
    if (! frm.form('validate')) return;
	// evaluate 'Categorias' field
	var cat='';
	if ( $('#team_edit_dialog-L').is(':checked') ) cat+='L';
	if ( $('#team_edit_dialog-M').is(':checked') ) cat+='M';
	if ( $('#team_edit_dialog-S').is(':checked') ) cat+='S';
	if ( $('#team_edit_dialog-T').is(':checked') ) cat+='T';
	$('#team_edit_dialog-Categorias').val(cat);
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/equiposFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){ 
            	$.messager.show({width:300, height:200, title:'Error',msg: result.errorMsg });
            } else {// on submit success, reload results
            	// on save done refresh related data/combo grids
				$('#team_edit_dialog').dialog('close');
				$('#team_datagrid').datagrid('load',{ Operation:'select', Prueba:workingData.prueba, Jornada:workingData.jornada, where:''}); 
            }
        }
    });
}

/**
* Open assign team dialog
* @param {string} datagrid parent datagrid name
* @param {array} row selected datagrid data
*/
function changeTeamDialog(datagrid,row) {
	// cogemos datos de la inscripcion a modificar
	// actualizamos lista de equipos en el combogrid
	$('#selteam-Equipo').combogrid('grid').datagrid('load',{ Operation:'select', Prueba:workingData.prueba, Jornada:workingData.jornada, where:''});
	// ajustamos variables extras del formulario
    row.Parent=datagrid;
	// recargamos el formulario con los datos de la fila seleccionada
    $('#selteam-Form').form('load',row); // onLoadSuccess takes care on combogrid
	// desplegamos formulario 
    $('#selteam-window').window('open');
}

/**
* Change team to selected one
*/
function changeTeam() {
	// si no hay ninguna equipo valido seleccionada aborta
	var p=$('#selteam-Equipo').combogrid('grid').datagrid('getSelected');
	if (p==null) {
		// indica error
		$.messager.alert("Error","<?php _e('Debe indicar un equipo v&aacute;lido');?>","error");
		return;
	}
	$('#selteam-ID').val(p.ID);
    var frm = $('#selteam-Form');
    if (! frm.form('validate')) return;
    $.ajax({
        type: 'GET',
        url: '/agility/server/database/equiposFunctions.php',
        data: frm.serialize(),
        dataType: 'json',
        success: function (result) {
            if (result.errorMsg){ 
            	$.messager.show({width:300, height:200, title:'Error',msg: result.errorMsg });
            } else {// on submit success, reload results
            	var parent=$('#selteam-Parent').val();
            	// on save done refresh related data/combo grids
                $(parent).datagrid('reload');
            }
        }
    });
	$('#selteam-window').window('close');
}