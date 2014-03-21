// ********************* Gestion de formularios (easyui-dialog) ****************************

// ***** gestion de clubes		*********************************************************

/**
 * Recalcula la tabla de clubes anyadiendo parametros de busqueda
 */
function doSearchClub() {
	// reload data adding search criteria
    $('#clubes-datagrid').datagrid('load',{
        where: $('#clubes-search').val()
    });
}

/**
 * Abre el dialogo para crear un nuevo club
 *@param {string} def nombre por defecto del club
 *@param {function} onAccept what to do when a new club is created
 */
function newClub(def,onAccept){
	$('#clubes-dialog').dialog('open').dialog('setTitle','Nuevo club');
	$('#clubes-form').form('clear');
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
 */
function editClub(){
	if ($('#clubes-search').is(":focus")) return; // on enter key in search input ignore
    var row = $('#clubes-datagrid').datagrid('getSelected');
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
 */
function deleteClub(){
    var row = $('#clubes-datagrid').datagrid('getSelected');
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
                $('#clubes-datagrid').datagrid('reload');    // reload the guia data
            } else {
                $.messager.show({    // show error message
                    title: 'Error',
                    msg: result.errorMsg
                });
            }
        },'json');
    });
}

// ***** gestion de guias		*********************************************************

/**
 * Abre el formulario para anyadir guias a un club
 *@param {object} club: datos del club
 *@param {function} onAccept what to do (only once) when chguias-dialog gets closed by pressing ok
 */
function assignGuiaToClub(club,onAccept) {
	// clear data forms
	$('#chguias-header').form('clear'); // erase header form
	$('#chguias-Search').form('clear'); // reset header combogrid
	$('#chguias-form').form('clear'); // erase data form
	// fill default values
	$('#chguias-newClub').val(club.ID); // id del club to assign
	$('#chguias-Operation').val('update'); // operation
	// finalmente desplegamos el formulario y ajustamos textos
	$('#chguias-title').text('Reasignar/Declarar un guia como perteneciente al club '+club.Nombre);
	$('#chguias-dialog').dialog('open').dialog('setTitle','Asignar/Registrar un gu&iacute;a');
	if (onAccept!==undefined)
		$('#chguias-okBtn').one('click',onAccept); // usually refresh parent datagrid
}

/**
 * Abre el formulario de edicion de guias para cambiar los datos de un guia preasignado a un club
 * @param {object} club datos del club
 * @param {function} onAccept what to do (only once) when window gets closed
 */
function editGuiaFromClub(club,onAccept) {
	var parent = '-' + replaceAll(' ','_',club.ID);
    var row = $('#guias-datagrid'+parent).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Delete Error:","!No ha seleccionado ningún Guia!","warning");
    	return; // no way to know which guia is selected
    }
    // add extra needed parameters to dialog
    row.Club=club.ID;
    row.NombreClub=club.Nombre;
    row.Parent=parent;
    row.Operation='update';
    $('#guias-form').form('load',row);
    $('#guias-dialog').dialog('open').dialog('setTitle','Modificar datos del guia inscrito en el club '+club.Nombre);
	if (onAccept!==undefined)
		$('#guias-okBtn').one('click',onAccept);
}

/**
 * Quita la asignacion del guia marcado al club indicado
 * Invocada desde el menu de clubes
 * @param {object} club datos del club
 * @param {function} onAccept what to do (only once) when window gets closed
 */
