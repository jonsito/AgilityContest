/*
 equipos.js

Copyright 2013-2014 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

// ***** gestion de equipos de una prueba	*****************************************************

/**
 * Abre un dialogo para declarar un nuevo equipo para la prueba 
 */
function openTeamWindow(pruebaID) {
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

/* same as newTeam, but using a combogrid as parent element */
function newTeam2(cg,def){
    var idprueba=$(cg).combogrid('grid').datagrid('getRows')[0]; // first row ('-- Sin asignar --') allways exist
    $('#team_edit_dialog').dialog('open').dialog('setTitle','A&ntilde;adir nuevo equipo');
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
    });
}

/**
 * Save Team being edited, as result of doneBtn.onClick()
 * On success refresh every related datagrids
 */
function saveTeam() {
    $('#team_edit_dialog-form').form('submit',{
        url: '/agility/server/database/equiposFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(res){
            var result = eval('('+res+')');
            if (result.errorMsg){
                $.messager.show({width:300,height:200,title: 'Error',msg: result.errorMsg});
            } else {
            	// on save done refresh related data/combo grids
                $('#new_inscripcion-Equipo').combogrid('grid').datagrid('load'); 
                $('#edit_inscripcion-Equipo').combogrid('grid').datagrid('load'); 
                $('#team_datagrid').datagrid('load'); 
            }
        }
    });
}
