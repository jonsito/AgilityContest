// ********************* Gestion de formularios (easyui-dialog) ****************************

// ***** gestion de clubes		*********************************************************

/**
 * Recalcula la tabla de clubes anyadiendo parametros de busqueda
 */
function doSearchClub() {
	// reload data adding search criteria
    $('#clubes-datagrid').datagrid('load',{
        where: $('#clubes-datagrid-search').val()
    });
}

/**
 * Abre el dialogo para crear un nuevo club
 *@param {string} dg datagrid id
 *@param {string} def nombre por defecto del club
 *@param {function} onAccept what to do when a new club is created
 */
function newClub(dg,def,onAccept){
	$('#clubes-dialog').dialog('open').dialog('setTitle','Nuevo club');
	$('#clubes-form').form('clear');
	// si el nombre del club contiene "Buscar" ignoramos
	if (!strpos(def,"Buscar")) $('#clubes-Nombre').val(def);
	$('#clubes-Operation').val('insert');
	// select ID=1 to get default logo
	var nombre="/agility/database/clubFunctions.php?Operation=logo&ID=1";
    $('#clubes-Logo').attr("src",nombre);
    // add onAccept related function if any
	if (onAccept!==undefined)
		$('#clubes-okBtn').one('click',onAccept);
}

/**
 * Abre el dialogo para editar un club existente
 * @var {string} dg current active datagrid ID
 */
function editClub(dg){
	if ($('#clubes-datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Update Error:","!No ha seleccionado ningún Club!","warning");
    	return; // no way to know which dog is selected
    }
    row.Operation='update';
	var nombre="/agility/database/clubFunctions.php?Operation=logo&ID="+row.ID;
    $('#clubes-Logo').attr("src",nombre);
    $('#clubes-dialog').dialog('open').dialog('setTitle','Modificar datos del club');
    $('#clubes-form').form('load',row);
}

/**
 * Funcion invocada cuando se pulsa "OK" en el dialogo de clubes
 * Ask for commit new/edit club to server
 */
function saveClub(){
    // do normal submit
    $('#clubes-form').form('submit',{
        url: 'database/clubFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(res){
            var result = eval('('+res+')');
            if (result.errorMsg){
                $.messager.show({
                    title: 'Error',
                    msg: result.errorMsg
                });
            } else {
                $('#clubes-dialog').dialog('close');        // close the dialog
                $('#clubes-datagrid').datagrid('reload');    // reload the clubes data
            }
        }
    });
}

/**
 * Pide confirmacion para borrar un club de la base de datos
 * En caso afirmativo lo borra
 * @var {string} dg current active datagrid ID
 */
function deleteClub(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Delete Error:","!No ha seleccionado ningún Club!","warning");
    	return; // no way to know which dog is selected
    }
    if (row.ID==1) {
    	$.messager.alert("Delete Error:","Esta entrada no se puede borrar","error");
    	return; // cannot delete default club
    }
    $.messager.confirm('Confirm','Borrar el club "'+row.Nombre+'" de la base de datos ¿Seguro?',function(r){
        if (!r) return;
        $.get('database/clubFunctions.php',{Operation:'delete',ID:row.ID},function(result){
            if (result.success){
                $(dg).datagrid('reload');    // reload the provided datagrid
            } else {
                $.messager.show({ width:300,height:200,title: 'Error',msg: result.errorMsg });
            }
        },'json');
    });
}

// ***** gestion de guias		*********************************************************

/**
 * Abre el formulario para anyadir guias a un club
 *@param {String} ID: Identificador del elemento ( datagrid) desde el que se invoca esta funcion
 *@param {object} data: datos del club
 */
function assignGuiaToClub(id,club) {
	// clear data forms
	$('#chguias-header').form('clear'); // erase header form
	$('#chguias-Search').combogrid('clear'); // reset header combogrid
	$('#chguias-form').form('clear'); // erase data form
	// fill default values
	$('#chguias-newClub').val(club.ID); // id del club to assign
	$('#chguias-Operation').val('update'); // operation
	// finalmente desplegamos el formulario y ajustamos textos
	$('#chguias-title').text('Reasignar/Declarar un guia como perteneciente al club '+club.Nombre);
	$('#chguias-dialog').dialog('open').dialog('setTitle','Asignar/Registrar un gu&iacute;a');
	// on click OK button, close dialog and refresh data
	$('#chguias-okBtn').one('click',function () { $(id).datagrid('reload'); } ); 
}