function delGuiaFromClub(club,onAccept) {
    var row = $('#guias-datagrid-'+replaceAll(' ','_',club.ID)).datagrid('getSelected');
    if (!row) return;

    $.messager.confirm('Confirm',"Borrar asignacion del gu&iacute;a '"+row.Nombre+"' al club '"+club.Nombre+"' ¿Seguro?'",function(r){
        if (r){
            $.get('database/guiaFunctions.php',{'Operation':'orphan','ID':row.ID},function(result){
                if (result.success){
                	if (onAccept!==undefined) onAccept(); // usually reload the guia data 
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
 * @param {string} def valor por defecto para el campo nombre
 * @param {function} onAccept what to do (only once) when window gets closed
 */
function newGuia(def,onAccept){
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
 */
function editGuia(){
	if ($('#guias-search').is(":focus")) return; // on enter key in search input ignore
    var row = $('#guias-datagrid').datagrid('getSelected');
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
    $('#guias-Club').combogrid('clear');
    $('#guias-Club').combogrid('setValue',row.Club);
    $('#guias-Club').combogrid('setText',row.NombreClub);
}

/**
 * Borra de la BBDD los datos del guia seleccionado. 
 * Invocada desde el menu de guias
 */
function deleteGuia(){
    var row = $('#guias-datagrid').datagrid('getSelected');
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
    			$('#guias-datagrid').datagrid('reload');    // reload the guia data
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
                $.messager.show({
                	width: 300,
                	height:200,
                    title: 'Error',
                    msg: result.errorMsg
                });
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
                $.messager.show({
                    title: 'Error',
                    msg: result.errorMsg
                });
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
 * @param def nombre por defecto que se asigna al perro en el formulario
 */
function newDog(def){
	$('#perros-dialog').dialog('open').dialog('setTitle','Nuevo perro');
	$('#perros-form').form('clear'); // start with an empty form
	if (!strpos(def,"Buscar")) $('#perros-Nombre').val(def);
	$('#perros-Operation').val('insert');
	$('#perros-Parent').val(''); // no parent datagrid
}

/**
 * Abre el formulario para anyadir/asignar perros a un guia
 *@param guia: nombre del guia
 */
function assignPerroToGuia(guia) {
	$('#chperros-dialog').dialog('open').dialog('setTitle',"Reasignar / Declarar perro");
	$('#chperros-form').form('clear'); // start with an empty form
	$('#chperros-title').text('Buscar perro / Declarar un nuevo perro y asignarlo a '+guia.Nombre); // start with an empty form
	// set up default guia
	$('#chperros-newGuia').val(guia.Nombre);
	$('#chperros-Guia').val(guia.Nombre);
	$('#chperros-Operation').val('insert');
	$('#chperros-Parent').val('-'+replaceAll(' ','_',guia.Nombre));
}

/**
 * Abre el dialogo para editar datos de un perro ya existente
 */
function editDog(){
	if ($('#perros-search').is(":focus")) return; // on enter key in search input ignore
    var row = $('#perros-datagrid').datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Edit Error:","!No ha seleccionado ningún perro!","warning");
    	return; // no way to know which dog is selected
    }
    $('#perros-dialog').dialog('open').dialog('setTitle','Modificar datos del perro');
    // add extra required data to form dialog
    row.Parent='';
    row.Operation='update';
    $('#perros-form').form('load',row);// load form with row data
    // stupid combogrid that doesn't display right data after form load
    $('#perros-Guia').combogrid('clear');
    $('#perros-Guia').combogrid('setValue',row.Guia);
    $('#perros-Guia').combogrid('setText',row.NombreGuia);
}

/**
 * Abre el dialogo para editar datos de un perro ya existente desde el menu de inscripciones
 * @param {integer} mode 0:newInscripcion 1:editInscripcion
 */
function editInscribedDog(mode){
	var idperro=0;
	if (mode==0) idperro= $('#inscripciones-ID').val();
	else idperro= $('#chinscripciones-ID').val();
    $('#perros-dialog').dialog('open').dialog('setTitle','Modificar datos del perro a inscribir');
    $('#perros-form').form('load','database/dogFunctions.php?Operation=getbyidperro&ID='+idperro);// load form with row data
    $('#perros-form').form({
    	onLoadSuccess: function(data) {
    		$('#perros-Parent').val(''); // no parent datagrid
    	    $('#perros-Operation').val('update'); // mark "update" operation
    	}
    });
}
/**
* Abre el formulario para anyadir perros a un guia
*@param guia: nombre del guia
*/
function editPerroFromGuia(guia) {
	var parent = '-'+replaceAll(' ','_',guia.ID);
	// try to locate which dog has been selected 
    var row = $('#perros-datagrid'+parent).datagrid('getSelected');
    if (!row) {
    	$.messager.alert("Error","!No ha seleccionado ningún perro!","warning");
    	return; // no way to know which dog is selected
    }
    $('#perros-dialog').dialog('open').dialog('setTitle','Modificar datos del perro asignado a '+guia.Nombre);
    // add extra required data to form dialog
    row.Parent=parent;
    row.Operation='update';
    $('#perros-form').form('load',row);	// load form with row data
    // stupid combogrid that doesn't display right data after form load
    $('#perros-Guia').combogrid('clear');
    $('#perros-Guia').combogrid('setValue',row.Guia);
    $('#perros-Guia').combogrid('setText',row.NombreGuia);
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
                $.messager.show({
                    title: 'Error',
                    msg: result.errorMsg
                });
            } else {
            	var parent=$('#chperros-Parent').val();
                $('#perros-datagrid'+parent).datagrid('reload');    // reload the dog data
            	$('#chperros-Search').combogrid({ 'value': '' } );  // clear search field
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
                $.messager.show({
                    title: 'Error',
                    msg: result.errorMsg
                });
            } else {
            	var parent=$('#perros-Parent').val();
            	var url='database/dogFunctions.php?Operation=getbyidperro&ID='+idperro;
            	// reload the dog data on datagrid (if any)
                $('#perros-datagrid'+parent).datagrid('reload');
                // reload the dog data from inscripciones (if any)
    	        $('#inscripciones-data').form('load',url);
    	        $('#chinscripciones-data').form('load',url);
    	        // close the dialog
                $('#perros-dialog').dialog('close');   
            }
        }
    });
}

/**
 * Quita la asignacion del perro marcado al guia indicado
 */
function delPerroFromGuia(guia) {
    var row = $('#perros-datagrid-'+replaceAll(' ','_',guia.ID)).datagrid('getSelected');
    if (!row) return;
    $.messager.confirm('Confirm',"Borrar asignacion del perro '"+row.Nombre+"' al guia '"+guia.Nombre+"' ¿Seguro?'",function(r){
        if (r){
            $.get('database/dogFunctions.php',{Operation:'orphan',ID:row.ID},function(result){
                if (result.success){
                    $('#perros-datagrid-'+replaceAll(' ','_',guia.Nombre)).datagrid('reload');    // reload the guia data
                } else {
                    $.messager.show({    // show error message
                        title: 'Error',
                        msg: result.errorMsg,
                        width: 300,
                        height:200
                    });
                }
            },'json');
        }
    });
}

/**
 * Borra el perro seleccionado de la base de datos
 */
function deleteDog(){
    var row = $('#perros-datagrid').datagrid('getSelected');
    if (row){
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
    } else {
    	$.messager.alert("Delete Error:","!No ha seleccionado ningún perro!","info");
    	return; // no way to know which dog is selected
    }
}

//***** gestion de jueces		*********************************************************

/**
 * Open "New Juez dialog"
 *@param {string} def default value to insert into Nombre 
 *@param {function} onAccept what to do when a new Juez is created
 */
function newJuez(def,onAccept){
	$('#jueces-dialog').dialog('open').dialog('setTitle','Nuevo juez'); // open dialog
	$('#jueces-form').form('clear');// clear old data (if any)
	if (!strpos(def,"Buscar")) $('#jueces-Nombre').val(def);// fill juez Name
	$('#jueces-Operation').val('insert');// set up operation
	if (onAccept!==undefined)$('#jueces-okBtn').one('click',onAccept);
}

/**
 * Open "Edit Juez" dialog
 */
function editJuez(){
	if ($('#jueces-search').is(":focus")) return; // on enter key in search input ignore
    var row = $('#jueces-datagrid').datagrid('getSelected');
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
                $.messager.show({
                    title: 'Error',
                    msg: result.errorMsg
                });
            } else {
                $('#jueces-dialog').dialog('close');        // close the dialog
                $('#jueces-datagrid').datagrid('reload');    // reload the juez data
            }
        }
    });
}

