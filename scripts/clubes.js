/**
 * Abre el formulario para anyadir guias a un club
 *@param index: indice que ocupa el club en la entrada principal
 *@param club: nombre del club
 */
function addGuiaToClub(index,club) {
	alert('TODO:<br/> Index'+index+'<br/>Asignar gu&iacute;as al '+club);
}

/**
 * Quita la asignacion del guia marcado al club indicado
 */
function delGuiaFromClub(index,club) {
    var row = $('#clubes-guia-datagrid-'+index).datagrid('getSelected');
    if (!row) return;

    $.messager.confirm('Confirm',"Borrar asignacion del gu&iacute;a '"+row.Nombre+"' al club '"+club+"' ¿Seguro?'",function(r){
        if (r){
            $.get('database/clubFunctions.php',{'Operation':'orphan','Nombre':row.Nombre},function(result){
                if (result.success){
                    $('#clubes-guia-datagrid-'+index).datagrid('reload');    // reload the guia data
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
function doSearchClub() {
	// reload data adding search criteria
    $('#clubes-datagrid').datagrid('load',{
        where: $('#clubes-search').val()
    });
}

/**
 * Open "Club dialog"
 */
function newClub(){
	$('#clubes-dialog').dialog('open').dialog('setTitle','Nuevo club');
	$('#clubes-form').form('clear');
	$('#clubbes-Operation').val('insert');
}

/**
 * Open "Edit club" dialog
 */
function editClub(){
    var row = $('#clubes-datagrid').datagrid('getSelected');
    if (!row) return;
    $('#clubes-dialog').dialog('open').dialog('setTitle','Modificar datos del club');
    $('#clubes-form').form('load',row);
    // save old club name in "Viejo" hidden form input to allow change guia name
    $('#clubes-Viejo').val( $('#clubes-Nombre').val());
	$('#clubbes-Operation').val('update');
}

/**
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
 * Delete guia data
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