/**
 * Abre el formulario de edicion de guias para cambiar los datos de un guia preasignado a un club
 * @param {string} dg datagrid ID de donde se obtiene el guia
 * @param {object} club datos del club
 */
function editGuiaFromClub(dg, club) {
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Delete Error:","!No ha seleccionado ningún Guia!","warning");
    	return; // no way to know which guia is selected
    }
    // add extra needed parameters to dialog
    row.Club=club.ID;
    row.NombreClub=club.Nombre;
    row.Operation='update';
    $('#guias-form').form('load',row);
    $('#guias-dialog').dialog('open').dialog('setTitle','Modificar datos del guia inscrito en el club '+club.Nombre);
	// on click OK button, close dialog and refresh data
	$('#guias-okBtn').one('click',function () { $(id).datagrid('reload'); } ); 
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
    	$.messager.alert("Delete Error:","!No ha seleccionado ningún Guia!","warning");
    	return; // no way to know which guia is selected
    }
    $.messager.confirm('Confirm',"Borrar asignacion del gu&iacute;a '"+row.Nombre+"' al club '"+club.Nombre+"' ¿Seguro?'",function(r){
        if (r){
            $.get('database/guiaFunctions.php',{'Operation':'orphan','ID':row.ID},function(result){
                if (result.success){
                	$(dg).datagrid('reload');
                } else {
                	// show error message
                    $.messager.show({ title: 'Error', width: 300, height: 200, msg: result.errorMsg });
                }
            },'json');
        }
    });
}

/**
 * Abre el dialogo para crear un nuevo guia
 * @param {string} dg ID del datagrid activo
 * @param {string} def valor por defecto para el campo nombre
 * @param {function} onAccept what to do (only once) when window gets closed
 */
function newGuia(dg,def,onAccept){
	$('#guias-dialog').dialog('open').dialog('setTitle','Nuevo gu&iacute;a');
	$('#guias-form').form('clear');
	if (!strpos(def,"Buscar")) $('#guias-Nombre').val(def);
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
    	$.messager.alert("Edit Error:","!No ha seleccionado ningún guía!","warning");
    	return; // no way to know which dog is selected
    }
    $('#guias-dialog').dialog('open').dialog('setTitle','Modificar datos del gu&iacute;a');
    // add extra required parameters to dialog
    row.Parent='';
    row.Operation='update';
    // stupid trick to make dialog's clubs combogrid display right data
    $('#guias-form').form('load',row); // load row data into guia edit form
}

/**
 * Borra de la BBDD los datos del guia seleccionado. 
 * Invocada desde el menu de guias
 * @param {string} dg datagrid ID de donde se obtiene el guia
 */
