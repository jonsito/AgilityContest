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
function destroyClub(){
    var row = $('#clubes-datagrid').datagrid('getSelected');
    if (!row) return;
    $.messager.confirm('Confirm','Borrar el club "'+row.Nombre+'" de la base de datos ¿Seguro?',function(r){
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
 * Recalcula la tabla de guias anyadiendo parametros de busqueda
 */
function doSearchGuia() {
	// reload data adding search criteria
    $('#guias-datagrid').datagrid('load',{
        where: $('#guias-search').val()
    });
}

/**
 * Abre el formulario para anyadir guias a un club
 *@param club: nombre del club
 */
function assignGuiaToClub(club) {
	$('#chguias-dialog').dialog('open').dialog('setTitle','Asignar/Registrar un gu&iacute;a');
	$('#chguias-form').form('clear'); // erase form
	$('#chguias-title').text('Reasignar/Declarar un guia como perteneciente al club '+club.Nombre);
	$('#chguias-Club').val(club.Nombre);
	$('#chguias-newClub').val(club.Nombre);
	$('#chguias-Operation').val('insert');
	$('#chguias-Parent').val('-' + replaceAll(' ','_',club.Nombre));
}

/**
 * Abre el dialogo para crear un nuevo guia
 */
function newGuia(){
	$('#guias-dialog').dialog('open').dialog('setTitle','Nuevo g&iacute;a');
	$('#guias-form').form('clear');
	$('#guias-Operation').val('insert');
	$('#guias-Parent').val('');
}

/**
 * Abre el formulario de edicion de guias para cambiar los datos de un guia preasignado a un club
 * @param club
 */
function editGuiaFromClub(club) {
	var parent = '-' + replaceAll(' ','_',club.Nombre);
    var row = $('#guias-datagrid'+parent).datagrid('getSelected');
    if (!row) return; // no guia selected
    $('#guias-dialog').dialog('open').dialog('setTitle','Modificar datos del guia inscrito en el club '+club.Nombre);
    $('#guias-form').form('load',row);
    // save old guia name in "Viejo" hidden form input to allow change guia name
    $('#guias-Viejo').val( $('#guias-Nombre').val()); // set up old name in case of change
    // set default value for "club"
	$('#guias-Club').val(club.Nombre); 
    $('#guias-Parent').val(parent);
    $('#guias-Operation').val('update');
}

/**
 * Abre el dialogo para editar un guia existente
 */
function editGuia(){
    var row = $('#guias-datagrid').datagrid('getSelected');
    if (!row) return;// no guia selected
    $('#guias-dialog').dialog('open').dialog('setTitle','Modificar datos del gu&iacute;a');
    $('#guias-form').form('load',row); // load row data into form
    // save old guia name in "Viejo" hidden form input to allow change guia name
    $('#guias-Viejo').val( $('#guias-Nombre').val());
	$('#guias-Parent').val(''); // no parent, just in "guias" menu
	$('#guias-Operation').val('update'); // operation is change data
}

/**
 * Invoca a json para añadir/editar los datos del guia seleccionado en el formulario
 * Ask for commit new/edit guia to server
 */
function assignGuia(){
	// $('#chguias-Viejo').val($('#chguias-Search').combogrid('getValue'));
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
                    title: 'Error',
                    msg: result.errorMsg
                });
            } else {
            	var parent=$('#chguias-Parent').val();
                $('#guias-datagrid'+parent).datagrid('reload');    // reload the guia data
                $('#chguias-Search').combogrid({ 'value' : ''});        // clear search form
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
            	var parent=$('#guias-Parent').val();
                $('#guias-datagrid'+parent).datagrid('reload');    // reload the guia data
                $('#guias-dialog').dialog('close');        // close the dialog
            }
        }
    });
}

/**
 * Borra de la BBDD los datos del guia seleccionado. 
 * Invocada desde el menu de guias
 */
