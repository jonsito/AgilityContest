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
$config =new Config();
?>

// ***** gestion de equipos de una prueba	*****************************************************
 
function buscaEquipos() {
	$('#team_datagrid').datagrid( 'load', { 
		where: ($('#team_datagrid-search').val()==='---- Buscar ----')? '' : $('#team_datagrid-search').val()
		}
	);
}

/**
 * request if a contest has or not Team events
 * Tip: Instead of calling database, just analyze events datagrid
 * 
 * @param {integer} prueba PruebaID
 * @return true: has Team events; otherwise false
 */
function hasTeamEvents(prueba) {
	var rows=$('#inscripciones-jornadas').datagrid('getRows');
	for (var n=0; n<rows.length; n++) {
		if ( parseInt(rows[n].Equipos3)==1) return true;
		if ( parseInt(rows[n].Equipos4)==1) return true;
	}
	return false;
}

/**
 * Abre un dialogo para declarar un nuevo equipo para la prueba 
 */
function openTeamWindow(pruebaID) {
	// if no Team event declared in this contest refuse to open
	if (!hasTeamEvents(pruebaID)) {
		$.messager.alert("Error:","<?php _e('Esta prueba no tiene declaradas competiciones por equipos');?>","info");
		return;
	}
	// allright: open window
	$('#team_datagrid-dialog').dialog('open');
	$('#team_datagrid').datagrid('reload');
}

/**
 *Open dialogo de alta de equipos
 *@param {string} dg datagrid ID de donde se obtiene el equipo y el id de prueba
 *@param {string} def default value to insert into Nombre 
 *@param {function} onAccept what to do when a new team is created
 */
function newTeam(dg,def,onAccept){
	var idprueba=$(dg).datagrid('getRows')[0]; // first row ('-- Sin asignar --') allways exist
	$('#team_edit_dialog').dialog('open').dialog('setTitle','A&ntilde;adir nuevo equipo');
	$('#team_edit_dialog-form').form('clear');
	if (!strpos(def,"Buscar")) $('#team_edit_dialog-Nombre').val(def);// fill team Name
	$('#team_edit_dialog-Operation').val('insert');
	$('#team_edit_dialog-Prueba').val(idprueba.Prueba);
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
    // and load team edit dialog with provided data
    $('#team_edit_dialog-form').form('load',row);
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
    		"<p>Esta operaci&oacute;n borrar&aacute; el equipo y reasignar&aacute; los perros de &eacute;ste al equipo por defecto</p>" +
    		"<p>Desea realmente eliminar el equipo '"+row.Nombre+"' de esta prueba?</p>",function(r){
        if (r){
            $.get('/agility/server/database/equiposFunctions.php',{Operation:'delete',ID:row.ID,Prueba:row.Prueba},function(result){
                if (result.success){
                    $(dg).datagrid('load');    // reload the prueba data 
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
    var frm = $('#team_edit_dialog-form');
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
            	// on save done refresh related data/combo grids
                $('#new_inscripcion-Equipo').combogrid('grid').datagrid('load'); 
                $('#edit_inscripcion-Equipo').combogrid('grid').datagrid('load'); 
                $('#team_datagrid').datagrid('load'); 
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