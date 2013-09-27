var dogurl;

/**
 * Open "New Dog dialog"
 */
function newDog(){
	$('#dlg').dialog('open').dialog('setTitle','Nuevo perro');
	$('#fm').form('clear');
	dogurl = 'database/json/save_dog.php';
}

/**
 * Open "Edit Dog" dialog
 */
function editDog(){
    var row = $('#dg').datagrid('getSelected');
    if (row){
        $('#dlg').dialog('open').dialog('setTitle','Modificar datos del perro');
        $('#fm').form('load',row);
        dogurl = 'database/json/update_dog.php?id='+row.id;
    }
}

/**
 * Ask for commit new/edit dog to server
 */
function saveDog(){
    $('#fm').form('submit',{
        url: dogurl,
        onSubmit: function(){
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
                $('#dlg').dialog('close');        // close the dialog
                $('#dg').datagrid('reload');    // reload the dog data
            }
        }
    });
}

/**
 * Delete dog data
 */
function destroyDog(){
    var row = $('#dg').datagrid('getSelected');
    if (row){
        $.messager.confirm('Confirm','Borrar datos del perro. ¿Seguro?',function(r){
            if (r){
                $.post('database/json/destroy_dog.php',{id:row.id},function(result){
                    if (result.success){
                        $('#dg').datagrid('reload');    // reload the dog data
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