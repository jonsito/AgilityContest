var operation;

/**
 * Recalcula el formulario anyadiendo parametros de busqueda
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
	operation='insert';
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
        operation='update';
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
                $.get('database/juezFunctions.php',{operation:'delete',Nombre:row.Nombre},function(result){
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