/**
 * Delete juez data in bbdd
 */
function deleteJuez(){
    var row = $('#jueces-datagrid').datagrid('getSelected');
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
                $('#jueces-datagrid').datagrid('reload');    // reload the juez data
            } else {
            	// show error message
                $.messager.show({title: 'Error',msg: result.errorMsg});
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
 * Open dialogo de creacion de pruebas
 */
function newPrueba(){
	$('#pruebas-dialog').dialog('open').dialog('setTitle','Nueva Prueba');
	$('#pruebas-form').form('clear');
	$('#pruebas-Operation').val('insert');
}

/**
 * Open dialogo de modificacion de pruebas
 */
function editPrueba(){
    var row = $('#pruebas-datagrid').datagrid('getSelected');
    if (!row) return; // no prueba selected
    
    $('#pruebas-dialog').dialog('open').dialog('setTitle','Modificar datos de la prueba');
    $('#pruebas-form').form({
    	onLoadSuccess: function(data) {
            // take care on int-to-bool translation for checkboxes
            $('#pruebas-Cerrada').prop('checked',(row.Cerrada==1)?true:false);
    	}
    });
    row.Operation='update';
    $('#pruebas-form').form('load',row);
}

/**
 * Ask json routines for add/edit a prueba into BBDD
 */
function savePrueba(){
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
            if (result.errorMsg){
                $.messager.show({
                    title: 'Error',
                    msg: result.errorMsg
                });
            } else {
                $('#pruebas-dialog').dialog('close');        // close the dialog
                $('#pruebas-datagrid').datagrid('reload');    // reload the prueba data
            }
        }
    });
}