function deleteGuia(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Delete Error:","!No ha seleccionado ningún guía!","warning");
    	return; // no way to know which dog is selected
    }
    if (row.ID==1) {
    	$.messager.alert("Delete Error:","Esta entrada no se puede borrar","error");
    	return; // cannot delete default entry
    }
    $.messager.confirm('Confirm','Borrar datos del guia: '+ row.Nombre+'\n¿Seguro?',function(r){
    	if (!r) return;
    	$.get('database/guiaFunctions.php',{Operation:'delete',Nombre:row.Nombre},function(result){
    		if (result.success){
    			$(dg).datagrid('reload');    // reload the guia data
    		} else {
    			// show error message
    			$.messager.show({ title:'Error', width:300, height:200, msg:result.errorMsg });
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
    // do normal submit
    $('#chguias-form').form('submit',{
        url: 'database/guiaFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(res){
            var result = eval('('+res+')');
            if (result.errorMsg){
                $.messager.show({ width:300, height:200, title:'Error', msg:result.errorMsg });
            } else {
            	// notice that onAccept() already refresh parent dialog
                $('#chguias-dialog').dialog('close');        // close the dialog
            }
        }
    });
}

/**
 * Invoca a json para añadir/editar los datos del guia seleccionado en el formulario
 * Ask for commit new/edit guia to server
 */
function saveGuia(){
    // do normal submit
    $('#guias-form').form('submit',{
        url: 'database/guiaFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(res){
            var result = eval('('+res+')');
            if (result.errorMsg){
                $.messager.show({ width:300, height:200, title:'Error', msg:result.errorMsg });
            } else {
            	// notice that onAccept() already refresh parent dialog
                $('#guias-dialog').dialog('close');        // close the dialog
            }
        }
    });
}

// ***** gestion de perros		*********************************************************

/**
 * Abre el dialogo para insertar datos de un nuevo perro
 * @param {string} dg datagrid ID de donde se obtiene el perro
 * @param def nombre por defecto que se asigna al perro en el formulario
 */
function newDog(dg,def){
	$('#perros-dialog').dialog('open').dialog('setTitle','Nuevo perro');
	$('#perros-form').form('clear'); // start with an empty form
	if (!strpos(def,"Buscar")) $('#perros-Nombre').val(def);
	$('#perros-Operation').val('insert');
}

/**
 * Abre el dialogo para editar datos de un perro ya existente
 * @param {string} dg datagrid ID de donde se obtiene el perro
 */
function editDog(dg){
	if ($('#perros-datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Edit Error:","!No ha seleccionado ningún perro!","warning");
    	return; // no way to know which dog is selected
    }
    $('#perros-dialog').dialog('open').dialog('setTitle','Modificar datos del perro');
    // add extra required data to form dialog
    row.Parent='';
    row.Operation='update';
    $('#perros-form').form('load',row);// load form with row data
}

/**
 * Borra el perro seleccionado de la base de datos
 * @param {string} dg datagrid ID de donde se obtiene el perro
 */
function deleteDog(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Delete Error:","!No ha seleccionado ningún perro!","info");
    	return; // no way to know which dog is selected
    }
    $.messager.confirm('Confirm','Borrar el perro: "'+ row.Nombre+'" de la base de datos.\n¿Seguro?',function(r){
       	if (!r) return;
        $.get('database/dogFunctions.php',{Operation:'delete',ID:row.ID},function(result){
            if (result.success){
                $('#perros-datagrid').datagrid('reload');    // reload the dog data
            } else { // show error message
                $.messager.show({ title: 'Error',  msg: result.errorMsg });
            }
        },'json');
    });
}

/**
 * Abre el formulario para anyadir/asignar perros a un guia
 *@param {string} ID identificador del datagrid que se actualiza
 *@param {object} guia: datos del guia
 */
function assignPerroToGuia(id,guia) {
	// clean previous dialog data
	$('#chperros-header').form('clear');
	$('#chperros-Search').combogrid('clear');
	$('#chperros-form').form('clear'); 
	// set up default guia data
	$('#chperros-newGuia').val(guia.ID);
	$('#chperros-Operation').val('update');
	// desplegar ventana y ajustar textos
	$('#chperros-title').text('Buscar perro / Declarar un nuevo perro y asignarlo a '+guia.Nombre);
	$('#chperros-dialog').dialog('open').dialog('setTitle',"Reasignar / Declarar perro");
	$('#chperros-okBtn').one('click',function () { $(mySelf).datagrid('reload'); } );
}

/**
* Abre el formulario para anyadir perros a un guia
* @param {string} dg datagrid ID de donde se obtiene el perro
* @param {object} guia: datos del guia
* @param {function} onAccept what to do (only once) when window gets closed
*/
function editPerroFromGuia(dg,guia) {
	// try to locate which dog has been selected 
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Error","!No ha seleccionado ningún perro!","warning");
    	return; // no way to know which dog is selected
    }
    // add extra required data to form dialog
    row.Operation='update';
    $('#perros-form').form('load',row);	// load form with row data. onLoadSuccess will fix comboboxes
    // finally display composed data
    $('#perros-dialog').dialog('open').dialog('setTitle','Modificar datos del perro asignado a '+guia.Nombre);
	$('#perros-okBtn').one('click',function () { $(dg).datagrid('reload'); } );
}

/**
 * Quita la asignacion del perro marcado al guia indicado
 * @param {string} dg datagrid ID de donde se obtiene el perro
 * @param {object} guia: datos del guia
 * @param {function} onAccept what to do (only once) when window gets closed
 */
function delPerroFromGuia(dg,guia) {
    var row = $(dg).datagrid('getSelected');
    if (!row){
    	$.messager.alert("Error","!No ha seleccionado ningún perro!","warning");
    	return; // no way to know which dog is selected
    }
    $.messager.confirm('Confirm',"Borrar asignacion del perro '"+row.Nombre+"' al guia '"+guia.Nombre+"' ¿Seguro?'",function(r){
        if (r){
            $.get('database/dogFunctions.php',{Operation:'orphan',ID:row.ID},function(result){
                if (result.success){
                	$(dg).datagrid('reload');
                } else {
                    $.messager.show({title: 'Error', msg: result.errorMsg, width: 300,height:200 });
                }
            },'json');
        }
    });
}

/** 
 * Actualiza los datos de un perro pre-asignado a un guia
 */
function assignDog() {
	// set up guia
	$('#chperros-Guia').val($('#chperros-newGuia').val());
    $('#chperros-form').form('submit',{
        url: 'database/dogFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(res){
            var result = eval('('+res+')');
            if (result.errorMsg){
                $.messager.show({width:300,height:200,title: 'Error',msg: result.errorMsg});
            } else {
            	$('#chperros-Search').combogrid('clear');  // clear search field
                $('#chperros-dialog').dialog('close');        // close the dialog
            }
        }
    });
}