function destroyGuia(){
    var row = $('#guias-datagrid').datagrid('getSelected');
    if (!row) return;
    $.messager.confirm('Confirm','Borrar datos del guia. '+ row.Nombre+'¿Seguro?',function(r){
        if (r){
            $.get('database/guiaFunctions.php',{Operation:'delete',Nombre:row.Nombre},function(result){
                if (result.success){
                    $('#guias-datagrid').datagrid('reload');    // reload the guia data
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
 * Quita la asignacion del guia marcado al club indicado
 * Invocada desde el menu de clubes
 *@param club datos del club
 */
function delGuiaFromClub(club) {
    var row = $('#guias-datagrid-'+replaceAll(' ','_',club.Nombre)).datagrid('getSelected');
    if (!row) return;

    $.messager.confirm('Confirm',"Borrar asignacion del gu&iacute;a '"+row.Nombre+"' al club '"+club.Nombre+"' ¿Seguro?'",function(r){
        if (r){
            $.get('database/guiaFunctions.php',{'Operation':'orphan','Nombre':row.Nombre},function(result){
                if (result.success){
                    $('#guias-datagrid-'+replaceAll(' ','_',club.Nombre)).datagrid('reload');    // reload the guia data
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
	$('#perros-form').form('clear'); // start with an empty form
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
    var row = $('#perros-datagrid').datagrid('getSelected');
    if (!row) return;
    $('#perros-dialog').dialog('open').dialog('setTitle','Modificar datos del perro');
    $('#perros-form').form('load',row);// load form with row data
	$('#perros-Parent').val(''); // no parent datagrid
    $('#perros-Operation').val('update'); // mark "update" operation
}

/**
 * Abre el dialogo para editar datos de un perro ya existente desde el menu de inscripciones
 * @param {integer} mode 0:newInscripcion 1:editInscripcion
 */
function editInscribedDog(mode){
	var dorsal=0;
	if (mode==0) dorsal= $('#inscripciones-Dorsal').val();
	else dorsal= $('#chinscripciones-Dorsal').val();
    $('#perros-dialog').dialog('open').dialog('setTitle','Modificar datos del perro a inscribir');
    $('#perros-form').form('load','database/dogFunctions.php?Operation=getbydorsal&Dorsal='+dorsal);// load form with row data
	$('#perros-Parent').val(''); // no parent datagrid
    $('#perros-Operation').val('update'); // mark "update" operation
}
/**
* Abre el formulario para anyadir perros a un guia
*@param guia: nombre del guia
*/
function editPerroFromGuia(guia) {
	var parent = '-'+replaceAll(' ','_',guia.Nombre);
	// try to locate which dog has been selected 
    var row = $('#perros-datagrid'+parent).datagrid('getSelected');
    if (!row) return; // no way to know which dog is selected
    $('#perros-dialog').dialog('open').dialog('setTitle','Modificar datos del perro asignado a '+guia.Nombre);
    $('#perros-form').form('load',row);	// load form with row data
	$('#perros-Guia').combogrid({ 'value': guia.Nombre} );  // set up default "guia" value
	$('#perros-Parent').val(parent); // store parent datagrid suffix
    $('#perros-Operation').val('update'); // mark "update" operation
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
	var dorsal=$('#perros-Dorsal').val();
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
            	var url='database/dogFunctions.php=Operation=getbydorsal&Dorsal='+dorsal;
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
    var row = $('#perros-datagrid-'+replaceAll(' ','_',guia.Nombre)).datagrid('getSelected');
    if (!row) return;
    $.messager.confirm('Confirm',"Borrar asignacion del perro '"+row.Nombre+"' al guia '"+guia.Nombre+"' ¿Seguro?'",function(r){
        if (r){
            $.get('database/dogFunctions.php',{Operation:'orphan',Dorsal:row.Dorsal},function(result){
                if (result.success){
                    $('#perros-datagrid-'+replaceAll(' ','_',guia.Nombre)).datagrid('reload');    // reload the guia data
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
 * Borra el perro seleccionado de la base de datos
 */
function destroyDog(){
    var row = $('#perros-datagrid').datagrid('getSelected');
    if (row){
        $.messager.confirm('Confirm','Borrar el perro "'+ row.Nombre+'" de la base de datos. ¿Seguro?',function(r){
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
 * Open "New Juez dialog"
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
    $('#pruebas-form').form('load',row);
    // take care on int-to-bool translation for checkboxes
    $('#pruebas-Cerrada').prop('checked',(row.Cerrada==1)?true:false);
    // save old guia name in "Viejo" hidden form input to allow change guia name
    $('#pruebas-Viejo').val( $('#pruebas-Nombre').val());
	$('#pruebas-Operation').val('update');
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
                $('#jornadas-datagrid-'+id).datagrid('reload',{Prueba: id});    // reload the prueba data
                $('#inscripciones_jornadas').datagrid('reload');    // reload the prueba data
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

function doSearchInscripcion() {
	// reload data adding search criteria
    $('#inscripciones-datagrid').datagrid('load',{
        where: $('#inscripciones-search').val()
    });
}

function reloadInscripcion() {
	// clear search field and reload
	$('#inscripciones-search').val('');
	doSearchInscripcion();
}

function newInscripcion() {
	var cerrada=false;
	// cerramos dialogo de modificacion de inscripcion
	$('#chinscripciones-dialog').dialog('close');
	// abrimos dialogo de nueva inscripcion
	$('#inscripciones-dialog').dialog('open').dialog('setTitle','Inscripci&oacute;n de nuevos participantes');
	$('#inscripciones-Participante').combogrid('clear'); // clear form is not enought for an easyui component
	$('#inscripciones-form').form('clear');
	$('#inscripciones-data').form('clear');
	// disable those ones that belongs to closed journeys
	// disable those ones that belongs to closed journeys
	cerrada= ($('#jornada_cerrada-1').text()=='1')?true:false;
	$('#inscripciones-J1').prop('disabled',cerrada);
	cerrada= ($('#jornada_cerrada-2').text()=='1')?true:false;
	$('#inscripciones-J2').prop('disabled',cerrada);
	cerrada= ($('#jornada_cerrada-3').text()=='1')?true:false;
	$('#inscripciones-J3').prop('disabled',cerrada);
	cerrada= ($('#jornada_cerrada-4').text()=='1')?true:false;
	$('#inscripciones-J4').prop('disabled',cerrada);
	cerrada= ($('#jornada_cerrada-5').text()=='1')?true:false;
	$('#inscripciones-J5').prop('disabled',cerrada);
	cerrada= ($('#jornada_cerrada-6').text()=='1')?true:false;
	$('#inscripciones-J6').prop('disabled',cerrada);
	cerrada= ($('#jornada_cerrada-7').text()=='1')?true:false;
	$('#inscripciones-J7').prop('disabled',cerrada);
	cerrada= ($('#jornada_cerrada-8').text()=='1')?true:false;
	$('#inscripciones-J8').prop('disabled',cerrada);
}

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
	$('#chinscripciones-data').form('load','database/dogFunctions.php?Operation=getbydorsal&Dorsal='+row.Dorsal);
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
					Dorsal:row.Dorsal,
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
					if (result.success) $('#inscripciones-datagrid').datagrid('reload');    // reload the prueba data
					else $.messager.show({ title: 'Error',msg: result.errorMsg });
				},'json');
		} // if (r)
	}).window({width:475});
}

/**
 * Ask for commit new inscripcion to server
 */
function insertInscripcion(){
	// fill needed data to be sent
	$('#inscripciones-fDorsal').val($('#inscripciones-Dorsal').val());
	$('#inscripciones-fPruebaID').val(workingData.prueba);
	$('#inscripciones-fOperation').val('doit');
    // do normal submit
    $('#inscripciones-form').form('submit',{
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
                $('#inscripciones-dialog').dialog('close');        // close the dialog
                // notice that some of these items may fail if dialog is not deployed. just ignore
                $('#inscripciones-datagrid').datagrid('reload');    // reload the inscripciones table
            }
        }
    });
}

/**
 * Ask for submit inscription changes to server
 */
function updateInscripcion(){
	// fill needed data to be sent
	$('#chinscripciones-fDorsal').val($('#chinscripciones-Dorsal').val());
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
                $('#chinscripciones-datagrid').datagrid('reload');    // reload the inscripciones table
            }
        }
    });
}

function printInscripciones() {
	$.fileDownload('pdf/print_InscritosByPrueba.php?Prueba='+workingData.prueba);
}