/**
 * Delete data related with a prueba in BBDD
 */
function deletePrueba(){
    var row = $('#pruebas-datagrid').datagrid('getSelected');
    if (!row) return;
    $.messager.confirm('Confirm',
    		"<p>Importante:</p><p>Si decide borrar la prueba <b>se perder&aacute;n</b> todos los datos y resultados de &eacute;sta</p><p>Desea realmente borrar la prueba seleccionada?</p>",function(r){
        if (r){
            $.get('database/pruebaFunctions.php',{Operation:'delete',ID:row.ID},function(result){
                if (result.success){
                    $('#pruebas-datagrid').datagrid('reload');    // reload the prueba data
                } else {
                    $.messager.show({    // show error message
                        title: 'Error',
                        msg: result.errorMsg
                    });
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
    	$.messager.alert("Invalid selection","No se puede editar una prueba marcada como cerrada","error");
        return;
    }
    // todo ok: abrimos ventana de dialogo
    $('#jornadas-dialog').dialog('open').dialog('setTitle','Modificar datos de la jornada');
    $('#jornadas-form').form('load',row);
    // fix date and checkboxes value into datebox in "onLoadSuccess" event declaration
    $('#jornadas-Grado1').prop('checked',(row.Grado1==1)?true:false);
    $('#jornadas-Grado2').prop('checked',(row.Grado2==1)?true:false);
    $('#jornadas-Grado3').prop('checked',(row.Grado3==1)?true:false);
    $('#jornadas-Equipos').prop('checked',(row.Equipos==1)?true:false);
    $('#jornadas-PreAgility').prop('checked',(row.PreAgility==1)?true:false);
    $('#jornadas-KO').prop('checked',(row.KO==1)?true:false);
    $('#jornadas-Exhibicion').prop('checked',(row.Exhibicion==1)?true:false);
    $('#jornadas-Otras').prop('checked',(row.Otras==1)?true:false);
    $('#jornadas-Cerrada').prop('checked',(row.Cerrada==1)?true:false);
	$('#jornadas-Prueba').val(pruebaID);
	$('#jornadas-Operation').val('update');
}

/**
 * Quita la asignacion de la jornada indicada a la prueba asignada
 *@prueba objeto que contiene los datos de la prueba
 */
function delJornadaFromPrueba(prueba,datagridID) {
	var row= $(datagridID).datagrid('getSelected');
    // var row = $('#jornadas-datagrid-'+prueba.ID).datagrid('getSelected');
    if (!row) return; // no row selected
    if (prueba.Cerrada==true) {
        $.messager.show({    // show error message
            title: 'Error',
            msg: 'No se pueden borrar jornadas de una prueba cerrada'
        });
    }
    $.messager.confirm('Confirm',"Borrar Jornada '"+row.ID+"' de la prueba '"+prueba.Nombre+"' ¿Seguro?'",function(r){
        if (r){
            $.get('database/jornadaFunctions.php',{Operation:'delete',ID:row.ID},function(result){
                if (result.success){
                    $(datagridID).datagrid('reload');    // reload the pruebas data
                    // $('#jornadas-datagrid-'+prueba.ID).datagrid('reload');    // reload the pruebas data
                } else {
                    $.messager.show({    // show error message
                        title: 'Error',
                        msg: result.errorMsg
                    });
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
    $('#jornadas-Equipos').val( $('#jornadas-Equipos').is(':checked')?'1':'0');
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
                $.messager.show({
                    title: 'Error',
                    msg: result.errorMsg
                });
            } else {
            	var id=$('#jornadas-Prueba').val();
                $('#jornadas-dialog').dialog('close');        // close the dialog
                // notice that some of these items may fail if dialog is not deployed. just ignore
                $('#jornadas-datagrid-'+id).datagrid('reload',{ Prueba:id , Operation:'enumerate' }); // reload the prueba data
                $('#inscripciones-jornadas').datagrid('reload');    // reload the prueba data
            }
        }
    });
}

/**
 * No permitir activacion de una prueba si hay declarada una prueba ko o una de equipos
 */
function checkPrueba(id) {
	var count=0;
	count += $('#jornadas-Equipos').is(':checked')?1:0;
	count += $('#jornadas-KO').is(':checked')?1:0;
	if (count==0) return;
	$.messager.alert('Error','No se pueden declarar pruebas adicionales en una jornada KO o por Equipos','error');
	$(id).prop('checked',false);
}

/**
 * En el caso de una prueba KO debe ser la unica prueba de la jornada
 */
function setKO() {
	var count=0;
	var KO=$('#jornadas-KO').val( $('#jornadas-KO').is(':checked')?1:0);
	if (KO==0) return;
	count += $('#jornadas-Grado1').is(':checked')?1:0;
	count += $('#jornadas-Grado2').is(':checked')?1:0;
	count += $('#jornadas-Grado3').is(':checked')?1:0;
	count += $('#jornadas-PreAgility').is(':checked')?1:0;
	count += $('#jornadas-Equipos').is(':checked')?1:0;
	count += $('#jornadas-Exhibicion').is(':checked')?1:0;
	count += $('#jornadas-Otras').is(':checked')?1:0;
	if (count==0) return; // everything ok
	$.messager.alert('Error','Una prueba KO debe ser declarada en una jornada independiente','error');
	$('#jornadas-KO').prop('checked',false);
}

/**
 * En el caso de una prueba por equipos debe ser la unica prueba de la jornada
 */
function setEquipos() {
	var count=0;
	var KO=$('#jornadas-KO').val( $('#jornadas-KO').is(':checked')?1:0);
	if (KO==0) return;
	count += $('#jornadas-Grado1').is(':checked')?1:0;
	count += $('#jornadas-Grado2').is(':checked')?1:0;
	count += $('#jornadas-Grado3').is(':checked')?1:0;
	count += $('#jornadas-PreAgility').is(':checked')?1:0;
	count += $('#jornadas-KO').is(':checked')?1:0;
	count += $('#jornadas-Exhibicion').is(':checked')?1:0;
	count += $('#jornadas-Otras').is(':checked')?1:0;
	if (count==0) return; // everything ok
	$.messager.alert('Error','Una prueba por equipos debe ser declarada en una jornada independiente','error');
	$('#jornadas-Equipos').prop('checked',false);
}

// ***** gestion de inscripciones	*****************************************************

function editInscripcion() {
	var cerrada=false;
	// obtenemos datos de la inscripcion seleccionada
	var row= $('#inscripciones-datagrid').datagrid('getSelected');
    if (!row) return; // no hay ninguna inscripcion seleccionada. retornar
	// cerramos dialogo de nueva inscripcion
    $('#inscripciones-buscar').form('clear'); 
    $('#inscripciones-data').form('clear'); 
    $('#inscripciones-form').form('clear'); 
    $('#inscripciones-Participante').combogrid({ 'setValue' : '' });
    $('#inscripciones-dialog').dialog('close');
	// abrimos dialogo de edicion de inscripcion
	$('#chinscripciones-dialog').dialog('open').dialog('setTitle','Modificar datos de inscripci&oacute;n');
	// rellenamos formulario de datos del perro
	$('#chinscripciones-data').form('load','database/dogFunctions.php?Operation=getbyidperro&ID='+row.ID);
	// rellenamos formulario de la inscripcion
	$('#chinscripciones-form').form('load',row);
	// ajustamos checkboxes (un cb tiene "value" and "checked" como propiedades, y el 'load' solo toca "value")
	// store original values
	$('#chinscripciones-oldJ1').val(row.J1);
	$('#chinscripciones-oldJ2').val(row.J2);
	$('#chinscripciones-oldJ3').val(row.J3);
	$('#chinscripciones-oldJ4').val(row.J4);
	$('#chinscripciones-oldJ5').val(row.J5);
	$('#chinscripciones-oldJ6').val(row.J6);
	$('#chinscripciones-oldJ7').val(row.J7);
	$('#chinscripciones-oldJ8').val(row.J8);
	// set up checked status
	$('#chinscripciones-J1').prop('checked',row.J1);
	$('#chinscripciones-J2').prop('checked',row.J2);
	$('#chinscripciones-J3').prop('checked',row.J3);
	$('#chinscripciones-J4').prop('checked',row.J4);
	$('#chinscripciones-J5').prop('checked',row.J5);
	$('#chinscripciones-J6').prop('checked',row.J6);
	$('#chinscripciones-J7').prop('checked',row.J7);
	$('#chinscripciones-J8').prop('checked',row.J8);
	// disable those ones that belongs to closed journeys
	// store cerrada status into form
	cerrada= ($('#jornada_cerrada-1').text()=='1')?true:false;
	$('#chinscripciones-c1').val($('#jornada_cerrada-1').text());
	$('#chinscripciones-J1').prop('disabled',cerrada);
	cerrada= ($('#jornada_cerrada-2').text()=='1')?true:false;
	$('#chinscripciones-c2').val($('#jornada_cerrada-2').text());
	$('#chinscripciones-J2').prop('disabled',cerrada);
	cerrada= ($('#jornada_cerrada-3').text()=='1')?true:false;
	$('#chinscripciones-c3').val($('#jornada_cerrada-3').text());
	$('#chinscripciones-J3').prop('disabled',cerrada);
	cerrada= ($('#jornada_cerrada-4').text()=='1')?true:false;
	$('#chinscripciones-c4').val($('#jornada_cerrada-4').text());
	$('#chinscripciones-J4').prop('disabled',cerrada);
	cerrada= ($('#jornada_cerrada-5').text()=='1')?true:false;
	$('#chinscripciones-c5').val($('#jornada_cerrada-5').text());
	$('#chinscripciones-J5').prop('disabled',cerrada);
	cerrada= ($('#jornada_cerrada-6').text()=='1')?true:false;
	$('#chinscripciones-c6').val($('#jornada_cerrada-6').text());
	$('#chinscripciones-J6').prop('disabled',cerrada);
	cerrada= ($('#jornada_cerrada-7').text()=='1')?true:false;
	$('#chinscripciones-c7').val($('#jornada_cerrada-7').text());
	$('#chinscripciones-J7').prop('disabled',cerrada);
	cerrada= ($('#jornada_cerrada-8').text()=='1')?true:false;
	$('#chinscripciones-c8').val($('#jornada_cerrada-8').text());
	$('#chinscripciones-J8').prop('disabled',cerrada);
}

/**
 * Delete data related with an inscription in BBDD
 */
function deleteInscripcion() {
	var row = $('#inscripciones-datagrid').datagrid('getSelected');
	if (!row) return;
	$.messager.confirm('Confirm',
			"<p>Importante:</p>" +
			"<p>Si decide borrar la inscripcion <b>se perder&aacute;n</b> todos los datos y resultados de &eacute;sta.<br />" +
			"Desea realmente borrar la inscripción seleccionada?</p>",function(r){
		if (r){
			$.get('database/inscripcionFunctions.php',{
					Operation:'remove',
					ID:row.ID,
					ID:workingData.prueba,
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
						$.messager.show({ title: 'Error',msg: result.errorMsg });
					}
				},'json');
		} // if (r)
	}).window({width:475});
}

/**
 * Ask for commit new inscripcion to server
 */
function insertInscripcion() {
	var g=$('#inscripciones-newGrid').combogrid('grid');
	var selectedRows= g.datagrid('getSelections');
	var count=1;
	var size=selectedRows.length;
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
				ID: workingData.prueba,
				Operation: 'doit',
				IDPerro: row.IDPerro
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

/**
 * Ask for submit inscription changes to server
 */
function updateInscripcion(){
	// if no jornada selected warn
	var count=0;
	if ( $('#chinscripciones-J1').prop('checked')) count++;
	if ( $('#chinscripciones-J2').prop('checked')) count++;
	if ( $('#chinscripciones-J3').prop('checked')) count++;
	if ( $('#chinscripciones-J4').prop('checked')) count++;
	if ( $('#chinscripciones-J5').prop('checked')) count++;
	if ( $('#chinscripciones-J6').prop('checked')) count++;
	if ( $('#chinscripciones-J7').prop('checked')) count++;
	if ( $('#chinscripciones-J8').prop('checked')) count++;
	if (count==0) {
		$.messager.alert("Error","!No ha seleccionado ninguna jornada!","warning");
		return;
	}
	// fill needed data to be sent
	$('#chinscripciones-fIDPerro').val($('#chinscripciones-IDPerro').val());
	$('#chinscripciones-fPruebaID').val(workingData.prueba);
	$('#chinscripciones-fOperation').val('doit');
    // do normal submit
    $('#chinscripciones-form').form('submit',{
        url: 'database/inscripcionFunctions.php',
        method: 'get',
        onSubmit: function(param){ // nothing to validate, but...
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
                $('#chinscripciones-dialog').dialog('close');        // close the dialog
                // notice that some of these items may fail if dialog is not deployed. just ignore
                $('#inscripciones-datagrid').datagrid('reload'); // reload the inscripciones table
            }
        }
    });
}