/**
 * Ejecuta la peticion json para anyadir/editar un perro
 */
function saveDog(){
	var idperro=$('#perros-ID').val();
    $('#perros-form').form('submit',{
        url: 'database/dogFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(res){
            var result = eval('('+res+')');
            if (result.errorMsg){
                $.messager.show({ width:300,height:200, title: 'Error', msg: result.errorMsg });
            } else {
            	var url='database/dogFunctions.php?Operation=getbyidperro&ID='+idperro;
                // reload the dog data from inscripciones (if any)
    	        $('#inscripciones-data').form('load',url);
    	        $('#chinscripciones-data').form('load',url);
    	        // close the dialog
                $('#perros-dialog').dialog('close');   
            }
        }
    });
}

//***** gestion de jueces		*********************************************************

/**
 * Open "New Juez dialog"
 *@param {string} dg datagrid ID de donde se obtiene el juez
 *@param {string} def default value to insert into Nombre 
 *@param {function} onAccept what to do when a new Juez is created
 */
function newJuez(dg,def,onAccept){
	$('#jueces-dialog').dialog('open').dialog('setTitle','Nuevo juez'); // open dialog
	$('#jueces-form').form('clear');// clear old data (if any)
	if (!strpos(def,"Buscar")) $('#jueces-Nombre').val(def);// fill juez Name
	$('#jueces-Operation').val('insert');// set up operation
	if (onAccept!==undefined)$('#jueces-okBtn').one('click',onAccept);
}

/**
 * Open "Edit Juez" dialog
 * @param {string} dg datagrid ID de donde se obtiene el juez
 */
function editJuez(dg){
	if ($('#jueces-datagrid-search').is(":focus")) return; // on enter key in search input ignore
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Edit Error:","!No ha seleccionado ningún Juez!","warning");
    	return; // no way to know which dog is selected
    }
    // set up operation properly
    row.Operation='update';
    // open dialog
    $('#jueces-dialog').dialog('open').dialog('setTitle','Modificar datos del juez');
    // and fill form with row data
    $('#jueces-form').form('load',row);
}

/**
 * Call json to Ask for commit new/edit juez to server
 */
function saveJuez(){
	// take care on bool-to-int translation from checkboxes to database
    $('#jueces-Internacional').val( $('#jueces-Internacional').is(':checked')?'1':'0');
    $('#jueces-Practicas').val( $('#jueces-Practicas').is(':checked')?'1':'0');
    // do normal submit
    $('#jueces-form').form('submit',{
        url: 'database/juezFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(res){
            var result = eval('('+res+')');
            if (result.errorMsg){
                $.messager.show({width:300,height:200,title: 'Error',msg: result.errorMsg});
            } else {
                $('#jueces-dialog').dialog('close');        // close the dialog
                $('#jueces-datagrid').datagrid('reload');    // reload the juez data
            }
        }
    });
}

/**
 * Delete juez data in bbdd
 * @param {string} dg datagrid ID de donde se obtiene el juez
 */
