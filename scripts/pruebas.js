
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
                $('#jornadas-dialog').dialog('close');        // close the dialog
                $('#jornadas-datagrid').datagrid('reload');    // reload the prueba data
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