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
	$('#pruebas-dialog').dialog('open').dialog('setTitle','Nueva Prueba');
	$('#pruebas-form').form('clear');
	if (!strpos(def,"Buscar")) $('#pruebas-Nombre').val(def);// fill juez Name
	$('#pruebas-Operation').val('insert');
	if (onAccept!==undefined)$('#pruebas-okBtn').one('click',onAccept);
}

/**
 * Open dialogo de modificacion de pruebas
 * @param {string} dg datagrid ID de donde se obtiene la prueba
 */
function editPrueba(dg){
	if ($('#pruebas-datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Edit Error:","!No ha seleccionado ninga Prueba!","info");
    	return; // no way to know which prueba is selected
    }
    $('#pruebas-dialog').dialog('open').dialog('setTitle','Modificar datos de la prueba');
    $('#pruebas-form').form('load',row);
}

/**
 * Ask json routines for add/edit a prueba into BBDD
 */
function savePrueba() {
	// take care on bool-to-int translation from checkboxes to database
    $('#pruebas-Cerrada').val( $('#pruebas-Cerrada').is(':checked')?'1':'0');
    // do normal submit
    $('#pruebas-form').form('submit',{
        url: '/agility/server/database/pruebaFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(res){
            var result = eval('('+res+')');
            if (result.errorMsg){ $.messager.show({width:300, height:200, title:'Error',msg: result.errorMsg });
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
    	$.messager.alert("Delete Error:","!No ha seleccionado ninga Prueba!","info");
    	return; // no way to know which prueba is selected
    }
    $.messager.confirm('Confirm',
    		"<p>Importante:</p><p>Si decide borrar la prueba <b>se perder&aacute;n</b> todos los datos y resultados de &eacute;sta</p><p>Desea realmente borrar la prueba seleccionada?</p>",function(r){
        if (r){
            $.get('/agility/server/database/pruebaFunctions.php',{Operation:'delete',ID:row.ID},function(result){
                if (result.success){
                    $(dg).datagrid('reload');    // reload the prueba data
                } else {
                    $.messager.show({ width:300, height:200, title:'Error', msg:result.errorMsg });
                }
            },'json');
        }
    });
}

// ***** gestion de jornadas	*********************************************************

/**
 * Abre el formulario para jornadas a una prueba
 *@param prueba objeto que contiene los datos de la prueba
 */
function addJornadaToPrueba(prueba) {
	$('#jornadas-dialog').dialog('open').dialog('setTitle','A&ntilde;adir jornada a la prueba '+prueba.Nombre);
	$('#jornadas-form').form('clear');
	$('#jornadas-Prueba').val(prueba.ID);
	$('#jornadas-Operation').val('insert');
}

/**
 * Edita la jornada seleccionada
 *@param pruebaID objeto que contiene los datos de la prueba
 *@param datagridID identificador del datagrid del que se toman los datos
 */
function editJornadaFromPrueba(pruebaID,datagridID) {
	// obtenemos datos de la JORNADA seleccionada
	var row= $(datagridID).datagrid('getSelected');
    // var row = $('#jornadas-datagrid-'+prueba.ID).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("No selection","!No ha seleccionado ninguna jornada!","warning");
    	return; // no hay ninguna jornada seleccionada. retornar
    }
    if (row.Cerrada==true) { // no permitir la edicion de pruebas cerradas
    	$.messager.alert("Invalid selection","No se puede editar una jornada marcada como cerrada","error");
        return;
    }
    // todo ok: abrimos ventana de dialogo
    $('#jornadas-dialog').dialog('open').dialog('setTitle','Modificar datos de la jornada');
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
    	$.messager.alert("No selection","!No ha seleccionado ninguna jornada!","warning");
    	return; // no hay ninguna jornada seleccionada. retornar
    }
    if (row.Cerrada==true) { // no permitir la edicion de pruebas cerradas
    	$.messager.alert("Invalid selection","No se puede cerrar una jornada que ya está marcada como cerrada","error");
        return;
    }
    $.messager.defaults={ ok:"Cerrar", cancel:"Cancelar" };
    var w=$.messager.confirm(
    		"Aviso",
    		"Si marca una jornada como 'cerrada'<br />" +
    		"no podrá modificar los datos de mangas, <br/>" +
    		"inscripciones, o resultados<br />" +
    		"¿Desea continuar?",
    		function(r) { 
    	    	if(r) {
    	            $.get('/agility/server/database/jornadaFunctions.php',{Operation:'close',ID:row.ID},function(result){
    	                if (result.success){
    	                    $(datagridID).datagrid('reload');    // reload the pruebas data
    	                } else {
    	                    $.messager.show({ width:300, height:200, title:'Error', msg:result.errorMsg });
    	                }
    	            },'json');
    	    	}
    		});
    w.window('resize',{width:400,height:150}).window('center');
}

/**
 * Quita la asignacion de la jornada indicada a la prueba asignada
 *@prueba objeto que contiene los datos de la prueba
 */
function delJornadaFromPrueba(prueba,datagridID) {
	var row= $(datagridID).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("No selection","!No ha seleccionado ninguna jornada!","warning");
    	return; // no hay ninguna jornada seleccionada. retornar
    }
    if (prueba.Cerrada==true) {
        $.messager.show({width:300,heigh:200,title: 'Error',msg: 'No se pueden borrar jornadas de una prueba cerrada'});
    }
    $.messager.confirm('Confirm',"Borrar Jornada '"+row.ID+"' de la prueba '"+prueba.Nombre+"' ¿Seguro?'",function(r){
        if (r){
            $.get('/agility/server/database/jornadaFunctions.php',{Operation:'delete',ID:row.ID},function(result){
                if (result.success){
                    $(datagridID).datagrid('reload');    // reload the pruebas data
                    // $('#jornadas-datagrid-'+prueba.ID).datagrid('reload');    // reload the pruebas data
                } else {
                    $.messager.show({ width:300, height:200, title:'Error', msg:result.errorMsg });
                }
            },'json');
        }
    });
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
    $('#jornadas-form').form('submit',{
        url: '/agility/server/database/jornadaFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(res){
            var result = eval('('+res+')');
            if (result.errorMsg){
                $.messager.show({width:300,height:200,title: 'Error',msg: result.errorMsg});
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
			$.messager.alert('Error','Una prueba KO, un Open, o una prueba por equipos deben ser declaradas en jornadas independiente','error');
			$(id).prop('checked',false);
			if (id==='#jornadas-EquiposChk') $('#jornadas-MangasEquipos').prop('disabled','disabled');
			return;
		}
	} else {
		if ( (pruebas & 0x01E0) != 0 ) {
			$.messager.alert('Error','No se pueden añadir pruebas adicionales si hay declarado un Open, una jornada KO o una prueba por Equipos','error');
			$(id).prop('checked',false);
			if (id==='#jornadas-PreAgilityChk') $('#jornadas-MangasPreAgility').prop('disabled','disabled');
			if (id==='#jornadas-Especial') $('#jornadas-Observaciones').prop('disabled','disabled');
			return;
		}
	}
}
