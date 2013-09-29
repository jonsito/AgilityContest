var operation;

/**
 * Abre el formulario para anyadir perros a un guia
 *@param index: indice que ocupa el guia en la entrada principal
 *@param guia: nombre del guia
 */
function addPerroToGuia(index,guia) {
	alert('TODO:<br/> Index'+index+'<br/>Asignar perros a '+guia);
}
/**
 * Quita la asignacion del perro marcado al guia indicado
 */
function delPerroFromGuia(index,guia) {
    var row = $('#guias-dog-datagrid-'+index).datagrid('getSelected');
    if (!row) return;

    $.messager.confirm('Confirm',"Borrar asignacion del perro '"+row.Nombre+"' al guia '"+guia+"' ¿Seguro?'",function(r){
        if (r){
            $.get('database/guiaFunctions.php',{operation:'orphan',Dorsal:row.Dorsal},function(result){
                if (result.success){
                    $('#guias-dog-datagrid-'+index).datagrid('reload');    // reload the guia data
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
 * Recalcula el formulario anyadiendo parametros de busqueda
 */
function doSearchGuia() {
	// reload data adding search criteria
    $('#guias-datagrid').datagrid('load',{
        where: $('#guias-search').val()
    });
    // clear search textbox
    // hey, this fire up again onChangeEvent :-(
    // $('#guias-search').val('');
}

/**
 * Open "Guia dialog"
 */
function newGuia(){
	$('#guias-dialog').dialog('open').dialog('setTitle','Nuevo g&uiacute;a');
	$('#guias-form').form('clear');
	operation='insert';
}

/**
 * Open "Edit guia" dialog
 */
function editGuia(){
    var row = $('#guias-datagrid').datagrid('getSelected');
    if (!row) return;
    $('#guias-dialog').dialog('open').dialog('setTitle','Modificar datos del gu&iacute;a');
    $('#guias-form').form('load',row);
    // take care on int-to-bool translation for checkboxes
    $('#guias-Baja').prop('checked',(row.Baja==1)?true:false);
    // save old guia name in "Viejo" hidden form input to allow change guia name
    $('#guias-Viejo').val( $('#guias-Nombre').val());
    operation='update';
}

/**
 * Ask for commit new/edit guia to server
 */
function saveGuia(){
	// take care on bool-to-int translation from checkboxes to database
    $('#guias-Baja').val( $('#guias-Baja').is(':checked')?'1':'0');
    // do normal submit
    $('#guias-form').form('submit',{
        url: 'database/guiaFunctions.php',
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
                $('#guias-dialog').dialog('close');        // close the dialog
                $('#guias-datagrid').datagrid('reload');    // reload the guia data
            }
        }
    });
}

/**
 * Delete guia data
 */
function destroyGuia(){
    var row = $('#guias-datagrid').datagrid('getSelected');
    if (!row) return;
    $.messager.confirm('Confirm','Borrar datos del guia. ¿Seguro?',function(r){
        if (r){
            $.get('database/guiaFunctions.php',{operation:'delete',Nombre:row.Nombre},function(result){
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