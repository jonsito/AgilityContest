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
 */
function newClub(){
	$('#clubes-dialog').dialog('open').dialog('setTitle','Nuevo club');
	$('#clubes-form').form('clear');
	$('#clubes-Operation').val('insert');
}

/**
 * Abre el dialogo para editar un club existente
 */
function editClub(){
    var row = $('#clubes-datagrid').datagrid('getSelected');
    if (!row) return;
    $('#clubes-dialog').dialog('open').dialog('setTitle','Modificar datos del club');
    $('#clubes-form').form('load',row);
    // save old club name in "Viejo" hidden form input to allow change guia name
    $('#clubes-Viejo').val( $('#clubes-Nombre').val());
	$('#clubes-Operation').val('update');
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
        success: function(result){
            var result = eval('('+result+')');
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
function destroyClub(){
    var row = $('#clubes-datagrid').datagrid('getSelected');
    if (!row) return;
    $.messager.confirm('Confirm','Borrar datos del club. ¿Seguro?',function(r){
        if (r){
            $.get('database/clubFunctions.php',{Operation:'delete',Nombre:row.Nombre},function(result){
                if (result.success){
                    $('#clubes-datagrid').datagrid('reload');    // reload the guia data
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

// ***** gestion de guias		*********************************************************

/**
 * Abre el formulario para anyadir guias a un club
 *@param club: nombre del club
 */
function addGuiaToClub(club) {
	$('#guias-dialog').dialog('open').dialog('setTitle','Declarar un nuevo gu&iacute;a y asignarlo al club '+club.Nombre);
	$('#guias-form').form('clear'); // erase form
	$('#guias-Club').combogrid({ 'value': club.Nombre} ); 
	$('#guias-Club').combogrid({ 'readonly': true }); // mark guia as read-only
	$('#guias-Operation').val('insert');
}

/**
 * Abre el formulario de edicion de guias para cambiar los datos de un guia preasignado a un club
 * @param club
 */
function editGuiaFromClub(club) {
    var row = $('#guiasByClub-datagrid-'+replaceAll(' ','_',club.Nombre)).datagrid('getSelected');
    if (!row) return; // no guia selected
    $('#guias-dialog').dialog('open').dialog('setTitle','Modificar datos del guia inscrito en el club '+club.Nombre);
    $('#guias-form').form('load',row);
    $('#guias-Viejo').val( $('#guias-Nombre').val()); // set up old name in case of change
	$('#guias-Club').combogrid({ 'value': club.Nombre} ); 
	$('#guias-Club').combogrid({ 'readonly': true }); // mark guia as read-only
    $('#guias-Operation').val('update');
}

/**
 * Quita la asignacion del guia marcado al club indicado
 *@param club datos del club
 */
function delGuiaFromClub(club) {
    var row = $('#guiasByClub-datagrid-'+replaceAll(' ','_',club.Nombre)).datagrid('getSelected');
    if (!row) return;

    $.messager.confirm('Confirm',"Borrar asignacion del gu&iacute;a '"+row.Nombre+"' al club '"+club.Nombre+"' ¿Seguro?'",function(r){
        if (r){
            $.get('database/clubFunctions.php',{'Operation':'orphan','Nombre':row.Nombre},function(result){
                if (result.success){
                    $('#guiasByCLub-datagrid-'+replaceAll(' ','_',club.Nombre)).datagrid('reload');    // reload the guia data
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
 * Recalcula la tabla de guias anyadiendo parametros de busqueda
 */
function doSearchGuia() {
	// reload data adding search criteria
    $('#guias-datagrid').datagrid('load',{
        where: $('#guias-search').val()
    });
}

/**
 * Abre el dialogo para crear un nuevo guia
 */
function newGuia(){
	$('#guias-dialog').dialog('open').dialog('setTitle','Nuevo g&uiacute;a');
	$('#guias-form').form('clear');
	$('#guias-Operation').val('insert');
}

/**
 * Abre el dialogo para editar un guia existente
 */
function editGuia(){
    var row = $('#guias-datagrid').datagrid('getSelected');
    if (!row) return;
    $('#guias-dialog').dialog('open').dialog('setTitle','Modificar datos del gu&iacute;a');
    $('#guias-form').form('load',row);
    // save old guia name in "Viejo" hidden form input to allow change guia name
    $('#guias-Viejo').val( $('#guias-Nombre').val());
	$('#guias-Operation').val('update');
}

/**
 * Invoca a json para añadir/editar los datos del guia seleccionado en el formulario
 * Ask for commit new/edit guia to server
 */
function saveGuia(){
	var club=replaceAll(' ','_',$('#guias-Club').combobox('getValue'));
    // do normal submit
    $('#guias-form').form('submit',{
        url: 'database/guiaFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(result){
            var result = eval('('+result+')');
            if (result.errorMsg){
                $.messager.show({
                    title: 'Error',
                    msg: result.errorMsg
                });
            } else {
                $('#guias-datagrid').datagrid('reload');    // reload the guia data
                $('#guiasByClub-datagrid-'+club).datagrid('reload');
                $('#guias-dialog').dialog('close');        // close the dialog
            }
        }
    });
}

/**
 * Borra de la BBDD los datos del guia seleccionado
 */
function destroyGuia(){
    var row = $('#guias-datagrid').datagrid('getSelected');
    if (!row) return;
	var club=replaceAll(' ','_',row.Club);
    $.messager.confirm('Confirm','Borrar datos del guia. '+ row.Nombre+'¿Seguro?',function(r){
        if (r){
            $.get('database/guiaFunctions.php',{Operation:'delete',Nombre:row.Nombre},function(result){
                if (result.success){
                    $('#guias-datagrid').datagrid('reload');    // reload the guia data
                    $('#guiasByClub-datagrid-'+club).datagrid('reload'); // if so reload club's inner guia data
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

// ***** gestion de perros		*********************************************************

/**
 * Abre el formulario para anyadir perros a un guia
 *@param guia: nombre del guia
 */
function addPerroToGuia(guia) {
	$('#perros-dialog').dialog('open').dialog('setTitle','Crear un nuevo perro y asignarlo a '+guia.Nombre);
	$('#perros-form').form('clear'); // erase form
	$('#perros-Guia').combogrid({ 'value': guia.Nombre} ); 
	$('#perros-Guia').combogrid({ 'readonly': true }); // mark guia as read-only
	$('#perros-Operation').val('insert');
}

/**
* Abre el formulario para anyadir perros a un guia
*@param guia: nombre del guia
*/
function editPerroFromGuia(guia) {
	// try to locate which dog has been selected 
    var row = $('#perrosByGuia-datagrid-'+replaceAll(' ','_',guia.Nombre)).datagrid('getSelected');
    if (!row) return; // no way to know which dog is selected
    $('#perros-dialog').dialog('open').dialog('setTitle','Modificar datos del perro asignado a '+guia.Nombre);
    $('#perros-form').form('load',row);
	$('#perros-Guia').combogrid({ 'value': guia.Nombre} ); 
	$('#perros-Guia').combogrid({ 'readonly': true }); // mark guia as read-only
    $('#perros-Operation').val('update');
}

/**
 * Quita la asignacion del perro marcado al guia indicado
 */
function delPerroFromGuia(guia) {
    var row = $('#perrosByGuia-datagrid-'+replaceAll(' ','_',guia.Nombre)).datagrid('getSelected');
    if (!row) return;
    $.messager.confirm('Confirm',"Borrar asignacion del perro '"+row.Nombre+"' al guia '"+guia.Nombre+"' ¿Seguro?'",function(r){
        if (r){
            $.get('database/guiaFunctions.php',{Operation:'orphan',Dorsal:row.Dorsal},function(result){
                if (result.success){
                    $('#perrosByGuia-datagrid-'+replaceAll(' ','_',guia.Nombre)).datagrid('reload');    // reload the guia data
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
 * Recalcula la lista de perros anyadiendo parametros de busqueda
 */
function doSearchPerro() {
	// reload data adding search criteria
    $('#perros-datagrid').datagrid('load',{
        where: $('#perros-search').val()
    });
}

/**
 * Abre el dialogo para insertar datos de un nuevo perro
 */
function newDog(){
	$('#perros-dialog').dialog('open').dialog('setTitle','Nuevo perro');
	$('#perros-form').form('clear');
	$('#perros-Guia').combogrid({ 'readonly': false }); // mark guia as read-only
	$('#perros-Operation').val('insert');
}

/**
 * Abre el dialogo para editar datos de un perro ya existente
 */
function editDog(){
    var row = $('#perros-datagrid').datagrid('getSelected');
    if (!row) return;
    $('#perros-dialog').dialog('open').dialog('setTitle','Modificar datos del perro');
    $('#perros-form').form('load',row);
	$('#perros-Guia').combogrid({ 'readonly': false }); // mark guia as read-only
    $('#perros-Operation').val('update');
}

/**
 * Ejecuta la peticion json para anyadir/editar un perro
 */
function saveDog(){
    $('#perros-form').form('submit',{
        url: 'database/dogFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(result){
            var result = eval('('+result+')');
            if (result.errorMsg){
                $.messager.show({
                    title: 'Error',
                    msg: result.errorMsg
                });
            } else {
            	var guia=$('#perros-Guia').combogrid('getValue');
                $('#perros-datagrid').datagrid('reload');    // reload the dog data
                $('#perrosbyguia-datagrid-'+replaceAll(' ','_',guia)).datagrid('reload',{Guia:guia});    // reload the dog data inside guias menu, if any
                $('#perros-dialog').dialog('close');        // close the dialog
            }
        }
    });
}

/**
 * Borra el perro seleccionado de la base de datos
 */
function destroyDog(){
    var row = $('#perros-datagrid').datagrid('getSelected');
    if (row){
        $.messager.confirm('Confirm','Borrar datos del perro. ¿Seguro?',function(r){
            if (r){
                $.get('database/dogFunctions.php',{Operation:'delete',Dorsal:row.Dorsal},function(result){
                    if (result.success){
                        $('#perros-datagrid').datagrid('reload');    // reload the dog data
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
}

//***** gestion de jueces		*********************************************************

/**
 * Recalcula la tabla de jueces anyadiendo parametros de busqueda
 */
function doSearchJuez() {
	// reload data adding search criteria
    $('#jueces-datagrid').datagrid('load',{
        where: $('#jueces-search').val()
    });
    // clear search textbox
    // hey, this fire up again onChangeEvent :-(
    // $('#guias-search').val('');
}

/**
 * Open "Juez dialog"
 */
function newJuez(){
	$('#jueces-dialog').dialog('open').dialog('setTitle','Nuevo juez');
	$('#jueces-form').form('clear');	
	$('#jueces-Operation').val('insert');
}

/**
 * Open "Edit Juez" dialog
 */
function editJuez(){
    var row = $('#jueces-datagrid').datagrid('getSelected');
    if (row){
        $('#jueces-dialog').dialog('open').dialog('setTitle','Modificar datos del juez');
        $('#jueces-form').form('load',row);
        // take care on int-to-bool translation for checkboxes
        $('#jueces-Internacional').prop('checked',(row.Internacional==1)?true:false);
        $('#jueces-Practicas').prop('checked',(row.Practicas==1)?true:false);
        // save old juez name in "Viejo" hidden form input to allow change juez name
        $('#jueces-Viejo').val( $('#jueces-Nombre').val());
    	$('#jueces-Operation').val('update');
    }
}

/**
 * Ask for commit new/edit juez to server
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
        success: function(result){
            var result = eval('('+result+')');
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
 * Delete juez data
 */
function destroyJuez(){
    var row = $('#jueces-datagrid').datagrid('getSelected');
    if (row){
        $.messager.confirm('Confirm','Borrar datos del juez. ¿Seguro?',function(r){
            if (r){
                $.get('database/juezFunctions.php',{Operation:'delete',Nombre:row.Nombre},function(result){
                    if (result.success){
                        $('#jueces-datagrid').datagrid('reload');    // reload the juez data
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
}

//***** gestion de pruebas		*********************************************************

/**
 * Recalcula el formulario de pruebas anyadiendo parametros de busqueda
 */
function doSearchPrueba() {
	var includeClosed= $('#pruebas-openBox').is(':checked')?'1':'0'
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
    $('#pruebas-form').form('load',row);
    // take care on int-to-bool translation for checkboxes
    $('#pruebas-Cerrada').prop('checked',(row.Cerrada==1)?true:false);
    // save old guia name in "Viejo" hidden form input to allow change guia name
    $('#pruebas-Viejo').val( $('#pruebas-Nombre').val());
	$('#pruebas-Operation').val('update');
}

/**
 * Ask for commit new/edit prueba to server
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
        success: function(result){
            var result = eval('('+result+')');
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
 * Delete Prueba data
 */
function destroyPrueba(){
    var row = $('#pruebas-datagrid').datagrid('getSelected');
    if (!row) return;
    $.messager.confirm('Confirm',
    		"<p>Importante:</p><p>Si decide borrar la prueba <b>se perder&aacute;n</b> todos los datos y resultados de &eacute;sta</p><p>Desea realmente borrar la prueba seleccionada?</p>",function(r){
        if (r){
            $.get('database/pruebaFunctions.php',{Operation:'delete',Nombre:row.Nombre},function(result){
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
	myPrueba=prueba;
	$('#jornadas-dialog').dialog('open').dialog('setTitle','A&ntilde;adir jornada a la prueba '+prueba.Nombre);
	$('#jornadas-form').form('clear');
	$('#jornadas-Prueba').val(prueba.Nombre);
	$('#jornadas-Operation').val('insert');
}

/**
 * Edita la jornada seleccionada
 *@param index: indice que ocupa el guia en la entrada principal
 *@param prueba objeto que contiene los datos de la prueba
 */
function editJornadaFromPrueba(prueba) {
	// obtenemos datos de la JORNADA seleccionada
    var row = $('#jornadas-datagrid-'+replaceAll(' ','_',prueba.Nombre)).datagrid('getSelected');
    if (!row) return; // no hay ninguna jornada seleccionada. retornar
    if (row.Cerrada==true) { // no permitir la edicion de pruebas cerradas
        $.messager.show({    
            title: 'Error',
            msg: 'No se puede editar una jornada una vez cerrada'
        });
        return;
    }
    // todo ok: abrimos ventana de dialogo
    $('#jornadas-dialog').dialog('open').dialog('setTitle','Modificar datos de la jornada');
    $('#jornadas-form').form('load',row);
    // fix date value value into datebox
    // take care on int-to-bool translation for checkboxes
    $('#jornadas-Grado1').prop('checked',(row.Grado1==1)?true:false);
    $('#jornadas-Grado2').prop('checked',(row.Grado2==1)?true:false);
    $('#jornadas-Grado3').prop('checked',(row.Grado3==1)?true:false);
    $('#jornadas-Equipos').prop('checked',(row.Equipos==1)?true:false);
    $('#jornadas-PreAgility').prop('checked',(row.PreAgility==1)?true:false);
    $('#jornadas-KO').prop('checked',(row.KO==1)?true:false);
    $('#jornadas-Exhibicion').prop('checked',(row.Exhibicion==1)?true:false);
    $('#jornadas-Otras').prop('checked',(row.Otras==1)?true:false);
    $('#jornadas-Cerrada').prop('checked',(row.Cerrada==1)?true:false);
	$('#jornadas-Prueba').val(prueba.Nombre);
	$('#jornadas-Operation').val('update');
}

/**
 * Quita la asignacion de la jornada indicada a la prueba asignada
 *@param indice de la fila (jornada) afectada
 *@prueba objeto que contiene los datos de la prueba
 */
function delJornadaFromPrueba(prueba) {
    var row = $('#jornadas-datagrid-'+replaceAll(' ','_',prueba.Nombre)).datagrid('getSelected');
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
                    $('#jornadas-datagrid-'+replaceAll(' ','_',prueba.Nombre)).datagrid('reload');    // reload the pruebas data
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
    $('#jornadas-form').form('submit',{
        url: 'database/jornadaFunctions.php',
        method: 'get',
        onSubmit: function(param){
            return $(this).form('validate');
        },
        success: function(result){
            var result = eval('('+result+')');
            if (result.errorMsg){
                $.messager.show({
                    title: 'Error',
                    msg: result.errorMsg
                });
            } else {
            	var prueba=$('#jornadas-Prueba').val();
                $('#jornadas-dialog').dialog('close');        // close the dialog
                $('#jornadas-datagrid-'+replaceAll(' ','_',prueba)).datagrid('reload',{Prueba: prueba});    // reload the prueba data
            }
        }
    });
}
// ***** gestion de inscripciones	*****************************************************
