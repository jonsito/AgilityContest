
   <!-- FORMULARIO DE EDICION DE INSCRIPCIONES -->
    <div id="edit_inscripcion-dialog" style="width:460px;height:550px;padding:10px 20px">
        
        	<form id="edit_inscripcion-data" method="get" novalidate="novalidate">
        		<input id="edit_inscripcion-ID" type="hidden" name="ID"/>
        		<input id="edit_inscripcion-Operation" type="hidden" name="Operation" value="update"/>
                <input id="edit_inscripcion-LOE_RRC" name="LOE_RRC" type="hidden" />
                <input id="edit_inscripcion-Raza" name="Raza" type="hidden" />
            	<input id="edit_inscripcion-Perro" name="Perro" type="hidden" />
            	
        		<table>
        		<tr>
        		<td colspan="3"><div class="ftitle">Informaci&oacute;n del perro:</div></td>
        		<td rowspan="12" style="border-left: thin solid #c0c0c0;">&nbsp;</td>
        		<td colspan="2"><div class="ftitle">Datos de la inscripci&oacute;n:</div></td>
        		</tr>
        		
        		<tr> 
        		<td colspan="3">
        			<label for="Nombre">Nombre:</label>
        		</td>
        		<td colspan="2">
            	    <input id="edit_inscripcion-J1" name="J1" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            	    <label id="edit_inscripcion-J1lbl" for="J1">J1</label>
        		</td>
        		</tr>
        		
        		<tr>
        		<td colspan="3">
            		<input id="edit_inscripcion-Nombre" name="Nombre" type="text" size="20" disabled="disabled" />
        		</td>
        		<td colspan="2">
            	    <input id="edit_inscripcion-J2" name="J2" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            	    <label id="edit_inscripcion-J2lbl" for="J2">J2</label>
        		</td>
        		</tr>
        		
        		<tr>
        		<td colspan="3">
            		<label for="Licencia">Licencia: </label>
        		</td>
        		<td colspan="2">
            	    <input id="edit_inscripcion-J3" name="J3" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            	    <label id="edit_inscripcion-J3lbl" for="J3">J3</label>
        		</td>
        		</tr>
        		
        		<tr>
        		<td colspan="3">
            		<input id="edit_inscripcion-Licencia" name="Licencia" type="text" size="3" disabled="disabled"/>
        		</td>
        		<td colspan="2">
            	    <input id="edit_inscripcion-J4" name="J4" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
 	          	    <label id="edit_inscripcion-J4lbl" for="J4">J4</label>
        		</td>
        		</tr>
        		
        		<tr>
        		<td> <label for="Categoria">Categoria: </label></td>
        		<td>&nbsp;</td>
        		<td><label for="Grado">Grado: </label></td>
        		<td colspan="2">
            	    <input id="edit_inscripcion-J5" name="J5" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            	    <label id="edit_inscripcion-J5lbl" for="J5">J5</label>
        		</td>
        		</tr>
   
   				<tr>
   				
        		<td><input id="edit_inscripcion-Categoria" name="Categoria" type="text" size="3" disabled="disabled"/></td>
        		<td>&nbsp;</td>
        		<td><input id="edit_inscripcion-Grado" name="Grado" type="text" size="3" disabled="disabled" /></td>
        		<td colspan="2">
            	    <input id="edit_inscripcion-J6" name="J6" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            	    <label id="edit_inscripcion-J6lbl" for="J6">J6</label>
        		</td>
   				</tr> 
   				
   				<tr>
        		<td colspan="3">
            		<label for="NombreGuia">Gu&iacute;a: </label>
        		</td>
        		<td colspan="2">
            	    <input id="edit_inscripcion-J7" name="J7" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            	    <label id="edit_inscripcion-J7lbl" for="J7">J7</label>
        		</td>
   				</tr>
   				
   				<tr>
        		<td colspan="3">
            		<input id="edit_inscripcion-NombreGuia" name="NombreGuia" type="text" size="20" disabled="disabled"/>
        		</td>
        		<td colspan="2">
            	    <input id="edit_inscripcion-J8" name="J8" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
            	    <label id="edit_inscripcion-J8lbl" for="J8">J8</label>
        		</td>
   				</tr>
   				
   				<tr>
        		<td colspan="3">
            		<label for="NombreClub">Club: </label>
        		</td>
        		<td colspan="2">   
        			<label for="Equipo">Equipo: </label>         	    
            	    <select id="edit_inscripcion-Equipo" name="Equipo" stype="width:100px"></select>
        		</td>
   				</tr>
   				
   				<tr>
   				<td colspan="3">
           				<input id="edit_inscripcion-NombreClub" name="NombreClub" type="text" disabled="disabled" /><br />
   				</td>
   				<td colspan="2">
            		<label id="edit_inscripcion-Celolbl" for="Celo">Celo:</label>
            		<input id="edit_inscripcion-Celo" name="Celo" class="easyui-checkbox" type="checkbox" value="1" />
   				</td>
   				</tr>
   				
   				<tr>
   				<td colspan="3">
	                <label id="edit_inscripcion-Observacioneslbl" for="Observaciones">Observaciones:</label>
   				</td>
   				<td colspan="2">
	                <label id="edit_inscripcion-Pagadolbl" for="Pagado">Pagado: </label>
 	  				<input id="edit_inscripcion-Pagado" name="Pagado" 
 	  					class="easyui-validatebox" type="text" size="5" style="text-align:right;padding-right:5px"/>&euro;
   				</td>
   				</tr>
   				
   				<tr>
   				<td colspan="6">
	                <input id="edit_inscripcion-Observaciones" type="text" size="40" name="Observaciones"/>
   				</td>
   				</tr>
   				
           		</table>
        	</form>
                		
        <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
		<div id="edit_inscripcion-dlg-buttons">
			<span style="float:left">
		   		<a id="edit_inscripcion-editDogBtn" href="#" />
		   	</span>
		   	<span style="float:right">  	
				<a id="edit_inscripcion-cancelBtn" href="#" />
        		<a id="edit_inscripcion-okBtn" href="#"  onclick="updateInscripcion()" />
        		<a id="edit_inscripcion-clearBtn" href="#"/>
        	</span>
    	</div>

    </div>

    <script type="text/javascript">
    // datos del formulario de nuevo/edit prueba
    $('#edit_inscripcion-dialog').dialog({
        title: 'Editar inscripcion',
        closed: true,
		modal:true,
        buttons: '#edit_inscripcion-dlg-buttons'
    });
    
    // - declaracion del formulario
    $('#edit_inscripcion-data').form();
    
    // - botones
    $('#edit_inscripcion-editDogBtn').linkbutton({iconCls:'icon-edit',text: 'Editar'});
    $('#edit_inscripcion-okBtn').linkbutton({iconCls:'icon-save',text: 'Aceptar'});
    $('#edit_inscripcion-cancelBtn').linkbutton({iconCls:'icon-cancel',text: 'Cancelar'});
    $('#edit_inscripcion-clearBtn').linkbutton({iconCls:'icon-undo',text: 'Deshacer'});
    // tooltips
    addTooltip($('#edit_inscripcion-editDogBtn').linkbutton(),"Modificar la información del perro<br/>en la Base de Datos");
    addTooltip($('#edit_inscripcion-okBtn').linkbutton(),"Aceptar cambios<br/> Actualizar datos de la inscripci&oacute;n");
    addTooltip($('#edit_inscripcion-cancelBtn').linkbutton(),"Cancelar cambios. Cerrar la ventana");
    addTooltip($('#edit_inscripcion-clearBtn').linkbutton(),"Deshacer cambios. restaurar formulario"); 
 
    $('#edit_inscripcion-cancelBtn').bind('click',function() {
        $('#edit_inscripcion-buscar').form('clear'); 
        $('#edit_inscripcion-data').form('clear'); 
        $('#edit_inscripcion-form').form('clear'); 
        $('#edit_inscripcion-Participante').combogrid({ 'setValue' : '' });
        $('#edit_inscripcion-Equipo').combogrid({ 'setValue' : '-- Sin asignar --' });
        $('#edit_inscripcion-dialog').dialog('close');
    });
    
    $('#edit_inscripcion-clearBtn').bind('click',function() {
        $('#edit_inscripcion-buscar').form('clear'); 
        $('#edit_inscripcion-data').form('clear'); 
        $('#edit_inscripcion-form').form('clear'); 
        $('#edit_inscripcion-Participante').combogrid({ 'setValue' : '' });
        $('#edit_inscripcion-Equipo').combogrid({ 'setValue' : '-- Sin asignar --' });
    });

    $('#edit_inscripcion-editDogBtn').bind('click',function() {
        editInscribedDog(0);
    });
    
    // campos del formulario
	
    $('#edit_inscripcion-Equipo').combogrid({
		panelWidth: 350,
		panelHeight: 100,
		idField: 'ID',
		textField: 'Nombre',
		url: 'database/pruebaFunctions.php?Operation=equipos&ID='+workingData.prueba,
		method: 'get',
		mode: 'remote',
		required: false,
		columns: [[
			{field:'ID',hidden:'true'},
			{field:'Nombre',title:'Nombre',width:25,align:'right'},
			{field:'Observaciones',title:'Observaciones',width:75,align:'right'}
		]],
		multiple: false,
		fitColumns: true,
		singleSelect: true,
		selectOnNavigation: false
    });
    
    function doMoney(checkbox) {
        var money=Number($('#edit_inscripcion-Pagado').val());
        if (checkbox.checked) $('#edit_inscripcion-Pagado').val(money+12);
        else $('#edit_inscripcion-Pagado').val(money-12);
    }
    </script>