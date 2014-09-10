<!-- FORMULARIO DE EDICION DE INSCRIPCIONES -->
<div id="tablet-dialog" style="width:525px;padding:10px">
	<div style="height:425px" class="dialog_forms">
        <form id="edit_inscripcion-form" method="get" novalidate="novalidate">
        </form> 
    </div>
</div>
   <script type="text/javascript">
    
    // declaracion del dialogo
    $('#edit_inscripcion-dialog').dialog({
        iconCls: 'icon-notes',
        closed: true,
        modal: true,
        buttons: '#edit_inscripcion-dlg-buttons',
        title: 'Editar inscripcion'
    });

    $('#edit_inscripcion-form').form({
        onLoadSuccess: function(data) {
        }
    });
    
    // tooltips
    addTooltip($('#edit_inscripcion-editDogBtn').linkbutton(),"Modificar la información del perro<br/>en la Base de Datos");
    addTooltip($('#edit_inscripcion-okBtn').linkbutton(),"Aceptar cambios<br/> Actualizar datos de la inscripci&oacute;n");
    addTooltip($('#edit_inscripcion-cancelBtn').linkbutton(),"Cancelar cambios. Cerrar la ventana");
    
 	// acciones de los botones
    $('#edit_inscripcion-cancelBtn').bind('click',function() {
        $('#edit_inscripcion-form').form('reset'); 
        $('#edit_inscripcion-Equipo').combogrid({ 'setValue' : '-- Sin asignar --' });
        $('#edit_inscripcion-dialog').dialog('close');
    });

    $('#edit_inscripcion-editDogBtn').bind('click',function() {
        editInscribedDog();
    });
    
    // layout
    var dg= {'cols':8, 'rows':15}; // declare base datagrid
	doLayout(dg,'#edit_inscripcion-dogInfo',		0,	0,	3,	1	);
    </script>