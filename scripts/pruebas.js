var operation;

/**
 * Abre el formulario para jornadas a una prueba
 *@param prueba objeto que contiene los datos de la prueba
 */
function addJornadaToPrueba(prueba) {
	alert('TODO:<br/> Crear una nueva jornada para la prueba'+prueba.Nombre);
}

/**
 * Edita la jornada seleccionada
 *@param index: indice que ocupa el guia en la entrada principal
 *@param prueba objeto que contiene los datos de la prueba
 */
function editJornadaFromPrueba(index,prueba) {
    var row = $('#pruebas-jornada-datagrid-'+index).datagrid('getSelected');
    if (!row) return; // no row selected
    if (row.Cerrada==true) {
        $.messager.show({    // show error message
            title: 'Error',
            msg: 'No se puede editar una jornada una vez cerrada'
        });
    }
	alert('TODO:<br/> Index'+index+'<br/>editar jornada de la prueba'+prueba.Nombre);
}

/**
 * Quita la asignacion de la jornada indicada a la prueba asignada
 *@param indice de la fila (jornada) afectada
 *@prueba objeto que contiene los datos de la prueba
 */
function delJornadaFromPrueba(index,prueba) {
    var row = $('#pruebas-jornada-datagrid-'+index).datagrid('getSelected');
    if (!row) return; // no row selected
    if (prueba.Cerrada==true) {
        $.messager.show({    // show error message
            title: 'Error',
            msg: 'No se pueden borrar jornadas de una prueba cerrada'
        });
    }
    $.messager.confirm('Confirm',"Borrar Jornada '"+row.ID+"' de la prueba '"+prueba.Nombre+"' ¿Seguro?'",function(r){
        if (r){
            $.get('database/pruebaFunctions.php',{operation:'orphan',Jornada:row.ID},function(result){
                if (result.success){
                    $('#pruebas-jornada-datagrid-'+index).datagrid('reload');    // reload the pruebas data
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
 * Recalcula el formulario de pruebas anyadiendo parametros de busqueda
 */
function doSearchPrueba() {
	// reload data adding search criteria
    $('#pruebas-datagrid').datagrid('load',{
        where: $('#pruebas-search').val(),
        cerrada: $('#pruebas-openBox').val()
    });
}

/**
 * Open dialogo de creacion de pruebas
 */
function newPrueba(){
	$('#pruebas-dialog').dialog('open').dialog('setTitle','Nueva Prueba');
	$('#pruebas-form').form('clear');
	operation='insert';
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
    operation='update';
}

/**
 * Ask for commit new/edit guia to server
 */
function savePrueba(){
	// take care on bool-to-int translation from checkboxes to database
    $('#pruebas-Cerrada').val( $('#pruebas-Cerrada').is(':checked')?'1':'0');
    // do normal submit
    $('#pruebas-form').form('submit',{
        url: 'database/pruebaFunctions.php',
        method: 'get',
        onSubmit: function(param){
        	param.operation=operation;
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
    $.messager.confirm('Confirm','Borrar datos de la prueba ¿Seguro?',function(r){
        if (r){
            $.get('database/pruebaFunctions.php',{operation:'delete',Nombre:row.Nombre},function(result){
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