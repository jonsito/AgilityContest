/**
 * Abre el formulario para jornadas a una prueba
 *@param prueba objeto que contiene los datos de la prueba
 */
function addJornadaToPrueba(prueba) {
	myPrueba=prueba;
	$('#jornadas-dialog').dialog('open').dialog('setTitle','A&ntilde;adir jornada a la prueba '+prueba.Nombre);
	$('#jornadas-form').form('clear');
	$('#jornadas-Prueba').val(prueba.Nombre);
	$('#jornadas-Operacion').val('insert');
}

/**
 * Edita la jornada seleccionada
 *@param index: indice que ocupa el guia en la entrada principal
 *@param prueba objeto que contiene los datos de la prueba
 */
function editJornadaFromPrueba(index,prueba) {
	// obtenemos datos de la prueba seleccionada
    var row = $('#pruebas-jornada-datagrid-'+index).datagrid('getSelected');
    if (!row) return; // no hay ninguna jornada seleccionada. retornar
    if (row.Cerrada==true) { // no permitir la edicion de pruebas cerradas
        $.messager.show({    
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
