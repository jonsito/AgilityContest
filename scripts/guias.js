var operation;

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
    if (row){
        $('#guias-dialog').dialog('open').dialog('setTitle','Modificar datos del gu&iacute;a');
        $('#guias-form').form('load',row);
        // save old guia name in "Viejo" hidden form input to allow change guia name
        $('#guias-Viejo').val( $('#guias-Nombre').val());
        operation='update';
    }
}

/**
 * Ask for commit new/edit guia to server
 */
function saveGuia(){
    // do normal submit
    $('#guias-form').form('submit',{
        url: 'database/json/handlerFunctions.php',
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
    if (row){
        $.messager.confirm('Confirm','Borrar datos del guia. ¿Seguro?',function(r){
            if (r){
                $.get('database/json/handlerFunctions.php',{operation:'delete',Nombre:row.Nombre},function(result){
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
}