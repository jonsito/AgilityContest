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
	$('#team_edit_dialog').dialog('open').dialog('setTitle','<?php _e('A&ntilde;adir nuevo equipo');?>');
	$('#team_edit_dialog-form').form('clear');
	if (!strpos(def,"Buscar")) $('#team_edit_dialog-Nombre').val(def);// fill team Name
	$('#team_edit_dialog-Operation').val('insert');
	$('#team_edit_dialog-Prueba').val(idprueba.Prueba);
    // notice that on "new" window must be explicitely closed, so don't add close-on-ok() code
	if (onAccept!==undefined)$('#team_edit_dialog-okBtn').one('click',onAccept);
}

/* same as newTeam, but using a combogrid as parent element */
function newTeam2(cg,def){
	if (!hasTeamEvents(pruebaID)) {
    	$.messager.alert("Error:","<?php _e('Esta prueba no tiene declaradas competiciones por equipos');?>","info");
    	return; //
	}
    var idprueba=$(cg).combogrid('grid').datagrid('getRows')[0]; // first row ('-- Sin asignar --') allways exist
    $('#team_edit_dialog').dialog('open').dialog('setTitle','<?php _e('A&ntilde;adir nuevo equipo');?>');
    $('#team_edit_dialog-form').form('clear');
    if (!strpos(def,"Buscar")) $('#team_edit_dialog-Nombre').val(def);// fill team Name
    $('#team_edit_dialog-Operation').val('insert');
    $('#team_edit_dialog-Prueba').val(idprueba.Prueba);
    $('#team_edit_dialog-okBtn').one('click',function() {$('#team_edit_dialog').dialog('close');});
}

/**
 * Open dialogo de modificacion de equipos
 * @param {string} dg datagrid ID de donde se obtiene el equipo a editar
 */
function editTeam(dg){
	if ($('#team_datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Edit Error:","<?php _e('!No ha seleccionado ningun Equipo!');?>","info");
    	return;
    }

    if (row.Nombre==="-- Sin asignar --") {
    	$.messager.alert("Edit Error:","<?php _e('El equipo por defecto NO se puede editar');?>","info");
    	return;
    }
    $('#team_edit_dialog').dialog('open').dialog('setTitle','<?php _e('Modificar datos del equipo');?>');
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
    	$.messager.alert("Delete Error:","<?php _e('!No ha seleccionado ningun Equipo!');?>","info");
    	return; // no way to know which prueba is selected
    }
    if (row.Nombre==="-- Sin asignar --") {
    	$.messager.alert("Delete Error:","<?php _e('El equipo por defecto NO se puede borrar');?>","info");
    	return; // no way to know which prueba is selected
    }
    $.messager.confirm('Confirm',
    		"<p><?php _e('Esta operaci&oacute;n borrar&aacute; el equipo de la prueba');?><br />"+
    		"<?php _e('y reasignar&aacute; los perros de &eacute;ste al equipo por defecto');?></p>" +
    		"<p><?php _e('Desea realmente eliminar el equipo');?> '"+row.Nombre+"' <?php _e('de esta prueba');?>?</p>",function(r){
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