function deleteJuez(dg){
    var row = $(dg).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Delete Error:","!No ha seleccionado ningún Juez!","info");
    	return; // no way to know which juez is selected
    }
    if (row.ID==1) {
    	$.messager.alert("Delete Error:","Esta entrada no se puede borrar","error");
    	return; // cannot delete default juez
    }
    $.messager.confirm('Confirm','Borrar datos del juez:'+row.Nombre+'\n ¿Seguro?',function(r){
      	if (!r) return;
        $.get('database/juezFunctions.php',{Operation:'delete',ID:row.ID},function(result){
            if (result.success){
                $(dg).datagrid('reload');    // reload the juez data
            } else {
            	// show error message
                $.messager.show({width:300,height:200,title: 'Error',msg: result.errorMsg});
            }
        },'json');
    });
}

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
        url: 'database/pruebaFunctions.php',
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
            $.get('database/pruebaFunctions.php',{Operation:'delete',ID:row.ID},function(result){
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
    	            $.get('database/jornadaFunctions.php',{Operation:'close',ID:row.ID},function(result){
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
            $.get('database/jornadaFunctions.php',{Operation:'delete',ID:row.ID},function(result){
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
    $('#jornadas-Grado1').val( $('#jornadas-Grado1').is(':checked')?'1':'0');
    $('#jornadas-Grado2').val( $('#jornadas-Grado2').is(':checked')?'1':'0');
    $('#jornadas-Grado3').val( $('#jornadas-Grado3').is(':checked')?'1':'0');
    $('#jornadas-Open').val( $('#jornadas-Open').is(':checked')?'1':'0');
    $('#jornadas-Equipos3').val( $('#jornadas-Equipos3').is(':checked')?'1':'0');
    $('#jornadas-Equipos4').val( $('#jornadas-Equipos4').is(':checked')?'1':'0');
    $('#jornadas-PreAgility').val( $('#jornadas-PreAgility').is(':checked')?'1':'0');
    $('#jornadas-KO').val( $('#jornadas-KO').is(':checked')?'1':'0');
    $('#jornadas-Exhibicion').val( $('#jornadas-Exhibicion').is(':checked')?'1':'0');
    $('#jornadas-Otras').val( $('#jornadas-Otras').is(':checked')?'1':'0');
    $('#jornadas-Cerrada').val( $('#jornadas-Cerrada').is(':checked')?'1':'0');
    // handle fecha
    // do normal submit
    $('#jornadas-Operation').val('update');
    $('#jornadas-form').form('submit',{
        url: 'database/jornadaFunctions.php',
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
 * 0x0001, 'Otras'
 * 0x0002, 'PreAgility'
 * 0x0004, 'Grado1',
 * 0x0008, 'Grado2',
 * 0x0010, 'Grado3',
 * 0x0020, 'Open',
 * 0x0040, 'Equipos3',
 * 0x0080, 'Equipos4',
 * 0x0100, 'KO',
 * 0x0200, 'Exhibicion'
 */
function checkPrueba(id,mask) {
	var pruebas=0;
	// mascara de pruebas seleccionadas
	pruebas |= $('#jornadas-Otras').is(':checked')?0x0001:0;
	pruebas |= $('#jornadas-PreAgility').is(':checked')?0x0002:0;
	pruebas |= $('#jornadas-Grado1').is(':checked')?0x0004:0;
	pruebas |= $('#jornadas-Grado2').is(':checked')?0x0008:0;
	pruebas |= $('#jornadas-Grado3').is(':checked')?0x0010:0;
	pruebas |= $('#jornadas-Open').is(':checked')?0x0020:0;
	pruebas |= $('#jornadas-Equipos3').is(':checked')?0x0040:0;
	pruebas |= $('#jornadas-Equipos4').is(':checked')?0x0080:0;
	pruebas |= $('#jornadas-KO').is(':checked')?0x0100:0;
	pruebas |= $('#jornadas-Exhibicion').is(':checked')?0x0200:0;
	// si no hay prueba seleccionada no hacer nada
	if (pruebas==0) return;
	// si estamos seleccionando una prueba ko/open/equipos, no permitir ninguna otra
	if ( (mask & 0x01E0) != 0 ) {
		if (mask!=pruebas) {
			$.messager.alert('Error','Una prueba KO, un Open, o una prueba por equipos deben ser declaradas en jornadas independiente','error');
			$(id).prop('checked',false);
			return;
		}
	} else {
		if ( (pruebas & 0x01E0) != 0 ) {
			$.messager.alert('Error','No se pueden añadir pruebas adicionales si hay declarado un Open, una jornada KO o una prueba por Equipos','error');
			$(id).prop('checked',false);
			return;
		}
	}
}


// ***** gestion de equipos de una prueba	*****************************************************

/**
 * Abre un dialogo para declarar un nuevo equipo para la prueba 
 */
function openTeamWindow(pruebaID) {
	$('#team_datagrid-dialog').dialog('open');
	$('#team_datagrid').datagrid('reload');
}

/**
 * Cierra la ventana de  dialogo de creacion de equipos
 */
function closeTeamWindow() {
	
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
 * Open dialogo de modificacion de equiois
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
            $.get('database/equiposFunctions.php',{Operation:'delete',ID:row.ID,Prueba:row.Prueba},function(result){
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
        url: 'database/equiposFunctions.php',
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
 */
function saveInscripcion() {
	// make sure that "Celo" field has correct value
	$('#edit_inscripcion-Celo').val( $('#edit_inscripcion-Celo2').is(':checked')?'1':'0');
    $('#edit_inscripcion-form').form('submit',{
        url: 'database/inscripcionFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(res){
            var result = eval('('+res+')');
            if (result.errorMsg){
                $.messager.show({width:300,height:200,title: 'Error',msg: result.errorMsg});
            } else {
            	// on save done refresh related data/combo grids and close dialog
            	$('#inscripciones-datagrid').datagrid('reload');
            	$('#edit_inscripcion-dialog').dialog('close');
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
			"<p>Importante:</p>" +
			"<p>Si decide borrar la inscripcion <b>se perder&aacute;n</b> todos los datos y resultados de &eacute;sta.<br />" +
			"Desea realmente borrar la inscripción seleccionada?</p>",function(r){
		if (r){
			$.get('database/inscripcionFunctions.php',{
					Operation:'remove',
					ID:row.ID,
					Prueba:row.Prueba,
					J1:$('#jornada_cerrada-1').text(),
					J2:$('#jornada_cerrada-2').text(),
					J3:$('#jornada_cerrada-3').text(),
					J4:$('#jornada_cerrada-4').text(),
					J5:$('#jornada_cerrada-5').text(),
					J6:$('#jornada_cerrada-6').text(),
					J7:$('#jornada_cerrada-7').text(),
					J8:$('#jornada_cerrada-8').text()
					},
				function(result){
					if (result.success) {
						$('#inscripciones-datagrid').datagrid('reload',{ // reload the inscripciones table
							where: $('#inscripciones-search').val()
						});
					} else {
						$.messager.show({ width:300, height:200, title:'Error', msg:result.errorMsg });
					}
				},'json');
		} // if (r)
	}).window({width:475});
}

/**
 * Ask for commit new inscripcion to server
 * @param {string} dg datagrid to retrieve selections from
 */
function insertInscripcion(dg) {
	var g=$(dg).datagrid('grid');
	var selectedRows= g.datagrid('getSelections');
	var count=1;
	var size=selectedRows.length;
	if(size==0) {
    	$.messager.alert("No selection","!No ha marcado ningún perro para proceder a su inscripción!","warning");
    	return; // no hay ninguna inscripcion seleccionada. retornar
	}
	$('#inscripciones-progresswindow').window('open');
	$.each(selectedRows, function(index,row) {
		$('#inscripciones-progressbar').progressbar('setValue',count*(100/size));
		$('#inscripciones-progresslabel').text("Inscribiendo a: "+row.Nombre);
		$.ajax({
	        async: false,
	        cache: false,
	        timeout: 10000, // 10 segundos
			type:'GET',
			url:"database/inscripcionFunctions.php",
			dataType:'json',
			data: {
				Prueba: workingData.prueba,
				Operation: 'insert',
				IDPerro: row.ID
			}
		});
		count++;
	});
	$('#inscripciones-progresswindow').window('close');
    // notice that some of these items may fail if dialog is not deployed. just ignore
	// foreach finished, clean, close and refresh
	$('#inscripciones-newGrid').combogrid('grid').datagrid('clearSelections');
    // reload the inscripciones table
	$('#inscripciones-datagrid').datagrid('reload');
}

